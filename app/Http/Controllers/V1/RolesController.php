<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use JWTAuth;
use App\Helpers\Helper;
use App\Models\User; 
use App\Models\Role;

class RolesController extends Controller
{
    public function rolesByName(Request $request)
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
        $name = $request->name;
		$paginate = $request->paginate ?? 0;
        $roles = Role::whereNotIn('name', ['owner', 'distributor']);
		if(!empty($name))
		$roles->where('name', 'like', "%{$name}%");
		if($paginate == 1) {
            $data = $roles->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $roles->get();
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
		
        /* if(!empty($roles)) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('roles')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "States not found."
            ]);
        } */
    }
}
