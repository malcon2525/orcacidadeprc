<?php

namespace App\Http\Controllers\Web\ConfiguracoesGerais;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracoesGerais\ConfiguracaoGeral;
use App\Models\Gerais\EntidadeOrcamentaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfiguracaoGeralController extends Controller
{
    // Exibe a view principal
    public function index()
    {
        $usuario = Auth::user();
        return view('configuracoes_gerais.index', [
            'nomeUsuario' => $usuario->name
        ]);
    }

    // Retorna a configuração do usuário logado (JSON)
    public function show()
    {
        $userId = Auth::id();
        $config = ConfiguracaoGeral::where('user_id', $userId)->first();
        return response()->json($config);
    }

    // Cria ou atualiza a configuração do usuário logado
    public function store(Request $request)
    {
        $userId = Auth::id();
        $validated = $request->validate([
            'entidade_orcamentaria_id' => 'required|exists:entidades_orcamentarias,id',
            'data_base_derpr' => 'required|date',
            'derpr_desoneracao' => 'required|in:com,sem',
            'data_base_sinapi' => 'required|date',
            'sinapi_desoneracao' => 'required|in:com,sem',
        ]);
        $validated['user_id'] = $userId;
        $config = ConfiguracaoGeral::updateOrCreate(
            ['user_id' => $userId],
            $validated
        );
        return response()->json($config);
    }

    // Retorna selects auxiliares (entidades, datas-base, etc)
    public function selects()
    {
        $entidades = EntidadeOrcamentaria::orderBy('razao_social')->get(['id', 'razao_social as nome']);
        // Aqui você pode buscar as datas-base e opções de desoneração disponíveis nas tabelas DERPR e SINAPI
        // Exemplo fictício:
        $datasBaseDerpr = [
            ['data' => '2024-04-30', 'desoneracao' => 'com'],
            ['data' => '2024-04-30', 'desoneracao' => 'sem'],
        ];
        $datasBaseSinapi = [
            ['data' => '2025-03-01', 'desoneracao' => 'com'],
            ['data' => '2025-03-01', 'desoneracao' => 'sem'],
        ];
        return response()->json([
            'entidades_orcamentarias' => $entidades,
            'datas_base_derpr' => $datasBaseDerpr,
            'datas_base_sinapi' => $datasBaseSinapi,
        ]);
    }
} 