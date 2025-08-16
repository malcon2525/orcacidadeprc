<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DerprTransporteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller para APIs de Cálculo de Transporte DER-PR
 * 
 * FUNCIONALIDADE:
 * Fornece endpoints REST para cálculo de transporte em composições DER-PR.
 * Integra com DerprTransporteService para processar cálculos e buscar dados.
 * 
 * ROTAS:
 * - GET /api/preco/derpr/transporte/formulas - Busca fórmulas disponíveis
 * - GET /api/preco/derpr/transporte/itens - Busca itens de transporte
 * - POST /api/preco/derpr/transporte/calcular - Calcula valor (não usado pelo frontend)
 * 
 * UTILIZADO POR:
 * - ModalCalculoTransporte.vue (componente Vue)
 * - Outros componentes que precisem de dados de transporte
 */
class DerprTransporteController extends Controller
{
    /**
     * @var DerprTransporteService
     */
    protected $transporteService;

    /**
     * Construtor com injeção de dependência do service
     * 
     * @param DerprTransporteService $transporteService
     */
    public function __construct(DerprTransporteService $transporteService)
    {
        $this->transporteService = $transporteService;
    }

    /**
     * Retorna as fórmulas de transporte disponíveis para um serviço
     * 
     * ROTA: GET /api/preco/derpr/transporte/formulas
     * USADO EM: ModalCalculoTransporte.vue (método carregarOutrosDerpr)
     * 
     * PARÂMETROS:
     * - codigo: Código do serviço DER-PR
     * - data_base: Data base para cálculo (YYYY-MM-DD)
     * - desoneracao: Tipo de desoneração (com/sem)
     * 
     * RETORNO:
     * {
     *   "local": { "formula": "...", "coeficientes": {...} },
     *   "comercial": { "formula": "...", "coeficientes": {...} },
     *   "outros_derpr": { "CCB": {...}, "CCC": {...} }
     * }
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function obterFormulas(Request $request)
    {
        try {
            Log::info('🔍 DerprTransporteController@obterFormulas - Parâmetros recebidos:', [
                'codigo' => $request->codigo,
                'data_base' => $request->data_base,
                'desoneracao' => $request->desoneracao
            ]);

            // Validação dos parâmetros obrigatórios
            $request->validate([
                'codigo' => 'required|string',
                'data_base' => 'required|string', // Aceita YYYY-MM-DD
                'desoneracao' => 'required|in:com,sem'
            ]);

            // Busca fórmulas através do service
            $formulas = $this->transporteService->obterFormulasTransporte(
                $request->codigo,
                $request->data_base,
                $request->desoneracao
            );

            Log::info('✅ DerprTransporteController@obterFormulas - Fórmulas retornadas:', $formulas);

            return response()->json($formulas);
        } catch (\Exception $e) {
            Log::error('❌ DerprTransporteController@obterFormulas - Erro:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Erro ao obter fórmulas de transporte',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcula o valor do transporte com base em coeficientes e valores
     * 
     * ROTA: POST /api/preco/derpr/transporte/calcular
     * NOTA: Este endpoint não é utilizado pelo frontend (cálculo é feito em JavaScript)
     * 
     * PARÂMETROS:
     * - coeficientes: Array com coeficientes (x1, x2, termo_independente)
     * - x1: Valor de X1
     * - x2: Valor de X2
     * 
     * RETORNO:
     * { "valor": 123.45 }
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calcular(Request $request)
    {
        // Validação dos parâmetros obrigatórios
        $request->validate([
            'coeficientes' => 'required|array',
            'coeficientes.x1' => 'required|numeric',
            'coeficientes.x2' => 'required|numeric',
            'coeficientes.termo_independente' => 'required|numeric',
            'x1' => 'required|numeric',
            'x2' => 'required|numeric'
        ]);

        // Calcula valor através do service
        $valor = $this->transporteService->calcularTransporte(
            $request->coeficientes,
            $request->x1,
            $request->x2
        );

        return response()->json(['valor' => $valor]);
    }

    /**
     * Retorna todos os itens de transporte de uma composição
     * 
     * ROTA: GET /api/preco/derpr/transporte/itens
     * USADO EM: ModalCalculoTransporte.vue (método carregarItens)
     * 
     * PARÂMETROS:
     * - codigo_servico: Código do serviço DER-PR
     * - data_base: Data base para cálculo (YYYY-MM-DD)
     * - desoneracao: Tipo de desoneração (com/sem)
     * 
     * RETORNO:
     * Array de itens de transporte com fórmulas e consumos
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function itensPorComposicao(Request $request)
    {
        try {
            Log::info('🔍 DerprTransporteController@itensPorComposicao - Parâmetros recebidos:', [
                'codigo_servico' => $request->codigo_servico,
                'data_base' => $request->data_base,
                'desoneracao' => $request->desoneracao
            ]);

            // Validação dos parâmetros obrigatórios
            $request->validate([
                'codigo_servico' => 'required|string',
                'data_base' => 'required|string', // Aceita YYYY-MM-DD
                'desoneracao' => 'required|in:com,sem'
            ]);

            // Busca itens através do service
            $itens = $this->transporteService->obterItensTransportePorComposicao(
                $request->codigo_servico,
                $request->data_base,
                $request->desoneracao
            );

            Log::info('✅ DerprTransporteController@itensPorComposicao - Itens retornados:', $itens);

            return response()->json($itens);
        } catch (\Exception $e) {
            Log::error('❌ DerprTransporteController@itensPorComposicao - Erro:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Erro ao obter itens de transporte',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 