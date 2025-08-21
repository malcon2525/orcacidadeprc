<?php

namespace App\Http\Controllers\Api\Administracao\ActiveDirectory;

use App\Http\Controllers\Controller;
use App\Services\Logging\ActiveDirectoryLogService;
use Illuminate\Http\Request;
use App\Services\ActiveDirectorySyncService;
use App\Jobs\SyncActiveDirectoryJob;
use Illuminate\Support\Facades\Auth;

class SyncController extends Controller
{
    private $syncService;
    protected $logger;

    public function __construct(ActiveDirectoryLogService $logger)
    {
        $this->middleware('auth');
        $this->logger = $logger;
        $this->syncService = new ActiveDirectorySyncService($logger);
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
     * Executar sincronização manual
     */
    public function sync(Request $request)
    {
        $this->checkPermissions();
        
        try {
            $force = $request->get('force', false);
            $tipo = $force ? 'COMPLETA' : 'MANUAL';
            
            // Capturar usuário que executou a sincronização
            $usuario = auth()->user();
            $usuarioInfo = $usuario ? [
                'id' => $usuario->id,
                'nome' => $usuario->name,
                'email' => $usuario->email
            ] : null;
            
            // Log de início da sincronização
            $this->logger->inicioSincronizacao($tipo, [
                'executado_por' => $usuarioInfo,
                'forcada' => $force
            ]);
            
            // Executar sincronização e obter resultados
            $resultados = $this->syncService->sync($force, $usuarioInfo);
            
            // Log de sucesso
            $this->logger->sucessoSincronizacao($tipo, $resultados, [
                'executado_por' => $usuarioInfo,
                'executado_em' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => $force ? 'Sincronização completa concluída' : 'Sincronização manual concluída',
                'data' => [
                    'executado_em' => now()->toISOString(),
                    'executado_por' => $usuarioInfo,
                    'status' => 'concluido',
                    'tipo' => $force ? 'completa' : 'manual',
                    'resultados' => $resultados
                ]
            ]);

        } catch (\Exception $e) {
            $this->logger->erroCritico("SINCRONIZACAO_{$tipo}", $e->getMessage(), [
                'executado_por' => $usuarioInfo ?? null,
                'forcada' => $force
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro na sincronização: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar status da sincronização
     */
    public function status()
    {
        $this->checkPermissions();
        
        try {
            $stats = $this->syncService->getStats();

            // Retornar dados sem log desnecessário
            return response()->json([
                'success' => true,
                'data' => [
                    'estatisticas' => $stats
                ]
            ]);

        } catch (\Exception $e) {
            // Log apenas em caso de erro
            $this->logger->erroCritico('VERIFICAR_STATUS_SINCRONIZACAO', $e->getMessage(), [
                'verificado_por' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Testar conexão com AD
     */
    public function testConnection()
    {
        $this->checkPermissions();
        
        try {
            $result = $this->syncService->testConnection();

            // Retornar dados sem log desnecessário
            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            // Log apenas em caso de erro
            $this->logger->erroCritico('TESTE_CONEXAO', $e->getMessage(), [
                'testado_por' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro no teste de conexão: ' . $e->getMessage()
            ], 500);
        }
    }
}

