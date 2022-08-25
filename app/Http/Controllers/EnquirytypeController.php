<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
 
use Datatables;

use App\Http\Requests\Enquirytype\StoreEnquirytypeRequest;
use App\Http\Requests\Enquirytype\UpdateEnquirytypeRequest; 

use App\Models\User;
use App\Models\Enquiry;
use App\Models\EnquiryType;


class EnquirytypeController extends Controller
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
        
        $this->middleware('permission:inquiry-type-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:inquiry-type-create', ['only' => ['create','store']]);
		$this->middleware('permission:inquiry-type-update', ['only' => ['edit','update']]);
		$this->middleware('permission:inquiry-type-delete', ['only' => ['destroy']]);  
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('enquirytype.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('enquirytype.create');
    }

    public function checkName(Request $request)
    {  
        $id = $request->id;
        $name = $request->name;
        $enquiry = EnquiryType::where('name', $name)->first();

        if($enquiry !== null) { 
            if($id == $enquiry->id) { 
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
    public function store(StoreEnquirytypeRequest $request)
    {
        $name = $request->name;

        $user_id = Auth::id();

        $enquirytype = EnquiryType::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,  
            'created_by' => $user_id, 
            'updated_by' => $user_id, 
        ]);

        Session()->flash('success', __('Enquery type successfully added'));
        return redirect()->route('enquirytype.index');
    }
        
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {   
        $enquiry_types = EnquiryType::all(); 
  
        return Datatables::of($enquiry_types)
            ->addColumn('name', function ($enquiry_types) {
                return  $enquiry_types->name;
            })  
            ->addColumn('action', function ($enquiry_types) {
				$html = '<form action="'.route('enquirytype.destroy', $enquiry_types->external_id).'" method="POST">';
				if(\Entrust::can('inquiry-type-update'))
				$html .= '<a href="'.route('enquirytype.edit', $enquiry_types->external_id).'" class="btn btn-link" data-toggle="tooltip" title="Edit Inquiry Type"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';
				$html .= '<input type="hidden" name="_method" value="DELETE">';
				if(\Entrust::can('inquiry-type-delete'))
				// $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-enquirytype" data-toggle="tooltip" title="Delete Inquiry Type"><i class="flaticon2-trash text-danger text-hover-warning"></i></button>';
                $html .= '<input type="hidden" class="enquiry_type_id" value="'.$enquiry_types->external_id.'">'; 
				$html .= csrf_field();
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['name', 'parent_category', 'action'])
            ->make(true);
    } 

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $external_id
     * @return \Illuminate\Http\Response
     */
    public function edit($external_id)
    {
        $data['enquiry_type'] = $this->findByExternalId($external_id);  
  
        return view('enquirytype.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEnquirytypeRequest $request, $external_id)
    {
        $user_id = Auth::id();  
        $enquiry_type = $this->findByExternalId($external_id);
 
        $enquiry_type->fill([
            'name' => $request->name,  
            'updated_by' => $user_id,
        ])->save();

        Session()->flash('success', __('Enquiry type successfully updated'));
        return redirect()->route('enquirytype.index'); 
    }

    
    public function checkEnquirytpyDelete(Request $request) 
    {
        $external_id = $request->external_id; 
        
        $enquiry_type = $this->findByExternalId($external_id);

        $enquiry_count = Enquiry::where('enquiry_type', $enquiry_type->id)->count(); 

        if($enquiry_count === 0) {
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Sorry this enquiry tpye is in use!",
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxEnquirytpyDelete(Request $request)
    {
        $external_id = $request->external_id;  
        $category = $this->findByExternalId($external_id);
        $category->delete();

        Session()->flash('success', __('Enquiry type successfully deleted!'));
        return response()->json([
            'status' => true,
            'message' => "Enquiry tpye deleted successfully!"
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
        $enquiry_type = $this->findByExternalId($external_id);
        $enquiry_type->delete();

        Session()->flash('success', __('Enquiry type successfully deleted'));
        return redirect()->route('enquirytype.index');
    }
 
    public function findByExternalId($external_id)
    {
        return EnquiryType::where('external_id', $external_id)->firstOrFail();
    }
}
