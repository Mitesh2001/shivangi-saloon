<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Enums\Country as CountryEnum;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Status;
use App\Models\Task;
use App\Repositories\FilesystemIntegration\FilesystemIntegration;
use App\Repositories\Money\MoneyConverter;
use App\Services\ClientNumber\ClientNumberService;
use App\Services\Invoice\InvoiceCalculator;
use App\Services\Search\SearchService;
use App\Services\Storage\GetStorageProvider;
use Carbon\Carbon;
use Config;
use Dinero;
use Datatables;
use App\Models\Client;
use App\Models\Order;
use App\Models\Contact;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Requests\Client\StoreClientBasicRequest;
use App\Models\User;
use App\Models\Branch;
use App\Models\Integration;
use App\Models\Industry;
use App\Models\Enquiry;
use App\Models\Appointment;
use Ramsey\Uuid\Uuid;
use App\Models\ClientTimeline;

use App\Models\State;
use App\Models\Country;

use App\Helpers\Helper;
use App\Models\Distributor;

class ClientsController extends Controller
{
    const CREATED = 'created';
    const UPDATED_ASSIGN = 'updated_assign';

    protected $users;
    protected $clients;
    protected $settings;
    /**
     * @var FilesystemIntegration
     */
    private $filesystem;


    public function __construct()
    {
        $this->middleware('permission:client-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:client-create', ['only' => ['create','store']]);
		$this->middleware('permission:client-update', ['only' => ['edit','update']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['is_system_user'] = Helper::is_system_user();
        $data['all_status'] = $this->getAllStaticStatus();
        $data['status_wise'] = 0; // ClientTimelineController
        // Status wise data attribute for (timeline controller) to show specific data

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        }

        return view('clients.index')->with($data);
    }

    public function getAllStaticStatus()
    {
        return [
            'New Clients',
            'Repeating Clients',
            'Regular Clients',
            'Never Visited',
            'No Risk',
            'Dormant Clients',
            'At Risk',
            'Lost Clients',
        ];
    }

    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {
        $clients = Client::with(['getDistributor','appointments','orders'])->withCount(['appointments']);

        $distributor_id = Helper::getDistributorId();
        if($distributor_id != 0) { // Check if distributor
            $clients->where('distributor_id', $distributor_id);
        }

        $clients = $clients->orderBy('id', 'desc')->get();

        return Datatables::of($clients)
            ->addColumn('namelink', function ($clients) {
                $url = url('admin/clients/' . $clients->external_id);
                return '<a data-search="' . $clients->name . '" href="'.$url.'" ">' . $clients->name . '</a>';
            })
            ->addColumn('distributor', function ($branch) {
                return  $branch->getDistributor->name ?? "";
            })
            ->addColumn('email', function ($clients) {
                return $clients->primaryContact->email ?? "";
            })
            ->addColumn('gender', function ($clients) {
                return $clients->gender ?? "";
            })
            ->addColumn('primary_number', function ($clients) {
                return $clients->primaryContact->primary_number ?? "";
            })
            ->addColumn('secondary_number', function ($clients) {
                return $clients->primaryContact->secondary_number ?? "";
            })
            ->addColumn('date_of_birth', function ($clients) {
                if(!empty($clients->date_of_birth) && $clients->date_of_birth != "0000-00-00") {
                    return date('d-m-Y', strtotime($clients->date_of_birth));
                } else {
                    return "";
                }
            })
            ->addColumn('anniversary', function ($clients) {
                if(!empty($clients->anniversary) && $clients->anniversary != "0000-00-00") {
                    return date('d-m-Y', strtotime($clients->anniversary));
                } else {
                    return "";
                }
            })
            ->addColumn('city', function ($clients) {
                return $clients->city ?? "";
            })
            ->addColumn('zipcode', function ($clients) {
                return $clients->zipcode ?? "";
            })
            ->addColumn('client_type', function ($clients) {
                return $clients->client_type;
            })
			->addColumn('total_appointments', function ($clients) {
                return $clients->appointments_count;
            })
			->addColumn('total_sales', function ($clients) {
				$data=Order::where('client_id','=',$clients->id)->sum('final_amount');
				return $data;
            })
			->addColumn('outstanding', function ($clients) {
                $data=Order::where('client_id','=',$clients->id)->where('is_payment_pending','=','YES')->sum('final_amount');
				return $data;
            })
			->addColumn('last_appointment', function ($clients) {
				$data=Appointment::where('client_id','=',$clients->id)->orderBy('id', 'desc')->first();
				if($data)
					return date('d-m-Y h:i A', strtotime($data->start_at));
				else
					return '';
            })
			->addColumn('added_date', function ($clients) {
                return date('d-m-Y',strtotime($clients->created_at));
            })
            ->addColumn('view', '
                <a href="{{ route(\'clients.show\', $external_id) }}" class="btn btn-link" >'  . __('View') . '</a>')
            ->addColumn('action', function ($clients) {

                $url = url('admin/clients/' . $clients->external_id);
				$html = '<form class="d-flex" action="'.route('clients.destroy', $clients->external_id).'" method="POST">';
                // $html .= '<a href="'.$url.'" class="btn btn-link"><i class="flaticon-eye text-primary text-hover-primary text-hover-primary" data-toggle="tooltip" title="View Details"></i></a>';
				// if(\Entrust::can('client-update'))
				// $html .= '<a href="'.route('clients.edit', $clients->external_id).'" class="btn btn-link" data-toggle="tooltip" title="Edit Client"><i class="flaticon2-pen text-primary text-hover-primary text-hover-primary"></i></a>';
				$html .= '<input type="hidden" name="_method" value="DELETE">';
				if(\Entrust::can('client-delete'))
				// $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-client" data-toggle="tooltip" title="Delete Client"><i class="flaticon2-trash text-danger text-hover-warning text-hover-warning"></i></button>';
                $html .= '<input type="hidden" class="client_id" value="'.$clients->external_id.'">';
				$html .= csrf_field();
                if(\Entrust::can('client-update'))
                    $html .= '<div class="dropdown"><button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button> <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="">';
                if(\Entrust::can('client-view'))
                    $html .=    '<a href="'.route('clients.show', $clients->external_id).'" class="dropdown-item">View Details</a>';
                if(\Entrust::can('client-update') && !Helper::allowViewOnly($clients->distributor_id))
                    $html .=    '<a href="'.route('clients.edit', $clients->external_id).'" class="dropdown-item">Edit Client</a>';
                if(\Entrust::can('order-view'))
                    $html .=    '<a href="'.route("orders.index", ['client_id' => encrypt($clients->id)]) .'" class="dropdown-item">Orders</a>';

                $html .= '</div>
                        </div>';
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['namelink', 'email', 'gender', 'primary_number', 'assigned_user','client_type','total_appointments','total_sales','outstanding','added_date','action'])
            ->make(true);
    }

    public function taskDataTable($external_id)
    {
        $client = Client::where('external_id', $external_id)->firstOrFail();
        $tasks = $client->tasks()->with(['status'])->select(
            ['id', 'external_id', 'title', 'created_at', 'deadline', 'user_assigned_id', 'client_id', 'status_id']
        )->get();


        return Datatables::of($tasks)
            ->addColumn('titlelink', function ($tasks) {
                return '<a href="' . route('tasks.show', $tasks->external_id) . '">' . $tasks->title . '</a>';
            })
            ->editColumn('created_at', function ($tasks) {
                return $tasks->created_at ? with(new Carbon($tasks->created_at))
                    ->format(carbonDate()) : '';
            })
            ->editColumn('deadline', function ($tasks) {
                return $tasks->deadline ? with(new Carbon($tasks->deadline))
                    ->format(carbonDate()) : '';
            })
            ->editColumn('status_id', function ($tasks) {
                return '<span class="label label-success" style="background-color:' . $tasks->status->color . '"> ' .$tasks->status->title . '</span>';
            })
            ->editColumn('assigned', function ($tasks) {
                return $tasks->assigned_user->name;
            })
            ->rawColumns(['titlelink','status_id'])
            ->make(true);
    }

    public function projectDataTable($external_id)
    {
        $client = Client::where('external_id', $external_id)->firstOrFail();
        $projects = $client->projects()->with(['status'])->select(
            ['id', 'external_id', 'title', 'created_at', 'deadline', 'user_assigned_id', 'client_id', 'status_id']
        )->get();

        return Datatables::of($projects)
            ->addColumn('titlelink', function ($projects) {
                return '<a href="' . route('projects.show', $projects->external_id) . '">' . $projects->title . '</a>';
            })
            ->editColumn('created_at', function ($projects) {
                return $projects->created_at ? with(new Carbon($projects->created_at))
                    ->format(carbonDate()) : '';
            })
            ->editColumn('deadline', function ($projects) {
                return $projects->deadline ? with(new Carbon($projects->deadline))
                    ->format(carbonDate()) : '';
            })
            ->editColumn('status_id', function ($projects) {
                return '<span class="label label-success" style="background-color:' . $projects->status->color . '"> ' .$projects->status->title . '</span>';
            })
            ->editColumn('assigned', function ($projects) {
                return $projects->assignee->name;
            })
            ->rawColumns(['titlelink','status_id'])
            ->make(true);
    }

    public function leadDataTable($external_id)
    {
        $client = Client::where('external_id', $external_id)->firstOrFail();
        $leads = $client->leads()->with(['status'])->select(
            ['id', 'external_id', 'title', 'created_at', 'deadline', 'user_assigned_id', 'client_id', 'status_id']
        )->get();
        return Datatables::of($leads)
            ->addColumn('titlelink', function ($leads) {
                return '<a href="' . route('leads.show', $leads->external_id) . '">' . $leads->title . '</a>';
            })
            ->editColumn('created_at', function ($leads) {
                return $leads->created_at ? with(new Carbon($leads->created_at))
                    ->format(carbonDate()) : '';
            })
            ->editColumn('deadline', function ($leads) {
                return $leads->deadline ? with(new Carbon($leads->deadline))
                    ->format(carbonDate()) : '';
            })
            ->editColumn('status_id', function ($leads) {
                return '<span class="label label-success" style="background-color:' . $leads->status->color . '"> ' .
                    $leads->status->title . '</span>';
            })
            ->editColumn('assigned', function ($leads) {
                return $leads->assigned_user->name;
            })
            ->rawColumns(['titlelink','status_id'])
            ->make(true);
    }

    public function invoiceDataTable($external_id)
    {
        $client = Client::where('external_id', $external_id)->firstOrFail();

        $invoices = $client->invoices()->select(
            ['id', 'external_id', 'sent_at', 'status', 'invoice_number']
        );

        return Datatables::of($invoices)
            ->editColumn('invoice_number', function ($invoices) {
                return '<a href="' . url('invoices', $invoices->external_id) . '">' . ($invoices->invoice_number ?: 'X') . '</a>';
            })
            ->editColumn('total_amount', function ($invoices) {
                $totalPrice = app(InvoiceCalculator::class, ['invoice' => $invoices])->getTotalPrice();
                return app(MoneyConverter::class, ['money' => $totalPrice])->format();
            })
            ->editColumn('invoice_sent', function ($invoices) {
                return $invoices->sent_at ? __('yes'): __('no');
            })
            ->editColumn('status', function ($invoices) {
                return __(InvoiceStatus::fromStatus($invoices->status)->getDisplayValue());
            })
            ->rawColumns(['invoice_number'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        $data['users'] = User::all();
        $data['countries'] = Country::where('deleted', 0)->pluck('name', 'country_id');
        $data['states'] = State::where('country_id', '101')->where('deleted', 0)->pluck('name', 'state_id');
        $data['industries'] = $this->listAllIndustries();
        $data['is_system_user'] = Helper::getDistributorId();

        return view('clients.create')->with($data);
    }

    /**
     * @param StoreClientRequest $request
     * @return mixed
     */
    public function store(StoreClientRequest $request)
    {
        $is_system_user = Helper::getDistributorId();
        if($is_system_user == 0) { // is admin
            $distributor_id = $request->distributor_id;
        } else {
            $distributor_id = $is_system_user; // Current user distributor id
        }

        if(Helper::allowViewOnly($distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        $store_data = [
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'date_of_birth' => $request->date_of_birth,
            'anniversary' => $request->anniversary,
            'company_name' => $request->company_name,
            'address' => $request->address,
            'zipcode' => $request->zipcode,
            'city' => $request->city,
            'country_id' => $request->country_id,
            'company_type' => $request->company_type,
            'industry_id' => $request->industry_id,
            'distributor_id' => $distributor_id,
            // 'user_id' => $request->user_id,
            'client_number' => app(ClientNumberService::class)->setNextClientNumber(),
            'client_type' => $request->client_type,
            'notes' => $request->notes,
            'gender' => $request->gender,
            'allow_notifications' => $request->allow_notifications ? 1 : 0,
        ];

        if($request->country_id == 101){
            $store_data['state_id'] = $request->state_id;
            $store_data['state_name'] = "";
        }else{
            $store_data['state_id'] = "";
            $store_data['state_name'] = $request->state_name;
        }
        $client = Client::create($store_data);

        $contact = Contact::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'email' => $request->email,
            'primary_number' => $request->primary_number,
            'secondary_number' => $request->secondary_number,
            'client_id' => $client->id,
            'is_primary' => true,
            'distributor_id' => $distributor_id,
        ]);

        Session()->flash('success', __('Client successfully added'));
        // event(new \App\Events\ClientAction($client, self::CREATED));
        return redirect()->route('clients.index');
    }

    /**
     *  Store clients basic info (ajax for enquiry master)
     *
     * @param StoreClientBasicRequest $request
     * @return mixed
     */
    public function storeBasic(Request $request)
    {

        $client_data = array();
        parse_str($request->data, $client_data);

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $client_data['distributor_id'];
        }

        $client = Client::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $client_data['name'],
            'address' => $client_data['address'],
            'company_name' => "",
            'user_id' => 1, // Problem
            'industry_id' => 1, // Problem
            'client_number' => app(ClientNumberService::class)->setNextClientNumber(),
            'distributor_id' => $distributor_id,
            'gender' => $client_data['gender'],
        ]);

        $contact = Contact::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => $client_data['name'],
            'email' => $client_data['email'],
            'primary_number' => $client_data['primary_number'],
            'client_id' => $client->id,
            'is_primary' => true,
            'distributor_id' => $distributor_id,
        ]);

        return response()->json([
            'status' => true,
            'message' => "Client created successfully!",
            'data' => [
                'id' => $client->id,
                'name' => $client_data['name'] ?? "",
                'email' => $client_data['email'] ?? "",
                'contact_number' => $client_data['primary_number'] ?? "",
                'address' => $client->address ?? "",
                'gender' => $client->gender ?? "",
            ]
        ]);
    }

    /**
     * @param Request $vatRequest
     * @return mixed
     */
    public function cvrapiStart(Request $request)
    {
        $vat = $request->input('vat');

        $country = $request->input('country');
        $company_name = $request->input('company_name');

        // Strip all other characters than numbers
        $vat = preg_replace('/[^0-9]/', '', $vat);

        $result = $this->cvrApi($vat, 'dk');


        return redirect()->back()
            ->with('data', $result);
    }

    public function cvrApi($vat)
    {
        if (empty($vat)) {
            // Print error message

            return ('Please insert VAT');
        } else {
            // Start cURL
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, 'http://cvrapi.dk/api?search=' . $vat . '&country=dk');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Daybyday');

            // Parse result
            $result = curl_exec($ch);

            // Close connection when done
            curl_close($ch);

            // Return our decoded result
            return json_decode($result, 1);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $external_id
     * @return mixed
     */
    public function show(Request $request,$external_id)
    {
        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // system user
            $data['client'] = $this->findByExternalId($external_id);
        } else {
            $data['client'] = Client::where('external_id', $external_id)->where('distributor_id', $distributor_id)->firstOrFail();
        }

        $data['ref'] = $request->get('ref');
        $data['enquiry_count'] = $data['client']->get_enquiry->count();
        $data['appointment_count'] = $data['client']->appointments->count();
        $data['assinged_user'] = $data['client']->user;

        $payment_modes = config('global.payment_modes');
        $data['payment_modes'] = array_merge(array(''=>'Payment Mode'),$payment_modes);
        $data['client_id'] = encrypt($data['client']->id);
        $data['branch_data'] = $this->getBranch();
        $data['branches'] = Branch::pluck('name', 'name')->toArray();
        $data['export_title'] =  " ND Salon Software | Orders of ". $data['client']->name;

        $data['is_system_user'] = Helper::is_system_user();

        $data['allow_view_only'] = Helper::allowViewOnly($data['client']->distributor_id);

        // Appointment Images
        $data['appointments'] = Appointment::with('getImages')
        ->has('getImages')
        ->where('client_id', $data['client']->id)
        ->get();

        return view('clients.show')->with($data);
    }

    // return current user branch
    public function getBranch()
    {
        $branch_id = Auth::user()->branch_id ?? 0 ;
        $branch = Branch::find($branch_id);
        return $branch;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $external_id
     * @return mixed
     */
    public function edit($external_id)
    {
        $is_system_user = Helper::getDistributorId();

        // Only system user can access others data
        if($is_system_user == 0) {
            $client = $this->findByExternalId($external_id);
        } else {
            $client = Client::where('external_id', $external_id)->where('distributor_id', $is_system_user)->firstOrFail();
        }
		if($client->primaryContact){
        $contact = $client->primaryContact;
        $data['client'] = (object)array_merge($contact->toArray(), $client->toArray());
		}else{
			 $data['client'] = (object)$client->toArray();
		}
        $data['user'] = User::select('first_name', 'last_name', 'id')->where('id', $client->user_id)->first();
        $data['states'] = State::where('country_id', '101')->where('deleted', 0)->pluck('name', 'state_id');
        $data['countries'] = Country::where('deleted', 0)->pluck('name', 'country_id');
        $data['industry'] = Industry::where('id', $client->industry_id)->pluck('name', 'id');
        $data['is_system_user'] = $is_system_user;
        $data['distributor'] = Distributor::find($data['client']->distributor_id); // current record distributor name (for admin)

        return view('clients.edit')->with($data);
    }

    /**
     * @param $external_id
     * @param UpdateClientRequest $request
     * @return mixed
     */
    public function update($external_id, UpdateClientRequest $request)
    {
        // dd($request->anniversary);
        $client = $this->findByExternalId($external_id);

        if(Helper::allowViewOnly($client->distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        $update_arr = [
            'name' => $request->name,
            'company_name' => $request->company_name,
            'date_of_birth' => $request->date_of_birth,
            'anniversary' => $request->anniversary,
            'address' => $request->address,
            'zipcode' => $request->zipcode,
            'city' => $request->city,
            'country_id' => $request->country_id,
            'company_type' => $request->company_type,
            'industry_id' => $request->industry_id,
            // 'user_id' => $request->user_id,
            'client_type' => $request->client_type,
            'notes' => $request->notes,
            'gender' => $request->gender,
            'allow_notifications' => $request->allow_notifications ? 1 : 0,
        ];

        if($request->country_id == 101){
            $update_arr['state_id'] = $request->state_id;
            $update_arr['state_name'] = "";
        }else{
            $update_arr['state_id'] = "";
            $update_arr['state_name'] = $request->state_name;
        }


        $client->fill($update_arr)->save();

        $client->primaryContact->fill([
            'name' => $request->name,
            'email' => $request->email,
            'primary_number' => $request->primary_number,
            'secondary_number' => $request->secondary_number,
            'client_id' => $client->id,
            'is_primary' => true
        ])->save();

        
        $appoinments = $client->appointments;
        
        foreach ($appoinments as $key => $appoinment) {
            $appoinment->update([
                'email' => $client->primaryContact->email,
                'contact_number' => $client->primaryContact->primary_number
            ]);
        }
        
        Session()->flash('success', __('Client successfully updated'));
        return redirect()->route('clients.index');
    }


    public function checkClientDelete(Request $request)
    {
        $external_id = $request->external_id;

        $client = $this->findByExternalId($external_id);

        // $client_count = Enquiry::where('client_id', $client->id)->count();
        $appointment_count = Appointment::where('client_id', $client->id)->where('date', '>=', Carbon::today())->count();

        if($appointment_count == 0) {
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Sorry this client has appointments in future, Please remove future appointment to remove this client!s",
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxClientDelete(Request $request)
    {
        $external_id = $request->external_id;
        $client = $this->findByExternalId($external_id);
        $client->delete();

        Session()->flash('success', __('Client successfully deleted!'));
        return response()->json([
            'status' => true,
            'message' => "Client deleted successfully!"
        ]);
    }

    /**
     * @param $external_id
     * @return mixed
     */
    public function destroy($external_id)
    {
        try {
            $client = $this->findByExternalId($external_id);
            $client->delete();
            Session()->flash('success', __('Client successfully deleted'));
        } catch (\Exception $e) {
            Session()->flash('success_warning', __('Client could not be deleted, contact Daybyday support'));
        }

        return redirect()->route('clients.index');
    }

    /**
     * @param $external_id
     * @param Request $request
     * @return mixed
     */
    public function updateAssign($external_id, Request $request)
    {
        if (!auth()->user()->can('client-update')) {
            Session()->flash('success_warning', __("Not authorized"));
            return back();
        }

        $user = User::where('external_id', $request->user_external_id)->first();
        $client = Client::with('user')->where('external_id', $external_id)->first();
        $client->updateAssignee($user);

        Session()->flash('success', __('New user is assigned'));
        return redirect()->back();
    }


    /**
     * @param $client
     * @return mixed
     */
    public function getInvoices($client)
    {
        $invoice = $client->invoices()->with('invoiceLines')->get();

        return $invoice;
    }

    public function findByExternalId($external_id)
    {
        return Client::where('external_id', $external_id)->firstOrFail();
    }

    public function findById(Request $request)
    {
        $id = $request->id;
        $clients = Client::with('primaryContact')->where('id', $id)->firstOrFail();

        $address_arr = [];

        if(!empty($clients->address)) {
            array_push($address_arr, $clients->address);
        }

        if(!empty($clients->city)) {
            array_push($address_arr, $clients->city);
        }

        if(!empty($clients->zipcode)) {
            array_push($address_arr, $clients->zipcode);
        }

        $data = [
            'contact_number' => $clients->primaryContact->primary_number,
            'gender' => $clients->gender,
            'email' => $clients->primaryContact->email,
            'address' => implode(', ', $address_arr),
        ];

        return response()->json($data);
    }

    /**
     * @return mixed
     */
    public function listAllClients()
    {
        return Client::pluck('company_name', 'id');
    }

    /**
     * @return int
     */
    public function getAllClientsCount()
    {
        return Client::all()->count();
    }

    /**
     * @return mixed
     */
    public function listAllIndustries()
    {
        return Industry::pluck('name', 'id');
    }


    /**
     *  @return mixed
     *  $industries
     */
    public function getIntustryByName(Request $request)
    {
        $name = $request->get('q');
        $industries = Industry::where('name', 'like', "%{$name}%")->get();

        return response()->json($industries);
    }

    /**
     *  Client timeline setup view
     */
    public function clienTimelineView()
    {
        $data['clients'] = Client::all();
        return view('clients.timeline.timeline_setup');
    }

    /**
     * Update clients timeline
     */
    public function clientTimelineUpdate(Request $request)
    {
        dd($request->repeat_clients);
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

        $contact = Contact::where('primary_number', $number)->where('distributor_id', $distributor_id)->first();

        if(!empty($contact)) {
            if($contact->client_id == $id) {
                echo  "true";
            } else {
                echo  "false";
            }
        } else {
            echo "true";
        }
    }

    // Check repeat email of client
    public function checkEmailRepeat(Request $request)
    {
        $id = $request->id;
        $email = $request->email;

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // system user
            $distributor_id = $request->distributor_id;
        }
        $contact = Contact::where('email', $email)->where('distributor_id', $distributor_id)->first();

        if(!empty($contact)) {
            if($contact->client_id == $id) {
                echo  "true";
            } else {
                echo  "false";
            }
        } else {
            echo "true";
        }
    }

    /**
     *  @return mixed
     *  $users
     */
    public function getClientByName(Request $request)
    {
        $name = $request->get('q');
        $clients = Client::where('name', 'like', "%{$name}%")->where('distributor_id', 0)->get();

        return response()->json($clients);
    }
}
