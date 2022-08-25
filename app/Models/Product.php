<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_id',
        'name',
        'description',
        'sales_price',
        'purchase_price',
        'thumbnail',
        'unit_id',
        'package_id',
        'sku_code',
        'other_document',
        'type',
        // 'category_id',
        'created_by',
        'updated_by',
        'distributor_id',
        'expiry_reminder',
        'igst',
        'sgst',
        'cgst',
        'is_default',
        'reorder_qty',
    ];

    // public function category() {
    //     return $this->hasOne(Category::class, 'id', 'category_id');
    // }
    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }


    public function categories() {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function packageProducts() {
        return $this->belongsToMany(Product::class, 'package_products', 'package_id', 'product_id')->withPivot('qty');
    }

    public function unit() {
        return $this->hasOne(Unit::class, 'id', 'unit_id');
    }

    public function package() {
        return $this->hasOne(Package::class, 'id', 'package_id');
    }

    // Commission %
    public function commission()
    {
        return $this->hasMany(UserProductCommission::class);
    }

    public function users() {
        return $this->belongsToMany(User::class, 'user_services');
    }
}