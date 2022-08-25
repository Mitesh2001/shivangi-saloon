<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Datatables;

use App\Models\User;  
use App\Models\Tag;  
use App\Models\TagCondition;  

use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;

use App\Helpers\Helper;
use App\Models\Distributor;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('tags.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['is_system_user'] = Helper::getDistributorId(); 
        $data['kpi_arr'] = $this->getKpi(); 
        return view('tags.create')->with($data);
    }
    
    // Remote name validation
    public function checkName(Request $request)
    {  
        $id = $request->id;
        $name = $request->name;

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { //is system user
            $distributor_id = $request->distributor_id;
        }

        $tag = Tag::where('name', $name)->where('distributor_id', $distributor_id)->first();

        if($tag !== null) { 
            if($id == $tag->id) { 
                echo "true";
            } else {
                echo "false";
            }
        } else {
            echo "true";
        } 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTagRequest $request)
    {  
        $user_id = Auth::id();

        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;
        }  
 
        $tag = Tag::create([
            'external_id' => Uuid::uuid4()->toString(), 
            'name' => $request->name,    
            'type' => $request->type,    
            'created_by' => $user_id, 
            'distributor_id' => $distributor_id,
        ]);
        $tag_id = $tag->id;
 
        if(isset($request->condition_arr)) { 
            foreach($request->condition_arr as $condition) {   
                $tag_condition = TagCondition::create([ 
                    'tag_id' => $tag_id,
                    'kpi' => $condition['kpi'] ?? "", 
                    'start_range' => $condition['start_range'] ?? "", 
                    'end_range' => $condition['end_range'] ?? "",  
                    'date_start_range' => $condition['date_start_range'] ?? "", 
                    'date_end_range'     => $condition['date_end_range'] ?? "",   
                    'date_last_visit' => $condition['date_last_visit'] ?? "",  
                    'expiry_days_remain' => $condition['expiry_days_remain'] ?? "",  
                    'avg_orders' => $condition['avg_order'] ?? "",  
                    'gender' => $condition['gender'] ?? NULL,  
                ]);  
            } 
        }

        Session()->flash('success', __('Tag successfully added'));
        return redirect()->route('tags.index');
    }
   
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {   
        $tag = Tag::with(['getDistributor', 'getConditions'])->where('is_archive', 0);
  
        $distributor_id = Helper::getDistributorId();  
        if($distributor_id != 0) { // Check if distributor
            $tag->where('distributor_id', $distributor_id);
        }  
        $tag = $tag->orderBy('id', 'desc')->get();

        return Datatables::of($tag)
            ->addColumn('distributor', function ($tag) {
                return  $tag->getDistributor->name ?? "";
            }) 
            ->addColumn('name', function ($tag) {
                return  $tag->name;
            })  
            ->addColumn('number_of_rules', function ($tag) {
                return  count($tag->getConditions);
            })  
            ->addColumn('date', function ($tag) {
                return  date('d-m-Y', strtotime($tag->created_at));
            })    
            ->addColumn('action', function ($tag) {
                $url = url('admin/tags/'.$tag->external_id);
				$html = '<form action="'.route('tags.destroy', $tag->external_id).'" method="POST">';
                $html .= '<a href="'.$url.'" class="btn btn-link"><i class="flaticon-eye text-primary text-hover-primary" data-toggle="tooltip" title="View Details"></i></a>';
				if(\Entrust::can('tag-update')) 
				$html .= '<a href="'.route('tags.edit', $tag->external_id).'" class="btn btn-link" data-toggle="tooltip" title="Edit tag"><i class="flaticon2-pen text-primary text-hover-primary"></i></a>';
				$html .= '<input type="hidden" name="_method" value="DELETE">';
				if(\Entrust::can('tag-delete'))
                // $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-tag" data-toggle="tooltip" title="Archive Tag"><i class="fas fa-archive text-danger text-hover-warning"></i></button>';
                $html .= '<input type="hidden" class="tag_id" value="'.$tag->external_id.'">'; 
				$html .= csrf_field();
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['distributor', 'name', 'number_of_rules', 'number_of_customers', 'action'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($external_id)
    { 
        $data['tag'] = $this->findByExternalId($external_id);  
        // $data['conditions_arr'] = $data['tag']->getConditions;
        
        $data['conditions_arr'] = $this->getConditions($external_id);

        // dd($data['conditions_arr']);
  
        return view('tags.show')->with($data);   
    } 

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($external_id)
    { 
        $distributor_id = Helper::getDistributorId();
        
        // Only system user can access others data
        if($distributor_id == 0) { 
            $data['tag'] = $this->findByExternalId($external_id);   
        } else { 
            $data['tag'] = Tag::where('external_id', $external_id)->where('distributor_id', $distributor_id)->firstOrFail(); 
        } 
        $data['is_system_user'] = $distributor_id;
        $data['distributor'] = Distributor::findOrFail($data['tag']->distributor_id); // current record distributor name (for admin)

        $data['tag'] = $this->findByExternalId($external_id);  
        $data['conditions_arr'] = $data['tag']->getConditions;
        $data['kpi_arr'] = $this->getKpi();
  
        return view('tags.edit')->with($data);    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTagRequest $request, $external_id)
    { 
        $user_id = Auth::id();
        $tag_id = $request->id;   

        // Update Tag entry
        $tag = $this->findByExternalId($external_id); 
        $updated_tag = $tag->fill([ 
            'name' => $request->name,    
            'type' => $request->type,    
            'updated_by' => $user_id,  
        ])->save(); 
  
        // Update Conditions
        if(isset($request->condition_arr)) {
            foreach($request->condition_arr as $condition) {
                if($condition['kpi'] == "") {
                    continue;
                }
                if(isset($condition['id'])) {
                    $tagContion = TagCondition::where('id', $condition['id'])->firstOrFail();
    
                    $tagContion->fill([ 
                        'kpi' => $condition['kpi'], 
                        'start_range' => $condition['start_range'] ?? "", 
                        'end_range' => $condition['end_range'] ?? "",  
                        'date_start_range' => $condition['date_start_range'] ?? "", 
                        'date_end_range'     => $condition['date_end_range'] ?? "",   
                        'date_last_visit' => $condition['date_last_visit'] ?? "",  
                        'expiry_days_remain' => $condition['expiry_days_remain'] ?? "",  
                        'avg_orders' => $condition['avg_order'],   
                        'gender' => $condition['gender'] ?? NULL, 
                    ])->save();

                } else {
                    $tag_condition = TagCondition::create([ 
                        'tag_id' => $tag_id,
                        'kpi' => $condition['kpi'] ?? "", 
                        'start_range' => $condition['start_range'] ?? "", 
                        'end_range' => $condition['end_range'] ?? "",  
                        'date_start_range' => $condition['date_start_range'] ?? "", 
                        'date_end_range'     => $condition['date_end_range'] ?? "",   
                        'date_last_visit' => $condition['date_last_visit'] ?? "",  
                        'expiry_days_remain' => $condition['expiry_days_remain'] ?? "",  
                        'avg_orders' => $condition['avg_order'] ?? "",  
                        'gender' => $condition['gender'] ?? NULL,  
                    ]);  
                }  
            }
        }   
 
        Session()->flash('success', __('Tag successfully updated!'));
        return redirect()->route('tags.index');
    }

    // Archive tag
    public function archiveTag(Request $request)
    {
        $user_id = Auth::id();
        $external_id = $request->external_id;
        $tag = $this->findByExternalId($external_id);
  
        $tag->fill([
            'is_archive' => 1, 
            'updated_by' => $user_id,
        ])->save();

        Session()->flash('success', __('Tag successfully archived!'));

        return response()->json([
            'status' => true,
            'message' => "Tag successfully archived!", 
        ]);
    }

    public function getKpi()
    {
        return [
            "total_amount_paid" => "Total Amount Paid",
            "visits" => "Visits",
            "last_visit_date" => "Last Visit Date",
            "last_visit_range" => "Last Visit Range",
            "points" => "Points",
            "gender" => "Gender",
            "age" => "Age",
            "package_subscription" => "Package Subscription",
            "avg_order" => "Average Order",
            "billing_date" => "Billing Date",
            "birthday" => "Birthday",
            "anniversary" => "Anniversary",
            "package_expiry" => "Package Expiry Days Remaining", 
        ];
    }

    public function getKpiName($index_name) {
        $arr =  $this->getKpi();
        return $arr[$index_name];
    }

    public function removeCondtionEntry(Request $request) {
        
        $condition_id = $request->condition_id;
        $condition = TagCondition::find($condition_id); 

        // soft delete condition entry
        $condition->delete();

        echo json_encode([
            'status' => true,
            'message' => 'Condition Removed Successfully!!'
        ]);
    }

    public function getConditions($tag_id) {
        $tag = $this->findByExternalId($tag_id);
        $conditions = $tag->getConditions;
  
        $number_range = ['total_amount_paid', 'visits', 'last_visit_range', 'points', 'age'];
        $date_range = ['birthday', 'anniversary', 'billing_date'];

        $arr = [];
        $x = 0;
        foreach($conditions as $condition) { 

            $arr[$x]['name'] = $this->getKpiName($condition->kpi); 
   
            if(in_array($condition['kpi'], $number_range)) {
                if(!empty($condition->start_range)) {
                    $arr[$x]['td_1'] = $condition->start_range;
                } else {
                    $arr[$x]['td_1'] = "";
                }
                if(!empty($condition->end_range)) {
                    $arr[$x]['td_2'] = $condition->end_range;
                } else {
                    $arr[$x]['td_2'] = "";
                }
            }

            if(in_array($condition['kpi'], $date_range)) { 
                if(!empty($condition->date_start_range) && $condition->date_start_range != 0000-00-00) {
                    $arr[$x]['td_1'] = date('d-m-Y', strtotime($condition->date_start_range));
                } else {
                    $arr[$x]['td_1'] = "";
                }
                if(!empty($condition->date_end_range) && $condition->date_end_range != 0000-00-00) {
                    $arr[$x]['td_2'] = date('d-m-Y', strtotime($condition->date_end_range));
                } else {
                    $arr[$x]['td_2'] = "";
                }
            }
            
            if($condition['kpi'] == "last_visit_date") {  
                $arr[$x]['td_1'] = date('d-m-Y',strtotime($condition->date_last_visit)); 
                $arr[$x]['td_2'] = "";
            }
            
            if($condition['kpi'] == "avg_order") { 
                if(!empty($condition->avg_orders)) {
                    $arr[$x]['td_1'] = $condition->avg_orders;
                } else {
                    $arr[$x]['td_1'] = "";
                } 
                $arr[$x]['td_2'] = "";
            }  
            
            if($condition['kpi'] == "gender") {  
                    if($condition->gender = 0) {
                        $arr[$x]['td_1'] = "Male";
                    } else {
                        $arr[$x]['td_1'] = "Female";
                    }  
                $arr[$x]['td_2'] = "";
            }  
                
            if($condition['kpi'] == "package_expiry") { 
                if(!empty($condition->expiry_days_remain)) {
                    $arr[$x]['td_1'] = $condition->expiry_days_remain . " Days";
                } else {
                    $arr[$x]['td_1'] = "";
                } 
                $arr[$x]['td_2'] = "";
            }   
                
            if($condition['kpi'] == "package_subscription") {  
                $arr[$x]['td_1'] = ""; 
                $arr[$x]['td_2'] = "";
            }   

            $x++;
        }
        return $arr;
    }
 
    public function findByExternalId($external_id)
    {
        return Tag::where('external_id', $external_id)->firstOrFail();
    }
}
