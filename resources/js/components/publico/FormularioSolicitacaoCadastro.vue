<template>
    <div>
        <!-- Formulário de Solicitação -->
        <form @submit.prevent="enviarSolicitacao" v-if="!solicitacaoEnviada">
            <div class="row g-3">
                <!-- Dados Pessoais -->
                <div class="col-12">
                    <h6 class="text-custom mb-3">
                        <i class="fas fa-user me-2"></i>Dados Pessoais
                    </h6>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control" 
                               :class="{ 'is-invalid': errors.name }"
                               id="name" 
                               v-model="form.name"
                               placeholder="Nome completo"
                               required>
                        <label for="name">Nome Completo *</label>
                    </div>
                    <div class="invalid-feedback" v-if="errors.name">
                        {{ errors.name[0] }}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="email" 
                               class="form-control" 
                               :class="{ 'is-invalid': errors.email }"
                               id="email" 
                               v-model="form.email"
                               placeholder="Email"
                               required>
                        <label for="email">Email *</label>
                    </div>
                    <div class="invalid-feedback" v-if="errors.email">
                        {{ errors.email[0] }}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control" 
                               :class="{ 'is-invalid': errors.username }"
                               id="username" 
                               v-model="form.username"
                               placeholder="Nome de usuário (opcional)">
                        <label for="username">Nome de Usuário (opcional)</label>
                    </div>
                    <div class="invalid-feedback" v-if="errors.username">
                        {{ errors.username[0] }}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control"
                               id="telefone" 
                               v-model="form.telefone"
                               placeholder="Telefone (opcional)"
                               maxlength="20">
                        <label for="telefone">Telefone (opcional)</label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control"
                               id="cpf" 
                               v-model="form.cpf"
                               placeholder="CPF (opcional)"
                               maxlength="14">
                        <label for="cpf">CPF (opcional)</label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control"
                               id="cargo" 
                               v-model="form.cargo"
                               placeholder="Cargo/Função (opcional)"
                               maxlength="255">
                        <label for="cargo">Cargo/Função (opcional)</label>
                    </div>
                </div>
                
                <!-- Dados de Acesso -->
                <div class="col-12 mt-4">
                    <h6 class="text-custom mb-3">
                        <i class="fas fa-lock me-2"></i>Dados de Acesso
                    </h6>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="password" 
                               class="form-control" 
                               :class="{ 'is-invalid': errors.password }"
                               id="password" 
                               v-model="form.password"
                               placeholder="Senha"
                               required>
                        <label for="password">Senha *</label>
                    </div>
                    <div class="invalid-feedback" v-if="errors.password">
                        {{ errors.password[0] }}
                    </div>
                    <div class="form-text">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Mínimo 8 caracteres, incluindo maiúsculas, minúsculas e números
                        </small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="password" 
                               class="form-control" 
                               :class="{ 'is-invalid': errors.password }"
                               id="password_confirmation" 
                               v-model="form.password_confirmation"
                               placeholder="Confirmar senha"
                               required>
                        <label for="password_confirmation">Confirmar Senha *</label>
                    </div>
                </div>
                
                <!-- Sua Localização -->
                <div class="col-12 mt-4">
                    <h6 class="text-custom mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>Sua Localização
                    </h6>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control" 
                               :class="{ 'is-invalid': errors.visitante_municipio }"
                               id="visitante_municipio" 
                               v-model="form.visitante_municipio"
                               placeholder="Seu município"
                               required>
                        <label for="visitante_municipio">Seu Município *</label>
                    </div>
                    <div class="invalid-feedback" v-if="errors.visitante_municipio">
                        {{ errors.visitante_municipio[0] }}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control" 
                               :class="{ 'is-invalid': errors.visitante_uf }"
                               id="visitante_uf" 
                               v-model="form.visitante_uf"
                               placeholder="Sua UF"
                               maxlength="2"
                               style="text-transform: uppercase"
                               required>
                        <label for="visitante_uf">Sua UF *</label>
                    </div>
                    <div class="invalid-feedback" v-if="errors.visitante_uf">
                        {{ errors.visitante_uf[0] }}
                    </div>
                </div>
                
                <!-- Entidade Solicitada -->
                <div class="col-12 mt-4">
                    <h6 class="text-custom mb-3">
                        <i class="fas fa-building me-2"></i>Entidade Orçamentária Solicitada
                    </h6>
                </div>
                
                <div class="col-12">
                    <div class="form-floating">
                        <select class="form-control" 
                                :class="{ 'is-invalid': errors.entidade_orcamentaria_id }"
                                id="entidade_orcamentaria_id" 
                                v-model="form.entidade_orcamentaria_id"
                                required>
                            <option value="">Selecione uma entidade orçamentária...</option>
                            <option v-for="entidade in entidades" :key="entidade.id" :value="entidade.id">
                                {{ entidade.nome }} ({{ entidade.tipo_organizacao }} - {{ entidade.nivel_administrativo }})
                            </option>
                        </select>
                        <label for="entidade_orcamentaria_id">Entidade Orçamentária *</label>
                    </div>
                    <div class="invalid-feedback" v-if="errors.entidade_orcamentaria_id">
                        {{ errors.entidade_orcamentaria_id[0] }}
                    </div>
                    <div class="form-text">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Escolha a entidade orçamentária para a qual você precisa de acesso no sistema
                        </small>
                    </div>
                </div>
                
                <!-- Justificativa -->
                <div class="col-12 mt-3">
                    <div class="form-floating">
                        <textarea class="form-control" 
                                  :class="{ 'is-invalid': errors.justificativa }"
                                  id="justificativa" 
                                  v-model="form.justificativa"
                                  placeholder="Justifique sua necessidade de acesso ao sistema..."
                                  style="height: 120px;"
                                  required
                                  minlength="20"
                                  maxlength="1000"></textarea>
                        <label for="justificativa">Justificativa para Acesso ao Sistema *</label>
                    </div>
                    <div class="invalid-feedback" v-if="errors.justificativa">
                        {{ errors.justificativa[0] }}
                    </div>
                    <div class="form-text">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Explique por que você precisa acessar o OrçaCidade e como vai utilizá-lo (mínimo 20 caracteres)
                        </small>
                    </div>
                </div>
                
                <!-- Botões -->
                <div class="col-12 mt-4 d-grid">
                    <button type="submit" class="btn btn-gradient btn-lg" :disabled="enviando">
                        <span v-if="enviando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        <i v-else class="fas fa-paper-plane me-2"></i>
                        {{ enviando ? 'Enviando Solicitação...' : 'Enviar Solicitação' }}
                    </button>
                </div>
                
                <!-- Links úteis -->
                <div class="col-12 mt-3 text-center">
                    <hr class="my-3">
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-link text-decoration-none" @click="mostrarConsultaStatus">
                            <i class="fas fa-search me-1"></i>Consultar Status
                        </button>
                        <a href="/login" class="btn btn-link text-decoration-none">
                            <i class="fas fa-sign-in-alt me-1"></i>Já tenho acesso
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Sucesso -->
        <div v-else class="text-center">
            <div class="alert alert-success border-0 rounded-3">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h4 class="alert-heading">Solicitação Enviada com Sucesso!</h4>
                <p class="mb-3">{{ mensagemSucesso }}</p>
                <hr>
                <p class="mb-0">
                    <strong>ID da Solicitação:</strong> #{{ idSolicitacao }}<br>
                    <small class="text-muted">Guarde este número para consultar o status posteriormente</small>
                </p>
            </div>
            
            <div class="mt-4">
                <button class="btn btn-primary me-2" @click="novaSolicitacao">
                    <i class="fas fa-plus me-2"></i>Nova Solicitação
                </button>
                <button class="btn btn-outline-primary" @click="mostrarConsultaStatus">
                    <i class="fas fa-search me-2"></i>Consultar Status
                </button>
            </div>
        </div>

        <!-- Modal de Consulta de Status -->
        <div class="modal fade" id="modalConsultaStatus" tabindex="-1" ref="modalConsultaRef">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #18578A 0%, #5EA853 100%); color: white;">
                        <h5 class="modal-title">
                            <i class="fas fa-search me-2"></i>Consultar Status da Solicitação
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="consultarStatus">
                            <div class="mb-3">
                                <div class="form-floating">
                                    <input type="email" 
                                           class="form-control" 
                                           id="emailConsulta" 
                                           v-model="consultaForm.email"
                                           placeholder="Email cadastrado"
                                           required>
                                    <label for="emailConsulta">Email Cadastrado *</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-floating">
                                    <input type="number" 
                                           class="form-control" 
                                           id="idConsulta" 
                                           v-model="consultaForm.solicitacao_id"
                                           placeholder="ID da Solicitação (opcional)">
                                    <label for="idConsulta">ID da Solicitação (opcional)</label>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Resultados da Consulta -->
                        <div v-if="resultadosConsulta.length > 0" class="mt-4">
                            <h6>Suas Solicitações:</h6>
                            <div v-for="solicitacao in resultadosConsulta" :key="solicitacao.id" class="border rounded p-3 mb-2">
                                <div class="d-flex justify-content-between align-items-start">
                                                                    <div>
                                    <strong>ID #{{ solicitacao.id }}</strong><br>
                                    <small class="text-muted">{{ solicitacao.visitante_municipio }}/{{ solicitacao.visitante_uf }} → {{ solicitacao.entidade }}</small><br>
                                    <small class="text-muted">{{ solicitacao.entidade_tipo }}</small><br>
                                    <small class="text-muted">Solicitado em: {{ solicitacao.data_solicitacao }}</small>
                                    <div v-if="solicitacao.data_aprovacao">
                                        <small class="text-muted">Processado em: {{ solicitacao.data_aprovacao }}</small>
                                    </div>
                                </div>
                                    <span class="badge" :class="getStatusClass(solicitacao.status)">
                                        {{ solicitacao.status_label }}
                                    </span>
                                </div>
                                <div v-if="solicitacao.observacoes_aprovacao" class="mt-2">
                                    <small><strong>Observações:</strong> {{ solicitacao.observacoes_aprovacao }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <div v-else-if="consultaRealizada && resultadosConsulta.length === 0" class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Nenhuma solicitação encontrada para este email.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" @click="consultarStatus" :disabled="consultando">
                            <span v-if="consultando" class="spinner-border spinner-border-sm me-2"></span>
                            <i v-else class="fas fa-search me-2"></i>
                            {{ consultando ? 'Consultando...' : 'Consultar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div v-if="carregandoFormulario" class="loading-overlay">
            <div class="loading-content">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mb-0">Carregando formulário...</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'FormularioSolicitacaoCadastro',
    data() {
        return {
            // Estados
            carregandoFormulario: false,
            enviando: false,
            consultando: false,
            solicitacaoEnviada: false,
            consultaRealizada: false,
            
            // Dados do formulário
            form: {
                name: '',
                email: '',
                username: '',
                password: '',
                password_confirmation: '',
                telefone: '',
                cpf: '',
                cargo: '',
                visitante_municipio: '',
                visitante_uf: '',
                entidade_orcamentaria_id: '',
                justificativa: ''
            },
            
            // Dados para selects
            entidades: [],
            
            // Formulário de consulta
            consultaForm: {
                email: '',
                solicitacao_id: ''
            },
            
            // Resultados
            resultadosConsulta: [],
            mensagemSucesso: '',
            idSolicitacao: null,
            
            // Validação
            errors: {}
        }
    },
    
    mounted() {
        this.carregarDadosFormulario();
    },
    
    methods: {
        async carregarDadosFormulario() {
            this.carregandoFormulario = true;
            try {
                const response = await fetch('/api/publico/solicitar-cadastro/dados-formulario', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.entidades = data.entidades;
                } else {
                    throw new Error(data.message || 'Erro ao carregar dados do formulário');
                }
                
            } catch (error) {
                console.error('Erro ao carregar dados:', error);
                alert('Erro ao carregar formulário. Tente recarregar a página.');
            } finally {
                this.carregandoFormulario = false;
            }
        },
        

        
        async enviarSolicitacao() {
            this.enviando = true;
            this.errors = {};
            
            try {
                const response = await fetch('/api/publico/solicitar-cadastro', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.solicitacaoEnviada = true;
                    this.mensagemSucesso = data.detalhes;
                    this.idSolicitacao = data.solicitacao_id;
                } else {
                    if (response.status === 422) {
                        this.errors = data.errors;
                    }
                    throw new Error(data.error || 'Erro ao enviar solicitação');
                }
                
            } catch (error) {
                console.error('Erro ao enviar:', error);
                alert(`Erro: ${error.message}`);
            } finally {
                this.enviando = false;
            }
        },
        
        mostrarConsultaStatus() {
            this.consultaForm.email = '';
            this.consultaForm.solicitacao_id = '';
            this.resultadosConsulta = [];
            this.consultaRealizada = false;
            
            const modal = new bootstrap.Modal(document.getElementById('modalConsultaStatus'));
            modal.show();
        },
        
        async consultarStatus() {
            if (!this.consultaForm.email) {
                alert('Por favor, informe o email.');
                return;
            }
            
            this.consultando = true;
            try {
                const params = new URLSearchParams(this.consultaForm);
                
                const response = await fetch(`/api/publico/solicitar-cadastro/consultar-status?${params}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.resultadosConsulta = data.solicitacoes;
                } else {
                    this.resultadosConsulta = [];
                    if (response.status !== 404) {
                        throw new Error(data.message || 'Erro ao consultar status');
                    }
                }
                
                this.consultaRealizada = true;
                
            } catch (error) {
                console.error('Erro ao consultar:', error);
                alert(`Erro: ${error.message}`);
            } finally {
                this.consultando = false;
            }
        },
        
        novaSolicitacao() {
            this.solicitacaoEnviada = false;
            this.form = {
                name: '',
                email: '',
                username: '',
                password: '',
                password_confirmation: '',
                telefone: '',
                cpf: '',
                cargo: '',
                visitante_municipio: '',
                visitante_uf: '',
                entidade_orcamentaria_id: '',
                justificativa: ''
            };
            this.errors = {};
        },
        
        getStatusClass(status) {
            const classes = {
                'Aguardando Análise': 'bg-warning text-dark',
                'Aprovada': 'bg-success',
                'Rejeitada': 'bg-danger'
            };
            return classes[status] || 'bg-secondary';
        }
    }
}
</script>

<style scoped>
.form-floating .form-control {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
    line-height: 1.5 !important;
    min-height: 58px !important;
}

.form-floating .form-control:focus {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

.form-floating .form-control:not(:placeholder-shown) {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

.form-control.is-valid {
    border-color: #5EA853 !important;
    box-shadow: 0 0 0 0.2rem rgba(94, 168, 83, 0.25) !important;
}

.form-control.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.invalid-feedback {
    color: #dc3545 !important;
    font-size: 0.875rem !important;
}

.text-custom { 
    color: #18578A !important; 
    font-weight: 600; 
}

.btn-gradient {
    background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
    border: none;
    color: white;
    font-weight: 600;
    padding: 12px 30px;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    color: white;
}

.btn-gradient:disabled {
    transform: none;
    opacity: 0.7;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-content {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    max-width: 300px;
}
</style>
