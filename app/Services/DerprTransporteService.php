<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service responsável pelos cálculos de transporte DER-PR
 * 
 * FUNCIONALIDADE:
 * Este service processa os cálculos de transporte para composições DER-PR que possuem
 * campo "transporte" = "A acrescer". Busca fórmulas de múltiplas fontes (local, comercial,
 * outros DER-PR) e processa os coeficientes para cálculos matemáticos.
 * 
 * TIPOS DE CÁLCULO SUPORTADOS:
 * - Local: usa formula2 do item (transporte local)
 * - Comercial: usa formula1 do item (transporte comercial)
 * - Outro DER-PR: fórmulas de outros departamentos (siglas como CCB, CCC, etc.)
 * - Manual: valor informado diretamente pelo usuário
 * 
 * TABELAS UTILIZADAS:
 * - derpr_transportes: itens de transporte por composição (formula1, formula2)
 * - custo_transporte: definições de tipos de transporte (siglas)
 * - coeficiente_custo_transporte: coeficientes por data base e desoneração
 * 
 * UTILIZADO EM:
 * - Controller: App\Http\Controllers\Api\DerprTransporteController
 * - Componente Vue: resources/js/components/tabela-oficial/derpr/ModalCalculoTransporte.vue
 * - Módulo: Consulta DER-PR (Modal de Cálculo de Transporte)
 * - Documentação: docs/técnico/consultar tabelas/consulta_derpr.md
 */
class DerprTransporteService
{
    /**
     * Retorna todas as fórmulas de transporte disponíveis para um serviço
     * 
     * CHAMADO POR: DerprTransporteController@obterFormulas
     * ROTA: GET /api/preco/derpr/transporte/formulas
     * USADO EM: ModalCalculoTransporte.vue (método carregarOutrosDerpr)
     * 
     * PROCESSO:
     * 1. Busca fórmulas local e comercial na tabela derpr_transportes
     * 2. Busca fórmulas outros DER-PR nas tabelas custo_transporte + coeficiente_custo_transporte
     * 3. Processa e formata todas as fórmulas encontradas
     * 4. Retorna estrutura organizada por tipo de cálculo
     *
     * @param string $codigo Código do serviço DER-PR
     * @param string $data_base Data base para cálculo (YYYY-MM-DD)
     * @param string $desoneracao Tipo de desoneração (com/sem)
     * @return array Estrutura com fórmulas organizadas por tipo
     */
    public function obterFormulasTransporte(string $codigo, string $data_base, string $desoneracao): array
    {
        try {
            Log::info('🔍 DerprTransporteService@obterFormulasTransporte - Iniciando busca:', [
                'codigo' => $codigo,
                'data_base' => $data_base,
                'desoneracao' => $desoneracao
            ]);

            // ===== 1. BUSCA FÓRMULAS LOCAL E COMERCIAL =====
            $formulas_derpr = DB::table('derpr_transportes')
                ->where('codigo_servico', $codigo)
                ->select('formula1', 'formula2')
                ->first();

            Log::info('🔍 DerprTransporteService@obterFormulasTransporte - Fórmulas DER-PR encontradas:', [
                'formulas_derpr' => $formulas_derpr
            ]);

            // ===== 2. BUSCA FÓRMULAS OUTROS DER-PR =====
            $formulas_outros = DB::table('custo_transporte')
                ->join('coeficiente_custo_transporte', 'custo_transporte.id', '=', 'coeficiente_custo_transporte.custo_transporte_id')
                ->where('coeficiente_custo_transporte.data_base', $data_base)
                ->where('coeficiente_custo_transporte.desoneracao', $desoneracao)
                ->select(
                    'custo_transporte.sigla',
                    'custo_transporte.descricao',
                    'coeficiente_custo_transporte.x1 as coeficiente_x1',
                    'coeficiente_custo_transporte.x2 as coeficiente_x2',
                    'coeficiente_custo_transporte.k as termo_independente'
                )
                ->get();

            Log::info('🔍 DerprTransporteService@obterFormulasTransporte - Fórmulas outros DER-PR encontradas:', [
                'formulas_outros_count' => $formulas_outros->count(),
                'formulas_outros' => $formulas_outros->toArray()
            ]);

            // ===== 3. ESTRUTURA DE RETORNO =====
            $resultado = [
                'local' => null,
                'comercial' => null,
                'outros_derpr' => []
            ];

            // ===== 4. PROCESSAMENTO FÓRMULAS LOCAL E COMERCIAL =====
            if ($formulas_derpr) {
                // Local usa formula1
                if ($formulas_derpr->formula1) {
                    $coef_local = $this->extrairCoeficientes($formulas_derpr->formula1);
                    $resultado['local'] = [
                        'formula' => $formulas_derpr->formula1,
                        'coeficientes' => $coef_local
                    ];
                }
                
                // Comercial usa formula2
                if ($formulas_derpr->formula2) {
                    $coef_comercial = $this->extrairCoeficientes($formulas_derpr->formula2);
                    $resultado['comercial'] = [
                        'formula' => $formulas_derpr->formula2,
                        'coeficientes' => $coef_comercial
                    ];
                }
            }

            // ===== 5. PROCESSAMENTO FÓRMULAS OUTROS DER-PR =====
            foreach ($formulas_outros as $formula) {
                // Formata fórmula como texto (ex: "0,72x1 + 0,87x2 + 7,27")
                $formula_texto = sprintf(
                    '%.2fx1 + %.2fx2 + %.2f',
                    $formula->coeficiente_x1,
                    $formula->coeficiente_x2,
                    $formula->termo_independente
                );

                $resultado['outros_derpr'][$formula->sigla] = [
                    'descricao' => $formula->descricao,
                    'formula' => $formula_texto,
                    'coeficientes' => [
                        'x1' => $formula->coeficiente_x1,
                        'x2' => $formula->coeficiente_x2,
                        'termo_independente' => $formula->termo_independente
                    ]
                ];
            }

            return $resultado;
        } catch (\Exception $e) {
            Log::error('💥 Erro ao obter fórmulas de transporte:', [
                'codigo' => $codigo,
                'data_base' => $data_base,
                'desoneracao' => $desoneracao,
                'error' => $e->getMessage()
            ]);
            
            // Retorna estrutura vazia em caso de erro
            return [
                'local' => null,
                'comercial' => null,
                'outros_derpr' => []
            ];
        }
    }

    /**
     * Calcula o valor do transporte com base nos coeficientes e valores
     * 
     * CHAMADO POR: DerprTransporteController@calcular
     * ROTA: POST /api/preco/derpr/transporte/calcular
     * NOTA: Este método não é utilizado pelo frontend (cálculo é feito em JavaScript)
     * 
     * FÓRMULA: valor = (coef_x1 * x1) + (coef_x2 * x2) + termo_independente
     *
     * @param array $coeficientes Array com coeficientes (x1, x2, termo_independente)
     * @param float $x1 Valor de X1 (distância em km)
     * @param float $x2 Valor de X2 (distância em km)
     * @return float Valor calculado do transporte
     */
    public function calcularTransporte(array $coeficientes, float $x1, float $x2): float
    {
        return ($coeficientes['x1'] * $x1) + 
               ($coeficientes['x2'] * $x2) + 
               ($coeficientes['termo_independente'] ?? 0);
    }

    /**
     * Extrai coeficientes de uma fórmula texto
     * 
     * USADO INTERNAMENTE: Por obterFormulasTransporte() para processar formula1 e formula2
     * FUNÇÃO: Converte strings como "0,99x1 + 1,19x2 + 2,48" em array de coeficientes
     * 
     * PROCESSO:
     * 1. Remove espaços e substitui vírgulas por pontos
     * 2. Usa regex para extrair coeficientes de x1, x2 e termo independente
     * 3. Retorna array com coeficientes numéricos
     * 
     * @param string $formula Fórmula no formato "ax1 + bx2 + c"
     * @return array Array com coeficientes (x1, x2, termo_independente)
     */
    private function extrairCoeficientes(string $formula): array
    {
        $coeficientes = [
            'x1' => 0,
            'x2' => 0,
            'termo_independente' => 0
        ];

        // Remove espaços e substitui vírgulas por pontos
        $formula = str_replace([' ', ','], ['', '.'], $formula);

        // Extrai coeficiente de x1 (ex: "0,99x1" -> 0.99)
        if (preg_match('/(-?\d+\.?\d*)x1/', $formula, $matches)) {
            $coeficientes['x1'] = (float) $matches[1];
        }

        // Extrai coeficiente de x2 (ex: "1,19x2" -> 1.19)
        if (preg_match('/(-?\d+\.?\d*)x2/', $formula, $matches)) {
            $coeficientes['x2'] = (float) $matches[1];
        }

        // Extrai termo independente (ex: "+2,48" -> 2.48)
        if (preg_match('/([+-]\d+\.?\d*)$/', $formula, $matches)) {
            $coeficientes['termo_independente'] = (float) $matches[1];
        }

        return $coeficientes;
    }

    /**
     * Retorna todos os itens de transporte de uma composição
     * 
     * CHAMADO POR: DerprTransporteController@itensPorComposicao
     * ROTA: GET /api/preco/derpr/transporte/itens
     * USADO EM: ModalCalculoTransporte.vue (método carregarItens)
     * 
     * CAMPOS RETORNADOS:
     * - id: ID único do item
     * - codigo_servico: Código da composição DER-PR
     * - descricao_servico: Descrição da composição
     * - unidade_servico: Unidade da composição
     * - data_base: Data base do cálculo
     * - desoneracao: Tipo de desoneração
     * - descricao: Descrição do item de transporte
     * - codigo: Código do item de transporte
     * - unidade: Unidade do item de transporte
     * - formula1: Fórmula para cálculo comercial
     * - formula2: Fórmula para cálculo local
     * - consumo: Quantidade consumida do item
     *
     * @param string $codigo_servico Código da composição DER-PR
     * @param string $data_base Data base para cálculo (YYYY-MM-DD)
     * @param string $desoneracao Tipo de desoneração (com/sem)
     * @return array Array de itens de transporte
     */
    public function obterItensTransportePorComposicao(string $codigo_servico, string $data_base, string $desoneracao): array
    {
        return DB::table('derpr_transportes')
            ->where('codigo_servico', $codigo_servico)
            ->where('data_base', $data_base)
            ->where('desoneracao', $desoneracao)
            ->select(
                'id',
                'codigo_servico',
                'descricao_servico',
                'unidade_servico',
                'data_base',
                'desoneracao',
                'descricao',
                'codigo',
                'unidade',
                'formula1',
                'formula2',
                'consumo'
            )
            ->get()
            ->toArray();
    }
} 