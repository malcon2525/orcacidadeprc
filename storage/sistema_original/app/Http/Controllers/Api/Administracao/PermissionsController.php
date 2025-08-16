<?php

namespace App\Http\Controllers\Api\Administracao;

use App\Http\Controllers\Controller;
use App\Models\Administracao\Permission;
use App\Models\Administracao\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller API para gerenciar permissões globais
 */
class PermissionsController extends Controller
{
    /**
     * Listar todas as permissões com papéis e usuários
     */
    public function index(Request $request)
    {
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
            return response()->json(['message' => 'Erro ao vincular permissão'], 500);
        }
    }

    /**
     * Desvincular permissão de um papel
     */
    public function detachRole($permissionId, $roleId)
    {
        try {
            $permission = Permission::findOrFail($permissionId);
            $role = Role::findOrFail($roleId);

            // Verificar se a vinculação existe
            $existingRelation = DB::table('role_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->exists();

            if (!$existingRelation) {
                return response()->json([
                    'message' => 'Permissão não está vinculada a este papel'
                ], 404);
            }

            // Remover a vinculação
            $role->permissions()->detach($permissionId);

            return response()->json([
                'message' => 'Permissão desvinculada do papel com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao desvincular permissão'], 500);
        }
    }

    /**
     * Listar papéis disponíveis para uma permissão
     */
    public function availableRoles($permissionId)
    {
        try {
            $permission = Permission::findOrFail($permissionId);
            
            $availableRoles = Role::select([
                'roles.id',
                'roles.name',
                'roles.display_name',
                'roles.description',
                'roles.is_active'
            ])
            ->whereNotExists(function($query) use ($permissionId) {
                $query->select(DB::raw(1))
                      ->from('role_permissions')
                      ->whereRaw('role_permissions.role_id = roles.id')
                      ->where('role_permissions.permission_id', $permissionId);
            })
            ->where('roles.is_active', true)
            ->orderBy('roles.display_name')
            ->get();
            
            return response()->json($availableRoles);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao carregar papéis disponíveis'], 500);
        }
    }

    /**
     * Listar papéis que possuem uma permissão
     */
    public function getRoles($permissionId)
    {
        try {
            $permission = Permission::with(['roles' => function($query) {
                $query->select('roles.id', 'roles.name', 'roles.display_name', 'roles.description', 'roles.is_active');
            }])
            ->findOrFail($permissionId);

            return response()->json($permission->roles);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao carregar papéis da permissão'], 500);
        }
    }

    /**
     * Listar usuários que possuem uma permissão (através dos papéis)
     */
    public function getUsers($permissionId)
    {
        try {
            $permission = Permission::with(['roles.users' => function($query) {
                $query->select('users.id', 'users.name', 'users.email', 'users.is_active');
            }])
            ->findOrFail($permissionId);

            // Obter usuários únicos (pode ter a mesma permissão através de múltiplos papéis)
            $users = $permission->roles->flatMap->users->unique('id')->values();

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao carregar usuários da permissão'], 500);
        }
    }

    /**
     * Criar nova permissão
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:permissions,name',
                'display_name' => 'required|string|max:255',
                'description' => 'required|string|max:500',
                'is_active' => 'required|boolean'
            ]);

            $permission = Permission::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'message' => 'Permissão criada com sucesso!',
                'permission' => $permission
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar permissão'], 500);
        }
    }

    /**
     * Atualizar permissão existente
     */
    public function update(Request $request, $id)
    {
        try {
            $permission = Permission::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255|unique:permissions,name,' . $id,
                'display_name' => 'required|string|max:255',
                'description' => 'required|string|max:500',
                'is_active' => 'required|boolean'
            ]);

            $permission->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'message' => 'Permissão atualizada com sucesso!',
                'permission' => $permission
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar permissão'], 500);
        }
    }

    /**
     * Excluir permissão
     */
    public function destroy($id)
    {
        try {
            $permission = Permission::findOrFail($id);

            // Verificar se a permissão está sendo usada por algum papel
            if ($permission->roles()->count() > 0) {
                return response()->json([
                    'message' => 'Não é possível excluir uma permissão que está sendo usada por papéis'
                ], 409);
            }

            $permission->delete();

            return response()->json([
                'message' => 'Permissão excluída com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao excluir permissão'], 500);
        }
    }
} 