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
    
    <style>
        body {
            background: #f8f
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Header Principal */
        .main-header {
            background: white;
            border-bottom: 2px solid #e9ecef;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-pixels {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2px;
            width: 40px;
            height: 40px;
        }
        
        .pixel {
            width: 100%;
            height: 100%;
            border-radius: 2px;
        }
        
        .pixel.green { background: #5EA853; }
        .pixel.blue { background: #18578A; }
        
        .brand-title {
            color: #5EA853;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }
        
        .login-link {
            color: #333;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .login-link:hover {
            color: #5EA853;
            background: #f8f9fa;
        }
        
        /* Container Principal */
        .main-container {
            padding: 2rem 0;
            min-height: calc(100vh - 80px);
        }
        
        .content-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .main-title {
            color: #18578A;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .page-description {
            color: #6c757d;
            font-size: 1.1rem;
            margin: 0;
        }
        
        /* Formulário */
        .form-container {
            margin-top: 1rem;
        }
        
        .form-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            overflow: hidden;
            width: 100%;
        }
        
        .form-body {
            padding: 2rem;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #5EA853 0%, #18578A 100%);
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
        
        .text-custom { 
            color: #18578A !important; 
            font-weight: 600; 
        }
        

        
        /* Sidebar Fontes */
        .sidebar-fontes {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            height: fit-content;
        }
        
        .fontes-header h5 {
            color: #18578A;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #5EA853;
        }
        
        .fonte-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .fonte-item:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }
        
        .fonte-logo {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        
        .fonte-logo.der {
            background: #5EA853;
        }
        
        .fonte-logo.caixa {
            background: #18578A;
        }
        
        .fonte-logo.curitiba {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
        }
        
        .fonte-info h6 {
            color: #18578A;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            font-size: 0.9rem;
        }
        
        .fonte-info p {
            color: #6c757d;
            margin: 0;
            font-size: 0.8rem;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    <!-- Header Principal (igual ao da tela principal) -->
    <header class="main-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="logo-section">
                        <div class="logo-pixels">
                            <div class="pixel green"></div>
                            <div class="pixel blue"></div>
                            <div class="pixel green"></div>
                            <div class="pixel blue"></div>
                        </div>
                        <h1 class="brand-title">OrçaCidade</h1>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <a href="/login" class="login-link">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Container Principal -->
    <div class="main-container">
        <div class="row">
            <!-- Conteúdo Principal -->
            <div class="col-md-9">
                <div class="content-section">
                    <!-- Título da Página -->
                    <div class="page-header">
                        <h2 class="main-title">
                            <i class="fas fa-user-plus me-3"></i>
                            Solicitação de Cadastro
                        </h2>
                        <p class="page-description">
                            Solicite acesso ao sistema OrçaCidade para criar orçamentos de obras a partir de bases oficiais
                        </p>
                    </div>

                    <!-- Formulário -->
                    <div class="form-container">
                        <div class="form-card">
                            <!-- Formulário -->
                            <div class="form-body">
            
            <!-- Formulário -->
            <div class="form-body">
                @if (session('success'))
                    <div class="alert alert-success border-0 rounded-3 text-center">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h4 class="alert-heading">Solicitação Enviada com Sucesso!</h4>
                        <p class="mb-3">{{ session('success') }}</p>
                        <hr>
                        <p class="mb-0">
                            <strong>ID da Solicitação:</strong> #{{ session('solicitacao_id') }}<br>
                            <small class="text-muted">Guarde este número para consultar o status posteriormente</small>
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('publico.solicitar-cadastro.index') }}" class="btn btn-primary me-2">
                                <i class="fas fa-plus me-2"></i>Nova Solicitação
                            </a>
                        </div>
                    </div>
                @else
                    <form method="POST" action="/api/publico/solicitar-cadastro">
                        @csrf
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
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name"
                                           value="{{ old('name') }}"
                                           placeholder="Nome completo"
                                           required>
                                    <label for="name">Nome Completo *</label>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email"
                                           value="{{ old('email') }}"
                                           placeholder="Email"
                                           required>
                                    <label for="email">Email *</label>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
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
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password"
                                           placeholder="Senha"
                                           required>
                                    <label for="password">Senha *</label>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
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
                                           id="password_confirmation" 
                                           name="password_confirmation"
                                           placeholder="Confirmar senha"
                                           required>
                                    <label for="password_confirmation">Confirmar Senha *</label>
                                </div>
                            </div>
                            
                            <!-- Dados da Solicitação -->
                            <div class="col-12 mt-4">
                                <h6 class="text-custom mb-3">
                                    <i class="fas fa-building me-2"></i>Local de Trabalho
                                </h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-control @error('municipio_id') is-invalid @enderror" 
                                            id="municipio_id" 
                                            name="municipio_id"
                                            required>
                                        <option value="">Selecione um município...</option>
                                        <!-- Será preenchido via JavaScript -->
                                    </select>
                                    <label for="municipio_id">Município *</label>
                                </div>
                                @error('municipio_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-control @error('entidade_orcamentaria_id') is-invalid @enderror" 
                                            id="entidade_orcamentaria_id" 
                                            name="entidade_orcamentaria_id"
                                            required>
                                        <option value="">Selecione uma entidade...</option>
                                        <!-- Será preenchido via JavaScript -->
                                    </select>
                                    <label for="entidade_orcamentaria_id">Entidade Orçamentária *</label>
                                </div>
                                @error('entidade_orcamentaria_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Justificativa -->
                            <div class="col-12 mt-3">
                                <div class="form-floating">
                                    <textarea class="form-control @error('justificativa') is-invalid @enderror" 
                                              id="justificativa" 
                                              name="justificativa"
                                              placeholder="Justifique sua necessidade de acesso ao sistema..."
                                              style="height: 120px;"
                                              required
                                              minlength="20"
                                              maxlength="1000">{{ old('justificativa') }}</textarea>
                                    <label for="justificativa">Justificativa para Acesso ao Sistema *</label>
                                </div>
                                @error('justificativa')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <small>
                                        <i class="fas fa-info-circle me-1"></i>
                                        Explique por que você precisa acessar o OrçaCidade e como vai utilizá-lo (mínimo 20 caracteres)
                                    </small>
                                </div>
                            </div>
                            
                            <!-- Botões -->
                            <div class="col-12 mt-4 d-grid">
                                <button type="submit" class="btn btn-gradient btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Solicitação
                                </button>
                            </div>
                            
                            <!-- Links úteis -->
                            <div class="col-12 mt-3 text-center">
                                <hr class="my-3">
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="/login" class="btn btn-link text-decoration-none">
                                        <i class="fas fa-sign-in-alt me-1"></i>Já tenho acesso
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar "Fontes" (igual ao da tela principal) -->
            <div class="col-md-3">
                <div class="sidebar-fontes">
                    <div class="fontes-header">
                        <h5>Fontes:</h5>
                    </div>
                    
                    <div class="fonte-item">
                        <div class="fonte-logo der">
                            <span>DER</span>
                        </div>
                        <div class="fonte-info">
                            <h6>DER PARANÁ</h6>
                            <p>Departamento de Estrada e Rodagem - PR</p>
                        </div>
                    </div>
                    
                    <div class="fonte-item">
                        <div class="fonte-logo caixa">
                            <span>CAIXA</span>
                        </div>
                        <div class="fonte-info">
                            <h6>CAIXA SINAPI</h6>
                            <p>Sistema Nacional de Pesquisa de Custos e Índices da Construção Civil</p>
                        </div>
                    </div>
                    
                    <div class="fonte-item">
                        <div class="fonte-logo curitiba">
                            <span>CURITIBA</span>
                        </div>
                        <div class="fonte-info">
                            <h6>CURITIBA</h6>
                            <p>Prefeitura de Curitiba</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Carregar dados dos selects -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            carregarDadosFormulario();
        });
        
        async function carregarDadosFormulario() {
            try {
                const response = await fetch('/api/publico/solicitar-cadastro/dados-formulario');
                const data = await response.json();
                
                if (response.ok) {
                    // Preencher municípios
                    const selectMunicipio = document.getElementById('municipio_id');
                    data.municipios.forEach(municipio => {
                        const option = document.createElement('option');
                        option.value = municipio.id;
                        option.textContent = municipio.nome;
                        selectMunicipio.appendChild(option);
                    });
                    
                    // Preencher entidades
                    const selectEntidade = document.getElementById('entidade_orcamentaria_id');
                    data.entidades.forEach(entidade => {
                        const option = document.createElement('option');
                        option.value = entidade.id;
                        option.textContent = entidade.nome;
                        selectEntidade.appendChild(option);
                    });
                    
                    // Adicionar listener para filtrar entidades por município
                    selectMunicipio.addEventListener('change', function() {
                        filtrarEntidades(data.entidades, this.value);
                    });
                } else {
                    console.error('Erro ao carregar dados:', data.message);
                }
            } catch (error) {
                console.error('Erro ao carregar dados:', error);
            }
        }
        
        function filtrarEntidades(todasEntidades, municipioId) {
            const selectEntidade = document.getElementById('entidade_orcamentaria_id');
            
            // Limpar opções
            selectEntidade.innerHTML = '<option value="">Selecione uma entidade...</option>';
            
            // Filtrar entidades
            const entidadesFiltradas = todasEntidades.filter(entidade => 
                !entidade.municipio_id || entidade.municipio_id == municipioId
            );
            
            // Adicionar opções filtradas
            entidadesFiltradas.forEach(entidade => {
                const option = document.createElement('option');
                option.value = entidade.id;
                option.textContent = entidade.nome;
                selectEntidade.appendChild(option);
            });
        }
    </script>
</body>
</html>
