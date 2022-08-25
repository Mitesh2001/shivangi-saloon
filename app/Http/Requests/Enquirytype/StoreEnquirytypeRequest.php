<?php

namespace App\Http\Requests\enquirytype;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnquirytypeRequest extends FormRequest
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
            'name' => ['required', Rule::unique('categories')->whereNull('deleted_at')], 
        ];
    }

    // Messages
    public function messages() 
    {
        return [
            'name.required' => "Please enter name!",
            'name.unique' => "Enquiry type already exist!", 
        ];
    }
}
