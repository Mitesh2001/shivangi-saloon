<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
 
use DB;
use Auth; 
use JWTAuth;  
use App\Models\UsersCommission;

class CommissionController extends Controller
{ 
    public function myCommission(Request $request)
    {
        $paginate = $request->paginate ?? 0; 
        $user = JWTAuth::parseToken()->authenticate();  
        $users_commission = UsersCommission::select(
            'users_commission.id as id',  
            'orders.order_uid as order_id',  
            'orders.payment_mode as payment_mode',  
            'orders.payment_amount as payment_amount',  
            'orders.payment_date as invoice_payment_date',  
            'users_commission.invoice_commission as commission_amount',  
            'users_commission.is_paid as is_paid',  
            'users_commission.invoice_json as json_details',  
        )
        ->leftJoin('orders', 'orders.id', '=', 'users_commission.order_id') 
        ->where('user_id', $user->id)
        ->orderBy('orders.id', 'desc');   
        
        if($paginate == 1) {
            $data = $users_commission->paginate();
            $count = count($data);
        } else { 
            $data = [];
            $data['data'] = $users_commission->get();
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
}
