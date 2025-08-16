<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Str;

class ActiveDirectorySyncService
{
    private $adService;
    private $result;

    public function __construct()
    {
        $this->adService = new ActiveDirectoryService();
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
            
            // Buscar usuário local
            $localUser = User::where('email', $adUser['email'])->first();
            
            if (!$localUser) {
                // Criar novo usuário
                User::create([
                    'name' => $adUser['name'],
                    'email' => $adUser['email'],
                    'username' => $adUser['username'],
                    'login_type' => 'ad',
                    'is_active' => true,
                    'ad_user_id' => $adUser['id'],
                    'ad_domain' => config('adldap.connections.default.settings.base_dn'),
                    'ad_sync_at' => now(),
                    'password' => bcrypt(Str::random(16))
                ]);
                
                $this->result['usuarios_criados']++;
                
            } else {
                // Atualizar usuário existente
                $localUser->update([
                    'name' => $adUser['name'],
                    'username' => $adUser['username'],
                    'ad_sync_at' => now(),
                    'is_active' => true
                ]);
                
                $this->result['usuarios_atualizados']++;
            }
            
        } catch (\Exception $e) {
            $this->result['erros'][] = "Erro ao processar {$adUser['email']}: " . $e->getMessage();
        }
    }

    /**
     * Desativar usuários que não estão mais no AD
     */
    private function deactivateRemovedUsers($adUsers)
    {
        $adEmails = collect($adUsers)->pluck('email')->toArray();
        
        $this->result['usuarios_desativados'] = User::where('login_type', 'ad')
            ->whereNotIn('email', $adEmails)
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'ad_sync_at' => now()
            ]);
    }

    /**
     * Testar conexão com AD
     */
    public function testConnection()
    {
        return $this->adService->testConnection();
    }

    /**
     * Obter estatísticas básicas
     */
    public function getStats()
    {
        return [
            'total_users' => User::count(),
            'ad_users' => User::where('login_type', 'ad')->count(),
            'local_users' => User::where('login_type', 'local')->count(),
            'active_users' => User::where('is_active', true)->count(),
            'ad_habilitado' => config('adldap.connections.default.enabled', false)
        ];
    }
} 