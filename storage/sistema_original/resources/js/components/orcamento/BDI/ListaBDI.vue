<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold" style="color: #5EA853;">
                    <i class="fas fa-list me-2"></i>BDIs
                </h5>
                <button class="btn btn-primary" @click="abrirModalCriar">
                    <i class="fas fa-plus me-2"></i>Novo BDI
                </button>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <form class="row g-3 align-items-end mb-3" @submit.prevent="carregarDados(1)">
                    <div class="col-md-4 position-relative">
                        <label class="form-label mb-1">Entidade Orçamentária</label>
                        <input type="text" class="form-control" v-model="filtros.entidade_orcamentaria_nome" placeholder="Digite para buscar..." @input="onEntidadeInput" @focus="mostrarSugestoes = true" @blur="ocultarSugestoesComDelay">
                        <ul v-if="mostrarSugestoes && sugestoesEntidade.length > 0" class="list-group position-absolute w-100 zindex-tooltip" style="max-height: 200px; overflow-y: auto; top: 100%;">
                            <li v-for="entidade in sugestoesEntidade" :key="entidade.id" class="list-group-item list-group-item-action" @mousedown.prevent="selecionarEntidade(entidade)">
                                {{ entidade.nome }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1">Nome do BDI</label>
                        <input type="text" class="form-control" v-model="filtros.nome" placeholder="Digite o nome do BDI">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Analisado</label>
                        <select class="form-select" v-model="filtros.analisado">
                            <option value="">Todos</option>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-outline-success"><i class="fas fa-search"></i></button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="tabela-azul">
                            <tr>
                                <th class="text-custom">Entidade Orçamentária</th>
                                <th class="text-custom">Nome do BDI</th>
                                <th class="text-custom">BDI Serviço (%)</th>
                                <th class="text-custom">BDI Material (%)</th>
                                <th class="text-custom">BDI Equipamento (%)</th>
                                <th class="text-custom">Analisado</th>
                                <th class="text-end text-custom">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in items.data" :key="item.id">
                                <td>{{ item.entidade_orcamentaria?.nome ?? '-' }}</td>
                                <td>{{ item.nome }}</td>
                                <td class="destaque_bdi">{{ formatarPercentual(item.bdi_servico) }}</td>
                                <td class="destaque_bdi">{{ formatarPercentual(item.bdi_material) }}</td>
                                <td class="destaque_bdi">{{ formatarPercentual(item.bdi_equipamento) }}</td>
                                <td>
                                    <span class="badge" :class="item.analisado ? 'bg-success' : 'bg-secondary'">
                                        {{ item.analisado ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
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
        <!-- Modal Criar/Editar BDI -->
        <div class="modal fade" id="bdiModal" tabindex="-1" ref="bdiModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-semibold">
                            <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
                            {{ modoEdicao ? 'Editar' : 'Novo' }} BDI
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <form @submit.prevent="salvarBDI" class="needs-validation" novalidate>
                            <!-- Grupo: Identificação -->
                            <div class="card mb-4 shadow-sm">
                                <div class="card-header bg-light d-flex align-items-center">
                                    <i class="fas fa-id-card me-2 text-primary"></i>
                                    <span class="fw-semibold">Identificação</span>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" id="entidade_orcamentaria_id" v-model="form.entidade_orcamentaria_id" :class="{'is-invalid': errors.entidade_orcamentaria_id}" required>
                                                    <option value="">Escolha</option>
                                                    <option v-for="entidade in entidadesOrcamentarias" :key="entidade.id" :value="entidade.id">
                                                        {{ entidade.nome }}
                                                    </option>
                                                </select>
                                                <label for="entidade_orcamentaria_id">Entidade Orçamentária</label>
                                                <div class="invalid-feedback" v-if="errors.entidade_orcamentaria_id">{{ errors.entidade_orcamentaria_id[0] }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="orcamento_id" v-model="form.orcamento_id">
                                                <label for="orcamento_id">ID do Orçamento</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="nome" v-model="form.nome" :class="{'is-invalid': errors.nome}" required>
                                                <label for="nome">Nome do BDI</label>
                                                <div class="invalid-feedback" v-if="errors.nome">{{ errors.nome[0] }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" id="origem" v-model="form.origem" :class="{'is-invalid': errors.origem}" required>
                                                    <option value="">Escolha</option>
                                                    <option value="entidade_orcamentaria">Entidade Orçamentária</option>
                                                    <option value="orcamento">Orçamento</option>
                                                </select>
                                                <label for="origem">Origem</label>
                                                <div class="invalid-feedback" v-if="errors.origem">{{ errors.origem[0] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Grupo: Impostos -->
                            <div class="card mb-4 shadow-sm">
                                <div class="card-header bg-light d-flex align-items-center">
                                    <i class="fas fa-percent me-2 text-success"></i>
                                    <span class="fw-semibold">Impostos</span>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" step="0.01" class="form-control" id="iss_municipio" v-model.number="form.iss_municipio">
                                                <label for="iss_municipio">ISS Município (%)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" step="0.01" class="form-control" id="base_calculo_mao_obra" v-model.number="form.base_calculo_mao_obra">
                                                <label for="base_calculo_mao_obra">(BC) Mão de Obra (%)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" step="0.01" class="form-control" id="iss_calculado" v-model.number="form.iss_calculado" readonly>
                                                <label for="iss_calculado">ISS Calculado (%)</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" step="0.01" class="form-control" id="pis" v-model.number="form.pis">
                                                <label for="pis">PIS (%)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" step="0.01" class="form-control" id="cofins" v-model.number="form.cofins">
                                                <label for="cofins">COFINS (%)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" step="0.01" class="form-control" id="cprb" v-model.number="form.cprb">
                                                <label for="cprb">CPRB (%)</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" step="0.01" class="form-control text-success fw-bold" id="impostos_total" v-model.number="form.impostos_total" readonly>
                                                <label for="impostos_total" class="text-success">IMPOSTOS (%)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Grupo: Composição do BDI -->
                            <div class="card mb-4 shadow-sm">
                                <div class="card-header bg-light d-flex align-items-center">
                                    <i class="fas fa-cubes me-2 text-info"></i>
                                    <span class="fw-semibold">Composição do BDI</span>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle text-center mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th></th>
                                                    <th>SERVIÇOS (%)</th>
                                                    <th>MATERIAIS (%)</th>
                                                    <th>EQUIP. (%)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>ADM. CENTRAL</td>
                                                    <td><input type="number" step="0.01" class="form-control" id="adm_central_servico" v-model.number="form.adm_central_servico"></td>
                                                    <td><input type="number" step="0.01" class="form-control" id="adm_central_material" v-model.number="form.adm_central_material"></td>
                                                    <td><input type="number" step="0.01" class="form-control" id="adm_central_equipamento" v-model.number="form.adm_central_equipamento"></td>
                                                </tr>
                                                <tr>
                                                    <td>RISCOS</td>
                                                    <td><input type="number" step="0.01" class="form-control" id="riscos_servico" v-model.number="form.riscos_servico"></td>
                                                    <td><input type="number" step="0.01" class="form-control" id="riscos_material" v-model.number="form.riscos_material"></td>
                                                    <td><input type="number" step="0.01" class="form-control" id="riscos_equipamento" v-model.number="form.riscos_equipamento"></td>
                                                </tr>
                                                <tr>
                                                    <td>SEGUROS E GARANTIAS</td>
                                                    <td><input type="number" step="0.01" class="form-control" id="seguros_garantia_servico" v-model.number="form.seguros_garantia_servico"></td>
                                                    <td><input type="number" step="0.01" class="form-control" id="seguros_garantia_material" v-model.number="form.seguros_garantia_material"></td>
                                                    <td><input type="number" step="0.01" class="form-control" id="seguros_garantia_equipamento" v-model.number="form.seguros_garantia_equipamento"></td>
                                                </tr>
                                                <tr>
                                                    <td>DESPESAS FINANCEIRAS</td>
                                                    <td><input type="number" step="0.01" class="form-control" id="desp_financeira_servico" v-model.number="form.desp_financeira_servico"></td>
                                                    <td><input type="number" step="0.01" class="form-control" id="desp_financeira_material" v-model.number="form.desp_financeira_material"></td>
                                                    <td><input type="number" step="0.01" class="form-control" id="desp_financeira_equipamento" v-model.number="form.desp_financeira_equipamento"></td>
                                                </tr>
                                                <tr>
                                                    <td>LUCRO</td>
                                                    <td><input type="number" step="0.01" class="form-control" id="lucro_servico" v-model.number="form.lucro_servico"></td>
                                                    <td><input type="number" step="0.01" class="form-control" id="lucro_material" v-model.number="form.lucro_material"></td>
                                                    <td><input type="number" step="0.01" class="form-control" id="lucro_equipamento" v-model.number="form.lucro_equipamento"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Grupo: Resultado Final -->
                            <div class="card mb-4 shadow-sm border-success">
                                <div class="card-header bg-success bg-opacity-10 d-flex align-items-center">
                                    <i class="fas fa-check-circle me-2 text-success"></i>
                                    <span class="fw-semibold">Resultado Final (BDI)</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-4">
                                            <label for="bdi_servico" class="bdi-destaque-label">BDI Serviço (%)</label>
                                            <input type="number" step="0.01" class="form-control bdi-destaque-input" id="bdi_servico" v-model.number="form.bdi_servico" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="bdi_material" class="bdi-destaque-label">BDI Material (%)</label>
                                            <input type="number" step="0.01" class="form-control bdi-destaque-input" id="bdi_material" v-model.number="form.bdi_material" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="bdi_equipamento" class="bdi-destaque-label">BDI Equipamento (%)</label>
                                            <input type="number" step="0.01" class="form-control bdi-destaque-input" id="bdi_equipamento" v-model.number="form.bdi_equipamento" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="analisado" v-model="form.analisado">
                                <label class="form-check-label" for="analisado">Analisado</label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" @click="salvarBDI" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-save me-2"></i>
                            {{ loading ? 'Salvando...' : 'Salvar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
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
                nome: '',
                analisado: ''
            },
            modoEdicao: false,
            form: {
                entidade_orcamentaria_id: '',
                orcamento_id: '',
                nome: '',
                origem: '',
                iss_municipio: 0,
                base_calculo_mao_obra: 0,
                iss_calculado: 0,
                pis: 0,
                cofins: 0,
                cprb: 0,
                adm_central_servico: 0,
                adm_central_material: 0,
                adm_central_equipamento: 0,
                riscos_servico: 0,
                riscos_material: 0,
                riscos_equipamento: 0,
                seguros_garantia_servico: 0,
                seguros_garantia_material: 0,
                seguros_garantia_equipamento: 0,
                desp_financeira_servico: 0,
                desp_financeira_material: 0,
                desp_financeira_equipamento: 0,
                lucro_servico: 0,
                lucro_material: 0,
                lucro_equipamento: 0,
                bdi_servico: 0,
                bdi_material: 0,
                bdi_equipamento: 0,
                analisado: false
            },
            entidadesOrcamentarias: [],
            loading: false,
            errors: {},
            sugestoesEntidade: [],
            mostrarSugestoes: false
        }
    },
    watch: {
        'form.iss_municipio': {
            handler() {
                this.calcularISSCalculado();
            }
        },
        'form.base_calculo_mao_obra': {
            handler() {
                this.calcularISSCalculado();
            }
        },
        'form.iss_calculado': {
            handler() {
                this.calcularImpostosTotal();
            }
        },
        'form.pis': {
            handler() {
                this.calcularImpostosTotal();
            }
        },
        'form.cofins': {
            handler() {
                this.calcularImpostosTotal();
            }
        },
        'form.cprb': {
            handler() {
                this.calcularImpostosTotal();
            }
        },
        'form.adm_central_equipamento': {
            handler() {
                this.calcularBDIEquipamento();
            }
        },
        'form.riscos_equipamento': {
            handler() {
                this.calcularBDIEquipamento();
            }
        },
        'form.seguros_garantia_equipamento': {
            handler() {
                this.calcularBDIEquipamento();
            }
        },
        'form.desp_financeira_equipamento': {
            handler() {
                this.calcularBDIEquipamento();
            }
        },
        'form.lucro_equipamento': {
            handler() {
                this.calcularBDIEquipamento();
            }
        },
        'form.impostos_total': {
            handler() {
                this.calcularBDIEquipamento();
            }
        },
        'form.adm_central_servico': {
            handler() {
                this.calcularBDIServico();
            }
        },
        'form.riscos_servico': {
            handler() {
                this.calcularBDIServico();
            }
        },
        'form.seguros_garantia_servico': {
            handler() {
                this.calcularBDIServico();
            }
        },
        'form.desp_financeira_servico': {
            handler() {
                this.calcularBDIServico();
            }
        },
        'form.lucro_servico': {
            handler() {
                this.calcularBDIServico();
            }
        },
        'form.adm_central_material': {
            handler() {
                this.calcularBDIMaterial();
            }
        },
        'form.riscos_material': {
            handler() {
                this.calcularBDIMaterial();
            }
        },
        'form.seguros_garantia_material': {
            handler() {
                this.calcularBDIMaterial();
            }
        },
        'form.desp_financeira_material': {
            handler() {
                this.calcularBDIMaterial();
            }
        },
        'form.lucro_material': {
            handler() {
                this.calcularBDIMaterial();
            }
        }
    },
    mounted() {
        this.carregarEntidadesOrcamentarias();
        this.carregarDados();
    },
    methods: {
        calcularISSCalculado() {
            const issMunicipio = parseFloat(this.form.iss_municipio) || 0;
            const baseCalculo = parseFloat(this.form.base_calculo_mao_obra) || 0;
            this.form.iss_calculado = Number((issMunicipio * baseCalculo / 100).toFixed(2));
        },
        calcularImpostosTotal() {
            const issCalculado = parseFloat(this.form.iss_calculado) || 0;
            const pis = parseFloat(this.form.pis) || 0;
            const cofins = parseFloat(this.form.cofins) || 0;
            const cprb = parseFloat(this.form.cprb) || 0;
            this.form.impostos_total = Number((issCalculado + pis + cofins + cprb).toFixed(2));
        },
        async carregarEntidadesOrcamentarias() {
            try {
                const response = await axios.get('/entidades-orcamentarias/listar-select');
                this.entidadesOrcamentarias = response.data.data || [];
            } catch (error) {
                alert('Erro ao carregar entidades orçamentárias');
            }
        },
        async carregarDados(page = 1) {
            try {
                const response = await axios.get('/bdis/listar', {
                    params: {
                        page: page,
                        per_page: 100,
                        entidade_orcamentaria_id: this.filtros.entidade_orcamentaria_id,
                        nome: this.filtros.nome,
                        analisado: this.filtros.analisado
                    }
                });
                if (response.data && response.data.data) {
                    this.items = {
                        data: response.data.data || [],
                        current_page: response.data.current_page || 1,
                        from: response.data.from || 0,
                        to: response.data.to || 0,
                        total: response.data.total || 0,
                        last_page: response.data.last_page || 1
                    };
                }
            } catch (error) {
                alert('Erro ao carregar dados');
            }
        },
        async mudarPagina(page) {
            this.items.current_page = page;
            await this.carregarDados(page);
        },
        abrirModalCriar() {
            this.modoEdicao = false;
            this.resetForm();
            this.errors = {};
            this.showModal();
        },
        editarItem(item) {
            this.modoEdicao = true;
            this.form = { ...item };
            this.errors = {};
            this.showModal();
        },
        showModal() {
            if (!this.bdiModal) {
                this.bdiModal = new bootstrap.Modal(document.getElementById('bdiModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
            }
            this.bdiModal.show();
        },
        hideModal() {
            if (this.bdiModal) {
                this.bdiModal.hide();
            }
        },
        resetForm() {
            this.form = {
                entidade_orcamentaria_id: '',
                orcamento_id: '',
                nome: '',
                origem: '',
                iss_municipio: 0,
                base_calculo_mao_obra: 0,
                iss_calculado: 0,
                pis: 0,
                cofins: 0,
                cprb: 0,
                adm_central_servico: 0,
                adm_central_material: 0,
                adm_central_equipamento: 0,
                riscos_servico: 0,
                riscos_material: 0,
                riscos_equipamento: 0,
                seguros_garantia_servico: 0,
                seguros_garantia_material: 0,
                seguros_garantia_equipamento: 0,
                desp_financeira_servico: 0,
                desp_financeira_material: 0,
                desp_financeira_equipamento: 0,
                lucro_servico: 0,
                lucro_material: 0,
                lucro_equipamento: 0,
                bdi_servico: 0,
                bdi_material: 0,
                bdi_equipamento: 0,
                analisado: false
            };
        },
        async salvarBDI() {
            this.loading = true;
            this.errors = {};
            try {
                let response;
                if (this.modoEdicao && this.form.id) {
                    response = await axios.put(`/bdis/${this.form.id}`, this.form);
                } else {
                    response = await axios.post('/bdis', this.form);
                }
                this.hideModal();
                this.carregarDados();
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                } else {
                    alert('Erro ao salvar BDI');
                }
            } finally {
                this.loading = false;
            }
        },
        async excluirItem(item) {
            if (!confirm('Tem certeza que deseja excluir este BDI?')) return;
            try {
                await axios.delete(`/bdis/${item.id}`);
                this.carregarDados();
            } catch (error) {
                alert('Erro ao excluir BDI');
            }
        },
        formatarPercentual(valor) {
            const num = Number(valor);
            return isNaN(num) ? '-' : num.toFixed(2) + ' %';
        },
        calcularBDIEquipamento() {
            const adm = parseFloat(this.form.adm_central_equipamento) || 0;
            const riscos = parseFloat(this.form.riscos_equipamento) || 0;
            const seguros = parseFloat(this.form.seguros_garantia_equipamento) || 0;
            const desp = parseFloat(this.form.desp_financeira_equipamento) || 0;
            const lucro = parseFloat(this.form.lucro_equipamento) || 0;
            const pis = parseFloat(this.form.pis) || 0;
            const cofins = parseFloat(this.form.cofins) || 0;
            const cprb = parseFloat(this.form.cprb) || 0;

            let denominador = 1 - ((pis + cofins + cprb) / 100);
            if (denominador === 0) {
                this.form.bdi_equipamento = 0;
                return;
            }
            let resultado = (((1 + (adm + riscos + seguros) / 100)
                * (1 + desp / 100)
                * (1 + lucro / 100))
                / denominador - 1) * 100;
            this.form.bdi_equipamento = Number(resultado.toFixed(2));
        },
        calcularBDIServico() {
            const adm = parseFloat(this.form.adm_central_servico) || 0;
            const riscos = parseFloat(this.form.riscos_servico) || 0;
            const seguros = parseFloat(this.form.seguros_garantia_servico) || 0;
            const desp = parseFloat(this.form.desp_financeira_servico) || 0;
            const lucro = parseFloat(this.form.lucro_servico) || 0;
            const impostos = parseFloat(this.form.impostos_total) || 0;

            let denominador = 1 - (impostos / 100);
            if (denominador === 0) {
                this.form.bdi_servico = 0;
                return;
            }
            let resultado = (((1 + (adm + riscos + seguros) / 100)
                * (1 + desp / 100)
                * (1 + lucro / 100))
                / denominador - 1) * 100;
            this.form.bdi_servico = Number(resultado.toFixed(2));
        },
        calcularBDIMaterial() {
            const adm = parseFloat(this.form.adm_central_material) || 0;
            const riscos = parseFloat(this.form.riscos_material) || 0;
            const seguros = parseFloat(this.form.seguros_garantia_material) || 0;
            const desp = parseFloat(this.form.desp_financeira_material) || 0;
            const lucro = parseFloat(this.form.lucro_material) || 0;
            const pis = parseFloat(this.form.pis) || 0;
            const cofins = parseFloat(this.form.cofins) || 0;
            const cprb = parseFloat(this.form.cprb) || 0;

            let denominador = 1 - ((pis + cofins + cprb) / 100);
            if (denominador === 0) {
                this.form.bdi_material = 0;
                return;
            }
            let resultado = (((1 + (adm + riscos + seguros) / 100)
                * (1 + desp / 100)
                * (1 + lucro / 100))
                / denominador - 1) * 100;
            this.form.bdi_material = Number(resultado.toFixed(2));
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
        }
    }
}
</script>

<style scoped>


    .bdi-destaque-input {
        font-size: 1.3rem !important;
        color: #5EA853 !important;
        font-weight: bold !important;
        border-width: 2px;
        border-color: #5EA85333;
        background-color: #f8fff4 !important;
    }
    .bdi-destaque-label {
        color: #5EA853 !important;
        font-weight: bold;
        margin-top: 0.5rem;
        letter-spacing: 0.5px;
        font-size: 1.1rem;
    }

    .form-control[readonly] {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }

    .tabela-azul th {
        background: #fff !important;
        font-weight: bold;
        border-bottom: 2px solid #e9ecef;
        vertical-align: middle;
    }
</style> 