<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho Padrão -->
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                    <i class="fas fa-user-check me-2"></i>Aprovação de Cadastros
                </h6>
                <div class="d-flex gap-2">
                    <!-- Botão Filtros -->
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" @click="toggleFiltros">
                        <i class="fas fa-filter"></i>
                        <span>Filtros</span>
                        <i class="fas" :class="filtrosVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <!-- Botão Atualizar -->
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" @click="carregarDados">
                        <i class="fas fa-sync-alt"></i>
                        <span>Atualizar</span>
                    </button>
                </div>
            </div>

            <!-- Corpo do Card -->
            <div class="card-body">
                <!-- Filtros Colapsáveis -->
                <div class="filtros-aba-container mb-3" v-if="filtrosVisiveis">
                    <div class="filtros-aba-content" :class="{ 'show': filtrosVisiveis }">
                        <div class="row g-3 align-items-end">
                            <!-- Nome do Visitante -->
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           id="nome" 
                                           v-model="filtros.nome"
                                           placeholder="Buscar por nome..."
                                           @keyup.enter="filtrarDados"
                                           @input="onBuscaInput">
                                    <label for="nome">Nome</label>
                                </div>
                            </div>
                            
                            <!-- Email do Visitante -->
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           v-model="filtros.email"
                                           placeholder="Buscar por email..."
                                           @keyup.enter="filtrarDados"
                                           @input="onBuscaInput">
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            
                            <!-- Entidade Orçamentária -->
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-control" id="filtroEntidade" v-model="filtros.entidade_id">
                                        <option value="">Todas as entidades</option>
                                        <option v-for="entidade in entidades" :key="entidade.id" :value="entidade.id">
                                            {{ entidade.nome }}
                                        </option>
                                    </select>
                                    <label for="filtroEntidade">Entidade Orçamentária</label>
                                </div>
                            </div>
                            
                            <!-- Tipo de Organização -->
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-control" id="filtroTipo" v-model="filtros.tipo_organizacao">
                                        <option value="">Todos os tipos</option>
                                        <option v-for="tipo in tiposOrganizacao" :key="tipo" :value="tipo">
                                            {{ tipo }}
                                        </option>
                                    </select>
                                    <label for="filtroTipo">Tipo</label>
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-control" id="filtroStatus" v-model="filtros.status">
                                        <option value="">Todos</option>
                                        <option value="pendente">Pendente</option>
                                        <option value="aprovado">Aprovado</option>
                                        <option value="rejeitado">Rejeitado</option>
                                    </select>
                                    <label for="filtroStatus">Status</label>
                                </div>
                            </div>
                            
                            <!-- UF do Visitante -->
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           id="visitante_uf" 
                                           v-model="filtros.visitante_uf"
                                           placeholder="Ex: PR, SP..."
                                           maxlength="2"
                                           style="text-transform: uppercase"
                                           @keyup.enter="filtrarDados"
                                           @input="onBuscaInput">
                                    <label for="visitante_uf">UF</label>
                                </div>
                            </div>
                            
                            <!-- Botões de Ação dos Filtros -->
                            <div class="col-md-2">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary" @click="filtrarDados">
                                        <i class="fas fa-search me-2"></i>Filtrar
                                    </button>
                                    <button class="btn btn-secondary" @click="limparFiltros">
                                        <i class="fas fa-times me-2"></i>Limpar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando solicitações...</p>
                </div>
                
                <!-- Tabela -->
                <div v-else-if="dados.length > 0" class="table-responsive">
                    <table class="table table-hover align-middle" style="min-width: 800px;">
                        <thead>
                            <tr>
                                <th class="table-header">Visitante</th>
                                <th class="table-header">Localização</th>
                                <th class="table-header">Entidade Solicitada</th>
                                <th class="table-header">Status</th>
                                <th class="table-header">Data</th>
                                <th class="table-header">Processado por</th>
                                <th class="table-header text-end" style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="solicitacao in dados" :key="solicitacao.id" class="table-row">
                                <td class="table-cell">
                                    <div class="fw-medium">{{ solicitacao.visitante_nome }}</div>
                                    <small class="text-muted">{{ solicitacao.visitante_email }}</small>
                                    <div v-if="solicitacao.visitante_cargo" class="text-muted" style="font-size: 0.75rem;">
                                        {{ solicitacao.visitante_cargo }}
                                    </div>
                                </td>
                                <td class="table-cell">
                                    <div class="fw-medium">{{ solicitacao.visitante_municipio }}</div>
                                    <small class="text-muted">{{ solicitacao.visitante_uf }}</small>
                                </td>
                                <td class="table-cell">
                                    <div class="fw-medium">{{ solicitacao.entidade_orcamentaria?.jurisdicao_nome_fantasia }}</div>
                                    <small class="text-muted">{{ solicitacao.entidade_orcamentaria?.tipo_organizacao }} - {{ solicitacao.entidade_orcamentaria?.nivel_administrativo }}</small>
                                </td>
                                <td class="table-cell">
                                    <span class="badge badge-status" :class="getStatusClass(solicitacao.status)">
                                        <i class="fas" :class="getStatusIcon(solicitacao.status)"></i>
                                        {{ getStatusLabel(solicitacao.status) }}
                                    </span>
                                </td>
                                <td class="table-cell">
                                    <div class="fw-medium">{{ formatarData(solicitacao.data_solicitacao) }}</div>
                                    <small class="text-muted" v-if="solicitacao.data_aprovacao">
                                        Processado: {{ formatarData(solicitacao.data_aprovacao) }}
                                    </small>
                                </td>
                                <td class="table-cell">
                                    <div v-if="solicitacao.aprovado_por?.name" style="max-width: 180px;">
                                        <div class="fw-medium">{{ solicitacao.aprovado_por.name }}</div>
                                        <small class="text-muted" v-if="solicitacao.aprovado_por.email">{{ solicitacao.aprovado_por.email }}</small>
                                    </div>
                                    <small v-else class="text-muted fst-italic">
                                        Aguardando processamento
                                    </small>
                                </td>
                                <td class="table-cell text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <!-- Botão Visualizar -->
                                        <button class="btn btn-sm btn-info" @click="visualizarSolicitacao(solicitacao)" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <!-- Botões de Ação (apenas para pendentes) -->
                                        <template v-if="solicitacao.status === 'pendente'">
                                            <button class="btn btn-sm btn-success" @click="aprovarSolicitacao(solicitacao)" title="Aprovar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" @click="rejeitarSolicitacao(solicitacao)" title="Rejeitar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Estado Vazio -->
                <div v-else class="text-center py-5">
                    <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                    <h6 class="mt-3 text-muted">Nenhuma solicitação encontrada</h6>
                    <p class="text-muted">Não há solicitações de cadastro com os filtros aplicados.</p>
                </div>
            </div>
        </div>

        <!-- PAGINAÇÃO FORA DO CARD - OBRIGATÓRIO -->
        <div v-if="registros.last_page > 1" class="paginacao-container mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Informações de Registros -->
                <div class="text-muted fw-medium">
                    Mostrando {{ ((registros.current_page - 1) * registros.per_page) + 1 }} 
                    até {{ Math.min(registros.current_page * registros.per_page, registros.total) }} 
                    de {{ registros.total }} registros
                </div>
                
                <!-- Navegação -->
                <nav>
                    <ul class="pagination pagination-generic mb-0">
                        <li class="page-item" :class="{ disabled: registros.current_page === 1 }">
                            <button class="page-link" @click="mudarPagina(registros.current_page - 1)" :disabled="registros.current_page === 1">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        </li>
                        
                        <li v-for="page in getPaginasVisiveis()" :key="page" class="page-item" :class="{ active: page === registros.current_page }">
                            <button class="page-link" @click="mudarPagina(page)">{{ page }}</button>
                        </li>
                        
                        <li class="page-item" :class="{ disabled: registros.current_page === registros.last_page }">
                            <button class="page-link" @click="mudarPagina(registros.current_page + 1)" :disabled="registros.current_page === registros.last_page">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Modal de Visualização -->
    <div class="modal fade" id="modalVisualizacao" tabindex="-1" ref="modalVisualizacaoRef">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header" style="background: linear-gradient(135deg, #18578A 0%, #5EA853 100%); color: white; border-bottom: none; padding: 1.5rem; border-radius: 0.5rem 0.5rem 0 0;">
                    <div class="d-flex align-items-center">
                        <div class="header-icon" style="width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 1.2rem; color: white;">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h5 class="modal-title mb-0">Detalhes da Solicitação</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
                </div>
                
                <!-- Body do Modal -->
                <div class="modal-body" style="padding: 1.5rem;">
                    <div v-if="solicitacaoSelecionada">
                        <!-- Dados do Visitante -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #5EA853;">
                                <i class="fas fa-user me-2"></i>Dados do Visitante
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoSelecionada.visitante_nome"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Nome Completo</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" 
                                               class="form-control" 
                                               :value="solicitacaoSelecionada.visitante_email"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Email</label>
                                    </div>
                                </div>
                                <div class="col-md-6" v-if="solicitacaoSelecionada.visitante_telefone">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoSelecionada.visitante_telefone"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Telefone</label>
                                    </div>
                                </div>
                                <div class="col-md-6" v-if="solicitacaoSelecionada.visitante_cargo">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoSelecionada.visitante_cargo"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Cargo</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Localização do Visitante -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #5EA853;">
                                <i class="fas fa-map-marker-alt me-2"></i>Localização do Visitante
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoSelecionada.visitante_municipio"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Município</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoSelecionada.visitante_uf"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>UF</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Entidade Solicitada -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #5EA853;">
                                <i class="fas fa-building me-2"></i>Entidade Orçamentária Solicitada
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoSelecionada.entidade_orcamentaria?.jurisdicao_nome_fantasia"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Entidade Orçamentária</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoSelecionada.entidade_orcamentaria?.tipo_organizacao"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Tipo de Organização</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoSelecionada.entidade_orcamentaria?.nivel_administrativo"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Nível Administrativo</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Justificativa -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #5EA853;">
                                <i class="fas fa-comment-alt me-2"></i>Justificativa
                            </h6>
                            <div class="form-floating">
                                <textarea class="form-control" 
                                          :value="solicitacaoSelecionada.justificativa"
                                          readonly
                                          style="height: 120px; background-color: #f8f9fa; cursor: default; resize: none;"
                                          placeholder="Justificativa do solicitante"></textarea>
                                <label>Justificativa para o acesso</label>
                            </div>
                        </div>

                        <!-- Informações da Solicitação -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #5EA853;">
                                <i class="fas fa-info-circle me-2"></i>Informações da Solicitação
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="getStatusLabel(solicitacaoSelecionada.status)"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Status</label>
                                    </div>
                                    <div class="text-center mt-1">
                                        <span class="badge badge-status" :class="getStatusClass(solicitacaoSelecionada.status)">
                                            <i class="fas" :class="getStatusIcon(solicitacaoSelecionada.status)"></i>
                                            {{ getStatusLabel(solicitacaoSelecionada.status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="formatarData(solicitacaoSelecionada.data_solicitacao)"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Data da Solicitação</label>
                                    </div>
                                </div>
                                <div class="col-md-4" v-if="solicitacaoSelecionada.data_aprovacao">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="formatarData(solicitacaoSelecionada.data_aprovacao)"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Data de Processamento</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informações de Processamento -->
                        <div v-if="solicitacaoSelecionada.aprovado_por || solicitacaoSelecionada.data_aprovacao" class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #5EA853;">
                                <i class="fas fa-user-check me-2"></i>Informações do Processamento
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6" v-if="solicitacaoSelecionada.data_aprovacao">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="formatarData(solicitacaoSelecionada.data_aprovacao)"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Data do Processamento</label>
                                    </div>
                                </div>
                                <div class="col-md-6" v-if="solicitacaoSelecionada.aprovado_por">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoSelecionada.aprovado_por.name + (solicitacaoSelecionada.aprovado_por.email ? ' (' + solicitacaoSelecionada.aprovado_por.email + ')' : '')"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Processado por</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Observações (se houver) -->
                        <div v-if="solicitacaoSelecionada.observacoes_aprovacao" class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #5EA853;">
                                <i class="fas fa-clipboard-list me-2"></i>Observações
                            </h6>
                            <div class="form-floating">
                                <textarea class="form-control" 
                                          :value="solicitacaoSelecionada.observacoes_aprovacao"
                                          readonly
                                          style="height: 100px; background-color: #f8f9fa; cursor: default; resize: none;"
                                          placeholder="Observações da aprovação"></textarea>
                                <label>Observações do Processamento</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer do Modal -->
                <div class="modal-footer" style="background: transparent; border: none; padding: 1.5rem; justify-content: center;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 0.375rem; font-weight: 500; padding: 0.75rem 1.5rem; border: none; font-size: 1rem;">
                        <i class="fas fa-times me-2"></i>Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Aprovação -->
    <div class="modal fade" id="modalAprovacao" tabindex="-1" ref="modalAprovacaoRef">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header" style="background: linear-gradient(135deg, #18578A 0%, #5EA853 100%); color: white; border-bottom: none; padding: 1.5rem; border-radius: 0.5rem 0.5rem 0 0;">
                    <div class="d-flex align-items-center">
                        <div class="header-icon" style="width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 1.2rem; color: white;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h5 class="modal-title mb-0">Aprovar Solicitação</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
                </div>
                
                <!-- Body do Modal -->
                <div class="modal-body" style="padding: 1.5rem;">
                    <div v-if="solicitacaoParaAprovar">
                        <!-- Dados do Visitante (Somente Leitura) -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #5EA853;">
                                <i class="fas fa-user me-2"></i>Dados do Visitante
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoParaAprovar.visitante_nome"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Nome Completo</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" 
                                               class="form-control" 
                                               :value="solicitacaoParaAprovar.visitante_email"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Email</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoParaAprovar.visitante_municipio + '/' + solicitacaoParaAprovar.visitante_uf"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Localização</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoParaAprovar.entidade_orcamentaria?.jurisdicao_nome_fantasia"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Entidade Solicitada</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Justificativa (Somente Leitura) -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #5EA853;">
                                <i class="fas fa-comment-alt me-2"></i>Justificativa do Solicitante
                            </h6>
                            <div class="form-floating">
                                <textarea class="form-control" 
                                          :value="solicitacaoParaAprovar.justificativa"
                                          readonly
                                          style="height: 100px; background-color: #f8f9fa; cursor: default; resize: none;"
                                          placeholder="Justificativa do solicitante"></textarea>
                                <label>Justificativa para o acesso</label>
                            </div>
                        </div>

                        <!-- Observações (EDITÁVEL) -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #5EA853;">
                                <i class="fas fa-clipboard-list me-2"></i>Observações da Aprovação
                            </h6>
                            <div class="form-floating">
                                <textarea class="form-control" 
                                          id="observacoesAprovacao"
                                          v-model="formAprovacao.observacoes_aprovacao"
                                          placeholder="Observações sobre a aprovação..."
                                          style="height: 100px; resize: none;"></textarea>
                                <label for="observacoesAprovacao">Observações (opcional)</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer do Modal -->
                <div class="modal-footer" style="background: transparent; border: none; padding: 1.5rem; justify-content: center;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 0.375rem; font-weight: 500; padding: 0.75rem 1.5rem; border: none; font-size: 1rem;">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" @click="confirmarAprovacao" :disabled="aprovando" style="border-radius: 0.375rem; font-weight: 500; padding: 0.75rem 1.5rem; border: none; font-size: 1rem;">
                        <span v-if="aprovando" class="spinner-border spinner-border-sm me-2"></span>
                        <i v-else class="fas fa-check me-2"></i>
                        {{ aprovando ? 'Aprovando...' : 'Aprovar Solicitação' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Rejeição -->
    <div class="modal fade" id="modalRejeicao" tabindex="-1" ref="modalRejeicaoRef">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border-bottom: none; padding: 1.5rem; border-radius: 0.5rem 0.5rem 0 0;">
                    <div class="d-flex align-items-center">
                        <div class="header-icon" style="width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 1.2rem; color: white;">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <h5 class="modal-title mb-0">Rejeitar Solicitação</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
                </div>
                
                <!-- Body do Modal -->
                <div class="modal-body" style="padding: 1.5rem;">
                    <div v-if="solicitacaoParaRejeitar">
                        <!-- Dados do Solicitante (Somente Leitura) -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #5EA853;">
                                <i class="fas fa-user me-2"></i>Dados do Solicitante
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoParaRejeitar.user?.name"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Nome Completo</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" 
                                               class="form-control" 
                                               :value="solicitacaoParaRejeitar.user?.email"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Email</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dados da Solicitação (Somente Leitura) -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #5EA853;">
                                <i class="fas fa-building me-2"></i>Dados da Solicitação
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoParaRejeitar.municipio?.nome"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Município</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :value="solicitacaoParaRejeitar.entidade_orcamentaria?.nome_fantasia"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: default;">
                                        <label>Entidade Orçamentária</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Justificativa (Somente Leitura) -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #5EA853;">
                                <i class="fas fa-comment-alt me-2"></i>Justificativa do Solicitante
                            </h6>
                            <div class="form-floating">
                                <textarea class="form-control" 
                                          :value="solicitacaoParaRejeitar.justificativa"
                                          readonly
                                          style="height: 100px; background-color: #f8f9fa; cursor: default; resize: none;"
                                          placeholder="Justificativa do solicitante"></textarea>
                                <label>Justificativa para o acesso</label>
                            </div>
                        </div>

                        <!-- Motivo da Rejeição (EDITÁVEL - OBRIGATÓRIO) -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #dc3545;">
                                <i class="fas fa-exclamation-triangle me-2"></i>Motivo da Rejeição *
                            </h6>
                            <div class="form-floating">
                                <textarea class="form-control" 
                                          id="motivoRejeicao"
                                          v-model="formRejeicao.observacoes_aprovacao"
                                          :class="{ 'is-invalid': errors.observacoes_aprovacao }"
                                          placeholder="Explique detalhadamente o motivo da rejeição..."
                                          style="height: 120px; resize: none;"
                                          required></textarea>
                                <label for="motivoRejeicao">Motivo da Rejeição *</label>
                                <div class="invalid-feedback" v-if="errors.observacoes_aprovacao">
                                    {{ errors.observacoes_aprovacao[0] }}
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Este motivo será enviado ao solicitante para que possa corrigir e reenviar sua solicitação.
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Footer do Modal -->
                <div class="modal-footer" style="background: transparent; border: none; padding: 1.5rem; justify-content: center;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 0.375rem; font-weight: 500; padding: 0.75rem 1.5rem; border: none; font-size: 1rem;">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" @click="confirmarRejeicao" :disabled="rejeitando" style="border-radius: 0.375rem; font-weight: 500; padding: 0.75rem 1.5rem; border: none; font-size: 1rem;">
                        <span v-if="rejeitando" class="spinner-border spinner-border-sm me-2"></span>
                        <i v-else class="fas fa-times me-2"></i>
                        {{ rejeitando ? 'Rejeitando...' : 'Rejeitar Solicitação' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast de Notificação -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div class="toast" ref="toastRef" role="alert">
            <div class="toast-header">
                <i :class="toastIcon + ' me-2'"></i>
                <strong class="me-auto">{{ toastTitle }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">{{ toastMessage }}</div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ListaAprovacaoCadastros',
    data() {
        return {
            loading: true,
            dados: [],
            entidades: [],
            tiposOrganizacao: [],
            registros: {
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 0
            },
            filtros: {
                nome: '',
                email: '',
                entidade_id: '',
                tipo_organizacao: '',
                status: '',
                visitante_uf: ''
            },
            filtrosVisiveis: false,
            
            // Modal de visualização
            solicitacaoSelecionada: null,
            
            // Modal de aprovação
            solicitacaoParaAprovar: null,
            formAprovacao: {
                observacoes_aprovacao: ''
            },
            aprovando: false,
            
            // Modal de rejeição
            solicitacaoParaRejeitar: null,
            formRejeicao: {
                observacoes_aprovacao: ''
            },
            rejeitando: false,
            
            errors: {},
            
            // Toast
            toastTitle: '',
            toastMessage: '',
            toastIcon: '',
            
            // Busca com debounce
            searchTimeout: null
        }
    },
    watch: {
        // Watchers para filtros automáticos
        'filtros.status'() {
            this.filtrarDados();
        },
        'filtros.entidade_id'() {
            this.filtrarDados();
        },
        'filtros.tipo_organizacao'() {
            this.filtrarDados();
        }
    },
    mounted() {
        this.carregarFiltros();
        this.carregarDados();
    },
    methods: {
        async carregarDados() {
            this.loading = true;
            try {
                // Construir parâmetros apenas com valores não vazios
                const params = new URLSearchParams();
                params.append('page', this.registros.current_page);
                params.append('per_page', this.registros.per_page);
                
                if (this.filtros.nome && this.filtros.nome.toString().trim()) {
                    params.append('nome', this.filtros.nome.toString().trim());
                }
                if (this.filtros.email && this.filtros.email.toString().trim()) {
                    params.append('email', this.filtros.email.toString().trim());
                }
                if (this.filtros.entidade_id) {
                    params.append('entidade_id', this.filtros.entidade_id);
                }
                if (this.filtros.tipo_organizacao) {
                    params.append('tipo_organizacao', this.filtros.tipo_organizacao);
                }
                if (this.filtros.status) {
                    params.append('status', this.filtros.status);
                }
                if (this.filtros.visitante_uf && this.filtros.visitante_uf.toString().trim()) {
                    params.append('visitante_uf', this.filtros.visitante_uf.toString().trim());
                }



                const response = await fetch(`/api/administracao/aprovacao-cadastros?${params}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.dados = data.data || [];
                    this.registros = {
                        current_page: data.current_page || 1,
                        last_page: data.last_page || 1,
                        per_page: data.per_page || 15,
                        total: data.total || 0
                    };
                } else {
                    console.error('Erro ao carregar dados:', data.message);
                    this.mostrarToast('Erro', 'Erro ao carregar solicitações', 'fa-exclamation-circle text-danger');
                }
                
            } catch (error) {
                console.error('Erro ao carregar dados:', error);
                this.mostrarToast('Erro', 'Erro ao carregar solicitações', 'fa-exclamation-circle text-danger');
            } finally {
                this.loading = false;
            }
        },
        
        async carregarFiltros() {
            try {
                const response = await fetch('/api/administracao/aprovacao-cadastros/filtros', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.entidades = data.entidades || [];
                    this.tiposOrganizacao = data.tipos_organizacao || [];
                } else {
                    console.error('Erro ao carregar filtros:', data.message);
                }
                
            } catch (error) {
                console.error('Erro ao carregar filtros:', error);
            }
        },
        
        // Filtros
        toggleFiltros() {
            this.filtrosVisiveis = !this.filtrosVisiveis;
        },
        
        onBuscaInput() {
            // Limpar timeout anterior
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            
            // Criar novo timeout com debounce de 500ms
            this.searchTimeout = setTimeout(() => {
                this.filtrarDados();
            }, 500);
        },
        
        filtrarDados() {
            this.registros.current_page = 1;
            this.carregarDados();
        },
        
        limparFiltros() {
            // Limpar timeout de busca se existir
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = null;
            }
            
            this.filtros = {
                nome: '',
                email: '',
                entidade_id: '',
                tipo_organizacao: '',
                status: '',
                visitante_uf: ''
            };
            this.filtrarDados();
        },
        
        // Paginação
        mudarPagina(page) {
            if (page >= 1 && page <= this.registros.last_page) {
                this.registros.current_page = page;
                this.carregarDados();
            }
        },
        
        getPaginasVisiveis() {
            const pages = [];
            const start = Math.max(1, this.registros.current_page - 2);
            const end = Math.min(this.registros.last_page, this.registros.current_page + 2);
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },
        
        // Ações
        visualizarSolicitacao(solicitacao) {
            this.solicitacaoSelecionada = solicitacao;
            
            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('modalVisualizacao'));
            modal.show();
        },
        
        aprovarSolicitacao(solicitacao) {
            this.solicitacaoParaAprovar = solicitacao;
            
            // Limpar formulário
            this.formAprovacao = {
                observacoes_aprovacao: ''
            };
            
            // Limpar erros
            this.errors = {};
            
            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('modalAprovacao'));
            modal.show();
        },
        
        rejeitarSolicitacao(solicitacao) {
            this.solicitacaoParaRejeitar = solicitacao;
            
            // Limpar formulário
            this.formRejeicao = {
                observacoes_aprovacao: ''
            };
            
            // Limpar erros
            this.errors = {};
            
            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('modalRejeicao'));
            modal.show();
        },
        
        async confirmarAprovacao() {
            this.aprovando = true;
            this.errors = {};
            
            try {
                const response = await fetch(`/api/administracao/aprovacao-cadastros/${this.solicitacaoParaAprovar.id}/aprovar`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.formAprovacao)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Sucesso
                    this.mostrarToast('Sucesso', 'Solicitação aprovada com sucesso!', 'fa-check-circle text-success');
                    
                    // Fechar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalAprovacao'));
                    modal.hide();
                    
                    // Recarregar dados
                    this.carregarDados();
                    
                    // Limpar formulário
                    this.formAprovacao = {
                        observacoes_aprovacao: ''
                    };
                    this.solicitacaoParaAprovar = null;
                    
                } else {
                    // Erro de validação
                    if (response.status === 422 && data.errors) {
                        this.errors = data.errors;
                        this.mostrarToast('Erro', 'Corrija os erros de validação', 'fa-exclamation-circle text-danger');
                    } else {
                        this.mostrarToast('Erro', data.message || 'Erro ao aprovar solicitação', 'fa-exclamation-circle text-danger');
                    }
                }
                
            } catch (error) {
                console.error('Erro ao aprovar:', error);
                this.mostrarToast('Erro', 'Erro interno ao aprovar solicitação', 'fa-exclamation-circle text-danger');
            } finally {
                this.aprovando = false;
            }
        },
        
        async confirmarRejeicao() {
            // Validação
            if (!this.formRejeicao.observacoes_aprovacao || this.formRejeicao.observacoes_aprovacao.trim() === '') {
                this.mostrarToast('Erro', 'O motivo da rejeição é obrigatório', 'fa-exclamation-circle text-danger');
                return;
            }
            
            this.rejeitando = true;
            this.errors = {};
            
            try {
                const response = await fetch(`/api/administracao/aprovacao-cadastros/${this.solicitacaoParaRejeitar.id}/rejeitar`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.formRejeicao)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Sucesso
                    this.mostrarToast('Sucesso', 'Solicitação rejeitada com sucesso!', 'fa-times-circle text-warning');
                    
                    // Fechar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalRejeicao'));
                    modal.hide();
                    
                    // Recarregar dados
                    this.carregarDados();
                    
                    // Limpar formulário
                    this.formRejeicao = {
                        observacoes_aprovacao: ''
                    };
                    this.solicitacaoParaRejeitar = null;
                    
                } else {
                    // Erro de validação
                    if (response.status === 422 && data.errors) {
                        this.errors = data.errors;
                        this.mostrarToast('Erro', 'Corrija os erros de validação', 'fa-exclamation-circle text-danger');
                    } else {
                        this.mostrarToast('Erro', data.message || 'Erro ao rejeitar solicitação', 'fa-exclamation-circle text-danger');
                    }
                }
                
            } catch (error) {
                console.error('Erro ao rejeitar:', error);
                this.mostrarToast('Erro', 'Erro interno ao rejeitar solicitação', 'fa-exclamation-circle text-danger');
            } finally {
                this.rejeitando = false;
            }
        },
        
        // Utilitários
        formatarData(data) {
            if (!data) return '';
            return new Date(data).toLocaleDateString('pt-BR');
        },
        
        getStatusClass(status) {
            switch (status) {
                case 'pendente': return 'badge-warning';
                case 'aprovado': return 'badge-success';
                case 'rejeitado': return 'badge-danger';
                default: return 'badge-secondary';
            }
        },
        
        getStatusIcon(status) {
            switch (status) {
                case 'pendente': return 'fa-clock';
                case 'aprovado': return 'fa-check-circle';
                case 'rejeitado': return 'fa-times-circle';
                default: return 'fa-question-circle';
            }
        },
        
        getStatusLabel(status) {
            switch (status) {
                case 'pendente': return 'PENDENTE';
                case 'aprovado': return 'APROVADO';
                case 'rejeitado': return 'REJEITADO';
                default: return 'DESCONHECIDO';
            }
        },
        
        // Toast
        mostrarToast(title, message, icon) {
            this.toastTitle = title;
            this.toastMessage = message;
            this.toastIcon = icon;
            
            if (this.$refs.toastRef) {
                const toast = new bootstrap.Toast(this.$refs.toastRef);
                toast.show();
            }
        }
    }
}
</script>

<style scoped>
/* Estilos específicos do componente */
.filtros-aba-container {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 1rem;
}

.filtros-aba-content {
    opacity: 0;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.filtros-aba-content.show {
    opacity: 1;
    max-height: 200px;
}

/* Classes de tabela (seguindo padrão do projeto) */
.table-header {
    background-color: #f9fafb !important;
    border-bottom: 2px solid #e5e7eb !important;
    color: #18578A !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    padding: 0.75rem 0.5rem !important;
    vertical-align: middle !important;
}

.table-row {
    background-color: #ffffff !important;
    transition: all 0.2s ease !important;
    border-left: 3px solid transparent !important;
    cursor: default !important;
}

.table-row:hover {
    background-color: #f8f9fa !important;
    border-left: 3px solid #5EA853 !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
}

.table-cell {
    padding: 0.75rem 0.5rem !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #e5e7eb !important;
}

/* Badges discretos */
.badge-status {
    background-color: transparent !important;
    border: 1px solid #dee2e6 !important;
    color: #6c757d !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    letter-spacing: 0.5px !important;
    padding: 0.25rem 0.5rem !important;
}

.badge-warning {
    border-color: #ffc107 !important;
    color: #856404 !important;
    background-color: #fff3cd !important;
}

.badge-success {
    border-color: #198754 !important;
    color: #0f5132 !important;
    background-color: #d1e7dd !important;
}

.badge-danger {
    border-color: #dc3545 !important;
    color: #721c24 !important;
    background-color: #f8d7da !important;
}

/* Paginação */
.pagination-generic .page-link {
    border-color: #dee2e6;
    color: #6c757d;
}

.pagination-generic .page-item.active .page-link {
    background-color: #18578A;
    border-color: #18578A;
    color: white;
}

.pagination-generic .page-link:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    color: #495057;
}
</style>