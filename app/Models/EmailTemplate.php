<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
	use SoftDeletes;
	
    protected $primaryKey = 'email_template_id';

    protected $table = 'email_templates';

    protected $fillable = [
        'name',
        'subject', 
        'external_id', 
        'default_template', 
        'content', 
        'createdBy', 
        'event_type', 
        'before_days', 
        'event_date', 
        'client_id',
        'company_id', 
        'updatedBy', 
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

    public function parseSubject($data)
    {
        $parsed = preg_replace_callback('/{(.*?)}/', function ($matches) use ($data) {
            list($shortCode, $index) = $matches;
            return (isset($data[$shortCode])) ? $data[$shortCode] : $shortCode;
        }, $this->subject);

        return $parsed;
    }
	
	public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
