<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersCommission extends Model
{
    use SoftDeletes;
        
    protected $table = 'users_commission';
        
    protected $fillable = [
        'external_id',
        'user_id',
        'order_id',
        'user_product_commission',
        'user_service_commission',
        'invoice_json',
        'invoice_commission',
        'product_commission',
        'service_commission',
        'is_paid',
        'updated_at',
    ];

    public function getOrder()
    {
        return $this->hasOne(Order::class, 'id', 'order_id')->where('orders.deleted_at', null);
    }

    public function getSubscription()
    {
        return $this->hasOne(Subscriptions::class, 'id', 'subscription_id')->where('subscriptions.deleted_at', null);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->where('users.deleted_at', null);
    } 
    
    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }
}
