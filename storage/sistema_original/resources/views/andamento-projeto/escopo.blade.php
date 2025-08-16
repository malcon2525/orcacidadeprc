@extends('layouts.app')
@section('title', 'Escopo - Andamento do Projeto')

@push('styles')
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
    .titulo-escopo {
        color: #263238;
        font-weight: 800;
        letter-spacing: 1px;
        text-align: center;
        margin-bottom: 0.5rem;
        margin-top: 1rem;
        font-size: 2.1rem;
        line-height: 1.2;
    }
    .titulo-escopo i { 
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
        max-width: 720px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Estilos específicos do escopo */
    body {
        background-color: #f4f6fa;
        font-family: 'Segoe UI', sans-serif;
    }

    .card-custom {
        background-color: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        margin-bottom: 3rem;
        border: none;
    }

    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #e8f5e8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #43a047;
        margin-bottom: 1rem;
    }

    .image-container {
        border: 2px dashed #ced4da;
        border-radius: .75rem;
        padding: 2rem;
        text-align: center;
        color: #6c757d;
        margin-top: 1.5rem;
        background-color: #f8f9fa;
    }

    .image-container img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .image-placeholder {
        border: 2px dashed #ced4da;
        border-radius: .75rem;
        padding: 2rem;
        text-align: center;
        color: #6c757d;
        margin-top: 1.5rem;
        background-color: #f8f9fa;
    }

    .image-placeholder i {
        font-size: 24px;
        display: block;
        margin-bottom: .5rem;
    }

    .section-title {
        color: #1e2d50;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .escopo-content {
        max-width: 1000px;
        margin: 0 auto;
    }

    .escopo-list {
        color: #6c757d;
        line-height: 1.8;
    }

    .escopo-list li {
        margin-bottom: 0.5rem;
    }
    
    /* Layout responsivo */
    @media (max-width: 768px) {
        .titulo-escopo {
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
        .card-custom {
            padding: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Header da Página -->
    <div class="page-header">
        <a href="{{ route('andamento-projeto.index') }}" class="btn-voltar">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
        
        <h1 class="titulo-escopo mb-2">
            <i class="fas fa-bullseye"></i> Escopo do Projeto
        </h1>
        <p class="page-subtitle">
            O sistema Orçacidade estrutura o processo de criação de orçamento de obras públicas. 
        </p>
    </div>

    <div class="escopo-content">
        <!-- Card: Contexto Geral -->
        <div class="card-custom">
            <div class="icon-circle">
                <i class="fas fa-project-diagram"></i>
            </div>
            <h4 class="section-title">Contexto Geral do Sistema</h4>
            <p class="text-muted">
                O OrçaCidade integra os sistemas administrativos (SAM, Portal dos Municípios) ao fluxo técnico de criação de  orçamentos de obras públicas. A plataforma organiza, documenta e padroniza a criação, análise e entrega da planilha orçamentária de projetos desde de a criação do orçamento  até a emissão dos documentos para licitação e medição.
            </p>
            
            <!-- Imagem do Contexto -->
            @if(file_exists(public_path('assets/images/contexto.png')))
                <div class="image-container">
                    <img src="{{ asset('assets/images/contexto.png') }}" alt="Contexto Geral do Sistema" class="img-fluid">
                    <div class="mt-2">
                        <small class="text-muted">Diagrama do contexto geral do sistema</small>
                    </div>
                </div>
            @else
                <div class="image-placeholder">
                    <i class="fas fa-image"></i>
                    <strong><em>Inserir aqui a imagem "contexto.png"</em></strong><br />
                    <small>Diagrama do contexto geral do sistema</small>
                </div>
            @endif
        </div>

        <!-- Card: Escopo Funcional -->
        <div class="card-custom">
            <div class="icon-circle">
                <i class="fas fa-th-large"></i>
            </div>
            <h4 class="section-title">Escopo Funcional do Sistema</h4>
            <p class="text-muted">
                As funcionalidades do OrçaCidade apoiam diretamente os municípios em:
            </p>
            <ul class="escopo-list">
                <li>Criação de orçamentos com composições oficiais (DER, SINAPI) e composições personalizadas;</li>
                <li>Montagem de lotes e objetos para organização do orçamento;</li>
                <li>Montagem do planejamento das etapas do orçamento;</li>
                <li>Montagem do cronograma fisico-financeiro do orçamento;</li>
                <li>Análise técnica realizada pelo Paranacidade;</li>
                <li>Geração automatizada de documentos de contratação;</li>
                <li>Geração automatizada de planilhas de medições.</li>
            </ul>
            
            <!-- Imagem do Escopo -->
            @if(file_exists(public_path('assets/images/escopo.png')))
                <div class="image-container">
                    <img src="{{ asset('assets/images/escopo.png') }}" alt="Escopo Funcional do Sistema" class="img-fluid">
                    <div class="mt-2">
                        <small class="text-muted">Fluxo funcional do sistema</small>
                    </div>
                </div>
            @else
                <div class="image-placeholder">
                    <i class="fas fa-image"></i>
                    <strong><em>Inserir aqui a imagem "escopo.png"</em></strong><br />
                    <small>Fluxo funcional do sistema</small>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 