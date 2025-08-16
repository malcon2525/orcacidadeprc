<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ActiveDirectoryService;
use Illuminate\Support\Facades\Log;

class TestAdAuthentication extends Command
{
    protected $signature = 'ad:test-auth {email} {password}';
    protected $description = 'Testa autenticação de usuário no AD';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        $this->info("=== TESTE DE AUTENTICAÇÃO AD ===");
        $this->line("Email: {$email}");
        $this->line("AD Habilitado: " . (config('adldap.connections.default.enabled') ? 'Sim' : 'Não'));
        $this->line("AD Host: " . config('adldap.connections.default.settings.hosts.0'));
        $this->line("AD Base DN: " . config('adldap.connections.default.settings.base_dn'));
        
        try {
            $adService = new ActiveDirectoryService();
            
            $this->line("");
            $this->info("1. Conectando ao AD...");
            $connected = $adService->connect();
            $this->line("Conexão: " . ($connected ? '✅ Sucesso' : '❌ Falha'));
            
            if (!$connected) {
                return 1;
            }
            
            $this->line("");
            $this->info("2. Buscando usuário por email...");
            $adUser = $adService->findUserByEmail($email);
            
            if (!$adUser) {
                $this->error("❌ Usuário não encontrado no AD");
                return 1;
            }
            
            $this->line("✅ Usuário encontrado:");
            $this->line("   - Nome: {$adUser['name']}");
            $this->line("   - Username: {$adUser['username']}");
            $this->line("   - Email: {$adUser['email']}");
            
            $this->line("");
            $this->info("3. Testando autenticação...");
            $authenticated = $adService->authenticateUser($adUser['username'], $password);
            
            $this->line("Resultado: " . ($authenticated ? '✅ AUTENTICADO' : '❌ FALHA NA AUTENTICAÇÃO'));
            
            if (!$authenticated) {
                $this->warn("Possíveis causas:");
                $this->line("- Senha incorreta");
                $this->line("- Usuário bloqueado no AD");
                $this->line("- Problema de conectividade");
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Erro: " . $e->getMessage());
            return 1;
        }
    }
} 