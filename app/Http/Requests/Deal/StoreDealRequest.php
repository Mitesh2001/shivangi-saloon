<?php

namespace App\Http\Requests\Deal;

use Illuminate\Foundation\Http\FormRequest;

class StoreDealRequest extends FormRequest
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
            // 'segament_id' => ['required'],
            'deal_name' => ['required'], 
            'deal_code' => ['required', 'alpha_num', 'max:25'],  
            'deal_description' => ['max:2000'], 
            'validity' => ['required', 'date'], 
            // 'start_at' => ['required'], 
            // 'end_at' => ['required'], 
            'applicable_on_weekends' => ['required'], 
            'applicable_on_holidays' => ['required'], 
            'applicable_on_bday_anniv' => ['required'], 
            'week_days' => ['required'], 
            // 'benefit_type' => ['required'], 
            'invoice_min_amount' => ['numeric'], 
            'invoice_max_amount' => ['numeric'], 
            'redemptions_max' => ['required', 'numeric'], 
            'discount' => ['required', 'numeric'], 
        ];
    }

    // Messages
    public function messages() 
    {
        return [
            // 'segament_id.required' => 'Please enter select customer segament!',
            'deal_name.required' => 'Please enter deal name!',
            'deal_code.required' => 'Please enter deal code!',  
            'deal_code.alpha_num' => 'Deal code must contain only letters and numbers!',  
            'deal_code.max' => 'Deal code may not be greater than 25 characters!',  
            'deal_description.max' => 'Deal code may not be greater than 2000 characters!',
            'validity.required' => 'Please select deal validity date!', 
            // 'start_at.required' => 'Please select deal start time!', 
            // 'end_at.required' => 'Please select deal end time!', 
            'applicable_on_weekends.required' => 'Please select applicable on weekends!', 
            'applicable_on_bday_anniv.required' => 'Please select applicable on bday/anniv!', 
            'week_days.required' => 'Please select week days!', 
            // 'benefit_type.required' => 'Please select benefit type!', 
            // 'invoice_min_amount.required' => 'Please enter min invoice amount!', 
            'invoice_min_amount.numeric' => 'Please enter valid min invoice amount!', 
            // 'invoice_max_amount.required' => 'Please enter max invoice amount!!', 
            'invoice_max_amount.numeric' => 'Please enter valid max invoice amount!', 
            'redemptions_max.required' => 'Please enter redemptions max!', 
            'redemptions_max.numeric' => 'Please enter valid redemptions max!', 
            'discount.required' => 'Please enter discount!', 
            'discount.numeric' => 'Please enter valid discount!', 
        ];
    }
}
