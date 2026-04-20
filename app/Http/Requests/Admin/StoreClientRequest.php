<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'company_name'               => ['required', 'string', 'max:255'],
            'trade_name'                 => ['nullable', 'string', 'max:255'],
            'cnpj'                       => ['nullable', 'string', 'max:18', 'unique:clients,cnpj'],
            'email'                      => ['required', 'email', 'max:255'],
            'phone'                      => ['nullable', 'string', 'max:20'],
            'monthly_fee'                => ['required', 'numeric', 'min:0'],
            'contract_start'             => ['nullable', 'date'],
            'contract_end'               => ['nullable', 'date', 'after_or_equal:contract_start'],
            'status'                     => ['required', 'in:active,inactive,suspended'],
            'show_roi'                   => ['boolean'],
            'notes'                      => ['nullable', 'string'],
            // Usuário de acesso
            'user_name'                  => ['required', 'string', 'max:255'],
            'user_email'                 => ['required', 'email', 'unique:users,email'],
            'user_password'              => ['required', 'string', \Illuminate\Validation\Rules\Password::min(8)->numbers(), 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'company_name'  => 'razão social',
            'monthly_fee'   => 'mensalidade',
            'user_name'     => 'nome do usuário',
            'user_email'    => 'e-mail de acesso',
            'user_password' => 'senha',
        ];
    }
}