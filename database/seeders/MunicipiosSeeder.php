<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Administracao\Role;
use App\Models\Administracao\Permission;

class MunicipiosSeeder extends Seeder
{
    /**
     * Execute the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏙️ Iniciando setup do módulo de municípios...');
        
        // 1. CRIAR PERMISSÕES
        $this->criarPermissoes();
        
        // 2. CRIAR PAPÉIS
        $this->criarPapeis();
        
        // 3. VINCULAR PERMISSÕES AOS PAPÉIS
        $this->vincularPermissoes();
        
        $this->command->info('✅ Setup do módulo de municípios concluído com sucesso!');
    }
    
    /**
     * Criar todas as permissões necessárias para municípios
     */
    private function criarPermissoes(): void
    {
        $this->command->info('🔑 Criando permissões para municípios...');
        
        $permissoes = [
            [
                'name' => 'municipio_crud',
                'display_name' => 'Gerenciar Municípios (CRUD)',
                'description' => 'Permite criar, editar, excluir e visualizar municípios',
                'is_active' => true
            ],
            [
                'name' => 'municipio_consultar',
                'display_name' => 'Consultar Municípios',
                'description' => 'Permite apenas visualizar municípios (sem ações de edição)',
                'is_active' => true
            ],
            [
                'name' => 'municipio_importar',
                'display_name' => 'Importar Municípios',
                'description' => 'Permite importar municípios do banco PostgreSQL externo',
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
        
        $this->command->info('✅ ' . count($permissoes) . ' permissões para municípios verificadas/criadas!');
    }
    
    /**
     * Criar todos os papéis necessários para municípios
     */
    private function criarPapeis(): void
    {
        $this->command->info('👥 Criando papéis para municípios...');
        
        $papeis = [
            [
                'name' => 'gerenciar_municipios',
                'display_name' => 'Gerenciador de Municípios',
                'description' => 'Pode gerenciar municípios (CRUD completo) e importar dados',
                'is_active' => true
            ],
            [
                'name' => 'visualizar_municipios',
                'display_name' => 'Visualizador de Municípios',
                'description' => 'Pode apenas visualizar municípios (sem ações de edição)',
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
        
        $this->command->info('✅ ' . count($papeis) . ' papéis para municípios verificados/criados!');
    }
    
    /**
     * Vincular permissões aos papéis
     */
    private function vincularPermissoes(): void
    {
        $this->command->info('🔗 Vinculando permissões aos papéis...');
        
        // PAPEL: gerenciar_municipios (CRUD completo + importação)
        $papelGerenciar = Role::where('name', 'gerenciar_municipios')->first();
        if ($papelGerenciar) {
            $permissoesGerenciar = Permission::whereIn('name', [
                'municipio_crud', 'municipio_consultar', 'municipio_importar'
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
        
        // PAPEL: visualizar_municipios (apenas consulta)
        $papelVisualizar = Role::where('name', 'visualizar_municipios')->first();
        if ($papelVisualizar) {
            $permissoesVisualizar = Permission::whereIn('name', [
                'municipio_consultar'
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
        $this->command->info('📊 RESUMO DAS VINCULAÇÕES PARA MUNICÍPIOS:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        // Papel: gerenciar_municipios
        $papelGerenciar = Role::where('name', 'gerenciar_municipios')->first();
        if ($papelGerenciar) {
            $permissoesGerenciar = $papelGerenciar->permissions;
            $this->command->info("🔑 PAPEL: {$papelGerenciar->display_name}");
            foreach ($permissoesGerenciar as $permissao) {
                $this->command->info("   • {$permissao->display_name}");
            }
        }
        
        // Papel: visualizar_municipios
        $papelVisualizar = Role::where('name', 'visualizar_municipios')->first();
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
