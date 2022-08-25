<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use JWTAuth;
use DB;
use Ramsey\Uuid\Uuid;
use App\Services\ClientNumber\ClientNumberService;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\Subscriptions;
use App\Models\Distributor;
use App\Models\Client;
use App\Models\Country;
use App\Models\State;
use App\Models\Contact;

class ClientsController extends Controller
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
        $basic_details = $request->basic_details ?? 0;

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

        // Birthday & anniversary cant be there (cause of diffrent formtat in date)
        $clients = Client::leftJoin('contacts', 'contacts.client_id', '=', 'clients.id');
        if($basic_details == 1) {

            /* if(empty($search_text)) {
                return response()->json([
                    'status' => 'FAIL',
                    'data' => [],
                    'message' => 'Please search by client name!',
                ]);
            } */
            $clients->select('clients.id', 'clients.name', 'contacts.email', 'contacts.primary_number', 'clients.gender', 'clients.address');
			if(!empty($search_text))
            $clients->where('clients.name', 'LIKE', "%" . $search_text . "%");

        } else {
            $clients->select('*');
			if(!empty($search_text))
            $clients->where(function ($qeury) use ($search_text) {
                $qeury->where('clients.name', 'LIKE', "%" . $search_text . "%");
                $qeury->orWhere('clients.city', 'LIKE', "%" . $search_text . "%");
                $qeury->orWhere('clients.zipcode', 'LIKE', "%" . $search_text . "%");
                $qeury->orWhere('contacts.email', 'LIKE', "%" . $search_text . "%");
                $qeury->orWhere('contacts.primary_number', 'LIKE', "%" . $search_text . "%");
                $qeury->orWhere('contacts.secondary_number', 'LIKE', "%" . $search_text . "%");
            });
        }
        $clients->where('clients.distributor_id', $user->distributor_id);
        $clients->addSelect(DB::raw('clients.external_id as external_id'));
        $clients->orderBy('clients.id', 'desc');

        if($paginate == 1) {
            $data = $clients->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $clients->get();
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

        $rules_arr = [
            'name' => ['required'],
            'email' => ['email', Rule::unique('contacts')->where(function ($query) use ($distributor_id) {
                return $query->where('distributor_id', $distributor_id);
            })],
            'primary_number' => ['required', 'numeric', 'digits:10', Rule::unique('contacts')->where(function ($query) use ($distributor_id) {
                return $query->where('distributor_id', $distributor_id);
            })],
            'secondary_number' => ['numeric', 'digits:10'],
            'country_id' => ['required'],
        ];

        if($request->country_id == 101){
            $rules_arr['state_id'] = ['required'];
        }else{
            $rules_arr['state_name'] = ['required'];
        }

        // Validations
        $validator = Validator::make($request->all(), $rules_arr, [
            'name.required' => 'Please enter name!',
            'email.email' => 'Please enter valid email!',
            'primary_number.required' => 'Please enter primary number!',
            'primary_number.numeric' => 'Please enter valid primary number!',
            'primary_number.digits' => 'Please enter valid primary number!',
            'secondary_number.numeric' => 'Please enter whatsapp number!',
            'secondary_number.digits' => 'Please enter valid whatsapp number!',
            'country_id.required' => 'Please select country!',
            'state_name.required' => "Please enter state name!",
            'state_id.required' => "Please select state name!",
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $store_data = [
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'date_of_birth' => $request->date_of_birth,
            'anniversary' => $request->anniversary,
            'address' => $request->address,
            'zipcode' => $request->zipcode,
            'city' => $request->city,
            'country_id' => $request->country_id,
            'distributor_id' => $distributor_id,
            'client_number' => app(ClientNumberService::class)->setNextClientNumber(),
            'client_type' => $request->client_type,
            'notes' => $request->notes,
            'gender' => $request->gender,
            'allow_notifications' => $request->allow_notifications ? 1 : 0,
        ];

        if($request->country_id == 101){
            $store_data['state_id'] = $request->state_id;
            $store_data['state_name'] = "";
        }else{
            $store_data['state_id'] = 0;
            $store_data['state_name'] = $request->state_name;
        }

        $client = Client::create($store_data);

        $contact = Contact::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'email' => $request->email,
            'primary_number' => $request->primary_number,
            'secondary_number' => $request->secondary_number,
            'client_id' => $client->id,
            'is_primary' => true,
            'distributor_id' => $distributor_id,
        ]);

        $store_data['email'] = $request->email;
        $store_data['primary_number'] = $request->primary_number;
        $store_data['secondary_number'] = $request->secondary_number;

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $store_data,
            'message' => 'Client successfully added!'
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

        $client = $this->findByExternalId($external_id);

        if(!empty($client)) {

            $rules_arr = [
                'name' => ['required'],
                'email' => ['email', Rule::unique('contacts')->where(function ($query) use ($distributor_id, $client) {
                    return $query->where('distributor_id', $distributor_id)->where('client_id', '!=', $client->id);
                })],
                'primary_number' => ['required', 'numeric', 'digits:10', Rule::unique('contacts')->where(function ($query) use ($distributor_id, $client) {
                    return $query->where('distributor_id', $distributor_id)->where('client_id', '!=', $client->id);
                })],
                'secondary_number' => ['numeric', 'digits:10'],
                'country_id' => ['required'],
            ];

            if($request->country_id == 101){
                $rules_arr['state_id'] = ['required'];
            }else{
                $rules_arr['state_name'] = ['required'];
            }

            // Validations
            $validator = Validator::make($request->all(), $rules_arr, [
                'name.required' => 'Please enter name!',
                'email.email' => 'Please enter email!',
                'primary_number.required' => 'Please enter primary number!',
                'primary_number.numeric' => 'Please enter valid primary number!',
                'primary_number.digits' => 'Please enter valid primary number!',
                'secondary_number.numeric' => 'Please enter whatsapp number!',
                'secondary_number.digits' => 'Please enter valid whatsapp number!',
                'country_id.required' => 'Please select country!',
                'state_name.required' => "Please enter state name!",
                'state_id.required' => "Please select state name!",
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
                'date_of_birth' => $request->date_of_birth,
                'anniversary' => $request->anniversary,
                'address' => $request->address,
                'zipcode' => $request->zipcode,
                'city' => $request->city,
                'country_id' => $request->country_id,
                'distributor_id' => $distributor_id,
                'client_number' => app(ClientNumberService::class)->setNextClientNumber(),
                'client_type' => $request->client_type,
                'notes' => $request->notes,
                'gender' => $request->gender,
                'allow_notifications' => $request->allow_notifications ? 1 : 0,
            ];

            if($request->country_id == 101){
                $update_data['state_id'] = $request->state_id;
                $update_data['state_name'] = "";
            }else{
                $update_data['state_id'] = 0;
                $update_data['state_name'] = $request->state_name;
            }

            $client->fill($update_data)->save();

            $client->primaryContact->fill([
                'name' => $request->name,
                'email' => $request->email,
                'primary_number' => $request->primary_number,
                'secondary_number' => $request->secondary_number,
                'client_id' => $client->id,
                'is_primary' => true
            ])->save();

            $update_data['email'] = $request->email;
            $update_data['primary_number'] = $request->primary_number;
            $update_data['secondary_number'] = $request->secondary_number;

            return response()->json([
                'status' => 'SUCCESS',
                'data' => $update_data,
                'message' => 'Client successfully updated!'
            ]);

        } else {
            if(Helper::allowViewOnly($user->distributor_id)) {
                return response()->json([
                    'status' => 'FAIL',
                    'message' => "Client not found!"
                ]);
            }
        }
    }

    public function findByExternalId($external_id)
    {
        return Client::where('external_id', $external_id)->first();
    }

    public function storeBasicDetails(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $distributor_id = $user->distributor_id;

        $rules_arr = [
            'name' => ['required'],
            'email' => ['email', Rule::unique('contacts')->where(function ($query) use ($distributor_id) {
                return $query->where('distributor_id', $distributor_id);
            })],
            'primary_number' => ['required', 'numeric', 'digits:10', Rule::unique('contacts')->where(function ($query) use ($distributor_id) {
                return $query->where('distributor_id', $distributor_id);
            })],
            //'address' => ['required'],
        ];

        // Validations
        $validator = Validator::make($request->all(), $rules_arr, [
            'name.required' => 'Please enter name!',
            'email.email' => 'Please enter email!',
            'primary_number.required' => 'Please enter primary number!',
            'primary_number.numeric' => 'Please enter valid primary number!',
            'primary_number.digits' => 'Please enter valid primary number!',
            //'address.required' => "Please enter address!",
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $client = Client::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'address' => $request->address,
            'company_name' => "",
            'user_id' =>  $user->id, // Problem
            'industry_id' => 0, // Problem
            'client_number' => app(ClientNumberService::class)->setNextClientNumber(),
            'distributor_id' => $distributor_id,
            'gender' => $request->gender,
        ]);

        $contact = Contact::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'email' => $request->email,
            'primary_number' => $request->primary_number,
            'client_id' => $client->id,
            'is_primary' => true,
            'distributor_id' => $distributor_id,
        ]);

        return response()->json([
            'status' => "SUCCESS",
            'message' => "Client successfully added!",
            'data' => [
                'id' => $client->id,
                'name' => $request->name ?? "",
                'email' => $request->email ?? "",
                'contact_number' => $request->primary_number ?? "",
                'address' => $client->address ?? "",
                'gender' => $client->gender ?? "",
            ]
        ]);
    }
}
