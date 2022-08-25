<?php
namespace App\Http\Controllers;

use App\Models\Invoice;
use DB;
use Auth;
use Carbon;
use Session;
use Datatables;
use App\Models\Lead;
use App\Models\User;
use App\Models\Client;
use App\Models\Branch;
use App\Http\Requests;
use App\Models\Status;
use App\Models\Setting;
use App\Models\EnquiryType;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use App\Http\Requests\Lead\StoreLeadRequest;
use App\Http\Requests\Lead\UpdateLeadFollowUpRequest;
use Ramsey\Uuid\Uuid;

use App\Helpers\Helper;
use App\Models\Distributor;

class LeadsController extends Controller
{
    const CREATED = 'created';
    const UPDATED_STATUS = 'updated_status';
    const UPDATED_DEADLINE = 'updated_deadline';
    const UPDATED_ASSIGN = 'updated_assign';

        
    public function __construct()
    {
        $this->middleware('permission:inquiry-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:inquiry-create', ['only' => ['create','store']]);
		$this->middleware('permission:inquiry-update', ['only' => ['edit','update']]); 
    }
 
    public function index()
    {
        $data['statuses'] = Status::all();
        $data['enquiry_types'] = EnquiryType::all();
        $data['branches'] = Branch::all();
        $data['is_system_user'] = Helper::is_system_user(); 
        $data['distributors'] = Distributor::all();

        $distributor_id = Helper::getDistributorId();

        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        } 
        
        return view('leads.index')->with($data);
    }
    
    public function allLeads()
    {
        $leads = Lead::with(['user', 'status', 'client'])->select('leads.*')->get();

        return Datatables::of($leads)
            ->addColumn('titlelink', function ($leads) {
                 return '<a href="'.route('leads.show', $leads->external_id).'">'.$leads->client->company_name.'</a>';
            })
            ->editColumn('status_id', function ($leads) {
                if(!empty($leads->status->color)) {
                    return '<span class="label label-success" style="background-color:' . $leads->status->color . '"> </span>';//' .$leads->status->title . '
                } else {
                    return '';
                } 
            })
            ->addColumn('view', function ($leads) {
                return '<a href="' . route("leads.show", $leads->external_id) . '" class="btn btn-link" data-toggle="tooltip" title="View Details"><i class="flaticon-eye text-primary text-hover-primary"></i></a>'
                . '<a data-toggle="modal" data-id="'. route('leads.destroy',$leads->external_id) . '" data-title="'. $leads->title . '" data-target="#deletion" class="btn btn-link"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';
            })
            ->rawColumns(['titlelink','email','contact_number','date_to_follow','enquiry_type','enquiry_for', 'status_id','view'])
            ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unqualified()
    {
        return view('leads.unqualified')->withStatuses(Status::typeOfLead()->get());
    }

    /**
     * Data for Data tables
     * @return mixed
     */
    public function unqualifiedLeads()
    {
        $status_id = Status::typeOfLead()->where('title', 'Closed')->first()->id;
        $leads = Lead::isNotQualified()
            ->where('status_id', '!=', $status_id)
            ->with(['user', 'creator', 'client.primaryContact'])->get();

        $leads->map(function ($item) {
            return [$item['visible_deadline_date'] = $item['deadline']->format(carbonDate()), $item["visible_deadline_time"] = $item['deadline']->format(carbonTime())];
        });
        return $leads->toJson();
    }

    /**
     *  Return data for index page datatable
     *
     * @return \Illuminate\Http\Response
     */
    public function allData()
    {   
        $user = Auth::user(); 
        $enquiries = Enquiry::with(['getDistributor'])->select('enquiries.*')->join('clients', 'clients.id', '=', 'enquiries.client_id')->where('clients.deleted_at', null);  
 
        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id != 0) { // Check if distributor
            $enquiries->where('enquiries.distributor_id', $distributor_id);
        } 

        if($user->roles[0]->name == "employee") {
            $enquiries->where('branch_id', $user->branch_id);
        } 

        $enquiries = $enquiries->orderBy('date_to_follow', 'desc')->get();
 
        return Datatables::of($enquiries)
            ->addColumn('client_name', function ($enquiries) {
                 return $enquiries->client->name ?? "";
            })
            ->addColumn('distributor', function ($branch) {
                return  $branch->getDistributor->name ?? "";
            }) 
            ->addColumn('contact', function ($enquiries) {
                 return $enquiries->contact_number ?? "";
            })
            ->addColumn('email', function ($enquiries) {
                 return $enquiries->email ?? ""; 
            })
            ->addColumn('enquiry_for', function ($enquiries) {
                 return $enquiries->enquiry_for; 
            })
            ->addColumn('enquiry_type', function ($enquiries) {
                 return $enquiries->get_enquiry_type->name ?? ""; 
            })
            ->addColumn('date_to_follow', function ($enquiries) { 
                return $enquiries->date_to_follow;
            })  
            ->addColumn('branch', function ($enquiries) {
                return $enquiries->getBranch->name ?? "";
                // return $enquiries->status->title; 
            }) 
            ->addColumn('stage', function ($enquiries) {
                return $enquiries->stage; 
                // return $enquiries->status->title; 
            }) 
            ->addColumn('status', function ($enquiries) {
                if(!empty($enquiries->status->color)) {
                    return '<span class="label label-inline label-lg font-weight-bolder" style="background-color:'. $enquiries->status->color .';color:white;padding:5px!important">'.$enquiries->status->title.'</span>'; 
                } else {
                    return "";
                } 
                // return $enquiries->status->title; 
            }) 
            ->addColumn('action', function ($enquiries) { 
                $html = "<div class='d-flex' >";
                if(\Entrust::can('inquiry-view'))
                    $html .= '<a href="#" class="view-in-modal btn btn-link" data-toggle="modal" data-enquiry-id="'.$enquiries->external_id.'" data-target="#view-enquiry-modal" data-toggle="tooltip" title="View Details"><i class="flaticon-eye text-primary text-hover-primary" data-enquiry-id="'.$enquiries->external_id.'" data-toggle="tooltip" title="View Details"></i></a>';

                if(\Entrust::can('inquiry-update') && $enquiries->stage != "appointment" && !Helper::allowViewOnly($enquiries->distributor_id))
                    $html .= '<a href="'.route('appointments.create').'?inquiry_id='. $enquiries->external_id .'" class="view-in-modal btn btn-link" title="Convert to appointment"><i class="flaticon2-copy text-primary text-hover-primary" data-enquiry-id="'.$enquiries->external_id.'" data-toggle="tooltip" title="Convert to Appointment"></i></a>';
                $html .= "</div>";

                return $html;
            })
            ->rawColumns(['client_name', 'distributor', 'contact', 'email', 'enquiry_for', 'enquiry_type', 'date_to_follow', 'stage', 'status', 'branch', 'action'])
            ->make(true); 
    }

    
    /**
     *  Return json format data of enquiry.
     *
     * @return \Illuminate\Http\Response
     */
    public function datailById(Request $request)
    {
        $enquiry = Enquiry::where('external_id', $request->external_id)->first(); 
 
        $data = [
            'external_id' => $enquiry->external_id,
            'client_name' => $enquiry->client->name,
            'gender' => $enquiry->gender,
            'contact_number' => $enquiry->contact_number ?? "",
            'email' => $enquiry->email ?? "",
            'address' => $enquiry->address,
            'enquiry_for' => $enquiry->enquiry_for ?? "",
            'enquiry_type' => $enquiry->get_enquiry_type->name,
            'enquiry_response' => $enquiry->enquiry_response,
            'date_to_follow' => date('d-m-Y' ,strtotime($enquiry->date_to_follow)),
            'source_of_enquiry' => $enquiry->source_of_enquiry,
            'lead_representative' => $enquiry->user->first_name ." ". $enquiry->user->last_name,
            'lead_status' => $enquiry->status->id ?? "",
            'description' => $enquiry->description,
            'distributor' => $enquiry->getDistributor->name,
        ];

        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {       
        $data['users'] = User::all(); 
        $data['statuses'] = Status::pluck('title', 'id');
        $data['enquiry_types'] = EnquiryType::pluck('name', 'id');
        $data['branch'] = Branch::pluck('name', 'id')->toArray();
        $data['is_system_user'] = Helper::getDistributorId();  

        $client = $request->get('client') ?? 0;
        if($client !== 0) {
            $data['client'] = Client::where('external_id', $request->get('client'))->first();
            $data['selected_distributor'] = $data['client']->getDistributor;
        }
  
        return view('leads.create')->with($data);
    }
 
    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLeadRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLeadRequest $request)
    {      
        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;
        }

        if(Helper::allowViewOnly($distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        $client = Client::where('id', $request->client_external_id)->first();
         
        $client_id = $client->id;
        $client_name = $client->name; 

        $user_id = Auth::id();  

        $enquiry = Enquiry::create([
            'external_id' => Uuid::uuid4()->toString(),
            'client_id' => $client_id, 
            'client_name' => $client_name, 
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'address' => $request->address,
            'description' => $request->description,
            'branch_id' => $request->branch_id,
            'enquiry_for' => $request->enquiry_for ?? "",
            'enquiry_type' => $request->enquiry_type,
            'enquiry_response' => $request->enquiry_response,
            'date_to_follow' => $request->date_to_follow,
            'source_of_enquiry' => $request->enquiry_source,
            'user_assigned_id' => $request->user_assigned_id,
            'status_id' => $request->status_id,             
            'created_by' => $user_id,      
            'distributor_id' => $distributor_id,    
            'gender' => $request->gender,   
        ]);
        
        Session()->flash('success', __('Inquiry successfully added!'));
        return redirect()->route('leads.index');
    }

    public function updateStatus(Request $request) {

        $external_id = $request->external_id;  
        $status_id = $request->status_id;

        $enquiry = Enquiry::where('external_id', $external_id)->firstOrFail(); 

        if(Helper::allowViewOnly($enquiry->distributor_id)) { 
            return response()->json([
                'status' => false,
                'message' => "Subscription has been expired. please renew!"
            ]);
        }
        
        $res = $enquiry->fill([
            'status_id' => $status_id,
        ])->save();

        return response()->json([
            'status' => true,
            'message' => "Inquiry status updated successfully!"
        ]);
    }

    public function destroy(Lead $lead, Request $request)
    {
        $deleteInvoice = $request->delete_invoice ? true : false;
        if($lead->invoice && $deleteInvoice) {
            $lead->invoice()->delete();
        } elseif($lead->invoice) {
            $lead->invoice->removeReference();
        }
        $lead->delete();
        
        Session()->flash('success', __('Lead deleted'));
        return redirect()->back();
    }

    public function updateAssign($external_id, Request $request)
    {
        $lead = $this->findByExternalId($external_id);
        $input = $request->get('user_assigned_id');
        $input = array_replace($request->all());
        $lead->fill($input)->save();
        $insertedName = $lead->user->name;

        event(new \App\Events\LeadAction($lead, self::UPDATED_ASSIGN));
        Session()->flash('success', __('New user is assigned'));
        return redirect()->back();
    }

    /**
     * Update the follow up date (Deadline)
     * @param UpdateLeadFollowUpRequest $request
     * @param $external_id
     * @return mixed
     */
    public function updateFollowup(UpdateLeadFollowUpRequest $request, $external_id)
    {
        if (!auth()->user()->can('lead-update-deadline')) {
            session()->flash('success_warning', __('You do not have permission to change task deadline'));
            return redirect()->route('tasks.show', $external_id);
        }
        $lead = $this->findByExternalId($external_id);
        $lead->fill(['deadline' => Carbon::parse($request->deadline . " " . $request->contact_time . ":00")])->save();
        event(new \App\Events\LeadAction($lead, self::UPDATED_DEADLINE));
        Session()->flash('success', __('New follow up date is set'));
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $external_id
     * @return \Illuminate\Http\Response
     */
    public function show($external_id)
    {
        return view('leads.show')
            ->withLead($this->findByExternalId($external_id))
            ->withUsers(User::with(['department'])->get()->pluck('nameAndDepartmentEagerLoading', 'id'))
            ->withCompanyname(Setting::first()->company)
            ->withStatuses(Status::typeOfLead()->pluck('title', 'id'));
    }

    /**
     * Complete lead
     * @param $external_id
     * @param Request $request
     * @return mixed
     */
    // public function updateStatus($external_id, Request $request)
    // {
    //     if (!auth()->user()->can('lead-update-status')) {
    //         session()->flash('success_warning', __('You do not have permission to change lead status'));
    //         return redirect()->route('tasks.show', $external_id);
    //     }
    //     $lead = $this->findByExternalId($external_id);
    //     if (isset($request->closeLead) && $request->closeLead === true) {
    //         $lead->status_id = Status::typeOfLead()->where('title', 'Closed')->first()->id;
    //         $lead->save();
    //     } else {
    //         $lead->fill($request->all())->save();
    //     }
    //     event(new \App\Events\LeadAction($lead, self::UPDATED_STATUS));
    //     Session()->flash('success', __('Lead status updated'));
    //     return redirect()->back();
    // }

    public function convertToQualifiedLead(Lead $lead)
    {
        Session()->flash('success', __('Lead status updated'));
        return $lead->convertToQualified();
    }


    public function convertToOrder(Lead $lead)
    {
        $invoice = $lead->convertToOrder();
        return $invoice->external_id;
    }
    /**
     * @param $external_id
     * @return mixed
     */
    public function findByExternalId($external_id)
    {
        return Lead::whereExternalId($external_id)->first();
    }

    
    /**
     *  @return mixed
     *  $industries
     */
    public function getClientsByName(Request $request)
    {
        $name = $request->get('name');

        $is_system_user = Helper::getDistributorId();
        if($is_system_user == 0) { //is system user
            $distributor_id = $request->distributor_id;
        } else {
            $distributor_id = $is_system_user;
        } 

        $clients = Client::where('name', 'like', "%{$name}%")->where('distributor_id', $distributor_id)->get();
         
        return response()->json($clients);
    }
    

    /**
     *  @return mixed
     *  $industries
     */
    public function getAllClientsByName(Request $request)
    {
        $name = $request->get('q'); 
        $clients = Client::where('name', 'like', "%{$name}%")->get()->unique('name'); 
        return response()->json($clients);
    }
    
    /**
     *  @return mixed
     *  $industries
     */
    public function getUsersByName(Request $request)
    { 
        $name = $request->get('name');
        $branch_id = $request->get('branch_id');
   
        $is_system_user = Helper::getDistributorId();
        if($is_system_user == 0) { //is system user
            $distributor_id = $request->distributor_id;
        } else {
            $distributor_id = $is_system_user;
        } 
 
        $users = User::where('name', 'like', "%{$name}%")->where('branch_id', $branch_id)->get(); 
        return response()->json($users);
    }
}
