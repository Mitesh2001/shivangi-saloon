<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use JWTAuth;
use App\Helpers\Helper;
use App\Models\User;
use App\Models\Status;

class StatusController extends Controller
{
    public function statusByName(Request $request)
    {
        $title = $request->title;
		$paginate = $request->paginate ?? 0;
        $statuses = Status::orderBy('title', 'asc');
		if(!empty($title))
		$statuses->where('title', 'like', "%{$title}%");//->get();

		if($paginate == 1) {
            $data = $statuses->paginate();
            $count = count($data);
        } else {
            $data = [];
            $data['data'] = $statuses->get();
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

        /* if(!empty($statuses)) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('statuses')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "States not found."
            ]);
        } */
    }
}
