@extends('layouts.app')
@section('title', 'Documentação - Andamento do Projeto')

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
<div class="container py-4" style="max-width: 900px;">
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
    
    <div class="text-center py-5">
        <h2 class="text-muted">Bem-vindo à Documentação!</h2>
    </div>
</div>
@endsection 