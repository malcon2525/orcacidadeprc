<template>
    <div>
        <!-- Header com Botão Criar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="text-custom mb-0">
                <i class="fas fa-user-tag me-2"></i>Gerenciamento de Papéis (Roles)
            </h5>
            <button class="btn btn-success" @click="abrirModalCriar">
                <i class="fas fa-plus me-2"></i>Novo Papel
            </button>
        </div>

        <!-- Filtros Colapsáveis -->
        <div class="card mb-4">
            <div class="card-header bg-light py-2">
                <button class="btn btn-link text-decoration-none p-0" @click="toggleFiltros">
                    <i class="fas fa-filter me-2"></i>
                    {{ filtrosVisiveis ? 'Ocultar' : 'Mostrar' }} Filtros
                    <i class="fas" :class="filtrosVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
            </div>
            <div class="card-body" v-show="filtrosVisiveis">
                <div class="row g-3">
                    <!-- Campo de Busca -->
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control" 
                                   id="busca" 
                                   v-model="filtros.busca"
                                   placeholder="Buscar por nome..."
                                   @keyup.enter="filtrarDados">
                            <label for="busca">Buscar</label>
                        </div>
                    </div>
                    
                    <!-- Filtro de Status -->
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-control" id="filtroStatus" v-model="filtros.status">
                                <option value="">Todos</option>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                            <label for="filtroStatus">Status</label>
                        </div>
                    </div>
                    
                    <!-- Filtro de Tipo -->
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-control" id="filtroTipo" v-model="filtros.tipo">
                                <option value="">Todos</option>
                                <option value="sistema">Sistema</option>
                                <option value="personalizado">Personalizado</option>
                            </select>
                            <label for="filtroTipo">Tipo</label>
                        </div>
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="col-md-2 d-flex align-items-end gap-2">
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

        <!-- Tabela de Dados -->
        <div class="card">
            <div class="card-body p-0">
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando papéis...</p>
                </div>
                
                <!-- Tabela -->
                <div v-else-if="dados.length > 0" class="table-responsive">
                    <table class="table table-hover align-middle papeis-table">
                        <thead>
                            <tr>
                                <th class="fw-semibold text-custom">Nome</th>
                                <th class="fw-semibold text-custom">Slug</th>
                                <th class="fw-semibold text-custom">Descrição</th>
                                <th class="fw-semibold text-custom">Status</th>
                                <th class="fw-semibold text-custom">Tipo</th>
                                <th class="fw-semibold text-custom">Usuários</th>
                                <th class="fw-semibold text-end text-custom" style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="papel in dados" :key="papel.id" class="papel-row">
                                <td class="align-middle">
                                    <div class="fw-medium">{{ papel.name }}</div>
                                </td>
                                <td class="align-middle">
                                    <code class="text-muted">{{ papel.slug }}</code>
                                </td>
                                <td class="align-middle">
                                    <div class="text-muted">{{ papel.description || 'Sem descrição' }}</div>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-status" :class="papel.is_active ? 'badge-ativo' : 'badge-inativo'">
                                        <i class="fas" :class="papel.is_active ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                        {{ papel.is_active ? 'ATIVO' : 'INATIVO' }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="badge" :class="getTipoBadgeClass(papel.type)">
                                        {{ getTipoLabel(papel.type) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-info">{{ papel.users_count || 0 }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <!-- Botão Editar: AMARELO SÓLIDO -->
                                        <button class="btn btn-sm btn-warning" @click="editarItem(papel)" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Botão Excluir: VERMELHO SÓLIDO -->
                                        <button class="btn btn-sm btn-danger" @click="excluirItem(papel)" title="Excluir" :disabled="papel.type === 'sistema'">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Estado Vazio -->
                <div v-else class="text-center py-5">
                    <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                    <h6 class="mt-3 text-muted">Nenhum papel encontrado</h6>
                    <p class="text-muted">Não há papéis cadastrados ainda.</p>
                </div>
            </div>
        </div>

        <!-- Paginação Local -->
        <div class="mt-4 pt-3 border-top">
            <nav v-if="registros.last_page > 1" aria-label="Navegação de páginas">
                <ul class="pagination justify-content-center mb-0">
                    <!-- Botão Anterior -->
                    <li class="page-item" :class="{ disabled: registros.current_page === 1 }">
                        <button class="page-link" @click="mudarPagina(registros.current_page - 1)" :disabled="registros.current_page === 1">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </li>
                    
                    <!-- Páginas -->
                    <li v-for="page in getPaginasVisiveis()" :key="page" class="page-item" :class="{ active: page === registros.current_page }">
                        <button class="page-link" @click="mudarPagina(page)">
                            {{ page }}
                        </button>
                    </li>
                    
                    <!-- Botão Próximo -->
                    <li class="page-item" :class="{ disabled: registros.current_page === registros.last_page }">
                        <button class="page-link" @click="mudarPagina(registros.current_page + 1)" :disabled="registros.current_page === registros.last_page">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </li>
                </ul>
            </nav>
            
            <!-- Informações da Paginação -->
            <div class="text-center text-muted small mt-2">
                Mostrando {{ ((registros.current_page - 1) * registros.per_page) + 1 }} 
                a {{ Math.min(registros.current_page * registros.per_page, registros.total) }} 
                de {{ registros.total }} registros
            </div>
        </div>

        <!-- Modal de Formulário -->
        <div class="modal fade" id="modalPapel" tabindex="-1" ref="modalPapelRef">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Header com Gradiente -->
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
                            </div>
                            <h5 class="modal-title mb-0">
                                {{ modoEdicao ? 'Editar Papel' : 'Novo Papel' }}
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Corpo do Modal -->
                    <div class="modal-body">
                        <form @submit.prevent="salvarPapel">
                            <div class="row g-3">
                                <!-- Campo Nome -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.name }"
                                               id="name" 
                                               v-model="form.name"
                                               placeholder="Nome do papel"
                                               required>
                                        <label for="name">Nome do Papel</label>
                                    </div>
                                    <div class="invalid-feedback" v-if="errors.name">
                                        {{ errors.name[0] }}
                                    </div>
                                </div>
                                
                                <!-- Campo Slug -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.slug }"
                                               id="slug" 
                                               v-model="form.slug"
                                               placeholder="slug-do-papel"
                                               required>
                                        <label for="slug">Slug</label>
                                    </div>
                                    <div class="invalid-feedback" v-if="errors.slug">
                                        {{ errors.slug[0] }}
                                    </div>
                                </div>
                                
                                <!-- Campo Status -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-control" 
                                                :class="{ 'is-invalid': errors.is_active }"
                                                id="is_active" 
                                                v-model="form.is_active" 
                                                required>
                                            <option value="">Selecione...</option>
                                            <option :value="true">Ativo</option>
                                            <option :value="false">Inativo</option>
                                        </select>
                                        <label for="is_active">Status</label>
                                    </div>
                                    <div class="invalid-feedback" v-if="errors.is_active">
                                        {{ errors.is_active[0] }}
                                    </div>
                                </div>
                                
                                <!-- Campo Tipo -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-control" 
                                                :class="{ 'is-invalid': errors.type }"
                                                id="type" 
                                                v-model="form.type" 
                                                required>
                                            <option value="">Selecione...</option>
                                            <option value="personalizado">Personalizado</option>
                                            <option value="sistema">Sistema</option>
                                        </select>
                                        <label for="type">Tipo</label>
                                    </div>
                                    <div class="invalid-feedback" v-if="errors.type">
                                        {{ errors.type[0] }}
                                    </div>
                                </div>
                                
                                <!-- Campo Descrição -->
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" 
                                                  id="description" 
                                                  v-model="form.description"
                                                  placeholder="Descrição do papel"
                                                  style="height: 100px"></textarea>
                                        <label for="description">Descrição</label>
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
                        <button type="button" class="btn btn-success" @click="salvarPapel" :disabled="salvando">
                            <span v-if="salvando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-save me-2"></i>
                            {{ salvando ? 'Salvando...' : 'Salvar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação de Exclusão -->
        <div class="modal fade" id="modalConfirmacaoExclusao" tabindex="-1" ref="modalConfirmacaoRef">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Header com Gradiente -->
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h5 class="modal-title mb-0">
                                Confirmar Exclusão
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Corpo do Modal -->
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                            <h6 class="mb-3">Tem certeza que deseja excluir?</h6>
                            <p class="text-muted mb-0">
                                <strong>"{{ itemParaExcluir?.name }}"</strong> será removido permanentemente.
                            </p>
                            <p class="text-muted small mt-2">
                                Esta ação não pode ser desfeita.
                            </p>
                        </div>
                    </div>

                    <!-- Rodapé do Modal -->
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" @click="confirmarExclusao" :disabled="excluindo">
                            <span v-if="excluindo" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-trash me-2"></i>
                            {{ excluindo ? 'Excluindo...' : 'Excluir' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast de Notificação -->
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i :class="toastIcon + ' me-2'"></i>
                    <strong class="me-auto">{{ toastTitle }}</strong>
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
export default {
    name: 'ListaPapeis',
    data() {
        return {
            // Dados principais
            dados: [],
            registros: {
                data: [],
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 0
            },
            
            // Estados de interface
            loading: false,
            filtrosVisiveis: false,
            modoEdicao: false,
            itemSelecionado: null,
            
            // Formulário
            form: {
                name: '',
                slug: '',
                description: '',
                is_active: true,
                type: 'personalizado'
            },
            
            // Validação
            errors: {},
            
            // Estados de modal
            salvando: false,
            excluindo: false,
            itemParaExcluir: null,
            
            // Toast
            toastTitle: '',
            toastMessage: '',
            toastIcon: '',
            
            // Filtros
            filtros: {
                busca: '',
                status: '',
                tipo: ''
            }
        }
    },
    
    mounted() {
        this.carregarDados();
        this.toast = new bootstrap.Toast(document.getElementById('toast'));
        this.papelModal = new bootstrap.Modal(document.getElementById('modalPapel'));
    },
    
    methods: {
        // Métodos principais
        async carregarDados() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.registros.current_page,
                    ...this.filtros
                });
                
                const response = await fetch(`/api/papeis?${params}`);
                const data = await response.json();
                
                this.registros = data;
                this.dados = data.data;
                
            } catch (error) {
                console.error('Erro ao carregar dados:', error);
                this.mostrarToast('Erro', 'Erro ao carregar papéis', 'fa-exclamation-circle text-danger');
            } finally {
                this.loading = false;
            }
        },
        
        // Filtros
        filtrarDados() {
            this.registros.current_page = 1;
            this.carregarDados();
        },
        
        limparFiltros() {
            this.filtros = {
                busca: '',
                status: '',
                tipo: ''
            };
            this.filtrarDados();
        },
        
        toggleFiltros() {
            this.filtrosVisiveis = !this.filtrosVisiveis;
        },
        
        // Modal de formulário
        abrirModalCriar() {
            this.modoEdicao = false;
            this.limparFormulario();
            this.papelModal.show();
        },
        
        editarItem(item) {
            this.modoEdicao = true;
            this.itemSelecionado = item;
            this.form = { ...item };
            this.papelModal.show();
        },
        
        limparFormulario() {
            this.form = {
                name: '',
                slug: '',
                description: '',
                is_active: true,
                type: 'personalizado'
            };
            this.errors = {};
        },
        
        // Salvar
        async salvarPapel() {
            this.salvando = true;
            this.errors = {};

            try {
                const url = this.modoEdicao 
                    ? `/api/papeis/${this.form.id}`
                    : '/api/papeis';
                
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
                    if (response.status === 422) {
                        this.errors = data.errors;
                        throw new Error('Por favor, corrija os erros no formulário');
                    }
                    throw new Error(data.message || 'Erro ao salvar');
                }

                this.papelModal.hide();
                this.mostrarToast('Sucesso', this.modoEdicao ? 'Papel atualizado com sucesso!' : 'Papel criado com sucesso!', 'fa-check-circle text-success');
                this.carregarDados();
                
            } catch (error) {
                console.error('Erro ao salvar papel:', error);
                this.mostrarToast('Erro', error.message, 'fa-exclamation-circle text-danger');
            } finally {
                this.salvando = false;
            }
        },
        
        // Exclusão
        excluirItem(item) {
            this.itemParaExcluir = item;
            const modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacaoExclusao'));
            modalConfirmacao.show();
        },
        
        async confirmarExclusao() {
            if (!this.itemParaExcluir) return;
            
            this.excluindo = true;
            try {
                const response = await fetch(`/api/papeis/${this.itemParaExcluir.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const modalConfirmacao = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacaoExclusao'));
                    modalConfirmacao.hide();
                    
                    this.mostrarToast('Sucesso', 'Papel excluído com sucesso!', 'fa-check-circle text-success');
                    this.carregarDados();
                } else {
                    const data = await response.json();
                    this.mostrarToast('Erro', data.error || 'Erro ao excluir', 'fa-exclamation-circle text-danger');
                }
            } catch (error) {
                console.error('Erro:', error);
                this.mostrarToast('Erro', 'Erro ao excluir', 'fa-exclamation-circle text-danger');
            } finally {
                this.excluindo = false;
                this.itemParaExcluir = null;
            }
        },
        
        // Paginação local
        mudarPagina(page) {
            this.registros.current_page = page;
            this.carregarDados();
        },
        
        getPaginasVisiveis() {
            const total = this.registros.last_page;
            const atual = this.registros.current_page;
            const delta = 2;
            
            let inicio = Math.max(1, atual - delta);
            let fim = Math.min(total, atual + delta);
            
            if (fim - inicio < 4) {
                if (inicio === 1) {
                    fim = Math.min(total, inicio + 4);
                } else {
                    inicio = Math.max(1, fim - 4);
                }
            }
            
            const paginas = [];
            for (let i = inicio; i <= fim; i++) {
                paginas.push(i);
            }
            
            return paginas;
        },
        
        // Utilitários
        getTipoLabel(tipo) {
            const labels = {
                'sistema': 'Sistema',
                'personalizado': 'Personalizado'
            };
            return labels[tipo] || 'Desconhecido';
        },
        
        getTipoBadgeClass(tipo) {
            const classes = {
                'sistema': 'bg-warning',
                'personalizado': 'bg-primary'
            };
            return classes[tipo] || 'bg-secondary';
        },
        
        // Toast
        mostrarToast(title, message, icon) {
            this.toastTitle = title;
            this.toastMessage = message;
            this.toastIcon = icon;
            this.toast.show();
        }
    }
}
</script>

<style scoped>
/* ===== PADRÃO OFICIAL - FORM-FLOATING ===== */
/* ⚠️ NUNCA ALTERAR ESTAS REGRAS - ELAS GARANTEM FUNCIONAMENTO PERFEITO */
.form-floating .form-control {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
    line-height: 1.5 !important;
    min-height: 58px !important;
}

.form-floating .form-control:focus {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

.form-floating .form-control:not(:placeholder-shown) {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

/* Alinhamento dos campos em linha */
.row.g-3 {
    align-items: end;
}

/* Header personalizado do modal */
.custom-modal-header {
    background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
    color: white;
    border-bottom: none;
    padding: 1.5rem;
    border-radius: 0.5rem 0.5rem 0 0;
}

/* Ícone do header */
.header-icon {
    width: 40px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.2rem;
    color: white;
}

/* Título do modal */
.custom-modal-header .modal-title {
    color: white;
    font-weight: 600;
    font-size: 1.25rem;
}

/* Botão fechar */
.custom-modal-header .btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* Validação */
.form-control.is-valid {
    border-color: #5EA853 !important;
    box-shadow: 0 0 0 0.2rem rgba(94, 168, 83, 0.25) !important;
}

.form-control.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.invalid-feedback {
    color: #dc3545 !important;
    font-size: 0.875rem !important;
}

/* Badges de status */
.badge-status {
    font-size: 0.75rem;
    padding: 0.5em 0.75em;
}

.badge-ativo {
    background-color: #5EA853 !important;
    color: white !important;
}

.badge-inativo {
    background-color: #dc3545 !important;
    color: white !important;
}

/* Cores personalizadas */
.text-custom {
    color: #5EA853 !important;
}

/* Tabela responsiva */
.papeis-table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.papel-row:hover {
    background-color: #f8f9fa;
}

/* Código inline */
code {
    background-color: #f8f9fa;
    padding: 0.2em 0.4em;
    border-radius: 0.25rem;
    font-size: 0.875em;
}
</style>
