<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

class EmailLog extends Model
{  
    protected $table = "email_log";

    protected $fillable = [
        'template_id',
        'client_id', 
        'client_email',
        'from_email', 
        'from_name', 
        'event_type',   
        'email_json',  
        'invoice_json', 
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
