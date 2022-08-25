<?php
namespace App\Http\Controllers;

use Gate;
use Datatables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Session;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Task;
use App\Models\Client;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Lead;
use App\Models\Role;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use Ramsey\Uuid\Uuid;

use App\Helpers\Helper;
use App\Models\Distributor;
use App\Models\UsersCommission;

class UsersController extends Controller
{
    protected $users;
    protected $roles;

    public function __construct()
    {
        $this->middleware('permission:employee-view', ['only' => ['index']]);
		$this->middleware('permission:employee-create', ['only' => ['create','store']]);
		$this->middleware('permission:employee-update', ['only' => ['edit','update']]);
    }

    /**
     * @return mixed
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
        $data['distributors'] = Distributor::all();

        // Distributor & system user can view users according to distributor id
        if(Helper::is_system_user() || Helper::is_distributor_user()) {
            $external_id = $request->get('distributor') ?? "";

            if(!empty($external_id)) {
                $distributor = Distributor::findByExternalId($external_id);

                $data['back_url'] = route('salons.index');
                $data['distributor_filter'] = 0;
                $data['distributor_title'] = $distributor->name;
                $data['distributor_id'] = $distributor->id;
                $data['distributor'] = $distributor;

                $data['allow_view_only'] = Helper::allowViewOnly($distributor->id);
                $data['can_create_user'] = Helper::canCreateUser($distributor->id);
            } else {
                $data['allow_view_only'] = false;
                $data['can_create_user'] = true;
            }

            // If user is distributor and trying to access index page of users listing
            if(Helper::is_distributor_user() && empty($external_id)) {
                return abort(403);
            }
        } else {
            $distributor_id = Helper::getDistributorId();
            $data['can_create_user'] = Helper::canCreateUser($distributor_id);
            $data['distributor_filter'] = 0;
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        }

        if(Helper::is_system_user()) {
            $data['roles'] = Role::pluck('name', 'id');
        } else {
            $data['roles'] = Role::whereIn('name', ['employee', 'administrator'])->pluck('name', 'id');
        }


        $data['branches'] = Branch::pluck('name', 'name')->toArray();

        return view('users.index')->with($data);
    }

    public function typeWiseFilter(Request $request)
    {
        $user_type = $request->get('user_type');
        $data['title'] = ucwords($user_type . " list");
        $data['back_url'] = route('dashboard');
        $data['user_type'] = $user_type;
        return view('users.user_type_filter')->with($data);
    }

    public function calendarUsers()
    {
        return User::with(['department', 'absences' =>  function ($q) {
            return $q->whereBetween('start_at', [today()->subWeeks(2)->startOfDay(), today()->addWeeks(4)->endOfDay()])
                      ->orWhereBetween('end_at', [today()->subWeeks(2)->startOfDay(), today()->addWeeks(4)->endOfDay()]);
        }
        ])->get();
    }

    public function users()
    {
        return User::with(['department'])->get();
    }

    public function anyData(Request $request)
    {
        // Master Admin login (System user)
        if(Helper::is_system_user() || Helper::is_distributor_user()) {
            $distributor_id = $request->get('distributor');
            $user_type = $request->get('user_type');
            $distributor = Distributor::find($distributor_id);

            $users = User::with(['getDistributor']);
            // Check Id
            if(!empty($distributor_id)) {
                $users->where('distributor_id', $distributor_id);
            } else {
                $users->where('user_type', '!=', 1);
            }

            if(!empty($user_type)) {
                if($user_type == "users") {
                    $users->where('user_type', 0);
                }
                if($user_type == "distributors") {
                    $users->where('user_type', 2);
                }
            }

            $users = $users->orderBy('id','desc')->get();

        } else { // Distributors
            $distributor = "";
            $distributor_id = Helper::getDistributorId();
            $users = User::with(['getDistributor'])->where('distributor_id', $distributor_id)->orderBy('id','desc')->get();
        }

        return Datatables::of($users)
            ->addColumn('namelink', function ($users) {
                $first_name = $users->first_name ?? "";
                $last_name = $users->last_name ?? "";
                return $first_name ." ". $last_name;
            })
            ->addColumn('name', function ($users) {
                return $users->first_name ." ". $users->last_name;
            })
            ->addColumn('distributor', function ($branch) {
                return  $branch->getDistributor->name ?? "";
            })
            ->addColumn('primary_number', function ($user) {
                return $user->primary_number;
            })
            ->addColumn('email', function ($user) {
                return $user->email;
            })
            ->addColumn('branch', function ($user) {
                return $user->getBranch->name ?? "";
            })
            ->addColumn('role', function ($user) {
                return $user->roles->first()->name ?? "";
            })
            ->addColumn('action', function ($user) use ($distributor) {

                $edit_url = route('users.edit', $user->external_id);
                $view_url = url('admin/users/' . $user->external_id);
                $commission_url = route('product.viewCommission', $user->external_id);

                if(!empty($distributor)) {
                    $edit_url .= "?distributor=". $distributor->external_id;
                    $view_url .= "?distributor=". $distributor->external_id;
                    $commission_url .= "?distributor=". $distributor->external_id;
                }


                $html = '<form action="'.route('users.destroy', $user->external_id).'" class="d-flex" method="POST">';


                $html .= '<div class="dropdown"> <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="">';

                                $html .= '<a href="'.$view_url.'" class="dropdown-item">View Details</a>';

                                if($user->user_type == 1) {
                                    if(\Entrust::can('employee-update') && !Helper::allowViewOnly($user->distributor_id))
                                    $html .= '<a href="'.$edit_url.'" class="dropdown-item">Edit Employee</a>';
                                    $html .= '<a href="'.$commission_url.'" class="dropdown-item">Set Commission</a>';
                                } else {
                                    $html .= '<a href="'.$edit_url.'" class="dropdown-item">Edit User</a>';
                                }

                                if(\Entrust::can('employee-delete') && $user->hasRole('owner') === false)
                                // $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-user"  data-toggle="tooltip" title="Delete Employee"><i class="flaticon2-trash text-danger text-hover-warning"></i></button>';
                                $html .= '<input type="hidden" class="user_id" value="'.$user->external_id.'">';
                                $html .= csrf_field();

                $html .= '</div> </div>';
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['namelink', 'name', 'primary_number', 'email', 'branch', 'role', 'action'])
            ->make(true);
    }

    /**
     * Json for Data tables
     * @param $id
     * @return mixed
     */

    public function taskData($id)
    {
        $tasks = Task::with(['status', 'client'])->select(
            ['id', 'external_id', 'title', 'created_at', 'deadline', 'user_assigned_id', 'client_id', 'status_id']
        )
            ->where('user_assigned_id', $id)->get();
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
            ->editColumn('client_id', function ($tasks) {
                return $tasks->client->primaryContact->name;
            })
            ->rawColumns(['titlelink','status_id'])
            ->make(true);
    }

    /**
     * Json for Data tables
     * @param $id
     * @return mixed
     */

    public function leadData($id)
    {
        $leads = Lead::with(['status', 'client'])->select(
            ['id', 'external_id', 'title', 'created_at', 'deadline', 'user_assigned_id', 'client_id', 'status_id']
        )
            ->where('user_assigned_id', $id)->get();
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
            ->editColumn('client_id', function ($tasks) {
                return $tasks->client->primaryContact->name;
            })
            ->rawColumns(['titlelink','status_id'])
            ->make(true);
    }

    /**
     * Json for Data tables
     * @param $id
     * @return mixed
     */

    public function clientData($id)
    {
        $clients = Client::select(['external_id', 'company_name', 'vat', 'address'])->where('user_id', $id);
        return Datatables::of($clients)
            ->addColumn('clientlink', function ($clients) {
                return '<a href="' . route('clients.show', $clients->external_id) . '">' . $clients->company_name . '</a>';
            })
            ->editColumn('created_at', function ($clients) {
                return $clients->created_at ? with(new Carbon($clients->created_at))
                    ->format(carbonDate()) : '';
            })
            ->rawColumns(['clientlink'])
            ->make(true);
    }


    /**
     * @return mixed
     */

    public function create(Request $request)
    {
        $data['is_system_user'] = Helper::is_system_user();
        $data['is_distributor_user'] = Helper::is_distributor_user();
        $data['back_url'] = route('users.index');
        $data['distributor_title'] = false;
        $data['distributor_id'] = 0;
        $data['distributor'] = false;


        $data['roles'] = Role::whereNotIn('name', ['owner', 'distributor'])->pluck('name', 'id');
        $data['name_title'] = "Create Employee";

        if(Helper::is_system_user() || Helper::is_distributor_user()) {
            $external_id = $request->get('distributor') ?? "";

            if(!empty($external_id)) {
                $distributor = Distributor::findByExternalId($external_id);

                $data['back_url'] = route('users.index').'?distributor='.$external_id;
                $data['distributor_title'] = "<span class='text-muted'>(". $distributor->name .")</span>";
                $data['distributor_id'] = $distributor->id;
                $data['distributor'] = $distributor;
            } else {
                $data['roles'] = Role::whereIn('name', ['owner', 'distributor'])->pluck('name', 'id');
                $data['name_title'] = "Create User";
            }
        }

        $data['years'] = $this->years();
        $data['months'] = $this->allMonths();
        $data['branch'] = Branch::pluck('name', 'id')->toArray();

        return view('users.create')->with($data);
    }


    /**
     * @param StoreUserRequest $userRequest
     * @return mixed
     */
    public function store(StoreUserRequest $request)
    {
        if(Helper::is_system_user() || Helper::is_distributor_user()) {
            $distributor_id = $request->distributor_id;
        } else {
            $distributor_id = Helper::getDistributorId();

            if(!Helper::canCreateUser($distributor_id)) {
                return redirect()->back()->with('error', "As per subscription you can not create more users");
            }
        }

        if($request->user_type == 1 && Helper::allowViewOnly($distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        if($request->hasFile('profile_pic')) {

            $first_name  = strtolower(str_replace(' ', '_',$request->first_name));

            $pic_name = $first_name ."_". time() .".". $request->profile_pic->extension();
            $path_profile_pic = 'storage/assets/employees/profile_pics/';
            $returned = $request->profile_pic->move(public_path($path_profile_pic), $pic_name);

            $profile_pic_name = $path_profile_pic . $pic_name;
        } else {
            $profile_pic_name = "";
        }

        $certificate_arr = [];
        $path = 'storage/assets/employees/certificates/';
        foreach($request->certification_data as $certificate) {
            if(!empty($certificate['certification_attachment'])) {

                $name = strtolower(str_replace(' ', '_',$certificate['name']));
                $certificate_name = $name ."-". rand(3000, 6000) .".". $certificate['certification_attachment']->extension();
                $certificate['certification_attachment']->move(public_path($path), $certificate_name);
                $certificate_arr[] = [
                    'name' => $certificate['name'],
                    'from' => $certificate['from'],
                    'to' => $certificate['to'],
                    'attachment' => $path . $certificate_name
                ];
            } else {
                $certificate_arr[] = [
                    'name' => $certificate['name'],
                    'from' => $certificate['from'],
                    'to' => $certificate['to'],
                    'attachment' => ""
                ];
            }
        }

        if($request->hasFile('bank_attachment')) {
            $holder_name = strtolower(str_replace(' ', '_',$request->holder_name));

            $attachment_name = $holder_name ."_". time() .".". $request->bank_attachment->extension();
            $path_bank_attachment = 'storage/assets/employees/bank_attachments/';
            $returned = $request->bank_attachment->move(public_path($path_bank_attachment), $attachment_name);

            $bank_attachment_name = $path_bank_attachment . $attachment_name;
        } else {
            $bank_attachment_name = "";
        }


        $created_by = Auth::id();
        $user = User::create([
            'external_id' => Uuid::uuid4()->toString(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'nick_name' => $request->nick_name,
            'email' => $request->email,
            'primary_number' => $request->primary_number,
            'secondary_number' => $request->secondary_number,
            'branch_id' => $request->branch_id ?? 0,
            'expertise' => $request->expertise,
            'password' => bcrypt($request->password),
            'date_of_joining' => $request->date_of_joining,
            'address' => $request->address,
            'salary' => $request->salary,
            'basic' => $request->basic,
            'pf' => $request->pf,
            'gratutity' => $request->gratutity,
            'others' => $request->others,
            'pt' => $request->pt,
            'income_tax' => $request->income_tax,
            'over_time_ph' => $request->over_time_ph,
            'working_hours' => $request->working_hours,
            'account_number' => $request->account_number,
            'holder_name' => $request->holder_name,
            'bank_name' => $request->bank_name,
            'isfc_code' => $request->isfc_code,
            'bank_attachment' => $bank_attachment_name,
            'total_experience' => $request->total_experience,
            'profile_pic' => $profile_pic_name,

            // JSON Data
            'certificates' => json_encode($certificate_arr),
            'week_off' => json_encode($request->week_off ?? []),
            'employeer' => json_encode($request->employer_data ?? []),
            'created_by' => $created_by,

            // Distributor_id
            'distributor_id' => $distributor_id ?? 0,
            'product_commission' => $request->product_commission,
            'service_commission' => $request->service_commission,
            'plan_commission' => $request->plan_commission,

            // User Type = 0 = system user, 1 = salon employee, 2 = distributor
            'user_type' => intval($request->user_type),
        ]);
        $user->roles()->sync([$request->role]);

        $services = $request->services;
        $default_services = Product::where('type', 1)->where('is_default', 1)->where('distributor_id', $distributor_id)->pluck('id')->toArray();

        if(empty($services)) {
            $services = [];
        }
        if(empty($default_services)) {
            $default_services = [];
        }

        $assign_services = array_merge($services, $default_services);

        $user->services()->sync($assign_services);

        if($request->user_type == 1) {
            $user_type = "Employee";
        } else {
            $user_type = "User";
        }

        Session()->flash('success', __("$user_type successfully added!"));
        return redirect()->to($request->back_url);
    }

    public function profile(Request $request)
    {
        $data['back_url'] = false;
        $data['is_system_user'] = Helper::is_system_user();

        $user = Auth::user();
        $data['user'] = $user;
        $data['is_profile'] = true;
        $data['branches'] = Branch::pluck('name', 'id');
        $data['roles'] = Role::where('name', '!=','owner')->pluck('name', 'id');

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        }

        return view('users.details.personal')->with($data);
    }

    /**
     * @param $external_id
     * @return mixed
     *
     *  Show personal details
     */
    public function show($url_id, Request $request)
    {
        $data['back_url'] = route('users.index');
        $data['is_system_user'] = Helper::is_system_user();

        if(Helper::is_system_user() || Helper::is_distributor_user()) {
            $external_id = $request->get('distributor') ?? "";
            if(!empty($external_id)) {
                $data['distributor_id'] = $external_id;
                $data['back_url'] = route('users.index') . "?distributor=". $external_id;
            }
            $user = $this->findByExternalId($url_id);
        } else {
            $distributor_id = Helper::getDistributorId();
            $user = User::where('external_id', $url_id)->where('distributor_id', $distributor_id)->firstOrFail();
        }

        $data['user'] = $user;
        $data['is_profile'] = $user->id == Auth::user()->id ? true : false;
        $data['branches'] = Branch::pluck('name', 'id');
        $data['roles'] = Role::where('name', '!=','owner')->pluck('name', 'id');
        $data['companyname'] = Setting::first()->company;

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        }

        return view('users.details.personal')->with($data);
    }

    /**
     * @param $external_id
     * @return mixed
     *
     *  Show professional details
     */
    public function showProfessionalDetails($url_id, Request $request)
    {
        $data['back_url'] = route('users.index');
        $data['is_system_user'] = Helper::is_system_user();

        if(Helper::is_system_user() || Helper::is_distributor_user()) {
            $external_id = $request->get('distributor') ?? "";
            if(!empty($external_id)) {
                $data['distributor_id'] = $external_id;
                $data['back_url'] = route('users.index') . "?distributor=". $external_id;
            }
            $user = $this->findByExternalId($url_id);
        } else {
            $distributor_id = Helper::getDistributorId();
            $user = User::where('external_id', $url_id)->where('distributor_id', $distributor_id)->firstOrFail();
        }

        $data['unpaid_commission'] = Helper::decimalNumber(UsersCommission::where('user_id', $user->id)->where('is_paid', 0)->groupBy('user_id')->sum('invoice_commission'));
        $data['paid_commission'] = Helper::decimalNumber(UsersCommission::where('user_id', $user->id)->where('is_paid', 1)->groupBy('user_id')->sum('invoice_commission'));

        $data['user'] = $user;

        $data['is_profile'] = $user->id == Auth::user()->id ? true : false;
        $data['branches'] = Branch::pluck('name', 'id');
        $data['roles'] = Role::where('name', '!=','owner')->pluck('name', 'id');
        $data['week_offs'] = json_decode($user->week_off);
        $data['employeers'] = json_decode($user->employeer);
        $data['employee_services'] = $user->services;

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        }

        return view('users.details.professional')->with($data);
    }

    /**
     * @param $external_id
     * @return mixed
     *
     *  Show professional details
     */
    public function showOtherDetails($url_id, Request $request)
    {
        $data['back_url'] = route('users.index');
        $data['is_system_user'] = Helper::is_system_user();

        if(Helper::is_system_user() || Helper::is_distributor_user()) {
            $external_id = $request->get('distributor') ?? "";
            if(!empty($external_id)) {
                $data['distributor_id'] = $external_id;
                $data['back_url'] = route('users.index') . "?distributor=". $external_id;
            }
            $user = $this->findByExternalId($url_id);
        } else {
            $distributor_id = Helper::getDistributorId();
            $user = User::where('external_id', $url_id)->where('distributor_id', $distributor_id)->firstOrFail();
        }

        $data['user'] = $user;
        $data['is_profile'] = $user->id == Auth::user()->id ? true : false;
        $data['branches'] = Branch::pluck('name', 'id');
        $data['roles'] = Role::where('name', '!=','owner')->pluck('name', 'id');
        $data['certificates'] = json_decode($user->certificates);

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        }

        return view('users.details.other')->with($data);
    }


    /**
     * @param $external_id
     * @return mixed
     */
    public function edit($url_id, Request $request)
    {
        $data['is_system_user'] = Helper::is_system_user();
        $data['back_url'] = route('users.index');
        $data['distributor_title'] = false;
        $data['distributor_id'] = 0;
        $data['distributor'] = false;

        if(Helper::is_system_user() || Helper::is_distributor_user()) {
            $data['distributor_title'] = "";
            $external_id = $request->get('distributor') ?? "";

            if(!empty($external_id)) {
                $distributor = Distributor::findByExternalId($external_id);

                $data['back_url'] = route('users.index').'?distributor='.$external_id;
                $data['distributor_title'] = "<span class='text-muted'>(". $distributor->name ?? '' .")</span>";
                $data['distributor_id'] = $distributor->id;
                $data['distributor'] = $distributor;
            }

            $user = $this->findByExternalId($url_id);
        } else {
            $distributor_id = Helper::getDistributorId();
            $user = User::where('external_id', $url_id)->where('distributor_id', $distributor_id)->firstOrFail();
        }

        if($user->user_type == 1) {
            $data['roles'] = Role::whereNotIn('name', ['owner', 'distributor'])->pluck('name', 'id');
            $data['name_title'] = "Update Employee";
        } else {
            $data['roles'] = Role::whereIn('name', ['owner', 'distributor'])->pluck('name', 'id');
            $data['name_title'] = "Update User";
        }

        $data['user'] = $user;
        $data['dealers'] = Client::pluck('name', 'id');
        $data['branch'] = Branch::pluck('name', 'id');
        $data['years'] = $this->years();
        $data['months'] = $this->allMonths();
        $data['week_offs'] = json_decode($user->week_off);
        $data['employeers'] = json_decode($user->employeer);
        $data['certificates'] = json_decode($user->certificates);
        $data['user_type'] = $user->distributor_id > 0 ? 1 : 0;
        $data['selected_distributor'] = Distributor::find($user->distributor_id);
        $data['selected_branch'] = Branch::find($user->branch_id);
        $data['selected_services'] = $user->services->pluck('name', 'id');

        return view('users.edit')->with($data);
    }

    /**
     * @param $external_id
     * @param UpdateUserRequest $request
     * @return mixed
     */
    public function update($external_id, UpdateUserRequest $request)
    {
        $updated_by = Auth::id();
        $user = $this->findByExternalId($external_id);

        if($user->user_type == 1 && Helper::allowViewOnly($user->distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        $certificate_arr = [];
        $path = 'storage/assets/employees/certificates/';
		if(isset($request->certification_data)){
			if(count($request->certification_data)){
        foreach($request->certification_data as $certificate) {

            if(!empty($certificate['certification_attachment'])) {

                $name = strtolower(str_replace(' ', '_',$certificate['name']));
                $certificate_name = $name ."-". rand(3000, 6000) .".". $certificate['certification_attachment']->extension();
                $certificate['certification_attachment']->move(public_path($path), $certificate_name);
                $certificate_name = $path . $certificate_name;
                if(!empty($certificate['old_certification_attachment'])){
                    // unlink($certificate['old_certification_attachment']);
                }
            } else {
                $certificate_name = $certificate['old_certification_attachment'] ?? "";
            }

            $certificate_arr[] = [
                'name' => $certificate['name'],
                'from' => $certificate['from'],
                'to' => $certificate['to'],
                'attachment' => $certificate_name
            ];
        }
			}
		}

        if($request->hasFile('profile_pic')){

            $first_name  = strtolower(str_replace(' ', '_',$request->first_name));

            $pic_name = $first_name ."_". time() .".". $request->profile_pic->extension();
            $path_profile_pic = 'storage/assets/employees/profile_pics/';
            $returned = $request->profile_pic->move(public_path($path_profile_pic), $pic_name);

            $profile_pic_name = $path_profile_pic . $pic_name;

            // unlink(asset($request->old_profile_pic));
        } else {
            $profile_pic_name = $request->old_profile_pic;
        }

        if($request->hasFile('bank_attachment')) {
            $holder_name = strtolower(str_replace(' ', '_',$request->holder_name));

            $attachment_name = $holder_name ."_". time() .".". $request->bank_attachment->extension();
            $path_bank_attachment = 'storage/assets/employees/bank_attachments/';
            $returned = $request->bank_attachment->move(public_path($path_bank_attachment), $attachment_name);

            $bank_attachment_name = $path_bank_attachment . $attachment_name;

            // unlink(asset($request->old_bank_attachment));
        } else {
            $bank_attachment_name = $request->old_bank_attachment;
        }

        $update_employee = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'nick_name' => $request->nick_name,
            'email' => $request->email,
            'primary_number' => $request->primary_number,
            'secondary_number' => $request->secondary_number,
            'branch_id' => $request->branch_id ?? 0,
            'expertise' => $request->expertise,
            'date_of_joining' => $request->date_of_joining,
            'address' => $request->address,
            'salary' => $request->salary,
            'basic' => $request->basic,
            'pf' => $request->pf,
            'gratutity' => $request->gratutity,
            'others' => $request->others,
            'pt' => $request->pt,
            'income_tax' => $request->income_tax,
            'over_time_ph' => $request->over_time_ph,
            'working_hours' => $request->working_hours,
            'account_number' => $request->account_number,
            'holder_name' => $request->holder_name,
            'bank_name' => $request->bank_name,
            'isfc_code' => $request->isfc_code,
            'bank_attachment' => $bank_attachment_name,
            'total_experience' => $request->total_experience,
            'profile_pic' => $profile_pic_name,

            // JSON Data
            'certificates' => json_encode($certificate_arr),
            'week_off' => json_encode($request->week_off),
            'employeer' => json_encode($request->employer_data),
            'updated_by' => $updated_by,

            // Distributor_id
            'distributor_id' => $request->distributor_id ?? 0,
            'product_commission' => $request->product_commission,
            'service_commission' => $request->service_commission,
            'plan_commission' => $request->plan_commission,
        ];


        if($request->password !== "") {
            $update_employee['password'] = bcrypt($request->password);
        }
        $user->fill($update_employee)->save();
        $user->roles()->sync([$request->role]);
        $user->services()->sync($request->services);

        if($user->user_type == 1) {
            $user_type = "Employee";
        } else {
            $user_type = "User";
        }

        Session()->flash('success', __("$user_type successfully updated!"));
        return redirect()->to($request->back_url);
    }

    public function updatePersonal(Request $request)
    {
        if($request->hasFile('profile_pic')){

            $first_name  = strtolower(str_replace(' ', '_',$request->first_name));

            $pic_name = $first_name ."_". time() .".". $request->profile_pic->extension();
            $path_profile_pic = 'storage/assets/employees/profile_pics/';
            $returned = $request->profile_pic->move(public_path($path_profile_pic), $pic_name);

            $profile_pic_name = $path_profile_pic . $pic_name;

            // unlink(asset($request->old_profile_pic));
        } else {
            $profile_pic_name = $request->old_profile_pic;
        }

        $user = Auth::user();

        $user->update([
            'first_name' => $request->first_name ?? "",
            'last_name' => $request->last_name ?? "",
            'email' => $request->email ?? "",
            'expertise' => $request->expertise ?? "",
            'primary_number' => $request->primary_number ?? "",
            'secondary_number' => $request->secondary_number ?? "",
            'address' => $request->address ?? "",
            'profile_pic' => $profile_pic_name,
        ]);

        return redirect()->back()->with('success', 'Personal details successfully updated!');
    }

    public function updateOther(Request $request)
    {
        if($request->hasFile('bank_attachment')) {
            $holder_name = strtolower(str_replace(' ', '_',$request->holder_name));

            $attachment_name = $holder_name ."_". time() .".". $request->bank_attachment->extension();
            $path_bank_attachment = 'storage/assets/employees/bank_attachments/';
            $returned = $request->bank_attachment->move(public_path($path_bank_attachment), $attachment_name);

            $bank_attachment_name = $path_bank_attachment . $attachment_name;

            // unlink(asset($request->old_bank_attachment));
        } else {
            $bank_attachment_name = $request->old_bank_attachment;
        }

        $user = Auth::user();

        $user->update([
            'account_number' => $request->account_number,
            'holder_name' => $request->holder_name,
            'bank_name' => $request->bank_name,
            'isfc_code' => $request->isfc_code,
            'bank_attachment' => $bank_attachment_name,
        ]);

        return redirect()->back()->with('success', 'Bank details successfully updated!');
    }

    /**
     * @param $external_id
     * @return mixed
     */
    public function destroy(Request $request, $external_id)
    {
        $user = $this->findByExternalId($external_id);

        if ($user->hasRole('owner')) {
            return Session()->flash('success_warning', __('Not allowed to delete super admin'));
        }

        if ($request->tasks == "move_all_tasks" && $request->task_user != "") {
            $user->moveTasks($request->task_user);
        }
        if ($request->leads == "move_all_leads" && $request->lead_user != "") {
            $user->moveLeads($request->lead_user);
        }
        if ($request->clients == "move_all_clients" && $request->client_user != "") {
            $user->moveClients($request->client_user);
        }

        try {
            $user->delete();
            Session()->flash('success', __('User successfully deleted'));
        } catch (\Illuminate\Database\QueryException $e) {
            Session()->flash('success_warning', __('User can NOT have, leads, clients, or tasks assigned when deleted'));
        }

        return redirect()->route('users.index');
    }

    /**
    * @param $external_id
    * @return mixed
    */
    public function findByExternalId($external_id)
    {
        return User::whereExternalId($external_id)->first();
    }
    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    private function allRoles()
    {
        if (auth()->user()->roles->first()->name == Role::OWNER_ROLE) {
            return Role::all('display_name', 'id', 'name', 'external_id')->sortBy('display_name');
        }

        return Role::all('display_name', 'id', 'name', 'external_id')->filter(function ($value, $key) {
            return $value->name != "owner";
        })->sortBy('display_name');
    }

    /**
     *  Ajax delete (for sweet alert)
     */

    public function checkUserDelete(Request $request)
    {
        $external_id = $request->external_id;

        $user = $this->findByExternalId($external_id);

        $user_enquiries = Enquiry::where('user_assigned_id', $user->id)->count();
        $user_clients = Client::where('user_id', $user->id)->count();

        if($user_enquiries === 0 && $user_clients === 0) {
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Sorry this user has entries in other moduels!",
            ]);
        }
    }

    public function ajaxUserDelete(Request $request)
    {
        $external_id = $request->external_id;
        $user = $this->findByExternalId($external_id);
        $user->delete();

        Session()->flash('success', __('Employee successfully deleted!'));
        return response()->json([
            'status' => true,
            'message' => "Employee deleted successfully!"
        ]);
    }

    /**
     *  @return mixed
     *  $users
     */
    public function getUserByName(Request $request)
    {
        $is_system_user = Helper::getDistributorId();
        if($is_system_user == 0) { //is system user
            $distributor_id = $request->distributor_id;
        } else {
            $distributor_id = $is_system_user;
        }

        $name = $request->get('q');
        $users = User::where('first_name', 'like', "%{$name}%")->where('last_name', 'like', "%{$name}%")->where('distributor_id', $distributor_id)->get();

        return response()->json($users);
    }

    public function checkEmailRepeat(Request $request)
    {
        $id = $request->id;
        $email = $request->email;
        $email = User::where('email', $email)->first();

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

    // Check repeat primary number of User/ Employee
    public function checkPrimaryNumberRepeat(Request $request)
    {
        $id = $request->id;
        $number = $request->number;
        $number = User::where('primary_number', $number)->first();

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

    private function allMonths(){
        $months = array(
            "1" => "January", "2" => "February", "3" => "March", "4" => "April",
            "5" => "May", "6" => "June", "7" => "July", "8" => "August",
            "9" => "September", "10" => "October", "11" => "November", "12" => "December",
        );
        return $months;
    }

    private function years(){

        $current_year = date('Y');
        $next_year = date('Y', strtotime(date("Y-m-d", time()) . " + 365 day"));

        $years = array(
            $current_year => $current_year,
            $next_year => $next_year
        );

        return $years;
    }

    /**
     *  @return mixed
     *  $products
     */
    public function getServices(Request $request)
    {
        $name = $request->get('q');
        $id = $request->get('user_id');

        $user = User::find($id);
        return response()->json(($user->services)?$user->services:array());
    }
}
