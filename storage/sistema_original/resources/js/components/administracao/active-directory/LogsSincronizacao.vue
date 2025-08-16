<template>
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-semibold text-custom">
                <i class="fas fa-list-alt me-2"></i>Logs de Sincronização
            </h6>
        </div>
        <div class="card-body p-4">
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo de Log</label>
                    <select v-model="filtros.tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="sync">Sincronização</option>
                        <option value="error">Erro</option>
                        <option value="warning">Aviso</option>
                        <option value="info">Informação</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Data Início</label>
                    <input v-model="filtros.data_inicio" type="date" class="form-control">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Data Fim</label>
                    <input v-model="filtros.data_fim" type="date" class="form-control">
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button @click="aplicarFiltros" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                    <button @click="limparFiltros" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Limpar
                    </button>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <div class="h4 mb-1 text-primary">{{ estatisticas.total || 0 }}</div>
                            <small class="text-muted">Total de Logs</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <div class="h4 mb-1 text-success">{{ estatisticas.sucessos || 0 }}</div>
                            <small class="text-muted">Sucessos</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <div class="h4 mb-1 text-warning">{{ estatisticas.avisos || 0 }}</div>
                            <small class="text-muted">Avisos</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <div class="h4 mb-1 text-danger">{{ estatisticas.erros || 0 }}</div>
                            <small class="text-muted">Erros</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Logs -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Data/Hora</th>
                            <th>Tipo</th>
                            <th>Mensagem</th>
                            <th>Detalhes</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="log in logs" :key="log.id" :class="getLogRowClass(log.tipo)">
                            <td>
                                <div class="fw-semibold">{{ formatarData(log.created_at) }}</div>
                                <small class="text-muted">{{ formatarHora(log.created_at) }}</small>
                            </td>
                            <td>
                                <span class="badge" :class="getLogBadgeClass(log.tipo)">
                                    {{ getLogTipoText(log.tipo) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ log.mensagem }}</div>
                                <small class="text-muted">{{ log.contexto }}</small>
                            </td>
                            <td>
                                <button 
                                    v-if="log.detalhes" 
                                    @click="verDetalhes(log)" 
                                    class="btn btn-sm btn-outline-info"
                                >
                                    <i class="fas fa-eye me-1"></i>Ver
                                </button>
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td>
                                <button @click="excluirLog(log.id)" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Mostrando {{ paginacao.from || 0 }} a {{ paginacao.to || 0 }} de {{ paginacao.total || 0 }} registros
                </div>
                
                <nav v-if="paginacao.last_page > 1">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item" :class="{ disabled: paginacao.current_page === 1 }">
                            <button @click="mudarPagina(paginacao.current_page - 1)" class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        </li>
                        
                        <li 
                            v-for="page in getPaginasVisiveis()" 
                            :key="page" 
                            class="page-item"
                            :class="{ active: page === paginacao.current_page }"
                        >
                            <button @click="mudarPagina(page)" class="page-link">{{ page }}</button>
                        </li>
                        
                        <li class="page-item" :class="{ disabled: paginacao.current_page === paginacao.last_page }">
                            <button @click="mudarPagina(paginacao.current_page + 1)" class="page-link">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Ações em Lote -->
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <div>
                    <button @click="exportarLogs" class="btn btn-outline-success">
                        <i class="fas fa-download me-2"></i>Exportar Logs
                    </button>
                </div>
                
                <div>
                    <button @click="limparLogsAntigos" class="btn btn-outline-warning me-2">
                        <i class="fas fa-broom me-2"></i>Limpar Antigos
                    </button>
                    <button @click="limparTodosLogs" class="btn btn-outline-danger">
                        <i class="fas fa-trash me-2"></i>Limpar Todos
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Detalhes -->
        <div class="modal fade" id="modalDetalhes" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalhes do Log</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="logSelecionado">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Data/Hora:</strong>
                                    <div>{{ formatarData(logSelecionado.created_at) }} {{ formatarHora(logSelecionado.created_at) }}</div>
                                </div>
                                <div class="col-md-6">
                                    <strong>Tipo:</strong>
                                    <div>
                                        <span class="badge" :class="getLogBadgeClass(logSelecionado.tipo)">
                                            {{ getLogTipoText(logSelecionado.tipo) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Mensagem:</strong>
                                <div class="p-2 bg-light rounded">{{ logSelecionado.mensagem }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Contexto:</strong>
                                <div class="p-2 bg-light rounded">{{ logSelecionado.contexto }}</div>
                            </div>
                            
                            <div v-if="logSelecionado.detalhes">
                                <strong>Detalhes:</strong>
                                <pre class="p-2 bg-light rounded">{{ logSelecionado.detalhes }}</pre>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Modal } from 'bootstrap'

// Estados
const logs = ref([])
const estatisticas = ref({
    total: 0,
    sucessos: 0,
    avisos: 0,
    erros: 0
})
const paginacao = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    from: 0,
    to: 0
})
const filtros = ref({
    tipo: '',
    data_inicio: '',
    data_fim: ''
})
const logSelecionado = ref(null)
const modalDetalhes = ref(null)

// Lifecycle
onMounted(() => {
    carregarLogs()
    carregarEstatisticas()
    modalDetalhes.value = new Modal(document.getElementById('modalDetalhes'))
})

// Methods
const carregarLogs = async () => {
    try {
        const params = {
            page: paginacao.value.current_page,
            ...filtros.value
        }
        
        const response = await axios.get('/api/administracao/active-directory/logs', { params })
        const data = response.data.data
        
        logs.value = data.data
        paginacao.value = {
            current_page: data.current_page,
            last_page: data.last_page,
            per_page: data.per_page,
            total: data.total,
            from: data.from,
            to: data.to
        }
    } catch (error) {
        console.error('Erro ao carregar logs:', error)
    }
}

const carregarEstatisticas = async () => {
    try {
        const response = await axios.get('/api/administracao/active-directory/logs/stats')
        estatisticas.value = response.data.data
    } catch (error) {
        console.error('Erro ao carregar estatísticas:', error)
    }
}

const aplicarFiltros = () => {
    paginacao.value.current_page = 1
    carregarLogs()
}

const limparFiltros = () => {
    filtros.value = {
        tipo: '',
        data_inicio: '',
        data_fim: ''
    }
    aplicarFiltros()
}

const mudarPagina = (page) => {
    if (page >= 1 && page <= paginacao.value.last_page) {
        paginacao.value.current_page = page
        carregarLogs()
    }
}

const getPaginasVisiveis = () => {
    const pages = []
    const current = paginacao.value.current_page
    const last = paginacao.value.last_page
    
    for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
        pages.push(i)
    }
    
    return pages
}

const verDetalhes = (log) => {
    logSelecionado.value = log
    modalDetalhes.value.show()
}

const excluirLog = async (id) => {
    if (confirm('Tem certeza que deseja excluir este log?')) {
        try {
            await axios.delete(`/api/administracao/active-directory/logs/${id}`)
            carregarLogs()
            carregarEstatisticas()
        } catch (error) {
            console.error('Erro ao excluir log:', error)
        }
    }
}

const limparLogsAntigos = async () => {
    if (confirm('Tem certeza que deseja limpar logs antigos (mais de 30 dias)?')) {
        try {
            await axios.delete('/api/administracao/active-directory/logs/limpar-antigos')
            carregarLogs()
            carregarEstatisticas()
        } catch (error) {
            console.error('Erro ao limpar logs antigos:', error)
        }
    }
}

const limparTodosLogs = async () => {
    if (confirm('Tem certeza que deseja limpar TODOS os logs? Esta ação não pode ser desfeita!')) {
        try {
            await axios.delete('/api/administracao/active-directory/logs/limpar-todos')
            carregarLogs()
            carregarEstatisticas()
        } catch (error) {
            console.error('Erro ao limpar todos os logs:', error)
        }
    }
}

const exportarLogs = async () => {
    try {
        const params = { ...filtros.value }
        const response = await axios.get('/api/administracao/active-directory/logs/exportar', { 
            params,
            responseType: 'blob'
        })
        
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `logs_ad_${new Date().toISOString().split('T')[0]}.csv`)
        document.body.appendChild(link)
        link.click()
        link.remove()
    } catch (error) {
        console.error('Erro ao exportar logs:', error)
    }
}

// Helpers
const formatarData = (data) => {
    return new Date(data).toLocaleDateString('pt-BR')
}

const formatarHora = (data) => {
    return new Date(data).toLocaleTimeString('pt-BR')
}

const getLogTipoText = (tipo) => {
    const tipos = {
        'sync': 'Sincronização',
        'error': 'Erro',
        'warning': 'Aviso',
        'info': 'Informação'
    }
    return tipos[tipo] || tipo
}

const getLogBadgeClass = (tipo) => {
    const classes = {
        'sync': 'bg-primary',
        'error': 'bg-danger',
        'warning': 'bg-warning',
        'info': 'bg-info'
    }
    return classes[tipo] || 'bg-secondary'
}

const getLogRowClass = (tipo) => {
    const classes = {
        'error': 'table-danger',
        'warning': 'table-warning',
        'info': 'table-info'
    }
    return classes[tipo] || ''
}
</script>

<style scoped>
.text-custom {
    color: #18578A !important;
}
</style>