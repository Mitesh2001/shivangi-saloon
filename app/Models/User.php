<?php
namespace App\Models;

use Fenos\Notifynder\Notifable;
use Illuminate\Notifications\Notifiable;
use Cache;
use App\Models\Client;
use App\Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Setting;
use App\Api\v1\Models\Token;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Billable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Contracts\JWTSubject;

// use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, EntrustUserTrait,  SoftDeletes, Billable;

    public function restore()
    {
        $this->restoreA();
        $this->restoreB();
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';
 
    public function services() {
        return $this->belongsToMany(Product::class, 'user_services');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'external_id',
        'name',
        'email',
        'password', 
        'primary_number',
        'secondary_number',
        'image_path',
        'language',

        'first_name',
        'last_name',
        'nick_name',
        'branch_id', 
        'expertise',   
        'date_of_joining', 
        'address', 
        'salary', 
        'basic', 
        'pf', 
        'gratuity', 
        'others', 
        'pt', 
        'gratutity',
        'income_tax', 
        'over_time_ph', 
        'working_hours', 
        'account_number', 
        'holder_name', 
        'bank_name', 
        'isfc_code', 
        'bank_attachment', 
        'profile_pic', 
        'total_experience', 
        'distributor_id',
        'product_commission',
        'service_commission',
        'plan_commission',

        // JSON Data 
        'certificates',
        'week_off',
        'employeer',
        'user_type',
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    
    
    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'password_confirmation', 'remember_token', 'image_path','token','otp','otp_expired_at'];
    protected $appends = ['avatar'];

    protected $primaryKey = 'id';

    // public function getDistributor()
    // {
    //     $distributor = $this->where('distributor_id', $)
    // }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'user_assigned_id', 'id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'user_assigned_id', 'id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'user_id', 'id');
    }

    public function department()
    {
        return $this->belongsToMany(Department::class);
    }

    public function userRole()
    {
        return $this->hasOne(RoleUser::class, 'user_id', 'id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function tokens()
    {
        return $this->hasMany(Token::class, 'user_id', 'id');
    }



    public function isOnline()
    {
        return Cache::has('user-is-online-' . $this->id);
    }

    public function getNameAndDepartmentAttribute()
    {
        //dd($this->name, $this->department()->toSql(), $this->department()->getBindings());
        return $this->name . ' ' . '(' . $this->department()->first()->name . ')';
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    } 

    public function getNameAndDepartmentEagerLoadingAttribute()
    {
        //dd($this->name, $this->department()->toSql(), $this->department()->getBindings());
        return $this->name . ' ' . '(' . $this->relations['department'][0]->name . ')';
    }

    public function moveTasks($user_id)
    {
        $tasks = $this->tasks()->get();
        foreach ($tasks as $task) {
            $task->user_assigned_id = $user_id;
            $task->save();
        }
    }

    public function moveLeads($user_id)
    {
        $leads = $this->leads()->get();
        foreach ($leads as $lead) {
            $lead->user_assigned_id = $user_id;
            $lead->save();
        }
    }

    public function moveClients($user_id)
    {
        $clients = $this->clients()->get();
        foreach ($clients as $client) {
            $client->user_id = $user_id;
            $client->save();
        }
    }

    public function getAvatarattribute()
    {
        $image_path = $this->image_path ? Storage::url($this->image_path) : '/images/default_avatar.jpg';
        return $image_path;
    }

    public function totalOpenAndClosedLeads()
    {
        $groups = $this->leads()->with('status')->get()->sortBy('status.title')->groupBy('status.title');
        $keys = collect();
        $counts = collect();
        foreach ($groups as $groupKey => $group) {
            $keys->push($groupKey);
            $counts->push(count($group));
        }

        return collect(['keys' => $keys, 'counts' => $counts]);
    }

    /**
     * @param $external_id
     * @return mixed
     */
    public function totalOpenAndClosedTasks()
    {
        $groups = $this->tasks()->with('status')->get()->sortBy('status.title')->groupBy('status.title');
        $keys = collect();
        $counts = collect();
        foreach ($groups as $groupKey => $group) {
            $keys->push($groupKey);
            $counts->push(count($group));
        }

        return collect(['keys' => $keys, 'counts' => $counts]);
    }

    /**
     * Return branch 
     */ 
    public function getBranch() 
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function getDistibutor()
    {
        return $this->hasOne(Distributor::class,'id', 'distributor_id');
    } 
}
