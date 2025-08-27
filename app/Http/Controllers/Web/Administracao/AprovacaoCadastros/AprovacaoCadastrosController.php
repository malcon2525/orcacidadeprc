<?php

namespace App\Http\Controllers\Web\Administracao\AprovacaoCadastros;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AprovacaoCadastrosController extends Controller
{
    /**
     * Construtor com middleware de autenticação
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe a interface de aprovação de cadastros
     * O Vue.js fará todo o gerenciamento através da API
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return view('administracao.aprovacao-cadastros.index', [
                'permissoes' => [
                    'crud' => true,
                    'consultar' => true,
                    'aprovar' => true
                ]
            ]);
        }
        
        // 2. Tem a permissão de aprovar cadastros?
        if ($user->hasPermission('aprovar_cadastros')) {
            return view('administracao.aprovacao-cadastros.index', [
                'permissoes' => [
                    'crud' => false,
                    'consultar' => true,
                    'aprovar' => true
                ]
            ]);
        }
        
        // 3. Tem a permissão apenas para visualizar?
        if ($user->hasPermission('visualizar_cadastros_usuarios')) {
            return view('administracao.aprovacao-cadastros.index', [
                'permissoes' => [
                    'crud' => false,
                    'consultar' => true,
                    'aprovar' => false
                ]
            ]);
        }
        
        // 4. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente para acessar aprovação de cadastros.');
    }
}