<?php

namespace App\Http\Requests\Status;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
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
            // 'title' => ['required', Rule::unique('statuses')->where('id', '!=', $this->id)->whereNull('deleted_at')], 
            'title' => ['required'], 
        ];
    }

    // Messages
    public function messages() 
    {
        return [
            'title.required' => "Please enter title!", 
        ];
    }
}
