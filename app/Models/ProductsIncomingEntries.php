<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductsIncomingEntries extends Model
{
    use SoftDeletes;

    protected $table = "product_income_entries";
    
    protected $fillable = [
        'product_name',
        'product_id',
        'product_type',
        'sku_code',
        'mrp',
        'qty',
        'cost_per_unit',
        'gst_percent',
        'total_cost',
        'expiry',
        
        'branch_id',
        'stock_income_history_id',
        'distributor_id',
    ];

    public function getBranch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
