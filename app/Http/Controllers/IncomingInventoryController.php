<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Datatables;

use App\Models\User;
use App\Models\Branch; 
use App\Models\Product;

use App\Models\StockMaster;
use App\Models\StockIncomeHistory;
use App\Models\StockEditHistory;
use App\Models\ProductsIncomingEntries;

use App\Http\Requests\Inventory\StoreInventoryRequest;
use App\Http\Requests\Inventory\UpdateInventoryRequest;

use App\Helpers\Helper;
use App\Models\Distributor;

class IncomingInventoryController extends Controller
{
                
    public function __construct()
    {
        $this->middleware('permission:incoming-stock-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:incoming-stock-create', ['only' => ['create','store']]);
		$this->middleware('permission:incoming-stock-update', ['only' => ['edit','update']]);
		$this->middleware('permission:incoming-stock-delete', ['only' => ['destroy']]);  
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
        
        return view('inventory.incoming.index')->with($data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data['invoice_number'] = $this->getGeneratedSKU();  
        
        $data['distributors'] = Distributor::all();
        $data['is_system_user'] = Helper::getDistributorId(); 

        if($request->get('product_id')) {
            $data['selected_product'] = Product::where('external_id', $request->get('product_id'))->first();
        }  

        return view('inventory.incoming.create')->with($data);
    }

    public function getGeneratedSKU($length = 6)
    {
        $today_date = date('mY');
        $series = StockIncomeHistory::withTrashed()->count();  
        $series++; 
        return substr(str_repeat(0, $length).$series, - $length) . $today_date;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInventoryRequest $request)
    {  
        $user_id = Auth::id();
        $user = User::find($user_id);  
        $payment_status = $request->payment_status ?? "Paid";
  
        $branch_id = intval($user->branch_id);
        $distributor_id = intval(Helper::getDistributorId());
        if($distributor_id == 0) { // is admin
            $distributor_id = intval($request->distributor_id);
            $branch_id = intval($request->branch_id);
        } 

        if(Helper::allowViewOnly($distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }
 
        $stock_income_history = StockIncomeHistory::create([
            'external_id' => Uuid::uuid4()->toString(),
            'date' => $request->date,
            'invoice_number' => $request->invoice_number,
            'invoice_value' => $request->invoice_value,
            'extra_freight_charges' => $request->extra_freight_charges,
            'source_type' => $request->source_type,
            'source_id' => $request->source_id,
            'invoice_type' => $request->invoice_type,
            'notes' => $request->notes,
            'amount_paid' => $request->amount_paid,
            'payment_type' => $request->payment_type,
            'payment_status' => $payment_status,

            'products_array' => json_encode($request->product_data),

            'branch_id' => $branch_id,
            'cerated_by' => $user_id,
            'updated_by' => $user_id,
            'distributor_id' => $distributor_id,
        ]);  
   
        if(!isset($request->product_data)) {
            Session()->flash('success', __('Stock successfully added!'));
            return redirect()->route('incoming_inventory.index');
        }

        foreach($request->product_data as $product) { 
            
            if(!isset($product['product_id'])) {
                continue;
            }  
            $product = ProductsIncomingEntries::create([
                'product_name' => $product['product_name'],
                'product_id' => $product['product_id'],
                'product_type' => "", 
                'sku_code' => $product['sku_code'],
                'mrp' => $product['mrp'],
                'qty' => $product['qty'],
                'cost_per_unit' => $product['cost_per_unit'],
                'gst_percent' => $product['gst'],
                'total_cost' => $product['total_cost'],
                'expiry' => $product['expiry'],
                
                'branch_id' => $branch_id,
                'stock_income_history_id' => $stock_income_history->id,
                'distributor_id' => $distributor_id,
            ]); 
             
        }

        foreach($request->product_data as $product) {  
            
            if(!isset($product['product_id'])) {
                continue;
            }  
 
            $is_product = StockMaster::where('product_id', $product['product_id'])->where('branch_id', $branch_id)->where('distributor_id', $distributor_id)->first();

            // Insert if there is no records
            // Update if there is product with the current user branch
            if($is_product != null) { 

                $current_stock = $is_product->qty;
                $total_stock = $current_stock + $product['qty'];
                
                $is_product->fill([  
                    'qty' => $total_stock,  
                    'updated_by' => $user_id,
                    'updated_at' => date('Y-m-d h:i:s a'),
                ])->save();
                    
            } else {
                $stock_master = StockMaster::create([
                    'external_id' => Uuid::uuid4()->toString(),
                    'product_id' => $product['product_id'],
                    'qty' => $product['qty'],
                    'branch_id' => $branch_id,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'distributor_id' => $distributor_id,
                    'updated_at' => date('Y-m-d h:i:s a'),
                ]); 
            }   
            
        }   
           
        if(isset($request->product_data)) {
            Session()->flash('success', __('Stock successfully added!'));
            return redirect()->route('incoming_inventory.index');
        }
    }

    
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {   
        $stock = StockIncomeHistory::with(['getDistributor']);
        
        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id != 0) { // Check if distributor
            $stock->where('distributor_id', $distributor_id);
        }  
        $stock = $stock->orderBy('id', 'desc')->get();
  
        return Datatables::of($stock) 
            ->addColumn('distributor', function ($branch) {
                return  $branch->getDistributor->name ?? "";
            }) 
            ->addColumn('invoice_number', function ($stock) {  
                return $stock->invoice_number;
            })  
            ->addColumn('date', function ($stock) {  
                return date('d-m-Y',strtotime($stock->date));
            })  
            ->addColumn('invoice_type', function ($stock) {  
                return $stock->invoice_type;
            })  
            ->addColumn('invoice_value', function ($stock) {  
                return $stock->invoice_value;
            })  
            ->addColumn('extra_freight_charges', function ($stock) {  
                return $stock->extra_freight_charges;
            })  
            ->addColumn('source', function ($stock) {  
                return $stock->getVendor->name ?? "";
            })  
            ->addColumn('amount_paid', function ($stock) {  
                return $stock->amount_paid;
            })  
            ->addColumn('payment_type', function ($stock) {  
                return $stock->payment_type;
            })  
            ->addColumn('payment_status', function ($stock) {  
                return $stock->payment_status;
            })  
            ->addColumn('branch', function ($stock) {  
                return $stock->getBranch->name ?? "";
            })    
            ->addColumn('action', function ($stock) { 
                $html = "<div class='d-flex'>";
				$html .= '<a href="#" class="view-in-modal btn btn-link" data-toggle="modal" data-id="'.$stock->external_id.'" data-target="#view-enquiry-modal" data-toggle="tooltip" title="View Details"><i class="flaticon-eye text-primary text-hover-primary" data-id="'.$stock->external_id.'" data-toggle="tooltip" title="View Details" ></i></a>';
                    if(\Entrust::can('incoming-stock-update') && !Helper::allowViewOnly($stock->distributor_id))
                    $html .= '<a href="'.route('incoming_inventory.edit', $stock->external_id).'" class="btn btn-link"  data-toggle="tooltip" title="Edit Stock"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>'; 
				$html .= "<input type='hidden' class='products_array' value='".$stock->products_array."'>"; 
                $html .= "</div>";
                return $html;
            })
            ->rawColumns(['distributor', 'invoice_number', 'date', 'invoice_type', 'invoice_value', 'extra_freight_charges', 'source', 'amount_paid', 'payment_type', 'payment_status', 'branch', 'action'])
            ->make(true);
    }

    public function productsById(Request $request)
    {
        $external_id = $request->external_id;
        $stock = StockIncomeHistory::where("external_id", $external_id)->first();

        $products_array = json_decode($stock->products_array);

        $html = "";
        if(!empty($products_array)){
            foreach($products_array as $product) {

                $expiry_date = !empty($product->expiry) ? date('d-m-Y', strtotime($product->expiry)) : "";
    
                $html .= "<tr>
                             <td>$product->product_name</td>
                             <td>$product->sku_code</td>
                             <td>$product->mrp</td>
                             <td>$product->qty</td>
                             <td>$product->cost_per_unit</td>
                             <td>$product->gst</td>
                             <td>$product->total_cost</td>
                             <td>$expiry_date</td>
                         </tr>"; 
            } 
        }
         
        $return_array = [
            'products_row' => $html,
            'notes' => $stock->notes,
            'invoice_number' => $stock->invoice_number,
            'distributor' => $stock->getDistributor->name ?? ""
        ];
        echo json_encode($return_array);
    }

    public function checkInvoiceNumber(Request $request)
    {    
        $id = $request->id;
        $invoice_number = $request->invoice_number;
        
        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->salon_id;
        }

        $invoice_number = StockIncomeHistory::where('invoice_number', $invoice_number)->first();
 
        if($invoice_number !== null) { 

            if($id == $invoice_number->id) {
                echo "true";
            } else {
                if($distributor_id != $invoice_number->distributor_id) {
                    echo "true";
                } else {
                    echo "false";
                }
            }
 
        } else {
            echo "true";
        } 
    }

    public function getInvoiceProduct(Request $request)
    {
        $invoice_number = $request->invoice_number;
        $product_id     = $request->product_id;

        $stock_income_history = StockIncomeHistory::where('invoice_number', $invoice_number)->first(); 
        $product = ProductsIncomingEntries::where('stock_income_history_id', $stock_income_history->id)->where('product_id', $product_id)->first();

        echo json_encode([
            'stock_income_history' => $stock_income_history,
            'product' => $product,
            'product_branch' => $product->getBranch->name ?? "",
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($external_id)
    {
        $stock_history = StockIncomeHistory::where('external_id', $external_id)->first(); 
        $data['stock_history'] = $stock_history;
        $data['invoice_number'] = $stock_history->invoice_number;
        $data['source_id'] = $stock_history->getVendor::pluck('name', 'id');
        $data['products_array'] = json_decode($stock_history->products_array);

        $data['distributors'] = Distributor::all();
        $data['is_system_user'] = Helper::getDistributorId();
  
        // dd($data['products_array']);
        return view('inventory.incoming.edit')->with($data); 
    }

    public function updateFullInvoice(UpdateInventoryRequest $request)
    {
        $user_id = Auth::id();
        $user = User::find($user_id);  
  
        $stock_income_history = StockIncomeHistory::where('external_id', $request->external_id)->first();

        $branch_id = $stock_income_history->branch_id; 
        $branch = Branch::find($branch_id);
        $distributor_id = $branch->distributor_id;
 
        $products_array = $stock_income_history->getProducts;
  
        foreach ($products_array as $product_entry) {
            $product_id = $product_entry->product_id; 

            $stock_master = StockMaster::where('product_id', $product_id)->where('branch_id', $branch_id)->first(); 
            $new_total_stock = $stock_master->qty - $product_entry->qty;
              
            $stock_master->fill([
                'qty' => $new_total_stock,
            ])->save();
            
            foreach($request->product_data as $new_product)
            {
                if($new_product['product_id'] == $product_id) { 
                    $product_entry->fill([
                        'product_name' => $new_product['product_name'], 
                        'product_type' => "", 
                        'sku_code' => $new_product['sku_code'],
                        'mrp' => $new_product['mrp'],
                        'qty' => $new_product['qty'],
                        'cost_per_unit' => $new_product['cost_per_unit'],
                        'gst_percent' => $new_product['gst'],
                        'total_cost' => $new_product['total_cost'],
                        'expiry' => $new_product['expiry'],
                    ])->save();
                }
            } 
        }

        foreach($request->product_data as $product) { 
            
            if(isset($product['new_added'])) {
                if(!isset($product['product_id'])) {
                    continue;
                }    
                $product = ProductsIncomingEntries::create([
                    'product_name' => $product['product_name'],
                    'product_id' => $product['product_id'],
                    'product_type' => "", 
                    'sku_code' => $product['sku_code'],
                    'mrp' => $product['mrp'],
                    'qty' => $product['qty'],
                    'cost_per_unit' => $product['cost_per_unit'],
                    'gst_percent' => $product['gst'],
                    'total_cost' => $product['total_cost'],
                    'expiry' => $product['expiry'],
                    
                    'branch_id' => $branch_id,
                    'stock_income_history_id' => $stock_income_history->id,
                ]); 
            } 
        }

        foreach($request->product_data as $product) {  
            
            if(!isset($product['product_id'])) {
                continue;
            }  
 
            $is_product = StockMaster::where('product_id', $product['product_id'])->where('branch_id', $branch_id)->first();

            // Insert if there is no records
            // Update if there is product with the current user branch
            if($is_product != null) { 

                $current_stock = $is_product->qty;
                $total_stock = $current_stock + $product['qty'];
                
                $is_product->fill([  
                    'qty' => $total_stock,  
                    'updated_by' => $user_id,
                    'updated_at' => date('Y-m-d h:i:s a'),
                ])->save();
                    
            } else {
                $product = StockMaster::create([
                    'external_id' => Uuid::uuid4()->toString(),
                    'product_id' => $product['product_id'],
                    'qty' => $product['qty'],
                    'branch_id' => $branch_id,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'distributor_id' => $distributor_id,
                ]); 
            }   
            
        }   

        $stock_income_history->fill([ 
            'date' => $request->date,
            'invoice_number' => $request->invoice_number,
            'invoice_value' => $request->invoice_value,
            'extra_freight_charges' => $request->extra_freight_charges,
            'source_type' => $request->source_type,
            'source_id' => $request->source_id,
            'invoice_type' => $request->invoice_type,
            'notes' => $request->notes,
            'amount_paid' => $request->amount_paid,
            'payment_type' => $request->payment_type,
            'payment_status' => $request->payment_status,

            'products_array' => json_encode($request->product_data),

            'branch_id' => $branch_id, 
            'updated_by' => $user_id,
            'updated_at' => date('Y-m-d h:i a') 
        ])->save(); 

        Session()->flash('success', __('Stock updated successfully!'));
        return redirect()->route('incoming_inventory.index');
    }

    /**
     * Update single entry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    { 
        // dd($request);
        $user_id = Auth::id();
        $branch_id = $request->branch_id; 
        $product_id = $request->product_id;
        $invoice_number = $request->invoice_number;

        $total_stock = $request->total_qty;
        $old_qty = $request->old_qty;
        $new_qty = $request->qty; 
 
        if($old_qty != $new_qty){ 

            $total_stock = $total_stock - $old_qty + $new_qty;   

            $stock_level = StockMaster::where('product_id', $product_id)->where('branch_id', $branch_id)->first(); 
            $stock_level->fill([
                'qty' => $total_stock,
                'updated_at' => date('Y-m-d h:i:s a')
            ])->save();
        } else { 
            $stock_level = StockMaster::where('product_id', $product_id)->where('branch_id', $branch_id)->first();  
            $stock_level->fill([ 
                'updated_at' => date('Y-m-d h:i:s a')
            ])->save();
        }

        if(Helper::allowViewOnly($stock_level->distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        $stock_income_history = StockIncomeHistory::where('invoice_number', $invoice_number)->first(); 
        $product_entry = ProductsIncomingEntries::where('product_id', $product_id)->where('stock_income_history_id', $stock_income_history->id)->first();

        $new_products_array = [];
        $products_array = json_decode($stock_income_history->products_array);

        // dd($products_array); 
        $total_cost = ($new_qty * $request->cost_per_unit);
        $gst = $total_cost / 100 * $request->gst;
        $total_cost = $total_cost + $gst;

        foreach ($products_array as $product) {  
            if($product_id == $product->product_id) {
                $product = [
                    "product_id" => $product->product_id,
                    "product_name" => $product->product_name,
                    "sku_code" => $product->sku_code,
                    "mrp" => $request->mrp,  
                    "qty" => $new_qty,
                    "cost_per_unit" => $request->cost_per_unit, 
                    "gst" => $request->gst, 
                    "total_cost" => $total_cost,
                    "expiry" => $product->expiry,
                    'updated_by' => $user_id,
                ];
                array_push($new_products_array, $product);
            } else {
                array_push($new_products_array, $product);
            }
        } 

        $stock_income_history->fill([
            'products_array' => json_encode($new_products_array),
            'updated_at' => date('Y-m-d'),
            'updated_by' => $user_id
        ])->save();

        $product_entry->fill([  
            'qty' => $new_qty, 
            'cost_per_unit' => $request->cost_per_unit, 
            'gst_percent' => $request->gst, 
            'total_cost' => $total_cost, 
            'mrp' => $request->mrp,  
            'updated_by' => $user_id,
            'updated_at' => date('Y-m-d h:i:s a'),
        ])->save();

        $stock_edit_history = StockEditHistory::create([
            'external_id' => Uuid::uuid4()->toString(), 
            'product_id' => $product_id,
            'invoice_number' => $invoice_number, 
            'old_qty' => $old_qty,
            'new_qty' => $new_qty,
            'old_cost_per_unit' => $request->old_cost_per_unit,
            'new_cost_per_unit' => $request->cost_per_unit,
            'old_gst_percent' => $request->old_gst_percent,
            'new_gst_percent' => $request->gst,
            'old_mrp' => $request->old_mrp,
            'new_mrp' => $request->mrp,
            'remarks' => $request->remarks,
            'date' => $request->date, 
            'branch_id' => $request->branch_id,
            'cerated_by' => $user_id,
            'updated_by' => $user_id,
        ]); 

        Session()->flash('success', __('Stock successfully updated!'));
        return redirect()->route('stock_master.index');
    }

    public function remove_product_entry(Request $request)
    {
        $product_id = $request->product_id;
        $stock_history_id = $request->stock_history_id;

        
        $product_entry = ProductsIncomingEntries::where('product_id', $product_id)->where('stock_income_history_id', $stock_history_id)->first();

        // Update Current stock level
        $stock_master = StockMaster::where('product_id', $product_id)->where('branch_id', $product_entry->branch_id)->first(); 
        $new_total_stock = $stock_master->qty - $product_entry->qty; 
        $stock_master->fill([
            'qty' => $new_total_stock,
        ])->save();

         // soft delete product entry
        $product_entry->delete();
        
        // Update product json
        $product_history = StockIncomeHistory::find($stock_history_id);
        $products_array = json_decode($product_history->products_array, true);

        $new_arr = array_filter($products_array, function ($product_single) use ($product_id) {
            return $product_id != $product_single['product_id'];
        });
        $products_new_json = !empty($new_arr) ? json_encode($new_arr) : ""; 

        $product_history->fill([
            'products_array' => $products_new_json
        ])->save();

        echo json_encode([
            'status' => true,
            'message' => 'Product successfully removed!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
