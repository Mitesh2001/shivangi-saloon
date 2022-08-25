<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Daybook extends Model
{
    use SoftDeletes;

    protected $table = "daybook";

    protected $fillable = [
        'external_id', 
        'amount', 
        'entry_type', 
        'payment_method', 
        'description', 
        'branch_id', 
        'date', 
        'created_by', 
        'updated_by',  
        'distributor_id',
    ]; 

    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }
}
