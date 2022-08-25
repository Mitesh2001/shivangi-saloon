<?php

namespace App\Http\Controllers;

use App\Enums\Country;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Status;
use App\Models\Task;
use App\Repositories\FilesystemIntegration\FilesystemIntegration;
use App\Repositories\Money\MoneyConverter;
use App\Services\ClientNumber\ClientNumberService;
use App\Services\Invoice\InvoiceCalculator;
use App\Services\Search\SearchService;
use App\Services\Storage\GetStorageProvider;
use Carbon\Carbon;
use Config;
use Dinero;
use Datatables;
use App\Models\Client;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Models\Integration;
use App\Models\Industry;
use Ramsey\Uuid\Uuid;
use App\Models\Contact;

use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Package;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;

use App\Helpers\Helper;
use App\Models\Distributor;
use App\Models\ProductsIncomingEntries;
use App\Models\UserProductCommission;


class ProductsController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:product-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:product-create', ['only' => ['create','store']]);
		$this->middleware('permission:product-update', ['only' => ['edit','update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->can('product-view')) {
            return redirect('/');
        }

        $data['is_system_user'] = Helper::is_system_user();
        $data['distributors'] = Distributor::all();

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        }

        $data['categories'] = Category::all();

        return view('products.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('product-create')) {
            return redirect('/');
        }
        $data['distributors'] = Distributor::all();
        $data['is_system_user'] = Helper::getDistributorId();

        $data['categories'] = Category::pluck('name', 'id')->toArray();
        $data['units'] = Unit::all()->toArray();

        $data['packages'] = Package::where('distributor_id', $data['is_system_user'])->pluck('name', 'id')->toArray();

        return view('products.create')->with($data);
    }

    public function checkSKUCode(Request $request)
    {
        $id = $request->id;
        $sku_code = $request->sku_code;

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->salon_id;
        }

        $product = Product::where('sku_code', $sku_code)->first();

        if($product !== null) {

            if($id == $product->id) {
                echo "true";
            } else {
                if($distributor_id != $product->distributor_id) {
                    echo "true";
                } else {
                    echo "false";
                }
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
    public function store(Request $request)
    {
        // dd($request->product_data);

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;
        }

        if(Helper::allowViewOnly($distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        $product_name = strtolower(str_replace(' ', '_',$request->name));

        if($request->hasFile('thumbnail')) {

            $thumbnail_name = $product_name ."_". time() .".". $request->thumbnail->extension();
            $path = 'storage/assets/products/images/thumbnail/';
            $returned = $request->thumbnail->move(public_path($path), $thumbnail_name);
            $thumbnail_name_store = $path . $thumbnail_name;

        } else {
            $thumbnail_name_store = "";
        }

        if($request->hasFile('other_document')) {

            $document_name = $product_name ."_". time() .".". $request->other_document->extension();
            $other_document_path = 'storage/assets/products/documents/';
            $request->other_document->move(public_path($other_document_path), $document_name);
            $other_document_name = $other_document_path . $document_name;

        } else {
            $other_document_name = "";
        }

        $sku_code = "";
        if($request->type != "1") {
            $sku_code = $request->sku_code;
        }

        $user_id = Auth::id();
        $product = Product::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'description' => $request->description,
            'sales_price' => $request->sales_price,
            'purchase_price' => $request->purchase_price,
            'thumbnail' => $thumbnail_name_store,
            'other_document' => $other_document_name,
            'type' => $request->type,
            // 'category_id' => $request->category_id,
            'sku_code' => $sku_code,
            'unit_id' => $request->unit_id,
            // 'package_id' => $request->package_id,
            'package_id' => 0,
            'created_by' => $user_id,
            'updated_by' => $user_id,
            'distributor_id' => $distributor_id,
            'sgst' => $request->sgst,
            'cgst' => $request->cgst,
            'igst' => $request->igst,
            'expiry_reminder' => $request->expiry_reminder ?? 0,
            'is_default' => $request->is_default_service ? 1 : 0,
            'reorder_qty' => $request->reorder_qty,
        ]);

        $product->categories()->sync($request->categories);


        if($product->type == 2) {
            $product->packageProducts()->sync($request->product_data);
        }

        Session()->flash('success', __('Product/Service successfully added'));

        if($request->get('is_popup')) {
            return redirect()->route('product.index', ['is_popup' => 1]);
        } else {
            return redirect()->route('product.index');
        }
    }



    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {
        $product = Product::orderBy('id', 'desc');

        $distributor_id = Helper::getDistributorId();
        if($distributor_id != 0) { // Check if distributor
            $product->where('distributor_id', $distributor_id);
        }

        $product = $product->get();

        return Datatables::of($product)
            ->addColumn('distributor', function ($branch) {
                return  $branch->getDistributor->name ?? "";
            })
            ->addColumn('thumbnail', function ($product) {
                if(!empty($product->thumbnail)) {
                    return '<img src="'. asset($product->thumbnail) .'" alt="' . $product->name . '" height="100px">';
                } else {
                    return '<img src="'. asset("storage/assets/no_image.png") .'" alt="No-Image" height="100px">';
                }
            })
            ->addColumn('namelink', function ($product) {
                $url = url('admin/product/view/'.$product->external_id);
                return '<a href="'.$url.'">' . $product->name . '</a>';
            })
            ->addColumn('name', function ($product) {
                return $product->name;
            })
            ->addColumn('description', function ($product) {
                return $product->description;
            })
            ->addColumn('category', function ($product) {
                // return $product->category->name;
                $categories = $product->categories->pluck('name')->toArray();
                return implode(", ",$categories);
            })
            ->addColumn('type', function ($product) {
                if($product->type == 0) {
                    return "Product";
                } elseif($product->type == 1) {
                    return "Service";
                } elseif($product->type == 2) {
                    return "Package";
                }
            })
            ->addColumn('sales_price', function ($product) {
                return " &#8377; ". $product->sales_price;
            })
            ->addColumn('purchase_price', function ($product) {
                return " &#8377; ". $product->purchase_price;
            })
            ->addColumn('action', function ($product) {
                $url = url('admin/product/view/'.$product->external_id);
				$html = '<form action="'.route('product.destroy', $product->external_id).'" class="d-flex" method="POST">';
                $html .= '<a href="'.$url.'" class="btn btn-link"><i class="flaticon-eye text-primary text-hover-primary" data-toggle="tooltip" title="View Details"></i></a>';
				if(\Entrust::can('product-update') && !Helper::allowViewOnly($product->distributor_id))
				$html .= '<a href="'.route('product.edit', $product->external_id).'" class="btn btn-link"  data-toggle="tooltip" title="Edit Product/Service"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';
				$html .= '<input type="hidden" name="_method" value="DELETE">';
				if(\Entrust::can('product-delete'))
				// $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-product" data-toggle="tooltip" title="Delete Product"><i class="flaticon2-trash text-danger text-hover-warning"></i></button>';
                $html .= '<input type="hidden" class="product_id" value="'.$product->external_id.'">';
				$html .= csrf_field();
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['distributor' ,'thumbnail', 'namelink', 'description', 'category', 'type', 'sales_price', 'purchase_price', 'action'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $external_id
     * @return \Illuminate\Http\Response
     */
    public function show($external_id)
    {
        if(!auth()->user()->can('product-view')) {
            return redirect('/');
        }

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // system user
            $data['product'] = $this->findByExternalId($external_id);
        } else {
            $data['product'] = Product::where('external_id', $external_id)->where('distributor_id', $distributor_id)->firstOrFail();
        }

        $data['is_system_user'] = Helper::getDistributorId();
        $data['distributor'] = Distributor::findOrFail($data['product']->distributor_id); // current record distributor name (for admin)

        return view('products.show')->with($data);
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
        if($distributor_id == 0) { // system user
            $product = $this->findByExternalId($external_id);
        } else {
            $product = Product::where('external_id', $external_id)->where('distributor_id', $distributor_id)->firstOrFail();
        }

        $data['product'] = $product;
        $data['units'] = Unit::all()->toArray();
        $data['selected_categories'] = $product->categories->pluck('name', 'id');
        $data['selected_unit'] = Unit::where('id', $product->unit_id)->pluck('name', 'id');
        $data['selected_package'] = Package::where('id', $product->package_id)->pluck('name', 'id');

        $data['is_system_user'] = Helper::getDistributorId();
        $data['distributor'] = Distributor::findOrFail($data['product']->distributor_id); // current record distributor name (for admin)
        $data['packages'] = Package::where('distributor_id', $data['is_system_user'])->pluck('name', 'id')->toArray();

        $data['package_products'] = $product->packageProducts;

        return view('products.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, $external_id)
    {
        $user_id = Auth::id();
        $product = $this->findByExternalId($external_id);


        if(Helper::allowViewOnly($product->distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        if($request->hasFile('thumbnail')) {

            $product_name = strtolower(str_replace(' ', '_',$request->name));
            $thumbnail_name = $product_name ."_". time() . ".". $request->thumbnail->extension();
            $path = 'storage/assets/products/images/thumbnail/';
            $request->thumbnail->move(public_path($path), $thumbnail_name);

            if($request->old_thumbnail != "" && file_exists(public_path($request->old_thumbnail))) {
                unlink(public_path($request->old_thumbnail));
            }
            $thumbnail = $path . $thumbnail_name;

        } else {
            $thumbnail = $request->old_thumbnail;
        }

        if($request->hasFile('other_document')) {

            $product_name = strtolower(str_replace(' ', '_',$request->name));
            $other_document_name = $product_name ."_". time() . ".". $request->other_document->extension();
            $path = 'storage/assets/products/documents/';
            $request->other_document->move(public_path($path), $other_document_name);

            if($request->old_other_document != "" && file_exists(public_path($request->old_other_document))) {
                unlink(public_path($request->old_other_document));
            }
            $other_document = $path . $other_document_name;

        } else {
            $other_document = $request->old_other_document;
        }

        $sku_code = "";
        if($request->type != "1") {
            $sku_code = $request->sku_code;
        }

        $product->fill([
            'name' => $request->name,
            'sales_price' => $request->sales_price,
            'purchase_price' => $request->purchase_price,
            // 'category_id' => $request->category_id,
            'thumbnail' => $thumbnail,
            'other_document' => $other_document,
            'type' => $request->type,
            'sku_code' => $sku_code,
            'unit_id' => $request->unit_id,
            // 'package_id' => $request->package_id,
            'package_id' => 0,
            'description' => $request->description,
            'updated_by' => $user_id,
            'sgst' => $request->sgst,
            'cgst' => $request->cgst,
            'igst' => $request->igst,
            'expiry_reminder' => $request->expiry_reminder ?? 0,
            'is_default' => $request->is_default_service ? 1 : 0,
            'reorder_qty' => $request->reorder_qty,
        ])->save();
        $product->categories()->sync($request->categories);

        if($product->type == 2) {
            $product->packageProducts()->sync($request->product_data);
        }

        Session()->flash('success', __('Product/Service successfully updated'));
        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxProductDelete(Request $request)
    {
        $external_id = $request->external_id;
        $product = $this->findByExternalId($external_id);
        $product->delete();

        Session()->flash('success', __('Product successfully deleted!'));
        return response()->json([
            'status' => true,
            'message' => "Product deleted successfully!"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($external_id)
    {
        if(!auth()->user()->can('product-delete')) {
            return redirect('/');
        }

        $product = $this->findByExternalId($external_id);
        $product->delete();

        Session()->flash('success', __('Product successfully deleted'));
        return redirect()->route('product.index');
    }

    public function findByExternalId($external_id)
    {
        return Product::where('external_id', $external_id)->firstOrFail();
    }

    public function findById(Request $request)
    {
        $id = $request->id;
        return Product::find($id);
    }

    /**
     *  @return mixed
     *  $products
     */
    public function getProductByName(Request $request)
    {
        $name = $request->get('name');

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { //is system user
            $distributor_id = $request->get('distributor_id');
        }

        $products = Product::where('name', 'like', "%{$name}%");

        if(isset($request->package)) {
            $products->where('type', '!=', 2);
        }

        $products = $products->where('distributor_id', $distributor_id)->get();

        return response()->json($products);
    }

    /**
     *  @return mixed
     *  $products
     */
    public function getServicesByName(Request $request)
    {
        $name = $request->get('q');

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { //is system user
            $distributor_id = $request->get('distributor_id');
        }

        $services = Product::where('name', 'like', "%{$name}%")->where('distributor_id', $distributor_id)->where('type', 1)->get();

        return response()->json($services);
    }

    /**
     *  @return mixed
     *  $products
     */
    public function getProductTypeByName(Request $request)
    {
        $name = $request->get('name');

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { //is system user
            $distributor_id = $request->get('distributor_id');
        }

        $products = Product::where('name', 'like', "%{$name}%")->where('distributor_id', $distributor_id)->where('type', 0)->get();

        return response()->json($products);
    }

    /**
     *  @return mixed
     *  $products
     */
    public function getProductByCategory(Request $request)
    {
        $name = $request->get('name');
        $category_id = $request->get('category_id');
        $sub_category_id = $request->get('sub_category_id');

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { //is system user
            $distributor_id = $request->get('distributor_id');
        }

        if($sub_category_id !== "") {
            $products = Product::with('categories')->where('name', 'like', "%{$name}%")->whereHas('categories', function ($query) use ($sub_category_id) {
                $query->where('category_id', $sub_category_id);
            })->get();
        } else {

            $sub_categories = Category::where('parent_id', $category_id)->pluck('id');

            $products = Product::with('categories')->where('name', 'like', "%{$name}%")->whereHas('categories', function ($query) use ($sub_categories) {
                $query->whereIn('category_id', $sub_categories);
            })->get();
        }

        return response()->json($products);
    }


    /**
     *  Manage Employee wise product commission
     *
     */

    public function viewEmployeeCommission(Request $request, $user_external_id)
    {
        $is_system_user = Helper::is_system_user();
        $distributor_id = Helper::getDistributorId();
        $data['is_system_user'] = $is_system_user;
        $data['is_profile_view'] = $request->profile == true ? 1 : 0;
        $data['distributor_id'] = $request->distributor != null ? $request->distributor : false;

        if($is_system_user) {
            $data['user'] = User::where('external_id', $user_external_id)->firstOrFail();
        } else{
            $data['user'] = User::where('external_id', $user_external_id)->firstOrFail();
        }

        $user_name = $data['user']->first_name ." ". $data['user']->last_name;

        $data['page_title'] = "Product/Service Commission for $user_name";

        return view('products.commission')->with($data);
    }

    public function allCommissionData(Request $request)
    {
        $is_system_user = Helper::is_system_user();
        $distributor_id = Helper::getDistributorId();
        $data['is_system_user'] = $is_system_user;

        $user_id = $request->user_id;
        $is_profile_view = $request->is_profile_view;
        $user = User::find($user_id);

        $product = Product::orderBy('id', 'desc');
        $product->where('distributor_id', $user->distributor_id);
        $product = $product->get();

        return Datatables::of($product)
            ->addColumn('namelink', function ($product) {

                if(Helper::is_distributor_user()) {
                    return $product->name;
                } else {
                    $url = url('admin/product/view/'.$product->external_id);
                    return '<a href="'.$url.'" target="_blank">' . $product->name . '</a>';
                }
            })
            ->addColumn('sku_code', function ($product) {
                return $product->sku_code;
            })
            ->addColumn('gst', function ($product) {
                return "SGST : ". $product->sgst . "% <br> CGST : ". $product->cgst . "% <br> IGST : ". $product->igst .'%';
            })
            ->addColumn('sales_price', function ($product) {
                return " &#8377; ". $product->sales_price;
            })
            ->addColumn('commission', function ($product) use ($user, $is_profile_view) {

                $commission = UserProductCommission::where('product_id', $product->id)->where('user_id', $user->id)->first();

                if(empty($commission)) {
                    if($product->type == 0) { // product
                        $commission = $user->product_commission;
                    } else {
                        $commission = $user->service_commission;
                    }
                } else {
                    $commission = $commission->commission;
                }

                $html = "";

                if($is_profile_view == 1) {
                    $html .= "$commission%";
                } else {
                    $html .= '<input type="hidden" class="employee_id_'.$product->id.'" id="employee_id_'.$product->id.'" value="'.$user->id.'">';

                    $html .= '<div class="input-group" style="width: 85px;">';
                    $html .= '<input type="text" onChange="updateCommission('.$product->id.')" class="form-control form-control-solid commission_input_'. $product->id .' text-right" id="commission_input_'. $product->id .'" placeholder="Commission in %" value="'.$commission.'" min="0" max="100" step="0.1">';
                    $html .= '<div class="input-group-append"><span class="input-group-text">%</span></div>';
                    $html .= '</div>';
                }

                return $html;
            })
            ->addColumn('commission_inter_state', function ($product) use ($user) {

                $commission_entry = UserProductCommission::where('product_id', $product->id)->where('user_id', $user->id)->first();

                $inter_state = Helper::getCalucatedGST($product->sales_price, $product->sgst, $product->cgst, 0);
                $product_original_price = $product->sales_price - $inter_state['total_gst_amount'];

                if(empty($commission_entry)) {

                    if($product->type == 0) {
                        $commission_inter_state = $product_original_price * $user->product_commission / 100;
                    } else {
                        $commission_inter_state = $product_original_price * $user->service_commission / 100;
                    }

                } else {
                    $commission_inter_state = $product_original_price * $commission_entry->commission / 100;
                }

                $commission_inter_state = '₹'. Helper::decimalNumber($commission_inter_state );

                return '<span id="commission_inter_state_'. $product->id .'"" class="commission_inter_state_'. $product->id .'">'.$commission_inter_state.'</span>';
            })
            ->addColumn('commission_other_state', function ($product) use ($user) {

                $commission_entry = UserProductCommission::where('product_id', $product->id)->where('user_id', $user->id)->first();

                $other_state = Helper::getCalucatedGST($product->sales_price, 0, 0, $product->igst);
                $product_original_price = $product->sales_price - $other_state['total_gst_amount'];

                if(empty($commission_entry)) {

                    if($product->type == 0) {
                        $commission_other_state = $product_original_price * $user->product_commission / 100;
                    } else {
                        $commission_other_state = $product_original_price * $user->service_commission / 100;
                    }

                } else {
                    $commission_other_state = $product_original_price * $commission_entry->commission / 100;
                }

                $commission_other_state = '₹'. Helper::decimalNumber($commission_other_state );

                return '<span id="commission_other_state_'. $product->id .'" class="commission_other_state_'. $product->id .'">'.$commission_other_state.'</span>';
            })
            ->rawColumns(['namelink', 'sku_code', 'gst', 'sales_price', 'commission', 'commission_inter_state', 'commission_other_state'])
            ->make(true);
    }

    // Update Commission
    public function updateCommission(Request $request)
    {
        $auth_id = Auth::user()->id;

        $employee_id = $request->employee_id;
        $product_id = $request->product_id;
        $commission = $request->commission;

        $employee = User::find($employee_id);

        $distributor_id = $employee->distributor_id;

        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }

        $product = Product::find($product_id);

        $commission_entry = UserProductCommission::where('product_id', $product_id)->where('user_id', $employee_id)->first();

        if(!empty($commission_entry)) {
            $commission_entry->commission = $commission;
            $commission_entry->updated_by = $auth_id;
        } else {

            $commission_entry = new UserProductCommission();
            $commission_entry->product_id = $product_id;
            $commission_entry->user_id = $employee_id;
            $commission_entry->commission = $commission;
            $commission_entry->created_by = $auth_id;
            $commission_entry->updated_by = $auth_id;
        }

        $commission_entry->save();

        $inter_state = Helper::getCalucatedGST($product->sales_price, $product->sgst, $product->cgst, 0);
        $other_state = Helper::getCalucatedGST($product->sales_price, 0, 0, $product->igst);

        $product_original_price = $product->sales_price - $inter_state['total_gst_amount'];
        $commission_inter_state = $product_original_price * $commission / 100;

        $product_original_price = $product->sales_price - $other_state['total_gst_amount'];
        $commission_other_state = $product_original_price * $commission / 100;

        $result = [
            'status' => true,
            'message' => 'Product/Service commission successfully updated!',
            'commission_inter_state' => '₹'. Helper::decimalNumber($commission_inter_state),
            'commission_other_state' => '₹'. Helper::decimalNumber($commission_other_state),
        ];

        return response()->json($result);
    }

    public function resetCommission(Request $request)
    {
        $user_id = $request->user_id;

        $res = UserProductCommission::where('user_id', $user_id)->delete();
        return redirect()->back()->with('success', "Commission succefully reset!");
    }
}
