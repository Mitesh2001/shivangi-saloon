<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Validation\Rule;
use Datatables;
   
use DB;
use PDF;
use App\Helpers\Helper;
use Illuminate\Http\Response;

use App\Models\Report;
use App\Models\User; 
use App\Models\Appointment; 
use App\Models\Branch; 
use App\Models\Vendor; 
use App\Models\EnquiryType; 
use App\Models\Status; 
 
use App\Models\Distributor;

// use App\Http\Requests\Unit\StoreUnitRequest;
// use App\Http\Requests\Unit\UpdateUnitRequest;
  
class ReportsController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('permission:reports-view', ['only' => ['index', 'show']]);
		$this->middleware('permission:reports-create', ['only' => ['create','store']]);
		$this->middleware('permission:reports-edit', ['only' => ['edit','update']]); 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['is_system_user'] = Helper::is_system_user();  
        $data['distributors'] = Distributor::all();

        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        } 

        return view('reports.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['is_system_user'] = Helper::getDistributorId(); 
        return view('reports.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {  
        $user_id = Auth::id();

        $form_data;
        parse_str($request->form_data, $form_data);
        $rules_query = $request->rules_query; 
        $rules_set = $request->rules_set; 
        $select_columns = json_encode($form_data['select_columns']); 

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $form_data['distributor_id'];
        }  
        
        if(Helper::allowViewOnly($distributor_id)) {
            return redirect()->back()->with('error', 'Subscription has been expired. please renew.');
        }

        if($form_data['id'] != "" && $form_data['id'] != null) { 
            $report = Report::find($form_data['id']);  
            $report->updated_by = $user_id;
 
            $message = "Report successfully updated";
        } else {
            $report = new Report();
            $report->external_id = Uuid::uuid4()->toString();
            $report->created_by = $user_id;

            $message = "Report successfully added";
        }

        $report->name = $form_data['name'];
        $report->module = $form_data['module'];
        $report->group_by = $form_data['group_by'];
        $report->group_by_two = $form_data['group_by_two'];
        $report->select_columns = $select_columns;
        $report->rules_query = $rules_query;
        $report->rules_set = $rules_set; 
        $report->distributor_id = $distributor_id; 
        $report->save();
   
        
        Session()->flash('success', __($message));
        // return redirect()->route('reports.index');
        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }

    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {   
        $reports = Report::orderBy('id', 'desc'); 

        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id != 0) { // Check if distributor
            $reports->where('distributor_id', $distributor_id);
        } 

        $reports = $reports->get();
  
        return Datatables::of($reports)
            ->addColumn('distributor', function ($branch) {
                return  $branch->getDistributor->name ?? "";
            }) 
            ->addColumn('name', function ($reports) {
                return  $reports->name;
            })  
            ->addColumn('module', function ($reports) {
                return  $reports->module;
            })  
            ->addColumn('action', function ($reports) {
                $url = route('reports.show', $reports->external_id) . "?export_data=0";
				$html = '<form action="'.route('reports.destroy', $reports->external_id).'" class="d-flex" method="POST">';
				if(\Entrust::can('reports-view')) 
                $html .= '<a href="'.$url.'" class="btn btn-link" data-toggle="tooltip" title="Run Report"><i class="fas fa-play text-primary text-hover-primary"></i></a>';
                if(\Entrust::can('reports-edit') && !Helper::allowViewOnly($reports->distributor_id)) 
				$html .= '<a href="'.route('reports.edit', $reports->external_id).'" class="btn btn-link" data-toggle="tooltip" title="Edit reports"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';
				$html .= '<input type="hidden" name="_method" value="DELETE">'; 
                // $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-reports" data-toggle="tooltip" title="Delete reports"><i class="flaticon2-trash text-danger text-hover-warning"></i></button>';
                $html .= '<input type="hidden" class="reports_id" value="'.$reports->external_id.'">'; 
				$html .= csrf_field();
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['distributor', 'name', 'module', 'action'])
            ->make(true);
    }
 
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($external_id)
    { 
        $report = $this->findByExternalId($external_id);
        $data['report'] = $report; 
        $data['is_system_user'] = Helper::getDistributorId();  
        $data['distributor'] = Distributor::where('id', $report->distributor_id)->first();
  
        return view('reports.edit')->with($data);
    }
 
    /**
     * Display the specified resource.
     *
     * @param  int  $external_id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $external_id)
    {
        $export_data = $request->get('export_data');
        $report = Report::where("external_id", $external_id)->first();
 
        $data['report'] = $report; 
        $module = $report->module;
        $table = $this->getTableName($report->module);
        $select_columns = implode(", " ,json_decode($report->select_columns));
        $rules_query = $report->rules_query;
        $group_by = $report->group_by;
        $group_by_two = $report->group_by_two;


        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $report->distributor_id;
        }   

        $data = $this->runReport($module, $table, $select_columns, $rules_query, $group_by, $group_by_two, $distributor_id);
        $data['report'] = $report;
        // $data['table'] = $this->getHtmlTable($data);
          
        $data['table'] = $this->getReportTable($data);
 

        if($export_data == 1) {

            $template_content = Helper::reportTemplate(); 
            $pdf_template = str_replace("{{#title}}", $report->name, $template_content); 
            $pdf_template = str_replace("{{#template_content}}", $data['table'], $pdf_template);
            $pdfName = str_replace(' ','_',$report->name) ."_". date('d_m_Y_h_i_s').'.pdf';
            

            $pdf = PDF::loadHTML($pdf_template); 
            // return $pdf->setPaper('landscape')->setWarnings(false)->stream();
            return $pdf->setPaper('landscape')->setWarnings(false)->download($pdfName);
        }
        
        if($export_data == 2) {

            $this->exportCsv($data);
        }

        if($export_data == 0) {
            return view('reports.show')->with($data);
        } 
    }


    public function exportCsv($data)
    {
        $result = json_decode($data['result']);
            
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=assessment-data.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
         
        $callback = function() use( $result) {
            $file = fopen('php://output', 'w');
            $columns = array();
            foreach($result[0] as $key => $value) {
                $columns[]  = $key;  
            }
            fputcsv($file, $columns);

            foreach ($result as $key => $data_row) {
                $row=array();
                foreach((array)$data_row as $data) 
                {
                    $row[]  = $data;  
                } 
                fputcsv($file, $row);
            } 
            fclose($file); 
        };    
        return response()->stream($callback(), 200, $headers)->send(); // this is the solution;
    }
	
	public function appointmentReport()
    {
        $data['statuses'] = Status::all(); 
        $data['branches'] = Branch::all(); 
        $data['statuses_pluck'] = Status::pluck('title', 'id');  
        $data['is_system_user'] = Helper::is_system_user();  
        $data['distributors'] = Distributor::all();

        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        } 

        return view('reports.appointments')->with($data);
    }
	
	public function clientReport()
    {
        $data['is_system_user'] = Helper::is_system_user();
        $data['status_wise'] = 0;
		
        $distributor_id = Helper::getDistributorId(); 
        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        } 

        return view('reports.clients')->with($data);
    }

    public function getReportTable($data) 
    {
        $error = $data['error'];
        $error_message = $data['error_message'];
 
        if($error == 1) {
            $template_content = Helper::reportErrorTemplate(); 
            $html_table = str_replace("{{#error_message}}", $error_message, $template_content);
        }

        if($error == 0) {

            $report_keys = $this->getReportKeys($data['result']);
            $report_data = $this->getReportValues($data['result']); 
            
            $report_keys_html = ""; 
            foreach($report_keys as $key) {
                $report_keys_html .= "<th style='text-align:center'>".$key."</th>";
            }

            $report_data_html = "";
            foreach($report_data as $tr) {
                $report_data_html .= "<tr>";
                foreach($tr as $td) {
                    $report_data_html .= "<td style='text-align:center'>".$td."</td>";
                }
                $report_data_html .= "</tr>";
            }
            
            $template_content = Helper::reportTable(); 
            $html_table = str_replace("{{#thead_content}}", $report_keys_html, $template_content);
            $html_table = str_replace("{{#tbody_content}}", $report_data_html, $html_table);
        } 

        return $html_table;
    } 
    
    public function getReportKeys($report_data)
    {
        $report_data = json_decode($report_data);
        $array_keys = [];

        foreach($report_data[0] as $key => $value ) { 
            array_push($array_keys, $key);
        }
        
        return $array_keys;
    }

    public function getReportValues($report_data)
    {
        $report_data = json_decode($report_data);
        // $report_values;
        $i = 0;

        foreach($report_data as $obj) { 
            foreach($obj as $key => $value) {
                $report_values[$i][] = $value;
            }
            $i++;
        }
        
        return $report_values;
    }

    public function runWithoutSave(Request $request)
    {
        // $form_data;
        parse_str($request->form_data, $form_data);
  
        $module = $form_data['module'];
        $table = $this->getTableName($form_data['module']);
        $select_columns = implode(", " ,$form_data['select_columns']);
        $rules_query = $request->rules_query; 
        $group_by = $form_data['group_by'];
        $group_by_two = $form_data['group_by_two'];

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $form_data['distributor_id'];
        }    

        $data = $this->runReport($module, $table, $select_columns, $rules_query, $group_by, $group_by_two, $distributor_id);
          
        $table = $this->getReportTable($data);
        return $table;
    }
 
    public function runReport($module, $table, $select_columns, $rules_query, $group_by, $group_by_two, $distributor_id)
    { 
        // DB::enableQueryLog(); // Enable query log
        $result = []; // blank array (for count in frontenc)
        $error = 0;
        $error_message = "";
        // try {
  
            $query = DB::table($table);

            if(!empty($select_columns)) {
                $query->selectRaw($select_columns);
            } else {
                $query->select("*");
            }
    
            // $query->whereRaw($rules_query);
            
           
            if($module == "orders") {
                $query->leftJoin('clients', 'clients.id', '=', "$table.client_id");
                // $query->leftJoin('branches', 'branches.id', '=', "$table.branch_id");
                $query->leftJoin('branches', function ($join) use ($distributor_id, $rules_query, $table) {
                    $join->on('branches.id', '=', "orders.branch_id");
                });
            }
            // echo $module;
            // exit();
            if($module == "product") { 
                
                // $query->leftJoin('units', 'units.id', '=', "products.unit_id"); 

                $query->leftJoin('units', function ($join) use ($distributor_id, $rules_query, $table) {
                    $join->on('units.id', '=', "$table.unit_id");
                });
               
            }

            if($module == "clients") {
                // $query->leftJoin('contacts', 'contacts.client_id', '=', "$table.id"); 
                $query->leftJoin('contacts', function ($join) use ($distributor_id, $rules_query, $table) {
                    $join->on('contacts.client_id', '=', "$table.id");
                });
            }
    
            if($module == "inventory") {
                $query->leftJoin('vendors', 'vendors.id', '=', "$table.source_id");
                // $query->leftJoin('branches', 'branches.id', '=', "$table.branch_id");
                $query->leftJoin('branches', function ($join) use ($distributor_id, $rules_query, $table) {
                    $join->on('branches.id', '=', "$table.branch_id");
                });
            }
    
            if($module == "employee") { 
                // $query->leftJoin('branches', 'branches.id', '=', "$table.branch_id");
                $query->leftJoin('branches', function ($join) use ($distributor_id, $rules_query, $table) {
                    $join->on('branches.id', '=', "$table.branch_id");
                });
            }
    
            if($module == "inquiry") {
                // $query->leftJoin('branches', 'branches.id', '=', "$table.branch_id"); 
                $query->leftJoin('statuses', 'statuses.id', '=', "$table.status_id"); 
                $query->leftJoin('enquiry_types', 'enquiry_types.id', '=', "$table.enquiry_type"); 
                $query->leftJoin('clients', 'clients.id', '=', "$table.client_id"); 
                $query->leftJoin('branches', function ($join) use ($distributor_id, $rules_query, $table) {
                    $join->on('branches.id', '=', "$table.branch_id");
                });
                // $query->leftJoin('branches', 'branches.id', '=', "$table.branch_id"); 
                // $query->whereRaw($rules_query);
            }
    
            if($module == "deals_and_discount") {
                // $query->leftJoin('tags', 'tags.id', '=', "$table.customer_segment_special"); 
                $query->leftJoin('tags', function ($join) use ($distributor_id, $rules_query, $table) {
                    $join->on('tags.id', '=', "$table.customer_segment_special");
                }); 
            }
    
            if($module == "appointment") {
                $query->leftJoin('clients', 'clients.id', '=', "$table.client_id"); 
                // $query->leftJoin('branches', 'branches.id', '=', "$table.branch_id"); 
                $query->leftJoin('users', 'users.id', '=', "$table.user_id"); 
                $query->leftJoin('statuses', 'statuses.id', '=', "$table.status_id"); 
                $query->leftJoin('branches', function ($join) use ($distributor_id, $rules_query, $table) {
                    $join->on('branches.id', '=', "$table.branch_id");
                });
            } 
            
            if($module == "branches") { 
                $query->leftJoin('users', 'users.branch_id', '=', "$table.id");  
                if($group_by == 'users.id' || $group_by_two == 'users.id') {
                    $query->where('users.deleted_at', null);
                }
                $query->leftJoin('appointments', 'appointments.branch_id', '=', "$table.id");  
                if($group_by == 'appointments.id' || $group_by_two == 'appointments.id') {
                    $query->where('appointments.deleted_at', null);
                }
                $query->leftJoin('enquiries', 'enquiries.branch_id', '=', "$table.id"); 
                if($group_by == 'enquiries.id' || $group_by_two == 'enquiries.id') {
                    $query->where('enquiries.deleted_at', null);
                }
                $query->leftJoin('orders', 'orders.branch_id', '=', "$table.id");   
                if($group_by == 'orders.id' || $group_by_two == 'orders.id') {
                    $query->where('orders.deleted_at', null);
                }
                $query->groupBy('branches.id');
                // $query->leftJoin('appointments', 'appointments.branch_id', '=', "$table.id");  
                // $query->leftJoin('enquiries', 'enquiries.branch_id', '=', "$table.id");  
                // $query->leftJoin('orders', 'orders.branch_id', '=', "$table.id");  
            }  
            $query->where("$table.distributor_id", $distributor_id);
            $query->where("$table.deleted_at", null); 

            if(!empty($rules_query)) { 
                $query->whereRaw("(".$rules_query.")");
            }

            if($module !== "branches") {

                if($group_by != "") {  
                    $distributor_condition = $this->getGroupByQueryAlias($group_by);
                    $query->groupBy($group_by); 
    
                    // if(!empty($distributor_condition)) {
                    //     $query->where($distributor_condition, $distributor_id);
                    // } else {
                    // }
                    // $query->where("$table.distributor_id", $distributor_id);
                }
    
                if($group_by_two != "") { 
                    $distributor_condition_two = $this->getGroupByQueryAlias($group_by_two);
                    $query->groupBy($group_by_two);
    
                    // if(!empty($distributor_condition_two)) {
                    //     $query->where($distributor_condition_two, $distributor_id);
                    // } else {
                    //     $query->where($distributor_condition, $distributor_id);
                    // }
                }  
            }


            // if($group_by == "" && $group_by_two == "") {
            //     $query->where("$table.distributor_id", $distributor_id);
            // }

            // $list = $query->lists();
            $result = $query->get();

            if(count($result) == 0) {
                $error = 1;
                $error_message = " <tr class='bg-light text-center'><td colspan='100%'>No Records!</td></tr>";
            }

        // } catch(\Illuminate\Database\QueryException $e) {
        //     $error = 1;
        //     $error_message = " <tr class='bg-light text-center'><td>Please select appropriate columns!</td></tr>";
        // } 


        // dd($list);

        // dd(DB::getQueryLog()); // Show results of log

        // dd($result); 

        $data['result'] = json_encode($result); 
        $data['error'] = $error; 
        $data['error_message'] = $error_message; 
        // $data['report'] = $report; 

        return $data;
    }
 
    private function getGroupByQueryAlias($group_by)
    {
        $arr = [
            "branches.id" => "branches.distributor_id",
            "clients.id" => "clients.distributor_id",
            "vendors.id" => "vendors.distributor_id", 
            "enquiries.enquiry_type" => "", 
        ];

        return $arr[$group_by] ?? ""; 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($external_id)
    {   
        $report = $this->findByExternalId($external_id);
        $report->delete();

        Session()->flash('success', __('Report successfully deleted!'));
        return redirect(route('reports.index'));
    }

        /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxReportDelete(Request $request)
    {
        $external_id = $request->external_id;  
        $report = $this->findByExternalId($external_id);
        $report->delete();

        Session()->flash('success', __('Report successfully deleted!'));
        return response()->json([
            'status' => true,
            'message' => "Report deleted successfully!"
        ]);
    }

    public function getTableName($selected_module)
    {
        $arr = [
            "orders" => "orders",
            "clients" => "clients",
            "deals_and_discount" => "deals_and_discounts",
            "inventory" => "stock_income_history",
            "employee" => "users",
            "product" => "products",
            "appointment" => "appointments",
            "inquiry" => "enquiries",
            "enquiry_type" => "enquiry_type",
            'branches' => 'branches',
        ];
 
        return $arr[$selected_module];
    }

    public function getModuleRuleSet(Request $request) 
    {  
        $option = $request->option; 
        
        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { //is system user
            $distributor_id = $request->distributor_id;
        }

        $group_by_options = $this->getGroupByColumns($option); 
        $columns = $this->getColumns($option);
        $rule_set = $this->getRuleSet($option, $distributor_id);

        return response()->json([
            'group_by_options' => $group_by_options,
            'columns' => $columns,
            'rule_set' => $rule_set,
        ]);
    }

    public function getRuleSet($option, $distributor_id)
    {
        $rule_sets = [
            // ['equal', 'not_equal', 'is_null', 'is_not_null']
            "orders" => [
                [
                    "id" => "orders.order_uid",
                    "label" => "Order Id",
                    "type" => "string",
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ], 
                [
                    "id" => "clients.name",
                    "label" => "Client Name",
                    "type" => "string",
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "orders.payment_mode",
                    "label" => "Payment Mode",
                    "type" => "string",
                    "input" => "select",
                    "values" => config('global.payment_modes'),
                    "operators" => ['equal', 'not_equal']
                ], 
                [
                    "id" => "orders.final_amount",
                    "label" => "Final Amount",
                    "type" => "integer",  
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ], 
                [
                    "id" => "branches.id",
                    "label" => "Branch",
                    "input" => "select", 
                    "values" => Branch::getBranches($distributor_id),
                    "operators" => ['equal', 'not_equal'] 
                ], 
                [
                    "id" => "orders.is_payment_pending",
                    "label" => "Payment Pending",
                    "input" => "select", 
                    "values" => ['yes' => 'Yes', 'no' => 'No'],
                    "operators" => ['equal'] 
                ], 
                [
                    "id" => "DATE_FORMAT(orders.created_at, '%Y/%m/%d')",
                    "label" => "Date of Order",
                    "type" => 'date', 
                    "plugin" => 'datepicker',
                    "plugin_config" => config('global.date_picker'),
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ], 
            ], 
            "clients" => [ 
                [
                    "id" => "clients.name",
                    "label" => "Client Name",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "contacts.email",
                    "label" => "Email Address",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "contacts.primary_number",
                    "label" => "Primary Number",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "contacts.secondary_number",
                    "label" => "Secondary Number",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "DATE_FORMAT(clients.date_of_birth, '%Y/%m/%d')", 
                    "label" => "Date of Birth",
                    "type" => 'date', 
                    "plugin" => 'datepicker',
                    "plugin_config" => config('global.date_picker'),
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ], 
                [
                    "id" => "DATE_FORMAT(clients.anniversary, '%Y/%m/%d')", 
                    "label" => "Anniversary",
                    "type" => 'date', 
                    "plugin" => 'datepicker',
                    "plugin_config" => config('global.date_picker'),
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ], 
                [
                    "id" => "clients.city",
                    "label" => "City",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "clients.zipcode",
                    "label" => "Zip Code",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ],
                [
                    "id" => "clients.client_type",
                    "label" => "Client Type",
                    "type" => 'string', 
                    "input" => "select", 
                    "values" => ['A' => 'A', 'B' => 'B', 'C' => 'C'],
                    "operators" => ['equal', 'not_equal']
                ],
            ], 
            "deals_and_discount" => [  
                // [
                //     "id" => "deals_and_discounts.segament_name",
                //     "label" => "Segament Name",
                //     "type" => 'string', 
                //     "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                // ],  
                [
                    "id" => "deals_and_discounts.deal_code",
                    "label" => "Deal Code",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ],  
                [
                    "id" => "DATE_FORMAT(deals_and_discounts.validity, '%Y/%m/%d')", 
                    "label" => "Validity",
                    "type" => 'date', 
                    "plugin" => 'datepicker',
                    "plugin_config" => config('global.date_picker'),
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ],  
                [
                    "id" => "deals_and_discounts.start_at",
                    "label" => "Start At",
                    "type" => 'time', 
                    "plugin" => 'datepicker',
                    "plugin_config" => config('global.date_picker'),
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ],  
                [
                    "id" => "deals_and_discounts.end_at",
                    "label" => "End At",
                    "type" => 'time', 
                    "plugin" => 'datepicker',
                    "plugin_config" => config('global.date_picker'),
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ],  
                [
                    "id" => "deals_and_discounts.is_active",
                    "label" => "Is Active",
                    "input" => "select", 
                    "values" => [1 => 'Yes', 0 => 'No'],
                    "operators" => ['equal'] 
                ], 
            ], 
            "inventory" => [
                [
                    "id" => "stock_income_history.invoice_number",
                    "label" => "Invoice Number",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ],
                [
                    "id" => "DATE_FORMAT(stock_income_history.date, '%Y/%m/%d')", 
                    "label" => "Date",
                    "type" => 'date', 
                    "plugin" => 'datepicker',
                    "plugin_config" => config('global.date_picker'),
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ], 
                [
                    "id" => "stock_income_history.invoice_type",
                    "label" => "Invoice Type",
                    "input" => "select", 
                    "values" => [1 => 'Tax Invoice'],
                    "operators" => ['equal'] 
                ],
                [
                    "id" => "stock_income_history.extra_freight_charges",
                    "label" => "Extra Freight Charges",
                    "type" => "integer",  
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ], 
                [
                    "id" => "vendors.name",
                    "label" => "Vendor",
                    "input" => "select", 
                    "values" => Vendor::getVendors($distributor_id),
                    "operators" => ['equal', 'not_equal'] 
                ], 
                [
                    "id" => "stock_income_history.amount_paid",
                    "label" => "Amount Paid",
                    "type" => "integer",  
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ], 
                [
                    "id" => "stock_income_history.payment_type",
                    "label" => "Payment Type",
                    "input" => "select", 
                    "values" => [
                        'cash' => 'cash',
                        'credit card' => 'Credit Card',
                        'debit card' => 'Debit Card',
                        'phonepe' => 'Phonepe',
                        'google pay' => 'Google Pay',
                        'paytm' => 'paytm',
                        'bank a' => 'bank_a'
                    ],  
                    "operators" => ['equal', 'not_equal']
                ], 
                [
                    "id" => "stock_income_history.payment_status",
                    "label" => "Payment Status",
                    "input" => "select", 
                    "values" => [
                        'Paid' => 'Paid',
                        'Partialy Paid' => 'Partialy Paid'
                    ],  
                    "operators" => ['equal', 'not_equal']
                ], 
                [
                    "id" => "branches.id",
                    "label" => "Branch",
                    "input" => "select", 
                    "values" => Branch::getBranches($distributor_id),
                    "operators" => ['equal', 'not_equal'] 
                ], 
            ],
            "employee" => [
                [
                    "id" => "users.first_name",
                    "label" => "First Name",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "users.last_name",
                    "label" => "Last Name",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "users.expertise",
                    "label" => "Expertise",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ],  
                [
                    "id" => "branches.id",
                    "label" => "Branch",
                    "input" => "select", 
                    "values" => Branch::getBranches($distributor_id),
                    "operators" => ['equal', 'not_equal'] 
                ], 
                [
                    "id" => "users.primary_number",
                    "label" => "Primary Number",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "users.secondary_number",
                    "label" => "Secondary Number",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ],
                [
                    "id" => "users.email",
                    "label" => "Email Address",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
            ],
            "product" => [
                [
                    "id" => "products.name",
                    "label" => "Name",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'begins_with', 'not_begins_with', 'contains', 'not_contains']
                ],
                [
                    "id" => "products.sales_price",
                    "label" => "Sales Price",
                    "type" => 'integer', 
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ],
                [
                    "id" => "products.purchase_price",
                    "label" => "Purchase Price",
                    "type" => 'integer', 
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ],
                [
                    "id" => "products.sku_code",
                    "label" => "SKU CODE",
                    "type" => "string", 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ],
                [
                    "id" => "products.type",
                    "label" => "Type",
                    "input" => "select", 
                    "values" => [1 => 'service', 0 => 'product'],
                    "operators" => ['equal','not_equal']                
                ],
                [
                    "id" => "products.reorder_qty",
                    "label" => "ReOrder Quantity",
                    "type" => "integer", 
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ]                 
            ],
            "inquiry" => [
                [
                    "id" => "clients.name",
                    "label" => "Client Name",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "enquiries.contact_number",
                    "label" => "Primary Number",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "enquiries.email",
                    "label" => "Email Address",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ],  
                [
                    "id" => "enquiries.enquiry_for",
                    "label" => "Inquiry For",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "enquiries.enquiry_type",
                    "label" => "Inquiry Type",
                    "input" => "select", 
                    "values" => EnquiryType::getInquiryTypes(),
                    "operators" => ['equal', 'not_equal'] 
                ], 
                [
                    "id" => "branches.id",
                    "label" => "Branch",
                    "input" => "select", 
                    "values" => Branch::getBranches($distributor_id),
                    "operators" => ['equal', 'not_equal'] 
                ], 
                [
                    "id" => "statuses.id",
                    "label" => "Status",
                    "input" => "select", 
                    "values" => Status::getStatus(),
                    "operators" => ['equal', 'not_equal'] 
                ], 
                [
                    "id" => "DATE_FORMAT(enquiries.date_to_follow, '%Y/%m/%d')", 
                    "label" => "Date To Follow",
                    "type" => 'date', 
                    "plugin" => 'datepicker',
                    "plugin_config" => config('global.date_picker'),
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ], 
            ],
            "appointment" => [
                [
                    "id" => "clients.name",
                    "label" => "Client Name",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "users.first_name",
                    "label" => "Representative",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "appointments.contact_number",
                    "label" => "Primary Number",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "appointments.email",
                    "label" => "Email Address",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'is_null','begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ],   
                [
                    "id" => "branches.id",
                    "label" => "Branch",
                    "input" => "select", 
                    "values" => Branch::getBranches($distributor_id),
                    "operators" => ['equal', 'not_equal'] 
                ], 
                [
                    "id" => "statuses.id",
                    "label" => "Status",
                    "input" => "select", 
                    "values" => Status::getStatus(),
                    "operators" => ['equal', 'not_equal'] 
                ], 
                [
                    "id" => "DATE_FORMAT(appointments.date, '%Y/%m/%d')", 
                    "label" => "Date",
                    "type" => 'date', 
                    "plugin" => 'datepicker',
                    "plugin_config" => config('global.date_picker'), 
                    "operators" => ['equal', 'not_equal', 'is_null', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
                ], 
            ],
            "branches" => [
                [
                    "id" => "branches.name",
                    "label" => "Branch Name",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "branches.city",
                    "label" => "City",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ], 
                [
                    "id" => "users.first_name",
                    "label" => "Primary Number",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ],  
                [
                    "id" => "branches.primary_contact_number",
                    "label" => "Contact Number",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ],  
                [
                    "id" => "branches.primary_email as `Primary Email`",
                    "label" => "Primary Email",
                    "type" => 'string', 
                    "operators" => ['equal', 'not_equal', 'begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
                ],  
            ],
        ]; 

        return $rule_sets[$option] ?? [];
    }

    public function getColumns($option)
    {
        $group_by_columns = [
            "orders" => [
                "orders.order_uid as `Order Id`" => 'Order ID',  
                "clients.name as `Client Name`" => 'Client Name',  
                "orders.payment_mode as `Payment Metod`" => 'Payment Mode',  
                "orders.final_amount as `Amount`" => 'Amount',  
                "branches.name as `Branch`" => 'Branch',  
                "orders.is_payment_pending as `Payment Pending`" => 'Payment Pending',  
                'DATE_FORMAT(orders.created_at, "%Y/%m/%d") `Date of Order`' => 'Date of Order',  
            ], 
            "clients" => [
                "clients.name as `Name`" => "Name",
                "contacts.email as `Email`" => "Email",
                "contacts.primary_number as `Primary Number`" => "Primary Number",
                "contacts.secondary_number as `Secondary Number`" => "Secondary Number",
                "clients.date_of_birth as `Birthday`" => "Birthday",
                "clients.anniversary as `Anniversary`" => "Anniversary",
                "clients.city as `City`" => "City",
                "clients.zipcode as `Zip Code`" => "Zip Code",
                'clients.client_type as `Client Type`' => "Client Type"
            ],
            "deals_and_discount" => [
                "deals_and_discounts.deal_name as `Name`" => "Name", 
                "IFNULL(tags.name, customer_segment_client) as Segament" => "Segament", 
                "deals_and_discounts.deal_code as `Deal Code`" => "Deal Code", 
                'DATE_FORMAT(deals_and_discounts.validity, "%d/%m/%Y") as `Validity`' => "Validity", 
                'TIME_FORMAT(deals_and_discounts.start_at, "%h:%i %p") as `Start At`' => "Start At", 
                'TIME_FORMAT(deals_and_discounts.end_at, "%h:%i %p") as `End At`' => "End At", 
                'IF(is_active = 1, "Yes","No") as `Is Active`' => "Is Active", 
            ],
            "inventory" => [
                "stock_income_history.invoice_number as `Invoice Number`" => "Invoice Number", 
                "stock_income_history.date as `Date`" => "Date", 
                "stock_income_history.invoice_type as `Invoice Type`" => "Invoice Type", 
                "stock_income_history.invoice_value as `Invoice Value`" => "Invoice Value", 
                "stock_income_history.extra_freight_charges as `Extra Freight Charges`" => "Extra Freight Charges", 
                "vendors.name as `Vendor`" => "Vendor", 
                "stock_income_history.amount_paid as `Amount Paid`" => "Amount Paid", 
                "stock_income_history.payment_type as `Payment Type`" => "Payment Type", 
                "stock_income_history.payment_status as `Payment Status`" => "Payment Status", 
                "branches.name as `Branch`" => "Branch", 
            ],
            "employee" => [ 
                "users.first_name as `First Name`" => "First Name",
                "users.last_name as `Last Name`" => "Last Name",
                "users.expertise as `Expertise`" => "Expertise",
                // "roles.name" => "Role",
                "branches.name as `Branch`" => "Branch",
                "users.primary_number as `Primary Number`" => "Primary Number",
                "users.secondary_number as `Secondary Number`" => "Secondary Number",
                "users.email as `Email Address`" => "Email Address",
            ],
            "product" => [
                "products.name as `Name`" => "Name",
                'IF(type = 1,"Service", "Product") as `Type`' => "Type",
                "products.sales_price as `Sales Price`" => "Sales Price",                
                // "packages.name as `Package`" => 'Package',
                "products.purchase_price as `Purchase Price`" => "Purchase Price",
                "products.sku_code as `SKU Code`" => "SKU Code",
                "units.name as `Unit`" => 'Unit',
                "products.reorder_qty as `ReOrder Quantity`" => 'ReOrder Quantity',
            ],
            "inquiry" => [
                "clients.name as `Client Name`" => 'Client Name', 
                "enquiries.contact_number as `Contact`" => 'Contact', 
                "enquiries.email as `Email`" => 'Email', 
                "enquiries.enquiry_for as `Inquiry For`" => 'Inquiry For', 
                "enquiry_types.name as `Inquiry Type`" => 'Inquiry Type', 
                "branches.name as `Branch`" => 'Branch', 
                "statuses.title as `Status`" => 'Status', 
                'DATE_FORMAT(enquiries.date_to_follow, "%d/%m/%Y") as `Date to Follow`' => 'Date to Follow', 
            ],
            "appointment" => [
                "clients.name as `Client`" => 'Client',  
                "appointments.contact_number as `Contact`" => 'Contact',  
                "appointments.email as `Email`" => 'Email',  
                "branches.name as `Branch`" => 'Branch',  
                "users.first_name as `Representative`" => 'Representative',  
                "appointments.date as `Date`" => 'Date',  
                "statuses.title as `Status`" => 'Status',    
            ], 
            "branches" => [
                "branches.name as `Name`" => 'Name',    
                "branches.city as `City`" => 'City',    
                "users.first_name as `Contact Person`" => 'Contact Person',    
                "branches.primary_contact_number as `Contact Number`" => 'Contact Number',    
                "branches.primary_email as `Primary Email`" => 'Primary Email',    
            ], 
        ];
 
        $html = "";
        if(!empty($option)) {
            foreach($group_by_columns[$option] as $key => $value) {
                $html .= "<option value='$key'>$value</option>";
            }; 
        }
        return $html;
    }

    // Available select options 
    public function getGroupBySelectOptions(Request $request)
    {
        $option = $request->option; 
        $group_by = $request->group_by;

        $group_by_options = [
            "orders" => [
                'clients.name as `Client Name`' => "Client Name",
                'branches.name as `Branch`' => "Branch",
                // 'count(orders.is_payment_pending) as `Number of pending payments`' => "Number of Pending Payments",
                'orders.is_payment_pending as `Payment Pending`' => "Payment Pending",
                'orders.payment_mode as `Payment Mode`' => "Payment Mode",
                'IFNULL(sum(orders.final_amount), 0) as `Final Amount`' => "Final Amount",   
                'DATE_FORMAT(orders.created_at, "%Y/%m/%d") as `Date of Order`' => "Date of Order",  
                'count(orders.id) as `Number of Orders`' => "Number of Orders",
            ],
            "clients" => [
                'clients.name as `Client Name`' => "Client Name", 
                'clients.city as `City`' => "City",  
                'clients.zipcode as `Zip Code`' => "Zip Code",  
                'clients.client_type as `Client Type`' => "Client Type"
            ],
            "deals_and_discount" => [
                'deals_and_discounts.deal_name as `Deal`' => "Deal", 
                'IF(is_active = 1,"Yes", "No") as `Is Active`' => "Is Active",    
                'count(deals_and_discounts.id) as `Number of Deals`' => "Number of Deals", 
            ],
            "inventory" => [ 
                'stock_income_history.invoice_type as `Invoice Type`' => 'Invoice Type', 
                'vendors.name as `Vendor Name`' => 'Vendor Name',   
                'stock_income_history.extra_freight_charges as `Extra Freight Charge`' => 'Extra Freight Charge', 
                'IFNULL(sum(stock_income_history.extra_freight_charges), 0) as `Total Extra Freight Charge`' => 'Total Extra Freight Charge', 
                'stock_income_history.amount_paid as `Amount Paid`' => 'Amount Paid', 
                'IFNULL(sum(stock_income_history.amount_paid), 0) as `Total Amount Paid`' => 'Total Amount Paid', 
                'stock_income_history.payment_type as `Payment Type`' => 'Payment Type', 
                // 'statuses.title as `Status`' => 'Status', 
                'branches.name as `Branch`' => 'Branch', 
                'count(stock_income_history.id) as `Number of Entries`' => "Number of Entries",
            ],
            "employee" => [
                'users.first_name as `First Name`' => "First Name",
                'users.last_name as `Last Name`' => "Last Name",
                'users.expertise as `Expertise`' => "Expertise",
                'branches.name as `Branch`' => "Branch",
                'count(users.id) as `Number of Employees`' => "Number of Employees",
            ],
            "product" => [
                "products.name as `Name`" => "Name",
                'IF(type = 1,"Service", "Product") as `Type`' => "Type",
                "products.sales_price as `Sales Price`" => "Sales Price",
                // "packages.name as `Package`" => 'Package',
                "products.purchase_price as `Purchase Price`" => "Purchase Price",
                "products.sku_code as `SKU Code`" => "SKU Code",
                "units.name as `Unit`" => 'Unit',
                "products.reorder_qty as `ReOrder Quantity`" => 'ReOrder Quantity',
            ],
            "inquiry" => [
                'count(clients.name) as `Number of Clients`' => "Number of Clients",
                'clients.name as `Client Name`' => "Client Name",
                'enquiries.enquiry_for as `Inquery For`' => "Inquery For", 
                'enquiry_types.name as `Enquiry Type`' => "Enquiry Type",  
                'branches.name as `Branch`' => "Branch",  
                'statuses.title as `Status`' => "Status",  
                'DATE_FORMAT(enquiries.date_to_follow, "%d/%m/%Y") as `Date to Follow`' => "Date to follow",  
                'count(enquiries.id) as `Number of Inquiries`' => "Number of Inquiries",
            ],
            "appointment" => [
                // 'count(clients.name) as `Number of Clients`' => "Number of Clients",
                'clients.name as `Client`' => "Client",  
                // 'count(users.name) as `Number of Employee`' => "Number of Employee",
                // 'CONCAT_WS(" ", `users.first_name`, `users.last_name`) AS `Employee`' => "Emloyee",  
                'branches.name as `Branch`' => "Branch",  
                'statuses.title as `Status`' => "Status",  
                'appointments.date `Date of Appointment`' => "Date of Appointment",   
                'count(appointments.id) as `Number of Appointments`' => "Number of Appointments", 
            ],
            "branches" => [
                'branches.name as `Branch`' => "Branch",
                "branches.city as `City`" => 'City',  
                "users.first_name as `Contact Person`" => 'Contact Person',
                "branches.primary_contact_number as `Contact Number`" => 'Contact Number',  
                "branches.primary_email as `Primary Email`" => 'Primary Email',   
                "count(DISTINCT users.id) as `Number of Employees`" => 'Number of Employees',   
                "count(DISTINCT appointments.id) as `Number of Appointments`" => 'Number of Appointments',   
                "count(DISTINCT enquiries.id) as `Number of Inquiries`" => 'Number of Inquiries',   
                "count(DISTINCT orders.id) as `Number of Orders`" => 'Number of Orders',   
            ],  
        ];

        $html = "";
        if(!empty($option)) {
            foreach($group_by_options[$option] as $key => $value) {
                $html .= "<option value='$key'>$value</option>";
            }; 
        }
         
        // dd($group_by);
        if($group_by == "true") {
            $html = $html;
        } else {
            $html = $this->getColumns($option);
        }
        return $html; 
    }

    public function getGroupByColumns($option)
    {
        $group_by_columns = [
            "orders" => [
                "orders.is_payment_pending" => 'Payment Pending',
                "clients.id" => 'Client',
                "orders.payment_mode" => 'Payment Mode',
                "orders.created_at" => 'Date of Order', 
                "branches.id" => 'Branch',  
            ], 
            "clients" => [
                "clients.name" => "Name",
                "clients.city" => "City",
                "clients.zipcode" => "Zip Code",
                "clients.date_of_birth" => "Date of Birth",
                "clients.anniversary" => "Anniversary",
                'clients.client_type' => "Client Type"
            ],
            "deals_and_discount" => [
                // "deals_and_discounts.tag_id" => "Segament", 
                "deals_and_discounts.is_active" => "Status (active / inactive)", 
            ],
            "inventory" => [
                "stock_income_history.date" => "Date",
                "vendors.id" => "Vendor",
                "branches.id" => "Branch",
                "stock_income_history.invoice_type" => "Invoice Type",
                "stock_income_history.payment_type" => "Payment Type",
                "stock_income_history.payment_status" => "Payment Status",
            ],
            "employee" => [
               "branches.id" => 'Branch',
               "users.expertise" => 'Expertise',
            ],
            "product" => [
                "products.name" => "Name",
                "products.type" => "Type (Service / Product)",
            ],
            "inquiry" => [
                "clients.id" => 'Client',
                "enquiry_types.id" => 'Inquiry Type',
                "branches.id" => 'Branch',
                "statuses.id" => 'Status', 
                "enquiries.date_to_follow" => 'Date to Follow', 
            ],
            "appointment" => [
                "clients.id" => 'Client',
                "branches.id" => 'Branch',
                "statuses.id" => 'Status', 
                "appointments.date" => 'Date', 
            ], 
            "branches" => [
                "users.id" => 'Employee', 
                "appointments.id" => 'Appointment',
                "enquiries.id" => 'Inquiry',
                "orders.id" => 'Orders', 
            ], 
        ];
 
        $html = "<option value=''>Select Column</option>";
        if(!empty($option)) {
            foreach($group_by_columns[$option] as $key => $value) {
                $html .= "<option value='$key'>$value</option>";
            }; 
        }
        return $html;
    }

    public function checkName(Request $request)
    {  
        $id = $request->id; 
        $name = $request->name;
        

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { //is system user
            $distributor_id = $request->get('distributor_id');
        } 

        $report = Report::where('name', $name)->where('distributor_id', $distributor_id)->first();

        if($report !== null) { 
            if($id == $report->id) { 
                echo "true";
            } else {
                echo "false";
            }
        } else {
            echo "true";
        } 
    }

    public function findByExternalId($external_id)
    {
        return Report::where('external_id', $external_id)->firstOrFail();
    }
}
