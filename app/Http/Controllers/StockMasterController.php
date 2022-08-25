<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Datatables;
use DB;

use App\Models\User; 
use App\Models\Branch; 
use App\Models\Product;

use App\Models\StockMaster;
use App\Models\StockIncomeHistory;
use App\Models\ProductsIncomingEntries;

use App\Helpers\Helper;
use App\Models\Distributor;

class StockMasterController extends Controller
{
                    
    public function __construct()
    {
        $this->middleware('permission:stock-level-view', ['only' => ['index', 'anydata']]); 
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

        return view('inventory.stock.index')->with($data);
    }

    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {     
        $stock = StockMaster::with(['getDistributor'])->join('products', 'products.id', '=', 'stock_master.product_id')->select('stock_master.updated_at as stock_update_date', 'stock_master.*', 'products.*')->where('products.deleted_at', null); 

        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id != 0) { // Check if distributor
            $stock->where('stock_master.distributor_id', $distributor_id);
        }   
        $stock = $stock->orderBy('stock_master.id', 'desc')->get();

        return Datatables::of($stock) 
            ->addColumn('distributor', function ($stock) {
                return  $stock->getDistributor->name ?? "";
            }) 
            ->addColumn('name', function ($stock) {  
                return $stock->getProduct->name ?? "";
            })  
            ->addColumn('sku_code', function ($stock) {  
                return $stock->getProduct->sku_code ?? "";
            })  
            ->addColumn('qty', function ($stock) {  
                $qty = $stock->qty ?? 0;
                $unit = $stock->getProduct->unit->name ?? "QTY";
                return $qty ." ($unit)";
            })  
            ->addColumn('branch', function ($stock) {  
                return $stock->getBranch->name ?? "";
            })
			->addColumn('last_cost', function ($stock) {  
				$data=ProductsIncomingEntries::where('product_id','=',$stock->product_id)->where('branch_id','=',$stock->branch_id)->orderBy('id', 'desc')->first();
				if($data)
					return $data->total_cost;
				else
					return '';
                return $stock->getBranch->name ?? "";
            })
            ->addColumn('last_updated', function ($stock) {   
                return $stock->stock_update_date != "" ? date('d-m-Y ', strtotime($stock->stock_update_date)) : "";
            })  
            ->addColumn('action', function ($stock) { 
				// if(\Entrust::can('stock-update'))
				// $html = '<a href="#" class="btn btn-link"  data-toggle="tooltip" title="Edit Stock" data-id="'.$stock->external_id.'"><i class="flaticon2-pen text-primary text-hover-primary" data-id="'.$stock->external_id.'"></i></a>'; 
                $distributor = $stock->getDistributor->name ?? "";
                if(isset($stock->getProduct->id) && !Helper::allowViewOnly($stock->distributor_id)) {
                    $html = '<a href="#" class="edit-stock-modal ml-3" data-toggle="modal" data-id="'.$stock->external_id.'" data-target="#edit-stock-modal" data-toggle="tooltip" title="Edit stock level"><i class="flaticon2-pen text-primary text-hover-primary" data-id="'.$stock->external_id.'" data-toggle="tooltip" title="Edit stock level" ></i></a>';
                    $html .= "<input type='hidden' class='product_id' value='".$stock->product_id."'>"; 
                    $html .= "<input type='hidden' class='product_name' value='".$stock->getProduct->name."'>"; 
                    $html .= "<input type='hidden' class='sku_code' value='".$stock->getProduct->sku_code."'>"; 
                    $html .= "<input type='hidden' class='qty' value='".$stock->qty."'>"; 
                    $html .= "<input type='hidden' class='branch' value='".$stock->branch_id."'>"; 
                    $html .= "<input type='hidden' class='last_updated' value='".$stock->last_updated."'>"; 
                    $html .= "<input type='hidden' class='distributor' value='".$distributor."'>"; 
                    return $html;
                } else {
                    return "";
                } 
            })
            ->rawColumns(['distributor', 'name', 'sku_code', 'sales_price', 'qty', 'expiry_reminder','last_cost', 'reorder_qty', 'branch', 'last_updated', 'action'])
            ->make(true);
    }
    
    /**
     *  @return mixed
     *  $Invoice_Numbers
     */
    public function getInvoiceNumber(Request $request)
    {
        $id = $request->get('q'); 
        $distributor_id = Helper::getDistributorId();

        $invoices = StockIncomeHistory::where('invoice_number', 'like', "%{$id}%");
        if($distributor_id != 0) { 
            $invoices = $invoices->where('distributor_id', $distributor_id);
        } 
        $invoices = $invoices->with('getProducts')->get();
  
        // dd($invoices[0]->getProducts[0]->product_id);

        $final_array = [];
        foreach($invoices as $invoice) { 
            foreach($invoice->getProducts as $product) {
                $invoice_push = [
                    'invoice_number' => $invoice->invoice_number,
                    'product_id' => $product->product_id,
                    'branch_id' => $invoice->branch_id,
                ];
                array_push($final_array, $invoice_push);
            }
        }
  
        return response()->json($final_array);
    }
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function stockReport()
    {
        $data['is_system_user'] = Helper::is_system_user();  
        $data['distributors'] = Distributor::all(); 

        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        } 

        return view('reports.stock')->with($data);
    }
}
