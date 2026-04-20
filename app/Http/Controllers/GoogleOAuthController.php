<?php

namespace App\Http\Controllers;

use App\Models\GoogleOAuthToken;
use App\Http\Controllers\Controller;
use Google\Client as GoogleClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleOAuthController extends Controller
{
    private function buildClient(): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->addScope(\Google\Service\Calendar::CALENDAR_READONLY);
        $client->setAccessType('offline');
        $client->setPrompt('consent'); // garante refresh_token sempre
        return $client;
    }

    public function redirect(): RedirectResponse
    {
        try {
            $client = $this->buildClient();
            $url = $client->createAuthUrl();
            return redirect()->away($url);
        } catch (\Throwable $e) {
            Log::error('Erro ao redirecionar para Google: ' . $e->getMessage());
            return redirect()
                ->route('admin.configuracoes.index')
                ->with('error', 'Falha ao iniciar conexão com Google: ' . $e->getMessage());
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()
                ->route('admin.configuracoes.index')
                ->with('error', 'Autorização negada pelo Google.');
        }

        $client = $this->buildClient();
        $token  = $client->fetchAccessTokenWithAuthCode($request->input('code'));

        if (isset($token['error'])) {
            return redirect()
                ->route('admin.configuracoes.index')
                ->with('error', 'Erro ao obter token: ' . $token['error_description']);
        }

        $updateData = [
            'access_token' => $token['access_token'],
            'expires_at'   => now()->addSeconds($token['expires_in']),
        ];

        if (!empty($token['refresh_token'])) {
            $updateData['refresh_token'] = $token['refresh_token'];
        }

        GoogleOAuthToken::updateOrCreate(
            ['id' => 1], // única linha — conta global da agência
            $updateData
        );

        Log::info('Google OAuth token conectado/atualizado com sucesso na controller.');

        return redirect()
            ->route('admin.configuracoes.index')
            ->with('success', 'Conta Google conectada com sucesso.');
    }

    public function disconnect(): RedirectResponse
    {
        GoogleOAuthToken::truncate();

        return redirect()
            ->route('admin.configuracoes.index')
            ->with('success', 'Conta Google desconectada.');
    }
}