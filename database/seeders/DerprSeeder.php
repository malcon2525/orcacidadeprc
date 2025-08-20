<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administracao\Permission;
use App\Models\Administracao\Role;

class DerprSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeder DER-PR...');

        // ===================================================================
        // 1. CRIAR PERMISSÃO
        // ===================================================================
        $this->command->info('📋 Criando permissão...');
        
        $permissao = Permission::create([
            'name' => 'importar_derpr',
            'display_name' => 'Importar DER-PR',
            'description' => 'Acesso total ao menu de importação DER-PR',
            'is_active' => true
        ]);

        $this->command->info('✅ Permissão criada: ' . $permissao->display_name);

        // ===================================================================
        // 2. CRIAR PAPEL
        // ===================================================================
        $this->command->info('🏷️ Criando papel...');
        
        $papel = Role::create([
            'name' => 'gerenciar_importacao_derpr',
            'display_name' => 'Gerenciar Importação DER-PR',
            'description' => 'Acesso total ao sistema DER-PR (importação e consulta)',
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
        $this->command->info('🎯 SEEDER DER-PR CONCLUÍDO COM SUCESSO!');
        $this->command->info('');
        $this->command->info('📋 RESUMO:');
        $this->command->info('   • Permissão: ' . $permissao->display_name . ' (' . $permissao->name . ')');
        $this->command->info('   • Papel: ' . $papel->display_name . ' (' . $papel->name . ')');
        $this->command->info('   • Associação: ✅ Concluída');
        $this->command->info('');
        $this->command->info('🚀 Para usar:');
        $this->command->info('   • Atribua o papel "gerenciar_derpr" ao usuário');
        $this->command->info('   • Ou use: php artisan tinker');
        $this->command->info('   • $user = App\Models\Administracao\User::find(1);');
        $this->command->info('   • $user->roles()->attach(2); // ID do papel gerenciar_derpr');
        $this->command->info('');
    }
}
