<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Auth;
use Validator;
use Exception;
use App\Helpers\Helper; 
use App\Models\Distributor;
use App\Models\EmailLog;

class EmailsController extends Controller
{
         
    public function __construct()
    {      
        $this->middleware('permission:email-template-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:email-template-create', ['only' => ['create','store']]);
		$this->middleware('permission:email-template-update', ['only' => ['edit','update']]);  
    }
 

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        // $reminderTemplates = EmailTemplate::where('event_type', 'reminder')->get();
        // Helper::sendEventEmail($reminderTemplates, 'reminder');

        // dd($reminderTemplates);


        $data['is_system_user'] = Helper::is_system_user();  
        $data['distributors'] = Distributor::all();

        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        } 

        return view('email.index')->with($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function logs(Request $request)
    {   
        $data['distributor_id'] = Helper::getDistributorId();
        return view('email.logs')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['is_system_user'] = Helper::getDistributorId();
        $data['distributors'] = Distributor::all();
        return view('email.create')->with($data);
    }
     
    
        /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {
        $distributor_id = Helper::getDistributorId();
        
        if($distributor_id != 0) {

            $not_in_subscriptions = ['Notification Before expire 6', 'Notification Before expire 5', 'Notification Before expire 4', 'Notification Before expire 3', 'Notification Before expire 2', 'Notification Before expire 1'];
            $not_in_default = ['Invoice email send', 'Subscription Invoice Template'];

            $emailTemplate = EmailTemplate::with(['getDistributor'])->select(['email_template_id', 'name', 'subject', 'distributor_id', 'event_type', 'before_days'])->where('distributor_id', $distributor_id)->whereNotIn('name', $not_in_default)->whereNotIn('name', $not_in_subscriptions)->orderBy('email_template_id', 'desc')->get();
            $emailTemplateInvoice = EmailTemplate::with(['getDistributor'])->select(['email_template_id', 'name', 'subject', 'distributor_id', 'event_type', 'before_days'])->where('distributor_id', $distributor_id)->where('name', 'Invoice email send')->whereNotIn('name', $not_in_subscriptions)->orderBy('email_template_id', 'desc')->get();

            if(count($emailTemplateInvoice) == 0) {
                $emailTemplateInvoice = EmailTemplate::with(['getDistributor'])->select(['email_template_id', 'name', 'subject', 'distributor_id', 'event_type', 'before_days'])->where('default_template', 1)->where('name', '!=', 'Subscription Invoice Template')->whereNotIn('name', $not_in_subscriptions)->orderBy('email_template_id', 'desc')->get(); 
            }     

            $emailTemplate = $emailTemplate->merge($emailTemplateInvoice); 

        } else {
            $emailTemplate = EmailTemplate::with(['getDistributor'])->select(['email_template_id', 'name', 'subject', 'distributor_id', 'event_type', 'before_days'])->orderBy('email_template_id', 'desc')->get();  
        }  
  
        return Datatables::of($emailTemplate)
            ->addColumn('distributor', function ($emailTemplate) {
                return  $emailTemplate->getDistributor->name ?? "";
            }) 
            ->addColumn('event', function ($email_template) {
                $event_string = "";
                if($email_template->event_type == "date") {
                    $event_string = "Date ";
                    if(!empty($email_template->event_date))
                        $event_string .= "(".date('d-m-Y', strtotime($email_template->event_date)).")"; 
                }
                if($email_template->event_type == "birthday") {
                    $event_string = "Birthday ";
                    $event_string .= $email_template->before_days == 0 ? "(on birthday)" : "(Before $email_template->before_days day)"; 
                }
                if($email_template->event_type == "anniversary") {
                    $event_string = "anniversary ";
                    $event_string .= $email_template->before_days == 0 ? "(on anniversary)" : "(Before $email_template->before_days day)"; 
                }
                if($email_template->event_type == "") {
                    $event_string = ""; 
                }
                return $event_string;
            })   
            ->addColumn('action', function ($emailTemplate) {
                $html = ""; 
                if($emailTemplate->distributor_id != 0 ) {
                    if(\Entrust::can('email-template-update') && !Helper::allowViewOnly($emailTemplate->distributor_id))
                    $html .= '<a href="'.url('admin/emails/edit/'.encrypt($emailTemplate->email_template_id)).'" class="btn btn-link" data-toggle="tooltip" title="Edit Email"><i class="flaticon2-pen text-primary"></i></a>';
                } else {
                    if(\Entrust::can('email-template-update'))
                    $html .= '<a href="'.url('admin/emails/edit/'.encrypt($emailTemplate->email_template_id)).'" class="btn btn-link" data-toggle="tooltip" title="Edit Email"><i class="flaticon2-pen text-primary"></i></a>';
                } 
                return $html;
            })  
            ->rawColumns(['event', 'action'])
            ->make(true);
    }

    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function logsData()
    {
        $distributor_id = Helper::getDistributorId();

        $email_logs = EmailLog::orderBy('id', 'desc');
        
        if($distributor_id != 0) { 
            $email_logs->where('distributor_id', $distributor_id);  
        }

        $email_logs = $email_logs->get();
         
        return Datatables::of($email_logs)
            ->addColumn('salon_name', function ($email_logs) {
                return  $email_logs->getDistributor->name ?? "";
            })  
            ->addColumn('from_email', function ($email_logs) {
                return  $email_logs->from_email ?? "";
            })  
            ->addColumn('from_name', function ($email_logs) {
                return  $email_logs->from_name ?? "";
            })  
            ->addColumn('client_name', function ($email_logs) {
                return  $email_logs->getClient->name ?? "";
            })  
            ->addColumn('client_email', function ($email_logs) {
                return  $email_logs->client_email ?? "";
            })  
            ->addColumn('event_type', function ($email_logs) {
                return  $email_logs->event_type ?? "";
            })  
            ->addColumn('created_at', function ($email_logs) {
                return  date('d-m-Y h:i a', strtotime($email_logs->created_at));
            })  
            ->rawColumns(['salon_name','from_email','from_name','client_name','client_email','event_type','created_at'])
            ->make(true);
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'subject' => 'required|max:200',
            'content' => 'required',
        ],
            ['content.required' => 'The email template field is required.']
        );
        
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }
        
        $id = decrypt($id); 
        $user_id = Auth::user()->id;
        $user = User::find($user_id); 
        $getTemplate = EmailTemplate::find($id);

        if($getTemplate->default_template != 1 && Helper::allowViewOnly($getTemplate->distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }
 
        if($id == 1 && Helper::getDistributorId() !== 0){
            $emailTemplate = new EmailTemplate();
            $emailTemplate->default_template = 0;
            $emailTemplate->createdBy = $user_id;
            $emailTemplate->client_id = 0;
            $emailTemplate->company_id = 0;
            $emailTemplate->name = $getTemplate->name;
            $emailTemplate->distributor_id = Helper::getDistributorId();
        }else{
            $emailTemplate = EmailTemplate::find($id);
            $emailTemplate->name = $request->name;
        }
 
        $emailTemplate->event_type = $request->event_type;
        $emailTemplate->before_days = $request->before_days ?? 0;
        $emailTemplate->event_date = !empty($request->event_date) ? date('Y-m-d', strtotime($request->event_date)) : null;
        $emailTemplate->subject = $request->subject;
        $emailTemplate->content = $request->content;
        $emailTemplate->updatedBy = $user_id; 
  
        $emailTemplate->save();

        Session()->flash('success', __('Email template successfully updated'));
        return redirect('admin/emails');
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

        EmailTemplate::create([
            'distributor_id' => $distributor_id,
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $request->content,
            'createdBy' => $request->user_id,
            'updatedBy' => $request->user_id,
            'event_type' => $request->event_type,  
            'before_days' => $request->before_days ?? 0,  
            'event_date' => !empty($request->event_date) ? date('Y-m-d', strtotime($request->event_date)) : null, 
        ]);
          
        Session()->flash('success', __('Email template successfully added'));
        return redirect()->route('emails.index');
    }

    public function edit($id)
    { 
        $id = decrypt($id); 

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { 
            $template = EmailTemplate::where('email_template_id', $id)->first(); 
        } else {
            if($id != 1) {
                $template = EmailTemplate::where('email_template_id', $id)->where('distributor_id', $distributor_id)->firstOrFail();
            } else {
                $template = EmailTemplate::where('email_template_id', $id)->firstOrFail();
            } 
        } 
        
        $data['email'] = $template;
        $data['is_system_user'] = Helper::getDistributorId(); 
        $data['distributor'] = Distributor::find($data['email']->distributor_id); // current record distributor name (for admin)

        return view('email.edit')->with($data);
    } 

    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit($id)
    // {
    //     try{
    //         $id = decrypt($id);
    //         $emailTemplate = EmailTemplate::find($id);

    //         $emailTemplateBody =  Helper::emailTemplateBody();

    //         $emailTemplateBody =  str_replace("{{#template_body}}", $emailTemplate->content, $emailTemplateBody);

    //         $emailTemplate->content = str_replace("{{#all_css}}", Helper::emailTemplateCss(), $emailTemplateBody);

    //         return view('email.form',compact('emailTemplate'));
    //     }catch(Exception $e){
    //         abort(404);
    //     }
    // }
}
