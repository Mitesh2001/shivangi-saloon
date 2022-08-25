<?php

namespace App\Http\Controllers;

use DB;
use Carbon;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Datatables;
use App\Models\Country;
use App\Models\State;
use App\Helpers\Helper;
use App\Models\Distributor;

use App\Models\User;
use App\Models\Branch;
use App\Models\Order;

use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;


class BranchController extends Controller
{
        
    public function __construct()
    {
        $this->middleware('permission:branch-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:branch-create', ['only' => ['create','store']]);
		$this->middleware('permission:branch-update', ['only' => ['edit','update']]);
		$this->middleware('permission:branch-delete', ['only' => ['destroy']]);  
    }
 
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data['is_system_user'] = Helper::is_system_user();
        $data['is_distributor_user'] = Helper::is_distributor_user();
        $data['back_url'] = false;
        $data['distributor_filter'] = 1; // True while fetiching all records
        $data['distributor_title'] = false; 
        $data['distributor_id'] = 0;
        $data['distributor'] = false;

        if(Helper::is_system_user() || Helper::is_distributor_user()) { 
            $external_id = $request->get('distributor') ?? ""; 
  
            // Distributor & system user can view users according to distributor id
            if(!empty($external_id)) { 
                $distributor = Distributor::findByExternalId($external_id);

                $data['back_url'] = route('salons.index');
                $data['distributor_filter'] = 0;
                $data['distributor_title'] = $distributor->name; 
                $data['distributor_id'] = $distributor->id;
                $data['distributor'] = $distributor; 
            } 

            // If user is distributor and trying to access index page of users listing
            if(Helper::is_distributor_user() && empty($external_id)) {
                return abort(403);
            }   

            $data['can_create_branch'] = true;
        } else { 

            $salon_id = Helper::getDistributorId();
            $data['can_create_branch'] = Helper::canCreateBranch($salon_id); 
            $data['distributor_filter'] = 0; 
        }

        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        } 
        
        return view('branch.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    { 
        $data['is_system_user'] = Helper::is_system_user();
        $data['is_distributor_user'] = Helper::is_distributor_user();
        $data['back_url'] = route('branch.index'); 
        $data['distributor_title'] = false; 
        $data['distributor_id'] = 0;
        $data['distributor'] = false;

        if(Helper::is_system_user() || Helper::is_distributor_user()) { 
            $external_id = $request->get('distributor') ?? ""; 
  
            if(!empty($external_id)) { 
                $distributor = Distributor::findByExternalId($external_id);

                $data['back_url'] = route('branch.index').'?distributor='.$external_id; 
                $data['distributor_title'] = $distributor->name; 
                $data['distributor_id'] = $distributor->id;
                $data['distributor'] = $distributor; 
            } 
        } 
  
        $data['users'] = User::all();  
        $data['countries'] = Country::where('deleted', 0)->pluck('name', 'country_id');
        $data['states'] = State::where('country_id', '101')->where('deleted', 0)->pluck('name', 'state_id');
        
        return view('branch.create')->with($data);
    } 


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBranchRequest $request)
    {
        $user_id = Auth::id();
        $distributor_id = $request->distributor_id ?? Helper::getDistributorId();
        $distributor = Distributor::find($distributor_id);
        $is_primary = $request->is_primary != null ? 1 : 0; 
  
        if(!Helper::canCreateBranch($distributor_id)) {
            return redirect()->back()->with('error', "As per subscription you can not create more branches");
        }

        if($is_primary == 1) {
            Branch::where('distributor_id', $distributor_id)->update([
                'is_primary' => 0,
            ]);
        }

        $store_data = [
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,   
            'primary_contact_person' => $request->primary_contact_person,   
            'secondary_contact_person' => $request->secondary_contact_person,   
            'primary_contact_number' => $request->primary_contact_number,   
            'secondary_contact_number' => $request->secondary_contact_number,   
            'country_id' => $request->country_id,   
            'city' => $request->city,   
            'primary_email' => $request->primary_email,   
            'secondary_email' => $request->secondary_email,   
            'zipcode' => $request->zipcode,   
            'is_primary' => $is_primary,   
            'address' => $request->address,   
            'created_by' => $user_id, 
            'updated_by' => $user_id, 
            'distributor_id' => $distributor_id,
        ];

        if($request->country_id == 101){
            $store_data['state_id'] = $request->state_id;
            $store_data['state_name'] = "";
        }else{
            $store_data['state_id'] = "";
            $store_data['state_name'] = $request->state_name;
        } 
 
        $branch = Branch::create($store_data);

        Session()->flash('success', __('Branch successfully added'));
        return redirect()->to($request->back_url); 
    }
  

    /**
     *  @return mixed
     *  $products
     */
    public function branchByDistributor(Request $request)
    { 
        $name = $request->get('name');
        
        if(Helper::is_system_user() || Helper::is_distributor_user()) {
            $distributor_id = $request->get('distributor_id');
        } else {
            $distributor_id = Helper::getDistributorId();
        }

        $branch = Branch::where('name', 'like', "%{$name}%")->where('distributor_id', $distributor_id)->get();
 
        return response()->json($branch);
    }

    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData(Request $request)
    {      
        // Master Admin login (System user)
        if(Helper::is_system_user() || Helper::is_distributor_user()) {
            $distributor_id = $request->get('distributor');
            $distributor = Distributor::find($distributor_id);
      
            $branch = Branch::with(['getDistributor'])->where('is_archive', 0);   
            // Check Id
            if($distributor_id != 0) { 
                $branch->where('distributor_id', $distributor_id);
            }
            $branch = $branch->orderBy('id','desc')->get();

        } else { // Salon Login
 
            $distributor = "";
            $distributor_id = Helper::getDistributorId();
            $branch = Branch::with(['getDistributor'])->where('is_archive', 0)->where('distributor_id', $distributor_id)->orderBy('id','desc')->get();
        }   
 
        return Datatables::of($branch)
            ->addColumn('name', function ($branch) {
                return  $branch->name ?? "";
            }) 
            ->addColumn('distributor', function ($branch) {
                return  $branch->getDistributor->name ?? "";
            }) 
            ->addColumn('city', function ($branch) {
                return  $branch->city ?? "" ;
            }) 
            ->addColumn('primary_contact_person', function ($branch) {
                $first_name = $branch->get_primary_user->first_name ?? "";
                $last_name = $branch->get_primary_user->last_name ?? "";
                return $first_name ." ". $last_name; 
            }) 
            ->addColumn('primary_contact_number', function ($branch) {
                return  $branch->primary_contact_number ?? "";
            }) 
            ->addColumn('primary_email', function ($branch) {
                return  $branch->primary_email ?? "" ;
            }) 
            ->addColumn('email', function ($branch) {
                return  $branch->email ?? "" ;
            })  
            ->addColumn('action', function ($branch) use ($distributor) { 
                if(!empty($distributor)) {
                    $edit_url = route('branch.edit', $branch->external_id) . "?distributor=". $distributor->external_id;
                } else {
                    $edit_url = route('branch.edit', $branch->external_id);
                }
				$html = '<form action="'.route('branch.destroy', $branch->external_id).'" class="d-flex" method="POST">';
				$html .= '<a href="#" class="view-in-modal btn btn-link" data-toggle="modal" data-enquiry-id="'.$branch->external_id.'" data-target="#view-enquiry-modal" data-toggle="tooltip" title="View Details"><i class="flaticon-eye text-primary text-hover-primary" data-enquiry-id="'.$branch->external_id.'" data-toggle="tooltip" title="View Details"></i></a>';
				if(\Entrust::can('branch-update') && !Helper::allowViewOnly($branch->distributor_id))
				$html .= '<a href="'. $edit_url .'" class="btn btn-link"  data-toggle="tooltip" title="Edit Branch"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';
				$html .= '<input type="hidden" name="_method" value="DELETE">';
				if(\Entrust::can('branch-delete') && $branch->id != 1)
                // $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-branch" data-toggle="tooltip" title="Delete Branch"><i class="flaticon2-trash text-danger text-hover-warning"></i></button>';
                $html .= '<input type="hidden" class="branch_id" value="'.$branch->external_id.'">'; 
				$html .= csrf_field();
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['name', 'distributor', 'city', 'primary_contact_person','primary_contact_number', 'primary_email', 'action'])
            ->make(true);
    }

        
    /**
     *  Return json format data of enquiry.
     *
     * @return \Illuminate\Http\Response
     */
    public function datailById(Request $request)
    {
        $branch = Branch::where('external_id', $request->external_id)->first();  

        $pp_first_name = $branch->get_primary_user->first_name ?? "";
        $pp_last_name =  $branch->get_primary_user->last_name ?? "";
        $sp_first_name = $branch->get_secondary_user->first_name ?? "";
        $sp_last_name =  $branch->get_secondary_user->last_name ?? "";

        if($branch->state_id == 0) {
            $state = $branch->state_name;
        } else {
            $state = $branch->getState->name;
        }

        $data = [
            'name' => $branch->name, 
            'primary_contact_person' => $pp_first_name ." ". $pp_last_name, 
            'secondary_contact_person' => $sp_first_name ." ". $sp_last_name, 
            'primary_contact_number' => $branch->primary_contact_number, 
            'secondary_contact_number' => $branch->secondary_contact_number, 
            'country' => $branch->getCountry->name, 
            'state' => $state, 
            'city' => $branch->city, 
            'primary_email' => $branch->primary_email, 
            'secondary_email' => $branch->secondary_email, 
            'zipcode' => $branch->zipcode, 
            'address' => $branch->address, 
            'is_primary' => $branch->is_primary == 1 ? "Yes" : "No",
        ];

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($url_id, Request $request)
    {
        $data['is_system_user'] = Helper::is_system_user();
        $data['is_distributor_user'] = Helper::is_distributor_user();
        $data['back_url'] = route('branch.index'); 
        $data['distributor_title'] = false; 
        $data['distributor_id'] = 0;
        $data['distributor'] = false;

        if(Helper::is_system_user() || Helper::is_distributor_user()) { 
            $data['distributor_title'] = true; 
            $external_id = $request->get('distributor') ?? ""; 
  
            if(!empty($external_id)) { 
                $distributor = Distributor::findByExternalId($external_id);

                $data['back_url'] = route('branch.index').'?distributor='.$external_id; 
                $data['distributor_title'] = $distributor->name; 
                $data['distributor_id'] = $distributor->id;
                $data['distributor'] = $distributor; 
            } 

            $branch = $this->findByExternalId($url_id); // Branch external id 
        } else {
            $distributor_id = Helper::getDistributorId();
            $branch = Branch::where('external_id', $url_id)->where('distributor_id', $distributor_id)->firstOrFail();
        }

        $primary_contact_person = User::where('id', $branch->primary_contact_person)->get()->pluck('full_name', 'id');
        $secondary_contact_person = User::where('id', $branch->secondary_contact_person)->get()->pluck('full_name', 'id');
        
        $data['selected_distributor'] = Distributor::select('name', 'id')->where('id', $branch->distributor_id)->first();
        $data['branch'] = $branch;
        $data['primary_contact_person'] = $primary_contact_person;
        $data['secondary_contact_person'] = $secondary_contact_person; 
        
        $data['countries'] = Country::where('deleted', 0)->pluck('name', 'country_id');
        $data['states'] = State::where('country_id', '101')->where('deleted', 0)->pluck('name', 'state_id');
        
        // dd($data);
        return view('branch.edit')->with($data);
    }
 

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBranchRequest $request, $external_id)
    {
        $user_id = Auth::id();  
        $branch = $this->findByExternalId($external_id);
        $is_primary = $request->is_primary != null ? 1 : 0; 

        if($is_primary == 1) {
            Branch::where('distributor_id', $branch->distributor_id)->update([
                'is_primary' => 0,
            ]);
        }
 
        $branch->fill([ 
            'name' => $request->name,   
            'primary_contact_person' => $request->primary_contact_person,   
            'secondary_contact_person' => $request->secondary_contact_person,   
            'primary_contact_number' => $request->primary_contact_number,   
            'secondary_contact_number' => $request->secondary_contact_number,   
            'country_id' => $request->country_id,   
            'state_id' => $request->state_id,   
            'state_name' => $request->state_name,   
            'city' => $request->city,      
            'primary_email' => $request->primary_email,   
            'secondary_email' => $request->secondary_email,   
            'zipcode' => $request->zipcode,   
            'is_primary' => $is_primary,   
            'address' => $request->address,   
            'updated_by' => $user_id,
        ])->save();

        Session()->flash('success', __('Branch successfully updated'));
        return redirect()->to($request->back_url); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxBranchDelete(Request $request)
    {
        $external_id = $request->external_id;  
        $branch = $this->findByExternalId($external_id);
        
        $user_id = Auth::id();
        $branch->fill([
            'is_archive' => 1, 
            'updated_by' => $user_id,
        ])->save();

        Session()->flash('success', __('Branch successfully deleted'));  
        return response()->json([
            'status' => true,
            'message' => "Branch deleted successfully!"
        ]);
    }
 

    public function findByExternalId($external_id)
    { 
        return Branch::where('external_id', $external_id)->firstOrFail();
    }

    // Check repeat primary number of branch
    public function checkPrimaryNumberRepeat(Request $request)
    {     
        $is_system_user = Helper::getDistributorId(); 
        if($is_system_user == 0) {
            $distributor_id = $request->distributor_id;
        } else {
            $distributor_id = $is_system_user;
        }

        $id = $request->id;
        $number = $request->number;
        $number = Branch::where('primary_contact_number', $number)->where('distributor_id', $distributor_id)->first();

        if($number !== null) { 
            if($id == $number->id) { 
                echo "true";
            } else {
                echo "false";
            }
        } else {
            echo "true";
        } 
    }



    // Check repeat email of branch
    public function checkPrimaryEmailRepeat(Request $request)
    {     
        $is_system_user = Helper::getDistributorId(); 
        if($is_system_user == 0) {
            $distributor_id = $request->distributor_id;
        } else {
            $distributor_id = $is_system_user;
        }

        $id = $request->id;
        $email = $request->email;
        $email = Branch::where('primary_email', $email)->where('distributor_id', $distributor_id)->first();

        if($email !== null) { 
            if($id == $email->id) { 
                echo "true";
            } else {
                echo "false";
            }
        } else {
            echo "true";
        } 
    }

    /**
     *  @return mixed
     *  $industries
     */
    public function getBranchByName(Request $request)
    {
        $name = $request->get('name');

        $is_system_user = Helper::getDistributorId();  
        if($is_system_user == 0) { //is system user
            $distributor_id = $request->get('distributor_id');
        } else {
            $distributor_id = $is_system_user;
        }   
        
        $branches = Branch::where('name', 'like', "%{$name}%")->where('distributor_id', $distributor_id)->get();
         
        return response()->json($branches);
    }

    /**
     *  Branch by distributors external id  
     * 
     */
    public function getBranchByDistributor(Request $request)
    {
        $name = $request->get('name');

        $is_system_user = Helper::getDistributorId();  
        if($is_system_user == 0) { //is system user
            $distributor_id = $request->get('distributor_id');
            $distributor = Distributor::select('id')->where('external_id', $distributor_id)->first();
            $distributor_id = $distributor->id;
        } else {
            $distributor_id = $is_system_user;
        }   
        
         
        $branches = Branch::where('name', 'like', "%{$name}%")->where('distributor_id', $distributor_id)->get();
         
        return response()->json($branches);
    }



    public static function getSalesData($start_date = null, $end_date = null)
    {
        $distributor_id = Helper::getDistributorId();

        if(empty($start_date)) {
            $start_date = Carbon::parse('20-06-2021')->startOfYear()->format('Y-m-d');
        } else {
            $start_date = date('Y-m-d', strtotime($start_date));
        }
        if(empty($end_date)) {
            $end_date = Carbon::parse('20-06-2021')->endOfYear()->format('Y-m-d');;
        } else {
            $end_date = date('Y-m-d', strtotime($end_date));
        }

        $order = Order::select("*", DB::raw("SUM(`final_amount`) AS total_sales, DATE(created_at) AS created_at"));
        $order->where("distributor_id", $distributor_id);
        $order->whereRaw("created_at between date('".$start_date."') and date('".$end_date."')");
        $order->groupBy('branch_id');
        $order->groupBy(DB::raw('Date(created_at)'));

        $sales_data = $order->get(); 
 
        $branches_data = [];
 
        foreach($sales_data as $sales) {
            if(!Helper::in_array_r($sales->branch->name, $branches_data)) {
                array_push($branches_data, [
                    'name' => $sales->branch->name,
                    'type' => "spline",
                    'yValueFormatString' => "₹0#",
                    'showInLegend' => true,
                    'dataPoints' => []
                ]);
            } 
        }

        foreach($sales_data as $sales) { 
            for ($x = 0; count($branches_data) > $x; $x++) { 
                if(Helper::in_array_r($sales->branch->name, $branches_data[$x])) { 
                    array_push($branches_data[$x]['dataPoints'], [
                        'label' => date('d-m-Y', strtotime($sales->created_at)),
                        'y' => $sales->total_sales,
                    ]);
                }
            }
        } 

        // dd($branches_data);
        
        $start_date = date('d-m-Y', strtotime($start_date));
        $end_date = date('d-m-Y', strtotime($end_date));
        return [
            'title' => "Sales Report (from $start_date to $end_date)",
            'sales_data' => $branches_data, 
        ];
    }
}
