<template>
    <div>
        <!-- Header com Seleção de Entidade -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="text-custom mb-0">
                <i class="fas fa-users-cog me-2"></i>Usuários por Entidade
            </h5>
            <div class="d-flex gap-2">
                <button class="btn btn-success btn-sm" @click="abrirModalVincular" :disabled="!entidadeSelecionada">
                    <i class="fas fa-plus me-1"></i>Vincular Usuários
                </button>
                <button class="btn btn-outline-primary btn-sm" @click="carregarUsuarios">
                    <i class="fas fa-sync-alt me-1"></i>Atualizar
                </button>
            </div>
        </div>

        <!-- Seleção de Entidade -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-control" id="entidadeSelect" v-model="entidadeSelecionada" @change="onEntidadeChange">
                                <option value="">Selecione uma entidade...</option>
                                <option v-for="entidade in entidades" :key="entidade.id" :value="entidade.id">
                                    {{ entidade.nome }}
                                </option>
                            </select>
                            <label for="entidadeSelect">Entidade Orçamentária</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control" 
                                   id="busca" 
                                   v-model="filtros.busca"
                                   placeholder="Buscar usuários..."
                                   @keyup.enter="carregarUsuarios"
                                   :disabled="!entidadeSelecionada">
                            <label for="busca">Buscar</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-control" id="filtroAtivo" v-model="filtros.ativo" @change="carregarUsuarios">
                                <option value="">Todos</option>
                                <option value="true">Ativos</option>
                                <option value="false">Inativos</option>
                            </select>
                            <label for="filtroAtivo">Status</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Usuários -->
        <div class="card" v-if="entidadeSelecionada">
            <div class="card-body p-0">
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando usuários...</p>
                </div>
                
                <!-- Tabela -->
                <div v-else-if="dados.length > 0" class="table-responsive">
                    <table class="table table-hover align-middle table-admin">
                        <thead>
                            <tr>
                                <th class="fw-semibold text-custom">Usuário</th>
                                <th class="fw-semibold text-custom">Município</th>
                                <th class="fw-semibold text-custom">Status Usuário</th>
                                <th class="fw-semibold text-custom">Status Vinculação</th>
                                <th class="fw-semibold text-custom">Data Vinculação</th>
                                <th class="fw-semibold text-custom text-end w-180px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="usuario in dados" :key="usuario.id" class="table-admin-row">
                                <td class="align-middle">
                                    <div class="fw-medium">{{ usuario.name }}</div>
                                    <small class="text-muted">{{ usuario.email }}</small>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-medium">{{ usuario.municipio?.nome || 'N/A' }}</div>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-status" :class="usuario.is_active ? 'badge-ativo' : 'badge-inativo'">
                                        <i class="fas" :class="usuario.is_active ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                        {{ usuario.is_active ? 'ATIVO' : 'INATIVO' }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-status" :class="usuario.pivot?.ativo ? 'badge-ativo' : 'badge-inativo'">
                                        <i class="fas" :class="usuario.pivot?.ativo ? 'fa-link' : 'fa-unlink'"></i>
                                        {{ usuario.pivot?.ativo ? 'VINCULADO' : 'DESVINCULADO' }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-medium">{{ formatarData(usuario.pivot?.data_vinculacao) }}</div>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <template v-if="usuario.pivot?.ativo">
                                            <button class="btn btn-sm btn-warning" @click="desvinculerUsuario(usuario)" title="Desvincular">
                                                <i class="fas fa-unlink"></i>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button class="btn btn-sm btn-success" @click="reativarUsuario(usuario)" title="Reativar">
                                                <i class="fas fa-link"></i>
                                            </button>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Estado Vazio -->
                <div v-else class="text-center py-5">
                    <i class="fas fa-users text-muted" style="font-size: 3rem;"></i>
                    <h6 class="mt-3 text-muted">Nenhum usuário encontrado</h6>
                    <p class="text-muted">Esta entidade ainda não possui usuários vinculados.</p>
                </div>
            </div>
        </div>

        <!-- Estado Inicial -->
        <div v-else class="text-center py-5">
            <i class="fas fa-building text-muted" style="font-size: 3rem;"></i>
            <h6 class="mt-3 text-muted">Selecione uma entidade</h6>
            <p class="text-muted">Escolha uma entidade orçamentária para visualizar os usuários vinculados.</p>
        </div>

        <!-- Paginação -->
        <div v-if="registros.last_page > 1" class="paginacao-container mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted fw-medium">
                    Mostrando {{ ((registros.current_page - 1) * registros.per_page) + 1 }} 
                    até {{ Math.min(registros.current_page * registros.per_page, registros.total) }} 
                    de {{ registros.total }} registros
                </div>
                <nav>
                    <ul class="pagination pagination-generic mb-0">
                        <li class="page-item" :class="{ disabled: registros.current_page === 1 }">
                            <button class="page-link" @click="mudarPagina(registros.current_page - 1)" :disabled="registros.current_page === 1">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        </li>
                        <li v-for="page in getPaginasVisiveis()" :key="page" class="page-item" :class="{ active: page === registros.current_page }">
                            <button class="page-link" @click="mudarPagina(page)">{{ page }}</button>
                        </li>
                        <li class="page-item" :class="{ disabled: registros.current_page === registros.last_page }">
                            <button class="page-link" @click="mudarPagina(registros.current_page + 1)" :disabled="registros.current_page === registros.last_page">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Modal de Vinculação -->
        <div class="modal fade" id="modalVincular" tabindex="-1" ref="modalVincularRef">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <h5 class="modal-title mb-0">Vincular Usuários</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control" 
                                       id="buscaModal" 
                                       v-model="buscaModal"
                                       placeholder="Buscar usuários..."
                                       @input="carregarUsuariosDisponiveis">
                                <label for="buscaModal">Buscar usuários disponíveis</label>
                            </div>
                        </div>
                        
                        <!-- Loading -->
                        <div v-if="loadingDisponiveis" class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                        </div>
                        
                        <!-- Lista de usuários disponíveis -->
                        <div v-else class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                            <div v-if="usuariosDisponiveis.length === 0" class="text-center text-muted">
                                Nenhum usuário disponível para vinculação
                            </div>
                            <div v-else>
                                <div class="form-check mb-2" v-for="usuario in usuariosDisponiveis" :key="usuario.id">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           :value="usuario.id" 
                                           :id="`user-${usuario.id}`"
                                           v-model="usuariosSelecionados">
                                    <label class="form-check-label w-100" :for="`user-${usuario.id}`">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-medium">{{ usuario.name }}</div>
                                                <small class="text-muted">{{ usuario.email }}</small>
                                            </div>
                                            <small class="text-muted">{{ usuario.municipio?.nome || 'N/A' }}</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="usuariosSelecionados.length > 0" class="mt-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ usuariosSelecionados.length }} usuário(s) selecionado(s) para vinculação.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="button" 
                                class="btn btn-success" 
                                @click="confirmarVinculacao" 
                                :disabled="vinculando || usuariosSelecionados.length === 0">
                            <span v-if="vinculando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-link me-2"></i>
                            {{ vinculando ? 'Vinculando...' : `Vincular ${usuariosSelecionados.length} Usuário(s)` }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação para Desvinculação -->
        <div class="modal fade modal-confirmacao" id="modalConfirmacaoDesvinculacao" tabindex="-1" ref="modalConfirmacaoRef">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon" aria-hidden="true">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h5 class="modal-title mb-0">Confirmar Desvinculação</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="confirm-text mb-1">Tem certeza que deseja desvincular o usuário</p>
                        <p class="target-entity fs-5 mb-3">
                            <span>"{{ usuarioParaDesvincular?.name }}"</span>
                        </p>
                        <div class="irreversible mb-1" role="status" aria-live="polite">
                            <i class="fas fa-info-circle me-2"></i>
                            <span>O usuário ficará inativo nesta entidade mas o histórico será mantido.</span>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-warning" @click="confirmarDesvinculacao" :disabled="desvinculando">
                            <span v-if="desvinculando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-unlink me-2"></i>
                            {{ desvinculando ? 'Desvinculando...' : 'Desvincular' }}
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
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">{{ toastMessage }}</div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ListaUsuariosPorEntidade',
    data() {
        return {
            // Dados principais
            entidades: [],
            entidadeSelecionada: '',
            dados: [],
            registros: {
                data: [],
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 0
            },
            
            // Estados
            loading: false,
            loadingDisponiveis: false,
            vinculando: false,
            desvinculando: false,
            
            // Filtros
            filtros: {
                busca: '',
                ativo: ''
            },
            
            // Modal de vinculação
            usuariosDisponiveis: [],
            usuariosSelecionados: [],
            buscaModal: '',
            
            // Modal de confirmação
            usuarioParaDesvincular: null,
            
            // Toast
            toastTitle: '',
            toastMessage: '',
            toastIcon: '',
            toast: null
        }
    },
    
    mounted() {
        this.carregarEntidades();
        this.toast = new bootstrap.Toast(document.getElementById('toast'));
    },
    
    methods: {
        async carregarEntidades() {
            try {
                const response = await fetch('/api/administracao/usuarios-por-entidade/entidades', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.entidades = data;
                } else {
                    throw new Error(data.message || 'Erro ao carregar entidades');
                }
                
            } catch (error) {
                console.error('Erro ao carregar entidades:', error);
                this.mostrarToast('Erro', 'Erro ao carregar entidades', 'fa-exclamation-circle text-danger');
            }
        },
        
        async carregarUsuarios() {
            if (!this.entidadeSelecionada) return;
            
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.registros.current_page,
                    per_page: this.registros.per_page,
                    ...this.filtros
                });
                
                const response = await fetch(`/api/administracao/usuarios-por-entidade/${this.entidadeSelecionada}/usuarios?${params}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.registros = data.usuarios;
                    this.dados = data.usuarios.data;
                } else {
                    throw new Error(data.message || 'Erro ao carregar usuários');
                }
                
            } catch (error) {
                console.error('Erro ao carregar usuários:', error);
                this.mostrarToast('Erro', 'Erro ao carregar usuários', 'fa-exclamation-circle text-danger');
            } finally {
                this.loading = false;
            }
        },
        
        async carregarUsuariosDisponiveis() {
            if (!this.entidadeSelecionada) return;
            
            this.loadingDisponiveis = true;
            try {
                const params = new URLSearchParams({
                    busca: this.buscaModal,
                    per_page: 50
                });
                
                const response = await fetch(`/api/administracao/usuarios-por-entidade/${this.entidadeSelecionada}/usuarios-disponiveis?${params}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.usuariosDisponiveis = data.data;
                } else {
                    throw new Error(data.message || 'Erro ao carregar usuários disponíveis');
                }
                
            } catch (error) {
                console.error('Erro ao carregar usuários disponíveis:', error);
                this.mostrarToast('Erro', 'Erro ao carregar usuários disponíveis', 'fa-exclamation-circle text-danger');
            } finally {
                this.loadingDisponiveis = false;
            }
        },
        
        onEntidadeChange() {
            this.dados = [];
            this.registros.current_page = 1;
            this.filtros.busca = '';
            this.filtros.ativo = '';
            
            if (this.entidadeSelecionada) {
                this.carregarUsuarios();
            }
        },
        
        mudarPagina(page) {
            this.registros.current_page = page;
            this.carregarUsuarios();
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
        
        abrirModalVincular() {
            this.usuariosSelecionados = [];
            this.buscaModal = '';
            this.carregarUsuariosDisponiveis();
            const modal = new bootstrap.Modal(document.getElementById('modalVincular'));
            modal.show();
        },
        
        async confirmarVinculacao() {
            if (this.usuariosSelecionados.length === 0) return;
            
            this.vinculando = true;
            try {
                const response = await fetch(`/api/administracao/usuarios-por-entidade/${this.entidadeSelecionada}/vincular`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        user_ids: this.usuariosSelecionados
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalVincular'));
                    modal.hide();
                    
                    this.mostrarToast('Sucesso', `${data.vinculados} usuário(s) vinculado(s) com sucesso!`, 'fa-check-circle text-success');
                    this.carregarUsuarios();
                } else {
                    throw new Error(data.error || 'Erro ao vincular usuários');
                }
                
            } catch (error) {
                console.error('Erro ao vincular:', error);
                this.mostrarToast('Erro', error.message, 'fa-exclamation-circle text-danger');
            } finally {
                this.vinculando = false;
            }
        },
        
        desvinculerUsuario(usuario) {
            this.usuarioParaDesvincular = usuario;
            const modal = new bootstrap.Modal(document.getElementById('modalConfirmacaoDesvinculacao'));
            modal.show();
        },
        
        async confirmarDesvinculacao() {
            if (!this.usuarioParaDesvincular) return;
            
            this.desvinculando = true;
            try {
                const response = await fetch(`/api/administracao/usuarios-por-entidade/${this.entidadeSelecionada}/desvincular/${this.usuarioParaDesvincular.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacaoDesvinculacao'));
                    modal.hide();
                    
                    this.mostrarToast('Sucesso', 'Usuário desvinculado com sucesso!', 'fa-check-circle text-success');
                    this.carregarUsuarios();
                } else {
                    throw new Error(data.error || 'Erro ao desvincular usuário');
                }
                
            } catch (error) {
                console.error('Erro ao desvincular:', error);
                this.mostrarToast('Erro', error.message, 'fa-exclamation-circle text-danger');
            } finally {
                this.desvinculando = false;
                this.usuarioParaDesvincular = null;
            }
        },
        
        async reativarUsuario(usuario) {
            try {
                const response = await fetch(`/api/administracao/usuarios-por-entidade/${this.entidadeSelecionada}/reativar/${usuario.id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.mostrarToast('Sucesso', 'Usuário reativado com sucesso!', 'fa-check-circle text-success');
                    this.carregarUsuarios();
                } else {
                    throw new Error(data.error || 'Erro ao reativar usuário');
                }
                
            } catch (error) {
                console.error('Erro ao reativar:', error);
                this.mostrarToast('Erro', error.message, 'fa-exclamation-circle text-danger');
            }
        },
        
        formatarData(data) {
            if (!data) return '';
            return new Date(data).toLocaleDateString('pt-BR');
        },
        
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
/* Reutiliza os mesmos estilos do componente anterior */
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

.row.g-3 {
    align-items: end;
}

.custom-modal-header {
    background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
    color: white;
    border-bottom: none;
    padding: 1.5rem;
    border-radius: 0.5rem 0.5rem 0 0;
}

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

.custom-modal-header .modal-title {
    color: white;
    font-weight: 600;
    font-size: 1.25rem;
}

.custom-modal-header .btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

.w-180px { width: 180px !important; }
.text-custom { color: #18578A !important; font-weight: 600; }

.badge-ativo {
    background-color: #d1edcc !important;
    color: #155724 !important;
    border: 1px solid #c3e6cb !important;
}

.badge-inativo {
    background-color: #f8d7da !important;
    color: #721c24 !important;
    border: 1px solid #f1aeb5 !important;
}

.paginacao-container {
    border-top: 1px solid #dee2e6;
    padding-top: 1rem;
}

.pagination-generic .page-link {
    border: 1px solid #dee2e6;
    color: #6c757d;
    padding: 0.5rem 0.75rem;
    margin: 0 0.125rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
}

.pagination-generic .page-item.active .page-link {
    background-color: #18578A;
    border-color: #18578A;
    color: white;
}

.pagination-generic .page-link:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    color: #495057;
}

/* Estilo específico para modal de confirmação */
.modal-confirmacao {
    border-radius: 0.75rem !important;
    overflow: hidden !important;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
}

.modal-confirmacao .modal-header {
    background: linear-gradient(135deg, #18578A 0%, #5EA853 100%) !important;
    color: white !important;
    border-bottom: none !important;
    padding: 1.5rem !important;
    border-radius: 0.5rem 0.5rem 0 0 !important;
}

.modal-confirmacao .header-icon {
    width: 40px !important;
    height: 40px !important;
    background-color: rgba(255, 255, 255, 0.2) !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-right: 1rem !important;
    font-size: 1.2rem !important;
    color: white !important;
}

.modal-confirmacao .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%) !important;
}

.modal-confirmacao .modal-body {
    background: white !important;
    padding: 1.25rem 1.25rem 0 1.25rem !important;
    text-align: center !important;
}

.modal-confirmacao .confirm-text {
    color: #3E4653 !important;
    font-size: 1.1rem !important;
    margin: 0 0 1rem 0 !important;
    line-height: 1.6 !important;
}

.modal-confirmacao .target-entity {
    color: #2E77D0 !important;
    font-weight: 700 !important;
    font-size: 1.25rem !important;
    display: block !important;
    margin: 0.5rem 0 1.5rem 0 !important;
}

.modal-confirmacao .irreversible {
    background: #FFF5F5 !important;
    border: 1px solid #FAD4D4 !important;
    color: #5f6b7a !important;
    border-radius: 0.75rem !important;
    padding: 0.75rem 0.9rem !important;
    margin: 0 0 2rem 0 !important;
    display: flex !important;
    gap: 0.5rem !important;
    align-items: flex-start !important;
    text-align: left !important;
}

.modal-confirmacao .irreversible i {
    color: #007bff !important;
    font-size: 1.2rem !important;
    flex-shrink: 0 !important;
}

.modal-confirmacao .irreversible span {
    color: #5f6b7a !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
}

.modal-confirmacao .modal-footer {
    background: transparent !important;
    border: none !important;
    padding: 1.5rem 1.25rem 1.25rem 1.25rem !important;
    gap: 0.6rem !important;
    justify-content: center !important;
}

.modal-confirmacao .btn {
    border-radius: 0.375rem !important;
    font-weight: 500 !important;
    padding: 0.5rem 1rem !important;
    border: none !important;
    font-size: 0.875rem !important;
}
</style>
