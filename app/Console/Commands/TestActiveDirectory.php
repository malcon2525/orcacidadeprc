<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ActiveDirectoryService;

class TestActiveDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ad:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testar conexão com Active Directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testando conexão com Active Directory...');
        
        try {
            // Testar configurações
            $this->info('📋 Verificando configurações...');
            $this->info('AD_ENABLED: ' . (config('adldap.connections.default.enabled') ? '✅ SIM' : '❌ NÃO'));
            $this->info('AD_HOST: ' . config('adldap.connections.default.settings.hosts.0'));
            $this->info('AD_PORT: ' . config('adldap.connections.default.settings.port'));
            $this->info('AD_BASE_DN: ' . config('adldap.connections.default.settings.base_dn'));
            $this->info('AD_USERNAME: ' . (config('adldap.connections.default.settings.username') ? '✅ CONFIGURADO' : '❌ NÃO CONFIGURADO'));
            $this->info('AD_PASSWORD: ' . (config('adldap.connections.default.settings.password') ? '✅ CONFIGURADO' : '❌ NÃO CONFIGURADO'));
            
            $this->newLine();
            
            // Testar conexão
            $this->info('🔌 Testando conexão...');
            $adService = new ActiveDirectoryService();
            $result = $adService->testConnection();
            
            if ($result['success']) {
                $this->info('✅ ' . $result['message']);
                $this->info('👥 Usuários encontrados: ' . $result['users_count']);
                $this->info('🖥️  Servidor: ' . $result['server_info']['host'] . ':' . $result['server_info']['port']);
                $this->info('📁 Base DN: ' . $result['server_info']['base_dn']);
            } else {
                $this->error('❌ ' . $result['message']);
            }
            
        } catch (\Exception $e) {
            $this->error('💥 Erro: ' . $e->getMessage());
            $this->error('📍 Arquivo: ' . $e->getFile() . ':' . $e->getLine());
            
            if (config('app.debug')) {
                $this->error('🔍 Stack trace:');
                $this->error($e->getTraceAsString());
            }
        }
        
        return 0;
    }
}
