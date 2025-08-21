<?php

namespace App\Http\Controllers\Api\Administracao;

use App\Http\Controllers\Controller;
use App\Models\Administracao\Permission;
use App\Models\Administracao\Role;
use App\Models\Administracao\User;
use App\Services\Logging\GerenciarUsuariosLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Controller API para gerenciar permissões globais
 */
class PermissionsController extends Controller
{
    protected $logger;

    /**
     * Construtor com middleware de autenticação
     */
    public function __construct(GerenciarUsuariosLogService $logger)
    {
        $this->middleware('auth');
        $this->logger = $logger;
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
        $user = User::find(Auth::id());
        
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
     * Listar todas as permissões com papéis e usuários
     */
    public function index(Request $request)
    {
        // CONSULTA: verifica se tem permissao_crud OU permissao_consultar (ambos podem visualizar)
        $this->checkAccess(['permissao_crud', 'permissao_consultar']);
        
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
        // CONSULTA: verifica se tem permissao_crud OU permissao_consultar (ambos podem visualizar)
        $this->checkAccess(['permissao_crud', 'permissao_consultar']);
        
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
        // CRUD: apenas permissao_crud (modificar relacionamentos)
        $this->checkAccess('permissao_crud');
        
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

            // Log de início da operação
            $this->logger->vinculacaoPermissaoPapel($permissionId, $roleId, [
                'vinculado_por' => Auth::id()
            ]);

            // Criar a vinculação
            $role->permissions()->attach($permissionId);

            // Log de sucesso
            $this->logger->sucessoVinculacaoPermissaoPapel($permissionId, $roleId, [
                'vinculado_por' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Permissão vinculada ao papel com sucesso!'
            ], 201);

        } catch (\Exception $e) {
            $this->logger->erroCriticoUsuarios('VINCULACAO_PERMISSAO_PAPEL', $e->getMessage(), [
                'permissao_id' => $permissionId,
                'papel_id' => $roleId
            ]);

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
        // CRUD: apenas permissao_crud (modificar relacionamentos)
        $this->checkAccess('permissao_crud');
        
        try {
            $permission = Permission::findOrFail($permissionId);
            $role = Role::findOrFail($roleId);

            // Log de início da operação
            $this->logger->desvinculacaoPermissaoPapel($permissionId, $roleId, [
                'desvinculado_por' => Auth::id()
            ]);

            // Remover a vinculação
            $role->permissions()->detach($permissionId);

            // Log de sucesso
            $this->logger->sucessoDesvinculacaoPermissaoPapel($permissionId, $roleId, [
                'desvinculado_por' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Permissão desvinculada do papel com sucesso!'
            ]);

        } catch (\Exception $e) {
            $this->logger->erroCriticoUsuarios('DESVINCULACAO_PERMISSAO_PAPEL', $e->getMessage(), [
                'permissao_id' => $permissionId,
                'papel_id' => $roleId
            ]);

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
        // CRUD: apenas permissao_crud (criar nova permissão)
        $this->checkAccess('permissao_crud');
        
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:permissions',
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean'
            ]);

            // Log de início da operação
            $this->logger->criacaoPermissao(0, $request->all(), [
                'criado_por' => Auth::id()
            ]);

            $permission = Permission::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'is_active' => $request->get('is_active', true)
            ]);

            // Log de sucesso
            $this->logger->sucessoCriacaoPermissao($permission->id, $permission->toArray(), [
                'criado_por' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Permissão criada com sucesso!',
                'permission' => $permission
            ], 201);

        } catch (\Exception $e) {
            $this->logger->erroCriticoUsuarios('CRIACAO_PERMISSAO', $e->getMessage(), [
                'dados' => $request->all()
            ]);

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
        // CRUD: apenas permissao_crud (editar permissão existente)
        $this->checkAccess('permissao_crud');
        
        try {
            $permission = Permission::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255|unique:permissions,name,' . $id,
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean'
            ]);

            // Dados anteriores para o log
            $dadosAnteriores = $permission->toArray();
            
            // Log de início da operação
            $this->logger->edicaoPermissao($permission->id, $dadosAnteriores, $request->all(), [
                'editado_por' => Auth::id()
            ]);

            $permission->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'is_active' => $request->get('is_active', true)
            ]);

            // Log de sucesso
            $this->logger->sucessoEdicaoPermissao($permission->id, $dadosAnteriores, $permission->fresh()->toArray(), [
                'editado_por' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Permissão atualizada com sucesso!',
                'permission' => $permission
            ]);

        } catch (\Exception $e) {
            $this->logger->erroCriticoUsuarios('EDICAO_PERMISSAO', $e->getMessage(), [
                'permissao_id' => $id,
                'dados' => $request->all()
            ]);

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
        // CRUD: apenas permissao_crud (excluir permissão)
        $this->checkAccess('permissao_crud');
        
        try {
            $permission = Permission::findOrFail($id);

            // Verificar se há papéis vinculados
            if ($permission->roles()->count() > 0) {
                return response()->json([
                    'message' => 'Não é possível excluir uma permissão que possui papéis vinculados'
                ], 422);
            }

            // Dados da permissão para o log
            $dadosPermissao = $permission->toArray();
            
            // Log de início da operação
            $this->logger->exclusaoPermissao($permission->id, $dadosPermissao, [
                'excluido_por' => Auth::id()
            ]);

            $permission->delete();

            // Log de sucesso
            $this->logger->sucessoExclusaoPermissao($id, $dadosPermissao, [
                'excluido_por' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Permissão excluída com sucesso!'
            ]);

        } catch (\Exception $e) {
            $this->logger->erroCriticoUsuarios('EXCLUSAO_PERMISSAO', $e->getMessage(), [
                'permissao_id' => $id
            ]);

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
    
    /**
     * Obter papéis que utilizam uma permissão específica
     */
    public function getRoles($id)
    {
        // CONSULTA: verifica se tem permissao_crud OU permissao_consultar (ambos podem visualizar)
        $this->checkAccess(['permissao_crud', 'permissao_consultar']);
        
        try {
            $permission = Permission::findOrFail($id);
            
            $roles = $permission->roles()
                ->where('is_active', true)
                ->select('roles.id', 'roles.name', 'roles.display_name', 'roles.description', 'roles.is_active')
                ->orderBy('roles.display_name')
                ->get();
            
            return response()->json([
                'roles' => $roles
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao carregar papéis da permissão'
            ], 500);
        }
    }
    
    /**
     * Obter usuários afetados por uma permissão específica
     */
    public function getUsers($id)
    {
        // CONSULTA: verifica se tem permissao_crud OU permissao_consultar (ambos podem visualizar)
        $this->checkAccess(['permissao_crud', 'permissao_consultar']);
        
        try {
            $permission = Permission::findOrFail($id);
            
            $users = $permission->roles()
                ->with(['users' => function($query) {
                    $query->select('users.id', 'users.name', 'users.email', 'users.is_active')
                          ->where('users.is_active', true);
                }])
                ->get()
                ->pluck('users')
                ->flatten()
                ->unique('id')
                ->values();
            
            return response()->json([
                'users' => $users
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao carregar usuários da permissão'
            ], 500);
        }
    }
}

