<?php

use App\Http\Controllers\Api\Usuario\UsuarioController;
use App\Http\Controllers\Api\Importacao\ImportarDERPRController;

use App\Http\Controllers\Api\TabelaOficial\DerprController as DerprControllerTabelaOficial;
use App\Http\Controllers\Api\TabelaOficial\SinapiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Municipio\MunicipioController;
use App\Http\Controllers\Web\Gerais\EntidadeOrcamentariaController;
use App\Http\Controllers\Orcamento\FornecedorController;
use App\Http\Controllers\Orcamento\CotacaoController;
use App\Http\Controllers\Web\Preco\Consultas\ConsultarSINAPIController;

use App\Http\Controllers\Web\Transporte\DmtController;
use App\Http\Controllers\Api\DerprTransporteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas de autenticação (REVISADO)
Route::post('login', [UsuarioController::class, 'login']);
Route::post('logout', [UsuarioController::class, 'logout'])->middleware('jwt.auth');
Route::get('me', [UsuarioController::class, 'me'])->middleware('jwt.auth');



// Rotas da API para Municípios





//// ROTAS PARA O MÓDULO DE COTAÇÕES
// Rotas da API para Fornecedores
Route::get('fornecedores/buscar-por-documento', [FornecedorController::class, 'buscarPorDocumento']);
Route::get('fornecedores/buscar-select', [FornecedorController::class, 'buscarSelect']);
Route::post('fornecedores', [FornecedorController::class, 'store']);

// Rotas da API para Cotações
Route::post('cotacoes', [CotacaoController::class, 'store']);
Route::get('cotacoes', [CotacaoController::class, 'index']);
Route::put('cotacoes/{id}', [CotacaoController::class, 'update']);
Route::delete('cotacoes/{id}', [CotacaoController::class, 'destroy']);

//// FIM - ROTAS PARA O MÓDULO DE COTAÇÕES



// Rotas GET paginadas para zoom de serviços SINAPI e DERPR
Route::get('derpr/servicos/zoom', [App\Http\Controllers\Api\TabelaOficial\ConsultarDerprController::class, 'zoomServicos']);





// Rotas PUT para atualização em lote (dmt) (usado no DMT CRUD, botão "Gerar DMTs para Entidade")
Route::put('/dmt/atualizar-em-lote', [DmtController::class, 'atualizarEmLote']);





// Rotas para cálculo de transporte DER-PR
Route::prefix('preco/derpr/transporte')->group(function () {
    Route::get('formulas', [DerprTransporteController::class, 'obterFormulas']);
    Route::post('calcular', [DerprTransporteController::class, 'calcular']);
    Route::get('itens', [DerprTransporteController::class, 'itensPorComposicao']);
});

// ===================================================================
// ROTAS API PARA CONSULTA DER-PR (REFATORADO)
// ===================================================================
Route::prefix('tabela_oficial/consultar_derpr')->group(function () {
    Route::get('/tabelas', [App\Http\Controllers\Api\TabelaOficial\ConsultarDerprController::class, 'buscarTabelas'])->name('api.derpr.consultar.tabelas');
    Route::get('/dados', [App\Http\Controllers\Api\TabelaOficial\ConsultarDerprController::class, 'buscarDados'])->name('api.derpr.consultar.dados');
});

// ===================================================================
// ROTAS API PARA CONSULTA SINAPI (REFATORADO)
// ===================================================================
Route::prefix('tabela_oficial/consultar_sinapi')->group(function () {
    Route::get('/tabelas', [App\Http\Controllers\Api\TabelaOficial\ConsultarSinapiController::class, 'buscarTabelas'])->name('api.sinapi.consultar.tabelas');
    Route::get('/dados', [App\Http\Controllers\Api\TabelaOficial\ConsultarSinapiController::class, 'buscarDados'])->name('api.sinapi.consultar.dados');
    Route::get('/exportar-excel', [App\Http\Controllers\Api\TabelaOficial\ConsultarSinapiController::class, 'exportarExcel'])->name('api.sinapi.consultar.exportar-excel');
    Route::get('/zoom-servicos', [App\Http\Controllers\Api\TabelaOficial\ConsultarSinapiController::class, 'zoomServicos'])->name('api.sinapi.consultar.zoom-servicos');
});

// ===================================================================
// ROTAS API PARA IMPORTAÇÃO SINAPI (REFATORADO)
// Todas as rotas deste grupo são para processamento de dados SINAPI
// ===================================================================
Route::prefix('tabela_oficial/importar_sinapi')->group(function () {
    // Processamento de composições e insumos SINAPI
    Route::post('/composicoes_insumos', [App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'processarComposicoesInsumos'])->name('api.sinapi.composicoes_insumos');
    
    // Download de arquivos processados (composições e insumos)
    Route::get('/exportar_composicoes_insumos/{tipo}', [App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'downloadArquivoProcessado'])->name('api.sinapi.download_arquivo');
    
    // Processamento de percentagens de mão de obra
    Route::post('/percentagens_mao_de_obra', [App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'processarPercentagensMaoDeObra'])->name('api.sinapi.percentagens_mao_de_obra');
    
    // Download de arquivos processados (percentagens de mão de obra)
    Route::get('/download_arquivo_processado_mao_obra/{tipo}', [App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'downloadArquivoProcessadoMaoDeObra'])->name('api.sinapi.download_arquivo_mao_obra');
    
    // Verificação de arquivos disponíveis para gravação
    Route::get('/verificar_arquivos', [App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'verificarArquivosDisponiveis'])->name('api.sinapi.verificar_arquivos');
    
    // Gravação no banco de dados
    Route::post('/gravar', [App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'gravar'])->name('api.sinapi.gravar');
});



// SISTEMA DE ADMINISTRAÇÃO ANTIGO REMOVIDO - Substituído pelo novo padrão em routes/web.php



