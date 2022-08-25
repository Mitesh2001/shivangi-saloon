<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DaybookOpeningBalance extends Model
{
    use SoftDeletes;

    protected $table = "daybook_opening_balance";

    protected $fillable = [ 
        'opening_balance',
        'date',
    ];
}
