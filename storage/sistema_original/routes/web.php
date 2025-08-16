<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\Web\CentralTarefas\CentralTarefasController;
use App\Http\Controllers\Web\TabelaOficial\ImportarDerprController;
use App\Http\Controllers\Web\Principal\LoteController;
use App\Http\Controllers\Web\Principal\OrcamentoController;
use App\Http\Controllers\Web\Principal\PrincipalController;
use App\Http\Controllers\Web\Principal\PrioridadeController;
use App\Http\Controllers\Web\Principal\ProjetoController;
use App\Http\Controllers\AndamentoProjeto\AndamentoProjetoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Controllers\Web\Preco\Consultas\ConsultarSINAPIController;
use Illuminate\Support\Facades\Log;
// Imports antigos removidos - sistema antigo substituído pelo novo sistema integrado
use App\Http\Controllers\Orcamento\BDIController;
use App\Http\Controllers\Orcamento\CotacaoController;
use App\Http\Controllers\Orcamento\ComposicaoPropriaController;
use App\Http\Controllers\Web\Transporte\CustoTransporteController;
use App\Http\Controllers\Web\Transporte\CoeficienteCustoTransporteController;
use App\Http\Controllers\Web\Transporte\CustoTransportePageController;
use App\Http\Controllers\Web\Transporte\MaterialTransporteController;
use App\Http\Controllers\Web\Transporte\DmtController;
use App\Http\Controllers\Web\Transporte\DmtDefaultController;





Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', function () {
    return view('home');
})->name('home');

/**
 * ROTAS DE AUTENTICAÇÃO
 */
// Rota de login web (renderiza a view)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');



// Rota de logout web
Route::post('/logout', function () {
    return redirect('/login');
})->name('logout');

/* TABELAS DE PREÇOS OFICIAIS */
    // ===================================================================
    // MÓDULO DE IMPORTAÇÃO DE TABELAS DER-PR
    // Todas as rotas deste grupo são para importação de tabelas oficiais DER-PR
    // ===================================================================
    Route::prefix('tabela_oficial/importar_derpr')->middleware(['auth'])->group(function () {
        // Página principal da importação DER-PR (renderiza a view com as abas)
        Route::get('/', [App\Http\Controllers\Web\TabelaOficial\ImportarDerprController::class, 'index'])->name('derpr.importar.index');
        
        // Rotas para processamento de arquivos PDF (API)
        Route::post('/servicos-gerais', [App\Http\Controllers\Api\TabelaOficial\ImportarDerprController::class, 'processarServicosGerais'])->name('derpr.importar.servicos-gerais');
        Route::post('/insumos', [App\Http\Controllers\Api\TabelaOficial\ImportarDerprController::class, 'processarInsumos'])->name('derpr.importar.insumos');
        Route::post('/formulas-transporte', [App\Http\Controllers\Api\TabelaOficial\ImportarDerprController::class, 'processarFormulasTransporte'])->name('derpr.importar.formulas-transporte');
        Route::post('/lote', [App\Http\Controllers\Api\TabelaOficial\ImportarDerprController::class, 'importarLote'])->name('derpr.importar.lote');
    });

    // ===================================================================
    // ROTAS API PARA ABA 3 (IMPORTAR LOTE) - DETECÇÃO AUTOMÁTICA
    // ===================================================================
    Route::prefix('api/tabela_oficial/importar_derpr')->middleware(['auth'])->group(function () {
        Route::get('/verificar_arquivos', [App\Http\Controllers\Api\TabelaOficial\ImportarDerprController::class, 'verificarArquivosDisponiveis'])->name('api.derpr.verificar_arquivos');
        Route::post('/gravar', [App\Http\Controllers\Api\TabelaOficial\ImportarDerprController::class, 'importarLote'])->name('api.derpr.gravar');
        Route::get('/testar-logs', [App\Http\Controllers\Api\TabelaOficial\ImportarDerprController::class, 'testarLogs'])->name('api.derpr.testar_logs');
    });

    // ===================================================================
    // MÓDULO DE IMPORTAÇÃO DE TABELAS SINAPI
    // Todas as rotas deste grupo são para importação de tabelas oficiais SINAPI
    // ===================================================================
    Route::prefix('tabela_oficial/importar_sinapi')->group(function () {
        // Página principal da importação SINAPI (renderiza a view com as abas)
        Route::get('/', [App\Http\Controllers\Web\TabelaOficial\ImportarSinapiController::class, 'index'])->name('sinapi.importar.index.novo');
    });

    // ===================================================================
    // ROTAS API PARA IMPORTAÇÃO SINAPI (REFATORADO)
    // Todas as rotas deste grupo são para processamento de dados SINAPI
    // ===================================================================
    Route::prefix('tabela_oficial/importar_sinapi')->middleware(['auth'])->group(function () {
        // Página principal da importação SINAPI (renderiza a view com as abas)
        Route::get('/', [App\Http\Controllers\Web\TabelaOficial\ImportarSinapiController::class, 'index'])->name('sinapi.importar.index.novo');
        
        // Rotas para processamento de arquivos Excel (API)
        Route::post('/composicoes_insumos', [App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'processarComposicoesInsumos'])->name('sinapi.composicoes_insumos');
        Route::post('/percentagens_mao_de_obra', [App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'processarPercentagensMaoDeObra'])->name('sinapi.percentagens_mao_de_obra');
        
        // Download de arquivos processados (composições e insumos)
        Route::get('/exportar_composicoes_insumos/{tipo}', [App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'downloadArquivoProcessado'])->name('sinapi.download_arquivo');
        
        // Download de arquivos processados (percentagens de mão de obra)
        Route::get('/download_arquivo_processado_mao_obra/{tipo}', [App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'downloadArquivoProcessadoMaoDeObra'])->name('sinapi.download_arquivo_mao_obra');
        
        // Verificação de arquivos disponíveis para gravação
        Route::get('/verificar_arquivos', [App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'verificarArquivosDisponiveis'])->name('sinapi.verificar_arquivos');
        
        // Gravação no banco de dados
        Route::post('/gravar', [App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'gravar'])->name('sinapi.gravar');
        
        // Rota de teste para verificar o sistema de logs
        Route::get('/testar-logs', [App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'testarLogs'])->name('sinapi.testar_logs');
    });


    // ===================================================================
    // MÓDULO DE CONSULTA DE TABELAS SINAPI
    // Todas as rotas deste grupo são para consulta das tabelas oficiais SINAPI
    // ===================================================================
    Route::prefix('tabela_oficial/consultar_sinapi')->group(function () {
        Route::get('/', [App\Http\Controllers\Web\TabelaOficial\ConsultarSinapiController::class, 'index'])->name('sinapi.consultar');
    });
    
    // ===================================================================
    // MÓDULO DE CONSULTA DE TABELAS DER-PR
    // Todas as rotas deste grupo são para consulta das tabelas oficiais DER-PR
    // ===================================================================
    Route::prefix('tabela_oficial/consultar_derpr')->group(function () {
        Route::get('/', [App\Http\Controllers\Web\TabelaOficial\ConsultarDerprController::class, 'index'])->name('derpr.consultar');
    });

/**
 * ROTAS DO PROTOTIPO - NÃO UTILIZADO - APENAS PARA TESTES
 */ 
        Route::get('/inicio', [PrincipalController::class, 'inicio'])->name('inicio');

        Route::get('/projeto/cotacao', [ProjetoController::class, 'cotacao'])->name('projeto.cotacao');
        Route::get('/projeto/cotacao/cadastro', [ProjetoController::class, 'cotacaoCAD'])->name('projeto.cotacao.cadastro');
        Route::get('/projeto/composicao', [ProjetoController::class, 'composicao'])->name('projeto.composicao');
        Route::get('/projeto/composicao/cadastro', [ProjetoController::class, 'composicaoCAD'])->name('projeto.composicao.cadastro');

        Route::get('/lote/cadastro/municipio', [LoteController::class, 'municipio'])->name('lote-municipio.create');
        Route::get('/lote/cadastro/prc', [LoteController::class, 'prc'])->name('lote-prc.create');
        Route::get('/lote/cadastro/analise', [LoteController::class, 'analise'])->name('lote-analise.create');
        Route::get('/lote/cadastro/conclusao', [LoteController::class, 'conclusao'])->name('lote-conclusao.create');
        
        /**
         * MENU PRINCIPAL
         */
        Route::get('/central_tarefas', [CentralTarefasController::class, 'inicio'])->name('central.tarefas');
        Route::get('/prioridade/cadastro', [PrioridadeController::class, 'create'])->name('prioridade.create');
        Route::get('/prioridade/fluxo', [PrioridadeController::class, 'fluxo'])->name('prioridade.fluxo');
        Route::get('/prioridade/documentacao', [PrioridadeController::class, 'documentacao'])->name('prioridade.documentacao');
        Route::get('/prioridade/historico', [PrioridadeController::class, 'historico'])->name('prioridade.historico');

        Route::get('/projeto/cadastro', [ProjetoController::class, 'create'])->name('projeto.create');
        Route::get('/projeto/fluxo', [ProjetoController::class, 'fluxo'])->name('projeto.fluxo');

        Route::get('/orcamento/inicio', [OrcamentoController::class, 'orcamentoInicio'])->name('orcamento.inicio');
        Route::get('/orcamento/bdi', [OrcamentoController::class, 'orcamentoBdi'])->name('orcamento.bdi');
        Route::get('/orcamento/dmt', [OrcamentoController::class, 'orcamentoDmt'])->name('orcamento.dmt');
        Route::get('/orcamento/planilha', [OrcamentoController::class, 'orcamentoPlanilha'])->name('orcamento.planilha');
        Route::get('/orcamento/resumo', [OrcamentoController::class, 'orcamentoResumo'])->name('orcamento.resumo');









/* ROTAS DE MENSAGENS DO SISTEMA */
Route::post('/mensagem/show', [App\Http\Controllers\MessageController::class, 'show'])->name('mensagem.show');
Route::post('/mensagem/clear', [App\Http\Controllers\MessageController::class, 'clear'])->name('mensagem.clear');

// Rotas para Municípios (Módulo Administração)
Route::prefix('administracao')->name('administracao.')->group(function () {
    Route::prefix('municipios')->name('municipios.')->group(function () {
        Route::get('/', [App\Http\Controllers\Web\Administracao\Municipios\MunicipioController::class, 'index'])->name('index');
    });
});

// Rotas API para Municípios (Módulo Administração)
Route::prefix('api/administracao')->name('api.administracao.')->group(function () {
    Route::prefix('municipios')->name('municipios.')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Administracao\Municipios\MunicipioController::class, 'listar'])->name('listar');
        Route::post('/', [App\Http\Controllers\Api\Administracao\Municipios\MunicipioController::class, 'store'])->name('store');
        Route::put('/{id}', [App\Http\Controllers\Api\Administracao\Municipios\MunicipioController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Api\Administracao\Municipios\MunicipioController::class, 'destroy'])->name('destroy');
        Route::post('/importar', [App\Http\Controllers\Api\Administracao\Municipios\MunicipioController::class, 'importar'])->name('importar');
    });
});

// Rotas para Entidades Orçamentárias (Módulo Administração)
Route::prefix('administracao')->name('administracao.')->group(function () {
    Route::prefix('entidades-orcamentarias')->name('entidades-orcamentarias.')->group(function () {
        Route::get('/', [App\Http\Controllers\Web\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'index'])->name('index');
    });
    
    // Estrutura de Orçamento - Rota Web
    Route::prefix('estrutura-orcamento')->name('estrutura-orcamento.')->group(function () {
        Route::get('/', [App\Http\Controllers\Web\Administracao\EstruturaOrcamento\EstruturaOrcamentoController::class, 'index'])->name('index');
    });
});

// Rotas API para Entidades Orçamentárias (Módulo Administração)
Route::prefix('api/administracao')->name('api.administracao.')->group(function () {
    Route::prefix('entidades-orcamentarias')->name('entidades-orcamentarias.')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'listar'])->name('listar');
        Route::get('/listar-select', [App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'listarSelect'])->name('listar-select');
        Route::post('/', [App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'store'])->name('store');
        Route::put('/{id}', [App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'destroy'])->name('destroy');
        Route::post('/importar-municipios', [App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'importarMunicipios'])->name('importar-municipios');
    });
    
    // Rotas API para Estrutura de Orçamento
    Route::prefix('estrutura-orcamento')->name('estrutura-orcamento.')->group(function () {
        // CRUD Tipo de Orçamento
        Route::prefix('tipo-orcamento')->name('tipo-orcamento.')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\TipoOrcamentoController::class, 'listar'])->name('listar');
            Route::post('/', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\TipoOrcamentoController::class, 'store'])->name('store');
            Route::put('/{id}', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\TipoOrcamentoController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\TipoOrcamentoController::class, 'destroy'])->name('destroy');
        });
        
        // CRUD Grande Item
        Route::prefix('grande-item')->name('grande-item.')->group(function () {
            Route::get('/{tipoOrcamentoId}', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\GrandeItemController::class, 'listarPorTipoOrcamento'])->name('listar');
            Route::post('/', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\GrandeItemController::class, 'store'])->name('store');
            Route::put('/{id}', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\GrandeItemController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\GrandeItemController::class, 'destroy'])->name('destroy');
            Route::post('/reordenar', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\GrandeItemController::class, 'reordenar'])->name('reordenar');
        });
        
        // CRUD Subgrupo
        Route::prefix('subgrupo')->name('subgrupo.')->group(function () {
            Route::get('/{grandeItemId}', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\SubGrupoController::class, 'listarPorGrandeItem'])->name('listar');
            Route::post('/', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\SubGrupoController::class, 'store'])->name('store');
            Route::put('/{id}', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\SubGrupoController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\SubGrupoController::class, 'destroy'])->name('destroy');
            Route::post('/reordenar', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\SubGrupoController::class, 'reordenar'])->name('reordenar');
        });
        
        // Importação em Massa
        Route::post('/importar', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\ImportacaoController::class, 'importar'])->name('importar');
        
        // Limpar Estrutura
        Route::delete('/limpar/{tipoOrcamentoId}', [App\Http\Controllers\Api\Administracao\EstruturaOrcamento\LimparEstruturaController::class, 'limpar'])->name('limpar');
    });
});

// SISTEMA ANTIGO DE ESTRUTURA DE ORÇAMENTO REMOVIDO - SUBSTITUÍDO PELO NOVO SISTEMA INTEGRADO


// Rotas para BDI
Route::get('/bdis', [BDIController::class, 'index'])->name('bdis.index');
Route::get('/bdis/listar', [BDIController::class, 'listar'])->name('bdis.listar');
Route::get('/bdis/create', [BDIController::class, 'create'])->name('bdis.create');
Route::post('/bdis', [BDIController::class, 'store'])->name('bdis.store');
Route::get('/bdis/{id}/edit', [BDIController::class, 'edit'])->name('bdis.edit');
Route::put('/bdis/{id}', [BDIController::class, 'update'])->name('bdis.update');
Route::delete('/bdis/{id}', [BDIController::class, 'destroy'])->name('bdis.destroy');

// Rotas para Cotações
Route::get('/cotacoes', [CotacaoController::class, 'indexView'])->name('cotacoes.index');

// Rotas para Composições Próprias
Route::get('/composicao-proprias', [ComposicaoPropriaController::class, 'index'])->name('composicao-proprias.index');
Route::get('/composicao-proprias/listar', [ComposicaoPropriaController::class, 'listar'])->name('composicao-proprias.listar');
Route::get('/composicao-proprias/create', [ComposicaoPropriaController::class, 'create'])->name('composicao-proprias.create');
Route::post('/composicao-proprias', [ComposicaoPropriaController::class, 'store'])->name('composicao-proprias.store');
Route::get('/composicao-proprias/{id}/edit', [ComposicaoPropriaController::class, 'edit'])->name('composicao-proprias.edit');
Route::put('/composicao-proprias/{id}', [ComposicaoPropriaController::class, 'update'])->name('composicao-proprias.update');
Route::delete('/composicao-proprias/{id}', [ComposicaoPropriaController::class, 'destroy'])->name('composicao-proprias.destroy');

// Rotas para composições próprias (CRUD via resource)
Route::resource('orcamento/composicao-propria', ComposicaoPropriaController::class)->except(['show']);
Route::get('/orcamento/composicao-propria/listar', [App\Http\Controllers\Orcamento\ComposicaoPropriaController::class, 'listar']);

// Rotas para Custos de Transporte
Route::prefix('transporte')->group(function () {
    Route::get('custos', [CustoTransporteController::class, 'index'])->name('transporte.custos.index');
    Route::post('custos', [CustoTransporteController::class, 'store'])->name('transporte.custos.store');
    Route::delete('custos/{id}', [CustoTransporteController::class, 'destroy'])->name('transporte.custos.destroy');

    Route::get('coeficientes', [CoeficienteCustoTransporteController::class, 'index'])->name('transporte.coeficientes.index');
    Route::post('coeficientes', [CoeficienteCustoTransporteController::class, 'store'])->name('transporte.coeficientes.store');
    Route::delete('coeficientes/{id}', [CoeficienteCustoTransporteController::class, 'destroy'])->name('transporte.coeficientes.destroy');

    Route::get('databases', [CustoTransporteController::class, 'databases'])->name('transporte.databases');
    Route::post('coeficientes/lote', [CoeficienteCustoTransporteController::class, 'storeLote'])->name('transporte.coeficientes.storeLote');

    Route::get('custos/gerenciar', [CustoTransportePageController::class, 'index'])
    ->name('transporte.custos.gerenciar');

    Route::post('/importar-coeficientes', [App\Http\Controllers\Web\Transporte\CustoTransporteController::class, 'importarCoeficientes']);

});



// Rotas para Materiais de Transporte - padrão PRC
Route::get('/dmt-default', [App\Http\Controllers\Web\Transporte\DmtDefaultController::class, 'index'])->name('dmt-default.index');
Route::get('/dmt-default/listar', [App\Http\Controllers\Web\Transporte\DmtDefaultController::class, 'listar'])->name('dmt-default.listar');
Route::post('/dmt-default', [App\Http\Controllers\Web\Transporte\DmtDefaultController::class, 'store'])->name('dmt-default.store');
Route::put('/dmt-default/{id}', [App\Http\Controllers\Web\Transporte\DmtDefaultController::class, 'update'])->name('dmt-default.update');
Route::delete('/dmt-default/{id}', [App\Http\Controllers\Web\Transporte\DmtDefaultController::class, 'destroy'])->name('dmt-default.destroy');
Route::post('/dmt-default/importar', [App\Http\Controllers\Web\Transporte\DmtDefaultController::class, 'importar'])->name('dmt-default.importar');



// DMT CRUD
Route::get('/dmt', [App\Http\Controllers\Web\Transporte\DmtController::class, 'index'])->name('dmt.index');
Route::get('/dmt/listar', [App\Http\Controllers\Web\Transporte\DmtController::class, 'listar'])->name('dmt.listar');
Route::post('/dmt', [App\Http\Controllers\Web\Transporte\DmtController::class, 'store'])->name('dmt.store');
Route::put('/dmt/{id}', [App\Http\Controllers\Web\Transporte\DmtController::class, 'update'])->name('dmt.update');
Route::delete('/dmt/{id}', [App\Http\Controllers\Web\Transporte\DmtController::class, 'destroy'])->name('dmt.destroy');
Route::get('/dmt/selects', [App\Http\Controllers\Web\Transporte\DmtController::class, 'selects'])->name('dmt.selects');
Route::post('/dmt/gerar-para-entidade', [App\Http\Controllers\Web\Transporte\DmtController::class, 'gerarDmtsParaEntidadeOrcamentaria'])->name('dmt.gerar-para-entidade');





// Configurações Gerais
Route::prefix('configuracoes-gerais')->group(function () {
    Route::get('/', [App\Http\Controllers\Web\ConfiguracoesGerais\ConfiguracaoGeralController::class, 'index'])->name('configuracoes-gerais.index');
    Route::get('/show', [App\Http\Controllers\Web\ConfiguracoesGerais\ConfiguracaoGeralController::class, 'show'])->name('configuracoes-gerais.show');
    Route::post('/', [App\Http\Controllers\Web\ConfiguracoesGerais\ConfiguracaoGeralController::class, 'store'])->name('configuracoes-gerais.store');
    Route::get('/selects', [App\Http\Controllers\Web\ConfiguracoesGerais\ConfiguracaoGeralController::class, 'selects'])->name('configuracoes-gerais.selects');
});




// ===================================================================
// PÁGINA DE ANDAMENTO DO PROJETO - TEMPORÁRIA
// Objetivo: Mostrar o progresso do desenvolvimento do sistema OrçaCidade
// ===================================================================
Route::prefix('andamento-projeto')->group(function () {
    Route::get('/', [AndamentoProjetoController::class, 'index'])->name('andamento-projeto.index');
    Route::get('/escopo', [AndamentoProjetoController::class, 'escopo'])->name('andamento-projeto.escopo');
    Route::get('/backlog-global', [AndamentoProjetoController::class, 'backlogGlobal'])->name('andamento-projeto.backlog-global');
    Route::get('/fases-e-sprints', [AndamentoProjetoController::class, 'fasesESprints'])->name('andamento-projeto.fases-e-sprints');
    Route::get('/conceitos', [AndamentoProjetoController::class, 'conceitos'])->name('andamento-projeto.conceitos');
    Route::get('/relatorios', [AndamentoProjetoController::class, 'relatorios'])->name('andamento-projeto.relatorios');
    Route::get('/relatorios/{modulo}', [AndamentoProjetoController::class, 'relatorioIndividual'])->name('andamento-projeto.relatorio.individual');
});






// ===================================================================
// MÓDULO DE DOCUMENTAÇÃO TÉCNICA
// Todas as rotas deste grupo são para exibição da documentação técnica
// ===================================================================
Route::prefix('andamento-projeto/documentacao')->group(function () {
    Route::get('/', [App\Http\Controllers\AndamentoProjeto\DocumentacaoController::class, 'index'])->name('documentacao.index');
    Route::get('/{modulo}', [App\Http\Controllers\AndamentoProjeto\DocumentacaoController::class, 'show'])->name('documentacao.show');
});

// ===================================================================
// ROTA PARA PROTÓTIPO DA PLANILHA ORÇAMENTÁRIA
// Exibe o arquivo HTML da planilha prototipo dentro do sistema
// ===================================================================
Route::get('/planilha-prototipo', [AndamentoProjetoController::class, 'planilhaPrototipo'])->name('planilha-prototipo');





// ****************************************************
// ****************************************************

// ✅ GERENCIAMENTO DE USUÁRIOS E PERMISSÕES (implementado em 2025 - FASE 1)
Route::prefix('administracao/usuarios')->group(function () {
    Route::get('/', [App\Http\Controllers\Web\Administracao\Usuarios\UsuariosController::class, 'index'])->name('administracao.usuarios.index');
});

// ✅ ROTAS API NECESSÁRIAS PARA O ADMINCENTER (implementado em 2025)
Route::prefix('api/administracao/usuarios')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\Administracao\Usuarios\UsuariosController::class, 'index'])->name('api.administracao.usuarios.index');
    Route::post('/', [App\Http\Controllers\Api\Administracao\Usuarios\UsuariosController::class, 'store'])->name('api.administracao.usuarios.store');
    Route::put('/{id}', [App\Http\Controllers\Api\Administracao\Usuarios\UsuariosController::class, 'update'])->name('api.administracao.usuarios.update');
    Route::delete('/{id}', [App\Http\Controllers\Api\Administracao\Usuarios\UsuariosController::class, 'destroy'])->name('api.administracao.usuarios.destroy');
});

Route::prefix('api/administracao/roles')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'index'])->name('api.administracao.roles.index');
    Route::post('/', [App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'store'])->name('api.administracao.roles.store');
    Route::put('/{id}', [App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'update'])->name('api.administracao.roles.update');
    Route::delete('/{id}', [App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'destroy'])->name('api.administracao.roles.destroy');
    Route::post('/{id}/permissions', [App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'updatePermissions'])->name('api.administracao.roles.permissions');
    Route::get('/{id}/users', [App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'getUsers'])->name('api.administracao.roles.users');
    Route::get('/{id}/permissions', [App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'getPermissions'])->name('api.administracao.roles.permissions.list');
    
    // ✅ NOVAS ROTAS PARA GERENCIAR USUÁRIOS DOS PAPÉIS
    Route::post('/{id}/users', [App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'addUser'])->name('api.administracao.roles.users.add');
    Route::delete('/{id}/users/{userId}', [App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'removeUser'])->name('api.administracao.roles.users.remove');
});

Route::prefix('api/administracao/permissions')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\Administracao\PermissionsController::class, 'index'])->name('api.administracao.permissions.index');
    Route::post('/', [App\Http\Controllers\Api\Administracao\PermissionsController::class, 'store'])->name('api.administracao.permissions.store');
    Route::put('/{id}', [App\Http\Controllers\Api\Administracao\PermissionsController::class, 'update'])->name('api.administracao.permissions.update');
    Route::delete('/{id}', [App\Http\Controllers\Api\Administracao\PermissionsController::class, 'destroy'])->name('api.administracao.permissions.destroy');
    
    // ✅ NOVAS ROTAS PARA FUNCIONALIDADES DA ABA PERMISSÕES
    Route::get('/{id}/roles', [App\Http\Controllers\Api\Administracao\PermissionsController::class, 'getRoles'])->name('api.administracao.permissions.roles');
    Route::get('/{id}/users', [App\Http\Controllers\Api\Administracao\PermissionsController::class, 'getUsers'])->name('api.administracao.permissions.users');
    Route::post('/{id}/roles/{roleId}', [App\Http\Controllers\Api\Administracao\PermissionsController::class, 'attachRole'])->name('api.administracao.permissions.roles.attach');
    Route::delete('/{id}/roles/{roleId}', [App\Http\Controllers\Api\Administracao\PermissionsController::class, 'detachRole'])->name('api.administracao.permissions.roles.detach');
    Route::get('/{id}/available-roles', [App\Http\Controllers\Api\Administracao\PermissionsController::class, 'availableRoles'])->name('api.administracao.permissions.available-roles');
});

// ✅ ROTA PARA BUSCA GLOBAL (implementado em 2025)
Route::get('/api/busca-global', [App\Http\Controllers\Api\Administracao\BuscaGlobalController::class, 'buscar'])->name('api.busca.global');

// ===================================================================
// MÓDULO DE PADRÕES DE INTERFACE
// Todas as rotas deste grupo são para exibição dos padrões de interface
// ===================================================================
Route::prefix('padroes')->group(function () {
    Route::get('/', [App\Http\Controllers\Web\PadroesController::class, 'index'])->name('padroes.index');
});

// ===================================================================
// MÓDULO ACTIVE DIRECTORY - INTEGRAÇÃO LDAP
// Todas as rotas deste grupo são para gestão da integração com AD
// ===================================================================
// ✅ PADRÃO CORRETO - Vue.js + API (implementado em 2025 - FASE 5)
Route::prefix('administracao/active-directory')->group(function () {
    Route::get('/', [App\Http\Controllers\Web\Administracao\ActiveDirectory\ActiveDirectoryController::class, 'index'])->name('administracao.active_directory.index');
});

// ✅ PADRÃO CORRETO - Rotas API para Vue.js (implementado em 2025 - FASE 5)
Route::prefix('api/administracao/active-directory')->group(function () {
    // Sincronização
    Route::post('/sync', [App\Http\Controllers\Api\Administracao\ActiveDirectory\SyncController::class, 'sync'])->name('api.administracao.ad.sync');
    Route::get('/sync/status', [App\Http\Controllers\Api\Administracao\ActiveDirectory\SyncController::class, 'status'])->name('api.administracao.ad.sync.status');
    Route::get('/sync/test-connection', [App\Http\Controllers\Api\Administracao\ActiveDirectory\SyncController::class, 'testConnection'])->name('api.administracao.ad.sync.test');
    
    // Configurações
    Route::get('/config', [App\Http\Controllers\Api\Administracao\ActiveDirectory\ConfigController::class, 'index'])->name('api.administracao.ad.config.index');
    Route::post('/config', [App\Http\Controllers\Api\Administracao\ActiveDirectory\ConfigController::class, 'store'])->name('api.administracao.ad.config.store');
    Route::get('/config/test', [App\Http\Controllers\Api\Administracao\ActiveDirectory\ConfigController::class, 'test'])->name('api.administracao.ad.config.test');
});
