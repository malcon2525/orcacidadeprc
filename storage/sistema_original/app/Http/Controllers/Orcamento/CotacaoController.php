<?php

namespace App\Http\Controllers\Orcamento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CotacaoController extends Controller
{
    public function indexView()
    {
        return view('orcamento.cotacoes.index');
    }

    /**
     * Retorna lista paginada de cotações para a API
     * GET /api/cotacoes
     */
    public function index(Request $request)
    {
        $query = \App\Models\Orcamento\Cotacao::query();
        if ($request->filled('entidade_orcamentaria_id')) {
            $query->where('entidade_orcamentaria_id', $request->entidade_orcamentaria_id);
        }
        if ($request->filled('orcamento_id')) {
            $query->where('orcamento_id', $request->orcamento_id);
        }
        $result = $query->with(['entidadeOrcamentaria', 'fornecedores.fornecedor'])->orderByDesc('id')->paginate(10);
        // Adiciona nome da entidade orçamentária e fornecedores para cada cotação
        $result->getCollection()->transform(function($item) {
            $item->entidade_orcamentaria_nome = $item->entidadeOrcamentaria ? $item->entidadeOrcamentaria->nome_fantasia : null;
            // Monta array de fornecedores para o frontend
            $item->fornecedores = collect($item->fornecedores)->map(function($f) {
                return [
                    'cnpj_cpf' => $f->fornecedor ? $f->fornecedor->cnpj_cpf : '',
                    'nome_fantasia' => $f->fornecedor ? $f->fornecedor->nome_fantasia : '',
                    'telefone' => $f->fornecedor ? $f->fornecedor->telefone : '',
                    'email' => $f->fornecedor ? $f->fornecedor->email : '',
                    'site' => $f->fornecedor ? $f->fornecedor->site : '',
                    'mao_obra' => $f->mao_obra,
                    'mat_equip' => $f->mat_equip,
                    'valor_total' => $f->valor_total,
                    'data' => $f->data,
                    'observacoes' => $f->observacoes,
                    'arquivo' => $f->arquivo,
                ];
            })->toArray();
            return $item;
        });
        Log::info('Cotações retornadas:', $result->toArray());
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $mensagens = [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto.',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres.',
            'numeric' => 'O campo :attribute deve ser um número.',
            'date' => 'O campo :attribute deve ser uma data válida.',
            'array' => 'O campo :attribute deve ser uma lista.',
            'size' => 'O campo :attribute deve conter exatamente :size itens.'
        ];
        $atributos = [
            'codigo' => 'Código',
            'descricao' => 'Descrição',
            'entidade_orcamentaria_id' => 'Entidade Orçamentária',
            'orcamento_id' => 'ID Orçamento',
            'origem' => 'Origem',
            'unidade' => 'Unidade',
            'tipo_valor_final' => 'Tipo Valor Final',
            'valor_final' => 'Valor Final',
            'fornecedores' => 'Fornecedores',
            'fornecedores.0.cnpj_cpf' => 'CNPJ/CPF do Fornecedor 1',
            'fornecedores.1.cnpj_cpf' => 'CNPJ/CPF do Fornecedor 2',
            'fornecedores.2.cnpj_cpf' => 'CNPJ/CPF do Fornecedor 3',
            'fornecedores.0.nome_fantasia' => 'Nome Fantasia do Fornecedor 1',
            'fornecedores.1.nome_fantasia' => 'Nome Fantasia do Fornecedor 2',
            'fornecedores.2.nome_fantasia' => 'Nome Fantasia do Fornecedor 3',
            'fornecedores.*.telefone' => 'Telefone do Fornecedor',
            'fornecedores.*.email' => 'E-mail do Fornecedor',
            'fornecedores.*.site' => 'Site do Fornecedor',
            'fornecedores.*.mao_obra' => 'Mão de Obra',
            'fornecedores.*.mat_equip' => 'Mat/Equip',
            'fornecedores.*.valor_total' => 'Valor Total',
            'fornecedores.*.data' => 'Data',
            'fornecedores.*.observacoes' => 'Observações',
        ];
        $validated = $request->validate([
            'codigo' => 'required|string|max:50',
            'descricao' => 'required|string|max:255',
            'entidade_orcamentaria_id' => 'required|integer',
            'orcamento_id' => 'nullable|string|max:50',
            'origem' => 'required|string|max:50',
            'unidade' => 'nullable|string|max:20',
            'tipo_valor_final' => 'required|string',
            'valor_final' => 'required|numeric',
            'fornecedores' => 'required|array|size:3',
            'fornecedores.*.cnpj_cpf' => 'required|string|max:20',
            'fornecedores.*.nome_fantasia' => 'required|string|max:255',
            'fornecedores.*.telefone' => 'nullable|string|max:30',
            'fornecedores.*.email' => 'nullable|string|max:100',
            'fornecedores.*.site' => 'nullable|string|max:100',
            'fornecedores.*.mao_obra' => 'required|numeric',
            'fornecedores.*.mat_equip' => 'required|numeric',
            'fornecedores.*.valor_total' => 'required|numeric',
            'fornecedores.*.data' => 'nullable|date',
            'fornecedores.*.observacoes' => 'nullable|string',
        ], $mensagens, $atributos);

        $data = $validated;
        if (empty($data['orcamento_id']) || $data['orcamento_id'] === 'null') {
            $data['orcamento_id'] = null;
        }
        $cotacao = \App\Models\Orcamento\Cotacao::create($data);

        // Recupera nome fantasia da entidade orçamentária para o diretório
        $entidade = \App\Models\Gerais\EntidadeOrcamentaria::find($cotacao->entidade_orcamentaria_id);
        $nomeEntidade = $entidade ? preg_replace('/[^A-Za-z0-9_]/', '_', $entidade->nome_fantasia) : 'desconhecida';

        foreach ($validated['fornecedores'] as $idx => $f) {
            // Busca ou cria fornecedor global
            $fornecedor = \App\Models\Orcamento\Fornecedor::firstOrCreate(
                ['cnpj_cpf' => $f['cnpj_cpf']],
                [
                    'nome_fantasia' => $f['nome_fantasia'],
                    'telefone' => $f['telefone'],
                    'email' => $f['email'],
                    'site' => $f['site'],
                ]
            );

            // Salva arquivo PDF se enviado
            $arquivoPath = null;
            if ($request->hasFile("fornecedores.$idx.arquivo")) {
                $arquivo = $request->file("fornecedores.$idx.arquivo");
                $nomeFornecedor = $fornecedor ? preg_replace('/[^A-Za-z0-9_]/', '_', $fornecedor->nome_fantasia) : 'fornecedor';
                $nomeArquivo = $cotacao->codigo . '-' . $nomeFornecedor . '.pdf';
                $arquivoPath = $arquivo->storeAs("cotacoes/$nomeEntidade", $nomeArquivo, 'public');
            }

            \App\Models\Orcamento\CotacaoFornecedor::create([
                'cotacao_id' => $cotacao->id,
                'fornecedor_id' => $fornecedor->id,
                'valor_total' => $f['valor_total'],
                'mao_obra' => $f['mao_obra'],
                'mat_equip' => $f['mat_equip'],
                'data' => $f['data'] ?? null,
                'arquivo' => $arquivoPath,
                'observacoes' => $f['observacoes'] ?? null,
            ]);
        }

        return response()->json(['success' => true, 'cotacao_id' => $cotacao->id]);
    }

    /**
     * Atualiza uma cotação existente
     * PUT /api/cotacoes/{id}
     */
    public function update(Request $request, $id)
    {
        $mensagens = [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto.',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres.',
            'numeric' => 'O campo :attribute deve ser um número.',
            'date' => 'O campo :attribute deve ser uma data válida.',
            'array' => 'O campo :attribute deve ser uma lista.',
            'size' => 'O campo :attribute deve conter exatamente :size itens.'
        ];
        $atributos = [
            'codigo' => 'Código',
            'descricao' => 'Descrição',
            'entidade_orcamentaria_id' => 'Entidade Orçamentária',
            'orcamento_id' => 'ID Orçamento',
            'origem' => 'Origem',
            'unidade' => 'Unidade',
            'tipo_valor_final' => 'Tipo Valor Final',
            'valor_final' => 'Valor Final',
            'fornecedores' => 'Fornecedores',
            'fornecedores.0.cnpj_cpf' => 'CNPJ/CPF do Fornecedor 1',
            'fornecedores.1.cnpj_cpf' => 'CNPJ/CPF do Fornecedor 2',
            'fornecedores.2.cnpj_cpf' => 'CNPJ/CPF do Fornecedor 3',
            'fornecedores.0.nome_fantasia' => 'Nome Fantasia do Fornecedor 1',
            'fornecedores.1.nome_fantasia' => 'Nome Fantasia do Fornecedor 2',
            'fornecedores.2.nome_fantasia' => 'Nome Fantasia do Fornecedor 3',
            'fornecedores.*.telefone' => 'Telefone do Fornecedor',
            'fornecedores.*.email' => 'E-mail do Fornecedor',
            'fornecedores.*.site' => 'Site do Fornecedor',
            'fornecedores.*.mao_obra' => 'Mão de Obra',
            'fornecedores.*.mat_equip' => 'Mat/Equip',
            'fornecedores.*.valor_total' => 'Valor Total',
            'fornecedores.*.data' => 'Data',
            'fornecedores.*.observacoes' => 'Observações',
        ];
        $validated = $request->validate([
            'codigo' => 'required|string|max:50',
            'descricao' => 'required|string|max:255',
            'entidade_orcamentaria_id' => 'required|integer',
            'orcamento_id' => 'nullable|string|max:50',
            'origem' => 'required|string|max:50',
            'unidade' => 'nullable|string|max:20',
            'tipo_valor_final' => 'required|string',
            'valor_final' => 'required|numeric',
            'fornecedores' => 'required|array|size:3',
            'fornecedores.*.cnpj_cpf' => 'required|string|max:20',
            'fornecedores.*.nome_fantasia' => 'required|string|max:255',
            'fornecedores.*.telefone' => 'nullable|string|max:30',
            'fornecedores.*.email' => 'nullable|string|max:100',
            'fornecedores.*.site' => 'nullable|string|max:100',
            'fornecedores.*.mao_obra' => 'required|numeric',
            'fornecedores.*.mat_equip' => 'required|numeric',
            'fornecedores.*.valor_total' => 'required|numeric',
            'fornecedores.*.data' => 'nullable|date',
            'fornecedores.*.observacoes' => 'nullable|string',
        ], $mensagens, $atributos);

        $data = $validated;
        if (empty($data['orcamento_id']) || $data['orcamento_id'] === 'null') {
            $data['orcamento_id'] = null;
        }
        $cotacao = \App\Models\Orcamento\Cotacao::findOrFail($id);
        $cotacao->update($data);

        // Recupera nome fantasia da entidade orçamentária para o diretório
        $entidade = \App\Models\Gerais\EntidadeOrcamentaria::find($cotacao->entidade_orcamentaria_id);
        $nomeEntidade = $entidade ? preg_replace('/[^A-Za-z0-9_]/', '_', $entidade->nome_fantasia) : 'desconhecida';

        // Salva os arquivos antigos antes de remover os fornecedores antigos
        $fornecedoresAntigos = \App\Models\Orcamento\CotacaoFornecedor::where('cotacao_id', $cotacao->id)->get()->keyBy('fornecedor_id');

        // Remove fornecedores antigos
        \App\Models\Orcamento\CotacaoFornecedor::where('cotacao_id', $cotacao->id)->delete();

        foreach ($validated['fornecedores'] as $idx => $f) {
            // Busca ou cria fornecedor global
            $fornecedor = \App\Models\Orcamento\Fornecedor::firstOrCreate(
                ['cnpj_cpf' => $f['cnpj_cpf']],
                [
                    'nome_fantasia' => $f['nome_fantasia'],
                    'telefone' => $f['telefone'],
                    'email' => $f['email'],
                    'site' => $f['site'],
                ]
            );
            $fornecedorId = $fornecedor->id;
            $arquivoAntigo = isset($fornecedoresAntigos[$fornecedorId]) ? $fornecedoresAntigos[$fornecedorId]->arquivo : null;

            $arquivoPath = null;
            $inputArquivo = $request->input("fornecedores.$idx.arquivo");

            if ($request->hasFile("fornecedores.$idx.arquivo")) {
                // Se veio arquivo novo, exclui o antigo (se houver)
                if ($arquivoAntigo) {
                    Storage::disk('public')->delete($arquivoAntigo);
                }
                $arquivo = $request->file("fornecedores.$idx.arquivo");
                $nomeFornecedor = $fornecedor ? preg_replace('/[^A-Za-z0-9_]/', '_', $fornecedor->nome_fantasia) : 'fornecedor';
                $nomeArquivo = $cotacao->codigo . '-' . $nomeFornecedor . '.pdf';
                $arquivoPath = $arquivo->storeAs("cotacoes/$nomeEntidade", $nomeArquivo, 'public');
            } else {
                // Se veio string vazia e havia arquivo antigo, exclui do storage
                if (empty($inputArquivo) && $arquivoAntigo) {
                    Storage::disk('public')->delete($arquivoAntigo);
                    $arquivoPath = null;
                } elseif (!empty($inputArquivo)) {
                    $arquivoPath = $inputArquivo;
                } else {
                    $arquivoPath = null;
                }
            }

            \App\Models\Orcamento\CotacaoFornecedor::create([
                'cotacao_id' => $cotacao->id,
                'fornecedor_id' => $fornecedor->id,
                'valor_total' => $f['valor_total'],
                'mao_obra' => $f['mao_obra'],
                'mat_equip' => $f['mat_equip'],
                'data' => $f['data'] ?? null,
                'arquivo' => $arquivoPath,
                'observacoes' => $f['observacoes'] ?? null,
            ]);
        }

        return response()->json(['success' => true, 'cotacao_id' => $cotacao->id]);
    }

    /**
     * Remove uma cotação e todos os fornecedores/arquivos relacionados
     * DELETE /api/cotacoes/{id}
     */
    public function destroy($id)
    {
        $cotacao = \App\Models\Orcamento\Cotacao::findOrFail($id);
        // Busca todos os fornecedores relacionados
        $fornecedores = \App\Models\Orcamento\CotacaoFornecedor::where('cotacao_id', $cotacao->id)->get();
        // Exclui arquivos físicos
        foreach ($fornecedores as $f) {
            if (!empty($f->arquivo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($f->arquivo);
            }
        }
        // Remove fornecedores relacionados
        \App\Models\Orcamento\CotacaoFornecedor::where('cotacao_id', $cotacao->id)->delete();
        // Remove a cotação
        $cotacao->delete();
        return response()->json(['success' => true]);
    }
} 