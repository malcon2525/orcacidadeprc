<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administracao\Permission;
use App\Models\Administracao\Role;

class SinapiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeder SINAPI...');

        // ===================================================================
        // 1. CRIAR PERMISSÃO
        // ===================================================================
        $this->command->info('📋 Criando permissão...');
        
        $permissao = Permission::create([
            'name' => 'importar_sinapi',
            'display_name' => 'Importar SINAPI',
            'description' => 'Acesso total ao menu de importação SINAPI',
            'is_active' => true
        ]);

        $this->command->info('✅ Permissão criada: ' . $permissao->display_name);

        // ===================================================================
        // 2. CRIAR PAPEL
        // ===================================================================
        $this->command->info('🏷️ Criando papel...');
        
        $papel = Role::create([
            'name' => 'gerenciar_importacao_sinapi',
            'display_name' => 'Gerenciar Importação SINAPI',
            'description' => 'Acesso total ao sistema SINAPI (importação e consulta)',
            'is_active' => true
        ]);

        $this->command->info('✅ Papel criado: ' . $papel->display_name);

        // ===================================================================
        // 3. ASSOCIAR PERMISSÃO AO PAPEL
        // ===================================================================
        $this->command->info('🔗 Associando permissão ao papel...');
        
        $papel->permissions()->attach($permissao->id);

        $this->command->info('✅ Permissão associada ao papel com sucesso!');

        // ===================================================================
        // 4. RESUMO FINAL
        // ===================================================================
        $this->command->info('');
        $this->command->info('🎯 SEEDER SINAPI CONCLUÍDO COM SUCESSO!');
        $this->command->info('');
        $this->command->info('📋 RESUMO:');
        $this->command->info('   • Permissão: ' . $permissao->display_name . ' (' . $permissao->name . ')');
        $this->command->info('   • Papel: ' . $papel->display_name . ' (' . $papel->name . ')');
        $this->command->info('   • Associação: ✅ Concluída');
        $this->command->info('');
        $this->command->info('🚀 Para usar:');
        $this->command->info('   • Atribua o papel "gerenciar_importacao_sinapi" ao usuário');
        $this->command->info('   • Ou use: php artisan tinker');
        $this->command->info('   • $user = App\Models\Administracao\User::find(1);');
        $this->command->info('   • $user->roles()->attach(X); // ID do papel gerenciar_importacao_sinapi');
        $this->command->info('');
    }
}
