<?php

namespace App\Http\Requests\Status;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStatusRequest extends FormRequest
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
            'title' => ['required', Rule::unique('statuses')->whereNull('deleted_at')], 
        ];
    }

    // Messages
    public function messages() 
    {
        return [
            'title.required' => "Please enter title!",
            'title.unique' => "Title already exist!", 
        ];
    }
}
