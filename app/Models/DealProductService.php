<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealProductService extends Model
{
    use SoftDeletes;

    protected $table = "deals_and_discounts_products";

    protected $fillable = [
        'external_id', 
        'product_type', 
        'category_id', 
        'sub_category_id', 
        'product_id', 
        'product_min_price', 
        'product_max_price', 
        'deal_id', 
    ];

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function sub_category()
    {
        return $this->hasOne(Category::class, 'id', 'sub_category_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
