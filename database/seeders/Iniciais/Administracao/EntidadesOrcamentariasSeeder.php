<?php

namespace Database\Seeders\Iniciais\Administracao;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Administracao\Role;
use App\Models\Administracao\Permission;

class EntidadesOrcamentariasSeeder extends Seeder
{
    /**
     * Execute the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏢 Iniciando setup do módulo de entidades orçamentárias...');
        
        // 1. CRIAR PERMISSÕES
        $this->criarPermissoes();
        
        // 2. CRIAR PAPÉIS
        $this->criarPapeis();
        
        // 3. VINCULAR PERMISSÕES AOS PAPÉIS
        $this->vincularPermissoes();
        
        $this->command->info('✅ Setup do módulo de entidades orçamentárias concluído com sucesso!');
    }
    
    /**
     * Criar todas as permissões necessárias para entidades orçamentárias
     */
    private function criarPermissoes(): void
    {
        $this->command->info('🔑 Criando permissões para entidades orçamentárias...');
        
        $permissoes = [
            [
                'name' => 'entidade_orcamentaria_crud',
                'display_name' => 'Gerenciar Entidades Orçamentárias (CRUD)',
                'description' => 'Permite criar, editar, excluir e visualizar entidades orçamentárias',
                'is_active' => true
            ],
            [
                'name' => 'entidade_orcamentaria_consultar',
                'display_name' => 'Consultar Entidades Orçamentárias',
                'description' => 'Permite apenas visualizar entidades orçamentárias (sem ações de edição)',
                'is_active' => true
            ],
            [
                'name' => 'entidade_orcamentaria_importar',
                'display_name' => 'Importar Entidades Orçamentárias',
                'description' => 'Permite importar municípios como entidades orçamentárias do banco PostgreSQL externo',
                'is_active' => true
            ]
        ];
        
        foreach ($permissoes as $permissao) {
            if (!DB::table('permissions')->where('name', $permissao['name'])->exists()) {
                Permission::create($permissao);
                $this->command->line("  ✅ {$permissao['display_name']}");
            } else {
                $this->command->line("  ℹ️  {$permissao['display_name']} já existe");
            }
        }
        
        $this->command->info('✅ ' . count($permissoes) . ' permissões para entidades orçamentárias verificadas/criadas!');
    }
    
    /**
     * Criar todos os papéis necessários para entidades orçamentárias
     */
    private function criarPapeis(): void
    {
        $this->command->info('👥 Criando papéis para entidades orçamentárias...');
        
        $papeis = [
            [
                'name' => 'gerenciar_entidade_orcamentaria',
                'display_name' => 'Gerenciador de Entidade Orçamentária',
                'description' => 'Pode gerenciar entidades orçamentárias (CRUD completo) e importar municípios',
                'is_active' => true
            ],
            [
                'name' => 'visualizar_entidade_orcamentaria',
                'display_name' => 'Visualizador de Entidade Orçamentária',
                'description' => 'Pode apenas visualizar entidades orçamentárias (sem ações de edição)',
                'is_active' => true
            ]
        ];
        
        foreach ($papeis as $papel) {
            if (!DB::table('roles')->where('name', $papel['name'])->exists()) {
                Role::create($papel);
                $this->command->line("  ✅ {$papel['display_name']}");
            } else {
                $this->command->line("  ℹ️  {$papel['display_name']} já existe");
            }
        }
        
        $this->command->info('✅ ' . count($papeis) . ' papéis para entidades orçamentárias verificados/criados!');
    }
    
    /**
     * Vincular permissões aos papéis
     */
    private function vincularPermissoes(): void
    {
        $this->command->info('🔗 Vinculando permissões aos papéis...');
        
        // PAPEL: gerenciar_entidade_orcamentaria (CRUD completo + importação)
        $papelGerenciar = Role::where('name', 'gerenciar_entidade_orcamentaria')->first();
        if ($papelGerenciar) {
            $permissoesGerenciar = Permission::whereIn('name', [
                'entidade_orcamentaria_crud', 'entidade_orcamentaria_consultar', 'entidade_orcamentaria_importar'
            ])->get();
            
            foreach ($permissoesGerenciar as $permissao) {
                if (!DB::table('role_permissions')->where('role_id', $papelGerenciar->id)->where('permission_id', $permissao->id)->exists()) {
                    $papelGerenciar->permissions()->attach($permissao->id);
                    $this->command->line("  ✅ {$permissao->display_name} → {$papelGerenciar->display_name}");
                } else {
                    $this->command->line("  ℹ️  {$permissao->display_name} → {$papelGerenciar->display_name} já vinculado");
                }
            }
        }
        
        // PAPEL: visualizar_entidade_orcamentaria (apenas consulta)
        $papelVisualizar = Role::where('name', 'visualizar_entidade_orcamentaria')->first();
        if ($papelVisualizar) {
            $permissoesVisualizar = Permission::whereIn('name', [
                'entidade_orcamentaria_consultar'
            ])->get();
            
            foreach ($permissoesVisualizar as $permissao) {
                if (!DB::table('role_permissions')->where('role_id', $papelVisualizar->id)->where('permission_id', $permissao->id)->exists()) {
                    $papelVisualizar->permissions()->attach($permissao->id);
                    $this->command->line("  ✅ {$permissao->display_name} → {$papelVisualizar->display_name}");
                } else {
                    $this->command->line("  ℹ️  {$permissao->display_name} → {$papelVisualizar->display_name} já vinculado");
                }
            }
        }
        
        $this->command->info('✅ Permissões vinculadas aos papéis com sucesso!');
        
        // Mostrar resumo das vinculações
        $this->mostrarResumoVinculacoes();
    }
    
    /**
     * Mostrar resumo das vinculações criadas
     */
    private function mostrarResumoVinculacoes(): void
    {
        $this->command->info('📊 RESUMO DAS VINCULAÇÕES PARA ENTIDADES ORÇAMENTÁRIAS:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        // Papel: gerenciar_entidade_orcamentaria
        $papelGerenciar = Role::where('name', 'gerenciar_entidade_orcamentaria')->first();
        if ($papelGerenciar) {
            $permissoesGerenciar = $papelGerenciar->permissions;
            $this->command->info("🔑 PAPEL: {$papelGerenciar->display_name}");
            foreach ($permissoesGerenciar as $permissao) {
                $this->command->info("   • {$permissao->display_name}");
            }
        }
        
        // Papel: visualizar_entidade_orcamentaria
        $papelVisualizar = Role::where('name', 'visualizar_entidade_orcamentaria')->first();
        if ($papelVisualizar) {
            $permissoesVisualizar = $papelVisualizar->permissions;
            $this->command->info("👁️ PAPEL: {$papelVisualizar->display_name}");
            foreach ($permissoesVisualizar as $permissao) {
                $this->command->info("   • {$permissao->display_name}");
            }
        }
        
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
