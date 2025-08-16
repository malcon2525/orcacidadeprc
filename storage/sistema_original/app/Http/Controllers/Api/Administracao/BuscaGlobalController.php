<?php

namespace App\Http\Controllers\Api\Administracao;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Administracao\Role;
use App\Models\Administracao\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller API para busca global no sistema de gerenciamento de usuários
 * 
 * Responsável por buscar usuários, papéis e permissões de forma unificada
 * Otimizado para sistemas com 1000+ usuários
 */
class BuscaGlobalController extends Controller
{
    /**
     * Realiza busca global em relacionamentos usuário-papel-permissão
     * 
     * @param Request $request Requisição HTTP com filtros
     * @return \Illuminate\Http\JsonResponse Resultados da tabela consolidada
     */
    public function buscar(Request $request)
    {
        try {
            $inicio = microtime(true);
            
            // Query base para relacionamentos - retornar todos os dados
            $query = DB::table('users')
                ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
                ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                ->join('role_permissions', 'roles.id', '=', 'role_permissions.role_id')
                ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                ->select([
                    'users.name as user_name',
                    'roles.display_name as role_name',
                    'permissions.display_name as permission_name'
                ])
                ->where('users.is_active', true)
                ->where('roles.is_active', true)
                ->where('permissions.is_active', true)
                ->orderBy('users.name')
                ->orderBy('roles.display_name')
                ->orderBy('permissions.display_name');

            $resultados = $query->get();
            $tempoBusca = round((microtime(true) - $inicio) * 1000, 2);

            return response()->json([
                'resultados' => $resultados,
                'tempo_busca' => $tempoBusca
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao realizar busca global',
                'mensagem' => $e->getMessage()
            ], 500);
        }
    }


} 