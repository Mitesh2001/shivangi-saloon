<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Branch;
use App\Models\Role;

class EmployeesController extends Controller
{
            
    public function __construct()
    {
        $this->middleware('permission:employee-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:employee-create', ['only' => ['create','store']]);
		$this->middleware('permission:employee-update', ['only' => ['edit','update']]);
		$this->middleware('permission:employee-delete', ['only' => ['destroy']]);  
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('employees.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
        $data['years'] = $this->years();
        $data['months'] = $this->allMonths();
        $data['roles'] = Role::pluck('name', 'id')->toArray();
        $data['branch'] = Branch::pluck('name', 'id')->toArray(); 
 
        return view('employees.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
}
