<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriptions extends Model
{
	use SoftDeletes;
	
	protected $table = 'subscriptions'; 

    protected $fillable = [
        'subscriptions_uid',
        'salon_id',
        'discount',
        'discount_amount',
        'sgst',
        'cgst',
        'igst',
        'sgst_amount',
        'cgst_amount',
        'igst_amount',
        'final_amount',
        'payment_mode',
        'payment_bank_mode',
        'payment_number',
        'total_amount',
        'payment_amount',
        'state_id',
        'payment_date',
        'subscription_expiry_date',
        'created_by',
        'updated_by',
        'deleted_by',
        'is_payment_pending',
        'round_off_amount',
    ];
 
	public function salon()
    {
        return $this->hasOne(Distributor::class, 'id', 'salon_id');
    } 

    public function distributor()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
