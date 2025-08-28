<?php

namespace Database\Seeders\Iniciais\TabelasOficiais;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Seeder para criar as permissões e papéis necessários para a funcionalidade
 * "Consultar DER-PR" do módulo "Tabela Oficial"
 */
class ConsultarDerprSeeder extends Seeder
{
    /**
     * Executa o seeder
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            // 1. Criar permissão
            $this->criarPermissao();

            // 2. Criar papel
            $this->criarPapel();

            // 3. Associar permissão ao papel
            $this->associarPermissaoAoPapel();

            DB::commit();

            Log::info('Seeder ConsultarDerprSeeder executado com sucesso');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao executar ConsultarDerprSeeder:', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Cria a permissão para consultar DER-PR
     */
    private function criarPermissao(): void
    {
        $permissao = DB::table('permissions')->where('name', 'consultar_derpr')->first();

        if (!$permissao) {
            $permissaoId = DB::table('permissions')->insertGetId([
                'name' => 'consultar_derpr',
                'display_name' => 'Consultar DER-PR',
                'description' => 'Permite consultar tabelas DER-PR',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Permissão consultar_derpr criada com ID: ' . $permissaoId);
        } else {
            Log::info('Permissão consultar_derpr já existe com ID: ' . $permissao->id);
        }
    }

    /**
     * Cria o papel para consultar tabelas DER-PR
     */
    private function criarPapel(): void
    {
        $papel = DB::table('roles')->where('name', 'consultar_tabela_derpr')->first();

        if (!$papel) {
            $papelId = DB::table('roles')->insertGetId([
                'name' => 'consultar_tabela_derpr',
                'display_name' => 'Consultar Tabela DER-PR',
                'description' => 'Papel para usuários que podem consultar tabelas DER-PR',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Papel consultar_tabela_derpr criado com ID: ' . $papelId);
        } else {
            Log::info('Papel consultar_tabela_derpr já existe com ID: ' . $papel->id);
        }
    }

    /**
     * Associa a permissão ao papel
     */
    private function associarPermissaoAoPapel(): void
    {
        $papel = DB::table('roles')->where('name', 'consultar_tabela_derpr')->first();
        $permissao = DB::table('permissions')->where('name', 'consultar_derpr')->first();

        if ($papel && $permissao) {
            // Verifica se já existe a associação
            $existeAssociacao = DB::table('role_permissions')
                ->where('role_id', $papel->id)
                ->where('permission_id', $permissao->id)
                ->exists();

            if (!$existeAssociacao) {
                DB::table('role_permissions')->insert([
                    'role_id' => $papel->id,
                    'permission_id' => $permissao->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                Log::info('Permissão consultar_derpr associada ao papel consultar_tabela_derpr');
            } else {
                Log::info('Associação entre permissão e papel já existe');
            }
        } else {
            Log::warning('Não foi possível associar permissão ao papel - papel ou permissão não encontrados');
        }
    }
}
