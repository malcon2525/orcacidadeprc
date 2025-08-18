<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class TestAdSyncConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ad:test-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testar se as configurações da sincronização AD estão funcionando';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testando configurações da Sincronização AD...');
        $this->newLine();

        // Verificar se as configurações existem no cache
        $frequency = Cache::get('ad_sync_frequency');
        $time = Cache::get('ad_sync_time');
        $enabled = Cache::get('ad_sync_enabled');
        $updatedAt = Cache::get('ad_sync_updated_at');

        $this->info('📋 Verificando se as configurações estão salvas:');
        
        if ($frequency !== null) {
            $this->line('✅ Frequência: ' . $frequency);
        } else {
            $this->error('❌ Frequência não encontrada no cache');
        }

        if ($time !== null) {
            $this->line('✅ Horário: ' . $time);
        } else {
            $this->error('❌ Horário não encontrado no cache');
        }

        if ($enabled !== null) {
            $this->line('✅ Status: ' . ($enabled ? 'Habilitada' : 'Desabilitada'));
        } else {
            $this->error('❌ Status não encontrado no cache');
        }

        if ($updatedAt !== null) {
            $this->line('✅ Última atualização: ' . date('d/m/Y H:i:s', strtotime($updatedAt)));
        } else {
            $this->warn('⚠️  Data de atualização não encontrada');
        }

        $this->newLine();

        // Testar se as configurações são válidas
        $this->info('🔍 Validando configurações:');
        
        $validFrequencies = ['daily', 'weekly', 'monthly'];
        if (in_array($frequency, $validFrequencies)) {
            $this->line('✅ Frequência válida: ' . $frequency);
        } else {
            $this->error('❌ Frequência inválida: ' . $frequency);
        }

        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
            $this->line('✅ Formato de horário válido: ' . $time);
        } else {
            $this->error('❌ Formato de horário inválido: ' . $time);
        }

        if (is_bool($enabled)) {
            $this->line('✅ Status válido: ' . ($enabled ? 'Habilitada' : 'Desabilitada'));
        } else {
            $this->error('❌ Status inválido: ' . $enabled);
        }

        $this->newLine();

        // Simular próxima execução
        if ($enabled) {
            $this->info('📅 Simulando próxima execução:');
            $nextExecution = $this->calculateNextExecution($frequency, $time);
            $this->line('Próxima execução: ' . $nextExecution);
        } else {
            $this->warn('⚠️  Sincronização desabilitada - não há próxima execução');
        }

        $this->newLine();
        $this->info('✅ Teste concluído!');
    }

    private function calculateNextExecution($frequency, $time)
    {
        $now = now();
        [$hours, $minutes] = explode(':', $time);
        
        $nextExecution = $now->copy()->setTime($hours, $minutes, 0);
        
        // Se já passou do horário hoje
        if ($nextExecution <= $now) {
            $nextExecution->addDay();
        }
        
        // Ajustar para frequência
        switch ($frequency) {
            case 'weekly':
                // Próximo domingo
                $daysToSunday = (7 - $nextExecution->dayOfWeek) % 7;
                $nextExecution->addDays($daysToSunday);
                break;
            case 'monthly':
                $nextExecution->addMonth();
                break;
        }
        
        return $nextExecution->format('d/m/Y \à\s H:i');
    }
}
