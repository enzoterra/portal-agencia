<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['super_admin', 'admin']);
    }

    public function rules(): array
    {
        return [
            'client_id'      => ['required', 'string', 'exists:clients,uuid'],
            'amount'         => ['required', 'numeric', 'min:0'],
            'due_date'       => ['required', 'date'],
            'status'         => ['nullable', Rule::in(['pending', 'paid', 'overdue', 'cancelled', 'under_review'])],
            'payment_method' => ['nullable', Rule::in(['pix', 'bank_transfer', 'credit_card', 'other'])],
            'reference'      => ['nullable', 'string', 'max:255'],
            'notes'          => ['nullable', 'string'],
        ];
    }
}
