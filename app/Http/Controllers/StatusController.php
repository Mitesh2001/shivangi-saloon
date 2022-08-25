<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
 
use Datatables;

use App\Http\Requests\Status\StoreStatusRequest;
use App\Http\Requests\Status\UpdateStatusRequest; 

use App\Models\User; 
use App\Models\Status; 
use App\Models\Enquiry; 
use App\Models\Appointment; 


class StatusController extends Controller
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
        
        $this->middleware('permission:status-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:status-create', ['only' => ['create','store']]);
		$this->middleware('permission:status-update', ['only' => ['edit','update']]);
		$this->middleware('permission:status-delete', ['only' => ['destroy']]);  
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('status.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('status.create');
    }

    public function checkName(Request $request)
    {    
        $id = $request->id;
        $title = $request->title;
        $status = Status::where('title', $title)->first();

        if($status !== null) { 
            if($id == $status->id) { 
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
    public function store(StoreStatusRequest $request)
    {  
        $user_id = Auth::id();

        $enquirytype = Status::create([
            'external_id' => Uuid::uuid4()->toString(),
            'title' => $request->title,  
            'color' => $request->color,  
            'created_by' => $user_id, 
            'updated_by' => $user_id, 
        ]);

        Session()->flash('success', __('Status successfully added'));
        return redirect()->route('status.index');
    }
        
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {   
        $statuses = Status::all();  
  
        return Datatables::of($statuses)
            ->addColumn('title', function ($statuses) {
                return  $statuses->title;
            })  
            ->addColumn('color', function ($statuses) {
                return '<span class="label label-inline label-lg font-weight-bolder" style="background-color:'. $statuses->color .'; color: #fff">'.$statuses->title.'</span>'; 
            })  
            ->addColumn('action', function ($statuses) {
				$html = '<form action="'.route('status.destroy', $statuses->external_id).'" method="POST">';
				if(\Entrust::can('status-update'))
				$html .= '<a href="'.route('status.edit', $statuses->external_id).'" class="btn btn-link" data-toggle="tooltip" title="Edit Status"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';
				$html .= '<input type="hidden" name="_method" value="DELETE">';
				if(\Entrust::can('status-delete'))
				// $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-status" data-toggle="tooltip" title="Delete Status"><i class="flaticon2-trash text-danger text-hover-warning"></i></button>';
                $html .= '<input type="hidden" class="status_id" value="'.$statuses->external_id.'">'; 
				$html .= csrf_field();
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['title', 'color', 'action'])
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
        $data['status'] = $this->findByExternalId($external_id);  
  
        return view('status.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStatusRequest $request, $external_id)
    { 
        $user_id = Auth::id();  
        $enquiry_type = $this->findByExternalId($external_id);
 
        $enquiry_type->fill([ 
            'title' => $request->title,  
            'color' => $request->color,   
            'updated_by' => $user_id, 
        ])->save();

        Session()->flash('success', __('Status successfully updated'));
        return redirect()->route('status.index'); 
    }

    
    public function checkStatusDelete(Request $request) 
    {  
        $external_id = $request->external_id; 
        
        $status = $this->findByExternalId($external_id);

        $enquiry_count = Enquiry::where('status_id', $status->id)->count(); 
        $appointment_count = Appointment::where('status_id', $status->id)->count(); 

        if($enquiry_count === 0 && $appointment_count === 0) {
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Sorry this status is in use!",
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxStatusDelete(Request $request)
    {
        $external_id = $request->external_id;  
        $status = $this->findByExternalId($external_id);
        $status->delete();

        Session()->flash('success', __('Status successfully deleted!'));
        return response()->json([
            'status' => true,
            'message' => "status deleted successfully!"
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

        Session()->flash('success', __('Status successfully deleted'));
        return redirect()->route('enquirytype.index');
    }
 
    public function findByExternalId($external_id)
    {
        return Status::where('external_id', $external_id)->firstOrFail();
    }
}
