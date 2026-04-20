<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $clientId = $this->route('client')->id;

        return [
            'company_name'   => ['required', 'string', 'max:255'],
            'trade_name'     => ['nullable', 'string', 'max:255'],
            'cnpj'           => ['nullable', 'string', 'max:18', "unique:clients,cnpj,{$clientId}"],
            'email'          => ['required', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'monthly_fee'    => ['required', 'numeric', 'min:0'],
            'contract_start' => ['nullable', 'date'],
            'contract_end'   => ['nullable', 'date', 'after_or_equal:contract_start'],
            'status'         => ['required', 'in:active,inactive,suspended'],
            'show_roi'       => ['boolean'],
            'notes'          => ['nullable', 'string'],
        ];
    }
}