<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /**
     * Limpa entradas vazias dos arrays antes da validação,
     * evitando falsos erros de required_with quando o form
     * renderiza linhas em branco (top_contents, audience_locations).
     */
    protected function prepareForValidation(): void
    {
        // Remove itens de top_contents onde url, title e description estão todos vazios
        if ($this->has('top_contents')) {
            $topContents = collect($this->input('top_contents', []))
                ->filter(fn($item) =>
                    !empty(trim($item['url'] ?? '')) ||
                    !empty(trim($item['title'] ?? '')) ||
                    !empty(trim($item['description'] ?? ''))
                )
                ->values()
                ->all();

            $this->merge(['top_contents' => $topContents ?: null]);
        }

        // Remove itens de audience_locations onde city e percentage estão ambos vazios
        if ($this->has('audience_locations')) {
            $locations = collect($this->input('audience_locations', []))
                ->filter(fn($item) =>
                    !empty(trim($item['city'] ?? '')) ||
                    (isset($item['percentage']) && $item['percentage'] !== '')
                )
                ->values()
                ->all();

            $this->merge(['audience_locations' => $locations ?: null]);
        }
    }

    public function rules(): array
    {
        $isDraft = $this->input('action') === 'draft';

        return [
            'client_id'       => ['required', 'exists:clients,id'],
            'title'           => ['required', 'string', 'max:255'],
            'reference_month' => ['required', 'date_format:Y-m'],

            // Resumo — obrigatório ao publicar
            'summary'          => [$isDraft ? 'nullable' : 'required', 'string'],
            'next_month_goals' => [$isDraft ? 'nullable' : 'required', 'string'],

            // Tráfego pago — obrigatório ao publicar
            'investment'         => [$isDraft ? 'nullable' : 'required', 'numeric', 'min:0'],
            'revenue'            => [$isDraft ? 'nullable' : 'required', 'numeric', 'min:0'],
            'paid_conversations' => [$isDraft ? 'nullable' : 'required', 'integer', 'min:0'],
            'cpc'                => [$isDraft ? 'nullable' : 'required', 'numeric', 'min:0'],

            // Instagram — sempre opcional
            'ig_publications'   => ['nullable', 'integer', 'min:0'],
            'ig_interactions'   => ['nullable', 'integer', 'min:0'],
            'ig_reach'          => ['nullable', 'string', 'max:20'],
            'ig_new_followers'  => ['nullable', 'integer'],
            'ig_views'          => ['nullable', 'integer', 'min:0'],
            'ig_profile_visits' => ['nullable', 'integer', 'min:0'],

            // Top conteúdos (até 3) — sempre opcional
            'top_contents'               => ['nullable', 'array', 'max:3'],
            'top_contents.*.title'       => ['required_with:top_contents', 'string', 'max:255'],
            'top_contents.*.description' => ['nullable', 'string', 'max:500'],
            'top_contents.*.url'         => ['required_with:top_contents', 'url'],

            // Localizações (top 5) — sempre opcional
            'audience_locations'              => ['nullable', 'array', 'max:5'],
            'audience_locations.*.city'       => ['required_with:audience_locations', 'string', 'max:100'],
            'audience_locations.*.percentage' => ['required_with:audience_locations', 'numeric', 'min:0', 'max:100'],

            // Faixa etária — sempre opcional
            'audience_age'       => ['nullable', 'array'],
            'audience_age.13-17' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'audience_age.18-24' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'audience_age.25-34' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'audience_age.35-44' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'audience_age.45-54' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'audience_age.55+'   => ['nullable', 'numeric', 'min:0', 'max:100'],

            // Gênero — sempre opcional
            'audience_gender'        => ['nullable', 'array'],
            'audience_gender.male'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'audience_gender.female' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'client_id'          => 'cliente',
            'reference_month'    => 'mês de referência',
            'investment'         => 'valor investido',
            'revenue'            => 'receita gerada',
            'paid_conversations' => 'conversas pagas',
            'cpc'                => 'CPC',
            'summary'            => 'resumo do mês',
            'next_month_goals'   => 'metas para o próximo mês',
        ];
    }

    public function messages(): array
    {
        return [
            'reference_month.date_format' => 'O formato do mês de referência deve ser AAAA-MM (ex: 2026-03).',
        ];
    }
}