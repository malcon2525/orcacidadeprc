<template>
    <div class="busca-global">
        <!-- Cabeçalho Compacto da Aba Busca Global -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 text-custom">
                <i class="fas fa-search me-2"></i>Relacionamentos Usuário-Papel-Permissão
            </h6>
            <div class="d-flex gap-2">
                <!-- Botão Filtros -->
                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" @click="toggleFiltros">
                    <i class="fas fa-filter"></i>
                    <span>Filtros</span>
                    <i class="fas" :class="filtrosVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <!-- Botão Limpar Filtros -->
                <button class="btn btn-outline-warning d-flex align-items-center gap-2 px-3 py-2" @click="limparFiltros">
                    <i class="fas fa-times"></i>
                    <span>Limpar Filtros</span>
                </button>
            </div>
        </div>
        
        <!-- Filtros da Aba Busca Global (Compactos) -->
        <div class="filtros-aba-container mb-3" v-if="filtrosVisiveis">
            <div class="filtros-aba-content" :class="{ 'show': filtrosVisiveis }">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="filtroUsuario" v-model="filtros.usuario" @input="realizarBusca" placeholder="Nome do usuário...">
                            <label for="filtroUsuario">Usuário</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="filtroPapel" v-model="filtros.papel" @input="realizarBusca" placeholder="Nome do papel...">
                            <label for="filtroPapel">Papel</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="filtroPermissao" v-model="filtros.permissao" @input="realizarBusca" placeholder="Nome da permissão...">
                            <label for="filtroPermissao">Permissão</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resultados em Layout Discreto e Profissional -->
        <div class="results-container">
            <!-- Estado de Carregamento -->
            <div v-if="loading" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2 text-muted">Carregando relacionamentos...</p>
            </div>

            <!-- Resultados -->
            <div v-else-if="resultadosAgrupados.length > 0" class="table-responsive">
                <table class="table table-hover align-middle usuarios-table">
                    <thead>
                        <tr>
                            <th class="fw-semibold text-custom" style="width: 30%;">Usuário</th>
                            <th class="fw-semibold text-custom" style="width: 70%;">Papéis e Permissões</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(grupo, usuarioIndex) in resultadosAgrupados" :key="usuarioIndex" class="usuario-row">
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-3">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium text-custom">{{ grupo.usuario }}</div>
                                        <small class="text-muted">{{ Object.keys(grupo.papeis).length }} papel{{ Object.keys(grupo.papeis).length !== 1 ? 'éis' : 'el' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div v-for="(permissoes, papel) in grupo.papeis" :key="papel" class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge-papel me-2">
                                            <i class="fas fa-shield-alt me-1"></i>
                                            {{ papel }}
                                        </span>
                                        <small class="text-muted">{{ permissoes.length }} permissão{{ permissoes.length !== 1 ? 'ões' : 'ão' }}</small>
                                    </div>
                                    <div class="permissoes-container">
                                        <span 
                                            v-for="permissao in permissoes" 
                                            :key="permissao" 
                                            class="badge-status badge-ativo me-1 mb-1 d-inline-block"
                                        >
                                            <i class="fas fa-key me-1"></i>
                                            {{ permissao }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Nenhum Resultado -->
            <div v-else class="text-center py-4">
                <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
                <h6 class="text-muted">Nenhum relacionamento encontrado</h6>
                <p class="text-muted small mb-0">Tente ajustar os filtros ou verificar se há dados cadastrados.</p>
            </div>
        </div>



        <!-- Toast para mensagens -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div ref="toast" class="toast" role="alert">
                <div class="toast-header">
                    <i :class="toastIcon" class="me-2"></i>
                    <strong class="me-auto">{{ toastTitle }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    {{ toastMessage }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Toast } from 'bootstrap'

export default {
    name: 'BuscaGlobal',
    props: {
        // Receber notificação de quando a aba está ativa
        isActive: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            loading: false,
            resultados: [], // Todos os dados carregados uma vez
            filtrosVisiveis: false,
            filtros: {
                usuario: '',
                papel: '',
                permissao: ''
            },
            toast: null,
            toastIcon: '',
            toastTitle: '',
            toastMessage: '',
            timeoutBusca: null
        }
    },
    computed: {
        // Filtrar resultados (igual à aba Usuários)
        resultadosFiltrados() {
            return this.resultados.filter(item => {
                const usuarioMatch = !this.filtros.usuario || 
                    item.user_name.toLowerCase().includes(this.filtros.usuario.toLowerCase());
                const papelMatch = !this.filtros.papel || 
                    item.role_name.toLowerCase().includes(this.filtros.papel.toLowerCase());
                const permissaoMatch = !this.filtros.permissao || 
                    item.permission_name.toLowerCase().includes(this.filtros.permissao.toLowerCase());
                
                return usuarioMatch && papelMatch && permissaoMatch;
            });
        },
        
        // Agrupar resultados por usuário (igual antes)
        resultadosAgrupados() {
            const agrupados = {};
            
            this.resultadosFiltrados.forEach(item => {
                const chave = item.user_name;
                
                if (!agrupados[chave]) {
                    agrupados[chave] = {
                        usuario: item.user_name,
                        papeis: {}
                    };
                }
                
                if (!agrupados[chave].papeis[item.role_name]) {
                    agrupados[chave].papeis[item.role_name] = [];
                }
                
                agrupados[chave].papeis[item.role_name].push(item.permission_name);
            });
            
            return Object.values(agrupados);
        }
    },

    mounted() {
        // Aguardar o próximo tick para garantir que o DOM está renderizado
        this.$nextTick(() => {
            if (this.$refs.toast) {
                this.toast = new Toast(this.$refs.toast);
            }
        });
        this.carregarDados();
    },
    
    watch: {
        // Observar mudanças na prop isActive
        isActive: {
            handler(newValue) {
                if (newValue) {
                    // Aba foi ativada - recarregar dados
                    this.carregarDados();
                }
            },
            immediate: true
        }
    },
    methods: {
        // Toggle filtros
        toggleFiltros() {
            this.filtrosVisiveis = !this.filtrosVisiveis;
        },

        // Carregar todos os dados uma vez (igual à aba Usuários)
        async carregarDados() {
            try {
                this.loading = true;
                // Verificar se axios está disponível
                if (typeof axios === 'undefined') {
                    console.error('Axios não está disponível');
                    this.resultados = [];
                    return;
                }
                const response = await axios.get('/api/busca-global');
                this.resultados = response.data.resultados || [];
            } catch (error) {
                console.error('Erro ao carregar dados:', error);
                this.resultados = [];
                // Só mostrar toast se o toast estiver disponível
                if (this.toast) {
                    this.mostrarToast('Erro', 'Erro ao carregar dados', 'fa-exclamation-circle text-danger');
                }
            } finally {
                this.loading = false;
            }
        },

        // Filtrar no frontend (igual à aba Usuários)
        realizarBusca() {
            // Debounce para evitar muitas operações
            clearTimeout(this.timeoutBusca);
            this.timeoutBusca = setTimeout(() => {
                // Filtro acontece automaticamente no computed
            }, 300);
        },

        limparFiltros() {
            this.filtros.usuario = '';
            this.filtros.papel = '';
            this.filtros.permissao = '';
        },

        mostrarToast(titulo, mensagem, icone) {
            this.toastTitle = titulo;
            this.toastMessage = mensagem;
            this.toastIcon = icone;
            // Só mostrar toast se estiver disponível
            if (this.toast) {
                this.toast.show();
            }
        },




    }
}
</script>

<style>
/* Estilos específicos para a aba Busca Global */
.permissoes-container {
    max-height: 200px;
    overflow-y: auto;
    padding-right: 10px; /* Espaço para evitar corte dos badges */
}

.permissoes-container::-webkit-scrollbar {
    width: 6px;
}

.permissoes-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.permissoes-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.permissoes-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Corrigir badges quebrados */
.badge-status {
    white-space: nowrap;
    overflow: visible;
    word-break: keep-all;
}

/* Remover bordas residuais */
.usuario-row td:last-child {
    border-right: none !important;
}

.usuario-row:hover td:last-child {
    border-right: none !important;
}
</style>