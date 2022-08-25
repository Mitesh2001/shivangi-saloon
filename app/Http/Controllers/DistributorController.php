<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Datatables;

use App\Models\User; 
use App\Models\Branch;
use App\Models\Client; 
use App\Models\Distributor; 
use App\Models\Country;
use App\Models\State;

use App\Helpers\Helper; 

use App\Http\Requests\Distributor\StoreDistributorRequest;
use App\Http\Requests\Distributor\UpdateDistributorRequest;


class DistributorController extends Controller
{
        
    public function __construct()
    {  
        $this->middleware('permission:salon-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:salon-create', ['only' => ['create','store']]);
		$this->middleware('permission:salon-update', ['only' => ['edit','update']]); 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        $from = $request->get('from');
        $data['back_url'] = false;
        if(!empty($from)){
            $data['back_url'] = route('dashboard');
        }

        $data['is_distributor_user'] = Helper::is_distributor_user();
        return view('salons.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {     
        $data['establish_years'] = $this->establishYears();
        $data['countries'] = Country::where('deleted', 0)->pluck('name', 'country_id');
        $data['states'] = State::where('country_id', '101')->where('deleted', 0)->pluck('name', 'state_id');
        return view('salons.create')->with($data);
    }

    public function checkName(Request $request)
    {  
        $id = $request->id;
        $name = $request->name;
        $unit = Package::where('name', $name)->first();

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

    
    // Check repeat primary number of vendor
    public function checkPrimaryNumberRepeat(Request $request)
    {     
        $id = $request->id;
        $number = $request->number;
        $number = Distributor::where('primary_number', $number)->first();

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

    // Check repeat email of vendor
    public function checkPrimaryEmailRepeat(Request $request)
    {     
        $id = $request->id;
        $email = $request->email;
        $email = Distributor::where('primary_email', $email)->first();

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDistributorRequest $request)
    {   
        $user_id = Auth::id();

        if($request->hasFile('logo')) {

            $logo = strtolower(str_replace(' ', '_',$request->name));

            $logo_name = $logo ."_". time() .".". $request->logo->extension();
            $path = 'storage/assets/distributors/logo/';
            $returned = $request->logo->move(public_path($path), $logo_name); 
            $logo_name_store = $path . $logo_name;

        } else {
            $logo_name_store = "";
        }
 
        $sms_service = $request->sms_service == "on" ? 1 : 0;
        $email_service = $request->email_service == "on" ? 1 : 0; 

        $store_data = [
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,    
            'gst_number' => $request->gst_number,    
            'primary_number' => $request->primary_number,    
            'secondary_number' => $request->secondary_number,    
            'primary_email' => $request->primary_email,    
            'secondary_email' => $request->secondary_email,    
            'contact_person' => $request->contact_person,    
            'contact_person_number' => $request->contact_person_number,    
            'contact_person_email' => $request->contact_person_email,   
            'pan_number' => $request->pan_number,   
            'number_of_employees' => $request->number_of_employees,   
            'country_id' => $request->country_id,    
            'logo' => $logo_name_store,   
            'city' => $request->city,    
            'address' => $request->address,    
            'zipcode' => $request->zipcode,    
            'sender_id' => $request->sender_id,    
            'from_email' => $request->from_email,    
            'from_name' => $request->from_name,    
            'sms_service' => $sms_service,    
            'email_service' => $email_service,    
            'created_by' => $user_id, 
            'updated_by' => $user_id, 
        ];

        if($request->country_id == 101){
            $store_data['state_id'] = $request->state_id;
            $store_data['state_name'] = "";
        }else{
            $store_data['state_id'] = "";
            $store_data['state_name'] = $request->state_name;
        } 
  
        $distributor = Distributor::create($store_data);

        Session()->flash('success', __('Salon successfully added!'));
        return redirect()->route('salons.index');
    }
  
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {   
        $user_id = Auth::id();
 
        $query = Distributor::orderBy('id', 'desc');
        if(Helper::is_distributor_user() === true) {
            $query->where('created_by', $user_id); // get salons created by current user
        }
        $distributor = $query->get();
  
        return Datatables::of($distributor)
            ->addColumn('created_by', function ($distributor) { 
                $fullname = $distributor->createdBy->first_name . " " ?? "";
                $fullname .= $distributor->createdBy->last_name ?? "";
                return  $fullname;
            })  
            ->addColumn('name', function ($distributor) {
                return  $distributor->name ?? "";
            })  
            ->addColumn('gst_number', function ($distributor) {
                return  $distributor->gst_number ?? "";
            })  
            ->addColumn('primary_number', function ($distributor) {
                return  $distributor->primary_number ?? "";
            })  
            ->addColumn('secondary_number', function ($distributor) {
                return  $distributor->secondary_number ?? "";
            })  
            ->addColumn('primary_email', function ($distributor) {
                return  $distributor->primary_email ?? "";
            })  
            ->addColumn('secondary_email', function ($distributor) {
                return  $distributor->secondary_email ?? "";
            })  
            ->addColumn('contact_person', function ($distributor) {
                return  $distributor->contact_person ?? "";
            })  
            ->addColumn('contact_person_number', function ($distributor) {
                return  $distributor->contact_person_number ?? "";
            })  
            ->addColumn('contact_person_email', function ($distributor) {
                return  $distributor->contact_person_email ?? "";
            })  
            ->addColumn('city', function ($distributor) {
                return  $distributor->city ?? "";
            })  
            ->addColumn('address', function ($distributor) {
                return  $distributor->address ?? "";
            })  
            ->addColumn('zipcode', function ($distributor) {
                return  $distributor->zipcode ?? "";
            })  
            ->addColumn('action', function ($distributor) {
				$html = '<form action="'.route('salons.destroy', $distributor->external_id).'" class="d-flex" method="POST">';
				// $html .= '<a href="#" class="view-in-modal mr-3" data-toggle="modal" data-enquiry-id="'.$distributor->external_id.'" data-target="#view-enquiry-modal" data-toggle="tooltip" title="View Details"><i class="flaticon-eye text-primary text-hover-primary" data-enquiry-id="'.$distributor->external_id.'" data-toggle="tooltip" title="View Details"></i></a>'; 
				$html .= '<input type="hidden" name="_method" value="DELETE">';

                    if(\Entrust::can('salon-view') || \Entrust::can('salon-update')) 
                    $html .= '<div class="dropdown ml-3 "><button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button> <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="">';
                        $html .=    '<a href="#" class="dropdown-item view-in-modal" data-toggle="modal" data-enquiry-id="'.$distributor->external_id.'" data-target="#view-enquiry-modal" data-toggle="tooltip" title="View Details">View Salon</a>'; 
                    if(\Entrust::can('salon-update'))  
                        $html .=    '<a href="'.route('salons.edit', $distributor->external_id).'" class="dropdown-item">Edit Salon</a>';  
                        // $html .=    '<a href="#" class="dropdown-item delete-distributor">Delete Distributor</a>';       

                    if(\Entrust::can('salon-update'))  
                        $html .=    '<a href="'.route('branch.index').'?distributor='.$distributor->external_id.'" class="dropdown-item">Manage Branches</a>'; 
                    if(\Entrust::can('salon-update'))  
                        $html .=    '<a href="'.route('users.index').'?distributor='.$distributor->external_id.'" class="dropdown-item">Manage Employees</a>'; 
                    // if(\Entrust::can('distributor-edit'))  
                    $html .=    '<a href="'.route('subscriptions.index').'?salon_id='.$distributor->external_id.'" class="dropdown-item">Subscriptions</a>'; 
                    // if(\Entrust::can('distributor-edit'))  
                    //     $html .=    '<a href="'.route('users.index').'?distributor='.$distributor->external_id.'" class="dropdown-item">Custom Form</a>'; 

                    $html .= '</div>
                            </div>'; 

                $html .= '<input type="hidden" class="distributor_id" value="'.$distributor->external_id.'">'; 
				$html .= csrf_field();
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['created_by', 'name', 'gst_number', 'primary_number', 'secondary_number', 'primary_email', 'secondary_email', 'contact_person', 'contact_person_number', 'contact_person_email', 'city', 'address', 'zipcode', 'action'])
            ->make(true);
    }
 
    /**
     *  Return json format data of salons.
     *
     * @return \Illuminate\Http\Response
     */
    public function datailById(Request $request)
    {
        $distributor = Distributor::where('external_id', $request->external_id)->first(); 
        

        $state_name = $distributor->state_name ?? "";
        if($distributor->state_id != 0) {
            $state_name = $distributor->getState->name;
        } 
  
        $data = [
            'name' => $distributor->name ?? "", 
            'number_of_employees' => $distributor->number_of_employees,   
            'country' => $distributor->getCountry->name,   
            'state' => $state_name,   
            'logo' => asset($distributor->logo),   
            'gst_number' => $distributor->gst_number ?? "", 
            'pan_number' => $distributor->pan_number ?? "", 
            'primary_number' => $distributor->primary_number ?? "", 
            'secondary_number' => $distributor->secondary_number ?? "", 
            'primary_email' => $distributor->primary_email ?? "", 
            'secondary_email' => $distributor->secondary_email ?? "", 
            'contact_person' => $distributor->contact_person ?? "", 
            'contact_person_number' => $distributor->contact_person_number ?? "", 
            'contact_person_email' => $distributor->contact_person_email ?? "", 
            'city' => $distributor->city ?? "", 
            'address' => $distributor->address ?? "", 
            'zipcode' => $distributor->zipcode ?? "", 
        ];

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($external_id)
    {   
        $data['establish_years'] = $this->establishYears();
        $data['countries'] = Country::where('deleted', 0)->pluck('name', 'country_id');
        $data['states'] = State::where('country_id', '101')->where('deleted', 0)->pluck('name', 'state_id');
        $data['distributor'] = $this->findByExternalId($external_id); 
        return view('salons.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDistributorRequest $request, $external_id)
    {   
        $user_id = Auth::id();  
        $distributor = $this->findByExternalId($external_id);

        if($request->hasFile('logo')) {

            $product_name = strtolower(str_replace(' ', '_',$request->name));
            $logo_name = $product_name ."_". time() . ".". $request->logo->extension();
            $path = 'storage/assets/distributors/logo/';
            $request->logo->move(public_path($path), $logo_name);
            
            if($request->old_logo != "" && file_exists(public_path($request->old_logo))) { 
                unlink(public_path($request->old_logo));
            }
            $logo = $path . $logo_name;

        } else {
            $logo = $request->old_logo;
        }
         
        $sms_service = $request->sms_service == "on" ? 1 : 0;
        $email_service = $request->email_service == "on" ? 1 : 0;

        $update_arr = [ 
            'name' => $request->name,    
            'gst_number' => $request->gst_number,    
            'primary_number' => $request->primary_number,    
            'secondary_number' => $request->secondary_number,    
            'primary_email' => $request->primary_email,    
            'secondary_email' => $request->secondary_email,    
            'contact_person' => $request->contact_person,    
            'contact_person_number' => $request->contact_person_number,    
            'contact_person_email' => $request->contact_person_email,    
            'pan_number' => $request->pan_number,   
            'number_of_employees' => $request->number_of_employees,   
            'country_id' => $request->country_id,   
            'state_id' => $request->state_id,   
            'logo' => $logo,   
            'city' => $request->city,    
            'address' => $request->address,    
            'zipcode' => $request->zipcode, 
            'sender_id' => $request->sender_id,    
            'from_email' => $request->from_email,    
            'from_name' => $request->from_name,    
            'sms_service' => $sms_service,    
            'email_service' => $email_service,      
            'updated_by' => $user_id,
        ];
        
        if($request->country_id == 101){
            $update_arr['state_id'] = $request->state_id;
            $update_arr['state_name'] = "";
        }else{
            $update_arr['state_id'] = "";
            $update_arr['state_name'] = $request->state_name;
        } 
          
        $distributor->fill($update_arr)->save();

        Session()->flash('success', __('Salon successfully updated!'));
        return redirect()->route('salons.index'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkDistributorDelete(Request $request) 
    {
        $external_id = $request->external_id;  
 
        $distributor = $this->findByExternalId($external_id);  
        $clients_count = Client::where('distributor_id', $distributor->id)->count();
        $branches_count = Branch::where('distributor_id', $distributor->id)->count();
 
        if($clients_count === 0 && $branches_count === 0) {
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Sorry this dealer is in use!",
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxDistributorDelete(Request $request)
    {
        $external_id = $request->external_id;  
        $distributor = $this->findByExternalId($external_id);
        $distributor->delete();

        Session()->flash('success', __('Salon successfully deleted!'));
        return response()->json([
            'status' => true,
            'message' => "Salon successfully deleted!"
        ]);
    }

    public function findByExternalId($external_id)
    {
        return Distributor::where('external_id', $external_id)->firstOrFail();
    }
 
    /**
     *  @return mixed
     *  $distributors
     */
    public function getDistributorByName(Request $request)
    {
        $name = $request->get('q');
        $distributors = Distributor::where('name', 'like', "%{$name}%")->get();
 
        return response()->json($distributors);
    }

    public function establishYears()
    {
        $years = range(1949,date('Y'));
        $years = array_values($years);
        $years = array_slice($years, 1, null, true);
        return $years;
    }
}
