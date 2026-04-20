<?php

namespace App\Http\Requests\Admin;

class UpdateReportRequest extends StoreReportRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        // No update, client_id não pode ser alterado
        $rules['client_id'] = ['prohibited'];
        return $rules;
    }
}