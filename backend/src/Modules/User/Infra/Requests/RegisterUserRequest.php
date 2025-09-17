<?php

namespace Modules\User\Infra\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo ":attribute" é obrigatório.',
            'email.email' => 'O campo ":attribute" deve ser um e-mail válido.',
            'unique' => 'O campo ":attribute" já está em uso.',
            'min' => 'O campo ":attribute" deve ter no mínimo :min caracteres.',
            'max' => 'O campo ":attribute" deve ter no máximo :max caracteres.',

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'error' => $validator->errors()->first()
            ], 422)
        );
    }
}