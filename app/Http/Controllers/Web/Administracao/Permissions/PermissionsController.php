<?php

namespace App\Http\Controllers\Web\Administracao\Permissions;

use App\Http\Controllers\Controller;
use App\Services\Logging\GerenciarUsuariosLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Exibe a interface principal do gerenciamento de permissões
     * O Vue.js fará todo o CRUD através da API
     */
    public function index()
    {
        $user = Auth::user();
        
        // Log de acesso à funcionalidade
        $this->logger->inicioOperacao('ACESSO_GERENCIAR_PERMISSOES', [
            'usuario_id' => $user->id,
            'usuario_email' => $user->email,
            'usuario_papeis' => $user->roles->pluck('name')->toArray()
        ]);
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            $this->logger->sucesso('ACESSO_GERENCIAR_PERMISSOES', [
                'tipo_acesso' => 'super_admin'
            ]);
            return view('administracao.permissions.index');
        }
        
        // 2. Tem permissão específica?
        if ($user->hasPermission('gerenciar_permissoes')) {
            $this->logger->sucesso('ACESSO_GERENCIAR_PERMISSOES', [
                'tipo_acesso' => 'permissao_especifica'
            ]);
            return view('administracao.permissions.index');
        }
        
        // 3. Nenhuma das opções → Acesso negado
        $this->logger->erroCritico('ACESSO_GERENCIAR_PERMISSOES', 'Acesso negado - permissão insuficiente', [
            'usuario_id' => $user->id,
            'papeis_usuario' => $user->roles->pluck('name')->toArray()
        ]);
        
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }
}
