@extends('layouts.app')

@section('title', 'Relatórios do Projeto - OrçaCidade')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/andamento-projeto.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    body { background: #f5f5f5; font-family: 'Inter', 'Segoe UI', 'Roboto', Arial, sans-serif; }
    .orcacidade-consolidado-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(44,79,161,0.08);
        border: 1.5px solid #e0e0e0;
        margin-bottom: 2.5rem;
        padding: 2.2rem 1.5rem 1.7rem 1.5rem;
        max-width: 1100px;
        margin-left: auto;
        margin-right: auto;
    }
    .orcacidade-consolidado-header {
        background: transparent;
        color: #43a047;
        font-size: 1.35rem;
        font-weight: 700;
        text-align: center;
        padding: 0.7rem 0 0.2rem 0;
        letter-spacing: 0.5px;
        border-radius: 14px 14px 0 0;
        border: none;
        margin-bottom: 1.2rem;
    }
    .orcacidade-consolidado-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 2.5rem;
    }
    .orcacidade-consolidado-box {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(44,79,161,0.07);
        border: 1.5px solid #e0e0e0;
        color: #263238;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding: 1.3rem 1.2rem 1.1rem 1.2rem;
        min-width: 120px;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: box-shadow 0.2s, border 0.2s;
    }
    .orcacidade-consolidado-box .display-6 {
        font-size: 2.5rem;
        color: #2e7d32;
        font-weight: 700;
        margin-bottom: 0.2rem;
        letter-spacing: 0.5px;
    }
    .orcacidade-consolidado-box .small {
        color: #757575;
        font-size: 1.07rem;
        font-weight: 500;
        margin-top: 0.2rem;
        letter-spacing: 0.1px;
    }
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
    .titulo-relatorio {
        color: #263238;
        font-weight: 800;
        letter-spacing: 1px;
        text-align: center;
        margin-bottom: 0.5rem;
        margin-top: 1rem;
        font-size: 2.1rem;
        line-height: 1.2;
    }
    .titulo-relatorio i { color: #43a047; font-size: 2.3rem; margin-right: 0.5rem; vertical-align: -2px; }
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
    /* Seção de Relatórios Detalhados */
    .relatorios-detalhados-header {
        text-align: center;
        margin: 3rem 0 2rem 0;
    }
    .relatorios-detalhados-title {
        color: #263238;
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        letter-spacing: 0.5px;
    }
    .relatorios-detalhados-subtitle {
        color: #666;
        font-size: 1.1rem;
        font-weight: 400;
    }
    
    /* Grid de Cards de Relatórios */
    .relatorios-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .relatorio-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid #e8e8e8;
        padding: 2rem 1.5rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        position: relative;
        overflow: hidden;
    }
    
    .relatorio-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        text-decoration: none;
        border-color: #43a047;
    }
    
    .relatorio-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #43a047, #4caf50);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .relatorio-card:hover::before {
        transform: scaleX(1);
    }
    
    .relatorio-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #fff;
        flex-shrink: 0;
        margin-top: 0.2rem;
    }
    
    .relatorio-content {
        flex: 1;
    }
    
    .relatorio-title {
        color: #263238;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.4rem;
        line-height: 1.3;
    }
    
    .relatorio-subtitle {
        color: #666;
        font-size: 0.9rem;
        font-weight: 400;
        line-height: 1.4;
    }
    
    /* Ícones por categoria */
    .icon-clientes { background: linear-gradient(135deg, #43a047, #4caf50); }
    .icon-consulta { background: linear-gradient(135deg, #2196f3, #42a5f5); }
    .icon-importacao { background: linear-gradient(135deg, #ff9800, #ffb74d); }
    .icon-orcamento { background: linear-gradient(135deg, #9c27b0, #ba68c8); }
    .icon-transporte { background: linear-gradient(135deg, #607d8b, #78909c); }
    @media (max-width: 900px) {
        .orcacidade-consolidado-row { gap: 1.2rem; }
        .relatorios-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.2rem;
        }
    }
    @media (max-width: 768px) {
        .orcacidade-consolidado-row { flex-direction: column; gap: 0.7rem; }
        .orcacidade-consolidado-box { min-width: 100px; }
        .titulo-relatorio {
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
        .relatorios-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        .relatorio-card {
            padding: 1.5rem 1.2rem;
        }
        .relatorios-detalhados-title {
            font-size: 1.5rem;
        }
        .relatorios-detalhados-subtitle {
            font-size: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <!-- Header da Página -->
    <div class="page-header">
        <a href="{{ route('andamento-projeto.index') }}" class="btn-voltar">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
        
        <h1 class="titulo-relatorio mb-2">
            <i class="fas fa-chart-bar"></i> Relatórios do Projeto
        </h1>
        <p class="page-subtitle">
            Acompanhe o andamento dos principais módulos do sistema.<br>
            Veja o consolidado geral e acesse os relatórios detalhados de cada módulo.
        </p>
    </div>

    <!-- Consolidado Geral -->
    <div class="orcacidade-consolidado-card">
        <div class="orcacidade-consolidado-header">
            {{ $consolidado['titulo'] }}
        </div>
        <div class="orcacidade-consolidado-row">
            <div class="orcacidade-consolidado-box">
                <div class="display-6">{{ $consolidado['total_modulos'] }}</div>
                <div class="small">Módulos</div>
            </div>
            <div class="orcacidade-consolidado-box">
                <div class="display-6">{{ $consolidado['metricas_gerais']['total_controllers'] }}</div>
                <div class="small">Controllers</div>
            </div>
            <div class="orcacidade-consolidado-box">
                <div class="display-6">{{ $consolidado['metricas_gerais']['total_services'] }}</div>
                <div class="small">Services</div>
            </div>
            <div class="orcacidade-consolidado-box">
                <div class="display-6">{{ $consolidado['metricas_gerais']['total_models'] }}</div>
                <div class="small">Models</div>
            </div>
            <div class="orcacidade-consolidado-box">
                <div class="display-6">{{ $consolidado['metricas_gerais']['total_componentes'] }}</div>
                <div class="small">Componentes</div>
            </div>
            <div class="orcacidade-consolidado-box">
                <div class="display-6">{{ $consolidado['metricas_gerais']['total_pf'] }}</div>
                <div class="small">Total PF</div>
            </div>
        </div>
    </div>

    <!-- Seção de Relatórios Detalhados -->
    <div class="relatorios-detalhados-header">
        <h2 class="relatorios-detalhados-title">Acesse os Relatórios Detalhados</h2>
        <p class="relatorios-detalhados-subtitle">Clique em qualquer módulo para visualizar informações completas</p>
    </div>

    <!-- Grid de Cards de Relatórios -->
    <div class="relatorios-grid">
        @foreach($consolidado['modulos_disponiveis'] as $modulo)
            @php
                $iconClass = 'fas fa-chart-bar';
                $iconCategory = 'icon-orcamento';
                $subtitle = 'Dados do módulo';
                
                // Definir ícone e categoria baseado no nome do módulo
                if (str_contains(strtolower($modulo['nome']), 'municípios')) {
                    $iconClass = 'fas fa-map-marker-alt';
                    $iconCategory = 'icon-clientes';
                    $subtitle = 'Visualizar dados por município';
                } elseif (str_contains(strtolower($modulo['nome']), 'entidades')) {
                    $iconClass = 'fas fa-building';
                    $iconCategory = 'icon-clientes';
                    $subtitle = 'Dados de entidades';
                } elseif (str_contains(strtolower($modulo['nome']), 'consulta der-pr')) {
                    $iconClass = 'fas fa-search';
                    $iconCategory = 'icon-consulta';
                    $subtitle = 'Consultas ao DER-PR';
                } elseif (str_contains(strtolower($modulo['nome']), 'consulta sinapi')) {
                    $iconClass = 'fas fa-search-plus';
                    $iconCategory = 'icon-consulta';
                    $subtitle = 'Consultas à base SINAPI';
                } elseif (str_contains(strtolower($modulo['nome']), 'importação der-pr')) {
                    $iconClass = 'fas fa-cloud-upload-alt';
                    $iconCategory = 'icon-importacao';
                    $subtitle = 'Importação de dados';
                } elseif (str_contains(strtolower($modulo['nome']), 'importação sinapi')) {
                    $iconClass = 'fas fa-cloud-download-alt';
                    $iconCategory = 'icon-importacao';
                    $subtitle = 'Importação de dados';
                } elseif (str_contains(strtolower($modulo['nome']), 'bdi')) {
                    $iconClass = 'fas fa-calculator';
                    $iconCategory = 'icon-orcamento';
                    $subtitle = 'Bonificações e Despesas';
                } elseif (str_contains(strtolower($modulo['nome']), 'composições')) {
                    $iconClass = 'fas fa-layer-group';
                    $iconCategory = 'icon-orcamento';
                    $subtitle = 'Composições personalizadas';
                } elseif (str_contains(strtolower($modulo['nome']), 'cotações')) {
                    $iconClass = 'fas fa-tags';
                    $iconCategory = 'icon-orcamento';
                    $subtitle = 'Cotações de preços';
                } elseif (str_contains(strtolower($modulo['nome']), 'transporte')) {
                    $iconClass = 'fas fa-truck';
                    $iconCategory = 'icon-transporte';
                    $subtitle = 'Custos de transporte';
                } elseif (str_contains(strtolower($modulo['nome']), 'dmt')) {
                    $iconClass = 'fas fa-route';
                    $iconCategory = 'icon-transporte';
                    $subtitle = 'Dados de materiais de transporte';
                } elseif (str_contains(strtolower($modulo['nome']), 'estrutura')) {
                    $iconClass = 'fas fa-sitemap';
                    $iconCategory = 'icon-orcamento';
                    $subtitle = 'Estrutura de orçamento';
                } elseif (str_contains(strtolower($modulo['nome']), 'tipos')) {
                    $iconClass = 'fas fa-list-ul';
                    $iconCategory = 'icon-orcamento';
                    $subtitle = 'Tipos de orçamento';
                }
            @endphp
            
            <a href="{{ route('andamento-projeto.relatorio.individual', ['modulo' => str_replace('.json', '', $modulo['arquivo'])]) }}" 
               class="relatorio-card">
                <div class="relatorio-icon {{ $iconCategory }}">
                    <i class="{{ $iconClass }}"></i>
                </div>
                <div class="relatorio-content">
                    <h3 class="relatorio-title">Relatório de {{ $modulo['nome'] }}</h3>
                    <p class="relatorio-subtitle">{{ $subtitle }}</p>
                </div>
            </a>
        @endforeach
    </div>


</div>
@endsection 