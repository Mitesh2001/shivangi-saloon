<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Datatables;

use App\Models\User;
use App\Models\Unit;
use App\Models\Product;

use App\Http\Requests\Unit\StoreUnitRequest;
use App\Http\Requests\Unit\UpdateUnitRequest;


class UnitsController extends Controller
{
 
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            if($this->user->distributor_id !== 0) {
                return abort(403);
            }
    
            return $next($request);
        });
        
        $this->middleware('permission:unit-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:unit-create', ['only' => ['create','store']]);
		$this->middleware('permission:unit-update', ['only' => ['edit','update']]);
		$this->middleware('permission:unit-delete', ['only' => ['destroy']]);  
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('unit.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['users'] = User::all();  
        return view('unit.create')->with($data);
    }

    public function checkName(Request $request)
    {  
        $id = $request->id;
        $name = $request->name;
        $unit = Unit::where('name', $name)->first();

        if($unit !== null) { 
            if($id == $unit->id) { 
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
    public function store(StoreUnitRequest $request)
    {
        $user_id = Auth::id();

        $unit = Unit::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,    
            'created_by' => $user_id, 
            'updated_by' => $user_id, 
        ]);

        Session()->flash('success', __('Unit successfully added'));
        return redirect()->route('unit.index');
    }
  
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {   
        $unit = Unit::all();  
  
        return Datatables::of($unit)
            ->addColumn('name', function ($unit) {
                return  $unit->name;
            })  
            ->addColumn('action', function ($unit) {
				$html = '<form action="'.route('unit.destroy', $unit->external_id).'" method="POST">';
				if(\Entrust::can('unit-update')) 
				$html .= '<a href="'.route('unit.edit', $unit->external_id).'" class="btn btn-link" data-toggle="tooltip" title="Edit Unit"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';
				$html .= '<input type="hidden" name="_method" value="DELETE">';
				if(\Entrust::can('unit-delete'))
                // $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-unit" data-toggle="tooltip" title="Delete Unit"><i class="flaticon2-trash text-danger text-hover-warning"></i></button>';
                $html .= '<input type="hidden" class="unit_id" value="'.$unit->external_id.'">'; 
				$html .= csrf_field();
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['name', 'action'])
            ->make(true);
    }
 

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($external_id)
    { 
        $data['unit'] = $this->findByExternalId($external_id);

        return view('unit.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUnitRequest $request, $external_id)
    {  
        $user_id = Auth::id();  
        $unit = $this->findByExternalId($external_id);
 
        $unit->fill([ 
            'name' => $request->name,    
            'updated_by' => $user_id,
        ])->save();

        Session()->flash('success', __('Unit successfully updated!'));
        return redirect()->route('unit.index'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkUnitDelete(Request $request) 
    {
        $external_id = $request->external_id;  
 
        $unit = $this->findByExternalId($external_id); 
        $unit_count = Product::where('unit_id', $unit->id)->count(); 

        if($unit_count === 0) {
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Sorry this unit is in use!",
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxUnitDelete(Request $request)
    {
        $external_id = $request->external_id;  
        $unit = $this->findByExternalId($external_id);
        $unit->delete();

        Session()->flash('success', __('Unit successfully deleted!'));
        return response()->json([
            'status' => true,
            'message' => "Unit deleted successfully!"
        ]);
    }

    public function findByExternalId($external_id)
    {
        return Unit::where('external_id', $external_id)->firstOrFail();
    }
}
