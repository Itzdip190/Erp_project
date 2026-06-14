<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class FaceLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_code'        => 'required|string',
            'face_encoding'      => 'required|array|size:128',
            'face_encoding.*'    => 'required|numeric',
            'device_fingerprint' => 'required|string|max:255',
            'device_name'        => 'required|string|max:255',
            'fcm_token'          => 'nullable|string',
        ];
    }
}
