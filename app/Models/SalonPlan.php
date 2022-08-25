<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalonPlan extends Model
{
	use SoftDeletes;
	
	protected $table = 'salon_plans';
    
    protected $fillable = [
        'salon_id',
        'subscription_id',
        'plan_id', 
        'no_of_sms', 
        'no_of_email', 
        'no_of_users', 
        'no_of_branches', 
        'duration_months', 
        'plan_price', 
        'discount', 
        'discount_amount', 
        'final_amount', 
        'subscription_date',  
    ];
 
	public function plan()
    {
        return $this->hasOne(Plan::class, 'id', 'plan_id');
    } 
}
