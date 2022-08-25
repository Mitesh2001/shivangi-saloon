<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use JWTAuth;
use Ramsey\Uuid\Uuid;
use App\Helpers\Helper;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Package;
use App\Models\User;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
		$user = JWTAuth::parseToken()->authenticate();
		if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }
		$search_text = $request->search_text;
		$paginate = $request->paginate ?? 0;
		$distributor_id = $user->distributor_id;

        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }
		$products = Product::with(['categories','packageProducts','unit','package'])->where('distributor_id','=',$distributor_id);//->get();
		if(!empty($search_text))
		$products->where(function ($q) use ($search_text) {
			$q->where('name', 'LIKE', "%" . $search_text . "%");
			$q->orWhere('description', 'LIKE', "%" . $search_text . "%");
			$q->orWhere('sku_code', 'LIKE', "%" . $search_text . "%");
		});
		$products->orderBy('name', 'asc');
		if($paginate == 1) {
            $data = $products->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $products->get();
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

	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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

		$product = $this->findByExternalId($id);
		return response()->json([
            'status' => 'SUCCESS',
            'data' => $product
        ]);
    }

	/**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
		$validator = Validator::make($request->all(), [
            'name' => ['required'],
        ], [
            'name' => 'Please enter name',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
		$user_id = $user->id;
		$thumbnail_name = '';
		if(!empty($request->thumbnail)) {
            $path = 'assets/products/images/thumbnail/';
            $name  = strtolower(str_replace(' ', '_',$request->name));
            $thumbnail_name = Helper::createImageFromBase64($request->thumbnail, $name, $path);
        }
		$other_document_name = '';
		if(!empty($request->thumbnail)) {
            $path = 'assets/products/documents/';
            $name  = strtolower(str_replace(' ', '_',$request->name));
            $other_document_name = Helper::createImageFromBase64($request->other_document, $name, $path);
        }

		$sku_code = "";
        if($request->type != "1") {
            $sku_code = $request->sku_code;
        }

		$product = Product::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'description' => $request->description,
            'sales_price' => $request->sales_price,
            'purchase_price' => $request->purchase_price,
            'thumbnail' => $thumbnail_name,
            'other_document' => $other_document_name,
            'type' => $request->type,
            'sku_code' => $sku_code,
            'unit_id' => $request->unit_id,
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

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $product,
            'message' => 'Product successfully added!'
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
		$product = $this->findByExternalId($id);

        $validator = Validator::make($request->all(), [
            'name' => ['required'],
        ], [
            'name' => 'Please enter name',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

		$thumbnail_name = $product->thumbnail;
		if(!empty($request->thumbnail)) {
            $path = 'assets/products/images/thumbnail/';
            $name  = strtolower(str_replace(' ', '_',$request->name));
            $thumbnail_name = Helper::createImageFromBase64($request->thumbnail, $name, $path);
        }
		$other_document_name = $product->other_document;
		if(!empty($request->thumbnail)) {
            $path = 'assets/products/documents/';
            $name  = strtolower(str_replace(' ', '_',$request->name));
            $other_document_name = Helper::createImageFromBase64($request->other_document, $name, $path);
        }

		$sku_code = $product->sku_code;
        if($request->type != "1") {
            $sku_code = $request->sku_code;
        }

		$update_data = [
            'name' => $request->name,
            'description' => $request->description,
            'sales_price' => $request->sales_price,
            'purchase_price' => $request->purchase_price,
            'thumbnail' => $thumbnail_name,
            'other_document' => $other_document_name,
            'type' => $request->type,
            'sku_code' => $sku_code,
            'unit_id' => $request->unit_id,
            'package_id' => 0,
            'updated_by' => $user_id,
            'distributor_id' => $distributor_id,
            'sgst' => $request->sgst,
            'cgst' => $request->cgst,
            'igst' => $request->igst,
            'expiry_reminder' => $request->expiry_reminder ?? 0,
            'is_default' => $request->is_default_service ? 1 : 0,
            'reorder_qty' => $request->reorder_qty,
        ];

		$product->fill($update_data)->save();

		$product->categories()->sync($request->categories);

        if($product->type == 2) {
            $product->packageProducts()->sync($request->product_data);
        }

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $product,
            'message' => 'Product successfully updated!'
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
		$product = $this->findByExternalId($id);

		$product->delete();

		return response()->json([
			'status' => 'SUCCESS',
			'message' => 'Product successfully deleted!'
		]);
    }

	public function getProductByName(Request $request)
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
		$paginate = $request->paginate ?? 0;
		$name = $request->name;

		$products = Product::with(['categories','packageProducts','unit','package'])->where('distributor_id', $distributor_id);
		if(!empty($name)) {
            $products->where('name', 'like', "%{$name}%");
        }
		if(isset($request->package)) {
            $products->where('type', '=', 0);
        }

		if($paginate == 1) {
            $data = $products->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $products->get();
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

	public function getServicesByName(Request $request)
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
		$paginate = $request->paginate ?? 0;
		$name = $request->name;

		$products = Product::with(['categories','packageProducts','unit','package'])->where('distributor_id', $distributor_id);
		if(!empty($name)) {
            $products->where('name', 'like', "%{$name}%");
        }

        $products->where('type', 1);

		if($paginate == 1) {
            $data = $products->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $products->get();
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
	
	public function getPackagesByName(Request $request)
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
		$paginate = $request->paginate ?? 0;
		$name = $request->name;

		$products = Product::with(['categories','packageProducts','unit','package'])->where('distributor_id', $distributor_id);
		if(!empty($name)) {
            $products->where('name', 'like', "%{$name}%");
        }

        $products->where('type', 2);

		if($paginate == 1) {
            $data = $products->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $products->get();
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
	
	public function getProductByCategory(Request $request)
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
		$paginate = $request->paginate ?? 0;
		$name = $request->name;
		$category_id = $request->get('category_id');
        $sub_category_id = $request->get('sub_category_id');
		
		$products = Product::with(['categories','packageProducts','unit','package'])->where('distributor_id', $distributor_id);
		if(!empty($name)) {
            $products->where('name', 'like', "%{$name}%");
        }
		if(!empty($sub_category_id)) {
			$products->whereHas('categories', function ($query) use ($sub_category_id) {
                $query->where('category_id', $sub_category_id);
            });
		}
		if(!empty($category_id)){
			$sub_categories = Category::where('parent_id', $category_id)->pluck('id');
			$products->whereHas('categories', function ($query) use ($sub_categories) {
                $query->whereIn('category_id', $sub_categories);
            });
		}
		
		if($paginate == 1) {
            $data = $products->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $products->get();
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

	public function findByExternalId($external_id)
    {
        return Product::with(['categories','packageProducts','unit','package'])->where('external_id', $external_id)->first();
    }
}
