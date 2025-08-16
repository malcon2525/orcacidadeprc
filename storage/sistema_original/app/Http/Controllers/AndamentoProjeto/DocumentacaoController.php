<?php

namespace App\Http\Controllers\AndamentoProjeto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentacaoController extends Controller
{
    public function index()
    {
        // Carrega o arquivo JSON com todos os módulos
        $jsonPath = storage_path('app/documentacao-tecnica-json/modulos.json');
        
        if (!file_exists($jsonPath)) {
            abort(404, 'Arquivo de módulos não encontrado');
        }
        
        $modulosData = json_decode(file_get_contents($jsonPath), true);
        
        return view('andamento-projeto.documentacao.index', compact('modulosData'));
    }
    
    public function show($modulo)
    {
        // Carrega o arquivo JSON específico do módulo
        $jsonPath = storage_path("app/documentacao-tecnica-json/{$modulo}.json");
        
        if (!file_exists($jsonPath)) {
            abort(404, 'Documentação do módulo não encontrada');
        }
        
        $documentacao = json_decode(file_get_contents($jsonPath), true);
        
        return view('andamento-projeto.documentacao.show', compact('documentacao'));
    }
} 