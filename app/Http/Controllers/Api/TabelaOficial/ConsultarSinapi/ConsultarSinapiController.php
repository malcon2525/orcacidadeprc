<?php

namespace App\Http\Controllers\Api\TabelaOficial\ConsultarSinapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * API Controller responsável por gerenciar as consultas à tabela SINAPI
 * 
 * Este controller fornece endpoints para:
 * - Listar as tabelas disponíveis (data_base e desoneracao)
 * - Buscar os dados específicos de uma tabela selecionada
 * - Exportar dados para Excel
 * - Zoom de serviços
 */
class ConsultarSinapiController extends Controller
{
    /**
     * Retorna a lista de tabelas disponíveis
     * 
     * Busca as combinações únicas de data_base e desoneracao na view
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscarTabelas()
    {
        try {
            $tabelas = DB::table('sinapi_composicoes_view')
                ->select('data_base', 'desoneracao')
                ->distinct()
                ->orderBy('data_base', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->data_base . '|' . $item->desoneracao,
                        'data_base' => $item->data_base,
                        'desoneracao' => $item->desoneracao
                    ];
                });

            return response()->json($tabelas);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar tabelas SINAPI:', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Erro ao buscar tabelas',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna os dados da tabela selecionada
     * 
     * Consulta a view sinapi_composicoes_view que já contém todos os cálculos prontos
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscarDados(Request $request)
    {
        try {
            $request->validate([
                'tabela' => 'required|string'
            ]);

            // Extrai data_base e desoneracao do parâmetro tabela
            list($data_base, $desoneracao) = explode('|', $request->tabela);

            // Verifica se existem registros na view com os parâmetros
            $existeRegistro = DB::table('sinapi_composicoes_view')
                ->where('data_base', $data_base)
                ->where('desoneracao', $desoneracao)
                ->exists();

            if (!$existeRegistro) {
                return response()->json([
                    'data' => [],
                    'total' => 0,
                    'message' => 'Nenhuma composição encontrada para a tabela selecionada'
                ]);
            }

            // Busca os dados da view que já contém todos os cálculos prontos
            $composicoes = DB::table('sinapi_composicoes_view')
                ->select([
                    'grupo',
                    'codigo',
                    'descricao',
                    'unidade',
                    'valor_mao_obra',
                    'valor_mat_equip',
                    'valor_total',
                    'desoneracao',
                    'data_base'
                ])
                ->where('data_base', $data_base)
                ->where('desoneracao', $desoneracao)
                ->get();

            return response()->json([
                'data' => $composicoes,
                'total' => count($composicoes)
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados SINAPI:', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Erro ao buscar dados',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exporta os dados para Excel
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportarExcel(Request $request)
    {
        try {
            $request->validate([
                'tabela' => 'required|string'
            ]);

            // Extrai data_base e desoneracao do parâmetro tabela
            list($data_base, $desoneracao) = explode('|', $request->tabela);

            // Busca os dados da view que já contém todos os cálculos prontos
            $composicoes = DB::table('sinapi_composicoes_view')
                ->select([
                    'grupo',
                    'codigo',
                    'descricao',
                    'unidade',
                    'valor_mao_obra',
                    'valor_mat_equip',
                    'valor_total',
                    'desoneracao',
                    'data_base'
                ])
                ->where('data_base', $data_base)
                ->where('desoneracao', $desoneracao)
                ->get();

            // Cria uma nova planilha
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Define o título da planilha
            $sheet->setTitle('SINAPI - ' . $data_base);

            // Define os cabeçalhos
            $headers = [
                'A1' => 'Grupo',
                'B1' => 'Código',
                'C1' => 'Descrição',
                'D1' => 'Unidade',
                'E1' => 'Data Base',
                'F1' => 'Desoneração',
                'G1' => 'Mão de Obra',
                'H1' => 'Mat./Equip.',
                'I1' => 'Custo Total'
            ];

            // Aplica os cabeçalhos
            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // Estiliza os cabeçalhos
            $headerStyle = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'CCCCCC',
                    ],
                ],
            ];
            $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

            // Preenche os dados
            $row = 2;
            foreach ($composicoes as $composicao) {
                $sheet->setCellValue('A' . $row, $composicao->grupo);
                $sheet->setCellValue('B' . $row, $composicao->codigo);
                $sheet->setCellValue('C' . $row, $composicao->descricao);
                $sheet->setCellValue('D' . $row, $composicao->unidade);
                $sheet->setCellValue('E' . $row, $composicao->data_base);
                $sheet->setCellValue('F' . $row, $composicao->desoneracao === 'com' ? 'Com Desoneração' : 'Sem Desoneração');
                $sheet->setCellValue('G' . $row, number_format($composicao->valor_mao_obra, 2, ',', '.'));
                $sheet->setCellValue('H' . $row, number_format($composicao->valor_mat_equip, 2, ',', '.'));
                $sheet->setCellValue('I' . $row, number_format($composicao->valor_total, 2, ',', '.'));
                $row++;
            }

            // Ajusta a largura das colunas
            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(50);
            $sheet->getColumnDimension('D')->setWidth(10);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(15);

            // Estiliza os valores monetários
            $sheet->getStyle('G2:I' . ($row - 1))->getNumberFormat()
                ->setFormatCode('#,##0.00');

            // Cria o arquivo Excel
            $writer = new Xlsx($spreadsheet);
            $filename = 'SINAPI_' . $data_base . '_' . $desoneracao . '.xlsx';
            
            // Define os headers para download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Salva o arquivo
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            Log::error('Erro ao exportar dados SINAPI:', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'error' => 'Erro ao exportar dados',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Busca paginada para campo zoom de composições SINAPI
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function zoomServicos(Request $request)
    {
        try {
            $termo = $request->input('termo');
            $desoneracao = strtolower($request->input('desoneracao', 'com'));
            $perPage = 10;
            
            $query = DB::table('sinapi_composicoes_view')
                ->select([
                    'codigo',
                    'descricao',
                    'unidade',
                    'valor_mao_obra',
                    'valor_mat_equip',
                    'valor_total',
                    'data_base',
                    'desoneracao'
                ])
                ->whereRaw('TRIM(LOWER(desoneracao)) = ?', [trim(strtolower($desoneracao))]);
                
            if ($termo) {
                $query->where(function($q) use ($termo) {
                    $q->where('codigo', 'like', "%$termo%")
                      ->orWhere('descricao', 'like', "%$termo%");
                });
            }
            
            $servicos = $query->orderBy('codigo')->paginate($perPage);
            return response()->json($servicos);
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar serviços SINAPI para zoom:', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Erro ao buscar serviços',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
