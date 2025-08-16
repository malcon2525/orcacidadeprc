@extends('layouts.app')

@section('title', 'Documentação Técnica - Andamento do Projeto')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/andamento-projeto.css') }}">
<style>
    /* Header Styles */
    .page-header {
        margin-bottom: 3rem;
        position: relative;
        padding-top: 1rem;
    }
    .btn-voltar {
        background: none;
        border: none;
        color: #666;
        padding: 0.5rem 0;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        font-size: 0.95rem;
        position: absolute;
        top: 0;
        left: 0;
    }
    .btn-voltar:hover {
        color: #43a047;
        text-decoration: none;
    }
    .titulo-documentacao {
        color: #263238;
        font-weight: 800;
        letter-spacing: 1px;
        text-align: center;
        margin-bottom: 0.5rem;
        margin-top: 1rem;
        font-size: 2.1rem;
        line-height: 1.2;
    }
    .titulo-documentacao i { 
        color: #43a047; 
        font-size: 2.3rem; 
        margin-right: 0.5rem; 
        vertical-align: -2px; 
    }
    .page-subtitle {
        color: #263238;
        font-size: 1.13rem;
        margin-bottom: 2.5rem;
        line-height: 1.5;
        text-align: center;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .documentacao-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .categorias-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .categoria-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        overflow: hidden;
        transition: box-shadow 0.3s ease;
        border: 1px solid #e0e0e0;
    }
    
    .categoria-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .categoria-header {
        padding: 25px;
        color: white;
        text-align: center;
    }
    
    .categoria-header h3 {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 600;
    }
    
    .categoria-header p {
        margin: 10px 0 0 0;
        opacity: 0.9;
        font-size: 0.95rem;
    }
    
    .modulos-lista {
        padding: 25px;
    }
    
    .modulo-item {
        display: block;
        padding: 18px;
        margin-bottom: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        text-decoration: none;
        color: var(--orcacidade-dark);
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        border: 1px solid #e0e0e0;
    }
    
    .modulo-item:hover {
        background: #e9ecef;
        color: var(--orcacidade-accent);
        border-left-color: var(--orcacidade-accent);
        transform: translateX(5px);
        text-decoration: none;
    }
    
    .modulo-item:last-child {
        margin-bottom: 0;
    }
    
    .modulo-titulo {
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 8px;
        color: var(--orcacidade-dark);
    }
    
    .modulo-objetivo {
        font-size: 0.9rem;
        color: #6c757d;
        line-height: 1.5;
    }
    
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }
    
    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        text-align: center;
        border: 1px solid #e0e0e0;
        transition: box-shadow 0.3s ease;
    }
    
    .stat-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--orcacidade-accent);
        margin-bottom: 8px;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 0.95rem;
        font-weight: 500;
    }
    
    /* Cores das categorias são aplicadas via inline style usando as cores do JSON */
    
    /* Layout responsivo */
    @media (max-width: 768px) {
        .titulo-documentacao {
            font-size: 1.7rem;
        }
        .page-subtitle {
            font-size: 1rem;
            margin-bottom: 2rem;
        }
        .page-header {
            margin-bottom: 2rem;
        }
        .btn-voltar {
            position: static;
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-4" style="max-width: 1200px;">
    <!-- Header da Página -->
    <div class="page-header">
        <a href="{{ route('andamento-projeto.index') }}" class="btn-voltar">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
        
        <h1 class="titulo-documentacao mb-2">
            <i class="fas fa-file-alt"></i> Documentação
        </h1>
        <p class="page-subtitle">
            Documentação técnica e guias do sistema OrçaCidade.<br>
            Consulte manuais, especificações e diretrizes do projeto.
        </p>
    </div>
    
    <div class="documentacao-container">
    
    <!-- Categorias e Módulos -->
    <div class="categorias-grid">
        @foreach($modulosData['categorias'] as $categoriaKey => $categoria)
            <div class="categoria-card categoria-{{ $categoriaKey }}" style="border: 2px solid {{ $categoria['cor'] }};">
                <div class="categoria-header" style="background: {{ $categoria['cor'] }} !important;">
                    <h3>{{ $categoria['nome'] }}</h3>
                    <p>{{ $categoria['descricao'] }}</p>
                </div>
                <div class="modulos-lista">
                    @foreach($modulosData['modulos'] as $modulo)
                        @if($modulo['categoria'] === $categoriaKey)
                            <a href="{{ route('documentacao.show', $modulo['modulo']) }}" class="modulo-item">
                                <div class="modulo-titulo">{{ $modulo['titulo'] }}</div>
                                <div class="modulo-objetivo">{{ $modulo['objetivo'] }}</div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    </div>
</div>
@endsection 