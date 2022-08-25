<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Datatables;

use App\Models\User;
use App\Models\Branch;
use App\Models\Daybook; 
use App\Models\StockIncomeHistory;

use App\Http\Requests\Daybook\StoreCashInRequest;
use App\Http\Requests\Daybook\StoreCashOutRequest;

use App\Helpers\Helper;
use App\Models\Distributor;

class DaybookController extends Controller
{
            
    public function __construct()
    {
        $this->middleware('permission:daybook-view', ['only' => ['index']]); 
        $this->middleware('permission:daybook-cash-in-entry', ['only' => ['storeCashIn']]); 
        $this->middleware('permission:daybook-cash-out-entry', ['only' => ['storeCashOut']]); 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['is_system_user'] = Helper::getDistributorId();
        $data['distributors'] = Distributor::all();

        $branch_id = Auth::user()->branch_id; 
        if($data['is_system_user'] != 0) {
            $data['selected_branch'] = Branch::where('id', $branch_id)->pluck('name', 'id');
        }   

        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        } 

        return view('daybook.index')->with($data);
    }

    public function storeCashIn(StoreCashInRequest $request)
    { 
        $user_id = Auth::id();
        $branch_id = Auth::user()->branch_id;

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;
            $branch_id = $request->branch_id;
        }  

        if(Helper::allowViewOnly($distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }
  
        $this->storeTodaysOpeningBalance($distributor_id, $branch_id);
        $cashIn = Daybook::create([
            'external_id' => Uuid::uuid4()->toString(),
            'amount' => $request->amount,
            'description' => $request->description,
            'entry_type' => 0, // (0 = cash in, 1 = cash out)
            'branch_id' => $branch_id,
            'date' => date('Y-m-d'),
            'created_by' => $user_id, 
            'updated_by' => $user_id, 
            'distributor_id' => $distributor_id,
        ]); 

        
        Session()->flash('success', __('Cash in successfully added!'));
        return redirect()->route('daybook.index');
    }       

    public function storeCashOut(StoreCashOutRequest $request)
    { 
        $user_id = Auth::id();
        $branch_id = Auth::user()->branch_id;

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;
            $branch_id = $request->branch_id;
        }   

        if(Helper::allowViewOnly($distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }
        
        $this->storeTodaysOpeningBalance($distributor_id, $branch_id);
        $cashIn = Daybook::create([
            'external_id' => Uuid::uuid4()->toString(),
            'amount' => $request->amount,
            'description' => $request->description,
            'entry_type' => 1, // (0 = cash in, 1 = cash out)
            'payment_method' => $request->payment_method,
            'branch_id' => $branch_id,
            'date' => date('Y-m-d'),
            'created_by' => $user_id, 
            'updated_by' => $user_id, 
            'distributor_id' => $distributor_id,
        ]); 
        

        Session()->flash('success', __('Cash Out successfully added!'));
        return redirect()->route('daybook.index');
    }  

    public function storeTodaysOpeningBalance($distributor_id, $branch_id)
    {
        $user_id = Auth::id(); 
  
        $todays_opening_balance = Daybook::where('payment_method', 'Opening Balance')->where('date', date('Y-m-d'))->where('branch_id', $branch_id)->where('distributor_id', $distributor_id)->count();
 
        // Last Total Opening Balance  
        $last_opening = Daybook::where('payment_method', 'Opening Balance')->where('distributor_id', $distributor_id);
        if(!empty($branch_id)) { 
            $last_opening->where('branch_id', $branch_id);
        }
        // $last_opening = $last_opening->where('date', '<=', date('Y-m-d', strtotime($date)))->first();
        $last_opening = $last_opening->orderBy('id', 'desc');
        $last_opening_balance = $last_opening->sum('amount');
        $last_opening = $last_opening->first();  

        $last_opening_balance = intval($last_opening->amount ?? 0);
  
        $last_cash_in = Daybook::where('branch_id', $branch_id)->where('distributor_id', $distributor_id)->orderBy('id', 'desc')->first();
        if(!empty($last_cash_in)) {
            $total_cash_in = Daybook::where('date', date('Y-m-d', strtotime($last_cash_in->date)))->where('entry_type', 0)->where('branch_id', $branch_id)->where('distributor_id', $distributor_id)->sum('amount');
            $total_cash_out = Daybook::where('date', date('Y-m-d', strtotime($last_cash_in->date)))->where('entry_type', 1)->where('branch_id', $branch_id)->where('distributor_id', $distributor_id)->sum('amount');
 
            $total_opening_balance = ($last_opening_balance + $total_cash_in) - $total_cash_out;  
        } else {
            $total_opening_balance = 0;
        }
 
        if($todays_opening_balance == 0) {
            $cashIn = Daybook::create([
                'external_id' => Uuid::uuid4()->toString(),
                'amount' => $total_opening_balance,
                'entry_type' => 3, // (0 = cash in, 1 = cash out, 3 = opening balance)
                'payment_method' => 'Opening Balance',
                'branch_id' => $branch_id,
                'date' => date('Y-m-d'),
                'created_by' => $user_id, 
                'updated_by' => $user_id, 
                'distributor_id' => $distributor_id,
            ]);  
        }  
    }
    
    public function entriesByDate(Request $request) { 
          
        $distributor_id = Helper::getDistributorId(); 
        
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;  
        }  

        $branch_id = $request->branch_id;
        $date = $request->date;   
   
        $date = !empty($date) ? date('Y-m-d', strtotime($date)) : date('Y-m-d');


        // Last Opening Balance
        $last_opening = Daybook::where('payment_method', 'Opening Balance')->where('distributor_id', $distributor_id);
        $last_opening->where('date', '<=', date('Y-m-d', strtotime('-1 day', strtotime($date))));
        $last_opening = $last_opening->orderBy('id', 'desc')->first();
 
        $last_opening_balance = intval($last_opening->amount ?? 0);


        if(!empty($last_opening)) {
            $search_date = $last_opening->date; 
        } else {
            $search_date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
        }  

        // last Day Closing Balance
        $total_cash_in = Daybook::where('date', $search_date)->where('distributor_id', $distributor_id);
        if(!empty($branch_id)) { 
            $total_cash_in->where('branch_id', $branch_id);
        }
        $total_cash_in = $total_cash_in->where('entry_type', 0)->sum('amount');

        $total_cash_out = Daybook::where('date', $search_date)->where('distributor_id', $distributor_id);
        if(!empty($branch_id)) { 
            $total_cash_out->where('branch_id', $branch_id);
        }
        $total_cash_out = $total_cash_out->where('entry_type', 1)->sum('amount'); 

        $yesterdays_closing = ($total_cash_in + $last_opening_balance)  - $total_cash_out; 
         
        // Today's closing balance
        $total_cash_in = Daybook::where('date', $date)->where('distributor_id', $distributor_id);
        if(!empty($branch_id)) { 
            $total_cash_in->where('branch_id', $branch_id);
        }
        $total_cash_in = $total_cash_in->where('entry_type', 0)->sum('amount');

        $total_cash_out = Daybook::where('date', $date)->where('distributor_id', $distributor_id);
        if(!empty($branch_id)) { 
            $total_cash_out->where('branch_id', $branch_id);
        }
        $total_cash_out = $total_cash_out->where('entry_type', 1)->sum('amount');
        
        $todays_closing = ($total_cash_in - $total_cash_out) + $yesterdays_closing;

        $date = !empty($date) ? date('Y-m-d', strtotime($date)) : date('Y-m-d');
        $data_by_date = Daybook::where('date', $date)->selectRaw('*, SUM(amount) as total_amount')->where('distributor_id', $distributor_id); 
        if(!empty($branch_id)) { 
            $data_by_date->where('branch_id', $branch_id);
        }
        $data_by_date = $data_by_date->groupBy('date')->groupBy('payment_method')->get();
  
        $html = "";
        $html .= "<tr>
                    <td> <b>Opening Balance</b> </td>
                    <td></td>
                    <td></td>   
                    <td> <b>$yesterdays_closing</b> </td>   
                </tr>";
        if(count($data_by_date) > 0){ 
            foreach($data_by_date as $entry) { 
 
                if($entry->entry_type == 0){
                    $cashIn = $entry->total_amount;
                    $cashOut = "";
                    $entry_type_string = "Cash In";
                } else {
                    $cashIn = "";
                    $cashOut = $entry->total_amount;
                    $entry_type_string = "Cash Out";
                }
                $payment_method = $entry->payment_method == "" ? "Cash In" : $entry->payment_method;

                if($payment_method == "Opening Balance") {
                    continue;
                }

                $html .= "<tr>
                            <td> <b>$payment_method</b> </td>
                            <td>$cashIn</td>
                            <td>$cashOut</td> 
                            <td>
                                <a href='#' class='btn btn-link view-in-modal' data-toggle='modal' data-target='#view-daybook-modal'>
                                    <i class='flaticon-eye text-primary text-hover-primary' data-toggle='tooltip' title='View Details'></i>
                                </a>
                                <input type='hidden' class='branch_id' value='".$entry->branch_id."'> 
                                <input type='hidden' class='payment_method' value='".$entry->payment_method."'> 
                                <input type='hidden' class='entry_type' value='".$entry->entry_type."'> 
                                <input type='hidden' class='date' value='".$entry->date."'> 
                                <input type='hidden' class='entry_type_string' value='".$entry_type_string."'> 
                            </td> 
                         </tr>"; 
            } 
        } 
        $html .= "<tr>
                    <td> <b>Closing Balance</b> </td>
                    <td></td>
                    <td></td>   
                    <td> <b>$todays_closing</b> </td>   
                </tr>";

        $arr = [
            'total_opening_balance' => $yesterdays_closing,
            'total_closing_balance' => $todays_closing,
            'entries' => $html,
        ];

        return response()->json($arr);
    }
    
    public function getEntryDetails(Request $request)
    {
        $branch_id = $request->branch_id;
        $payment_method = $request->payment_method;
        $entry_type = $request->entry_type;
        $date = $request->date;

        $query = Daybook::where('date', $date);
        $query->where('branch_id', $branch_id);
        if($entry_type != 0) 
        {
            $query->where('payment_method', $payment_method);
        }
        $query->where('entry_type', $entry_type);
        $resultList = $query->get();

        $table = "<table class='table table-hover table-bordered'>";
        $table .= "<thead>";
        $table .= "<tr><th width='30%'>Amount</th><th width='70%'>Description</th></tr>";
        $table .= "<tbody>";
        $table .= "</thead>";
        
        foreach($resultList as $entry) 
        {
            $table .= "<tr>";
            $table .= "<td>". $entry->amount ."</td>";
            $table .= "<td>". $entry->description ."</td>";
            $table .= "</tr>";
        }
        $table .= "</tbody>";
        $table .= "</table>";

        return response()->json([
            'status' => true,
            'table' => $table,
        ]);
    }
}
