<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho Compacto -->
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                    <i class="fas fa-user me-2"></i>Meu Perfil
                </h6>
            </div>

            <!-- Corpo -->
            <div class="card-body">
                <!-- Sistema de Abas -->
                <div class="admin-tabs-container">
                    <!-- Navegação das Abas -->
                    <ul class="nav nav-tabs admin-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link admin-tab" 
                                    :class="{ active: abaAtiva === 'dados-pessoais' }"
                                    @click="changeTab('dados-pessoais')"
                                    type="button"
                                    role="tab">
                                <i class="fas fa-user me-2"></i>
                                Dados Pessoais
                            </button>
                        </li>
                        
                        <li class="nav-item" role="presentation">
                            <button class="nav-link admin-tab" 
                                    :class="{ active: abaAtiva === 'vinculos' }"
                                    @click="changeTab('vinculos')"
                                    type="button"
                                    role="tab">
                                <i class="fas fa-building me-2"></i>
                                Vínculos Organizacionais
                            </button>
                        </li>
                        
                        <li class="nav-item" role="presentation">
                            <button class="nav-link admin-tab" 
                                    :class="{ active: abaAtiva === 'permissoes' }"
                                    @click="changeTab('permissoes')"
                                    type="button"
                                    role="tab">
                                <i class="fas fa-shield-alt me-2"></i>
                                Permissões e Papéis
                            </button>
                        </li>
                        
                        <li class="nav-item" role="presentation">
                            <button class="nav-link admin-tab" 
                                    :class="{ active: abaAtiva === 'seguranca' }"
                                    @click="changeTab('seguranca')"
                                    type="button"
                                    role="tab">
                                <i class="fas fa-lock me-2"></i>
                                Segurança
                            </button>
                        </li>
                    </ul>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content admin-tab-content">
                    
                        <!-- ABA: DADOS PESSOAIS -->
                        <div class="tab-pane fade" 
                             :class="{ 'show active': abaAtiva === 'dados-pessoais' }" 
                             role="tabpanel">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="fw-semibold mb-3 text-custom">
                                    <i class="fas fa-user-edit me-2"></i>Informações Pessoais
                                </h6>
                                
                                <form @submit.prevent="salvarDadosPessoais">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    :class="{ 'is-invalid': errors.name }"
                                                    id="nome" 
                                                    v-model="formDados.name"
                                                    placeholder="Nome Completo"
                                                    :disabled="salvandoDados"
                                                    required>
                                                <label for="nome">Nome Completo *</label>
                                            </div>
                                            <div class="invalid-feedback" v-if="errors.name">
                                                {{ errors.name[0] }}
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input 
                                                    type="email" 
                                                    class="form-control" 
                                                    :class="{ 'is-invalid': errors.email }"
                                                    id="email" 
                                                    v-model="formDados.email"
                                                    placeholder="Email"
                                                    :disabled="salvandoDados"
                                                    required>
                                                <label for="email">Email *</label>
                                            </div>
                                            <div class="invalid-feedback" v-if="errors.email">
                                                {{ errors.email[0] }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button 
                                            type="submit" 
                                            class="btn btn-success"
                                            :disabled="salvandoDados || !dadosAlterados">
                                            <span v-if="salvandoDados" class="spinner-border spinner-border-sm me-2"></span>
                                            <i v-else class="fas fa-save me-2"></i>
                                            {{ salvandoDados ? 'Salvando...' : 'Salvar Alterações' }}
                                        </button>
                                        
                                        <button 
                                            type="button" 
                                            class="btn btn-outline-secondary"
                                            @click="cancelarEdicaoDados"
                                            :disabled="salvandoDados || !dadosAlterados">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="fw-semibold mb-3" style="color: #6c757d;">
                                            <i class="fas fa-info-circle me-2"></i>Informações da Conta
                                        </h6>
                                        
                                        <div class="mb-2">
                                            <small class="text-muted">Status da Conta:</small>
                                            <div>
                                                <span v-if="dados.dados_pessoais?.is_active" class="badge badge-status badge-ativo">
                                                    <i class="fas fa-check-circle"></i> ATIVO
                                                </span>
                                                <span v-else class="badge badge-status badge-inativo">
                                                    <i class="fas fa-times-circle"></i> INATIVO
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <small class="text-muted">Data de Cadastro:</small>
                                            <div class="fw-medium">{{ formatarData(dados.dados_pessoais?.created_at) }}</div>
                                        </div>
                                        
                                        <div>
                                            <small class="text-muted">Última Atualização:</small>
                                            <div class="fw-medium">{{ formatarData(dados.dados_pessoais?.updated_at) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                        <!-- ABA: VÍNCULOS ORGANIZACIONAIS -->
                        <div class="tab-pane fade" 
                             :class="{ 'show active': abaAtiva === 'vinculos' }" 
                             role="tabpanel">
                        <div class="row">
                            <!-- Vínculos Ativos -->
                            <div class="col-12 mb-4">
                                <h6 class="fw-semibold mb-3 text-custom">
                                    <i class="fas fa-building me-2"></i>Entidades Vinculadas ({{ dados.vinculos_ativos?.length || 0 }})
                                </h6>
                                
                                <div v-if="dados.vinculos_ativos && dados.vinculos_ativos.length > 0" class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th class="fw-semibold text-custom">Entidade</th>
                                                <th class="fw-semibold text-custom">Tipo</th>
                                                <th class="fw-semibold text-custom">Nível</th>
                                                <th class="fw-semibold text-custom">UF</th>
                                                <th class="fw-semibold text-custom">Data Vinculação</th>
                                                <th class="fw-semibold text-custom">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="vinculo in dados.vinculos_ativos" :key="vinculo.id">
                                                <td>
                                                    <div class="fw-medium">{{ vinculo.nome_entidade }}</div>
                                                    <small class="text-muted" v-if="vinculo.razao_social && vinculo.razao_social !== vinculo.nome_entidade">
                                                        {{ vinculo.razao_social }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ vinculo.tipo_organizacao }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ formatarNivelAdministrativo(vinculo.nivel_administrativo) }}</span>
                                                </td>
                                                <td class="fw-medium">{{ vinculo.uf || '—' }}</td>
                                                <td>{{ formatarData(vinculo.data_vinculacao) }}</td>
                                                <td>
                                                    <span class="badge badge-status badge-ativo">
                                                        <i class="fas fa-check-circle"></i> {{ vinculo.status_vinculo }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div v-else class="text-center py-4">
                                    <i class="fas fa-building text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-3 mb-0">Você não possui vínculos ativos com entidades orçamentárias.</p>
                                </div>
                            </div>
                            
                            <!-- Histórico Expandível -->
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-header bg-transparent border-0 py-2">
                                        <button 
                                            class="btn btn-link text-decoration-none p-0 fw-semibold text-custom"
                                            @click="mostrarHistorico = !mostrarHistorico">
                                            <i class="fas" :class="mostrarHistorico ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                            Histórico Completo de Vínculos ({{ dados.historico_vinculos?.length || 0 }})
                                        </button>
                                    </div>
                                    
                                    <div v-if="mostrarHistorico" class="card-body">
                                        <div v-if="dados.historico_vinculos && dados.historico_vinculos.length > 0" class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th class="fw-semibold text-custom">Entidade</th>
                                                        <th class="fw-semibold text-custom">Tipo</th>
                                                        <th class="fw-semibold text-custom">Data Vinculação</th>
                                                        <th class="fw-semibold text-custom">Última Alteração</th>
                                                        <th class="fw-semibold text-custom">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="vinculo in dados.historico_vinculos" :key="`historico-${vinculo.id}`">
                                                        <td>
                                                            <div class="fw-medium">{{ vinculo.nome_entidade }}</div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary">{{ vinculo.tipo_organizacao }}</span>
                                                        </td>
                                                        <td>{{ formatarData(vinculo.data_vinculacao) }}</td>
                                                        <td>{{ formatarData(vinculo.data_alteracao) }}</td>
                                                        <td>
                                                            <span v-if="vinculo.status_vinculo === 'Ativo'" class="badge badge-status badge-ativo">
                                                                <i class="fas fa-check-circle"></i> {{ vinculo.status_vinculo }}
                                                            </span>
                                                            <span v-else class="badge badge-status badge-inativo">
                                                                <i class="fas fa-times-circle"></i> {{ vinculo.status_vinculo }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <div v-else class="text-center py-3">
                                            <p class="text-muted mb-0">Nenhum histórico de vínculos encontrado.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                        <!-- ABA: PERMISSÕES E PAPÉIS -->
                        <div class="tab-pane fade" 
                             :class="{ 'show active': abaAtiva === 'permissoes' }" 
                             role="tabpanel">
                        <div class="row">
                            <!-- Papéis Atribuídos -->
                            <div class="col-md-6 mb-4">
                                <h6 class="fw-semibold mb-3 text-custom">
                                    <i class="fas fa-user-tag me-2"></i>Papéis Atribuídos ({{ dados.papeis_atribuidos?.length || 0 }})
                                </h6>
                                
                                <div v-if="dados.papeis_atribuidos && dados.papeis_atribuidos.length > 0">
                                    <div v-for="papel in dados.papeis_atribuidos" :key="papel.id" class="card border-0 bg-light mb-3">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="fw-semibold mb-1" style="color: #5EA853;">{{ papel.nome_papel }}</h6>
                                                    <p class="text-muted mb-2 small">{{ papel.descricao || 'Sem descrição disponível' }}</p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar-plus me-1"></i>
                                                        Atribuído em: {{ formatarData(papel.data_atribuicao) }}
                                                    </small>
                                                </div>
                                                <span class="badge bg-success">{{ papel.name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-else class="text-center py-4">
                                    <i class="fas fa-user-tag text-muted" style="font-size: 2rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0">Nenhum papel atribuído.</p>
                                </div>
                            </div>
                            
                            <!-- Permissões Específicas -->
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-3 text-custom">
                                    <i class="fas fa-shield-alt me-2"></i>Permissões Específicas
                                </h6>
                                
                                <div v-if="dados.permissoes_especificas && dados.permissoes_especificas.length > 0">
                                    <div v-for="grupo in dados.permissoes_especificas" :key="grupo.modulo" class="mb-4">
                                        <h6 class="fw-medium mb-2" style="color: #6c757d;">
                                            <i class="fas fa-cube me-2"></i>{{ grupo.modulo }}
                                        </h6>
                                        
                                        <div class="list-group list-group-flush">
                                            <div v-for="permissao in grupo.permissoes" :key="permissao.id" 
                                                 class="list-group-item border-0 bg-transparent px-0 py-1">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-medium small">{{ permissao.nome_permissao }}</div>
                                                        <small class="text-muted">{{ permissao.descricao || permissao.name }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-else class="text-center py-4">
                                    <i class="fas fa-shield-alt text-muted" style="font-size: 2rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0">Nenhuma permissão específica encontrada.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                        <!-- ABA: SEGURANÇA -->
                        <div class="tab-pane fade" 
                             :class="{ 'show active': abaAtiva === 'seguranca' }" 
                             role="tabpanel">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="fw-semibold mb-3 text-custom">
                                    <i class="fas fa-key me-2"></i>Alterar Senha
                                </h6>
                                
                                <form @submit.prevent="alterarSenha">
                                    <div class="row mb-3">
                                        <div class="col-12 mb-3">
                                            <div class="form-floating">
                                                <input 
                                                    type="password" 
                                                    class="form-control" 
                                                    :class="{ 'is-invalid': errors.senha_atual }"
                                                    id="senhaAtual" 
                                                    v-model="formSenha.senha_atual"
                                                    placeholder="Senha Atual"
                                                    :disabled="alterandoSenha"
                                                    required>
                                                <label for="senhaAtual">Senha Atual *</label>
                                            </div>
                                            <div class="invalid-feedback" v-if="errors.senha_atual">
                                                {{ errors.senha_atual[0] }}
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <div class="form-floating">
                                                <input 
                                                    type="password" 
                                                    class="form-control" 
                                                    :class="{ 'is-invalid': errors.nova_senha }"
                                                    id="novaSenha" 
                                                    v-model="formSenha.nova_senha"
                                                    placeholder="Nova Senha"
                                                    :disabled="alterandoSenha"
                                                    minlength="8"
                                                    required>
                                                <label for="novaSenha">Nova Senha *</label>
                                            </div>
                                            <div class="invalid-feedback" v-if="errors.nova_senha">
                                                {{ errors.nova_senha[0] }}
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <div class="form-floating">
                                                <input 
                                                    type="password" 
                                                    class="form-control" 
                                                    :class="{ 'is-invalid': errors.nova_senha_confirmation }"
                                                    id="confirmarSenha" 
                                                    v-model="formSenha.nova_senha_confirmation"
                                                    placeholder="Confirmar Nova Senha"
                                                    :disabled="alterandoSenha"
                                                    minlength="8"
                                                    required>
                                                <label for="confirmarSenha">Confirmar Nova Senha *</label>
                                            </div>
                                            <div class="invalid-feedback" v-if="errors.nova_senha_confirmation">
                                                {{ errors.nova_senha_confirmation[0] }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button 
                                            type="submit" 
                                            class="btn btn-warning"
                                            :disabled="alterandoSenha || !senhaFormPreenchido">
                                            <span v-if="alterandoSenha" class="spinner-border spinner-border-sm me-2"></span>
                                            <i v-else class="fas fa-key me-2"></i>
                                            {{ alterandoSenha ? 'Alterando...' : 'Alterar Senha' }}
                                        </button>
                                        
                                        <button 
                                            type="button" 
                                            class="btn btn-outline-secondary"
                                            @click="cancelarEdicaoSenha"
                                            :disabled="alterandoSenha">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="fw-semibold mb-3" style="color: #6c757d;">
                                            <i class="fas fa-info-circle me-2"></i>Dicas de Segurança
                                        </h6>
                                        
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <small>Use pelo menos 8 caracteres</small>
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <small>Combine letras, números e símbolos</small>
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <small>Evite informações pessoais</small>
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <small>Use senhas diferentes para cada conta</small>
                                            </li>
                                        </ul>
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

    <!-- Toast de Notificação -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div ref="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-user text-primary me-2"></i>
                <strong class="me-auto">Meu Perfil</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ toastMessage }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ListaMeuPerfil',
    props: {
        permissoes: {
            type: Object,
            default: () => ({
                consultar: false,
                editar_dados: false,
                alterar_senha: false
            })
        }
    },
    data() {
        return {
            abaAtiva: 'dados-pessoais',
            carregando: false,
            mostrarHistorico: false,
            
            // Dados do perfil
            dados: {
                dados_pessoais: {},
                vinculos_ativos: [],
                historico_vinculos: [],
                papeis_atribuidos: [],
                permissoes_especificas: []
            },
            
            // Formulário de dados pessoais
            formDados: {
                name: '',
                email: ''
            },
            dadosOriginais: {},
            salvandoDados: false,
            
            // Formulário de senha
            formSenha: {
                senha_atual: '',
                nova_senha: '',
                nova_senha_confirmation: ''
            },
            alterandoSenha: false,
            
            // Controle de erros
            errors: {},
            
            // Toast
            toastMessage: ''
        };
    },
    computed: {
        dadosAlterados() {
            return this.formDados.name !== this.dadosOriginais.name || 
                   this.formDados.email !== this.dadosOriginais.email;
        },
        
        senhaFormPreenchido() {
            return this.formSenha.senha_atual && 
                   this.formSenha.nova_senha && 
                   this.formSenha.nova_senha_confirmation;
        }
    },
    mounted() {
        this.carregarDados();
    },
    methods: {
        async carregarDados() {
            this.carregando = true;
            this.errors = {};
            
            try {
                const response = await fetch('/api/perfil/meu-perfil', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.dados = data.dados;
                    
                    // Preencher formulário de dados pessoais
                    this.formDados.name = data.dados.dados_pessoais.name || '';
                    this.formDados.email = data.dados.dados_pessoais.email || '';
                    
                    // Guardar dados originais para comparação
                    this.dadosOriginais = { ...this.formDados };
                } else {
                    this.mostrarToast('Erro ao carregar dados do perfil: ' + data.message);
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                this.mostrarToast('Erro de conexão ao carregar dados do perfil.');
            } finally {
                this.carregando = false;
            }
        },
        
        async salvarDadosPessoais() {
            this.salvandoDados = true;
            this.errors = {};
            
            try {
                const response = await fetch('/api/perfil/meu-perfil/dados-pessoais', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formDados)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Atualizar dados locais
                    this.dados.dados_pessoais.name = data.dados.name;
                    this.dados.dados_pessoais.email = data.dados.email;
                    this.dados.dados_pessoais.updated_at = data.dados.updated_at;
                    
                    // Atualizar dados originais
                    this.dadosOriginais = { ...this.formDados };
                    
                    this.mostrarToast('Dados pessoais atualizados com sucesso!');
                } else {
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    this.mostrarToast('Erro: ' + data.message);
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                this.mostrarToast('Erro de conexão ao salvar dados.');
            } finally {
                this.salvandoDados = false;
            }
        },
        
        cancelarEdicaoDados() {
            this.formDados = { ...this.dadosOriginais };
            this.errors = {};
        },
        
        async alterarSenha() {
            this.alterandoSenha = true;
            this.errors = {};
            
            try {
                const response = await fetch('/api/perfil/meu-perfil/senha', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formSenha)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Limpar formulário
                    this.formSenha = {
                        senha_atual: '',
                        nova_senha: '',
                        nova_senha_confirmation: ''
                    };
                    
                    this.mostrarToast('Senha alterada com sucesso!');
                } else {
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    this.mostrarToast('Erro: ' + data.message);
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                this.mostrarToast('Erro de conexão ao alterar senha.');
            } finally {
                this.alterandoSenha = false;
            }
        },
        
        cancelarEdicaoSenha() {
            this.formSenha = {
                senha_atual: '',
                nova_senha: '',
                nova_senha_confirmation: ''
            };
            this.errors = {};
        },
        
        changeTab(novaAba) {
            this.abaAtiva = novaAba;
            // Limpar erros ao trocar de aba
            this.errors = {};
        },
        
        formatarData(data) {
            if (!data) return '—';
            return new Date(data).toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        formatarNivelAdministrativo(nivel) {
            const niveis = {
                'municipal': 'Municipal',
                'estadual': 'Estadual',
                'federal': 'Federal'
            };
            return niveis[nivel] || nivel;
        },
        
        mostrarToast(message) {
            this.toastMessage = message;
            
            this.$nextTick(() => {
                const toastElement = this.$refs.toast;
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            });
        }
    }
};
</script>


