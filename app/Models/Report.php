<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'external_id',
        'name', 
        'module', 
        'group_by', 
        'group_by_two', 
        'select_columns', 
        'rules_query', 
        'rules_set', 
        'created_by',
        'updated_by',
        'updated_at',
        'distributor_id',
    ];
 
    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }
}
