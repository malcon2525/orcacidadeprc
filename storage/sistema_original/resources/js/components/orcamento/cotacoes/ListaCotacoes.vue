<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-custom">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Cotações
                </h5>
                <button class="btn btn-primary" @click="abrirModalCriar">
                    <i class="fas fa-plus me-2"></i>Nova Cotação
                </button>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <form class="row g-3 align-items-end mb-3" @submit.prevent="carregarDados(1)">
                    <div class="col-md-5">
                        <label class="form-label mb-1">Entidade Orçamentária</label>
                        <input type="text" class="form-control" v-model="filtros.entidade_orcamentaria_nome" placeholder="Digite para buscar..." @input="onEntidadeInput" @focus="mostrarSugestoes = true" @blur="ocultarSugestoesComDelay">
                        <ul v-if="mostrarSugestoes && sugestoesEntidade.length > 0" class="list-group position-absolute w-100 zindex-tooltip" style="max-height: 200px; overflow-y: auto; top: 100%;">
                            <li v-for="entidade in sugestoesEntidade" :key="entidade.id" class="list-group-item list-group-item-action" @mousedown.prevent="selecionarEntidade(entidade)">
                                {{ entidade.nome }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1">ID Orçamento</label>
                        <input type="text" class="form-control" v-model="filtros.orcamento_id" placeholder="Digite o ID do orçamento">
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-outline-success"><i class="fas fa-search"></i></button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="tabela-azul">
                            <tr>
                                <th class="text-custom">Código</th>
                                <th class="text-custom">Descrição</th>
                                <th class="text-custom">Entidade Orçamentária</th>
                                <th class="text-custom">Orçamento</th>
                                <th class="text-custom">Valor Final</th>
                                <th class="text-end text-custom">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in items.data" :key="item.id">
                                <td>{{ item.codigo }}</td>
                                <td>{{ item.descricao }}</td>
                                <td>{{ item.entidade_orcamentaria_nome }}</td>
                                <td>{{ item.orcamento_id }}</td>
                                <td>{{ formatarMoeda(item.valor_final) }}</td>
                                <td class="text-end">
                                    <button class="btn btn-outline-secondary btn-sm me-2" @click="editarItem(item)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" @click="excluirItem(item)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Mostrando {{ items.from }} até {{ items.to }} de {{ items.total }} registros
                        </div>
                        <nav v-if="items.last_page > 1">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item" :class="{ disabled: items.current_page === 1 }">
                                    <a class="page-link" href="#" @click.prevent="mudarPagina(items.current_page - 1)">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                                <li v-for="page in items.last_page" :key="page" class="page-item" :class="{ active: page === items.current_page }">
                                    <a class="page-link" href="#" @click.prevent="mudarPagina(page)">{{ page }}</a>
                                </li>
                                <li class="page-item" :class="{ disabled: items.current_page === items.last_page }">
                                    <a class="page-link" href="#" @click.prevent="mudarPagina(items.current_page + 1)">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Criar/Editar Cotação -->
        <div class="modal fade" id="cotacaoModal" tabindex="-1" ref="cotacaoModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-semibold">
                            <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
                            {{ modoEdicao ? 'Editar' : 'Nova' }} Cotação
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <form @submit.prevent="onSubmit" class="needs-validation" novalidate>
                            <div class="row mb-3 align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label">CÓDIGO</label>
                                    <input type="text" class="form-control" v-model="form.codigo" required :class="{'is-invalid': errors.codigo}">
                                    <div v-if="errors.codigo" class="invalid-feedback">{{ errors.codigo[0] }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">DESCRIÇÃO</label>
                                    <input type="text" class="form-control" v-model="form.descricao" required :class="{'is-invalid': errors.descricao}">
                                    <div v-if="errors.descricao" class="invalid-feedback">{{ errors.descricao[0] }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">UNIDADE</label>
                                    <input type="text" class="form-control" v-model="form.unidade" :class="{'is-invalid': errors.unidade}">
                                    <div v-if="errors.unidade" class="invalid-feedback">{{ errors.unidade[0] }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tipo Valor Final</label>
                                    <select class="form-select" v-model="form.tipo_valor_final" @change="atualizarValorFinal" :class="{'is-invalid': errors.tipo_valor_final}">
                                        <option value="media">Média</option>
                                        <option value="mediana">Mediana</option>
                                        <option value="menor_valor">Menor Valor</option>
                                        <option value="manual">Manual</option>
                                    </select>
                                    <div v-if="errors.tipo_valor_final" class="invalid-feedback">{{ errors.tipo_valor_final[0] }}</div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <label class="form-label">VALOR FINAL</label>
                                    <input :readonly="form.tipo_valor_final !== 'manual'" type="text" class="form-control fw-bold fs-5 px-3 py-1 bg-success bg-opacity-10 rounded text-success d-inline-block text-end" v-model="valorFinalInput" @input="onValorFinalManual" :class="{'is-invalid': errors.valor_final}">
                                    <div v-if="errors.valor_final" class="invalid-feedback">{{ errors.valor_final[0] }}</div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Entidade Orçamentária</label>
                                    <select class="form-select" v-model="form.entidade_orcamentaria_id" required :class="{'is-invalid': errors.entidade_orcamentaria_id}">
                                        <option value="">Escolha</option>
                                        <option v-for="entidade in entidadesOrcamentarias" :key="entidade.id" :value="entidade.id">
                                            {{ entidade.nome }}
                                        </option>
                                    </select>
                                    <div v-if="errors.entidade_orcamentaria_id" class="invalid-feedback">{{ errors.entidade_orcamentaria_id[0] }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ORÇAMENTO ID</label>
                                    <input type="text" class="form-control" v-model="form.orcamento_id" :class="{'is-invalid': errors.orcamento_id}">
                                    <div v-if="errors.orcamento_id" class="invalid-feedback">{{ errors.orcamento_id[0] }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Origem</label>
                                    <select class="form-select" v-model="form.origem" required :class="{'is-invalid': errors.origem}">
                                        <option value="">Escolha</option>
                                        <option value="entidade_orcamentaria">Entidade Orçamentária</option>
                                        <option value="orcamento">Orçamento</option>
                                    </select>
                                    <div v-if="errors.origem" class="invalid-feedback">{{ errors.origem[0] }}</div>
                                </div>
                            </div>
                            <hr class="my-4">
                            <div class="row g-3">
                                <div v-for="(fornecedor, idx) in form.fornecedores" :key="idx" class="col-md-4">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-header bg-light fw-semibold text-secondary">
                                            FORNECEDOR {{ idx + 1 }}
                                            <span class="float-end text-success fw-bold">{{ formatarMoeda(fornecedor.valor_total) }}</span>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <label class="form-label">Fornecedor</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" v-model="fornecedor.nome_fantasia" readonly style="background:#f5f5f5" placeholder="Selecione um fornecedor...">
                                                    <button class="btn btn-outline-secondary" type="button" @click="abrirZoomFornecedor(idx)"><i class="fas fa-ellipsis-h"></i></button>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">CNPJ/CPF</label>
                                                <input type="text" class="form-control" v-model="fornecedor.cnpj_cpf" readonly style="background:#f5f5f5" maxlength="18" required :class="{'is-invalid': errors[`fornecedores.${idx}.cnpj_cpf`]}" placeholder="Preenchido pelo zoom">
                                                <div v-if="errors[`fornecedores.${idx}.cnpj_cpf`]" class="invalid-feedback">{{ errors[`fornecedores.${idx}.cnpj_cpf`][0] }}</div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Telefone</label>
                                                <input type="text" class="form-control" v-model="fornecedor.telefone" readonly style="background:#f5f5f5" :class="{'is-invalid': errors[`fornecedores.${idx}.telefone`]}" placeholder="Preenchido pelo zoom">
                                                <div v-if="errors[`fornecedores.${idx}.telefone`]" class="invalid-feedback">{{ errors[`fornecedores.${idx}.telefone`][0] }}</div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">E-mail</label>
                                                <input type="email" class="form-control" v-model="fornecedor.email" readonly style="background:#f5f5f5" :class="{'is-invalid': errors[`fornecedores.${idx}.email`]}" placeholder="Preenchido pelo zoom">
                                                <div v-if="errors[`fornecedores.${idx}.email`]" class="invalid-feedback">{{ errors[`fornecedores.${idx}.email`][0] }}</div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Site</label>
                                                <input type="text" class="form-control" v-model="fornecedor.site" readonly style="background:#f5f5f5" :class="{'is-invalid': errors[`fornecedores.${idx}.site`]}" placeholder="Preenchido pelo zoom">
                                                <div v-if="errors[`fornecedores.${idx}.site`]" class="invalid-feedback">{{ errors[`fornecedores.${idx}.site`][0] }}</div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-2">
                                                <div class="col-6">
                                                    <label class="form-label">Mão de Obra (R$)</label>
                                                    <input type="number" step="0.01" class="form-control" v-model.number="fornecedor.mao_obra" @input="atualizarValorTotal(idx)" :class="{'is-invalid': errors[`fornecedores.${idx}.mao_obra`]}" placeholder="0,00">
                                                    <div v-if="errors[`fornecedores.${idx}.mao_obra`]" class="invalid-feedback">{{ errors[`fornecedores.${idx}.mao_obra`][0] }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label">Mat/Equip (R$)</label>
                                                    <input type="number" step="0.01" class="form-control" v-model.number="fornecedor.mat_equip" @input="atualizarValorTotal(idx)" :class="{'is-invalid': errors[`fornecedores.${idx}.mat_equip`]}" placeholder="0,00">
                                                    <div v-if="errors[`fornecedores.${idx}.mat_equip`]" class="invalid-feedback">{{ errors[`fornecedores.${idx}.mat_equip`][0] }}</div>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Valor Total (R$)</label>
                                                <input type="text" class="form-control fw-bold bg-light text-success" :value="formatarMoeda(fornecedor.valor_total)" readonly :class="{'is-invalid': errors[`fornecedores.${idx}.valor_total`]}" placeholder="0,00">
                                                <div v-if="errors[`fornecedores.${idx}.valor_total`]" class="invalid-feedback">{{ errors[`fornecedores.${idx}.valor_total`][0] }}</div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-6">
                                                    <label class="form-label">Data</label>
                                                    <input type="date" class="form-control" v-model="fornecedor.data" :class="{'is-invalid': errors[`fornecedores.${idx}.data`]}" placeholder="Data da cotação">
                                                    <div v-if="errors[`fornecedores.${idx}.data`]" class="invalid-feedback">{{ errors[`fornecedores.${idx}.data`][0] }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label">Arquivo (PDF)</label>
                                                    <input v-if="!fornecedor.arquivo" type="file" class="form-control" accept="application/pdf" @change="onFileChange($event, idx)">
                                                    <div v-if="fornecedor.arquivo" class="mt-1 d-flex align-items-center gap-2">
                                                        <span class="text-truncate">{{ fornecedor.arquivo_nome || 'Arquivo.pdf' }}</span>
                                                        <button type="button" class="btn btn-link btn-sm" @click="downloadArquivo(fornecedor)"><i class="fas fa-download"></i> Download</button>
                                                        <button type="button" class="btn btn-link btn-sm text-danger" @click="excluirArquivo(fornecedor)"><i class="fas fa-trash"></i> Excluir</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Observações</label>
                                                <textarea class="form-control" v-model="fornecedor.observacoes" rows="2" :class="{'is-invalid': errors[`fornecedores.${idx}.observacoes`]}" placeholder="Observações"></textarea>
                                                <div v-if="errors[`fornecedores.${idx}.observacoes`]" class="invalid-feedback">{{ errors[`fornecedores.${idx}.observacoes`][0] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-4">
                                <button v-if="!modoEdicao" class="btn btn-primary px-4" type="submit" :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    <i v-else class="fas fa-save me-2"></i>
                                    {{ loading ? 'Salvando...' : 'Adicionar Cotação' }}
                                </button>
                                <button v-else class="btn btn-success px-4" type="submit" :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    <i v-else class="fas fa-save me-2"></i>
                                    {{ loading ? 'Salvando...' : 'Salvar Alterações' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Zoom Fornecedor -->
        <div class="modal fade" id="zoomFornecedorModal" tabindex="-1" ref="zoomFornecedorModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Selecionar Fornecedor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" v-model="zoomBusca" placeholder="Buscar por nome fantasia ou CNPJ/CPF" @input="buscarFornecedoresZoom">
                                <button class="btn btn-outline-primary" type="button" @click="buscarFornecedoresZoom"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                        <div v-if="zoomNovoFornecedor" class="mb-3 border rounded p-3 bg-light-subtle">
                            <h6 class="mb-2">Novo Fornecedor</h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" v-model="zoomNovo.cnpj_cpf" placeholder="CNPJ/CPF">
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" v-model="zoomNovo.nome_fantasia" placeholder="Nome Fantasia">
                                </div>
                            </div>
                            <div class="row g-2 mt-2">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" v-model="zoomNovo.telefone" placeholder="Telefone">
                                </div>
                                <div class="col-md-4">
                                    <input type="email" class="form-control" v-model="zoomNovo.email" placeholder="E-mail">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" v-model="zoomNovo.site" placeholder="Site">
                                </div>
                            </div>
                            <div class="mt-2 text-end">
                                <button class="btn btn-success btn-sm" @click="cadastrarFornecedorZoom">Salvar e Selecionar</button>
                                <button class="btn btn-link btn-sm" @click="zoomNovoFornecedor = false">Cancelar</button>
                            </div>
                        </div>
                        <div v-else>
                            <table class="table table-sm table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>CNPJ/CPF</th>
                                        <th>Nome Fantasia</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="f in fornecedoresZoomModal" :key="f.id">
                                        <td>{{ f.cnpj_cpf }}</td>
                                        <td>{{ f.nome_fantasia }}</td>
                                        <td class="text-end">
                                            <button class="btn btn-outline-primary btn-sm" @click="selecionarFornecedorZoomModal(f)"><i class="fas fa-check"></i> Selecionar</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">Página {{ zoomPaginaAtual }} de {{ zoomTotalPaginas }}</div>
                                <div>
                                    <button class="btn btn-outline-secondary btn-sm me-1" :disabled="zoomPaginaAtual === 1" @click="buscarFornecedoresZoom(zoomPaginaAtual - 1)"><i class="fas fa-chevron-left"></i></button>
                                    <button class="btn btn-outline-secondary btn-sm" :disabled="zoomPaginaAtual === zoomTotalPaginas" @click="buscarFornecedoresZoom(zoomPaginaAtual + 1)"><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                            <div class="text-end mt-2">
                                <button class="btn btn-outline-success btn-sm" @click="zoomNovoFornecedor = true"><i class="fas fa-plus"></i> Novo Fornecedor</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            items: {
                data: [],
                current_page: 1,
                from: 0,
                to: 0,
                total: 0,
                last_page: 1
            },
            filtros: {
                entidade_orcamentaria_id: '',
                entidade_orcamentaria_nome: '',
                orcamento_id: ''
            },
            sugestoesEntidade: [],
            mostrarSugestoes: false,
            entidadesOrcamentarias: [],
            loading: false,
            errors: {},
            modoEdicao: false,
            form: {
                codigo: '',
                descricao: '',
                entidade_orcamentaria_id: '',
                orcamento_id: '',
                origem: '',
                unidade: '',
                tipo_valor_final: 'media',
                valor_final: '',
                fornecedores: [
                    { cnpj_cpf: '', nome_fantasia: '', telefone: '', email: '', site: '', mao_obra: '', mat_equip: '', valor_total: 0, data: '', arquivo: null, observacoes: '' },
                    { cnpj_cpf: '', nome_fantasia: '', telefone: '', email: '', site: '', mao_obra: '', mat_equip: '', valor_total: 0, data: '', arquivo: null, observacoes: '' },
                    { cnpj_cpf: '', nome_fantasia: '', telefone: '', email: '', site: '', mao_obra: '', mat_equip: '', valor_total: 0, data: '', arquivo: null, observacoes: '' }
                ]
            },
            valorFinalManual: '',
            cotacaoModal: null,
            fornecedoresZoom: [[], [], []],
            mostrarZoom: [false, false, false],
            zoomFornecedorIdx: null,
            zoomBusca: '',
            fornecedoresZoomModal: [],
            zoomNovoFornecedor: false,
            zoomNovo: { cnpj_cpf: '', nome_fantasia: '', telefone: '', email: '', site: '' },
            zoomTotalPaginas: 1,
            zoomPaginaAtual: 1
        }
    },
    mounted() {
        this.carregarEntidadesOrcamentarias();
        this.carregarDados();
    },
    computed: {
        valorFinalCotacao() {
            const totais = this.form.fornecedores.map(f => Number(f.valor_total) || 0);
            if (totais.length === 0) return 0;
            if (this.form.tipo_valor_final === 'media') {
                return (totais.reduce((a, b) => a + b, 0) / totais.length) || 0;
            } else if (this.form.tipo_valor_final === 'mediana') {
                const sorted = [...totais].sort((a, b) => a - b);
                return sorted[1] || 0;
            } else if (this.form.tipo_valor_final === 'menor_valor') {
                return Math.min(...totais);
            } else if (this.form.tipo_valor_final === 'manual') {
                return Number(this.valorFinalManual) || 0;
            }
            return 0;
        },
        valorFinalInput: {
            get() {
                if (this.form.tipo_valor_final === 'manual') {
                    return this.valorFinalManual;
                } else {
                    return this.formatarMoeda(this.valorFinalCotacao);
                }
            },
            set(val) {
                this.valorFinalManual = val;
            }
        }
    },
    methods: {
        async carregarEntidadesOrcamentarias() {
            try {
                const response = await axios.get('/entidades-orcamentarias/listar-select');
                this.entidadesOrcamentarias = response.data.data || [];
            } catch (e) {
                this.entidadesOrcamentarias = [];
            }
        },
        async carregarDados(page = 1) {
            try {
                const params = {
                    page,
                    entidade_orcamentaria_id: this.filtros.entidade_orcamentaria_id,
                    orcamento_id: this.filtros.orcamento_id
                };
                const res = await axios.get('/api/cotacoes', { params });
                this.items = res.data;
            } catch (e) {
                this.items = { data: [], current_page: 1, from: 0, to: 0, total: 0, last_page: 1 };
            }
        },
        mudarPagina(page) {
            this.items.current_page = page;
            this.carregarDados(page);
        },
        abrirModalCriar() {
            this.modoEdicao = false;
            this.resetForm();
            this.errors = {};
            this.showModal();
        },
        showModal() {
            if (!this.cotacaoModal) {
                this.cotacaoModal = new bootstrap.Modal(document.getElementById('cotacaoModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
            }
            this.cotacaoModal.show();
        },
        hideModal() {
            if (this.cotacaoModal) {
                this.cotacaoModal.hide();
            }
        },
        resetForm() {
            this.form = {
                codigo: '',
                descricao: '',
                entidade_orcamentaria_id: '',
                orcamento_id: '',
                origem: '',
                unidade: '',
                tipo_valor_final: 'media',
                valor_final: '',
                fornecedores: [
                    { cnpj_cpf: '', nome_fantasia: '', telefone: '', email: '', site: '', mao_obra: '', mat_equip: '', valor_total: 0, data: '', arquivo: null, observacoes: '' },
                    { cnpj_cpf: '', nome_fantasia: '', telefone: '', email: '', site: '', mao_obra: '', mat_equip: '', valor_total: 0, data: '', arquivo: null, observacoes: '' },
                    { cnpj_cpf: '', nome_fantasia: '', telefone: '', email: '', site: '', mao_obra: '', mat_equip: '', valor_total: 0, data: '', arquivo: null, observacoes: '' }
                ]
            };
        },
        atualizarValorTotal(idx) {
            const f = this.form.fornecedores[idx];
            f.valor_total = (Number(f.mao_obra) || 0) + (Number(f.mat_equip) || 0);
            this.atualizarValorFinal();
        },
        atualizarValorFinal() {
            if (this.form.tipo_valor_final !== 'manual') {
                this.valorFinalManual = '';
            }
        },
        onValorFinalManual(e) {
            if (this.form.tipo_valor_final === 'manual') {
                this.valorFinalManual = e.target.value;
            }
        },
        onFileChange(event, idx) {
            const file = event.target.files[0];
            if (file && file.type === 'application/pdf' && file.size <= 10 * 1024 * 1024) {
                this.form.fornecedores[idx].arquivo = file;
            } else {
                alert('Selecione um arquivo PDF de até 10MB.');
                event.target.value = '';
            }
        },
        async buscarFornecedoresZoom(page = 1) {
            try {
                const res = await axios.get('/api/fornecedores/buscar-select', {
                    params: { termo: this.zoomBusca, page, per_page: 10 }
                });
                this.fornecedoresZoomModal = res.data.data;
                this.zoomTotalPaginas = res.data.last_page;
                this.zoomPaginaAtual = res.data.current_page;
            } catch {
                this.fornecedoresZoomModal = [];
                this.zoomTotalPaginas = 1;
                this.zoomPaginaAtual = 1;
            }
        },
        selecionarFornecedorZoom(f, idx) {
            this.form.fornecedores[idx].cnpj_cpf = f.cnpj_cpf;
            this.form.fornecedores[idx].nome_fantasia = f.nome_fantasia;
            this.form.fornecedores[idx].telefone = f.telefone;
            this.form.fornecedores[idx].email = f.email;
            this.form.fornecedores[idx].site = f.site;
            this.form.fornecedores[idx].zoomTermo = `${f.nome_fantasia} (${f.cnpj_cpf})`;
            this.fornecedoresZoom[idx] = [];
            this.mostrarZoom[idx] = false;
        },
        selecionarFornecedorZoomModal(f) {
            if (this.zoomFornecedorIdx !== null) {
                this.form.fornecedores[this.zoomFornecedorIdx].cnpj_cpf = f.cnpj_cpf;
                this.form.fornecedores[this.zoomFornecedorIdx].nome_fantasia = f.nome_fantasia;
                this.form.fornecedores[this.zoomFornecedorIdx].telefone = f.telefone;
                this.form.fornecedores[this.zoomFornecedorIdx].email = f.email;
                this.form.fornecedores[this.zoomFornecedorIdx].site = f.site;
            }
            if (this.zoomFornecedorModal) {
                this.zoomFornecedorModal.hide();
            }
            this.zoomFornecedorIdx = null;
            this.zoomBusca = '';
        },
        ocultarZoomComDelay(idx) {
            setTimeout(() => { this.mostrarZoom[idx] = false; }, 150);
        },
        async salvarCotacao() {
            this.loading = true;
            this.errors = {};
            try {
                const formData = new FormData();
                formData.append('codigo', this.form.codigo);
                formData.append('descricao', this.form.descricao);
                formData.append('entidade_orcamentaria_id', this.form.entidade_orcamentaria_id);
                formData.append('orcamento_id', this.form.orcamento_id);
                formData.append('origem', this.form.origem);
                formData.append('unidade', this.form.unidade);
                formData.append('tipo_valor_final', this.form.tipo_valor_final);
                formData.append('valor_final', this.valorFinalCotacao);
                this.form.fornecedores.forEach((f, idx) => {
                    formData.append(`fornecedores[${idx}][cnpj_cpf]`, f.cnpj_cpf);
                    formData.append(`fornecedores[${idx}][nome_fantasia]`, f.nome_fantasia);
                    formData.append(`fornecedores[${idx}][telefone]`, f.telefone);
                    formData.append(`fornecedores[${idx}][email]`, f.email);
                    formData.append(`fornecedores[${idx}][site]`, f.site);
                    formData.append(`fornecedores[${idx}][mao_obra]`, f.mao_obra);
                    formData.append(`fornecedores[${idx}][mat_equip]`, f.mat_equip);
                    formData.append(`fornecedores[${idx}][valor_total]`, f.valor_total);
                    formData.append(`fornecedores[${idx}][data]`, f.data);
                    formData.append(`fornecedores[${idx}][observacoes]`, f.observacoes === null ? '' : f.observacoes);
                    if (f.arquivo instanceof File) {
                        formData.append(`fornecedores[${idx}][arquivo]`, f.arquivo);
                    } else if (typeof f.arquivo === 'string' && f.arquivo !== '') {
                        formData.append(`fornecedores[${idx}][arquivo]`, f.arquivo);
                    } else {
                        formData.append(`fornecedores[${idx}][arquivo]`, '');
                    }
                });
                await axios.post('/api/cotacoes', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                this.hideModal();
                this.carregarDados();
            } catch (error) {
                if (error.response && error.response.status === 422 && error.response.data && error.response.data.errors) {
                    this.errors = error.response.data.errors;
                    this.$nextTick(() => {
                        const el = document.querySelector('.is-invalid');
                        if (el) el.focus();
                    });
                } else {
                    alert('Erro ao salvar cotação.');
                }
            } finally {
                this.loading = false;
            }
        },
        editarItem(item) {
            this.modoEdicao = true;
            this.errors = {};
            this.form.id = item.id;
            // Preenche o form principal
            this.form.codigo = item.codigo;
            this.form.descricao = item.descricao;
            this.form.entidade_orcamentaria_id = item.entidade_orcamentaria_id;
            this.form.orcamento_id = item.orcamento_id;
            this.form.origem = item.origem;
            this.form.unidade = item.unidade;
            this.form.tipo_valor_final = item.tipo_valor_final;
            this.valorFinalManual = item.tipo_valor_final === 'manual' ? item.valor_final : '';
            // Preenche os fornecedores
            if (item.fornecedores && item.fornecedores.length > 0) {
                this.form.fornecedores = item.fornecedores.map(f => {
                    return {
                        cnpj_cpf: f.fornecedor ? f.fornecedor.cnpj_cpf : '',
                        nome_fantasia: f.fornecedor ? f.fornecedor.nome_fantasia : '',
                        telefone: f.fornecedor ? f.fornecedor.telefone : '',
                        email: f.fornecedor ? f.fornecedor.email : '',
                        site: f.fornecedor ? f.fornecedor.site : '',
                        mao_obra: f.mao_obra,
                        mat_equip: f.mat_equip,
                        valor_total: f.valor_total,
                        data: f.data,
                        observacoes: f.observacoes === null ? '' : f.observacoes,
                        arquivo: f.arquivo,
                        arquivo_nome: f.arquivo ? f.arquivo.split('/').pop() : null
                    };
                });
            }
            this.showModal();
        },
        excluirItem(item) {
            if (!confirm('Tem certeza que deseja excluir esta cotação? Esta ação não pode ser desfeita.')) {
                return;
            }
            this.loading = true;
            axios.delete(`/api/cotacoes/${item.id}`)
                .then(() => {
                    this.carregarDados();
                })
                .catch(() => {
                    alert('Erro ao excluir cotação.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        formatarMoeda(valor) {
            if (valor === null || valor === undefined) return '-';
            return Number(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        },
        onEntidadeInput() {
            this.filtros.entidade_orcamentaria_id = '';
            this.filtrarSugestoesEntidade();
        },
        filtrarSugestoesEntidade() {
            const termo = this.filtros.entidade_orcamentaria_nome.trim().toLowerCase();
            if (!termo) {
                this.sugestoesEntidade = this.entidadesOrcamentarias.slice(0, 10);
                return;
            }
            this.sugestoesEntidade = this.entidadesOrcamentarias.filter(e => e.nome.toLowerCase().includes(termo)).slice(0, 10);
        },
        selecionarEntidade(entidade) {
            this.filtros.entidade_orcamentaria_nome = entidade.nome;
            this.filtros.entidade_orcamentaria_id = entidade.id;
            this.mostrarSugestoes = false;
        },
        ocultarSugestoesComDelay() {
            setTimeout(() => { this.mostrarSugestoes = false; }, 150);
        },
        abrirZoomFornecedor(idx) {
            this.zoomFornecedorIdx = idx;
            this.zoomBusca = '';
            this.fornecedoresZoomModal = [];
            this.zoomNovoFornecedor = false;
            this.zoomNovo = { cnpj_cpf: '', nome_fantasia: '', telefone: '', email: '', site: '' };
            this.zoomTotalPaginas = 1;
            this.zoomPaginaAtual = 1;
            if (!this.zoomFornecedorModal) {
                this.zoomFornecedorModal = new bootstrap.Modal(document.getElementById('zoomFornecedorModal'), {
                    backdrop: 'static', keyboard: false
                });
            }
            this.zoomFornecedorModal.show();
            this.buscarFornecedoresZoom(1);
        },
        async cadastrarFornecedorZoom() {
            if (!this.zoomNovo.cnpj_cpf || !this.zoomNovo.nome_fantasia) {
                alert('Preencha CNPJ/CPF e Nome Fantasia.');
                return;
            }
            try {
                const res = await axios.post('/api/fornecedores', this.zoomNovo);
                const f = res.data;
                if (this.zoomFornecedorIdx !== null) {
                    this.form.fornecedores[this.zoomFornecedorIdx].cnpj_cpf = f.cnpj_cpf;
                    this.form.fornecedores[this.zoomFornecedorIdx].nome_fantasia = f.nome_fantasia;
                    this.form.fornecedores[this.zoomFornecedorIdx].telefone = f.telefone;
                    this.form.fornecedores[this.zoomFornecedorIdx].email = f.email;
                    this.form.fornecedores[this.zoomFornecedorIdx].site = f.site;
                }
                this.fornecedoresZoomModal.unshift(f);
                this.zoomBusca = '';
                this.zoomFornecedorModal.hide();
            } catch {
                alert('Erro ao cadastrar fornecedor.');
            }
        },
        downloadArquivo(fornecedor) {
            if (fornecedor.arquivo) {
                window.open(`/storage/${fornecedor.arquivo}`, '_blank');
            }
        },
        excluirArquivo(fornecedor) {
            if (fornecedor.arquivo) {
                fornecedor.arquivo = null;
                fornecedor.arquivo_nome = null;
            }
        },
        async atualizarCotacao() {
            this.loading = true;
            this.errors = {};
            try {
                const formData = new FormData();
                formData.append('codigo', this.form.codigo);
                formData.append('descricao', this.form.descricao);
                formData.append('entidade_orcamentaria_id', this.form.entidade_orcamentaria_id);
                formData.append('orcamento_id', this.form.orcamento_id);
                formData.append('origem', this.form.origem);
                formData.append('unidade', this.form.unidade);
                formData.append('tipo_valor_final', this.form.tipo_valor_final);
                formData.append('valor_final', this.valorFinalCotacao);
                this.form.fornecedores.forEach((f, idx) => {
                    formData.append(`fornecedores[${idx}][cnpj_cpf]`, f.cnpj_cpf);
                    formData.append(`fornecedores[${idx}][nome_fantasia]`, f.nome_fantasia);
                    formData.append(`fornecedores[${idx}][telefone]`, f.telefone);
                    formData.append(`fornecedores[${idx}][email]`, f.email);
                    formData.append(`fornecedores[${idx}][site]`, f.site);
                    formData.append(`fornecedores[${idx}][mao_obra]`, f.mao_obra);
                    formData.append(`fornecedores[${idx}][mat_equip]`, f.mat_equip);
                    formData.append(`fornecedores[${idx}][valor_total]`, f.valor_total);
                    formData.append(`fornecedores[${idx}][data]`, f.data);
                    formData.append(`fornecedores[${idx}][observacoes]`, f.observacoes === null ? '' : f.observacoes);
                    if (f.arquivo instanceof File) {
                        formData.append(`fornecedores[${idx}][arquivo]`, f.arquivo);
                    } else if (typeof f.arquivo === 'string' && f.arquivo !== '') {
                        formData.append(`fornecedores[${idx}][arquivo]`, f.arquivo);
                    } else {
                        formData.append(`fornecedores[${idx}][arquivo]`, '');
                    }
                });
                await axios.post(`/api/cotacoes/${this.form.id || this.form.codigo}`, formData, {
                    method: 'POST',
                    headers: { 'Content-Type': 'multipart/form-data', 'X-HTTP-Method-Override': 'PUT' }
                });
                this.hideModal();
                this.carregarDados();
            } catch (error) {
                if (error.response && error.response.data && error.response.data.errors) {
                    this.errors = error.response.data.errors;
                    this.$nextTick(() => {
                        const el = document.querySelector('.is-invalid');
                        if (el) el.focus();
                    });
                } else {
                    alert('Erro ao atualizar cotacao.');
                }
            } finally {
                this.loading = false;
            }
        },
        async onSubmit() {
            if (this.modoEdicao) {
                await this.atualizarCotacao();
            } else {
                await this.salvarCotacao();
            }
        }
    }
}
</script>

<style scoped>
    .tabela-azul th {
        background: #fff !important;
        font-weight: bold;
        border-bottom: 2px solid #e9ecef;
        vertical-align: middle;
    }
</style>
