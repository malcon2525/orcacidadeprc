<?php

namespace App\Http\Controllers\Web\Administracao\EntidadesOrcamentarias;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use App\Services\Logging\EntidadesOrcamentariasLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EntidadeOrcamentariaController extends Controller
{
    protected $logger;
    
    /**
     * Construtor com middleware de autenticação
     */
    public function __construct(EntidadesOrcamentariasLogService $logger)
    {
        $this->middleware('auth');
        $this->logger = $logger;
    }

    /**
     * Exibe a interface principal de entidades orçamentárias
     * O Vue.js fará todo o gerenciamento através da API
     */
    public function index()
    {
        $user = User::find(Auth::id());
        
        // Log de acesso à funcionalidade
        $this->logger->acessoFuncionalidade('INTERFACE_PRINCIPAL', [
            'usuario_id' => $user->id,
            'usuario_nome' => $user->name,
            'usuario_email' => $user->email
        ]);
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            $this->logger->sucessoAcessoFuncionalidade('INTERFACE_PRINCIPAL', [
                'tipo_acesso' => 'SUPER_ADMIN',
                'permissoes' => ['crud' => true, 'consultar' => true, 'importar' => true]
            ]);
            
            return view('administracao.entidades-orcamentarias.index', [
                'permissoes' => [
                    'crud' => true,
                    'consultar' => true,
                    'importar' => true
                ]
            ]);
        }
        
        // 2. Tem o papel gerenciar_entidade_orcamentaria? → Verificar permissões específicas
        if ($user->hasRole('gerenciar_entidade_orcamentaria')) {
            $permissoes = [
                'crud' => $user->hasPermission('entidade_orcamentaria_crud'),
                'consultar' => $user->hasPermission('entidade_orcamentaria_consultar'),
                'importar' => $user->hasPermission('entidade_orcamentaria_importar')
            ];
            
            // Deve ter pelo menos uma permissão
            if (!in_array(true, $permissoes)) {
                $this->logger->acessoNegadoFuncionalidade('INTERFACE_PRINCIPAL', 'Nenhuma permissão específica encontrada', [
                    'papel' => 'gerenciar_entidade_orcamentaria',
                    'permissoes_solicitadas' => $permissoes
                ]);
                
                abort(403, 'Acesso negado. Nenhuma permissão específica encontrada.');
            }
            
            $this->logger->sucessoAcessoFuncionalidade('INTERFACE_PRINCIPAL', [
                'tipo_acesso' => 'GERENCIAR_ENTIDADE_ORCAMENTARIA',
                'permissoes' => $permissoes
            ]);
            
            return view('administracao.entidades-orcamentarias.index', compact('permissoes'));
        }
        
        // 3. Tem o papel visualizar_entidade_orcamentaria? → Acesso somente leitura
        if ($user->hasRole('visualizar_entidade_orcamentaria')) {
            $permissoes = [
                'crud' => false,
                'consultar' => true,
                'importar' => false
            ];
            
            $this->logger->sucessoAcessoFuncionalidade('INTERFACE_PRINCIPAL', [
                'tipo_acesso' => 'VISUALIZAR_ENTIDADE_ORCAMENTARIA',
                'permissoes' => $permissoes
            ]);
            
            return view('administracao.entidades-orcamentarias.index', compact('permissoes'));
        }
        
        // 4. Nenhuma das opções → Acesso negado
        $this->logger->acessoNegadoFuncionalidade('INTERFACE_PRINCIPAL', 'Permissão insuficiente', [
            'papel_usuario' => $user->roles->pluck('name')->toArray(),
            'permissoes_usuario' => $user->permissions->pluck('name')->toArray()
        ]);
        
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }
}
