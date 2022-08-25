<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
 
use DB;
use Auth; 
use JWTAuth; 
use Datatables;

use Ramsey\Uuid\Uuid;
use App\Helpers\Helper;
use App\Models\Distributor;

use App\Models\User;
use App\Models\Branch;
use App\Models\Daybook; 
use App\Models\StockIncomeHistory;

class DaybookController extends Controller
{
    public function index(Request $request)
    { 
        // Auth User
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        } 
 
        $user_id = intval($user->id);
        if(!isset($request->branch_id)){
            $branch_id = intval($user->branch_id); 
        } else {
            $branch_id = intval($request->branch_id); 

            $check_branch = Branch::where('id', $branch_id)->where('distributor_id', $user->distributor_id)->count();

            if($check_branch < 1) {
                return response()->json([
                    'status' => 'FAIL',
                    'message' => 'Not authorize to view selected branch data!.'
                ]);
            }

        } 
        $distributor_id = intval($user->distributor_id); 
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

        $groupByEntries = [];
        $groupByEntries['total_opening_balance'] = $yesterdays_closing;
        $groupByEntries['entries'] = [];

        foreach($data_by_date->toArray() as $arr) {
            if($arr['payment_method'] != "Opening Balance") {
                $method = $arr['payment_method'] == "" ? "Cash In" : $arr['payment_method'];
                if($arr['entry_type'] == 0){
                    $cashIn = $arr['total_amount'];
                    $cashOut = "";
                } else {
                    $cashIn = "";
                    $cashOut = $arr['total_amount'];
                } 

                array_push($groupByEntries['entries'], [
                    'method' => $method,
                    'cash-in' => $cashIn,
                    'cash-out' => $cashOut,
                ]); 
            }  
        }
        $groupByEntries['total_closing_balance'] = $todays_closing; 
        return $groupByEntries;
        
        $groupByEntries = array_map(function ($arr) { 

            if($arr['payment_method'] != "Opening Balance") {
                $method = $arr['payment_method'] == "" ? "Cash In" : $arr['payment_method'];
                if($arr['entry_type'] == 0){
                    $cashIn = $arr['total_amount'];
                    $cashOut = "";
                } else {
                    $cashIn = "";
                    $cashOut = $arr['total_amount'];
                } 
    
                return [
                    'method' => $method,
                    'cash-in' => $cashIn,
                    'cash-out' => $cashOut,
                ];
            }  

        }, $data_by_date->toArray());
        
        $arr = [
            'date' => $date,
            'total_opening_balance' => $yesterdays_closing,
            'entries' => array_filter($groupByEntries),
            'total_closing_balance' => $todays_closing,
        ];

        return response()->json($arr);
    }
    
    public function storeCashIn(Request $request)
    { 
        // Auth User
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        } 
 
        $user_id = intval($user->id);
        $branch_id = intval($user->branch_id); 
        $distributor_id = intval($user->distributor_id); 

        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }
  
        $this->storeTodaysOpeningBalance($distributor_id, $branch_id, $user_id); 

        $cashIn = Daybook::create([
            'external_id' => Uuid::uuid4()->toString(),
            'amount' => $request->amount,
            'description' => $request->description ?? "",
            'entry_type' => 0, // (0 = cash in, 1 = cash out)
            'branch_id' => $branch_id,
            'date' => date('Y-m-d'),
            'created_by' => $user_id, 
            'updated_by' => $user_id, 
            'distributor_id' => $distributor_id,
        ]);  

        return response()->json([
            'status' => 'SUCCESS',
            'message' => "Cash in successfully added!"
        ]); 
    }       

    public function storeCashOut(Request $request)
    { 
        // Auth User
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        } 
 
        $user_id = intval($user->id);
        $branch_id = intval($user->branch_id); 
        $distributor_id = intval($user->distributor_id); 

        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }
        
        $this->storeTodaysOpeningBalance($distributor_id, $branch_id, $user_id);
        $cashIn = Daybook::create([
            'external_id' => Uuid::uuid4()->toString(),
            'amount' => $request->amount,
            'description' => $request->description ?? "",
            'entry_type' => 1, // (0 = cash in, 1 = cash out)
            'payment_method' => $request->payment_method,
            'branch_id' => $branch_id,
            'date' => date('Y-m-d'),
            'created_by' => $user_id, 
            'updated_by' => $user_id, 
            'distributor_id' => $distributor_id,
        ]);  

        return response()->json([
            'status' => 'SUCCESS',
            'message' => "Cash Out successfully added!"
        ]);  
    }  

    public function storeTodaysOpeningBalance($distributor_id, $branch_id, $user_id)
    {  
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
}  