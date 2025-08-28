<template>
    <div class="composicao-propria-container">
        <!-- Botão Nova Composição -->
        <div class="d-flex justify-content-end mb-4">
            <button v-if="permissoes.crud" class="btn btn-success" @click="abrirModalNovaComposicao">
                <i class="fas fa-plus me-2"></i>Nova Composição
            </button>
        </div>

        <!-- Tabela de Composições -->
        <div class="table-responsive">
            <table class="table table-hover align-middle composicoes-table">
                <thead>
                    <tr>
                        <th class="fw-semibold text-custom">Código</th>
                        <th class="fw-semibold text-custom">Descrição</th>
                        <th class="fw-semibold text-custom">Unidade</th>
                        <th class="fw-semibold text-custom text-end">Valor Total</th>
                        <th class="fw-semibold text-custom text-center">Itens</th>
                        <th class="fw-semibold text-custom text-end w-180px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="composicao in composicoes" :key="composicao.id" class="composicao-row">
                        <td class="align-middle">
                            <div class="fw-medium">{{ composicao.codigo || '-' }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="fw-medium">{{ composicao.descricao }}</div>
                        </td>
                        <td class="align-middle">
                            <span class="badge badge-status badge-ativo">{{ composicao.unidade }}</span>
                        </td>
                        <td class="align-middle text-end">
                            <div class="fw-semibold">R$ {{ formatarValor(composicao.valor_total_geral) }}</div>
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge badge-status badge-ativo">{{ composicao.total_itens }}</span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <!-- <button class="btn btn-sm btn-info" @click="visualizarComposicao(composicao)" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </button> -->
                                <button v-if="permissoes.crud" class="btn btn-sm btn-warning" @click="editarComposicao(composicao)" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button v-if="permissoes.crud" class="btn btn-sm btn-danger" @click="excluirComposicao(composicao)" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Estado Vazio -->
        <div v-if="composicoes.length === 0 && !carregando" class="text-center py-5">
            <div class="empty-state">
                <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma composição encontrada</h5>
                <p class="text-muted">Clique no botão "Nova" para criar sua primeira composição própria</p>
            </div>
        </div>

        <!-- Paginação -->
        <div v-if="composicoes.length > 0" class="paginacao-container mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted fw-medium">
                    Mostrando {{ (paginaAtual - 1) * itensPorPagina + 1 }} até {{ Math.min(paginaAtual * itensPorPagina, totalRegistros) }} de {{ totalRegistros }} registros
                </div>
                
                <nav v-if="totalPaginas > 1">
                    <ul class="pagination pagination-generic mb-0">
                        <li class="page-item" :class="{ disabled: paginaAtual === 1 }">
                            <button class="page-link" @click="mudarPagina(paginaAtual - 1)" :disabled="paginaAtual === 1">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        </li>
                        
                        <li v-for="pagina in paginasVisiveis" :key="pagina" class="page-item" :class="{ active: pagina === paginaAtual }">
                            <button class="page-link" @click="mudarPagina(pagina)">{{ pagina }}</button>
                        </li>
                        
                        <li class="page-item" :class="{ disabled: paginaAtual === totalPaginas }">
                            <button class="page-link" @click="mudarPagina(paginaAtual + 1)" :disabled="paginaAtual === totalPaginas">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Modal de Composição -->
        <composicao-propria-form 
            v-if="modalComposicao"
            ref="composicaoForm"
            :composicao="composicaoSelecionada"
            :permissoes="permissoes"
            @fechar="fecharModalComposicao"
            @salvar="salvarComposicao"
            @atualizar="atualizarComposicao">
        </composicao-propria-form>

        <!-- Modal de Confirmação de Exclusão -->
        <div class="modal fade modal-confirmacao" id="modalConfirmacaoExclusao">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h5 class="modal-title mb-0">Confirmar Exclusão</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="confirm-text">Tem certeza que deseja excluir a composição</p>
                        <p class="target-entity">"{{ composicaoParaExcluir?.descricao }}"</p>
                        <div class="irreversible">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span>Esta ação é permanente e não poderá ser desfeita.</span>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" @click="confirmarExclusao" :disabled="excluindo">
                            <span v-if="excluindo" class="spinner-border spinner-border-sm me-2"></span>
                            <i v-else class="fas fa-trash me-2"></i>
                            {{ excluindo ? 'Excluindo...' : 'Excluir' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast de Notificação -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div v-for="toast in toasts" :key="toast.id" 
                 class="toast align-items-center text-white bg-{{ toast.tipo }} border-0" 
                 role="alert" 
                 aria-live="assertive" 
                 aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i :class="toast.icone" class="me-2"></i>
                        {{ toast.mensagem }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="removerToast(toast.id)"></button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ComposicaoPropriaForm from './ComposicaoPropriaForm.vue';

export default {
    name: 'ListaComposicaoPropria',
    components: {
        ComposicaoPropriaForm
    },
    props: {
        permissoes: {
            type: Object,
            required: true,
            default: () => ({
                crud: false,
                consultar: false
            })
        }
    },
    data() {
        return {
            composicoes: [],
            carregando: false,

            paginaAtual: 1,
            itensPorPagina: 10,
            totalRegistros: 0,
            totalPaginas: 0,
            modalComposicao: false,
            composicaoSelecionada: null,
            composicaoParaExcluir: null,
            excluindo: false,
            toasts: [],
            toastId: 0
        };
    },
    computed: {
        paginasVisiveis() {
            const paginas = [];
            const inicio = Math.max(1, this.paginaAtual - 2);
            const fim = Math.min(this.totalPaginas, this.paginaAtual + 2);
            
            for (let i = inicio; i <= fim; i++) {
                paginas.push(i);
            }
            
            return paginas;
        }
    },
    mounted() {
        this.carregarComposicoes();
    },
    methods: {
        async carregarComposicoes() {
            this.carregando = true;
            try {
                const response = await fetch('/api/orcamento/composicao-propria', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    this.composicoes = data.data;
                    this.totalRegistros = data.total;
                    this.totalPaginas = data.last_page;
                    this.paginaAtual = data.current_page;
                } else {
                    this.mostrarToast('Erro ao carregar composições', 'error');
                }
            } catch (error) {
                console.error('Erro ao carregar composições:', error);
                this.mostrarToast('Erro ao carregar composições', 'error');
            } finally {
                this.carregando = false;
            }
        },

        mudarPagina(pagina) {
            if (pagina >= 1 && pagina <= this.totalPaginas) {
                this.paginaAtual = pagina;
                this.carregarComposicoes();
            }
        },

        abrirModalNovaComposicao() {
            this.composicaoSelecionada = null;
            this.modalComposicao = true;
        },

        editarComposicao(composicao) {
            this.composicaoSelecionada = { ...composicao };
            this.modalComposicao = true;
        },

        visualizarComposicao(composicao) {
            this.composicaoSelecionada = { ...composicao };
            this.modalComposicao = true;
        },

        fecharModalComposicao() {
            this.modalComposicao = false;
            this.composicaoSelecionada = null;
        },

        async salvarComposicao(composicao) {
            try {
                //console.log('Dados sendo enviados:', JSON.stringify(composicao, null, 2)); // Debug detalhado
                
                const response = await fetch('/api/orcamento/composicao-propria', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(composicao)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('Erro de validação:', errorData);
                    
                    if (response.status === 422 && errorData.errors) {
                        // Erro de validação - repassar para o componente filho
                        this.$refs.composicaoForm?.mostrarErrosValidacao(errorData.errors);
                        return;
                    }
                    
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    this.mostrarToast('Composição criada com sucesso!', 'success');
                    this.fecharModalComposicao();
                    this.carregarComposicoes();
                } else {
                    this.mostrarToast(data.message || 'Erro ao criar composição', 'error');
                }
            } catch (error) {
                console.error('Erro ao criar composição:', error);
                this.mostrarToast('Erro ao criar composição', 'error');
            }
        },

        async atualizarComposicao(composicao) {
            try {
                const response = await fetch(`/api/orcamento/composicao-propria/${composicao.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(composicao)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    this.mostrarToast('Composição atualizada com sucesso!', 'success');
                    this.fecharModalComposicao();
                    this.carregarComposicoes();
                } else {
                    this.mostrarToast(data.message || 'Erro ao atualizar composição', 'error');
                }
            } catch (error) {
                console.error('Erro ao atualizar composição:', error);
                this.mostrarToast('Erro ao atualizar composição', 'error');
            }
        },

        excluirComposicao(composicao) {
            this.composicaoParaExcluir = composicao;
            const modal = new bootstrap.Modal(document.getElementById('modalConfirmacaoExclusao'));
            modal.show();
        },

        async confirmarExclusao() {
            if (!this.composicaoParaExcluir) return;

            this.excluindo = true;
            try {
                const response = await fetch(`/api/orcamento/composicao-propria/${this.composicaoParaExcluir.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    this.mostrarToast('Composição excluída com sucesso!', 'success');
                    this.carregarComposicoes();
                    
                    // Fechar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacaoExclusao'));
                    if (modal) modal.hide();
                } else {
                    this.mostrarToast(data.message || 'Erro ao excluir composição', 'error');
                }
            } catch (error) {
                console.error('Erro ao excluir composição:', error);
                this.mostrarToast('Erro ao excluir composição', 'error');
            } finally {
                this.excluindo = false;
            }
        },

        formatarValor(valor) {
            return parseFloat(valor).toFixed(2).replace('.', ',');
        },

        mostrarToast(mensagem, tipo = 'info') {
            const toast = {
                id: ++this.toastId,
                mensagem,
                tipo,
                icone: this.getIconeToast(tipo)
            };

            this.toasts.push(toast);

            // Auto-remover após 5 segundos
            setTimeout(() => {
                this.removerToast(toast.id);
            }, 5000);
        },

        getIconeToast(tipo) {
            const icones = {
                success: 'fas fa-check-circle',
                error: 'fas fa-exclamation-circle',
                warning: 'fas fa-exclamation-triangle',
                info: 'fas fa-info-circle'
            };
            return icones[tipo] || icones.info;
        },

        removerToast(id) {
            this.toasts = this.toasts.filter(toast => toast.id !== id);
        }
    }
};
</script>

<style scoped>
.composicao-propria-container {
    min-height: 400px;
}

.composicoes-table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.composicao-row:hover {
    background-color: #f8f9fa;
}

.empty-state {
    color: #6c757d;
}

.toast-container {
    z-index: 1055;
}

.badge-status {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-weight: 500;
}

.badge-ativo {
    background-color: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
}
</style>
