<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

	protected $table = 'orders';

    protected $fillable = [
        'subscriptions_uid',  
        'external_id',  
        'client_id',  
        'branch_id',  
        'total_amount',  
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
        'payment_bank_name',  
        'payment_number',  
        'payment_amount',  
        'payment_date',  
        'is_payment_pending',  
        'round_off_amount',  
        'created_by',  
        'updated_by',  
        'deleted_by', 
        'distributor_id',  
        'deal_id',
        'discount_code',
        'state_id',
        'branch_state_id',
    ];

    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'clients_product')->withPivot('qty', 'product_price','discount','discount_amount','final_amount', 'igst', 'sgst', 'cgst', 'igst_amount', 'sgst_amount', 'cgst_amount', 'order_date', 'package_products', 'deal_discount')->whereNull('clients_product.deleted_at');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }
	
	public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
}
