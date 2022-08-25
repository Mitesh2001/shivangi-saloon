<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class StockEditHistory extends Model
{
    use SoftDeletes;

    protected $table = "stock_edit_history";

    protected $fillable = [
        'external_id', 
        'product_id',
        'invoice_number', 
        'old_qty',
        'new_qty',
        'old_cost_per_unit',
        'new_cost_per_unit',
        'old_gst_percent',
        'new_gst_percent',
        'old_mrp',
        'new_mrp',
        'remarks',
        'date', 
        'branch_id',
        'cerated_by',
        'updated_by',
    ];

    public function getBranch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    } 
}
