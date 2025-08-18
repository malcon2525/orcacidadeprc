<?php

namespace App\Http\Controllers\Api\Administracao\Roles;

use App\Http\Controllers\Controller;
use App\Models\Administracao\Role;
use App\Models\Administracao\User;
use App\Models\Administracao\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
        if ($user->hasPermission('gerenciar_papeis')) {
            return true;
        }
        
        // 3. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }

    /**
     * Lista papéis com filtros e estatísticas
     * 
     * @param Request $request Requisição HTTP com filtros opcionais
     * @return \Illuminate\Http\JsonResponse Lista de papéis em formato JSON
     */
    public function index(Request $request)
    {
        $this->checkPermissions();
        
        try {
            $query = Role::query();

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
            
            // Calcular contadores manualmente para garantir precisão
            foreach ($papeis as $papel) {
                $papel->users_count = $papel->users()->count();
                $papel->permissions_count = $papel->permissions()->count();
                $papel->active_permissions_count = $papel->permissions()->where('is_active', true)->count();
            }

            return response()->json($papeis);
            
        } catch (\Exception $e) {
            Log::error('Erro ao listar papéis', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Erro ao carregar papéis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cria um novo papel
     * 
     * @param Request $request Requisição HTTP com dados do papel
     * @return \Illuminate\Http\JsonResponse Papel criado ou erros de validação
     */
    public function store(Request $request)
    {
        $this->checkPermissions();
        
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
     * Exibe um papel específico
     */
    public function show($id)
    {
        $this->checkPermissions();
        
        try {
            $papel = Role::with(['permissions', 'users'])->findOrFail($id);
            return response()->json($papel);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Papel não encontrado'
            ], 404);
        }
    }

    /**
     * Atualiza um papel
     */
    public function update(Request $request, $id)
    {
        $this->checkPermissions();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $papel = Role::findOrFail($id);
            
            $papel->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
            ]);

            return response()->json([
                'message' => 'Papel atualizado com sucesso',
                'role' => $papel
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar papel', [
                'role_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Erro ao atualizar papel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove um papel
     */
    public function destroy($id)
    {
        $this->checkPermissions();
        
        try {
            $papel = Role::findOrFail($id);
            
            // Verificar se é o papel "super" (não pode ser excluído)
            if ($papel->name === 'super') {
                return response()->json([
                    'message' => 'O papel "Super Administrador" não pode ser excluído'
                ], 422);
            }
            
            // Verificar se há usuários associados
            $usersCount = $papel->users()->count();
            if ($usersCount > 0) {
                return response()->json([
                    'message' => "Não é possível excluir um papel que possui {$usersCount} usuário(s) associado(s)",
                    'users_count' => $usersCount
                ], 422);
            }

            // Verificar se há permissões associadas
            $permissionsCount = $papel->permissions()->count();
            if ($permissionsCount > 0) {
                // Remover todas as permissões antes de excluir
                $papel->permissions()->detach();
                Log::info('Permissões removidas do papel antes da exclusão', [
                    'role_id' => $id,
                    'permissions_removed' => $permissionsCount
                ]);
            }

            $papel->delete();

            Log::info('Papel excluído com sucesso', [
                'role_id' => $id,
                'role_name' => $papel->display_name,
                'deleted_by' => auth()->id()
            ]);

            return response()->json([
                'message' => 'Papel excluído com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao excluir papel', [
                'role_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Erro ao excluir papel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista permissões de um papel específico
     */
    public function getPermissions($id)
    {
        $this->checkPermissions();
        
        try {
            $papel = Role::findOrFail($id);
            
            // Retornar apenas as permissões ATRIBUÍDAS ao papel
            $permissions = $papel->permissions()->where('is_active', true)->get();
            
            return response()->json($permissions);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Papel não encontrado'
            ], 404);
        }
    }

    /**
     * Atualiza as permissões de um papel
     */
    public function updatePermissions(Request $request, $id)
    {
        $this->checkPermissions();
        
        $validator = Validator::make($request->all(), [
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $papel = Role::findOrFail($id);
            $papel->syncPermissions($request->permissions ?? []);

            Log::info('Permissões do papel atualizadas', [
                'role_id' => $id,
                'permissions_count' => count($request->permissions ?? []),
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'message' => 'Permissões atualizadas com sucesso',
                'role' => $papel->load('permissions')
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar permissões do papel', [
                'role_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Erro ao atualizar permissões',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista permissões disponíveis para atribuir ao papel
     */
    public function getAvailablePermissions($id)
    {
        $this->checkPermissions();
        
        try {
            $papel = Role::findOrFail($id);
            
            // Buscar permissões que NÃO estão atribuídas ao papel
            $permissionsAttribuidas = $papel->permissions()->pluck('permissions.id');
            
            $permissionsDisponiveis = Permission::where('is_active', true)
                ->whereNotIn('id', $permissionsAttribuidas)
                ->get();
            
            return response()->json($permissionsDisponiveis);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Papel não encontrado'
            ], 404);
        }
    }
}
