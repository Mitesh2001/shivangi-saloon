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
use App\Models\StockMaster;

class OrdersController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('permission:order-view', ['only' => ['index', 'show', 'all']]);
		$this->middleware('permission:order-create', ['only' => ['create','store']]);
		$this->middleware('permission:order-update', ['only' => ['edit','update']]);
		$this->middleware('permission:order-delete', ['only' => ['cancel']]);  
    }

    public function currentUser(){
        return Auth::user();
    }
    
    /**
     * Display a listing client wise.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        try{  
            $client_id = decrypt($request->client_id); 
            $data['client_id'] = $request->client_id;
            
            $client = Client::where('id', $client_id);
 
            // Check if user is current distributor
            $distributor_id = Helper::getDistributorId();
            if($distributor_id != 0) { // system user
                $client = $client->where('distributor_id', $distributor_id);
            } 
            $client = $client->firstOrFail();
            
            $data['payment_modes'] = array_merge(array(''=>'Payment Mode'), config('global.payment_modes'));
            $data['client_data'] = $client;
            $data['branch_data'] = $this->getBranch();
            $data['branches'] = Branch::pluck('name', 'name')->toArray();

            $data['is_system_user'] = Helper::is_system_user();  
            $data['distributors'] = Distributor::all();

            return view('orders.index')->with($data);  
        }catch(Exception $e)
        {
            abort(404);
        }
    }

    /**
     * Display a listing all the orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        try{
            $payment_modes = config('global.payment_modes');
            $data['payment_modes'] = array_merge(array(''=>'Select Payment Mode'),$payment_modes);
            $data['branches'] = Branch::pluck('name', 'name')->toArray();
            $data['is_system_user'] = Helper::is_system_user();  
            $data['distributors'] = Distributor::all();


            return view('orders.all')->with($data);
        }catch(Exception $e)
        {
            abort(404);
        }
    }
    	
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try{
            $client_id = decrypt($request->client_id);
            $data['client_id'] = $client_id; 
            $data['branch_id'] = Auth::user()->branch_id;
            $data['branch_data'] = $this->getBranch();

            $distributor_id = Helper::getDistributorId();
            if($distributor_id == 0) { // system user 
                $data['client_data'] = Client::where('id', $client_id)->first(); 
            } else {
                $data['client_data'] = Client::where('id', $client_id)->where('distributor_id', $distributor_id)->firstOrFail();
            }
               
            $data['branches'] = Branch::where('distributor_id', $data['client_data']->distributor_id)->pluck('name', 'id');
            $data['payment_modes'] = array_merge(array(''=>'Select Payment Mode'), config('global.payment_modes'));
            $data['is_system_user'] = Helper::getDistributorId();
  
            return view('orders.create')->with($data); 
        }catch(Exception $e){
            abort(404);
        }
    } 

    // Manage Stock of products
    private function ManageProductStock($product, $branch_id, $remove_qty, $old_qty = 0) // Old stock for update product qty (will add old_stock + current stock - new qty)
    {  
        $stock_level = StockMaster::where('product_id', $product->id)->where('branch_id', $branch_id)->first();

        $current_stock_lvl = $stock_level->qty;
        $total_stock_lvl = $old_qty + $current_stock_lvl;
   
        if(!empty($stock_level)) {
            if($total_stock_lvl >= $remove_qty) { 
                $stock_qty = $total_stock_lvl;
                $product_qty_current = $remove_qty;
                $stock_level->update([
                    'qty' => $stock_qty - $product_qty_current,
                ]);
            } else {
                if($total_stock_lvl == 0) { 
                    return redirect()->back()->withErrors("Product not available in stock!")->withInput(); 
                } else {
                    return redirect()->back()->withErrors("Only $total_stock_lvl $product->name available in stock!")->withInput();  
                }
            }
        } else {
            return redirect()->back()->withErrors("Product not available in stock!")->withInput();
        }  
    }

    // Manage User Commission
    public function manageUserCommission($user, $order, $service_commission, $product_commission)
    {
        $distributor_id = Helper::getDistributorId(); 
        
        // Current Commission % of user
        $user_product_commission = Auth::user()->product_commission; 
        $user_service_commission = Auth::user()->service_commission;

        if($user_product_commission > 0 || $user_service_commission > 0) { 
            $invoice_json = json_encode($order);  

            $invoice_commission = $product_commission + $service_commission;
             
            $userCommission = new UsersCommission();
            $userCommission->external_id = Uuid::uuid4()->toString();
            $userCommission->user_id = $user->id;
            $userCommission->order_id = $order->id;
            $userCommission->user_product_commission = $user_product_commission;
            $userCommission->user_service_commission = $user_service_commission;
            $userCommission->invoice_json = $invoice_json;
            $userCommission->invoice_commission = $invoice_commission; 
            $userCommission->product_commission = $product_commission; 
            $userCommission->service_commission = $service_commission; 
            $userCommission->is_paid = 0; 
            $userCommission->distributor_id = $distributor_id;

            $userCommission->save();
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
            // 'company_id' => 'required|numeric',
            'products' => 'required|array|min:1',
            'payment_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:today',
            'payment_bank_name' => 'nullable|max:40|regex:/^[A-Za-z ]+$/u',
            'payment_number' => 'nullable|max:16|regex:/^[0-9]+$/u'
        ],
        [
            'products.required' => "Can not submit empty invoice!",
        ]);   

        $client_id =  $request->client_id;
        $distributor_id = Helper::getDistributorId(); 
        $branch_id = Auth::user()->branch_id ?? 0 ; 

        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;
            $branch_id = $request->branch_id;
        }   
        
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }
  
        $order = new Order();
         
        $orders_uid_count = Order::withTrashed()->count();  
        $orders_uid = str_pad($orders_uid_count, 6, '0', STR_PAD_LEFT);  
       
        $products = $request->products; 
        $allProducts = Product::whereIn('id',$products)->get(); // Product
        $product_discount = $request->products_discount; // Array of products discount with key product id
        $deal_discounts = $request->deal_discount;  // Array of deal discount with key product id 
        $product_qty = $request->order_qty; 
        $order_date = $request->order_date;
		 
       
        // Commission vars
        $user = Auth::user();
        $user_product_commission = Auth::user()->product_commission;
        $user_service_commission = Auth::user()->service_commission;
        $product_commission = 0;
        $service_commission = 0;

        $total_amount = $totaluser = $totalmonth = 0;  
 
		foreach($allProducts as $product){ 

            // Manage Stock if type is product
            if($product->type == 0) { 
                $this->ManageProductStock($product, $branch_id, $product_qty[$product->id]); 
            } 

			$amount = $product->sales_price;
            $amount = $product->sales_price * $product_qty[$product->id]; 
  
            // Caculate Commission of user
            if($user_product_commission > 0 && $product->type == 0) { 
                $product_commission += $amount / 100 * $user_product_commission;
            }
            if($user_service_commission > 0 && $product->type == 1) { 
                $service_commission += $amount / 100 * $user_service_commission;
            }
            
			$deal_discount = $deal_discounts[$product->id]; 
			$discount = $product_discount[$product->id]; 
			$discount = Helper::decimalNumber($discount) + Helper::decimalNumber($deal_discount);
			$discount_amount = $amount * $discount / 100;
			$discount_amount = Helper::decimalNumber($discount_amount);
			$netamount = $amount - $discount_amount; 
			$netamount = Helper::decimalNumber($netamount);
			$total_amount += $netamount;
        }     

        $client_data = Client::find($client_id); 
        $client_primary_id = $client_data->id; 
   
		$sgst_amount = Helper::decimalNumber($request->sgst_amount); // Total Bill SGST
		$cgst_amount = Helper::decimalNumber($request->cgst_amount); // Total Bill CGST
		$igst_amount = Helper::decimalNumber($request->igst_amount); // Total Bill IGST
          
        $order->external_id = Uuid::uuid4()->toString();
        $order->client_id = $client_primary_id;
        $order->branch_id = $branch_id;
        $order->total_amount = $total_amount;
		$order->sgst = 0;
		$order->sgst_amount = $sgst_amount;
		$order->cgst = 0;
		$order->cgst_amount = $cgst_amount;
		$order->igst = 0;
		$order->igst_amount = $igst_amount;
        $order->final_amount = $total_amount;
        $order->payment_mode = $request->payment_mode;
        $order->payment_bank_name = $request->payment_bank_name;
        $order->payment_number = $request->payment_number;
        $order->payment_amount = $total_amount;
        $order->payment_date = $request->payment_date;
        $order->is_payment_pending = $request->payment_status;
        $order->round_off_amount = 0;
        $order->order_uid = $orders_uid;
        $order->created_by = auth()->user()->id;
        $order->distributor_id = $distributor_id;
        $order->deal_id = $request->deal_id;
        $order->discount_code = $request->discount_code; 
        $order->save(); 
         
        // Add Commission for User / Employee
        if($order->is_payment_pending == "NO" && $user->distributor_id != 0) {
            $this->manageUserCommission($user, $order, $service_commission, $product_commission);
        }
 
        // Store Deal History
        if($request->deal_id !== 0) {
            $deal = DealAndDiscount::find($request->deal_id);
            $deal_json = json_encode($deal);
            
            $dealLog = DealLogs::create([
                'deal_id' => $request->deal_id,
                'order_id' => $order->id,
                'deal_json' => $deal_json,
            ]);
        }
        
        // Client Products
		foreach($allProducts as $product){  
			$client_product = new clientProduct();

            $client_product->client_id = $client_primary_id;
            $client_product->product_id = $product->id;
            $client_product->order_id = $order->id; 

            $amount = $product->sales_price;
            $amount = Helper::decimalNumber($amount);
            $deal_discount = $deal_discounts[$product->id];
            $discount = $product_discount[$product->id];
            $qty = $product_qty[$product->id];
			$discount = Helper::decimalNumber($discount) + Helper::decimalNumber($deal_discount);
            $discount_amount = $amount * $discount / 100;
            $discount_amount = Helper::decimalNumber($discount_amount);
            $netamount = ($amount - $discount_amount) * $qty;
            $netamount = Helper::decimalNumber($netamount); 
			
			$client_product->qty = $qty;
			$client_product->product_price = $amount;
			$client_product->deal_discount = $deal_discounts[$product->id];
			$client_product->discount = $product_discount[$product->id];
			$client_product->discount_amount = $discount_amount;
            $client_product->final_amount = $netamount; 
            $client_product->order_date = $order_date;
            $order->distributor_id = $distributor_id;
 
            if($client_data->state_id === 12){
                $client_product->sgst = $product->sgst;
                $client_product->cgst = $product->cgst;

                $client_product->sgst_amount = $netamount * $product->sgst / 100;
                $client_product->cgst_amount = $netamount * $product->cgst / 100;
            } else {
                $client_product->igst = $product->igst;
                $client_product->igst_amount = $netamount * $product->igst / 100;
            }
 
			$client_product->save();
		} 
        $message = 'Order has been added successfully'; 
        return redirect(route("orders.index", ['client_id' => encrypt($client_id)]))->with('success', $message);
    }
 
    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            // 'company_id' => 'required|numeric',
            'payment_date' => 'nullable|date',
            'payment_bank_name' => 'nullable|max:40|regex:/^[A-Za-z ]+$/u',
            'payment_number' => 'nullable|max:16|regex:/^[0-9]+$/u'
        ]);
		// dd($validation->errors());
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }
        
        $order = new Order();
        $message = 'Order has been added successfully';
        if($request->order_id)
        {
            $orderId = decrypt($request->order_id); 
            $order = Order::find($orderId); 
            $message = 'Order has been updated successfully';
        } 
        if(isset($request->product_add_id) && isset($request->product_update))
        {
            $products = array_merge($request->product_add_id, $request->product_update);
            $products_discount = $request->plans_discount + $request->plans_add_discount;
        }else if(isset($request->product_add_id)){
            $products = $request->product_add_id;
            $products_discount = $request->plans_add_discount;
        }else if(isset($request->product_update)){
            $products = $request->product_update;
            $products_discount = $request->plans_discount;
        }

        if(!isset($products)){
            return redirect()->back()->with('error', 'Can not submit empty invoice!');
        }
        
        // dd($product_discount);

        $product_update_data = $request->product_update_data;
        $new_plans_add = $request->product_add_id;
        $products_add_price = $request->plans_add_price;
 
        $order_add_date = $request->order_add_date;
        $products_add_discount = $request->plans_add_discount;
        $product_qty = $request->order_qty;  
        
        $order_date = date('Y-m-d', strtotime($request->order_date));
        $products_price = $request->plans_price;
   
        $allProducts = Product::whereIn('id',$products)->get();
        $total_amount = $totaluser = $totalmonth = $no_of_sms = $no_of_email = $used_sms = $used_email = 0;
        
        $companyData = Client::find($request->client_id); 
        
        $user = Auth::user();
        $user_product_commission = Auth::user()->product_commission;
        $user_service_commission = Auth::user()->service_commission;
        $product_commission = 0;
        $service_commission = 0;

        //for update company data
		foreach($products as $product_id){ 

            $product = Product::find($product_id);
            $amount = $product->sales_price;
            $amount = $product->sales_price * $product_qty[$product->id]; 

            if($user_product_commission > 0 && $product->type == 0) { 
                $product_commission += $amount / 100 * $user_product_commission;
            }
            if($user_service_commission > 0 && $product->type == 1) { 
                $service_commission += $amount / 100 * $user_service_commission;
            }

			$discount = $products_discount[$product->id]; 
			$discount = Helper::decimalNumber($discount);
			$discount_amount = $amount * $discount / 100;
			$discount_amount = Helper::decimalNumber($discount_amount);
			$netamount = $amount - $discount_amount; 
			$netamount = Helper::decimalNumber($netamount);
			$total_amount += $netamount; 
        } 

        $distributor_id = Helper::getDistributorId();
        $branch_id = Auth::user()->branch_id;
        if($distributor_id == 0){
            $branch_id = $request->branch_id;
        }


        if(isset($request->product_update_data)){
            foreach($product_update_data as $product){ 

                $clientProduct = ClientProduct::find($product);
                $product = Product::find($clientProduct->product_id);

                if($product->type == 0) {
                    $old_qty = $clientProduct->qty;
                    $new_qty = $product_qty[$product->id];
 
                    $stock_level = StockMaster::where('product_id', $product->id)->where('branch_id', $branch_id)->first();

                    $current_qty = $stock_level->qty;
                    $original_qty = $current_qty + $old_qty;
           
                    if($original_qty >= $new_qty) {  
                        $product_qty_current = $new_qty;
                        $stock_level->update([
                            'qty' => $original_qty - $product_qty_current,
                        ]);
                    } else {
                        if($stock_level->qty == 0) { 
                            return redirect()->back()->withErrors("Product not available in stock!")->withInput(); 
                        } else {
                            return redirect()->back()->withErrors("Only $stock_level->qty $product->name available in stock!")->withInput();  
                        }
                    }      
                }

            }
        }
  
        if(isset($request->product_add_id)){ 
            foreach($new_plans_add as $product){

                $product_data = Product::find($product); 
                 
                if($product_data->type == 0) {
            
                    $stock_level = StockMaster::where('product_id', $product_data->id)->where('branch_id', $branch_id)->first();
  
                    if(!empty($stock_level)) {
                        if($stock_level->qty >= $product_qty[$product_data->id]) { 
                            $stock_qty = $stock_level->qty;
                            $product_qty_current = $product_qty[$product_data->id];
                            $stock_level->update([
                                'qty' => $stock_qty - $product_qty_current,
                            ]);
                        } else {
                            if($stock_level->qty == 0) { 
                                return redirect()->back()->withErrors("$product_data->name not available in stock!")->withInput(); 
                            } else {
                                return redirect()->back()->withErrors("Only $stock_level->qty $product_data->name available in stock!")->withInput();  
                            }
                        }
                    } else {
                        return response()->json(['success'=>false, 'message' => "$product_data->name not available in stock!"]);
                    } 
                } 
            }
        }
  
        //update plan data
        if(isset($request->product_update_data)){
            foreach($product_update_data as $product){ 
  
                $clientProduct = ClientProduct::find($product);
                $product = Product::find($clientProduct->product_id); 
  
                $clientProduct->order_id = $order->id;
                $qty = $product_qty[$product->id];
                $amount = $products_price[$product->id];
                $amount = Helper::decimalNumber($amount);
                $discount = $products_discount[$product->id];
                $discount = Helper::decimalNumber($discount);
                $discount_amount = $amount * $discount / 100;
                $discount_amount = Helper::decimalNumber($discount_amount);
                $netamount = $amount - $discount_amount;
                $netamount = Helper::decimalNumber($netamount);
                
                $clientProduct->qty = $qty;
                $clientProduct->product_price = $amount;
                $clientProduct->discount = $discount;
                $clientProduct->discount_amount = $discount_amount;
                $clientProduct->final_amount = $netamount; 
                $clientProduct->order_date = $order_date;

                if($companyData->state_id === 12){
                    $clientProduct->sgst = $product->sgst;
                    $clientProduct->cgst = $product->cgst;
    
                    $clientProduct->sgst_amount = $netamount * $product->sgst / 100;
                    $clientProduct->cgst_amount = $netamount * $product->cgst / 100;
                } else {
                    $clientProduct->igst = $product->igst;
                    $clientProduct->igst_amount = $netamount * $product->igst / 100;
                }

                $clientProduct->save();
            } 
        }
       
        //for add new plan
        if(isset($request->product_add_id)){ 
            foreach($new_plans_add as $product){  

                $product_data = Product::find($product); 
                $qty = $product_qty[$product];
                $clientProduct = new ClientProduct();               
                $clientProduct->client_id = $request->client_id;   
                $clientProduct->order_id = $order->id;
                $clientProduct->product_id = $product;
                $clientProduct->qty = $qty;
                $amount =  $products_add_price[$product];
                
                $amount = Helper::decimalNumber($amount);
                $discount = $products_add_discount[$product];
                $discount = Helper::decimalNumber($discount);
                $discount_amount = $amount * $discount / 100;
                $discount_amount = Helper::decimalNumber($discount_amount);
                $netamount = $amount - $discount_amount;
                $netamount = Helper::decimalNumber($netamount);
                
                $clientProduct->product_price = $amount;
                $clientProduct->discount = $discount;
                $clientProduct->discount_amount = $discount_amount;
                $clientProduct->final_amount = $netamount; 
                $clientProduct->order_date = $order_date; 

                // dd($netamount * $product_data->sgst / 100);

                if($companyData->state_id === 12){
                    $clientProduct->sgst = $product_data->sgst;
                    $clientProduct->cgst = $product_data->cgst;
    
                    $clientProduct->sgst_amount = $netamount * $product_data->sgst / 100;
                    $clientProduct->cgst_amount = $netamount * $product_data->cgst / 100;
                } else {
                    $clientProduct->igst = $product_data->igst;
                    $clientProduct->igst_amount = $netamount * $product_data->igst / 100;
                }

                $clientProduct->save(); 
            }
        }
  
        $sgst = Helper::decimalNumber($request->sgst);
		$sgst_amount = Helper::decimalNumber($request->sgst_amount);
		$cgst = Helper::decimalNumber($request->cgst);
		$cgst_amount = Helper::decimalNumber($request->cgst_amount);
		$igst = Helper::decimalNumber($request->igst);
		$igst_amount = Helper::decimalNumber($request->igst_amount);
 
        // if(isset($companyData->state_id)) {
        //     if($companyData->state_id === 12){
        //         $final_amount = $total_amount + $sgst_amount + $cgst_amount;
        //     }else{
        //         $final_amount = $total_amount + $igst_amount;
        //     } 
        // } else {
        //     $final_amount = $total_amount + $igst_amount;
        // }

        $order->total_amount = $total_amount;
		$order->sgst = 0;
		$order->sgst_amount = $sgst_amount;
		$order->cgst = 0;
		$order->cgst_amount = $cgst_amount;
		$order->igst = 0;
		$order->igst_amount = $igst_amount;
        $order->final_amount = $total_amount;
        $order->payment_mode = $request->payment_mode;
        $order->payment_bank_name = $request->payment_bank_name;
        $order->payment_number = $request->payment_number;
        $order->payment_amount = $total_amount;
        $order->payment_date = $request->payment_date;
        $order->is_payment_pending = $request->payment_status;
        $order->round_off_amount = round($total_amount);
        $order->created_by = auth()->user()->id; 
        $order->save(); 

        if($order->is_payment_pending == "NO" && $user->distributor_id != 0) {
 
            if($user_product_commission > 0 || $user_service_commission > 0) { 
                $invoice_json = json_encode($order);  

                $invoice_commission = $product_commission + $service_commission;
                 
                $userCommission = new UsersCommission();
                $userCommission->external_id = Uuid::uuid4()->toString();
                $userCommission->user_id = $user->id;
                $userCommission->order_id = $order->id;
                $userCommission->user_product_commission = $user_product_commission;
                $userCommission->user_service_commission = $user_service_commission;
                $userCommission->invoice_json = $invoice_json;
                $userCommission->invoice_commission = $invoice_commission; 
                $userCommission->product_commission = $product_commission; 
                $userCommission->service_commission = $service_commission; 
                $userCommission->is_paid = 0; // 0 = unpaid, 1 = paid  
                $userCommission->distributor_id = $order->distributor_id;
    
                $userCommission->save();
            }
        }

        return redirect(route("orders.index", ['client_id' => encrypt($request->client_id)]))->with('success', $message); 
       
    }

    public function salesSummeryView(Request $request)
    { 
        $start_range = $request->get('start_range');
        $end_range = $request->get('end_range');

        $title = "Sales Report";
        if(!empty($start_range) && !empty($end_range)) {
            $start_range = date('d-m-Y', strtotime($start_range));
            $end_range = date('d-m-Y', strtotime($end_range));
            $title = "Sales Report (from: $start_range to: $end_range)";
        }
        if(!empty($start_range) && empty($end_range)) {
            $start_range = date('d-m-Y', strtotime($start_range)); 
            $title = "Sales Report (from: $start_range)";
        }
        if(empty($start_range) && !empty($end_range)) { 
            $end_range = date('d-m-Y', strtotime($end_range));
            $title = "Sales Report (till: $end_range)";
        }

        $data['title'] = $title;
        $data['distributors'] = Distributor::all();
        $data['is_system_user'] = Helper::is_system_user();

        return view('orders.sales')->with($data);  
    }

    public function salesSummeryReport(Request $request)
    {
        $distributor_id = Helper::getDistributorId();
        $start_range = $request->get('start_range');
        $end_range = $request->get('end_range');
  
        $query = 'SELECT   
            salons.name as destributor_name,  
            branches.name as branch_name,
            products.id as product_id,
            products.name as product_name,
            products.type as product_type,
            SUM(clients_product.qty) as total_sell,
            date(clients_product.created_at) as date
        FROM orders
        LEFT JOIN branches
        ON branches.id = orders.branch_id
        LEFT JOIN distributors
        ON salons.id = orders.distributor_id
        LEFT JOIN clients_product
        ON orders.id = clients_product.order_id 
        LEFT JOIN products
        ON products.id = clients_product.product_id
        where clients_product.deleted_at is null'; 
 
        
        if($distributor_id != 0) { // is admin
            $query .= ' AND orders.distributor_id = '. $distributor_id; 
        } 
        if(!empty($start_range)) {  
            $query .= ' AND date(orders.created_at) >= date("'.$start_range.'")'; 
        } 
        if(!empty($end_range)) { 
            $query .= ' AND date(orders.created_at) <= date("'.$end_range.'")'; 
        }

        $query .= ' GROUP BY date(clients_product.created_at), clients_product.product_id, orders.branch_id order by date desc';
    
        $sales_report = DB::select($query);
 
        return Datatables::of($sales_report)
        ->addColumn('distributor', function ($sales_report) {
            return  $sales_report->destributor_name ?? "";
        })   
        ->addColumn('branch', function ($sales_report) {
            return  $sales_report->branch_name ?? "";
        })   
        ->addColumn('product_name', function ($sales_report) {
            return  $sales_report->product_name ?? "";
        })   
        ->addColumn('product_type', function ($sales_report) {
            if($sales_report->product_type == 0) {
                return "Product";
            } else {
                return "Service";
            }
        })   
        ->addColumn('total_sell', function ($sales_report) {
            return  $sales_report->total_sell ?? "";
        })   
        ->addColumn('date', function ($sales_report) {
            if(!empty($sales_report->date)) {
                return date('d-m-Y', strtotime($sales_report->date));
            } else {
                return "";
            }
        })   
        ->rawColumns(['distributor', 'branch', 'product_name', 'product_type', 'total_sell', 'date'])
        ->make(true);
    }
	
	/**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData(Request $request)
    {
        $payment_pending = ['YES','NO'];

        if($request->payment_pending == '0'){
            $payment_pending = ['YES','NO'];
        }else if($request->payment_pending == '1'){
            $payment_pending = ['YES'];
        }else if($request->payment_pending == '2'){
            $payment_pending = ['NO'];
        }else{
            $payment_pending = ['YES','NO'];
        }

        $client_id = decrypt($request->client_id);  
        
        $orders = Order::select([
            'id',
            'external_id', 
            'order_uid',
            'payment_mode',
            'client_id',
            'final_amount',
            'is_payment_pending',
            'created_at'
        ]) 
        ->with(['client', 'products'])
        ->where('client_id', $client_id);

        $distributor_id = Helper::getDistributorId();
        if($distributor_id != 0) { // system user 
            $orders->where('distributor_id', $distributor_id);
        }

        $orders = $orders->orderBy('id', 'desc')->get();
 
        return Datatables::of($orders)
            ->addColumn('products', function ($orders) {
                $products = [];
                foreach($orders->products as $product) {
                    array_push($products, $product['name']);
                }
                return implode(', ', $products);
            })				
            ->addColumn('final_amount', function ($orders) {
                return Helper::decimalNumber($orders->final_amount);
            })				
			->addColumn('created', function ($orders) {
                return date("d-m-Y",strtotime($orders->created_at));
            }) 
            ->addColumn('action', function ($orders) {
                $html = $edit_btn = $cancel_btn = '';  
                $html .= "<div class='d-flex'>";
                $html .= '<a href="'.route('orders.show', $orders->external_id).'?all_orders=0" class="btn btn-link" data-toggle="tooltip" title="View Details"><i class="flaticon-eye text-primary"></i></a>';
                
                if($orders->is_payment_pending == "YES"){
                    if(\Entrust::can('order-delete'))
                    $edit_btn = '<a href="'.route('orders.edit', $orders->external_id).'" class="btn btn-link" data-toggle="tooltip" title="Edit Order"><i class="flaticon2-pen text-primary"></i></a>'; 
                    $html .= $edit_btn; 
                    // $cancel_btn = '<a href="javascript:;" data-toggle="modal" data-target="#cancel-subscription" data-id="'.$orders->external_id.'"  class="btn btn-link cancel_subscription" ><i class="flaticon2-cancel text-danger"></i></a>';
                    if(\Entrust::can('order-delete'))
                    // $cancel_btn = '<a href="javascript:;" data-id="'.$orders->external_id.'"  class="btn btn-link cancel_order" data-toggle="tooltip" title="Cancel Order"><i class="flaticon2-cancel text-danger"></i></a>'; 
                    $html .= $cancel_btn;
                }
                $html .= "</div>";
                return $html;
            })
            ->addColumn('action_view', function ($orders) { 
                $html = '<a href="'.route('orders.show', $orders->external_id).'?all_orders=0&client=1" class="btn btn-link" data-toggle="tooltip" title="View Details"><i class="flaticon-eye text-primary"></i></a>';  
                return $html;
            })
            ->rawColumns(['action', 'action_view'])
            ->make(true);
    }

        
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function allData(Request $request)
    {  
        $payment_pending = ['YES','NO'];

        if($request->payment_pending == '0'){
            $payment_pending = ['YES','NO'];
        }else if($request->payment_pending == '1'){
            $payment_pending = ['YES'];
        }else if($request->payment_pending == '2'){
            $payment_pending = ['NO'];
        }else{
            $payment_pending = ['YES','NO'];
        }   

        $orders = Order::select('orders.*')
        ->with(['client', 'branch', 'products', 'getDistributor']); 
  
        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id != 0) { // Check if distributor
            $orders->where('orders.distributor_id', $distributor_id);
        } 

        $orders = $orders->orderBy('orders.id', 'desc')->get();

        // dd($orders);
  
        return Datatables::of($orders)
            ->addColumn('distributor', function ($orders) {
                return  $orders->getDistributor->name ?? "";
            }) 
            ->addColumn('order_id', function ($orders) {
                return $orders->order_uid;
            })				
            ->addColumn('client_name', function ($orders) {
                return $orders->client->name;
            })		
            ->addColumn('products', function ($orders) {
                $products = [];
                foreach($orders->products as $product) {
                    array_push($products, $product['name']);
                }
                return implode(', ', $products);
            })			
            ->addColumn('payment_mode', function ($orders) {
                return $orders->payment_mode;
            })				
            ->addColumn('amount', function ($orders) {
                return Helper::decimalNumber($orders->final_amount);
            })				
            ->addColumn('branch', function ($orders) {
                return $orders->branch->name ?? "";
            })				
            ->addColumn('is_payment_pending', function ($orders) {
                return $orders->is_payment_pending;
            })				
            ->addColumn('date_of_order', function ($orders) {
                return date('d-m-Y', strtotime($orders->created_at));
            })		 
            ->addColumn('action', function ($orders) {
                $html = $edit_btn = $cancel_btn = '';  
                $html .= "<div class='d-flex'>";
                $html .= '<a href="'.route('orders.show', $orders->external_id).'?all_orders=1" class="btn btn-link" data-toggle="tooltip" title="View Details"><i class="flaticon-eye text-primary"></i></a>';
                
                if($orders->is_payment_pending == "YES")
                    if(\Entrust::can('order-update')){ 
                    $edit_btn = '<a href="'.route('orders.edit', $orders->external_id).'" class="btn btn-link" data-toggle="tooltip" title="Edit Order"><i class="flaticon2-pen text-primary"></i></a>'; 
                    $html .= $edit_btn; 
                    // $cancel_btn = '<a href="javascript:;" data-toggle="modal" data-target="#cancel-subscription" data-id="'.$orders->external_id.'"  class="btn btn-link cancel_subscription" ><i class="flaticon2-cancel text-danger"></i></a>';
                    if(\Entrust::can('order-delete'))
                    // $cancel_btn = '<a href="javascript:;" data-id="'.$orders->external_id.'"  class="btn btn-link cancel_order" data-toggle="tooltip" title="Cancel Order"><i class="flaticon2-cancel text-danger"></i></a>'; 
                    $html .= $cancel_btn;
                }
                $html .= "</div>";
                return $html;
            })
            ->rawColumns(['distributor', 'order_id', 'client_name', 'payment_mode', 'amount', 'is_payment_pending', 'date_of_order', 'action'])
            ->make(true);
    }

    /**
     * Show the form for editing the specified plan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($external_id)
    {

        // try{
            // dd($external_id);
            $distributor_id = Helper::getDistributorId();
            if($distributor_id == 0) { // system user 
                $order = Order::where('external_id', $external_id)->first(); 
            } else {
                $order = Order::where('external_id', $external_id)->where('distributor_id', $distributor_id)->firstOrFail(); 
            } 
            $data['order'] = $order; 
            $data['branch_id'] = $order->branch_id;
            $data['client_id'] = $order->client_id;
            $data['client_data'] = Client::find($order->client_id); 
            $data['client_products'] = ClientProduct::where('order_id', $order->id)->with('product')->get();
            $data['branch_data'] = $this->getBranch();
            $payment_modes = config('global.payment_modes');
            $data['payment_modes'] = array_merge(array(''=>'Select Payment Mode'),$payment_modes);

            return view('orders.edit')->with($data);
            
        // }catch(Exception $e)
        // {
        //     abort(404);
        // } 
    }
        /**
         * 
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($external_id,Request $request)
    { 
        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // system user 
            $order = Order::with(['client'])->where('external_id', $external_id)->first();
        } else {
            $order = Order::with(['client'])->where('external_id', $external_id)->where('distributor_id', $distributor_id)->firstOrFail(); 
        }

        $distributor = Distributor::find($order->distributor_id); 
 
        $clientproducts = ClientProduct::where('order_id','=',$order->id)->with(['product'])->get();
        $clientEmail = !empty($request->email) ? $request->email : null;
        $company = Branch::pluck('name', 'id');
        $company->prepend('Please Select Company', '');
        $plan = Product::pluck('name', 'id');
        $plan->prepend('Please Select Plan', '');
        $payment_modes = config('global.payment_modes');
        $companyId = $order->branch_id;
       
        $user = Auth::user();

        // $template = EmailTemplate::select('template')->where("distributor_id", $distributor_id)->first();

        // if(empty($template)) {
        //     $template = EmailTemplate::select('template')->where('default_template', 0)->first();
        // }
        $distributor_id_admin = $request->get('distributor') ?? 0;
        
        if($distributor_id_admin != 0) {
            $template = EmailTemplate::select('content')->where('distributor_id', $order->distributor_id)->first();  
        } else { 
            $template = EmailTemplate::select('content')->where('distributor_id', $distributor_id)->first(); 
        }
        if(empty($template)) {
            $template = EmailTemplate::select('content')->where('distributor_id', 0)->first();
        }
  
       $template = $template->content;
         
        if(isset($user)){
            if(isset($user->invoice_template)){
                $template = $user->invoice_template;
            }else{
                $template = Helper::defaultInvoiceTemplate();  
            }
        }else{
            $template = Helper::defaultInvoiceTemplate();    
        }

        $template_content = Helper::invoiceTemplateBody();
        

        $template = str_replace("<p></p>","",$template);
        $template = str_replace("<p>&nbsp;</p>","",$template);    
        $invoice_template = str_replace("{{#template_content}}", $template, $template_content);
               
        $plan_list = '';
        foreach ($clientproducts as $cplan){ 
            $plan_list .='<tr class="plan_item">';
            $plan_list .='<td class="text_left">'.$cplan->product->name.'</td>';
            $plan_list .='<td class="text_left">'.$cplan->qty.'</td>';
            // $plan_list .='<td class="text_right" width="15%">'.date('d-m-Y',strtotime($cplan->order_date)).'</td>';
            $plan_list .='<td class="text_right" width="5%">'.Helper::decimalNumber($cplan->product_price).'</td>';
            $plan_list .='<td class="text_right" width="10%">'.Helper::decimalNumber($cplan->deal_discount).'</td>';
            $plan_list .='<td class="text_right" width="10%">'.Helper::decimalNumber($cplan->discount).'</td>';
            $plan_list .='<td class="text_right" width="15%">'.Helper::decimalNumber($cplan->discount_amount * $cplan->qty).'</td>';
            $plan_list .='<td class="text_right" width="20%">'.Helper::decimalNumber($cplan->final_amount * $cplan->qty).'</td>';
            $plan_list .='</tr>';
        } 

        $server_css = "#plan_list{display:none}";

        if($order->client->state_id == 12) {
            $server_css .= "#igst{display:none}";
        } else {
            $server_css .= "#cgst{display:none}";
            $server_css .= "#sgst{display:none}";
        }

        if($order->payment_mode == "CASH"){
            $server_css .= "#bank_name_row{display:none}";
            $server_css .= "#tansaction_number{display:none}";
        }
        
        if($order->is_payment_pending == "YES") {
            $server_css .= "#payment_mode{display:none}";
            $server_css .= "#payment_date{display:none}";
            $server_css .= "#bank_name_row{display:none}";
            $server_css .= "#tansaction_number{display:none}"; 
        }  

        if($order->deal_id == 0) {
            $server_css .= "#discount_code_tr{display:none}";
        }

        $variables = array(
            "{{#company_name}}" => $distributor->name,
            // "{{#order_date}}" => date('d-m-Y', strtotime($clientproducts[0]->order_date)),
            "{{#company_address}}" => $order->client->address ?? "".'-'.$order->client->city ?? "",
            "{{#invoice_no}}" => $order->orders_uid ?? "",
            "{{#client_name}}" => $order->client->name ?? "",
            "{{#client_email}}" => $order->client->email ?? "",
            "{{#created_date}}" => $order->created_at->format('d-m-Y'),
            "{{#gst_no}}" => $order->branch->gst_no ?? "",
            "{{#total_amount}}" => Helper::decimalNumber($order->total_amount),
            "{{#discount_code}}" => $order->discount_code,
            "{{#sgst_amount}}" => Helper::decimalNumber($order->sgst_amount),
            "{{#sgst}}" => Helper::decimalNumber($order->sgst),
            "{{#cgst_amount}}" => Helper::decimalNumber($order->cgst_amount),
            "{{#cgst}}" => Helper::decimalNumber($order->cgst),
            "{{#igst_amount}}" => Helper::decimalNumber($order->igst_amount),
            "{{#igst}}" => Helper::decimalNumber($order->igst),
            "{{#final_amount}}" => Helper::decimalNumber($order->final_amount),
            "{{#round_off_amt}}" => Helper::decimalNumber($order->round_off_amount),
            "{{#yes_no}}" => $order->is_payment_pending ?? "",
            "{{#payment_mode}}" => $order->payment_mode ?? "",
            "{{#payment_date}}" => date('d-m-Y',strtotime($order->payment_date)),
            "{{#bank_name}}" => $order->payment_bank_name ?? "",
            "{{#transaction_no}}" => $order->payment_number ?? "",
            "{{#server_css}}" => $server_css ?? "",
            '<tfoot></tfoot>' => $plan_list ?? "",
        );
 
    
        foreach ($variables as $key => $value)
            $invoice_template = str_replace($key, $value, $invoice_template);
 
        
        $pdfName = date('Y_m_d_H_i_s_'.$external_id).'.pdf';
        $pdfName = str_replace(' ','_',$order->client->name) ."_". date('d_m_Y_h_i_s').'.pdf';
        
        if($request->is_pdf || $request->is_email){
            $data['order'] = $order;
            $data['company'] = $company;
            $data['plan'] = $plan;
            $data['clientproducts'] = $clientproducts;
            $data['companyId'] = $companyId;
            $data['payment_modes'] = $payment_modes;

            $pdf = PDF::loadHTML($invoice_template);
            // echo $invoice_template;
            // die;
            // return $pdf->setPaper('landscape')->setWarnings(false)->stream();
            if($request->is_pdf){
                return $pdf->setPaper('landscape')->setWarnings(false)->download($pdfName);
            }
             
            if($request->is_email){ 

                // Check current plan  
                if($distributor->email_service != 1) {
                    return redirect()->back()->with('error', 'Your email services is inactive.');
                }
                if($distributor->total_email <= 0) {
                    return redirect()->back()->with('error', 'insufficient email balance.');
                }

                if($clientEmail){ 
                    if($user){ 
                        $message = 'Invoice sent successfully.';
                        $emails = explode(',', $clientEmail);
                        $emails =  array_slice($emails, 0, 3);
 
                        $getInvoiceTemplateSend = EmailTemplate::select(['email_template_id', 'subject', 'content'])->where('name','Invoice email send')->where('default_template','1')->get(); 

                        if(count($getInvoiceTemplateSend)){
                            $message_content = $getInvoiceTemplateSend[0]['content'];
                            $subject = $getInvoiceTemplateSend[0]['subject'];  
                        }else{
                            $getInvoiceTemplateSend = EmailTemplate::select(['email_template_id', 'subject', 'content'])->where('name','Invoice email send')->where('client_id',0)->where('company_id',0)->where('default_template','1')->get();
                            $message_content = $getInvoiceTemplateSend[0]['content'];
                            $subject = $getInvoiceTemplateSend[0]['subject'];  
                        }
  
                        $branch_details = Branch::find($user->branch_id);
 
                        $details = ($branch_details != null) ? $branch_details->name : '';
 
                        if(isset($branch_details)){
                            
                            if($branch_details->address){
                                $details .= ', '.$branch_details->address;
                            }
                            if($branch_details->address_line_2){
                                $details .= ', '.$branch_details->address_line_2;
                            } 
                        }

                        $email_variable = array(
                            '{{#client_name}}' => $order->client->name,
                            '{{#user_name}}' =>  $user->name,
                            '{{#company_details}}' =>  $details,
                            '{{#copy_right}}' =>  ($branch_details != null) ? $branch_details->name : '' .' @ ' .date('Y-m-d'),
                        );

                         
                        foreach ($email_variable as $key => $value)
                            $message_content = str_replace($key, $value, $message_content);

                            $emailTemplateBody =  Helper::emailTemplateBody();

                            $emailTemplateBody =  str_replace("{{#template_body}}", $message_content, $emailTemplateBody);
            
                            $message_content = str_replace("{{#all_css}}", Helper::emailTemplateCss(), $emailTemplateBody); 

                            $data['subject'] = $subject;
                            $data['messagecontent'] = $message_content;
                            $data['from_email'] = $order->getDistributor->from_email;
                            $data['from_name'] = $order->getDistributor->from_name;
 
                            // $companyData = Company::find($companyId);

                            // if($companyData->used_email >=1){
                            //     $used_email = ($companyData->used_email > 1) ? $companyData->used_email - 1 : 0;

                                Mail::send('emails.email', $data, function($message)use($data, $pdf,$pdfName,$emails) {
                                    $message->subject($data['subject']);
                                    
                                    if(isset($data['from_email']) && isset($data['from_name'])){
                                        $message->from($data['from_email'], $data['from_name']);
                                    }
                                    $message->to($emails)->attachData($pdf->output(), $pdfName);
                                });
 
                            //     $companyData->used_email = $used_email;
                            //     $companyData->save(); 
                               
                            // }else{
                            //     $message = 'Your email balance is 0. Please recharge and send again.';
                            // }
                    }
                }
                return back()->with('success', $message);
            }
        } 
        if($request->get('all_orders') == 1) {
            $back_url = route('orders.all');
        } else {
            $back_url = route('orders.index', ['client_id' => encrypt($order->client->id)]);
        } 
        $client = $request->get('client') ?? 0;
        if($client !== 0) {
            $back_url = route('clients.show', $order->client->external_id);
        }

        return view('orders.show',compact('order','company','plan','clientproducts','companyId'))->with(['payment_modes'=>$payment_modes, 'distributor_id' => Helper::getDistributorId(), 'back_url' => $back_url]);
       
    }
 
    public function cancel($id)
    {
        $message = 'Order has been cancelled successfully'; 
        $order = Order::where('external_id', $id)->first(); 
        $client_id = $order->client_id;
        $order->delete();
        return redirect('admin/orders?client_id='.encrypt($client_id))->with('success', $message);
    }


	public function getCompanyDetail(Request $request)
    {
        $client_id = decrypt($request->client_id);
        $client = Client::find($client_id); 
        return response()->json(['success'=>true, 'company'=>$client]);
    }

    // return current user branch
    public function getBranch()
    {
        $branch_id = Auth::user()->branch_id ?? 0 ;
        $branch = Branch::find($branch_id); 
        return $branch;
    }

    public function getProductDetail(Request $request)
    { 
        $id = $request->product_id;
        $distributor_id = Helper::getDistributorId();
        $branch_id = Auth::user()->branch_id;
        if($distributor_id == 0){
            $branch_id = $request->branch_id;
        } 
        $productId = ($id);
        $product = Product::find($productId);
         
        if($product->type == 0) {
            
            $stock_level = StockMaster::where('product_id', $productId)->where('branch_id', $branch_id)->first();

            if(!empty($stock_level)) {
                if($stock_level->qty > 0) {
                    return response()->json(['success'=>true,'product'=>$product]);
                } else {
                    return response()->json(['success'=>false, 'message' => "Product not available in stock!"]);
                }
            } else {
                return response()->json(['success'=>false, 'message' => "Product not available in stock!"]);
            } 
        }

        return response()->json(['success'=>true,'product'=>$product]);
    }

    public function checkStockLevel(Request $request)
    {
        $distributor_id = Helper::getDistributorId();
        $branch_id = Auth::user()->branch_id;
        if($distributor_id == 0){
            $branch_id = $request->branch_id;
        } 
        $product_id = $request->product_id;
        $product_qty = intval($request->qty);

        $product = Product::find($product_id); 
 
        if($product->type == 0) { 
            
            $stock_level = StockMaster::where('product_id', $product_id)->where('branch_id', $branch_id)->first();
  
            if(!empty($stock_level)) { 
                $final_qty = $stock_level->qty;
                if($request->current_qty) {  
                    $final_qty = $final_qty + intval($request->current_qty);
                }  

                if($final_qty >= $product_qty) {
                    return response()->json(['success'=>true]);
                } else {
                    if($final_qty == 0) {
                        return response()->json(['success'=>false, 'message' => "Product not available in stock!"]);
                    } else {
                        return response()->json(['success'=>false, 'qty' => $final_qty, 'message' => "Only $final_qty product available in stock!"]);
                    }
                }
            } else {
                return response()->json(['success'=>false, 'message' => "Product not available in stock!"]);
            } 
        } else {
            return response()->json(['success'=>true]); 
        } 
    }

    public function decimalNumber($nunber, $zero = 2, $dot = '.')
    {
        return number_format($nunber, $zero, $dot,'');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deletePlan($id, $branch_id)
    {  
        $message = 'Product/Service has been removed successfully';
        $product = ClientProduct::find($id); 
        $product_final_amount = $product->final_amount;
        $order_id = $product->order_id;
        $productData = Product::find($product->product_id);
        $order = Order::find($order_id);    
        $clientData = Client::find($order->client_id); 

        // Update Stock LVL
        $distributor_id = Helper::getDistributorId();
        $branch_id = Auth::user()->branch_id;
        if($distributor_id == 0){
            $branch_id = $branch_id;
        }

        $stock_level = StockMaster::where('product_id', $product->product_id)->where('branch_id', $branch_id)->first(); 

        if(!empty($stock_level)) {
            $stock_level->update([
                'qty' => $stock_level->qty + $product->qty,
            ]);
        }

        $final_amount = $order->final_amount;
        $round_off_amount = $order->round_off_amount;
   
        $product_final_amount = $product_final_amount * $product->qty; 

        if($clientData->state_id === 12){
            $order->sgst_amount = $order->sgst_amount - (($product->product_price * $product->qty) * $product->sgst / 100);
            $order->cgst_amount = $order->cgst_amount - (($product->product_price * $product->qty) * $product->sgst / 100);
        }else{
            $order->igst_amount = $order->igst_amount - (($product->product_price * $product->qty) * $product->sgst / 100);
        }
        
        $final_amount = $final_amount - $product_final_amount;
        $round_off_amount = $round_off_amount - $product_final_amount;
         
        $order->total_amount = $final_amount;
        $order->final_amount = $final_amount;
        $order->round_off_amount = round($round_off_amount); 
        $order->save();
        $product->delete();
    
        return redirect(route('orders.edit', $order->external_id))->with('success', $message);  
    }
 
    public function applyCode(Request $request)
    {
        $data = "";
        parse_str($request->form_data, $data);
 
        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // system user 
            $distributor_id = $data['distributor_id'];
        } 
        
        $deal = DealAndDiscount::whereRaw("BINARY `deal_code`= ?", $data['discount_code'])->where('is_active', 1)->where('distributor_id', $distributor_id); 
        
        $dealCount = $deal->count();
        $deal = $deal->first();

        
        if($dealCount == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Discount code is invalid!',
                'description' => false,
            ]);
        } 

        $max_redemptions = intval($deal->redemptions_max);
        // dd($data['client_id']); 
        $client_redemptions = Order::where('client_id', $data['client_id'])->where('deal_id',  $deal->id)->count(); 
  

        if($client_redemptions >= $max_redemptions) {
            return response()->json([
                'status' => false,
                'message' => "Can not redeem this code more than $max_redemptions times!",
                'description' => false,
            ]);
        }
        
        $applicable_clients = $deal->clients->pluck('id')->toArray();
        $order_client = $data['client_id'];
        $applicable_products = $deal->products->pluck('id')->toArray();
        $order_products = $data['plans'];
        
        if(count($applicable_clients) > 0) { 
            if(!in_array($order_client, $applicable_clients)){
                return $this->returnErrorMessage($deal);
            } 
        }      
        
       
        // if(count($applicable_products) > 0) {
        
        //     if(!in_array($order_products, $applicable_products)){
        //         return $this->returnErrorMessage($deal);
        //     } 
        // }     
  
        $validity = date('Y-m-d', strtotime($deal['validity']));
        $order_date = date('Y-m-d', strtotime($data['order_date']));
        
        if($order_date >= $validity) {
            return response()->json([
                'status' => false,
                'message' => 'Discount code is expired!',
                'description' => false,
            ]);
        }
         
  
        $is_weekend = Helper::is_weekend();
        $is_weekday = Helper::is_weekday($deal['week_days'], $data['order_date']);
        $is_holiday = Helper::is_holiday($data['order_date'], $distributor_id); 
        $is_birthday = Helper::is_event($data['client_id'], $data['order_date'], 'date_of_birth', $distributor_id);
        $is_anniversary = Helper::is_event($data['client_id'], $data['order_date'], 'anniversary', $distributor_id);
  
        $final_amount = $this->getFinalAmount($data);    
        if(!$is_weekday) {
            return $this->returnErrorMessage($deal);
        }
        if($deal['applicable_on_weekends'] == 1) {
            if($is_weekend == false) {   
                return $this->returnErrorMessage($deal);
            }
        } 
        if($deal['applicable_on_holidays'] == 1) {
            if($is_holiday == false) { 
                return $this->returnErrorMessage($deal);
            }
        } 

        if($deal['applicable_on_bday_anniv'] == 1) {  
            if($is_birthday == true || $is_anniversary == true) { 
               
            } else {
                return $this->returnErrorMessage($deal);
            }
        } 
        if($deal['invoice_min_amount'] != null) {   
            if($final_amount < $deal['invoice_min_amount']) {
                return $this->returnErrorMessage($deal);
            }
        } 
        if($deal['invoice_max_amount'] != null) {  
            if($final_amount > $deal['invoice_max_amount']) {
                return $this->returnErrorMessage($deal);
            }
        }  

  
        if($deal->apply_on_bill_total > 0) {

            $returnResponse = [
                'status' => true,
                'message' => "Applicable on bill total",
                'applicable_on' => "bill_total",
                'discount' => $deal->discount,
                'products' => [],
                'deal_id' => $deal->id,
            ];

        } else {
            $applicable_products_order = array_intersect($order_products, $applicable_products);
            
            if(count($applicable_products_order) == 0) {
                return $this->returnErrorMessage($deal, "Cant apply code in selected products!");
            } else {
                $returnResponse = [
                    'status' => true,
                    'message' => "Applicable on products",
                    'applicable_on' => "products",
                    'discount' => $deal->discount,
                    'products' => array_values($applicable_products_order),
                    'deal_id' => $deal->id,
                ];
            }
        }

        return response()->json($returnResponse);
    }  
 
    private function returnErrorMessage($deal, $message = false)
    {
        $message = $message ?? "Discount code is invalid!";
        return response()->json([
            'status' => false,
            'message' => $message,
            'description' => "<div class='text-left'>".$deal['deal_description']."</div>",
        ]);
    }

    public function getFinalAmount($data)
    {
        $products = $data['plans'];  
        $product_discount = $data['plans_discount']; 
        $product_qty = $data['order_qty']; 
        $order_date = $data['order_date'];
		$allProducts = Product::whereIn('id',$products)->get();
        
		$total_amount = $totaluser = $totalmonth = $no_of_sms = $no_of_email = 0;  
		foreach($allProducts as $product){ 
			$amount = $product->sales_price;
            $amount = $product->sales_price * $product_qty[$product->id]; 
			$discount = $product_discount[$product->id]; 
			$discount = Helper::decimalNumber($discount);
			$discount_amount = $amount * $discount / 100;
			$discount_amount = Helper::decimalNumber($discount_amount);
			$netamount = $amount - $discount_amount; 
			$netamount = Helper::decimalNumber($netamount);
			$total_amount += $netamount; 
        }  
        $client_data = Client::find($data['client_id']); 
        $client_primary_id = $client_data->id; 
  
		$sgst = Helper::decimalNumber($data['sgst']);
		$sgst_amount = Helper::decimalNumber($data['sgst_amount']);
		$cgst = Helper::decimalNumber($data['cgst']);
		$cgst_amount = Helper::decimalNumber($data['cgst_amount']);
		$igst = Helper::decimalNumber($data['igst']);
		$igst_amount = Helper::decimalNumber($data['igst_amount']);
         
        if($client_data->state_id === 12){
            $final_amount = $total_amount + $sgst_amount + $cgst_amount;
        }else{
            $final_amount = $total_amount + $igst_amount;
        }
        return round($final_amount);
    }


     // public function checkOfferProducts($deal, $products)
    // {
    //     $dealProducts = DealProductService::with('product', 'category', 'sub_category')->where('deal_id', $deal->id)->get();

    //     if(!empty($dealProducts)) {
    //         $products_return = [];
    //         foreach($dealProducts as $product) {
    //             if($product['product_id'] !== 0) {
    //                 array_push($product['product_id'], $products_return);
    //                 continue;
    //             }    
    //             if($product['sub_category_id'] !== 0) {
    //                 $sub_category = Category::find($product['sub_category_id']);
    //                 $products = $sub_category->products->pluck('id')->toArray();
 
    //                 foreach($products as $id) {
    //                     array_push($products_return, $id);
    //                 } 
    //                 continue;
    //             }
    //             if($product['category'] !== 0) {
    //                 $sub_category = Category::find($product['category']);
    //                 $products = $sub_category->products->pluck('id')->toArray();
 
    //                 foreach($products as $id) {
    //                     array_push($products_return, $id);
    //                 } 
    //                 continue;
    //             }
    //         }
    //         dd($products);
    //     } else {
    //         return [];
    //     }
    // }
}

