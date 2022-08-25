<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientTimeline extends Model
{
    use SoftDeletes;

    protected $table = "clients_timelines";
    
    protected $fillable = [ 
        'name',
        'from',
        'to',
        'other',
        'updated_by',
        'distributor_id',
    ];

    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }
}
