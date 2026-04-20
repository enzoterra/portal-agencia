<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['super_admin', 'admin']);
    }

    public function rules(): array
    {
        return [
            'client_id'  => ['required', 'string', 'exists:clients,uuid'],
            'title'      => ['required', 'string', 'max:255'],
            'description'=> ['nullable', 'string'],
            'url'        => ['required', 'url', 'max:2048'],
            'type'       => ['nullable', 'string'],
            'month'      => ['required', 'integer', 'min:1', 'max:12'],
            'year'       => ['required', 'integer', 'min:2000', 'max:2100'],
            'is_public'  => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Selecione um cliente.',
            'client_id.exists'   => 'Cliente não encontrado.',
            'title.required'     => 'O título é obrigatório.',
            'url.required'       => 'A URL é obrigatória.',
            'url.url'            => 'Insira uma URL válida.',
            'month.required'     => 'O mês é obrigatório.',
            'year.required'      => 'O ano é obrigatório.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Checkbox desmarcado não envia valor — normaliza para false
        $this->merge(['is_public' => $this->boolean('is_public')]);
    }
}