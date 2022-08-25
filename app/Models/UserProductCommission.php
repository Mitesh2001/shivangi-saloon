<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class UserProductCommission extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_product_commission';

   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'commission',
        'created_by',
        'updated_by',
    ];

}
