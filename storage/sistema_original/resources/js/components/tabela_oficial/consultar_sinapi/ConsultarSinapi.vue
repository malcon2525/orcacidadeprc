<!--
  Componente de consulta da tabela SINAPI
  
  Este componente permite:
  - Selecionar uma tabela de preços (data_base + desoneracao)
  - Visualizar os dados em formato tabular
  - Navegar entre as páginas dos resultados
  - Exportar os dados
-->
<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho Compacto -->
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                    <i class="fas fa-search me-2"></i>Lista de Tabelas Oficiais (SINAPI)
                </h6>
                <div class="d-flex gap-2">
                    <!-- Botão Filtros -->
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" @click="toggleFiltros">
                        <i class="fas fa-filter"></i>
                        <span>Filtros</span>
                        <i class="fas" :class="filtrosVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                </div>
            </div>

            <!-- Corpo -->
            <div class="card-body">
                <!-- Filtros da Lista (Colapsáveis) -->
                <div class="filtros-aba-container mb-4" v-if="filtrosVisiveis">
                    <div class="filtros-aba-content" :class="{ 'show': filtrosVisiveis }">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="filtroDataBase" v-model="filtros.dataBase" @change="filtrarTabelas">
                                        <option value="">Todas as datas</option>
                                        <option v-for="data in datasUnicas" :key="data" :value="data">
                                            {{ formatarData(data) }}
                                        </option>
                                    </select>
                                    <label for="filtroDataBase">Data Base</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="filtroDesoneracao" v-model="filtros.desoneracao" @change="filtrarTabelas">
                                        <option value="">Todas</option>
                                        <option value="com">Com Desoneração</option>
                                        <option value="sem">Sem Desoneração</option>
                                    </select>
                                    <label for="filtroDesoneracao">Desoneração</label>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button class="btn btn-outline-secondary w-100 h-100" @click="limparFiltros">
                                    <i class="fas fa-times me-2"></i>Limpar Filtros
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cards de Tabelas Disponíveis -->
                <div class="row g-4">
                    <div v-for="tabela in tabelasFiltradas" :key="tabela.id" class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                        <div class="tabela-card" @click="abrirModalTabela(tabela)">
                            <!-- Header com Ícone e Badge -->
                            <div class="card-header-section">
                                <div class="card-icon-container">
                                    <i class="fas fa-table"></i>
                                </div>
                                <div class="card-title-section">
                                    <h6 class="card-title">Tabela SINAPI</h6>
                                    <p class="card-subtitle">Sistema Nacional de Pesquisa de Custos e Índices</p>
                                </div>
                                <div class="card-badge">
                                    <span class="badge-official">OFICIAL</span>
                                </div>
                            </div>
                            
                            <!-- Informações da Tabela -->
                            <div class="card-info-section">
                                <div class="info-line">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span class="info-text">
                                        Data base: <span class="date-value">{{ formatarData(tabela.data_base) }}</span>
                                    </span>
                                </div>
                                <div class="info-line">
                                    <i class="fas fa-percentage"></i>
                                    <span class="info-text">
                                        <span class="desoneracao-text" :class="tabela.desoneracao === 'com' ? 'text-success' : 'text-info'">
                                            {{ tabela.desoneracao === 'com' ? 'Com Desoneração' : 'Sem Desoneração' }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Botão de Ação -->
                            <div class="card-action-section">
                                <button class="btn-visualizar">
                                    <i class="fas fa-play"></i>
                                    <span>Visualizar Tabela</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mensagem quando não há tabelas -->
                <div v-if="tabelasFiltradas.length === 0" class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma tabela encontrada</h5>
                    <p class="text-muted">Não há tabelas disponíveis com os filtros aplicados.</p>
                </div>
            </div>
        </div>

        <!-- Modal de Dados da Tabela -->
        <modal-tabela-dados
            :tabela="tabelaSelecionada"
            @fechar="fecharModalTabela"
            modal-id="modal-tabela-dados-sinapi"
        />
    </div>
</template>

<script>
import { ref, reactive, onMounted, computed, nextTick } from 'vue'
import axios from 'axios'
import ModalTabelaDados from './components/ModalTabelaDados.vue'

export default {
    name: 'ConsultarSinapiComponent',
    components: {
        ModalTabelaDados
    },
    setup() {
        // Estado reativo para os filtros
        const filtros = ref({
            dataBase: '',
            desoneracao: ''
        })

        // Estado reativo para ordenação
        const ordenacao = ref({
            campo: 'data_base',
            direcao: 'desc'
        })

        // Estado reativo para os dados
        const tabelas = ref([])
        const tabelaSelecionada = ref(null)
        const filtrosVisiveis = ref(false)

        /**
         * Computed property para datas únicas
         */
        const datasUnicas = computed(() => {
            const datas = [...new Set(tabelas.value.map(t => t.data_base))]
            return datas.sort().reverse()
        })

        /**
         * Computed property para tabelas filtradas e ordenadas
         */
        const tabelasFiltradas = computed(() => {
            let filtradas = [...tabelas.value]

            // Aplica filtros
            if (filtros.value.dataBase) {
                filtradas = filtradas.filter(t => t.data_base === filtros.value.dataBase)
            }

            if (filtros.value.desoneracao) {
                filtradas = filtradas.filter(t => t.desoneracao === filtros.value.desoneracao)
            }

            // Aplica ordenação
            filtradas.sort((a, b) => {
                let valorA = a[ordenacao.value.campo]
                let valorB = b[ordenacao.value.campo]

                // Converte datas para comparação
                if (ordenacao.value.campo === 'data_base') {
                    valorA = new Date(valorA)
                    valorB = new Date(valorB)
                }

                if (ordenacao.value.direcao === 'asc') {
                    return valorA > valorB ? 1 : -1
                } else {
                    return valorA < valorB ? 1 : -1
                }
            })

            return filtradas
        })

        // Funções
        const formatarData = (data) => {
            if (!data) return ''
            const [ano, mes, dia] = data.split('-')
            return `${dia}/${mes}/${ano}`
        }

        const toggleFiltros = () => {
            filtrosVisiveis.value = !filtrosVisiveis.value
        }

        const filtrarTabelas = () => {
            // A filtragem é automática através do computed
        }

        const limparFiltros = () => {
            filtros.value.dataBase = ''
            filtros.value.desoneracao = ''
        }

        const abrirModalTabela = (tabela) => {
            console.log('Abrindo modal para tabela:', tabela)
            tabelaSelecionada.value = tabela
            
            // Aguarda o próximo tick para garantir que o modal seja renderizado
            nextTick(() => {
                setTimeout(() => {
                    const modalEl = document.getElementById('modal-tabela-dados-sinapi')
                    console.log('Elemento do modal encontrado:', modalEl)
                    if (modalEl) {
                        const modal = new window.bootstrap.Modal(modalEl)
                        modal.show()
                        console.log('Modal aberto com sucesso')
                    } else {
                        console.error('Elemento do modal não encontrado')
                    }
                }, 100)
            })
        }

        const fecharModalTabela = () => {
            tabelaSelecionada.value = null
        }

        // Carrega as tabelas disponíveis ao montar o componente
        onMounted(async () => {
            try {
                console.log('Carregando tabelas SINAPI...')
                const response = await axios.get('/api/tabela_oficial/consultar_sinapi/tabelas')
                console.log('Tabelas carregadas:', response.data)
                tabelas.value = response.data
            } catch (error) {
                console.error('Erro ao carregar tabelas:', error)
                if (error.response) {
                    console.error('Detalhes do erro:', error.response.data)
                }
            }
        })

        return {
            filtros,
            ordenacao,
            tabelas,
            tabelaSelecionada,
            filtrosVisiveis,
            datasUnicas,
            tabelasFiltradas,
            formatarData,
            toggleFiltros,
            filtrarTabelas,
            limparFiltros,
            abrirModalTabela,
            fecharModalTabela
        }
    }
}
</script>

<style scoped>
/* Estilos dos Cards de Tabela */
.tabela-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.tabela-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-color: #5EA853;
}

.card-header-section {
    display: flex;
    align-items: flex-start;
    margin-bottom: 15px;
    position: relative;
}

.card-icon-container {
    background: linear-gradient(135deg, #5EA853, #4CAF50);
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
}

.card-icon-container i {
    font-size: 20px;
}

.card-title-section {
    flex: 1;
}

.card-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0 0 5px 0;
}

.card-subtitle {
    font-size: 12px;
    color: #666;
    margin: 0;
    line-height: 1.3;
}

.card-badge {
    position: absolute;
    top: 0;
    right: 0;
}

.badge-official {
    background: linear-gradient(135deg, #5EA853, #4CAF50);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-info-section {
    flex: 1;
    margin-bottom: 15px;
}

.info-line {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    font-size: 14px;
}

.info-line i {
    color: #5EA853;
    width: 16px;
    margin-right: 8px;
    font-size: 12px;
}

.info-text {
    color: #555;
    flex: 1;
}

.date-value {
    font-weight: 600;
    color: #333;
}

.desoneracao-text {
    font-weight: 600;
}

.card-action-section {
    margin-top: auto;
}

.btn-visualizar {
    background: linear-gradient(135deg, #2E86AB, #5EA853);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 10px 16px;
    width: 100%;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-visualizar:hover {
    background: linear-gradient(135deg, #5EA853, #4CAF50);
    transform: translateY(-1px);
}

/* Estilos dos Filtros */
.filtros-aba-container {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #e9ecef;
}

.filtros-aba-content {
    transition: all 0.3s ease;
}

.filtros-aba-content.show {
    opacity: 1;
    max-height: 200px;
    overflow: hidden;
}

/* Responsividade */
@media (max-width: 768px) {
    .tabela-card {
        padding: 15px;
    }
    
    .card-icon-container {
        width: 40px;
        height: 40px;
    }
    
    .card-icon-container i {
        font-size: 16px;
    }
    
    .card-title {
        font-size: 14px;
    }
    
    .card-subtitle {
        font-size: 11px;
    }
}
</style>
