<template>
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl modal-composicao-dialog">
            <div class="modal-content">
                <!-- Header com Gradiente -->
                <div class="modal-header custom-modal-header">
                    <div class="d-flex align-items-center">
                        <div class="header-icon me-3">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h5 class="modal-title mb-0">
                            {{ composicao ? 'Editar Composição Própria' : 'Nova Composição Própria' }}
                        </h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" @click="fecharModal"></button>
                </div>

                <div class="modal-body">
                    <form @submit.prevent="salvarFormulario">
                        <!-- Dados da Composição -->
                        <!-- Linha 1: Entidade Orçamentária + Código + Unidade -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control bg-light"
                                           id="entidade_orcamentaria_display"
                                           :value="getEntidadeOrcamentariaDisplay()"
                                           readonly
                                           placeholder="Entidade do contexto">
                                    <label for="entidade_orcamentaria_display">Entidade Orçamentária</label>
                                </div>
                                <!-- Campo hidden para enviar o ID -->
                                <input type="hidden" v-model="form.entidade_orcamentaria_id">
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           :class="{ 'is-invalid': errors.codigo }"
                                           id="codigo" 
                                           v-model="form.codigo"
                                           placeholder="Código da composição"
                                           required>
                                    <label for="codigo">Código *</label>
                                </div>
                                <div class="invalid-feedback" v-if="errors.codigo">
                                    {{ errors.codigo[0] }}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           :class="{ 'is-invalid': errors.unidade }"
                                           id="unidade" 
                                           v-model="form.unidade"
                                           placeholder="Unidade de medida"
                                           required>
                                    <label for="unidade">Unidade *</label>
                                </div>
                                <div class="invalid-feedback" v-if="errors.unidade">
                                    {{ errors.unidade[0] }}
                                </div>
                            </div>
                        </div>

                        <!-- Linha 2: Descrição (largura total) -->
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           :class="{ 'is-invalid': errors.descricao }"
                                           id="descricao" 
                                           v-model="form.descricao"
                                           placeholder="Descrição da composição"
                                           required>
                                    <label for="descricao">Descrição *</label>
                                </div>
                                <div class="invalid-feedback" v-if="errors.descricao">
                                    {{ errors.descricao[0] }}
                                </div>
                            </div>
                        </div>

                        <!-- Header com Busca de Itens -->
                        <div class="itens-header mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 fw-semibold text-custom">
                                    <i class="fas fa-list me-2"></i>Itens da Composição
                                </h6>
                                <button v-if="permissoes.crud" type="button" class="btn btn-primary" @click="adicionarItem">
                                    <i class="fas fa-plus me-2"></i>Adicionar Item
                                </button>
                            </div>
                            
                            <!-- <div class="row g-3 align-items-center" v-if="form.itens.length > 0">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               placeholder="Buscar itens..."
                                               v-model="buscaItens">
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <small class="text-muted">{{ itensFiltrados.length }} {{ itensFiltrados.length === 1 ? 'item' : 'itens' }}</small>
                                </div>
                            </div> -->
                        </div>

                        <!-- Lista de Itens -->
                        <div class="itens-container mb-4">
                            <!-- Estado Vazio -->
                            <div v-if="form.itens.length === 0" class="empty-state-modern">
                                <div class="empty-icon">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h5 class="empty-title">Nenhum item adicionado</h5>
                                <p class="empty-text">Comece adicionando itens à sua composição</p>
                                <button v-if="permissoes.crud" 
                                        type="button" 
                                        class="btn btn-primary" 
                                        @click="adicionarItem">
                                    <i class="fas fa-plus me-2"></i>Adicionar Primeiro Item
                                </button>
                            </div>

                            <!-- Lista de Cards -->
                            <div v-else class="itens-cards">
                                <div v-for="(item, index) in itensFiltrados" :key="index" class="item-card-modern">
                                    <!-- Header do Card -->
                                    <div class="item-header">
                                        <div class="item-numero">
                                            <span class="numero-badge">{{ index + 1 }}</span>
                                        </div>
                                        <div class="item-referencia-select">
                                            <select v-if="permissoes.crud" 
                                                    class="form-select form-select-sm" 
                                                    v-model="item.referencia"
                                                    @change="onReferenciaChange(index)"
                                                    required>
                                                <option value="">Selecionar</option>
                                                <option value="SINAPI">SINAPI</option>
                                                <option value="DERPR">DERPR</option>
                                                <option value="PERSONALIZADA">PERSONALIZADA</option>
                                            </select>
                                            <span v-else class="referencia-badge">{{ item.referencia }}</span>
                                        </div>
                                        <div class="item-actions">
                                            <button v-if="permissoes.crud" 
                                                    type="button" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    @click="removerItem(index)"
                                                    title="Remover item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Corpo do Card -->
                                    <div class="item-body">
                                        <!-- Linha 1: Código + Data Base + Unidade -->
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-4">
                                                <label class="field-label">Código</label>
                                                <div v-if="permissoes.crud" class="input-group">
                                                    <input type="text" 
                                                           class="form-control" 
                                                           v-model="item.codigo_item"
                                                           placeholder="Ex: 74209/1"
                                                           required>
                                                    <button v-if="item.referencia !== 'PERSONALIZADA'" 
                                                            type="button" 
                                                            class="btn btn-zoom-search" 
                                                            @click="abrirZoomServico(index)"
                                                            title="Buscar serviço">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                                <div v-else class="field-static">{{ item.codigo_item || '-' }}</div>
                                            </div>
                                            <div v-if="item.referencia && item.referencia !== 'PERSONALIZADA'" class="col-md-4">
                                                <label class="field-label">Data Base</label>
                                                <input type="text" 
                                                       class="form-control bg-light" 
                                                       :value="getDataBaseItem(item)"
                                                       readonly
                                                       placeholder="Será preenchida automaticamente">
                                            </div>
                                            <div v-if="item.referencia && item.referencia !== 'PERSONALIZADA'" class="col-md-2">
                                                <label class="field-label">Desoneração</label>
                                                <input type="text" 
                                                       class="form-control bg-light" 
                                                       :value="getDesoneracaoItem(item)"
                                                       readonly
                                                       placeholder="Automático">
                                            </div>
                                            <div :class="item.referencia === 'PERSONALIZADA' ? 'col-md-8' : 'col-md-2'">
                                                <label class="field-label">Unidade</label>
                                                <input v-if="permissoes.crud" 
                                                       type="text" 
                                                       class="form-control" 
                                                       v-model="item.unidade"
                                                       placeholder="m², kg, un"
                                                       required>
                                                <div v-else class="field-static">{{ item.unidade || '-' }}</div>
                                            </div>
                                        </div>

                                        <!-- Linha 2: Descrição (largura total) -->
                                        <div class="row g-2 mb-2">
                                            <div class="col-12">
                                                <label class="field-label">Descrição</label>
                                                <input v-if="permissoes.crud" 
                                                       type="text" 
                                                       class="form-control" 
                                                       v-model="item.descricao"
                                                       placeholder="Descrição detalhada do item"
                                                       required>
                                                <div v-else class="field-static">{{ item.descricao || '-' }}</div>
                                            </div>
                                        </div>

                                        <!-- Linha 3: Valores -->
                                        <div class="row g-2">
                                            <div class="col-md-2">
                                                <label class="field-label">Coeficiente</label>
                                                <input v-if="permissoes.crud" 
                                                       type="number" 
                                                       step="0.00001" 
                                                       class="form-control text-end" 
                                                       v-model="item.coeficiente"
                                                       @input="calcularValoresAjustados(index)"
                                                       placeholder="1,00000"
                                                       required>
                                                <div v-else class="field-static text-end">{{ formatarCoeficiente(item.coeficiente) }}</div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="field-label">Mat/Equip</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">R$</span>
                                                    <input v-if="permissoes.crud" 
                                                           type="number" 
                                                           step="0.01" 
                                                           class="form-control text-end" 
                                                           v-model="item.valor_mat_equip"
                                                           @input="calcularValoresAjustados(index)"
                                                           placeholder="0,00"
                                                           required>
                                                    <div v-else class="form-control text-end bg-light">{{ formatarValor(item.valor_mat_equip) }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="field-label">Mão de Obra</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">R$</span>
                                                    <input v-if="permissoes.crud" 
                                                           type="number" 
                                                           step="0.01" 
                                                           class="form-control text-end" 
                                                           v-model="item.valor_mao_obra"
                                                           @input="calcularValoresAjustados(index)"
                                                           placeholder="0,00"
                                                           required>
                                                    <div v-else class="form-control text-end bg-light">{{ formatarValor(item.valor_mao_obra) }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="field-label">Total</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">R$</span>
                                                    <input v-if="permissoes.crud" 
                                                           type="number" 
                                                           step="0.01" 
                                                           class="form-control text-end" 
                                                           v-model="item.valor_total"
                                                           @input="calcularValoresAjustados(index)"
                                                           placeholder="0,00"
                                                           required>
                                                    <div v-else class="form-control text-end bg-light">{{ formatarValor(item.valor_total) }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="field-label text-success">Valor Ajustado</label>
                                                <div class="valor-ajustado-final">
                                                    R$ {{ formatarValor(item.valor_total_ajustado) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cards de Totais Modernos -->
                        <div class="totais-modernos mb-4">
                            <div class="row g-3">
                                <div class="col-lg-3 col-md-6">
                                    <div class="total-card-modern mat-equip">
                                        <div class="total-icon">
                                            <i class="fas fa-tools"></i>
                                        </div>
                                        <div class="total-content">
                                            <div class="total-label">Total Mat/Equip</div>
                                            <div class="total-valor">R$ {{ formatarValor(form.valor_total_mat_equip) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="total-card-modern mao-obra">
                                        <div class="total-icon">
                                            <i class="fas fa-hard-hat"></i>
                                        </div>
                                        <div class="total-content">
                                            <div class="total-label">Total M.O.</div>
                                            <div class="total-valor">R$ {{ formatarValor(form.valor_total_mao_obra) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="total-card-modern geral">
                                        <div class="total-icon">
                                            <i class="fas fa-calculator"></i>
                                        </div>
                                        <div class="total-content">
                                            <div class="total-label">Total Geral</div>
                                            <div class="total-valor">R$ {{ formatarValor(form.valor_total_geral) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="total-card-modern final">
                                        <div class="total-content-final">
                                            <div class="total-label-final">Valor Final</div>
                                            <div class="total-valor-final">R$ {{ formatarValor(form.valor_total_geral) }}</div>
                                            <!-- <button v-if="permissoes.crud" 
                                                    type="button" 
                                                    class="btn btn-sm btn-light mt-2" 
                                                    @click="calcularTotais">
                                                <i class="fas fa-sync-alt me-1"></i>Calcular Totais
                                            </button> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="fecharModal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button v-if="permissoes.crud" type="button" class="btn btn-primary" @click="salvarFormulario" :disabled="salvando">
                        <span v-if="salvando" class="spinner-border spinner-border-sm me-2"></span>
                        <i v-else class="fas fa-save me-2"></i>
                        {{ salvando ? 'Salvando...' : 'Salvar' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Zoom de Serviços -->
        <div v-if="modalZoom" class="modal fade show d-block modal-zoom" tabindex="-1" style="background-color: rgba(0,0,0,0.7);">
            <div class="modal-dialog modal-zoom-size modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header custom-modal-header">
                        <h5 class="modal-title mb-0">
                            <i class="fas fa-search me-2"></i>Buscar Serviço - {{ servicoZoomSelecionado?.referencia || '' }}
                            <span v-if="metaZoom && metaZoom.data_base_formatada" class="badge bg-info ms-2">{{ metaZoom.data_base_formatada }}</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" @click="fecharZoom"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Filtros de Busca -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           id="buscaServico" 
                                           v-model="filtrosZoom.busca"
                                           placeholder="Buscar por código ou descrição"
                                           @input="debounceBuscarServicosFn">
                                    <label for="buscaServico">Buscar Serviço</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select" 
                                            id="desoneracao" 
                                            v-model="filtrosZoom.desoneracao"
                                            @change="buscarServicos">
                                        <option value="sem">Sem Desoneração</option>
                                        <option value="com">Com Desoneração</option>
                                    </select>
                                    <label for="desoneracao">Desoneração</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary w-100 h-58" @click="buscarServicos">
                                    <i class="fas fa-search me-2"></i>Buscar
                                </button>
                            </div>
                        </div>

                        <!-- Lista de Serviços Compacta -->
                        <div class="servicos-container-zoom">
                            <div v-if="carregandoZoom" class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="text-muted mt-2 mb-0">Buscando serviços...</p>
                            </div>
                            
                            <div v-else-if="servicosZoom.length > 0" class="servicos-list-compact">
                                <div v-for="servico in servicosZoom" :key="servico.codigo" class="servico-item-compact" @click="selecionarServico(servico)">
                                    <!-- Linha Principal -->
                                    <div class="servico-linha-principal">
                                        <div class="servico-info-basica">
                                            <span class="codigo-servico">{{ servico.codigo }}</span>
                                            <span class="unidade-servico">{{ servico.unidade }}</span>
                                            <span class="data-servico" v-if="servico.data_base">{{ formatarDataBase(servico.data_base) }}</span>
                                            <span class="badge-desoneracao" :class="servico.desoneracao === 'sem' ? 'sem-desoner' : 'com-desoner'">
                                                {{ servico.desoneracao === 'sem' ? 'Sem Desoneração' : 'Com Desoneração' }}
                                            </span>
                                        </div>
                                        <div class="servico-acoes">
                                            <i class="fas fa-mouse-pointer text-muted"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Descrição -->
                                    <div class="servico-descricao-compact">{{ servico.descricao }}</div>
                                    
                                    <!-- Valores Inline -->
                                    <div class="servico-valores-compact">
                                        <!-- Valores SINAPI -->
                                        <div v-if="servicoZoomSelecionado?.referencia === 'SINAPI'" class="valores-inline">
                                            <span class="valor-item-inline">
                                                <strong>Mão de obra:</strong> R$ {{ formatarValor(servico.valor_mao_obra) }}
                                            </span>
                                            <span class="separador">|</span>
                                            <span class="valor-item-inline">
                                                <strong>Mat/Equip:</strong> R$ {{ formatarValor(servico.valor_mat_equip) }}
                                            </span>
                                            <span class="separador">|</span>
                                            <span class="valor-item-inline valor-total-inline">
                                                <strong>Total:</strong> R$ {{ formatarValor(servico.valor_total) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Valores DERPR -->
                                        <div v-else class="valores-inline">
                                            <span class="valor-item-inline valor-total-inline">
                                                <strong>Custo unitário:</strong> R$ {{ formatarValor(servico.custo_unitario) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estado Vazio -->
                        <div v-if="servicosZoom.length === 0 && !carregandoZoom" class="text-center py-4">
                            <div class="empty-state">
                                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Nenhum serviço encontrado</p>
                                <small class="text-muted">Tente ajustar os filtros de busca</small>
                            </div>
                        </div>

                        <!-- Paginação -->
                        <div v-if="totalPaginasZoom > 1" class="paginacao-container mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Página {{ paginaAtualZoom }} de {{ totalPaginasZoom }}
                                </div>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0 pagination-zoom">
                                        <!-- Primeira página -->
                                        <li class="page-item" :class="{ disabled: paginaAtualZoom === 1 }">
                                            <button class="page-link" @click="mudarPaginaZoom(1)" title="Primeira página">
                                                <i class="fas fa-angle-double-left"></i>
                                            </button>
                                        </li>
                                        <!-- Página anterior -->
                                        <li class="page-item" :class="{ disabled: paginaAtualZoom === 1 }">
                                            <button class="page-link" @click="mudarPaginaZoom(paginaAtualZoom - 1)" title="Página anterior">
                                                <i class="fas fa-angle-left"></i>
                                            </button>
                                        </li>
                                        <!-- Páginas visíveis -->
                                        <li v-for="pagina in paginasVisiveisZoom" :key="pagina" class="page-item" :class="{ active: pagina === paginaAtualZoom }">
                                            <button class="page-link" @click="mudarPaginaZoom(pagina)">{{ pagina }}</button>
                                        </li>
                                        <!-- Próxima página -->
                                        <li class="page-item" :class="{ disabled: paginaAtualZoom === totalPaginasZoom }">
                                            <button class="page-link" @click="mudarPaginaZoom(paginaAtualZoom + 1)" title="Próxima página">
                                                <i class="fas fa-angle-right"></i>
                                            </button>
                                        </li>
                                        <!-- Última página -->
                                        <li class="page-item" :class="{ disabled: paginaAtualZoom === totalPaginasZoom }">
                                            <button class="page-link" @click="mudarPaginaZoom(totalPaginasZoom)" title="Última página">
                                                <i class="fas fa-angle-double-right"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Erros de Validação - Padrão do Projeto -->
        <div v-if="modalErros" class="modal fade show d-block modal-erros-overlay" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered" style="z-index: 1000000 !important; position: relative !important;">
                <div class="modal-content modal-validacao" style="z-index: 1000001 !important; position: relative !important;">
                    <!-- Header com Gradiente Padrão -->
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon" aria-hidden="true">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h5 class="modal-title mb-0">Erros de Validação</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" @click="fecharModalErros" aria-label="Fechar"></button>
                    </div>
                    
                    <!-- Corpo do Modal -->
                    <div class="modal-body text-center">
                        <p class="confirm-text mb-3">Os seguintes erros precisam ser corrigidos antes de salvar:</p>
                        
                        <!-- Lista de Erros -->
                        <div class="lista-erros-validacao">
                            <div v-for="(erro, index) in listaErros" :key="index" class="erro-item-validacao">
                                <i class="fas fa-times-circle erro-bullet-validacao"></i>
                                <span class="erro-texto-validacao">{{ erro }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rodapé -->
                    <div class="modal-footer justify-content-center border-0">
                        <button type="button" class="btn btn-success" @click="fecharModalErros">
                            <i class="fas fa-check me-2"></i>Entendi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// Removido import do lodash - usando debounce manual

export default {
    name: 'ComposicaoPropriaForm',
    props: {
        composicao: {
            type: Object,
            default: null
        },
        permissoes: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            form: {
                entidade_orcamentaria_id: '',
                codigo: '',
                descricao: '',
                unidade: '',
                valor_total_mat_equip: 0,
                valor_total_mao_obra: 0,
                valor_total_geral: 0,
                itens: []
            },
            entidadesOrcamentarias: [],
            contextoInfo: null, // Informações do contexto orçamentário
            errors: {},
            salvando: false,
            buscaItens: '',
            modalZoom: false,
            modalErros: false,
            listaErros: [],
            servicoZoomSelecionado: null,
            servicosZoom: [],
            metaZoom: null,
            carregandoZoom: false,
            filtrosZoom: {
                busca: '',
                desoneracao: 'sem',
                dataBase: null
            },
            paginaAtualZoom: 1,
            totalPaginasZoom: 1,
            searchTimeoutZoom: null
        };
    },
    computed: {
        itensFiltrados() {
            if (!this.buscaItens) {
                return this.form.itens;
            }
            
            const busca = this.buscaItens.toLowerCase();
            return this.form.itens.filter(item => {
                return (
                    (item.codigo_item && item.codigo_item.toLowerCase().includes(busca)) ||
                    (item.descricao && item.descricao.toLowerCase().includes(busca)) ||
                    (item.referencia && item.referencia.toLowerCase().includes(busca)) ||
                    (item.unidade && item.unidade.toLowerCase().includes(busca))
                );
            });
        },
        
        paginasVisiveisZoom() {
            const paginas = [];
            const inicio = Math.max(1, this.paginaAtualZoom - 2);
            const fim = Math.min(this.totalPaginasZoom, this.paginaAtualZoom + 2);
            
            for (let i = inicio; i <= fim; i++) {
                paginas.push(i);
            }
            
            return paginas;
        }
    },
    mounted() {
        this.buscarEntidadesOrcamentarias();
        this.inicializarFormulario();
    },
    methods: {
        async buscarEntidadesOrcamentarias() {
            try {
                const response = await fetch('/api/orcamento/composicao-propria/entidades-orcamentarias');
                const data = await response.json();
                
                if (data.success) {
                    this.entidadesOrcamentarias = data.data;
                    // Captura as informações do contexto orçamentário
                    this.contextoInfo = data.context_info || null;
                    
                    // Define automaticamente a entidade do contexto (apenas se não estiver definida)
                    if (this.entidadesOrcamentarias.length > 0 && !this.form.entidade_orcamentaria_id) {
                        this.form.entidade_orcamentaria_id = this.entidadesOrcamentarias[0].id;
                    }
                } else {
                    console.error('Erro ao buscar entidades:', data.message);
                }
            } catch (error) {
                console.error('Erro na requisição de entidades:', error);
            }
        },

        inicializarFormulario() {
            if (this.composicao) {
                // Modo edição
                this.form = { ...this.composicao };
                this.form.itens = this.composicao.itens ? [...this.composicao.itens] : [];
            } else {
                // Modo criação
                this.form = {
                    entidade_orcamentaria_id: '',
                    codigo: '',
                    descricao: '',
                    unidade: '',
                    valor_total_mat_equip: 0,
                    valor_total_mao_obra: 0,
                    valor_total_geral: 0,
                    itens: []
                };
            }
        },

        adicionarItem() {
            const novoItem = {
                referencia: 'PERSONALIZADA',
                codigo_item: '',
                descricao: '',
                unidade: '',
                coeficiente: 1.00000,
                valor_mat_equip: 0,
                valor_mao_obra: 0,
                valor_total: 0,
                valor_mat_equip_ajustado: 0,
                valor_mao_obra_ajustado: 0,
                valor_total_ajustado: 0
            };
            
            this.form.itens.push(novoItem);
        },

        removerItem(index) {
            this.form.itens.splice(index, 1);
            this.calcularTotais();
        },

        onReferenciaChange(index) {
            const item = this.form.itens[index];
            if (item.referencia === 'PERSONALIZADA') {
                item.codigo_item = '';
                item.descricao = '';
                item.unidade = '';
                item.valor_mat_equip = 0;
                item.valor_mao_obra = 0;
                item.valor_total = 0;
            }
            this.calcularValoresAjustados(index);
        },

        calcularValoresAjustados(index) {
            const item = this.form.itens[index];
            const coeficiente = parseFloat(item.coeficiente) || 0;
            
            item.valor_mat_equip_ajustado = (parseFloat(item.valor_mat_equip) || 0) * coeficiente;
            item.valor_mao_obra_ajustado = (parseFloat(item.valor_mao_obra) || 0) * coeficiente;
            item.valor_total_ajustado = (parseFloat(item.valor_total) || 0) * coeficiente;
            
            this.calcularTotais();
        },

        calcularTotais() {
            let totalMatEquip = 0;
            let totalMaoObra = 0;
            let totalGeral = 0;

            this.form.itens.forEach(item => {
                totalMatEquip += parseFloat(item.valor_mat_equip_ajustado) || 0;
                totalMaoObra += parseFloat(item.valor_mao_obra_ajustado) || 0;
                totalGeral += parseFloat(item.valor_total_ajustado) || 0;
            });

            this.form.valor_total_mat_equip = totalMatEquip;
            this.form.valor_total_mao_obra = totalMaoObra;
            this.form.valor_total_geral = totalGeral;
        },

        abrirZoomServico(index) {
            this.servicoZoomSelecionado = {
                ...this.form.itens[index],
                indice: index
            };
            this.modalZoom = true;
            this.buscarServicos();
        },

        fecharZoom() {
            this.modalZoom = false;
            this.servicoZoomSelecionado = null;
            this.servicosZoom = [];
            this.metaZoom = null;
        },

        async buscarServicos() {
            if (!this.servicoZoomSelecionado) return;

            this.carregandoZoom = true;
            try {
                const referencia = this.servicoZoomSelecionado.referencia;
                const endpoint = referencia === 'SINAPI' ? '/api/tabela_oficial/consultar_sinapi/zoom_servicos' : '/api/tabela_oficial/consultar_derpr/zoom_servicos';
                
                // Constrói parâmetros baseado na referência
                let params = `page=${this.paginaAtualZoom}`;
                
                // Adiciona desoneração
                params += `&desoneracao=${this.filtrosZoom.desoneracao}`;
                
                // Adiciona data_base se especificada
                if (this.filtrosZoom.dataBase) {
                    params += `&data_base=${this.filtrosZoom.dataBase}`;
                }
                
                // Adiciona busca se preenchida
                if (this.filtrosZoom.busca) {
                    if (referencia === 'SINAPI') {
                        params += `&termo=${encodeURIComponent(this.filtrosZoom.busca)}`;
                    } else {
                        params += `&search=${encodeURIComponent(this.filtrosZoom.busca)}`;
                    }
                }
                
                const response = await fetch(`${endpoint}?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                // Verifica se é resposta de paginação Laravel
                if (data.data && Array.isArray(data.data)) {
                    this.servicosZoom = data.data;
                    this.totalPaginasZoom = data.last_page;
                    // Capturar metadados se disponíveis
                    this.metaZoom = data.meta || null;
                } else if (Array.isArray(data)) {
                    // Resposta direta sem wrapper
                    this.servicosZoom = data;
                    this.totalPaginasZoom = 1;
                    this.metaZoom = null;
                } else {
                    console.error('Formato de resposta não reconhecido:', data);
                    this.servicosZoom = [];
                    this.totalPaginasZoom = 1;
                    this.metaZoom = null;
                }
            } catch (error) {
                console.error('Erro ao buscar serviços:', error);
            } finally {
                this.carregandoZoom = false;
            }
        },

        debounceBuscarServicosFn() {
            // Limpar o timeout anterior se existir
            if (this.searchTimeoutZoom) {
                clearTimeout(this.searchTimeoutZoom);
            }
            
            // Criar novo timeout com debounce de 300ms
            this.searchTimeoutZoom = setTimeout(() => {
                this.buscarServicos();
            }, 300);
        },

        selecionarServico(servico) {
            if (!this.servicoZoomSelecionado) return;

            const index = this.form.itens.findIndex(item => item === this.servicoZoomSelecionado);
            if (index !== -1) {
                this.form.itens[index].codigo_item = servico.codigo;
                this.form.itens[index].descricao = servico.descricao;
                this.form.itens[index].unidade = servico.unidade;
                
                // Mapeia campos baseado na referência
                if (this.servicoZoomSelecionado.referencia === 'SINAPI') {
                    this.form.itens[index].valor_mat_equip = servico.valor_mat_equip || 0;
                    this.form.itens[index].valor_mao_obra = servico.valor_mao_obra || 0;
                    this.form.itens[index].valor_total = servico.valor_total || 0;
                } else if (this.servicoZoomSelecionado.referencia === 'DERPR') {
                    // DERPR só tem custo_unitario
                    this.form.itens[index].valor_mat_equip = 0;
                    this.form.itens[index].valor_mao_obra = 0;
                    this.form.itens[index].valor_total = servico.custo_unitario || 0;
                }
                
                this.calcularValoresAjustados(index);
            }

            this.fecharZoom();
        },

        mudarPaginaZoom(pagina) {
            if (pagina >= 1 && pagina <= this.totalPaginasZoom) {
                this.paginaAtualZoom = pagina;
                this.buscarServicos();
            }
        },

        async salvarFormulario() {
            if (!this.permissoes.crud) return;

            // Limpar erros anteriores
            this.errors = {};

            // Validações obrigatórias
            const erros = {};

            if (!this.form.entidade_orcamentaria_id) {
                erros.entidade_orcamentaria_id = ['A entidade orçamentária é obrigatória.'];
            }

            if (!this.form.codigo || this.form.codigo.trim() === '') {
                erros.codigo = ['O código é obrigatório.'];
            }

            if (!this.form.descricao || this.form.descricao.trim() === '') {
                erros.descricao = ['A descrição é obrigatória.'];
            }

            if (!this.form.unidade || this.form.unidade.trim() === '') {
                erros.unidade = ['A unidade é obrigatória.'];
            }

            if (this.form.itens.length === 0) {
                erros.itens = ['Pelo menos um item deve ser adicionado à composição.'];
            }

            if (this.form.valor_total_geral <= 0) {
                erros.valor_total_geral = ['O valor total deve ser maior que zero.'];
            }

            // Se houver erros, exibir modal detalhado
            if (Object.keys(erros).length > 0) {
                this.errors = erros;
                this.listaErros = Object.values(erros).flat();
                this.modalErros = true;
                return;
            }

            this.salvando = true;

            try {
                if (this.composicao) {
                    // Atualizar
                    this.$emit('atualizar', this.form);
                } else {
                    // Criar
                    this.$emit('salvar', this.form);
                }
            } catch (error) {
                console.error('Erro ao salvar:', error);
            } finally {
                this.salvando = false;
            }
        },

        fecharModal() {
            this.$emit('fechar');
        },

        fecharModalErros() {
            this.modalErros = false;
            this.listaErros = [];
        },

        mostrarErrosValidacao(errors) {
            // Método para ser chamado pelo componente pai quando há erros do backend
            this.errors = errors;
            this.listaErros = Object.values(errors).flat();
            this.modalErros = true;
        },

        mostrarToast(message, type = 'info') {
            // Implementação simples - pode ser melhorada com biblioteca de toast
            const types = {
                'success': '✅',
                'error': '❌', 
                'warning': '⚠️',
                'info': 'ℹ️'
            };
            
            alert(`${types[type] || types.info} ${message}`);
        },

        formatarValor(valor) {
            return parseFloat(valor || 0).toFixed(2).replace('.', ',');
        },

        formatarDataBase(dataBase) {
            if (!dataBase) return '';
            // Converte YYYY-MM-DD para MM/YYYY
            const [ano, mes] = dataBase.split('-');
            const meses = {
                '01': 'JAN', '02': 'FEV', '03': 'MAR', '04': 'ABR',
                '05': 'MAI', '06': 'JUN', '07': 'JUL', '08': 'AGO',
                '09': 'SET', '10': 'OUT', '11': 'NOV', '12': 'DEZ'
            };
            return `${meses[mes]} / ${ano}`;
        },

        formatarCoeficiente(coeficiente) {
            return parseFloat(coeficiente || 0).toFixed(5).replace('.', ',');
        },

        /**
         * Formata data base para exibição no formato mm/yyyy
         */
        formatarDataBase(data) {
            if (!data) return '';
            
            // Se já está no formato esperado (YYYY-MM-DD)
            if (typeof data === 'string' && data.includes('-')) {
                const [ano, mes] = data.split('-');
                const meses = {
                    '01': 'JAN', '02': 'FEV', '03': 'MAR', '04': 'ABR',
                    '05': 'MAI', '06': 'JUN', '07': 'JUL', '08': 'AGO',
                    '09': 'SET', '10': 'OUT', '11': 'NOV', '12': 'DEZ'
                };
                return `${meses[mes]} / ${ano}`;
            }
            
            return data;
        },

        /**
         * Formata data para exibição completa
         */
        formatarDataParaExibicao(data) {
            if (!data) return '';
            
            if (typeof data === 'string' && data.includes('-')) {
                const [ano, mes, dia] = data.split('-');
                return `${dia || '01'}/${mes}/${ano}`;
            }
            
            return data;
        },

        /**
         * Retorna o nome da entidade orçamentária para exibição
         */
        getEntidadeOrcamentariaDisplay() {
            if (this.entidadesOrcamentarias && this.entidadesOrcamentarias.length > 0) {
                return this.entidadesOrcamentarias[0].jurisdicao_razao_social;
            }
            return 'Carregando...';
        },

        /**
         * Retorna a desoneração aplicada no item
         */
        getDesoneracaoItem(item) {
            if (!item.referencia || item.referencia === 'PERSONALIZADA') {
                return '';
            }
            
            // 1. Verifica se o item já tem desoneração salva
            if (item.desoneracao) {
                return item.desoneracao === 'com' ? 'Com Desoneração' : 'Sem Desoneração';
            }
            
            // 2. Se não tem desoneração no item, mostra o padrão (que será aplicado)
            return 'Sem Desoneração (será aplicada)';
        },

        /**
         * Retorna a data base apropriada para o item baseada na referência
         */
        getDataBaseItem(item) {
            if (!item.referencia || item.referencia === 'PERSONALIZADA') {
                return '';
            }
            
            // 1. Verifica se o item já tem data base salva
            if (item.referencia === 'SINAPI' && item.data_base_sinapi) {
                return this.formatarDataBase(item.data_base_sinapi);
            }
            
            if (item.referencia === 'DERPR' && item.data_base_derpr) {
                return this.formatarDataBase(item.data_base_derpr);
            }
            
            // 2. Se não tem data no item, usa a data do contexto (para itens novos)
            if (this.contextoInfo) {
                if (item.referencia === 'SINAPI' && this.contextoInfo.data_base_sinapi) {
                    return this.formatarDataBase(this.contextoInfo.data_base_sinapi) + ' (será aplicada)';
                }
                
                if (item.referencia === 'DERPR' && this.contextoInfo.data_base_derpr) {
                    return this.formatarDataBase(this.contextoInfo.data_base_derpr) + ' (será aplicada)';
                }
            }
            
            return 'Não definida';
        },

        /**
         * Seleciona um serviço do zoom e aplica ao item
         */
        selecionarServico(servico) {
            if (this.servicoZoomSelecionado && this.servicoZoomSelecionado.indice !== undefined) {
                const indice = this.servicoZoomSelecionado.indice;
                
                // Aplica os dados do serviço ao item
                this.form.itens[indice].codigo_item = servico.codigo;
                this.form.itens[indice].descricao = servico.descricao;
                this.form.itens[indice].unidade = servico.unidade;
                this.form.itens[indice].valor_mat_equip = servico.valor_mat_equip || 0;
                this.form.itens[indice].valor_mao_obra = servico.valor_mao_obra || 0;
                this.form.itens[indice].valor_total = servico.valor_total || 0;
                
                // Captura a desoneração utilizada na busca
                this.form.itens[indice].desoneracao = this.filtrosZoom.desoneracao || 'sem';
                
                // Recalcula valores ajustados
                this.calcularValoresAjustados(indice);
                
                // Fecha o modal
                this.modalZoom = false;
                this.servicoZoomSelecionado = null;
                this.servicosZoom = [];
                this.metaZoom = null;
            }
        }
    }
};
</script>

<style scoped>
.custom-modal-header {
    background: linear-gradient(135deg, #5EA853 0%, #18578A 100%);
    color: white;
    border-bottom: none;
}

.header-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.itens-container {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    border: 1px solid #dee2e6;
}

.totais-container {
    background-color: #e9ecef;
    padding: 1rem;
    border-radius: 0.5rem;
    border: 1px solid #dee2e6;
}

.item-row:hover {
    background-color: #f8f9fa;
}

.servico-row:hover {
    background-color: #f8f9fa;
}

.empty-state {
    color: #6c757d;
}

.badge-status {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-weight: 500;
}

.badge-ativo {
    background-color: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
}

.form-control[readonly] {
    background-color: #e9ecef;
    cursor: not-allowed;
}

/* Modal Zoom - configurado via CSS global modern-interface.css */
/* Z-index definido globalmente para evitar conflitos */

/* ===== MODAL ZOOM - TAMANHO CUSTOMIZADO ===== */
.modal-zoom-size {
    max-width: 60vw;
    width: 60vw;
    height: 95vh;
    max-height: 95vh;
}

.modal-zoom-size .modal-content {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.modal-zoom-size .modal-body {
    flex: 1;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

/* ===== LAYOUT COMPACTO DO MODAL ZOOM ===== */
.servicos-container-zoom {
    flex: 1;
    overflow-y: auto;
    margin-bottom: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: #fff;
}

.servicos-list-compact {
    display: flex;
    flex-direction: column;
}

.servico-item-compact {
          border-bottom: 1px solid #f0f0f0;
      padding: 8px 12px;
      transition: all 0.15s ease;
      cursor: pointer;
}

.servico-item-compact:hover {
    background-color: #e8f5e8;
    border-left: 3px solid #5EA853;
    padding-left: 9px;
}

.servico-item-compact:last-child {
    border-bottom: none;
}

/* Linha Principal */
.servico-linha-principal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
}

.servico-info-basica {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.codigo-servico {
    font-weight: 700;
    color: #18578A;
    font-size: 0.9rem;
    min-width: 65px;
}

.unidade-servico {
    background: #18578A;
    color: white;
    padding: 1px 6px;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
    min-width: 28px;
    text-align: center;
    line-height: 1.2;
}

.data-servico {
    background: #5EA853;
    color: white;
    padding: 1px 6px;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
    line-height: 1.2;
}

.badge-desoneracao {
    font-size: 0.6rem;
    padding: 1px 4px;
    border-radius: 6px;
    font-weight: 500;
    line-height: 1.2;
    opacity: 0.7;
}

.badge-desoneracao.sem-desoner {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f1aeb5;
}

.badge-desoneracao.com-desoner {
    background: #d1e7dd;
    color: #0a3622;
    border: 1px solid #a3cfbb;
}

.servico-acoes {
    flex-shrink: 0;
}

.btn-selecionar-compact {
    font-size: 0.75rem;
    padding: 3px 8px;
    font-weight: 600;
    border-radius: 12px;
    line-height: 1.2;
}

/* Descrição Compacta */
.servico-descricao-compact {
    font-size: 0.8rem;
    line-height: 1.3;
    color: #333;
    margin-bottom: 4px;
    font-weight: 500;
}

/* Valores Inline */
.servico-valores-compact {
    margin-top: 4px;
}

.valores-inline {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.valor-item-inline {
    font-size: 0.75rem;
    color: #666;
}

.valor-item-inline strong {
    color: #333;
    font-weight: 600;
}

.valor-total-inline strong {
    color: #5EA853;
}

.separador {
    color: #ccc;
    font-weight: normal;
}

/* ===== PAGINAÇÃO DO ZOOM ===== */
.pagination-zoom .page-link {
    color: #6c757d;
    background-color: #fff;
    border: 1px solid #dee2e6;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.pagination-zoom .page-link:hover {
    color: #5EA853;
    background-color: #e8f5e8;
    border-color: #5EA853;
}

.pagination-zoom .page-item.active .page-link {
    color: #fff;
    background-color: #5EA853;
    border-color: #5EA853;
}

.pagination-zoom .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    opacity: 0.5;
}

/* Responsividade */
@media (max-width: 768px) {
    .modal-zoom-size {
        max-width: 95%;
        width: 95%;
        height: 90vh;
        max-height: 90vh;
    }
    
    .servico-linha-principal {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
    
    .servico-acoes {
        align-self: flex-end;
        width: 100%;
    }
    
    .valores-inline {
        flex-direction: column;
        align-items: flex-start;
        gap: 2px;
    }
    
    .separador {
        display: none;
    }
    
    .paginacao-container {
        flex-direction: column;
        gap: 8px;
    }
    
    .paginacao-container .d-flex {
        flex-direction: column;
        gap: 8px;
    }
}

/* ===== MODAL CONFIGURADO PARA NOTEBOOKS ===== */
.modal-composicao-dialog {
    max-width: 95vw;
    height: 90vh;
    margin: 5vh auto;
}

.modal-composicao-dialog .modal-content {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.modal-composicao-dialog .modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 1rem 1.5rem;
}

/* ===== LAYOUT MODERNO DOS ITENS ===== */
.empty-state-modern {
    text-align: center;
    padding: 2rem 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%);
    border-radius: 8px;
    border: 2px dashed #dee2e6;
}

.empty-icon {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.empty-title {
    color: #495057;
    margin-bottom: 0.5rem;
}

.empty-text {
    color: #6c757d;
    margin-bottom: 1.5rem;
}

/* Cards dos Itens */
.itens-cards {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.item-card-modern {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    overflow: hidden;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.item-card-modern:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    border-color: #5EA853;
}

/* Header do Card Item */
.item-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.numero-badge {
    background: #18578A;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 50%;
    font-size: 0.8rem;
    font-weight: 600;
    min-width: 28px;
    text-align: center;
}

.item-referencia-select {
    min-width: 140px;
}

.referencia-badge {
    background: #5EA853;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.item-actions {
    margin-left: auto;
}

/* Corpo do Card Item */
.item-body {
    padding: 0.75rem;
}

.field-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.field-static {
    background: #f8f9fa;
    padding: 0.375rem 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    min-height: 38px;
    display: flex;
    align-items: center;
    color: #495057;
}

.valor-ajustado-final {
    background: linear-gradient(135deg, #d1e7dd 0%, #f8d7da 100%);
    color: #0f5132;
    font-weight: 700;
    font-size: 1.1rem;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    text-align: center;
    border: 1px solid #badbcc;
}

/* Cards de Totais Modernos */
.totais-modernos {
    margin-top: 1rem;
}

.total-card-modern {
    background: #fff;
    border-radius: 6px;
    padding: 0.75rem;
    text-align: center;
    transition: all 0.2s ease;
    border: 1px solid #e0e0e0;
    height: 100%;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.total-card-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.total-card-modern.mat-equip {
    border-left: 4px solid #18578A;
}

.total-card-modern.mao-obra {
    border-left: 4px solid #6f42c1;
}

.total-card-modern.geral {
    border-left: 4px solid #fd7e14;
}

.total-card-modern.final {
    background: linear-gradient(135deg, #5EA853 0%, #18578A 100%);
    color: white;
    text-align: center;
    display: block;
}

.total-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.mat-equip .total-icon {
    background: rgba(24, 87, 138, 0.1);
    color: #18578A;
}

.mao-obra .total-icon {
    background: rgba(111, 66, 193, 0.1);
    color: #6f42c1;
}

.geral .total-icon {
    background: rgba(253, 126, 20, 0.1);
    color: #fd7e14;
}

.total-content {
    flex: 1;
    text-align: left;
}

.total-label {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.total-valor {
    font-size: 1.1rem;
    font-weight: 700;
    color: #495057;
}

.total-content-final {
    text-align: center;
}

.total-label-final {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    opacity: 0.9;
}

.total-valor-final {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

/* Botão Zoom Search - Cores Padrão */
.btn-zoom-search {
    background-color: #5EA853;
    border-color: #5EA853;
    color: white;
    transition: all 0.2s ease;
}

.btn-zoom-search:hover {
    background-color: #18578A;
    border-color: #18578A;
    color: white;
}

.btn-zoom-search:focus {
    background-color: #18578A;
    border-color: #18578A;
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(24, 87, 138, 0.25);
}

.btn-zoom-search:active {
    background-color: #18578A !important;
    border-color: #18578A !important;
    color: white !important;
}

/* Responsividade */
@media (max-width: 768px) {
    .item-header {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .item-actions {
        margin-left: 0;
        width: 100%;
    }
    
    .item-actions .btn-group {
        width: 100%;
    }
    
    .item-actions .btn {
        flex: 1;
    }
    
    .total-card-modern {
        text-align: center;
        flex-direction: column;
    }
    
    .total-content {
        text-align: center;
    }
}

/* ===== MODAL DE VALIDAÇÃO - PADRÃO DO PROJETO ===== */
/* Força a exibição do modal de erros */
.modal-erros-overlay {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    z-index: 999999 !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background-color: rgba(0,0,0,0.8) !important;
}

/* Modal de Validação - Segue padrão dos modais do projeto */
.modal-validacao {
    border-radius: 0.75rem !important;
    overflow: hidden !important;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
    z-index: 1000002 !important;
    position: relative !important;
}

/* Lista de Erros de Validação */
.lista-erros-validacao {
    background: #FFF5F5 !important;
    border: 1px solid #FAD4D4 !important;
    border-radius: 0.75rem !important;
    padding: 1rem !important;
    margin: 0 !important;
    text-align: left !important;
}

.erro-item-validacao {
    display: flex !important;
    align-items: flex-start !important;
    margin-bottom: 0.75rem !important;
    gap: 0.75rem !important;
}

.erro-item-validacao:last-child {
    margin-bottom: 0 !important;
}

.erro-bullet-validacao {
    color: #D64545 !important;
    font-size: 1rem !important;
    flex-shrink: 0 !important;
    margin-top: 0.1rem !important;
}

.erro-texto-validacao {
    color: #5f6b7a !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
    line-height: 1.4 !important;
    flex: 1 !important;
}
</style>
