
@php
    $action = isset($report)
        ? route('admin.relatorios.update', $report)
        : route('admin.relatorios.store');
    $method = isset($report) ? 'PUT' : 'POST';
    $old = fn($field, $default = null) => old($field, isset($report) ? data_get($report, $field) : $default);
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6" id="report-form">
    @csrf
    @method($method)

    {{-- ── Cliente e Mês ─────────────────────────────────────── --}}
    <div class="card p-6">
        <h3 class="text-sm font-semibold font-title text-ink mb-5 flex items-center gap-2">
            <x-heroicon-o-calendar-days class="w-5 h-5 text-brand" /> Identificação
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="form-group sm:col-span-1">
                <label class="label">Cliente <span class="text-brand">*</span></label>
                @if(isset($report))
                    <input type="text" class="input opacity-60 cursor-not-allowed"
                           value="{{ $report->client?->trade_name ?? $report->client?->company_name ?? 'Cliente Desconhecido' }}" disabled>
                @else
                    <select name="client_id" class="select @error('client_id') input-error @enderror" required>
                        <option value="">Selecione...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->trade_name ?? $client->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="form-error">{{ $message == 'validation.required' ? 'Este campo é obrigatório' : $message }}</p> @enderror
                @endif
            </div>

            <div class="form-group">
                <label class="label">Mês de Referência <span class="text-brand">*</span></label>
                <input type="month" name="reference_month"
                       value="{{ old('reference_month', isset($report) ? $report->reference_month->format('Y-m') : '') }}"
                       placeholder="2026-03"
                       class="input @error('reference_month') input-error @enderror" required>
                @error('reference_month') <p class="form-error">{{ $message == 'validation.required' ? 'Este campo é obrigatório' : $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="label">Título <span class="text-brand">*</span></label>
                <input type="text" name="title"
                       value="{{ $old('title') }}"
                       placeholder="Ex: Relatório Março 2026"
                       class="input @error('title') input-error @enderror" required>
                @error('title') <p class="form-error">{{ $message == 'validation.required' ? 'Este campo é obrigatório' : $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- ── Tráfego Pago ────────────────────────────────────────── --}}
    <div class="card p-6">
        <h3 class="text-sm font-semibold font-title text-ink mb-1.5 flex items-center gap-2">
            <x-heroicon-o-currency-dollar class="w-5 h-5 text-brand" /> Tráfego Pago
        </h3>
        <p class="text-xs text-ink-muted mb-5">O ROI é calculado automaticamente com base no investimento e receita.</p>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="form-group">
                <label class="label">Valor Investido (R$) <span class="text-brand">*</span></label>
                <input type="number" name="investment" step="0.01" min="0"
                       value="{{ $old('investment', 0) }}"
                       class="input @error('investment') input-error @enderror">
                @error('investment') <p class="form-error">{{ $message == 'validation.required' ? 'Este campo é obrigatório' : $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="label">Receita Gerada (R$) <span class="text-brand">*</span></label>
                <input type="number" name="revenue" step="0.01" min="0"
                       value="{{ $old('revenue', 0) }}"
                       class="input @error('revenue') input-error @enderror">
                @error('revenue') <p class="form-error">{{ $message == 'validation.required' ? 'Este campo é obrigatório' : $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="label">Conversas <span class="text-brand">*</span></label>
                <input type="number" name="paid_conversations" min="0"
                       value="{{ $old('paid_conversations') }}" class="input @error('paid_conversations') input-error @enderror">
                @error('paid_conversations') <p class="form-error">{{ $message == 'validation.required' ? 'Este campo é obrigatório' : $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="label">CPC (R$) <span class="text-brand">*</span></label>
                <input type="number" name="cpc" step="0.01" min="0"
                       value="{{ $old('cpc') }}" class="input @error('cpc') input-error @enderror">
                @error('cpc') <p class="form-error">{{ $message == 'validation.required' ? 'Este campo é obrigatório' : $message }}</p> @enderror
            </div>
        </div>

        {{-- Preview ROI em tempo real --}}
        <div class="mt-4 card-accent rounded-xl px-4 py-3 flex items-center gap-4">
            <div class="text-xs text-ink-muted">ROI calculado:</div>
            <div id="roi-preview" class="text-green-400">—</div>
            <div class="text-xs text-ink-subtle font-mono" id="roi-formula">—</div>
        </div>
    </div>

    {{-- ── Instagram ───────────────────────────────────────────── --}}
    <div class="card p-6">
        <h3 class="text-sm font-semibold font-title text-ink mb-5 flex items-center gap-2">
            <x-ri-instagram-line class="w-5 h-5 text-brand" /> Resultados Instagram
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach([
                ['name' => 'ig_publications',   'label' => 'Publicações',       'type' => 'number', 'placeholder' => '12'],
                ['name' => 'ig_interactions',   'label' => 'Interações',        'type' => 'number', 'placeholder' => '3.400'],
                ['name' => 'ig_reach',          'label' => 'Alcance',           'type' => 'text',   'placeholder' => '56k'],
                ['name' => 'ig_new_followers',  'label' => 'Novos Seguidores',  'type' => 'number', 'placeholder' => '120'],
                ['name' => 'ig_views',          'label' => 'Visualizações',     'type' => 'number', 'placeholder' => '10000'],
                ['name' => 'ig_profile_visits', 'label' => 'Visitas ao Perfil', 'type' => 'number', 'placeholder' => '850'],
            ] as $field)
                <div class="form-group">
                    <label class="label">{{ $field['label'] }}</label>
                    <input type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                           value="{{ $old($field['name']) }}"
                           placeholder="{{ $field['placeholder'] }}"
                           class="input" {{ $field['type'] === 'number' ? 'min=0' : '' }}>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Top Conteúdos ───────────────────────────────────────── --}}
    <div class="card p-6">
        <h3 class="text-sm font-semibold font-title text-ink mb-1 flex items-center gap-2">
            <x-heroicon-o-star class="w-5 h-5 text-brand" /> Top Conteúdos
        </h3>
        <p class="text-xs text-ink-muted mb-5">Até 3 publicações com maior alcance do mês.</p>

        <div class="space-y-4" id="top-contents">
            @for($i = 0; $i < 3; $i++)
                @php
                    $content = $old("top_contents.{$i}") ?? (isset($report) ? ($report->top_contents[$i] ?? null) : null);
                @endphp
                <div class="card-accent rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 rounded-lg bg-brand flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                            {{ $i + 1 }}
                        </div>
                        <span class="text-sm font-medium text-ink">{{ $i + 1 }}º Conteúdo</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="form-group sm:col-span-1">
                            <label class="label">Link do Instagram</label>
                            <input type="url" name="top_contents[{{ $i }}][url]"
                                   value="{{ data_get($content, 'url') }}"
                                   placeholder="https://www.instagram.com/p/..."
                                   class="input text-xs @error("top_contents.{$i}.url") input-error @enderror">
                            @error("top_contents.{$i}.url") <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="label">Título</label>
                            <input type="text" name="top_contents[{{ $i }}][title]"
                                   value="{{ data_get($content, 'title') }}"
                                   placeholder="Título do post"
                                   class="input @error("top_contents.{$i}.title") input-error @enderror">
                            @error("top_contents.{$i}.title") <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="label">Descrição</label>
                            <input type="text" name="top_contents[{{ $i }}][description]"
                                   value="{{ data_get($content, 'description') }}"
                                   placeholder="Breve descrição..."
                                   class="input @error("top_contents.{$i}.description") input-error @enderror">
                            @error("top_contents.{$i}.description") <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    {{-- ── Público ───────────────────────────────────────── --}}
    <div class="card p-6">
        <h3 class="text-sm font-semibold font-title text-ink mb-5 flex items-center gap-2">
            <x-heroicon-o-users class="w-5 h-5 text-brand" /> Público
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Localizações top 5 --}}
            <div>
                <p class="text-xs font-semibold text-ink-muted uppercase tracking-wider mb-3">
                    Principais Localizações (Top 5)
                </p>
                <div class="space-y-2">
                    @for($i = 0; $i < 5; $i++)
                        @php
                            $loc = old("audience_locations.{$i}") ?? (isset($report) ? ($report->audience_locations[$i] ?? null) : null);
                        @endphp
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-ink-subtle w-4">{{ $i + 1 }}.</span>
                            <div class="flex-1">
                                <input type="text" name="audience_locations[{{ $i }}][city]"
                                       value="{{ data_get($loc, 'city') }}"
                                       placeholder="Cidade"
                                       class="input text-sm w-full @error("audience_locations.{$i}.city") input-error @enderror">
                            </div>
                            <div class="relative w-24">
                                <input type="number" name="audience_locations[{{ $i }}][percentage]"
                                       value="{{ data_get($loc, 'percentage') }}"
                                       min="0" max="100" step="0.1"
                                       placeholder="0"
                                       class="input text-sm pr-6 @error("audience_locations.{$i}.percentage") input-error @enderror">
                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-ink-subtle">%</span>
                            </div>
                        </div>
                        @error("audience_locations.{$i}.city") <p class="form-error mt-1">{{ $message }}</p> @enderror
                        @error("audience_locations.{$i}.percentage") <p class="form-error mt-1">{{ $message }}</p> @enderror
                    @endfor
                </div>
            </div>

            {{-- Faixa etária --}}
            <div>
                <p class="text-xs font-semibold text-ink-muted uppercase tracking-wider mb-3">
                    Faixa Etária
                </p>
                <div class="space-y-2">
                    @foreach(['13-17', '18-24', '25-34', '35-44', '45-54', '55+'] as $range)
                        @php $val = old("audience_age.{$range}") ?? (isset($report) ? ($report->audience_age[$range] ?? null) : null); @endphp
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-ink-muted w-12 font-mono">{{ $range }}</span>
                                <div class="relative flex-1">
                                    <input type="number" name="audience_age[{{ $range }}]"
                                           value="{{ $val }}"
                                           min="0" max="100" step="0.1"
                                           placeholder="0"
                                           class="input text-sm pr-6 @error("audience_age.{$range}") input-error @enderror">
                                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-ink-subtle">%</span>
                                </div>
                            </div>
                            @error("audience_age.{$range}") <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Gênero --}}
            <div>
                <p class="text-xs font-semibold text-ink-muted uppercase tracking-wider mb-3">
                    Gênero
                </p>
                <div class="space-y-2">
                    @foreach(['male' => 'Masculino', 'female' => 'Feminino'] as $key => $label)
                        @php $val = old("audience_gender.{$key}") ?? (isset($report) ? ($report->audience_gender[$key] ?? null) : null); @endphp
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-ink-muted w-20">{{ $label }}</span>
                                <div class="relative flex-1">
                                    <input type="number" name="audience_gender[{{ $key }}]"
                                           value="{{ $val }}"
                                           min="0" max="100" step="0.1"
                                           placeholder="0"
                                           class="input text-sm pr-6 @error("audience_gender.{$key}") input-error @enderror">
                                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-ink-subtle">%</span>
                                </div>
                            </div>
                            @error("audience_gender.{$key}") <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    @endforeach
                </div>

                {{-- Preview gênero --}}
                <div class="mt-4 card-accent rounded-xl p-3">
                    <div class="flex rounded-full overflow-hidden h-3 mb-2" id="gender-bar">
                        <div id="gender-male-bar"   class="bg-blue-500 h-full transition-all duration-300" style="width:50%"></div>
                        <div id="gender-female-bar" class="bg-brand h-full transition-all duration-300"   style="width:50%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-ink-muted">
                        <span>♂ <span id="gender-male-val">—</span></span>
                        <span>♀ <span id="gender-female-val">—</span></span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Resumo e Metas ──────────────────────────────────────── --}}
    <div class="card p-6">
        <h3 class="text-sm font-semibold font-title text-ink mb-5 flex items-center gap-2">
            <x-heroicon-o-document-text class="w-5 h-5 text-brand" /> Resumo & Metas
        </h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="form-group">
                <label class="label">Resumo do Mês <span class="text-brand">*</span></label>
                <textarea name="summary" rows="5" class="textarea @error('summary') input-error @enderror"
                          placeholder="Descreva os principais resultados e acontecimentos do mês...">{{ $old('summary') }}</textarea>
                @error('summary') <p class="form-error">{{ $message == 'validation.required' ? 'Este campo é obrigatório' : $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="label">Metas para o Próximo Mês <span class="text-brand">*</span></label>
                <textarea name="next_month_goals" rows="5" class="textarea @error('next_month_goals') input-error @enderror"
                          placeholder="Quais são os objetivos e metas planejadas para o próximo mês?">{{ $old('next_month_goals') }}</textarea>
                @error('next_month_goals') <p class="form-error">{{ $message == 'validation.required' ? 'Este campo é obrigatório' : $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- ── Ações ───────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3 justify-end">
        <a href="{{ route('admin.relatorios.index') }}" class="btn-secondary">Cancelar</a>
        @if(!isset($report) || $report->status !== 'published')
            <button type="submit" name="action" value="draft" class="btn-secondary">
                <x-heroicon-o-document class="w-4 h-4" /> Salvar Rascunho
            </button>
        @endif
        <button type="submit" name="action" value="publish" class="btn-primary">
            <x-heroicon-o-paper-airplane class="w-4 h-4" /> Salvar e Publicar
        </button>
    </div>

</form>

@push('scripts')
<script>
// Preview ROI em tempo real
function updateRoi() {
    const invInput = document.querySelector('[name=investment]');
    const revInput = document.querySelector('[name=revenue]');
    if (!invInput || !revInput) return;

    const inv = parseFloat(invInput.value) || 0;
    const rev = parseFloat(revInput.value) || 0;
    const roi = inv > 0 ? ((rev - inv) / inv * 100) : 0;
    
    const roiPreview = document.getElementById('roi-preview');
    const roiFormula = document.getElementById('roi-formula');
    
    if (roiPreview) {
        roiPreview.textContent = roi.toFixed(0) + '%';
        roiPreview.className = 'text-xl font-impact ' + (roi >= 0 ? 'text-green-400' : 'text-brand');
    }
    if (roiFormula) {
        roiFormula.textContent = 
            `(${rev.toLocaleString('pt-BR', {minimumFractionDigits:2})} − ${inv.toLocaleString('pt-BR', {minimumFractionDigits:2})}) / ${inv.toLocaleString('pt-BR', {minimumFractionDigits:2})} × 100`;
    }
}

const invInput = document.querySelector('[name=investment]');
const revInput = document.querySelector('[name=revenue]');
if (invInput) invInput.addEventListener('input', updateRoi);
if (revInput) revInput.addEventListener('input', updateRoi);
updateRoi();

// Preview gênero
function updateGender() {
    const maleInput = document.querySelector('[name="audience_gender[male]"]');
    const femaleInput = document.querySelector('[name="audience_gender[female]"]');
    if (!maleInput || !femaleInput) return;

    const m = parseFloat(maleInput.value) || 0;
    const f = parseFloat(femaleInput.value) || 0;
    const total = m + f || 100;

    const maleBar = document.getElementById('gender-male-bar');
    const femaleBar = document.getElementById('gender-female-bar');
    const maleVal = document.getElementById('gender-male-val');
    const femaleVal = document.getElementById('gender-female-val');

    if (maleBar) maleBar.style.width = (m / total * 100) + '%';
    if (femaleBar) femaleBar.style.width = (f / total * 100) + '%';
    if (maleVal) maleVal.textContent = m ? m + '%' : '—';
    if (femaleVal) femaleVal.textContent = f ? f + '%' : '—';
}

const maleInput = document.querySelector('[name="audience_gender[male]"]');
const femaleInput = document.querySelector('[name="audience_gender[female]"]');
if (maleInput) maleInput.addEventListener('input', updateGender);
if (femaleInput) femaleInput.addEventListener('input', updateGender);
updateGender();
</script>
@endpush