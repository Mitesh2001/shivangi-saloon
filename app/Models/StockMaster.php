<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMaster extends Model
{
    use SoftDeletes;

    protected $table = "stock_master";
    
    protected $fillable = [
        'external_id', 
        'product_id', 
        'qty', 
        'branch_id', 
        'created_by', 
        'updated_by', 
        'updated_at',
        'distributor_id',
    ]; 
        
    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }
    
    public function getProduct()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    } 

    public function getBranch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }
}
