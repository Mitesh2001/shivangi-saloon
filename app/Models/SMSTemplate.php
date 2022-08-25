<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SMSTemplate extends Model
{
	use SoftDeletes;
	  
    protected $table = 'sms_template';

    protected $fillable = [
        'external_id',
        'name', 
        'message', 
        'default_template', 
        'event_type', 
        'before_days', 
        'event_date', 
        'client_id', 
        'created_by', 
        'updated_by', 
        'distributor_id',
    ];
 
    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id');
    }

    public function parseContent($data)
    {
        $parsed = preg_replace_callback('/{(.*?)}/', function ($matches) use ($data) {
            list($shortCode, $index) = $matches;
            return (isset($data[$shortCode])) ? $data[$shortCode] : $shortCode;
        }, $this->content);

        return $parsed;
    }
}
