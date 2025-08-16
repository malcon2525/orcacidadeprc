<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Administracao\Permission;

class TestPermissionsData extends Command
{
    protected $signature = 'test:permissions-data';
    protected $description = 'Testa se os dados das permissões estão sendo carregados corretamente';

    public function handle()
    {
        $this->info("=== TESTE DE DADOS DE PERMISSÕES ===");
        
        try {
            $permission = Permission::with(['roles.users' => function($query) {
                $query->select('users.id', 'users.name', 'users.email');
            }])->first();
            
            if (!$permission) {
                $this->error("Nenhuma permissão encontrada!");
                return 1;
            }
            
            $this->line("Permissão: {$permission->display_name}");
            $this->line("Papéis: {$permission->roles->count()}");
            
            foreach ($permission->roles as $role) {
                $this->line("");
                $this->line("  Papel: {$role->display_name}");
                $this->line("  Usuários: {$role->users->count()}");
                
                foreach ($role->users as $user) {
                    $this->line("    - {$user->name} ({$user->email})");
                }
            }
            
            $this->info("✅ Dados carregados com sucesso!");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Erro: " . $e->getMessage());
            return 1;
        }
    }
} 