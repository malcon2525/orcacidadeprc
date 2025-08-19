<?php

namespace App\Http\Controllers\Api\Administracao\Usuarios;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use App\Models\Administracao\Role;
use App\Models\Administracao\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
     * Lista todos os usuários com filtros
     * 
     * @param Request $request Requisição HTTP com filtros opcionais
     * @return \Illuminate\Http\JsonResponse Lista de usuários em formato JSON
     */
    public function index(Request $request)
    {
        // CONSULTA: verifica se tem usuario_crud OU usuario_consultar (ambos podem visualizar)
        $this->checkAccess(['usuario_crud', 'usuario_consultar']);
        
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
        $usuarios = $query->orderBy('name', 'asc')->get();
        return response()->json($usuarios);
    }

    /**
     * Cria um novo usuário
     */
    public function store(Request $request)
    {
        // CRUD: verifica se tem usuario_crud (apenas CRUD pode criar usuários)
        $this->checkAccess('usuario_crud');
        
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
                'is_active' => $request->get('is_active', true),
                'login_type' => $request->login_type,
            ]);

            // Atribuir papéis se fornecidos
            if ($request->has('roles') && is_array($request->roles)) {
                $usuario->roles()->attach($request->roles);
            }

            Log::info('Usuário criado com sucesso', [
                'user_id' => $usuario->id,
                'email' => $usuario->email,
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Usuário criado com sucesso',
                'user' => $usuario->load('roles')
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erro ao criar usuário', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'message' => 'Erro ao criar usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibe um usuário específico
     */
    public function show($id)
    {
        // Visualizar: usuario_crud OU usuario_consultar
        $this->checkAccess(['usuario_crud', 'usuario_consultar']);
        
        try {
            $usuario = User::with(['roles.permissions'])->findOrFail($id);
            return response()->json($usuario);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Usuário não encontrado'
            ], 404);
        }
    }

    /**
     * Atualiza um usuário
     */
    public function update(Request $request, $id)
    {
        // CRUD: apenas usuario_crud
        $this->checkAccess('usuario_crud');
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'is_active' => 'boolean',
            'login_type' => 'required|in:local,ad',
            'password' => 'nullable|string|min:6', // Senha opcional na edição
            'password_confirmation' => 'nullable|same:password', // Confirmação opcional
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
            $usuario = User::findOrFail($id);
            
            $dadosUpdate = [
                'name' => $request->name,
                'email' => $request->email,
                'is_active' => $request->get('is_active', true),
                'login_type' => $request->login_type,
            ];
            
            // Atualizar senha apenas se fornecida
            if ($request->filled('password')) {
                $dadosUpdate['password'] = Hash::make($request->password);
            }
            
            $usuario->update($dadosUpdate);

            // Atualizar papéis
            if ($request->has('roles')) {
                $usuario->roles()->sync($request->roles);
            }

            Log::info('Usuário atualizado com sucesso', [
                'user_id' => $usuario->id,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Usuário atualizado com sucesso',
                'user' => $usuario->load('roles')
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar usuário', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Erro ao atualizar usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove um usuário
     */
    public function destroy($id)
    {
        // CRUD: apenas usuario_crud
        $this->checkAccess('usuario_crud');
        
        try {
            $usuario = User::findOrFail($id);
            
            // Não permitir excluir o próprio usuário
            if ($usuario->id === Auth::id()) {
                return response()->json([
                    'message' => 'Não é possível excluir o próprio usuário'
                ], 422);
            }

            $usuario->delete();

            Log::info('Usuário excluído com sucesso', [
                'user_id' => $id,
                'deleted_by' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Usuário excluído com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao excluir usuário', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Erro ao excluir usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista todos os papéis disponíveis
     */
    public function getRoles()
    {
        // Visualizar: usuario_crud OU usuario_consultar
        $this->checkAccess(['usuario_crud', 'usuario_consultar']);
        
        $roles = Role::active()->get();
        return response()->json($roles);
    }

    /**
     * Atualiza a senha de um usuário
     */
    public function updatePassword(Request $request, $id)
    {
        // Visualizar: usuario_crud OU usuario_consultar
        $this->checkAccess(['usuario_crud', 'usuario_consultar']);
        
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $usuario = User::findOrFail($id);
            $usuario->update([
                'password' => Hash::make($request->password)
            ]);

            Log::info('Senha do usuário atualizada', [
                'user_id' => $id,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Senha atualizada com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar senha',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
