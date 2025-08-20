<?php

namespace App\Http\Controllers\Web\TabelaOficial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Administracao\User;

class ImportarDerprController extends Controller
{
    /**
     * Exibe a interface de importação DER-PR
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Verifica se é super admin ou tem papel específico
        if ($user->isSuperAdmin() || $user->hasRole('gerenciar_importacao_derpr')) {
            return view('tabela_oficial.importar_derpr.index');
        }
        
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }
} 