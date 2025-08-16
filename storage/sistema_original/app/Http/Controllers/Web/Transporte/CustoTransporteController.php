<?php

namespace App\Http\Controllers\Web\Transporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transportes\CustoTransporte;
use Illuminate\Support\Facades\DB;
use App\Models\Transportes\CoeficienteCustoTransporte;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class CustoTransporteController extends Controller
{
    public function index()
    {
        return response()->json(CustoTransporte::orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->only(['sigla', 'codigo', 'descricao', 'unidade']);
        
        if ($request->has('id')) {
            return DB::transaction(function () use ($request, $data) {
                $custo = CustoTransporte::find($request->input('id'));
                if ($custo) {
                    $custo->update($data);
                    return response()->json($custo);
                } else {
                    $custo = CustoTransporte::create($data);
                    return response()->json($custo);
                }
            });
        } else {
            $custo = CustoTransporte::updateOrCreate(
                ['codigo' => $data['codigo']],
                $data
            );
            return response()->json($custo);
        }
    }

    public function destroy($id)
    {
        $custo = CustoTransporte::findOrFail($id);
        $custo->delete();
        return response()->json(['success' => true]);
    }

    public function databases()
    {
        $bases = DB::table('derpr_composicoes')
            ->select('data_base', 'desoneracao')
            ->distinct()
            ->orderBy('data_base', 'desc')
            ->orderBy('desoneracao')
            ->get();
        $result = $bases->map(function ($item) {
            $label = date('d/m/Y', strtotime($item->data_base)) . ' - ' . ($item->desoneracao === 'com' ? 'com desoneração' : 'sem desoneração');
            $value = $item->data_base . '|' . $item->desoneracao;
            return [
                'label' => $label,
                'value' => $value
            ];
        });
        return response()->json($result);
    }

    public function importarCoeficientes(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $header = array_map(function($h) {
            return strtolower(trim($h));
        }, $rows[1]);
        $map = array_flip($header);

        // Log para depuração do cabeçalho e mapeamento
        \Illuminate\Support\Facades\Log::info('Cabeçalho lido na importação', ['header' => $header]);
        \Illuminate\Support\Facades\Log::info('Mapeamento de colunas', ['map' => $map]);

        // Nova função para validação e preparação
        $resultado = $this->validarLinhasCoeficientesExcel($rows, $map);
        $linhasValidas = $resultado['validas'];
        $erros = $resultado['erros'];

        if (count($erros) > 0) {
            \Illuminate\Support\Facades\Log::error('Erros na importação de coeficientes', ['erros' => $erros]);
            return response()->json([
                'success' => false,
                'mensagem' => 'Erros encontrados na importação. Nenhum dado foi gravado.',
                'erros' => $erros
            ], 422);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($linhasValidas as $linha) {
                $custo = \App\Models\Transportes\CustoTransporte::updateOrCreate(
                    [ 'codigo' => $linha['codigo'] ],
                    [
                        'sigla' => $linha['sigla'],
                        'codigo' => $linha['codigo'],
                        'descricao' => $linha['descricao'],
                        'unidade' => $linha['unidade'],
                    ]
                );
                // Log detalhado antes de gravar coeficiente
                \Illuminate\Support\Facades\Log::info('Gravando coeficiente', [
                    'where' => [
                        'custo_transporte_id' => $custo->id,
                        'data_base' => $linha['data_base'],
                        'desoneracao' => $linha['desoneracao'],
                    ],
                    'update' => [
                        'coeficiente_x1' => $linha['x1'],
                        'coeficiente_x2' => $linha['x2'],
                        'termo_independente'  => $linha['k'],
                    ]
                ]);
                \App\Models\Transportes\CoeficienteCustoTransporte::updateOrCreate(
                    [
                        'custo_transporte_id' => $custo->id,
                        'data_base' => $linha['data_base'],
                        'desoneracao' => $linha['desoneracao'],
                    ],
                    [
                        'coeficiente_x1' => $linha['x1'],
                        'coeficiente_x2' => $linha['x2'],
                        'termo_independente'  => $linha['k'],
                    ]
                );
            }
            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Erro ao importar coeficientes', ['exception' => $e]);
            return response()->json([
                'success' => false,
                'mensagem' => 'Erro inesperado ao gravar os dados. Nenhum dado foi gravado.'
            ], 500);
        }
    }

    /**
     * Valida e prepara as linhas do Excel de coeficientes de transporte.
     * Regras de negócio implementadas:
     *
     * 1. Todos os campos são obrigatórios (sigla, código, descrição, unidade, x1, x2, k, data_base, desoneracao).
     * 2. Para x1, x2, k: se vier string, traço, vazio ou valor não numérico, assume 0.
     * 3. Para campos texto (sigla, código, descrição, unidade): se qualquer um estiver vazio, ERRO (não grava nada).
     * 4. Campo data_base: obrigatório, deve ser uma data válida reconhecida pelo sistema (qualquer formato aceito por DateTime/Carbon). Se faltar ou for inválida, ERRO (não grava nada).
     * 5. Campo desoneracao: obrigatório, só aceita 'com' ou 'sem' (case insensitive). Se diferente disso, ERRO (não grava nada).
     * 6. Critério de parada: parar de ler ao encontrar uma linha onde todas as colunas estejam vazias ou só com espaços.
     * 7. Se houver qualquer erro em qualquer linha, mostrar todos os erros encontrados e não gravar nada (transação).
     * 8. Só grava se todas as linhas estiverem válidas.
     * 9. Todas as exceções e erros são logados no laravel.log com detalhes.
     */
    private function validarLinhasCoeficientesExcel($rows, $map)
    {
        $erros = [];
        $linhasValidas = [];
        for ($i = 2; $i <= count($rows); $i++) {
            $row = $rows[$i];
            // Critério de parada: todas as colunas vazias ou só espaços
            $todasVazias = true;
            foreach ($row as $col) {
                if (trim((string)$col) !== '') {
                    $todasVazias = false;
                    break;
                }
            }
            if ($todasVazias) break;

            $linhaErro = [];
            $sigla_raw = $row[$map['sigla']] ?? '';
            $codigo_raw = $row[$map['código']] ?? $row[$map['codigo']] ?? '';
            $descricao_raw = $row[$map['descricao']] ?? '';
            $unidade_raw = $row[$map['unidade']] ?? '';
            $x1_raw = $row[$map['x1']] ?? '';
            $x2_raw = $row[$map['x2']] ?? '';
            $k_raw  = $row[$map['k']] ?? '';
            $data_base_raw = $row[$map['data_base']] ?? '';
            $desoneracao_raw = $row[$map['desoneracao']] ?? '';
            
            \Illuminate\Support\Facades\Log::info('Linha lida do Excel', [
                'linha' => $i,
                'sigla' => $sigla_raw,
                'codigo' => $codigo_raw,
                'descricao' => $descricao_raw,
                'unidade' => $unidade_raw,
                'x1' => $x1_raw,
                'x2' => $x2_raw,
                'k' => $k_raw,
                'data_base' => $data_base_raw,
                'desoneracao' => $desoneracao_raw
            ]);

            $sigla = trim((string)$sigla_raw);
            $codigo = trim((string)$codigo_raw);
            $descricao = trim((string)$descricao_raw);
            $unidade = trim((string)$unidade_raw);
            // Validação dos coeficientes numéricos obrigatórios
            $x1_valido = (trim($x1_raw) !== '' && is_numeric(str_replace(',', '.', trim($x1_raw))));
            $x2_valido = (trim($x2_raw) !== '' && is_numeric(str_replace(',', '.', trim($x2_raw))));
            $k_valido  = (trim($k_raw)  !== '' && is_numeric(str_replace(',', '.', trim($k_raw))));
            if (!$x1_valido) $linhaErro[] = 'x1 ausente ou inválido';
            if (!$x2_valido) $linhaErro[] = 'x2 ausente ou inválido';
            if (!$k_valido)  $linhaErro[] = 'k ausente ou inválido';
            $x1 = $x1_valido ? (float)str_replace(',', '.', trim($x1_raw)) : null;
            $x2 = $x2_valido ? (float)str_replace(',', '.', trim($x2_raw)) : null;
            $k  = $k_valido  ? (float)str_replace(',', '.', trim($k_raw))  : null;
            $data_base_trim = trim((string)$data_base_raw);
            $data_base_valida = true;
            $data_base_formatada = null;
            if ($data_base_trim === '') {
                $data_base_valida = false;
            } else {
                try {
                    $data_base_formatada = \Carbon\Carbon::parse($data_base_trim)->format('Y-m-d');
                } catch (\Exception $e) {
                    $data_base_valida = false;
                }
            }
            \Illuminate\Support\Facades\Log::info('Valor lido de data_base', [
                'linha' => $i,
                'raw' => $data_base_raw,
                'após_trim' => $data_base_trim,
                'tipo' => gettype($data_base_raw),
                'ordem_bytes' => bin2hex((string)$data_base_raw),
                'valida' => $data_base_valida,
                'formatada' => $data_base_formatada
            ]);
            $desoneracao = strtolower(trim((string)$desoneracao_raw));

            // Validação dos campos texto
            if ($sigla === '') $linhaErro[] = 'sigla vazia';
            if ($codigo === '') $linhaErro[] = 'código vazio';
            if ($descricao === '') $linhaErro[] = 'descrição vazia';
            if ($unidade === '') $linhaErro[] = 'unidade vazia';
            // Validação data_base
            if (!$data_base_valida) {
                $linhaErro[] = 'data_base ausente ou inválida (deve ser uma data válida)';
            }
            // Validação desoneracao
            if (!in_array($desoneracao, ['com', 'sem'])) {
                $linhaErro[] = "desoneracao inválida (esperado 'com' ou 'sem')";
            }
            if (count($linhaErro) > 0) {
                $erros[] = "Linha $i: " . implode('; ', $linhaErro);
                continue;
            }
            $linhasValidas[] = [
                'sigla' => $sigla,
                'codigo' => $codigo,
                'descricao' => $descricao,
                'unidade' => $unidade,
                'x1' => $x1,
                'x2' => $x2,
                'k' => $k,
                'data_base' => $data_base_formatada,
                'desoneracao' => $desoneracao,
            ];
        }
        return [ 'validas' => $linhasValidas, 'erros' => $erros ];
    }

    private function normalizarData($data)
    {
        $data = trim((string)$data);

        // Se vier como número do Excel (ex: 45321)
        if (is_numeric($data) && $data > 40000) {
            $unix = ($data - 25569) * 86400;
            return date('Y-m-d', $unix);
        }

        // Converte d/m/yyyy ou m/d/yyyy para yyyy-mm-dd
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $data, $m)) {
            $a = (int)$m[1];
            $b = (int)$m[2];
            $ano = $m[3];
            // Se o segundo número for maior que 12, ele é o dia
            if ($b > 12) {
                $dia = $b;
                $mes = $a;
            } else {
                $dia = $a;
                $mes = $b;
            }
            $dia = str_pad($dia, 2, '0', STR_PAD_LEFT);
            $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
            return "$ano-$mes-$dia";
        }
        // Se já estiver no formato yyyy-mm-dd, retorna como está
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            return $data;
        }
        return null;
    }

    private function normalizarFloat($valor)
    {
        $valor = trim((string)$valor);
        if ($valor === '' || $valor === '-' || $valor === ' - ' || $valor === null) {
            return 0.0;
        }
        $valor = str_replace(',', '.', $valor);
        if (!is_numeric($valor)) {
            return 0.0;
        }
        return (float)$valor;
    }
} 