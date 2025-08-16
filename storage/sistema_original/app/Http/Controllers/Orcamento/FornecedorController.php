<?php

namespace App\Http\Controllers\Orcamento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orcamento\Fornecedor;

class FornecedorController extends Controller
{
    /**
     * Busca fornecedor por CNPJ/CPF
     * GET /api/fornecedores/buscar-por-documento?documento=xxx
     */
    public function buscarPorDocumento(Request $request)
    {
        $documento = $request->get('documento');
        if (!$documento) {
            return response()->json(['error' => 'Documento não informado'], 400);
        }
        $fornecedor = Fornecedor::where('cnpj_cpf', $documento)->first();
        if ($fornecedor) {
            return response()->json($fornecedor);
        } else {
            return response()->json(['error' => 'Fornecedor não encontrado'], 404);
        }
    }

    /**
     * Busca fornecedores para autocomplete (zoom) por CNPJ/CPF ou nome_fantasia
     * GET /api/fornecedores/buscar-select?termo=xxx
     */
    public function buscarSelect(Request $request)
    {
        $termo = $request->get('termo');
        $perPage = $request->get('per_page', 10);
        $query = Fornecedor::query();
        if ($termo) {
            $query->where(function($q) use ($termo) {
                $q->where('cnpj_cpf', 'like', "%$termo%")
                  ->orWhere('nome_fantasia', 'like', "%$termo%") ;
            });
        }
        $result = $query->orderBy('nome_fantasia')->paginate($perPage);
        return response()->json($result);
    }

    /**
     * Cadastra um novo fornecedor
     * POST /api/fornecedores
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cnpj_cpf' => 'required|string|max:20',
            'nome_fantasia' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:30',
            'email' => 'nullable|string|max:100',
            'site' => 'nullable|string|max:100',
        ]);
        $fornecedor = Fornecedor::create($validated);
        return response()->json($fornecedor, 201);
    }
} 