<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho Compacto -->
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold" style="color: #18578A; font-size: 1.2rem; padding: 5px 0;">
                    <i class="fas fa-file-import me-2"></i>Importação de Dados da Tabela Oficial DER-PR
                </h6>
            </div>

            <!-- Corpo -->
            <div class="card-body">
                <!-- Sistema de Abas -->
                <div class="admin-tabs-container">
                    <!-- Navegação das Abas -->
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

                    <!-- Conteúdo das Abas -->
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
import ServicosGerais from './components/ServicosGerais.vue'
import Insumos from './components/Insumos.vue'
import FormulasTransporte from './components/FormulasTransporte.vue'
import ImportarLoteDerpr from './components/ImportarLoteDerpr.vue'

export default {
    name: 'ImportarDerpr',
    components: {
        ServicosGerais,
        Insumos,
        FormulasTransporte,
        ImportarLoteDerpr
    },
    data() {
        return {
            tabAtual: 0,
            tabs: [
                {
                    titulo: 'Serviços (Tabela Sintética)',
                    componente: 'ServicosGerais'
                },
                {
                    titulo: 'Insumos (Tabela Analítica)',
                    componente: 'Insumos'
                },
                {
                    titulo: 'Fórmulas de Transporte',
                    componente: 'FormulasTransporte'
                },
                {
                    titulo: 'Gravar no Banco',
                    componente: 'ImportarLoteDerpr'
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

 