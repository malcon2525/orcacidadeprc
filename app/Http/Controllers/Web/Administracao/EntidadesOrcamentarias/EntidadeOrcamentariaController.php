<?php

namespace App\Http\Controllers\Web\Administracao\EntidadesOrcamentarias;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EntidadeOrcamentariaController extends Controller
{
    /**
     * Construtor com middleware de autenticação
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe a interface principal de entidades orçamentárias
     * O Vue.js fará todo o gerenciamento através da API
     */
    public function index()
    {
        $user = User::find(Auth::id());
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
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
                abort(403, 'Acesso negado. Nenhuma permissão específica encontrada.');
            }
            
            return view('administracao.entidades-orcamentarias.index', compact('permissoes'));
        }
        
        // 3. Tem o papel visualizar_entidade_orcamentaria? → Acesso somente leitura
        if ($user->hasRole('visualizar_entidade_orcamentaria')) {
            $permissoes = [
                'crud' => false,
                'consultar' => true,
                'importar' => false
            ];
            
            return view('administracao.entidades-orcamentarias.index', compact('permissoes'));
        }
        
        // 4. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }
}
