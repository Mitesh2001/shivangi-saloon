<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use JWTAuth;
use Ramsey\Uuid\Uuid;

use App\Models\Category;
use App\Models\Product;
use App\Helpers\Helper;

class CategoriesController extends Controller
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
		$distributor_id = $user->distributor_id;
		$search_text = $request->search_text; 
		$paginate = $request->paginate ?? 0; 

        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }

        $categories = Category::with('parent');//->where('distributor_id','=',$distributor_id);
		if(!empty($search_text))
		$categories->where('name', 'LIKE', "%" . $search_text . "%");
		if($paginate == 1) {
            $data = $categories->paginate();
            $count = count($data);
        } else { 
            $data = [];
            $data['data'] = $categories->get();
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
		$category = $this->findByExternalId($id);
        return response()->json([
            'status' => 'SUCCESS',
            'data' => $category
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
            'name' => ['required', Rule::unique('categories')->where(function ($query) use ($distributor_id) {
                return $query->where('distributor_id', $distributor_id);
            })],
        ], [
            'name.required' => "Please enter category name!",
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
		$user_id = $user->id;
		$category = Category::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'created_by' => $user_id,
            'updated_by' => $user_id,
           // 'distributor_id' => $distributor_id,
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $category,
            'message' => 'Category successfully added!'
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
		$category = $this->findByExternalId($id);

        $validator = Validator::make($request->all(), [
			'name' => ['required', Rule::unique('categories')->where(function ($query) use ($distributor_id, $category) {
				return $query->where('distributor_id', $distributor_id)->where('id', '!=', $category->id);
			})],
        ], [
            'name.required' => "Please enter category name!",
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

		$category->fill([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'updated_by' => $user_id,
        ])->save();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $category,
            'message' => 'Category successfully updated!'
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
		$category = $this->findByExternalId($id);

		$category->delete();
		return response()->json([
			'status' => 'SUCCESS',
			'message' => 'Category successfully deleted!'
		]);

    }

	public function findByExternalId($external_id)
    {
        return Category::where('external_id', $external_id)->first();
    }
}
