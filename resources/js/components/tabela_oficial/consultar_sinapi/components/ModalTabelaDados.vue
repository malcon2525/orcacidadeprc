<template>
    <div class="modal fade" id="modal-tabela-dados-sinapi" tabindex="-1" aria-labelledby="modalTabelaDadosLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <!-- Header Fixo -->
                <div class="modal-header custom-modal-header">
                    <div class="d-flex align-items-center">
                        <div class="header-icon">
                            <i class="fas fa-table"></i>
                        </div>
                        <h5 class="modal-title mb-0" id="modalTabelaDadosLabel">
                            Tabela SINAPI - {{ tabela ? formatarData(tabela.data_base) : '' }} {{ tabela ? '(' + (tabela.desoneracao === 'com' ? 'Com' : 'Sem') + ' Desoneração)' : '' }}
                        </h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" @click="fecharModal" aria-label="Close"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-0" v-if="tabela">
                    <div class="container-fluid p-4">
                        <!-- Filtros e Ações -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="row g-3" style="height: 70px;">
                                    <div class="col-md-2">
                                        <div class="form-floating">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                id="filtroGrupo"
                                                v-model="filtros.grupo"
                                                @input="filtrarDados"
                                                placeholder="Digite o grupo..."
                                            >
                                            <label for="filtroGrupo">Filtrar por Grupo</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-floating">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                id="filtroCodigo"
                                                v-model="filtros.codigo"
                                                @input="filtrarDados"
                                                placeholder="Digite o código..."
                                            >
                                            <label for="filtroCodigo">Filtrar por Código</label>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="form-floating">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                id="filtroDescricao"
                                                v-model="filtros.descricao"
                                                @input="filtrarDados"
                                                placeholder="Digite a descrição..."
                                            >
                                            <label for="filtroDescricao">Filtrar por Descrição</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end justify-content-end">
                                <button 
                                    v-if="podeExportar"
                                    class="btn btn-outline-success d-flex align-items-center gap-2 px-3 py-2"
                                    @click="exportarExcel"
                                    :disabled="exportando"
                                >
                                    <i class="fas" :class="exportando ? 'fa-spinner fa-spin' : 'fa-file-excel'"></i>
                                    <span>{{ exportando ? 'Exportando...' : 'Excel - Exportar Tudo' }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Tabela de Dados -->
                        <div class="table-responsive">
                            <table class="table consultar-derpr-table">
                                <thead>
                                    <tr>
                                        <th style="width: 180px;">Grupo</th>
                                        <th style="width: 120px;">Código</th>
                                        <th> Descrição</th>
                                        <th style="width: 110px;">Unidade</th>
                                        <th style="width: 130px;">Mão de Obra</th>
                                        <th style="width: 130px;">Mat./Equip.</th>
                                        <th style="width: 130px;">Custo Total</th>
                                        <!-- <th style="width: 120px;">Desoneração</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in dadosPaginados" :key="item.codigo" class="consultar-derpr-row">
                                        <td>{{ item.grupo }}</td>
                                        <td>{{ item.codigo }}</td>
                                        <td>{{ item.descricao }}</td>
                                        <td>{{ item.unidade }}</td>
                                        <td>{{ formatarPreco(item.valor_mao_obra) }}</td>
                                        <td>{{ formatarPreco(item.valor_mat_equip) }}</td>
                                        <td>{{ formatarPreco(item.valor_total) }}</td>
                                        <!-- <td>
                                            <span class="badge" :class="item.desoneracao === 'com' ? 'bg-success' : 'bg-info'">
                                                {{ item.desoneracao === 'com' ? 'Com' : 'Sem' }}
                                            </span>
                                        </td> -->
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mensagem quando não há dados -->
                        <div v-if="dadosFiltrados.length === 0" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum dado encontrado</h5>
                            <p class="text-muted">Não há dados disponíveis com os filtros aplicados.</p>
                        </div>

                        <!-- Paginação -->
                        <div v-if="dadosFiltrados.length > 0" class="paginacao-container mt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Informações de Registros -->
                                <div class="text-muted fw-medium" style="color: #3B7873 !important;">
                                    Mostrando {{ paginacao.from }} até {{ paginacao.to }} de {{ paginacao.total }} registros
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <!-- Seletor de Itens por Página -->
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="text-muted small mb-0 mt-3 me-3">Itens por página:</label>
                                        <select class="form-select form-select-sm mt-3 me-3" style="width: auto;" v-model="paginacao.per_page" @change="mudarPagina(1)">
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Navegação -->
                                    <nav>
                                        <ul class="pagination admin-pagination mb-0">
                                            <!-- Botão Anterior -->
                                            <li class="page-item" :class="{ disabled: paginacao.current_page === 1 }">
                                                <a class="page-link" href="#" @click.prevent="mudarPagina(paginacao.current_page - 1)" aria-label="Anterior">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            </li>
                                            
                                            <!-- Páginas -->
                                            <li v-for="pagina in paginasVisiveis" 
                                                :key="pagina" 
                                                class="page-item" 
                                                :class="{ active: pagina === paginacao.current_page, disabled: pagina === '...' }">
                                                <a v-if="pagina !== '...'" 
                                                   class="page-link" 
                                                   href="#" 
                                                   @click.prevent="mudarPagina(pagina)">
                                                    {{ pagina }}
                                                </a>
                                                <span v-else class="page-link">{{ pagina }}</span>
                                            </li>
                                            
                                            <!-- Botão Próximo -->
                                            <li class="page-item" :class="{ disabled: paginacao.current_page === paginacao.last_page }">
                                                <a class="page-link" href="#" @click.prevent="mudarPagina(paginacao.current_page + 1)" aria-label="Próximo">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mensagem quando não há tabela selecionada -->
                <div class="modal-body p-0" v-else>
                    <div class="container-fluid p-4">
                        <div class="text-center py-5">
                            <i class="fas fa-table fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma tabela selecionada</h5>
                            <p class="text-muted">Selecione uma tabela para visualizar os dados.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, reactive, onMounted, computed, watch, nextTick } from 'vue'
import axios from 'axios'
import * as XLSX from 'xlsx'

export default {
    name: 'ModalTabelaDados',
    props: {
        tabela: {
            type: Object,
            required: false,
            default: null
        },
        modalId: {
            type: String,
            required: true
        },
        permissoes: {
            type: Object,
            required: true,
            default: () => ({
                crud: false,
                consultar: true,
                importar: false
            })
        }
    },
    emits: ['fechar'],
    setup(props, { emit }) {
        // Estado reativo para os filtros
        const filtros = ref({
            grupo: '',
            codigo: '',
            descricao: ''
        })

        // Estado reativo para os dados
        const dadosCompletos = ref([])
        const dadosFiltrados = ref([])
        const exportando = ref(false)

        // Estado reativo para paginação
        const paginacao = ref({
            current_page: 1,
            total: 0,
            per_page: 100,
            from: 0,
            to: 0,
            last_page: 1
        })

        /**
         * Computed property para dados paginados
         */
        const dadosPaginados = computed(() => {
            const inicio = (paginacao.value.current_page - 1) * paginacao.value.per_page
            const fim = inicio + paginacao.value.per_page
            return dadosFiltrados.value.slice(inicio, fim)
        })

        /**
         * Calcula quais páginas devem ser exibidas na paginação
         */
        const paginasVisiveis = computed(() => {
            const total = paginacao.value.last_page
            const atual = paginacao.value.current_page
            const paginas = []
            
            // Sempre mostra a primeira página
            paginas.push(1)
            
            // Calcula o intervalo de páginas ao redor da página atual
            let inicio = Math.max(2, atual - 2)
            let fim = Math.min(total - 1, atual + 2)
            
            // Ajusta o intervalo se estiver próximo das extremidades
            if (inicio > 2) paginas.push('...')
            for (let i = inicio; i <= fim; i++) {
                paginas.push(i)
            }
            if (fim < total - 1) paginas.push('...')
            
            // Sempre mostra a última página
            if (total > 1) paginas.push(total)
            
            return paginas
        })

        // Controle de acesso baseado nas permissões
        const podeExportar = computed(() => {
            return props.permissoes.crud;
        });

        /**
         * Função para filtrar dados
         */
        const filtrarDados = () => {
            let filtrados = [...dadosCompletos.value]
            
            // Aplica filtros
            if (filtros.value.grupo) {
                filtrados = filtrados.filter(item => 
                    item.grupo.toLowerCase().includes(filtros.value.grupo.toLowerCase())
                )
            }
            
            if (filtros.value.codigo) {
                filtrados = filtrados.filter(item => 
                    item.codigo.toLowerCase().includes(filtros.value.codigo.toLowerCase())
                )
            }
            
            if (filtros.value.descricao) {
                filtrados = filtrados.filter(item => 
                    item.descricao.toLowerCase().includes(filtros.value.descricao.toLowerCase())
                )
            }
            
            dadosFiltrados.value = filtrados
            
            // Atualiza a paginação
            paginacao.value.total = filtrados.length
            paginacao.value.last_page = Math.ceil(filtrados.length / paginacao.value.per_page)
            paginacao.value.current_page = 1
            
            // Atualiza os índices from/to
            paginacao.value.from = 1
            paginacao.value.to = Math.min(paginacao.value.per_page, filtrados.length)
        }

        /**
         * Função para mudar de página
         */
        const mudarPagina = (pagina) => {
            paginacao.value.current_page = pagina
            
            // Atualiza os índices from/to
            const inicio = (pagina - 1) * paginacao.value.per_page
            const fim = inicio + paginacao.value.per_page
            paginacao.value.from = inicio + 1
            paginacao.value.to = Math.min(fim, dadosFiltrados.value.length)
        }

        /**
         * Função para carregar dados da tabela
         */
        const carregarDados = async () => {
            try {
                if (!props.tabela || !props.tabela.data_base || !props.tabela.desoneracao) {
                    console.log('Tabela não selecionada, não carregando dados')
                    return
                }
                
                const tabelaParam = props.tabela.data_base + '|' + props.tabela.desoneracao
                const response = await axios.get('/api/tabela_oficial/consultar_sinapi/buscar_dados', {
                    params: {
                        tabela: tabelaParam
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                
                dadosCompletos.value = response.data.data
                dadosFiltrados.value = response.data.data
                
                // Atualiza a paginação inicial
                paginacao.value.total = response.data.data.length
                paginacao.value.last_page = Math.ceil(response.data.data.length / paginacao.value.per_page)
                paginacao.value.from = 1
                paginacao.value.to = Math.min(paginacao.value.per_page, response.data.data.length)
                
            } catch (error) {
                console.error('Erro ao buscar dados:', error)
                if (error.response) {
                    console.error('Detalhes do erro:', error.response.data)
                }
            }
        }

        /**
         * Função para exportar Excel
         */
        const exportarExcel = async () => {
            try {
                if (!props.tabela || !props.tabela.data_base || !props.tabela.desoneracao) {
                    console.error('Tabela não selecionada')
                    return
                }
                
                exportando.value = true
                const tabelaParam = props.tabela.data_base + '|' + props.tabela.desoneracao
                window.open(`/api/tabela_oficial/consultar_sinapi/exportar_excel?tabela=${tabelaParam}`, '_blank')
            } catch (error) {
                console.error('Erro ao exportar dados:', error)
            } finally {
                exportando.value = false
            }
        }

        /**
         * Formata um valor numérico para moeda brasileira
         */
        const formatarPreco = (valor) => {
            if (!valor) return 'R$ 0,00'
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(valor)
        }

        /**
         * Formata uma data do formato YYYY-MM-DD para DD/MM/YYYY
         */
        const formatarData = (data) => {
            if (!data) return '-'
            const [ano, mes, dia] = data.split('-')
            return `${dia}/${mes}/${ano}`
        }

        /**
         * Função para fechar modal
         */
        const fecharModal = () => {
            try {
                // Remove qualquer backdrop que possa estar travando
                const backdropElements = document.querySelectorAll('.modal-backdrop')
                backdropElements.forEach(backdrop => backdrop.remove())
                
                // Remove classes do body que podem estar causando travamento
                document.body.classList.remove('modal-open')
                document.body.style.overflow = ''
                document.body.style.paddingRight = ''
                
                // Fecha o modal Bootstrap
                const modalEl = document.getElementById('modal-tabela-dados-sinapi')
                if (modalEl) {
                    const modal = window.bootstrap.Modal.getInstance(modalEl)
                    if (modal) {
                        modal.hide()
                    }
                    // Remove a classe show do modal
                    modalEl.classList.remove('show')
                    modalEl.style.display = 'none'
                }
                
                // Emite o evento para o componente pai
                emit('fechar')
                
            } catch (error) {
                console.error('Erro ao fechar modal:', error)
                // Força o fechamento mesmo se houver erro
                emit('fechar')
            }
        }

        // Carrega dados quando o componente é montado
        onMounted(() => {
            // NÃO abre o modal automaticamente - será aberto apenas quando chamado pelo componente pai
        })

        // Watch para recarregar dados quando a tabela muda
        watch(() => props.tabela, (newTabela, oldTabela) => {
            if (newTabela && newTabela.data_base && newTabela.desoneracao) {
                carregarDados()
                
                // Abre o modal apenas quando uma nova tabela é selecionada
                nextTick(() => {
                    setTimeout(() => {
                        const modalEl = document.getElementById('modal-tabela-dados-sinapi')
                        if (modalEl) {
                            try {
                                // Remove listeners existentes para evitar duplicação
                                modalEl.removeEventListener('hidden.bs.modal', () => emit('fechar'))
                                
                                // Adiciona novo listener
                                modalEl.addEventListener('hidden.bs.modal', () => {
                                    emit('fechar')
                                }, { once: true })
                                
                                // Cria e mostra o modal
                                const modal = new window.bootstrap.Modal(modalEl, {
                                    backdrop: true,
                                    keyboard: true,
                                    focus: true
                                })
                                modal.show()
                            } catch (error) {
                                console.error('Erro ao abrir modal:', error)
                            }
                        } else {
                            console.error('Modal element não encontrado')
                        }
                    }, 100)
                })
            }
        })

        return {
            filtros,
            dadosCompletos,
            dadosFiltrados,
            dadosPaginados,
            paginacao,
            paginasVisiveis,
            exportando,
            podeExportar,
            filtrarDados,
            mudarPagina,
            exportarExcel,
            formatarPreco,
            formatarData,
            fecharModal
        }
    }
}
</script>

<style scoped>
/* HEADER DO MODAL */
.custom-modal-header {
    background: linear-gradient(135deg, #2E86AB 0%, #5EA853 100%);
    color: white;
    border-bottom: none;
    padding: 1rem 1.5rem;
}

.header-icon {
    background: rgba(255, 255, 255, 0.2);
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.header-icon i {
    font-size: 18px;
    color: white;
}

.modal-title {
    font-weight: 600;
    font-size: 1.1rem;
}

.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* PAGINAÇÃO */
.paginacao-container {
    border-top: 1px solid #dee2e6;
    padding-top: 15px;
}

.admin-pagination .page-link {
    color: #5EA853;
    border-color: #dee2e6;
}

.admin-pagination .page-item.active .page-link {
    background-color: #5EA853;
    border-color: #5EA853;
    color: white;
}

.admin-pagination .page-link:hover {
    color: #4CAF50;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

/* Correção para campos de filtro - evitar texto cortado */
.form-floating {
    margin-bottom: 1rem;
}

.form-floating .form-control {
    height: 50px !important; /* ALTURA MÍNIMA PARA O TEXTO NÃO SER CORTADO */
    padding: 0.75rem 0.75rem !important; /* PADDING ADEQUADO */
    font-size: 0.9rem !important;
    border: 1px solid #ced4da !important;
    border-radius: 8px !important;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
}

.form-floating .form-control:focus {
    border-color: #5EA853 !important;
    box-shadow: 0 0 0 0.2rem rgba(94, 168, 83, 0.25) !important;
}

.form-floating .form-label {
    padding: 0.75rem 0.75rem !important; /* PADDING IGUAL AO INPUT */
    font-size: 0.9rem !important;
    color: #6c757d !important;
    pointer-events: none !important;
    transform-origin: 0 0 !important;
    transition: opacity 0.1s ease-in-out, transform 0.1s ease-in-out !important;
}

.form-floating .form-control:focus ~ .form-label,
.form-floating .form-control:not(:placeholder-shown) ~ .form-label {
    opacity: 0.65 !important;
    transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem) !important;
}

/* Garantir que o texto não seja cortado quando digitado */
.form-floating .form-control:not(:placeholder-shown) {
    padding-top: 1.25rem !important;
    padding-bottom: 0.25rem !important;
}

/* CABEÇALHO FIXO DA TABELA */
.table-responsive {
    max-height: 60vh; /* ALTURA MÁXIMA PARA PERMITIR ROLAGEM */
    overflow-y: auto; /* ROLAGEM VERTICAL */
    position: relative;
}

.consultar-derpr-table {
    border-collapse: separate;
    border-spacing: 0;
}

.consultar-derpr-table thead {
    position: sticky;
    top: 0;
    z-index: 10;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.consultar-derpr-table thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
    padding: 12px 8px;
    text-align: left;
    position: relative;
}

.consultar-derpr-table thead th:first-child {
    border-top-left-radius: 8px;
}

.consultar-derpr-table thead th:last-child {
    border-top-right-radius: 8px;
}

/* EFEITO HOVER NO CABEÇALHO */
.consultar-derpr-table thead th:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    transition: background 0.2s ease;
}

/* CORPO DA TABELA */
.consultar-derpr-table tbody {
    background: white;
}

.consultar-derpr-table tbody tr {
    border-bottom: 1px solid #f1f3f4;
    transition: background-color 0.2s ease;
}

.consultar-derpr-table tbody tr:hover {
    background-color: #f8f9fa;
}

.consultar-derpr-table tbody td {
    padding: 12px 8px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f4;
}

/* RESPONSIVIDADE */
@media (max-width: 768px) {
    .table-responsive {
        max-height: 50vh;
    }
    
    .consultar-derpr-table thead th,
    .consultar-derpr-table tbody td {
        padding: 8px 4px;
        font-size: 0.85rem;
    }
}
</style>