<?php

namespace App\Http\Controllers\Web\Administracao\Usuarios;

use App\Http\Controllers\Controller;
use App\Services\Logging\GerenciarUsuariosLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuariosController extends Controller
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
     * Exibe a interface principal do gerenciamento de usuários
     * O Vue.js fará todo o CRUD através da API
     */
    public function index()
    {
        $user = Auth::user();
        
        // Log de acesso à funcionalidade
        $this->logger->inicioOperacao('ACESSO_GERENCIAR_USUARIOS', [
            'usuario_id' => $user->id,
            'usuario_email' => $user->email,
            'usuario_papeis' => $user->roles->pluck('name')->toArray()
        ]);
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            $this->logger->sucesso('ACESSO_GERENCIAR_USUARIOS', [
                'tipo_acesso' => 'super_admin'
            ]);
            return view('administracao.usuarios.index');
        }
        
        // 2. Tem permissão específica? (qualquer uma das permissões de usuário)
        if ($user->hasPermission('usuario_crud') || $user->hasPermission('usuario_consultar')) {
            $this->logger->sucesso('ACESSO_GERENCIAR_USUARIOS', [
                'tipo_acesso' => 'permissao_especifica',
                'permissoes_verificadas' => ['usuario_crud', 'usuario_consultar']
            ]);
            return view('administracao.usuarios.index');
        }
        
        // 3. Nenhuma das opções → Acesso negado
        $this->logger->erroCritico('ACESSO_GERENCIAR_USUARIOS', 'Acesso negado - permissão insuficiente', [
            'usuario_id' => $user->id,
            'papeis_usuario' => $user->roles->pluck('name')->toArray()
        ]);
        
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }
}
