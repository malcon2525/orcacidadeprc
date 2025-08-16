<?php

namespace App\Http\Controllers\Api\Administracao\EstruturaOrcamento;

use App\Http\Controllers\Controller;
use App\Models\Orcamento\TipoOrcamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TipoOrcamentoController extends Controller
{
    /**
     * Lista registros com filtros e paginação
     */
    public function listar(Request $request)
    {
        $query = TipoOrcamento::query();
        
        // Aplicar filtros
        if ($request->has('descricao') && !empty($request->descricao)) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }
        
        if ($request->has('ativo') && $request->ativo !== '') {
            $query->where('ativo', $request->ativo);
        }
        
        $registros = $query->orderBy('descricao')->orderBy('versao', 'desc')->paginate(2);
        return response()->json($registros);
    }

    /**
     * Cria um novo registro
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'versao' => 'required|integer|min:1',
            'descricao' => 'required|string|max:100',
            'ativo' => 'required|boolean'
        ], [
            'versao.required' => 'A versão é obrigatória',
            'versao.integer' => 'A versão deve ser um número inteiro',
            'versao.min' => 'A versão deve ser maior que zero',
            'descricao.required' => 'A descrição é obrigatória',
            'descricao.string' => 'A descrição deve ser um texto',
            'descricao.max' => 'A descrição não pode ter mais de 100 caracteres',
            'ativo.required' => 'O status é obrigatório',
            'ativo.boolean' => 'O status deve ser ativo ou inativo'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $registro = TipoOrcamento::create($request->all());
            return response()->json($registro, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Atualiza um registro existente
     */
    public function update(Request $request, $id)
    {
        $registro = TipoOrcamento::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'versao' => 'required|integer|min:1',
            'descricao' => 'required|string|max:100',
            'ativo' => 'required|boolean'
        ], [
            'versao.required' => 'A versão é obrigatória',
            'versao.integer' => 'A versão deve ser um número inteiro',
            'versao.min' => 'A versão deve ser maior que zero',
            'descricao.required' => 'A descrição é obrigatória',
            'descricao.string' => 'A descrição deve ser um texto',
            'descricao.max' => 'A descrição não pode ter mais de 100 caracteres',
            'ativo.required' => 'O status é obrigatório',
            'ativo.boolean' => 'O status deve ser ativo ou inativo'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $registro->update($request->all());
            return response()->json($registro);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Remove um registro
     */
    public function destroy($id)
    {
        try {
            $registro = TipoOrcamento::findOrFail($id);
            $registro->delete();
            return response()->json(['message' => 'Tipo de orçamento excluído com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }
}
