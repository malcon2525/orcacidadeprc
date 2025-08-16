<template>
    <div>
        <!-- Cabeçalho Integrado com Seletor -->
        <div class="header-integrado">
            <div class="header-content">
                <div class="titulo-container">
                    <h5 class="text-custom mb-0">
                        <i class="fas fa-sitemap me-2"></i>Estrutura de Orçamento
                    </h5>
                    <p class="text-muted mt-2 mb-0">Selecione um tipo de orçamento para começar</p>
                </div>
                
                <div class="seletor-container">
                    <div class="d-flex align-items-end gap-3">
                    <div class="tipo-orcamento-selector">
                        <div class="form-floating">
                            <select class="form-control" 
                                    id="tipoOrcamentoSelector" 
                                    v-model="tipoOrcamentoSelecionado"
                                    @change="onTipoOrcamentoChange"
                                    :class="{ 'is-invalid': !tipoOrcamentoSelecionado }">
                                <option value="">Selecione o tipo de orçamento...</option>
                                <option v-for="tipo in tiposOrcamento" 
                                        :key="tipo.id" 
                                        :value="tipo.id">
                                    {{ tipo.descricao }} - Versão {{ tipo.versao }}
                                </option>
                            </select>
                            <label for="tipoOrcamentoSelector">
                                <i class="fas fa-chart-pie me-2"></i>Tipo de Orçamento
                            </label>
                        </div>
                    </div>
                        
                        <!-- Botão de Importação Excel -->
                        <div class="importacao-container" v-if="tipoOrcamentoSelecionado">
                            <button class="btn btn-success" @click="abrirModalImportacao" :disabled="loading">
                                <i class="fas fa-file-excel me-2"></i>
                                Importar Excel
                            </button>
                        </div>
                        
                        <!-- Botão de Limpar Estrutura -->
                        <div class="limpar-container" v-if="tipoOrcamentoSelecionado && grandesItens.length > 0">
                            <button class="btn btn-outline-danger" @click="abrirModalLimparEstrutura" :disabled="loading">
                                <i class="fas fa-trash-alt me-2"></i>
                                Limpar Estrutura
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros da Aba (Compactos) -->
        <div class="filtros-aba-container mb-3" v-if="filtrosVisiveis && tipoOrcamentoSelecionado">
            <div class="filtros-aba-content" :class="{ 'show': filtrosVisiveis }">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="filtroDescricao" v-model="filtros.busca" @input="filtrarDados" placeholder="Filtrar por descrição">
                            <label for="filtroDescricao">Filtrar por descrição</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-select" id="filtroStatus" v-model="filtros.status" @change="filtrarDados">
                                <option value="">Todos os status</option>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                            <label for="filtroStatus">Status</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estrutura Hierárquica -->
        <div v-if="tipoOrcamentoSelecionado" class="card">
            <div class="card-body p-0">
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando estrutura...</p>
                </div>
                
                <!-- Estrutura -->
                <div v-else-if="grandesItens.length > 0" class="estrutura-container">
                    <!-- Tabela Principal -->
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">
                                        <i class="fas fa-grip-vertical text-muted"></i>
                                    </th>
                                    <th>Grande Item</th>
                                    <th style="width: 80px;">Subgrupos</th>
                                    <th style="width: 120px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="grandeItem in grandesItens" :key="grandeItem.id">
                                    <!-- Linha do Grande Item -->
                                    <tr class="grande-item-row" 
                                        draggable="true"
                                        @dragstart="onDragStart($event, grandeItem, 'grande-item')"
                                        @dragover.prevent
                                        @drop="onDrop($event, grandeItem, 'grande-item')">
                                        <td class="drag-handle text-center">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-sitemap text-primary me-2"></i>
                                                <div>
                                                    <div class="fw-semibold">{{ grandeItem.ordem || 0 }}. {{ grandeItem.descricao }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ grandeItem.subgrupos?.length || 0 }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 justify-content-end">
                                                <button class="btn btn-sm btn-warning" @click="editarGrandeItem(grandeItem)" title="Editar Grande Item">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" @click="excluirGrandeItem(grandeItem)" title="Excluir Grande Item">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Linhas dos Subgrupos - SEMPRE VISÍVEL -->
                                    <tr class="subgrupos-expanded">
                                        <td colspan="4" class="p-0">
                                            <!-- Header dos Subgrupos -->
                                            <div class="subgrupos-header">
                                                <div class="subgrupos-header-content">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-chevron-down text-muted me-2"></i>
                                                        <span class="text-muted fw-medium">
                                                            Subgrupos ({{ grandeItem.sub_grupos?.length || 0 }})
                                                        </span>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-success" @click="adicionarSubgrupo(grandeItem)" title="Adicionar Subgrupo">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Conteúdo dos Subgrupos -->
                                            <div class="subgrupos-content">
                                                <!-- Se não há subgrupos, mostrar mensagem -->
                                                <div v-if="!grandeItem.sub_grupos || grandeItem.sub_grupos.length === 0" 
                                                     class="subgrupos-empty text-center py-3">
                                                    <i class="fas fa-folder-open text-muted mb-2" style="font-size: 1.5rem;"></i>
                                                    <p class="text-muted mb-2">Nenhum subgrupo criado</p>
                                                    <small class="text-muted">Clique no botão + para adicionar o primeiro subgrupo</small>
                                                </div>
                                                
                                                <!-- Tabela de Subgrupos (quando existem) -->
                                                <div v-else class="subgrupos-table-container">
                                                <table class="table table-sm mb-0">
                                                    <tbody>
                                                            <tr v-for="subgrupo in grandeItem.sub_grupos" 
                                                            :key="subgrupo.id"
                                                            class="subgrupo-row"
                                                            draggable="true"
                                                            @dragstart="onDragStart($event, subgrupo, 'subgrupo')"
                                                            @dragover.prevent
                                                            @drop="onDrop($event, subgrupo, 'subgrupo')">
                                                            <td class="drag-handle text-center" style="width: 50px;">
                                                                <i class="fas fa-grip-vertical text-muted"></i>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-folder text-warning me-2"></i>
                                                                    <div>
                                                                        <div class="fw-medium">{{ subgrupo.ordem || 0 }}. {{ subgrupo.descricao }}</div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td style="width: 120px;">
                                                                <div class="d-flex gap-1 justify-content-end">
                                                                        <button class="btn btn-sm btn-outline-warning subgrupo-action-btn" @click="editarSubgrupo(subgrupo)" title="Editar Subgrupo">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                        <button class="btn btn-sm btn-outline-danger subgrupo-action-btn" @click="excluirSubgrupo(subgrupo)" title="Excluir Subgrupo">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                
                                <!-- Linha para Adicionar Grande Item -->
                                <tr class="adicionar-grande-item-row">
                                    <td class="text-center">
                                        <i class="fas fa-plus text-success"></i>
                                    </td>
                                    <td colspan="3" class="text-center py-4">
                                        <button class="btn btn-outline-success d-flex align-items-center gap-2 mx-auto" 
                                                @click="abrirModalCriar" 
                                                title="Adicionar Novo Grande Item">
                                            <i class="fas fa-plus"></i>
                                            <span>Adicionar Grande Item</span>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Estado Vazio -->
                <div v-else class="text-center py-5">
                    <i class="fas fa-sitemap text-muted" style="font-size: 3rem;"></i>
                    <p class="mt-3 text-muted">Nenhum grande item encontrado</p>
                    <button class="btn btn-success" @click="abrirModalCriar">
                        <i class="fas fa-plus me-2"></i>Criar Primeiro Grande Item
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Grande Item -->
        <div class="modal fade" id="modalGrandeItem" tabindex="-1" ref="grandeItemModalRef">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Header com Gradiente -->
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
                            </div>
                            <h5 class="modal-title mb-0">
                                {{ modoEdicao ? 'Editar Grande Item' : 'Novo Grande Item' }}
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Corpo do Modal -->
                    <div class="modal-body">
                        <form @submit.prevent="salvarGrandeItem">
                            <div class="row g-3">
                                <!-- Campo Descrição -->
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.descricao }"
                                               id="descricao" 
                                               v-model="form.descricao"
                                               placeholder="Descrição do grande item"
                                               required>
                                        <label for="descricao">Descrição do Grande Item</label>
                                    </div>
                                    <div class="invalid-feedback" v-if="errors.descricao">
                                        {{ errors.descricao[0] }}
                                    </div>
                                </div>
                                
                                <!-- Campo Ordem -->
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.ordem }"
                                               id="ordem" 
                                               v-model="form.ordem"
                                               placeholder="Ordem"
                                               min="0"
                                               required>
                                        <label for="ordem">Ordem</label>
                                    </div>
                                    <div class="invalid-feedback" v-if="errors.ordem">
                                        {{ errors.ordem[0] }}
                                    </div>
                                </div>
                                
                                <!-- Informação do Tipo de Orçamento -->
                                <div class="col-12">
                                    <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                                        <i class="fas fa-info-circle"></i>
                                        <span><strong>Tipo de Orçamento:</strong> {{ tipoOrcamentoAtual?.descricao }} - Versão {{ tipoOrcamentoAtual?.versao }}</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Rodapé do Modal -->
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" @click="salvarGrandeItem" :disabled="salvando">
                            <span v-if="salvando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-save me-2"></i>
                            {{ salvando ? 'Salvando...' : 'Salvar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Subgrupo -->
        <div class="modal fade" id="modalSubgrupo" tabindex="-1" ref="modalSubgrupoRef">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Header com Gradiente -->
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
                            </div>
                            <h5 class="modal-title mb-0">
                                {{ modoEdicao ? 'Editar Subgrupo' : 'Novo Subgrupo' }}
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Corpo do Modal -->
                    <div class="modal-body">
                        <form @submit.prevent="salvarSubgrupo">
                            <div class="row g-3">
                                <!-- Campo Grande Item ID (Readonly) -->
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               id="grandeItemIdSubgrupo" 
                                               :value="form.grande_item_id ? `Grande Item: ${itemSelecionado?.descricao || 'N/A'}` : ''"
                                               readonly
                                               disabled>
                                        <label for="grandeItemIdSubgrupo">Grande Item Pai</label>
                                    </div>
                                    <small class="text-muted">Este subgrupo será criado dentro do grande item selecionado</small>
                                </div>
                                
                                <!-- Campo Descrição -->
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.descricao }"
                                               id="descricaoSubgrupo" 
                                               v-model="form.descricao"
                                               placeholder="Descrição do subgrupo"
                                               required>
                                        <label for="descricaoSubgrupo">Descrição do Subgrupo</label>
                                    </div>
                                    <div class="invalid-feedback" v-if="errors.descricao">
                                        {{ errors.descricao[0] }}
                                    </div>
                                </div>
                                
                                <!-- Campo Ordem -->
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.ordem }"
                                               id="ordemSubgrupo" 
                                               v-model="form.ordem"
                                               placeholder="Ordem"
                                               min="0"
                                               required>
                                        <label for="ordemSubgrupo">Ordem</label>
                                    </div>
                                    <div class="invalid-feedback" v-if="errors.ordem">
                                        {{ errors.ordem[0] }}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Rodapé do Modal -->
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" @click="salvarSubgrupo" :disabled="salvando">
                            <span v-if="salvando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-save me-2"></i>
                            {{ salvando ? 'Salvando...' : 'Salvar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação de Exclusão -->
        <div class="modal fade" id="modalConfirmacao" tabindex="-1" ref="modalConfirmacaoRef">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <!-- Header com Gradiente -->
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h5 class="modal-title mb-0">Confirmar Exclusão</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Corpo do Modal -->
                    <div class="modal-body text-center py-4">
                        <i class="fas fa-question-circle text-warning mb-3" style="font-size: 3rem;"></i>
                        <h6 class="mb-3">Tem certeza que deseja excluir este {{ tipoItemParaExcluir === 'grande-item' ? 'grande item' : 'subgrupo' }}?</h6>
                        <p class="text-muted mb-0">
                            <strong>{{ itemParaExcluir?.descricao }}</strong>
                        </p>
                        <small class="text-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Esta ação não pode ser desfeita!
                        </small>
                    </div>

                    <!-- Rodapé do Modal -->
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" @click="confirmarExclusao" :disabled="excluindo">
                            <span v-if="excluindo" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-trash me-2"></i>
                            {{ excluindo ? 'Excluindo...' : 'Sim, Excluir' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Importação Excel -->
        <div class="modal fade" id="modalImportacao" tabindex="-1" ref="modalImportacaoRef">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Header com Gradiente -->
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-file-excel"></i>
                            </div>
                            <h5 class="modal-title mb-0">Importar Estrutura de Orçamento</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Corpo do Modal -->
                    <div class="modal-body">
                        <!-- Passo 1: Upload do arquivo -->
                        <div v-if="passoImportacao === 1" class="upload-step">
                            <div class="text-center mb-4">
                                <i class="fas fa-cloud-upload-alt text-primary mb-3" style="font-size: 3rem;"></i>
                                <h6>Selecione o arquivo Excel</h6>
                                <p class="text-muted">O arquivo deve conter as colunas: grande_item_ordem, grande_item_descricao, subgrupo_ordem, subgrupo_descricao</p>
                            </div>
                            
                            <div class="upload-area" 
                                 @drop="onFileDrop" 
                                 @dragover.prevent 
                                 @dragenter.prevent
                                 :class="{ 'drag-over': isDragOver }">
                                <input type="file" 
                                       ref="fileInput" 
                                       @change="onFileSelect" 
                                       accept=".xlsx,.xls" 
                                       class="file-input">
                                <div class="upload-content">
                                    <i class="fas fa-file-excel text-success mb-3" style="font-size: 2rem;"></i>
                                    <p class="mb-2">Arraste o arquivo Excel aqui ou clique para selecionar</p>
                                    <small class="text-muted">Formatos aceitos: .xlsx, .xls</small>
                                </div>
                            </div>
                            
                            <div v-if="arquivoSelecionado" class="arquivo-info mt-3">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <div>
                                        <strong>Arquivo selecionado:</strong> {{ arquivoSelecionado.name }}
                                        <br><small class="text-muted">Tamanho: {{ formatarTamanho(arquivoSelecionado.size) }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Passo 2: Confirmação -->
                        <div v-if="passoImportacao === 2" class="confirmacao-step">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>ATENÇÃO:</strong> Esta operação irá SUBSTITUIR COMPLETAMENTE a estrutura de orçamento selecionada!
                            </div>
                            
                            <div class="confirmacao-importacao">
                                <div class="text-center p-3">
                                    <p class="text-muted mb-0">Arquivo selecionado: <strong>{{ arquivoSelecionado?.name }}</strong></p>
                                    <small class="text-muted">{{ formatarTamanho(arquivoSelecionado?.size || 0) }}</small>
                                </div>
                                
                                <div class="mt-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h4 class="text-primary mb-1">{{ resumoImportacao.grandesItens }}</h4>
                                                    <small class="text-muted">Grandes Itens</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h4 class="text-success mb-1">{{ resumoImportacao.subgrupos }}</h4>
                                                    <small class="text-muted">Subgrupos</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Passo 3: Progresso -->
                        <div v-if="passoImportacao === 3" class="progresso-step">
                            <div class="text-center mb-4">
                                <i class="fas fa-cogs text-primary mb-3" style="font-size: 3rem;"></i>
                                <h6>Processando Importação...</h6>
                                <p class="text-muted">Aguarde enquanto processamos o arquivo</p>
                            </div>
                            
                            <div class="progress mb-3" style="height: 25px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     :style="{ width: progressoImportacao + '%' }"
                                     :aria-valuenow="progressoImportacao" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ progressoImportacao }}%
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <small class="text-muted">{{ mensagemProgresso }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Rodapé do Modal -->
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" @click="fecharModalImportacao" v-if="passoImportacao === 1">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-warning" @click="voltarPasso" v-if="passoImportacao === 2">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </button>
                        <button type="button" class="btn btn-danger" @click="confirmarImportacao" v-if="passoImportacao === 2" :disabled="importando">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Sim, Substituir Tudo
                        </button>
                        <button type="button" class="btn btn-success" @click="fecharModalImportacao" v-if="passoImportacao === 3 && !importando">
                            <i class="fas fa-check me-2"></i>Concluído
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação para Limpar Estrutura -->
        <div class="modal fade" id="modalLimparEstrutura" tabindex="-1" ref="modalLimparEstruturaRef">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <!-- Header com Gradiente -->
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                            <h5 class="modal-title mb-0">Limpar Estrutura</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Corpo do Modal -->
                    <div class="modal-body text-center py-4">
                        <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                        <h6 class="mb-3">Tem certeza que deseja limpar toda a estrutura?</h6>
                        <p class="text-muted mb-0">
                            <strong>{{ tipoOrcamentoAtual?.descricao }} - Versão {{ tipoOrcamentoAtual?.versao }}</strong>
                        </p>
                        <div class="alert alert-warning mt-3 mb-0">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Serão removidos:</strong><br>
                                • {{ grandesItens.length }} Grande(s) Item(ns)<br>
                                • {{ totalSubgrupos }} Subgrupo(s)
                            </small>
                        </div>
                        <small class="text-danger d-block mt-3">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Esta ação não pode ser desfeita!
                        </small>
                    </div>

                    <!-- Rodapé do Modal -->
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" @click="confirmarLimparEstrutura" :disabled="limpando">
                            <span v-if="limpando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-trash-alt me-2"></i>
                            {{ limpando ? 'Limpando...' : 'Sim, Limpar Tudo' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'EstruturaOrcamento',
    
    data() {
        return {
            // Dados
            tiposOrcamento: [],
            tipoOrcamentoSelecionado: '',
            tipoOrcamentoAtual: null,
            grandesItens: [],
            loading: false,
            salvando: false,
            
            // Filtros
            filtros: {
                busca: '',
                status: ''
            },
            filtrosVisiveis: false,
            
            // Formulário
            form: {
                id: null,
                descricao: '',
                ordem: 0,
                tipo_orcamento_id: '',
                grande_item_id: ''
            },
            
            // Estado
            modoEdicao: false,
            itemSelecionado: null,
            errors: {},
            
            // Refs dos modais
            grandeItemModal: null,
            subgrupoModal: null,
            modalConfirmacao: null,
            modalImportacao: null,
            modalLimparEstrutura: null,
            
            // Confirmação de exclusão
            mostrarModalConfirmacao: false,
            itemParaExcluir: null,
            tipoItemParaExcluir: '',
            excluindo: false,
            
            // Importação Excel
            passoImportacao: 1,
            arquivoSelecionado: null,
            isDragOver: false,
            dadosExcel: null,
            resumoImportacao: {
                grandesItens: 0,
                subgrupos: 0
            },
            previewEstrutura: [],
            progressoImportacao: 0,
            mensagemProgresso: '',
            importando: false,
            
            // Limpar estrutura
            limpando: false
        }
    },
    
    computed: {
        // Calcula o total de subgrupos
        totalSubgrupos() {
            return this.grandesItens.reduce((total, item) => {
                return total + (item.sub_grupos ? item.sub_grupos.length : 0);
            }, 0);
        }
    },
    
    mounted() {
        this.carregarTiposOrcamento();
        this.inicializarModais();
    },
    
    methods: {
        async carregarTiposOrcamento() {
            try {
                const response = await axios.get('/api/administracao/estrutura-orcamento/tipo-orcamento/');
                if (response.data && response.data.data) {
                    this.tiposOrcamento = response.data.data;
                }
            } catch (error) {
                console.error('Erro ao carregar tipos de orçamento:', error);
                this.mostrarToast('Erro ao carregar tipos de orçamento', 'error');
            }
        },
        
        async recarregarTiposOrcamento() {
            await this.carregarTiposOrcamento();
        },
        
        async carregarDados() {
            if (!this.tipoOrcamentoSelecionado) return;
            
            this.loading = true;
            try {
                const response = await axios.get(`/api/administracao/estrutura-orcamento/grande-item/${this.tipoOrcamentoSelecionado}`);
                
                if (response.data && response.data.success) {
                    this.grandesItens = response.data.data;
                } else {
                    this.grandesItens = [];
                }
            } catch (error) {
                console.error('Erro ao carregar dados:', error);
                this.mostrarToast('Erro ao carregar estrutura de orçamento', 'error');
                this.grandesItens = [];
            } finally {
                this.loading = false;
            }
        },
        
        // Mudança no tipo de orçamento
        onTipoOrcamentoChange() {
            this.grandesItens = [];
            this.filtrosVisiveis = false;
            this.filtros = { busca: '', status: '' };
            
            if (this.tipoOrcamentoSelecionado) {
                this.tipoOrcamentoAtual = this.tiposOrcamento.find(t => t.id === this.tipoOrcamentoSelecionado);
                this.form.tipo_orcamento_id = this.tipoOrcamentoSelecionado;
                this.$nextTick(() => {
                    this.carregarDados();
                });
            }
        },
        
        // Filtrar dados
        filtrarDados() {
            // Implementar lógica de filtro
        },
        
        // Inicializar modais
        inicializarModais() {
            this.$nextTick(() => {
                if (this.$refs.grandeItemModalRef) {
                    this.grandeItemModal = new bootstrap.Modal(this.$refs.grandeItemModalRef);
                }
                if (this.$refs.modalSubgrupoRef) {
                    this.subgrupoModal = new bootstrap.Modal(this.$refs.modalSubgrupoRef);
                }
                if (this.$refs.modalConfirmacaoRef) {
                    this.modalConfirmacao = new bootstrap.Modal(this.$refs.modalConfirmacaoRef);
                }
                if (this.$refs.modalImportacaoRef) {
                    this.modalImportacao = new bootstrap.Modal(this.$refs.modalImportacaoRef);
                }
                if (this.$refs.modalLimparEstruturaRef) {
                    this.modalLimparEstrutura = new bootstrap.Modal(this.$refs.modalLimparEstruturaRef);
                }
            });
        },
        
        // Abrir modal de criação
        abrirModalCriar() {
            this.modoEdicao = false;
            this.limparFormulario();
            if (this.grandeItemModal) {
                this.grandeItemModal.show();
            }
        },
        
        // Editar grande item
        editarGrandeItem(item) {
            this.modoEdicao = true;
            this.itemSelecionado = item;
            this.form = { ...item };
            if (this.grandeItemModal) {
                this.grandeItemModal.show();
            }
        },
        
        // Adicionar subgrupo
        adicionarSubgrupo(grandeItem) {
            this.modoEdicao = false;
            this.itemSelecionado = grandeItem;
            this.form.grande_item_id = grandeItem.id;
            this.limparFormulario();
            if (this.subgrupoModal) {
                this.subgrupoModal.show();
            }
        },
        
        // Editar subgrupo
        editarSubgrupo(subgrupo) {
            this.modoEdicao = true;
            this.itemSelecionado = subgrupo;
            this.form = { ...subgrupo };
            
            // Encontrar o grande item pai
            const grandeItem = this.grandesItens.find(gi => 
                gi.sub_grupos && gi.sub_grupos.some(sg => sg.id === subgrupo.id)
            );
            if (grandeItem) {
                this.form.grande_item_id = grandeItem.id;
            }
            
            if (this.subgrupoModal) {
                this.subgrupoModal.show();
            }
        },
        
        async salvarGrandeItem() {
            this.salvando = true;
            try {
                let response;
                
                if (this.modoEdicao) {
                    // Atualizar
                    response = await axios.put(`/api/administracao/estrutura-orcamento/grande-item/${this.form.id}`, {
                        descricao: this.form.descricao,
                        ordem: this.form.ordem
                    });
                } else {
                    // Criar
                    response = await axios.post('/api/administracao/estrutura-orcamento/grande-item/', {
                        tipo_orcamento_id: this.tipoOrcamentoSelecionado,
                        descricao: this.form.descricao,
                        ordem: this.form.ordem
                    });
                }
                
                if (response.data && response.data.success) {
                    if (this.grandeItemModal) {
                        this.grandeItemModal.hide();
                    }
                    await this.carregarDados();
                
                    this.mostrarToast(response.data.message || 'Grande item salvo com sucesso!', 'success');
                } else {
                    this.mostrarToast('Erro ao salvar grande item', 'error');
                }
            } catch (error) {
                console.error('Erro ao salvar grande item:', error);
                if (error.response && error.response.data && error.response.data.errors) {
                    this.errors = error.response.data.errors;
                } else {
                this.mostrarToast('Erro ao salvar grande item', 'error');
                }
            } finally {
                this.salvando = false;
            }
        },
        
        async salvarSubgrupo() {
            this.salvando = true;
            try {
                let response;
                
                if (this.modoEdicao) {
                    // Atualizar
                    response = await axios.put(`/api/administracao/estrutura-orcamento/subgrupo/${this.form.id}`, {
                        descricao: this.form.descricao,
                        ordem: this.form.ordem
                    });
                } else {
                    // Criar
                    response = await axios.post('/api/administracao/estrutura-orcamento/subgrupo/', {
                        grande_item_id: this.form.grande_item_id,
                        descricao: this.form.descricao,
                        ordem: this.form.ordem
                    });
                }
                
                if (response.data && response.data.success) {
                    if (this.subgrupoModal) {
                        this.subgrupoModal.hide();
                    }
                    await this.carregarDados();
                
                    this.mostrarToast(response.data.message || 'Subgrupo salvo com sucesso!', 'success');
                } else {
                    this.mostrarToast('Erro ao salvar subgrupo', 'error');
                }
            } catch (error) {
                console.error('Erro ao salvar subgrupo:', error);
                if (error.response && error.response.data && error.response.data.errors) {
                    this.errors = error.response.data.errors;
                } else {
                this.mostrarToast('Erro ao salvar subgrupo', 'error');
                }
            } finally {
                this.salvando = false;
            }
        },
        
        excluirGrandeItem(item) {
            this.itemParaExcluir = item;
            this.tipoItemParaExcluir = 'grande-item';
            if (this.modalConfirmacao) {
                this.modalConfirmacao.show();
            }
        },
        
        excluirSubgrupo(subgrupo) {
            this.itemParaExcluir = subgrupo;
            this.tipoItemParaExcluir = 'subgrupo';
            if (this.modalConfirmacao) {
                this.modalConfirmacao.show();
            }
        },
        
        // Confirmar exclusão (chamado pelo modal)
        async confirmarExclusao() {
            if (!this.itemParaExcluir || !this.tipoItemParaExcluir) return;
            
            this.excluindo = true;
            
            try {
                let response;
                
                if (this.tipoItemParaExcluir === 'grande-item') {
                    response = await axios.delete(`/api/administracao/estrutura-orcamento/grande-item/${this.itemParaExcluir.id}`);
                } else if (this.tipoItemParaExcluir === 'subgrupo') {
                    response = await axios.delete(`/api/administracao/estrutura-orcamento/subgrupo/${this.itemParaExcluir.id}`);
                }
                
                if (response && response.data && response.data.success) {
                    await this.carregarDados();
                    this.mostrarToast(response.data.message || `${this.tipoItemParaExcluir === 'grande-item' ? 'Grande item' : 'Subgrupo'} excluído com sucesso!`, 'success');
                } else {
                    this.mostrarToast(`Erro ao excluir ${this.tipoItemParaExcluir === 'grande-item' ? 'grande item' : 'subgrupo'}`, 'error');
                }
                } catch (error) {
                console.error(`Erro ao excluir ${this.tipoItemParaExcluir}:`, error);
                this.mostrarToast(`Erro ao excluir ${this.tipoItemParaExcluir === 'grande-item' ? 'grande item' : 'subgrupo'}`, 'error');
            } finally {
                this.excluindo = false;
                
                // Fechar modal e limpar dados
                if (this.modalConfirmacao) {
                    this.modalConfirmacao.hide();
                }
                this.itemParaExcluir = null;
                this.tipoItemParaExcluir = '';
            }
        },
        
        // Limpar formulário
        limparFormulario() {
            this.form = {
                id: null,
                descricao: '',
                ordem: 0,
                tipo_orcamento_id: this.tipoOrcamentoSelecionado,
                grande_item_id: this.form.grande_item_id || ''
            };
            this.errors = {};
        },
        
        // Drag & Drop
        onDragStart(event, item, type) {
            event.dataTransfer.setData('text/plain', JSON.stringify({ item, type }));
        },
        
        onDrop(event, target, type) {
            event.preventDefault();
            // Implementar lógica de reordenação
        },
        
        // Toast
        mostrarToast(mensagem, tipo) {
            // Sistema de toast simples
            const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
            const icon = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            const toast = document.createElement('div');
            toast.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                <i class="fas ${icon} me-2"></i>${mensagem}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(toast);
            
            // Auto-remover após 5 segundos
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 5000);
        },
        abrirModalImportacao() {
            this.resetarImportacao();
            if (this.modalImportacao) {
                this.modalImportacao.show();
            }
        },
        
        fecharModalImportacao() {
            if (this.modalImportacao) {
                this.modalImportacao.hide();
            }
            this.resetarImportacao();
        },
        
        resetarImportacao() {
            this.passoImportacao = 1;
            this.arquivoSelecionado = null;
            this.isDragOver = false;
            this.dadosExcel = null;
            this.resumoImportacao = { grandesItens: 0, subgrupos: 0 };
            this.previewEstrutura = [];
            this.progressoImportacao = 0;
            this.mensagemProgresso = '';
            this.importando = false;
        },
        
        onDragOver(event) {
            this.isDragOver = true;
        },
        
        onDragEnter(event) {
            this.isDragOver = true;
        },
        
        onDragLeave(event) {
            this.isDragOver = false;
        },
        
        onFileDrop(event) {
            event.preventDefault();
            this.isDragOver = false;
            
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                this.processarArquivo(files[0]);
            }
        },
        
        onFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.processarArquivo(file);
            }
        },
        
        async processarArquivo(file) {
            // Validar tipo de arquivo
            if (!file.name.match(/\.(xlsx|xls)$/i)) {
                this.mostrarToast('Por favor, selecione um arquivo Excel (.xlsx ou .xls)', 'error');
                return;
            }
            
            // Validar tamanho (máximo 10MB)
            if (file.size > 10 * 1024 * 1024) {
                this.mostrarToast('Arquivo muito grande. Máximo permitido: 10MB', 'error');
                return;
            }
            
            this.arquivoSelecionado = file;
            
            try {
                // Fazer upload do arquivo para obter preview real
                await this.processarArquivoReal();
                this.passoImportacao = 2;
            } catch (error) {
                this.mostrarToast('Erro ao processar arquivo: ' + error.message, 'error');
            }
        },
        
        // Processar arquivo real (upload para API)
        async processarArquivoReal() {
            try {
                // Criar FormData para envio do arquivo
                const formData = new FormData();
                formData.append('arquivo', this.arquivoSelecionado);
                formData.append('tipo_orcamento_id', this.tipoOrcamentoSelecionado);

                // Fazer upload para a API para obter preview
                const response = await axios.post('/api/administracao/estrutura-orcamento/importar', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data && response.data.success) {
                    // Atualizar dados com resposta real da API
                    this.dadosExcel = response.data.data;
                    this.resumoImportacao.grandesItens = response.data.data.grandes_itens_criados || 0;
                    this.resumoImportacao.subgrupos = response.data.data.subgrupos_criados || 0;
                    
                    // Preparar preview (se a API retornar estrutura)
                    if (response.data.data.estrutura) {
                        this.previewEstrutura = response.data.data.estrutura;
                    }
                } else {
                    throw new Error(response.data?.message || 'Erro ao processar arquivo');
                }
            } catch (error) {
                console.error('Erro ao processar arquivo:', error);
                throw new Error(error.response?.data?.message || 'Erro ao processar arquivo Excel');
            }
        },
        
        // Voltar passo
        voltarPasso() {
            if (this.passoImportacao > 1) {
                this.passoImportacao--;
            }
        },
        
        // Confirmar importação
        async confirmarImportacao() {
            if (!this.dadosExcel || !this.tipoOrcamentoSelecionado) {
                this.mostrarToast('Dados inválidos para importação', 'error');
                return;
            }
            
            this.importando = true;
            this.passoImportacao = 3;
            
            try {
                await this.executarImportacao();
                this.mostrarToast('Importação concluída com sucesso!', 'success');
                await this.carregarDados();
            } catch (error) {
                this.mostrarToast('Erro na importação: ' + error.message, 'error');
                this.passoImportacao = 2; // Voltar para confirmação
            } finally {
                this.importando = false;
            }
        },
        
        // Executar importação (já foi feito upload, apenas confirmar)
        async executarImportacao() {
            try {
                // Progresso inicial
                this.progressoImportacao = 50;
                this.mensagemProgresso = 'Confirmando importação...';

                // Simular processamento final
                await new Promise(resolve => setTimeout(resolve, 1000));

                // Progresso final
                this.progressoImportacao = 100;
                this.mensagemProgresso = 'Importação concluída com sucesso!';

                // Aguardar um pouco para mostrar o progresso completo
                await new Promise(resolve => setTimeout(resolve, 500));

            } catch (error) {
                console.error('Erro na importação:', error);
                throw new Error('Erro ao confirmar importação');
            }
        },
        
        // Utilitários
        formatarTamanho(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        abrirModalLimparEstrutura() {
            if (this.modalLimparEstrutura) {
                this.modalLimparEstrutura.show();
            }
        },
        
        // Confirmar limpeza da estrutura
        async confirmarLimparEstrutura() {
            if (!this.tipoOrcamentoSelecionado) {
                this.mostrarToast('Tipo de orçamento não selecionado', 'error');
                return;
            }
            
            this.limpando = true;
            
            try {
                // Chamar API para limpar estrutura
                const response = await axios.delete(`/api/administracao/estrutura-orcamento/limpar/${this.tipoOrcamentoSelecionado}`);
                
                if (response.data && response.data.success) {
                    this.mostrarToast('Estrutura limpa com sucesso!', 'success');
                    
                    // Fechar modal
                    if (this.modalLimparEstrutura) {
                        this.modalLimparEstrutura.hide();
                    }
                    
                    // Limpar dados locais
                    this.grandesItens = [];
                    
                } else {
                    this.mostrarToast('Erro ao limpar estrutura', 'error');
                }
                
            } catch (error) {
                console.error('Erro ao limpar estrutura:', error);
                this.mostrarToast('Erro ao limpar estrutura: ' + (error.response?.data?.message || error.message), 'error');
            } finally {
                this.limpando = false;
            }
        }
    }
}
</script>

<style scoped>
/* CSS compartilhado movido para estrutura-orcamento-shared.css */

/* Estrutura da tabela */
.estrutura-container {
    padding: 0;
}

/* Linhas dos grandes itens */
.grande-item-row {
    background-color: #ffffff;
    transition: all 0.2s ease;
}

.grande-item-row:hover {
    background-color: #f8f9fa;
}

/* Linhas dos subgrupos expandidos */
.subgrupos-expanded {
    background-color: #f8f9fa;
}

/* Header dos subgrupos */
.subgrupos-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-top: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    padding: 0.75rem 1rem;
}

.subgrupos-header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.9rem;
}

/* Container da tabela de subgrupos */
.subgrupos-table-container {
    padding: 0.5rem 0;
    background: linear-gradient(90deg, #f8f9fa 0%, #ffffff 100%);
}

/* Conteúdo dos subgrupos */
.subgrupos-content {
    background: linear-gradient(90deg, #f8f9fa 0%, #ffffff 100%);
}

/* Estado vazio dos subgrupos */
.subgrupos-empty {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-top: 1px solid #e9ecef;
    color: #6c757d;
}

/* Linhas dos subgrupos */
.subgrupo-row {
    background-color: rgba(255, 255, 255, 0.9);
    transition: all 0.2s ease;
}

.subgrupo-row:hover {
    background-color: #ffffff;
}

/* Linha para adicionar grande item */
.adicionar-grande-item-row {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-top: 2px solid #e9ecef;
}

.adicionar-grande-item-row:hover {
    background: linear-gradient(135deg, #f0f8f0 0%, #f8fff9 100%);
}

/* Drag & Drop */
.drag-handle {
    cursor: grab;
    opacity: 0.6;
    transition: all 0.2s ease;
}

.drag-handle:hover {
    opacity: 1;
}

/* Header personalizado do modal - PADRÃO OBRIGATÓRIO */
.custom-modal-header {
    background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
    color: white;
    border-bottom: none;
    padding: 1.5rem;
    border-radius: 0.5rem 0.5rem 0 0;
}

/* Ícone do header - PADRÃO OBRIGATÓRIO */
.header-icon {
    width: 40px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
}

/* Botões de ação dos subgrupos - Estilo diferenciado */
.subgrupo-action-btn {
    border-width: 1px;
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
    transition: all 0.2s ease;
    background-color: rgba(255, 255, 255, 0.9);
}

/* ===== ESTILOS DE IMPORTAÇÃO ===== */

/* Container de importação */
.importacao-container {
    display: flex;
    align-items: center;
    flex-shrink: 0;
}

/* Container de limpeza */
.limpar-container {
    display: flex;
    align-items: center;
    flex-shrink: 0;
}

/* Ajuste do seletor para alinhar com o botão */
.seletor-container .d-flex {
    align-items: flex-end;
}

.seletor-container .tipo-orcamento-selector {
    flex: 1;
    min-width: 400px;
}

/* Área de upload */
.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 1rem;
    padding: 3rem 2rem;
    text-align: center;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.upload-area:hover,
.upload-area.drag-over {
    border-color: #5EA853;
    background-color: #e8f5e8;
}

.upload-area .file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.upload-content {
    pointer-events: none;
}

/* Informações do arquivo */
.arquivo-info {
    margin-top: 1rem;
}

/* Resumo da importação */
.resumo-importacao .card {
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.resumo-importacao .card-body {
    padding: 1.5rem;
}

/* Tabela de preview */
.preview-table {
    max-height: 300px;
    overflow-y: auto;
}

/* Progresso */
.progresso-step .progress {
    border-radius: 1rem;
}

.progresso-step .progress-bar {
    background: linear-gradient(135deg, #5EA853 0%, #18578A 100%);
}

.subgrupo-action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.subgrupo-action-btn.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.subgrupo-action-btn.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: #fff;
}

/* Responsividade */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .tipo-orcamento-selector .form-floating {
        min-width: 280px;
    }
    
    .tipo-orcamento-selector .form-control {
        padding: 0.375rem 0.75rem;
        min-height: 58px;
        font-size: 0.875rem;
    }
}
</style>
