<template>
    <div class="gestao-usuarios">
        <!-- Navegação de Abas -->
        <div class="admin-tabs-container">
            <ul class="nav admin-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link admin-tab" 
                            @click="changeTab(1)" 
                            :class="{ active: abaAtiva === 1 }"
                            type="button" 
                            role="tab">
                        <i class="fas fa-users me-2"></i>Usuários
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link admin-tab" 
                            @click="changeTab(2)" 
                            :class="{ active: abaAtiva === 2 }"
                            type="button" 
                            role="tab">
                        <i class="fas fa-shield-alt me-2"></i>Papéis
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link admin-tab" 
                            @click="changeTab(3)" 
                            :class="{ active: abaAtiva === 3 }"
                            type="button" 
                            role="tab">
                        <i class="fas fa-key me-2"></i>Permissões
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link admin-tab" 
                            @click="changeTab(4)" 
                            :class="{ active: abaAtiva === 4 }"
                            type="button" 
                            role="tab">
                        <i class="fas fa-search me-2"></i>Busca Global
                    </button>
                </li>
            </ul>
        </div>

        <!-- Conteúdo das Abas -->
        <div class="admin-tab-content">
            <!-- Aba 1: Usuários -->
            <div v-show="abaAtiva === 1" class="tab-pane">
                <lista-usuarios 
                    ref="listaUsuariosRef"
                    @paginacao-updated="onPaginacaoUpdated">
                </lista-usuarios>
            </div>

            <!-- Aba 2: Papéis -->
            <div v-show="abaAtiva === 2" class="tab-pane">
                <lista-papeis 
                    ref="listaPapeisRef"
                    @paginacao-updated="onPaginacaoUpdated">
                </lista-papeis>
            </div>

            <!-- Aba 3: Permissões -->
            <div v-show="abaAtiva === 3" class="tab-pane">
                <lista-permissoes 
                    ref="listaPermissoesRef"
                    @paginacao-updated="onPaginacaoUpdated">
                </lista-permissoes>
            </div>

            <!-- Aba 4: Busca Global -->
            <div v-show="abaAtiva === 4" class="tab-pane">
                <busca-global 
                    ref="buscaGlobalRef">
                </busca-global>
            </div>
        </div>

        <!-- Paginação Centralizada -->
        <div v-if="paginacao && paginacao.last_page > 1" class="mt-4 pt-3 border-top">
            <nav aria-label="Navegação de páginas">
                <ul class="pagination justify-content-center mb-0">
                    <!-- Botão Anterior -->
                    <li class="page-item" :class="{ disabled: paginacao.current_page === 1 }">
                        <button class="page-link" @click="mudarPagina(paginacao.current_page - 1)" :disabled="paginacao.current_page === 1">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </li>
                    
                    <!-- Páginas -->
                    <li v-for="page in getPaginasVisiveis()" :key="page" class="page-item" :class="{ active: page === paginacao.current_page }">
                        <button class="page-link" @click="mudarPagina(page)">
                            {{ page }}
                        </button>
                    </li>
                    
                    <!-- Botão Próximo -->
                    <li class="page-item" :class="{ disabled: paginacao.current_page === paginacao.last_page }">
                        <button class="page-link" @click="mudarPagina(paginacao.current_page + 1)" :disabled="paginacao.current_page === paginacao.last_page">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </li>
                </ul>
            </nav>
            
            <!-- Informações da Paginação -->
            <div class="text-center text-muted small mt-2">
                Mostrando {{ ((paginacao.current_page - 1) * paginacao.per_page) + 1 }} 
                a {{ Math.min(paginacao.current_page * paginacao.per_page, paginacao.total) }} 
                de {{ paginacao.total }} registros
            </div>
        </div>
    </div>
</template>

<script>
import ListaUsuarios from './ListaUsuarios.vue'
import ListaPapeis from './ListaPapeis.vue'
import ListaPermissoes from './ListaPermissoes.vue'
import BuscaGlobal from './BuscaGlobal.vue'

export default {
    name: 'GestaoUsuarios',
    components: {
        ListaUsuarios,
        ListaPapeis,
        ListaPermissoes,
        BuscaGlobal
    },
    
    data() {
        return {
            abaAtiva: 1,
            paginacao: null
        }
    },
    
    mounted() {
        console.log('GestaoUsuarios montado, aba ativa:', this.abaAtiva);
        console.log('Componentes filhos:', this.$refs);
    },
    
    methods: {
        changeTab(aba) {
            console.log('Mudando para aba:', aba);
            this.abaAtiva = aba;
            // Reset da paginação ao trocar de aba
            this.paginacao = null;
        },
        
        onPaginacaoUpdated(paginacaoData) {
            this.paginacao = paginacaoData;
        },
        
        mudarPagina(page) {
            if (this.abaAtiva === 1 && this.$refs.listaUsuariosRef) {
                this.$refs.listaUsuariosRef.mudarPaginaExterna(page);
            } else if (this.abaAtiva === 2 && this.$refs.listaPapeisRef) {
                this.$refs.listaPapeisRef.mudarPaginaExterna(page);
            } else if (this.abaAtiva === 3 && this.$refs.listaPermissoesRef) {
                this.$refs.listaPermissoesRef.mudarPaginaExterna(page);
            }
        },
        
        getPaginasVisiveis() {
            if (!this.paginacao) return [];
            
            const total = this.paginacao.last_page;
            const atual = this.paginacao.current_page;
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
        }
    }
}
</script>

<style scoped>
/* Sistema de Abas */
.admin-tabs-container {
    background-color: #ffffff;
    border-radius: 0.5rem;
    overflow: visible;
}

.admin-tabs {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 0 1rem;
    margin: 0;
}

.admin-tab {
    border: none;
    background: transparent;
    color: #6c757d;
    font-weight: 500;
    padding: 0.75rem 1.25rem;
    transition: all 0.3s ease;
    position: relative;
    font-size: 0.9rem;
}

.admin-tab:hover {
    color: #18578A;
    background-color: rgba(24, 87, 138, 0.1);
}

.admin-tab.active {
    color: #18578A;
    background-color: #ffffff;
    border-bottom: 3px solid #18578A;
    box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
}

.admin-tab-content {
    padding: 2rem;
    min-height: auto;
}

/* Conteúdo das abas */
.tab-pane {
    display: block;
}
</style>
