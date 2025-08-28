<?php

namespace Database\Seeders\Iniciais\Autenticacao;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Administracao\User;
use App\Models\Administracao\Role;
use App\Models\Administracao\Permission;

class SetupUsuariosPermissoesSeeder extends Seeder
{
    /**
     * Execute the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando setup do módulo de usuários e permissões...');
        
        // 1. LIMPEZA COMPLETA DAS TABELAS
        $this->limparTabelas();
        
        // 2. CRIAR PERMISSÕES
        $this->criarPermissoes();
        
        // 3. CRIAR PAPÉIS
        $this->criarPapeis();
        
        // 4. CRIAR USUÁRIO SUPER
        $this->criarUsuarioSuper();
        
        // 5. VINCULAR PERMISSÕES AOS PAPÉIS
        $this->vincularPermissoes();
        
        $this->command->info('✅ Setup do módulo de usuários e permissões concluído com sucesso!');
    }
    
    /**
     * Limpar todas as tabelas envolvidas com o módulo
     */
    private function limparTabelas(): void
    {
        $this->command->info('🧹 Limpando tabelas...');
        
        // Desabilitar verificação de chaves estrangeiras
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Limpar tabelas na ordem correta (evitar problemas de FK)
        DB::table('role_permissions')->truncate();
        DB::table('user_roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('users')->truncate();
        
        // Reabilitar verificação de chaves estrangeiras
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('✅ Tabelas limpas com sucesso!');
    }
    
    /**
     * Criar todas as permissões necessárias
     */
    private function criarPermissoes(): void
    {
        $this->command->info('🔑 Criando permissões...');
        
        $permissoes = [
            [
                'name' => 'usuario_crud',
                'display_name' => 'Gerenciar Usuários (CRUD)',
                'description' => 'Permite criar, editar, excluir e visualizar usuários',
                'is_active' => true
            ],
            [
                'name' => 'usuario_consultar',
                'display_name' => 'Consultar Usuários',
                'description' => 'Permite apenas visualizar usuários (sem ações de edição)',
                'is_active' => true
            ],
            [
                'name' => 'papel_crud',
                'display_name' => 'Gerenciar Papéis (CRUD)',
                'description' => 'Permite criar, editar, excluir e visualizar papéis',
                'is_active' => true
            ],
            [
                'name' => 'papel_consultar',
                'display_name' => 'Consultar Papéis',
                'description' => 'Permite apenas visualizar papéis (sem ações de edição)',
                'is_active' => true
            ],
            [
                'name' => 'permissao_crud',
                'display_name' => 'Gerenciar Permissões (CRUD)',
                'description' => 'Permite criar, editar, excluir e visualizar permissões',
                'is_active' => true
            ],
            [
                'name' => 'permissao_consultar',
                'display_name' => 'Consultar Permissões',
                'description' => 'Permite apenas visualizar permissões (sem ações de edição)',
                'is_active' => true
            ]
        ];
        
        foreach ($permissoes as $permissao) {
            Permission::create($permissao);
        }
        
        $this->command->info('✅ ' . count($permissoes) . ' permissões criadas com sucesso!');
    }
    
    /**
     * Criar todos os papéis necessários
     */
    private function criarPapeis(): void
    {
        $this->command->info('👥 Criando papéis...');
        
        $papeis = [
            [
                'name' => 'super',
                'display_name' => 'Super Administrador',
                'description' => 'Acesso total ao sistema - bypassa todas as verificações de permissão',
                'is_active' => true
            ],
            [
                'name' => 'gerenciar_usuarios',
                'display_name' => 'Gerenciador de Usuários',
                'description' => 'Pode gerenciar usuários, papéis e permissões (CRUD completo)',
                'is_active' => true
            ],
            [
                'name' => 'visualizar_usuarios',
                'display_name' => 'Visualizador de Usuários',
                'description' => 'Pode apenas visualizar usuários, papéis e permissões (sem edição)',
                'is_active' => true
            ]
        ];
        
        foreach ($papeis as $papel) {
            Role::create($papel);
        }
        
        $this->command->info('✅ ' . count($papeis) . ' papéis criados com sucesso!');
    }
    
    /**
     * Criar usuário super administrador
     */
    private function criarUsuarioSuper(): void
    {
        $this->command->info('👑 Criando usuário super administrador...');
        
        $usuario = User::create([
            'name' => 'Super Administrador',
            'email' => 'adm@adm.com.br',
            'username' => 'adm',
            'password' => Hash::make('@octrab#'),
            'is_active' => true,
            'login_type' => 'local',
            'email_verified_at' => now()
        ]);
        
        // Atribuir papel 'super' ao usuário
        $papelSuper = Role::where('name', 'super')->first();
        $usuario->roles()->attach($papelSuper->id);
        
        $this->command->info('✅ Usuário super administrador criado com sucesso!');
        $this->command->info('📧 Email: adm@adm.com.br');
        $this->command->info('🔑 Senha: @octrab#');
    }
    
    /**
     * Vincular permissões aos papéis
     */
    private function vincularPermissoes(): void
    {
        $this->command->info('🔗 Vinculando permissões aos papéis...');
        
        // PAPEL: gerenciar_usuarios (CRUD completo)
        $papelGerenciar = Role::where('name', 'gerenciar_usuarios')->first();
        $permissoesGerenciar = Permission::whereIn('name', [
            'usuario_crud', 'usuario_consultar',
            'papel_crud', 'papel_consultar',
            'permissao_crud', 'permissao_consultar'
        ])->get();
        
        $papelGerenciar->permissions()->attach($permissoesGerenciar->pluck('id'));
        
        // PAPEL: visualizar_usuarios (apenas consulta)
        $papelVisualizar = Role::where('name', 'visualizar_usuarios')->first();
        $permissoesVisualizar = Permission::whereIn('name', [
            'usuario_consultar', 'papel_consultar', 'permissao_consultar'
        ])->get();
        
        $papelVisualizar->permissions()->attach($permissoesVisualizar->pluck('id'));
        
        $this->command->info('✅ Permissões vinculadas aos papéis com sucesso!');
        
        // Mostrar resumo das vinculações
        $this->mostrarResumoVinculacoes();
    }
    
    /**
     * Mostrar resumo das vinculações criadas
     */
    private function mostrarResumoVinculacoes(): void
    {
        $this->command->info('📊 RESUMO DAS VINCULAÇÕES:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        // Papel: super
        $papelSuper = Role::where('name', 'super')->first();
        $this->command->info("👑 PAPEL: {$papelSuper->display_name}");
        $this->command->info("   • Acesso total (bypassa verificações)");
        
        // Papel: gerenciar_usuarios
        $papelGerenciar = Role::where('name', 'gerenciar_usuarios')->first();
        $permissoesGerenciar = $papelGerenciar->permissions;
        $this->command->info("🔑 PAPEL: {$papelGerenciar->display_name}");
        foreach ($permissoesGerenciar as $permissao) {
            $this->command->info("   • {$permissao->display_name}");
        }
        
        // Papel: visualizar_usuarios
        $papelVisualizar = Role::where('name', 'visualizar_usuarios')->first();
        $permissoesVisualizar = $papelVisualizar->permissions;
        $this->command->info("👁️ PAPEL: {$papelVisualizar->display_name}");
        foreach ($permissoesVisualizar as $permissao) {
            $this->command->info("   • {$permissao->display_name}");
        }
        
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
