<?php

namespace Database\Seeders\Iniciais\Administracao;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Administracao\Role;
use App\Models\Administracao\Permission;

class EstruturaOrcamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ===== PERMISSÕES =====
        
        // Permissão para CRUD completo
        $permissaoCrud = Permission::create([
            'name' => 'estrutura_orcamento_crud',
            'display_name' => 'Gerenciar Estrutura de Orçamento',
            'description' => 'Permite criar, editar, excluir e visualizar toda a estrutura de orçamento (tipos, grandes itens e subgrupos)',
            'is_active' => true
        ]);
        
        // Permissão para consulta
        $permissaoConsultar = Permission::create([
            'name' => 'estrutura_orcamento_consultar',
            'display_name' => 'Consultar Estrutura de Orçamento',
            'description' => 'Permite visualizar e consultar a estrutura de orçamento sem fazer alterações',
            'is_active' => true
        ]);
        
        // Permissão para importação
        $permissaoImportar = Permission::create([
            'name' => 'estrutura_orcamento_importar',
            'display_name' => 'Importar Estrutura de Orçamento',
            'description' => 'Permite importar estrutura de orçamento via arquivo Excel',
            'is_active' => true
        ]);
        
        // ===== ROLES =====
        
        // Role para gerenciar estrutura de orçamento
        $roleGerenciar = Role::create([
            'name' => 'gerenciar_estrutura_orcamento',
            'display_name' => 'Gerenciar Estrutura de Orçamento',
            'description' => 'Papel que permite gerenciar completamente a estrutura de orçamento',
            'is_active' => true
        ]);
        
        // Role para visualizar estrutura de orçamento
        $roleVisualizar = Role::create([
            'name' => 'visualizar_estrutura_orcamento',
            'display_name' => 'Visualizar Estrutura de Orçamento',
            'description' => 'Pagel que permite apenas visualizar a estrutura de orçamento',
            'is_active' => true
        ]);
        
        // ===== ASSOCIAÇÕES =====
        
        // Role "gerenciar" recebe todas as permissões
        $roleGerenciar->permissions()->attach([
            $permissaoCrud->id,
            $permissaoConsultar->id,
            $permissaoImportar->id
        ]);
        
        // Role "visualizar" recebe apenas permissão de consulta
        $roleVisualizar->permissions()->attach([
            $permissaoConsultar->id
        ]);
        
        $this->command->info('✅ Seeder EstruturaOrcamentoSeeder executado com sucesso!');
        $this->command->info('📋 Permissões criadas: estrutura_orcamento_crud, estrutura_orcamento_consultar, estrutura_orcamento_importar');
        $this->command->info('👥 Roles criados: gerenciar_estrutura_orcamento, visualizar_estrutura_orcamento');
    }
}
