<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho Padrão -->
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                    <i class="fas fa-users me-2"></i>Usuários por Entidade
                </h6>
                <div class="d-flex gap-2">
                    <!-- Botão Filtros -->
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" @click="toggleFiltros">
                        <i class="fas fa-filter"></i>
                        <span>Filtros</span>
                        <i class="fas" :class="filtrosVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <!-- Botão Atualizar -->
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" @click="carregarEntidades">
                        <i class="fas fa-sync-alt"></i>
                        <span>Atualizar</span>
                    </button>
                </div>
            </div>

            <!-- Corpo do Card -->
            <div class="card-body">
                <!-- Filtros Colapsáveis -->
                <div class="filtros-aba-container mb-3" v-if="filtrosVisiveis">
                    <div class="filtros-aba-content" :class="{ 'show': filtrosVisiveis }">
                        <div class="row g-3 align-items-end">
                            <!-- Campo de Busca -->
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           id="busca" 
                                           v-model="filtros.busca"
                                           placeholder="Buscar por nome da entidade..."
                                           @keyup.enter="filtrarEntidades"
                                           @input="onBuscaInput">
                                    <label for="busca">Buscar</label>
                                </div>
                            </div>
                            
                            <!-- Filtro de Nível Administrativo -->
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-control" id="filtroNivel" v-model="filtros.nivel_administrativo">
                                        <option value="">Todos os níveis</option>
                                        <option v-for="nivel in niveisAdministrativos" :key="nivel.value" :value="nivel.value">
                                            {{ nivel.label }}
                                        </option>
                                    </select>
                                    <label for="filtroNivel">Nível</label>
                                </div>
                            </div>
                            
                            <!-- Filtro de Tipo de Organização -->
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-control" id="filtroTipo" v-model="filtros.tipo_organizacao">
                                        <option value="">Todos os tipos</option>
                                        <option v-for="tipo in tiposOrganizacao" :key="tipo.value" :value="tipo.value">
                                            {{ tipo.label }}
                                        </option>
                                    </select>
                                    <label for="filtroTipo">Tipo</label>
                                </div>
                            </div>
                            
                            <!-- Filtro de Status -->
                            <div class="col-md-2">
                                <div class="form-floating">
                                    <select class="form-control" id="filtroAtivo" v-model="filtros.ativo">
                                        <option value="">Todas</option>
                                        <option value="true">Ativas</option>
                                        <option value="false">Inativas</option>
                                    </select>
                                    <label for="filtroAtivo">Status</label>
                                </div>
                            </div>
                            
                            <!-- Botões de Ação dos Filtros -->
                            <div class="col-md-2">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary" @click="filtrarEntidades">
                                        <i class="fas fa-search me-2"></i>Filtrar
                                    </button>
                                    <button class="btn btn-secondary" @click="limparFiltros">
                                        <i class="fas fa-times me-2"></i>Limpar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando entidades...</p>
                </div>
                
                <!-- Tabela de Entidades -->
                <div v-else-if="entidades.length > 0" class="table-responsive">
                    <table class="table table-hover align-middle" style="min-width: 700px;">
                        <thead>
                            <tr>
                                <th class="table-header">Entidade</th>
                                <th class="table-header">Tipo</th>
                                <th class="table-header">Jurisdição</th>
                                <th class="table-header">Status</th>
                                <th class="table-header">Usuários Ativos</th>
                                <th class="table-header">Total Usuários</th>
                                <th class="table-header text-end" style="width: 120px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="entidade in entidades" :key="entidade.id" class="table-row">
                                <td class="table-cell">
                                    <div class="fw-medium">{{ entidade.nome_fantasia }}</div>
                                </td>
                                <td class="table-cell">
                                    <span class="badge" :class="getTipoClass(entidade.tipo_organizacao)">
                                        {{ formatarTipo(entidade.tipo_organizacao) }}
                                    </span>
                                </td>
                                <td class="table-cell">
                                    <div class="fw-medium">{{ formatarJurisdicao(entidade) }}</div>
                                    <small v-if="entidade.nivel_administrativo" class="text-muted">
                                        {{ formatarNivel(entidade.nivel_administrativo) }}
                                    </small>
                                </td>
                                <td class="table-cell">
                                    <span class="badge badge-status" :class="entidade.ativo ? 'badge-ativo' : 'badge-inativo'">
                                        <i class="fas" :class="entidade.ativo ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                        {{ entidade.ativo ? 'ATIVA' : 'INATIVA' }}
                                    </span>
                                </td>
                                <td class="table-cell">
                                    <div class="fw-medium text-success">{{ entidade.usuarios_ativos_count }}</div>
                                </td>
                                <td class="table-cell">
                                    <div class="fw-medium">{{ entidade.usuarios_total_count }}</div>
                                </td>
                                <td class="table-cell text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <!-- Botão Gerenciar Usuários -->
                                        <button class="btn btn-sm btn-primary" @click="gerenciarUsuarios(entidade)" title="Gerenciar Usuários">
                                            <i class="fas fa-users"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Estado Vazio -->
                <div v-else class="text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma entidade encontrada</h5>
                    <p class="text-muted">Não há entidades cadastradas ou que atendam aos filtros aplicados.</p>
                </div>

                <!-- Paginação -->
                <div v-if="entidades.length > 0" class="paginacao-container mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Informações de Registros -->
                        <div class="text-muted fw-medium">
                            Mostrando {{ ((registrosEntidades.current_page - 1) * registrosEntidades.per_page) + 1 }} até {{ Math.min(registrosEntidades.current_page * registrosEntidades.per_page, registrosEntidades.total) }} de {{ registrosEntidades.total }} entidades
                        </div>
                        
                        <!-- Navegação -->
                        <nav v-if="registrosEntidades.last_page > 1">
                            <ul class="pagination pagination-generic mb-0">
                                <!-- Primeira página -->
                                <li class="page-item" :class="{ 'disabled': registrosEntidades.current_page === 1 }">
                                    <button class="page-link" @click="mudarPaginaEntidades(1)" :disabled="registrosEntidades.current_page === 1">
                                        <i class="fas fa-angle-double-left"></i>
                                    </button>
                                </li>
                                
                                <!-- Página anterior -->
                                <li class="page-item" :class="{ 'disabled': registrosEntidades.current_page === 1 }">
                                    <button class="page-link" @click="mudarPaginaEntidades(registrosEntidades.current_page - 1)" :disabled="registrosEntidades.current_page === 1">
                                        <i class="fas fa-angle-left"></i>
                                    </button>
                                </li>
                                
                                <!-- Páginas numeradas -->
                                <li v-for="page in getPaginasVisiveisEntidades()" :key="page" class="page-item" :class="{ 'active': page === registrosEntidades.current_page }">
                                    <button class="page-link" @click="mudarPaginaEntidades(page)">{{ page }}</button>
                                </li>
                                
                                <!-- Próxima página -->
                                <li class="page-item" :class="{ 'disabled': registrosEntidades.current_page === registrosEntidades.last_page }">
                                    <button class="page-link" @click="mudarPaginaEntidades(registrosEntidades.current_page + 1)" :disabled="registrosEntidades.current_page === registrosEntidades.last_page">
                                        <i class="fas fa-angle-right"></i>
                                    </button>
                                </li>
                                
                                <!-- Última página -->
                                <li class="page-item" :class="{ 'disabled': registrosEntidades.current_page === registrosEntidades.last_page }">
                                    <button class="page-link" @click="mudarPaginaEntidades(registrosEntidades.last_page)" :disabled="registrosEntidades.current_page === registrosEntidades.last_page">
                                        <i class="fas fa-angle-double-right"></i>
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Gerenciar Usuários -->
        <div class="modal fade" id="modalGerenciarUsuarios" tabindex="-1" ref="modalGerenciarUsuariosRef">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <!-- Header do Modal -->
                    <div class="modal-header" style="background: linear-gradient(135deg, #18578A 0%, #5EA853 100%); color: white; border-bottom: none; padding: 1.5rem; border-radius: 0.5rem 0.5rem 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="header-icon" style="width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 1.2rem; color: white;">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5 class="modal-title mb-0">Gerenciar Usuários - {{ entidadeSelecionada?.nome_fantasia }}</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
                    </div>
                    
                    <!-- Body do Modal -->
                    <div class="modal-body" style="padding: 1.5rem; max-height: 70vh; overflow-y: auto;">
                        <!-- Informações da Entidade -->
                        <div class="mb-4 p-3 rounded" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-left: 4px solid #5EA853;">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong style="color: #18578A;">Entidade:</strong> {{ entidadeSelecionada?.nome_fantasia }}
                                </div>
                                <div class="col-md-6">
                                    <strong style="color: #18578A;">Município:</strong> {{ entidadeSelecionada?.municipio?.nome }}
                                </div>
                            </div>
                        </div>

                        <!-- Botão Vincular Usuário -->
                        <div class="mb-4">
                            <button class="btn btn-success" @click="abrirModalVincular">
                                <i class="fas fa-user-plus me-2"></i>Vincular Usuário
                            </button>
                        </div>

                        <!-- Loading State dos Usuários -->
                        <div v-if="loadingUsuarios" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando usuários...</span>
                            </div>
                        </div>

                        <!-- Tabela de Usuários da Entidade -->
                        <div v-else-if="usuariosEntidade.length > 0" class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th class="table-header">Usuário</th>
                                        <th class="table-header">Status do Usuário</th>
                                        <th class="table-header">Status do Vínculo</th>
                                        <th class="table-header">Vinculado em</th>
                                        <th class="table-header">Vinculado por</th>
                                        <th class="table-header text-end" style="width: 150px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="usuario in usuariosEntidade" :key="usuario.id" class="table-row">
                                        <td class="table-cell">
                                            <div class="fw-medium">{{ usuario.name }}</div>
                                            <small class="text-muted">{{ usuario.email }}</small>
                                        </td>
                                        <td class="table-cell">
                                            <span class="badge badge-status" :class="usuario.is_active ? 'badge-ativo' : 'badge-inativo'">
                                                <i class="fas" :class="usuario.is_active ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                                {{ usuario.is_active ? 'ATIVO' : 'INATIVO' }}
                                            </span>
                                        </td>
                                        <td class="table-cell">
                                            <span class="badge badge-status" :class="usuario.vinculo_ativo ? 'badge-ativo' : 'badge-inativo'">
                                                <i class="fas" :class="usuario.vinculo_ativo ? 'fa-link' : 'fa-unlink'"></i>
                                                {{ usuario.vinculo_ativo ? 'VINCULADO' : 'DESVINCULADO' }}
                                            </span>
                                        </td>
                                        <td class="table-cell">
                                            <div class="fw-medium">{{ formatarData(usuario.data_vinculacao) }}</div>
                                        </td>
                                        <td class="table-cell">
                                            <div class="fw-medium" v-if="usuario.vinculado_por_nome">{{ usuario.vinculado_por_nome }}</div>
                                            <small v-else class="text-muted">—</small>
                                        </td>
                                        <td class="table-cell text-end">
                                            <div class="d-flex gap-1 justify-content-end">
                                                <!-- Botão Desvincular (se vinculado) -->
                                                <button v-if="usuario.vinculo_ativo" class="btn btn-sm btn-danger" @click="desvincularUsuario(usuario)" title="Desvincular">
                                                    <i class="fas fa-unlink"></i>
                                                </button>
                                                <!-- Botão Reativar (se desvinculado) -->
                                                <button v-else class="btn btn-sm btn-success" @click="reativarVinculo(usuario)" title="Reativar Vínculo">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Estado Vazio dos Usuários -->
                        <div v-else class="text-center py-4">
                            <i class="fas fa-user-slash fa-2x text-muted mb-3"></i>
                            <h6 class="text-muted">Nenhum usuário vinculado</h6>
                            <p class="text-muted">Esta entidade ainda não possui usuários vinculados.</p>
                        </div>
                    </div>
                    
                    <!-- Footer do Modal -->
                    <div class="modal-footer" style="background: transparent; border: none; padding: 1.5rem; justify-content: center;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 0.375rem; font-weight: 500; padding: 0.75rem 1.5rem; border: none; font-size: 1rem;">
                            <i class="fas fa-times me-2"></i>Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Vincular Usuário -->
        <div class="modal fade" id="modalVincularUsuario" tabindex="-1" ref="modalVincularUsuarioRef">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <!-- Header do Modal -->
                    <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #5EA853 100%); color: white; border-bottom: none; padding: 1.5rem; border-radius: 0.5rem 0.5rem 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="header-icon" style="width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 1.2rem; color: white;">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h5 class="modal-title mb-0">Vincular Usuário</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
                    </div>
                    
                    <!-- Body do Modal -->
                    <div class="modal-body" style="padding: 1.5rem;">
                        <!-- Busca de Usuários -->
                        <div class="mb-4">
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control" 
                                       id="buscaUsuario"
                                       v-model="buscaUsuario"
                                       placeholder="Buscar usuário por nome ou email..."
                                       @input="buscarUsuariosDisponiveis">
                                <label for="buscaUsuario">Buscar usuário por nome ou email</label>
                            </div>
                        </div>

                        <!-- Loading State dos Usuários Disponíveis -->
                        <div v-if="loadingUsuariosDisponiveis" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando usuários...</span>
                            </div>
                        </div>

                        <!-- Lista de Usuários Disponíveis -->
                        <div v-else-if="usuariosDisponiveis.length > 0" style="max-height: 300px; overflow-y: auto;">
                            <div v-for="usuario in usuariosDisponiveis" :key="usuario.id" 
                                 class="d-flex justify-content-between align-items-center p-3 border rounded mb-2" 
                                 style="cursor: pointer; transition: all 0.2s ease;"
                                 :style="{ backgroundColor: usuarioSelecionado?.id === usuario.id ? '#e3f2fd' : '#f8f9fa' }"
                                 @click="selecionarUsuario(usuario)">
                                <div>
                                    <div class="fw-medium">{{ usuario.name }}</div>
                                    <small class="text-muted">{{ usuario.email }}</small>
                                </div>
                                <div>
                                    <input type="radio" 
                                           :checked="usuarioSelecionado?.id === usuario.id"
                                           class="form-check-input"
                                           style="pointer-events: none;">
                                </div>
                            </div>
                        </div>

                        <!-- Estado Vazio dos Usuários Disponíveis -->
                        <div v-else class="text-center py-4">
                            <i class="fas fa-search fa-2x text-muted mb-3"></i>
                            <h6 class="text-muted">Nenhum usuário encontrado</h6>
                            <p class="text-muted">Digite no campo de busca para encontrar usuários disponíveis para vinculação.</p>
                        </div>
                    </div>
                    
                    <!-- Footer do Modal -->
                    <div class="modal-footer" style="background: transparent; border: none; padding: 1.5rem; justify-content: center;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 0.375rem; font-weight: 500; padding: 0.75rem 1.5rem; border: none; font-size: 1rem;">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" @click="confirmarVinculacao" :disabled="!usuarioSelecionado || vinculando" style="border-radius: 0.375rem; font-weight: 500; padding: 0.75rem 1.5rem; border: none; font-size: 1rem;">
                            <span v-if="vinculando" class="spinner-border spinner-border-sm me-2"></span>
                            <i v-else class="fas fa-link me-2"></i>
                            {{ vinculando ? 'Vinculando...' : 'Vincular Usuário' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast de Notificação -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div class="toast" ref="toastRef" role="alert">
            <div class="toast-header">
                <i :class="toastIcon + ' me-2'"></i>
                <strong class="me-auto">{{ toastTitle }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">{{ toastMessage }}</div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ListaUsuariosPorEntidade',
    data() {
        return {
            loading: true,
            entidades: [],
            municipios: [],
            niveisAdministrativos: [],
            tiposOrganizacao: [],
            registrosEntidades: {
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 0
            },
            filtros: {
                busca: '',
                municipio_id: '',
                nivel_administrativo: '',
                tipo_organizacao: '',
                ativo: ''
            },
            filtrosVisiveis: false,
            searchTimeout: null,
            
            // Modal de gerenciar usuários
            entidadeSelecionada: null,
            usuariosEntidade: [],
            loadingUsuarios: false,
            
            // Modal de vincular usuário
            usuariosDisponiveis: [],
            loadingUsuariosDisponiveis: false,
            buscaUsuario: '',
            usuarioSelecionado: null,
            vinculando: false,
            
            // Toast
            toastTitle: '',
            toastMessage: '',
            toastIcon: ''
        }
    },
    watch: {
        'filtros.municipio_id'() {
            this.filtrarEntidades();
        },
        'filtros.nivel_administrativo'() {
            this.filtrarEntidades();
        },
        'filtros.tipo_organizacao'() {
            this.filtrarEntidades();
        },
        'filtros.ativo'() {
            this.filtrarEntidades();
        }
    },
    mounted() {
        this.carregarFiltros();
        this.carregarEntidades();
    },
    methods: {
        async carregarEntidades() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                params.append('page', this.registrosEntidades.current_page);
                params.append('per_page', this.registrosEntidades.per_page);
                
                if (this.filtros.busca && this.filtros.busca.toString().trim()) {
                    params.append('busca', this.filtros.busca.toString().trim());
                }
                if (this.filtros.municipio_id) {
                    params.append('municipio_id', this.filtros.municipio_id);
                }
                if (this.filtros.nivel_administrativo) {
                    params.append('nivel_administrativo', this.filtros.nivel_administrativo);
                }
                if (this.filtros.tipo_organizacao) {
                    params.append('tipo_organizacao', this.filtros.tipo_organizacao);
                }
                if (this.filtros.ativo) {
                    params.append('ativo', this.filtros.ativo);
                }

                const response = await fetch(`/api/administracao/usuarios-por-entidade/entidades?${params}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.entidades = data.data || [];
                    this.registrosEntidades = {
                        current_page: data.current_page || 1,
                        last_page: data.last_page || 1,
                        per_page: data.per_page || 15,
                        total: data.total || 0
                    };
                } else {
                    console.error('Erro ao carregar entidades:', data.message);
                    this.mostrarToast('Erro', 'Erro ao carregar entidades', 'fa-exclamation-circle text-danger');
                }
                
            } catch (error) {
                console.error('Erro ao carregar entidades:', error);
                this.mostrarToast('Erro', 'Erro ao carregar entidades', 'fa-exclamation-circle text-danger');
            } finally {
                this.loading = false;
            }
        },
        
        async carregarFiltros() {
            try {
                const response = await fetch('/api/administracao/usuarios-por-entidade/filtros', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.municipios = data.municipios || [];
                    this.niveisAdministrativos = data.niveis_administrativos || [];
                    this.tiposOrganizacao = data.tipos_organizacao || [];
                } else {
                    console.error('Erro ao carregar filtros:', data.message);
                }
                
            } catch (error) {
                console.error('Erro ao carregar filtros:', error);
            }
        },
        
        // Filtros
        toggleFiltros() {
            this.filtrosVisiveis = !this.filtrosVisiveis;
        },
        
        onBuscaInput() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            
            this.searchTimeout = setTimeout(() => {
                this.filtrarEntidades();
            }, 500);
        },
        
        filtrarEntidades() {
            this.registrosEntidades.current_page = 1;
            this.carregarEntidades();
        },
        
        limparFiltros() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = null;
            }
            
            this.filtros = {
                busca: '',
                municipio_id: '',
                nivel_administrativo: '',
                tipo_organizacao: '',
                ativo: ''
            };
            this.filtrarEntidades();
        },
        
        // Paginação
        mudarPaginaEntidades(page) {
            if (page >= 1 && page <= this.registrosEntidades.last_page) {
                this.registrosEntidades.current_page = page;
                this.carregarEntidades();
            }
        },
        
        getPaginasVisiveisEntidades() {
            const pages = [];
            const start = Math.max(1, this.registrosEntidades.current_page - 2);
            const end = Math.min(this.registrosEntidades.last_page, this.registrosEntidades.current_page + 2);
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },
        
        // Gerenciar usuários
        async gerenciarUsuarios(entidade) {
            this.entidadeSelecionada = entidade;
            this.carregarUsuariosEntidade();
            
            const modal = new bootstrap.Modal(document.getElementById('modalGerenciarUsuarios'));
            modal.show();
        },
        
        async carregarUsuariosEntidade() {
            this.loadingUsuarios = true;
            try {
                const response = await fetch(`/api/administracao/usuarios-por-entidade/${this.entidadeSelecionada.id}/usuarios`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.usuariosEntidade = data.usuarios.data || [];
                } else {
                    console.error('Erro ao carregar usuários:', data.message);
                    this.mostrarToast('Erro', 'Erro ao carregar usuários da entidade', 'fa-exclamation-circle text-danger');
                }
                
            } catch (error) {
                console.error('Erro ao carregar usuários:', error);
                this.mostrarToast('Erro', 'Erro ao carregar usuários da entidade', 'fa-exclamation-circle text-danger');
            } finally {
                this.loadingUsuarios = false;
            }
        },
        
        // Vincular usuário
        abrirModalVincular() {
            this.buscaUsuario = '';
            this.usuarioSelecionado = null;
            this.usuariosDisponiveis = [];
            
            const modal = new bootstrap.Modal(document.getElementById('modalVincularUsuario'));
            modal.show();
        },
        
        async buscarUsuariosDisponiveis() {
            if (!this.buscaUsuario || this.buscaUsuario.trim().length < 2) {
                this.usuariosDisponiveis = [];
                return;
            }
            
            this.loadingUsuariosDisponiveis = true;
            try {
                const params = new URLSearchParams();
                params.append('busca', this.buscaUsuario.trim());
                
                const response = await fetch(`/api/administracao/usuarios-por-entidade/${this.entidadeSelecionada.id}/usuarios-disponiveis?${params}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.usuariosDisponiveis = data.data || [];
                } else {
                    console.error('Erro ao buscar usuários:', data.message);
                }
                
            } catch (error) {
                console.error('Erro ao buscar usuários:', error);
            } finally {
                this.loadingUsuariosDisponiveis = false;
            }
        },
        
        selecionarUsuario(usuario) {
            this.usuarioSelecionado = usuario;
        },
        
        async confirmarVinculacao() {
            if (!this.usuarioSelecionado) return;
            
            this.vinculando = true;
            try {
                const response = await fetch(`/api/administracao/usuarios-por-entidade/${this.entidadeSelecionada.id}/vincular`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: this.usuarioSelecionado.id
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.mostrarToast('Sucesso', data.message, 'fa-check-circle text-success');
                    
                    // Fechar modal
                    bootstrap.Modal.getInstance(document.getElementById('modalVincularUsuario')).hide();
                    
                    // Recarregar usuários da entidade
                    this.carregarUsuariosEntidade();
                    
                    // Recarregar entidades para atualizar contadores
                    this.carregarEntidades();
                } else {
                    this.mostrarToast('Erro', data.message || 'Erro ao vincular usuário', 'fa-exclamation-circle text-danger');
                }
                
            } catch (error) {
                console.error('Erro ao vincular usuário:', error);
                this.mostrarToast('Erro', 'Erro ao vincular usuário', 'fa-exclamation-circle text-danger');
            } finally {
                this.vinculando = false;
            }
        },
        
        // Desvincular usuário
        async desvincularUsuario(usuario) {
            if (!confirm(`Tem certeza que deseja desvincular o usuário "${usuario.name}" desta entidade?`)) {
                return;
            }
            
            try {
                const response = await fetch(`/api/administracao/usuarios-por-entidade/${this.entidadeSelecionada.id}/desvincular/${usuario.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.mostrarToast('Sucesso', data.message, 'fa-check-circle text-success');
                    
                    // Recarregar usuários da entidade
                    this.carregarUsuariosEntidade();
                    
                    // Recarregar entidades para atualizar contadores
                    this.carregarEntidades();
                } else {
                    this.mostrarToast('Erro', data.message || 'Erro ao desvincular usuário', 'fa-exclamation-circle text-danger');
                }
                
            } catch (error) {
                console.error('Erro ao desvincular usuário:', error);
                this.mostrarToast('Erro', 'Erro ao desvincular usuário', 'fa-exclamation-circle text-danger');
            }
        },
        
        // Reativar vínculo
        async reativarVinculo(usuario) {
            if (!confirm(`Tem certeza que deseja reativar o vínculo do usuário "${usuario.name}" com esta entidade?`)) {
                return;
            }
            
            try {
                const response = await fetch(`/api/administracao/usuarios-por-entidade/${this.entidadeSelecionada.id}/reativar/${usuario.id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.mostrarToast('Sucesso', data.message, 'fa-check-circle text-success');
                    
                    // Recarregar usuários da entidade
                    this.carregarUsuariosEntidade();
                    
                    // Recarregar entidades para atualizar contadores
                    this.carregarEntidades();
                } else {
                    this.mostrarToast('Erro', data.message || 'Erro ao reativar vínculo', 'fa-exclamation-circle text-danger');
                }
                
            } catch (error) {
                console.error('Erro ao reativar vínculo:', error);
                this.mostrarToast('Erro', 'Erro ao reativar vínculo', 'fa-exclamation-circle text-danger');
            }
        },
        
        // Utilitários
        formatarData(data) {
            if (!data) return '';
            
            const date = new Date(data);
            return date.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        },
        
        mostrarToast(title, message, icon) {
            this.toastTitle = title;
            this.toastMessage = message;
            this.toastIcon = icon;
            
            const toast = new bootstrap.Toast(this.$refs.toastRef);
            toast.show();
        },

        // Métodos de formatação
        formatarTipo(tipo) {
            const tipos = {
                'municipio': 'MUNICÍPIO',
                'secretaria': 'SECRETARIA',
                'órgão': 'ÓRGÃO',
                'autarquia': 'AUTARQUIA',
                'outros': 'OUTROS'
            };
            return tipos[tipo] || tipo?.toUpperCase();
        },

        formatarNivel(nivel) {
            const niveis = {
                'municipal': 'MUNICIPAL',
                'estadual': 'ESTADUAL',
                'federal': 'FEDERAL'
            };
            return niveis[nivel] || nivel?.toUpperCase();
        },

        formatarJurisdicao(entidade) {
            if (entidade.nivel_administrativo === 'municipal' && entidade.municipio) {
                return entidade.municipio.nome + ' - PR';
            }
            return entidade.jurisdicao_nome || '—';
        },

        getTipoClass(tipo) {
            const classes = {
                'municipio': 'badge-primary',
                'secretaria': 'badge-info',
                'órgão': 'badge-warning',
                'autarquia': 'badge-success',
                'outros': 'badge-secondary'
            };
            return classes[tipo] || 'badge-secondary';
        }
    }
}
</script>

<style scoped>
/* Estilos específicos do componente */
.table-header {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #18578A;
    padding: 12px;
}

.table-cell {
    padding: 12px;
    vertical-align: middle;
}

.table-row:hover {
    background-color: #f8f9fa;
}

.badge-status {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
    border-radius: 0.375rem;
}

.badge-ativo {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.badge-inativo {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.filtros-aba-container {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #e9ecef;
}

.pagination-generic .page-link {
    color: #18578A;
    border-color: #dee2e6;
}

.pagination-generic .page-item.active .page-link {
    background-color: #18578A;
    border-color: #18578A;
    color: white;
}

.pagination-generic .page-link:hover {
    color: #5EA853;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.paginacao-container {
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
}

/* Badge colors for entity types */
.badge-primary {
    background-color: #0d6efd;
    color: white;
}

.badge-info {
    background-color: #0dcaf0;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: black;
}

.badge-success {
    background-color: #198754;
    color: white;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}
</style>