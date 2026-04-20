<?php

// Responsável por sincronizar eventos do Google Calendar
// para a tabela calendar_events.
//
// Regra de negócio:
//   - Eventos cujo título contenha "[NomeCliente]" → client_id = id do cliente
//   - Demais eventos → client_id = NULL (evento geral da agência)
//
// Chamado pelo scheduler a cada hora (sem queue, sem jobs).
// ============================================================

namespace App\Domain\Calendar\Services;

use App\Domain\Calendar\Models\CalendarEvent;
use App\Domain\Client\Models\Client;
use App\Models\GoogleOAuthToken;
use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    public function sync(): void
    {
        $token = GoogleOAuthToken::first(); // Agência tem um único token

        if (! $token || ! $token->refresh_token) {
            return;
        }

        $client = $this->buildClient($token);
        $service = new Calendar($client);

        // Salva token atualizado se foi renovado
        if ($client->isAccessTokenExpired()) {
            Log::info('Token do Google expirado ou ausente, tentando renovar com refresh_token.');
            try {
                $newToken = $client->fetchAccessTokenWithRefreshToken($token->refresh_token);

                if (isset($newToken['error'])) {
                    Log::error('Erro retornado pela API do Google ao renovar token: ' . json_encode($newToken));
                    return;
                }

                if (isset($newToken['access_token'])) {
                    $token->update([
                        'access_token' => $newToken['access_token'],
                        'expires_at'   => \Carbon\Carbon::now()->addSeconds($newToken['expires_in'] ?? 3600),
                    ]);
                    Log::info('Token do Google renovado e salvo com sucesso pelo Scheduler.');
                }
            } catch (\Exception $e) {
                Log::error('Falha ao tentar renovar o token do Google: ' . $e->getMessage());
                return;
            }
        }

        // Pré-carrega clientes para o matching de tags
        $clients = Client::whereNotNull('trade_name')
            ->orWhereNotNull('company_name')
            ->get(['id', 'company_name', 'trade_name']);

        // Janela de sincronização: -30 dias até +90 dias
        $timeMin = now()->subDays(30)->toRfc3339String();
        $timeMax = now()->addDays(90)->toRfc3339String();

        $events = $service->events->listEvents('primary', [
            'timeMin'      => $timeMin,
            'timeMax'      => $timeMax,
            'singleEvents' => true,
            'orderBy'      => 'startTime',
            'maxResults'   => 500,
        ]);

        foreach ($events->getItems() as $googleEvent) {
            $this->upsertEvent($googleEvent, $clients);
        }

        // Remove eventos cancelados / deletados no Google que ainda existem localmente
        CalendarEvent::where('synced_at', '<', now()->subHours(2))->delete();
    }

    private function upsertEvent(\Google\Service\Calendar\Event $googleEvent, $clients): void
    {
        $title = $googleEvent->getSummary() ?? '';

        // Detecta se o título contém uma tag de cliente: [NomeCliente]
        $clientId = $this->resolveClientId($title, $clients);

        // Datas — eventos de dia inteiro têm date, não dateTime
        $startsAt = $googleEvent->getStart()->getDateTime()
            ?? $googleEvent->getStart()->getDate();
        $endsAt   = $googleEvent->getEnd()->getDateTime()
            ?? $googleEvent->getEnd()->getDate();

        $allDay = is_null($googleEvent->getStart()->getDateTime());

        CalendarEvent::updateOrCreate(
            // Chave de identificação única
            ['google_event_id' => $googleEvent->getId()],
            [
                'client_id'          => $clientId,
                'google_calendar_id' => 'primary',
                'title'              => $title,
                'description'        => $googleEvent->getDescription(),
                'location'           => $googleEvent->getLocation(),
                'starts_at'          => Carbon::parse($startsAt),
                'ends_at'            => Carbon::parse($endsAt),
                'all_day'            => $allDay,
                'status'             => $googleEvent->getStatus() ?? 'confirmed',
                'color'              => $googleEvent->getColorId()
                    ? $this->googleColorToHex($googleEvent->getColorId())
                    : null,
                'synced_at'          => now(),
            ]
        );
    }

    /**
     * Extrai [Tag] do título e tenta casar com um cliente.
     * Ex: "[Fazenda Bela Vista] Reunião de briefing" → client_id da Fazenda Bela Vista
     * Sem tag → NULL (evento geral)
     */
    private function resolveClientId(string $title, $clients): ?int
    {
        if (! preg_match('/^\[(.+?)\]/', $title, $matches)) {
            return null;
        }

        $tag = \Illuminate\Support\Str::slug($matches[1], '');

        if (empty($tag)) {
            return null;
        }

        $matched = $clients->first(function ($client) use ($tag) {
            $tradeName   = \Illuminate\Support\Str::slug($client->trade_name ?? '', '');
            $companyName = \Illuminate\Support\Str::slug($client->company_name ?? '', '');

            $matchTrade   = !empty($tradeName) && (str_contains($tradeName, $tag) || str_contains($tag, $tradeName));
            $matchCompany = !empty($companyName) && (str_contains($companyName, $tag) || str_contains($tag, $companyName));

            return $matchTrade || $matchCompany;
        });

        return $matched?->id;
    }

    /**
     * Google usa IDs numéricos de cor (1–11).
     * Mapeamento aproximado para hex.
     */
    private function googleColorToHex(string $colorId): string
    {
        return [
            '1'  => '#A4BDFC', '2'  => '#7AE7BF', '3'  => '#DBADFF',
            '4'  => '#FF887C', '5'  => '#FBD75B', '6'  => '#FFB878',
            '7'  => '#46D6DB', '8'  => '#E1E1E1', '9'  => '#5484ED',
            '10' => '#51B749', '11' => '#DC2127',
        ][$colorId] ?? '#E1E1E1';
    }

    private function buildClient(GoogleOAuthToken $token): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessToken([
            'access_token'  => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'expires_in'    => 3600,
        ]);

        return $client;
    }
}