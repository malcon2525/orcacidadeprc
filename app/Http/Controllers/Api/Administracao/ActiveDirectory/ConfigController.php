<?php

namespace App\Http\Controllers\Api\Administracao\ActiveDirectory;

use App\Http\Controllers\Controller;
use App\Services\Logging\ActiveDirectoryLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    protected $logger;

    /**
     * Construtor com middleware de autenticação
     */
    public function __construct(ActiveDirectoryLogService $logger)
    {
        $this->middleware('auth');
        $this->logger = $logger;
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
        if ($user->hasPermission('sincronizar_ad')) {
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

            // Retornar dados sem log desnecessário
            return response()->json([
                'success' => true,
                'data' => $config
            ]);

        } catch (\Exception $e) {
            // Log apenas em caso de erro
            $this->logger->erroCritico('CONSULTA_CONFIGURACOES', $e->getMessage(), [
                'consultado_por' => Auth::id()
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

            // Validar se as configurações existem
            $frequencyExists = $frequency !== null;
            $timeExists = $time !== null;
            $enabledExists = $enabled !== null;
            $updatedAtExists = $updatedAt !== null;

            // Validar se as configurações são válidas
            $frequencyValid = in_array($frequency, ['daily', 'weekly', 'monthly']);
            $timeValid = $time && preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time);
            $enabledValid = is_bool($enabled);

            // Calcular próxima execução
            $nextExecution = $this->calculateNextExecution($frequency, $time, $enabled);

            // Determinar se tudo está válido
            $allValid = $frequencyValid && $timeValid && $enabledValid;

            $tests = [
                'frequency_exists' => $frequencyExists,
                'time_exists' => $timeExists,
                'enabled_exists' => $enabledExists,
                'updated_at_exists' => $updatedAtExists,
                'frequency_valid' => $frequencyValid,
                'time_valid' => $timeValid,
                'enabled_valid' => $enabledValid,
                'next_execution' => $nextExecution,
                'overall_valid' => $allValid
            ];

            // Determinar status e mensagem
            if ($allValid) {
                $status = 'success';
                $message = 'Configurações válidas!';
            } else {
                $status = 'warning';
                $message = 'Configurações com problemas';
            }

            // Retornar dados sem log desnecessário
            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => $message,
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
            // Log apenas em caso de erro
            $this->logger->erroCritico('TESTE_CONFIGURACOES', $e->getMessage(), [
                'testado_por' => Auth::id()
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
                'sync_time.date_format' => 'Formato de horário inválido (HH:MM)'
            ]);

            // Obter configurações atuais para o log
            $configAnteriores = [
                'sync_frequency' => Cache::get('ad_sync_frequency', 'daily'),
                'sync_time' => Cache::get('ad_sync_time', '02:00'),
                'sync_enabled' => Cache::get('ad_sync_enabled', true)
            ];

            // Salvar configurações no cache com expiração longa
            $now = now();
            Cache::put('ad_sync_frequency', $request->sync_frequency, now()->addYears(10));
            Cache::put('ad_sync_time', $request->sync_time, now()->addYears(10));
            Cache::put('ad_sync_enabled', $request->get('sync_enabled', true), now()->addYears(10));
            Cache::put('ad_sync_updated_at', $now->toISOString(), now()->addYears(10));

            // Configurações novas para o log
            $configNovas = [
                'sync_frequency' => $request->sync_frequency,
                'sync_time' => $request->sync_time,
                'sync_enabled' => $request->get('sync_enabled', true)
            ];

            // Log de alteração de configurações
            $this->logger->alteracaoConfiguracoes($configAnteriores, $configNovas, [
                'alterado_por' => Auth::id(),
                'alterado_em' => $now->toISOString()
            ]);

            // Log de sucesso
            $this->logger->sucessoAlteracaoConfiguracoes($configAnteriores, $configNovas, [
                'alterado_por' => Auth::id(),
                'alterado_em' => $now->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Configurações salvas com sucesso!',
                'data' => [
                    'sync_frequency' => $request->sync_frequency,
                    'sync_time' => $request->sync_time,
                    'sync_enabled' => $request->get('sync_enabled', true),
                    'updated_at' => $now->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            $this->logger->erroCritico('ALTERACAO_CONFIGURACOES', $e->getMessage(), [
                'alterado_por' => Auth::id(),
                'dados' => $request->all()
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
        try {
            // Se não estiver habilitado, retornar mensagem
            if (!$enabled) {
                return 'Sincronização desabilitada';
            }

            // Se não tiver frequência ou horário válidos, retornar mensagem
            if (!$frequency || !$time) {
                return 'Configuração incompleta';
            }

            $now = now();
            $timeParts = explode(':', $time);
            
            // Validar formato do horário
            if (count($timeParts) !== 2) {
                return 'Formato de horário inválido';
            }
            
            $hour = (int) $timeParts[0];
            $minute = (int) $timeParts[1];
            
            // Validar valores do horário
            if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
                return 'Horário inválido';
            }

            $nextExecution = $now->copy()->setTime($hour, $minute, 0);

            // Se já passou do horário hoje, calcular para o próximo período
            if ($nextExecution <= $now) {
                switch ($frequency) {
                    case 'daily':
                        $nextExecution->addDay();
                        break;
                    case 'weekly':
                        // Próximo domingo
                        $daysToSunday = (7 - $nextExecution->dayOfWeek) % 7;
                        $nextExecution->addDays($daysToSunday);
                        break;
                    case 'monthly':
                        $nextExecution->addMonth();
                        break;
                    default:
                        $nextExecution->addDay(); // Padrão diário
                        break;
                }
            }

            return $nextExecution->format('d/m/Y \à\s H:i');
            
        } catch (\Exception $e) {
            $this->logger->erroCritico('CALCULO_PROXIMA_EXECUCAO', $e->getMessage(), [
                'frequency' => $frequency,
                'time' => $time,
                'enabled' => $enabled
            ]);
            
            return 'Erro no cálculo';
        }
    }
}
