<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Datatables;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\StockIncomeHistory;

use App\Http\Requests\Vendor\StoreVendorRequest;
use App\Http\Requests\Vendor\UpdateVendorRequest;

use App\Helpers\Helper;
use App\Models\Distributor;
 
class VendorsController extends Controller
{
        
    public function __construct()
    {
        $this->middleware('permission:vendor-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:vendor-create', ['only' => ['create','store']]);
		$this->middleware('permission:vendor-update', ['only' => ['edit','update']]);
		$this->middleware('permission:vendor-delete', ['only' => ['destroy']]);  
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

        return view('vendors.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $data['is_system_user'] = Helper::getDistributorId(); 
        return view('vendors.create')->with($data);
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

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // system user
            $distributor_id = $request->distributor_id;
        }

        $number = Vendor::where('primary_number', $number)->where('distributor_id', $distributor_id)->first();

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

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // system user
            $distributor_id = $request->distributor_id;
        }

        $email = Vendor::where('primary_email', $email)->where('distributor_id', $distributor_id)->first();

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
    public function store(StoreVendorRequest $request)
    { 
        $user_id = Auth::id();
        
        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;
        }  

        if(Helper::allowViewOnly($distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        $vendor = Vendor::create([
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
            'city' => $request->city,    
            'address' => $request->address,    
            'zipcode' => $request->zipcode,    
            'created_by' => $user_id, 
            'updated_by' => $user_id, 
            'distributor_id' => $distributor_id,
        ]);

        Session()->flash('success', __('Vendor successfully added!'));
        return redirect()->route('vendors.index');
    }
  
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {   
        $vendor = Vendor::with(['getDistributor']); 
        
        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id != 0) { // Check if distributor
            $vendor->where('distributor_id', $distributor_id);
        }  
        $vendor = $vendor->orderBy('id', 'desc')->get();  
  
        return Datatables::of($vendor)
            ->addColumn('distributor', function ($branch) {
                return  $branch->getDistributor->name ?? "";
            }) 
            ->addColumn('name', function ($vendor) {
                return  $vendor->name ?? "";
            })  
            ->addColumn('gst_number', function ($vendor) {
                return  $vendor->gst_number ?? "";
            })  
            ->addColumn('primary_number', function ($vendor) {
                return  $vendor->primary_number ?? "";
            })  
            ->addColumn('secondary_number', function ($vendor) {
                return  $vendor->secondary_number ?? "";
            })  
            ->addColumn('primary_email', function ($vendor) {
                return  $vendor->primary_email ?? "";
            })  
            ->addColumn('secondary_email', function ($vendor) {
                return  $vendor->secondary_email ?? "";
            })  
            ->addColumn('contact_person', function ($vendor) {
                return  $vendor->contact_person ?? "";
            })  
            ->addColumn('contact_person_number', function ($vendor) {
                return  $vendor->contact_person_number ?? "";
            })  
            ->addColumn('contact_person_email', function ($vendor) {
                return  $vendor->contact_person_email ?? "";
            })  
            ->addColumn('city', function ($vendor) {
                return  $vendor->city ?? "";
            })  
            ->addColumn('address', function ($vendor) {
                return  $vendor->address ?? "";
            })  
            ->addColumn('zipcode', function ($vendor) {
                return  $vendor->zipcode ?? "";
            })  
            ->addColumn('action', function ($vendor) {
				$html = '<form action="'.route('vendors.destroy', $vendor->external_id).'" class="d-flex" method="POST">';
				$html .= '<a href="#" class="view-in-modal btn btn-link mr-3" data-toggle="modal" data-enquiry-id="'.$vendor->external_id.'" data-target="#view-enquiry-modal" data-toggle="tooltip" title="View Details"><i class="flaticon-eye text-primary text-hover-primary" data-enquiry-id="'.$vendor->external_id.'" data-toggle="tooltip" title="View Details"></i></a>';
				if(\Entrust::can('vendor-update') && !Helper::allowViewOnly($vendor->distributor_id)) 
				$html .= '<a href="'.route('vendors.edit', $vendor->external_id).'" class="btn btn-link" data-toggle="tooltip" title="Edit Vendor"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';
				$html .= '<input type="hidden" name="_method" value="DELETE">';
				if(\Entrust::can('vendor-delete'))
                // $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-vendor" data-toggle="tooltip" title="Delete Vendor"><i class="flaticon2-trash text-danger text-hover-warning"></i></button>';
                $html .= '<input type="hidden" class="vendor_id" value="'.$vendor->external_id.'">'; 
				$html .= csrf_field();
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['distributor', 'name', 'gst_number', 'primary_number', 'secondary_number', 'primary_email', 'secondary_email', 'contact_person', 'contact_person_number', 'contact_person_email', 'city', 'address', 'zipcode', 'action'])
            ->make(true);
    }
 
        /**
     *  Return json format data of Vendors.
     *
     * @return \Illuminate\Http\Response
     */
    public function datailById(Request $request)
    {
        $vendor = Vendor::where('external_id', $request->external_id)->first(); 
  
        $data = [
            'distributor' => $vendor->getDistributor->name ?? "", 
            'name' => $vendor->name ?? "", 
            'gst_number' => $vendor->gst_number ?? "", 
            'primary_number' => $vendor->primary_number ?? "", 
            'secondary_number' => $vendor->secondary_number ?? "", 
            'primary_email' => $vendor->primary_email ?? "", 
            'secondary_email' => $vendor->secondary_email ?? "", 
            'contact_person' => $vendor->contact_person ?? "", 
            'contact_person_number' => $vendor->contact_person_number ?? "", 
            'contact_person_email' => $vendor->contact_person_email ?? "", 
            'city' => $vendor->city ?? "", 
            'address' => $vendor->address ?? "", 
            'zipcode' => $vendor->zipcode ?? "", 
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
        $is_system_user = Helper::getDistributorId();

        // Only system user can access others data
        if($is_system_user == 0) { 
            $data['vendor'] = $this->findByExternalId($external_id);
        } else {
            $data['vendor'] = Vendor::where('external_id', $external_id)->where('distributor_id', $is_system_user)->firstOrFail();
        }
 
        $data['is_system_user'] = $is_system_user;
        $data['distributor'] = Distributor::findOrFail($data['vendor']->distributor_id); // current record distributor name (for admin)

        return view('vendors.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVendorRequest $request, $external_id)
    {  
        $user_id = Auth::id();  
        $vendor = $this->findByExternalId($external_id);

        if(Helper::allowViewOnly($vendor->distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }
 
        $vendor->fill([ 
            'name' => $request->name,    
            'gst_number' => $request->gst_number,    
            'primary_number' => $request->primary_number,    
            'secondary_number' => $request->secondary_number,    
            'primary_email' => $request->primary_email,    
            'secondary_email' => $request->secondary_email,    
            'contact_person' => $request->contact_person,    
            'contact_person_number' => $request->contact_person_number,    
            'contact_person_email' => $request->contact_person_email,    
            'city' => $request->city,    
            'address' => $request->address,    
            'zipcode' => $request->zipcode,      
            'updated_by' => $user_id,
        ])->save();

        Session()->flash('success', __('Vendor successfully updated!'));
        return redirect()->route('vendors.index'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkVendorDelete(Request $request) 
    {
        $external_id = $request->external_id;  
 
        $vendor = $this->findByExternalId($external_id); 
        $vendor_count = StockIncomeHistory::where('vendor_id', $vendor->id)->count(); 
 
        if($vendor_count === 0) {
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Sorry this vendor is in use!",
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxVendorDelete(Request $request)
    {
        $external_id = $request->external_id;  
        $vendor = $this->findByExternalId($external_id);
        $vendor->delete();

        Session()->flash('success', __('Vendor successfully deleted!'));
        return response()->json([
            'status' => true,
            'message' => "Vendor deleted successfully!"
        ]);
    }

    public function findByExternalId($external_id)
    {
        return Vendor::where('external_id', $external_id)->firstOrFail();
    }
 
    /**
     *  @return mixed
     *  $vendors
     */
    public function getVendorsByName(Request $request)
    {
        $name = $request->get('q');

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { //is system user
            $distributor_id = $request->get('distributor_id');
        }

        $vendors = Vendor::where('name', 'like', "%{$name}%")->where('distributor_id', $distributor_id)->get();
 
        return response()->json($vendors);
    }
}
