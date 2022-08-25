<?php

namespace App\Http\Controllers;
 
use Carbon\Carbon;
use Config; 
use Datatables;
use App\Models\Client;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Setting; 
use App\Models\User; 
use Ramsey\Uuid\Uuid;
use App\Models\Contact; 

use Illuminate\Support\Facades\Auth; 

use App\Http\Requests\Plan\StorePlanRequest;
use App\Http\Requests\Plan\UpdatePlanRequest;

use App\Helpers\Helper;
use App\Models\Distributor; 
use App\Models\Plan; 

class PlansController extends Controller
{
        
    public function __construct()
    {
        $this->middleware('permission:plan-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:plan-create', ['only' => ['create','store']]);
		$this->middleware('permission:plan-update', ['only' => ['edit','update']]);
		$this->middleware('permission:plan-delete', ['only' => ['destroy']]);  
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        return view('plans.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {  
        return view('plans.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePlanRequest $request)
    { 
        $plan = Plan::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,   
            'price' => $request->price,  
            'sgst' => $request->sgst,  
            'cgst' => $request->cgst,  
            'igst' => $request->igst,  
            'no_of_users' => $request->no_of_users,  
            'no_of_branches' => $request->no_of_branches,  
            'no_of_email' => $request->no_of_email,  
            'no_of_sms' => $request->no_of_sms,  
            'duration_months' => $request->duration_months,  
            'description' => $request->description,  
            'created_by' => Auth::id(),  
        ]);
  
        Session()->flash('success', __('Plan successfully added'));
        return redirect()->route('plans.index');
    }

    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {   
        $plan = Plan::orderBy('id', 'desc')->get(); 
    
        return Datatables::of($plan)  
            ->addColumn('name', function ($plan) {  
                return $plan->name;
            })   
            ->addColumn('duration_months', function ($plan) {  
                return $plan->duration_months;
            })   
            ->addColumn('no_of_users', function ($plan) {  
                return $plan->no_of_users;
            })   
            ->addColumn('no_of_branches', function ($plan) {  
                return $plan->no_of_branches;
            })   
            ->addColumn('no_of_sms', function ($plan) {  
                return $plan->no_of_sms;
            })   
            ->addColumn('no_of_email', function ($plan) {  
                return $plan->no_of_email;
            })   
            ->addColumn('price', function ($plan) {  
                return $plan->price;
            })   
            ->addColumn('action', function ($plan) {  
				return '<a href="'.route('plans.edit', $plan->external_id).'" class="btn btn-link"  data-toggle="tooltip" title="Edit Plan"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';  
            })
            ->rawColumns(['name','validity','no_of_users','no_of_branches','no_of_sms','no_of_email','price','action'])
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
        $data['plan'] = $this->findByExternalId($external_id);
        return view('plans.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $external_id)
    {
        $user_id = Auth::id();  
        $plan = $this->findByExternalId($external_id); 
          
        $plan->fill([
            'name' => $request->name,   
            'price' => $request->price,  
            'sgst' => $request->sgst,  
            'cgst' => $request->cgst,  
            'igst' => $request->igst,  
            'no_of_users' => $request->no_of_users,  
            'no_of_branches' => $request->no_of_branches,  
            'no_of_email' => $request->no_of_email,  
            'no_of_sms' => $request->no_of_sms,  
            'duration_months' => $request->duration_months,  
            'description' => $request->description,    
            'updated_by' => Auth::id(),  
        ])->save(); 

        Session()->flash('success', __('Plan successfully updated'));
        return redirect()->route('plans.index'); 
    } 

    public function findByExternalId($external_id)
    {
        return Plan::where('external_id', $external_id)->firstOrFail();
    }

    /**
     *  @return mixed
     *  $products
     */
    public function getPlanByName(Request $request)
    {
        $name = $request->get('name'); 

        $plans = Plan::where('name', 'like', "%{$name}%")->get();
 
        return response()->json($plans);
    }
}
