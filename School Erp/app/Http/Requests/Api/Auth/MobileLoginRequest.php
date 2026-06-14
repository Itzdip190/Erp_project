<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class MobileLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_code' => 'required|string',
            'email'       => 'required|email',
            'password'    => 'required|string',
            'device_name' => 'required|string',
            'fcm_token'   => 'nullable|string',
        ];
    }
}
