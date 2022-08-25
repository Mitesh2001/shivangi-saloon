<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignTemplate extends Model
{
    use SoftDeletes;

    protected $table = "campaign_templetes";

    protected $fillable = [ 
        'external_id',
        'type',
        'subject',
        'teplate',
        'created_by',
        'updated_by',
        'updated_at', 
    ];

    // Special Segament
    public function getTag()
    {
        return $this->hasOne(Tag::class, 'id', 'customer_segment_special');
    }
}
