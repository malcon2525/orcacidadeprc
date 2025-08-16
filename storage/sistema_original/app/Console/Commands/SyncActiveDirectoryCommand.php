<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\ActiveDirectorySyncService;
use App\Jobs\SyncActiveDirectoryJob;

class SyncActiveDirectoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ad:sync {--force : Força sincronização completa} {--dry-run : Executa sem fazer alterações}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronizar usuários do Active Directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Iniciando sincronização do Active Directory...');
        
        try {
            $force = $this->option('force');
            $dryRun = $this->option('dry-run');

            $this->info('📋 Configurações:');
            $this->line("   - Tipo: " . ($force ? 'Sincronização Completa' : 'Sincronização Manual'));
            $this->line("   - Modo: " . ($dryRun ? 'Simulação (dry-run)' : 'Execução Real'));
            $this->line("   - Servidor: " . config('adldap.connections.default.settings.hosts.0', 'N/A'));

            if ($dryRun) {
                $this->warn('⚠️  Modo dry-run não implementado - use o job para execução real');
                return 1;
            }

            // Registrar log de execução via CLI
            Log::channel('ad')->info('Sincronização AD executada via comando CLI', [
                'executado_por' => 'CLI (comando artisan)',
                'tipo' => $force ? 'completa' : 'manual'
            ]);

            // Usar o job para execução
            dispatch(new SyncActiveDirectoryJob());
            $this->info('✅ Job de sincronização AD enviado para a fila!');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erro na sincronização: ' . $e->getMessage());
            Log::error('Erro no comando AD sync', [
                'error' => $e->getMessage(),
                'executado_por' => 'CLI (comando artisan)',
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
} 