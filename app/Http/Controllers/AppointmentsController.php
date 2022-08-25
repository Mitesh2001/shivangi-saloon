<?php

namespace App\Http\Controllers; 
use Datatables;
use DB;
use Auth; 
use App\Models\User;
use App\Models\Client;
use App\Http\Requests;
use App\Models\Branch; 
use App\Models\Status; 
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Requests\Appointment\CreateAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentFollowUpRequest;
use Ramsey\Uuid\Uuid;

use App\Helpers\Helper;
use App\Models\Enquiry;
use App\Models\Distributor;
use App\Models\AppointmentImages;

use Image; // To resize image 
use Validator;

class AppointmentsController extends Controller
{
       
    public function __construct()
    {
        $this->middleware('permission:appointment-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:appointment-create', ['only' => ['create','store']]);
		$this->middleware('permission:appointment-update', ['only' => ['edit','update']]); 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['statuses'] = Status::all(); 
        $data['branches'] = Branch::all(); 
        $data['statuses_pluck'] = Status::pluck('title', 'id');  
        $data['is_system_user'] = Helper::is_system_user();  
        $data['distributors'] = Distributor::all();

        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        } 

        return view('appointments.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Redirected from
        $from_calendar = $request->get('caledar'); 
        if($from_calendar == 1) {
            $data['back'] = route('appointments.calendar'); 
        }  else {
            $data['back'] = route('appointments.index');
        }

        $data['users'] = User::all();
        // $data['client'] = Client::whereExternalId($client_external_id);
        $data['statuses'] = Status::pluck('title', 'id'); 
        $data['branch'] = Branch::pluck('name', 'id')->toArray();  
        $data['is_system_user'] = Helper::getDistributorId();
        
        $client = $request->get('client') ?? 0;
        if($client !== 0) {
            $data['client'] = Client::where('external_id', $request->get('client'))->first();
            $data['selected_distributor'] = $data['client']->getDistributor; 
            $data['contact_number'] = $data['client']->primaryContact->primary_number;
            $data['email'] = $data['client']->primaryContact->email;
            $data['address'] = $data['client']->address;
        }

        // convert to appointment 
        $inquiry_id = $request->get('inquiry_id');

        if($inquiry_id !== null) {
            $inquiry = Enquiry::where('external_id', $inquiry_id)->firstOrFail();
            $data['client'] = Client::findOrFail($inquiry->client_id);
            $data['selected_distributor'] = $data['client']->getDistributor;
            $data['selected_branch'] = $inquiry->getBranch; 
            $data['contact_number'] = $inquiry->contact_number;
            $data['email'] = $inquiry->email;
            $data['address'] = $inquiry->address;
            $data['description'] = $inquiry->description;
            $data['inquiry_id'] = $inquiry->id;
            $data['source'] = $inquiry->source_of_enquiry;

            $data['back'] = route('leads.index');
        } 

        $status = Status::where('title', "pending")->first();
        $data['default_status'] = $status->id;
  
        return view('appointments.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAppointmentRequest $request)
    { 
        $inquiry_id = $request->inquiry_id;
        
        $enquiry = Enquiry::find($inquiry_id);
        if(!empty($enquiry)) {
            $enquiry->stage = "appointment";
            $enquiry->update();
        } 
        
        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;
        }

        if(Helper::allowViewOnly($distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        $user_id = Auth::id(); 
        $client = Client::where('id', $request->client_external_id)->first();
        $sms_number = $request->contact_number;
        
        // $appointment = Appointment::find(52); 
          
        $client_id = $client->id;   
  
        $appointment = Appointment::create([
            "external_id" => Uuid::uuid4()->toString(),
            "appointment_for" => "",
            "description" => $request->description,
            'source_type_string' => $request->source_type_string,
            'status_id' => $request->status_id ?? 0,
            'branch_id' => $request->branch_id,
            'user_id' => $request->user_assigned_id, 
            'client_id' => $client_id,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'address' => $request->address,
            "start_at" => date('h:i:s a',strtotime($request->start_at)),
            "end_at" => date('h:i:s a', strtotime($request->end_at)),
            'date' => $request->date,
            'created_by' => $user_id,
            'distributor_id' => $distributor_id,
            'inquiry_id' => $inquiry_id ?? 0,
            'gender' => $request->gender,
        ]);
        $appointment->services()->sync($request->appointment_for); 

        // Send SMS for appointment booking
        Helper::sendAppointmentSMS($client, $sms_number, $appointment);

        $index_url = $request->index_url;

        Session()->flash('success', __('Appointment successfully added!'));
        return redirect($index_url);
        // return redirect()->route('appointments.index');
    }
    
    /**
     *  Return data for index page datatable
     *
     * @return \Illuminate\Http\Response
     */
    public function allData(Request $request)
    {    
        $user = Auth::user();
        $appointments = Appointment::with(['getDistributor'])->select('appointments.*')->join('clients', 'clients.id', '=', 'appointments.client_id')->where('clients.deleted_at', null);  
        $distributor_id = Helper::getDistributorId();
		
		if(isset($request->fromdate) && $request->fromdate!=''){
		$fromdate = $request->fromdate;
		$appointments->whereRaw('date(appointments.date) >= "'.date("Y-m-d",strtotime($fromdate)).'"');
		}
		if(isset($request->todate) && $request->todate!=''){
		$todate = $request->todate;
		$appointments->whereRaw('date(appointments.date) <= "'.date("Y-m-d",strtotime($todate)).'"');
		}

        if($distributor_id != 0) { // Check if distributor
            $appointments->where('appointments.distributor_id', $distributor_id);
        } 

        if($user->roles[0]->name == "employee") {
            $appointments->where('branch_id', $user->branch_id);
        } 

        $appointments = $appointments->orderBy('date', 'desc')->orderBy('start_at', 'desc')->get(); 
        
        return Datatables::of($appointments)
            ->addColumn('client_name', function ($appointments) {
                 return $appointments->client->name;
            })
            ->addColumn('distributor', function ($appointments) {
                return  $appointments->getDistributor->name ?? "";
            }) 
            ->addColumn('contact', function ($appointments) {
                 return $appointments->contact_number;
            })
            ->addColumn('email', function ($appointments) {
                 return $appointments->email; 
            }) 
            ->addColumn('appointment_for', function ($appointments) {
                $services = $appointments->services->pluck('name')->toArray(); 
                 return implode(', ',$services); 
            })
            ->addColumn('branch', function ($appointments) {
                 return $appointments->getBranch->name ?? ""; 
            })
            ->addColumn('assigned_user', function ($appointments) { 
                 $name = isset($appointments->user->first_name) ? $appointments->user->first_name : '';
                 $name .= isset($appointments->user->last_name) ? $appointments->user->last_name : "";
                 return $name;
            })
            ->addColumn('date', function ($appointments) {
                // return date('d-m-Y', strtotime($appointments->date));
                return $appointments->date;
            })  
            ->addColumn('start_at', function ($appointments) {
                return date('h:i a', strtotime($appointments->start_at));
            })  
            ->addColumn('end_at', function ($appointments) {
                return date('h:i a', strtotime($appointments->end_at));
            })  
            ->addColumn('status', function ($appointments) {
                if($appointments->getStatus)
                    return '<span class="label label-inline label-lg font-weight-bolder" style="background-color:'. $appointments->getStatus->color .';color:white">'.$appointments->getStatus->title.'</span>'; 
                else 
                    return '';
                // return $appointments->status->title; 
            }) 
            ->addColumn('action', function ($appointments) {
                $html = ""; 
                if(\Entrust::can('appointment-update')) {
                    $html .= '<div class="d-flex">';
                        if(!Helper::allowViewOnly($appointments->distributor_id))
                        $html .= '<a href="#" class="btn btn-link edit-in-modal" data-toggle="modal" data-appointment-id="'.$appointments->external_id.'" data-target="#edit-appointment-modal"><i data-appointment-id="'.$appointments->external_id.'" data-toggle="tooltip" title="Edit Appointment" class="flaticon2-pen text-primary text-hover-primary"></i></a>';
                    $html .= '<a href="#" class="btn btn-link view-in-modal" data-toggle="modal" data-appointment-id="'.$appointments->external_id.'" data-target="#view-enquiry-modal"><i class="flaticon-eye text-primary text-hover-primary" data-appointment-id="'.$appointments->external_id.'"  data-toggle="tooltip" title="View Details"></i></a>';

                    if($appointments->stage != "Order") {
                        $html .= '<a href="'.route('orders.create').'?client_id='. encrypt($appointments->client_id) .'&appointment_id='.encrypt($appointments->id).'" class="btn btn-link" title="Create Order"><i class="flaticon2-copy text-primary text-hover-primary" data-enquiry-id="'.$appointments->external_id.'" data-toggle="tooltip" title="Create Order"></i></a>';
                    }

                    $html .= '</div>';
                }  
                return $html;
            })
            ->rawColumns(['client_name','distributor' ,'contact', 'email', 'appointment_for', 'branch', 'assigned_user', 'date', 'start_at', 'end_at', 'status', 'action'])
            ->make(true); 
    }

    public function todaysAppointments()
    {
        $distributor_id = Helper::getDistributorId();
        $appointments = Appointment::getTodays($distributor_id);

        return Datatables::of($appointments)
            ->addColumn('client_name', function ($appointments) {
                 return $appointments->client->name;
            })
            ->addColumn('distributor', function ($branch) {
                return  $branch->getDistributor->name ?? "";
            }) 
            ->addColumn('contact', function ($appointments) {
                 return $appointments->client->primaryContact->primary_number;
            })
            ->addColumn('email', function ($appointments) {
                 return $appointments->client->primaryContact->email; 
            }) 
            ->addColumn('appointment_for', function ($appointments) {
                $services = $appointments->services->pluck('name')->toArray(); 
                 return implode(', ',$services); 
            })
            ->addColumn('branch', function ($appointments) {
                 return $appointments->getBranch->name ?? ""; 
            })
            ->addColumn('assigned_user', function ($appointments) { 
                 $name = isset($appointments->user->first_name) ? $appointments->user->first_name : '';
                 $name .= isset($appointments->user->last_name) ? $appointments->user->last_name : "";
                 return $name;
            })
            ->addColumn('date', function ($appointments) {
                return date('d-m-Y', strtotime($appointments->date));
            })  
            ->addColumn('start_at', function ($appointments) {
                return date('h:i a', strtotime($appointments->start_at));
            })  
            ->addColumn('end_at', function ($appointments) {
                return date('h:i a', strtotime($appointments->end_at));
            })  
            ->addColumn('status', function ($appointments) {
                if($appointments->getStatus)
                    return '<span class="label label-inline label-lg font-weight-bolder" style="background-color:'. $appointments->getStatus->color .';color:white">'.$appointments->getStatus->title.'</span>'; 
                else 
                    return '';
                // return $appointments->status->title; 
            }) 
            ->addColumn('action', function ($appointments) {
                $html = ""; 
                if(\Entrust::can('appointment-update')) {
                    $html .= '<div class="d-flex">';
                        if(!Helper::allowViewOnly($appointments->distributor_id))
                        $html .= '<a href="#" class="btn btn-link edit-in-modal" data-toggle="modal" data-appointment-id="'.$appointments->external_id.'" data-target="#edit-appointment-modal"><i data-appointment-id="'.$appointments->external_id.'" data-toggle="tooltip" title="Edit Appointment" class="flaticon2-pen text-primary text-hover-primary"></i></a>';
                    $html .= '<a href="#" class="btn btn-link view-in-modal" data-toggle="modal" data-appointment-id="'.$appointments->external_id.'" data-target="#view-enquiry-modal"><i class="flaticon-eye text-primary text-hover-primary" data-appointment-id="'.$appointments->external_id.'"  data-toggle="tooltip" title="View Details"></i></a>';
                    $html .= '</div>';
                } 
                return $html;
            })
            ->rawColumns(['client_name','distributor' ,'contact', 'email', 'appointment_for', 'branch', 'assigned_user', 'date', 'start_at', 'end_at', 'status', 'action'])
            ->make(true); 
    }
    

    public function findById(Request $request)
    {
        $external_id = $request->external_id;
        $appointment = Appointment::where('external_id', $external_id)->first();
        $services = $appointment->services->pluck('name')->toArray();
        $appointmnet_services = implode(', ',$services);
  
        $service_categories = [];
        foreach($appointment->services as $service) {
            foreach($service->categories as $category) {
                array_push($service_categories, $category->name);
            } 
        }

        $service_categories = implode(', ', $service_categories);

        $name = isset($appointment->user->first_name) ? $appointment->user->first_name . " " : '';
        $name .= isset($appointment->user->last_name) ? $appointment->user->last_name : "";

        $status_tag = "";
        if(isset($appointment->getStatus->color)) {
            $status_tag = '<span class="label label-inline label-lg font-weight-bolder" style="background-color:'. $appointment->getStatus->color .';color:white">'.$appointment->getStatus->title.'</span>';
        }

        
        $arr = [
            'id' => $appointment->id,
            'external_id' => $appointment->external_id,
            'client_name' => $appointment->client->name,
            'gender' => $appointment->gender,
            'client_type' => $appointment->client->client_type,
            'client_notes' => $appointment->client->notes,
            'client_id' => $appointment->client->id,
            'contact_number' => $appointment->contact_number,
            'email' => $appointment->email,
            'address' => $appointment->address,
            'service_categories' => $service_categories,
            'appointment_for' => $appointmnet_services,
            'appointment_source' => $appointment->source_type_string,
            'representative_id' => $appointment->user_id,
            'representative' => $name,
            'status' => $appointment->status_id,
            'status_tag' => $status_tag,
            'branch' => $appointment->branch_id,
            'branch_name' => $appointment->getBranch->name,
            'date' => date('d-m-Y',strtotime($appointment->date)),
            'date_fomatted' => date('Y-m-d',strtotime($appointment->date)),
            'start_at' => date('h:i a',strtotime($appointment->start_at)),
            'end_at' => date('h:i a',strtotime($appointment->end_at)),
            'description' => $appointment->description ?? "",
            'distributor' => $appointment->getDistributor->name ?? "",
            'distributor_id' => $appointment->distributor_id,
        ];
 
        return response()->json($arr);
    }

    public function reschedule(Request $request)
    {
        $external_id = $request->appointment_id;
        $appointment = Appointment::where('external_id', $external_id)->first(); 

        if(Helper::allowViewOnly($appointment->distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }
         
        $appointment->fill([
            'date' => date('Y-m-d', strtotime($request->date)),
            'start_at' => date('h:i a', strtotime($request->start_at)),
            'end_at' => date('h:i a', strtotime($request->end_at)),
            'user_id' => $request->user_assigned_id,
            // 'status_id' => $request->status_id,
        ])->save();

        Session()->flash('success', __('Appointment successfully updated!'));
        return redirect()->route('appointments.index');
    }

    /**
     *  Update Status
     */
    public function updateStatus(Request $request) {

        $appointment_id = $request->appointment_id;
        $status = $request->status_id;

        $appointment = Appointment::find($appointment_id); 

        if(Helper::allowViewOnly($appointment->distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        $appointment->fill([ 
            'status_id' => $request->status_id,
        ])->save();
 
        return redirect()->back()->with('success', 'Appointment status successfully updated!');
    }


    /**
     *  Calendar view
     *  
     */
 
    public function calendar(Request $request)
    {  
        $filter_distributor = $request->get('distributor_id');
        $filter_branch = $request->get('branch_id');
        $filter_status = $request->get('status_id');
 
        if(!empty($filter_distributor)) {
            $data['selected_distributor'] = Distributor::where('external_id', $filter_distributor)->first();  
        }  
        if(!empty($filter_branch)) {
            $data['selected_branch'] = Branch::where('external_id', $filter_branch)->first(); 
        } 
        if(!empty($filter_status)) {
            $data['selected_status'] = Status::where('external_id', $filter_status)->first(); 
        }  

        $data['statuses'] = Status::all(); 
        $data['branches'] = Branch::all(); 
        $data['is_system_user'] = Helper::getDistributorId();
        $data['distributor_id'] = Helper::getDistributorId();

        if($data['distributor_id'] != 0) { 
            $data['distributor_data'] = Distributor::find($data['distributor_id']);
            $distributor_id = $data['distributor_data']->id;
        } else {
            $distributor_id = $data['selected_distributor']->id ?? 0;
        }
         
        if($distributor_id != 0)
        {
            $employees = User::where('distributor_id', $distributor_id);

            if(isset($data['selected_branch'])) {
                $employees->where('branch_id', $data['selected_branch']->id);
            }
            $employees = $employees->get();
        } else {
            $employees = [];
        }
        
        $employees_arr = [];
        foreach($employees as $employee) {
            $employee_fullname = $employee->first_name . " " ?? "";
            $employee_fullname .= $employee->last_name ?? "";
            array_push($employees_arr, [
                'title' => $employee_fullname,
                'id' => $employee->id,
            ]);
        }
        $data['employees'] = json_encode($employees_arr); 

        if(!empty($filter_branch)) {
            $employees->where('branch_id', $filter_branch);
        } 

        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        } 
         
        return view('appointments.calendar_view')->with($data);
    }
  
    public function calendarData(Request $request)
    {
        $user = Auth::user(); 
        $start_date = date('Y-m-d', strtotime($request->get('start')));
        $end_date = date('Y-m-d', strtotime($request->get('end')));

        $appointments = Appointment::with(['client', 'getStatus']);
        $appointments->whereDate('date', '>=', $start_date);
        $appointments->whereDate('date', '<=', $end_date);
        
        $distributor_id = Helper::getDistributorId();
        $branch_id = $request->get('branch_id');
        $status_id = $request->get('status_id'); 

        if($distributor_id == 0) { // Check if distributor
            $external_distributor_id = $request->get('distributor_id');  
            $distributor = Distributor::where('external_id', $external_distributor_id)->firstOrFail(); 
            $appointments->where('distributor_id', $distributor->id);
        } else {
            $appointments->where('distributor_id', $distributor_id);
        } 
        
        if(!empty($branch_id)) {
            $branch = Branch::where('external_id', $branch_id)->firstOrFail();
            $appointments->where('branch_id', $branch->id);
        }
        
        if(!empty($status_id)) { 
            $status = Status::where('external_id', $status_id)->firstOrFail();
            $appointments->where('status_id', $status->id);
        }

        if($user->roles[0]->name == "employee") {
            $appointments->where('branch_id', $user->branch_id);
        } 

        $appointments = $appointments->get()->toArray(); 
  
        $appointments_arr = array_map(function ($appointment){
            $appointment_date = date('Y-m-d', strtotime($appointment['date']));
            $start_time = date('H:i:m', strtotime($appointment['start_at'])); 
            $end_time = date('H:i:m', strtotime($appointment['end_at']));
            $start_date_time = $appointment_date ."T". $start_time;
            $end_date_time = $appointment_date ."T". $end_time;

            return [
                'id' => $appointment['external_id'],
                'title' => $appointment['client']['name'] ?? "",
                'start' => $start_date_time ?? "",
                'end' => $end_date_time ?? "",
                'description' => $appointment['description'] ?? "",
                'textColor' => 'white',
                'color' => $appointment['get_status']['color'] ?? "",
                'resourceId' => $appointment['user_id'] ?? "",
            ];
        }, $appointments);

        return response()->json($appointments_arr);
    }
 
    public function storeImages(Request $request)
    {   
        // file validation
        $request->validate([
            'appointment_images' => 'required',
            'appointment_images.*' => 'mimes:jpeg,jpg,png,gif|max:1024'
        ], [
            'appointment_images.required' => 'Please add at least one image!',
            'appointment_images.*.mimes' => 'Image format is not supported (supported : jpeg, jpg, png, gif)',
            'appointment_images.*.max' => 'File too big, please select a file less than 1mb!'
        ]); 

        $user_id = Auth::id();
        $appointment_id = $request->appointment_id; 
        $path_appointments_images = 'storage/assets/appointments/';

        $files = $request->file('appointment_images');

        $x = 0;
        foreach($files as $file) { 
            $imageName = $file->getClientOriginalName();
            $imageName = str_replace(' ', '_', $imageName);
            $fileName =  time() . '_' . $imageName;
            Image::make($file)->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save(public_path($path_appointments_images) . $fileName, 100);
            
            $data[$x]['external_id'] = Uuid::uuid4()->toString();
            $data[$x]['image'] = $path_appointments_images . $fileName;
            $data[$x]['appointment_id'] = $appointment_id;
            $data[$x]['created_by'] = $user_id; 
            $x++;
        }
        
        AppointmentImages::insert($data);
        Session()->flash('success', __('Images successfully uploaded!'));
        return redirect()->back();
    }

    public function updateImage(Request $request)
    {
        // file validation
        $validator = Validator::make($request->all(), [ 
            'new_image' => 'mimes:jpeg,jpg,png,gif|max:1024'
        ], [ 
            'new_image.mimes' => 'Image format is not supported (supported : jpeg, jpg, png, gif)',
            'new_image.max' => 'File too big, please select a file less than 1mb!'
        ]);   
  
        if ($validator->passes()) {

            $user_id = Auth::id();
            $appointment_id = $request->appointment_id;
            $image_id = $request->image_id; 
            $old_image = $request->old_image;  
            $new_image = $request->new_image;  
            $path_appointments_images = 'storage/assets/appointments/';
      
            // Soft delete old image
            AppointmentImages::find($image_id)->delete();
    
            // Upload Image 
            $imageName = $new_image->getClientOriginalName();
            $imageName = str_replace(' ', '_', $imageName);
            $fileName =  time() . '_' . $imageName;
            Image::make($new_image)->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save(public_path($path_appointments_images) . $fileName, 100);
    
            // Add new Image
            $new_entrie = AppointmentImages::create([
                'external_id' => Uuid::uuid4()->toString(),
                'image' => $path_appointments_images . $fileName,
                'appointment_id' => $appointment_id,
                'created_by' => $user_id,
            ]);
    
            return response()->json([
                'uploaded_id' => $new_entrie->id, 
                'status' => true,
                'image_src' => asset($path_appointments_images . $fileName),
            ]); 

        }
        return response()->json([
            'status' => false,
            'error' => $validator->errors()->all(),
        ]);
    }

    public function deleteImage(Request $request)
    {
        $image_id = $request->image_id;
        
        AppointmentImages::find($image_id)->delete();
        Session()->flash('success', __('Image successfully deleted!'));
        return redirect()->back();
    }
}
