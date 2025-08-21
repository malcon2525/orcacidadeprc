<?php

namespace App\Http\Controllers\Api\Administracao\Roles;

use App\Http\Controllers\Controller;
use App\Models\Administracao\Role;
use App\Models\Administracao\User;
use App\Models\Administracao\Permission;
use App\Services\Logging\GerenciarUsuariosLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
        // Garantir que o usuário está autenticado
        if (!Auth::check()) {
            abort(401, 'Usuário não autenticado');
        }
        
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
     * Lista papéis com filtros e estatísticas
     * 
     * @param Request $request Requisição HTTP com filtros opcionais
     * @return \Illuminate\Http\JsonResponse Lista de papéis em formato JSON
     */
    public function index(Request $request)
    {
        // CONSULTA: verifica se tem papel_crud OU papel_consultar (ambos podem visualizar)
        $this->checkAccess(['papel_crud', 'papel_consultar']);
        
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

            return response()->json($papeis);
            
        } catch (\Exception $e) {
            $this->logger->erroCriticoUsuarios('LISTAGEM_PAPEIS', $e->getMessage(), [
                'filtros' => $request->all()
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
        // CRUD: apenas papel_crud
        $this->checkAccess('papel_crud');
        
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
            $this->logger->erroValidacaoUsuarios('CRIACAO_PAPEL', $validator->errors()->toArray(), [
                'dados_recebidos' => $request->all()
            ]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Log de início da operação
            $this->logger->criacaoPapel(0, $request->all(), [
                'criado_por' => Auth::id()
            ]);

            $papel = Role::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
            ]);

            // Log de sucesso
            $this->logger->sucessoCriacaoPapel($papel->id, $papel->toArray(), [
                'criado_por' => Auth::id()
            ]);

            return response()->json($papel, 201);
        } catch (\Exception $e) {
            $this->logger->erroCriticoUsuarios('CRIACAO_PAPEL', $e->getMessage(), [
                'dados' => $request->all()
            ]);
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Exibe um papel específico
     */
    public function show($id)
    {
        // Visualizar: papel_crud OU papel_consultar
        $this->checkAccess(['papel_crud', 'papel_consultar']);
        
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
        // CRUD: apenas papel_crud
        $this->checkAccess('papel_crud');
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            $this->logger->erroValidacaoUsuarios('EDICAO_PAPEL', $validator->errors()->toArray(), [
                'papel_id' => $id,
                'dados_recebidos' => $request->all()
            ]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $papel = Role::findOrFail($id);
            
            // Dados anteriores para o log
            $dadosAnteriores = $papel->toArray();
            
            // Log de início da operação
            $this->logger->edicaoPapel($papel->id, $dadosAnteriores, $request->all(), [
                'editado_por' => Auth::id()
            ]);
            
            $papel->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
            ]);

            // Log de sucesso
            $this->logger->sucessoEdicaoPapel($papel->id, $dadosAnteriores, $papel->fresh()->toArray(), [
                'editado_por' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Papel atualizado com sucesso',
                'role' => $papel
            ]);

        } catch (\Exception $e) {
            $this->logger->erroCriticoUsuarios('EDICAO_PAPEL', $e->getMessage(), [
                'papel_id' => $id,
                'dados' => $request->all()
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
        // CRUD: apenas papel_crud
        $this->checkAccess('papel_crud');
        
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
            }

            // Dados do papel para o log
            $dadosPapel = $papel->toArray();
            
            // Log de início da operação
            $this->logger->exclusaoPapel($papel->id, $dadosPapel, [
                'excluido_por' => Auth::id(),
                'permissoes_removidas' => $permissionsCount
            ]);

            $papel->delete();

            // Log de sucesso
            $this->logger->sucessoExclusaoPapel($id, $dadosPapel, [
                'excluido_por' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Papel excluído com sucesso'
            ]);

        } catch (\Exception $e) {
            $this->logger->erroCriticoUsuarios('EXCLUSAO_PAPEL', $e->getMessage(), [
                'papel_id' => $id
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
        // Visualizar: papel_crud OU papel_consultar
        $this->checkAccess(['papel_crud', 'papel_consultar']);
        
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
        // CRUD: apenas papel_crud
        $this->checkAccess('papel_crud');
        
        $validator = Validator::make($request->all(), [
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            $this->logger->erroValidacaoUsuarios('SINCRONIZACAO_PERMISSOES_PAPEL', $validator->errors()->toArray(), [
                'papel_id' => $id,
                'dados_recebidos' => $request->all()
            ]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $papel = Role::findOrFail($id);
            
            // Permissões anteriores para o log
            $permissoesAnteriores = $papel->permissions()->pluck('permissions.id')->toArray();
            $permissoesNovas = $request->permissions ?? [];
            
            // Log de início da operação
            $this->logger->sincronizacaoPermissoesPapel($papel->id, $permissoesAnteriores, $permissoesNovas, [
                'editado_por' => Auth::id()
            ]);
            
            $papel->syncPermissions($permissoesNovas);

            // Log de sucesso
            $this->logger->sucessoSincronizacaoPermissoesPapel($papel->id, $permissoesAnteriores, $permissoesNovas, [
                'editado_por' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Permissões atualizadas com sucesso',
                'role' => $papel->load('permissions')
            ]);

        } catch (\Exception $e) {
            $this->logger->erroCriticoUsuarios('SINCRONIZACAO_PERMISSOES_PAPEL', $e->getMessage(), [
                'papel_id' => $id,
                'dados' => $request->all()
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
        // Visualizar: papel_crud OU papel_consultar
        $this->checkAccess(['papel_crud', 'papel_consultar']);
        
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

    /**
     * Lista usuários de um papel específico
     */
    public function getUsers($id)
    {
        try {
            // Visualizar: papel_crud OU papel_consultar
            $this->checkAccess(['papel_crud', 'papel_consultar']);
            
            $papel = Role::findOrFail($id);
            
            // Retornar usuários ativos associados ao papel
            $usuarios = $papel->users()
                ->where('users.is_active', true)
                ->with(['roles' => function($query) {
                    $query->select('roles.id', 'roles.name', 'roles.display_name');
                }])
                ->get(['users.id', 'users.name', 'users.email', 'users.username', 'users.is_active', 'users.last_login_at']);
            
            return response()->json($usuarios);
            
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json([
                    'message' => 'Papel não encontrado',
                    'error' => 'Role ID ' . $id . ' não existe no banco de dados'
                ], 404);
            }
            
            return response()->json([
                'message' => 'Erro interno do servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Adiciona um usuário a um papel
     */
    public function addUser(Request $request, $id)
    {
        // CRUD: apenas papel_crud
        $this->checkAccess('papel_crud');

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            $this->logger->erroValidacaoUsuarios('ADICAO_USUARIO_PAPEL', $validator->errors()->toArray(), [
                'papel_id' => $id,
                'dados_recebidos' => $request->all()
            ]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $papel = Role::findOrFail($id);
            $usuario = User::findOrFail($request->user_id);

            // Evitar duplicidade
            if (!$papel->users()->where('users.id', $usuario->id)->exists()) {
                // Log de início da operação
                $this->logger->adicaoUsuarioPapel($papel->id, $usuario->id, [
                    'adicionado_por' => Auth::id()
                ]);
                
                $papel->users()->attach($usuario->id);
                
                // Log de sucesso
                $this->logger->sucessoAdicaoUsuarioPapel($papel->id, $usuario->id, [
                    'adicionado_por' => Auth::id()
                ]);
            }

            return response()->json([
                'message' => 'Usuário vinculado ao papel com sucesso',
                'role_id' => $papel->id,
                'user_id' => $usuario->id,
            ], 201);
        } catch (\Exception $e) {
            $this->logger->erroCriticoUsuarios('ADICAO_USUARIO_PAPEL', $e->getMessage(), [
                'papel_id' => $id,
                'usuario_id' => $request->user_id ?? null
            ]);

            return response()->json([
                'message' => 'Erro ao adicionar usuário ao papel',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove um usuário de um papel
     */
    public function removeUser($id, $userId)
    {
        // CRUD: apenas papel_crud
        $this->checkAccess('papel_crud');

        try {
            $papel = Role::findOrFail($id);
            $usuario = User::findOrFail($userId);

            // Log de início da operação
            $this->logger->remocaoUsuarioPapel($papel->id, $usuario->id, [
                'removido_por' => Auth::id()
            ]);

            $papel->users()->detach($usuario->id);

            // Log de sucesso
            $this->logger->sucessoRemocaoUsuarioPapel($papel->id, $usuario->id, [
                'removido_por' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Usuário removido do papel com sucesso',
                'role_id' => $papel->id,
                'user_id' => $usuario->id,
            ]);
        } catch (\Exception $e) {
            $this->logger->erroCriticoUsuarios('REMOCAO_USUARIO_PAPEL', $e->getMessage(), [
                'papel_id' => $id,
                'usuario_id' => $userId
            ]);

            return response()->json([
                'message' => 'Erro ao remover usuário do papel',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
