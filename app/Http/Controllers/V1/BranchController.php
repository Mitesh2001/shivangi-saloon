<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use DB;
use Auth;
use JWTAuth;
use Datatables;

use Ramsey\Uuid\Uuid;
use App\Helpers\Helper;

use App\Models\User;
use App\Models\Distributor;
use App\Models\Branch;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }
        $search_text = $request->search_text;
        $paginate = $request->paginate ?? 0;
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $branch = Branch::with(['get_primary_user', 'get_secondary_user', 'getCountry', 'getCountry'])->where('is_archive', 0);
        $branch->where('distributor_id', $user->distributor_id);
		if(!empty($search_text))
        $branch->where(function ($qeury) use ($search_text) {
            $qeury->where('name', 'LIKE', "%" . $search_text . "%");
            $qeury->orWhere('city', 'LIKE', "%" . $search_text . "%");
        });
        $branch->orderBy('id', 'desc');

        if($paginate == 1) {
            $data = $branch->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $branch->get();
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

        $distributor_id = $user->distributor_id;
        // Check if salon has subscription or not
        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }
        if(!Helper::canCreateBranch($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "As per subscription you can not create more branches"
            ]);
        }

        $rules_arr = [
            'name' => ['required'],
            'primary_contact_number' => ['required', 'numeric', 'digits:10', Rule::unique('branches')->where(function ($query) use ($distributor_id) {
                return $query->where('distributor_id', $distributor_id);
            })],
            'secondary_contact_number' => ['numeric', 'digits:10'],
            'primary_email' => ['email', Rule::unique('branches')->where(function ($query) use ($distributor_id) {
                return $query->where('distributor_id', $distributor_id);
            })],
            'secondary_email' => ['email'],
            'country_id' => ['required'],
            'city' => ['required'],
            'zipcode' => ['required'],
        ];

        if($request->country_id == 101){
            $rules_arr['state_id'] = ['required'];
        }else{
            $rules_arr['state_name'] = ['required'];
        }

        // Validations
        $validator = Validator::make($request->all(), $rules_arr, [
            'name.required' => 'Please enter name!',
            'primary_email.email' => 'Please enter valid primary email!',
            'secondary_email.email' => 'Please enter valid secondary email!',
            'primary_contact_number.required' => 'Please enter primary number!',
            'primary_contact_number.numeric' => 'Please enter valid primary number!',
            'primary_contact_number.digits' => 'Please enter valid primary number!',
            'secondary_contact_number.numeric' => 'Please enter whatsapp number!',
            'secondary_contact_number.digits' => 'Please enter valid whatsapp number!',
            'country_id.required' => 'Please select country!',
            'state_name.required' => "Please enter state name!",
            'state_id.required' => "Please select state name!",
            'city.required' => "Please enter city name!",
            'zipcode.required' => "Please enter zip code!",
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $is_primary = $request->is_primary != null ? 1 : 0;
        if($is_primary == 1) {
            Branch::where('distributor_id', $distributor_id)->update([
                'is_primary' => 0,
            ]);
        }

        $store_data = [
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'primary_contact_person' => $request->primary_contact_person,
            'secondary_contact_person' => $request->secondary_contact_person,
            'primary_contact_number' => $request->primary_contact_number,
            'secondary_contact_number' => $request->secondary_contact_number,
            'country_id' => $request->country_id,
            'city' => $request->city,
            'primary_email' => $request->primary_email,
            'secondary_email' => $request->secondary_email,
            'zipcode' => $request->zipcode,
            'is_primary' => $is_primary,
            'address' => $request->address,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'distributor_id' => $distributor_id,
        ];

        if($request->country_id == 101){
            $store_data['state_id'] = $request->state_id;
            $store_data['state_name'] = "";
        }else{
            $store_data['state_id'] = "";
            $store_data['state_name'] = $request->state_name;
        }

        $branch = Branch::create($store_data);

        return response()->json([
            'status' => 'SUCCESS',
            'message' => "Branch successfully added!",
            'data' => $branch,
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
        // Auth User
        $user = JWTAuth::parseToken()->authenticate();
        $branch = $this->findByExternalId($external_id);

        if (!$user || empty($branch)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $distributor_id = $user->distributor_id;
        // Check if salon has subscription or not
        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }

        $rules_arr = [
            'name' => ['required'],
            'primary_email' => ['email', Rule::unique('branches')->where(function ($query) use ($distributor_id, $branch) {
                return $query->where('distributor_id', $distributor_id)->where('id', '!=', $branch->id);
            })],
            'primary_contact_number' => ['required', 'numeric', 'digits:10', Rule::unique('branches')->where(function ($query) use ($distributor_id, $branch) {
                return $query->where('distributor_id', $distributor_id)->where('id', '!=', $branch->id);
            })],
            'secondary_contact_number' => ['numeric', 'digits:10'],
            'secondary_email' => ['email'],
            'country_id' => ['required'],
            'city' => ['required'],
            'zipcode' => ['required'],
        ];

        if($request->country_id == 101){
            $rules_arr['state_id'] = ['required'];
        }else{
            $rules_arr['state_name'] = ['required'];
        }

        // Validations
        $validator = Validator::make($request->all(), $rules_arr, [
            'name.required' => 'Please enter name!',
            'primary_email.email' => 'Please enter valid primary email!',
            'secondary_email.email' => 'Please enter valid secondary email!',
            'primary_contact_number.required' => 'Please enter primary number!',
            'primary_contact_number.numeric' => 'Please enter valid primary number!',
            'primary_contact_number.digits' => 'Please enter valid primary number!',
            'secondary_contact_number.numeric' => 'Please enter whatsapp number!',
            'secondary_contact_number.digits' => 'Please enter valid whatsapp number!',
            'country_id.required' => 'Please select country!',
            'state_name.required' => "Please enter state name!",
            'state_id.required' => "Please select state name!",
            'city.required' => "Please enter city name!",
            'zipcode.required' => "Please enter zip code!",
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        // Make branch primary if checkbox is checked
        $is_primary = $request->is_primary != null ? 1 : 0;
        if($is_primary == 1) {
            Branch::where('distributor_id', $branch->distributor_id)->update([
                'is_primary' => 0,
            ]);
        }

        // Prepare data to update
        $update_arr = [
            'name' => $request->name,
            'primary_contact_person' => $request->primary_contact_person,
            'secondary_contact_person' => $request->secondary_contact_person,
            'primary_contact_number' => $request->primary_contact_number,
            'secondary_contact_number' => $request->secondary_contact_number,
            'country_id' => $request->country_id,
            'city' => $request->city,
            'primary_email' => $request->primary_email,
            'secondary_email' => $request->secondary_email,
            'zipcode' => $request->zipcode,
            'is_primary' => $is_primary,
            'address' => $request->address,
            'updated_by' => $user->id,
        ];

        if($request->country_id == 101){
            $update_arr['state_id'] = $request->state_id;
            $update_arr['state_name'] = "";
        }else{
            $update_arr['state_id'] = "";
            $update_arr['state_name'] = $request->state_name;
        }

        $branch->fill($update_arr)->save();

        return response()->json([
            'status' => 'SUCCESS',
            'message' => "Branch successfully updated!",
            'data' => $branch,
        ]);
    }

    /**
     *  @return mixed
     *  $products
     */
    public function branchByDistributor(Request $request)
    {
        // Auth User
        $user = JWTAuth::parseToken()->authenticate();
        $distributor_id = $user->distributor_id;
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $name = $request->get('name');
		$paginate = $request->paginate ?? 0; 
		$branches = Branch::select('id', 'name')->where('distributor_id', $distributor_id);
        if(!empty($name)) {
            $branches->where('name', 'like', "%{$name}%");
        }/*  else {
            $branches = [];
            $message = "Please search by branch name!";
        } */
		if($paginate == 1) {
            $data = $branches->paginate();
            $count = count($data);
        } else { 
            $data = [];
            $data['data'] = $branches->get();
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
        /* $message = "No data found!";

        if(count($branches) > 0) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => $branches,
                'message' => '',
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'data' => [],
                'message' => $message,
            ]);
        } */
    }

    public function findByExternalId($external_id)
    {
        return Branch::where('external_id', $external_id)->first();
    }
}
