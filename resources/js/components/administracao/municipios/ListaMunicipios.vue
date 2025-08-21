<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                    <i class="fas fa-city me-2"></i>Gerenciamento de Municípios 
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" @click="toggleFilters">
                        <i class="fas fa-filter"></i>
                        <span>Filtros</span>
                        <i class="fas" :class="filtrosVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <button v-if="permissoes.crud" class="btn btn-moderno btn-success-moderno d-flex align-items-center gap-2 px-3 py-2" @click="abrirModalCriar">
                        <i class="fas fa-plus"></i>
                        <span>Novo Município</span>
                    </button>
                    <button v-if="permissoes.importar" class="btn btn-moderno btn-primary-moderno d-flex align-items-center gap-2 px-3 py-2" @click="importarMunicipios" :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm" role="status"></span>
                        <i v-else class="fas fa-file-import"></i>
                        <span>Importar</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros Colapsáveis -->
                <div class="filtros-aba-container mb-3" v-if="filtrosVisiveis">
                    <div class="filtros-aba-content" :class="{ 'show': filtrosVisiveis }">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="filtroNome" 
                                           v-model="filtros.nome" 
                                           @input="filtrarMunicipios"
                                           placeholder="Nome...">
                                    <label for="filtroNome">Nome</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="filtroPrefeito" 
                                           v-model="filtros.prefeito" 
                                           @input="filtrarMunicipios"
                                           placeholder="Prefeito...">
                                    <label for="filtroPrefeito">Prefeito</label>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button class="btn btn-outline-secondary w-100" 
                                        style="height: 58px; line-height: 1.5;" 
                                        @click="limparFiltros">
                                    <i class="fas fa-times me-2"></i>Limpar Filtros
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" v-if="municipiosOrdenados.length > 0">
                    <table class="table table-hover align-middle admin-table">
                        <thead>
                            <tr>
                                <th class="fw-semibold text-custom">Nome</th>
                                <th class="fw-semibold text-custom">Prefeito</th>
                                <th class="fw-semibold text-custom">Email</th>
                                <th class="fw-semibold text-custom" style="width: 130px;">Código IBGE</th>
                                <th class="fw-semibold text-custom" style="width: 130px;">População</th>
                                <th class="fw-semibold text-custom" style="width: 130px;">Telefone</th>
                                <th class="fw-semibold text-end text-custom" style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="municipio in municipiosOrdenados" :key="municipio.id" class="admin-row">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="fw-medium">{{ municipio.nome }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ municipio.prefeito }}</div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" :title="municipio.email">
                                        {{ municipio.email || 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="">
                                        {{ municipio.codigo_ibge }}
                                    </span>
                                </td>
                                <td>
                                    <span class="">
                                        {{ municipio.populacao.toLocaleString('pt-BR') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 150px;" :title="municipio.telefone">
                                        {{ municipio.telefone || 'N/A' }}
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group-actions" v-if="permissoes.crud">
                                        <button class="btn btn-action btn-warning me-1" @click="editarMunicipio(municipio)" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-action btn-danger" @click="confirmarExclusao(municipio)" title="Excluir">
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

                <!-- Estado de Carregamento -->
                <div v-if="loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando municípios...</p>
                </div>

                <!-- Estado Vazio -->
                <div v-if="!loading && municipiosOrdenados.length === 0" class="text-center py-5">
                    <i class="fas fa-city text-muted mb-3" style="font-size: 3rem;"></i>
                    <h6 class="text-muted mb-2">Nenhum município encontrado</h6>
                    <p class="text-muted small mb-0">Tente ajustar os filtros ou criar um novo município.</p>
                </div>
            </div>
        </div>

        <!-- Paginação (Fora do Card - Padrão) -->
        <div v-if="municipiosOrdenados.length > 0" class="paginacao-container mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted fw-medium">
                    Mostrando {{ municipios.from }} até {{ municipios.to }} de {{ municipios.total }} registros
                </div>
                <nav v-if="municipios.last_page > 1">
                    <ul class="pagination admin-pagination mb-0">
                        <!-- Botão Anterior -->
                        <li class="page-item" :class="{ disabled: municipios.current_page === 1 }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(municipios.current_page - 1)" aria-label="Anterior">
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
                        <li v-for="page in paginas" :key="page" class="page-item" :class="{ active: page === municipios.current_page }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(page)">{{ page }}</a>
                        </li>

                        <!-- Última Página -->
                        <li v-if="paginas[paginas.length - 1] < municipios.last_page - 1" class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <li v-if="paginas[paginas.length - 1] < municipios.last_page" class="page-item">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(municipios.last_page)">{{ municipios.last_page }}</a>
                        </li>

                        <!-- Botão Próximo -->
                        <li class="page-item" :class="{ disabled: municipios.current_page === municipios.last_page }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(municipios.current_page + 1)" aria-label="Próximo">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Modal Criar/Editar -->
        <div class="modal fade" id="municipioModal" tabindex="-1" ref="municipioModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
                            </div>
                            <h5 class="modal-title mb-0" id="modalLabel">
                                {{ modoEdicao ? 'Editar Município' : 'Novo Município' }}
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <form @submit.prevent="salvarMunicipio" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.nome }"
                                               id="nome" 
                                               v-model="form.nome" 
                                               required>
                                        <label for="nome">Nome</label>
                                        <div class="invalid-feedback" v-if="errors.nome">
                                            {{ Array.isArray(errors.nome) ? errors.nome[0] : errors.nome }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.prefeito }"
                                               id="prefeito" 
                                               v-model="form.prefeito" 
                                               required>
                                        <label for="prefeito">Prefeito</label>
                                        <div class="invalid-feedback" v-if="errors.prefeito">
                                            {{ Array.isArray(errors.prefeito) ? errors.prefeito[0] : errors.prefeito }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.email }"
                                               id="email" 
                                               v-model="form.email">
                                        <label for="email">Email</label>
                                        <div class="invalid-feedback" v-if="errors.email">
                                            {{ Array.isArray(errors.email) ? errors.email[0] : errors.email }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.codigo_ibge }"
                                               id="codigo_ibge" 
                                               v-model="form.codigo_ibge" 
                                               required>
                                        <label for="codigo_ibge">Código IBGE</label>
                                        <div class="invalid-feedback" v-if="errors.codigo_ibge">
                                            {{ Array.isArray(errors.codigo_ibge) ? errors.codigo_ibge[0] : errors.codigo_ibge }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.endereco_prefeitura }"
                                               id="endereco_prefeitura" 
                                               v-model="form.endereco_prefeitura" 
                                               required>
                                        <label for="endereco_prefeitura">Endereço da Prefeitura</label>
                                        <div class="invalid-feedback" v-if="errors.endereco_prefeitura">
                                            {{ Array.isArray(errors.endereco_prefeitura) ? errors.endereco_prefeitura[0] : errors.endereco_prefeitura }}
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
                                               required>
                                        <label for="cep">CEP</label>
                                        <div class="invalid-feedback" v-if="errors.cep">
                                            {{ Array.isArray(errors.cep) ? errors.cep[0] : errors.cep }}
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
                                               required>
                                        <label for="telefone">Telefone</label>
                                        <div class="invalid-feedback" v-if="errors.telefone">
                                            {{ Array.isArray(errors.telefone) ? errors.telefone[0] : errors.telefone }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.cnpj }"
                                               id="cnpj" 
                                               v-model="form.cnpj" 
                                               required>
                                        <label for="cnpj">CNPJ</label>
                                        <div class="invalid-feedback" v-if="errors.cnpj">
                                            {{ Array.isArray(errors.cnpj) ? errors.cnpj[0] : errors.cnpj }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="number" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.populacao }"
                                               id="populacao" 
                                               v-model="form.populacao" 
                                               required>
                                        <label for="populacao">População</label>
                                        <div class="invalid-feedback" v-if="errors.populacao">
                                            {{ Array.isArray(errors.populacao) ? errors.populacao[0] : errors.populacao }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" @click="salvarMunicipio" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-save me-2"></i>
                            {{ loading ? 'Salvando...' : 'Salvar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação -->
        <div class="modal fade" id="confirmacaoModal" tabindex="-1" ref="confirmacaoModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h5 class="modal-title mb-0" id="modalLabel">
                                Confirmar Exclusão
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Tem certeza que deseja excluir o município <strong>"{{ municipioSelecionado?.nome }}"</strong>?</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" @click="excluirMunicipio" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-trash me-2"></i>
                            {{ loading ? 'Excluindo...' : 'Excluir' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast de Notificação -->
        <div class="position-fixed bottom-0 end-0 p-3 toast-container">
            <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="fas" :class="toastIcon"></i>
                    <strong class="me-auto ms-2">{{ toastTitle }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ toastMessage }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Modal, Toast } from 'bootstrap';
import axios from 'axios';

export default {
    props: {
        permissoes: {
            type: Object,
            default: () => ({
                crud: true,
                consultar: true,
                importar: true
            })
        }
    },
    data() {
        return {
            municipios: {
                data: [],
                current_page: 1,
                from: 0,
                to: 0,
                total: 0,
                last_page: 1
            },
            filtros: {
                nome: '',
                prefeito: ''
            },
            form: {
                nome: '',
                prefeito: '',
                email: '',
                endereco_prefeitura: '',
                codigo_ibge: '',
                populacao: '',
                cep: '',
                telefone: '',
                cnpj: ''
            },
            errors: {},
            modoEdicao: false,
            municipioSelecionado: null,
            municipioModal: null,
            confirmacaoModal: null,
            loading: false,
            toast: null,
            toastTitle: '',
            toastMessage: '',
            toastIcon: '',
            filtrosVisiveis: false
        }
    },
    mounted() {
        this.carregarMunicipios();
        this.municipioModal = new Modal(this.$refs.municipioModal);
        this.confirmacaoModal = new Modal(this.$refs.confirmacaoModal);
        this.toast = new Toast(document.getElementById('toast'));
    },
    computed: {
        paginas() {
            const pages = [];
            const maxPages = 5;
            let start = Math.max(1, this.municipios.current_page - Math.floor(maxPages / 2));
            let end = Math.min(this.municipios.last_page, start + maxPages - 1);

            if (end - start + 1 < maxPages) {
                start = Math.max(1, end - maxPages + 1);
            }

            for (let i = start; i <= end; i++) {
                pages.push(i);
            }

            return pages;
        },
        
        // Municípios ordenados alfabeticamente
        municipiosOrdenados() {
            return [...this.municipios.data].sort((a, b) => {
                return a.nome.localeCompare(b.nome, 'pt-BR', { sensitivity: 'base' });
            });
        }
    },
    methods: {
        async carregarMunicipios() {
            try {
                const params = new URLSearchParams();
                if (this.filtros.nome) params.append('nome', this.filtros.nome);
                if (this.filtros.prefeito) params.append('prefeito', this.filtros.prefeito);
                params.append('page', this.municipios.current_page);

                const response = await axios.get(`/api/administracao/municipios?${params.toString()}`);
                this.municipios = response.data;
            } catch (error) {
                console.error('Erro ao carregar municípios:', error);
                this.mostrarToast('Erro', 'Não foi possível carregar os municípios', 'fa-exclamation-circle text-danger');
            }
        },
        mudarPagina(page) {
            if (page >= 1 && page <= this.municipios.last_page) {
                this.municipios.current_page = page;
                this.carregarMunicipios();
            }
        },
        toggleFilters() {
            this.filtrosVisiveis = !this.filtrosVisiveis;
        },
        filtrarMunicipios() {
            this.municipios.current_page = 1; // Volta para a primeira página ao filtrar
            this.carregarMunicipios();
        },
        limparFiltros() {
            this.filtros.nome = '';
            this.filtros.prefeito = '';
            this.municipios.current_page = 1;
            this.carregarMunicipios();
        },
        abrirModalCriar() {
            this.modoEdicao = false;
            this.limparFormulario();
            this.municipioModal.show();
        },
        editarMunicipio(municipio) {
            this.modoEdicao = true;
            this.form = { ...municipio };
            this.municipioModal.show();
        },
        limparFormulario() {
            this.form = {
                nome: '',
                prefeito: '',
                email: '',
                endereco_prefeitura: '',
                codigo_ibge: '',
                populacao: '',
                cep: '',
                telefone: '',
                cnpj: ''
            };
            this.errors = {};
        },
        mostrarToast(title, message, icon) {
            this.toastTitle = title;
            this.toastMessage = message;
            this.toastIcon = icon;
            this.toast.show();
        },
        async salvarMunicipio() {
            this.loading = true;
            this.errors = {};

            try {
                const url = this.modoEdicao 
                    ? `/api/administracao/municipios/${this.form.id}`
                    : '/api/administracao/municipios';
                
                const method = this.modoEdicao ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        // Erro de validação - seguir padrão do gerenciamento de usuários
                        this.errors = data.errors;
                        
                        // Focar no primeiro campo inválido
                        this.$nextTick(() => {
                            if (this.errors.nome) {
                                document.getElementById('nome')?.focus();
                            } else if (this.errors.prefeito) {
                                document.getElementById('prefeito')?.focus();
                            } else if (this.errors.email) {
                                document.getElementById('email')?.focus();
                            } else if (this.errors.endereco_prefeitura) {
                                document.getElementById('endereco_prefeitura')?.focus();
                            } else if (this.errors.codigo_ibge) {
                                document.getElementById('codigo_ibge')?.focus();
                            } else if (this.errors.populacao) {
                                document.getElementById('populacao')?.focus();
                            } else if (this.errors.cep) {
                                document.getElementById('cep')?.focus();
                            } else if (this.errors.telefone) {
                                document.getElementById('telefone')?.focus();
                            } else if (this.errors.cnpj) {
                                document.getElementById('cnpj')?.focus();
                            }
                        });
                        
                        throw new Error('Por favor, corrija os erros no formulário');
                    }
                    throw new Error(data.message || 'Erro ao salvar município');
                }

                this.municipioModal.hide();
                this.carregarMunicipios();
                this.limparFormulario();
                
                // Feedback de sucesso
                this.mostrarToast(
                    'Sucesso', 
                    `Município ${this.modoEdicao ? 'atualizado' : 'criado'} com sucesso!`,
                    'fa-check-circle text-success'
                );
            } catch (error) {
                console.error('Erro ao salvar município:', error);
                this.mostrarToast('Erro', error.message, 'fa-exclamation-circle text-danger');
            } finally {
                this.loading = false;
            }
        },
        confirmarExclusao(municipio) {
            this.municipioSelecionado = municipio;
            this.confirmacaoModal.show();
        },
        async excluirMunicipio() {
            this.loading = true;
            try {
                const response = await fetch(`/api/administracao/municipios/${this.municipioSelecionado.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao excluir município');
                }

                this.confirmacaoModal.hide();
                this.carregarMunicipios();
                this.mostrarToast('Sucesso', 'Município excluído com sucesso!', 'fa-check-circle text-success');
            } catch (error) {
                console.error('Erro ao excluir município:', error);
                this.mostrarToast('Erro', error.message, 'fa-exclamation-circle text-danger');
            } finally {
                this.loading = false;
            }
        },
        async importarMunicipios() {
            this.loading = true;
            try {
                const response = await fetch('/api/administracao/municipios/importar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Erro ao importar municípios');
                }

                this.carregarMunicipios();
                this.mostrarToast(
                    'Sucesso', 
                    `${data.importados} municípios importados com sucesso!`,
                    'fa-check-circle text-success'
                );
            } catch (error) {
                console.error('Erro ao importar municípios:', error);
                this.mostrarToast('Erro', error.message, 'fa-exclamation-circle text-danger');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<!-- Estilos específicos removidos - usando CSS global modern-interface.css -->
