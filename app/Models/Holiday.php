<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'external_id',
        'name', 
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
