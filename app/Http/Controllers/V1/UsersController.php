<?php

namespace App\Http\Controllers\V1;

use Ramsey\Uuid\Uuid;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use JWTAuth;
use App\Helpers\Helper;
use App\Models\User;
use App\Models\Subscriptions;
use App\Models\Distributor;
use App\Models\UsersCommission;
use App\Models\Product;

class UsersController extends Controller
{
    public function getUserDetails(Request $request, $user_id)
    {
        $user_id = $request->id;
        $user = User::find($user_id);

        if(!empty($user)) {

            $roleId = isset($user->roles[0]) ? $user->roles[0]->id : null;
            $user_data = $user->toArray();
            $user_data['role']  = $user->roles->pluck('name')[0];
            $user_data['services']  = $user->services;
            $user_data['salon_details']  = Distributor::find($user->distributor_id);
            $user_data['permissions']    = $this->getRoleWisePermission($roleId);
            $user_data['unpaid_commission'] = Helper::decimalNumber(UsersCommission::where('user_id', $user->id)->where('is_paid', 0)->groupBy('user_id')->sum('invoice_commission'));
            $user_data['paid_commission'] = Helper::decimalNumber(UsersCommission::where('user_id', $user->id)->where('is_paid', 1)->groupBy('user_id')->sum('invoice_commission'));

            return response()->json([
                'status' => 'SUCCESS',
                'message' => '',
                'data' => $user_data,
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "No user found!",
            ]);
        }
    }

    public function updatePersonalDetails(Request $request)
    {
        // Get Auth user
        $user = JWTAuth::parseToken()->authenticate();

        // Check subscription of salon
        if(Helper::allowViewOnly($user->distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }

        // Validations
        $validator = Validator::make($request->all(), [
            'first_name' =>  'required',
            'last_name' =>  'required',
            'primary_number' =>  'required|digits:10|unique:users,primary_number,'.$user->id,
            'secondary_number' =>  'digits:10',
            'email' =>  'required|email|unique:users,email,'.$user->id,
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $update_data = [
            'first_name' => $request->first_name ?? "",
            'last_name' => $request->last_name ?? "",
            'email' => $request->email ?? "",
            'expertise' => $request->expertise ?? "",
            'primary_number' => $request->primary_number ?? "",
            'secondary_number' => $request->secondary_number ?? "",
            'address' => $request->address ?? "",
        ];

        // if($request->hasFile('profile_pic')){

        //     $first_name  = strtolower(str_replace(' ', '_',$request->first_name));

        //     $pic_name = $first_name ."_". time() .".". $request->profile_pic->extension();
        //     $path_profile_pic = 'storage/assets/employees/profile_pics/';
        //     $returned = $request->profile_pic->move(public_path($path_profile_pic), $pic_name);

        //     $profile_pic_name = $path_profile_pic . $pic_name;
        //     $update_data['profile_pic'] = $profile_pic_name;

        //     // unlink(asset($request->old_profile_pic));
        // }

        if(!empty($request->profile_pic)) {
            $path = 'assets/employees/profile_pics/';
            $first_name  = strtolower(str_replace(' ', '_',$request->first_name));
            $profile_pic_name = Helper::createImageFromBase64($request->profile_pic, $first_name, $path);
            $update_data['profile_pic'] = $profile_pic_name;
        }

        $user->update($update_data);

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $update_data,
            'message' => 'Personal details successfully updated!'
        ]);
    }


    public function updateBankDetails(Request $request)
    {
        // Get Auth user
        $user = JWTAuth::parseToken()->authenticate();

        // Check subscription of salon
        if(Helper::allowViewOnly($user->distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }

        $update_data = [
            'account_number' => $request->account_number,
            'holder_name' => $request->holder_name,
            'bank_name' => $request->bank_name,
            'isfc_code' => $request->isfc_code,
        ];

        // if($request->hasFile('bank_attachment')) {
        //     $holder_name = strtolower(str_replace(' ', '_',$request->holder_name));

        //     $attachment_name = $holder_name ."_". time() .".". $request->bank_attachment->extension();
        //     $path_bank_attachment = 'storage/assets/employees/bank_attachments/';
        //     $returned = $request->bank_attachment->move(public_path($path_bank_attachment), $attachment_name);

        //     $bank_attachment_name = $path_bank_attachment . $attachment_name;
        //     $update_data['bank_attachment'] = $bank_attachment_name;
        // }

        if(!empty($request->bank_attachment)) {
            $path = 'assets/employees/bank_attachments/';
            $holder_name  = strtolower(str_replace(' ', '_',$request->holder_name));
            $bank_attachment_name = Helper::createImageFromBase64($request->bank_attachment, $holder_name, $path);
            $update_data['bank_attachment'] = $bank_attachment_name;
        }

        $user->update($update_data);

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $update_data,
            'message' => 'Bank details successfully updated!'
        ]);
    }

    public function getRoleWisePermission($roleId)
    {
        $permissionId = \DB::table("permission_role")->where("role_id", $roleId)
        ->pluck('permission_role.permission_id')
        ->toArray();
        $data = [];
        $permission = collect(\DB::table("permissions")->whereIn('id',$permissionId)->get(['name']))->toArray();
        return ['permission'=>$permission];
    }

    /**
     *  @return mixed
     *  $industries
     */
    public function getUsersByBranch(Request $request)
    {
        // Auth User
        $user = JWTAuth::parseToken()->authenticate();
        $distributor_id = $user->distributor_id;
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $name = $request->get('name');
        $branch_id = $request->get('branch_id');
		$paginate = $request->paginate ?? 0;
		$query = User::where('distributor_id','=',$distributor_id);
        //if(!empty($name)) {
			if(!empty($name)){
            $query->where(function ($query) use ($name) {
                $query->where('first_name', 'like', "%{$name}%");
                $query->orWhere('last_name', 'like', "%{$name}%");
            });
			}

            if(!empty($branch_id)) {
                $query->where('branch_id', $branch_id);
                $message = "No data found!";
            }
			$query->orderBy('first_name', 'asc');
			$query->orderBy('last_name', 'asc');

			if($paginate == 1) {
				$data = $query->paginate();
				$count = count($data);
			} else {
				$data = [];
				$data['data'] = $query->get();
				$count = count($data['data']);
			}
			if($count > 0) {
				$custom = collect(['status' => 'SUCCESS']);
				$custom = collect(['message' => '']);
				$data = $custom->merge($data);
				return response()->json($data);
			} else {
				$custom = collect(['status' => 'FAIL']);
				$custom = collect(['message' => 'No data found!']);
				$data = $custom->merge($data);
				return response()->json($data);
			}
            //$users = $query->get();

        /* } else {
            $users = [];
            $message = "Please search by employee name!";
        }

        if(count($users) > 0) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => $users,
                'message' => '',
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'data' => [],
                'message' => $message,
            ]);
        } */
    }


    /**
     *  @return mixed
     *  $products
     */
    public function getServices(Request $request)
    {
		$user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }
		$distributor_id = $user->distributor_id;

        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }
        $service = $request->get('service');
        $user_id = $request->get('user_id');
		$paginate = $request->paginate ?? 0;

        if(empty($user_id)) {
            return response()->json([
                'status' => 'FAIL',
                'data' => [],
                'message' => "Please provide user id!",
            ]);
        }

		$services = Product::where('distributor_id','=',$distributor_id);
		if(!empty($service))
		$services->where('name', 'like', "%{$service}%");

        $services->whereHas('users', function ($query) use ($user_id) {
                $query->where('users.id', $user_id);
            });//->get();

		if($paginate == 1) {
            $data = $services->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $services->get();
            $count = count($data['data']);
        }

        if($count > 0) {
            $custom = collect(['status' => 'SUCCESS']);
            $custom = collect(['message' => '']);
            $data = $custom->merge($data);
            return response()->json($data);
        } else {
            $custom = collect(['status' => 'FAIL']);
            $custom = collect(['message' => 'No data found!']);
            $data = $custom->merge($data);
            return response()->json($data);
        }

        /* if(!empty($service)) {

            $services = Product::where('name', 'like', "%{$service}%")
            ->whereHas('users', function ($query) use ($user_id) {
                $query->where('users.id', $user_id);
            })->get();

            $message = "No data found!";

        } else {
            $services = [];
            $message = "Please search by service name!";
        }

        if(count($services) > 0) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => $services,
                'message' => '',
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'data' => [],
                'message' => $message,
            ]);
        } */
    }


    // Listing for employee specific listing (provide particular salons employee listing only)
    public function employeesList(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $search_text = $request->search_text;
		$branch_id = $request->get('branch_id');
        $paginate = $request->paginate ?? 0;
        $basic_details = $request->basic_details ?? 0;

        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }
		$distributor_id = $user->distributor_id;

        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }

        $distributor_id = $user->distributor_id;
        $users = User::with(['getDistributor', 'getBranch', 'roles', 'services']);
        $users->where('distributor_id', $distributor_id);
		if($branch_id)
        $users->where('branch_id', $branch_id);
        $users->where(function ($qeury) use ($search_text) {
            $qeury->where('first_name', 'LIKE', "%" . $search_text . "%");
            $qeury->orWhere('last_name', 'LIKE', "%" . $search_text . "%");
            $qeury->orWhere('primary_number', 'LIKE', "%" . $search_text . "%");
        });
        $users->orderBy('id','desc');

        if($paginate == 1) {
            $data = $users->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $users->get();
            $count = count($data['data']);
        }

        if($count > 0) {
            $custom = collect(['status' => 'SUCCESS']);
            $custom = collect(['message' => '']);
            $data = $custom->merge($data);
            return response()->json($data);
        } else {
            $custom = collect(['status' => 'FAIL']);
            $custom = collect(['message' => 'No data found!']);
            $data = $custom->merge($data);
            return response()->json($data);
        }
    }


    // Create User (employee, distributor, super admin) - (with user type)
    public function storeUser(Request $request)
    {
        // Auth User
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $distributor_id = $user->distributor_id;
        // Check if salon has subscription or not
        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }
        if(!Helper::canCreateUser($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "As per subscription you can not create more users!"
            ]);
        }

        // Defined user type
        $user_type = $request->user_type ?? 1;

        // Validations
        $validator = Validator::make($request->all(), [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'primary_number' => ['required', 'numeric', 'digits:10', 'unique:users'],
            'secondary_number' => ['numeric', 'digits:10'],
            'branch_id' => ['required'],
            'role' => ['required'],
            'password' => ['required'],
            'date_of_joining' => ['required', 'date_format:Y-m-d'],
            'salary' => ['required', 'numeric'],
            'basic' => [ 'numeric'],
            'pf' => [ 'numeric'],
            'gratutity' => [ 'numeric'],
            'others' => [ 'numeric'],
            'pt' => [ 'numeric'],
            'over_time_ph' => [ 'numeric'],
            'income_tax' => [ 'numeric'],
            'total_experience' => [ 'numeric'],
            'working_hours' => ['required', 'numeric'],
            'product_commission' => ['required', 'numeric'],
            'service_commission' => ['required', 'numeric'],
        ], [
            'first_name.required' => 'Please enter first name!',
            'last_name.required' => 'Please enter first name!',
            'email.required' => 'Please enter email address!',
            'email.email' => 'Please enter valid email address!',
            'primary_number.required' => 'Please enter primary number!',
            'primary_number.numeric' => 'Please enter primary number!',
            'primary_number.digits' => 'Please enter valid primary number!',
            'secondary_number.numeric' => 'Please enter valid primary number!',
            'secondary_number.digits' => 'Please enter valid primary number!',
            'branch_id.required' => 'Please select branch!',
            'role.required' => 'Please select role!',
            'password.required' => 'Please enter password!',
            'date_of_joining.required' => 'Please select date of joining!',
            'date_of_joining.date_format' => 'Please enter valid date (format : Y-m-d)!',
            'salary.required' => 'Please enter salary!',
            'salary.numeric' => 'Please enter valid salary!',
            'working_hours.required' => 'Please enter working hours!',
            'working_hours.numeric' => 'Please enter valid working hours!',
            'product_commission.required' => 'Please enter plan commission',
            'product_commission.numeric' => 'Please enter valid plan commission',
            'service_commission.required' => 'Please enter service commission',
            'service_commission.numeric' => 'Please enter valid service commission',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        // Upload certificates
        $certificate_arr = [];
        $path = 'storage/assets/employees/certificates/';
        foreach($request->certification_data as $certificate) {
            if(!empty($certificate['certification_attachment'])) {

                $path = 'assets/employees/certificates/';
                $certificate_name  = strtolower(str_replace(' ', '_',$certificate['name']));

                $certificate_arr[] = [
                    'name' => $certificate['name'],
                    'from' => $certificate['from'],
                    'to' => $certificate['to'],
                    'attachment' => Helper::createImageFromBase64($certificate['certification_attachment'], $certificate_name, $path)
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

        // Upload profile pic
        if(!empty($request->profile_pic)) {
            $path = 'assets/employees/profile_pics/';
            $first_name  = strtolower(str_replace(' ', '_',$request->first_name));
            $profile_pic_name = Helper::createImageFromBase64($request->profile_pic, $first_name, $path);
            $dataset['profile_pic'] = $profile_pic_name;
        }

        // upload bank attachment
        if(!empty($request->bank_attachment)) {
            $path = 'assets/employees/bank_attachments/';
            $holder_name  = strtolower(str_replace(' ', '_',$request->holder_name));
            $bank_attachment_name = Helper::createImageFromBase64($request->bank_attachment, $holder_name, $path);
            $dataset['bank_attachment'] = $bank_attachment_name;
        }

        // Prepare data
        $dataset = [
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
            'salary' => $request->salary ?? 0,
            'basic' => $request->basic ?? 0,
            'pf' => $request->pf ?? 0,
            'gratutity' => $request->gratutity ?? 0,
            'others' => $request->others ?? 0,
            'pt' => $request->pt ?? 0,
            'income_tax' => $request->income_tax ?? 0,
            'over_time_ph' => $request->over_time_ph ?? 0,
            'working_hours' => $request->working_hours,
            'account_number' => $request->account_number,
            'holder_name' => $request->holder_name,
            'bank_name' => $request->bank_name,
            'isfc_code' => $request->isfc_code,
            'bank_attachment' => $bank_attachment_name ?? "",
            'total_experience' => $request->total_experience,
            'profile_pic' => $profile_pic_name ?? "",

            // JSON Data
            'certificates' => json_encode($certificate_arr),
            'week_off' => json_encode($request->week_off),
            'employeer' => json_encode($request->employer_data),
            'created_by' => $user->id,

            // Distributor_id
            'distributor_id' => $distributor_id ?? 0,
            'product_commission' => $request->product_commission ?? 0,
            'service_commission' => $request->service_commission ?? 0,
            'plan_commission' => $request->plan_commission ?? 0,

            // User Type = 0 = system user, 1 = salon employee, 2 = distributor
            'user_type' => intval($user_type),
        ];

        // Insert Data
        $user = User::create($dataset);
        // Set user role
        $user->roles()->sync([$request->role]);

        // Add services & default services
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
            $message = "Employee successfully added!";
        } else {
            $message = "User successfully added!";
        }

        return response()->json([
            'status' => 'SUCCESS',
            'message' => $message,
            'data' => $dataset,
        ]);
    }

    // Create User (employee, distributor, super admin) - (with user type)
    public function updateUser(Request $request, $external_id)
    {
        // Auth User
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $employee = User::where('external_id', $external_id)->first();
        if(empty($employee)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $distributor_id = $employee->distributor_id;
        // Check if salon has subscription or not
        if(Helper::allowViewOnly($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Subscription has been expired. please renew."
            ]);
        }
        if(!Helper::canCreateUser($distributor_id)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "As per subscription you can not create more users!"
            ]);
        }

        // Validations
        $validator = Validator::make($request->all(), [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', Rule::unique('users')->where(function ($query) use ($distributor_id, $employee) {
                return $query->where('id', '!=', $employee->id);
            })],
            'primary_number' => ['required', 'numeric', 'digits:10', Rule::unique('users')->where(function ($query) use ($distributor_id, $employee) {
                return $query->where('id', '!=', $employee->id);
            })],
            'secondary_number' => ['numeric', 'digits:10'],
            'branch_id' => ['required'],
            'role' => ['required'],
            'date_of_joining' => ['required', 'date_format:Y-m-d'],
            'salary' => ['required', 'numeric'],
            'basic' => [ 'numeric'],
            'pf' => [ 'numeric'],
            'gratutity' => [ 'numeric'],
            'others' => [ 'numeric'],
            'pt' => [ 'numeric'],
            'over_time_ph' => [ 'numeric'],
            'income_tax' => [ 'numeric'],
            'total_experience' => [ 'numeric'],
            'working_hours' => ['required', 'numeric'],
            'product_commission' => ['required', 'numeric'],
            'service_commission' => ['required', 'numeric'],
        ], [
            'first_name.required' => 'Please enter first name!',
            'last_name.required' => 'Please enter first name!',
            'email.required' => 'Please enter email address!',
            'email.email' => 'Please enter valid email address!',
            'primary_number.required' => 'Please enter primary number!',
            'primary_number.numeric' => 'Please enter primary number!',
            'primary_number.digits' => 'Please enter valid primary number!',
            'secondary_number.numeric' => 'Please enter valid primary number!',
            'secondary_number.digits' => 'Please enter valid primary number!',
            'branch_id.required' => 'Please select branch!',
            'role.required' => 'Please select role!',
            'date_of_joining.required' => 'Please select date of joining!',
            'date_of_joining.date_format' => 'Please enter valid date (format : Y-m-d)!',
            'salary.required' => 'Please enter salary!',
            'salary.numeric' => 'Please enter valid salary!',
            'working_hours.required' => 'Please enter working hours!',
            'working_hours.numeric' => 'Please enter valid working hours!',
            'product_commission.required' => 'Please enter plan commission',
            'product_commission.numeric' => 'Please enter valid plan commission',
            'service_commission.required' => 'Please enter service commission',
            'service_commission.numeric' => 'Please enter valid service commission',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        // Upload certificates
        $certificate_arr = [];
        $path = 'storage/assets/employees/certificates/';
        foreach($request->certification_data as $certificate) {
            if(!empty($certificate['certification_attachment'])) {

                $path = 'assets/employees/certificates/';
                $certificate_name  = strtolower(str_replace(' ', '_',$certificate['name']));

                $certificate_arr[] = [
                    'name' => $certificate['name'],
                    'from' => $certificate['from'],
                    'to' => $certificate['to'],
                    'attachment' => Helper::createImageFromBase64($certificate['certification_attachment'], $certificate_name, $path)
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

        // Upload profile pic
        if(!empty($request->profile_pic)) {
            $path = 'assets/employees/profile_pics/';
            $first_name  = strtolower(str_replace(' ', '_',$request->first_name));
            $profile_pic_name = Helper::createImageFromBase64($request->profile_pic, $first_name, $path);
            $update_employee['profile_pic'] = $profile_pic_name;
        }

        // upload bank attachment
        if(!empty($request->bank_attachment)) {
            $path = 'assets/employees/bank_attachments/';
            $holder_name  = strtolower(str_replace(' ', '_',$request->holder_name));
            $bank_attachment_name = Helper::createImageFromBase64($request->bank_attachment, $holder_name, $path);
            $update_employee['bank_attachment'] = $bank_attachment_name;
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
            'total_experience' => $request->total_experience,

            // JSON Data
            'certificates' => json_encode($certificate_arr),
            'week_off' => json_encode($request->week_off),
            'employeer' => json_encode($request->employer_data),
            'updated_by' => $user->id,

            // Distributor_id
            'product_commission' => $request->product_commission,
            'service_commission' => $request->service_commission,
            'plan_commission' => $request->plan_commission,
        ];


        if($request->password !== "") {
            $update_employee['password'] = bcrypt($request->password);
        }
        $employee->fill($update_employee)->save();
        $employee->roles()->sync([$request->role]);
        $employee->services()->sync($request->services);

        if($employee->user_type == 1) {
            $message = "Employee successfully added!";
        } else {
            $message = "User successfully added!";
        }

        return response()->json([
            'status' => 'SUCCESS',
            'message' => $message,
            'data' => $update_employee,
        ]);
    }
}
