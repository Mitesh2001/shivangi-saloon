<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
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
            'price' => ['required', 'numeric'], 
            'duration_months' => ['required', 'numeric'], 
            'no_of_users' => ['required', 'numeric'],  
            'no_of_sms' => ['required', 'numeric'],  
            'no_of_email' => ['required', 'numeric'],  
        ];
    }

    // Messages
    public function messages() 
    {
        return [
            'name.required' => "Please enter product name!", 
            'price.required' => "Please enter price!", 
            'price.numeric' => "Please enter vaild price!", 
            'duration_months.required' => "Please enter duration!", 
            'duration_months.numeric' => "Please enter valid duration!", 
            'no_of_users.required' => "Please enter number of employees!", 
            'no_of_users.numeric' => "Please enter valid number of employees!", 
            'no_of_sms.required' => "Please enter number of sms!", 
            'no_of_sms.numeric' => "Please enter valid number of sms!", 
            'no_of_email.required' => "Please enter number of email!", 
            'no_of_email.numeric' => "Please enter valid number of email!", 
        ];
    }
}
