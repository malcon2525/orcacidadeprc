<template>
    <div>
        <!-- Cabeçalho Compacto da Aba -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 text-custom">
                <i class="fas fa-sitemap me-2"></i>Tipos de Orçamento
            </h6>
            <div class="d-flex gap-2">
                <!-- Botão Filtros -->
                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" @click="toggleFiltros" :class="{ 'active': filtrosVisiveis }">
                    <i class="fas fa-filter"></i>
                    <span>Filtros</span>
                    <i class="fas" :class="filtrosVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <!-- Botão Novo Tipo -->
                <button class="btn btn-outline-success d-flex align-items-center gap-2 px-3 py-2" @click="abrirModalCriar">
                    <i class="fas fa-plus"></i>
                    <span>Novo Tipo</span>
                </button>
            </div>
        </div>
        
        <!-- Filtros da Aba (Compactos) -->
        <div class="filtros-aba-container mb-3" v-if="filtrosVisiveis">
            <div class="filtros-aba-content" :class="{ 'show': filtrosVisiveis }">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="filtroDescricao" v-model="filtros.descricao" @input="filtrarDados" placeholder="Filtrar por descrição">
                            <label for="filtroDescricao">Filtrar por descrição</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-select" id="filtroStatus" v-model="filtros.ativo" @change="filtrarDados">
                                <option value="">Todos os status</option>
                                <option value="1">Ativo</option>
                                <option value="0">Inativo</option>
                            </select>
                            <label for="filtroStatus">Status</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela -->
        <div class="table-responsive" v-if="registros.data.length > 0">
            <table class="table table-hover align-middle usuarios-table">
                <thead>
                    <tr>
                        <th class="fw-semibold text-custom">Descrição</th>
                        <th class="fw-semibold text-custom">Versão</th>
                        <th class="fw-semibold text-custom">Status</th>
                        <th class="fw-semibold text-end text-custom">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in registros.data" :key="item.id" class="usuario-row">
                        <td>{{ item.descricao }}</td>
                        <td>{{ item.versao }}</td>
                        <td class="text-center">
                            <span class="badge badge-status" :class="item.ativo ? 'badge-ativo' : 'badge-inativo'">
                                <i class="fas" :class="item.ativo ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                {{ item.ativo ? 'ATIVO' : 'INATIVO' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <button class="btn btn-sm btn-warning" @click="editarItem(item)" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" @click="excluirItem(item)" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Estado Vazio -->
        <div v-if="!loading && registros.data.length === 0" class="text-center py-5">
            <i class="fas fa-sitemap text-muted mb-3" style="font-size: 3rem;"></i>
            <h6 class="text-muted mb-2">Nenhum tipo de orçamento encontrado</h6>
            <p class="text-muted small mb-0">Tente ajustar os filtros ou criar um novo tipo.</p>
        </div>

        <!-- Modal para Criar/Editar Tipo de Orçamento -->
        <div class="modal fade" id="modalTipoOrcamento" tabindex="-1" aria-labelledby="modalTipoOrcamentoLabel" aria-hidden="true" ref="modalTipoOrcamento">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
                            </div>
                            <h5 class="modal-title mb-0" id="modalTipoOrcamentoLabel">
                                {{ modoEdicao ? 'Editar Tipo de Orçamento' : 'Novo Tipo de Orçamento' }}
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <form @submit.prevent="salvarTipoOrcamento">
                        <div class="modal-body">
                            <div class="row g-3">
                                <!-- Descrição -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="descricao" 
                                            v-model="form.descricao"
                                            :class="{ 'is-invalid': errors.descricao }"
                                            placeholder="Descrição"
                                            required
                                        >
                                        <label for="descricao">Descrição *</label>
                                        <div class="invalid-feedback" v-if="errors.descricao">
                                            {{ errors.descricao }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Versão -->
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input 
                                            type="number" 
                                            class="form-control" 
                                            id="versao" 
                                            v-model="form.versao"
                                            :class="{ 'is-invalid': errors.versao }"
                                            placeholder="Versão"
                                            min="1"
                                            required
                                        >
                                        <label for="versao">Versão *</label>
                                        <div class="invalid-feedback" v-if="errors.versao">
                                            {{ errors.versao }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <select 
                                            class="form-select" 
                                            id="ativo" 
                                            v-model="form.ativo"
                                            :class="{ 'is-invalid': errors.ativo }"
                                            required
                                        >
                                            <option :value="true">Ativo</option>
                                            <option :value="false">Inativo</option>
                                        </select>
                                        <label for="ativo">Status *</label>
                                        <div class="invalid-feedback" v-if="errors.ativo">
                                            {{ errors.ativo }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-success" :disabled="loading">
                                <i class="fas fa-spinner fa-spin me-2" v-if="loading"></i>
                                <i class="fas fa-save me-2" v-else></i>
                                {{ loading ? 'Salvando...' : 'Salvar Tipo de Orçamento' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação de Exclusão -->
        <div class="modal fade" id="modalConfirmacaoExclusao" tabindex="-1" aria-labelledby="modalConfirmacaoExclusaoLabel" aria-hidden="true" ref="modalConfirmacaoExclusao">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h5 class="modal-title mb-0" id="modalConfirmacaoExclusaoLabel">
                                Confirmar Exclusão
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <p class="mb-0">Tem certeza que deseja excluir o tipo de orçamento <strong>"{{ itemParaExcluir?.descricao }}"</strong> (Versão {{ itemParaExcluir?.versao }})?</p>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" @click="confirmarExclusao" :disabled="excluindo">
                            <i class="fas fa-trash me-2" v-if="!excluindo"></i>
                            <span class="spinner-border spinner-border-sm me-2" v-if="excluindo" role="status"></span>
                            {{ excluindo ? 'Excluindo...' : 'Excluir' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast para notificações -->
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
import { Modal, Toast } from 'bootstrap';
import axios from 'axios';

export default {
    name: 'ListaTipoOrcamento',
    data() {
        return {
            registros: {
                data: [],
                from: 0,
                to: 0,
                total: 0,
                current_page: 1,
                last_page: 1,
            },
            form: {
                id: null,
                descricao: '',
                versao: 1,
                ativo: true,
            },
            errors: {},
            filtros: {
                descricao: '',
                ativo: '',
            },
            filtrosVisiveis: false,
            modoEdicao: false,
            loading: false,
            excluindo: false,
            itemParaExcluir: null,
            modalTipoOrcamento: null,
            modalConfirmacaoExclusao: null,
            toast: null,
            toastTitle: '',
            toastMessage: '',
            toastIcon: '',
        };
    },
    mounted() {
        this.carregarDados();
        this.modalTipoOrcamento = new Modal(document.getElementById('modalTipoOrcamento'));
        this.modalConfirmacaoExclusao = new Modal(document.getElementById('modalConfirmacaoExclusao'));
        this.toast = new Toast(document.getElementById('toast'));
    },
    computed: {
        paginas() {
            const pages = [];
            const maxPages = 5;
            let start = Math.max(1, this.registros.current_page - Math.floor(maxPages / 2));
            let end = Math.min(this.registros.last_page, start + maxPages - 1);

            if (end - start + 1 < maxPages) {
                start = Math.max(1, end - maxPages + 1);
            }

            for (let i = start; i <= end; i++) {
                pages.push(i);
            }

            return pages;
        }
    },
    methods: {
        async carregarDados() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.filtros.descricao) params.append('descricao', this.filtros.descricao);
                if (this.filtros.ativo !== '') params.append('ativo', this.filtros.ativo);
                params.append('page', this.registros.current_page);

                const response = await axios.get(`/api/administracao/estrutura-orcamento/tipo-orcamento?${params.toString()}`);
                this.registros = response.data;
                
                // Emitir dados de paginação para o componente pai
                this.$emit('paginacao-updated', {
                    hasData: this.registros.data.length > 0,
                    data: this.registros, // Mudança: paginacao -> data
                    tipo: 'tipos-orcamento'
                });
            } catch (error) {
                this.mostrarToast('Erro', 'Não foi possível carregar os dados', 'fa-exclamation-circle text-danger');
                
                this.$emit('paginacao-updated', {
                    hasData: false,
                    data: null, // Mudança: paginacao -> data
                    tipo: 'tipos-orcamento'
                });
            } finally {
                this.loading = false;
            }
        },
        mudarPagina(page) {
            if (page >= 1 && page <= this.registros.last_page) {
                this.registros.current_page = page;
                this.carregarDados();
            }
        },
        mudarPaginaExterna(page) {
            this.mudarPagina(page);
        },
        filtrarDados() {
            this.registros.current_page = 1;
            this.carregarDados();
        },
        toggleFiltros() {
            this.filtrosVisiveis = !this.filtrosVisiveis;
        },
        abrirModalCriar() {
            this.modoEdicao = false;
            this.limparFormulario();
            this.modalTipoOrcamento.show();
        },
        editarItem(item) {
            this.modoEdicao = true;
            this.form = { ...item };
            this.modalTipoOrcamento.show();
        },
        limparFormulario() {
            this.form = {
                id: null,
                descricao: '',
                versao: 1,
                ativo: true,
            };
            this.errors = {};
        },
        async salvarTipoOrcamento() {
            this.loading = true;
            this.errors = {};

            try {
                if (this.modoEdicao) {
                    await axios.put(`/api/administracao/estrutura-orcamento/tipo-orcamento/${this.form.id}`, this.form);
                    this.mostrarToast('Sucesso', 'Tipo de orçamento atualizado com sucesso!', 'fa-check-circle text-success');
                } else {
                    await axios.post('/api/administracao/estrutura-orcamento/tipo-orcamento', this.form);
                    this.mostrarToast('Sucesso', 'Tipo de orçamento criado com sucesso!', 'fa-check-circle text-success');
                }
                
                this.modalTipoOrcamento.hide();
                this.carregarDados();
                
                // Emitir evento para sincronizar outras abas
                this.$emit('tipo-orcamento-atualizado');
                
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    this.mostrarToast('Erro', error.response?.data?.message || 'Erro ao salvar', 'fa-exclamation-circle text-danger');
                }
            } finally {
                this.loading = false;
            }
        },
        excluirItem(item) {
            this.itemParaExcluir = item;
            this.modalConfirmacaoExclusao.show();
        },
        async confirmarExclusao() {
            if (!this.itemParaExcluir) return;
            
            this.excluindo = true;
            try {
                await axios.delete(`/api/administracao/estrutura-orcamento/tipo-orcamento/${this.itemParaExcluir.id}`);
                
                this.modalConfirmacaoExclusao.hide();
                this.mostrarToast('Sucesso', 'Tipo de orçamento excluído com sucesso!', 'fa-check-circle text-success');
                this.carregarDados();
                
                // Emitir evento para sincronizar outras abas
                this.$emit('tipo-orcamento-atualizado');
                
            } catch (error) {
                this.mostrarToast('Erro', error.response?.data?.message || 'Erro ao excluir', 'fa-exclamation-circle text-danger');
            } finally {
                this.excluindo = false;
                this.itemParaExcluir = null;
            }
        },
        mostrarToast(title, message, icon) {
            this.toastTitle = title;
            this.toastMessage = message;
            this.toastIcon = icon;
            this.toast.show();
        },
    },
};
</script>

<style scoped>
/* ===== MODAL GERENCIAR USUÁRIOS - VERSÃO SIMPLIFICADA ===== */
.modal-xl {
    max-width: 90% !important;
    width: 90% !important;
}

/* Layout simples em duas colunas */
.d-flex.flex-wrap {
    gap: 1rem;
}

.coluna-usuarios {
    flex: 1;
    min-width: 300px;
}

/* Responsividade */
@media (max-width: 768px) {
    .coluna-usuarios {
        flex: 100%;
        min-width: auto;
    }
}

/* Avatar simples */
.user-avatar {
    width: 32px;
    height: 32px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 600;
}

/* ===== MODAIS DE PERMISSÕES ===== */

/* Layout para modais de permissões */
.coluna-papeis {
    flex: 1;
    min-width: 300px;
}

/* Layout para modais de gerenciar permissões do papel */
.coluna-permissoes {
    flex: 1;
    min-width: 300px;
}

/* Responsividade para modais de permissões */
@media (max-width: 768px) {
    .coluna-papeis {
        flex: 100%;
        min-width: auto;
    }
    
    .coluna-permissoes {
        flex: 100%;
        min-width: auto;
    }
}

/* Estilos para cards de permissões */
.card-header h6 {
    font-size: 1.2rem;
    font-weight: 600;
    padding-top: 5px;
    padding-bottom: 5px;
}

/* Estilos específicos para modais de permissões - não afetar outros card-body */
#modalDetalhesPermissao .modal-body .card-body,
#modalGerenciarPapeisPermissao .modal-body .card-body {
    max-height: 400px;
    overflow-y: auto;
}

/* Estilos para badges de status - Usando CSS global */

/* Estilos para código inline */
code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    color: #e83e8c;
}

/* ===== FILTROS DO MODAL GERENCIAR USUÁRIOS ===== */
.input-group-sm .form-control {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.input-group-sm .input-group-text {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #6c757d;
}

/* ===== CAMPOS TEXTAREA - ALTURA HARMÔNICA COM OUTROS CAMPOS ===== */
.form-floating textarea {
    min-height: 58px !important;
    height: 58px !important;
    resize: vertical;
    line-height: 1.5 !important;
    padding: 1.625rem 0.75rem 0.625rem 0.75rem !important;
    font-size: 14px !important;
    overflow-y: auto !important;
}

.form-floating textarea:focus {
    min-height: 58px !important;
    height: 58px !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Garantir que o label não sobreponha o conteúdo */
.form-floating textarea:not(:placeholder-shown) ~ label,
.form-floating textarea:focus ~ label {
    transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
    color: #6c757d;
    pointer-events: none;
}

/* Ajuste específico para campos de descrição - ALTURA HARMÔNICA */
#descricaoPapel,
#descricaoPermissao {
    min-height: 58px !important;
    height: 100px !important;
    max-height: 200px !important;
    overflow-y: auto !important;
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
    line-height: 1.5 !important;
}

/* Forçar altura mínima em todos os estados */
#descricaoPapel:focus,
#descricaoPermissao:focus,
#descricaoPapel:not(:placeholder-shown),
#descricaoPermissao:not(:placeholder-shown) {
    min-height: 58px !important;
    height: 100px !important;
}
</style>
