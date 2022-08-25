<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Distributor extends Model
{
    use SoftDeletes;
        
    protected $fillable = [
        'external_id',
        'name', 
        'gst_number', 
        'primary_number', 
        'secondary_number', 
        'primary_email', 
        'secondary_email', 
        'contact_person', 
        'contact_person_number', 
        'contact_person_email', 
        'pan_number',
        'number_of_employees',
        'country_id',
        'state_id',
        'state_name',
        'logo',
        'city', 
        'address', 
        'zipcode',  
        'sender_id',  
        'from_email',  
        'from_name',  
        'sms_service',  
        'email_service',  
        'created_by',
        'updated_by',

        // Subscription Fields
        'no_of_users',
        'no_of_branches',
        'total_email',
        'used_email',
        'total_sms',
        'used_sms',
        'expiry_date',
    ];

    public static function findByExternalId($external_id)
    {
        return self::where('external_id', $external_id)->firstOrFail();
    }

    public static function getDistributors()
    {
        return self::pluck('name', 'id');
    }

    public function getCountry()
    {
        return $this->hasOne(Country::class, 'country_id', 'country_id');
    }

    public function getState()
    {
        return $this->hasOne(State::class, 'state_id', 'state_id');
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function getUsers()
    {
        return $this->hasMany(User::class, 'distributor_id', 'id');
    }

    public function getBranches()
    {
        return $this->hasMany(Branch::class, 'distributor_id', 'id');
    }
}
