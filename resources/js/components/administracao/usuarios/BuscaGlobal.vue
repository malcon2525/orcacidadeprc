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

        <!-- Resultados em Layout Melhorado com Ícones Discretos -->
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
                <!-- Informações da busca -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ resultadosAgrupados.length }} usuário{{ resultadosAgrupados.length !== 1 ? 's' : '' }} encontrado{{ resultadosAgrupados.length !== 1 ? 's' : '' }}
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Filtros ativos -->
                        <div v-if="filtros.usuario || filtros.papel || filtros.permissao" class="text-muted small">
                            <i class="fas fa-filter me-1"></i>
                            <span v-if="filtros.usuario">Usuário: "{{ filtros.usuario }}"</span>
                            <span v-else-if="filtros.papel">Papel: "{{ filtros.papel }}"</span>
                            <span v-else-if="filtros.permissao">Permissão: "{{ filtros.permissao }}"</span>
                        </div>
                        <!-- Tempo de busca -->
                        <div class="text-muted small" v-if="tempoBusca">
                            <i class="fas fa-clock me-1"></i>
                            {{ tempoBusca }}ms
                        </div>
                    </div>
                </div>
                
                <!-- Layout Melhorado com Grid Responsivo -->
                <div class="row g-3">
                    <div v-for="(grupo, usuarioIndex) in resultadosAgrupados" :key="usuarioIndex" class="col-12">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="row g-0">
                                    <!-- Coluna Usuário -->
                                    <div class="col-md-3 border-end pe-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="user-avatar me-3">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 text-dark fw-semibold">{{ grupo.usuario }}</h6>
                                                <div class="d-flex align-items-center text-muted small">
                                                    <i class="fas fa-tag me-1"></i>
                                                    <span>{{ Object.keys(grupo.papeis).length }} papel{{ Object.keys(grupo.papeis).length !== 1 ? 'éis' : 'el' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Coluna Papéis e Permissões -->
                                    <div class="col-md-9 ps-3">
                                        <div class="row g-2">
                                            <div v-for="(permissoes, papel) in grupo.papeis" :key="papel" class="col-12">
                                                <div class="papel-container p-2 rounded border-start border-3" 
                                                     :class="papel === 'Nenhum papel' ? 'border-secondary bg-light' : 'border-blue bg-white'">
                                                    
                                                    <!-- Cabeçalho do Papel -->
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="papel-icon me-2">
                                                            <i class="fas" :class="papel === 'Nenhum papel' ? 'fa-user-slash text-secondary' : 'fa-shield-alt cor-titulo-papel'"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 fw-semibold" :class="papel === 'Nenhum papel' ? 'text-secondary' : 'cor-titulo-papel'">
                                                                {{ papel }}
                                                            </h6>
                                                            <small class="text-muted" v-if="papel !== 'Nenhum papel'">
                                                                <code class="bg-light px-1 rounded">{{ getPapelName(papel) }}</code>
                                                            </small>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Permissões -->
                                                    <div class="permissoes-container">
                                                        <div v-if="permissoes.length === 0 || (permissoes.length === 1 && permissoes[0] === null)" 
                                                              class="permissao-item warning">
                                                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                                                            <span class="text-warning">Sem permissões</span>
                                                        </div>
                                                        <div v-else class="row g-1">
                                                            <div v-for="permissao in permissoes.filter(p => p !== null)" :key="permissao" class="col-auto">
                                                                <div class="permissao-item success">
                                                                    <i class="fas fa-key text-muted me-1"></i>
                                                                    <span class="text-muted">{{ permissao }}</span>
                                                                    <small class="text-muted d-block mt-1 ms-2">
                                                                        <code class="bg-light px-1 rounded">{{ getPermissaoName(permissao) }}</code>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nenhum Resultado -->
            <div v-else class="text-center py-4">
                <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
                <h6 class="text-muted">Nenhum relacionamento encontrado</h6>
                <p class="text-muted small mb-0">
                    {{ getMensagemSemResultados() }}
                </p>
                <div v-if="resultados.length === 0 && !temFiltrosAtivos" class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Para ver dados aqui, você precisa ter usuários, papéis e permissões cadastrados e relacionados.
                    </small>
                </div>
                <div v-else-if="temFiltrosAtivos" class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-lightbulb me-1"></i>
                        Tente ajustar os filtros ou usar termos mais amplos na busca.
                    </small>
                </div>
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
            tempoBusca: null,
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
                    (item.user_name && item.user_name.toLowerCase().includes(this.filtros.usuario.toLowerCase()));
                const papelMatch = !this.filtros.papel || 
                    (item.role_name && item.role_name.toLowerCase().includes(this.filtros.papel.toLowerCase()));
                const permissaoMatch = !this.filtros.permissao || 
                    (item.permission_name && item.permission_name.toLowerCase().includes(this.filtros.permissao.toLowerCase()));
                
                return usuarioMatch && papelMatch && permissaoMatch;
            });
        },
        
        // Agrupar resultados por usuário
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
                
                // Adicionar permissão apenas se existir
                if (item.permission_name) {
                    agrupados[chave].papeis[item.role_name].push(item.permission_name);
                } else {
                    // Papel sem permissões
                    agrupados[chave].papeis[item.role_name].push(null);
                }
            });
            
            return Object.values(agrupados);
        },
        
        // Verificar se há filtros ativos
        temFiltrosAtivos() {
            return this.filtros.usuario || this.filtros.papel || this.filtros.permissao;
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

        // Carregar todos os dados uma vez
        async carregarDados() {
            try {
                this.loading = true;
                
                // Verificar se axios está disponível
                if (typeof axios === 'undefined') {
                    console.error('Axios não está disponível');
                    this.resultados = [];
                    return;
                }
                
                // Construir parâmetros de filtro
                const params = {};
                if (this.filtros.usuario) params.usuario = this.filtros.usuario;
                if (this.filtros.papel) params.papel = this.filtros.papel;
                if (this.filtros.permissao) params.permissao = this.filtros.permissao;
                
                const response = await axios.get('/api/administracao/busca-global', { params });
                
                // Validar estrutura da resposta
                if (response.data && response.data.success && response.data.resultados) {
                    this.resultados = response.data.resultados;
                    this.tempoBusca = response.data.tempo_busca || null;
                    
                } else {
                    console.error('Estrutura de resposta inválida:', response.data);
                    this.resultados = [];
                    this.tempoBusca = null;
                    if (this.toast) {
                        this.mostrarToast('Aviso', 'Estrutura de dados inválida', 'fa-exclamation-triangle text-warning');
                    }
                }
                
            } catch (error) {
                console.error('Erro ao carregar dados:', error);
                this.resultados = [];
                this.tempoBusca = null;
                
                // Mostrar mensagem de erro mais específica
                let errorMessage = 'Erro ao carregar dados';
                if (error.response?.status === 403) {
                    errorMessage = 'Acesso negado. Permissão insuficiente.';
                } else if (error.response?.status === 500) {
                    errorMessage = 'Erro interno do servidor.';
                } else if (error.response?.data?.mensagem) {
                    errorMessage = error.response.data.mensagem;
                }
                
                if (this.toast) {
                    this.mostrarToast('Erro', errorMessage, 'fa-exclamation-circle text-danger');
                }
            } finally {
                this.loading = false;
            }
        },

        // Realizar busca com filtros
        realizarBusca() {
            // Debounce para evitar muitas operações
            clearTimeout(this.timeoutBusca);
            this.timeoutBusca = setTimeout(() => {
                // Recarregar dados com os filtros aplicados
                this.carregarDados();
            }, 500);
        },

        limparFiltros() {
            this.filtros.usuario = '';
            this.filtros.papel = '';
            this.filtros.permissao = '';
            // Recarregar dados sem filtros
            this.carregarDados();
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
        
        // Obter mensagem apropriada quando não há resultados
        getMensagemSemResultados() {
            if (this.resultados.length === 0) {
                return 'Não há dados cadastrados no sistema.';
            }
            
            if (this.temFiltrosAtivos) {
                return 'Nenhum resultado encontrado para os filtros aplicados.';
            }
            
            return 'Tente ajustar os filtros de busca.';
        },

        // Obter nome interno do papel
        getPapelName(displayName) {
            // Mapeamento de display_name para name (nome interno)
            const mapeamentoPapeis = {
                'Super Administrador': 'super',
                'Gerenciador de Usuários': 'gerenciar_usuarios',
                'Visualizador de Usuários': 'visualizar_usuarios',
                'Gerenciador de Municípios': 'gerenciar_municipios',
                'Visualizador de Municípios': 'visualizar_municipios',
                'Gerenciador de Entidade Orçamentária': 'gerenciar_entidade_orcamentaria',
                'Visualizador de Entidade Orçamentária': 'visualizar_entidade_orcamentaria',
                'Gerenciador de Integração com o AD': 'gerenciar_ad',
                'Gerenciador de Estrutura': 'gerenciar_estrutura'
            };
            
            return mapeamentoPapeis[displayName] || displayName;
        },

        // Obter nome interno da permissão
        getPermissaoName(displayName) {
            // Mapeamento de display_name para name (nome interno) - CORRIGIDO
            const mapeamentoPermissoes = {
                'Consultar Usuários': 'usuario_consultar',
                'Gerenciar Usuários (CRUD)': 'usuario_crud',
                'Consultar Papéis': 'papel_consultar',
                'Gerenciar Papéis (CRUD)': 'papel_crud',
                'Consultar Permissões': 'permissao_consultar',
                'Gerenciar Permissões (CRUD)': 'permissao_crud',
                'Sincronizar com AD': 'sincronizar_ad',
                'Gerenciar Municípios (CRUD)': 'municipio_crud',
                'Consultar Municípios': 'municipio_consultar',
                'Importar Municípios': 'municipio_importar',
                'Gerenciar Entidades Orçamentárias (CRUD)': 'entidade_orcamentaria_crud',
                'Consultar Entidades Orçamentárias': 'entidade_orcamentaria_consultar',
                'Importar Entidades Orçamentárias': 'entidade_orcamentaria_importar',
                'Sem permissões': 'no_permissions'
            };
            
            return mapeamentoPermissoes[displayName] || displayName;
        }




    }
}
</script>

<style scoped>
/* Estilos específicos para o layout melhorado da Busca Global */

/* Cards de usuário */
.card {
    transition: all 0.2s ease-in-out;
    border-radius: 8px;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

/* Avatar do usuário */
.user-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #dee2e6;
}

.user-avatar i {
    font-size: 16px;
    color: #6c757d;
}

/* Container de papel */
.papel-container {
    transition: all 0.2s ease-in-out;
    border-left: 4px solid;
}

.papel-container:hover {
    transform: translateX(2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

/* Ícone do papel */
.papel-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.papel-icon i {
    font-size: 14px;
}

/* Container de permissões */
.permissoes-container {
    margin-top: 8px;
}

/* Item de permissão */
.permissao-item {
    padding: 6px 12px;
    border-radius: 6px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    display: inline-flex;
    align-items: center;
    font-size: 13px;
    transition: all 0.2s ease-in-out;
}

.permissao-item:hover {
    background: #e9ecef;
    border-color: #dee2e6;
}

.permissao-item.warning {
    background: #fff3cd;
    border-color: #ffeaa7;
}

.permissao-item.success {
    background: #f8f9fa79;
    border-color: #fff;
}

.permissao-item i {
    font-size: 12px;
}

/* Responsividade */
@media (max-width: 768px) {
    .col-md-3.border-end {
        border-right: none !important;
        border-bottom: 1px solid #dee2e6 !important;
        padding-bottom: 1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .col-md-9.ps-3 {
        padding-left: 0 !important;
    }
    
    .papel-container {
        margin-bottom: 1rem;
    }
}

/* Animações suaves */
.card, .papel-container, .permissao-item {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Melhorias nos códigos */
code {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 4px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    color: #495057;
}

/* Cores harmoniosas para os ícones - Hierarquia Visual Correta */
.text-primary {
    color: #495057 !important; /* Cinza escuro mais neutro */
}

.text-success {
    color: #6c757d !important; /* Cinza neutro para permissões (menos destaque) */
}

.text-warning {
    color: #ffc107 !important; /* Mantém amarelo para avisos */
}

.text-secondary {
    color: #6c757d !important;
}

.text-muted {
    color: #6c757d !important;
}

.text-dark {
    color: #212529 !important; /* Preto para papéis (mais destaque) */
}

.cor-titulo-papel {
    color: #165891 !important;
}

.border-blue {
    border-left: 3px solid #165891 !important;
}

</style>