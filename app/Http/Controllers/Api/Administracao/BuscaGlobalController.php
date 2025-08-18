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
     * Verifica permissões do usuário
     */
    private function checkPermissions()
    {
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        // 2. Tem permissão específica?
        if ($user->hasPermission('gerenciar_usuarios') || 
            $user->hasPermission('gerenciar_papeis') || 
            $user->hasPermission('gerenciar_permissoes')) {
            return true;
        }
        
        // 3. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }

    /**
     * Realiza busca global em relacionamentos usuário-papel-permissão
     * 
     * @param Request $request Requisição HTTP com filtros
     * @return \Illuminate\Http\JsonResponse Resultados da tabela consolidada
     */
    public function buscar(Request $request)
    {
        $this->checkPermissions();
        
        try {
            $inicio = microtime(true);
            
            // Obter filtros da requisição
            $filtroUsuario = $request->get('usuario', '');
            $filtroPapel = $request->get('papel', '');
            $filtroPermissao = $request->get('permissao', '');
            
            // Estrutura base para resultados
            $resultados = [];
            
            // 1. FILTRO POR USUÁRIO: Mostra papéis e permissões de um usuário específico
            if (!empty($filtroUsuario)) {
                $resultados = $this->buscarPorUsuario($filtroUsuario);
            }
            // 2. FILTRO POR PAPEL: Mostra usuários que têm um papel específico
            elseif (!empty($filtroPapel)) {
                $resultados = $this->buscarPorPapel($filtroPapel);
            }
            // 3. FILTRO POR PERMISSÃO: Mostra usuários que têm uma permissão específica
            elseif (!empty($filtroPermissao)) {
                $resultados = $this->buscarPorPermissao($filtroPermissao);
            }
            // 4. SEM FILTROS: Mostra todos os relacionamentos
            else {
                $resultados = $this->buscarTodosRelacionamentos();
            }
            
            $tempoBusca = round((microtime(true) - $inicio) * 1000, 2);

            return response()->json([
                'success' => true,
                'resultados' => $resultados,
                'tempo_busca' => $tempoBusca,
                'total' => count($resultados),
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
     * Busca papéis e permissões de um usuário específico
     */
    private function buscarPorUsuario($nomeUsuario)
    {
        $resultados = [];
        
        // Buscar usuário
        $usuarios = User::where('name', 'like', "%{$nomeUsuario}%")
            ->where('is_active', true)
            ->get();
        
        foreach ($usuarios as $usuario) {
            // Buscar papéis do usuário
            $papeis = $usuario->roles()->where('is_active', true)->get();
            
            if ($papeis->count() > 0) {
                foreach ($papeis as $papel) {
                    // Buscar permissões do papel
                    $permissoes = $papel->permissions()->where('is_active', true)->get();
                    
                    if ($permissoes->count() > 0) {
                        foreach ($permissoes as $permissao) {
                            $resultados[] = [
                                'user_name' => $usuario->name,
                                'user_email' => $usuario->email,
                                'role_name' => $papel->display_name,
                                'permission_name' => $permissao->display_name
                            ];
                        }
                    } else {
                        // Papel sem permissões
                        $resultados[] = [
                            'user_name' => $usuario->name,
                            'user_email' => $usuario->email,
                            'role_name' => $papel->display_name,
                            'permission_name' => 'Nenhuma permissão'
                        ];
                    }
                }
            } else {
                // Usuário sem papéis
                $resultados[] = [
                    'user_name' => $usuario->name,
                    'user_email' => $usuario->email,
                    'role_name' => 'Nenhum papel',
                    'permission_name' => 'Nenhuma permissão'
                ];
            }
        }
        
        return $resultados;
    }

    /**
     * Busca usuários que têm um papel específico
     */
    private function buscarPorPapel($nomePapel)
    {
        $resultados = [];
        
        // Buscar papel
        $papeis = Role::where('display_name', 'like', "%{$nomePapel}%")
            ->where('is_active', true)
            ->get();
        
        foreach ($papeis as $papel) {
            // Buscar usuários que têm este papel
            $usuarios = $papel->users()->where('is_active', true)->get();
            
            if ($usuarios->count() > 0) {
                foreach ($usuarios as $usuario) {
                    // Buscar permissões do papel
                    $permissoes = $papel->permissions()->where('is_active', true)->get();
                    
                    if ($permissoes->count() > 0) {
                        foreach ($permissoes as $permissao) {
                            $resultados[] = [
                                'user_name' => $usuario->name,
                                'user_email' => $usuario->email,
                                'role_name' => $papel->display_name,
                                'permission_name' => $permissao->display_name
                            ];
                        }
                    } else {
                        // Papel sem permissões
                        $resultados[] = [
                            'user_name' => $usuario->name,
                            'user_email' => $usuario->email,
                            'role_name' => $papel->display_name,
                            'permission_name' => 'Nenhuma permissão'
                        ];
                    }
                }
            }
        }
        
        return $resultados;
    }

    /**
     * Busca usuários que têm uma permissão específica
     */
    private function buscarPorPermissao($nomePermissao)
    {
        $resultados = [];
        
        // Buscar permissão
        $permissoes = Permission::where('display_name', 'like', "%{$nomePermissao}%")
            ->where('is_active', true)
            ->get();
        
        foreach ($permissoes as $permissao) {
            // Buscar papéis que têm esta permissão
            $papeis = $permissao->roles()->where('is_active', true)->get();
            
            foreach ($papeis as $papel) {
                // Buscar usuários que têm este papel
                $usuarios = $papel->users()->where('is_active', true)->get();
                
                foreach ($usuarios as $usuario) {
                    $resultados[] = [
                        'user_name' => $usuario->name,
                        'user_email' => $usuario->email,
                        'role_name' => $papel->display_name,
                        'permission_name' => $permissao->display_name
                    ];
                }
            }
        }
        
        return $resultados;
    }

    /**
     * Busca todos os relacionamentos (sem filtros) - VERSÃO ROBUSTA
     */
    private function buscarTodosRelacionamentos()
    {
        $resultados = [];
        
        // Buscar todos os usuários ativos
        $usuarios = User::where('is_active', true)->get();
        
        foreach ($usuarios as $usuario) {
            // Buscar papéis do usuário
            $papeis = $usuario->roles()->where('is_active', true)->get();
            
            if ($papeis->count() > 0) {
                foreach ($papeis as $papel) {
                    // Buscar permissões do papel
                    $permissoes = $papel->permissions()->where('is_active', true)->get();
                    
                    if ($permissoes->count() > 0) {
                        foreach ($permissoes as $permissao) {
                            $resultados[] = [
                                'user_name' => $usuario->name,
                                'user_email' => $usuario->email,
                                'role_name' => $papel->display_name,
                                'permission_name' => $permissao->display_name
                            ];
                        }
                    } else {
                        // Papel sem permissões
                        $resultados[] = [
                            'user_name' => $usuario->name,
                            'user_email' => $usuario->email,
                            'role_name' => $papel->display_name,
                            'permission_name' => 'Nenhuma permissão'
                        ];
                    }
                }
            } else {
                // Usuário sem papéis
                $resultados[] = [
                    'user_name' => $usuario->name,
                    'user_email' => $usuario->email,
                    'role_name' => 'Nenhum papel',
                    'permission_name' => 'Nenhuma permissão'
                ];
            }
        }
        
        return $resultados;
    }
}
