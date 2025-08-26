<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Solicitação de Cadastro - OrçaCidade</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS Global do Projeto -->
    <link rel="stylesheet" href="{{ asset('assets/css/modern-interface.css') }}">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
        }
        
        .header-gradient {
            background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .header-gradient h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .header-gradient p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 1rem;
        }
        
        .form-body {
            padding: 2rem;
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
        
        .form-floating .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-floating .form-control:focus {
            border-color: #5EA853;
            box-shadow: 0 0 0 0.2rem rgba(94, 168, 83, 0.25);
        }
        
        .alert-custom {
            border-radius: 10px;
            border: none;
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
</head>
<body>
    <div class="main-container">
        <div class="form-card">
            <!-- Header -->
            <div class="header-gradient">
                <h1><i class="fas fa-user-plus me-2"></i>Solicitação de Cadastro</h1>
                <p>Preencha os dados abaixo para solicitar acesso ao OrçaCidade</p>
            </div>
            
            <!-- Formulário -->
            <div class="form-body">
                <div id="solicitar-cadastro-app">
                    <formulario-solicitacao-cadastro></formulario-solicitacao-cadastro>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Vue.js 3 CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    
    <!-- Vue.js App -->
    <script>
        const { createApp } = Vue;
        
        createApp({
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
                        municipio_id: '',
                        entidade_orcamentaria_id: '',
                        justificativa: ''
                    },
                    
                    // Dados para selects
                    municipios: [],
                    entidades: [],
                    entidadesFiltradas: [],
                    
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
                            this.municipios = data.municipios;
                            this.entidades = data.entidades;
                            this.entidadesFiltradas = data.entidades;
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
                
                filtrarEntidades() {
                    if (this.form.municipio_id) {
                        this.entidadesFiltradas = this.entidades.filter(entidade => 
                            !entidade.municipio_id || entidade.municipio_id == this.form.municipio_id
                        );
                    } else {
                        this.entidadesFiltradas = this.entidades;
                    }
                    
                    // Limpar seleção de entidade se não for válida para o município
                    if (this.form.entidade_orcamentaria_id) {
                        const entidadeValida = this.entidadesFiltradas.find(e => e.id == this.form.entidade_orcamentaria_id);
                        if (!entidadeValida) {
                            this.form.entidade_orcamentaria_id = '';
                        }
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
                        municipio_id: '',
                        entidade_orcamentaria_id: '',
                        justificativa: ''
                    };
                    this.errors = {};
                    this.entidadesFiltradas = this.entidades;
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
        }).mount('#solicitar-cadastro-app');
    </script>
</body>
</html>
