<?php

namespace App\Http\Controllers\Api\Administracao\Usuarios;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Controller API para gerenciamento de usuários
 * 
 * Responsável por operações CRUD de usuários, incluindo:
 * - Listagem com filtros
 * - Criação de novos usuários
 * - Atualização de dados
 * - Exclusão de usuários
 * - Gerenciamento de papéis
 */
class UsuariosController extends Controller
{
    /**
     * Lista todos os usuários com filtros
     * 
     * @param Request $request Requisição HTTP com filtros opcionais
     * @return \Illuminate\Http\JsonResponse Lista de usuários em formato JSON
     */
    public function index(Request $request)
    {
        $query = User::with(['roles.permissions']);

        // Filtro por nome
        if ($request->has('nome') && !empty($request->nome)) {
            $query->where('name', 'like', '%' . $request->nome . '%');
        }

        // Filtro por email
        if ($request->has('email') && !empty($request->email)) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Filtro por status
        if ($request->has('status') && $request->status !== '' && $request->status !== null) {
            $isActive = $request->status === 'ativo';
            $query->where('is_active', $isActive);
        }

        // Filtro por tipo de login
        if ($request->has('tipo') && !empty($request->tipo)) {
            $query->where('login_type', $request->tipo);
        }

        // Retornar todos os usuários sem paginação
        $usuarios = $query->get();
        return response()->json($usuarios);
    }



    /**
     * Cria um novo usuário
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'is_active' => 'boolean',
            'login_type' => 'required|in:local,ad',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ], [
            'name.required' => 'O nome é obrigatório',
            'email.required' => 'O email é obrigatório',
            'email.email' => 'O email informado é inválido',
            'email.unique' => 'Este email já está cadastrado',
            'password.required' => 'A senha é obrigatória',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres',
            'password_confirmation.required' => 'A confirmação de senha é obrigatória',
            'password_confirmation.same' => 'A confirmação de senha não confere',
            'login_type.required' => 'O tipo de login é obrigatório',
            'login_type.in' => 'O tipo de login deve ser local ou ad',
            'roles.array' => 'Os papéis devem ser uma lista',
            'roles.*.exists' => 'Papel inválido selecionado'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $usuario = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->email, // Usar email como username
                'password' => Hash::make($request->password),
                'is_active' => $request->is_active ?? true,
                'login_type' => $request->login_type,
            ]);

            // Vincular papéis se fornecidos
            if ($request->has('roles') && is_array($request->roles)) {
                $usuario->roles()->attach($request->roles);
            }

            // Carregar relacionamentos para retorno
            $usuario->load('roles');

            return response()->json($usuario, 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Atualiza um usuário existente
     */
    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($usuario->id)
            ],
            'password' => 'nullable|string|min:6',
            'password_confirmation' => 'nullable|same:password',
            'is_active' => 'boolean',
            'login_type' => 'required|in:local,ad',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ], [
            'name.required' => 'O nome é obrigatório',
            'email.required' => 'O email é obrigatório',
            'email.email' => 'O email informado é inválido',
            'email.unique' => 'Este email já está cadastrado',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres',
            'password_confirmation.same' => 'A confirmação de senha não confere',
            'login_type.required' => 'O tipo de login é obrigatório',
            'login_type.in' => 'O tipo de login deve ser local ou ad',
            'roles.array' => 'Os papéis devem ser uma lista',
            'roles.*.exists' => 'Papel inválido selecionado'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $dados = [
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->email, // Usar email como username
                'is_active' => $request->is_active ?? true,
                'login_type' => $request->login_type,
            ];

            // Atualizar senha apenas se fornecida
            if ($request->filled('password')) {
                $dados['password'] = Hash::make($request->password);
            }

            $usuario->update($dados);

            // Atualizar papéis
            if ($request->has('roles')) {
                $usuario->roles()->sync($request->roles);
            }

            // Carregar relacionamentos para retorno
            $usuario->load('roles');

            return response()->json($usuario);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar usuário: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Remove um usuário
     */
    public function destroy($id)
    {
        try {
            $usuario = User::findOrFail($id);
            
            // Verificar se não é o usuário logado
            if ($usuario->id === auth()->id()) {
                return response()->json(['message' => 'Não é possível excluir o próprio usuário'], 400);
            }

            $usuario->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir usuário: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Busca todas as permissões de um usuário
     */
    public function permissoes($id)
    {
        try {
            // Primeiro: Verificar se o usuário existe
            $usuario = User::findOrFail($id);

            // Segundo: Buscar roles de forma mais simples
            $roles = $usuario->roles()
                ->where('is_active', true)
                ->with(['permissions' => function($query) {
                    $query->where('is_active', true);
                }])
                ->get();

            // Terceiro: Consolidar permissões únicas de forma mais segura
            $consolidatedPermissions = collect();
            
            foreach ($roles as $role) {
                foreach ($role->permissions as $permission) {
                    // Verificar se já existe esta permissão
                    $existing = $consolidatedPermissions->firstWhere('name', $permission->name);
                    
                    if (!$existing) {
                        $consolidatedPermissions->push([
                            'name' => $permission->name,
                            'display_name' => $permission->display_name,
                            'description' => $permission->description ?? '',
                            'from_role' => $role->display_name
                        ]);
                    }
                }
            }

            // Retornar resposta simplificada
            return response()->json([
                'user' => [
                    'id' => $usuario->id,
                    'name' => $usuario->name,
                    'email' => $usuario->email,
                    'username' => $usuario->username
                ],
                'roles' => $roles->map(function($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => $role->display_name,
                        'description' => $role->description ?? '',
                        'permissions' => $role->permissions->map(function($perm) {
                            return [
                                'name' => $perm->name,
                                'display_name' => $perm->display_name,
                                'description' => $perm->description ?? ''
                            ];
                        })
                    ];
                }),
                'consolidated_permissions' => $consolidatedPermissions->values(),
                'total_roles' => $roles->count(),
                'total_permissions' => $consolidatedPermissions->count()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar permissões do usuário: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'Erro interno do servidor',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
} 