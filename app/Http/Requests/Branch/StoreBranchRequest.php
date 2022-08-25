<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
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
            'name' => ['required'],
            // 'primary_contact_person' => ['required'],
            'primary_contact_number' => ['required', 'numeric', 'digits:10'],
            'secondary_contact_number' => ['numeric', 'digits:10'],
            'primary_email' => ['email'],
            'secondary_email' => ['email'],
        ];
    }

    // Messages
    public function messages() 
    {
        return [
            'name.required' => "Please enter branch name!", 
            // 'primary_contact_person.required' => "Please select primary contact person!", 
            'primary_contact_number.required' => "Please enter primary contact number!", 
            'primary_contact_number.numeric' => "Please enter valid primary contact number!", 
            'primary_contact_number.digits' => "Please enter valid primary contact number!", 
            'secondary_contact_number.numeric' => "Please enter valid secondary contact number!", 
            'secondary_contact_number.digits' => "Please enter valid secondary contact number!", 
            'primary_email.email' => "Please enter valid primary email!", 
            'secondary_email.email' => "Please enter valid secondary email!", 
        ];
    }
}
