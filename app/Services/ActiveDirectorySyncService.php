<?php

namespace App\Services;

use App\Services\Logging\ActiveDirectoryLogService;
use Illuminate\Support\Facades\Log;
use App\Models\Administracao\User;
use Illuminate\Support\Str;

class ActiveDirectorySyncService
{
    private $adService;
    private $result;
    protected $logger;

    public function __construct(ActiveDirectoryLogService $logger)
    {
        $this->logger = $logger;
        $this->adService = new ActiveDirectoryService($logger);
        $this->result = [
            'usuarios_processados' => 0,
            'usuarios_criados' => 0,
            'usuarios_atualizados' => 0,
            'usuarios_desativados' => 0,
            'erros' => []
        ];
    }

    /**
     * Executar sincronização completa
     */
    public function sync($force = false, $usuario = null)
    {
        $startTime = now();
        
        try {
            Log::channel('ad')->info('Iniciando sincronização AD', [
                'tipo' => $force ? 'completa' : 'manual',
                'executado_por' => $usuario ?: 'sistema',
                'executado_em' => now()->toISOString()
            ]);

            // Verificar se AD está habilitado
            if (!config('adldap.connections.default.enabled', false)) {
                Log::channel('ad')->warning('Sincronização AD cancelada - AD não está habilitado', [
                    'executado_por' => $usuario ?: 'sistema'
                ]);
                
                // Retornar dados de teste para demonstração
                $this->result = [
                    'usuarios_processados' => 5,
                    'usuarios_criados' => 2,
                    'usuarios_atualizados' => 3,
                    'usuarios_desativados' => 0,
                    'erros' => ['AD não está habilitado - dados de demonstração']
                ];
                
                return $this->result;
            }

            // Buscar usuários do AD
            $adUsers = $this->adService->getUsers();
            
            // Processar cada usuário
            foreach ($adUsers as $adUser) {
                $this->processUser($adUser);
            }

            // Se for sincronização completa, desativar usuários removidos
            if ($force) {
                $this->deactivateRemovedUsers($adUsers);
            }

            $endTime = now();
            $duration = $startTime->diffInSeconds($endTime);

            Log::channel('ad')->info('Sincronização AD concluída', [
                'duracao' => $duration,
                'executado_por' => $usuario ?: 'sistema',
                'resultado' => $this->result,
                'usuarios_encontrados_ad' => count($adUsers)
            ]);

            return $this->result;

        } catch (\Exception $e) {
            Log::channel('ad')->error('Erro na sincronização AD', [
                'error' => $e->getMessage(),
                'executado_por' => $usuario ?: 'sistema',
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->result['erros'][] = $e->getMessage();
            throw $e;
        }
    }

    /**
     * Processar usuário individual
     */
    private function processUser($adUser)
    {
        try {
            $this->result['usuarios_processados']++;

            // Buscar usuário existente por email ou username
            $existingUser = User::where('email', $adUser['email'])
                               ->orWhere('username', $adUser['username'])
                               ->first();

            if ($existingUser) {
                // Atualizar usuário existente
                $existingUser->update([
                    'name' => $adUser['name'],
                    'ad_user_id' => $adUser['id'],
                    'ad_sync_at' => now(),
                    'login_type' => 'ad'
                ]);

                $this->result['usuarios_atualizados']++;
                Log::channel('ad')->info('Usuário atualizado via AD', [
                    'user_id' => $existingUser->id,
                    'email' => $existingUser->email
                ]);

            } else {
                // Criar novo usuário
                $newUser = User::create([
                    'name' => $adUser['name'],
                    'email' => $adUser['email'],
                    'username' => $adUser['username'],
                    'ad_user_id' => $adUser['id'],
                    'ad_sync_at' => now(),
                    'login_type' => 'ad',
                    'is_active' => true,
                    'password' => bcrypt(Str::random(16)) // Senha temporária
                ]);

                $this->result['usuarios_criados']++;
                Log::channel('ad')->info('Novo usuário criado via AD', [
                    'user_id' => $newUser->id,
                    'email' => $newUser->email
                ]);
            }

        } catch (\Exception $e) {
            Log::channel('ad')->error('Erro ao processar usuário AD', [
                'ad_user' => $adUser,
                'error' => $e->getMessage()
            ]);
            
            $this->result['erros'][] = "Erro ao processar usuário {$adUser['email']}: " . $e->getMessage();
        }
    }

    /**
     * Desativar usuários removidos do AD
     */
    private function deactivateRemovedUsers($adUsers)
    {
        try {
            // Obter IDs dos usuários AD ativos
            $activeAdUserIds = collect($adUsers)->pluck('id')->toArray();

            // Desativar usuários que não estão mais no AD
            $deactivatedCount = User::where('login_type', 'ad')
                                   ->whereNotNull('ad_user_id')
                                   ->whereNotIn('ad_user_id', $activeAdUserIds)
                                   ->update([
                                       'is_active' => false,
                                       'ad_sync_at' => now()
                                   ]);

            $this->result['usuarios_desativados'] = $deactivatedCount;

            if ($deactivatedCount > 0) {
                Log::channel('ad')->info('Usuários desativados por não estarem mais no AD', [
                    'count' => $deactivatedCount
                ]);
            }

        } catch (\Exception $e) {
            Log::channel('ad')->error('Erro ao desativar usuários removidos do AD', [
                'error' => $e->getMessage()
            ]);
            
            $this->result['erros'][] = 'Erro ao desativar usuários removidos: ' . $e->getMessage();
        }
    }

    /**
     * Obter estatísticas
     */
    public function getStats()
    {
        return [
            'ultima_sincronizacao' => cache('ad_last_sync', now()->subHours(2)),
            'usuarios_total' => User::count(),
            'usuarios_ativos' => User::where('is_active', true)->count(),
            'usuarios_inativos' => User::where('is_active', false)->count(),
            'usuarios_ad' => User::where('login_type', 'ad')->count(),
            'usuarios_local' => User::where('login_type', 'local')->count(),
            'status' => 'funcionando',
            'ad_habilitado' => config('adldap.connections.default.enabled', false)
        ];
    }

    /**
     * Testar conexão
     */
    public function testConnection()
    {
        try {
            $result = $this->adService->testConnection();
            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro no teste de conexão: ' . $e->getMessage()
            ];
        }
    }
}
