@extends('layouts.app')

@section('title', 'Padrões de Interface - Consolidação')

@push('styles')
<!-- CSS específico para padrões já incluído no layout principal -->
@endpush

@section('content')
<div class="padroes-container">
    <!-- Header da página -->
    <div class="padroes-header">
        <h1 class="padroes-title">🎨 Consolidação de Padrões</h1>
        <p class="padroes-subtitle">Padrões existentes consolidados e documentados para evolução futura</p>
    </div>

    <!-- Seção: Documentação Atual -->
    <div class="padroes-section">
        <h2 class="section-title">📚 Documentação Atual (.md)</h2>
        <div class="component-demo">
            <div class="component-title">Padrões Documentados</div>
            <p class="mb-3">Os padrões estão documentados em: <code>docs/diretrizes/01_projeto/02_padrao_layout_interface.md</code></p>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Fonte Única da Verdade:</strong> Este documento define padrões visuais baseados na implementação real do AdminCenter.
            </div>
            
            <div class="code-example">
                <strong>Arquivo:</strong> 02_padrao_layout_interface.md<br>
                <strong>Status:</strong> ✅ Atualizado em 2025<br>
                <strong>Base:</strong> Implementação real do AdminCenter<br>
                <strong>Flexibilidade:</strong> Permite evolução futura
            </div>
        </div>
    </div>

    <!-- Seção: CSS Global Consolidado -->
    <div class="padroes-section">
        <h2 class="section-title">🎨 CSS Global Consolidado</h2>
        
        <div class="component-demo">
            <div class="component-title">Arquivo Principal: modern-interface.css</div>
            <p class="mb-3">CSS centralizado que contém todos os estilos modernos implementados no AdminCenter.</p>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>Arquivos CSS Importados:</h6>
                    <ul class="list-unstyled">
                        <li><code>applayout.css</code> - Layout geral</li>
                        <li><code>form.css</code> - Estilos de formulários</li>
                        <li><code>modern-interface.css</code> - <strong>PADRÃO PRINCIPAL</strong></li>
                        <li><code>sidebar.css</code> - Menu lateral</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Características:</h6>
                    <ul class="list-unstyled">
                        <li>✅ Sistema de abas com sombras</li>
                        <li>✅ Badges discretos (não sólidos)</li>
                        <li>✅ Tabelas modernas com hover</li>
                        <li>✅ Botões de ação com efeitos</li>
                        <li>✅ Modais personalizados</li>
                        <li>✅ Filtros dinâmicos</li>
                    </ul>
                </div>
            </div>
            
            <div class="code-example">
                <strong>Localização:</strong> resources/css/modern-interface.css<br>
                <strong>Importação:</strong> @import "modern-interface.css"<br>
                <strong>Status:</strong> ✅ Consolidado e funcional<br>
                <strong>Evolução:</strong> Pode ser atualizado quando necessário
            </div>
        </div>
    </div>

    <!-- Seção: Paleta de Cores Consolidada -->
    <div class="padroes-section">
        <h2 class="section-title">🎨 Paleta de Cores Consolidada</h2>
        
        <div class="component-demo">
            <div class="component-title">Cores Obrigatórias (Baseadas na Documentação)</div>
            <div class="color-palette">
                <div class="color-item color-success" data-tooltip="Títulos principais do card header">
                    <div>Verde Principal</div>
                    <small>#5EA853</small>
                </div>
                <div class="color-item color-primary" data-tooltip="Títulos de abas, texto custom">
                    <div>Azul Secundário</div>
                    <small>#18578A</small>
                </div>
                <div class="color-item color-warning" data-tooltip="Botão editar (amarelo sólido)">
                    <div>Warning</div>
                    <small>#ffc107</small>
                </div>
                <div class="color-item color-danger" data-tooltip="Botão excluir (vermelho sólido)">
                    <div>Danger</div>
                    <small>#dc3545</small>
                </div>
                <div class="color-item color-info" data-tooltip="Botão visualizar">
                    <div>Info</div>
                    <small>#17a2b8</small>
                </div>
                <div class="color-item color-light" data-tooltip="Fundos, cards, filtros">
                    <div>Light</div>
                    <small>#ffffff</small>
                </div>
            </div>
            
            <div class="code-example">
                <strong>Regra:</strong> NUNCA usar cores fora da paleta definida<br>
                <strong>Título Principal:</strong> style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;"<br>
                <strong>Títulos de Aba:</strong> class="text-custom" (cor #18578A)<br>
                <strong>Cabeçalhos de Tabela:</strong> class="text-custom" (NÃO usar #374151)
            </div>
        </div>
    </div>

    <!-- Seção: Sistema de Abas Consolidado -->
    <div class="padroes-section">
        <h2 class="section-title">📋 Sistema de Abas Consolidado</h2>
        
        <div class="component-demo">
            <div class="component-title">Estrutura Baseada no Componente Vue Existente</div>
            
            <!-- Demonstração do Sistema de Abas -->
            <div class="admin-tabs-container">
                <ul class="nav nav-tabs admin-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link admin-tab active" type="button">
                            <i class="fas fa-sitemap me-2"></i>Estrutura de Orçamento
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link admin-tab" type="button">
                            <i class="fas fa-sitemap me-2"></i>Estrutura Manual
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link admin-tab" type="button">
                            <i class="fas fa-eye me-2"></i>Visualização Integrada
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content admin-tab-content">
                    <div class="tab-pane fade show active" role="tabpanel">
                        <div class="py-4">
                            <h5 class="mb-1 fw-semibold text-custom">
                                <i class="fas fa-sitemap me-2"></i>Conteúdo da Aba
                            </h5>
                            <p class="text-muted mb-0">Este é o padrão de aba consolidado do sistema.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="code-example">
                <strong>Classes CSS:</strong> admin-tabs-container, admin-tabs, admin-tab, admin-tab-content<br>
                <strong>Base:</strong> Componente Vue GestaoEstruturaOrcamento.vue<br>
                <strong>Status:</strong> ✅ Funcional e consolidado<br>
                <strong>Evolução:</strong> Pode ser expandido para novas funcionalidades
            </div>
        </div>
    </div>

    <!-- Seção: Tabelas Consolidadas -->
    <div class="padroes-section">
        <h2 class="section-title">📊 Tabelas Consolidadas</h2>
        
        <div class="component-demo">
            <div class="component-title">Padrão Baseado na Documentação</div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle usuarios-table">
                    <thead>
                        <tr>
                            <th class="fw-semibold text-custom">ID</th>
                            <th class="fw-semibold text-custom">Nome</th>
                            <th class="fw-semibold text-custom">Status</th>
                            <th class="fw-semibold text-end text-custom" style="width: 150px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="usuario-row">
                            <td class="align-middle">
                                <div class="fw-medium">#001</div>
                            </td>
                            <td class="align-middle">
                                <div class="fw-medium">Usuário Administrador</div>
                            </td>
                            <td class="align-middle">
                                <span class="badge-status badge-ativo">Ativo</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <button class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="usuario-row">
                            <td class="align-middle">
                                <div class="fw-medium">#002</div>
                            </td>
                            <td class="align-middle">
                                <div class="fw-medium">Usuário Padrão</div>
                            </td>
                            <td class="align-middle">
                                <span class="badge-status badge-inativo">Inativo</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <button class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="code-example">
                <strong>Classes Obrigatórias:</strong> usuarios-table, usuario-row<br>
                <strong>Botões de Ação:</strong> btn btn-sm btn-warning (editar), btn btn-sm btn-danger (excluir)<br>
                <strong>Container:</strong> d-flex gap-1 justify-content-end<br>
                <strong>Status:</strong> ✅ Padrão consolidado e funcional
            </div>
        </div>
    </div>

    <!-- Seção: Badges Consolidados -->
    <div class="padroes-section">
        <h2 class="section-title">🏷️ Badges Consolidados (DISCRETOS)</h2>
        
        <div class="component-demo">
            <div class="component-title">Badges Discretos (NÃO Sólidos Bootstrap)</div>
            
            <div class="mb-3">
                <h6>Badges de Status:</h6>
                <span class="badge-status badge-ativo badge-demo">Ativo</span>
                <span class="badge-status badge-inativo badge-demo">Inativo</span>
                <span class="badge-status badge-pendente badge-demo">Pendente</span>
            </div>
            
            <div class="mb-3">
                <h6>Badges de Tipo:</h6>
                <span class="badge-tipo badge-local badge-demo">Local</span>
                <span class="badge-tipo badge-ad badge-demo">Active Directory</span>
                <span class="badge-papel badge-demo">Administrador</span>
            </div>
            
            <div class="code-example">
                <strong>Regra:</strong> NUNCA usar badges sólidos Bootstrap (bg-success, bg-danger)<br>
                <strong>Classes:</strong> badge-status, badge-tipo, badge-papel<br>
                <strong>Status:</strong> ✅ Padrão consolidado e funcional<br>
                <strong>Evolução:</strong> Novos tipos podem ser adicionados
            </div>
        </div>
    </div>

    <!-- Seção: Formulários Consolidados -->
    <div class="padroes-section">
        <h2 class="section-title">📝 Formulários Consolidados</h2>
        
        <div class="component-demo">
            <div class="component-title">Padrão Form-Floating (RESOLVIDO EM 2025)</div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="nome" placeholder="Nome completo">
                        <label for="nome">Nome Completo</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" placeholder="Email">
                        <label for="email">Email</label>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <h6>Labels com Padrão:</h6>
                <label class="form-label fw-semibold text-custom">
                    <i class="fas fa-user me-2"></i>Nome do Campo
                </label>
                <input type="text" class="form-control" placeholder="Digite o nome">
            </div>
            
            <div class="code-example">
                <strong>Form-Floating:</strong> CSS padrão obrigatório incluído no modern-interface.css<br>
                <strong>Labels:</strong> class="form-label fw-semibold text-custom"<br>
                <strong>Ícones:</strong> Sempre usar Font Awesome nos labels<br>
                <strong>Status:</strong> ✅ Problema resolvido e consolidado
            </div>
        </div>
    </div>

    <!-- Seção: Filtros Consolidados -->
    <div class="padroes-section">
        <h2 class="section-title">🔍 Filtros Consolidados</h2>
        
        <div class="component-demo">
            <div class="component-title">Filtros Dinâmicos com Fundo Branco</div>
            
            <div class="filtros-aba-container mb-3">
                <div class="filtros-aba-content show">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="filtroNome" 
                                       placeholder="Nome...">
                                <label for="filtroNome">Nome</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-select form-control-lg" id="filtroStatus">
                                    <option value="">Selecione...</option>
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                </select>
                                <label for="filtroStatus">Status</label>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-outline-secondary w-100" 
                                    style="height: 58px; line-height: 1.5;">
                                <i class="fas fa-times me-2"></i>Limpar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="code-example">
                <strong>Fundos:</strong> background-color: #ffffff (NÃO usar #f8f9fa)<br>
                <strong>Campos:</strong> Sempre usar form-control-lg<br>
                <strong>Botão:</strong> Altura 58px para alinhar com campos<br>
                <strong>Alinhamento:</strong> align-items-end na row<br>
                <strong>Status:</strong> ✅ Padrão consolidado e funcional
            </div>
        </div>
    </div>

    <!-- Seção: Modais Consolidados -->
    <div class="padroes-section">
        <h2 class="section-title">🪟 Modais Consolidados</h2>
        
        <div class="component-demo">
            <div class="component-title">Header com Gradiente e Z-index Automático</div>
            
            <div class="modal-demo">
                <div class="modal-header custom-modal-header">
                    <div class="d-flex align-items-center">
                        <div class="header-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <h5 class="modal-title mb-0">Novo Item</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white"></button>
                </div>
                <div class="modal-body">
                    <p>Conteúdo do modal aqui...</p>
                </div>
            </div>
            
            <div class="code-example">
                <strong>Z-index:</strong> Configurado automaticamente via CSS global<br>
                <strong>Header:</strong> Gradiente #18578A → #5EA853<br>
                <strong>Ícone:</strong> Círculo com fundo semi-transparente<br>
                <strong>Status:</strong> ✅ Problema de z-index resolvido e consolidado
            </div>
        </div>
    </div>

    <!-- Seção: Paginação Consolidada -->
    <div class="padroes-section">
        <h2 class="section-title">📄 Paginação Consolidada</h2>
        
        <div class="component-demo">
            <div class="component-title">Paginação FORA do Card Principal</div>
            
            <div class="paginacao-container mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted fw-medium">
                        Mostrando 1 até 10 de 25 registros
                    </div>
                    
                    <nav>
                        <ul class="pagination admin-pagination mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" aria-label="Anterior">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">3</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Próximo">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            
            <div class="code-example">
                <strong>Posição:</strong> SEMPRE fora do card principal<br>
                <strong>Classes:</strong> paginacao-container, admin-pagination<br>
                <strong>Ícones:</strong> fa-chevron-left, fa-chevron-right<br>
                <strong>Status:</strong> ✅ Padrão consolidado e funcional
            </div>
        </div>
    </div>

    <!-- Seção: Responsividade Consolidada -->
    <div class="padroes-section">
        <h2 class="section-title">📱 Responsividade Consolidada</h2>
        
        <div class="component-demo">
            <div class="component-title">Breakpoints para Notebook 14" e Mobile</div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>Notebook 14" (max-width: 1366px):</h6>
                    <ul>
                        <li>admin-tab-content: padding 1.5rem</li>
                        <li>admin-tabs: padding 0 1rem</li>
                        <li>admin-tab: padding 0.75rem 1rem</li>
                        <li>usuarios-table: padding 0.75rem</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Mobile (max-width: 768px):</h6>
                    <ul>
                        <li>usuarios-table: font-size 13px</li>
                        <li>usuarios-table: padding 0.5rem</li>
                        <li>btn-action: 28px x 28px</li>
                        <li>papeis-selecao: max-height 200px</li>
                    </ul>
                </div>
            </div>
            
            <div class="code-example">
                <strong>CSS:</strong> Responsividade incluída no modern-interface.css<br>
                <strong>Breakpoints:</strong> 1366px (notebook), 768px (mobile)<br>
                <strong>Status:</strong> ✅ Padrão consolidado e funcional<br>
                <strong>Evolução:</strong> Novos breakpoints podem ser adicionados
            </div>
        </div>
    </div>

    <!-- Seção: Evolução e Flexibilidade -->
    <div class="padroes-section">
        <h2 class="section-title">🚀 Evolução e Flexibilidade</h2>
        
        <div class="component-demo">
            <div class="component-title">Como Atualizar Padrões</div>
            
            <div class="alert alert-success">
                <i class="fas fa-lightbulb"></i>
                <strong>Flexibilidade:</strong> Os padrões podem ser atualizados quando necessário, mantendo a consistência.
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>Para Atualizar Padrões:</h6>
                    <ol>
                        <li>Modificar o arquivo .md correspondente</li>
                        <li>Atualizar o CSS global se necessário</li>
                        <li>Testar em todas as funcionalidades</li>
                        <li>Documentar as mudanças</li>
                    </ol>
                </div>
                <div class="col-md-6">
                    <h6>Para Adicionar Novos Padrões:</h6>
                    <ol>
                        <li>Implementar no CSS global</li>
                        <li>Documentar no arquivo .md</li>
                        <li>Testar em funcionalidades existentes</li>
                        <li>Manter consistência visual</li>
                    </ol>
                </div>
            </div>
            
            <div class="code-example">
                <strong>Princípio:</strong> Consolidação sem engessamento<br>
                <strong>Base:</strong> Padrões existentes e funcionais<br>
                <strong>Evolução:</strong> Permitida e incentivada<br>
                <strong>Consistência:</strong> Sempre mantida
            </div>
        </div>
    </div>

    <!-- Seção: Checklist de Implementação -->
    <div class="padroes-section">
        <h2 class="section-title">✅ Checklist de Implementação</h2>
        
        <div class="component-demo">
            <div class="component-title">Verificações Obrigatórias</div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>Para CRUD com Abas:</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="check1" checked disabled>
                        <label class="form-check-label" for="check1">Usar cores da paleta definida</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="check2" checked disabled>
                        <label class="form-check-label" for="check2">Usar classes CSS obrigatórias</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="check3" checked disabled>
                        <label class="form-check-label" for="check3">Implementar sistema de abas</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="check4" checked disabled>
                        <label class="form-check-label" for="check4">Usar badges discretos</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Para Funcionalidade Única:</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="check5" checked disabled>
                        <label class="form-check-label" for="check5">Usar estrutura de card único</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="check6" checked disabled>
                        <label class="form-check-label" for="check6">Implementar filtros colapsáveis</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="check7" checked disabled>
                        <label class="form-check-label" for="check7">Usar botões de ação padronizados</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="check8" checked disabled>
                        <label class="form-check-label" for="check8">Colocar paginação fora do card</label>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Importante:</strong> Sempre seguir o padrão da aba "Usuários" como referência. Se algo não está igual, está errado.
            </div>
        </div>
    </div>

    <!-- Seção: Conclusão -->
    <div class="padroes-section">
        <h2 class="section-title">🎉 Conclusão da Consolidação</h2>
        
        <div class="component-demo">
            <div class="component-title">Padrões Consolidados com Sucesso</div>
            
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <strong>Consolidação Completa:</strong> Todos os padrões existentes foram consolidados de forma flexível e evolutiva.
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>✅ O que foi consolidado:</h6>
                    <ul>
                        <li>Documentação .md existente</li>
                        <li>CSS global modern-interface.css</li>
                        <li>Sistema de abas funcional</li>
                        <li>Padrões de tabelas</li>
                        <li>Badges discretos</li>
                        <li>Formulários form-floating</li>
                        <li>Filtros dinâmicos</li>
                        <li>Modais personalizados</li>
                        <li>Paginação padronizada</li>
                        <li>Responsividade</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>🚀 Flexibilidade mantida:</h6>
                    <ul>
                        <li>Padrões podem ser atualizados</li>
                        <li>Novos padrões podem ser adicionados</li>
                        <li>Evolução é permitida</li>
                        <li>Consistência é mantida</li>
                        <li>Base sólida para crescimento</li>
                    </ul>
                </div>
            </div>
            
            <div class="code-example">
                <strong>Resultado:</strong> Sistema consolidado sem engessamento<br>
                <strong>Base:</strong> Padrões existentes e funcionais<br>
                <strong>Futuro:</strong> Flexível para evolução<br>
                <strong>Status:</strong> ✅ Consolidação completa e bem-sucedida
            </div>
        </div>
    </div>
</div>
@endsection
