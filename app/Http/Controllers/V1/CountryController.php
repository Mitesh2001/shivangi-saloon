<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use JWTAuth;
use App\Helpers\Helper;
use App\Models\User;
use App\Models\Country; 
use App\Models\State; 

class CountryController extends Controller
{
    public function index()
    { 
        $countries = Country::where('deleted', 0)->get();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('countries')
        ]);
    }
}
