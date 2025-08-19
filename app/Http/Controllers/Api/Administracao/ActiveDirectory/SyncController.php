<?php

namespace App\Http\Controllers\Api\Administracao\ActiveDirectory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ActiveDirectorySyncService;
use App\Jobs\SyncActiveDirectoryJob;
use Illuminate\Support\Facades\Auth;

class SyncController extends Controller
{
    private $syncService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->syncService = new ActiveDirectorySyncService();
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
            
            // Capturar usuário que executou a sincronização
            $usuario = auth()->user();
            $usuarioInfo = $usuario ? [
                'id' => $usuario->id,
                'nome' => $usuario->name,
                'email' => $usuario->email
            ] : null;
            
            // Executar sincronização e obter resultados
            $resultados = $this->syncService->sync($force, $usuarioInfo);
            
            // Registrar log
            Log::channel('ad')->info('Sincronização AD concluída', [
                'tipo' => $force ? 'completa' : 'manual',
                'executado_por' => $usuarioInfo,
                'resultados' => $resultados,
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
            Log::error('Erro na sincronização AD', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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

            return response()->json([
                'success' => true,
                'data' => [
                    'estatisticas' => $stats
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao verificar status AD', [
                'error' => $e->getMessage()
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

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro no teste de conexão: ' . $e->getMessage()
            ], 500);
        }
    }
}

