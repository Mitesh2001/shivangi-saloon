<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;
use App\Helpers\Helper;
use JWTAuth;

use App\Models\Unit;

use App\Http\Requests\Unit\StoreUnitRequest;
use App\Http\Requests\Unit\UpdateUnitRequest;

class UnitsController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $search_text = $request->search_text;
        $paginate = $request->paginate ?? 0;

        $units = Unit::select('*');
		if(!empty($search_text))
			$units->where('name', 'LIKE', "%" . $search_text . "%");
		
        $units = $units->orderBy('name', 'asc');

        if($paginate == 1) {
            $data = $units->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $units->get();
            $count = count($data['data']);
        }

        if($count > 0) {
            $custom = collect(['status' => 'SUCCESS', 'message' => '']);
            $data = $custom->merge($data);
            return response()->json($data);
        } else {
            $custom = collect(['status' => 'FAIL', 'message' => 'No data found!']);
            $data = $custom->merge($data);
            return response()->json($data);
        }
    }
	
	
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Auth User
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        // Check if salon has subscription or not
        $distributor_id = $user->distributor_id;
        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }

        // Validations
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
        ], [
            'name.required' => "Please enter unit name!",  
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
		$user_id = $user->id();
      
        $unit = Unit::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,    
            'created_by' => $user_id, 
            'updated_by' => $user_id, 
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $unit,
            'message' => 'Unit successfully added!'
        ]);
    }
	
	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $external_id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        } 
        $distributor_id = $user->distributor_id;

        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        } 

        $unit = $this->findByExternalId($external_id);

        if(!empty($unit)) {
 
            $rules_arr = [
                'name' => ['required'],
            ];
    
    
            // Validations
            $validator = Validator::make($request->all(), $rules_arr, [
				'name.required' => "Please enter unit name!",  
			]);
    
            if ($validator->fails()) {
                $errorString = implode(",", $validator->messages()->all());
                return response()->json([
                    'status' => 'FAIL',
                    'message' => $errorString
                ]);
            } 

            $update_data = [ 
                'name' => $request->name,
            ];
            
            $unit->fill($update_data)->save();
 
            return response()->json([
                'status' => 'SUCCESS', 
                'data' => $update_data,
                'message' => 'Unit successfully updated!'
            ]);
             
        } else {
            if(Helper::allowViewOnly($user->distributor_id)) {
                return response()->json([
                    'status' => 'FAIL',
                    'message' => "Unit not found!"
                ]);
            }  
        } 
    }
  
    public function findByExternalId($external_id)
    {
        return Unit::where('external_id', $external_id)->first();
    }
}
