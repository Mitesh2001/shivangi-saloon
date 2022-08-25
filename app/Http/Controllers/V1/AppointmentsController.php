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

use App\Models\User;
use App\Models\Client;
use App\Models\Distributor;
use App\Models\Enquiry;
use App\Models\Branch;
use App\Models\Status;
use App\Models\Appointment;
use App\Models\AppointmentImages;
use App\Models\Product;

class AppointmentsController extends Controller
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
        $appointment_date = $request->appointment_date;
        $paginate = $request->paginate ?? 0;

        $appointments = Appointment::with(['getDistributor', 'client', 'user', 'getStatus', 'getBranch','services'])->whereHas('client', function ($q) {
                $q->whereNull('deleted_at');
            });//->select('appointments.*')->join('clients', 'clients.id', '=', 'appointments.client_id')->where('clients.deleted_at', null)
        $appointments->where('appointments.distributor_id', $user->distributor_id);

        if(!empty($appointment_date)) {
            $appointments->whereDate('appointments.date', $appointment_date);
        }
		if(!empty($search_text))
        $appointments->where(function ($appointments) use ($search_text) {
            $appointments->where(function ($qeury) use ($search_text) {
                $qeury->whereHas('client', function ($q) use ($search_text){
                $q->where('clients.name', 'LIKE', "%" . $search_text . "%");
            });//where('clients.name', 'LIKE', "%" . $search_text . "%");
                $qeury->orWhere('contact_number', 'LIKE', "%" . $search_text . "%");
            });
        });

        if($user->roles[0]->name == "employee") {
            $appointments->where('branch_id', $user->branch_id);
        }

        $appointments->orderBy('id', 'desc');

        if($paginate == 1) {
            $data = $appointments->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $appointments->get();
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

        $distributor_id = $user->distributor_id;
        // Check if salon has subscription or not
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
            //'user_assigned_id' => ['required'],
            //'appointment_for' => ['required'],
            //'status_id' => ['required'],
            //'date' => ['required', 'date_format:Y-m-d'],
            //'start_at' => ['required', 'date_format:h:i a'],
            //'end_at' => ['required','date_format:h:i a', 'after:start_at'],
        ], [
            'email.email' => 'Please enter email!',
            'contact_number.required' => 'Please enter contact number!',
            'contact_number.numeric' => 'Please enter valid contact number!',
            'contact_number.digits' => 'Please enter valid contact number!',
            //'address' => 'Please enter address!',
            'branch_id.required' => "Please select branch!",
            'user_assigned_id.required' => "Please select appointment representative!",
            'appointment_for.required' => "Please select services appointment for!",
            'status_id.required' => "Please select status!",
            'date.required' => "Please select date!",
            'date.date_format' => "Please enter date in Y-m-d format!",
            'start_at.required' => "Please select appointment start time!",
            'start_at.date_format' => "Invalid appointment start time!",
            'end_at.required' => "Please select appointment end time!",
            'end_at.date_format' => "Invalid appointment end time!",
            'end_at.after' => "Appointmetn start time cannot be greater then end time!",
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        // Update status of enquiry if converted from inquiry
        $inquiry_id = $request->inquiry_id;
        $enquiry = Enquiry::find($inquiry_id);
        if(!empty($enquiry)) {
            $enquiry->stage = "appointment";
            $enquiry->update();
        }

        $client = Client::where('id', $request->client_id)->first();
        $client_id = $client->id;
        $sms_number = $request->contact_number;

        $appointment = Appointment::create([
            "external_id" => Uuid::uuid4()->toString(),
            'client_id' => $client_id,
            'gender' => $request->gender,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'address' => $request->address,
            'branch_id' => $request->branch_id,
            'user_id' => $request->user_assigned_id,
            "appointment_for" => "",
            'status_id' => $request->status_id,
            'source_type_string' => $request->source_type_string,
            'date' => $request->date,
            "start_at" => date('h:i:s a',strtotime($request->start_at)),
            "end_at" => date('h:i:s a', strtotime($request->end_at)),
            "description" => $request->description,
            'created_by' => $user->id,
            'distributor_id' => $distributor_id,
            'inquiry_id' => $inquiry_id ?? 0,
        ]);
        $appointment->services()->sync($request->appointment_for);

        $appointment['appointment_for'] = Product::whereIn('id', $request->appointment_for)->get();

        // Send SMS for appointment booking
        Helper::sendAppointmentSMS($client, $sms_number, $appointment);

        $index_url = $request->index_url;

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $appointment,
            'message' => 'Appointment successfully added!'
        ]);
    }


    public function reschedule(Request $request)
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

        // Validations
        $validator = Validator::make($request->all(), [
            'user_assigned_id' => ['required'],
            'date' => ['required', 'date_format:Y-m-d'],
            'start_at' => ['required', 'date_format:h:i a'],
            'end_at' => ['required','date_format:h:i a', 'after:start_at'],
        ], [
            'user_assigned_id.required' => "Please select appointment representative!",
            'date.required' => "Please select date!",
            'date.date_format' => "Please enter date in Y-m-d format!",
            'start_at.required' => "Please select appointment start time!",
            'start_at.date_format' => "Please enter start time in h:i a format!",
            'end_at.required' => "Please select appointment end time!",
            'end_at.date_format' => "Please enter end time in h:i a format!",
            'end_at.after' => "Appointmetn start time cannot be greater or equal end time!",
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $id = $request->appointment_id;
        $appointment = Appointment::with(['getDistributor', 'client', 'user', 'getStatus', 'getBranch','services'])->find($id);

        $appointment->update([
            'date' => date('Y-m-d', strtotime($request->date)),
            'start_at' => date('h:i a', strtotime($request->start_at)),
            'end_at' => date('h:i a', strtotime($request->end_at)),
            'user_id' => $request->user_assigned_id,
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $appointment,
            'message' => 'Appointment successfully resheduled!'
        ]);
    }

    /**
     *  Update Status
     */
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
		
		if(Helper::allowViewOnly($user->distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }

        $appointment_id = $request->appointment_id;
        $status = $request->status_id;

        // Validations
        $validator = Validator::make($request->all(), [
            'status_id' => ['required'],
        ], [
            'status_id.required' => 'Please select status!',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
		
		$appointment = Appointment::with(['getDistributor', 'client', 'user', 'getStatus', 'getBranch','services'])->find($appointment_id);

        if(empty($appointment)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $appointment->fill([
            'status_id' => $request->status_id,
        ])->save();

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Appointment status successfully updated!'
        ]);
    }

    // Store before after images for appointments
    public function storeImages(Request $request)
    {
        // Auth User
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $appointment_id = $request->appointment_id;
        $appointment = Appointment::find($appointment_id);

        if(empty($appointment)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "No appointment found!"
            ]);
        }

        if(Helper::allowViewOnly($appointment->distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }

        $path_appointments_images = 'storage/assets/appointments/';

        $images = $request->images;

        $x = 0;
        foreach($images as $image) {

            // Image name without extension
            $imageName = rand(100000, 900000) ."_". time();
            $uploaded_image = Helper::resizeImageFromBase64($image, $imageName, $path_appointments_images);
            $data[$x]['external_id'] = Uuid::uuid4()->toString();
            $data[$x]['image'] = $uploaded_image;
            $data[$x]['appointment_id'] = $appointment_id;
            $data[$x]['created_by'] = $user->id;
            $x++;
        }

        AppointmentImages::insert($data);

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Images successfully uploaded!'
        ]);
    }

    public function listImages(Request $request)
    {
        $client_id = $request->client_id;

        $appointments = Appointment::with(['getImages', 'services'])
        ->has('getImages')
        ->where('client_id', $client_id)
        ->get();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $appointments,
        ]);
    }

    public function deleteImage(Request $request)
    {
        // Auth User
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $image_id = $request->image_id;
        $appointment_image = AppointmentImages::find($image_id);

        if(empty($appointment_image)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Image not found!",
            ]);
        }

        if(Helper::allowViewOnly($user->distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }

        $appointment_image->delete();

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Images successfully deleted!'
        ]);
    }


    public function updateImage(Request $request)
    {
        // Auth User
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $image_id = $request->image_id;
        $new_image = $request->new_image;

        $old_record = AppointmentImages::find($image_id);

        if(empty($old_record)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }
        if(Helper::allowViewOnly($user->distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }

        $old_image = $old_record->image;
        $appointment_id = $old_record->appointment_id;
        $path_appointments_images = 'storage/assets/appointments/';

        // Soft delete old image record
        $old_record->delete();

        // Upload Image
        // Image name without extension
        $imageName = rand(100000, 900000) ."_". time();
        $uploaded_image = Helper::resizeImageFromBase64($new_image, $imageName, $path_appointments_images);

        // Add new Image
        $new_entrie = AppointmentImages::create([
            'external_id' => Uuid::uuid4()->toString(),
            'image' => $uploaded_image,
            'appointment_id' => $appointment_id,
            'created_by' => $user->id,
        ]);

        return response()->json([
            'new_image_id' => $new_entrie->id,
            'status' => "SUCCESS",
            'image_src' => asset($uploaded_image),
            'message' => "Image successfully update!",
        ]);
    }
}
