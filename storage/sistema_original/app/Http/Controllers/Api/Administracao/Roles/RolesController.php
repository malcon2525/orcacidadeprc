<?php

namespace App\Http\Controllers\Api\Administracao\Roles;

use App\Http\Controllers\Controller;
use App\Models\Administracao\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

/**
 * Controller API para gerenciamento de papéis (roles)
 * 
 * Responsável por operações CRUD de papéis, incluindo:
 * - Listagem com filtros e estatísticas
 * - Criação de novos papéis
 * - Atualização de dados
 * - Exclusão de papéis
 * - Gerenciamento de permissões
 * - Gerenciamento de usuários
 */
class RolesController extends Controller
{
    /**
     * Lista papéis com filtros e estatísticas
     * 
     * @param Request $request Requisição HTTP com filtros opcionais
     * @return \Illuminate\Http\JsonResponse Lista de papéis em formato JSON
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions');

        // Carregar contadores de usuários e permissões
        $query->withCount([
            'users as users_count',
            'permissions as permissions_count',
            'permissions as active_permissions_count' => function($q) {
                $q->where('is_active', true);
            }
        ]);

        // Filtro por nome
        if ($request->has('nome') && !empty($request->nome)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->nome}%")
                  ->orWhere('display_name', 'like', "%{$request->nome}%");
            });
        }

        // Filtro por status
        if ($request->has('is_active') && $request->is_active !== '' && $request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }

        // Ordenação padrão
        $query->orderBy('display_name', 'asc');

        $papeis = $query->get();
        return response()->json($papeis);
    }

    /**
     * Cria um novo papel
     * 
     * @param Request $request Requisição HTTP com dados do papel
     * @return \Illuminate\Http\JsonResponse Papel criado ou erros de validação
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'O nome interno é obrigatório',
            'name.unique' => 'Este nome interno já está cadastrado',
            'display_name.required' => 'O nome de exibição é obrigatório',
            'description.max' => 'A descrição deve ter no máximo 1000 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $papel = Role::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
            ]);

            return response()->json($papel, 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar papel: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Atualiza um papel existente
     * 
     * @param Request $request Requisição HTTP com dados atualizados
     * @param int $id ID do papel a ser atualizado
     * @return \Illuminate\Http\JsonResponse Papel atualizado ou erros de validação
     */
    public function update(Request $request, $id)
    {
        $papel = Role::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($papel->id)
            ],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'O nome interno é obrigatório',
            'name.unique' => 'Este nome interno já está cadastrado',
            'display_name.required' => 'O nome de exibição é obrigatório',
            'description.max' => 'A descrição deve ter no máximo 1000 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $papel->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
            ]);

            return response()->json($papel);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar papel: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Remove um papel
     * 
     * @param int $id ID do papel a ser removido
     * @return \Illuminate\Http\JsonResponse Confirmação de remoção ou erro
     */
    public function destroy($id)
    {
        try {
            Log::info('Tentando excluir papel', ['id' => $id]);
            
            $papel = Role::findOrFail($id);
            Log::info('Papel encontrado', ['papel' => $papel->toArray()]);

            // Verificar se há usuários vinculados
            $usersCount = $papel->users()->count();
            Log::info('Usuários vinculados', ['count' => $usersCount]);
            
            if ($usersCount > 0) {
                Log::warning('Tentativa de excluir papel com usuários vinculados', ['papel_id' => $id, 'users_count' => $usersCount]);
                return response()->json([
                    'message' => "Não é possível excluir o papel '{$papel->display_name}' porque ele possui {$usersCount} usuário(s) vinculado(s). Remova os usuários deste papel antes de excluí-lo."
                ], 422);
            }

            // Verificar se há permissões vinculadas
            $permissionsCount = $papel->permissions()->count();
            Log::info('Permissões vinculadas', ['count' => $permissionsCount]);
            
            if ($permissionsCount > 0) {
                Log::warning('Tentativa de excluir papel com permissões vinculadas', ['papel_id' => $id, 'permissions_count' => $permissionsCount]);
                return response()->json([
                    'message' => "Não é possível excluir o papel '{$papel->display_name}' porque ele possui {$permissionsCount} permissão(ões) vinculada(s). Use 'Gerenciar Permissões' para remover as permissões antes de excluir o papel."
                ], 422);
            }

            $papel->delete();
            Log::info('Papel excluído com sucesso', ['id' => $id]);

            return response()->json(['message' => 'Papel excluído com sucesso']);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir papel: ' . $e->getMessage(), ['id' => $id, 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Atualiza as permissões de um papel
     */
    public function updatePermissions(Request $request, $id)
    {
        try {
            // Log para debug
            Log::info('updatePermissions chamado', [
                'id' => $id,
                'request_data' => $request->all(),
                'permissions' => $request->permissions
            ]);
            
            $papel = Role::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'permissions' => 'array',
                'permissions.*' => 'integer'
            ], [
                'permissions.array' => 'As permissões devem ser uma lista',
                'permissions.*.integer' => 'ID da permissão deve ser um número'
            ]);

            if ($validator->fails()) {
                Log::error('Validação falhou', [
                    'errors' => $validator->errors(),
                    'request_data' => $request->all()
                ]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Sincronizar permissões (remove as antigas e adiciona as novas)
            $papel->permissions()->sync($request->permissions);

            return response()->json([
                'message' => 'Permissões atualizadas com sucesso!',
                'papel' => $papel->load('permissions')
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar permissões do papel: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Retorna os usuários vinculados a um papel
     */
    public function getUsers($id)
    {
        try {
            $papel = Role::findOrFail($id);
            $usuarios = $papel->users()->with('roles')->get();
            
            return response()->json($usuarios);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar usuários do papel: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Retorna as permissões vinculadas a um papel
     */
    public function getPermissions($id)
    {
        try {
            $papel = Role::findOrFail($id);
            $permissoes = $papel->permissions()->get();
            
            return response()->json($permissoes);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar permissões do papel: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Adiciona um usuário a um papel
     */
    public function addUser(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id'
            ], [
                'user_id.required' => 'ID do usuário é obrigatório',
                'user_id.integer' => 'ID do usuário deve ser um número',
                'user_id.exists' => 'Usuário não encontrado'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $papel = Role::findOrFail($id);
            $usuario = User::findOrFail($request->user_id);

            // Verificar se o usuário já tem este papel
            if ($papel->users()->where('user_id', $request->user_id)->exists()) {
                return response()->json(['message' => 'Usuário já possui este papel'], 422);
            }

            // Adicionar usuário ao papel
            $papel->users()->attach($request->user_id);

            Log::info('Usuário adicionado ao papel', [
                'papel_id' => $id,
                'papel_name' => $papel->display_name,
                'user_id' => $request->user_id,
                'user_name' => $usuario->name
            ]);

            return response()->json([
                'message' => 'Usuário adicionado ao papel com sucesso',
                'usuario' => $usuario
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao adicionar usuário ao papel: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Remove um usuário de um papel
     */
    public function removeUser($papelId, $userId)
    {
        try {
            $papel = Role::findOrFail($papelId);
            $usuario = User::findOrFail($userId);

            // Verificar se o usuário tem este papel
            if (!$papel->users()->where('user_id', $userId)->exists()) {
                return response()->json(['message' => 'Usuário não possui este papel'], 422);
            }

            // Remover usuário do papel
            $papel->users()->detach($userId);

            Log::info('Usuário removido do papel', [
                'papel_id' => $papelId,
                'papel_name' => $papel->display_name,
                'user_id' => $userId,
                'user_name' => $usuario->name
            ]);

            return response()->json([
                'message' => 'Usuário removido do papel com sucesso',
                'usuario' => $usuario
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao remover usuário do papel: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }
} 