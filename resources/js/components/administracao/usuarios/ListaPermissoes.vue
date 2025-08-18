<template>
    <div class="lista-permissoes">
        <!-- Header com Botão Criar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="text-custom mb-0">
                <i class="fas fa-key me-2"></i>Gerenciamento de Permissões
            </h5>
            <button class="btn btn-success" @click="abrirModalCriar">
                <i class="fas fa-plus me-2"></i>Nova Permissão
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
                    <div class="col-md-6">
                        <label for="filtroNomePermissao" class="form-label">Nome da Permissão</label>
                        <input type="text" class="form-control" id="filtroNomePermissao" v-model="filtros.nome" placeholder="Buscar por nome...">
                    </div>
                    <div class="col-md-3">
                        <label for="filtroStatusPermissao" class="form-label">Status</label>
                        <select class="form-select" id="filtroStatusPermissao" v-model="filtros.is_active">
                            <option value="">Todos</option>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary me-2" @click="filtrarDados">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                        <button class="btn btn-outline-secondary" @click="limparFiltros">
                            <i class="fas fa-times me-1"></i>Limpar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Dados -->
        <div class="card">
            <div class="card-body p-0">
                <div v-if="loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando permissões...</p>
                </div>

                <div v-else-if="permissoes.length === 0" class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Nenhuma permissão encontrada</p>
                </div>

                <div v-else class="table-responsive">
                    <table class="table table-hover align-middle table-admin mb-0">
                        <thead>
                            <tr>
                                <th class="fw-semibold text-custom">Nome Interno</th>
                                <th class="fw-semibold text-custom">Nome de Exibição</th>
                                <th class="fw-semibold text-custom">Descrição</th>
                                <th class="fw-semibold text-custom">Status</th>
                                <th class="fw-semibold text-custom">Papéis</th>
                                <th class="fw-semibold text-custom">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="permissao in permissoes" :key="permissao.id" class="table-admin-row">
                                <td>
                                    <code class="text-primary">{{ permissao.name }}</code>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ permissao.display_name }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ permissao.description || 'Sem descrição' }}</span>
                                </td>
                                <td>
                                    <span class="badge" :class="permissao.is_active ? 'badge-success' : 'badge-danger'">
                                        {{ permissao.is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ permissao.roles_count || 0 }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" @click="editarPermissao(permissao)" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" @click="gerenciarRoles(permissao)" title="Gerenciar Papéis">
                                            <i class="fas fa-shield-alt"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" @click="excluirPermissao(permissao)" title="Excluir">
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

        <!-- Modais -->
        <!-- Modal de Formulário -->
        <div class="modal fade" id="modalPermissao" tabindex="-1" aria-labelledby="modalPermissaoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-key"></i>
                            </div>
                            <h5 class="modal-title" id="modalPermissaoLabel">
                                {{ modoEdicao ? 'Editar' : 'Nova' }} Permissão
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="salvarPermissao">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="namePermissao" class="form-label">Nome Interno *</label>
                                    <input type="text" class="form-control" id="namePermissao" v-model="form.name" :disabled="modoEdicao" required>
                                    <div v-if="errors.name" class="invalid-feedback d-block">{{ errors.name[0] }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="display_namePermissao" class="form-label">Nome de Exibição *</label>
                                    <input type="text" class="form-control" id="display_namePermissao" v-model="form.display_name" required>
                                    <div v-if="errors.display_name" class="invalid-feedback d-block">{{ errors.display_name[0] }}</div>
                                </div>
                                <div class="col-12">
                                    <label for="descriptionPermissao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="descriptionPermissao" v-model="form.description" rows="3"></textarea>
                                    <div v-if="errors.description" class="invalid-feedback d-block">{{ errors.description[0] }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_activePermissao" v-model="form.is_active">
                                        <label class="form-check-label" for="is_activePermissao">
                                            Permissão Ativa
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" @click="salvarPermissao" :disabled="salvando">
                            <span v-if="salvando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            {{ modoEdicao ? 'Atualizar' : 'Criar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação de Exclusão -->
        <div class="modal fade modal-confirmacao" id="modalConfirmacaoExclusao" tabindex="-1" ref="modalConfirmacaoRef">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon" aria-hidden="true">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h5 class="modal-title mb-0">
                                Confirmar Exclusão
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="confirm-text mb-1">Tem certeza que deseja excluir a permissão</p>
                        <p class="target-entity fs-5 mb-3">
                            <span id="nomePermissao">"{{ itemParaExcluir?.display_name }}"</span>
                        </p>

                        <!-- Caixa de Aviso -->
                        <div class="irreversible mb-1" role="status" aria-live="polite">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span>Esta ação é permanente e não poderá ser desfeita.</span>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
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

        <!-- Toast -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i :class="toastIcon" class="me-2"></i>
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
    name: 'ListaPermissoes',
    
    data() {
        return {
            // Dados principais
            permissoes: [],
            
            // Estados de interface
            loading: false,
            filtrosVisiveis: false,
            modoEdicao: false,
            itemSelecionado: null,
            
            // Formulário
            form: {
                name: '',
                display_name: '',
                description: '',
                is_active: true
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
                nome: '',
                is_active: ''
            }
        }
    },
    
    mounted() {
        this.carregarDados();
        this.toast = new bootstrap.Toast(document.getElementById('toast'));
        this.permissaoModal = new bootstrap.Modal(document.getElementById('modalPermissao'));
    },
    
    methods: {
        // Métodos principais
        async carregarDados() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.filtros.nome) params.append('nome', this.filtros.nome);
                if (this.filtros.is_active !== '') params.append('is_active', this.filtros.is_active);
                
                const response = await axios.get(`/api/administracao/permissoes?${params}`);
                
                this.permissoes = response.data;
                
                // EMITIR para o componente pai
                this.$emit('paginacao-updated', {
                    data: this.permissoes,
                    current_page: 1,
                    last_page: 1,
                    per_page: this.permissoes.length,
                    total: this.permissoes.length
                });
                
            } catch (error) {
                console.error('Erro ao carregar permissões:', error);
                this.mostrarToast('Erro', 'Erro ao carregar permissões', 'fa-exclamation-circle text-danger');
            } finally {
                this.loading = false;
            }
        },
        
        // Filtros
        filtrarDados() {
            this.carregarDados();
        },
        
        limparFiltros() {
            this.filtros = {
                nome: '',
                is_active: ''
            };
            this.carregarDados();
        },
        
        toggleFiltros() {
            this.filtrosVisiveis = !this.filtrosVisiveis;
        },
        
        // Modal de formulário
        abrirModalCriar() {
            this.modoEdicao = false;
            this.limparFormulario();
            this.permissaoModal.show();
        },
        
        editarPermissao(permissao) {
            this.modoEdicao = true;
            this.itemSelecionado = permissao;
            this.form = { ...permissao };
            this.permissaoModal.show();
        },
        
        limparFormulario() {
            this.form = {
                name: '',
                display_name: '',
                description: '',
                is_active: true
            };
            this.errors = {};
        },
        
        // Salvar
        async salvarPermissao() {
            this.salvando = true;
            this.errors = {};

            try {
                const url = this.modoEdicao 
                    ? `/api/administracao/permissoes/${this.form.id}`
                    : '/api/administracao/permissoes';
                
                const method = this.modoEdicao ? 'put' : 'post';
                
                const response = await axios[method](url, this.form);

                this.permissaoModal.hide();
                this.mostrarToast('Sucesso', this.modoEdicao ? 'Permissão atualizada com sucesso!' : 'Permissão criada com sucesso!', 'fa-check-circle text-success');
                this.carregarDados();
                
            } catch (error) {
                console.error('Erro ao salvar permissão:', error);
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    this.mostrarToast('Erro', error.response?.data?.message || 'Erro ao salvar permissão', 'fa-exclamation-circle text-danger');
                }
            } finally {
                this.salvando = false;
            }
        },
        
        // Exclusão
        excluirPermissao(permissao) {
            this.itemParaExcluir = permissao;
            const modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacaoExclusao'));
            modalConfirmacao.show();
        },
        
        async confirmarExclusao() {
            if (!this.itemParaExcluir) return;
            
            this.excluindo = true;
            try {
                const response = await axios.delete(`/api/administracao/permissoes/${this.itemParaExcluir.id}`);

                const modalConfirmacao = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacaoExclusao'));
                modalConfirmacao.hide();
                
                this.mostrarToast('Sucesso', 'Permissão excluída com sucesso!', 'fa-check-circle text-success');
                this.carregarDados();
                
            } catch (error) {
                console.error('Erro:', error);
                this.mostrarToast('Erro', error.response?.data?.message || 'Erro ao excluir permissão', 'fa-exclamation-circle text-danger');
            } finally {
                this.excluindo = false;
                this.itemParaExcluir = null;
            }
        },
        
        // Gerenciar papéis
        gerenciarRoles(permissao) {
            // Emitir evento para o componente pai gerenciar
            this.$emit('gerenciar-roles', permissao);
        },
        
        // Método para controle externo da paginação
        mudarPaginaExterna(page) {
            // Como não temos paginação real, apenas recarregar
            this.carregarDados();
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
