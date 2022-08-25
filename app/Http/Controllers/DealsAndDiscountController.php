<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Datatables;

use App\Models\User; 
use App\Models\Holiday; 
use App\Models\Product;
use App\Models\DealAndDiscount;
use App\Models\DealProductService;
use App\Models\Tag;
use App\Models\TagCondition;

use App\Http\Requests\Deal\StoreDealRequest;
use App\Http\Requests\Deal\UpdateDealRequest;

use App\Helpers\Helper;
use App\Models\Distributor;

class DealsAndDiscountController extends Controller
{
        
    public function __construct()
    {
        $this->middleware('permission:deal-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:deal-create', ['only' => ['create','store']]);
		$this->middleware('permission:deal-update', ['only' => ['edit','update']]);  
		$this->middleware('permission:deal-toggle', ['only' => ['toggleDealStatus']]);  
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['is_system_user'] = Helper::is_system_user(); 
        
        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        } 

        return view('deals.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
        $data['segaments'] = ['1' => "Fake Entry", '2' => "Entry Two"]; 
        $data['is_system_user'] = Helper::getDistributorId(); 
        return view('deals.create')->with($data);
    }
 
    // Check repeat deal code
    public function checkCodeRepeat(Request $request)
    {    
        $id = $request->id;
        $deal_code = $request->deal_code;
 
        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // system user
            $distributor_id = $request->distributor_id;
        }

        $deal_code = DealAndDiscount::where('deal_code', $deal_code)->where('distributor_id', $distributor_id)->first();

        if($deal_code !== null) { 
            if($id == $deal_code->id) { 
                echo "true";
            } else {
                echo "false";
            }
        } else {
            echo "true";
        } 
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDealRequest $request)
    {
        $user_id = Auth::id();

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;
        }

        if(Helper::allowViewOnly($distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }
  
        $deal_entry = [
            'customer_segment_special' => 0,
            'external_id' => Uuid::uuid4()->toString(), 
            'deal_name' => $request->deal_name,
            'deal_code' => $request->deal_code,
            'deal_description' => $request->deal_description,
            'validity' => $request->validity,
            'start_at' => $request->start_at != null ? date("Y-m-d H:i:s", strtotime($request->start_at)) : null,
            'end_at' => $request->end_at != null ? date("Y-m-d H:i:s", strtotime($request->end_at)) : null,
            'applicable_on_weekends' => $request->applicable_on_weekends,
            'applicable_on_holidays' => $request->applicable_on_holidays,
            'applicable_on_bday_anniv' => $request->applicable_on_bday_anniv,
            'week_days' => $request->week_days,
            'invoice_min_amount' => $request->invoice_min_amount ?? 0,
            'invoice_max_amount' => $request->invoice_max_amount ?? 0,
            'redemptions_max' => $request->redemptions_max,
            'benifit_type' => $request->benefit_type,
            'discount' => $request->discount,
            'products_service_array' => json_encode($request->deal_array),
            'is_active' => $request->is_active !== null ? 1 : 0,
            'created_by' => $user_id,
            'updated_by' => $user_id,
            'distributor_id' => $distributor_id,
            'apply_on_bill_total' => $request->apply_on_bill_total == "on" ? 1 : 0
        ];
 
        $deal_entry = DealAndDiscount::create($deal_entry);
 

        // if(!isset($request->deal_array)) {
        //     Session()->flash('success', __('Deal successfully added!'));
        //     return redirect()->route('deals.index');
        // }
  
        $deal_entry->products()->sync($request->products);
        $deal_entry->clients()->sync($request->clients);

        // foreach($request->deal_array as $deal_product) {  
        //     if(!isset($deal_product['type'])) {
        //         continue;
        //     }  
        //     $deal = DealProductService::create([
        //         'external_id' => Uuid::uuid4()->toString(), 
        //         'product_type' => $deal_product['type'],
        //         'category_id' => $deal_product['category'] ?? 0,
        //         'sub_category_id' => $deal_product['sub_category'] ?? 0,
        //         'product_id' => $deal_product['product'] ?? 0,
        //         'product_min_price' => $deal_product['min_price'],
        //         'product_max_price' => $deal_product['max_price'],
        //         'deal_id' => $deal_entry->id,
        //     ]); 
        // }

        Session()->flash('success', __('Deal successfully added!'));
        return redirect()->route('deals.index');
    }

    private function checkDealClient($segament_value)
    {  
        $data = [
            'All Customers',
            'New Customers',
            'Repeat Customers', 
            'Regular Customers',  
            'Risk Customers', 
            'Lost Customers',  
            'No Risk',
            'At Risk',
        ];

        if(in_array($segament_value, $data)){
            return true;
        } else {
            return false;
        }
    }

    
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {   
        $deal = DealAndDiscount::with(['getDistributor'])->where('is_archive', 0); 

        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id != 0) { // Check if distributor
            $deal->where('distributor_id', $distributor_id);
        } 
        
        $deal = $deal->orderBy('id', 'desc')->get();
           
        return Datatables::of($deal) 
            ->addColumn('distributor', function ($branch) {
                return  $branch->getDistributor->name ?? "";
            }) 
            ->addColumn('namelink', function ($deal) {  
                $url = url('admin/deals/'.$deal->external_id);
                return '<a href="'.$url.'">' . $deal->deal_name . '</a>';
            }) 
            ->addColumn('segament', function ($deal) {  
                if($deal->customer_segment_special !== 0) {
                    return $deal->getTag->name;
                } else {
                    return $deal->customer_segment_client;
                }
            }) 
            ->addColumn('deal_code', function ($deal) {
                return $deal->deal_code;
            })  
            ->addColumn('validity', function ($deal) {
                return date('d-m-Y', strtotime($deal->validity));
            })  
            ->addColumn('start_at', function ($deal) {
                return  date("h:i a", strtotime($deal->start_at));
            })  
            ->addColumn('end_at', function ($deal) {
                return  date("h:i a", strtotime($deal->end_at));
            })  
            ->addColumn('is_active', function ($deal) { 
                $html = '<span class="switch switch-primary" data-toggle="tooltip" title="Active/Inactive deal">';
                $html .= '<label>'; 
                if(\Entrust::can('deal-toggle') && !Helper::allowViewOnly($deal->distributor_id)) {
                    if($deal->is_active == 0) {  
                        $html .= '<input type="checkbox" name="is_active" class="toggle-deal-status">';
                    } else {
                        $html .= '<input type="checkbox" name="is_active" class="toggle-deal-status" checked>';
                    }
                } else {
                    if($deal->is_active == 0) {  
                        $html .= "Inactive";
                    } else {
                        $html .= "Active";
                    }
                }
                $html .= '<span></span>';
                $html .= '</label>';  
                $html .= '</span>'; 
                return $html;
            })  
            ->addColumn('is_active_word', function ($deal) { 
                if($deal->is_active == 0) {
                    return "Inactive";
                } else {
                    return "Active";
                }
            })  
            ->addColumn('action', function ($deal) {
                $url = url('admin/deals/'.$deal->external_id);
				$html = '<form action="'.route('deals.destroy', $deal->external_id).'" class="d-flex" method="POST">';
                $html .= '<a href="'.$url.'" class="btn btn-link"><i class="flaticon-eye text-primary text-hover-primary" data-toggle="tooltip" title="View Details"></i></a>';
				if(\Entrust::can('deal-update') && !Helper::allowViewOnly($deal->distributor_id))
				$html .= '<a href="'.route('deals.edit', $deal->external_id).'" class="btn btn-link"  data-toggle="tooltip" title="Edit deal"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';
				$html .= '<input type="hidden" name="_method" value="DELETE">';
				if(\Entrust::can('deal-delete'))
				// $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-deal" data-toggle="tooltip" title="Archive deal"><i class="fas fa-archive text-danger text-hover-warning"></i></button>';  
                $html .= '<input type="hidden" class="deal_id" value="'.$deal->external_id.'">'; 
				$html .= csrf_field();
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['distributor', 'namelink', 'segament', 'deal_code', 'validity', 'start_at', 'end_at', 'is_active', 'is_active_word', 'action'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($external_id)
    {
        $data['deal'] = DealAndDiscount::where('external_id', $external_id)->first(); 
        $data['clients'] = $data['deal']->clients; 
        $data['clients_count'] = $data['deal']->clients->count(); 
        $data['products'] = $data['deal']->products; 
 
        // $data['deal_products'] = DealProductService::where('deal_id', $data['deal']->id)->with('product', 'category', 'sub_category')->get();
  
        return view('deals.show')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($external_id)
    {  
        $distributor_id = Helper::getDistributorId();
        
        // Only system user can access others data
        if($distributor_id == 0) { 
            $deal = $this->findByExternalId($external_id);  
        } else {    
            $deal = DealAndDiscount::where('external_id', $external_id)->where('distributor_id', $distributor_id)->firstOrFail(); 
        } 
        
        $data['is_system_user'] = $distributor_id;
        $data['distributor'] = Distributor::findOrFail($deal->distributor_id); // current record distributor name (for admin)

        $data['deal'] = $deal;
        // $data['deal_products'] = DealProductService::with('product', 'category', 'sub_category')->where('deal_id', $deal->id)->get();

        if($deal->customer_segment_special !== 0) {  
            $data['selected_segament'] = [
                $deal->customer_segment_special => $deal->getTag->name
            ];
        } else { 
            $data['selected_segament'] = [
                $deal->customer_segment_client => $deal->customer_segment_client
            ];
        }

        $data['selected_clients'] = $deal->clients->pluck('name', 'id'); 
        $data['selected_products'] = $deal->products->pluck('name', 'id'); 

        // dd($data);
 
        return view('deals.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDealRequest $request, $external_id)
    { 
        $user_id = Auth::id();
        $start_at = $request->start_at != null ? date("Y-m-d H:i:s", strtotime($request->start_at)) : null;
        $end_at = $request->end_at != null ? date("Y-m-d H:i:s", strtotime($request->end_at)) : null;
  
        $deal_entry = [ 
            'deal_name' => $request->deal_name,
            'deal_code' => $request->deal_code,
            'deal_description' => $request->deal_description,
            'validity' => $request->validity,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'applicable_on_weekends' => $request->applicable_on_weekends,
            'applicable_on_holidays' => $request->applicable_on_holidays,
            'applicable_on_bday_anniv' => $request->applicable_on_bday_anniv,
            'week_days' => $request->week_days,
            'invoice_min_amount' => $request->invoice_min_amount ?? 0,
            'invoice_max_amount' => $request->invoice_max_amount ?? 0,
            'redemptions_max' => $request->redemptions_max,
            'benifit_type' => $request->benefit_type,
            'discount' => $request->discount,
            'products_service_array' => json_encode($request->deal_array),
            'is_active' => $request->is_active !== null ? 1 : 0,
            'created_by' => $user_id,
            'updated_by' => $user_id,
            'apply_on_bill_total' => $request->apply_on_bill_total == "on" ? 1 : 0
        ];
  
        if($this->checkDealClient($request->segament_id)) { 
            $deal_entry['customer_segment_client'] = $request->segament_id;
            $deal_entry['customer_segment_special'] = "";
        } else { 
            $deal_entry['customer_segment_client'] = "";
            $deal_entry['customer_segment_special'] = $request->segament_id;
        }

        $DealAntDiscount = $this->findByExternalId($external_id);

        if(Helper::allowViewOnly($DealAntDiscount->distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        $deal_entry = $DealAntDiscount->fill($deal_entry)->save();
        // $deal_entry_id = $DealAntDiscount->id;
  
        // if(!isset($request->deal_array)) {
        //     Session()->flash('success', __('Deal successfully added!'));
        //     return redirect()->route('deals.index');
        // } 
        
        $DealAntDiscount->products()->sync($request->products);
        $DealAntDiscount->clients()->sync($request->clients);

        // foreach($request->deal_array as $deal_product) {  
        //     if(!isset($deal_product['type'])) {
        //         continue;
        //     }   
        //     if(isset($deal_product['entry_id'])) {

        //         $deal = DealProductService::find($deal_product['entry_id']);
        //         $deal->fill([  
        //             'product_type' => 0,
        //             'category_id' => 0,
        //             'sub_category_id' => 0,
        //             'product_id' => $deal_product['product'] ?? 0,
        //             'product_min_price' => 0,
        //             'product_max_price' => 0, 
        //         ])->save();

        //     } else { 
        //         $deal = DealProductService::create([
        //             'external_id' => Uuid::uuid4()->toString(), 
        //             'product_type' => 0,
        //             'category_id' => 0,
        //             'sub_category_id' => 0,
        //             'product_id' => $deal_product['product'] ?? 0,
        //             'product_min_price' => 0,
        //             'product_max_price' => 0,
        //             'deal_id' => $deal_entry_id,
        //         ]); 
        //     } 
        // }

        Session()->flash('success', __('Deal successfully updated!'));
        return redirect()->route('deals.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $external_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getSegaments()
    {
        $holidays = Holiday::select('name', 'id')->get()->toArray(); 
  
        $customers_segaments = [
            ['id' =>  'All Customers' ,'name' => 'All Customers'],
            ['id' =>  'New Customers' ,'name' => 'New Customers'],
            ['id' =>  'Repeat Customers' ,'name' => 'Repeat Customers'], 
            ['id' =>  'Regular Customers' ,'name' => 'Regular Customers'], 
            ['id' =>  'Risk Customers' ,'name' => 'Risk Customers'], 
            ['id' =>  'Lost Customers' ,'name' => 'Lost Customers'],  
        ];

        $new_arr = array_merge($customers_segaments, $holidays); 

        return response()->json($new_arr);
    }

    
    /**
     *  @return mixed
     *  $industries
     */
    public function getSegamentsByName(Request $request)
    {
        $name = $request->get('q');  

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { //is system user
            $distributor_id = $request->distributor_id;
        }

        $tags = Tag::where('name', 'like', "%{$name}%")->where('distributor_id', $distributor_id)->where('is_archive', 0)->get()->toArray();
  
        $customers_segaments = [
            ['id' =>  'All Customers' ,'name' => 'All Customers'],
            ['id' =>  'New Customers' ,'name' => 'New Customers'],
            ['id' =>  'Repeat Customers' ,'name' => 'Repeat Customers'], 
            ['id' =>  'Regular Customers' ,'name' => 'Regular Customers'], 
            ['id' =>  'Risk Customers' ,'name' => 'Risk Customers'], 
            ['id' =>  'Lost Customers' ,'name' => 'Lost Customers'],   
            ['id' =>  'No Risk' ,'name' => 'No Risk'],   
            ['id' =>  'At Risk' ,'name' => 'At Risk'], 
        ];

        $new_arr = array_merge($tags, $customers_segaments); 
         
        return response()->json($new_arr);
    }  

    
    public function findByExternalId($external_id)
    {
        return DealAndDiscount::where('external_id', $external_id)->firstOrFail();
    }

    public function archiveDeal(Request $request)
    {
        $user_id = Auth::id();
        $external_id = $request->external_id;
        $deal = $this->findByExternalId($external_id);

        $deal->fill([
            'is_archive' => 1,
            'is_active' => 0,
            'updated_by' => $user_id,
        ])->save();

        Session()->flash('success', __('Deal successfully archived!'));

        return response()->json([
            'status' => true,
            'message' => "Deal successfully archived!", 
        ]);
    }

    /**
     * Toggle deal status to active / inactive
     */
    public function toggleDealStatus(Request $request)
    {
        $user_id = Auth::id();
        $external_id = $request->external_id;
        $deal = $this->findByExternalId($external_id);
  
        $deal->fill([ 
            'is_active' => $request->is_active,
            'updated_by' => $user_id,
        ])->save();

        if($request->is_active == 1) {
            $title = "Activated";
            $msg = "Deal successfully activated!";
        } else {
            $title = "Inactived";
            $msg = "Deal successfully inactivated!";
        }

        Session()->flash('success', __($msg));

        return response()->json([
            'status' => true,
            'title' => $title,
            'message' => $msg, 
        ]);
    }

    /**
     * Remove specific product/service entry from deals_and_discount table 
     * and remove specific product/service from json stored in deals_and_discount
     */
    public function remove_product_entry(Request $request)
    {
        $product_id = $request->product_id; 
   
        // $product_entry = DealProductService::find($product_id);
        $deal_entry = DealAndDiscount::find($product_entry->deal_id);
   
        // soft delete product/service entry
        $product_entry->delete();
        
        // Update product/service json 
        $products_array = json_decode($deal_entry->products_service_array, true);
  
        $new_arr = array_filter($products_array, function ($product_single) use ($product_id) {
            return $product_id != $product_single['entry_id'];
        });
        $products_new_json = !empty($new_arr) ? json_encode($new_arr) : ""; 
  
        $product_entry->fill([
            'products_service_array' => $products_new_json
        ])->save();

        echo json_encode([
            'status' => true,
            'message' => 'Product/service Removed Successfully!!'
        ]);
    }
}
