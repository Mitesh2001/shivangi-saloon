<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use JWTAuth;
use Ramsey\Uuid\Uuid;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\StockIncomeHistory;

use App\Helpers\Helper;

class VendorsController extends Controller
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
		$search_text = $request->search_text; 
		$paginate = $request->paginate ?? 0; 
		$distributor_id = $user->distributor_id;

        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }
		$vendors = Vendor::where('distributor_id','=',$distributor_id);//->get();
		if(!empty($search_text))
		$vendors->where(function ($q) use ($search_text) {
			$q->where('name', 'LIKE', "%" . $search_text . "%");
			$q->orWhere('gst_number', 'LIKE', "%" . $search_text . "%");
			$q->orWhere('primary_number', 'LIKE', "%" . $search_text . "%");
			$q->orWhere('primary_email', 'LIKE', "%" . $search_text . "%");
			$q->orWhere('secondary_number', 'LIKE', "%" . $search_text . "%");
			$q->orWhere('secondary_email', 'LIKE', "%" . $search_text . "%");
			$q->orWhere('contact_person', 'LIKE', "%" . $search_text . "%");
			$q->orWhere('contact_person_number', 'LIKE', "%" . $search_text . "%");
			$q->orWhere('contact_person_email', 'LIKE', "%" . $search_text . "%");
			$q->orWhere('city', 'LIKE', "%" . $search_text . "%");
			$q->orWhere('zipcode', 'LIKE', "%" . $search_text . "%");
		});
		if($paginate == 1) {
            $data = $vendors->paginate();
            $count = count($data);
        } else { 
            $data = [];
            $data['data'] = $vendors->get();
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
		
		$vendor = $this->findByExternalId($id);
		return response()->json([
            'status' => 'SUCCESS',
            'data' => $vendor
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
            'name' => ['required'],
            'primary_number' => ['digits:10', 'required', Rule::unique('vendors')->where(function ($query) use ($distributor_id) {
                return $query->where('distributor_id', $distributor_id);
            })],
            'secondary_number' => ['digits:10'],
            'primary_email' => ['email', Rule::unique('vendors')->where(function ($query) use ($distributor_id) {
                return $query->where('distributor_id', $distributor_id);
            })],
            'secondary_email' => ['email'],
            'contact_person_number' => ['digits:10'],
            'contact_person_email' => ['email'],
        ], [
            'name' => 'Please enter name',
            'primary_number.required' => 'Please enter primary number!',
            'primary_number.digits' => 'Please enter valid primary number!',
            'secondary_number.digits' => 'Please enter valid secondary number!',
            'primary_email.email' => 'Please enter valid primary email!',
            'secondary_email.email' => 'Please enter valid primary email!',
            'contact_person_number.digits' => 'Please enter valid contact person number!',
            'contact_person_email.email' => 'Please enter valid contact person email!',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
		$user_id = $user->id;
		$vendor = Vendor::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'gst_number' => $request->gst_number,
            'primary_number' => $request->primary_number,
            'secondary_number' => $request->secondary_number,
            'primary_email' => $request->primary_email,
            'secondary_email' => $request->secondary_email,
            'contact_person' => $request->contact_person,
            'contact_person_number' => $request->contact_person_number,
            'contact_person_email' => $request->contact_person_email,
            'city' => $request->city,
            'address' => $request->address,
            'zipcode' => $request->zipcode,
            'created_by' => $user_id,
            'updated_by' => $user_id,
            'distributor_id' => $distributor_id,
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $vendor,
            'message' => 'Vendor successfully added!'
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
		$vendor = $this->findByExternalId($id);
		
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
			'primary_email' => ['email', Rule::unique('vendors')->where(function ($query) use ($distributor_id, $vendor) {
				return $query->where('distributor_id', $distributor_id)->where('id', '!=', $vendor->id);
			})],
			'primary_number' => ['required', 'numeric', 'digits:10', Rule::unique('vendors')->where(function ($query) use ($distributor_id, $vendor) {
				return $query->where('distributor_id', $distributor_id)->where('id', '!=', $vendor->id);
			})],
            'secondary_number' => ['digits:10'],
            'secondary_email' => ['email'],
            'contact_person_number' => ['digits:10'],
            'contact_person_email' => ['email'],
        ], [
            'name' => 'Please enter name',
            'primary_number.required' => 'Please enter primary number!',
            'primary_number.digits' => 'Please enter valid primary number!',
            'secondary_number.digits' => 'Please enter valid secondary number!',
            'primary_email.email' => 'Please enter valid primary email!',
            'secondary_email.email' => 'Please enter valid primary email!',
            'contact_person_number.digits' => 'Please enter valid contact person number!',
            'contact_person_email.email' => 'Please enter valid contact person email!',
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
            'gst_number' => $request->gst_number,
            'primary_number' => $request->primary_number,
            'secondary_number' => $request->secondary_number,
            'primary_email' => $request->primary_email,
            'secondary_email' => $request->secondary_email,
            'contact_person' => $request->contact_person,
            'contact_person_number' => $request->contact_person_number,
            'contact_person_email' => $request->contact_person_email,
            'city' => $request->city,
            'address' => $request->address,
            'zipcode' => $request->zipcode,
            'updated_by' => $user_id,
            'distributor_id' => $distributor_id,
        ];

		$vendor->fill($update_data)->save();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $vendor,
            'message' => 'Vendor successfully updated!'
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
		$vendor = $this->findByExternalId($id);
		$vendor_count = StockIncomeHistory::where('vendor_id', $vendor->id)->count(); 
		if($vendor_count === 0) {
			$vendor->delete();
            return response()->json([
                'status' => 'SUCCESS',
				'message' => 'Vendor successfully deleted!'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Sorry this vendor is in use!",
            ]);
        }
        

        return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
    }
	
	public function findByExternalId($external_id)
    {
        return Vendor::where('external_id', $external_id)->first();
    }
}
