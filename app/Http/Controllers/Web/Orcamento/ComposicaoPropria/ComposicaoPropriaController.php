<?php

namespace App\Http\Controllers\Web\Orcamento\ComposicaoPropria;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use App\Helpers\OrcamentoContextHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComposicaoPropriaController extends Controller
{
    /**
     * Verifica se o usuário tem acesso à funcionalidade
     */
    private function checkAccess()
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(401, 'Usuário não autenticado.');
        }
        
        // 1. Super admin tem acesso total
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        // 2. Verificar papel criar_orcamentos
        if (!$user->hasRole('criar_orcamentos')) {
            abort(403, 'Acesso negado. É necessário ter o papel "criar_orcamentos".');
        }
        
        // 3. Verificar permissão composicao_propria_crud
        if (!$user->hasPermission('composicao_propria_crud')) {
            abort(403, 'Acesso negado. É necessário ter a permissão "composicao_propria_crud".');
        }
        
        // 4. Verificar vínculo com entidade orçamentária
        $temVinculos = DB::table('user_entidades_orcamentarias')
            ->where('user_id', $user->id)
            ->where('ativo', true)
            ->exists();
        
        if (!$temVinculos) {
            abort(403, 'Acesso negado. Usuário não possui vínculos ativos com entidades orçamentárias.');
        }
        
        return true;
    }
    
    /**
     * Verifica contexto orçamentário obrigatório
     */
    private function verificarContexto()
    {
        $resultado = OrcamentoContextHelper::verificarContextoParaModuloOrcamento();
        
        if (is_string($resultado)) {
            if (filter_var($resultado, FILTER_VALIDATE_URL)) {
                // É uma URL de redirecionamento
                return redirect($resultado)->with('warning', 'É necessário configurar o contexto orçamentário antes de acessar esta funcionalidade.');
            } else {
                // É uma mensagem de erro
                abort(403, $resultado);
            }
        }
        
        return null; // Tudo OK
    }

    /**
     * Exibe a página principal de composições próprias
     */
    public function index()
    {
        $this->checkAccess();
        
        // Verificar contexto orçamentário
        $redirectContexto = $this->verificarContexto();
        if ($redirectContexto) {
            return $redirectContexto;
        }
        
        // Carregar contexto na sessão se necessário
        if (OrcamentoContextHelper::temContextoDefinido() && !OrcamentoContextHelper::getContextoDaSessao()) {
            OrcamentoContextHelper::carregarContextoNaSessao();
        }
        
        // Permissões do usuário (abordagem binária - tem acesso completo ou não tem)
        $permissoes = [
            'crud' => true, // Se chegou até aqui, tem acesso completo
            'consultar' => true,
        ];
        
        return view('orcamento.composicao-propria.index', compact('permissoes'));
    }
}
