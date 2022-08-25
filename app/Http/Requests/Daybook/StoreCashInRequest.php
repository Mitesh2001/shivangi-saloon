<?php

namespace App\Http\Requests\Daybook;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashInRequest extends FormRequest
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
            'description' => ['required']
        ];
    }

    // Messages
    public function messages() 
    {
        return [
            'amount.required' => "Please enter amount!",  
            'amount.numeric' => "Please enter valid amount!", 
            'description.required' => "Please Enter some description !"
        ];
    }
}
