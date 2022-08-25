<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
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
        'city', 
        'address', 
        'zipcode',  
        'created_by',
        'updated_by',
        'distributor_id',
    ];

    public static function getVendors($distributor_id = false)
    {
        if($distributor_id) {
            $branches = self::where('distributor_id', $distributor_id)->pluck('name', 'id');  
        } else {
            $branches = self::pluck('name', 'id');
        }
        return $branches; 
    }

    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }
}
