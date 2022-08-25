<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use JWTAuth;
use App\Helpers\Helper;
use App\Models\User;
use App\Models\Country; 
use App\Models\State;

class StateController extends Controller
{
    public function index($country_id)
    {  
        $states = State::where('country_id', $country_id)->where('deleted', 0)->get();

        if(!empty($states)) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('states')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "States not found."
            ]);
        }
    }
}
