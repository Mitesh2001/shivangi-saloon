<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class TagCondition extends Model
{
    use SoftDeletes;
    protected $table = "tag_conditions";

    protected $fillable = [
        'tag_id',
        'kpi',
        'start_range',
        'end_range',
        'date_start_range',
        'date_end_range',
        'date_last_visit',
        'expiry_days_remain',
        'avg_orders',
        'gender',
    ];
}
