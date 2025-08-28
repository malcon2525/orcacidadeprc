<?php

namespace Database\Seeders\Iniciais\TabelasOficiais;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Seeder para criar as permissões e papéis necessários para a funcionalidade
 * "Consultar SINAPI-PR" do módulo "Tabela Oficial"
 */
class ConsultarSinapiSeeder extends Seeder
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

            Log::info('Seeder ConsultarSinapiSeeder executado com sucesso');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao executar ConsultarSinapiSeeder:', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Cria a permissão para consultar SINAPI
     */
    private function criarPermissao(): void
    {
        $permissao = DB::table('permissions')->where('name', 'consultar_sinapi')->first();

        if (!$permissao) {
            $permissaoId = DB::table('permissions')->insertGetId([
                'name' => 'consultar_sinapi',
                'display_name' => 'Consultar SINAPI',
                'description' => 'Permite consultar tabelas SINAPI-PR',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Permissão consultar_sinapi criada com ID: ' . $permissaoId);
        } else {
            Log::info('Permissão consultar_sinapi já existe com ID: ' . $permissao->id);
        }
    }

    /**
     * Cria o papel para consultar tabelas SINAPI
     */
    private function criarPapel(): void
    {
        $papel = DB::table('roles')->where('name', 'consultar_tabela_sinapi')->first();

        if (!$papel) {
            $papelId = DB::table('roles')->insertGetId([
                'name' => 'consultar_tabela_sinapi',
                'display_name' => 'Consultar Tabela SINAPI',
                'description' => 'Papel para usuários que podem consultar tabelas SINAPI-PR',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Papel consultar_tabela_sinapi criado com ID: ' . $papelId);
        } else {
            Log::info('Papel consultar_tabela_sinapi já existe com ID: ' . $papel->id);
        }
    }

    /**
     * Associa a permissão ao papel
     */
    private function associarPermissaoAoPapel(): void
    {
        $papel = DB::table('roles')->where('name', 'consultar_tabela_sinapi')->first();
        $permissao = DB::table('permissions')->where('name', 'consultar_sinapi')->first();

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

                Log::info('Permissão consultar_sinapi associada ao papel consultar_tabela_sinapi');
            } else {
                Log::info('Associação entre permissão e papel já existe');
            }
        } else {
            Log::warning('Não foi possível associar permissão ao papel - papel ou permissão não encontrados');
        }
    }
}
