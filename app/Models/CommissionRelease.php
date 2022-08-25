<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionRelease extends Model
{
    use SoftDeletes;
        
    protected $table = 'commission_release';
        
    protected $fillable = [
        'external_id',
        'user_id',
        'payment_method',
        'commission_amount',
        'commission_json',
        'released_by', 
        'distributor_id',
    ];
     
    public function getUser()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->where('users.deleted_at', null);
    } 
    
    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }
}
