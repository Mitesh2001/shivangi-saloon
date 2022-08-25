<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealAndDiscount extends Model
{
    use SoftDeletes;

    protected $table = "deals_and_discounts";

    protected $fillable = [
        'external_id',
        'customer_segment_client',
        'customer_segment_special',
        'deal_name',
        'deal_code',
        'deal_description',
        'validity',
        'start_at',
        'end_at',
        'applicable_on_weekends',
        'applicable_on_holidays',
        'applicable_on_bday_anniv',
        'week_days',
        'invoice_min_amount',
        'invoice_max_amount',
        'redemptions_max',
        'benifit_type',
        'discount',
        'products_service_array',
        'is_archive',
        'is_active',
        'created_by',
        'updated_by',
        'distributor_id', 
        'apply_on_bill_total', 
    ];
 
    public function products() {
        return $this->belongsToMany(Product::class, 'deal_products');
    }
 
    public function clients() {
        return $this->belongsToMany(Client::class, 'deal_clients');
    }

    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }

    // Special Segament
    public function getTag()
    {
        return $this->hasOne(Tag::class, 'id', 'customer_segment_special');
    }
}
