<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use DB;
use Auth;
use JWTAuth;
use Image;
use Datatables;

use Ramsey\Uuid\Uuid;
use App\Helpers\Helper;
use App\Models\Distributor;

use App\Models\Lead;
use App\Models\User;
use App\Models\Client;
use App\Models\Branch;
use App\Http\Requests;
use App\Models\Status;
use App\Models\Setting;
use App\Models\EnquiryType;
use App\Models\Enquiry;


class InquiryController extends Controller
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
        $followup_date = $request->followup_date;
        $paginate = $request->paginate ?? 0;

        $enquiries = Enquiry::with(['getDistributor', 'client', 'getBranch', 'status', 'user'])->select('*');

        $distributor_id = $user->distributor_id;
        $enquiries->where('enquiries.distributor_id', $distributor_id);

        if(!empty($followup_date)) {
            $enquiries->whereDate('enquiries.date_to_follow', $followup_date);
        }
		if(!empty($search_text))
        $enquiries->where(function ($enquiries) use ($search_text) {
            $enquiries->where(function ($qeury) use ($search_text) {
                $qeury->where('enquiries.client_name', 'LIKE', "%" . $search_text . "%");
                $qeury->orWhere('enquiries.contact_number', 'LIKE', "%" . $search_text . "%");
            });
        });

        if($user->roles[0]->name == "employee") {
            $enquiries->where('enquiries.branch_id', $user->branch_id);
        }

        $enquiries = $enquiries->addSelect('enquiries.id as id, enquiries.external_id as external_id');
        $enquiries = $enquiries->orderBy('enquiries.id', 'desc');

        if($paginate == 1) {
            $data = $enquiries->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $enquiries->get();
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
            'client_id' => ['required'],
            'email' => ['email'],
            'contact_number' => ['required', 'numeric', 'digits:10'],
            //'address' => ['required'],
            'branch_id' => ['required'],
            //'enquiry_for' => ['required'],
            //'enquiry_type' => ['required'],
            //'enquiry_response' => ['required'],
            //'date_to_follow' => ['required', 'date_format:Y-m-d'],
            //'source_of_enquiry' => ['required'],
            //'user_assigned_id' => ['required'],
            //'status_id' => ['required'],
        ], [
            'client_id.required' => 'Please select client!',
            'email.email' => 'Please enter valid email!',
            'contact_number.required' => 'Please enter contact number!',
            'contact_number.numeric' => 'Please enter valid contact number!',
            'contact_number.digits' => 'Please enter valid contact number!',
            'address.email' => 'Please enter address!',
            'branch_id.required' => "Please select branch!",
            'enquiry_for.required' => "Please select services inquiry for!",
            'enquiry_type.required' => "Please select iinquiry type!",
            'enquiry_response.required' => "Please enter iinquiry response!",
            'date_to_follow.required' => "Please select date to follow!",
            'date_to_follow.date_format' => "Please enter valid date format (Y-m-d)!",
            'source_of_enquiry.required' => "Please enter source of inquiry!",
            'user_assigned_id.required' => "Please select lead representative!",
            'status_id.required' => "Please select inquiry status!",
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $client = Client::where('id', $request->client_id)->first();
        $client_id = $client->id;
        $client_name = $client->name;

        $enquiry = Enquiry::create([
            'external_id' => Uuid::uuid4()->toString(),
            'client_id' => $client_id,
            'client_name' => $client_name,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'address' => $request->address,
            'description' => $request->description,
            'branch_id' => $request->branch_id,
            'enquiry_for' => $request->enquiry_for,
            'enquiry_type' => $request->enquiry_type,
            'enquiry_response' => $request->enquiry_response,
            'date_to_follow' => $request->date_to_follow,
            'source_of_enquiry' => $request->source_of_enquiry,
            'user_assigned_id' => $request->user_assigned_id,
            'status_id' => $request->status_id,
            'created_by' => $user->id,
            'distributor_id' => $distributor_id,
            'gender' => $request->gender,
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $enquiry,
            'message' => 'Enquiry successfully added!'
        ]);
    }

    public function updateStatus(Request $request)
    {
        // Auth User
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $inquiry_id = $request->inquiry_id;
        $status_id = $request->status_id;

        // Validations
        $validator = Validator::make($request->all(), [
            'status_id' => ['required'],
        ], [
            'status_id.required' => 'Please select status!',
        ]);

        $enquiry = Enquiry::find($inquiry_id);

        if(empty($enquiry)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        if(Helper::allowViewOnly($enquiry->distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }

        $res = $enquiry->fill([
            'status_id' => $status_id,
        ])->save();

        return response()->json([
            'status' => true,
            'message' => "inquiry status updated successfully!"
        ]);
    }
}
