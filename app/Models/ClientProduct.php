<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientProduct extends Model
{
    use SoftDeletes;
    
    protected $table = "clients_product";

    protected $fillable = [
        'external_id',
        'order_id', 
        'client_id',
        'product_id',
        'product_price',
        'discount',
        'discount_amount',
        'final_amount',
        'order_date',
        'updated_at',
        'distributor_id',
        'deal_discount',
        'igst',
        'sgst',
        'cgst',
        'igst_amount',
        'sgst_amount',
        'cgst_amount',
    ];
 
    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }
 
	public function product()
    {
        return $this->hasOne(Product::class,'id','product_id');
    }
}
