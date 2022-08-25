<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_id',
        'name',
        'primary_contact_person',
        'secondary_contact_person',
        'primary_contact_number',
        'secondary_contact_number',
        'primary_email',
        'secondary_email',
        'country_id',
        'state_id',
        'state_name',
        'city',
        'address',
        'zipcode',
        'is_primary',
        'is_archive',
        'created_by',
        'updated_by',
        'distributor_id',
    ];

    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }

    public static function getBranches($distributor_id = false)
    {  
        if($distributor_id) {
            $branches = self::where('distributor_id', $distributor_id)->pluck('name', 'id');  
        } else {
            $branches = self::pluck('name', 'id');
        }
        return $branches;
    }

    public function get_primary_user()
    {
        return $this->hasOne(User::class, 'id','primary_contact_person');
    }

    public function get_secondary_user()
    {
        return $this->hasOne(User::class, 'id','secondary_contact_person');
    }

    public function getUsers()
    {
        return $this->hasMany(User::class, 'id', 'branch_id');
    }
 
    public function getCountry()
    {
        return $this->hasOne(Country::class, 'country_id', 'country_id');
    }

    public function getState()
    {
        return $this->hasOne(State::class, 'state_id', 'state_id');
    }
}
