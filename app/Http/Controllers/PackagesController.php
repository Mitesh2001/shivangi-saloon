<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Datatables;

use App\Models\User;
use App\Models\Package;
use App\Models\Product;

use App\Http\Requests\Package\StorePackageRequest;
use App\Http\Requests\Package\UpdatePackageRequest;

use App\Helpers\Helper;
use App\Models\Distributor;

class PackagesController extends Controller
{
            
    public function __construct()
    {
        $this->middleware('permission:package-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:package-create', ['only' => ['create','store']]);
		$this->middleware('permission:package-update', ['only' => ['edit','update']]);
		$this->middleware('permission:package-delete', ['only' => ['destroy']]);  
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

        return view('package.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $data['is_system_user'] = Helper::getDistributorId(); 
        return view('package.create')->with($data);
    }

    public function checkName(Request $request)
    {  
        $id = $request->id;
        $name = $request->name;
        
        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // system user
            $distributor_id = $request->distributor_id;
        }

        $package = Package::where('name', $name)->where('distributor_id', $distributor_id)->first();

        if($package !== null) { 
            if($id == $package->id) { 
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
    public function store(StorePackageRequest $request)
    {
        $user_id = Auth::id();

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;
        }  

        if(Helper::allowViewOnly($distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        $package = Package::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,    
            'created_by' => $user_id, 
            'updated_by' => $user_id, 
            'distributor_id' => $distributor_id,
        ]);

        Session()->flash('success', __('Package successfully added'));
        return redirect()->route('package.index');
    }
  
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {   
        $package = Package::with(['getDistributor']);  
        
        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id != 0) { // Check if distributor
            $package->where('distributor_id', $distributor_id);
        }  

        $package = $package->orderBy('id', 'desc')->get();
  
        return Datatables::of($package)
            ->addColumn('distributor', function ($branch) {
                return  $branch->getDistributor->name ?? "";
            }) 
            ->addColumn('name', function ($package) {
                return  $package->name;
            })  
            ->addColumn('action', function ($package) {
				$html = '<form action="'.route('package.destroy', $package->external_id).'" method="POST">';
				if(\Entrust::can('package-update') && !Helper::allowViewOnly($package->distributor_id)) 
				$html .= '<a href="'.route('package.edit', $package->external_id).'" class="btn btn-link" data-toggle="tooltip" title="Edit Package"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';
				$html .= '<input type="hidden" name="_method" value="DELETE">';
				if(\Entrust::can('package-delete'))
                // $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-package" data-toggle="tooltip" title="Delete Package"><i class="flaticon2-trash text-danger text-hover-warning"></i></button>';
                $html .= '<input type="hidden" class="package_id" value="'.$package->external_id.'">'; 
				$html .= csrf_field();
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['distributor', 'name', 'action'])
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
        $distributor_id = Helper::getDistributorId();
        
        // Only system user can access others data
        if($distributor_id == 0) { 
            $data['package'] = $this->findByExternalId($external_id); 
        } else {  
            $data['package'] = Package::where('external_id', $external_id)->where('distributor_id', $distributor_id)->firstOrFail(); 
        }
   
        $data['is_system_user'] = $distributor_id;
        $data['distributor'] = Distributor::findOrFail($data['package']->distributor_id); // current record distributor name (for admin)

        return view('package.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePackageRequest $request, $external_id)
    {  
        $user_id = Auth::id();  
        $package = $this->findByExternalId($external_id);

        if(Helper::allowViewOnly($package->distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }
 
        $package->fill([ 
            'name' => $request->name,    
            'updated_by' => $user_id,
        ])->save();

        Session()->flash('success', __('Package successfully updated!'));
        return redirect()->route('package.index'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkPackageDelete(Request $request) 
    {
        $external_id = $request->external_id;  
 
        $package = $this->findByExternalId($external_id); 
        $package_count = Product::where('package_id', $package->id)->count(); 

        if($package_count === 0) {
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Sorry this package is in use!",
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxPackageDelete(Request $request)
    {
        $external_id = $request->external_id;  
        $package = $this->findByExternalId($external_id);
        $package->delete();

        Session()->flash('success', __('Package successfully deleted!'));
        return response()->json([
            'status' => true,
            'message' => "Package deleted successfully!"
        ]);
    }

    public function findByExternalId($external_id)
    {
        return Package::where('external_id', $external_id)->firstOrFail();
    }
}
