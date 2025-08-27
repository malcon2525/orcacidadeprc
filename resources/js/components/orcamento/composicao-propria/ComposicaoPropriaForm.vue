<template>
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl modal-dialog-centered">
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
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           :class="{ 'is-invalid': errors.codigo }"
                                           id="codigo" 
                                           v-model="form.codigo"
                                           placeholder="Código da composição">
                                    <label for="codigo">Código</label>
                                </div>
                                <div class="invalid-feedback" v-if="errors.codigo">
                                    {{ errors.codigo[0] }}
                                </div>
                            </div>
                            
                            <div class="col-md-6">
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
                            
                            <div class="col-md-3">
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

                        <!-- Tabela de Itens -->
                        <div class="itens-container mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 fw-semibold text-custom">
                                    <i class="fas fa-list me-2"></i>Itens da Composição
                                </h6>
                                <button v-if="permissoes.crud" type="button" class="btn btn-sm btn-primary" @click="adicionarItem">
                                    <i class="fas fa-plus me-2"></i>Adicionar Item
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 80px;">#</th>
                                            <th style="width: 120px;">Referência</th>
                                            <th style="width: 100px;">Código</th>
                                            <th>Descrição</th>
                                            <th style="width: 80px;">Unidade</th>
                                            <th style="width: 100px;">Coeficiente</th>
                                            <th style="width: 120px;">Valor Mat/Equip</th>
                                            <th style="width: 120px;">Valor M.O.</th>
                                            <th style="width: 120px;">Valor Total</th>
                                            <th style="width: 120px;">Valor Ajustado</th>
                                            <th v-if="permissoes.crud" style="width: 80px;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in form.itens" :key="index" class="item-row">
                                            <td class="text-center align-middle">
                                                <span class="badge badge-status badge-ativo">{{ index + 1 }}</span>
                                            </td>
                                            
                                            <td>
                                                <select v-if="permissoes.crud" 
                                                        class="form-select form-select-sm" 
                                                        :class="{ 'is-invalid': errors[`itens.${index}.referencia`] }"
                                                        v-model="item.referencia"
                                                        @change="onReferenciaChange(index)"
                                                        required>
                                                    <option value="">Selecione</option>
                                                    <option value="SINAPI">SINAPI</option>
                                                    <option value="DERPR">DERPR</option>
                                                    <option value="PERSONALIZADA">PERSONALIZADA</option>
                                                </select>
                                                <span v-else class="badge badge-status badge-ativo">{{ item.referencia }}</span>
                                            </td>
                                            
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input v-if="permissoes.crud" 
                                                           type="text" 
                                                           class="form-control" 
                                                           :class="{ 'is-invalid': errors[`itens.${index}.codigo_item`] }"
                                                           v-model="item.codigo_item"
                                                           placeholder="Código"
                                                           required>
                                                    <button v-if="permissoes.crud && item.referencia !== 'PERSONALIZADA'" 
                                                            type="button" 
                                                            class="btn btn-outline-secondary btn-sm" 
                                                            @click="abrirZoomServico(index)">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                    <span v-else class="form-control-plaintext">{{ item.codigo_item }}</span>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <input v-if="permissoes.crud" 
                                                       type="text" 
                                                       class="form-control form-control-sm" 
                                                       :class="{ 'is-invalid': errors[`itens.${index}.descricao`] }"
                                                       v-model="item.descricao"
                                                       placeholder="Descrição do item"
                                                       required>
                                                <span v-else class="form-control-plaintext">{{ item.descricao }}</span>
                                            </td>
                                            
                                            <td>
                                                <input v-if="permissoes.crud" 
                                                       type="text" 
                                                       class="form-control form-control-sm" 
                                                       :class="{ 'is-invalid': errors[`itens.${index}.unidade`] }"
                                                       v-model="item.unidade"
                                                       placeholder="Unidade"
                                                       required>
                                                <span v-else class="form-control-plaintext">{{ item.unidade }}</span>
                                            </td>
                                            
                                            <td>
                                                <input v-if="permissoes.crud" 
                                                       type="number" 
                                                       class="form-control form-control-sm" 
                                                       :class="{ 'is-invalid': errors[`itens.${index}.coeficiente`] }"
                                                       v-model="item.coeficiente"
                                                       step="0.00001"
                                                       min="0"
                                                       placeholder="1.00000"
                                                       @input="calcularValoresAjustados(index)"
                                                       required>
                                                <span v-else class="form-control-plaintext">{{ formatarCoeficiente(item.coeficiente) }}</span>
                                            </td>
                                            
                                            <td>
                                                <input v-if="permissoes.crud" 
                                                       type="number" 
                                                       class="form-control form-control-sm" 
                                                       :class="{ 'is-invalid': errors[`itens.${index}.valor_mat_equip`] }"
                                                       v-model="item.valor_mat_equip"
                                                       step="0.01"
                                                       min="0"
                                                       placeholder="0.00"
                                                       @input="calcularValoresAjustados(index)"
                                                       required>
                                                <span v-else class="form-control-plaintext">R$ {{ formatarValor(item.valor_mat_equip) }}</span>
                                            </td>
                                            
                                            <td>
                                                <input v-if="permissoes.crud" 
                                                       type="number" 
                                                       class="form-control form-control-sm" 
                                                       :class="{ 'is-invalid': errors[`itens.${index}.valor_mao_obra`] }"
                                                       v-model="item.valor_mao_obra"
                                                       step="0.01"
                                                       min="0"
                                                       placeholder="0.00"
                                                       @input="calcularValoresAjustados(index)"
                                                       required>
                                                <span v-else class="form-control-plaintext">R$ {{ formatarValor(item.valor_mao_obra) }}</span>
                                            </td>
                                            
                                            <td>
                                                <input v-if="permissoes.crud" 
                                                       type="number" 
                                                       class="form-control form-control-sm" 
                                                       :class="{ 'is-invalid': errors[`itens.${index}.valor_total`] }"
                                                       v-model="item.valor_total"
                                                       step="0.01"
                                                       min="0"
                                                       placeholder="0.00"
                                                       @input="calcularValoresAjustados(index)"
                                                       required>
                                                <span v-else class="form-control-plaintext">R$ {{ formatarValor(item.valor_total) }}</span>
                                            </td>
                                            
                                            <td class="text-end">
                                                <span class="fw-semibold">R$ {{ formatarValor(item.valor_total_ajustado) }}</span>
                                            </td>
                                            
                                            <td v-if="permissoes.crud" class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger" @click="removerItem(index)" title="Remover">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Estado Vazio dos Itens -->
                            <div v-if="form.itens.length === 0" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-list fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Nenhum item adicionado</p>
                                    <small class="text-muted">Clique em "Adicionar Item" para começar</small>
                                </div>
                            </div>
                        </div>

                        <!-- Totais -->
                        <div class="totais-container mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input type="number" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.valor_total_mat_equip }"
                                               id="valor_total_mat_equip" 
                                               v-model="form.valor_total_mat_equip"
                                               step="0.01"
                                               min="0"
                                               placeholder="0.00"
                                               readonly>
                                        <label for="valor_total_mat_equip">Total Mat/Equip</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input type="number" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.valor_total_mao_obra }"
                                               id="valor_total_mao_obra" 
                                               v-model="form.valor_total_mao_obra"
                                               step="0.01"
                                               min="0"
                                               placeholder="0.00"
                                               readonly>
                                        <label for="valor_total_mao_obra">Total M.O.</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input type="number" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.valor_total_geral }"
                                               id="valor_total_geral" 
                                               v-model="form.valor_total_geral"
                                               step="0.01"
                                               min="0"
                                               placeholder="0.00"
                                               readonly>
                                        <label for="valor_total_geral">Total Geral</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-primary w-100" @click="calcularTotais">
                                        <i class="fas fa-calculator me-2"></i>Calcular Totais
                                    </button>
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
                codigo: '',
                descricao: '',
                unidade: '',
                valor_total_mat_equip: 0,
                valor_total_mao_obra: 0,
                valor_total_geral: 0,
                itens: []
            },
            errors: {},
            salvando: false,
            modalZoom: false,
            servicoZoomSelecionado: null,
            servicosZoom: [],
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
        this.inicializarFormulario();
    },
    methods: {
        inicializarFormulario() {
            if (this.composicao) {
                // Modo edição
                this.form = { ...this.composicao };
                this.form.itens = this.composicao.itens ? [...this.composicao.itens] : [];
            } else {
                // Modo criação
                this.form = {
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
            this.servicoZoomSelecionado = this.form.itens[index];
            this.modalZoom = true;
            this.buscarServicos();
        },

        fecharZoom() {
            this.modalZoom = false;
            this.servicoZoomSelecionado = null;
            this.servicosZoom = [];
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
                } else if (Array.isArray(data)) {
                    // Resposta direta sem wrapper
                    this.servicosZoom = data;
                    this.totalPaginasZoom = 1;
                } else {
                    console.error('Formato de resposta não reconhecido:', data);
                    this.servicosZoom = [];
                    this.totalPaginasZoom = 1;
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

            // Validação básica
            if (this.form.itens.length === 0) {
                alert('Adicione pelo menos um item à composição');
                return;
            }

            this.salvando = true;
            this.errors = {};

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
</style>
