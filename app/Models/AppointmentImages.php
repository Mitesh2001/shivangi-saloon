<?php

namespace App\Models;

use DateTimeInterface; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentImages extends Model
{
    use SoftDeletes;

    protected $table = 'appointment_images';

    protected $fillable = [
        "external_id",
        "image",
        "appointment_id",
        "created_by",
        "updated_by",
    ]; 
}
