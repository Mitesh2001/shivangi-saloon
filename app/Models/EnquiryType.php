<?php

namespace App\Models;
            
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnquiryType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_id',
        'name', 
        'created_by',
        'updated_by'
    ];

    public static function getInquiryTypes()
    {
        return self::pluck('name', 'id');
    }
}
