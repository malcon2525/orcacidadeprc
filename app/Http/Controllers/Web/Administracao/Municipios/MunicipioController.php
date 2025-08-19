<?php

namespace App\Http\Controllers\Web\Administracao\Municipios;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MunicipioController extends Controller
{
    /**
     * Construtor com middleware de autenticação
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe a interface principal de municípios
     * O Vue.js fará todo o gerenciamento através da API
     */
    public function index()
    {
        $user = User::find(Auth::id());
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return view('administracao.municipios.index');
        }
        
        // 2. Tem o papel gerenciar_municipio? → Acesso ao módulo
        if ($user->hasRole('gerenciar_municipio')) {
            return view('administracao.municipios.index');
        }
        
        // 3. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }
}
