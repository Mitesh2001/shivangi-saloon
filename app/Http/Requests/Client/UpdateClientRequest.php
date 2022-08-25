<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
            'email' => ['email'],
            'primary_number' => ['required', 'numeric', 'digits:10'],
            'secondary_number' => ['numeric', 'digits:10'],
            // 'company_name' => ['required'],
            // 'industry_id' => ['required'],
            // 'company_type' => ['required'],
            // 'user_id' => ['required'],
            // 'address' => ['required'],
            // 'zipcode' => ['required'],
            // 'city' => ['required'],
        ];
    }

    // Messages
    public function messages() 
    {
        return [
            'name.required' => "Please enter name!",
            'email.email' => 'Please enter valid email address!',
            'primary_number.required' => "Please enter primary number!",
            'primary_number.numeric' => "Please enter valid primary number!",
            'primary_number.digits' => "Please enter valid primary number!",
            'secondary_number.numeric' => "Please enter valid secondary number!",
            'secondary_number.digits' => "Please enter valid secondary number!",
            // 'company_name.required' => "Please enter company name!",
            // 'industry_id.required' => "Please select industry!",
            // 'company_type.required' => "Please select company type!",
            // 'user_id.required' => "Please select user!",
            // 'address.required' => "Please enter address!",
            // 'zipcode.required' => "Please enter zipcode!",
            // 'city.required' => "Please enter city!",
        ];
    }
}
