<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Models\Distributor;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
    

    /**
     * Check Subscription plan of user - (if user is salon employee)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $user_type = $user->user_type; // 0 = system user, 1 = salon user, 2 = distributor
    
        if($user_type == 1) {

            $salon = Distributor::find($user->distributor_id); 
            $expiry_date = $salon->expiry_date;

            if(!empty($expiry_date)){
                if($expiry_date <= date('Y-m-d')){
                    $start_date = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                    $end_date   = Carbon::createFromFormat('Y-m-d', $expiry_date);
                    $different_days = $start_date->diffInDays($end_date);
                    if($different_days >= 15){
                        
                        Auth::logout();
                        return redirect(url('admin/login'))->withErrors(array('message' => "Your subscription has been expired. please renew."));
                    }
                }
            } else {
                Auth::logout();
                return redirect(url('admin/login'))->withErrors(array('message' => "Your subscription has been expired. please renew."));
            }
        } 
    }


    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = 'admin/login';

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    // Logout then redirect
    public function logout(Request $request) {
        Auth::logout();
        return redirect(url('admin/login'));
    }
}
