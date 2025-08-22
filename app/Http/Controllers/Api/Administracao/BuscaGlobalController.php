<?php

namespace App\Http\Controllers\Api\Administracao;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use App\Models\Administracao\Role;
use App\Models\Administracao\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Controller API para busca global no sistema de gerenciamento de usuários
 * 
 * Responsável por buscar usuários, papéis e permissões de forma unificada
 * Permite filtrar por usuário, papel ou permissão específica
 */
class BuscaGlobalController extends Controller
{
    /**
     * Construtor com middleware de autenticação
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Sistema unificado de verificação de permissões
     * 
     * @param string|array $permissions Permissões necessárias
     * @param bool $requireAll Se true, todas as permissões são obrigatórias (AND)
     * @return bool
     */
    private function checkAccess($permissions, $requireAll = false)
    {
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        // 2. Verificação flexível de permissões
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }
        
        if ($requireAll) {
            // Todas as permissões são obrigatórias (AND)
            foreach ($permissions as $permission) {
                if (!$user->hasPermission($permission)) {
                    abort(403, "Permissão obrigatória: {$permission}");
                }
            }
        } else {
            // Pelo menos uma permissão é suficiente (OR)
            $hasAnyPermission = false;
            foreach ($permissions as $permission) {
                if ($user->hasPermission($permission)) {
                    $hasAnyPermission = true;
                    break;
                }
            }
            
            if (!$hasAnyPermission) {
                abort(403, 'Acesso negado. Permissão insuficiente.');
            }
        }
        
        return true;
    }

    /**
     * Realiza busca global otimizada em relacionamentos usuário-papel-permissão
     * 
     * @param Request $request Requisição HTTP com filtros e paginação
     * @return \Illuminate\Http\JsonResponse Resultados paginados e otimizados
     */
    public function buscar(Request $request)
    {
        // CONSULTA: verifica se tem usuario_crud OU usuario_consultar (ambos podem visualizar a aba busca global)
        $this->checkAccess(['usuario_crud', 'usuario_consultar']);
        
        try {
            $inicio = microtime(true);
            
            // Obter filtros da requisição
            $filtroUsuario = $request->get('usuario', '');
            $filtroPapel = $request->get('papel', '');
            $filtroPermissao = $request->get('permissao', '');
            
            // Configurações de paginação
            $perPage = min($request->get('per_page', 50), 100); // Máximo 100 por página
            $page = $request->get('page', 1);
            
            // Estrutura base para resultados
            $resultados = [];
            $total = 0;
            
            // 1. FILTRO POR USUÁRIO: Mostra papéis e permissões de um usuário específico
            if (!empty($filtroUsuario)) {
                list($resultados, $total) = $this->buscarPorUsuario($filtroUsuario, $perPage, $page);
            }
            // 2. FILTRO POR PAPEL: Mostra usuários que têm um papel específico
            elseif (!empty($filtroPapel)) {
                list($resultados, $total) = $this->buscarPorPapel($filtroPapel, $perPage, $page);
            }
            // 3. FILTRO POR PERMISSÃO: Mostra usuários que têm uma permissão específica
            elseif (!empty($filtroPermissao)) {
                list($resultados, $total) = $this->buscarPorPermissao($filtroPermissao, $perPage, $page);
            }
            // 4. SEM FILTROS: Mostra todos os relacionamentos (PAGINADO)
            else {
                list($resultados, $total) = $this->buscarTodosRelacionamentos($perPage, $page);
            }
            
            $tempoBusca = round((microtime(true) - $inicio) * 1000, 2);
            $totalPages = ceil($total / $perPage);

            return response()->json([
                'success' => true,
                'resultados' => $resultados,
                'paginacao' => [
                    'pagina_atual' => $page,
                    'por_pagina' => $perPage,
                    'total_registros' => $total,
                    'total_paginas' => $totalPages,
                    'tem_primeira_pagina' => $page > 1,
                    'tem_proxima_pagina' => $page < $totalPages
                ],
                'tempo_busca' => $tempoBusca,
                'filtros_aplicados' => [
                    'usuario' => $filtroUsuario,
                    'papel' => $filtroPapel,
                    'permissao' => $filtroPermissao
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'erro' => 'Erro ao realizar busca global',
                'mensagem' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Busca otimizada de papéis e permissões de um usuário específico
     * 
     * @param string $nomeUsuario Nome do usuário para buscar
     * @param int $perPage Registros por página
     * @param int $page Página atual
     * @return array [resultados, total]
     */
    private function buscarPorUsuario($nomeUsuario, $perPage = 50, $page = 1)
    {
        // Buscar usuários com eager loading otimizado
        $usuarios = User::where('name', 'like', "%{$nomeUsuario}%")
            ->where('is_active', true)
            ->with([
                'roles' => function($query) {
                    $query->where('is_active', true)
                          ->with(['permissions' => function($q) {
                              $q->where('is_active', true);
                          }]);
                }
            ])
            ->paginate($perPage, ['*'], 'page', $page);
        
        $resultados = [];
        
        foreach ($usuarios->items() as $usuario) {
            if ($usuario->roles->count() > 0) {
                foreach ($usuario->roles as $papel) {
                    if ($papel->permissions->count() > 0) {
                        foreach ($papel->permissions as $permissao) {
                            $resultados[] = [
                                'user_name' => $usuario->name,
                                'user_email' => $usuario->email,
                                'role_name' => $papel->display_name,
                                'permission_name' => $permissao->display_name,
                                'has_relationships' => true
                            ];
                        }
                    } else {
                        $resultados[] = [
                            'user_name' => $usuario->name,
                            'user_email' => $usuario->email,
                            'role_name' => $papel->display_name,
                            'permission_name' => null,
                            'has_relationships' => false
                        ];
                    }
                }
            } else {
                $resultados[] = [
                    'user_name' => $usuario->name,
                    'user_email' => $usuario->email,
                    'role_name' => null,
                    'permission_name' => null,
                    'has_relationships' => false
                ];
            }
        }
        
        return [$resultados, $usuarios->total()];
    }

    /**
     * Busca otimizada de usuários que têm um papel específico
     * 
     * @param string $nomePapel Nome do papel para buscar
     * @param int $perPage Registros por página
     * @param int $page Página atual
     * @return array [resultados, total]
     */
    private function buscarPorPapel($nomePapel, $perPage = 50, $page = 1)
    {
        // Buscar papéis com eager loading otimizado
        $papeis = Role::where('display_name', 'like', "%{$nomePapel}%")
            ->where('is_active', true)
            ->with([
                'users' => function($query) {
                    $query->where('is_active', true);
                },
                'permissions' => function($query) {
                    $query->where('is_active', true);
                }
            ])
            ->get();
        
        $resultados = [];
        $total = 0;
        
        foreach ($papeis as $papel) {
            if ($papel->users->count() > 0) {
                foreach ($papel->users as $usuario) {
                    if ($papel->permissions->count() > 0) {
                        foreach ($papel->permissions as $permissao) {
                            $resultados[] = [
                                'user_name' => $usuario->name,
                                'user_email' => $usuario->email,
                                'role_name' => $papel->display_name,
                                'permission_name' => $permissao->display_name,
                                'has_relationships' => true
                            ];
                        }
                    } else {
                        $resultados[] = [
                            'user_name' => $usuario->name,
                            'user_email' => $usuario->email,
                            'role_name' => $papel->display_name,
                            'permission_name' => null,
                            'has_relationships' => false
                        ];
                    }
                }
                $total += $papel->users->count();
            } else {
                $resultados[] = [
                    'user_name' => null,
                    'user_email' => null,
                    'role_name' => $papel->display_name,
                    'permission_name' => null,
                    'has_relationships' => false
                ];
                $total += 1;
            }
        }
        
        // Aplicar paginação manualmente
        $offset = ($page - 1) * $perPage;
        $resultadosPaginados = array_slice($resultados, $offset, $perPage);
        
        return [$resultadosPaginados, count($resultados)];
    }

    /**
     * Busca otimizada de usuários que têm uma permissão específica
     * 
     * @param string $nomePermissao Nome da permissão para buscar
     * @param int $perPage Registros por página
     * @param int $page Página atual
     * @return array [resultados, total]
     */
    private function buscarPorPermissao($nomePermissao, $perPage = 50, $page = 1)
    {
        // Buscar permissões com eager loading otimizado
        $permissoes = Permission::where('display_name', 'like', "%{$nomePermissao}%")
            ->where('is_active', true)
            ->with([
                'roles' => function($query) {
                    $query->where('is_active', true)
                          ->with(['users' => function($q) {
                              $q->where('is_active', true);
                          }]);
                }
            ])
            ->get();
        
        $resultados = [];
        $total = 0;
        
        foreach ($permissoes as $permissao) {
            foreach ($permissao->roles as $papel) {
                if ($papel->users->count() > 0) {
                    foreach ($papel->users as $usuario) {
                        $resultados[] = [
                            'user_name' => $usuario->name,
                            'user_email' => $usuario->email,
                            'role_name' => $papel->display_name,
                            'permission_name' => $permissao->display_name,
                            'has_relationships' => true
                        ];
                    }
                    $total += $papel->users->count();
                }
            }
        }
        
        // Aplicar paginação manualmente
        $offset = ($page - 1) * $perPage;
        $resultadosPaginados = array_slice($resultados, $offset, $perPage);
        
        return [$resultadosPaginados, count($resultados)];
    }

    /**
     * Busca otimizada de todos os relacionamentos existentes (PAGINADO)
     * 
     * @param int $perPage Registros por página
     * @param int $page Página atual
     * @return array [resultados, total]
     */
    private function buscarTodosRelacionamentos($perPage = 50, $page = 1)
    {
        // Buscar usuários ativos com eager loading otimizado e paginação
        $usuarios = User::where('is_active', true)
            ->with([
                'roles' => function($query) {
                    $query->where('is_active', true)
                          ->with(['permissions' => function($q) {
                              $q->where('is_active', true);
                          }]);
                }
            ])
            ->paginate($perPage, ['*'], 'page', $page);
        
        $resultados = [];
        
        foreach ($usuarios->items() as $usuario) {
            if ($usuario->roles->count() > 0) {
                foreach ($usuario->roles as $papel) {
                    if ($papel->permissions->count() > 0) {
                        foreach ($papel->permissions as $permissao) {
                            $resultados[] = [
                                'user_name' => $usuario->name,
                                'user_email' => $usuario->email,
                                'role_name' => $papel->display_name,
                                'permission_name' => $permissao->display_name,
                                'has_relationships' => true
                            ];
                        }
                    } else {
                        $resultados[] = [
                            'user_name' => $usuario->name,
                            'user_email' => $usuario->email,
                            'role_name' => $papel->display_name,
                            'permission_name' => null,
                            'has_relationships' => false
                        ];
                    }
                }
            }
            // Usuários sem papéis não aparecem na busca geral
        }
        
        return [$resultados, $usuarios->total()];
    }
}
