<?php

namespace App\Http\Controllers\Api\Administracao\ActiveDirectory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ConfigController extends Controller
{
    /**
     * Obter configurações atuais
     */
    public function index()
    {
        try {
            $config = [
                'sync_frequency' => Cache::get('ad_sync_frequency', 'daily'),
                'sync_time' => Cache::get('ad_sync_time', '02:00'),
                'sync_enabled' => Cache::get('ad_sync_enabled', true),
                'updated_at' => Cache::get('ad_sync_updated_at', null)
            ];

            return response()->json([
                'success' => true,
                'data' => $config
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao obter configurações AD', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar se as configurações estão funcionando
     */
    public function test()
    {
        try {
            $frequency = Cache::get('ad_sync_frequency');
            $time = Cache::get('ad_sync_time');
            $enabled = Cache::get('ad_sync_enabled');
            $updatedAt = Cache::get('ad_sync_updated_at');

            $tests = [
                'frequency_exists' => $frequency !== null,
                'time_exists' => $time !== null,
                'enabled_exists' => $enabled !== null,
                'updated_at_exists' => $updatedAt !== null,
                'frequency_valid' => in_array($frequency, ['daily', 'weekly', 'monthly']),
                'time_valid' => preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time),
                'next_execution' => $this->calculateNextExecution($frequency, $time, $enabled)
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'tests' => $tests,
                    'config' => [
                        'frequency' => $frequency,
                        'time' => $time,
                        'enabled' => $enabled,
                        'updated_at' => $updatedAt
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao testar configurações AD', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao testar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Salvar configurações
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'sync_frequency' => 'required|in:daily,weekly,monthly',
                'sync_time' => 'required|date_format:H:i',
                'sync_enabled' => 'boolean'
            ], [
                'sync_frequency.required' => 'A frequência é obrigatória',
                'sync_frequency.in' => 'Frequência inválida',
                'sync_time.required' => 'O horário é obrigatório',
                'sync_time.date_format' => 'Formato de horário inválido (HH:MM)'
            ]);

            // Salvar configurações no cache
            $now = now();
            Cache::put('ad_sync_frequency', $request->sync_frequency, now()->addYears(10));
            Cache::put('ad_sync_time', $request->sync_time, now()->addYears(10));
            Cache::put('ad_sync_enabled', $request->sync_enabled ?? true, now()->addYears(10));
            Cache::put('ad_sync_updated_at', $now->toISOString(), now()->addYears(10));

            Log::info('Configurações AD atualizadas', [
                'frequencia' => $request->sync_frequency,
                'horario' => $request->sync_time,
                'habilitado' => $request->sync_enabled ?? true,
                'atualizado_em' => $now->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Configurações salvas com sucesso',
                'data' => [
                    'sync_frequency' => $request->sync_frequency,
                    'sync_time' => $request->sync_time,
                    'sync_enabled' => $request->sync_enabled ?? true,
                    'updated_at' => $now->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações AD', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular próxima execução baseada nas configurações
     */
    private function calculateNextExecution($frequency, $time, $enabled)
    {
        if (!$enabled) {
            return 'Desabilitada';
        }

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