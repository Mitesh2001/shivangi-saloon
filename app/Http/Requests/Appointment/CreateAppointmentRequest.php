<?php

namespace App\Http\Requests\appointment;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRequest extends FormRequest
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
            'client_external_id' => 'required',
            'contact_number' => ['required', 'numeric', 'digits:10'],
            'email' => ['email'],
            'address' => ['required'],
            'user_assigned_id' => ['required'],
            'status_id' => ['required'],
            'appointment_for' => 'required',
            'date' => ['required', 'date'],
            'start_at' => 'required',
            'end_at' => 'required',
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "client_external_id.required" => "Please select client name!",
            "contact_number.required" => "Please enter contact number!",
            "contact_number.numeric" => "Please enter valid contact number!",
            "contact_number.digits" => "Please enter valid contact number!", 
            "email.email" => "Please enter valid email!",
            "address.required" => "Please enter address!", 
            "user_assigned_id.required" => "Please select representative!",
            "status_id.required" => "Please select appointment status!",
            "appointment_for.required" => "Please enter appointment for!",
            "date.required" => "Please enter date of appointment!",
            "start_at.required" => "Please select appointment start time!",
            "end_at.required" => "Please select appointment end time!",
        ];
    }
}
