<?php

namespace App\Models;

use DateTimeInterface;
use App\Models\User; 
use App\Models\Client; 
use App\Models\Branch; 
use App\Models\Status; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "external_id",
        "appointment_for",
        "description",
        'source_type_string',
        'status_id',
        'status_id',
        'branch_id',
        'client_id',
        'user_id',
        'contact_number',
        'email',
        'address',
        "start_at",
        "end_at",
        'date',
        'created_by',
        'distributor_id',
        'enquiry_id',
        'stage',
        'gender',
    ]; 

    protected $dates = ['start_at', 'end_at'];
    // protected $hidden = ['id', 'user_id', 'source_type', 'source_id', 'client_id'];
 
    public function services() {
        return $this->belongsToMany(Product::class, 'appointment_services');
    }
    
    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }

    public function getRouteKeyName()
    {
        return 'external_id';
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getBranch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function getStatus()
    {
        return $this->hasOne(Status::class, 'id', 'status_id');
    }

    public function getImages()
    {
        return $this->hasMany(AppointmentImages::class, 'appointment_id', 'id');
    }
     
    /**
     *  Returns appointmetn booked for today
     * 
     * @return Object
     */ 
    public static function getTodays($distributor_id)
    {
        $current_time = date('H:i:s', (time() - (30 * 60)));
        $current_date = date('Y-m-d'); 
    
        return Self::whereDate('date', $current_date)->whereTime('end_at', '>=', $current_time)->orderBy('start_at', 'desc')->get(); 
    }
}
