<?php

namespace App\Http\Requests\inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
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
            'date' => ['required'],
            'invoice_number' => ['required'],
            'source_type' => ['required'],
            'source_id' => ['required'],
            'amount_paid' => ['required'],
            'payment_type' => ['required'],  
        ];
    }

    // Messages
    public function messages() 
    {
        return [
            'date.required' => 'Please select date!',
            'invoice_number.required' => 'Please enter invoice number!',
            'source_type.required' => 'Please select source type!',
            'source_id.required' => 'Please select source!',
            'amount_paid.required' => 'Please enter amount paid!',
            'payment_type.required' => 'Please select payment type!',  
        ];
    }
}
