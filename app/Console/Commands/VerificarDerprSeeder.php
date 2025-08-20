<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Administracao\Permission;
use App\Models\Administracao\Role;

class VerificarDerprSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'derpr:verificar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica se o seeder DER-PR funcionou corretamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando seeder DER-PR...');
        $this->newLine();

        // Verificar permissão
        $permissao = Permission::where('name', 'importar_derpr')->first();
        
        if ($permissao) {
            $this->info('✅ PERMISSÃO ENCONTRADA:');
            $this->line("   ID: {$permissao->id}");
            $this->line("   Nome: {$permissao->name}");
            $this->line("   Display: {$permissao->display_name}");
            $this->line("   Descrição: {$permissao->description}");
            $this->line("   Ativa: " . ($permissao->is_active ? 'Sim' : 'Não'));
        } else {
            $this->error('❌ PERMISSÃO NÃO ENCONTRADA!');
        }

        $this->newLine();

        // Verificar papel
        $papel = Role::where('name', 'gerenciar_importacao_derpr')->first();
        
        if ($papel) {
            $this->info('✅ PAPEL ENCONTRADO:');
            $this->line("   ID: {$papel->id}");
            $this->line("   Nome: {$papel->name}");
            $this->line("   Display: {$papel->display_name}");
            $this->line("   Descrição: {$papel->description}");
            $this->line("   Ativo: " . ($papel->is_active ? 'Sim' : 'Não'));
        } else {
            $this->error('❌ PAPEL NÃO ENCONTRADO!');
        }

        $this->newLine();

        // Verificar associação
        if ($permissao && $papel) {
            $temPermissao = $papel->permissions()->where('permission_id', $permissao->id)->exists();
            
            if ($temPermissao) {
                $this->info('✅ ASSOCIAÇÃO FUNCIONANDO:');
                $this->line("   Papel '{$papel->name}' tem permissão '{$permissao->name}'");
            } else {
                $this->error('❌ ASSOCIAÇÃO NÃO FUNCIONANDO!');
                $this->line("   Papel '{$papel->name}' NÃO tem permissão '{$permissao->name}'");
            }
        }

        $this->newLine();
        
        if ($permissao && $papel && $papel->permissions()->where('permission_id', $permissao->id)->exists()) {
            $this->info('🎯 SEEDER DER-PR FUNCIONOU PERFEITAMENTE!');
        } else {
            $this->error('🚨 SEEDER DER-PR TEM PROBLEMAS!');
        }

        return 0;
    }
}
