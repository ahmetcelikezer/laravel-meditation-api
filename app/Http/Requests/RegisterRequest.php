<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return !$this->bearerToken();
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ];
    }
}
