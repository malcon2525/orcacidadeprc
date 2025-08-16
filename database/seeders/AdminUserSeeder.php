<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário administrador
        $this->createAdminUser();
        
        // Criar roles básicas
        $this->createBasicRoles();
        
        // Criar permissions básicas
        $this->createBasicPermissions();
        
        // Atribuir role ao usuário admin
        $this->assignRoleToAdmin();
        
        // Atribuir permissions ao role admin
        $this->assignPermissionsToAdminRole();
    }

    private function createAdminUser(): void
    {
        // Verificar se o usuário admin já existe
        $adminExists = DB::table('users')->where('email', 'adm@adm.com.br')->exists();
        
        if (!$adminExists) {
            DB::table('users')->insert([
                'name' => 'Administrador',
                'email' => 'adm@adm.com.br',
                'username' => 'adm',
                'password' => Hash::make('@octrab#'),
                'is_active' => true,
                'login_type' => 'local',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->command->info('Usuário administrador criado com sucesso!');
            $this->command->info('Email: adm@adm.com.br');
            $this->command->info('Senha: @octrab#');
        } else {
            $this->command->info('Usuário administrador já existe!');
        }
    }

    private function createBasicRoles(): void
    {
        $roles = [
            [
                'name' => 'super',
                'display_name' => 'Super Administrador',
                'description' => 'Acesso total ao sistema',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrador',
                'description' => 'Administrador do sistema',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user',
                'display_name' => 'Usuário',
                'description' => 'Usuário padrão',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($roles as $role) {
            if (!DB::table('roles')->where('name', $role['name'])->exists()) {
                DB::table('roles')->insert($role);
                $this->command->info("Role '{$role['name']}' criada com sucesso!");
            }
        }
    }

    private function createBasicPermissions(): void
    {
        $permissions = [
            // Permissões de usuários
            [
                'name' => 'gerenciar_usuarios',
                'display_name' => 'Gerenciar Usuários',
                'description' => 'Criar, editar e excluir usuários',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'visualizar_usuarios',
                'display_name' => 'Visualizar Usuários',
                'description' => 'Visualizar lista de usuários',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Permissões de roles
            [
                'name' => 'gerenciar_roles',
                'display_name' => 'Gerenciar Roles',
                'description' => 'Criar, editar e excluir roles',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'visualizar_roles',
                'display_name' => 'Visualizar Roles',
                'description' => 'Visualizar lista de roles',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Permissões de permissões
            [
                'name' => 'gerenciar_permissoes',
                'display_name' => 'Gerenciar Permissões',
                'description' => 'Criar, editar e excluir permissões',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'visualizar_permissoes',
                'display_name' => 'Visualizar Permissões',
                'description' => 'Visualizar lista de permissões',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Permissões de Active Directory
            [
                'name' => 'gerenciar_active_directory',
                'display_name' => 'Gerenciar Active Directory',
                'description' => 'Configurar e sincronizar AD',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'visualizar_active_directory',
                'display_name' => 'Visualizar Active Directory',
                'description' => 'Visualizar configurações do AD',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Permissões gerais
            [
                'name' => 'acessar_admin',
                'display_name' => 'Acessar Administração',
                'description' => 'Acesso ao painel administrativo',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($permissions as $permission) {
            if (!DB::table('permissions')->where('name', $permission['name'])->exists()) {
                DB::table('permissions')->insert($permission);
                $this->command->info("Permission '{$permission['name']}' criada com sucesso!");
            }
        }
    }

    private function assignRoleToAdmin(): void
    {
        $userId = DB::table('users')->where('email', 'adm@adm.com.br')->value('id');
        $roleId = DB::table('roles')->where('name', 'super')->value('id');
        
        if ($userId && $roleId) {
            if (!DB::table('user_roles')->where('user_id', $userId)->where('role_id', $roleId)->exists()) {
                DB::table('user_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info('Role "super" atribuída ao usuário admin!');
            }
        }
    }

    private function assignPermissionsToAdminRole(): void
    {
        $roleId = DB::table('roles')->where('name', 'super')->value('id');
        
        if ($roleId) {
            // Pegar todas as permissões
            $permissions = DB::table('permissions')->pluck('id');
            
            foreach ($permissions as $permissionId) {
                if (!DB::table('role_permissions')->where('role_id', $roleId)->where('permission_id', $permissionId)->exists()) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            $this->command->info('Todas as permissões atribuídas ao role "super"!');
        }
    }
}
