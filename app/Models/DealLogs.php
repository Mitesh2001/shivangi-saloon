<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealLogs extends Model
{
    use SoftDeletes;

    protected $table = "deal_logs";

    protected $fillable = [
        'external_id', 
        'order_id', 
        'deal_id', 
        'deal_json',
    ];
    
    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function deal()
    {
        return $this->hasOne(DealAndDiscount::class, 'id', 'deal_id');
    }
}
