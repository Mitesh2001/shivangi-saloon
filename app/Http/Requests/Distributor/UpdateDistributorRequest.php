<?php

namespace App\Http\Requests\Distributor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDistributorRequest extends FormRequest
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
            'primary_number' => ['digits:10', 'required'],  
            'secondary_number' => ['digits:10'],  
            'primary_email' => ['email', 'required'], 
            'secondary_email' => ['email'], 
            'contact_person' => ['required'], 
            'contact_person_number' => ['digits:10', 'required'], 
        ];
    }

    // Messages
    public function messages() 
    {
        return [
            'name' => 'Please enter name',  
            'primary_number.required' => 'Please enter primary number!',
            'primary_number.digits' => 'Please enter valid primary number!',
            'secondary_number.digits' => 'Please enter valid secondary number!',
            'primary_email.required' => 'Please enter primary email!',
            'primary_email.email' => 'Please enter valid primary email!',
            'secondary_email.email' => 'Please enter valid primary email!',
            'contact_person.required' => 'Please enter contact person name!',
            'contact_person_number.required' => 'Please enter contact person number!',
            'contact_person_number.digits' => 'Please enter valid contact person number!', 
        ];
    }
}
