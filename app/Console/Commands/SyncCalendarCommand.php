<?php

namespace App\Console\Commands;

use App\Domain\Calendar\Services\GoogleCalendarService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncCalendarCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza eventos do Google Calendar para o banco de dados local';

    /**
     * Execute the console command.
     */
    public function handle(GoogleCalendarService $service): int
    {
        $this->info('Iniciando sincronização do Google Calendar...');
        Log::info('--- Início da Sincronização via Console (calendar:sync) ---');

        try {
            $service->sync();
            
            $this->info('Sincronização concluída com sucesso!');
            Log::info('Calendário sincronizado com sucesso via Scheduler/Console.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erro durante a sincronização: ' . $e->getMessage());
            Log::error('Erro ao sincronizar calendário via Console: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            return Command::FAILURE;
        } finally {
            Log::info('--- Fim da Sincronização via Console ---');
        }
    }
}
