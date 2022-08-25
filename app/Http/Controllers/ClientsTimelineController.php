<?php

namespace App\Http\Controllers;

use DB; 

use Illuminate\Support\Facades\Auth;
use App\Enums\Country;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Status;
use App\Models\Task;
use App\Repositories\FilesystemIntegration\FilesystemIntegration;
use App\Repositories\Money\MoneyConverter;
use App\Services\ClientNumber\ClientNumberService;
use App\Services\Invoice\InvoiceCalculator;
use App\Services\Search\SearchService;
use App\Services\Storage\GetStorageProvider;
use Carbon\Carbon;
use Config;
use Dinero;
use Datatables;
use App\Models\Client;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Requests\Client\StoreClientBasicRequest;
use App\Models\User;
use App\Models\Integration;
use App\Models\Industry;
use App\Models\Enquiry;
use Ramsey\Uuid\Uuid;
use App\Models\Contact; 
use App\Models\ClientTimeline;
use App\Models\Appointment;

use App\Helpers\Helper;
use App\Models\Distributor;

class ClientsTimelineController extends Controller
{
         
    public function __construct()
    {
        // $this->middleware('permission:client-timeline-view', ['only' => ['index']]); 
		$this->middleware('permission:client-timeline-update', ['only' => ['clientTimelineUpdate', 'index']]);  
    }

    /**
     *  Client timeline setup view 
     */
    public function index()
    {
        $distributor_id = Helper::getDistributorId();  
        $data['distributor_id'] = $distributor_id;

        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        } 
        
        return view('clients.timeline.timeline_setup')->with($data);
    }

    /**
     * Update clients timeline
     */
    public function clientTimelineUpdate(Request $request)
    {  
        $user_id = Auth::id();

        $distributor_id = Helper::getDistributorId();
        $message = "!";
        if($distributor_id == 0) { // is admin
            // $distributor_id = $request->distributor_id != null ? $request->distributor_id : 0;

            if(empty($request->distributor_id))
            {
                return redirect()->back()->with([
                    'error' => "Please select salon!",
                ]);
            }

            $distributor_id = $request->distributor_id ?? 0;
            $distributor = Distributor::find($distributor_id);
            $message = "for $distributor->name!";
        }  
        
        $cliet_count = ClientTimeline::where('distributor_id', $distributor_id)->count();

        if($cliet_count > 0) {
            $repeating_client = ClientTimeline::where('name', 'repeating_clients')->where('distributor_id', $distributor_id)->first();
        } else {
            $repeating_client = new ClientTimeline();
            $repeating_client->name = "repeating_clients";
            $repeating_client->distributor_id = $distributor_id;
        } 
        
        $repeating_client->from = $request->repeat_clients_min;
        $repeating_client->to = $request->repeat_clients_max;
        $repeating_client->updated_by = $user_id;
 
        $repeating_client->save(); 

        if($cliet_count > 0) {
            $regular_client = ClientTimeline::where('name', 'regular_clients')->where('distributor_id', $distributor_id)->first();
        } else {
            $regular_client = new ClientTimeline();
            $regular_client->name = "regular_clients";
            $regular_client->distributor_id = $distributor_id;
        }  
        
        $regular_client->other = $request->regular_clients; 
        $regular_client->updated_by = $user_id;
        $regular_client->save();


        if($cliet_count > 0) {
            $never_visited = ClientTimeline::where('name', 'never_visited')->where('distributor_id', $distributor_id)->first();
        } else {
            $never_visited = new ClientTimeline();
            $never_visited->name = "never_visited";
            $never_visited->distributor_id = $distributor_id;
        }  
 
        $never_visited->other = $request->never_visited; 
        $never_visited->updated_by = $user_id;
        $never_visited->save();


        
        if($cliet_count > 0) {
            $no_risk = ClientTimeline::where('name', 'no_risk')->where('distributor_id', $distributor_id)->first();
        } else {
            $no_risk = new ClientTimeline();
            $no_risk->name = "no_risk";
            $no_risk->distributor_id = $distributor_id;
        }  
 
        $no_risk->other = $request->no_risk; 
        $no_risk->updated_by = $user_id;
        $no_risk->save();


        if($cliet_count > 0) {
            $dormant_clients = ClientTimeline::where('name', 'dormant_clients')->where('distributor_id', $distributor_id)->first();
        } else {
            $dormant_clients = new ClientTimeline();
            $dormant_clients->name = "dormant_clients";
            $dormant_clients->distributor_id = $distributor_id;
        }  
 
        $dormant_clients->from = $request->dormant_clients_min;
        $dormant_clients->to = $request->dormant_clients_max;
        $dormant_clients->updated_by = $user_id;
        $dormant_clients->save();

        
        if($cliet_count > 0) {
            $at_risk = ClientTimeline::where('name', 'at_risk')->where('distributor_id', $distributor_id)->first(); 
        } else {
            $at_risk = new ClientTimeline();
            $at_risk->name = "at_risk";
            $at_risk->distributor_id = $distributor_id;
        }  
 
        $at_risk->from = $request->at_risk_min;
        $at_risk->to = $request->at_risk_max;
        $at_risk->updated_by = $user_id;
        $at_risk->save();


        if($cliet_count > 0) {
            $lost_clients = ClientTimeline::where('name', 'lost_clients')->where('distributor_id', $distributor_id)->first();
        } else {
            $lost_clients = new ClientTimeline();
            $lost_clients->name = "lost_clients";
            $lost_clients->distributor_id = $distributor_id;
        }  
 
        $lost_clients->other = $request->lost_clients; 
        $lost_clients->updated_by = $user_id;
        $lost_clients->save();
  
        // return redirect()->route('clients_timeline.index'); 
        $distributor = Distributor::find($distributor_id);
        return redirect()->back()->with([
            'success' => 'Clients timeline successfully updated'. $message,
            'selected_distributor' => [
                'id' => $distributor_id,
                'name' => $distributor->name,
            ],
        ]);
    }

    public function allStatuses(Request $request)
    {
        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) { // is admin
            $distributor_id = $request->distributor_id;
        } 
  
        $statuses = ClientTimeline::where('distributor_id', $distributor_id)->get();
 
        if(count($statuses) == 0) { // Get Default if not found 
            $statuses = ClientTimeline::where('distributor_id', '0')->get();
        }
        if($distributor_id == 0) {
            return response()->json([  
                'repeating_clients_from' => $statuses[1]->from,
                'repeating_clients_to' => $statuses[1]->to,
                'regular_clients' => $statuses[2]->other,  
                'no_risk' => $statuses[4]->other, 
                'dormant_clients_from' => $statuses[5]->from,
                'dormant_clients_to' => $statuses[5]->to,
                'at_risk_from' => $statuses[6]->from,
                'at_risk_to' => $statuses[6]->to,
                'lost_clients' => $statuses[7]->other, 
            ]);
        } else {
            return response()->json([  
                'repeating_clients_from' => $statuses[0]->from,
                'repeating_clients_to' => $statuses[0]->to,
                'regular_clients' => $statuses[1]->other,  
                'no_risk' => $statuses[3]->other, 
                'dormant_clients_from' => $statuses[4]->from,
                'dormant_clients_to' => $statuses[4]->to,
                'at_risk_from' => $statuses[5]->from,
                'at_risk_to' => $statuses[5]->to,
                'lost_clients' => $statuses[6]->other, 
            ]);
        }
       
    }

    public function ClientsList(Request $request)
    {
        $status = $request->get('status');
        
        $distributor_id = Helper::getDistributorId();
        if($distributor_id == 0) {
            $distributor_id = $request->get('distributor');
            $data['back_url'] = route('clients_timeline.index');
        } else {
            $data['back_url'] = route('clients.index');
        } 

        if($distributor_id == 0) {
            $data['allow_view_only'] = false;
        } else {
            $data['allow_view_only'] = Helper::allowViewOnly($distributor_id);
        } 

        $data['distributor_id'] = $distributor_id;
        $data['is_system_user'] = Helper::is_system_user();  
        $data['all_status'] = $this->getAllStaticStatus();
        $data['status_wise'] = 1;
        $data['status'] = $status;
        $data['status_details'] = $this->getStatusTitle($status);
        return view('clients.index')->with($data);
    }

    public function getStatusTitle($status) {
 
        $statuses = [
            'no_risk' => [
                'title' => "Green",
                'bg_color' => "bg-green",
            ],
            'dormant_clients' => [
                'title' => "Yellow",
                'bg_color' => "bg-warning",
            ],
            'lost_clients' => [
                'title' => "Red",
                'bg_color' => "bg-danger",
            ],
            'at_risk' => [
                'title' => "At Risk",
                'bg_color' => "",
            ],
            'new_clients' => [
                'title' => "New Clients",
                'bg_color' => "",
            ],
            'repeating_clients' => [
                'title' => "Repeating Clients",
                'bg_color' => "",
            ], 
            'regular_clients' => [
                'title' => "Regular Clients",
                'bg_color' => "",
            ], 
            'never_visited' => [
                'title' => "Never Visited",
                'bg_color' => "",
            ], 
        ];

        return $statuses[$status] ?? ['title' => "", "bg_color" => ""]; 
    }

    public function ClientsListAPI(Request $request)
    {  
        $status = $request->get('status');
        $distributor_id = Helper::getDistributorId();

        if($distributor_id == 0) {
            $distributor_id = $request->get('distributor');
        } 
 
        if($status == "new_clients") {
            $clients = DB::select("SELECT *, 'New Clients' as status FROM clients WHERE id in(SELECT client_id from appointments where distributor_id = '$distributor_id' GROUP BY client_id HAVING count(client_id) = 1 )  AND distributor_id = $distributor_id AND  deleted_at is null");
            $clients = $this->getClients($clients, true);
        } 

        if($status == "never_visited") {
            $clients = DB::select("SELECT *, 'Never Visited' as status FROM clients WHERE id NOT in(SELECT client_id from appointments WHERE distributor_id = $distributor_id) AND distributor_id = $distributor_id AND deleted_at is null");
            $clients = $this->getClients($clients, true);
        } 

        if($status == "repeating_clients") {
            $clients =  $this->getVisitBetween('repeating_clients', 'Repeating Client', $distributor_id);
            $clients = $this->getClients($clients, true);
        } 

        if($status == "regular_clients") {
            $clients =  $this->getByVisit('regular_clients', 'Regular Clients', $distributor_id);
            $clients = $this->getClients($clients, true);
        } 

        if($status == "no_risk") {
            $appointments =  $this->getByDateGreen('No Risk', $distributor_id);
            $clients = $this->getClients($appointments);
        } 

        if($status == "dormant_clients") {
            $appointments =  $this->getByDateYellow('Dormant Client', $distributor_id);
            $clients = $this->getClients($appointments);
        } 

        // if($status == "at_risk") {
        //     $appointments =  $this->getByDateRed('at_risk', 'At Risk', $distributor_id);
        //     $clients = $this->getClients($appointments);
        // } 

        if($status == "lost_clients") {
            $never_visited = DB::select("SELECT *, 'Never Visited' as status FROM clients WHERE id NOT in(SELECT client_id from appointments WHERE distributor_id = $distributor_id) AND distributor_id = $distributor_id AND deleted_at is null");
            $appointments =  $this->getDateBefore('lost_clients', 'Lost Clients', $distributor_id);
            $clients = $this->getClients($appointments, true, $never_visited);
        }  
  
        return Datatables::of($clients)
            ->addColumn('namelink', function ($clients) {
                $url = url('admin/clients/' . $clients->external_id);
                return '<a data-search="' . $clients->name . '" href="'.$url.'" ">' . $clients->name . '</a>';
            })
            ->addColumn('distributor', function ($branch) {
                return  $branch->getDistributor->name ?? "";
            }) 
            ->addColumn('email', function ($clients) {
                return $clients->primaryContact->email ?? "";
            })
            ->addColumn('primary_number', function ($clients) {
                return $clients->primaryContact->primary_number ?? "";
            }) 
            ->addColumn('secondary_number', function ($clients) {
                return $clients->primaryContact->secondary_number ?? "";
            }) 
            ->addColumn('date_of_birth', function ($clients) {
                if(!empty($clients->date_of_birth) && $clients->date_of_birth != "0000-00-00") {
                    return date('d-m-Y', strtotime($clients->date_of_birth));
                } else {
                    return "";
                } 
            })
            ->addColumn('anniversary', function ($clients) { 
                if(!empty($clients->anniversary) && $clients->anniversary != "0000-00-00") {
                    return date('d-m-Y', strtotime($clients->anniversary));
                } else {
                    return "";
                } 
            })
            ->addColumn('city', function ($clients) {
                return $clients->city ?? "";
            })
            ->addColumn('zipcode', function ($clients) {
                return $clients->zipcode ?? "";
            })
            // ->addColumn('assigned_user', function ($clients) {
            //     return $clients->user->first_name ." ". $clients->user->last_name;
            // })
            ->addColumn('view', '
                <a href="{{ route(\'clients.show\', $external_id) }}" class="btn btn-link" >'  . __('View') . '</a>')
            ->addColumn('action', function ($clients) {
 
                $url = url('admin/clients/' . $clients->external_id);
				$html = '<form class="d-flex" action="'.route('clients.destroy', $clients->external_id).'" method="POST">';
                // $html .= '<a href="'.$url.'" class="btn btn-link"><i class="flaticon-eye text-primary text-hover-primary text-hover-primary" data-toggle="tooltip" title="View Details"></i></a>';
				// if(\Entrust::can('client-update'))
				// $html .= '<a href="'.route('clients.edit', $clients->external_id).'" class="btn btn-link" data-toggle="tooltip" title="Edit Client"><i class="flaticon2-pen text-primary text-hover-primary text-hover-primary"></i></a>';
				$html .= '<input type="hidden" name="_method" value="DELETE">';
				if(\Entrust::can('client-delete'))
				// $html .= '<button type="button" name="submit" value="' . __('Delete') . '" class="btn btn-link delete-client" data-toggle="tooltip" title="Delete Client"><i class="flaticon2-trash text-danger text-hover-warning text-hover-warning"></i></button>';
                $html .= '<input type="hidden" class="client_id" value="'.$clients->external_id.'">';
				$html .= csrf_field();
                if(\Entrust::can('client-update'))
                $html .= '<div class="dropdown"><button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button> <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="">';
                if(\Entrust::can('client-update'))  
                    $html .=    '<a href="'.route('clients.show', $clients->external_id).'" class="dropdown-item">View Details</a>'; 
                if(\Entrust::can('client-update'))  
                    $html .=    '<a href="'.route('clients.edit', $clients->external_id).'" class="dropdown-item">Edit Client</a>'; 
                if(\Entrust::can('order-view'))
                    $html .=    '<a href="'.route("orders.index", ['client_id' => encrypt($clients->id)]) .'" class="dropdown-item">Orders</a>';
                                  
                $html .= '</div>
                        </div>';
				$html .= '</form>';
                return $html;
            })
            ->rawColumns(['namelink', 'email', 'primary_number', 'assigned_user', 'action'])
            ->make(true);
    }

    public function getClients($segaments, $client = false, $lost_clients = false) {
  
        $client_ids = [];   

        if($client == false) {
            foreach($segaments as $appointment) {  
                array_push($client_ids, $appointment->client_id); // Appintment->client_id
            }
        } else {
            foreach($segaments as $appointment) { 
                array_push($client_ids, $appointment->id); // client->id 
            }
        } 
 
        if($lost_clients) {
            $never_visited = [];
            foreach($lost_clients as $appointment) {  
                array_push($never_visited, $appointment->id); // Appintment->client_id
            } 
            if(!empty($never_visited)) {
                return Client::whereIn('id', $client_ids)->whereNotIn('id', $never_visited)->get();
            } else {
                return Client::whereIn('id', $client_ids)->get();
            } 
        } else {
            return Client::whereIn('id', $client_ids)->get();
        }
        

        // $clients = [];
        // foreach($appointments as $appointment) {
        //     $client = Client::find($appointment->client_id)->toArray();
        //     array_push($clients, $client);
        // }
        // return $clients;
    }

    public function reportApi(Request $request)
    {    
        $distributor_id = intval(Helper::getDistributorId());
        if($distributor_id == 0) { // is admin
            $distributor_id = intval($request->distributor_id);
        }    
        if($distributor_id == "") {
            return [
                'new_clients' => 0,
                'repeating_clients' => 0,
                'regular_clients' => 0,
                'never_visited' => 0,
                'no_risk' => 0,
                'dormant_clients' => 0,
                // 'at_risk' => 0,
                'lost_clients' => 0,
            ];
        }

        $never_visited = DB::select("SELECT *, 'Never Visited' as status FROM clients WHERE id NOT in(SELECT client_id from appointments WHERE distributor_id = $distributor_id) AND distributor_id = $distributor_id AND deleted_at is null");
        $new_clients = DB::select("SELECT *, 'New Clients' as status FROM clients WHERE id in(SELECT client_id from appointments where distributor_id = $distributor_id GROUP BY client_id HAVING count(client_id) = 1 )  AND distributor_id = $distributor_id AND  deleted_at is null");
       
        $repeating_clients =  $this->getVisitBetween('repeating_clients', 'Repeating Client', $distributor_id);
        $regular_clients = $this->getByVisit('regular_clients', 'Regular Clients', $distributor_id);
        $no_risk = $this->getByDateGreen('No Risk', $distributor_id);
        $dormant_clients =  $this->getByDateYellow('Dormant Client', $distributor_id);
        // $at_risk =  $this->getDateBetween('at_risk', 'At Risk', $distributor_id);
        $lost_clients = $this->getDateBefore('lost_clients', 'Lost Clients', $distributor_id);

        $arr = [
            'new_clients' => count($new_clients),
            'repeating_clients' => count($repeating_clients),
            'regular_clients' => count($regular_clients),
            'never_visited' => count($never_visited),
            'no_risk' => count($this->getClients($no_risk)),
            'dormant_clients' => count($this->getClients($dormant_clients)),
            // 'at_risk' => count($this->getClients($at_risk)),
            'lost_clients' => count($this->getClients($lost_clients, true, $never_visited)),
        ]; 
  
        return response()->json($arr);
    }

    public function getVisitBetween($column_name, $status = "", $distributor_id = "")
    {
        if(empty($distributor_id)) { 
            $distributor_id = Helper::getDistributorId();
        }

        $timeline_count = ClientTimeline::where('distributor_id', $distributor_id)->count();

        if($timeline_count > 0) {
            $condition = "distributor_id = $distributor_id";
        } else {
            $condition = "distributor_id = 0";
        }
         
        return DB::select("SELECT clients.*, '$status' as status from clients WHERE id IN(SELECT client_id FROM appointments GROUP BY client_id HAVING COUNT(client_id) >= (SELECT `from` FROM clients_timelines WHERE name = '$column_name' AND $condition) AND COUNT(client_id) <= (SELECT `to` FROM clients_timelines WHERE name = '$column_name' AND $condition)) AND distributor_id = '$distributor_id' AND deleted_at is null");
    }
    
    public function getByVisit($column_name, $status = "", $distributor_id = "")
    {
        if(empty($distributor_id)) { 
            $distributor_id = Helper::getDistributorId();
        }
        
        $timeline_count = ClientTimeline::where('distributor_id', $distributor_id)->count();

        if($timeline_count > 0) {
            $condition = "distributor_id = $distributor_id";
        } else {
            $condition = "distributor_id = 0";
        }
         
        return DB::select("SELECT clients.*, '$status' as status from clients WHERE id IN(SELECT client_id FROM appointments GROUP BY client_id HAVING COUNT(client_id) >= (SELECT `other` FROM clients_timelines WHERE name = '$column_name' AND $condition)) AND distributor_id = '$distributor_id' AND  deleted_at is null");
    }

    public function getDateBetween($column_name, $status = "", $distributor_id = "")
    {
        if(empty($distributor_id)) { 
            $distributor_id = Helper::getDistributorId();
        }
       
        $timeline_count = ClientTimeline::where('distributor_id', $distributor_id)->count();

        if($timeline_count > 0) {
            $condition = "distributor_id = $distributor_id";
        } else {
            $condition = "distributor_id = 0";
        }
         
        return DB::select("SELECT *, '$status' as status FROM appointments WHERE date BETWEEN CURDATE() - INTERVAL (SELECT `to` from clients_timelines WHERE name = '$column_name' AND $condition) DAY AND CURDATE() - INTERVAL (SELECT `from` from clients_timelines WHERE name = '$column_name' AND $condition) DAY AND distributor_id = $distributor_id AND  deleted_at is null");
    }

    public function getByDate($column_name, $status = "", $distributor_id = "")
    {
        if(empty($distributor_id)) { 
            $distributor_id = Helper::getDistributorId();
        }
                
        $timeline_count = ClientTimeline::where('distributor_id', $distributor_id)->count();

        if($timeline_count > 0) {
            $condition = "distributor_id = $distributor_id";
        } else {
            $condition = "distributor_id = 0";
        }
         
        return DB::select("SELECT *, '$status' as status FROM appointments WHERE date BETWEEN CURDATE() - INTERVAL (60) DAY AND CURDATE() - INTERVAL (30) DAY AND distributor_id = $distributor_id AND deleted_at is null");
    }

    public function getByDateGreen($status = "", $distributor_id = "")
    {
        if(empty($distributor_id)) { 
            $distributor_id = Helper::getDistributorId();
        }
                
        $timeline_count = ClientTimeline::where('distributor_id', $distributor_id)->count();

        if($timeline_count > 0) {
            $condition = "distributor_id = $distributor_id";
        } else {
            $condition = "distributor_id = 0";
        }
         
        return DB::select("SELECT *, '$status' as status FROM appointments WHERE date BETWEEN CURDATE() - INTERVAL (60) DAY AND CURDATE() - INTERVAL (30) DAY AND distributor_id = $distributor_id AND deleted_at is null");
    }

    public function getByDateYellow($status = "", $distributor_id = "")
    {
        if(empty($distributor_id)) { 
            $distributor_id = Helper::getDistributorId();
        }
                
        $timeline_count = ClientTimeline::where('distributor_id', $distributor_id)->count();

        if($timeline_count > 0) {
            $condition = "distributor_id = $distributor_id";
        } else {
            $condition = "distributor_id = 0";
        }
         
        return DB::select("SELECT *, '$status' as status FROM appointments WHERE date BETWEEN CURDATE() - INTERVAL (120) DAY AND CURDATE() - INTERVAL (60) DAY AND distributor_id = $distributor_id AND deleted_at is null");
    }

    public function getByDateRed($status = "", $distributor_id = "")
    {
        if(empty($distributor_id)) { 
            $distributor_id = Helper::getDistributorId();
        }
                
        $timeline_count = ClientTimeline::where('distributor_id', $distributor_id)->count();

        if($timeline_count > 0) {
            $condition = "distributor_id = $distributor_id";
        } else {
            $condition = "distributor_id = 0";
        }
         
        return DB::select("SELECT *, '$status' as status FROM appointments WHERE date >= CURDATE() - INTERVAL (120) DAY  distributor_id = $distributor_id AND deleted_at is null");
    }

    public function getDateBefore($column_name, $status = "", $distributor_id = "")
    {
        if(empty($distributor_id)) { 
            $distributor_id = Helper::getDistributorId();
        }

        $timeline_count = ClientTimeline::where('distributor_id', $distributor_id)->count();

        if($timeline_count > 0) {
            $condition = "distributor_id = $distributor_id";
        } else {
            $condition = "distributor_id = 0";
        }
         
        // return DB::select("SELECT *, '$status' as status FROM appointments WHERE date < CURDATE() - INTERVAL (SELECT other from clients_timelines WHERE name = '$column_name' AND $condition) DAY  AND distributor_id = $distributor_id AND deleted_at is null");
        return DB::select("SELECT a.*
        FROM clients a
        LEFT JOIN appointments b ON 
                  a.id = b.client_id AND 
                  b.date >= CURDATE() - INTERVAL (120) DAY
        WHERE b.client_id IS NULL AND a.distributor_id = $distributor_id AND a.deleted_at is null");
    }

    
    public function getAllStaticStatus()
    {
        return [
            'New Clients',
            'Repeating Clients',
            'Regular Clients',
            'Never Visited',
            'No Risk',
            'Dormant Clients',
            // 'At Risk',
            'Lost Clients',
        ];
    }
}
