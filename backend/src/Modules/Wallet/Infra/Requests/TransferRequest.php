<?php

namespace Modules\Wallet\Infra\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255|exists:users,email',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo ":attribute" é obrigatório.',
            'email.email' => 'O campo ":attribute" deve ser um e-mail válido.',
            'email.exists' => '":attribute" não encontrado.',
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