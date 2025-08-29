<?php

namespace App\Http\Controllers\Api\TabelaOficial\ConsultarDerpr;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller responsável por gerenciar as consultas à tabela DER-PR
 * 
 * Este controller fornece endpoints para:
 * - Listar as tabelas disponíveis (data_base e desoneracao)
 * - Buscar os dados específicos de uma tabela selecionada
 * - Calcular valores de transporte
 */
class ConsultarDerprController extends Controller
{
    /**
     * Verifica se o usuário tem acesso à funcionalidade
     */
    private function checkAccess()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->hasRole('super')) {
            return true;
        }
        
        // 2. Tem o papel específico para consultar DERPR? → Acesso permitido
        if ($user->hasRole('consultar_tabela_derpr')) {
            return true;
        }
        
        // 3. Tem papel criar_orcamentos? → Acesso permitido (para composições próprias)
        if ($user->hasRole('criar_orcamentos')) {
            return true;
        }
        
        // 4. Acesso negado
        abort(403, 'Acesso negado. Papel insuficiente.');
    }

    /**
     * Retorna a lista de tabelas disponíveis no banco de dados
     * 
     * Busca todas as combinações únicas de data_base e desoneracao
     * na tabela derpr_composicoes, ordenadas por data_base decrescente
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscarTabelas()
    {
        try {
            $tabelas = DB::table('derpr_composicoes')
                ->select('data_base', 'desoneracao')
                ->distinct()
                ->orderBy('data_base', 'desc')
                ->get()
                ->map(function ($tabela) {
                    return [
                        'id' => $tabela->data_base . '_' . $tabela->desoneracao,
                        'data_base' => $tabela->data_base,
                        'desoneracao' => $tabela->desoneracao
                    ];
                });

            return response()->json($tabelas);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar tabelas DER-PR:', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Erro ao buscar tabelas: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Busca os dados da tabela selecionada
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscarDados(Request $request)
    {
        try {
            // Valida a requisição
            $request->validate([
                'tabela' => 'required|string'
            ]);

            // Extrai data_base e desoneracao da tabela selecionada
            list($data_base, $desoneracao) = explode('_', $request->tabela);

            // Constrói a query base
            $query = DB::table('derpr_composicoes')
                ->select([
                    'codigo',
                    'descricao',
                    'unidade',
                    DB::raw('(custo_execucao + custo_sub_servico) as mao_de_obra'),
                    DB::raw('(custo_unitario - (custo_execucao + custo_sub_servico)) as material_equipamento'),
                    'custo_unitario as custo_total',
                    'transporte'
                ])
                ->where('data_base', $data_base)
                ->where('desoneracao', $desoneracao);

            // Retorna todos os dados
            $resultados = $query->get();
            return response()->json(['data' => $resultados]);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados DER-PR:', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Erro ao buscar dados: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Busca serviços DER-PR para zoom (paginado)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function zoomServicos(Request $request)
    {
        // Verifica acesso
        $this->checkAccess();
        
        try {
            $search = $request->input('search');
            $desoneracao = strtolower($request->input('desoneracao', 'sem'));
            $dataBase = $request->input('data_base');
            $perPage = 15;
            
            $query = DB::table('derpr_composicoes')
                ->select([
                    'codigo', 
                    'descricao', 
                    'unidade', 
                    'custo_unitario',
                    'data_base',
                    'desoneracao'
                ])
                ->where('desoneracao', $desoneracao);
            
            // Se data_base não for especificada, usar a data do contexto orçamentário
            if ($dataBase) {
                $query->where('data_base', $dataBase);
            } else {
                // Buscar data_base do contexto orçamentário do usuário
                $user = \Illuminate\Support\Facades\Auth::user();
                $contexto = \App\Models\Orcamento\UserOrcamentoContext::getContextoUsuario($user->id);
                
                if ($contexto && $contexto->data_base_derpr) {
                    $query->where('data_base', $contexto->data_base_derpr->format('Y-m-d'));
                } else {
                    // Fallback: usar a mais recente se não há contexto
                    $latestDate = DB::table('derpr_composicoes')
                        ->select('data_base')
                        ->where('desoneracao', $desoneracao)
                        ->orderBy('data_base', 'desc')
                        ->limit(1)
                        ->value('data_base');
                        
                    if ($latestDate) {
                        $query->where('data_base', $latestDate);
                    }
                }
            }

            // Aplica filtros se fornecidos
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('codigo', 'like', "%{$search}%")
                      ->orWhere('descricao', 'like', "%{$search}%");
                });
            }

            // Paginação
            $resultados = $query->orderBy('codigo')->paginate($perPage);

            // Capturar qual data_base está sendo usada para o frontend
            $dataBaseUsada = null;
            if ($dataBase) {
                $dataBaseUsada = $dataBase;
            } else {
                // Buscar data_base do contexto orçamentário do usuário
                $user = \Illuminate\Support\Facades\Auth::user();
                $contexto = \App\Models\Orcamento\UserOrcamentoContext::getContextoUsuario($user->id);
                
                if ($contexto && $contexto->data_base_derpr) {
                    $dataBaseUsada = $contexto->data_base_derpr->format('Y-m-d');
                } else {
                    // Fallback: buscar a mais recente
                    $dataBaseUsada = DB::table('derpr_composicoes')
                        ->select('data_base')
                        ->where('desoneracao', $desoneracao)
                        ->orderBy('data_base', 'desc')
                        ->limit(1)
                        ->value('data_base');
                }
            }
            
            // Adicionar informações extras no response
            $response = $resultados->toArray();
            $response['meta'] = array_merge($response['meta'] ?? [], [
                'data_base_utilizada' => $dataBaseUsada,
                'data_base_formatada' => $dataBaseUsada ? \Carbon\Carbon::parse($dataBaseUsada)->format('m/Y') : null,
                'desoneracao' => $desoneracao,
                'fonte' => 'DERPR'
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar serviços DER-PR para zoom:', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Erro ao buscar serviços: ' . $e->getMessage()], 500);
        }
    }
}
