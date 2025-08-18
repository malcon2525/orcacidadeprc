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
                <table class="table table-hover align-middle table-admin">
                    <thead>
                        <tr>
                            <th class="fw-semibold text-custom" style="width: 30%;">Usuário</th>
                            <th class="fw-semibold text-custom" style="width: 70%;">Papéis e Permissões</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(grupo, usuarioIndex) in resultadosAgrupados" :key="usuarioIndex" class="table-admin-row">
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-admin me-3">
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
                                        <span class="badge me-2" :class="papel === 'Nenhum papel' ? 'badge-secondary' : 'badge-info'">
                                            <i class="fas me-1" :class="papel === 'Nenhum papel' ? 'fa-user-slash' : 'fa-shield-alt'"></i>
                                            {{ papel }}
                                        </span>
                                    </div>
                                    
                                    <!-- Permissões -->
                                    <div class="scrollable-badges">
                                        <span v-if="permissoes.length === 0 || (permissoes.length === 1 && permissoes[0] === null)" 
                                              class="badge badge-warning me-1 mb-1 d-inline-block">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Sem permissões
                                        </span>
                                        <span v-else
                                              v-for="permissao in permissoes.filter(p => p !== null)" 
                                              :key="permissao" 
                                              class="badge badge-success me-1 mb-1 d-inline-block">
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
                    item.user_name.toLowerCase().includes(this.filtros.usuario.toLowerCase());
                const papelMatch = !this.filtros.papel || 
                    item.role_name.toLowerCase().includes(this.filtros.papel.toLowerCase());
                const permissaoMatch = !this.filtros.permissao || 
                    item.permission_name.toLowerCase().includes(this.filtros.permissao.toLowerCase());
                
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
                    console.log('Dados carregados com sucesso:', this.resultados.length, 'registros');
                    console.log('Filtros aplicados:', response.data.filtros_aplicados);
                    console.log('Dados brutos:', this.resultados);
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
        }




    }
}
</script>

<style scoped>
/* Apenas regras verdadeiramente específicas deste componente devem ficar aqui. */
</style>