<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DataTables;
use App\Models\Order;
use App\Models\Client;
use App\Models\Branch; 
use App\Models\Product; 
use App\Models\User;
use App\Models\ClientProduct;
use App\Models\EmailTemplate;
use Illuminate\Validation\Rule;
use Validator;
use Exception;
use App\Helpers\Helper;
use Mail;
use PDF;
use DB;
use URL;
use Ramsey\Uuid\Uuid; 
use Illuminate\Support\Facades\Auth;
 
use App\Models\Distributor;
use App\Models\DealAndDiscount;
use App\Models\DealProductService;
use App\Models\Category;
use App\Models\DealLogs;
use App\Models\UsersCommission;
use App\Models\CommissionRelease;

class CommissionController extends Controller
{  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $start_range = $request->get('start_range');
        $end_range = $request->get('end_range');

        $title = "Employee commission";

        if(!empty($start_range) && !empty($end_range)) {
            $start_range = date('d-m-Y', strtotime($start_range));
            $end_range = date('d-m-Y', strtotime($end_range));
            $title = "Employee commission (from: $start_range to: $end_range)";
        }
        if(!empty($start_range) && empty($end_range)) {
            $start_range = date('d-m-Y', strtotime($start_range)); 
            $title = "Employee commission (from: $start_range)";
        }
        if(empty($start_range) && !empty($end_range)) { 
            $end_range = date('d-m-Y', strtotime($end_range));
            $title = "Employee commission (till: $end_range)";
        }
        $data['title'] = $title;
        $data['is_system_user'] = Helper::is_system_user();   

        return view('commission.index')->with($data); 
    }

    public function DistributorsCommission(Request $request) {

        if(!Helper::is_system_user()){
            return abort(403);
        }

        $start_range = $request->get('start_range');
        $end_range = $request->get('end_range');

        $title = "Distributors commission";

        if(!empty($start_range) && !empty($end_range)) {
            $start_range = date('d-m-Y', strtotime($start_range));
            $end_range = date('d-m-Y', strtotime($end_range));
            $title = "Distributors commission (from: $start_range to: $end_range)";
        }
        if(!empty($start_range) && empty($end_range)) {
            $start_range = date('d-m-Y', strtotime($start_range)); 
            $title = "Distributors commission (from: $start_range)";
        }
        if(empty($start_range) && !empty($end_range)) { 
            $end_range = date('d-m-Y', strtotime($end_range));
            $title = "Distributors commission (till: $end_range)";
        }
        $data['title'] = $title;
        $data['is_system_user'] = Helper::is_system_user();   

        return view('commission.distributor_commission')->with($data); 
    }

    /**
     *  Shows commission of authenticated user
     * 
     */
    public function myCommission(Request $request)
    { 
        $data['title'] = "MY Commission"; 
        $data['user'] = Auth()->user();
        $data['user_id'] = $data['user']->id; 

        if($data['user']->user_type == 1) {
            $data['type'] = "employees"; 
        } else { 
            $data['type'] = "distributor"; 
        }

        return view('commission.my_commission')->with($data);
    }


    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData(Request $request)
    {    
        $start_range = $request->get('start_range');
        $end_range = $request->get('end_range');
        $type = $request->get('type'); 
         
        $users_commission = UsersCommission::selectRaw('users_commission.*, sum(invoice_commission) as commission_amount')
        ->leftJoin('users', 'users.id', '=', 'users_commission.user_id');
         
        if($type == "employees") {
            $distributor_id = Helper::getDistributorId();
            if($distributor_id != 0) { // Check if distributor
                $users_commission->where('users_commission.distributor_id', $distributor_id);
            } 
            $users_commission->where('users_commission.user_type', '<=', 1);
        } else {
            $users_commission->where('users_commission.user_type', '>', 1);
        }

        if(!empty($start_range)) {
            $users_commission->whereRaw('date(users_commission.created_at) >= '."'$start_range'".'');
        }

        if(!empty($end_range)) {
            $users_commission->whereRaw('date(users_commission.created_at) <= '."'$end_range'".''); 
        }

        $users_commission = $users_commission->groupBy('user_id')->groupBy('is_paid')->get();  
  
        return Datatables::of($users_commission)
            ->addColumn('distributor', function ($users_commission) {
                return  $users_commission->getDistributor->name ?? "";
            })  
            ->addColumn('user_name', function ($users_commission) {
                $first_name = $users_commission->getUser->first_name ?? "";
                $last_name = $users_commission->getUser->last_name ?? "";
                return  $first_name . " ". $last_name;
            })  
            ->addColumn('commission_amount', function ($users_commission) { 
                if($users_commission->is_paid == 0) {
                    $payment_lable = '<span class="label label-inline label-lg bg-danger text-white font-weight-bolder">Unpaid</span>';
                } else {
                    $payment_lable = '<span class="label label-inline label-lg bg-primary text-white font-weight-bolder">Paid</span>';
                }
                 
                $commission = $users_commission->commission_amount ?? 0;
                return  Helper::decimalNumber($commission) ." $payment_lable";
            })  
            ->addColumn('commission_amount_simple', function ($users_commission) { 
                if($users_commission->is_paid == 0) {
                    $payment_lable = 'Unpaid';
                } else {
                    $payment_lable = 'Paid';
                }
                 
                $commission = $users_commission->commission_amount ?? 0;
                return  Helper::decimalNumber($commission) ." ($payment_lable)";
            })  
            ->addColumn('action', function ($users_commission) use ($start_range, $end_range, $type) {
                
                $is_paid =  $users_commission->is_paid;
                $url = url('admin/commissions/view?user='.$users_commission->getUser->external_id."&is_paid=".$is_paid."&type=".$type);
				$html = '<form action="'.route('product.destroy', $users_commission->external_id).'" class="d-flex" method="POST">';
 
                $html .= '<a href="'.$url.'" class="btn btn-link"><i class="flaticon-eye text-primary text-hover-primary text-hover-primary" data-toggle="tooltip" title="View Details"></i></a>';
                 
				if($is_paid == 0 && \Entrust::can(['distributor-commission-release', 'employee-commission-release'])) { 
                    if($users_commission->user_type < 1) {
                        if(!Helper::allowViewOnly($users_commission->distributor_id)) {
                            $html .= '<a href="#" class="btn btn-link release-commission" data-toggle="modal" data-user="'.$users_commission->getUser->id.'" data-target="#release-payment"><i class="flaticon2-check-mark text-primary text-hover-primary" data-toggle="tooltip" title="Release Commission"></i></a>'; 
                        }
                    } else {
                        $html .= '<a href="#" class="btn btn-link release-commission" data-toggle="modal" data-user="'.$users_commission->getUser->id.'" data-target="#release-payment"><i class="flaticon2-check-mark text-primary text-hover-primary" data-toggle="tooltip" title="Release Commission"></i></a>'; 
                    } 
                }
                $html .= '<input type="hidden" class="product_id" value="'.$users_commission->external_id.'">'; 
				$html .= csrf_field();
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['distributor', 'user_name', 'commission_amount_simple', 'commission_amount', 'action'])
            ->make(true);
    } 

    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function commissionDetails(Request $request)
    {    
        $profile = $request->get('profile');
        $user_id = $request->get('user_id');
        $is_paid = $request->get('is_paid'); 
        $type = $request->get('type'); // employee or distirubutor 

        $users_commission = UsersCommission::orderBy('id', 'desc')->where('user_id', $user_id);
        if(!$profile) { // if not profile view then apply condition
            $users_commission->where('is_paid', $is_paid);
        }  
        $users_commission = $users_commission->get();   
        
        return Datatables::of($users_commission)
            ->addColumn('order_id', function ($users_commission) use ($type) {  
                if($type == "employees") {
                    return  $users_commission->getOrder->order_uid ?? "";
                } else {
                    return  $users_commission->getSubscription->subscription_id ?? "";
                } 
            })   
            ->addColumn('payment_mode', function ($users_commission) use ($type) {

                if($type == "employees") {
                    return  $users_commission->getOrder->payment_mode ?? "";
                } else {
                    return  $users_commission->getSubscription->payment_mode ?? "";
                } 
            })   
            ->addColumn('payment_amount', function ($users_commission) use ($type) { 

                if($type == "employees") {
                    return  Helper::decimalNumber($users_commission->getOrder->payment_amount ?? 0);
                } else {
                    return  Helper::decimalNumber($users_commission->getSubscription->payment_amount ?? 0);
                } 
            })   
            ->addColumn('commission', function ($users_commission) use ($type) {
                
                return  Helper::decimalNumber($users_commission->invoice_commission ?? 0);
            })   
            ->addColumn('payment_date', function ($users_commission) use ($type) {

                if($type == "employees") {
                    if(isset($users_commission->getOrder->payment_date)) {
                        return  date('d-m-Y' ,strtotime($users_commission->getOrder->payment_date));
                    } else {
                        return "";
                    } 
                } else {
                    if(isset($users_commission->getSubscription->payment_date)) {
                        return  date('d-m-Y' ,strtotime($users_commission->getSubscription->payment_date));
                    } else {
                        return "";
                    } 
                } 
            })   
            ->addColumn('status', function ($users_commission) use ($type) { 
                if($users_commission->is_paid == 0) {
                    $payment_lable = '<span class="label label-inline label-lg bg-danger text-white font-weight-bolder">Unpaid</span>';
                } else {
                    $payment_lable = '<span class="label label-inline label-lg bg-primary text-white font-weight-bolder">Paid</span>';
                }
                return $payment_lable;
            })   
            ->addColumn('status_text', function ($users_commission) use ($type) { 
                if($users_commission->is_paid == 0) {
                    $payment_lable = 'Unpaid';
                } else {
                    $payment_lable = 'Paid';
                }
                return $payment_lable;
            })   
            ->addColumn('status_filter', function ($users_commission) use ($type) { 
                return $users_commission->is_paid;
            })   
            ->addColumn('action', function ($users_commission) use ($type) {
                $html = ''; 
                $html .= '<a data-toggle="modal" data-target="#commission-modal" class="btn btn-link view-commission-details"><i class="flaticon-eye text-primary text-hover-primary" data-toggle="tooltip" title="View Details"></i></a>'; 
                $html .= '<input type="hidden" class="commission_id" value="'.$users_commission->id.'" >';
                return $html;
            })   
            ->rawColumns(['order_id','payment_mode','payment_amount', 'commission','payment_date', 'status', 'status_filter', 'action'])
            ->make(true);
    } 

    public function commissionDetailsById(Request $request)
    {   
        $commission = UsersCommission::find($request->id);

        $html = "";

        if(!empty($commission)) {

            $commision_arr = json_decode($commission->invoice_json);

            if(is_array($commision_arr)) {
                $html .= "<tr>";
                $html .= "<th>Product Name</th>";
                $html .= "<th>SKU Code</th>";
                $html .= "<th>QTY</th>";
                $html .= "<th>Sales Price</th>";
                $html .= "<th>GST</th>";
                $html .= "<th>GST Amount</th>";
                $html .= "<th>Total Amount Before GST</th>";
                $html .= "<th>Commission</th>";
                $html .= "<th>Commission Amount</th>";
                $html .= "</tr>";
     
                foreach($commision_arr as $entry) {
                    $html .= "<tr>";
                    $html .= "<td>".($entry->product_name ?? "")."</td>";
                    $html .= "<td>".($entry->sku_code ?? "")."</td>";
                    $html .= "<td>".($entry->qty ?? 0)."</td>";
                    $html .= "<td> ₹". Helper::decimalNumber($entry->sales_price) ."</td>";
                    $html .= "<td> SGST : ".$entry->sgst."% <br> CGST : ".$entry->cgst."% <br> IGST : ".$entry->igst."%</td>";
                    $html .= "<td> ₹". Helper::decimalNumber($entry->gst_amount) ."</td>";
                    $html .= "<td> ₹". Helper::decimalNumber($entry->origina_price) ."</td>";
                    $html .= "<td>".($entry->commission ?? 0)."% </td>";
                    $html .= "<td> ₹". Helper::decimalNumber($entry->commission_amount) ."</td>";
                    $html .= "</tr>";
                }
            } else {
                $html .= ' <tr><td>No Records Found!</td></tr>';
            }
 
        } else {
            $html .= ' <tr><td>No Records Found!</td></tr>';
        }

        return $html;
    }
    
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function show(Request $request)
    {     
        $user_external_id = $request->get('user');
        $is_paid = $request->get('is_paid');
        $type = $request->get('type');
  
        $data['user'] = User::where('external_id', $user_external_id)->first();
        $data['user_id'] = $data['user']->id;
        $data['is_paid'] = $is_paid;
        $data['status'] = $is_paid == 0 ? "Unpaid" : "Paid";
        $data['title'] = $data['status'] ." Commission of ". $data['user']->first_name ." ". $data['user']->last_name;

        if($type == "employees") {
            $data['back_url'] = route('commissions.index');
        } else {
            $data['back_url'] = route('commissions.distributors');
        }

        return view('commission.show')->with($data);
    }  

    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function releaseCommission(Request $request)
    {      
        $login_user = Auth::user();
        $user_id = $request->user_id;
        $payment_type = $request->payment_type;
 
        $unpaid_commission = UsersCommission::where('user_id', $user_id)->where('is_paid', 0);
        $unpaid_json = json_encode($unpaid_commission->get());;
        $unpaid_amount = $unpaid_commission->sum('invoice_commission');
        
        CommissionRelease::create([
            'external_id' => Uuid::uuid4()->toString(),
            'user_id' => $user_id,
            'payment_method' => $payment_type,
            'commission_amount' => $unpaid_amount,
            'commission_json' => $unpaid_json,
            'released_by' => $login_user->id, 
            'distributor_id' => $login_user->distributor_id,
        ]);

        $unpaid_commission->update([
            'is_paid' => 1,
        ]);

        return redirect()->back()->with('success', 'Commission released successfully!'); 
    }  
}
