<?php

namespace App\Models;
use App\Models\Distributor;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'external_id',
        'name', 
        'created_by',
        'updated_by',
        'distributor_id',
    ];
    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id');
    }
}
