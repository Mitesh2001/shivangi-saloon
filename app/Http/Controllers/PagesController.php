<?php
namespace App\Http\Controllers;

use Auth;
use App\Models\Absence;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Task;
use App\Models\User;
use App\Models\ProductsIncomingEntries;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;

use App\Helpers\Helper;
use App\Models\Status;
use App\Models\Branch; 
use App\Models\Product;
use App\Models\Appointment;
use App\Models\Distributor;
use App\Models\Subscriptions;

use App\Http\Controllers\BranchController;

class PagesController extends Controller
{
    /**
     * Dashobard view
     * @return mixed
     */
    public function dashboard()
    { 
        $authUser = Auth::user();
        $distributor_id = Helper::getDistributorId();
 
        // Get Salon Statistics
        $salon_statistics = $this->getSalonStatistics($distributor_id);
        
        // Get Stock Reminder 
        $stock_remider = $this->getStockReminders($distributor_id);
                 
        // Get sales data
        if(empty($_GET['revenue_start_range'])) {
            $start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
        } else {
            $start_date = date('Y-m-d', strtotime($_GET['revenue_start_range']));
        }
        if(empty($_GET['revenue_end_range'])) {
            $end_date = Carbon::now()->endOfMonth()->format('Y-m-d');;
        } else {
            $end_date = date('Y-m-d', strtotime($_GET['revenue_end_range']));
        } 
        
        $sales_data = BranchController::getSalesData($start_date, $end_date);
 
        // Get all subscriptions of sales
        $subscriptions = Subscriptions::where('salon_id', $distributor_id)->get();
   
        // Get birthdays & anniversary report
        $birthdays = Client::whereRaw('DAYOFYEAR(curdate()) <= DAYOFYEAR(date_of_birth) AND DAYOFYEAR(curdate()) + 15 >=  DAYOFYEAR(date_of_birth)' )->where('distributor_id', $distributor_id)->get();
        $anniverssaries = Client::whereRaw('DAYOFYEAR(curdate()) <= DAYOFYEAR(anniversary) AND DAYOFYEAR(curdate()) + 15 >=  DAYOFYEAR(anniversary)' )->where('distributor_id', $distributor_id)->get();

        // Statistics for admin panel
        $adminStatistics = $this->getAdminStatistics(); 
        $todaysAppointments = Appointment::getTodays($distributor_id);
        $statuses = Status::all(); 
        $branches = Branch::all(); 
        $statusesPluck = Status::pluck('title', 'id');
 
        return view('pages.dashboard')
            ->withStatuses($statuses)
            ->withBranches($branches)
            ->withStatusesPluck($statusesPluck)
            ->withTodaysAppointments($todaysAppointments)
            ->withAuthUser($authUser)
            ->withBirthdays($birthdays)
            ->withAnniverssaries($anniverssaries) 
            ->withstockremiders($stock_remider)  
            ->withChartData($sales_data)
            ->withDistributor($distributor_id)
            ->withSalonStatistics($salon_statistics)
            ->withAdminStatistics($adminStatistics)
            ->withSubscriptions($subscriptions)
            ->withRevenueStartDate($start_date)
            ->withRevenueEndDate($end_date);
    }
     
    /**
     *  Return stock reminder data
     *
     * @param  int  $distributor_id (Distributor id refers as salon id (because of module rename))
     * @return Object
     */ 
    public function getStockReminders($distributor_id) 
    { 
        return Product::leftJoin('stock_master', 'stock_master.product_id', '=', 'products.id')->whereRaw('stock_master.qty <= products.expiry_reminder')->where('stock_master.distributor_id', $distributor_id)->get();
    }   

    /**
     * Will return all the basic statistics related to salon
     *
     * @param  int  $distributor_id (Distributor id refers as salon id (because of module rename))
     * @return Array
     */ 
    public function getSalonStatistics($distributor_id) 
    {
        $distributor = Distributor::find($distributor_id);
  
        if(isset($distributor->getUsers)) {
            $total_users = $distributor->getUsers->count();
            $remaining_users = $distributor->no_of_users - $distributor->getUsers->count();
        }  
        if(isset($distributor->getBranches)) {
            $total_branches = $distributor->getBranches->count();
            $remaining_branches = $distributor->no_of_branches - $distributor->getBranches->count();
        } 

        if(isset($distributor->expiry_date)) {
            $subscription_expiry = date('d-m-Y', strtotime($distributor->expiry_date));
        }

        return [
            'subscription_expiry' => $subscription_expiry ?? "",
            'remaining_emails' => $distributor->total_email ?? 0,
            'remaining_sms' => $distributor->total_sms ?? 0,
            'remaining_users' => $remaining_users ?? 0,
            'remaining_branches' => $remaining_branches ?? 0,
            'total_emails' => $distributor->used_email ?? 0,
            'total_sms' => $distributor->used_sms ?? 0,
            'total_users' => $total_users ?? 0,
            'total_branches' => $total_branches ?? 0,
        ];
    }   

    /**
     * Will return all the basic statistics related to number of users, distributors, salons
     * and subscriptions
     *
     * @param  int  $distributor_id (Distributor id refers as salon id (because of module rename))
     * @return Array
     */ 
    public function getAdminStatistics() 
    {
        $user = Auth::user();
        $is_system_user = Helper::is_system_user();
        
        $today = date('Y-m-d');
        $number_of_salons = Distributor::count();
        $number_of_users  = User::where('user_type', 0)->count();
        $number_of_distributors = User::where('user_type', 2)->count();

        $new_subscriptions = Subscriptions::whereDate('created_at', $today); 
        $running_subscriptions = Subscriptions::whereDate('subscription_expiry_date', '>', $today);
        $expired_subscriptions = Subscriptions::whereDate('subscription_expiry_date', $today);

        if(!$is_system_user) {
            $new_subscriptions->where('created_by', $user->id);
            $running_subscriptions->where('created_by', $user->id);
            $expired_subscriptions->where('created_by', $user->id);
        }

        $new_subscriptions = $new_subscriptions->count(); 
        $running_subscriptions = $running_subscriptions->count();
        $expired_subscriptions = $expired_subscriptions->count();

        return [
            'number_of_salons' => $number_of_salons,
            'number_of_users' => $number_of_users,
            'number_of_distributors' => $number_of_distributors,
            'new_subscriptions' => $new_subscriptions,
            'running_subscriptions' => $running_subscriptions,
            'expired_subscriptions' => $expired_subscriptions, 
        ];
    }   
}
