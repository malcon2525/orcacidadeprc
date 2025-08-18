<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Administracao\Role;
use App\Models\Administracao\Permission;
use App\Models\Administracao\User;
use Illuminate\Support\Facades\DB;

class CheckSuperRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:super-role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar se o papel super possui permissões e usuários';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando papel Super Administrador...');
        $this->newLine();

        // Verificar se o papel existe
        $papel = Role::where('name', 'super')->first();
        
        if (!$papel) {
            $this->error('❌ Papel "super" não encontrado!');
            return 1;
        }

        $this->info("✅ Papel encontrado: {$papel->display_name} (ID: {$papel->id})");
        $this->newLine();

        // Verificar permissões
        $this->info('📋 Verificando permissões...');
        $permissoes = $papel->permissions;
        $this->line("Total de permissões: {$permissoes->count()}");
        
        if ($permissoes->count() > 0) {
            foreach ($permissoes as $permissao) {
                $this->line("  ✅ {$permissao->display_name} ({$permissao->name})");
            }
        } else {
            $this->warn("  ⚠️  Nenhuma permissão encontrada!");
        }

        $this->newLine();

        // Verificar usuários
        $this->info('👥 Verificando usuários...');
        $usuarios = $papel->users;
        $this->line("Total de usuários: {$usuarios->count()}");
        
        if ($usuarios->count() > 0) {
            foreach ($usuarios as $usuario) {
                $this->line("  ✅ {$usuario->name} ({$usuario->email})");
            }
        } else {
            $this->warn("  ⚠️  Nenhum usuário encontrado!");
        }

        $this->newLine();

        // Verificar tabelas de relacionamento
        $this->info('🔗 Verificando tabelas de relacionamento...');
        
        $rolePermissionsCount = DB::table('role_permissions')->where('role_id', $papel->id)->count();
        $this->line("Registros em role_permissions: {$rolePermissionsCount}");
        
        $userRolesCount = DB::table('user_roles')->where('role_id', $papel->id)->count();
        $this->line("Registros em user_roles: {$userRolesCount}");

        $this->newLine();

        // Verificar se há discrepância
        if ($permissoes->count() !== $rolePermissionsCount) {
            $this->error("❌ DISCREPÂNCIA: Eloquent mostra {$permissoes->count()} permissões, mas tabela tem {$rolePermissionsCount}");
        } else {
            $this->info("✅ Contadores de permissões estão sincronizados");
        }

        if ($usuarios->count() !== $userRolesCount) {
            $this->error("❌ DISCREPÂNCIA: Eloquent mostra {$usuarios->count()} usuários, mas tabela tem {$userRolesCount}");
        } else {
            $this->info("✅ Contadores de usuários estão sincronizados");
        }

        $this->newLine();
        $this->info('✅ Verificação concluída!');
        
        return 0;
    }
}
