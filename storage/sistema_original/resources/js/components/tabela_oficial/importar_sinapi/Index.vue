<!-- 
 * Componente Principal da Importação SINAPI
 * 
 * OBJETIVO: Interface principal para importação de dados da tabela oficial SINAPI
 * (Sistema Nacional de Pesquisa de Custos e Índices da Construção Civil)
 * 
 * ESTRUTURA:
 * - Container principal com design moderno e responsivo
 * - Sistema de abas para organizar as 3 funcionalidades principais
 * - Integração com componentes filhos para cada funcionalidade específica
 * 
 * FUNCIONALIDADES:
 * 1. Aba 1: Composições e Insumos - Upload e processamento de arquivo Excel com 5 abas
 * 2. Aba 2: Mão de Obra - Processamento de percentagens de mão de obra (2 abas)
 * 3. Aba 3: Gravar no Banco - Gravação dos dados processados no banco de dados
 * 
 * COMPONENTES FILHOS:
 * - ComposicoesInsumos.vue: Gerencia upload e processamento de composições e insumos
 * - PercentagensMaoDeObra.vue: Gerencia processamento de percentagens de mão de obra
 * - GravarSinapi.vue: Gerencia gravação no banco de dados
 -->
<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold" style="color: #18578A; font-size: 1.2rem; padding: 5px 0;">
                    <i class="fas fa-file-import me-2"></i>Importação de Dados da Tabela Oficial SINAPI
                </h6>
            </div>

            <div class="card-body">
                <div class="admin-tabs-container">
                    <ul class="nav nav-tabs admin-tabs" role="tablist">
                        <li class="nav-item" role="presentation" v-for="(tab, index) in tabs" :key="index">
                            <button class="nav-link admin-tab" 
                                    :class="{ 'active': tabAtual === index }"
                                    :id="`tab-${index}`"
                                    data-bs-toggle="tab"
                                    :data-bs-target="`#content-${index}`"
                                    type="button"
                                    role="tab"
                                    :aria-controls="`content-${index}`"
                                    :aria-selected="tabAtual === index"
                                    @click="mudarTab(index)">
                                {{ tab.titulo }}
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content admin-tab-content" id="importTabsContent">
                        <div v-for="(tab, index) in tabs" 
                             :key="index"
                             class="tab-pane fade"
                             :class="{ 'show active': tabAtual === index }"
                             :id="`content-${index}`"
                             role="tabpanel"
                             :aria-labelledby="`tab-${index}`">
                            <component :is="getComponentName(index)">
                            </component>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
/**
 * Script do Componente Principal da Importação SINAPI
 * 
 * RESPONSABILIDADES:
 * - Gerenciar o estado das abas (tabAtual)
 * - Controlar a navegação entre as 3 funcionalidades
 * - Integrar os componentes filhos através de sistema de abas
 * - Manter a estrutura de dados das abas disponíveis
 * 
 * DADOS:
 * - tabAtual: Índice da aba atualmente ativa (0-2)
 * - tabs: Array com configuração das 3 abas principais
 * 
 * MÉTODOS:
 * - mudarTab(index): Altera a aba ativa
 * - getComponentName(index): Retorna o nome do componente da aba
 */
import ComposicoesInsumos from './components/ComposicoesInsumos.vue'
import PercentagensMaoDeObra from './components/PercentagensMaoDeObra.vue'
import GravarSinapi from './components/GravarSinapi.vue'

export default {
    name: 'ImportarSinapi',
    components: {
        ComposicoesInsumos,
        PercentagensMaoDeObra,
        GravarSinapi
    },
    data() {
        return {
            tabAtual: 0,
            tabs: [
                {
                    titulo: 'Composições e Insumos',
                    componente: 'ComposicoesInsumos'
                },
                {
                    titulo: 'Mão de Obra',
                    componente: 'PercentagensMaoDeObra'
                },
                {
                    titulo: 'Gravar no Banco',
                    componente: 'GravarSinapi'
                }
            ]
        }
    },
    methods: {
        mudarTab(index) {
            this.tabAtual = index
        },
        getComponentName(index) {
            return this.tabs[index].componente
        }
    }
}
</script>

<style scoped>
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
    color: #5EA853;
    background-color: rgba(94, 168, 83, 0.1);
}

.admin-tab.active {
    color: #000000;
    background-color: #ffffff;
    border-bottom: 3px solid #5EA853;
    box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
}

.admin-tab-content {
    padding: 2rem;
    min-height: auto;
}
</style>


 