<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'OrçaCidade') }}</title>

    {{-- bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    {{-- CSS Files --}}
    <link rel="stylesheet" href="{{ asset('assets/css/andamento-projeto.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modern-interface.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/background-images.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/home-final.css') }}?v={{ time() }}">
    
    <!-- Scripts -->            
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
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

<body>
    <div id="app">
        @auth
        {{-- SISTEMA COMPLETO COM MENU LATERAL - SÓ APARECE QUANDO LOGADO --}}

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
            <!-- DEBUG TEMPORÁRIO -->
            @php
                $user = Auth::user()->load('roles');
                $roles = $user->roles;
                $isSuper = $user->isSuperAdmin();
            @endphp
            <div style="background: #f0f0f0; padding: 10px; margin: 10px; border-radius: 5px; font-size: 12px;">
                <strong>DEBUG:</strong><br>
                Usuário: {{ $user->name }}<br>
                Email: {{ $user->email }}<br>
                Papéis: {{ $roles->pluck('name')->implode(', ') ?: 'Nenhum papel' }}<br>
                Count: {{ $roles->count() }}<br>
                isSuperAdmin(): {{ $isSuper ? 'SIM' : 'NÃO' }}<br>
                <hr>
                <strong>Detalhes dos papéis:</strong><br>
                @foreach($roles as $role)
                    - {{ $role->name }} ({{ $role->display_name }})<br>
                @endforeach
            </div>

            <!-- TABELAS OFICIAIS -->
            @if(Auth::user()->hasRole('super') || Auth::user()->hasRole('gerenciar_importacao_derpr') || Auth::user()->hasRole('gerenciar_importacao_sinapi') || Auth::user()->hasRole('consultar_tabela_derpr') || Auth::user()->hasRole('consultar_tabela_sinapi'))
            <div class="menu-group">
                <div class="menu-header myBox">
                    <div class="menu-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <span>TABELAS OFICIAIS</span>
                </div>
                <div class="menu-items">
                    @if(Auth::user()->hasRole('super') || Auth::user()->hasRole('gerenciar_importacao_derpr'))
                    <a href="{{ route('derpr.importar.index') }}" class="menu-link">
                        <i class="fas fa-download"></i>
                        <span>Importar DER-PR</span>
                    </a>
                    @endif
                    @if(Auth::user()->hasRole('super') || Auth::user()->hasRole('gerenciar_importacao_sinapi'))
                    <a href="{{ route('sinapi.importar.index') }}" class="menu-link">
                        <i class="fas fa-download"></i>
                        <span>Importar SINAPI</span>
                    </a>
                    @endif
                    @if(Auth::user()->hasRole('super') || Auth::user()->hasRole('consultar_tabela_derpr'))
                    <a href="{{ route('derpr.consultar.index') }}" class="menu-link">
                        <i class="fas fa-search"></i>
                        <span>Consultar DER-PR</span>
                    </a>
                    @endif
                    @if(Auth::user()->hasRole('super') || Auth::user()->hasRole('consultar_tabela_sinapi'))
                    <a href="{{ route('sinapi.consultar.index') }}" class="menu-link">
                        <i class="fas fa-search"></i>
                        <span>Consultar SINAPI-PR</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- ADMINISTRAÇÃO -->
            @if(Auth::user()->hasRole('super') || Auth::user()->hasRole('gerenciar_usuarios_entidades') || Auth::user()->hasRole('visualizar_usuarios_entidades') || Auth::user()->hasRole('gerenciar_usuarios') || Auth::user()->hasRole('visualizar_usuarios') || Auth::user()->hasRole('gerenciar_municipios') || Auth::user()->hasRole('visualizar_municipios') || Auth::user()->hasRole('gerenciar_entidade_orcamentaria') || Auth::user()->hasRole('visualizar_entidade_orcamentaria') || Auth::user()->hasRole('gerenciar_ad') || Auth::user()->hasRole('gerenciar_estrutura_orcamento') || Auth::user()->hasRole('visualizar_estrutura_orcamento'))
            <div class="menu-group">
                <div class="menu-header myBox">
                    <div class="menu-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <span>ADMINISTRAÇÃO</span>
                </div>
                <div class="menu-items">
                    @if(Auth::user()->hasRole('super') || Auth::user()->hasRole('gerenciar_usuarios') || Auth::user()->hasRole('visualizar_usuarios'))
                    <a href="{{ route('admin.usuarios.index') }}" class="menu-link">
                        <i class="fas fa-users-cog"></i>
                        <span>Gerenciamento de Usuários</span>
                    </a>
                    @endif
                    @if(Auth::user()->hasRole('super') || Auth::user()->hasRole('gerenciar_ad'))
                    <a href="{{ route('admin.active-directory.index') }}" class="menu-link">
                        <i class="fas fa-network-wired"></i>
                        <span>Active Directory</span>
                    </a>
                    @endif

                    @if(Auth::user()->hasRole('super') || Auth::user()->hasRole('gerenciar_municipios') || Auth::user()->hasRole('visualizar_municipios'))
                    <a href="{{ route('admin.municipios.index') }}" class="menu-link">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Municípios</span>
                    </a>
                    @endif
                    
                    @if(Auth::user()->hasRole('super') || Auth::user()->hasRole('gerenciar_entidade_orcamentaria') || Auth::user()->hasRole('visualizar_entidade_orcamentaria'))
                    <a href="{{ route('admin.entidades-orcamentarias.index') }}" class="menu-link">
                        <i class="fas fa-building"></i>
                        <span>Entidades Orçamentárias</span>
                    </a>
                    @endif

                    @if(Auth::user()->hasRole('super') || Auth::user()->hasRole('gerenciar_estrutura_orcamento') || Auth::user()->hasRole('visualizar_estrutura_orcamento'))
                    <a href="{{ route('admin.estrutura-orcamento.index') }}" class="menu-link">
                        <i class="fas fa-sitemap"></i>
                        <span>Estrutura de Orçamentos</span>
                    </a>
                    @endif

                    @if(Auth::user()->hasRole('super') || Auth::user()->hasPermission('aprovar_cadastros') || Auth::user()->hasPermission('visualizar_cadastros_usuarios'))
                    <a href="{{ route('admin.aprovacao-cadastros.index') }}" class="menu-link">
                        <i class="fas fa-user-check"></i>
                        <span>Aprovação de Cadastros</span>
                    </a>
                    @endif

                    @if(Auth::user()->hasRole('super') || Auth::user()->hasPermission('gerenciar_vinculos_usuarios') || Auth::user()->hasPermission('visualizar_cadastros_usuarios'))
                    <a href="{{ route('admin.usuarios-por-entidade.index') }}" class="menu-link">
                        <i class="fas fa-users-cog"></i>
                        <span>Usuários por Entidade</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

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

        {{-- HEADER COMPONENT SERÁ CRIADO --}}
        <header-component></header-component>

        <main class="py-4">
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

            @yield('content')
        </main>
        @else
        {{-- LAYOUT SIMPLES PARA LOGIN --}}
        @yield('content')
        @endauth
    </div>

    <!-- Imagem de boas-vindas (cidade) -->
    {{-- <div class="predio">
        <img width="200px" src="{{ asset('assets/images/boasvindas-cidade.png') }}" class="" alt="Boas-vindas">
        <div class="predio_txt">
            <span class="txt_boas_vindas_borda">
                <span class="cor_verde">Orça<span class="cor_azul">Cidade</span></span>
            </span>
        </div>            
    </div> --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    @stack('scripts')

    <!-- Script para atualizar horário em tempo real -->
    <script>
        // Atualizar horário em tempo real
        setInterval(function() {
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                const now = new Date();
                const timeString = now.toLocaleTimeString('pt-BR', { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit' 
                });
                timeElement.textContent = timeString;
            }
        }, 1000);
    </script>

    <!-- Formulário de logout oculto -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

</body>
</html>