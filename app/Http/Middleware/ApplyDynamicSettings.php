<?php

namespace App\Http\Middleware;

use App\Domain\Setting\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyDynamicSettings
{
    public function __construct(
        private readonly SettingService $settings
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        // 1. Session Lifetime
        if ($lifetime = $this->settings->get('session_lifetime')) {
            config(['session.lifetime' => (int) $lifetime]);
        }

        // 2. Google OAuth
        if ($clientId = $this->settings->get('google_client_id')) {
            config(['services.google.client_id' => $clientId]);
        }
        if ($clientSecret = $this->settings->get('google_client_secret')) {
            config(['services.google.client_secret' => $clientSecret]);
        }

        // Redirect URI dinâmico se não estiver no env
        if (!config('services.google.redirect')) {
            config(['services.google.redirect' => route('admin.google.callback')]);
        }

        // 3. Force HTTPS se configurado
        /*if ($this->settings->get('force_https', false) && !app()->isLocal()) {
            // Verifica também o header X-Forwarded-Proto (proxy da Hostinger)
            $isSecure = $request->isSecure()
                || $request->header('X-Forwarded-Proto') === 'https'
                || $request->header('X-Forwarded-SSL') === 'on';

            if (!$isSecure) {
                return redirect()->secure($request->getRequestUri());
            }
        }*/

        return $next($request);
    }
}
