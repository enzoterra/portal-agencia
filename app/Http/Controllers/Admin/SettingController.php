<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Calendar\Services\GoogleCalendarService;
use App\Http\Controllers\Controller;
use App\Models\GoogleOAuthToken;
use App\Domain\Setting\Services\SettingService;
use App\Support\Traits\HasAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    use HasAuditLog;

    public function __construct(
        private readonly SettingService $settings
    ) {}

    public function index()
    {
        $oauthToken      = GoogleOAuthToken::first();
        $oauthConnected  = $oauthToken && $oauthToken->access_token;
        $oauthExpiresAt  = $oauthToken?->expires_at;

        // Atividades importantes (Audit Logs)
        $auditLogs = \App\Models\AuditLog::with(['user', 'client'])
            ->latest('created_at')
            ->limit(30)
            ->get();

        return view('admin.settings.index', compact(
            'oauthConnected',
            'oauthExpiresAt',
            'auditLogs',
        ))->with('settings', $this->settings);
    }

    public function update(Request $request)
    {
        $tab = $request->input('_tab', 'google');

        $validated = match($tab) {
            'google'   => $request->validate([
                'google_client_id'     => ['nullable', 'string', 'max:255'],
                'google_client_secret' => ['nullable', 'string', 'max:255'],
                'google_redirect_uri'  => ['nullable', 'url', 'max:255'],
            ]),
            'security' => $request->validate([
                'login_max_attempts' => ['required', 'integer', 'min:1', 'max:20'],
                'login_decay_minutes'=> ['required', 'integer', 'min:1', 'max:60'],
                'session_lifetime'   => ['required', 'integer', 'min:15', 'max:1440'],
                'force_https'        => ['nullable', 'boolean'],
            ]),
            'finance'  => $request->validate([
                'pix_key'  => ['nullable', 'string', 'max:255'],
                'pix_name' => ['nullable', 'string', 'max:255'],
                'pix_city' => ['nullable', 'string', 'max:255'],
            ]),
            default => [],
        };

        // Normaliza checkbox
        if ($tab === 'security') {
            $validated['force_https'] = $request->boolean('force_https');
        }

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                $this->settings->set($key, $value);
            }
        }

        // Registrar atividade
        $this->recordActivity("Updated settings tab: {$tab}", new \App\Domain\Setting\Models\Setting(), null, $validated);

        return back()
            ->with('success', 'Configurações salvas com sucesso.')
            ->with('settings_tab', $tab);
    }

    public function maintenance(Request $request)
    {
        $action = $request->input('action');

        match($action) {
            'clear_cache'  => $this->clearCache(),
            'sync_calendar'=> app(GoogleCalendarService::class)->sync(),
            'mark_overdue' => $this->markOverduePayments(),
            default        => null,
        };

        $messages = [
            'clear_cache'   => 'Cache limpo com sucesso.',
            'sync_calendar' => 'Calendário sincronizado.',
            'mark_overdue'  => 'Pagamentos vencidos verificados.',
        ];

        // Registrar atividade
        $this->recordActivity("Executed maintenance action: {$action}", new \App\Domain\Setting\Models\Setting());

        return back()
            ->with('success', $messages[$action] ?? 'Ação executada.')
            ->with('settings_tab', 'maintenance');
    }

    private function clearCache(): void
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
    }

    private function markOverduePayments(): void
    {
        \App\Domain\Financial\Models\Payment::where('status', 'pending')
            ->where('due_date', '<', today())
            ->update(['status' => 'overdue']);
    }
}
