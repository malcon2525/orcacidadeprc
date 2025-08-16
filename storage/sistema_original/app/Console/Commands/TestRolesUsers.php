<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Administracao\Role;

class TestRolesUsers extends Command
{
    protected $signature = 'test:roles-users';
    protected $description = 'Testa se os papéis têm usuários associados';

    public function handle()
    {
        $this->info("=== TESTE DE USUÁRIOS NOS PAPÉIS ===");
        
        try {
            $roles = Role::with('users')->get();
            
            if ($roles->isEmpty()) {
                $this->error("Nenhum papel encontrado!");
                return 1;
            }
            
            foreach ($roles as $role) {
                $this->line("");
                $this->line("Papel: {$role->display_name}");
                $this->line("Usuários: {$role->users->count()}");
                
                if ($role->users->count() > 0) {
                    foreach ($role->users as $user) {
                        $this->line("  - {$user->name} ({$user->email})");
                    }
                } else {
                    $this->line("  - Nenhum usuário associado");
                }
            }
            
            $this->info("✅ Verificação concluída!");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Erro: " . $e->getMessage());
            return 1;
        }
    }
} 