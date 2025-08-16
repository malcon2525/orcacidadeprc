<?php

namespace App\Http\Controllers\Api\Administracao\ActiveDirectory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    /**
     * Construtor com middleware de autenticação
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Verifica permissões do usuário
     */
    private function checkPermissions()
    {
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        // 2. Tem permissão específica?
        if ($user->hasPermission('gerenciar_active_directory')) {
            return true;
        }
        
        // 3. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }

    /**
     * Obter configurações atuais
     */
    public function index()
    {
        $this->checkPermissions();
        
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
        $this->checkPermissions();
        
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
        $this->checkPermissions();
        
        try {
            $request->validate([
                'sync_frequency' => 'required|in:daily,weekly,monthly',
                'sync_time' => 'required|date_format:H:i',
                'sync_enabled' => 'boolean'
            ], [
                'sync_frequency.required' => 'A frequência é obrigatória',
                'sync_frequency.in' => 'Frequência inválida',
                'sync_time.required' => 'O horário é obrigatório',
                'sync_time.date_format' => 'Formato de horário inválido'
            ]);

            // Salvar configurações no cache
            Cache::put('ad_sync_frequency', $request->sync_frequency);
            Cache::put('ad_sync_time', $request->sync_time);
            Cache::put('ad_sync_enabled', $request->get('sync_enabled', true));
            Cache::put('ad_sync_updated_at', now());

            Log::info('Configurações AD atualizadas', [
                'frequency' => $request->sync_frequency,
                'time' => $request->sync_time,
                'enabled' => $request->get('sync_enabled', true),
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Configurações salvas com sucesso!',
                'data' => [
                    'sync_frequency' => $request->sync_frequency,
                    'sync_time' => $request->sync_time,
                    'sync_enabled' => $request->get('sync_enabled', true),
                    'updated_at' => now()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações AD', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular próxima execução
     */
    private function calculateNextExecution($frequency, $time, $enabled)
    {
        if (!$enabled) {
            return null;
        }

        $now = now();
        $timeParts = explode(':', $time);
        $hour = (int) $timeParts[0];
        $minute = (int) $timeParts[1];

        $nextExecution = $now->copy()->setTime($hour, $minute, 0);

        if ($nextExecution <= $now) {
            switch ($frequency) {
                case 'daily':
                    $nextExecution->addDay();
                    break;
                case 'weekly':
                    $nextExecution->addWeek();
                    break;
                case 'monthly':
                    $nextExecution->addMonth();
                    break;
            }
        }

        return $nextExecution->format('Y-m-d H:i:s');
    }
}
