<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\ActiveDirectorySyncService;

class SyncActiveDirectoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutos
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Log::channel('ad')->info('Iniciando job de sincronização automática do AD', [
                'executado_por' => 'sistema (agendamento)'
            ]);

            $syncService = new ActiveDirectorySyncService();
            $result = $syncService->sync(true, null); // true = sincronização completa, null = sistema

            Log::channel('ad')->info('Job de sincronização AD concluído', [
                'executado_por' => 'sistema (agendamento)',
                'resultado' => $result
            ]);

        } catch (\Exception $e) {
            Log::channel('ad')->error('Erro no job de sincronização AD', [
                'error' => $e->getMessage(),
                'executado_por' => 'sistema (agendamento)',
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-throw para que o job seja marcado como falhado
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::channel('ad')->error('Job de sincronização AD falhou', [
            'error' => $exception->getMessage(),
            'executado_por' => 'sistema (agendamento)',
            'trace' => $exception->getTraceAsString()
        ]);
    }
} 