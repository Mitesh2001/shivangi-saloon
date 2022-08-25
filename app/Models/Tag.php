<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes; 
 
    protected $fillable = [
        'external_id', 
        'name', 
        'type', 
        'created_by',  
        'updated_by',   
        'is_archive',
        'distributor_id',
    ];

    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }

    public function getConditions()
    {
        return $this->hasMany(TagCondition::class, 'tag_id', 'id');
    } 
}
