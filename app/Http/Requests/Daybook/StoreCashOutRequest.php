<?php

namespace App\Http\Requests\Daybook;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashOutRequest extends FormRequest
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
            'amount' => ['required', 'numeric'],
            'payment_method' => ['required'],
            'description' => ['required']
        ];
    }

    // Messages
    public function messages() 
    {
        return [
            'amount.required' => "Please enter amount!",  
            'amount.numeric' => "Please enter valid amount!",  
            'payment_method.required' => "Please select payment method!",
            'description.required' => "Please Enter some description !"
        ];
    }
}
