<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
 
use DB;
use Auth; 
use JWTAuth;
use Image;  
use Datatables;
 
use App\Models\User;
use App\Models\Enquiry;
use App\Models\EnquiryType;

class InquiryTypeController extends Controller
{
    public function searchByName(Request $request)
    {
        $name = $request->name;
        $paginate = $request->paginate ?? 0; 
        /* if(empty($name)) {
            return response()->json([
                'status' => 'FAIL',
                'data' => "Please search by inquriry type!",
            ]);
        } */

        $enquiry_types = EnquiryType::orderBy('name', 'asc');
		if(!empty($name))
		$enquiry_types->where('name', 'like', "%{$name}%");//->get();
		
		if($paginate == 1) {
            $data = $enquiry_types->paginate();
            $count = count($data);
        } else { 
            $data = [];
            $data['data'] = $enquiry_types->get();
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

        /* if(!empty($enquiry_types)) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('enquiry_types')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "States not found."
            ]);
        } */
    }
}
