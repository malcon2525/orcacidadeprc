<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho Compacto -->
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold" style="color: #5EA853;">
                    <i class="fas fa-sitemap me-2"></i>Gestão de Estrutura de Orçamentos
                </h6>
            </div>

            <!-- Corpo -->
            <div class="card-body">
                <!-- Sistema de Abas -->
                <div class="admin-tabs-container">
                    <!-- Navegação das Abas -->
                    <ul class="nav nav-tabs admin-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button 
                                class="nav-link admin-tab" 
                                :class="{ active: activeTab === 'tipos' }"
                                type="button"
                                role="tab"
                                @click="changeTab('tipos')"
                            >
                                <i class="fas fa-list me-2"></i>
                                Tipo de Orçamento
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button 
                                class="nav-link admin-tab" 
                                :class="{ active: activeTab === 'estrutura' }"
                                type="button"
                                role="tab"
                                @click="changeTab('estrutura')"
                            >
                                <i class="fas fa-sitemap me-2"></i>
                                Estrutura de Orçamento
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button 
                                class="nav-link admin-tab" 
                                :class="{ active: activeTab === 'visualizacao' }"
                                type="button"
                                role="tab"
                                @click="changeTab('visualizacao')"
                            >
                                <i class="fas fa-eye me-2"></i>
                                Visualização Integrada
                            </button>
                        </li>
                    </ul>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content admin-tab-content">
                        <!-- Aba Tipo de Orçamento -->
                        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'tipos' }" role="tabpanel">
                            <ListaTipoOrcamento 
                                ref="listaTipoOrcamento"
                                @paginacao-updated="onPaginacaoUpdated"
                                @tipo-orcamento-atualizado="onTipoOrcamentoAtualizado"
                            />
                        </div>
                        
                        <!-- Aba Estrutura de Orçamento -->
                        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'estrutura' }" role="tabpanel">
                            <EstruturaOrcamento 
                                ref="estruturaOrcamento"
                                @paginacao-updated="onPaginacaoUpdated"
                            />
                        </div>
                        
                        <!-- Aba Visualização Integrada -->
                        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'visualizacao' }" role="tabpanel">
                            <VisualizacaoIntegrada 
                                ref="visualizacaoIntegrada"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paginação - FORA DO CARD PRINCIPAL (seguindo o padrão) -->
        <!-- Só exibe paginação nas abas que precisam (tipos e visualizacao) -->
        <div v-if="paginacao.hasData && paginacao.data && activeTab !== 'estrutura'" class="mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Informações de Registros -->
                <div class="text-muted fw-medium">
                    Mostrando {{ paginacao.data.from }} até {{ paginacao.data.to }} de {{ paginacao.data.total }} registros
                </div>
                
                <!-- Navegação -->
                <nav v-if="paginacao.data.last_page > 1">
                    <ul class="pagination admin-pagination mb-0">
                        <!-- Botão Anterior -->
                        <li class="page-item" :class="{ disabled: paginacao.data.current_page === 1 }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(paginacao.data.current_page - 1)" aria-label="Anterior">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        
                        <!-- Páginas -->
                        <li v-for="page in paginasVisiveis" 
                            :key="page" 
                            class="page-item" 
                            :class="{ active: page === paginacao.data.current_page }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(page)">
                                {{ page }}
                            </a>
                        </li>
                        
                        <!-- Botão Próximo -->
                        <li class="page-item" :class="{ disabled: paginacao.data.current_page === paginacao.data.last_page }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(paginacao.data.current_page + 1)" aria-label="Próximo">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Modais serão implementados na Fase 2 -->
    </div>
</template>

<script>
import ListaTipoOrcamento from './ListaTipoOrcamento.vue';
import EstruturaOrcamento from './EstruturaOrcamento.vue';
import VisualizacaoIntegrada from './VisualizacaoIntegrada.vue';

export default {
    name: 'GestaoEstruturaOrcamento',
    components: {
        ListaTipoOrcamento,
        EstruturaOrcamento,
        VisualizacaoIntegrada
    },
    data() {
        return {
            activeTab: 'tipos',
            
            // Dados de paginação centralizada
            paginacao: {
                hasData: false,
                data: null,
                tipo: null
            },
        };
    },
    computed: {
        // Computar páginas visíveis para a paginação
        paginasVisiveis() {
            if (!this.paginacao.data || !this.paginacao.data.last_page) return [];
            
            const pages = [];
            const maxPages = 5;
            let start = Math.max(1, this.paginacao.data.current_page - Math.floor(maxPages / 2));
            let end = Math.min(this.paginacao.data.last_page, start + maxPages - 1);

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
        changeTab(tab) {
            this.activeTab = tab;
            // Resetar paginação ao trocar de aba
            this.paginacao = {
                hasData: false,
                data: null,
                tipo: null
            };
        },
        
        // Receber dados de paginação dos componentes filhos
        onPaginacaoUpdated(paginacaoData) {
            this.paginacao = paginacaoData;
        },
        
        // Receber notificação de atualização de tipo de orçamento
        onTipoOrcamentoAtualizado() {
            // Sincronizar dados nas outras abas
            this.sincronizarAbas();
        },
        
        // Sincronizar dados em todas as abas
        sincronizarAbas() {
            // Aba 2: Estrutura de Orçamento
            if (this.$refs.estruturaOrcamento) {
                this.$refs.estruturaOrcamento.recarregarTiposOrcamento();
            }
            
            // Aba 3: Visualização Integrada
            if (this.$refs.visualizacaoIntegrada) {
                this.$refs.visualizacaoIntegrada.recarregarTiposOrcamento();
            }
        },
        
        // Mudar página (comunica com o componente filho ativo)
        mudarPagina(page) {
            if (this.activeTab === 'tipos' && this.$refs.listaTipoOrcamento) {
                this.$refs.listaTipoOrcamento.mudarPaginaExterna(page);
            }
            // Estrutura de Orçamento não tem paginação (estrutura hierárquica)
            // Visualização Integrada não tem paginação
        },
    },
    mounted() {
        // Inicializar com a aba tipos ativa
        this.changeTab('tipos');
    }
};
</script>

<style>
/* Estilos específicos para este componente */
</style>
