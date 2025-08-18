<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SetupAdminData extends Command
{
    protected $signature = 'setup:admin-data';
    protected $description = 'Configurar dados administrativos básicos';

    public function handle()
    {
        $this->info('🚀 Configurando dados administrativos...');
        
        try {
            // 1. Criar permissões
            $this->createPermissions();
            
            // 2. Criar papéis
            $this->createRoles();
            
            // 3. Criar usuário admin
            $this->createAdminUser();
            
            // 4. Atribuir relacionamentos
            $this->assignRelationships();
            
            $this->info('✅ Dados administrativos configurados com sucesso!');
            
        } catch (\Exception $e) {
            $this->error('❌ Erro: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }

    private function createPermissions()
    {
        $this->info('📋 Criando permissões...');
        
        $permissions = [
            ['name' => 'gerenciar_usuarios', 'display_name' => 'Gerenciar Usuários', 'description' => 'Criar, editar e excluir usuários'],
            ['name' => 'visualizar_usuarios', 'display_name' => 'Visualizar Usuários', 'description' => 'Visualizar lista de usuários'],
            ['name' => 'gerenciar_papeis', 'display_name' => 'Gerenciar Papéis', 'description' => 'Criar, editar e excluir papéis'],
            ['name' => 'visualizar_papeis', 'display_name' => 'Visualizar Papéis', 'description' => 'Visualizar lista de papéis'],
            ['name' => 'gerenciar_permissoes', 'display_name' => 'Gerenciar Permissões', 'description' => 'Criar, editar e excluir permissões'],
            ['name' => 'visualizar_permissoes', 'display_name' => 'Visualizar Permissões', 'description' => 'Visualizar lista de permissões'],
            ['name' => 'gerenciar_active_directory', 'display_name' => 'Gerenciar Active Directory', 'description' => 'Configurar e sincronizar AD'],
            ['name' => 'visualizar_active_directory', 'display_name' => 'Visualizar Active Directory', 'description' => 'Visualizar configurações do AD'],
            ['name' => 'acessar_admin', 'display_name' => 'Acessar Administração', 'description' => 'Acesso ao painel administrativo']
        ];

        foreach ($permissions as $permission) {
            if (!DB::table('permissions')->where('name', $permission['name'])->exists()) {
                DB::table('permissions')->insert([
                    'name' => $permission['name'],
                    'display_name' => $permission['display_name'],
                    'description' => $permission['description'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $this->line("  ✅ {$permission['display_name']}");
            }
        }
    }

    private function createRoles()
    {
        $this->info('👑 Criando papéis...');
        
        $roles = [
            ['name' => 'super', 'display_name' => 'Super Administrador', 'description' => 'Acesso total ao sistema'],
            ['name' => 'admin', 'display_name' => 'Administrador', 'description' => 'Administrador do sistema'],
            ['name' => 'user', 'display_name' => 'Usuário', 'description' => 'Usuário padrão']
        ];

        foreach ($roles as $role) {
            if (!DB::table('roles')->where('name', $role['name'])->exists()) {
                DB::table('roles')->insert([
                    'name' => $role['name'],
                    'display_name' => $role['display_name'],
                    'description' => $role['description'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $this->line("  ✅ {$role['display_name']}");
            }
        }
    }

    private function createAdminUser()
    {
        $this->info('👤 Criando usuário administrador...');
        
        if (!DB::table('users')->where('email', 'adm@adm.com.br')->exists()) {
            DB::table('users')->insert([
                'name' => 'Administrador',
                'email' => 'adm@adm.com.br',
                'username' => 'adm',
                'password' => Hash::make('@octrab#'),
                'is_active' => true,
                'login_type' => 'local',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->line("  ✅ Usuário admin criado");
        } else {
            $this->line("  ℹ️  Usuário admin já existe");
        }
    }

    private function assignRelationships()
    {
        $this->info('🔗 Atribuindo relacionamentos...');
        
        // Pegar IDs
        $superRoleId = DB::table('roles')->where('name', 'super')->value('id');
        $adminUserId = DB::table('users')->where('email', 'adm@adm.com.br')->value('id');
        $permissionIds = DB::table('permissions')->pluck('id');
        
        if (!$superRoleId || !$adminUserId) {
            throw new \Exception('Papel super ou usuário admin não encontrado');
        }
        
        // Atribuir papel ao usuário
        if (!DB::table('user_roles')->where('user_id', $adminUserId)->where('role_id', $superRoleId)->exists()) {
            DB::table('user_roles')->insert([
                'user_id' => $adminUserId,
                'role_id' => $superRoleId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->line("  ✅ Papel 'super' atribuído ao usuário admin");
        }
        
        // Atribuir todas as permissões ao papel super
        foreach ($permissionIds as $permissionId) {
            if (!DB::table('role_permissions')->where('role_id', $superRoleId)->where('permission_id', $permissionId)->exists()) {
                DB::table('role_permissions')->insert([
                    'role_id' => $superRoleId,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        $this->line("  ✅ {$permissionIds->count()} permissões atribuídas ao papel 'super'");
    }
}
