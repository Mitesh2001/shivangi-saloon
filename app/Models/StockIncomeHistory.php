<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class StockIncomeHistory extends Model
{
    use SoftDeletes;

    protected $table = "stock_income_history";

    protected $fillable = [
        'external_id',
        'date',
        'invoice_number',
        'invoice_value',
        'extra_freight_charges',
        'source_type',
        'source_id',
        'invoice_type',
        'notes',
        'amount_paid',
        'payment_type',
        'payment_status', 
        'products_array', 
        'branch_id',
        'cerated_by',
        'updated_by',
        'distributor_id',
    ];
        
    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }
    
    public function getBranch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function getVendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'source_id');
    }

    public function getProducts(){
        return $this->hasMany(ProductsIncomingEntries::class, 'stock_income_history_id', 'id');
    } 
}
