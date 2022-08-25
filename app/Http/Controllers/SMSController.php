<?php

namespace App\Http\Controllers;
 
use DB;
use Datatables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Session;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Task;
use App\Models\Client;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Lead;
use App\Models\Role;
use App\Models\Department; 
use App\Models\Branch; 
use App\Models\Enquiry;
use Illuminate\Http\Request;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use Ramsey\Uuid\Uuid;

use App\Helpers\Helper; 
use App\Models\Distributor;
use App\Models\SMSTemplate;
use App\Models\SmsSettings;
use App\Models\SMSLog;

class SMSController extends Controller
{
             
    public function __construct()
    {      
        $this->middleware('permission:sms-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:sms-create', ['only' => ['create','store']]);
		$this->middleware('permission:sms-edit', ['only' => ['edit','update']]);  
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $data['is_system_user'] = Helper::is_system_user();  
        $data['distributors'] = Distributor::all();

        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        } 

        $data['distributor_id'] = Helper::getDistributorId();
        return view('sms.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['is_system_user'] = Helper::getDistributorId();   

        return view('sms.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id = Auth::id();

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // system user
            $distributor_id = $request->distributor_id;
        } 

        if(Helper::allowViewOnly($distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        $product = SMSTemplate::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name, 
            'message' => $request->message,
            'event_type' => $request->event_type,  
            'before_days' => $request->before_days ?? 0,  
            'event_date' => !empty($request->event_date) ? date('Y-m-d', strtotime($request->event_date)) : null,  
            'default_template' => 0, 
            'client_id' => 0, 
            'created_by' => $user_id,
            'updated_by' => $user_id, 
            'distributor_id' => $distributor_id, 
        ]); 
  
        Session()->flash('success', __('SMS template successfully added'));
        return redirect()->route('sms.index');
    }
 
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {
        $distributor_id = Helper::getDistributorId();
        
        if($distributor_id != 0) {

            $sms_template = SMSTemplate::with(['getDistributor'])->select(['external_id', 'id', 'name', 'event_type', 'distributor_id', 'event_date', 'before_days'])->where('distributor_id', $distributor_id)->where('name', '!=','Appointment SMS Template')->orderBy('id', 'desc')->get();
            $smsAppointmentTemplate = SMSTemplate::with(['getDistributor'])->select(['external_id', 'id', 'name', 'event_type', 'distributor_id', 'event_date', 'before_days'])->where('distributor_id', $distributor_id)->where('name', 'Appointment SMS Template')->orderBy('id', 'desc')->get();

            if(count($smsAppointmentTemplate) == 0) {
                $smsAppointmentTemplate = SMSTemplate::with(['getDistributor'])->select(['external_id', 'id', 'name', 'event_type', 'distributor_id', 'event_date', 'before_days'])->where('default_template', 1)->orderBy('id', 'desc')->get(); 
            }     

            $sms_template = $sms_template->merge($smsAppointmentTemplate); 

        } else {
            $sms_template = SMSTemplate::with(['getDistributor'])->select(['external_id', 'id', 'name', 'event_type', 'distributor_id', 'event_date', 'before_days'])->orderBy('id', 'desc')->get();  
        }  

        // $sms_template = SMSTemplate::orderBy('id', 'desc');

        // $distributor_id = Helper::getDistributorId();
        // if($distributor_id != 0) { // Check if distributor
        //     $sms_template->where('distributor_id', $distributor_id);
        // }  
        // $sms_template = $sms_template->get();

        return Datatables::of($sms_template)
        ->addColumn('distributor', function ($sms_template) {
            return  $sms_template->getDistributor->name ?? "";
        })  
        ->addColumn('name', function ($sms_template) {
            return $sms_template->name;
        })   
        ->addColumn('event', function ($sms_template) {
            $event_string = "";
            if($sms_template->event_type == "date") {
                $event_string = "Date ";
                if(!empty($sms_template->event_date))
                    $event_string .= "(".date('d-m-Y', strtotime($sms_template->event_date)).")"; 
            }
            if($sms_template->event_type == "birthday") {
                $event_string = "Birthday ";
                $event_string .= $sms_template->before_days == 0 ? "(on birthday)" : "(Before $sms_template->before_days day)"; 
            }
            if($sms_template->event_type == "anniversary") {
                $event_string = "anniversary ";
                $event_string .= $sms_template->before_days == 0 ? "(on anniversary)" : "(Before $sms_template->before_days day)"; 
            }
            if($sms_template->event_type == "appointment") {
                $event_string = "Appointment Booking"; 
            }
            return $event_string;
        })   
        ->addColumn('action', function ($sms_template) {
            $url = url('admin/product/view/'.$sms_template->external_id);
            $html = '<form action="#" class="d-flex" method="POST">'; 
            if(\Entrust::can('sms-edit'))
            $html .= '<a href="'.route('sms.edit', $sms_template->external_id).'" class="btn btn-link"  data-toggle="tooltip" title="Edit Product"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';
            $html .= '<input type="hidden" name="_method" value="DELETE">';
            if(\Entrust::can('sms-delete'))
            // $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-product" data-toggle="tooltip" title="Delete Product"><i class="flaticon2-trash text-danger text-hover-warning"></i></button>';
            $html .= '<input type="hidden" class="product_id" value="'.$sms_template->external_id.'">'; 
            $html .= csrf_field();
            $html .= '</form>';
            return $html;
        })
        ->rawColumns(['distributor' ,'name', 'event', 'action'])
        ->make(true);
    }

    // Show SMS Logs Page 
    public function logs()
    {  
        $data['distributor_id'] = Helper::getDistributorId();
        return view('sms.logs')->with($data);
    }

        /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function logsData()
    { 
        $distributor_id = Helper::getDistributorId();

        $sms_logs = SMSLog::orderBy('id', 'desc');
        
        if($distributor_id != 0) { 
            $sms_logs->where('distributor_id', $distributor_id);  
        }

        $sms_logs = $sms_logs->get(); 

        return Datatables::of($sms_logs)
            ->addColumn('salon_name', function ($sms_logs) {
                return  $sms_logs->getDistributor->name ?? "";
            })  
            ->addColumn('client_name', function ($sms_logs) {
                return  $sms_logs->getClient->name ?? "";
            })  
            ->addColumn('number', function ($sms_logs) {
                return  $sms_logs->number ?? "";
            })  
            ->addColumn('event_type', function ($sms_logs) {
                return  $sms_logs->event_type ?? "";
            })   
            ->addColumn('created_at', function ($sms_logs) {
                return  date('d-m-Y h:i a', strtotime($sms_logs->created_at));
            })  
            ->rawColumns(['salon_name','client_name','number','event_type','created_at',])
            ->make(true);
    }
    
    // Remote name validation
    public function checkName(Request $request)
    {  
        $id = $request->id;
        $name = $request->name;

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;
        } 

        $sms = SMSTemplate::where('name', $name)->where('distributor_id', $distributor_id)->first();

        // dd($sms);
  
        if($sms !== null) { 
            if($id == $sms->id) { 
                echo "true";
            } else {
                echo "false";
            }
        } else {
            echo "true";
        } 
    } 

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($external_id)
    {
        
        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // system user
            $template = $this->findByExternalId($external_id);
        } else {
            if($external_id != 1) { 
                $template = SMSTemplate::where('external_id', $external_id)->where('distributor_id', $distributor_id)->firstOrFail();
            } else {
                $template = SMSTemplate::where('external_id', $external_id)->firstOrFail();
            }  
        }

        // dd($template);

        $data['sms'] = $template;
        $data['is_system_user'] = Helper::getDistributorId(); 
        $data['distributor'] = Distributor::find($data['sms']->distributor_id); // current record distributor name (for admin)
 
        return view('sms.edit')->with($data);
    }

    public function findByExternalId($external_id)
    {
        return SMSTemplate::where('external_id', $external_id)->firstOrFail();
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
        $user_id = Auth::id();
        $template = $this->findByExternalId($external_id);
          
        if($template->default_template != 1 && Helper::allowViewOnly($template->distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        if($external_id == 1 && Helper::getDistributorId() !== 0) {
            // dd("Create NEw");
            $sms_template = new SMSTemplate();
            $sms_template->external_id = Uuid::uuid4()->toString(); 
            $sms_template->name = "Appointment SMS Template"; 
            $sms_template->message = $request->message; 
            $sms_template->event_type = "appointment"; 
            $sms_template->before_days = 0; 
            $sms_template->updated_by = $user_id; 
            $sms_template->default_template = 0;  
            $sms_template->distributor_id = Helper::getDistributorId();
            $sms_template->save();
        } else { 
            $sms_template = SMSTemplate::where('external_id', $external_id)->update([ 
                'name' => $request->name, 
                'message' => $request->message,
                'event_type' => $request->event_type,  
                'before_days' => $request->before_days ?? 0,  
                'event_date' => !empty($request->event_date) ? date('Y-m-d', strtotime($request->event_date)) : null,   
                'updated_by' => $user_id, 
            ]);
        } 
         
        Session()->flash('success', __('SMS template successfully updated'));
        return redirect()->route('sms.index');
    }
    
    public function configView()
    { 
        try{

            $settings = SmsSettings::find(1); 
  
            return view('sms.config', compact('settings'));

        }catch(Exception $e){
            abort(404);
        }
    }
  
    public function storeConfig(Request $request)
    {  
        $user_id = Auth::user()->id;
        $api_url = $request->api_url;
        $parameters = $request->parameters;
        $values = $request->values;

        $arr = array();     
        $x = 1;
        foreach($parameters as $parameter) {
            array_push($arr, ['key' => $parameters[$x], 'value' => $values[$x]]);
            $x++;
        } 

        $settings = SmsSettings::find(1);
        
        if(!empty($settings) > 0) {
            
            $settings->update([
                'api_url' => $api_url,
                'parameters' => json_encode($arr),
                'final_url' => $request->url_preview,
                'updated_by' => $user_id,
                'mobile_param' => $request->mobile_param,
                'msg_param' => $request->msg_param,
                'is_tested' => 0,
                'is_working' => 0,
            ]);

        } else {  
            SmsSettings::create([
                'api_url' => $api_url,
                'parameters' => json_encode($arr),
                'final_url' => $request->url_preview,
                'updated_by' => $user_id,
                'mobile_param' => $request->mobile_param,
                'msg_param' => $request->msg_param,
                'is_tested' => 0,
                'is_working' => 0,
            ]);
        } 
 
        return redirect()->back()->with('success', 'SMS API Settings is successfully updated');
    }
    
    public function updateParameters(Request $request)
    { 
        $form_data = $request->form_data;
        
        $data = [];
        parse_str($form_data, $data); 

        $user_id = Auth::user()->id;
        $parameters = $data['parameters'] ?? [];
        $values = $data['values'] ?? [];
  
        $arr = array();     
        $x = 1;
        foreach($parameters as $key => $parameter) {
            array_push($arr, ['key' => $parameter, 'value' => $values[$key]]);
            $x++;
        } 
 
        try {

            SmsSettings::find(1)->update([ 
                'parameters' => json_encode($arr),
                'final_url' => $data['url_preview'],
                'updated_by' => $user_id,
                'is_tested' => 0,
                'is_working' => 0,
            ]);
            
     
            return response()->json([
                'status' => true,
                'message' => "Parameter successfully removed",
            ]);

        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Something went to wrong!",
            ]);
        } 
    }
 
    public function testAPI(Request $request)
    {  
        $number = $request->number;
        $message = $request->message;
          
        $test = Helper::sendSingleSMS($number, $message); 

        if($test == true) {
            SmsSettings::find(1)->update([  
                'is_tested' => 1,
                'is_working' => 1,
            ]);

            $alert = "success";
            $message = "SMS configuration is working. Please check sms on ". $number;

        } else {
            SmsSettings::find(1)->update([  
                'is_tested' => 1,
                'is_working' => 0,
            ]);

            $alert = "error";
            $message = "SMS configuration is not working! please check configuration and test again.";
        }
         
        return redirect()->back()->with($alert, $message); 
    }
}
