<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use Datatables;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;


class CategoriesController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:category-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:category-create', ['only' => ['create','store']]);
		$this->middleware('permission:category-update', ['only' => ['edit','update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('categories.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['all_categories'] = Category::pluck('name', 'id')->toArray();

        return view('categories.create')->with($data);
    }

        /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {
        $categories = Category::orderBy('id', 'desc')->get();

        return Datatables::of($categories)
            ->addColumn('name', function ($categories) {
                return  $categories->name;
            })
            ->addColumn('parent_category', function ($categories) {
                return  $categories->parent->name ?? "" ;
            })
            ->addColumn('action', function ($categories) {
				$html = '<form action="'.route('category.destroy', $categories->external_id).'" method="POST">';
				if(\Entrust::can('category-update'))
				$html .= '<a href="'.route('category.edit', $categories->external_id).'" class="btn btn-link"  data-toggle="tooltip" title="Edit Category"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';
				$html .= '<input type="hidden" name="_method" value="DELETE">';
				if(\Entrust::can('category-delete'))
                // $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-category" data-toggle="tooltip" title="Delete Category"><i class="flaticon2-trash text-danger text-hover-warning"></i></button>';
                $html .= '<input type="hidden" class="category_id" value="'.$categories->external_id.'">';
				$html .= csrf_field();
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['name', 'parent_category', 'action'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $user_id = Auth::id();

        $category = Category::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'created_by' => $user_id,
            'updated_by' => $user_id,
        ]);

        Session()->flash('success', __('Category successfully added'));
        return redirect()->route('category.index');
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
        $category = $this->findByExternalId($external_id);
        $selected_category = Category::where('id', $category->parent_id)->pluck('name', 'id');

        $data['category'] = $category;
        $data['selected_category'] = $selected_category;

        return view('categories.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, $external_id)
    {
        $user_id = Auth::id();
        $category = $this->findByExternalId($external_id);

        $category->fill([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'updated_by' => $user_id,
        ])->save();

        Session()->flash('success', __('Category successfully updated'));
        return redirect()->route('category.index');
    }

    public function checkCategoryDelete(Request $request)
    {
        $external_id = $request->external_id;

        $category = $this->findByExternalId($external_id);

        $category_children = Category::where('parent_id', $category->id)->count();
        // $category_products = Product::where('category_id', $category->id)->count();

        $category_products = Category::where('id', $category->id)->first();
        $category_products = $category_products->products->count();

        if($category_children === 0 && $category_products === 0) {
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Sorry this category is in use!",
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxCategoryDelete(Request $request)
    {
        $external_id = $request->external_id;
        $category = $this->findByExternalId($external_id);
        $category->delete();

        Session()->flash('success', __('Category successfully deleted!'));
        return response()->json([
            'status' => true,
            'message' => "Category deleted successfully!"
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
        $category = $this->findByExternalId($external_id);
        $category->delete();

        Session()->flash('success', __('Category successfully deleted'));
        return redirect()->route('category.index');
    }

    public function findByExternalId($external_id)
    {
        return Category::where('external_id', $external_id)->firstOrFail();
    }

    /**
     *  @return mixed
     *  $industries
     */
    public function getCategoryByName(Request $request)
    {
        $name = $request->get('q');
        $categories = Category::where('name', 'like', "%{$name}%")->get();

        return response()->json($categories);
    }

    public function getSubCategoryByName(Request $request)
    {
        $name = $request->get('name');
        $category_id = $request->get('category_id');

        $categories = Category::where('name', 'like', "%{$name}%")->where('parent_id', $category_id)->get();
        return response()->json($categories);
    }
}
