<?php

namespace App\Http\Controllers\Api\Administracao;

use App\Http\Controllers\Controller;
use App\Models\Administracao\Permission;
use App\Models\Administracao\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Controller API para gerenciar permissões globais
 */
class PermissionsController extends Controller
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
        if ($user->hasPermission('gerenciar_permissoes')) {
            return true;
        }
        
        // 3. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }

    /**
     * Listar todas as permissões com papéis e usuários
     */
    public function index(Request $request)
    {
        $this->checkPermissions();
        
        try {
            $query = Permission::with(['roles.users' => function($query) {
                $query->select('users.id', 'users.name', 'users.email');
            }])
            ->withCount(['roles as total_roles']);

            // Filtros
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('display_name', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            $permissions = $query->orderBy('display_name')
                                ->get();

            // Adicionar contadores para cada permissão
            $permissions->each(function($permission) {
                $permission->roles_count = $permission->roles->count();
                $permission->users_count = $permission->roles->flatMap->users->unique('id')->count();
            });

            return response()->json($permissions);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao carregar permissões'], 500);
        }
    }

    /**
     * Mostrar uma permissão específica com detalhes completos
     */
    public function show($id)
    {
        $this->checkPermissions();
        
        try {
            $permission = Permission::with(['roles.users' => function($query) {
                $query->select('users.id', 'users.name', 'users.email', 'users.is_active');
            }])
            ->withCount(['roles as total_roles'])
            ->findOrFail($id);

            return response()->json($permission);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Permissão não encontrada'], 404);
        }
    }

    /**
     * Vincular permissão a um papel
     */
    public function attachRole($permissionId, $roleId)
    {
        $this->checkPermissions();
        
        try {
            $permission = Permission::findOrFail($permissionId);
            $role = Role::findOrFail($roleId);

            // Verificar se a vinculação já existe
            $existingRelation = DB::table('role_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->exists();

            if ($existingRelation) {
                return response()->json([
                    'message' => 'Permissão já está vinculada a este papel'
                ], 409);
            }

            // Criar a vinculação
            $role->permissions()->attach($permissionId);

            return response()->json([
                'message' => 'Permissão vinculada ao papel com sucesso!'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao vincular permissão ao papel'
            ], 500);
        }
    }

    /**
     * Desvincular permissão de um papel
     */
    public function detachRole($permissionId, $roleId)
    {
        $this->checkPermissions();
        
        try {
            $permission = Permission::findOrFail($permissionId);
            $role = Role::findOrFail($roleId);

            // Remover a vinculação
            $role->permissions()->detach($permissionId);

            return response()->json([
                'message' => 'Permissão desvinculada do papel com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao desvincular permissão do papel'
            ], 500);
        }
    }

    /**
     * Criar nova permissão
     */
    public function store(Request $request)
    {
        $this->checkPermissions();
        
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:permissions',
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean'
            ]);

            $permission = Permission::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'is_active' => $request->get('is_active', true)
            ]);

            return response()->json([
                'message' => 'Permissão criada com sucesso!',
                'permission' => $permission
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar permissão'
            ], 500);
        }
    }

    /**
     * Atualizar permissão
     */
    public function update(Request $request, $id)
    {
        $this->checkPermissions();
        
        try {
            $permission = Permission::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255|unique:permissions,name,' . $id,
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean'
            ]);

            $permission->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'is_active' => $request->get('is_active', true)
            ]);

            return response()->json([
                'message' => 'Permissão atualizada com sucesso!',
                'permission' => $permission
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar permissão'
            ], 500);
        }
    }

    /**
     * Excluir permissão
     */
    public function destroy($id)
    {
        $this->checkPermissions();
        
        try {
            $permission = Permission::findOrFail($id);

            // Verificar se há papéis vinculados
            if ($permission->roles()->count() > 0) {
                return response()->json([
                    'message' => 'Não é possível excluir uma permissão que possui papéis vinculados'
                ], 422);
            }

            $permission->delete();

            return response()->json([
                'message' => 'Permissão excluída com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir permissão'
            ], 500);
        }
    }

    /**
     * Listar permissões por papel
     */
    public function getByRole($roleId)
    {
        try {
            $role = Role::with('permissions')->findOrFail($roleId);
            return response()->json($role->permissions);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Papel não encontrado'], 404);
        }
    }

    /**
     * Sincronizar permissões de um papel
     */
    public function syncRolePermissions(Request $request, $roleId)
    {
        try {
            $role = Role::findOrFail($roleId);
            
            $request->validate([
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            $role->permissions()->sync($request->permissions ?? []);

            return response()->json([
                'message' => 'Permissões sincronizadas com sucesso!',
                'role' => $role->load('permissions')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao sincronizar permissões'
            ], 500);
        }
    }
}

