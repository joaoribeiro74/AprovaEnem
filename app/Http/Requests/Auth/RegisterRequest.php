<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => Str::lower((string) $this->input('email')),
            'phone' => preg_replace('/\D/', '', (string) $this->input('phone')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:32'],
            'last_name' => ['required', 'string', 'max:50'],
            'nickname' => ['nullable', 'string', 'max:32'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'digits:11', 'unique:users,phone'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O campo nome deve ter no máximo :max caracteres.',
            'last_name.required' => 'O campo sobrenome é obrigatório.',
            'last_name.max' => 'O campo sobrenome deve ter no máximo :max caracteres.',
            'nickname.max' => 'O campo apelido deve ter no máximo :max caracteres.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Digite um formato de e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'phone.required' => 'O campo telefone é obrigatório.',
            'phone.digits' => 'Digite um telefone válido com DDD (11 dígitos).',
            'phone.unique' => 'Este telefone já está cadastrado.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.min' => 'A senha deve ter no mínimo :min caracteres.',
        ];
    }
}
