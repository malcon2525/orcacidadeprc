@extends('layouts.app')

@section('title', $documentacao['titulo'] . ' - Documentação Técnica')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/andamento-projeto.css') }}">
<style>
    .documentacao-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .titulo-documentacao {
        color: var(--orcacidade-dark);
        font-size: 2.5rem;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .titulo-documentacao i {
        color: var(--orcacidade-accent);
        font-size: 2.3rem;
        margin-right: 0.5rem;
        vertical-align: -2px;
    }
    
    .categoria-badge {
        display: inline-block;
        background: var(--orcacidade-accent);
        color: white;
        padding: 8px 20px;
        border-radius: 25px;
        font-size: 0.95rem;
        font-weight: 500;
        margin-bottom: 20px;
    }
    
    .objetivo {
        color: #6c757d;
        font-size: 1.1rem;
        line-height: 1.6;
        margin-bottom: 20px;
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid var(--orcacidade-accent);
    }
    
    .contexto {
        color: #495057;
        font-size: 1rem;
        line-height: 1.5;
        font-style: italic;
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid var(--orcacidade-warning);
        margin-bottom: 30px;
    }
    
    .secao {
        background: white;
        border-radius: 10px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 25px;
        overflow: hidden;
        border: 1px solid #e0e0e0;
        transition: box-shadow 0.3s ease;
    }
    
    .secao:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .secao-header {
        background: var(--orcacidade-dark);
        color: white;
        padding: 20px;
        font-size: 1.3rem;
        font-weight: 600;
    }
    
    .secao-content {
        padding: 25px;
    }
    
    .rota-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 18px;
        margin-bottom: 12px;
        border-left: 4px solid var(--orcacidade-accent);
        border: 1px solid #e0e0e0;
    }
    
    .rota-metodo {
        display: inline-block;
        background: var(--orcacidade-accent);
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-right: 12px;
    }
    
    .rota-endpoint {
        font-family: 'Courier New', monospace;
        background: #e9ecef;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.9rem;
        color: var(--orcacidade-dark);
    }
    
    .rota-descricao {
        margin-top: 10px;
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .arquivo-item {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 12px;
        margin-bottom: 10px;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        color: var(--orcacidade-dark);
        border: 1px solid #e0e0e0;
    }
    
    .tabela-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
        border: 1px solid #e0e0e0;
    }
    
    .tabela-nome {
        font-weight: 600;
        color: var(--orcacidade-accent);
        font-size: 1.2rem;
        margin-bottom: 12px;
    }
    
    .tabela-descricao {
        color: #6c757d;
        margin-bottom: 20px;
        line-height: 1.5;
    }
    
    .tabela-campos {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }
    
    .campo-item {
        background: white;
        padding: 10px 15px;
        border-radius: 6px;
        border-left: 4px solid var(--orcacidade-accent);
        font-size: 0.9rem;
        border: 1px solid #e0e0e0;
    }
    
    .lista-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        border-left: 4px solid var(--orcacidade-accent);
        border: 1px solid #e0e0e0;
        line-height: 1.5;
    }
    
    .formula-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
        border: 1px solid #e0e0e0;
    }
    
    .formula-nome {
        font-weight: 600;
        color: var(--orcacidade-accent);
        margin-bottom: 12px;
        font-size: 1.1rem;
    }
    
    .formula-texto {
        font-family: 'Courier New', monospace;
        background: white;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 12px;
        border-left: 4px solid var(--orcacidade-warning);
        border: 1px solid #e0e0e0;
        font-size: 0.95rem;
    }
    
    .exemplo-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
        border: 1px solid #e0e0e0;
    }
    
    .exemplo-titulo {
        font-weight: 600;
        color: var(--orcacidade-accent);
        margin-bottom: 12px;
        font-size: 1.1rem;
    }
    
    .exemplo-codigo {
        background: #2d3748;
        color: #e2e8f0;
        padding: 20px;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        overflow-x: auto;
        white-space: pre-wrap;
        border: 1px solid #4a5568;
    }
    
    .historico-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 12px;
        border-left: 4px solid var(--orcacidade-info);
        border: 1px solid #e0e0e0;
    }
    
    .historico-data {
        font-weight: 600;
        color: var(--orcacidade-info);
        margin-bottom: 8px;
    }
    
    .historico-versao {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }
    
    .historico-descricao {
        color: #495057;
        line-height: 1.5;
    }
    
    .voltar-btn {
        display: inline-block;
        background: var(--orcacidade-accent);
        color: white;
        padding: 12px 25px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: background 0.3s ease;
        margin-bottom: 30px;
        border: none;
    }
    
    .voltar-btn:hover {
        background: #2e7d32;
        color: white;
        text-decoration: none;
    }
    
    .interface-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }
    
    .interface-item {
        background: white;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid var(--orcacidade-accent);
        border: 1px solid #e0e0e0;
    }
    
    .interface-label {
        font-weight: 600;
        color: var(--orcacidade-accent);
        margin-bottom: 8px;
    }
    
    .interface-value {
        color: #495057;
        line-height: 1.5;
    }
    
    .cores-container {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 15px;
    }
    
    .cor-item {
        width: 35px;
        height: 35px;
        border-radius: 6px;
        border: 2px solid #ddd;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .rotas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        gap: 18px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="documentacao-container">
    <!-- Botão Voltar -->
    <a href="{{ route('documentacao.index') }}" class="voltar-btn">
        <i class="fas fa-arrow-left"></i> Voltar à Lista de Módulos
    </a>
    
    <!-- Header -->
    <div class="mb-4">
        <div class="categoria-badge">{{ ucfirst(str_replace('_', ' ', $documentacao['categoria'])) }}</div>
        <h1 class="titulo-documentacao">
            <i class="fas fa-file-alt"></i>{{ $documentacao['titulo'] }}
        </h1>
        <div class="objetivo">{{ $documentacao['objetivo'] }}</div>
        @if(isset($documentacao['contexto']))
            <div class="contexto">{{ $documentacao['contexto'] }}</div>
        @endif
    </div>
    
    <!-- Rotas -->
    @if(isset($documentacao['rotas']))
        <div class="secao">
            <div class="secao-header">
                <i class="fas fa-route"></i> Rotas e API
            </div>
            <div class="secao-content">
                @if(isset($documentacao['rotas']['web']) && count($documentacao['rotas']['web']) > 0)
                    <h4 style="color: var(--orcacidade-accent); margin-bottom: 20px; font-weight: 600;">
                        <i class="fas fa-globe"></i> Rotas Web
                    </h4>
                    <div class="rotas-grid">
                    @foreach($documentacao['rotas']['web'] as $rota)
                        <div class="rota-item">
                            <div>
                                <span class="rota-metodo">{{ $rota['metodo'] }}</span>
                                <span class="rota-endpoint">{{ $rota['endpoint'] }}</span>
                            </div>
                            <div style="margin-top: 8px; color: #6c757d; font-size: 0.9rem;">
                                {{ $rota['controller'] }}
                            </div>
                            <div class="rota-descricao">{{ $rota['descricao'] }}</div>
                        </div>
                    @endforeach
                    </div>
                @endif
                
                @if(isset($documentacao['rotas']['api']) && count($documentacao['rotas']['api']) > 0)
                    <h4 style="color: var(--orcacidade-accent); margin: 30px 0 20px 0; font-weight: 600;">
                        <i class="fas fa-code"></i> Rotas API
                    </h4>
                    <div class="rotas-grid">
                    @foreach($documentacao['rotas']['api'] as $rota)
                        <div class="rota-item">
                            <div>
                                <span class="rota-metodo">{{ $rota['metodo'] }}</span>
                                <span class="rota-endpoint">{{ $rota['endpoint'] }}</span>
                            </div>
                            <div style="margin-top: 8px; color: #6c757d; font-size: 0.9rem;">
                                {{ $rota['controller'] }}
                            </div>
                            <div class="rota-descricao">{{ $rota['descricao'] }}</div>
                        </div>
                    @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
    
    <!-- Arquivos -->
    @if(isset($documentacao['arquivos']))
        <div class="secao">
            <div class="secao-header">
                <i class="fas fa-folder"></i> Arquivos Envolvidos
            </div>
            <div class="secao-content">
                @foreach($documentacao['arquivos'] as $tipo => $arquivos)
                    <h4 style="color: var(--orcacidade-accent); margin: 25px 0 15px 0; font-weight: 600; text-transform: capitalize;">
                        <i class="fas fa-file"></i> {{ str_replace('_', ' ', $tipo) }}
                    </h4>
                    @foreach($arquivos as $arquivo)
                        <div class="arquivo-item">{{ $arquivo }}</div>
                    @endforeach
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Tabelas do Banco -->
    @if(isset($documentacao['tabelas_banco']))
        <div class="secao">
            <div class="secao-header">
                <i class="fas fa-database"></i> Tabelas do Banco de Dados
            </div>
            <div class="secao-content">
                @foreach($documentacao['tabelas_banco'] as $tabela)
                    <div class="tabela-item">
                        <div class="tabela-nome">{{ $tabela['nome'] }}</div>
                        <div class="tabela-descricao">{{ $tabela['descricao'] }}</div>
                        
                        <h5 style="color: var(--orcacidade-accent); margin: 20px 0 15px 0; font-weight: 600;">
                            <i class="fas fa-list"></i> Campos Principais
                        </h5>
                        <div class="tabela-campos">
                            @foreach($tabela['campos_principais'] as $campo)
                                <div class="campo-item">{{ $campo }}</div>
                            @endforeach
                        </div>
                        
                        @if(isset($tabela['chaves']))
                            <h5 style="color: var(--orcacidade-accent); margin: 20px 0 15px 0; font-weight: 600;">
                                <i class="fas fa-key"></i> Chaves
                            </h5>
                            <div style="margin-bottom: 15px;">
                                <strong>Primária:</strong> {{ $tabela['chaves']['primaria'] }}
                            </div>
                            @if(isset($tabela['chaves']['estrangeiras']) && count($tabela['chaves']['estrangeiras']) > 0)
                                <div>
                                    <strong>Estrangeiras:</strong>
                                    @foreach($tabela['chaves']['estrangeiras'] as $fk)
                                        <div class="arquivo-item" style="margin-top: 8px;">{{ $fk }}</div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                        
                        @if(isset($tabela['relacionamentos']) && count($tabela['relacionamentos']) > 0)
                            <h5 style="color: var(--orcacidade-accent); margin: 20px 0 15px 0; font-weight: 600;">
                                <i class="fas fa-project-diagram"></i> Relacionamentos
                            </h5>
                            @foreach($tabela['relacionamentos'] as $rel)
                                <div class="lista-item">
                                    <strong>{{ $rel['tabela'] }}</strong> ({{ $rel['tipo'] }}) - {{ $rel['descricao'] }}
                                </div>
                            @endforeach
                        @endif
                        
                        @if(isset($tabela['integracoes']) && count($tabela['integracoes']) > 0)
                            <h5 style="color: var(--orcacidade-accent); margin: 20px 0 15px 0; font-weight: 600;">
                                <i class="fas fa-plug"></i> Integrações Externas
                            </h5>
                            @foreach($tabela['integracoes'] as $integ)
                                <div class="lista-item">
                                    <strong>{{ $integ['sistema'] }}</strong> - {{ $integ['descricao'] }}
                                    <br><small style="color: #6c757d;">Campo: {{ $integ['campo'] }}</small>
                                </div>
                            @endforeach
                        @endif
                        
                        @if(isset($tabela['indices']) && count($tabela['indices']) > 0)
                            <h5 style="color: var(--orcacidade-accent); margin: 20px 0 15px 0; font-weight: 600;">
                                <i class="fas fa-search"></i> Índices
                            </h5>
                            <div class="tabela-campos">
                                @foreach($tabela['indices'] as $indice)
                                    <div class="campo-item">{{ $indice }}</div>
                                @endforeach
                            </div>
                        @endif
                        
                        @if(isset($tabela['constraints']) && count($tabela['constraints']) > 0)
                            <h5 style="color: var(--orcacidade-accent); margin: 20px 0 15px 0; font-weight: 600;">
                                <i class="fas fa-shield-alt"></i> Constraints
                            </h5>
                            <div class="tabela-campos">
                                @foreach($tabela['constraints'] as $constraint)
                                    <div class="campo-item">{{ $constraint }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Regras de Negócio -->
    @if(isset($documentacao['regras_negocio']))
        <div class="secao">
            <div class="secao-header">
                <i class="fas fa-clipboard-list"></i> Regras de Negócio
            </div>
            <div class="secao-content">
                @foreach($documentacao['regras_negocio'] as $regra)
                    <div class="lista-item">{{ $regra }}</div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Funcionalidades -->
    @if(isset($documentacao['funcionalidades']))
        <div class="secao">
            <div class="secao-header">
                <i class="fas fa-cogs"></i> Funcionalidades
            </div>
            <div class="secao-content">
                @foreach($documentacao['funcionalidades'] as $funcionalidade)
                    <div class="lista-item">{{ $funcionalidade }}</div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Fluxo de Uso -->
    @if(isset($documentacao['fluxo_uso']))
        <div class="secao">
            <div class="secao-header">
                <i class="fas fa-sync-alt"></i> Fluxo de Uso
            </div>
            <div class="secao-content">
                @foreach($documentacao['fluxo_uso'] as $index => $passo)
                    <div class="lista-item">
                        <strong>{{ $index + 1 }}.</strong> {{ $passo }}
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Interface -->
    @if(isset($documentacao['interface']))
        <div class="secao">
            <div class="secao-header">
                <i class="fas fa-palette"></i> Interface/UX/UI
            </div>
            <div class="secao-content">
                <div class="interface-info">
                    <div class="interface-item">
                        <div class="interface-label">Layout</div>
                        <div class="interface-value">{{ $documentacao['interface']['layout'] }}</div>
                    </div>
                    <div class="interface-item">
                        <div class="interface-label">Responsivo</div>
                        <div class="interface-value">{{ $documentacao['interface']['responsivo'] ? 'Sim' : 'Não' }}</div>
                    </div>
                </div>
                
                <h5 style="color: var(--orcacidade-accent); margin: 25px 0 15px 0; font-weight: 600;">
                    <i class="fas fa-puzzle-piece"></i> Componentes
                </h5>
                @foreach($documentacao['interface']['componentes'] as $componente)
                    <div class="lista-item">{{ $componente }}</div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Processos Especiais -->
    @if(isset($documentacao['processos_especiais']))
        <div class="secao">
            <div class="secao-header">
                <i class="fas fa-tools"></i> Processos Especiais
            </div>
            <div class="secao-content">
                @foreach($documentacao['processos_especiais'] as $processo)
                    <div class="lista-item">{{ $processo }}</div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Cálculos e Fórmulas -->
    @if(isset($documentacao['calculos_formulas']) && count($documentacao['calculos_formulas']) > 0)
        <div class="secao">
            <div class="secao-header">
                <i class="fas fa-calculator"></i> Cálculos e Fórmulas
            </div>
            <div class="secao-content">
                @foreach($documentacao['calculos_formulas'] as $formula)
                    @if(is_array($formula))
                        <div class="formula-item">
                            <div class="formula-nome">{{ $formula['nome'] }}</div>
                            <div class="formula-texto">{{ $formula['formula'] }}</div>
                            <div>{{ $formula['descricao'] }}</div>
                        </div>
                    @else
                        <div class="formula-item">{{ $formula }}</div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection 