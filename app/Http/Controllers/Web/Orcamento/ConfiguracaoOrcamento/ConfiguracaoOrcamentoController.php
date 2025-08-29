<?php

namespace App\Http\Controllers\Web\Orcamento\ConfiguracaoOrcamento;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ConfiguracaoOrcamentoController extends Controller
{
    /**
     * Exibe a interface de configuração do contexto orçamentário
     */
    public function index()
    {
        // Verificar autenticação
        if (!Auth::check()) {
            abort(401, 'Usuário não autenticado.');
        }

        $user = Auth::user();

        // Verificar se o usuário tem vínculo com entidades orçamentárias
        $temVinculos = $user->entidadesOrcamentarias()
            ->wherePivot('ativo', true)
            ->exists();

        if (!$temVinculos) {
            abort(403, 'Usuário não possui vínculos ativos com entidades orçamentárias.');
        }

        return view('orcamento.configuracao_orcamento.index');
    }
}
