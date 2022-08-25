<?php
namespace App\Http\Controllers;
 
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Integration;
use App\Http\Requests;
use App\Http\Requests\Role\StoreRoleRequest;
use Illuminate\Http\Request;
use Session;
use Yajra\Datatables\Datatables;
use Ramsey\Uuid\Uuid;

class RolesController extends Controller
{
    /**
     * RolesController constructor.
     */
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

        $this->middleware('user.is.admin', ['only' => ['index', 'create', 'destroy', 'show', 'update']]);
    }

    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function indexData()
    {
        $roles = Role::select(['id', 'name', 'external_id', 'display_name'])->get();
        return Datatables::of($roles)
            ->addColumn('namelink', function ($roles) {
                // if ($roles->name == Role::OWNER_ROLE) { 
                //     return '<a href="'.route('roles.show', $roles->external_id).'">'.$roles->display_name.'</a>' . '<br>' . __('Extra: Owner is able to do the same as an administrator but also controls billing');
                // }
                // if ($roles->name == Role::ADMIN_ROLE) {
                //     return '<a href="'.route('roles.show', $roles->external_id).'">'.$roles->display_name.'</a>' . '<br>' . __('Extra: Administrator is able to update and create departments, integrations, and settings');
                // }
                return '<a href="'.route('roles.show', $roles->external_id).'">'.$roles->display_name.'</a>';
            })
            ->editColumn('permissions', function ($roles) {
                return $roles->permissions->map(function ($permission) {
                    return $permission->display_name;
                })->implode("<br>");
            }) 
            ->editColumn('view_button', function ($roles) {
                return '<a href="'.route('roles.show', $roles->external_id).'"><i class="flaticon-eye text-primary text-hover-primary" data-enquiry-id="'.$roles->external_id.'" data-toggle="tooltip" title="View Details"></i></a>';
            }) 
            // ->addColumn('action', function ($roles) {
            //     if ($roles->canBeDeleted() && auth()->user()->can('role-delete')) {
            //         return '
            //         <form action="">
            //             <input type="hidden" class="role_id" value="'.$roles->external_id.'">
            //             <button type="button" class="btn btn-link delete-role" data-toggle="tooltip" title="Delete Role"><i class="flaticon2-trash text-danger text-hover-warning"></i></button> 
            //         </form>
            //         ';
                    
            //         // return '
            //         // <form action="'. route('roles.destroy', $roles->external_id) .'" method="POST"> 
            //         //     <input type="hidden" name="_method" value="DELETE">
            //         //     <button type="submit" class="btn btn-link" onClick="return confirm(\'Are you sure?\')"" data-toggle="tooltip" title="Delete Role"><i class="flaticon2-trash text-danger text-hover-warning"></i></button> 
            //         //     <input type="hidden" name="_token" value="' . csrf_token(). '">
            //         // </form>';
            //     }
            // })
            ->rawColumns(['namelink','view_button'])
            ->make(true);
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return view('roles.index');
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * @return mixed
     */
    public function show($external_id)
    {
        $permissions_grouping = Permission::all()->groupBy('grouping');

        if (!Integration::whereApiType('file')->first()) {
            unset($permissions_grouping['document']);
        }
        
        return view('roles.show')
        ->withRole(Role::whereExternalId($external_id)->first())
        ->with('permissions_grouping', $permissions_grouping);
    }

    /**
     * @param StoreRoleRequest $request
     * @return mixed
     */ 
    public function store(StoreRoleRequest $request)
    {
        $roleName = $request->name;
        $roleDescription = $request->description;
        Role::create([
            'external_id' => Uuid::uuid4()->toString(),
            'name' => strtolower($roleName),
            'display_name' => ucfirst($roleName),
            'description' => $roleDescription
        ]);
        Session()->flash('success', __('Role successfully created!'));
        return redirect()->route('roles.index');
    }

    /**
     * @param $external_id
     * @return mixed
     */
    public function destroy($external_id)
    { 
        $role = Role::where('external_id', $external_id)->first();
        if (!$role->users->isEmpty()) {
            Session::flash('success', __("Can't delete role with users, please remove users"));
            return redirect()->route('roles.index');
        }
        if ($role->name !== Role::ADMIN_ROLE && $role->name !== Role::OWNER_ROLE) {
            $role->delete();
        } else {
            Session()->flash('success', __('Can not delete role'));
            return redirect()->route('roles.index');
        }
        Session()->flash('success', __('Role successfully deleted!'));
        return redirect()->route('roles.index');
    }

    /**
     * @param Request $request
     * @return mixed
     */ 
    public function update(Request $request, $external_id)
    {
        $allowed_permissions = [];

        if ($request->input('permissions') != null) {
            foreach ($request->input('permissions')
                     as $permissionId => $permission) {
                if ($permission === '1') {
                    $allowed_permissions[] = (int)$permissionId;
                }
            }
        } else {
            $allowed_permissions = [];
        }

        $role = Role::whereExternalId($external_id)->first();

        $role->permissions()->sync($allowed_permissions);
        $role->save();
        Session()->flash('success', __('Role successfully updated!'));
        return redirect()->route('roles.index');
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkRoleDelete(Request $request) 
    {
        $external_id = $request->external_id;
        $role = $this->findByExternalId($external_id);
        
        if (!$role->users->isEmpty()) { 
            return response()->json([
                'status' => false,
                'message' => "Can't delete role with users, please remove users",
            ]);
        } else {
            return response()->json([
                'status' => true,
            ]);
        } 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxRoleDelete(Request $request)
    {
        $external_id = $request->external_id;  
        $role = $this->findByExternalId($external_id);
        $role->delete();

        Session()->flash('success', __('Role successfully deleted!'));
        return response()->json([
            'status' => true,
            'message' => "Role deleted successfully!"
        ]);
    }

    
    public function findByExternalId($external_id)
    {
        return Role::where('external_id', $external_id)->firstOrFail();
    }
}
