<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                    <i class="fas fa-building me-2"></i>Gerenciamento de Entidades Orçamentárias
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" @click="toggleFilters">
                        <i class="fas fa-filter"></i>
                        <span>Filtros</span>
                        <i class="fas" :class="filtrosVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <button v-if="permissoes.crud" class="btn btn-moderno btn-success-moderno d-flex align-items-center gap-2 px-3 py-2" @click="abrirModalCriar">
                        <i class="fas fa-plus"></i>
                        <span>Nova Entidade</span>
                    </button>
                    <button v-if="permissoes.crud" class="btn btn-moderno btn-primary-moderno d-flex align-items-center gap-2 px-3 py-2" @click="importarMunicipios" :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm" role="status"></span>
                        <i v-else class="fas fa-file-import"></i>
                        <span>Importar Municípios</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros Colapsáveis -->
                <div class="filtros-aba-container mb-3" v-if="filtrosVisiveis">
                    <div class="filtros-aba-content" :class="{ 'show': filtrosVisiveis }">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="filtroRazaoSocial" 
                                           v-model="filtros.jurisdicao_razao_social" 
                                           @input="filtrarDados"
                                           placeholder="Razão Social...">
                                    <label for="filtroRazaoSocial">Razão Social</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="filtroNomeFantasia" 
                                           v-model="filtros.jurisdicao_nome_fantasia" 
                                           @input="filtrarDados"
                                           placeholder="Nome Fantasia...">
                                    <label for="filtroNomeFantasia">Nome Fantasia</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-floating">
                                    <select class="form-select form-select-lg" 
                                            id="filtroTipoOrganizacao" 
                                            v-model="filtros.tipo_organizacao" 
                                            @change="filtrarDados">
                                        <option value="">Todos os tipos</option>
                                        <option value="Unidade Federativa">Unidade Federativa</option>
                                        <option value="Secretaria">Secretaria</option>
                                        <option value="Órgão">Órgão</option>
                                        <option value="Autarquia">Autarquia</option>
                                        <option value="Consórcio">Consórcio</option>
                                        <option value="S/A">S/A</option>
                                        <option value="PJ">PJ</option>
                                        <option value="PF">PF</option>
                                    </select>
                                    <label for="filtroTipoOrganizacao">Tipo</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-floating">
                                    <select class="form-select form-select-lg" 
                                            id="filtroNivelAdministrativo" 
                                            v-model="filtros.nivel_administrativo" 
                                            @change="filtrarDados">
                                        <option value="">Todos os níveis</option>
                                        <option value="municipal">Municipal</option>
                                        <option value="estadual">Estadual</option>
                                        <option value="federal">Federal</option>
                                    </select>
                                    <label for="filtroNivelAdministrativo">Nível</label>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-outline-secondary w-100" 
                                        style="height: 58px; line-height: 1.5;" 
                                        @click="limparFiltros">
                                    <i class="fas fa-times me-2"></i>Limpar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado de Carregamento -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando entidades orçamentárias...</p>
                </div>

                <!-- Estado Vazio -->
                <div v-else-if="!items.data || items.data.length === 0" class="text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Nenhuma entidade orçamentária encontrada</p>
                </div>

                <!-- Tabela -->
                <div class="table-responsive" v-else>
                    <table class="table table-hover align-middle admin-table">
                        <thead>
                            <tr>
                                <th class="fw-semibold text-custom">Razão Social</th>
                                <th class="fw-semibold text-custom">Nome Fantasia</th>
                                <th class="fw-semibold text-custom" style="width: 120px;">Tipo</th>
                                <th class="fw-semibold text-custom" style="width: 80px;">Nível</th>
                                <th class="fw-semibold text-custom" style="width: 60px;">UF</th>
                                <th class="fw-semibold text-custom">Email</th>
                                <th class="fw-semibold text-end text-custom" style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in items.data" :key="item.id" class="admin-row">
                                <td>
                                    <div class="fw-medium">{{ item.jurisdicao_razao_social }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ item.jurisdicao_nome_fantasia }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-status badge-neutro">
                                        <i class="fas fa-building me-1"></i>
                                        {{ item.tipo_organizacao }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-status" :class="{
                                        'badge-municipal': item.nivel_administrativo === 'municipal',
                                        'badge-estadual': item.nivel_administrativo === 'estadual',
                                        'badge-federal': item.nivel_administrativo === 'federal'
                                    }">
                                        <i class="fas me-1" :class="{
                                            'fa-city': item.nivel_administrativo === 'municipal',
                                            'fa-landmark': item.nivel_administrativo === 'estadual',
                                            'fa-flag': item.nivel_administrativo === 'federal'
                                        }"></i>
                                        {{ item.nivel_administrativo.toUpperCase() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ item.jurisdicao_uf }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ item.email }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end" v-if="permissoes.crud">
                                        <button class="btn btn-sm btn-warning" 
                                                @click="editarItem(item)" 
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                @click="excluirItem(item)" 
                                                title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div v-else class="text-muted small">
                                        <i class="fas fa-eye me-1"></i>Apenas visualização
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Paginação -->
        <div v-if="items.data && items.data.length > 0" class="paginacao-container mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted fw-medium">
                    Mostrando {{ items.from }} até {{ items.to }} de {{ items.total }} registros
                </div>
                <nav v-if="items.last_page > 1">
                    <ul class="pagination admin-pagination mb-0">
                        <li class="page-item" :class="{ disabled: items.current_page === 1 }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(items.current_page - 1)">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <li v-for="page in paginas" :key="page" class="page-item" :class="{ active: page === items.current_page }">
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

        <!-- Modal para Criar/Editar -->
        <div class="modal fade" id="modalEntidade" tabindex="-1" ref="modalRef">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #18578A 0%, #5EA853 100%); color: white; border-bottom: none; padding: 1.5rem; border-radius: 0.5rem 0.5rem 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="header-icon" style="width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 1.2rem; color: white;">
                                <i class="fas fa-building"></i>
                            </div>
                            <h5 class="modal-title mb-0">{{ modalTitle }}</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <form @submit.prevent="salvarItem">
                            <div class="row g-3">
                                <!-- CAMPOS OBRIGATÓRIOS -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" 
                                                :class="{ 'is-invalid': errors.tipo_organizacao }"
                                                id="tipo_organizacao" 
                                                v-model="form.tipo_organizacao">
                                            <option value="">Selecione...</option>
                                            <option value="Unidade Federativa">Unidade Federativa</option>
                                            <option value="Secretaria">Secretaria</option>
                                            <option value="Órgão">Órgão</option>
                                            <option value="Autarquia">Autarquia</option>
                                            <option value="Consórcio">Consórcio</option>
                                            <option value="S/A">S/A</option>
                                            <option value="PJ">PJ</option>
                                            <option value="PF">PF</option>
                                        </select>
                                        <label for="tipo_organizacao">Tipo de Organização *</label>
                                        <div v-if="errors.tipo_organizacao" class="invalid-feedback">
                                            {{ errors.tipo_organizacao[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" 
                                                :class="{ 'is-invalid': errors.nivel_administrativo }"
                                                id="nivel_administrativo" 
                                                v-model="form.nivel_administrativo">
                                            <option value="">Selecione...</option>
                                            <option value="municipal">Municipal</option>
                                            <option value="estadual">Estadual</option>
                                            <option value="federal">Federal</option>
                                        </select>
                                        <label for="nivel_administrativo">Nível Administrativo *</label>
                                        <div v-if="errors.nivel_administrativo" class="invalid-feedback">
                                            {{ errors.nivel_administrativo[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.jurisdicao_razao_social }"
                                               id="jurisdicao_razao_social" 
                                               v-model="form.jurisdicao_razao_social" 
                                               placeholder="Razão Social">
                                        <label for="jurisdicao_razao_social">Razão Social *</label>
                                        <div v-if="errors.jurisdicao_razao_social" class="invalid-feedback">
                                            {{ errors.jurisdicao_razao_social[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.jurisdicao_nome_fantasia }"
                                               id="jurisdicao_nome_fantasia" 
                                               v-model="form.jurisdicao_nome_fantasia" 
                                               placeholder="Nome Fantasia">
                                        <label for="jurisdicao_nome_fantasia">Nome Fantasia *</label>
                                        <div v-if="errors.jurisdicao_nome_fantasia" class="invalid-feedback">
                                            {{ errors.jurisdicao_nome_fantasia[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.jurisdicao_uf }"
                                               id="jurisdicao_uf" 
                                               v-model="form.jurisdicao_uf" 
                                               placeholder="UF"
                                               maxlength="2"
                                               style="text-transform: uppercase;">
                                        <label for="jurisdicao_uf">UF *</label>
                                        <div v-if="errors.jurisdicao_uf" class="invalid-feedback">
                                            {{ errors.jurisdicao_uf[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-floating">
                                        <input type="email" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.email }"
                                               id="email" 
                                               v-model="form.email" 
                                               placeholder="Email">
                                        <label for="email">Email *</label>
                                        <div v-if="errors.email" class="invalid-feedback">
                                            {{ errors.email[0] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- CAMPOS OPCIONAIS -->
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.jurisdicao_codigo_ibge }"
                                               id="jurisdicao_codigo_ibge" 
                                               v-model="form.jurisdicao_codigo_ibge" 
                                               placeholder="Código IBGE">
                                        <label for="jurisdicao_codigo_ibge">Código IBGE</label>
                                        <div v-if="errors.jurisdicao_codigo_ibge" class="invalid-feedback">
                                            {{ errors.jurisdicao_codigo_ibge[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.cep }"
                                               id="cep" 
                                               v-model="form.cep" 
                                               placeholder="CEP">
                                        <label for="cep">CEP</label>
                                        <div v-if="errors.cep" class="invalid-feedback">
                                            {{ errors.cep[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.telefone }"
                                               id="telefone" 
                                               v-model="form.telefone" 
                                               placeholder="Telefone">
                                        <label for="telefone">Telefone</label>
                                        <div v-if="errors.telefone" class="invalid-feedback">
                                            {{ errors.telefone[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.endereco }"
                                               id="endereco" 
                                               v-model="form.endereco" 
                                               placeholder="Endereço">
                                        <label for="endereco">Endereço</label>
                                        <div v-if="errors.endereco" class="invalid-feedback">
                                            {{ errors.endereco[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.cnpj }"
                                               id="cnpj" 
                                               v-model="form.cnpj" 
                                               placeholder="CNPJ">
                                        <label for="cnpj">CNPJ</label>
                                        <div v-if="errors.cnpj" class="invalid-feedback">
                                            {{ errors.cnpj[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.responsavel }"
                                               id="responsavel" 
                                               v-model="form.responsavel" 
                                               placeholder="Responsável">
                                        <label for="responsavel">Responsável</label>
                                        <div v-if="errors.responsavel" class="invalid-feedback">
                                            {{ errors.responsavel[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.responsavel_cargo }"
                                               id="responsavel_cargo" 
                                               v-model="form.responsavel_cargo" 
                                               placeholder="Cargo do Responsável">
                                        <label for="responsavel_cargo">Cargo do Responsável</label>
                                        <div v-if="errors.responsavel_cargo" class="invalid-feedback">
                                            {{ errors.responsavel_cargo[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" 
                                                  :class="{ 'is-invalid': errors.observacao }"
                                                  id="observacao" 
                                                  v-model="form.observacao" 
                                                  placeholder="Observações"
                                                  style="height: 100px;"></textarea>
                                        <label for="observacao">Observações</label>
                                        <div v-if="errors.observacao" class="invalid-feedback">
                                            {{ errors.observacao[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" @click="salvarItem" :disabled="salvando">
                            <span v-if="salvando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-save me-2"></i>
                            {{ salvando ? 'Salvando...' : (editando ? 'Atualizar' : 'Salvar') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast para Notificações -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div class="toast" :class="{ show: toast.show }" role="alert">
                <div class="toast-header" :class="toast.type === 'success' ? 'bg-success text-white' : 'bg-danger text-white'">
                    <i class="fas" :class="toast.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
                    <strong class="ms-2">{{ toast.title }}</strong>
                    <button type="button" class="btn-close btn-close-white" @click="toast.show = false"></button>
                </div>
                <div class="toast-body">
                    {{ toast.message }}
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação de Exclusão -->
        <div class="modal fade" id="modalConfirmacaoExclusao" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-semibold">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            Confirmar Exclusão
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Tem certeza que deseja excluir a entidade orçamentária <strong>"{{ itemParaExcluir?.jurisdicao_razao_social }}"</strong>?</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" @click="confirmarExclusao" :disabled="excluindo">
                            <span v-if="excluindo" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-trash me-2"></i>
                            {{ excluindo ? 'Excluindo...' : 'Excluir' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        permissoes: {
            type: Object,
            default: () => ({
                crud: true,
                consultar: true
            })
        }
    },
    data() {
        return {
            items: { data: [], current_page: 1, total: 0, last_page: 1, from: 0, to: 0 },
            filtros: {
                jurisdicao_razao_social: '',
                jurisdicao_nome_fantasia: '',
                tipo_organizacao: '',
                nivel_administrativo: '',
                jurisdicao_uf: ''
            },
            form: {
                tipo_organizacao: '',
                nivel_administrativo: '',
                jurisdicao_razao_social: '',
                jurisdicao_nome_fantasia: '',
                jurisdicao_uf: '',
                email: '',
                jurisdicao_codigo_ibge: '',
                cep: '',
                endereco: '',
                telefone: '',
                cnpj: '',
                observacao: '',
                responsavel: '',
                responsavel_cargo: '',
                ativo: true
            },
            errors: {},
            loading: false,
            salvando: false,
            editando: false,
            itemEditando: null,
            filtrosVisiveis: false,
            modalRef: null,
            toast: {
                show: false,
                type: 'success',
                title: '',
                message: ''
            },
            itemParaExcluir: null,
            excluindo: false
        }
    },
    computed: {
        modalTitle() {
            return this.editando ? 'Editar Entidade Orçamentária' : 'Nova Entidade Orçamentária';
        },
        paginas() {
            const pages = [];
            const maxPages = 5;
            let start = Math.max(1, this.items.current_page - Math.floor(maxPages / 2));
            let end = Math.min(this.items.last_page, start + maxPages - 1);

            if (end - start + 1 < maxPages) {
                start = Math.max(1, end - maxPages + 1);
            }

            for (let i = start; i <= end; i++) {
                pages.push(i);
            }

            return pages;
        }
    },
    mounted() {
        this.carregarDados();
        this.inicializarModal();
    },
    methods: {
        async carregarDados() {
            this.loading = true;
            try {
                const params = {
                    ...this.filtros,
                    page: this.items.current_page
                };
                
                const response = await fetch('/api/administracao/entidades-orcamentarias?' + new URLSearchParams(params));
                if (response.ok) {
                    this.items = await response.json();
                } else {
                    this.mostrarToast('Erro', 'Erro ao carregar dados', 'error');
                }
            } catch (error) {
                this.mostrarToast('Erro', 'Erro ao carregar dados', 'error');
            } finally {
                this.loading = false;
            }
        },
        toggleFilters() {
            this.filtrosVisiveis = !this.filtrosVisiveis;
        },
        limparFiltros() {
            this.filtros = {
                jurisdicao_razao_social: '',
                jurisdicao_nome_fantasia: '',
                tipo_organizacao: '',
                nivel_administrativo: '',
                jurisdicao_uf: ''
            };
            this.items.current_page = 1;
            this.carregarDados();
        },
        filtrarDados() {
            this.items.current_page = 1;
            this.carregarDados();
        },
        mudarPagina(pagina) {
            if (pagina >= 1 && pagina <= this.items.last_page) {
                this.items.current_page = pagina;
                this.carregarDados();
            }
        },
        inicializarModal() {
            this.modalRef = new bootstrap.Modal(document.getElementById('modalEntidade'));
        },
        abrirModalCriar() {
            this.editando = false;
            this.itemEditando = null;
            this.form = {
                tipo_organizacao: '',
                nivel_administrativo: '',
                jurisdicao_razao_social: '',
                jurisdicao_nome_fantasia: '',
                jurisdicao_uf: '',
                email: '',
                jurisdicao_codigo_ibge: '',
                cep: '',
                endereco: '',
                telefone: '',
                cnpj: '',
                observacao: '',
                responsavel: '',
                responsavel_cargo: '',
                ativo: true
            };
            this.errors = {};
            this.modalRef.show();
        },
        editarItem(item) {
            this.editando = true;
            this.itemEditando = item;
            this.form = { ...item };
            this.errors = {};
            this.modalRef.show();
        },
        async salvarItem() {
            this.salvando = true;
            this.errors = {};

            try {
                const url = this.editando 
                    ? `/api/administracao/entidades-orcamentarias/${this.itemEditando.id}`
                    : '/api/administracao/entidades-orcamentarias';
                
                const method = this.editando ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (response.ok) {
                    this.modalRef.hide();
                    this.mostrarToast(
                        'Sucesso', 
                        this.editando ? 'Entidade atualizada com sucesso!' : 'Entidade criada com sucesso!', 
                        'success'
                    );
                    this.carregarDados();
                } else if (response.status === 422) {
                    this.errors = data.errors;
                } else {
                    this.mostrarToast('Erro', data.error || 'Erro ao salvar', 'error');
                }
            } catch (error) {
                this.mostrarToast('Erro', 'Erro ao salvar', 'error');
            } finally {
                this.salvando = false;
            }
        },
        async excluirItem(item) {
            this.itemParaExcluir = item;
            const modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacaoExclusao'));
            modalConfirmacao.show();
        },
        async confirmarExclusao() {
            if (!this.itemParaExcluir) return;
            
            this.excluindo = true;
            try {
                const response = await fetch(`/api/administracao/entidades-orcamentarias/${this.itemParaExcluir.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const modalConfirmacao = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacaoExclusao'));
                    modalConfirmacao.hide();
                    
                    this.mostrarToast('Sucesso', 'Entidade excluída com sucesso!', 'success');
                    this.carregarDados();
                } else {
                    const data = await response.json();
                    this.mostrarToast('Erro', data.error || 'Erro ao excluir', 'error');
                }
            } catch (error) {
                this.mostrarToast('Erro', 'Erro ao excluir', 'error');
            } finally {
                this.excluindo = false;
                this.itemParaExcluir = null;
            }
        },
        mostrarToast(title, message, type = 'success') {
            this.toast = {
                show: true,
                type: type,
                title: title,
                message: message
            };
            setTimeout(() => {
                this.toast.show = false;
            }, 5000);
        },
        async importarMunicipios() {
            this.loading = true;
            try {
                const response = await fetch('/api/administracao/entidades-orcamentarias/importar-municipios', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    this.mostrarToast('Sucesso', `Importação concluída! ${data.novos} novos municípios e ${data.atualizados} atualizados.`, 'success');
                    this.carregarDados();
                } else {
                    this.mostrarToast('Erro', data.message || 'Erro na importação', 'error');
                }
            } catch (error) {
                this.mostrarToast('Erro', 'Erro na importação', 'error');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style scoped>


.filtros-aba-container {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #e9ecef;
}

.admin-table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.admin-table th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    color: #495057;
    padding: 15px;
}

.admin-row:hover {
    background-color: #f8f9fa;
}

.text-custom {
    color: #18578A !important;
}

.admin-pagination .page-link {
    border: 1px solid #dee2e6;
    color: #6c757d;
    padding: 8px 12px;
}

.admin-pagination .page-item.active .page-link {
    background-color: #5EA853;
    border-color: #5EA853;
    color: white;
}

.admin-pagination .page-link:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
    color: #495057;
}

/* Ajustes de fonte nos filtros */
.form-control-lg {
    font-size: 0.9rem !important;
}

.form-select-lg {
    font-size: 0.9rem !important;
}

/* Badges discretos */
.badge-status {
    background-color: transparent !important;
    border: 1px solid #dee2e6;
    color: #6c757d;
    font-weight: 500;
    font-size: 0.75rem;
    padding: 4px 8px;
}

.badge-neutro {
    background-color: #f8f9fa !important;
    border-color: #dee2e6;
    color: #495057;
}

.badge-municipal {
    background-color: #d4edda !important;
    border-color: #c3e6cb;
    color: #155724;
}

.badge-estadual {
    background-color: #d1ecf1 !important;
    border-color: #bee5eb;
    color: #0c5460;
}

.badge-federal {
    background-color: #fff3cd !important;
    border-color: #ffeaa7;
    color: #856404;
}
</style>