<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CheckAdSyncConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ad:check-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar configurações atuais da sincronização AD';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando configurações da Sincronização AD...');
        $this->newLine();

        // Obter configurações do cache
        $frequency = Cache::get('ad_sync_frequency', 'daily');
        $time = Cache::get('ad_sync_time', '02:00');
        $enabled = Cache::get('ad_sync_enabled', true);
        $updatedAt = Cache::get('ad_sync_updated_at', null);

        // Exibir configurações
        $this->table(
            ['Configuração', 'Valor'],
            [
                ['Status', $enabled ? '✅ Habilitada' : '❌ Desabilitada'],
                ['Frequência', $this->getFrequencyText($frequency)],
                ['Horário', $time . 'h'],
                ['Última Atualização', $updatedAt ? date('d/m/Y H:i:s', strtotime($updatedAt)) : 'Nunca'],
            ]
        );

        $this->newLine();
        
        if ($enabled) {
            $this->info('📅 Próxima execução: ' . $this->getNextExecution($frequency, $time));
        } else {
            $this->warn('⚠️  Sincronização automática está desabilitada');
        }

        $this->newLine();
        $this->info('💡 Para alterar configurações, acesse: /admin/active-directory');
    }

    private function getFrequencyText($frequency)
    {
        $frequencies = [
            'daily' => 'Diária',
            'weekly' => 'Semanal (Domingos)',
            'monthly' => 'Mensal'
        ];

        return $frequencies[$frequency] ?? 'Desconhecida';
    }

    private function getNextExecution($frequency, $time)
    {
        $now = now();
        $hours = (int)explode(':', $time)[0];
        $minutes = (int)explode(':', $time)[1];
        
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
