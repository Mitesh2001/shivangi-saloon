<?php

namespace App\Http\Controllers;

use Validator;
use Exception; 
use Mail;
use PDF;
use DB;
use URL;
use Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Datatables;
use App\Models\Subscriptions;   
use App\Models\Distributor;   
use App\Models\User; 
use App\Models\Plan; 
use App\Models\SalonPlan; 
use App\Models\UsersCommission;
use Ramsey\Uuid\Uuid; 
use App\Models\EmailTemplate;
use App\Models\EmailLog;


class SubscriptionsController extends Controller
{
        
    public function __construct()
    {
        $this->middleware('permission:subscription-view', ['only' => ['index']]);
		$this->middleware('permission:subscription-create', ['only' => ['create','store']]);
		$this->middleware('permission:subscription-update', ['only' => ['edit','update']]); 
    }


    public function currentUser(){
        return Auth::user();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $salon_id = $request->get('salon_id'); 
            $salon_data = Distributor::where('external_id', $salon_id)->first();   
            $payment_modes = array_merge(array('Please select payment mode'), config('global.payment_modes'));
            return view('subscriptions.index',compact('salon_id','salon_data','payment_modes'));
        }catch(Exception $e)
        {
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    { 
        try{ 

            $filter_type = $request->get('filter');
            $title = "All Subscriptions";
            $back_url = false;

            if(!empty($filter_type)) {
                $back_url = route('dashboard');
                $title = ucwords($filter_type ." Subscriptions");
            }

            $payment_modes = array_merge(array(''=>'Please Select'),config('global.payment_modes'));
			$new = ($request->new)?$request->new:0;
			$running = ($request->running)?$request->running:0;
			$closed = ($request->closed)?$request->closed:0;
            return view('subscriptions.all',compact('title','payment_modes','new','running','closed', 'filter_type', 'back_url'));
        }catch(Exception $e)
        {
            abort(404);
        }
    }
	
	/**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData(Request $request)
    {  
        $salon_id = $request->salon_id; 
         
        $subscriptions = Subscriptions::where('salon_id',$salon_id); 

        $distributor_id = Helper::getDistributorId();
        if($distributor_id != 0) { // if not system user
            $subscriptions->where('created_by', $distributor_id);
        }
        $subscriptions = $subscriptions->with(['salon'])->orderBy('id', 'desc')->get(); 

        return Datatables::of($subscriptions)
            ->addColumn('subscription_id', function ($subscriptions) {  
                return $subscriptions->subscription_id;
            })	 
            ->addColumn('payment_mode', function ($subscriptions) {  
                return $subscriptions->payment_mode;
            })	 
            ->addColumn('final_amount', function ($subscriptions) {  
                return $subscriptions->final_amount;
            })	 
            ->addColumn('is_payment_pending', function ($subscriptions) {  
                return $subscriptions->is_payment_pending;
            })	 
            ->addColumn('subscription_expiry_date', function ($subscriptions) {  
                if(!empty($subscriptions->subscription_expiry_date))
                    return date('d-m-Y', strtotime($subscriptions->subscription_expiry_date));
            })	 
            ->addColumn('action', function ($subscriptions) {
                $html = $edit_btn = $cancel_btn = '';

                $html .= '<a href="'.route('subscriptions.show', encrypt($subscriptions->id)).'" class="btn btn-link" data-toggle="tooltip" title="View"><i class="flaticon-eye text-primary"></i></a>';
                
                if($subscriptions->is_payment_pending == "YES"){

                    $edit_btn = '<a href="'.route('subscriptions.edit', encrypt($subscriptions->id)).'" class="btn btn-link" data-toggle="tooltip" title="Edit"><i class="flaticon2-pen text-primary"></i></a>';

                    $html .= $edit_btn;
                
                    $cancel_btn = '<a href="'.route("subscriptions.delete", $subscriptions->id).'"   class="btn btn-link cancel_subscription" data-toggle="tooltip" title="Cancel Subscription"><i class="flaticon2-cancel text-danger"></i></a>';
                    
                    $html .= $cancel_btn;
                }

                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function allData(Request $request)
    { 
        $user = Auth::user();
        $filter_type = $request->get('filter_type');
        
        if(!empty($filter_type)) {
            $today = date('Y-m-d');
            if($filter_type == "new") {
                $subscriptions = Subscriptions::whereDate('created_at', $today);
            } 
            if($filter_type == "running") {
                $subscriptions = Subscriptions::whereDate('subscription_expiry_date', '>', $today);
            } 
            if($filter_type == "expired") {
                $subscriptions = Subscriptions::whereDate('subscription_expiry_date', $today);
            }

            if($user->user_type == 2) {
                $subscriptions->where('created_by', $user->id);
            }

            $subscriptions = $subscriptions->get();

        } else {
            
            if($user->user_type == 2) {
                $subscriptions = Subscriptions::where('created_by', $user->id)->get();
            } else {
                $subscriptions = Subscriptions::all();
            }
        }
 
        return Datatables::of($subscriptions)
            ->addColumn('subscriptions_uid', function ($subscriptions) {
                return $subscriptions->subscription_id;
            })
            ->addColumn('distributor_name', function ($subscriptions) {
                $full_name = $subscriptions->distributor->first_name ? $subscriptions->distributor->first_name . " " : "";
                $full_name .=  $subscriptions->distributor->last_name ? $subscriptions->distributor->last_name . " " : "";

                return $full_name;
            })
            ->addColumn('salon_name', function ($subscriptions) {
                return $subscriptions->salon->name ?? "";
            })	    
            ->addColumn('payment_mode', function ($subscriptions) {
                return $subscriptions->payment_mode;
            })				
            ->addColumn('final_amount', function ($subscriptions) {
                return $subscriptions->final_amount;
            })				
            ->addColumn('is_payment_pending', function ($subscriptions) {
                return $subscriptions->is_payment_pending;
            })				
			->addColumn('created', function ($subscriptions) {
                return date("d-m-Y",strtotime($subscriptions->created_at));
            })
            ->addColumn('expiry_date', function ($subscriptions) {
                if($subscriptions->subscription_expiry_date!=NULL){
                    return date("d-m-Y",strtotime($subscriptions->subscription_expiry_date));
                }else{
                    return '';
                }
            })
            ->rawColumns(['distributor_name','salon_name','payment_mode','final_amount','is_payment_pending','created','expiry_date'])
            ->make(true);
    }
	
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {     
        try{
            $salon_id = $request->salon_id; 
            $data['salon_id'] = $salon_id;

            $plan = Plan::pluck('name', 'id'); 
            $plan->prepend('Please Select Plan', ''); 
    
            $salon_data = Distributor::where('external_id', $salon_id)->first();  
            $payment_modes = array_merge(array(''=>'Select Payment Mode'), config('global.payment_modes'));
    
            return view('subscriptions.create', compact('salon_id', 'salon_data', 'payment_modes', 'plan'));
        }catch(Exception $e){
            abort(404);
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
        $validation = Validator::make($request->all(), [ 
            'plans' => 'required|array|min:1',
            'payment_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:today',
            'payment_bank_name' => 'nullable|max:40|regex:/^[A-Za-z ]+$/u',
            'payment_number' => 'nullable|max:16|regex:/^[0-9]+$/u'
        ],
        [
            'plans.required'=>'The plan field is required.'
        ]);
   
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }
       
        $subscription = new Subscriptions();

        $subscriptions_uid_count = Subscriptions::withTrashed()->count();
        $subscriptions_uid_count = $subscriptions_uid_count == 0 ? 1 : $subscriptions_uid_count;
        $subscriptions_uid = str_pad($subscriptions_uid_count, 6, '0', STR_PAD_LEFT);
          
        $message = 'Plan has been added successfully';
        if($request->subscription_id)
        {
            $subscriptionId = decrypt($request->subscription_id);
            $subscription = Subscriptions::find($subscriptionId);
            $message = 'Plan has been updated successfully';
        }
         
        // Input Array
        $plans = $request->plans; 
        $plans_discount = $request->plans_discount;
        $subscription_date = $request->subscription_date;

        $salon_data = Distributor::where('external_id', $request->salon_id)->first();

        $user = Auth::user(); 
        $user_subscription_commission = $user->plan_commission;
  
		$allPlans = Plan::whereIn('id',$plans)->get();
		$total_amount = $totaluser = $totalbranches = $totalmonth = $no_of_sms = $no_of_email = $subscription_commission = 0;

		foreach($allPlans as $plan){
			$amount = $plan->price; 
            $discount = intval($plans_discount[$plan->id]);
            $calculated_amount = $amount;
            if($discount != 0) {
                $calculated_amount = $amount - ($amount * $discount / 100);
            }  
            if($salon_data->state_id === 12){ 
                $gst_array = Helper::getCalucatedGST($calculated_amount, $plan->sgst, $plan->cgst, 0);
                $plan_original_price = ($calculated_amount - $gst_array['sgst_amount'] - $gst_array['cgst_amount']);
            } else { 
                $gst_array = Helper::getCalucatedGST($calculated_amount, 0, 0, $plan->igst); 
                $plan_original_price = ($calculated_amount - $gst_array['igst_amount']);
            } 

            if($user_subscription_commission > 0) { 
                $subscription_commission += $plan_original_price / 100 * $user_subscription_commission;
            }
 
			$discount = Helper::decimalNumber($discount);
			$discount_amount = $amount * $discount / 100;
			$discount_amount = Helper::decimalNumber($discount_amount);
			$netamount = $amount - $discount_amount;
			$netamount = Helper::decimalNumber($netamount);
			$total_amount += $netamount;
			$totaluser += $plan->no_of_users;
			$totalbranches += $plan->no_of_branches;
			$totalmonth += $plan->duration_months;
            $no_of_sms += $plan->no_of_sms;
            $no_of_email += $plan->no_of_email;
        }  
         

   
		$sgst_amount = Helper::decimalNumber($request->sgst_amount); 
		$cgst_amount = Helper::decimalNumber($request->cgst_amount); 
		$igst_amount = Helper::decimalNumber($request->igst_amount); 
          
        if($salon_data){ 
            $subscription->salon_id = $salon_data->id; 
			
			if($salon_data->expiry_date!= NULL && date("Y-m-d",strtotime($salon_data->expiry_date)) > date("Y-m-d"))
			{
				$startdate = date("Y-m-d",strtotime($salon_data->expiry_date));
			}else{
                $startdate = date("Y-m-d");
			}
            if($totalmonth>0){
			    $expiry_date = date('Y-m-d', strtotime("+".$totalmonth." months", strtotime($startdate))); 
                $subscription_expiry_date = date('Y-m-d', strtotime("+".$totalmonth." months", strtotime($startdate)));
			} 

            if($request->payment_status == "NO") {
                if($totalmonth>0){
                    $salon_data->expiry_date = $expiry_date;
                }

                $salon_data->no_of_users   = $salon_data->no_of_users + $totaluser;
                $salon_data->no_of_branches   = $salon_data->no_of_branches + $totalbranches;
                $salon_data->total_sms     = $salon_data->total_sms + $no_of_sms;
                $salon_data->total_email   = $salon_data->total_email + $no_of_email; 
                $salon_data->save();
            } 
        } 
        
        $subscription->total_amount = $total_amount; 
		$subscription->sgst_amount = $sgst_amount; 
		$subscription->cgst_amount = $cgst_amount; 
		$subscription->igst_amount = $igst_amount;
        $subscription->final_amount = $total_amount;
        $subscription->payment_mode = $request->payment_mode;
        $subscription->payment_bank_name = $request->payment_bank_name;
        $subscription->payment_number = $request->payment_number;
        $subscription->payment_amount = $total_amount;
        $subscription->state_id = $salon_data->state_id; // State id was assigned as string so attaching the same
        $subscription->payment_date = $request->payment_date;
        if(isset($expiry_date) && $request->payment_status == "NO") {
            $subscription->subscription_expiry_date = $expiry_date;
        } 
        $subscription->is_payment_pending = $request->payment_status;
        $subscription->round_off_amount = round($total_amount);
        $subscription->subscription_id = $subscriptions_uid;
        $subscription->created_by = auth()->user()->id;  
        $subscription->save(); 

		foreach($allPlans as $plan){ 
			$salonPlan = new salonPlan();
            if($salon_data){
				$salonPlan->salon_id = $salon_data->id; 
			}

			$salonPlan->subscription_id = $subscription->id;
			$salonPlan->plan_id = $plan->id;
			$amount = $plan->price;
			$amount = Helper::decimalNumber($amount);
			$discount = intval($plans_discount[$plan->id]);
			$discount = Helper::decimalNumber($discount);
			$discount_amount = $amount * $discount / 100;
			$discount_amount = Helper::decimalNumber($discount_amount);
			$netamount = $amount - $discount_amount;
			$netamount = Helper::decimalNumber($netamount);
			
			$salonPlan->plan_price = $amount;
			$salonPlan->discount = $discount;
			$salonPlan->discount_amount = $discount_amount;
            $salonPlan->final_amount = $netamount;
            $salonPlan->no_of_sms = $plan->no_of_sms;
            $salonPlan->no_of_email = $plan->no_of_email;
            $salonPlan->no_of_users = $plan->no_of_users;
            $salonPlan->no_of_branches = $plan->no_of_branches;
            $salonPlan->duration_months = $plan->duration_months;
            $salonPlan->subscription_date = $subscription_date[$plan->id]; 

            if($salon_data->state_id === 12){
                $salonPlan->sgst = $plan->sgst; 
                $salonPlan->sgst_amount = floatval($netamount) - floatval($netamount) * (100 / (100 + floatval($plan->sgst)));

                $salonPlan->cgst = $plan->cgst;
                $salonPlan->cgst_amount  = floatval($netamount) - floatval($netamount) * (100 / (100 + floatval($plan->cgst)));
            } else {
                $salonPlan->igst = $plan->igst;
                $salonPlan->igst_amount = floatval($netamount) - floatval($netamount) * (100 / (100 + floatval($plan->igst)));
            } 
			$salonPlan->save();
		}
         
        // Manage Commission

        if($request->payment_status == "NO") { 
            $this->manageUserCommission($user, $subscription, $subscription_commission);
        } 
        return redirect(route('subscriptions.index').'?salon_id='.$request->salon_id)->with('success', $message);
    }

    
    // Manage User Commission
    public function manageUserCommission($user, $subscription, $subscription_commission)
    {   
        $distributor_id = 0; // For distributors itself
        
        // Current Commission % of user
        $user_subscription_commission = Auth::user()->plan_commission;
          
        if($user_subscription_commission > 0) { 

            $invoice_json = json_encode($subscription);  
 
            $userCommission = new UsersCommission();
            $userCommission->external_id = Uuid::uuid4()->toString();
            $userCommission->user_id = $user->id;
            $userCommission->user_type = $user->user_type;
            $userCommission->order_id = 0;
            $userCommission->subscription_id = $subscription->id ?? 0;
            $userCommission->user_subscription_commission = $user_subscription_commission; 
            $userCommission->invoice_json = $invoice_json;
            $userCommission->invoice_commission = $subscription_commission;
            $userCommission->subscription_commission = $subscription_commission; 
            $userCommission->is_paid = 0; 
            $userCommission->distributor_id = $distributor_id; 

            $userCommission->save();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        $sId = decrypt($id); 
        
        $subscription = Subscriptions::with(['salon'])->find($sId);
        $salon_id = $subscription->salon_id;
        $SalonPlans = SalonPlan::where('subscription_id','=',$sId)->with(['plan'])->get();
        $clientEmail = !empty($request->email) ? $request->email : null;
   
        $user = Auth::user();
         
        $template = Helper::defaultSubscriptionTemplate();  
        $template_content = Helper::invoiceTemplateBody(); 

        $template = str_replace("<p></p>","",$template);
        $template = str_replace("<p>&nbsp;</p>","",$template);    
        $invoice_template = str_replace("{{#template_content}}", $template, $template_content);

        // dd($invoice_template);

        $dom = new \DOMDocument();
        $dom->loadHTML($invoice_template); 
        $xpath = new \DOMXPath($dom); 

        if($subscription->state_id == 12) {
            $text_arr = ['{{#igst}}', '{{#igst_amount}}'];
        } else {
            $text_arr = ['{{#sgst}}', '{{#sgst_amount}}', '{{#cgst}}', '{{#cgst_amount}}'];
        } 
 
        if($subscription->payment_mode == "CASH") { 
            array_push($text_arr, '{{#bank_name}}', '{{#transaction_no}}');
        } 

        foreach ($text_arr as $text) {
            foreach ($xpath->query("(//*[text()[contains(., '$text')]])[1]/parent::tr") as $row) {  
                $row->parentNode->removeChild($row);
            } 
        } 
        $invoice_template = $dom->saveHTML(); 

        // dd($invoice_template);
               
        $plan_list = '';
        foreach ($SalonPlans as $splan){
            $plan_list .='<tr class="plan_item">';
            $plan_list .='<td class="text_left">'.$splan->plan->name. '<br/>Email :' .$splan->no_of_email .', SMS : '.$splan->no_of_sms. ',<br/> Branch :' .$splan->no_of_branches .', Users : '.$splan->no_of_users.'</td>';
            $plan_list .='<td class="text_right" width="15%">'.$splan->subscription_date.'</td>';
            $plan_list .='<td class="text_right" width="5%">'.Helper::decimalNumber($splan->plan_price).'</td>';
            $plan_list .='<td class="text_right" width="10%">'.Helper::decimalNumber($splan->discount).'</td>';
            $plan_list .='<td class="text_right" width="15%">'.Helper::decimalNumber($splan->discount_amount).'</td>';
            $plan_list .='<td class="text_right" width="20%">'.Helper::decimalNumber($splan->final_amount).'</td>';
            $plan_list .='</tr>';
        }

        $server_css = "#plan_list{display:none}";

        if($subscription->is_payment_pending == "YES") {
            $server_css .= "#payment_mode{display:none}";
            $server_css .= "#payment_date{display:none}";
            $server_css .= "#bank_name_row{display:none}";
            $server_css .= "#tansaction_number{display:none}"; 
        } 
 
        $variables = array( 
            "{{#salon_address}}" => $subscription->salon->address,
            "{{#invoice_no}}" => $subscription->subscription_id,
            "{{#salon_name}}" => "ND Salon Software",
            "{{#salon_email}}" => $subscription->salon->email,
            "{{#created_date}}" => $subscription->created_at->format('Y-m-d'),
            "{{#gst_no}}" => $subscription->salon->gst_number,
            "{{#total_amount}}" => Helper::decimalNumber($subscription->total_amount),
            "{{#sgst_amount}}" => Helper::decimalNumber($subscription->sgst_amount),
            "{{#sgst}}" => Helper::decimalNumber($subscription->sgst),
            "{{#cgst_amount}}" => Helper::decimalNumber($subscription->cgst_amount),
            "{{#cgst}}" => Helper::decimalNumber($subscription->cgst),
            "{{#igst_amount}}" => Helper::decimalNumber($subscription->igst_amount),
            "{{#igst}}" => Helper::decimalNumber($subscription->igst),
            "{{#final_amount}}" => Helper::decimalNumber($subscription->final_amount),
            // "{{#round_off_amt}}" => Helper::decimalNumber($subscription->round_off_amount),
            "{{#yes_no}}" => $subscription->is_payment_pending,
            "{{#payment_mode}}" => $subscription->payment_mode,
            "{{#payment_date}}" => $subscription->payment_date,
            "{{#bank_name}}" => $subscription->payment_bank_name,
            "{{#transaction_no}}" => $subscription->payment_number,
            "{{#server_css}}" => $server_css,
            '<tfoot></tfoot>' => $plan_list
        );
 
        foreach ($variables as $key => $value)
            $invoice_template = str_replace($key, $value, $invoice_template);
        
        $pdfName = date('Y_m_d_H_i_s_'.$sId).'.pdf';

        $payment_modes = array_merge(array(''=>'Select Payment Mode'), config('global.payment_modes'));
        $salon_data = Distributor::find($salon_id); 
        
        if($request->is_pdf || $request->is_email){
            $data['subscription'] = $subscription;
            $data['salon'] = $salon_data; 
            $data['clientplans'] = $SalonPlans;
            $data['salon_id'] = $salon_id;
            $data['payment_modes'] = $payment_modes;

            $pdf = PDF::loadHTML($invoice_template);
        
            // return $pdf->setPaper('landscape')->setWarnings(false)->stream();

            if($request->is_pdf){
                return $pdf->setPaper('landscape')->setWarnings(false)->download($pdfName);
            }
            if($request->is_email){ 
 
                if($clientEmail){
                    if($user){
                        $message = 'Invoice sent successfully.';
                        $emails = explode(',', $clientEmail);
                        $emails =  array_slice($emails, 0, 3);

                        $getInvoiceTemplateSend = EmailTemplate::select(['email_template_id', 'subject', 'content'])->where('name','Subscription Invoice Template')->where('default_template','1')->first();
    
                        $message_content = $getInvoiceTemplateSend['content'];
                        $subject = $getInvoiceTemplateSend['subject'];   

                        $salon_details = Distributor::find($subscription->salon_od);

                        $details = ($salon_details != null) ? $salon_details->company_name : '';
                        
                        if(isset($salon_details)){
                            
                            if($salon_details->address_line_1){
                                $details .= ', '.$salon_details->address_line_1;
                            }
                            if($salon_details->address_line_2){
                                $details .= ', '.$salon_details->address_line_2;
                            } 
                        }

                        $email_variable = array(
                            '{{#client_name}}' => $subscription->salon->name,
                            '{{#user_name}}' =>  Auth::user()->first_name ." ". Auth::user()->laast_name,
                            '{{#salon_details}}' =>  $details,
                            '{{#copy_right}}' =>  ($salon_details != null) ? $salon_details->company_name : '' .' @ ' .date('Y-m-d'),
                        );
                        // $companyData = Company::find($companyId);
                        
                        foreach ($email_variable as $key => $value)
                            $message_content = str_replace($key, $value, $message_content);

                            $emailTemplateBody =  Helper::emailTemplateBody();

                            $emailTemplateBody =  str_replace("{{#template_body}}", $message_content, $emailTemplateBody);
            
                            $message_content = str_replace("{{#all_css}}", Helper::emailTemplateCss(), $emailTemplateBody); 

                            // $companyData = Company::find($companyId);
                          
                            $data['subject'] = $subject;
                            $data['messagecontent'] = $message_content;
                            $data['from_email'] = "shivangi@noreplay.com";
                            $data['from_name'] = "ND Salon Software";
 
                            EmailLog::create([
                                'template_id' => $getInvoiceTemplateSend->email_template_id, 
                                'client_id' => $subscription->salon_id, 
                                'client_email' => $clientEmail, 
                                'from_email' =>  "shivangi@noreplay.com",  
                                'from_name' => "ND Salon Software",  
                                'event_type' => "Order Invoice",    
                                'template_json' => json_encode($getInvoiceTemplateSend), 
                                'distributor_id' => 0, 
                            ]);

                            Mail::send('emails.email', $data, function($message)use($data, $pdf,$pdfName,$emails) {
                                $message->subject($data['subject']);
                                
                                if(isset($data['from_email']) && isset($data['from_name'])){
                                    $message->from($data['from_email'], $data['from_name']);
                                }
                                $message->to($emails)->attachData($pdf->output(), $pdfName);
                            });
                    }
                }
                return back()->with('success', $message);
            }
        }

        $payment_modes = array_merge(array(''=>'Select Payment Mode'), config('global.payment_modes'));

        return view('subscriptions.show',compact('subscription','payment_modes','SalonPlans','salon_id', 'salon_data'));
       
    }

    /**
     * Show the form for editing the specified plan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { 
        try{
            $sId = decrypt($id);  
            $subscription = Subscriptions::with(['salon'])->find($sId); 
            $salon_id = $subscription->salon_id;  
            $salon_plan = SalonPlan::where('subscription_id','=',$sId)->with(['plan'])->get();  
            $salon_data = Distributor::find($subscription->salon_id);  
            $payment_modes = array_merge(array(''=>'Select Payment Mode'),config('global.payment_modes'));
            return view('subscriptions.edit',compact('salon_plan', 'salon_data', 'subscription', 'payment_modes', 'salon_id'));
        }catch(Exception $e)
        {
            abort(404);
        }
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
        $validation = Validator::make($request->all(), [ 
            'payment_date' => 'nullable|date',
            'payment_bank_name' => 'nullable|max:40|regex:/^[A-Za-z ]+$/u',
            'payment_number' => 'nullable|max:16|regex:/^[0-9]+$/u'
        ]);
		
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }  

        $subscription = Subscriptions::find($id); 
        
        // Input Array
        $plans = $request->plans; 
        $plans_discount = $request->plans_discount;
        $subscription_date = $request->subscription_date;
        $plans_price = $request->plans_price;
        $is_new_plan = $request->is_new_plan; // 0 = update, 1 = new
        $salon_plans = $request->salon_plans;

        $user = Auth::user(); 
        $user_subscription_commission = $user->plan_commission;

        if(empty($plans)){
            return redirect()->back()->with('error', 'Can not submit empty invoice!');
        } 

		$allPlans = Plan::whereIn('id',$plans)->get();
		$total_amount = $totaluser = $totalbranches = $totalmonth = $totalSMS = $totalEmail = $subscription_commission = 0;
        
        $salon_data = Distributor::find($request->salon_id); 

        $subscription_commission = 0; 

        //for update company data
		foreach($plans as $plan_id){  

            $plan_id = intval($plan_id);
            $plan = Plan::find($plan_id);  

            if($is_new_plan[$plan_id] == 0) { 
                $salonPlan = SalonPlan::find($salon_plans[$plan_id]); 
            } else {
                $salonPlan = new SalonPlan(); 
                $salonPlan->salon_id = $salon_data->id;   
            }
  
			$salonPlan->subscription_id = $subscription->id;
			$salonPlan->plan_id = $plan->id;
			$amount = $plan->price;
			$amount = Helper::decimalNumber($amount);
			$discount = intval($plans_discount[$plan->id]);
			$discount = Helper::decimalNumber($discount);
			$discount_amount = $amount * $discount / 100;
			$discount_amount = Helper::decimalNumber($discount_amount);
			$netamount = $amount - $discount_amount;
			

            // Count Total
            $total_amount += $netamount;
            $totaluser += $plan->no_of_users;  
            $totalbranches += $plan->no_of_branches; 
            $totalmonth += $plan->duration_months;
            $totalSMS += $plan->no_of_sms;
            $totalEmail += $plan->no_of_email; 
            
            $calculated_amount = $amount;
            if($discount != 0) {
                $calculated_amount =  $amount - ($amount * $discount / 100);
            }   
            if($salon_data->state_id === 12){ 
                $gst_array = Helper::getCalucatedGST($calculated_amount, $plan->sgst, $plan->cgst, 0);
                $plan_original_price = ($calculated_amount - $gst_array['sgst_amount'] - $gst_array['cgst_amount']);
            } else { 
                $gst_array = Helper::getCalucatedGST($calculated_amount, 0, 0, $plan->igst); 
                $plan_original_price = ($calculated_amount - $gst_array['igst_amount']);
            }

            if($user_subscription_commission > 0) { 
                $subscription_commission += $plan_original_price / 100 * $user_subscription_commission;
            }
			
			$salonPlan->plan_price = $amount;
			$salonPlan->discount = $discount;
			$salonPlan->discount_amount = $discount_amount;
            $salonPlan->final_amount = $netamount;
            $salonPlan->no_of_sms = $plan->no_of_sms;
            $salonPlan->no_of_email = $plan->no_of_email;
            $salonPlan->no_of_users = $plan->no_of_users;
            $salonPlan->no_of_branches = $plan->no_of_branches;
            $salonPlan->duration_months = $plan->duration_months;
            $salonPlan->subscription_date = $subscription_date[$plan->id]; 

            if($salon_data->state_id === 12){
                $salonPlan->sgst = $plan->sgst; 
                $salonPlan->sgst_amount = floatval($netamount) - floatval($netamount) * (100 / (100 + floatval($plan->sgst)));

                $salonPlan->cgst = $plan->cgst;
                $salonPlan->cgst_amount  = floatval($netamount) - floatval($netamount) * (100 / (100 + floatval($plan->cgst)));
            } else {
                $salonPlan->igst = $plan->igst;
                $salonPlan->igst_amount = floatval($netamount) - floatval($netamount) * (100 / (100 + floatval($plan->igst)));
            }  
			$salonPlan->save(); 
        }  
  
		$sgst_amount = Helper::decimalNumber($request->sgst_amount); 
		$cgst_amount = Helper::decimalNumber($request->cgst_amount); 
		$igst_amount = Helper::decimalNumber($request->igst_amount);
   
        if($salon_data && $request->payment_status != "YES"){ 
 
			if($salon_data->expiry_date != NULL && date("Y-m-d",strtotime($salon_data->expiry_date)) > date("Y-m-d"))
			{
				$startdate = date("Y-m-d", strtotime($salon_data->expiry_date));
			}else{
				$startdate = date("Y-m-d");
			}  

			if($totalmonth > 0){

                $expiry_date_check = date('Y-m-d', strtotime("+".$totalmonth." months", strtotime($salon_data->expiry_date))); 
                $subscription_expiry_date = date('Y-m-d', strtotime("+".$totalmonth." months", strtotime($salon_data->expiry_date)));

                if($startdate > $expiry_date_check){
                    $expiry_date = date('Y-m-d', strtotime("+".$totalmonth." months", strtotime($startdate)));
                }else{
                    $expiry_date = $expiry_date_check;
                }  
			    $salon_data->expiry_date = $expiry_date; // Add Subscription end date to salon (Distributor table)
                $subscription->subscription_expiry_date = $expiry_date; // Add Expiry Date to subscription
			}
    
            if(isset($salon_data->no_of_users)) {
                $total_users = $salon_data->no_of_users + $totaluser;
            } 
            if(isset($salon_data->no_of_branches)) { 
                $totalbranches = $salon_data->no_of_branches + $totalbranches;
            } 
            if(isset($salon_data->total_sms)) {
                $totalSMS = $salon_data->total_sms + $totalSMS;
            } 
            if(isset($salon_data->total_email)) {
                $totalEmail = $salon_data->total_email + $totalEmail;
            }
  
            $salon_data->no_of_users   = $total_users;
            $salon_data->no_of_branches   = $totalbranches;
            $salon_data->total_sms     = $totalSMS;
            $salon_data->total_email   = $totalEmail; 

			$salon_data->save();
        }

        $subscription->total_amount = $total_amount; 
		$subscription->sgst_amount = $sgst_amount; 
		$subscription->cgst_amount = $cgst_amount; 
		$subscription->igst_amount = $igst_amount;
        $subscription->final_amount = $total_amount;
        $subscription->payment_mode = $request->payment_mode;
        $subscription->payment_bank_name = $request->payment_bank_name;
        $subscription->payment_number = $request->payment_number;
        $subscription->payment_amount = $total_amount;
        $subscription->payment_date = $request->payment_date;

        // if($totalmonth>0 && $request->payment_status != "YES") { 
        //     $subscription->subscription_expiry_date = $subscription_expiry_date; // Add Expiry Date to subscription
        // }
        
        $subscription->is_payment_pending = $request->payment_status; 
        $subscription->updated_by = auth()->user()->id;
        $subscription->save();
         
        // Manage Commission
        if($subscription->is_payment_pending == "NO") { 
            $this->manageUserCommission($user, $subscription, $subscription_commission);
        } 
         
        return redirect(route('subscriptions.index').'?salon_id='.$salon_data->external_id)->with('success', "Subscription successfully updated!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        try{ 
            $subscription = Subscriptions::find($id);
            $salon = Distributor::find($subscription->salon_id);
            $subscription->delete();

            return redirect(route('subscriptions.index').'?salon_id='.$salon->external_id)->with('success', 'Subscription deleted successfully'); 
        }catch(Exception $e)
        {
            abort(500);
        }
    }

    public function cancel($id)
    {
        $message = 'Plan has been cancelled successfully';
        $subscriptionsId = decrypt($id);

        $subscription = Subscriptions::find($subscriptionsId);
        $company_id = $subscription->company_id;

        $companyDataUpdate = Company::find($company_id);
       
        $clientPlanGet = ClientPlan::where('subscription_id',$subscriptionsId)->get()->pluck('plan_id');
       
        $planGet = Plan::select(
            DB::raw("SUM(no_of_users) as userSum"),
            DB::raw("SUM(no_of_sms) as smsSum"),
            DB::raw("SUM(no_of_email) as emailSum"),
            DB::raw("SUM(duration_months) as monthSum")
        )->whereIn('id',$clientPlanGet)->get();
        
        $userSum    = $planGet[0]['userSum'];
        $smsSum     = $planGet[0]['smsSum'];
        $emailSum   = $planGet[0]['emailSum'];
        $monthSum   = $planGet[0]['monthSum'];
        
        $expiry_date = date('Y-m-d', strtotime("-".$monthSum." months", strtotime($companyDataUpdate->expiry_date)));

        $companyDataUpdate->no_of_users = $companyDataUpdate->no_of_users - $userSum;
        $companyDataUpdate->total_sms = $companyDataUpdate->total_sms - $smsSum;
        $companyDataUpdate->total_email = $companyDataUpdate->total_email - $emailSum;

        $companyDataUpdate->used_sms = $companyDataUpdate->used_email - $smsSum;
        $companyDataUpdate->used_email = $companyDataUpdate->used_email - $emailSum;
        $companyDataUpdate->expiry_date = $expiry_date;
        
        $clientPlan = ClientPlan::where('subscription_id',$subscriptionsId);

        $companyDataUpdate->save();
        $clientPlan->delete();
        $subscription->delete();

        return redirect('rkadmin/subscriptions?company_id='.encrypt($company_id))->with('success', $message);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deletePlan($id)
    { 
        $message = 'Plan has been deleted successfully';
        $plan = SalonPlan::find($id); 

        $plan_final_amount = $plan->final_amount;
        $subscription_id = $plan->subscription_id;
        $planData = Plan::find($plan->plan_id);

        $subscriptions = Subscriptions::find($subscription_id);    
 
        $subscriptions->sgst_amount -= $plan->sgst_amount;
        $subscriptions->cgst_amount -= $plan->cgst_amount;
        $subscriptions->igst_amount -= $plan->igst_amount;
        
        $subscriptions->total_amount -= $plan->final_amount;
        $subscriptions->final_amount -= $plan->final_amount; 
        $subscriptions->payment_amount -= $plan->final_amount;  
 
        $subscriptions->save();
  
        $plan->delete();
        
        // return redirect(route('subscriptions.index').'?salon_id='.$salon_data->external_id)->with('success', "Subscription successfully updated!");
        return redirect('admin/subscriptions/'.encrypt($subscription_id).'/edit')->with('success', $message);

    }
     
	public function getPlanDetail(Request $request)
    {
        $id = $request->plan_id;  
        $plan = Plan::find($id);
        return response()->json(['success'=>true,'plan'=>$plan]);
    }

	public function getCompanyDetail(Request $request)
    {
		 $company = Company::find($request->company_id);
		 return response()->json(['success'=>true, 'company'=>$company]);
    } 
     
}
