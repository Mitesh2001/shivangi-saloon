<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use JWTAuth;
use Ramsey\Uuid\Uuid;

use App\Models\User;
use App\Models\Holiday;
use App\Helpers\Helper;

class HolidaysController extends Controller
{
    public function index(Request $request)
    {
		$user = JWTAuth::parseToken()->authenticate();
		if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }
		$distributor_id = $user->distributor_id;
		$search_text = $request->search_text; 
		$paginate = $request->paginate ?? 0; 

        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }

        $holidays = Holiday::where('distributor_id','=',$distributor_id);//->get();
		if(!empty($search_text))
		$holidays->where(function ($q) use ($search_text) {
			$q->where('name', 'LIKE', "%" . $search_text . "%");
			$q->orWhere('date', 'LIKE', "%" . $search_text . "%");
		});
		if($paginate == 1) {
            $data = $holidays->paginate();
            $count = count($data);
        } else { 
            $data = [];
            $data['data'] = $holidays->get();
            $count = count($data['data']);
        } 

        if($count > 0) {
            $custom = collect(['status' => 'SUCCESS']); 
            $custom = collect(['message' => '']); 
            $data = $custom->merge($data); 
            return response()->json($data);
        } else {
            $custom = collect(['status' => 'FAIL']); 
            $custom = collect(['message' => 'No data found!']); 
            $data = $custom->merge($data); 
            return response()->json($data);
        }
    }

	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
		$holiday = $this->findByExternalId($id);
        return response()->json([
            'status' => 'SUCCESS',
            'data' => $holiday
        ]);
    }

	/**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
		$validator = Validator::make($request->all(), [
            'name' => ['required', Rule::unique('holidays')->where(function ($query) use ($distributor_id) {
                return $query->where('distributor_id', $distributor_id);
            })],
            'date' => ['required', 'date'],
        ], [
            'name.required' => "Please enter holiday name!",
            'date.required' => "Please select holiday date!",
            'date.date' => "Please select valid holiday date!",
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
		$user_id = $user->id;
		$holiday = Holiday::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'date' => $request->date,
            'created_by' => $user_id,
            'updated_by' => $user_id,
            'distributor_id' => $distributor_id,
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $holiday,
            'message' => 'Holiday successfully added!'
        ]);
    }

	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
		$user_id = $user->id;
		$holiday = $this->findByExternalId($id);

        $validator = Validator::make($request->all(), [
			'name' => ['required', Rule::unique('holidays')->where(function ($query) use ($distributor_id, $holiday) {
				return $query->where('distributor_id', $distributor_id)->where('id', '!=', $holiday->id);
			})],
            'date' => ['required', 'date'],
        ], [
            'name.required' => "Please enter holiday name!",
            'date.required' => "Please select holiday date!",
            'date.date' => "Please select valid holiday date!",
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

		$holiday->fill([
            'name' => $request->name,
            'date' => $request->date,
            'updated_by' => $user_id,
        ])->save();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $holiday,
            'message' => 'Holiday successfully updated!'
        ]);
    }

	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
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
		$holiday = $this->findByExternalId($id);

		$holiday->delete();
		return response()->json([
			'status' => 'SUCCESS',
			'message' => 'Holiday successfully deleted!'
		]);

    }

	public function findByExternalId($external_id)
    {
        return Holiday::where('external_id', $external_id)->first();
    }
}
