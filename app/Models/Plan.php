<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes;

    protected $table = "plans";

    protected $fillable = [
        'external_id',
        'name',  
        'price', 
        'sgst', 
        'igst', 
        'cgst', 
        'description',
        'duration_months',
        'no_of_users',
        'no_of_branches',
        'no_of_sms',
        'no_of_email',
        'created_by',  
        'updated_by',    
    ];
}
