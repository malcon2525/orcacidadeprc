<?php

namespace App\Http\Controllers\Web\Transporte;

use App\Http\Controllers\Controller;
use App\Models\Transportes\DmtDefault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Imports\MaterialTransporteImport;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DmtDefaultController extends Controller
{
    /**
     * Exibe a listagem do recurso
     */
    public function index()
    {
        return view('transporte.dmt-default.index');
    }

    /**
     * Retorna os dados para a listagem
     */
    public function listar(Request $request)
    {
        Log::info('Iniciando listagem de materiais de transporte');
        
        $query = DmtDefault::query();

        // Ordenação por destino
        $query->orderBy('destino', 'asc');

        // Paginação
        $result = $query->paginate(100);
        
        Log::info('Dados encontrados:', ['total' => $result->total()]);
        
        return response()->json($result);
    }

    /**
     * Armazena um novo recurso
     */
    public function store(Request $request)
    {
        Log::info('Iniciando criação de material de transporte', $request->all());
        
        $mensagens = [
            'required' => 'O campo :attribute é obrigatório',
            'string' => 'O campo :attribute deve ser um texto',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres',
            'numeric' => 'O campo :attribute deve ser um número',
            'decimal' => 'O campo :attribute deve ser um número decimal',
            'in' => 'O campo :attribute deve ser um dos valores: :values'
        ];

        $atributos = [
            'codigo_material' => 'código do material',
            'nome_material' => 'nome do material',
            'origem' => 'origem',
            'destino' => 'destino',
            'sigla_transporte' => 'sigla do transporte',
            'x1' => 'X1',
            'x2' => 'X2',
            'tipo' => 'tipo'
        ];

        $validated = $request->validate([
            'codigo_material' => 'required|string|max:10',
            'nome_material' => 'required|string|max:255',
            'origem' => 'nullable|string|max:150',
            'destino' => 'nullable|string|max:150',
            'sigla_transporte' => 'required|string|max:3',
            'x1' => 'required|numeric',
            'x2' => 'required|numeric',
            'tipo' => 'required|in:local,comercial'
        ], $mensagens, $atributos);

        $item = DmtDefault::create($validated);
        
        Log::info('Material de transporte criado com sucesso', ['id' => $item->id]);

        return response()->json($item, 201);
    }

    /**
     * Atualiza um recurso existente
     */
    public function update(Request $request, $id)
    {
        Log::info('Iniciando atualização de material de transporte', ['id' => $id, 'dados' => $request->all()]);
        
        $mensagens = [
            'required' => 'O campo :attribute é obrigatório',
            'string' => 'O campo :attribute deve ser um texto',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres',
            'numeric' => 'O campo :attribute deve ser um número',
            'decimal' => 'O campo :attribute deve ser um número decimal',
            'in' => 'O campo :attribute deve ser um dos valores: :values'
        ];

        $atributos = [
            'codigo_material' => 'código do material',
            'nome_material' => 'nome do material',
            'origem' => 'origem',
            'destino' => 'destino',
            'sigla_transporte' => 'sigla do transporte',
            'x1' => 'X1',
            'x2' => 'X2',
            'tipo' => 'tipo'
        ];

        $item = DmtDefault::findOrFail($id);

        $validated = $request->validate([
            'codigo_material' => 'required|string|max:10',
            'nome_material' => 'required|string|max:255',
            'origem' => 'nullable|string|max:150',
            'destino' => 'nullable|string|max:150',
            'sigla_transporte' => 'required|string|max:3',
            'x1' => 'required|numeric',
            'x2' => 'required|numeric',
            'tipo' => 'required|in:local,comercial'
        ], $mensagens, $atributos);

        $item->update($validated);
        
        Log::info('Material de transporte atualizado com sucesso', ['id' => $id]);

        return response()->json($item);
    }

    /**
     * Remove um recurso
     */
    public function destroy($id)
    {
        Log::info('Iniciando exclusão de material de transporte', ['id' => $id]);
        
        $item = DmtDefault::findOrFail($id);
        $item->delete();
        
        Log::info('Material de transporte excluído com sucesso', ['id' => $id]);

        return response()->json(null, 204);
    }

    /**
     * Importa materiais de transporte a partir de um arquivo Excel
     */
    public function importar(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        // Log do cabeçalho para debug
        Log::info('Cabeçalho do arquivo:', ['header' => $rows[1]]);

        // Descobrir os índices das colunas pelo cabeçalho
        $header = array_map(function($value) {
            return strtolower(trim($value));
        }, $rows[1]);
        
        Log::info('Cabeçalho processado:', ['header' => $header]);
        
        $map = array_flip($header);
        Log::info('Mapeamento de colunas:', ['map' => $map]);

        // Verificar se todas as colunas necessárias existem
        $requiredColumns = ['codigo_material', 'nome_material', 'sigla_transporte', 'tipo', 'x1', 'x2'];
        $missingColumns = array_diff($requiredColumns, array_keys($map));
        
        if (!empty($missingColumns)) {
            Log::error('Colunas obrigatórias não encontradas:', ['missing' => $missingColumns, 'available' => array_keys($map)]);
            return response()->json([
                'success' => false,
                'message' => 'Colunas obrigatórias não encontradas: ' . implode(', ', $missingColumns) . 
                            '. Colunas disponíveis: ' . implode(', ', array_keys($map))
            ], 400);
        }

        $imported = 0;
        $updated = 0;
        $errors = [];

        for ($i = 2; $i <= count($rows); $i++) {
            $row = $rows[$i];
            
            // Pular linhas vazias
            if (empty($row[$map['codigo_material']]) || empty($row[$map['nome_material']])) {
                continue;
            }

            try {
                $data = [
                    'codigo_material' => trim($row[$map['codigo_material']]),
                    'nome_material' => trim($row[$map['nome_material']]),
                    'origem' => isset($row[$map['origem']]) ? trim($row[$map['origem']]) : null,
                    'destino' => isset($row[$map['destino']]) ? trim($row[$map['destino']]) : null,
                    'sigla_transporte' => trim($row[$map['sigla_transporte']]),
                    'tipo' => strtolower(trim($row[$map['tipo']])),
                    'x1' => isset($row[$map['x1']]) ? (float) str_replace(',', '.', $row[$map['x1']]) : 0,
                    'x2' => isset($row[$map['x2']]) ? (float) str_replace(',', '.', $row[$map['x2']]) : 0
                ];

                // Validar dados
                $validator = Validator::make($data, [
                    'codigo_material' => 'required|string|max:10',
                    'nome_material' => 'required|string|max:255',
                    'origem' => 'nullable|string|max:150',
                    'destino' => 'nullable|string|max:150',
                    'sigla_transporte' => 'required|string|max:3',
                    'tipo' => 'required|in:local,comercial',
                    'x1' => 'required|numeric',
                    'x2' => 'required|numeric'
                ]);

                if ($validator->fails()) {
                    $errors[] = "Linha $i: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $item = DmtDefault::where('codigo_material', $data['codigo_material'])
                    ->where('nome_material', $data['nome_material'])
                    ->first();

                if ($item) {
                    $item->update($data);
                    $updated++;
                } else {
                    DmtDefault::create($data);
                    $imported++;
                }
            } catch (\Exception $e) {
                Log::error('Erro ao processar linha ' . $i, [
                    'error' => $e->getMessage(),
                    'row' => $row
                ]);
                $errors[] = "Linha $i: " . $e->getMessage();
            }
        }

        $message = "Importação concluída. $imported registros importados e $updated registros atualizados.";
        if (!empty($errors)) {
            $message .= "\nErros encontrados:\n" . implode("\n", $errors);
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
} 