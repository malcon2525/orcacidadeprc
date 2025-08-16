<template>
    <div>
        <!-- Cabeçalho Integrado com Seletor -->
        <div class="header-integrado">
            <div class="header-content">
                <div class="titulo-container">
                    <h5 class="text-custom mb-0">
                        <i class="fas fa-eye me-2"></i>Visualização da Estrutura
                    </h5>
                    <p class="text-muted mt-2 mb-0">Visualize a estrutura de orçamento de forma clara e organizada</p>
                </div>
                
                <div class="seletor-container">
                    <div class="d-flex align-items-end gap-3">
                        <div class="tipo-orcamento-selector">
                            <div class="form-floating">
                                <select class="form-control" 
                                        id="tipoOrcamentoSelector" 
                                        v-model="tipoOrcamentoSelecionado"
                                        @change="onTipoOrcamentoChange"
                                        :class="{ 'is-invalid': !tipoOrcamentoSelecionado }">
                                    <option value="">Selecione o tipo de orçamento...</option>
                                    <option v-for="tipo in tiposOrcamento" 
                                            :key="tipo.id" 
                                            :value="tipo.id">
                                        {{ tipo.descricao }} - Versão {{ tipo.versao }}
                                    </option>
                                </select>
                                <label for="tipoOrcamentoSelector">
                                    <i class="fas fa-chart-pie me-2"></i>Tipo de Orçamento
                                </label>
                            </div>
                        </div>
                        
                        <!-- Botão Toggle de Visualização -->
                        <div class="toggle-container" v-if="tipoOrcamentoSelecionado && grandesItens.length > 0">
                            <button class="btn btn-outline-secondary" @click="toggleVisualizacao" :disabled="loading">
                                <i class="fas" :class="mostrarArvoreCompleta ? 'fa-compress-alt' : 'fa-expand-alt'"></i>
                                {{ mostrarArvoreCompleta ? 'Apenas Grandes Itens' : 'Árvore Completa' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conteúdo da Visualização -->
        <div v-if="tipoOrcamentoSelecionado" class="card">
            <div class="card-body p-0">
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-custom" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando estrutura...</p>
                </div>
                
                <!-- Estrutura Vazia -->
                <div v-else-if="grandesItens.length === 0" class="text-center py-5">
                    <i class="fas fa-sitemap text-muted mb-3" style="font-size: 3rem;"></i>
                    <h6 class="text-muted mb-2">Nenhuma estrutura encontrada</h6>
                    <p class="text-muted mb-0">Este tipo de orçamento ainda não possui estrutura definida</p>
                </div>
                
                <!-- Estrutura -->
                <div v-else class="estrutura-visualizacao">
                                         <!-- Cabeçalho da Estrutura -->
                     <div class="estrutura-header">
                         <h4 class="estrutura-titulo">
                             <i class="fas fa-sitemap text-custom me-2"></i>
                             {{ tipoOrcamentoAtual?.descricao }} - Versão {{ tipoOrcamentoAtual?.versao }}
                         </h4>
                        <div class="estrutura-info">
                            <span class="badge badge-status badge-ativo me-2">{{ grandesItens.length }} Grande(s) Item(ns)</span>
                            <span class="badge badge-tipo badge-local">{{ totalSubgrupos }} Subgrupo(s)</span>
                        </div>
                    </div>
                    
                                         <!-- Árvore da Estrutura -->
                     <div class="estrutura-arvore">
                         <div v-for="grandeItem in grandesItens" :key="grandeItem.id" class="estrutura-item">
                            <!-- Grande Item -->
                            <div class="grande-item">
                                <span class="item-numero">{{ grandeItem.ordem }}.</span>
                                <span class="item-descricao">{{ grandeItem.descricao }}</span>
                                <span class="item-contador" v-if="mostrarArvoreCompleta">
                                    ({{ grandeItem.sub_grupos?.length || 0 }} subgrupos)
                                </span>
                            </div>
                            
                            <!-- Subgrupos (quando mostrarArvoreCompleta = true) -->
                            <div v-if="mostrarArvoreCompleta && grandeItem.sub_grupos && grandeItem.sub_grupos.length > 0" 
                                 class="subgrupos-container">
                                <div v-for="subgrupo in grandeItem.sub_grupos" :key="subgrupo.id" class="subgrupo-item">
                                    <span class="subgrupo-linha">│</span>
                                    <span class="subgrupo-linha">├──</span>
                                    <span class="subgrupo-numero">{{ subgrupo.ordem }}.</span>
                                    <span class="subgrupo-descricao">{{ subgrupo.descricao }}</span>
                                </div>
                                <!-- Linha de fechamento do último subgrupo -->
                                <div class="subgrupo-item" v-if="grandeItem.sub_grupos && grandeItem.sub_grupos.length > 0">
                                    <span class="subgrupo-linha">│</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'VisualizacaoIntegrada',
    
    data() {
        return {
            // Dados
            tiposOrcamento: [],
            tipoOrcamentoSelecionado: '',
            tipoOrcamentoAtual: null,
            grandesItens: [],
            loading: false,
            
            // Estado da visualização
            mostrarArvoreCompleta: false
        }
    },
    
    computed: {
        // Calcula o total de subgrupos
        totalSubgrupos() {
            return this.grandesItens.reduce((total, item) => {
                return total + (item.sub_grupos ? item.sub_grupos.length : 0);
            }, 0);
        }
    },
    
    mounted() {
        this.carregarTiposOrcamento();
    },
    
    methods: {
        async carregarTiposOrcamento() {
            try {
                const response = await axios.get('/api/administracao/estrutura-orcamento/tipo-orcamento/');
                if (response.data && response.data.data) {
                    this.tiposOrcamento = response.data.data;
                }
            } catch (error) {
                console.error('Erro ao carregar tipos de orçamento:', error);
                this.mostrarToast('Erro ao carregar tipos de orçamento', 'error');
            }
        },
        
        async recarregarTiposOrcamento() {
            await this.carregarTiposOrcamento();
        },
        
        // Mudança no tipo de orçamento
        onTipoOrcamentoChange() {
            this.grandesItens = [];
            this.mostrarArvoreCompleta = false;
            
            if (this.tipoOrcamentoSelecionado) {
                this.tipoOrcamentoAtual = this.tiposOrcamento.find(t => t.id === this.tipoOrcamentoSelecionado);
                this.$nextTick(() => {
                    this.carregarDados();
                });
            }
        },
        
        async carregarDados() {
            if (!this.tipoOrcamentoSelecionado) return;
            
            this.loading = true;
            try {
                const response = await axios.get(`/api/administracao/estrutura-orcamento/grande-item/${this.tipoOrcamentoSelecionado}`);
                
                if (response.data && response.data.success) {
                    this.grandesItens = response.data.data;
                } else {
                    this.grandesItens = [];
                }
            } catch (error) {
                console.error('Erro ao carregar dados:', error);
                this.mostrarToast('Erro ao carregar estrutura de orçamento', 'error');
                this.grandesItens = [];
            } finally {
                this.loading = false;
            }
        },
        
        // Toggle entre visualizações
        toggleVisualizacao() {
            this.mostrarArvoreCompleta = !this.mostrarArvoreCompleta;
        },
        
        // Toast simples
        mostrarToast(mensagem, tipo) {
            const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
            const icon = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            const toast = document.createElement('div');
            toast.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                <i class="fas ${icon} me-2"></i>${mensagem}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 5000);
        }
    }
}
</script>

<style scoped>
/* CSS compartilhado movido para estrutura-orcamento-shared.css */

/* Container de toggle */
.toggle-container {
    display: flex;
    align-items: center;
    flex-shrink: 0;
}

/* Ajuste do seletor para alinhar com o botão */
.seletor-container .d-flex {
    align-items: flex-end;
}

.seletor-container .tipo-orcamento-selector {
    flex: 1;
    min-width: 400px;
}

/* ===== ESTRUTURA DE VISUALIZAÇÃO ===== */

.estrutura-visualizacao {
    padding: 2rem;
}

.estrutura-header {
    text-align: center;
    margin-bottom: 3rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid #e9ecef;
}

.estrutura-titulo {
    color: #18578A;
    font-weight: 600;
    margin-bottom: 1rem;
}

.estrutura-info .badge {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
}

/* Árvore da estrutura */
.estrutura-arvore {
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
    font-size: 14px;
    line-height: 1.6;
    color: #2c3e50;
}

.estrutura-item {
    margin-bottom: 1.5rem;
}

/* Grande Item */
.grande-item {
    display: flex;
    align-items: baseline;
    font-weight: 600;
    font-size: 16px;
    color: #18578A;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.item-numero {
    color: #5EA853;
    font-weight: 700;
    margin-right: 0.75rem;
    min-width: 2rem;
}

.item-descricao {
    flex: 1;
    color: #2c3e50;
}

.item-contador {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 400;
    margin-left: 1rem;
}

/* Subgrupos */
.subgrupos-container {
    margin-left: 2rem;
    margin-top: 0.5rem;
}

.subgrupo-item {
    display: flex;
    align-items: baseline;
    margin-bottom: 0.25rem;
    font-size: 14px;
    color: #495057;
}

.subgrupo-linha {
    color: #dee2e6;
    margin-right: 0.5rem;
    font-weight: 300;
}

.subgrupo-numero {
    color: #5EA853;
    font-weight: 600;
    margin-right: 0.5rem;
    min-width: 2rem;
}

.subgrupo-descricao {
    flex: 1;
    color: #495057;
}

/* Responsividade */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .tipo-orcamento-selector .form-floating {
        min-width: 280px;
    }
    
    .tipo-orcamento-selector .form-control {
        padding: 0.375rem 0.75rem;
        min-height: 58px;
        font-size: 0.875rem;
    }
    
    .estrutura-visualizacao {
        padding: 1rem;
    }
    
    .subgrupos-container {
        margin-left: 1rem;
    }
}
</style>
