<?php

namespace App\Models;

use App\Models\EnquiryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{ 
    use SoftDeletes;

    protected $fillable = [
        'external_id',
        'client_id',
        'client_name', 
        'contact_number',
        'email',
        'description',
        'branch_id',
        'address',
        'enquiry_for',
        'enquiry_type',
        'enquiry_response',
        'date_to_follow',
        'source_of_enquiry',
        'user_assigned_id',
        'status_id',
        'created_by',
        'updated_by',
        'distributor_id',
        'gender',
    ];

    public function getDistributor()
    {
        return $this->hasOne(Distributor::class, 'id', 'distributor_id')->where('deleted_at', null);
    }
    
    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function status()
    {
        return $this->hasOne(Status::class, 'id', 'status_id');
    }

    public function get_enquiry_type()
    {
        return $this->hasOne(EnquiryType::class, 'id', 'enquiry_type');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_assigned_id');
    }

    public function getBranch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }
}
