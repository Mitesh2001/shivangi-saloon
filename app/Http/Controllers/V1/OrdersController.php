<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Helpers\Helper;
use Ramsey\Uuid\Uuid;
use Mail;
use PDF;
use JWTAuth;

use App\Models\Order;
use App\Models\Client;
use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use App\Models\ClientProduct;
use App\Models\EmailTemplate;
use App\Models\Distributor;
use App\Models\DealAndDiscount;
use App\Models\DealProductService;
use App\Models\Category;
use App\Models\DealLogs;
use App\Models\UsersCommission;
use App\Models\StockMaster;
use App\Models\EmailLog;
use App\Models\Unit;
use App\Models\UserProductCommission;

use App\Models\Appointment;
use App\Models\Status;

class OrdersController extends Controller
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
        $paginate = $request->paginate ?? 0;

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

		$orders = Order::with(['client', 'products'])
        ->where('distributor_id', $distributor_id)
        ->where('branch_id', $user->branch_id);
		$orders->orderBy('orders.id', 'desc');

        if($paginate == 1) {
            $data = $orders->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $orders->get();
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

		$validator = Validator::make($request->all(), [
            'client_id' => 'required|numeric',
            'products' => 'required|array|min:1',
            'payment_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:today',
            'payment_bank_name' => 'nullable|max:40|regex:/^[A-Za-z ]+$/u',
        ],
        [
            'products.required' => "Can not submit empty invoice!",
        ]);

		if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

		$branch_id = $user->branch_id ?? 0;
		$client_id = $request->client_id;

		$distributor = Distributor::find($distributor_id);
        $branch = Branch::find($branch_id);
		$client_data = Client::find($client_id);

		$order = new Order();
		$orders_uid_count = Order::withTrashed()->count();
        $orders_uid = str_pad($orders_uid_count, 6, '0', STR_PAD_LEFT);

		$products = $request->products;
        $allProducts = Product::whereIn('id',$products)->get(); // Product
        $product_discount = (array)$request->products_discount; // Array of products discount with key product id
        $deal_discounts = (array)$request->deal_discount;  // Array of deal discount with key product id
        $product_qty = (array)$request->order_qty;
        $order_date = date('Y-m-d', strtotime($request->order_date));
		$user_product_commission = $user->product_commission;
        $user_service_commission = $user->service_commission;
        $product_commission = 0;
        $service_commission = 0;

        $total_amount = $totaluser = $totalmonth = 0;
		$commission_json = []; // Store detailed commission history

        // Manage commission & Discount
		foreach($allProducts as $product){

            // // Manage Stock if type is product
            // if($product->type == 0) {
            //     $this->ManageProductStock($product, $branch_id, $product_qty[$product->id]);
            // }

            // Get Sales Price
			$amount = $product->sales_price;
            $amount = Helper::decimalNumber(($product->sales_price * $product_qty[$product->id]));

            // Default commission
            $sgst = $cgst = $igst = 0;

            if($client_data->state_id == $branch->state_id){

                $sgst = $product->sgst;
                $cgst = $product->cgst;

                $gst_array = Helper::getCalucatedGST($amount, $sgst, $cgst, 0);
                $product_original_price = Helper::decimalNumber(($amount - $gst_array['sgst_amount'] - $gst_array['cgst_amount']));
                $gst_amount = $gst_array['total_gst_amount'];

            } else {

                $igst = $product->igst;

                $gst_array = Helper::getCalucatedGST($amount, 0, 0, $igst);
                $product_original_price = Helper::decimalNumber($amount - $gst_array['igst_amount']);
                $gst_amount = $gst_array['total_gst_amount'];
            }

            $commission_arr = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku_code' => $product->sku_code,
                'qty' => $product_qty[$product->id],
                'type' => $product->type,
                'sales_price' => $product->sales_price,
                'sgst' => $sgst,
                'cgst' => $cgst,
                'igst' => $igst,
                'gst_amount' => $gst_amount,
                'origina_price' => $product_original_price,
            ];

            $commission_entry = UserProductCommission::where('product_id', $product->id)->where('user_id', $user->id)->first();

            // Store product & service commission separate for detailed commission calculation
            if(!empty($commission_entry)) {
                if($product->type == 0) {
                    $commission_amount = Helper::decimalNumber(($product_original_price / 100 * $commission_entry->commission));
                    $product_commission += $commission_amount;
                }
                if(($product->type == 1 || $product->type == 2)) {
                    $commission_amount = Helper::decimalNumber(($product_original_price / 100 * $commission_entry->commission));
                    $service_commission += $commission_amount;
                }
                $commission_arr['commission'] = $commission_entry->commission;
                $commission_arr['commission_amount'] = $commission_amount ?? 0;
                $commission_arr['commission_type'] = 1; // Commission set from grid (product wise)

            } else {
                if($user_product_commission > 0 && $product->type == 0) {
                    $commission_amount = Helper::decimalNumber(($product_original_price / 100 * $user_product_commission));
                    $product_commission += $commission_amount;
                    $commission_arr['commission'] = $user_product_commission;
                }
                if($user_service_commission > 0 && ($product->type == 1 || $product->type == 2)) {
                    $commission_amount = Helper::decimalNumber(($product_original_price / 100 * $user_service_commission));
                    $service_commission += $commission_amount;
                    $commission_arr['commission'] = $user_service_commission;
                }

                $commission_arr['commission_amount'] = Helper::decimalNumber($commission_amount) ?? 0;
                $commission_arr['commission_type'] = 0; // Default employee commission (from profile)
            }
            array_push($commission_json, $commission_arr);

			$deal_discount = $deal_discounts[$product->id];
			$discount = $product_discount[$product->id];
			$discount = Helper::decimalNumber($discount) + Helper::decimalNumber($deal_discount);
			$discount_amount = ($amount * $discount / 100);
			$discount_amount = Helper::decimalNumber($discount_amount);
			$netamount = $amount - $discount_amount;
			$netamount = Helper::decimalNumber($netamount);
			$total_amount += $netamount;
        }
		$service_commission = Helper::decimalNumber($service_commission);
		$product_commission = Helper::decimalNumber($product_commission);

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
        $order->state_id = $client_data->state_id;
        $order->branch_state_id = $branch->state_id;
        $order->state_name = $client_data->state_name;
        $order->order_uid = $orders_uid;
        $order->created_by = $user->id;
        $order->distributor_id = $distributor_id;
        $order->deal_id = $request->deal_id;
        $order->discount_code = $request->discount_code;
        $order->save();

        // Add Commission for User / Employee
        if($order->is_payment_pending == "NO" && $user->distributor_id != 0) {
            $this->manageUserCommission($user, $order, $service_commission, $product_commission, $commission_json,$distributor_id);
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

            if($product->type == 2) {
                $client_product->package_products = json_encode($product->packageProducts);
            }

            $client_product->client_id = $client_primary_id;
            $client_product->product_id = $product->id;
            $client_product->order_id = $order->id;

            $amount = $product->sales_price;
            $amount = Helper::decimalNumber($amount);
            $deal_discount = $deal_discounts[$product->id];
            $discount = $product_discount[$product->id];
            $qty = $product_qty[$product->id];
			$discount = Helper::decimalNumber($discount) + Helper::decimalNumber($deal_discount);
            $discount_amount = Helper::decimalNumber(($amount * $discount / 100));
            $discount_amount_total = ($amount * $discount / 100 * $qty);
            $discount_amount_total = Helper::decimalNumber($discount_amount_total);
            $netamount = ($amount - $discount_amount) * $qty;
            $netamount = Helper::decimalNumber($netamount);

			$client_product->qty = $qty;
			$client_product->product_price = $amount;
			$client_product->deal_discount = $deal_discounts[$product->id];
			$client_product->discount = $product_discount[$product->id];
			$client_product->discount_amount = $discount_amount_total;
            $client_product->final_amount = $netamount;
            $client_product->order_date = $order_date;
            $order->distributor_id = $distributor_id;

            if($client_data->state_id == $distributor->state_id){
                $gst_array = Helper::getCalucatedGST($netamount, $product->sgst, $product->cgst, 0);
                $client_product->sgst = $product->sgst;
                $client_product->cgst = $product->cgst;

                $client_product->sgst_amount = $gst_array['sgst_amount'];
                $client_product->cgst_amount = $gst_array['cgst_amount'];

            } else {
                $gst_array = Helper::getCalucatedGST($netamount, 0, 0, $product->igst);

                $client_product->igst = $product->igst;
                $client_product->igst_amount = $gst_array['igst_amount'];
            }

			$client_product->save();
		}

        if($request->appointment_id != 0)
        {
            $status = Status::where('title', 'Completed')->first();

            $appointment = Appointment::find($request->appointment_id);
            $appointment->stage = "Order";
            $appointment->status_id = $status->id;
            $appointment->save();

            $message = 'Order has been added successfully';
			return response()->json([
				'status' => 'SUCCESS',
				'data' => $appointment,
				'message' => $message
			]);
        }
		return response()->json([
			'status' => 'SUCCESS',
			'data' => $order,
			'message' => 'Order has been added successfully'
		]);
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
		$user = JWTAuth::parseToken()->authenticate();
		if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }
		$distributor_id = $user->distributor_id;

        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }
		$user_id = $user->id;

		$validator = Validator::make($request->all(), [
            // 'company_id' => 'required|numeric',
            'payment_date' => 'nullable|date',
            'payment_bank_name' => 'nullable|max:40|regex:/^[A-Za-z ]+$/u',
        ]);

		if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
		$order = Order::find($id);
		$products = ClientProduct::where('order_id', $order->id)->with('product')->orderBy('clients_product.id', 'desc')->get();
        $invoice_user = User::find($order->created_by);

		$user_product_commission = $invoice_user->product_commission;
        $user_service_commission = $invoice_user->service_commission;
        $product_commission = 0;
        $service_commission = 0;

        $client_data = Client::find($order->client_id);
        $branch = Branch::find($order->branch_id);
        $total_amount = 0;

        $commission_json = []; // Store detailed commission history


        foreach($products as $product)
        {
            $main_product = Product::find($product->product_id);

            $gst_amount = Helper::decimalNumber($product->igst_amount + $product->cgst_amount + $product->sgst_amount);
            $product_original_price = $product->final_amount - $gst_amount;

            $commission_arr = [
                'product_id' => $product->id,
                'product_name' => $main_product->name,
                'sku_code' => $main_product->sku_code,
                'type' => $main_product->type,
                'qty' => $product->qty,
                'sales_price' => $product->product_price,
                'sgst' => $product->sgst,
                'cgst' => $product->cgst,
                'igst' => $product->igst,
                'gst_amount' => $gst_amount,
                'origina_price' => $product_original_price,
            ];

            $commission_entry = UserProductCommission::where('product_id', $product->product_id)->where('user_id', $invoice_user->id)->first();

            // Store product & service commission separate for detailed commission calculation
            if(!empty($commission_entry)) {
                if($main_product->type == 0) {
                    $commission_amount = Helper::decimalNumber($product_original_price / 100 * $commission_entry->commission);
                    $product_commission += $commission_amount;
                }
                if(($main_product->type == 1 || $main_product->type == 2)) {
                    $commission_amount = Helper::decimalNumber($product_original_price / 100 * $commission_entry->commission);
                    $service_commission += $commission_amount;
                }
                $commission_arr['commission'] = $commission_entry->commission;
                $commission_arr['commission_amount'] = $commission_amount ?? 0;
                $commission_arr['commission_type'] = 1; // Commission set from grid (product wise)

            } else {
                if($user_product_commission > 0 && $main_product->type == 0) {
                    $commission_amount = Helper::decimalNumber($product_original_price / 100 * $user_product_commission);
                    $product_commission += $commission_amount;
                    $commission_arr['commission'] = $user_product_commission;
                }
                if($user_service_commission > 0 && ($main_product->type == 1 || $main_product->type == 2)) {
                    $commission_amount = Helper::decimalNumber($product_original_price / 100 * $user_service_commission);
                    $service_commission += $commission_amount;
                    $commission_arr['commission'] = $user_service_commission;
                }

                $commission_arr['commission_amount'] = $commission_amount ?? 0;
                $commission_arr['commission_type'] = 0; // Default employee commission (from profile)
            }
            array_push($commission_json, $commission_arr);
        }

        $order->is_payment_pending = $request->payment_status;
        $order->payment_mode = $request->payment_mode;
        $order->payment_date = $request->payment_date;
        $order->payment_bank_name = $request->payment_bank_name;
        $order->payment_number = $request->payment_number;
        $order->updated_by = $user->id;
        $order->save();

		$service_commission = Helper::decimalNumber($service_commission);
		$product_commission = Helper::decimalNumber($product_commission);

        // Manage Commission
        if($order->is_payment_pending == "NO" && $invoice_user->distributor_id != 0) {
            $this->manageUserCommission($invoice_user, $order, $service_commission, $product_commission, $commission_json);
        }
		return response()->json([
            'status' => 'SUCCESS',
            'data' => $order,
            'message' => 'Order payment status has been updated successfully!'
        ]);
	}

	public function cancelOrder($id)
    {
        $message = 'Order has been cancelled successfully';
        $order = $this->findByExternalId($id);
        $products = ClientProduct::where('order_id', $order->id)->with('product')->orderBy('clients_product.id', 'desc')->get();

        // Array will hold total qty of product that need to be in database
        $product_total_qty = [];

        // Calculate each product stock need
        foreach($products as $c_product) {

            $main_product = Product::find($c_product->product_id);

            if($main_product->type == 2) {

                $package_products = json_decode($c_product->package_products);

                foreach($package_products as $product) {

                    if($product->type != 0) {
                        continue;
                    }

                    if(array_key_exists($product->id, $product_total_qty)) {
                        $product_total_qty[$product->id]['qty'] += intval($product->pivot->qty) * intval($c_product->qty);
                    } else {
                        $product_total_qty[$product->id]['product_name'] = $product->name;
                        $product_total_qty[$product->id]['qty'] = intval($product->pivot->qty) * intval($c_product->qty);
                    }
                }
            }

            if($main_product->type == 0) {

                if(array_key_exists($main_product->id, $product_total_qty)) {
                    $product_total_qty[$main_product->id]['qty'] += intval($c_product->qty);
                } else {
                    $product_total_qty[$main_product->id]['product_name'] = $main_product->name;
                    $product_total_qty[$main_product->id]['qty'] = intval($c_product->qty);
                }
            }
        }

        // add required qty from stock
        foreach($product_total_qty as $product_id => $data) {
            $stock_level = StockMaster::where('product_id', $product_id)->where('branch_id', $order->branch_id)->first();

            $stock_level->update([
                'qty' => intval($stock_level->qty) + intval($data['qty']),
            ]);
        }

        $client_id = $order->client_id;
        $order->delete();
		return response()->json([
            'status' => 'SUCCESS',
            'message' => $message
        ]);

    }


	/**
     *  Check stock lvl branch wise and generate error message
     *  Default qty is 1 if not provided
     */
    public function CheckProductStock(Request $request)
    {
		$branch_id = $request->branch_id;
		$main_product_id = $request->main_product_id;
		$qty = 1;
		if(isset($request->qty)){
			$qty = $request->qty;
		}
		$current_qty = 0;
		if(isset($request->current_qty)){
			$current_qty = $request->current_qty;
		}

        $product_id   = intval($main_product_id);
        $product      = Product::find($main_product_id);
        $required_qty = intval($qty);
        $branch_id    = intval($branch_id);

        // Handle error if product not found

        if(empty($product)) {
			return response()->json([
                'status' => 'FAIL',
                'message' => "Product not found!"
            ]);
        }

        // dynamic message & status
        $in_stock = true;
        $error_messages = [];

        // Check stock if product type is package
        // & generate dynamic error message
        if($product->type == 2) {

            $package_products = $product->packageProducts;
			$package_stock_level = array();
            foreach($package_products as $p_product)
            {
                if($p_product->type == 0) {
                    $stock_level = StockMaster::where('product_id', $p_product->pivot->product_id)->where('branch_id', $branch_id)->first();
					$package_stock_level[] = $stock_level;
                    $required_stock = intval($p_product->pivot->qty) * $required_qty;

                    if(!empty($stock_level)) {
                        if($stock_level->qty > 0) {

                            if($stock_level->qty >= $required_stock) {
                                if($in_stock != false) {
                                    $in_stock = true;
                                }
                            } else {
                                $in_stock = false;
                                $error_messages[] = "Only $stock_level->qty $p_product->name available in stock. ".$required_stock." required for package! <br>";
                            }

                        } else {
                            $in_stock = false;
                            $error_messages[] = $p_product->name." is not available in stock! <br>";
                        }
                    } else {
                        $in_stock = false;
                        $error_messages[] = $p_product->name." is not available in stock! <br>";
                    }
                }
            }

            if($in_stock === false) {
				return response()->json([
					'status' => 'FAIL',
					'message' => implode('', $error_messages)
				]);
            } else {
				return response()->json([
					'status' => 'SUCCESS',
					'data' => $product,
					'stock_level' => $package_stock_level,
				]);
            }
        }

        if($product->type == 0) {

            $stock_level = StockMaster::where('product_id', $product_id)->where('branch_id', $branch_id)->first();

            if(!empty($stock_level)) {
                $final_qty = $stock_level->qty;
                if($current_qty) {
                    $final_qty = $final_qty + intval($current_qty);
                }

                if($final_qty >= $required_qty) {
                    $in_stock = true;
                } else {
                    if($final_qty == 0) {
                        $in_stock = false;
                        $error_messages[] = $product->name." is not available in stock! <br>";
                    } else {
                        $in_stock = false;
                        $error_messages[] = "Only $final_qty $product->name available in stock!";
                    }
                }
            } else {
                $in_stock = false;
                $error_messages[] = $product->name." is not available in stock! <br>";
            }


            if($in_stock === false) {
				return response()->json([
					'status' => 'FAIL',
					'message' => implode('', $error_messages)
				]);
            } else {
				return response()->json([
					'status' => 'SUCCESS',
					'data' => $product,
					'stock_level' => $stock_level,
				]);
            }

        } else {
            $in_stock = true;

            if($in_stock === false) {
				return response()->json([
					'status' => 'FAIL',
					'message' => implode('', $error_messages)
				]);
            } else {
				return response()->json([
					'status' => 'SUCCESS',
					'data' => $product,
					'stock_level'=> array()
				]);
            }
        }
    }

	public function applyCode(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
		if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }
		$distributor_id = $user->distributor_id;

        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }
		$user_id = $user->id;

        $deal = DealAndDiscount::whereRaw("BINARY `deal_code`= ?", $request->discount_code)->where('is_active', 1)->where('distributor_id', $distributor_id);

        $dealCount = $deal->count();
        $deal = $deal->first();


        if($dealCount == 0) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Discount code is invalid!',
            ]);
        }

        $max_redemptions = intval($deal->redemptions_max);
        $client_id = $request->client_id;
        $client_redemptions = Order::where('client_id', $client_id)->where('deal_id',  $deal->id)->count();


        if($client_redemptions >= $max_redemptions) {
			return response()->json([
                'status' => 'FAIL',
                'message' => "Can not redeem this code more than $max_redemptions times!",
            ]);
        }

        $applicable_clients = $deal->clients->pluck('id')->toArray();
        $order_client = $client_id;
        $applicable_products = $deal->products->pluck('id')->toArray();
        $order_products = $request->products;

        if(count($applicable_clients) > 0) {
            if(!in_array($order_client, $applicable_clients)){
				return $this->returnErrorMessage();
            }
        }

        $validity = date('Y-m-d', strtotime($deal['validity']));
        $order_date = date('Y-m-d', strtotime($request->order_date));

        if($order_date >= $validity) {
			return response()->json([
					'status' => 'FAIL',
					'message' => "Discount code is expired!",
				]);
        }


        $is_weekend = Helper::is_weekend();
        $is_weekday = Helper::is_weekday($deal['week_days'], $request->order_date);
        $is_holiday = Helper::is_holiday($request->order_date, $distributor_id);
        $is_birthday = Helper::is_event($request->client_id, $request->order_date, 'date_of_birth', $distributor_id);
        $is_anniversary = Helper::is_event($request->client_id, $request->order_date, 'anniversary', $distributor_id);

        $final_amount = $this->getFinalAmount($request);
        if(!$is_weekday) {
            return $this->returnErrorMessage();
        }
        if($deal['applicable_on_weekends'] == 1) {
            if($is_weekend == false) {
                return $this->returnErrorMessage();
            }
        }
        if($deal['applicable_on_holidays'] == 1) {
            if($is_holiday == false) {
                return $this->returnErrorMessage();
            }
        }

        if($deal['applicable_on_bday_anniv'] == 1) {
            if($is_birthday == true || $is_anniversary == true) {

            } else {
                return $this->returnErrorMessage();
            }
        }
        if($deal['invoice_min_amount'] != null) {
            if($final_amount < $deal['invoice_min_amount']) {
                return $this->returnErrorMessage();
            }
        }
        if($deal['invoice_max_amount'] != null) {
            if($final_amount > $deal['invoice_max_amount']) {
                return $this->returnErrorMessage();
            }
        }


        if($deal->apply_on_bill_total > 0) {
            $returnResponse = [
                'status' => 'SUCCESS',
                'message' => "Applicable on bill total",
                'applicable_on' => "bill_total",
                'discount' => $deal->discount,
                'products' => [],
                'deal_id' => $deal->id,
            ];

        } else {
            $applicable_products_order = array_intersect($order_products, $applicable_products);

            if(count($applicable_products_order) == 0) {
                return $this->returnErrorMessage("Cant apply code in selected products!");
            } else {
                $returnResponse = [
                    'status' => 'SUCCESS',
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
	public function calculateOrder(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
		if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }
		$distributor_id = $user->distributor_id;

        /* if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        } */
		$user_id = $user->id;

		$client_id = $request->client_id;
		$client_data = Client::find($request->client_id);
		$branch_id = $user->branch_id;
		$branch = Branch::find($branch_id);
        $products = $request->products;
		$products_discount = $request->products_discount;
        $product_qty = $request->product_qty;

		$parray = array();
		foreach($products as $pk=>$pv){
			$parray[] = $pv;
		}
		$applicable_products_order = array();
		$deal = DealAndDiscount::find($request->deal_id);
		if($deal){
		$applicable_clients = $deal->clients->pluck('id')->toArray();
        $applicable_products = $deal->products->pluck('id')->toArray();
		
		$applicable_products_order = array_intersect($products, $applicable_products);
		$applicable_products_order = array_values($applicable_products_order);
		}
		$allProducts = Product::whereIn('id',$parray)->get();
		$total_amount = $total_sgst_amount = $total_cgst_amount = $total_igst_amount = 0;
		$order_products = array();$total_discount_amount = $total_net_amount = 0; 
		foreach($allProducts as $product){
			$amount = $product->sales_price * $product_qty[$product->id];
			$amount = Helper::decimalNumber($amount);
			$discount = $products_discount[$product->id];
			$deal_discount = 0;
			if($deal){
			if($deal->apply_on_bill_total > 0)
				$deal_discount = $deal->discount;
			else{
				if(in_array($product->id,$applicable_products_order))
				$deal_discount = $deal->discount;
			}
			}
			$discount += $deal_discount;
			$discount = Helper::decimalNumber($discount);
			$discount_amount = $amount * $discount / 100;
			$discount_amount = Helper::decimalNumber($discount_amount);
			$final_amount = $amount - $discount_amount;
			$final_amount = Helper::decimalNumber($final_amount);
			$total_discount_amount += $discount_amount;
			$total_amount += $final_amount;
			
			if($client_data->state_id == $branch->state_id){
				$sgst = $product->sgst;
                $cgst = $product->cgst;
				$gst_array = Helper::getCalucatedGST($final_amount, $sgst, $cgst, 0);
				$igst = 0;
			}else{
				$igst = $product->igst;
				$sgst = 0;
                $cgst = 0;
                $gst_array = Helper::getCalucatedGST($final_amount, 0, 0, $igst);
			}
			$gst_amount = $gst_array['total_gst_amount'];
			$net_amount = $final_amount;// + $gst_amount;
			$net_amount = Helper::decimalNumber($net_amount);
			
			$total_sgst_amount += $gst_array['sgst_amount'];
			$total_cgst_amount += $gst_array['cgst_amount'];
			$total_igst_amount += $gst_array['igst_amount'];
			$total_net_amount += $net_amount;
			
			$op = array('sales_price'=>$product->sales_price, 'amount'=>$amount, 'deal_discount'=>$deal_discount, 'product_discount'=>$products_discount[$product->id], 'discount_amount'=>$discount_amount, 'final_amount'=>$final_amount, 'sgst'=>$sgst, 'cgst'=>$cgst, 'igst'=>$igst, 'sgst_amount'=>$gst_array['sgst_amount'], 'cgst_amount'=>$gst_array['cgst_amount'], 'igst_amount'=>$gst_array['igst_amount'],'net_amount'=>$net_amount);
			$order_products[$product->id] = $op;
		}
		$total_amount = Helper::decimalNumber($total_amount);
		$total_discount_amount = Helper::decimalNumber($total_discount_amount);
		$total_sgst_amount = Helper::decimalNumber($total_sgst_amount);
		$total_cgst_amount = Helper::decimalNumber($total_cgst_amount);
		$total_igst_amount = Helper::decimalNumber($total_igst_amount);
		$total_net_amount = Helper::decimalNumber($total_net_amount);
		return response()->json([
			'status' => 'SUCCESS',
			'message' => "Calculation completed",
			'order_products' => $order_products,
			'total_amount' => $total_amount,
			'total_discount_amount' => $total_discount_amount,
			'total_sgst_amount' => $total_sgst_amount,
			'total_cgst_amount' => $total_cgst_amount,
			'total_igst_amount' => $total_igst_amount,
			'total_net_amount' => $total_net_amount,
		]);
	}

	private function getFinalAmount($request)
    {
        $products = $request->products;$parray = array();
		foreach($products as $pk=>$pv){
			$parray[] = $pv;
		}
        $products_discount = $request->products_discount;
        $product_qty = $request->product_qty;
        $order_date = $request->order_date;
		$allProducts = Product::whereIn('id',$parray)->get();

		$total_amount = 0;
		foreach($allProducts as $product){
            $amount = $product->sales_price * $product_qty[$product->id];
			$amount = Helper::decimalNumber($amount);
			$discount = $products_discount[$product->id];
			$discount = Helper::decimalNumber($discount);
			$discount_amount = $amount * $discount / 100;
			$discount_amount = Helper::decimalNumber($discount_amount);
			$netamount = $amount - $discount_amount;
			$netamount = Helper::decimalNumber($netamount);
			$total_amount += $netamount;
        }

        $client_data = Client::find($request->client_id);
        $client_primary_id = $client_data->id;

        return $total_amount;
    }

	private function returnErrorMessage($message = '')
    {
        $message = (!empty($message)) ?$message: "Discount code is invalid!";
        return response()->json([
            'status' => 'FAIL',
            'message' => $message,
        ]);
    }
	public function manageUserCommission($user, $order, $service_commission, $product_commission, $commission_json = [])
    {
        $distributor_id = $user->distributor_id;

        // Current Commission % of user
        $user_product_commission = $user->product_commission;
        $user_service_commission = $user->service_commission;

        // if($user_product_commission > 0 || $user_service_commission > 0) {
            $invoice_json = json_encode($commission_json);

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
        // }
    }

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
					return response()->json([
						'status' => 'FAIL',
						'message' => "Product not available in stock!"
					]);

                } else {
					return response()->json([
						'status' => 'FAIL',
						'message' => "Only $total_stock_lvl $product->name available in stock!"
					]);
                }
            }
        } else {
			return response()->json([
						'status' => 'FAIL',
						'message' => "Product not available in stock!"
					]);
        }
     }

	public function findByExternalId($external_id)
    {
        return Order::where('external_id', $external_id)->first();
    }
}
