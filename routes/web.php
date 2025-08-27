<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ===================================================================
// ROTAS PÚBLICAS (SEM AUTENTICAÇÃO)
// ===================================================================

// Rota pública - página inicial
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// ===== ROTAS WEB - INTERFACE =====
Route::get('/login', [App\Http\Controllers\Web\Auth\AuthController::class, 'showLoginForm'])->name('login');

// ===== ROTAS API - DADOS =====
Route::prefix('api/auth')->name('api.auth.')->group(function () {
    Route::post('/login', [App\Http\Controllers\Api\Auth\AuthController::class, 'login'])->name('login');
    Route::get('/me', [App\Http\Controllers\Api\Auth\AuthController::class, 'me'])->middleware('auth')->name('me');
});

// ===== ROTA DE LOGOUT WEB =====
Route::post('/logout', [App\Http\Controllers\Api\Auth\AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ===== ROTAS PÚBLICAS - ANDAMENTO DO PROJETO =====

// PÁGINA DE ANDAMENTO DO PROJETO
Route::prefix('andamento-projeto')->group(function () {
    Route::get('/', [\App\Http\Controllers\AndamentoProjeto\AndamentoProjetoController::class, 'index'])->name('andamento-projeto.index');
    Route::get('/escopo', [\App\Http\Controllers\AndamentoProjeto\AndamentoProjetoController::class, 'escopo'])->name('andamento-projeto.escopo');
    Route::get('/backlog-global', [\App\Http\Controllers\AndamentoProjeto\AndamentoProjetoController::class, 'backlogGlobal'])->name('andamento-projeto.backlog-global');
    Route::get('/fases-e-sprints', [\App\Http\Controllers\AndamentoProjeto\AndamentoProjetoController::class, 'fasesESprints'])->name('andamento-projeto.fases-e-sprints');
    Route::get('/conceitos', [\App\Http\Controllers\AndamentoProjeto\AndamentoProjetoController::class, 'conceitos'])->name('andamento-projeto.conceitos');
    Route::get('/relatorios', [\App\Http\Controllers\AndamentoProjeto\AndamentoProjetoController::class, 'relatorios'])->name('andamento-projeto.relatorios');
    Route::get('/relatorios/{modulo}', [\App\Http\Controllers\AndamentoProjeto\AndamentoProjetoController::class, 'relatorioIndividual'])->name('andamento-projeto.relatorio.individual');
});

// ===== ROTAS DE DOCUMENTAÇÃO =====
// Route::prefix('andamento-projeto/documentacao')->group(function () {
//     Route::get('/', [\App\Http\Controllers\AndamentoProjeto\DocumentacaoController::class, 'index'])->name('documentacao.index');
//     Route::get('/{modulo}', [\App\Http\Controllers\AndamentoProjeto\DocumentacaoController::class, 'show'])->name('documentacao.show');
// });

// ===== ROTA PARA PROTÓTIPO DA PLANILHA ORÇAMENTÁRIA =====
Route::get('/planilha-prototipo', [\App\Http\Controllers\AndamentoProjeto\AndamentoProjetoController::class, 'planilhaPrototipo'])->name('planilha-prototipo');

// ===== ROTAS PÚBLICAS - SOLICITAÇÃO DE CADASTRO =====
Route::prefix('solicitar-cadastro')->name('publico.solicitar-cadastro.')->group(function () {
    Route::get('/', function () {
        return view('publico.solicitar-cadastro-simple');
    })->name('index');
});

// ===== ROTAS API PÚBLICAS - SOLICITAÇÃO DE CADASTRO =====
Route::prefix('api/publico/solicitar-cadastro')->name('api.publico.solicitar-cadastro.')->group(function () {
    Route::get('/dados-formulario', [\App\Http\Controllers\Web\Publico\SolicitacaoCadastro\SolicitacaoCadastroController::class, 'dadosFormulario'])->name('dados-formulario');
    Route::post('/', [\App\Http\Controllers\Web\Publico\SolicitacaoCadastro\SolicitacaoCadastroController::class, 'store'])->name('store');
    Route::get('/consultar-status', [\App\Http\Controllers\Web\Publico\SolicitacaoCadastro\SolicitacaoCadastroController::class, 'consultarStatus'])->name('consultar-status');
});



// ===================================================================
// ROTAS PROTEGIDAS (REQUEREM AUTENTICAÇÃO)
// ===================================================================

Route::middleware(['auth'])->group(function () {
    
    // ===== ROTAS WEB - INTERFACE =====
    
    // Home (dashboard principal)
    Route::get('/home', function () {
        return view('home');
    })->name('home');
    
    // ===== MÓDULO: PERFIL =====
    // Rotas Web - Interface
    Route::prefix('perfil')->name('perfil.')->group(function () {
        Route::prefix('meu-perfil')->name('meu-perfil.')->group(function () {
            Route::get('/', [App\Http\Controllers\Web\Perfil\MeuPerfil\MeuPerfilController::class, 'index'])
                ->name('index');
        });
    });
    
    // Rotas API - Dados
    Route::prefix('api/perfil')->name('api.perfil.')->group(function () {
        Route::prefix('meu-perfil')->name('meu-perfil.')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\Perfil\MeuPerfil\MeuPerfilController::class, 'dados'])
                ->name('dados');
            Route::put('/dados-pessoais', [App\Http\Controllers\Api\Perfil\MeuPerfil\MeuPerfilController::class, 'atualizarDados'])
                ->name('atualizar-dados');
            Route::put('/senha', [App\Http\Controllers\Api\Perfil\MeuPerfil\MeuPerfilController::class, 'alterarSenha'])
                ->name('alterar-senha');
        });
    });
    
    // ===== ROTAS ADMINISTRATIVAS - INTERFACE =====
    
    // Prefixo para todas as rotas administrativas
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Usuários
        Route::resource('usuarios', \App\Http\Controllers\Web\Administracao\Usuarios\UsuariosController::class);
        
        // Papéis (Roles)
        Route::resource('papeis', \App\Http\Controllers\Web\Administracao\Roles\RolesController::class);
        
        // Permissões
        Route::resource('permissoes', \App\Http\Controllers\Web\Administracao\Permissions\PermissionsController::class);
        
        // Active Directory
        Route::prefix('active-directory')->name('active-directory.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\Administracao\ActiveDirectory\ActiveDirectoryController::class, 'index'])->name('index');
            Route::get('/config', [\App\Http\Controllers\Web\Administracao\ActiveDirectory\ActiveDirectoryController::class, 'config'])->name('config');
            Route::get('/sync', [\App\Http\Controllers\Web\Administracao\ActiveDirectory\ActiveDirectoryController::class, 'sync'])->name('sync');
        });
        
        // Municípios
        Route::resource('municipios', \App\Http\Controllers\Web\Administracao\Municipios\MunicipioController::class);
        
        // Entidades Orçamentárias
        Route::resource('entidades-orcamentarias', \App\Http\Controllers\Web\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class);
        
        // Estrutura de Orçamento
        Route::prefix('estrutura-orcamento')->name('estrutura-orcamento.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\Administracao\EstruturaOrcamento\EstruturaOrcamentoController::class, 'index'])->name('index');
        });
        
        // Aprovação de Cadastros
        Route::prefix('aprovacao-cadastros')->name('aprovacao-cadastros.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\Administracao\AprovacaoCadastros\AprovacaoCadastrosController::class, 'index'])->name('index');
        });
        
        // Usuários por Entidade
        Route::prefix('usuarios-por-entidade')->name('usuarios-por-entidade.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\Administracao\UsuariosPorEntidade\UsuariosPorEntidadeController::class, 'index'])->name('index');
        });
        

    });
    
    // ===== MÓDULO: ORÇAMENTO =====
    // Rotas Web - Interface
    Route::prefix('orcamento')->name('orcamento.')->group(function () {
        Route::prefix('composicoes-proprias')->name('composicoes-proprias.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\Orcamento\ComposicaoPropria\ComposicaoPropriaController::class, 'index'])->name('index');
        });
    });
    
    // ===== ROTAS TABELAS OFICIAIS =====
    
    // DER-PR
    Route::prefix('tabela_oficial/importar_derpr')->name('derpr.importar.')->middleware(['auth'])->group(function () {
        // Página principal da importação DER-PR (renderiza a view com as abas)
        Route::get('/', [\App\Http\Controllers\Web\TabelaOficial\ImportarDerprController::class, 'index'])->name('index');
        
        // Rotas para processamento de arquivos PDF (API)
        Route::post('/servicos-gerais', [\App\Http\Controllers\Api\TabelaOficial\ImportarDerprController::class, 'processarServicosGerais'])->name('servicos-gerais');
        Route::post('/insumos', [\App\Http\Controllers\Api\TabelaOficial\ImportarDerprController::class, 'processarInsumos'])->name('insumos');
        Route::post('/formulas-transporte', [\App\Http\Controllers\Api\TabelaOficial\ImportarDerprController::class, 'processarFormulasTransporte'])->name('formulas-transporte');
        Route::post('/lote', [\App\Http\Controllers\Api\TabelaOficial\ImportarDerprController::class, 'importarLote'])->name('lote');
    });
    
    // ===================================================================
    // ROTAS API PARA ABA 4 (IMPORTAR LOTE) - DETECÇÃO AUTOMÁTICA
    // ===================================================================
    Route::prefix('api/tabela_oficial/importar_derpr')->middleware(['auth'])->group(function () {
        Route::get('/verificar_arquivos', [\App\Http\Controllers\Api\TabelaOficial\ImportarDerprController::class, 'verificarArquivosDisponiveis'])->name('api.derpr.verificar_arquivos');
        Route::post('/gravar', [\App\Http\Controllers\Api\TabelaOficial\ImportarDerprController::class, 'importarLote'])->name('api.derpr.gravar');
        Route::get('/testar-logs', [\App\Http\Controllers\Api\TabelaOficial\ImportarDerprController::class, 'testarLogs'])->name('api.derpr.testar_logs');
    });
    
    // ===================================================================
    // MÓDULO DE IMPORTAÇÃO DE TABELAS SINAPI
    // Todas as rotas deste grupo são para importação de tabelas oficiais SINAPI
    // ===================================================================
    
    // Rota Web para interface (sem prefixo api)
    Route::prefix('tabela_oficial/importar_sinapi')->name('sinapi.importar.')->middleware(['auth'])->group(function () {
        // Página principal da importação SINAPI (renderiza a view com as abas)
        Route::get('/', [\App\Http\Controllers\Web\TabelaOficial\ImportarSinapiController::class, 'index'])->name('index');
    });
    
    // Rotas API para processamento de dados (com prefixo api)
    Route::prefix('api/tabela_oficial/importar_sinapi')->name('api.sinapi.importar.')->middleware(['auth'])->group(function () {
        // Rotas para processamento de arquivos Excel (API)
        Route::post('/composicoes_insumos', [\App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'processarComposicoesInsumos'])->name('composicoes_insumos');
        Route::post('/percentagens_mao_de_obra', [\App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'processarPercentagensMaoDeObra'])->name('percentagens_mao_de_obra');
        
        // Download de arquivos processados (composições e insumos)
        Route::get('/exportar_composicoes_insumos/{tipo}', [\App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'downloadArquivoProcessado'])->name('download_arquivo');
        
        // Download de arquivos processados (percentagens de mão de obra)
        Route::get('/download_arquivo_processado_mao_obra/{tipo}', [\App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'downloadArquivoProcessadoMaoDeObra'])->name('download_arquivo_mao_obra');
        
        // Verificação de arquivos disponíveis para gravação
        Route::get('/verificar_arquivos', [\App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'verificarArquivosDisponiveis'])->name('verificar_arquivos');
        
        // Gravação no banco de dados
        Route::post('/gravar', [\App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'gravar'])->name('gravar');
        
        // Rota de teste para verificar o sistema de logs
        Route::get('/testar-logs', [\App\Http\Controllers\Api\TabelaOficial\ImportarSinapiController::class, 'testarLogs'])->name('testar_logs');
    });
    
    // ===================================================================
    // MÓDULO DE CONSULTA DE TABELAS SINAPI
    // Todas as rotas deste grupo são para consulta de tabelas oficiais SINAPI
    // ===================================================================
    
    // Rota Web para interface (sem prefixo api)
    Route::prefix('tabela_oficial/consultar_sinapi')->name('sinapi.consultar.')->middleware(['auth'])->group(function () {
        // Página principal da consulta SINAPI (renderiza a view com o componente Vue)
        Route::get('/', [\App\Http\Controllers\Web\TabelaOficial\ConsultarSinapi\ConsultarSinapiController::class, 'index'])->name('index');
    });
    
    // Rotas API para consulta de dados (com prefixo api)
    Route::prefix('api/tabela_oficial/consultar_sinapi')->name('api.sinapi.consultar.')->middleware(['auth'])->group(function () {
        // Rotas para consulta de dados
        Route::get('/buscar_tabelas', [\App\Http\Controllers\Api\TabelaOficial\ConsultarSinapi\ConsultarSinapiController::class, 'buscarTabelas'])->name('buscar_tabelas');
        Route::get('/buscar_dados', [\App\Http\Controllers\Api\TabelaOficial\ConsultarSinapi\ConsultarSinapiController::class, 'buscarDados'])->name('buscar_dados');
        Route::get('/exportar_excel', [\App\Http\Controllers\Api\TabelaOficial\ConsultarSinapi\ConsultarSinapiController::class, 'exportarExcel'])->name('exportar_excel');
        Route::get('/zoom_servicos', [\App\Http\Controllers\Api\TabelaOficial\ConsultarSinapi\ConsultarSinapiController::class, 'zoomServicos'])->name('zoom_servicos');
    });

    // ===================================================================
    // MÓDULO DE CONSULTA DE TABELAS DER-PR
    // Todas as rotas deste grupo são para consulta de tabelas oficiais DER-PR
    // ===================================================================
    
    // Rota Web para interface (sem prefixo api)
    Route::prefix('tabela_oficial/consultar_derpr')->name('derpr.consultar.')->middleware(['auth'])->group(function () {
        // Página principal da consulta DER-PR (renderiza a view com o componente Vue)
        Route::get('/', [\App\Http\Controllers\Web\TabelaOficial\ConsultarDerpr\ConsultarDerprController::class, 'index'])->name('index');
    });
    
    // Rotas API para consulta de dados (com prefixo api)
    Route::prefix('api/tabela_oficial/consultar_derpr')->name('api.derpr.consultar.')->middleware(['auth'])->group(function () {
        // Rotas para consulta de dados
        Route::get('/buscar_tabelas', [\App\Http\Controllers\Api\TabelaOficial\ConsultarDerpr\ConsultarDerprController::class, 'buscarTabelas'])->name('buscar_tabelas');
        Route::get('/buscar_dados', [\App\Http\Controllers\Api\TabelaOficial\ConsultarDerpr\ConsultarDerprController::class, 'buscarDados'])->name('buscar_dados');
        Route::get('/exportar_excel', [\App\Http\Controllers\Api\TabelaOficial\ConsultarDerpr\ConsultarDerprController::class, 'exportarExcel'])->name('exportar_excel');
        Route::get('/zoom_servicos', [\App\Http\Controllers\Api\TabelaOficial\ConsultarDerpr\ConsultarDerprController::class, 'zoomServicos'])->name('zoom_servicos');
    });


    
    // ===== ROTAS API - DADOS =====
    
    // API para dados do usuário (session-based) - compatibilidade com componentes Vue
    Route::get('/user-data', function () {
        if (\Illuminate\Support\Facades\Auth::check()) {
            return response()->json([
                'id' => \Illuminate\Support\Facades\Auth::user()->id,
                'name' => \Illuminate\Support\Facades\Auth::user()->name,
                'email' => \Illuminate\Support\Facades\Auth::user()->email,
                'authenticated' => true
            ]);
        }
        
        return response()->json(['authenticated' => false], 401);
    });
    






    // ===== ROTAS API ADMINISTRATIVAS - OPERAÇÕES COM BANCO DE DADOS =====
    
    // Prefixo para todas as APIs administrativas
    Route::prefix('api/administracao')->name('api.administracao.')->group(function () {
        
        // Usuários
        Route::apiResource('usuarios', \App\Http\Controllers\Api\Administracao\Usuarios\UsuariosController::class);
        Route::get('usuarios/{id}/roles', [\App\Http\Controllers\Api\Administracao\Usuarios\UsuariosController::class, 'getRoles']);
        Route::post('usuarios/{id}/roles', [\App\Http\Controllers\Api\Administracao\Usuarios\UsuariosController::class, 'addRole']);
        Route::delete('usuarios/{id}/roles/{roleId}', [\App\Http\Controllers\Api\Administracao\Usuarios\UsuariosController::class, 'removeRole']);
        Route::get('usuarios/roles/all', [\App\Http\Controllers\Api\Administracao\Usuarios\UsuariosController::class, 'getAllRoles']);
        
        // Papéis (Roles)
        Route::apiResource('papeis', \App\Http\Controllers\Api\Administracao\Roles\RolesController::class);
        Route::post('papeis/{id}/permissions', [\App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'updatePermissions'])->name('papeis.permissions.update');
        Route::get('papeis/{id}/permissions', [\App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'getPermissions'])->name('papeis.permissions.get');
        Route::get('papeis/{id}/permissions/available', [\App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'getAvailablePermissions'])->name('papeis.permissions.available');
        // Usuários por papel
        Route::get('papeis/{id}/users', [\App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'getUsers'])->name('papeis.users.get');
        Route::post('papeis/{id}/users', [\App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'addUser'])->name('papeis.users.add');
        Route::delete('papeis/{id}/users/{userId}', [\App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'removeUser'])->name('papeis.users.remove');
        
        // Permissões
        Route::apiResource('permissoes', \App\Http\Controllers\Api\Administracao\PermissionsController::class);
        Route::get('permissoes/{id}/roles', [\App\Http\Controllers\Api\Administracao\PermissionsController::class, 'getRoles'])->name('permissoes.roles.get');
        Route::get('permissoes/{id}/users', [\App\Http\Controllers\Api\Administracao\PermissionsController::class, 'getUsers'])->name('permissoes.users.get');
        Route::post('permissoes/{permissionId}/roles/{roleId}/attach', [\App\Http\Controllers\Api\Administracao\PermissionsController::class, 'attachRole'])->name('permissoes.roles.attach');
        Route::delete('permissoes/{permissionId}/roles/{roleId}/detach', [\App\Http\Controllers\Api\Administracao\PermissionsController::class, 'detachRole'])->name('permissoes.roles.detach');
        Route::get('permissoes/role/{roleId}', [\App\Http\Controllers\Api\Administracao\PermissionsController::class, 'getByRole'])->name('permissoes.roles.by-role');
        Route::post('permissoes/role/{roleId}/sync', [\App\Http\Controllers\Api\Administracao\PermissionsController::class, 'syncRolePermissions'])->name('permissoes.roles.sync');
        
        // Busca Global
        Route::get('busca-global', [\App\Http\Controllers\Api\Administracao\BuscaGlobalController::class, 'buscar'])->name('busca-global');
        
        // Active Directory
        Route::prefix('active-directory')->name('active-directory.')->group(function () {
            // Configurações
            Route::get('config', [\App\Http\Controllers\Api\Administracao\ActiveDirectory\ConfigController::class, 'index'])->name('config.index');
            Route::post('config', [\App\Http\Controllers\Api\Administracao\ActiveDirectory\ConfigController::class, 'store'])->name('config.store');
            Route::get('config/test', [\App\Http\Controllers\Api\Administracao\ActiveDirectory\ConfigController::class, 'test'])->name('config.test');
            
            // Sincronização
            Route::post('sync', [\App\Http\Controllers\Api\Administracao\ActiveDirectory\SyncController::class, 'sync'])->name('sync.execute');
            Route::get('sync/status', [\App\Http\Controllers\Api\Administracao\ActiveDirectory\SyncController::class, 'status'])->name('sync.status');
            Route::get('sync/test-connection', [\App\Http\Controllers\Api\Administracao\ActiveDirectory\SyncController::class, 'testConnection'])->name('sync.test-connection');
        });
        
        // Municípios
        Route::apiResource('municipios', \App\Http\Controllers\Api\Administracao\Municipios\MunicipioController::class);
        Route::get('municipios/listar-simples', [\App\Http\Controllers\Api\Administracao\Municipios\MunicipioController::class, 'listarSimples'])->name('municipios.listar-simples');
        Route::post('municipios/importar', [\App\Http\Controllers\Api\Administracao\Municipios\MunicipioController::class, 'importar'])->name('municipios.importar');
        
        // Entidades Orçamentárias - Rotas manuais para controle total
        Route::prefix('entidades-orcamentarias')->name('entidades-orcamentarias.')->group(function () {
            // Rotas específicas primeiro
            Route::get('listar-select', [\App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'listarSelect'])->name('listar-select');
            Route::post('importar-municipios', [\App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'importarMunicipios'])->name('importar-municipios');
            Route::get('buscar-municipios', [\App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'buscarMunicipios'])->name('buscar-municipios');
            Route::get('buscar-estados', [\App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'buscarEstados'])->name('buscar-estados');
            
            // Rotas CRUD manuais
            Route::get('/', [\App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'listar'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'store'])->name('store');
            Route::get('{id}', [\App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'show'])->name('show');
            Route::put('{id}', [\App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'update'])->name('update');
            Route::delete('{id}', [\App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias\EntidadeOrcamentariaController::class, 'destroy'])->name('destroy');
        });
        
        // Estrutura de Orçamento
        Route::prefix('estrutura-orcamento')->name('estrutura-orcamento.')->group(function () {
            // Tipos de Orçamento
            Route::apiResource('tipo-orcamento', \App\Http\Controllers\Api\Administracao\EstruturaOrcamento\TipoOrcamentoController::class);
            
            // Grandes Itens
            Route::apiResource('grande-item', \App\Http\Controllers\Api\Administracao\EstruturaOrcamento\GrandeItemController::class);
            Route::get('grande-item/{tipoOrcamentoId}/por-tipo', [\App\Http\Controllers\Api\Administracao\EstruturaOrcamento\GrandeItemController::class, 'listarPorTipoOrcamento'])->name('grande-item.por-tipo');
            Route::post('grande-item/reordenar', [\App\Http\Controllers\Api\Administracao\EstruturaOrcamento\GrandeItemController::class, 'reordenar'])->name('grande-item.reordenar');
            
            // Subgrupos
            Route::apiResource('subgrupo', \App\Http\Controllers\Api\Administracao\EstruturaOrcamento\SubGrupoController::class);
            Route::get('subgrupo/{grandeItemId}/por-grande-item', [\App\Http\Controllers\Api\Administracao\EstruturaOrcamento\SubGrupoController::class, 'listarPorGrandeItem'])->name('subgrupo.por-grande-item');
            Route::post('subgrupo/reordenar', [\App\Http\Controllers\Api\Administracao\EstruturaOrcamento\SubGrupoController::class, 'reordenar'])->name('subgrupo.reordenar');
            
            // Importação
            Route::post('importar', [\App\Http\Controllers\Api\Administracao\EstruturaOrcamento\ImportacaoController::class, 'importar'])->name('importar');
            
            // Limpar Estrutura
            Route::delete('limpar/{tipoOrcamentoId}', [\App\Http\Controllers\Api\Administracao\EstruturaOrcamento\LimparEstruturaController::class, 'limpar'])->name('limpar');
        });
        
        // Aprovação de Cadastros
        Route::prefix('aprovacao-cadastros')->name('aprovacao-cadastros.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Administracao\AprovacaoCadastros\AprovacaoCadastrosController::class, 'listar'])->name('listar');
            Route::post('/{id}/aprovar', [\App\Http\Controllers\Api\Administracao\AprovacaoCadastros\AprovacaoCadastrosController::class, 'aprovar'])->name('aprovar');
            Route::post('/{id}/rejeitar', [\App\Http\Controllers\Api\Administracao\AprovacaoCadastros\AprovacaoCadastrosController::class, 'rejeitar'])->name('rejeitar');
            Route::get('/filtros', [\App\Http\Controllers\Api\Administracao\AprovacaoCadastros\AprovacaoCadastrosController::class, 'filtros'])->name('filtros');
        });
        
        // Usuários por Entidade
        Route::prefix('usuarios-por-entidade')->name('usuarios-por-entidade.')->group(function () {
            Route::get('/entidades', [\App\Http\Controllers\Api\Administracao\UsuariosPorEntidade\UsuariosPorEntidadeController::class, 'listarEntidades'])->name('listar-entidades');
            Route::get('/{entidadeId}/usuarios', [\App\Http\Controllers\Api\Administracao\UsuariosPorEntidade\UsuariosPorEntidadeController::class, 'listarUsuariosPorEntidade'])->name('listar-usuarios');
            Route::get('/{entidadeId}/usuarios-disponiveis', [\App\Http\Controllers\Api\Administracao\UsuariosPorEntidade\UsuariosPorEntidadeController::class, 'listarUsuariosDisponiveis'])->name('usuarios-disponiveis');
            Route::post('/{entidadeId}/vincular', [\App\Http\Controllers\Api\Administracao\UsuariosPorEntidade\UsuariosPorEntidadeController::class, 'vincularUsuario'])->name('vincular');
            Route::delete('/{entidadeId}/desvincular/{userId}', [\App\Http\Controllers\Api\Administracao\UsuariosPorEntidade\UsuariosPorEntidadeController::class, 'desvincularUsuario'])->name('desvincular');
            Route::post('/{entidadeId}/reativar/{userId}', [\App\Http\Controllers\Api\Administracao\UsuariosPorEntidade\UsuariosPorEntidadeController::class, 'reativarVinculo'])->name('reativar');
            Route::get('/filtros', [\App\Http\Controllers\Api\Administracao\UsuariosPorEntidade\UsuariosPorEntidadeController::class, 'filtros'])->name('filtros');
        });
        

    });







    // ===== ROTAS API ORÇAMENTO - OPERAÇÕES COM BANCO DE DADOS =====
    
    // Prefixo para todas as APIs de orçamento
    Route::prefix('api/orcamento')->name('api.orcamento.')->group(function () {
        
        // Composições Próprias
        Route::prefix('composicao-propria')->name('composicao-propria.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Orcamento\ComposicaoPropria\ComposicaoPropriaController::class, 'listar'])->name('listar');
            Route::post('/', [\App\Http\Controllers\Api\Orcamento\ComposicaoPropria\ComposicaoPropriaController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\Api\Orcamento\ComposicaoPropria\ComposicaoPropriaController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Api\Orcamento\ComposicaoPropria\ComposicaoPropriaController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Api\Orcamento\ComposicaoPropria\ComposicaoPropriaController::class, 'destroy'])->name('destroy');
        });
        
    });

});