<?php

namespace Database\Seeders\Iniciais\Administracao;

use Illuminate\Database\Seeder;
use App\Models\Administracao\Permission;
use App\Models\Administracao\Role;

class UsuariosEntidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeder Usuários e Entidades...');

        // ===================================================================
        // 1. CRIAR PERMISSÕES
        // ===================================================================
        $this->command->info('📋 Criando permissões...');
        
        $permissoes = [
            [
                'name' => 'aprovar_cadastros',
                'display_name' => 'Aprovar Cadastros de Usuários',
                'description' => 'Permite aprovar/rejeitar solicitações de cadastro de novos usuários',
                'is_active' => true
            ],
            [
                'name' => 'gerenciar_vinculos_usuarios',
                'display_name' => 'Gerenciar Vínculos de Usuários',
                'description' => 'Permite vincular e desvincular usuários de entidades orçamentárias',
                'is_active' => true
            ],
            [
                'name' => 'visualizar_cadastros_usuarios',
                'display_name' => 'Visualizar Cadastros e Usuários',
                'description' => 'Permite visualizar solicitações de cadastro e vínculos de usuários (somente leitura)',
                'is_active' => true
            ]
        ];

        $permissoesCriadas = [];
        foreach ($permissoes as $permissaoData) {
            // Verificar se a permissão já existe
            $permissaoExistente = Permission::where('name', $permissaoData['name'])->first();
            
            if ($permissaoExistente) {
                $this->command->info("⚠️  Permissão já existe: {$permissaoExistente->display_name}");
                $permissoesCriadas[] = $permissaoExistente;
            } else {
                $permissao = Permission::create($permissaoData);
                $permissoesCriadas[] = $permissao;
                $this->command->info("✅ Permissão criada: {$permissao->display_name}");
            }
        }

        // ===================================================================
        // 2. CRIAR PAPÉIS
        // ===================================================================
        $this->command->info('🎭 Criando papéis...');
        
        $papeis = [
            [
                'name' => 'gerenciar_usuarios_entidades',
                'display_name' => 'Gerenciar Usuários e Entidades',
                'description' => 'Pode aprovar cadastros, vincular usuários e gerenciar todos os aspectos de usuários e entidades orçamentárias',
                'is_active' => true
            ],
            [
                'name' => 'visualizar_usuarios_entidades',
                'display_name' => 'Visualizar Usuários e Entidades',
                'description' => 'Pode apenas consultar cadastros e vínculos entre usuários e entidades (somente leitura)',
                'is_active' => true
            ]
        ];

        $papeisCriados = [];
        foreach ($papeis as $papelData) {
            // Verificar se o papel já existe
            $papelExistente = Role::where('name', $papelData['name'])->first();
            
            if ($papelExistente) {
                $this->command->info("⚠️  Papel já existe: {$papelExistente->display_name}");
                $papeisCriados[] = $papelExistente;
            } else {
                $papel = Role::create($papelData);
                $papeisCriados[] = $papel;
                $this->command->info("✅ Papel criado: {$papel->display_name}");
            }
        }

        // ===================================================================
        // 3. ASSOCIAR PERMISSÕES AOS PAPÉIS
        // ===================================================================
        $this->command->info('🔗 Associando permissões aos papéis...');
        
        // PAPEL 1: gerenciar_usuarios_entidades
        $gerenciarPapel = $papeisCriados[0];
        
        // Limpar associações existentes para recriar
        $gerenciarPapel->permissions()->detach();
        
        // Associar todas as 3 permissões
        foreach ($permissoesCriadas as $permissao) {
            $gerenciarPapel->permissions()->attach($permissao->id);
        }
        
        $this->command->info("✅ Permissões associadas ao papel: {$gerenciarPapel->display_name}");
        $this->command->info("   • aprovar_cadastros");
        $this->command->info("   • gerenciar_vinculos_usuarios");
        $this->command->info("   • visualizar_cadastros_usuarios");

        // PAPEL 2: visualizar_usuarios_entidades
        $visualizarPapel = $papeisCriados[1];
        
        // Limpar associações existentes para recriar
        $visualizarPapel->permissions()->detach();
        
        // Associar apenas a permissão de visualizar
        $visualizarPapel->permissions()->attach($permissoesCriadas[2]->id); // visualizar_cadastros_usuarios
        
        $this->command->info("✅ Permissões associadas ao papel: {$visualizarPapel->display_name}");
        $this->command->info("   • visualizar_cadastros_usuarios");

        // ===================================================================
        // 4. RESUMO FINAL
        // ===================================================================
        $this->command->info('');
        $this->command->info('🎉 SEEDER EXECUTADO COM SUCESSO!');
        $this->command->info('');
        $this->command->info('📊 RESUMO DA ESTRUTURA CRIADA:');
        $this->command->info('');
        
        $this->command->info('🎭 PAPÉIS:');
        $this->command->info('  1️⃣  gerenciar_usuarios_entidades');
        $this->command->info('      → Gerenciar Usuários e Entidades');
        $this->command->info('      → Pode: aprovar cadastros + vincular usuários + visualizar tudo');
        $this->command->info('');
        $this->command->info('  2️⃣  visualizar_usuarios_entidades');
        $this->command->info('      → Visualizar Usuários e Entidades');
        $this->command->info('      → Pode: apenas visualizar (somente leitura)');
        $this->command->info('');
        
        $this->command->info('🔐 PERMISSÕES:');
        $this->command->info('  🔹 aprovar_cadastros');
        $this->command->info('  🔹 gerenciar_vinculos_usuarios');
        $this->command->info('  🔹 visualizar_cadastros_usuarios');
        $this->command->info('');
        
        $this->command->info('🔗 ASSOCIAÇÕES:');
        $this->command->info('  • GERENCIAR → TODAS as permissões');
        $this->command->info('  • VISUALIZAR → Apenas visualizar_cadastros_usuarios');
        $this->command->info('');
        
        $this->command->info('📝 PRÓXIMOS PASSOS:');
        $this->command->info('  1. Atualizar controllers para usar as novas permissões');
        $this->command->info('  2. Atualizar views/menus para usar os novos papéis');
        $this->command->info('  3. Testar as funcionalidades com usuários que tenham esses papéis');
        $this->command->info('');
        $this->command->info('✅ Estrutura pronta para uso!');
    }
}