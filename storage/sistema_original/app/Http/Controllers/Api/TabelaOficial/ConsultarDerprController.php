<?php

namespace App\Http\Controllers\Api\TabelaOficial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        try {
            $query = DB::table('derpr_composicoes')
                ->select('codigo', 'descricao', 'unidade', 'custo_unitario')
                ->where('data_base', '2024-04-30') // Data base fixa para zoom
                ->where('desoneracao', 'sem'); // Desoneração fixa para zoom

            // Aplica filtros se fornecidos
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('codigo', 'like', "%{$search}%")
                      ->orWhere('descricao', 'like', "%{$search}%");
                });
            }

            // Paginação
            $perPage = $request->get('per_page', 15);
            $resultados = $query->paginate($perPage);

            return response()->json($resultados);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar serviços DER-PR para zoom:', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Erro ao buscar serviços: ' . $e->getMessage()], 500);
        }
    }
}
