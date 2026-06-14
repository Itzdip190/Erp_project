<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class OtpVerifyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_code' => 'required|string',
            'phone'       => 'required|digits:10',
            'otp'         => 'required|digits:6',
            'device_name' => 'required|string',
            'fcm_token'   => 'nullable|string',
        ];
    }
}
