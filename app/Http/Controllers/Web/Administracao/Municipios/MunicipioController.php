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
            return view('administracao.municipios.index', [
                'permissoes' => [
                    'crud' => true,
                    'consultar' => true,
                    'importar' => true
                ]
            ]);
        }
        
        // 2. Tem o papel gerenciar_municipios? → Verificar permissões específicas
        if ($user->hasRole('gerenciar_municipios')) {
            $permissoes = [
                'crud' => $user->hasPermission('municipio_crud'),
                'consultar' => $user->hasPermission('municipio_consultar'),
                'importar' => $user->hasPermission('municipio_importar')
            ];
            
            // Deve ter pelo menos uma permissão
            if (!in_array(true, $permissoes)) {
                abort(403, 'Acesso negado. Nenhuma permissão específica encontrada.');
            }
            
            return view('administracao.municipios.index', compact('permissoes'));
        }
        
        // 3. Tem o papel visualizar_municipios? → Acesso somente leitura
        if ($user->hasRole('visualizar_municipios')) {
            $permissoes = [
                'crud' => false,
                'consultar' => true,
                'importar' => false
            ];
            
            return view('administracao.municipios.index', compact('permissoes'));
        }
        
        // 3. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }
}
