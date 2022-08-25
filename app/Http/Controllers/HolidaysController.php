<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Datatables;

use App\Models\User;  
use App\Models\Holiday;

use App\Http\Requests\Holiday\StoreHolidayRequest;
use App\Http\Requests\Holiday\UpdateHolidayRequest;

use App\Helpers\Helper;
use App\Models\Distributor;

class HolidaysController extends Controller
{
 
    public function __construct()
    {
        $this->middleware('permission:holiday-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:holiday-create', ['only' => ['create','store']]);
		$this->middleware('permission:holiday-update', ['only' => ['edit','update']]); 
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

        return view('holidays.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
        $data['is_system_user'] = Helper::getDistributorId(); 
        return view('holidays.create')->with($data);
    }

    // Remote name validation
    public function checkName(Request $request)
    {  
        $id = $request->id;
        $name = $request->name;

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;
        } 

        $holiday = Holiday::where('name', $name)->where('distributor_id', $distributor_id)->first();
  
        if($holiday !== null) { 
            if($id == $holiday->id) { 
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
    public function store(StoreHolidayRequest $request)
    {
        $user_id = Auth::id();

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;
        } 
        
        if(Helper::allowViewOnly($distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        $branch = Holiday::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,    
            'date' => $request->date,    
            'created_by' => $user_id, 
            'updated_by' => $user_id, 
            'distributor_id' => $distributor_id,
        ]);

        Session()->flash('success', __('Holiday successfully added'));
        return redirect()->route('holidays.index');
    }

  
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {   
        $holiday = Holiday::with(['getDistributor']);   
        $distributor_id = Helper::getDistributorId(); 
        
        if($distributor_id != 0) { // Check if distributor
            $holiday->where('distributor_id', $distributor_id);
        }  
        $holiday = $holiday->orderBy('id', 'desc')->get();
  
        return Datatables::of($holiday)
            ->addColumn('distributor', function ($holiday) {
                return  $holiday->getDistributor->name ?? "";
            }) 
            ->addColumn('name', function ($holiday) {
                return  $holiday->name;
            })  
            ->addColumn('date', function ($holiday) {
                return  date('d-m-Y',strtotime($holiday->date));
            })  
            ->addColumn('action', function ($holiday) {
				$html = '<form action="'.route('holidays.destroy', $holiday->external_id).'" method="POST">';
				if(\Entrust::can('holiday-update') && !Helper::allowViewOnly($holiday->distributor_id)) 
				$html .= '<a href="'.route('holidays.edit', $holiday->external_id).'" class="btn btn-link" data-toggle="tooltip" title="Edit holiday"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';
				$html .= '<input type="hidden" name="_method" value="DELETE">';
				if(\Entrust::can('holiday-delete'))
                // $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-holiday" data-toggle="tooltip" title="Delete holiday"><i class="flaticon2-trash text-danger text-hover-warning"></i></button>';
                $html .= '<input type="hidden" class="holiday_id" value="'.$holiday->external_id.'">'; 
				$html .= csrf_field();
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['distributor', 'name', 'date', 'action'])
            ->make(true);
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
        $distributor_id = Helper::getDistributorId();
        
        // Only system user can access others data
        if($distributor_id == 0) { 
            $data['holiday'] = $this->findByExternalId($external_id); 
        } else {   
            $data['holiday'] = Holiday::where('external_id', $external_id)->where('distributor_id', $distributor_id)->firstOrFail(); 
        } 

        $data['is_system_user'] = $distributor_id;
        $data['distributor'] = Distributor::findOrFail($data['holiday']->distributor_id); // current record distributor name (for admin)

        return view('holidays.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHolidayRequest $request, $external_id)
    {  
        $user_id = Auth::id();  
        $holiday = $this->findByExternalId($external_id);

        if(Helper::allowViewOnly($holiday->distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }
 
        $holiday->fill([ 
            'name' => $request->name,    
            'date' => $request->date,    
            'updated_by' => $user_id,
        ])->save();

        Session()->flash('success', __('Holiday successfully updated!'));
        return redirect()->route('holidays.index'); 
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkHolidayDelete(Request $request) 
    {
        $external_id = $request->external_id;  

        return response()->json([
            'status' => true, 
        ]);
 
        // $unit = $this->findByExternalId($external_id); 
        // $unit_count = Product::where('unit_id', $unit->id)->count(); 

        // if($unit_count === 0) {
        //     return response()->json([
        //         'status' => true,
        //     ]);
        // } else {
        //     return response()->json([
        //         'status' => false,
        //         'message' => "Sorry this unit is in use!",
        //     ]);
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $idind
     * @return \Illuminate\Http\Response
     */
    public function ajaxHolidayDelete(Request $request)
    {
        $external_id = $request->external_id;  
        $holiday = $this->findByExternalId($external_id);
        $holiday->delete();

        Session()->flash('success', __('Holiday successfully deleted!'));
        return response()->json([
            'status' => true,
            'message' => "Holiday deleted successfully!"
        ]);
    }
    
    public function findByExternalId($external_id)
    {
        return Holiday::where('external_id', $external_id)->firstOrFail();
    }
}
