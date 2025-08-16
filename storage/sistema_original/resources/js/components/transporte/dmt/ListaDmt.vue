<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho -->
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold" style="color: #5EA853;">
                    <i class="fas fa-truck me-2"></i>DMT - Dados de Materiais de Transporte
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-success" @click="abrirModalGerarDmts" :disabled="!filtros.id_entidade_orcamentaria">
                        <i class="fas fa-sync-alt me-2"></i>Gerar DMTs para Entidade
                    </button>
                </div>
            </div>

            <!-- Filtro obrigatório de Entidade Orçamentária -->
            <div class="card-body">
                <div class="row mb-3 align-items-end">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" v-model="filtros.id_entidade_orcamentaria">
                                <option value="">Selecione uma Entidade Orçamentária</option>
                                <option v-for="e in selects.entidades_orcamentarias" :key="e.id" :value="e.id">{{ e.nome }}</option>
                            </select>
                            <label>Entidade Orçamentária <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" @click="pesquisarDmts" :disabled="!filtros.id_entidade_orcamentaria">
                            <i class="fas fa-search me-2"></i>Pesquisar
                        </button>
                    </div>
                </div>
                <div v-if="!tabelaVisivel" class="alert alert-info mt-3">
                    Selecione uma entidade orçamentária e clique em <b>Pesquisar</b> para visualizar os DMTs.
                </div>

                <!-- Tabela -->
                <div v-if="tabelaVisivel" class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="fw-semibold text-custom" style="width:100px;">Código</th>
                                <th class="fw-semibold text-custom" style="width:220px;">Nome do Material</th>
                                <th class="fw-semibold text-custom" style="width:250px;">Origem</th>
                                <th class="fw-semibold text-custom" style="width:150px;">Destino</th>
                                <th class="fw-semibold text-custom" style="width:90px;">Sigla</th>
                                <th class="fw-semibold text-custom" style="width:100px;">X1 (Km)</th>
                                <th class="fw-semibold text-custom" style="width:100px;">X2 (Km)</th>
                                <th class="fw-semibold text-custom" style="width:130px;">Tipo</th>
                                <th class="fw-semibold text-custom" style="width:150px;">Município</th>
                                <th class="fw-semibold text-custom" style="width:200px;">Entidade Orçamentária</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(item, idx) in dados" :key="item.id">
                                <tr v-if="idx === 0 || item.destino !== dados[idx-1].destino" class="table-group-destino">
                                    <td :colspan="11" class="group-destino-cell">
                                        <i class="fas fa-map-marker-alt me-2 group-destino-icon"></i>{{ item.destino }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:100px;">{{ item.codigo_material }}</td>
                                    <td style="width:220px;">{{ item.nome_material }}</td>
                                    <td class="col-origem">
                                        <input type="text" v-model="item.origem" class="form-control form-control-sm" :maxlength="150" />
                                    </td>
                                    <td style="width:150px;">{{ item.destino }}</td>
                                    <td style="width:90px;">{{ item.sigla_transporte }}</td>
                                    <td class="x-col">
                                        <input type="number" v-model.number="item.x1" class="form-control form-control-sm" step="0.01" />
                                    </td>
                                    <td class="x-col">
                                        <input type="number" v-model.number="item.x2" class="form-control form-control-sm" step="0.01" />
                                    </td>
                                    <td style="width:130px;">{{ item.tipo }}</td>
                                    <td style="width:150px;">{{ item.municipio ? item.municipio.nome : 'não atribuído' }}</td>
                                    <td style="width:200px;">{{ item.entidade_orcamentaria ? (item.entidade_orcamentaria.nome || item.entidade_orcamentaria.razao_social) : 'não atribuído' }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Adicionar botão Salvar alterações ao final da tabela -->
                <div v-if="tabelaVisivel" class="d-flex justify-content-end mt-3">
                    <button class="btn btn-success" @click="salvarAlteracoes" :disabled="salvando">
                        <i class="fas fa-save me-2"></i>Salvar alterações
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Formulário -->
        <div class="modal fade" id="modalForm" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ form.id ? 'Editar' : 'Novo' }} DMT</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="salvar">
                            <div class="mb-3">
                                <label class="form-label">Código do Material</label>
                                <input type="text" class="form-control" v-model="form.codigo_material" maxlength="10" required>
                                <div v-if="erros.codigo_material" class="text-danger small">{{ erros.codigo_material }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nome do Material</label>
                                <input type="text" class="form-control" v-model="form.nome_material" maxlength="255" required>
                                <div v-if="erros.nome_material" class="text-danger small">{{ erros.nome_material }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Origem</label>
                                <input type="text" class="form-control" v-model="form.origem" maxlength="150">
                                <div v-if="erros.origem" class="text-danger small">{{ erros.origem }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Destino</label>
                                <input type="text" class="form-control" v-model="form.destino" maxlength="150">
                                <div v-if="erros.destino" class="text-danger small">{{ erros.destino }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sigla do Transporte</label>
                                <input type="text" class="form-control" v-model="form.sigla_transporte" maxlength="3" required>
                                <div v-if="erros.sigla_transporte" class="text-danger small">{{ erros.sigla_transporte }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo</label>
                                <select class="form-select" v-model="form.tipo" required>
                                    <option value="local">Local</option>
                                    <option value="comercial">Comercial</option>
                                </select>
                                <div v-if="erros.tipo" class="text-danger small">{{ erros.tipo }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">X1 (Km)</label>
                                <input type="number" class="form-control" v-model="form.x1" step="0.01" required>
                                <div v-if="erros.x1" class="text-danger small">{{ erros.x1 }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">X2 (Km)</label>
                                <input type="number" class="form-control" v-model="form.x2" step="0.01" required>
                                <div v-if="erros.x2" class="text-danger small">{{ erros.x2 }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Município</label>
                                <select class="form-select" v-model="form.id_municipio">
                                    <option value="">Selecione (opcional)</option>
                                    <option v-for="m in selects.municipios" :key="m.id" :value="m.id">{{ m.nome }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Entidade Orçamentária</label>
                                <select class="form-select" v-model="form.id_entidade_orcamentaria" required>
                                    <option value="">Selecione</option>
                                    <option v-for="e in selects.entidades_orcamentarias" :key="e.id" :value="e.id">{{ e.nome }}</option>
                                </select>
                                <div v-if="erros.id_entidade_orcamentaria" class="text-danger small">{{ erros.id_entidade_orcamentaria }}</div>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação de Exclusão -->
        <div class="modal fade" id="modalConfirmacao" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Exclusão</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza que deseja excluir este DMT?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" @click="excluir">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Gerar DMTs para Entidade -->
        <div class="modal fade" id="modalGerarDmts" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Gerar DMTs para Entidade Orçamentária</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="gerarDmtsParaEntidade">
                            <div class="mb-3">
                                <label class="form-label">Entidade Orçamentária <span class="text-danger">*</span></label>
                                <select class="form-select" v-model="gerarDmt.entidade" required>
                                    <option value="">Selecione</option>
                                    <option v-for="e in selects.entidades_orcamentarias" :key="e.id" :value="e.id">{{ e.nome }}</option>
                                </select>
                                <div v-if="errosGerar.entidade" class="text-danger small">{{ errosGerar.entidade }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Município (opcional)</label>
                                <select class="form-select" v-model="gerarDmt.municipio">
                                    <option value="">Selecione</option>
                                    <option v-for="m in selects.municipios" :key="m.id" :value="m.id">{{ m.nome }}</option>
                                </select>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">Gerar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast de sucesso -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div ref="toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ toastMessage }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
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
            dados: [],
            filtros: {
                id_entidade_orcamentaria: ''
            },
            paginacao: {
                current_page: 1,
                from: 0,
                to: 0,
                total: 0,
                prev_page_url: null,
                next_page_url: null
            },
            tabelaVisivel: false,
            form: {
                id: null,
                codigo_material: '',
                nome_material: '',
                origem: '',
                destino: '',
                sigla_transporte: '',
                tipo: 'local',
                x1: 0,
                x2: 0,
                id_municipio: '',
                id_entidade_orcamentaria: ''
            },
            selects: {
                municipios: [],
                entidades_orcamentarias: []
            },
            erros: {},
            gerarDmt: {
                entidade: '',
                municipio: ''
            },
            errosGerar: {},
            salvando: false,
            toastMessage: '',
        };
    },
    mounted() {
        this.carregarSelects();
    },
    methods: {
        carregarSelects() {
            axios.get('/dmt/selects').then(res => {
                this.selects.municipios = res.data.municipios;
                this.selects.entidades_orcamentarias = res.data.entidades_orcamentarias;
            });
        },
        pesquisarDmts() {
            if (!this.filtros.id_entidade_orcamentaria) {
                this.tabelaVisivel = false;
                return;
            }
            this.tabelaVisivel = true;
            this.carregarDados(1);
        },
        carregarDados(pagina = 1) {
            let params = { ...this.filtros, page: pagina };
            axios.get('/dmt/listar', { params }).then(res => {
                this.dados = res.data.data;
                this.paginacao = {
                    current_page: res.data.current_page,
                    from: res.data.from,
                    to: res.data.to,
                    total: res.data.total,
                    prev_page_url: res.data.prev_page_url,
                    next_page_url: res.data.next_page_url
                };
            });
        },
        carregarPagina(pagina) {
            if (pagina < 1) return;
            this.carregarDados(pagina);
        },
        abrirModalCriar() {
            this.form = {
                id: null,
                codigo_material: '',
                nome_material: '',
                origem: '',
                destino: '',
                sigla_transporte: '',
                tipo: 'local',
                x1: 0,
                x2: 0,
                id_municipio: '',
                id_entidade_orcamentaria: ''
            };
            let modal = new bootstrap.Modal(document.getElementById('modalForm'));
            modal.show();
        },
        editar(item) {
            this.form = { ...item, id_municipio: item.id_municipio || '', id_entidade_orcamentaria: item.id_entidade_orcamentaria || '' };
            let modal = new bootstrap.Modal(document.getElementById('modalForm'));
            modal.show();
        },
        validarFormulario() {
            this.erros = {};
            if (!this.form.codigo_material || this.form.codigo_material.length > 10) {
                this.erros.codigo_material = 'Código do Material é obrigatório e deve ter até 10 caracteres.';
            }
            if (!this.form.nome_material || this.form.nome_material.length > 255) {
                this.erros.nome_material = 'Nome do Material é obrigatório e deve ter até 255 caracteres.';
            }
            if (this.form.origem && this.form.origem.length > 150) {
                this.erros.origem = 'Origem deve ter até 150 caracteres.';
            }
            if (this.form.destino && this.form.destino.length > 150) {
                this.erros.destino = 'Destino deve ter até 150 caracteres.';
            }
            if (!this.form.sigla_transporte || this.form.sigla_transporte.length > 3) {
                this.erros.sigla_transporte = 'Sigla do Transporte é obrigatória e deve ter até 3 caracteres.';
            }
            if (this.form.x1 === 0 || isNaN(this.form.x1)) {
                this.erros.x1 = 'X1 é obrigatório e deve ser um número.';
            }
            if (this.form.x2 === 0 || isNaN(this.form.x2)) {
                this.erros.x2 = 'X2 é obrigatório e deve ser um número.';
            }
            if (!this.form.tipo || (this.form.tipo !== 'local' && this.form.tipo !== 'comercial')) {
                this.erros.tipo = 'Tipo é obrigatório e deve ser "local" ou "comercial".';
            }
            if (!this.form.id_entidade_orcamentaria) {
                this.erros.id_entidade_orcamentaria = 'Entidade Orçamentária é obrigatória.';
            }
            // id_municipio é opcional
            return Object.keys(this.erros).length === 0;
        },
        salvar() {
            if (!this.validarFormulario()) {
                return;
            }
            const payload = { ...this.form };
            if (payload.id) {
                axios.put(`/dmt/${payload.id}`, payload).then(() => {
                    this.carregarDados(this.paginacao.current_page);
                    bootstrap.Modal.getInstance(document.getElementById('modalForm')).hide();
                }).catch(err => {
                    alert('Erro ao salvar: ' + (err.response?.data?.message || 'Erro desconhecido.'));
                });
            } else {
                axios.post('/dmt', payload).then(() => {
                    this.carregarDados(1);
                    bootstrap.Modal.getInstance(document.getElementById('modalForm')).hide();
                }).catch(err => {
                    alert('Erro ao salvar: ' + (err.response?.data?.message || 'Erro desconhecido.'));
                });
            }
        },
        confirmarExclusao(item) {
            this.form.id = item.id;
            let modal = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
            modal.show();
        },
        excluir() {
            axios.delete(`/dmt/${this.form.id}`).then(() => {
                this.carregarDados(this.paginacao.current_page);
                bootstrap.Modal.getInstance(document.getElementById('modalConfirmacao')).hide();
            });
        },
        abrirModalGerarDmts() {
            this.gerarDmt = { entidade: '', municipio: '' };
            this.errosGerar = {};
            let modal = new bootstrap.Modal(document.getElementById('modalGerarDmts'));
            modal.show();
        },
        validarGerarDmt() {
            this.errosGerar = {};
            if (!this.gerarDmt.entidade) {
                this.errosGerar.entidade = 'Selecione uma entidade orçamentária.';
            }
            return Object.keys(this.errosGerar).length === 0;
        },
        gerarDmtsParaEntidade() {
            if (!this.validarGerarDmt()) return;
            axios.post('/dmt/gerar-para-entidade', {
                id_entidade_orcamentaria: this.gerarDmt.entidade,
                id_municipio: this.gerarDmt.municipio || null
            }).then(res => {
                alert('DMTs gerados/atualizados com sucesso! Total: ' + res.data.total);
                this.carregarDados(1);
                bootstrap.Modal.getInstance(document.getElementById('modalGerarDmts')).hide();
            }).catch(err => {
                alert('Erro ao gerar DMTs: ' + (err.response?.data?.message || 'Erro desconhecido.'));
            });
        },
        mostrarToast(mensagem) {
            this.toastMessage = mensagem;
            const toastEl = this.$refs.toast;
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        },
        async salvarAlteracoes() {
            this.salvando = true;
            try {
                await axios.put('/api/dmt/atualizar-em-lote', this.dados);
                this.mostrarToast('As alterações foram salvas com sucesso! Todos os dados foram atualizados.');
                this.carregarDados(this.paginacao.current_page);
            } catch (err) {
                alert('Erro ao salvar alterações: ' + (err.response?.data?.message || 'Erro desconhecido.'));
            } finally {
                this.salvando = false;
            }
        }
    }
}
</script>

<style scoped>
.table-group-destino td.group-destino-cell {
    font-size: 1em;
    background: #f1f8f4 !important;
    border-top: 2px solid #5EA853 !important;
    color: #388e3c;
    font-weight: 500;
    font-style: normal;
    letter-spacing: 0.5px;
    padding-top: 8px;
    padding-bottom: 8px;
}
.group-destino-icon {
    font-size: 1em;
    vertical-align: middle;
    color: #388e3c;
}
.x-col {
    background: #e3f2fd !important;
    font-family: 'Fira Mono', 'Consolas', monospace;
    font-weight: 500;
    text-align: right;
}
.col-origem {
    width: 180px;
    max-width: 180px;
    overflow: hidden;
    text-overflow: ellipsis;
    word-break: break-word;
}
</style> 