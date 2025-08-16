<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Orcacidade') }}</title>

    {{-- bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

   
    {{-- para permitir campos textos ricos --}}
    {{-- https://quilljs.com/ --}}
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/andamento-projeto.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/padroes.css') }}?v={{ time() }}">
    


    <!-- Scripts -->            
    @vite(['resources/css/app.css', 
            'resources/js/app.js'])
    
    <!-- CSS Modern Interface -->
    <style>
        /* Labels de formulário */
        .form-label {
            font-weight: 600 !important;
            color: #18578A !important;
            margin-bottom: 0.5rem !important;
        }

        /* Input groups */
        .input-group-text {
            background-color: #f8f9fa !important;
            border-color: #e9ecef !important;
            color: #6c757d !important;
        }

        /* Campos de formulário */
        .form-control {
            border: 1px solid #e9ecef !important;
            border-radius: 0.375rem !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
            transition: all 0.2s ease !important;
        }

        .form-control:focus {
            border-color: #5EA853 !important;
            box-shadow: 0 0 0 0.2rem rgba(94, 168, 83, 0.25) !important;
        }

        /* Validação de erro */
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Classe para cor azul secundária */
        .text-custom {
            color: #18578A !important;
        }
    </style>
              
    @stack('styles')
</head>




{{-- inicio menu lateral esquerdo --}}

<!-- Trigger do Menu Lateral -->
<div class="sidebar-trigger" title="Menu Principal">
    <i class="fas fa-bars"></i>
</div>

<div class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="logo-text">
                <h3>OrçaCidade</h3>
                <p>Sistema de Orçamentos</p>
            </div>
        </div>
        <button class="sidebar-close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="sidebar-content">
        <!-- TABELAS OFICIAIS -->
        <div class="menu-group">
            <div class="menu-header myBox">
                <div class="menu-icon">
                    <i class="fas fa-search"></i>
                </div>
                <span>TABELAS OFICIAIS</span>
            </div>
            <div class="menu-items">
                <a href="{{ route('derpr.importar.index') }}" class="menu-link @if(request()->routeIs('derpr.importar.index')) active @endif">
                    <i class="fas fa-download"></i>
                    <span>Importar DER-PR</span>
                </a>
                <a href="{{ route('sinapi.importar.index.novo') }}" class="menu-link @if(request()->routeIs('sinapi.importar.index.novo')) active @endif">
                    <i class="fas fa-download"></i>
                    <span>Importar SINAPI</span>
                </a>
                <a href="{{ route('derpr.consultar') }}" class="menu-link @if(request()->routeIs('derpr.consultar')) active @endif">
                    <i class="fas fa-search"></i>
                    <span>Consultar DER-PR</span>
                </a>
                <a href="{{ route('sinapi.consultar') }}" class="menu-link @if(request()->routeIs('sinapi.consultar*')) active @endif">
                    <i class="fas fa-search"></i>
                    <span>Consultar SINAPI-PR</span>
                </a>
            </div>
        </div>

        <!-- ADMINISTRAÇÃO -->
        <div class="menu-group">
            <div class="menu-header myBox">
                <div class="menu-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <span>ADMINISTRAÇÃO</span>
            </div>
            <div class="menu-items">
                <a href="{{ route('administracao.usuarios.index') }}" class="menu-link @if(request()->routeIs('administracao.usuarios.index')) active @endif">
                    <i class="fas fa-users-cog"></i>
                    <span>Gerenciamento de Usuários</span>
                </a>
                <a href="{{ route('administracao.municipios.index') }}" class="menu-link @if(request()->routeIs('administracao.municipios.index')) active @endif">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Municípios</span>
                </a>
                <a href="{{ route('administracao.entidades-orcamentarias.index') }}" class="menu-link @if(request()->routeIs('administracao.entidades-orcamentarias.index')) active @endif">
                    <i class="fas fa-building"></i>
                    <span>Entidades Orçamentárias</span>
                </a>
                <a href="{{ route('administracao.active_directory.index') }}" class="menu-link">
                    <i class="fas fa-network-wired"></i>
                    <span>Active Directory</span>
                </a>
                <a href="{{ route('administracao.estrutura-orcamento.index') }}" class="menu-link @if(request()->routeIs('administracao.estrutura-orcamento*')) active @endif">
                    <i class="fas fa-sitemap"></i>
                    <span>Estrutura de Orçamentos</span>
                </a>
                <a href="{{ route('configuracoes-gerais.index') }}" class="menu-link @if(request()->routeIs('configuracoes-gerais.index')) active @endif">
                    <i class="fas fa-sliders-h"></i>
                    <span>Configurações Gerais</span>
                </a>
                <a href="{{ route('padroes.index') }}" class="menu-link @if(request()->routeIs('padroes.index')) active @endif">
                    <i class="fas fa-palette"></i>
                    <span>Padrões de Interface</span>
                </a>
            </div>
        </div>

        <!-- PRINCIPAL -->
        <div class="menu-group">
            <div class="menu-header myBox">
                <div class="menu-icon">
                    <i class="fas fa-home"></i>
                </div>
                <span>PRINCIPAL</span>
            </div>
            <div class="menu-items">
                <a href="{{ route('inicio') }}" class="menu-link @if(request()->routeIs('inicio')) active @endif">
                    <i class="fas fa-chart-bar"></i>
                    <span>Início</span>
                </a>
                <a href="{{ route('central.tarefas') }}" class="menu-link @if(request()->routeIs('central.tarefas')) active @endif">
                    <i class="fas fa-tasks"></i>
                    <span>Central de Tarefas</span>
                </a>
            </div>
        </div>

        <!-- ORÇAMENTOS -->
        <div class="menu-group">
            <div class="menu-header myBox">
                <div class="menu-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <span>ORÇAMENTOS</span>
            </div>
            <div class="menu-items">
                <a href="{{ route('bdis.index') }}" class="menu-link @if(request()->routeIs('bdis.index')) active @endif">
                    <i class="fas fa-percentage"></i>
                    <span>BDI</span>
                </a>
                <a href="{{ route('cotacoes.index') }}" class="menu-link @if(request()->routeIs('cotacoes.index')) active @endif">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Cotações</span>
                </a>
                <a href="{{ route('composicao-proprias.index') }}" class="menu-link @if(request()->routeIs('composicao-proprias.index')) active @endif">
                    <i class="fas fa-tools"></i>
                    <span>Composições Próprias</span>
                </a>
            </div>
        </div>

        <!-- TRANSPORTE -->
        <div class="menu-group">
            <div class="menu-header myBox">
                <div class="menu-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <span>TRANSPORTE</span>
            </div>
            <div class="menu-items">
                <a href="{{ route('transporte.custos.gerenciar') }}" class="menu-link @if(request()->routeIs('transporte.custos.gerenciar')) active @endif">
                    <i class="fas fa-route"></i>
                    <span>DMT - Fórmulas</span>
                </a>
                <a href="{{ route('dmt-default.index') }}" class="menu-link @if(request()->routeIs('dmt-default.index')) active @endif">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>DMT PRC</span>
                </a>
                <a href="{{ route('dmt.index') }}" class="menu-link @if(request()->routeIs('dmt.index')) active @endif">
                    <i class="fas fa-road"></i>
                    <span>DMT</span>
                </a>
            </div>
        </div>

        <!-- CLIENTES -->
        <div class="menu-group">
            <div class="menu-header myBox">
                <div class="menu-icon">
                    <i class="fas fa-users"></i>
                </div>
                <span>CLIENTES</span>
            </div>
            <div class="menu-items">
                <a href="#" class="menu-link">
                    <i class="fas fa-user-tie"></i>
                    <span>Gestão de Clientes</span>
                </a>
                <a href="#" class="menu-link">
                    <i class="fas fa-chart-line"></i>
                    <span>Relatórios</span>
                </a>
            </div>
        </div>

        <!-- USUÁRIO -->
        <div class="menu-group user-section">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <div class="user-name">{{ Auth::user()->name ?? 'Usuário' }}</div>
                    <div class="user-role">{{ Auth::user()->email ?? 'admin@orcacidade.com' }}</div>
                </div>
            </div>
            
            <div class="menu-items">
                <a href="#" class="menu-link">
                    <i class="fas fa-user"></i>
                    <span>Meu Perfil</span>
                </a>
                <a href="#" class="menu-link">
                    <i class="fas fa-cog"></i>
                    <span>Configurações</span>
                </a>
                <a href="#" class="menu-link logout-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sair</span>
                </a>
            </div>
        </div>
    </div>
</div>















{{-- fim menu lateral esquerdo --}}








<body>
    <div id="app">
        <header-component></header-component>

        <main class="py-4">


            {{-- caminho de pão --}}
            <?php 
            $url = request()->getPathInfo();
            $itens = explode('/', $url);
            unset($itens[0]);
            //var_dump($itens);
            ?>

            <!-- Breadcrumb será implementado posteriormente se necessário -->
           



            <!-- Sistema de Mensagens Simples -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999; max-width: 400px;" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999; max-width: 400px;" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999; max-width: 400px;" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999; max-width: 400px;" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>


    <div class="predio">
        <img width="200px" src="{{ asset('assets/images/boasvindas-cidade.png') }}" class="" alt="...">  
        <div class="predio_txt">
             <span class="txt_boas_vindas_borda">
                 <span class="cor_verde">Orça<span class="cor_azul">Cidade</span></span>
             </span>
         </div>            
    </div>


    <script>    
        // //variável que irá conter o endereço para acessar as apis
         const apiURL =@json(config('app.api_url')); //definido em config/app.php
         const appURL =@json(config('app.app_url')); //definido em config/app.php
        //  console.log('malcon')
        //  console.log(apiURL)
        //  console.log(apiURL2)
        //  console.log('malcon')
    </script>
    
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar todas as tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    trigger: 'hover',
                    html: true
                });
            });

            // Melhorar a experiência do menu lateral
            const offcanvas = document.getElementById('offcanvasWithBothOptions');
            const backdrop = document.querySelector('.offcanvas-backdrop');
            
            if (offcanvas) {
                // Melhorar o backdrop
                if (backdrop) {
                    backdrop.addEventListener('click', function () {
                        const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvas);
                        if (bsOffcanvas) {
                            bsOffcanvas.hide();
                        }
                    });
                }

                // Adicionar suporte para tecla ESC
                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvas);
                        if (bsOffcanvas && offcanvas.classList.contains('show')) {
                            bsOffcanvas.hide();
                        }
                    }
                });
            }
        });
    </script>

    @stack('scripts')

    <!-- Auto-hide simples para alertas -->
    <script>
        // Auto-hide dos alertas após 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>

    <!-- Formulário de logout oculto -->
    <form id="logout-form" action="/api/logout" method="POST" style="display: none;">
        @csrf
    </form>

</body>
</html>


