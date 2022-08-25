<?php

namespace App\Http\Requests\Lead;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'client_external_id' => ['required'],
            'contact_number' => ['required', 'numeric', 'digits:10'],
            'email' => ['email'],
            'address' => ['required'],
            // 'enquiry_for' => ['required'],
            'enquiry_response' => ['required'],
            'date_to_follow' => ['required'],
            'enquiry_source' => ['required'],
            'user_assigned_id' => ['required'],
            'status_id' => ['required'],
        ];
    }

    // Messages
    public function messages() 
    {
        return [
            'client_external_id.required' => "Please select client name!",
            'contact_number.required' => "Please enter contact number!",
            'contact_number.numeric' => "Please enter valid contact number!",
            'contact_number.digits' => "Please enter valid contact number!", 
            'email.email' => "Please enter valid email address!",
            'address.required' => "Please enter address!",
            // 'enquiry_for.required' => "Please enter enquiry for!",
            'enquiry_response.required' => "Please enter enquiry response!",
            'date_to_follow.required' => "Please select date to follow!",
            'date_to_follow.required' => "Please select date to follow!",
            'enquiry_source.required' => "Please enter source of enquiry!",
            'user_assigned_id.required' => "Please select Lead Representative!",
            'user_assigned_id.required' => "Please select Lead Representative!",
            'status_id.required' => "Please select Lead Status!",
        ];
    }
}
