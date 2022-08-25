<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class SMSLog extends Model
{  
    protected $table = "sms_log";

    protected $fillable = [
        'sender_id',
        'template_id', 
        'number_of_sms',
        'client_id', 
        'number', 
        'message_body',  
        'event_type',   
        'template_json', 
        'distributor_id', 
    ];
    
    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id');
    }
    
    public function getClient()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
}
