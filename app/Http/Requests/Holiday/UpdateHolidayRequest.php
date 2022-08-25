<?php

namespace App\Http\Requests\Holiday;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHolidayRequest extends FormRequest
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
            'date' => ['required', 'date'],
        ];
    }

    // Messages
    public function messages() 
    {
        return [
            'name.required' => "Please enter holiday name!",  
            'date.required' => "Please select holiday date!",
            'date.date' => "Please select valid holiday date!",
        ];
    }
}
