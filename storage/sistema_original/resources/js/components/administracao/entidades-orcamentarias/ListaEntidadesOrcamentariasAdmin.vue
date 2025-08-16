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
                    <button class="btn btn-outline-success d-flex align-items-center gap-2 px-3 py-2" @click="abrirModalCriar">
                        <i class="fas fa-plus"></i>
                        <span>Nova Entidade</span>
                    </button>
                    <button class="btn btn-outline-primary d-flex align-items-center gap-2 px-3 py-2" @click="importarMunicipios" :disabled="loading">
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
                                           v-model="filtros.razao_social" 
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
                                           v-model="filtros.nome_fantasia" 
                                           @input="filtrarDados"
                                           placeholder="Nome Fantasia...">
                                    <label for="filtroNomeFantasia">Nome Fantasia</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select form-select-lg" 
                                            id="filtroTipoOrganizacao" 
                                            v-model="filtros.tipo_organizacao" 
                                            @change="filtrarDados">
                                        <option value="">Todos os tipos</option>
                                        <option value="municipio">Município</option>
                                        <option value="secretaria">Secretaria</option>
                                        <option value="órgão">Órgão</option>
                                        <option value="autarquia">Autarquia</option>
                                        <option value="outros">Outros</option>
                                    </select>
                                    <label for="filtroTipoOrganizacao">Tipo de Organização</label>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-outline-secondary w-100" 
                                        style="height: 58px; line-height: 1.5;" 
                                        @click="limparFiltros">
                                    <i class="fas fa-times me-2"></i>Limpar Filtros
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
                    <table class="table table-hover align-middle usuarios-table">
                        <thead>
                            <tr>
                                <th class="fw-semibold text-custom">Razão Social</th>
                                <th class="fw-semibold text-custom">Nome Fantasia</th>
                                <th class="fw-semibold text-custom" style="width: 120px;">Tipo</th>
                                <th class="fw-semibold text-custom" style="width: 170px;">CNPJ</th>
                                <th class="fw-semibold text-custom" style="width: 120px;">Telefone</th>
                                <th class="fw-semibold text-end text-custom" style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in items.data" :key="item.id" class="usuario-row">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="fw-medium">{{ item.razao_social }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ item.nome_fantasia }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-tipo badge-local">{{ item.tipo_organizacao }}</span>
                                </td>
                                <td>
                                    <span class="">{{ item.cnpj || 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="">{{ item.telefone }}</span>
                                </td>
                                                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
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
                                    </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Paginação (Fora do Card - Padrão) -->
        <div v-if="items.data && items.data.length > 0" class="paginacao-container mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted fw-medium">
                    Mostrando {{ items.from }} até {{ items.to }} de {{ items.total }} registros
                </div>
                <nav v-if="items.last_page > 1">
                    <ul class="pagination admin-pagination mb-0">
                        <!-- Botão Anterior -->
                        <li class="page-item" :class="{ disabled: items.current_page === 1 }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(items.current_page - 1)" aria-label="Anterior">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>

                        <!-- Primeira Página -->
                        <li v-if="paginas[0] > 1" class="page-item">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(1)">1</a>
                        </li>
                        <li v-if="paginas[0] > 2" class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>

                        <!-- Páginas Numeradas -->
                        <li v-for="page in paginas" :key="page" class="page-item" :class="{ active: page === items.current_page }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(page)">{{ page }}</a>
                        </li>

                        <!-- Última Página -->
                        <li v-if="paginas[paginas.length - 1] < items.last_page - 1" class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <li v-if="paginas[paginas.length - 1] < items.last_page" class="page-item">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(items.last_page)">{{ items.last_page }}</a>
                        </li>

                        <!-- Botão Próximo -->
                        <li class="page-item" :class="{ disabled: items.current_page === items.last_page }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(items.current_page + 1)" aria-label="Próximo">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Modal para Criar/Editar -->
        <div class="modal fade" id="modalEntidade" tabindex="-1" ref="modalRef">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <h5 class="modal-title mb-0">{{ modalTitle }}</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <form @submit.prevent="salvarItem">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.razao_social }"
                                               id="razao_social" 
                                               v-model="form.razao_social" 
                                               placeholder="Razão Social">
                                        <label for="razao_social">Razão Social *</label>
                                        <div v-if="errors.razao_social" class="invalid-feedback">
                                            {{ errors.razao_social[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.nome_fantasia }"
                                               id="nome_fantasia" 
                                               v-model="form.nome_fantasia" 
                                               placeholder="Nome Fantasia">
                                        <label for="nome_fantasia">Nome Fantasia *</label>
                                        <div v-if="errors.nome_fantasia" class="invalid-feedback">
                                            {{ errors.nome_fantasia[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" 
                                                :class="{ 'is-invalid': errors.tipo_organizacao }"
                                                id="tipo_organizacao" 
                                                v-model="form.tipo_organizacao">
                                            <option value="">Selecione...</option>
                                            <option value="municipio">Município</option>
                                            <option value="secretaria">Secretaria</option>
                                            <option value="órgão">Órgão</option>
                                            <option value="autarquia">Autarquia</option>
                                            <option value="outros">Outros</option>
                                        </select>
                                        <label for="tipo_organizacao">Tipo de Organização *</label>
                                        <div v-if="errors.tipo_organizacao" class="invalid-feedback">
                                            {{ errors.tipo_organizacao[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
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
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.codigo_ibge }"
                                               id="codigo_ibge" 
                                               v-model="form.codigo_ibge" 
                                               placeholder="Código IBGE">
                                        <label for="codigo_ibge">Código IBGE</label>
                                        <div v-if="errors.codigo_ibge" class="invalid-feedback">
                                            {{ errors.codigo_ibge[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.populacao }"
                                               id="populacao" 
                                               v-model="form.populacao" 
                                               placeholder="População">
                                        <label for="populacao">População</label>
                                        <div v-if="errors.populacao" class="invalid-feedback">
                                            {{ errors.populacao[0] }}
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
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.telefone }"
                                               id="telefone" 
                                               v-model="form.telefone" 
                                               placeholder="Telefone">
                                        <label for="telefone">Telefone *</label>
                                        <div v-if="errors.telefone" class="invalid-feedback">
                                            {{ errors.telefone[0] }}
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
                                        <label for="responsavel">Responsável *</label>
                                        <div v-if="errors.responsavel" class="invalid-feedback">
                                            {{ errors.responsavel[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.responsavel_cargo }"
                                               id="responsavel_cargo" 
                                               v-model="form.responsavel_cargo" 
                                               placeholder="Cargo do Responsável">
                                        <label for="responsavel_cargo">Cargo do Responsável *</label>
                                        <div v-if="errors.responsavel_cargo" class="invalid-feedback">
                                            {{ errors.responsavel_cargo[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.responsavel_telefone }"
                                               id="responsavel_telefone" 
                                               v-model="form.responsavel_telefone" 
                                               placeholder="Telefone do Responsável">
                                        <label for="responsavel_telefone">Telefone do Responsável</label>
                                        <div v-if="errors.responsavel_telefone" class="invalid-feedback">
                                            {{ errors.responsavel_telefone[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.responsavel_email }"
                                               id="responsavel_email" 
                                               v-model="form.responsavel_email" 
                                               placeholder="Email do Responsável">
                                        <label for="responsavel_email">Email do Responsável</label>
                                        <div v-if="errors.responsavel_email" class="invalid-feedback">
                                            {{ errors.responsavel_email[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" @click="salvarItem" :disabled="salvando">
                            <span v-if="salvando" class="spinner-border spinner-border-sm" role="status"></span>
                            <span v-else>{{ editando ? 'Atualizar' : 'Criar' }}</span>
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
        <div class="modal fade" id="modalConfirmacaoExclusao" tabindex="-1" ref="modalConfirmacaoRef">
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
                        <p class="mb-0">Tem certeza que deseja excluir a entidade orçamentária <strong>"{{ itemParaExcluir?.razao_social }}"</strong>?</p>
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
    data() {
        return {
            items: { data: [], current_page: 1, total: 0, last_page: 1, from: 0, to: 0 },
            filtros: {
                razao_social: '',
                nome_fantasia: '',
                tipo_organizacao: ''
            },
            form: {
                razao_social: '',
                nome_fantasia: '',
                tipo_organizacao: '',
                email: '',
                endereco: '',
                codigo_ibge: '',
                populacao: '',
                cep: '',
                telefone: '',
                cnpj: '',
                responsavel: '',
                responsavel_cargo: '',
                responsavel_telefone: '',
                responsavel_email: ''
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
                // Incluir a página atual nos parâmetros da requisição
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
                console.error('Erro:', error);
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
                razao_social: '',
                nome_fantasia: '',
                tipo_organizacao: ''
            };
            // Resetar para a primeira página ao limpar filtros
            this.items.current_page = 1;
            this.carregarDados();
        },
        filtrarDados() {
            // Resetar para a primeira página ao aplicar filtros
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
                razao_social: '',
                nome_fantasia: '',
                tipo_organizacao: '',
                email: '',
                endereco: '',
                codigo_ibge: '',
                populacao: '',
                cep: '',
                telefone: '',
                cnpj: '',
                responsavel: '',
                responsavel_cargo: '',
                responsavel_telefone: '',
                responsavel_email: ''
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
                console.error('Erro:', error);
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
                    // Fechar o modal de confirmação
                    const modalConfirmacao = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacaoExclusao'));
                    modalConfirmacao.hide();
                    
                    this.mostrarToast('Sucesso', 'Entidade excluída com sucesso!', 'success');
                    this.carregarDados();
                } else {
                    const data = await response.json();
                    this.mostrarToast('Erro', data.error || 'Erro ao excluir', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                this.mostrarToast('Erro', 'Erro ao excluir', 'error');
            } finally {
                this.excluindo = false;
                this.itemParaExcluir = null;
            }
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
                    this.mostrarToast('Sucesso', data.message, 'success');
                    this.carregarDados();
                } else {
                    this.mostrarToast('Erro', data.error || 'Erro na importação', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                this.mostrarToast('Erro', 'Erro na importação', 'error');
            } finally {
                this.loading = false;
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
        }
    }
}
</script>

<style>
/* Estilos específicos para altura dos campos de filtro */
.form-floating .form-control-lg {
    height: 58px !important;
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
    line-height: 1.5 !important;
}

/* Corrigir conteúdo cortado no select */
.form-floating .form-select-lg {
    height: 58px !important;
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
    line-height: 1.5 !important;
}

/* Garantir que o label não corte o conteúdo */
.form-floating .form-select-lg + label {
    transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem) !important;
}

/* Ajustar espaçamento interno do select para evitar corte */
.form-select-lg {
    padding-left: 1rem !important;
    padding-right: 2.5rem !important;
}

/* Ajustes específicos para o select do filtro */
#filtroTipoOrganizacao {
    height: 58px !important;
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
    line-height: 1.5 !important;
    font-size: 0.875rem !important;
}

/* Garantir que o texto do select seja visível */
#filtroTipoOrganizacao option {
    padding: 8px 12px !important;
    font-size: 0.875rem !important;
}

/* Ajustar o label específico do select */
#filtroTipoOrganizacao + label {
    transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem) !important;
    pointer-events: none !important;
}
</style>
