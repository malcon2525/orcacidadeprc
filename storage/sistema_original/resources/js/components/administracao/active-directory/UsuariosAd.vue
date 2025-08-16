<template>
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-semibold text-custom">
                <i class="fas fa-users me-2"></i>Usuários Active Directory
            </h6>
        </div>
        <div class="card-body p-4">
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Buscar</label>
                    <input 
                        v-model="filtros.busca" 
                        type="text" 
                        class="form-control"
                        placeholder="Nome, email ou username..."
                        @input="aplicarFiltros"
                    >
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select v-model="filtros.status" class="form-select" @change="aplicarFiltros">
                        <option value="">Todos</option>
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo</label>
                    <select v-model="filtros.tipo" class="form-select" @change="aplicarFiltros">
                        <option value="">Todos</option>
                        <option value="ad">AD</option>
                        <option value="local">Local</option>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button @click="limparFiltros" class="btn btn-outline-secondary w-100">
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
                            <small class="text-muted">Total de Usuários</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <div class="h4 mb-1 text-success">{{ estatisticas.ativos || 0 }}</div>
                            <small class="text-muted">Usuários Ativos</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <div class="h4 mb-1 text-info">{{ estatisticas.ad || 0 }}</div>
                            <small class="text-muted">Usuários AD</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <div class="h4 mb-1 text-warning">{{ estatisticas.local || 0 }}</div>
                            <small class="text-muted">Usuários Locais</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Usuários -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Usuário</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Última Sincronização</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="usuario in usuarios" :key="usuario.id">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ usuario.name }}</div>
                                        <small class="text-muted">{{ usuario.username }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ usuario.email }}</div>
                                <small class="text-muted">{{ usuario.ad_domain || 'Local' }}</small>
                            </td>
                            <td>
                                <span class="badge" :class="getTipoBadgeClass(usuario.login_type)">
                                    {{ getTipoText(usuario.login_type) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge" :class="getStatusBadgeClass(usuario.is_active)">
                                    {{ usuario.is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td>
                                <div v-if="usuario.ad_sync_at">
                                    {{ formatarData(usuario.ad_sync_at) }}
                                </div>
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button 
                                        @click="sincronizarUsuario(usuario.id)" 
                                        class="btn btn-outline-primary"
                                        :disabled="sincronizando.includes(usuario.id)"
                                        :title="usuario.login_type === 'local' ? 'Usuário local não pode ser sincronizado' : 'Sincronizar com AD'"
                                    >
                                        <i class="fas fa-sync-alt" :class="{ 'fa-spin': sincronizando.includes(usuario.id) }"></i>
                                    </button>
                                    <button @click="verDetalhes(usuario)" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button @click="editarUsuario(usuario)" class="btn btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
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
                    <button @click="exportarUsuarios" class="btn btn-outline-success">
                        <i class="fas fa-download me-2"></i>Exportar Usuários
                    </button>
                </div>
                
                <div>
                    <button @click="sincronizarTodos" class="btn btn-primary">
                        <i class="fas fa-sync-alt me-2" :class="{ 'fa-spin': sincronizandoTodos }"></i>
                        {{ sincronizandoTodos ? 'Sincronizando...' : 'Sincronizar Todos' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Detalhes -->
        <div class="modal fade" id="modalDetalhes" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalhes do Usuário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="usuarioSelecionado">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Nome:</strong>
                                    <div>{{ usuarioSelecionado.name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <strong>Username:</strong>
                                    <div>{{ usuarioSelecionado.username }}</div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Email:</strong>
                                    <div>{{ usuarioSelecionado.email }}</div>
                                </div>
                                <div class="col-md-6">
                                    <strong>Tipo de Login:</strong>
                                    <div>
                                        <span class="badge" :class="getTipoBadgeClass(usuarioSelecionado.login_type)">
                                            {{ getTipoText(usuarioSelecionado.login_type) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Status:</strong>
                                    <div>
                                        <span class="badge" :class="getStatusBadgeClass(usuarioSelecionado.is_active)">
                                            {{ usuarioSelecionado.is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <strong>Último Login:</strong>
                                    <div>{{ usuarioSelecionado.last_login_at ? formatarData(usuarioSelecionado.last_login_at) : 'Nunca' }}</div>
                                </div>
                            </div>
                            
                            <div v-if="usuarioSelecionado.login_type === 'ad'" class="row mb-3">
                                <div class="col-md-6">
                                    <strong>ID AD:</strong>
                                    <div>{{ usuarioSelecionado.ad_user_id || '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <strong>Domínio AD:</strong>
                                    <div>{{ usuarioSelecionado.ad_domain || '-' }}</div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Última Sincronização:</strong>
                                    <div>{{ usuarioSelecionado.ad_sync_at ? formatarData(usuarioSelecionado.ad_sync_at) : 'Nunca' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <strong>Criado em:</strong>
                                    <div>{{ formatarData(usuarioSelecionado.created_at) }}</div>
                                </div>
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
const usuarios = ref([])
const estatisticas = ref({
    total: 0,
    ativos: 0,
    ad: 0,
    local: 0
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
    busca: '',
    status: '',
    tipo: ''
})
const usuarioSelecionado = ref(null)
const modalDetalhes = ref(null)
const sincronizando = ref([])
const sincronizandoTodos = ref(false)

// Lifecycle
onMounted(() => {
    carregarUsuarios()
    carregarEstatisticas()
    modalDetalhes.value = new Modal(document.getElementById('modalDetalhes'))
})

// Methods
const carregarUsuarios = async () => {
    try {
        const params = {
            page: paginacao.value.current_page,
            ...filtros.value
        }
        
        const response = await axios.get('/api/administracao/active-directory/usuarios', { params })
        const data = response.data.data
        
        usuarios.value = data.data
        paginacao.value = {
            current_page: data.current_page,
            last_page: data.last_page,
            per_page: data.per_page,
            total: data.total,
            from: data.from,
            to: data.to
        }
    } catch (error) {
        console.error('Erro ao carregar usuários:', error)
    }
}

const carregarEstatisticas = async () => {
    try {
        const response = await axios.get('/api/administracao/active-directory/usuarios/stats')
        estatisticas.value = response.data.data
    } catch (error) {
        console.error('Erro ao carregar estatísticas:', error)
    }
}

const aplicarFiltros = () => {
    paginacao.value.current_page = 1
    carregarUsuarios()
}

const limparFiltros = () => {
    filtros.value = {
        busca: '',
        status: '',
        tipo: ''
    }
    aplicarFiltros()
}

const mudarPagina = (page) => {
    if (page >= 1 && page <= paginacao.value.last_page) {
        paginacao.value.current_page = page
        carregarUsuarios()
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

const sincronizarUsuario = async (id) => {
    try {
        sincronizando.value.push(id)
        
        await axios.post(`/api/administracao/active-directory/usuarios/${id}/sync`)
        
        // Recarregar dados
        carregarUsuarios()
        carregarEstatisticas()
        
    } catch (error) {
        console.error('Erro ao sincronizar usuário:', error)
    } finally {
        sincronizando.value = sincronizando.value.filter(item => item !== id)
    }
}

const sincronizarTodos = async () => {
    try {
        sincronizandoTodos.value = true
        
        await axios.post('/api/administracao/active-directory/usuarios/sync-todos')
        
        // Recarregar dados
        carregarUsuarios()
        carregarEstatisticas()
        
    } catch (error) {
        console.error('Erro ao sincronizar todos os usuários:', error)
    } finally {
        sincronizandoTodos.value = false
    }
}

const verDetalhes = (usuario) => {
    usuarioSelecionado.value = usuario
    modalDetalhes.value.show()
}

const editarUsuario = (usuario) => {
    // Redirecionar para a página de edição de usuários
    window.location.href = `/administracao/usuarios/${usuario.id}/edit`
}

const exportarUsuarios = async () => {
    try {
        const params = { ...filtros.value }
        const response = await axios.get('/api/administracao/active-directory/usuarios/exportar', { 
            params,
            responseType: 'blob'
        })
        
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `usuarios_ad_${new Date().toISOString().split('T')[0]}.csv`)
        document.body.appendChild(link)
        link.click()
        link.remove()
    } catch (error) {
        console.error('Erro ao exportar usuários:', error)
    }
}

// Helpers
const formatarData = (data) => {
    return new Date(data).toLocaleDateString('pt-BR')
}

const getTipoText = (tipo) => {
    const tipos = {
        'ad': 'Active Directory',
        'local': 'Local',
        'hybrid': 'Híbrido'
    }
    return tipos[tipo] || tipo
}

const getTipoBadgeClass = (tipo) => {
    const classes = {
        'ad': 'bg-primary',
        'local': 'bg-warning',
        'hybrid': 'bg-info'
    }
    return classes[tipo] || 'bg-secondary'
}

const getStatusBadgeClass = (ativo) => {
    return ativo ? 'bg-success' : 'bg-danger'
}
</script>

<style scoped>
.text-custom {
    color: #18578A !important;
}

.avatar-circle {
    width: 40px;
    height: 40px;
    background-color: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}
</style>