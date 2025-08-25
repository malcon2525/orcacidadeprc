@extends('layouts.app')
@section('title', 'Fases e Sprints - Andamento do Projeto')

@push('styles')
<style>
    /* Marco Header - Estilo moderno */
    .marco-header {
        background: linear-gradient(135deg, #43a047, #2e7d32);
        color: white;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        box-shadow: 0 4px 16px rgba(67, 160, 71, 0.3);
    }
    .marco-numero {
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
    }
    .marco-titulo {
        font-size: 1.3rem;
        font-weight: 600;
        margin: 0;
    }
    
    .sprint-card {
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(38,50,56,0.06);
        margin-bottom: 2rem;
        background: #fff;
        border: 1px solid #e8e8e8;
        overflow: hidden;
    }
    .sprint-title {
        color: #263238;
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 0.3rem;
    }
    
    /* Chips de Status Modernos */
    .fase-row-compact {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        margin-bottom: 1.5rem;
    }
    .fase-chip {
        display: flex;
        align-items: center;
        border-radius: 20px;
        border: none;
        font-size: 0.9rem;
        font-weight: 500;
        padding: 0.4rem 1rem;
        color: #fff;
        min-width: 100px;
        gap: 0.4rem;
        line-height: 1.2;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }
    .fase-chip.concluido {
        background: linear-gradient(135deg, #43a047, #2e7d32);
        color: #fff;
    }
    .fase-chip.andamento {
        background: linear-gradient(135deg, #ffa726, #f57c00);
        color: #fff;
    }
    .fase-chip.pendente {
        background: #e0e0e0;
        color: #666;
        box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }
    .fase-chip .fase-status {
        font-size: 1rem;
        margin-left: 0.2rem;
        font-weight: 600;
    }
    /* Barra de Progresso */
    .progress-container {
        margin-bottom: 1.5rem;
    }
    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .progress-title {
        color: #263238;
        font-weight: 600;
        font-size: 0.95rem;
    }
    .progress-count {
        color: #666;
        font-size: 0.9rem;
        font-weight: 500;
    }
    .progress-bar-custom {
        height: 8px;
        background: #e8e8e8;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #43a047, #2e7d32);
        border-radius: 4px;
        transition: width 0.3s ease;
    }
    
    /* Lista de Atividades Moderna */
    .entrega-item {
        border-radius: 8px;
        padding: 0.8rem 1rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        font-weight: 500;
        font-size: 0.95rem;
        gap: 0.8rem;
        border: 1px solid #e8e8e8;
        background: #fff;
        transition: all 0.2s ease;
    }
    .entrega-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transform: translateY(-1px);
    }
    .entrega-item.concluido {
        background: #f8fcf8;
        border-color: #c8e6c9;
        color: #2e7d32;
    }
    .entrega-item.concluido .entrega-texto {
        text-decoration: line-through;
        opacity: 0.8;
    }
    .entrega-item.execucao {
        background: #fffdf7;
        border-color: #ffe0b2;
        color: #f57c00;
    }
    .entrega-item.pendente {
        background: #fafafa;
        border-color: #e0e0e0;
        color: #666;
    }
    
    /* Status Badge Moderno */
    .status-badge {
        padding: 0.25rem 0.6rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-left: auto;
    }
    .status-badge.concluido {
        background: #e8f5e9;
        color: #2e7d32;
    }
    .status-badge.execucao {
        background: #fff8e1;
        color: #f57c00;
    }
    .status-badge.pendente {
        background: #f5f5f5;
        color: #666;
    }
    
    /* Ícones de Status */
    .status-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .status-icon.concluido {
        background: #43a047;
        color: #fff;
    }
    .status-icon.execucao {
        background: #ffa726;
        color: #fff;
    }
    .status-icon.pendente {
        background: #e0e0e0;
        color: #999;
    }
    
    /* Responsividade */
    @media (max-width: 768px) {
        .marco-header {
            padding: 0.8rem 1rem;
            gap: 0.6rem;
        }
        .marco-numero {
            width: 32px;
            height: 32px;
            font-size: 0.9rem;
        }
        .marco-titulo {
            font-size: 1.1rem;
        }
        .fase-row-compact {
            gap: 0.4rem;
        }
        .fase-chip {
            min-width: 80px;
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
        }
        .entrega-item {
            padding: 0.6rem 0.8rem;
            font-size: 0.9rem;
        }
        .status-icon {
            width: 18px;
            height: 18px;
            font-size: 0.6rem;
        }
        .status-badge {
            padding: 0.2rem 0.5rem;
            font-size: 0.7rem;
        }
        .progress-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.3rem;
        }
    }
    .entrega-item.pendente {
        background: #f5f5f5;
        color: #757575;
        text-decoration: none;
    }
    .entrega-item .form-check-input {
        margin-right: 0.7em;
        accent-color: #43a047;
    }
    .entrega-item .badge {
        font-size: 0.95em;
        font-weight: 500;
        margin-left: 0.5em;
    }
    .entregas-header {
        font-weight: 600;
        color: #263238;
        margin-bottom: 0.5em;
        display: flex;
        align-items: center;
        gap: 0.5em;
    }
    .entregas-header .form-check-input {
        margin-right: 0.5em;
    }
    .entregas-contador {
        color: #888;
        font-size: 0.98em;
        margin-left: auto;
        font-weight: 500;
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
    .titulo-fases-sprints {
        color: #263238;
        font-weight: 800;
        letter-spacing: 1px;
        text-align: center;
        margin-bottom: 0.5rem;
        margin-top: 1rem;
        font-size: 2.1rem;
        line-height: 1.2;
    }
    .titulo-fases-sprints i { 
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
    
    /* Timeline Styles */
    .timeline-container {
        background: #fff;
        border-radius: 16px;
        padding: 3rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 24px rgba(38,50,56,0.08);
        border: 1px solid #e0e0e0;
    }
    .timeline-wrapper {
        position: relative;
        overflow-x: auto;
        padding: 2rem 0;
    }
    .timeline {
        position: relative;
        max-width: 1000px;
        margin: 0 auto;
    }
    .timeline-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 4rem;
        position: relative;
    }
    .timeline-row:last-child {
        margin-bottom: 0;
    }
    .timeline-line {
        position: absolute;
        top: 25%;
        left: 60px;
        right: 60px;
        height: 4px;
        background: linear-gradient(to right, #43a047 30%, #ffa726 30% 40%, #e0e0e0 40%);
        z-index: 1;
        border-radius: 2px;
        transform: translateY(-50%);
    }
    .timeline-row:nth-child(2) .timeline-line {
        background: #e0e0e0;
    }
    .timeline-item {
        position: relative;
        z-index: 2;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 1rem 0.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }
    .timeline-item:hover:not(.andamento) {
        transform: translateY(-3px);
    }
    .timeline-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        margin: 0 auto 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.3rem;
        border: 4px solid;
        background: #fff;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        position: relative;
        z-index: 2;
    }
    .timeline-circle.concluido {
        background: #43a047;
        border-color: #43a047;
        color: #fff;
        box-shadow: 0 4px 16px rgba(67,160,71,0.3);
    }
    .timeline-circle.andamento {
        background: #ffa726;
        border-color: #ffa726;
        color: #fff;
        box-shadow: 0 4px 16px rgba(255,167,38,0.3);
        animation: pulse 2s infinite;
    }
    .timeline-circle.pendente {
        background: #fff;
        border-color: #e0e0e0;
        color: #888;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .timeline-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #263238;
        max-width: 120px;
        margin: 0 auto;
        line-height: 1.3;
        text-align: center;
    }
    .timeline-item.concluido .timeline-title {
        color: #43a047;
        font-weight: 700;
    }
    .timeline-item.andamento .timeline-title {
        color: #ffa726;
        font-weight: 700;
    }
    .timeline-item.pendente .timeline-title {
        color: #888;
    }
    
    /* Animação para marco atual */
    @keyframes pulse {
        0% { box-shadow: 0 4px 16px rgba(255,167,38,0.3); }
        50% { box-shadow: 0 6px 24px rgba(255,167,38,0.6); }
        100% { box-shadow: 0 4px 16px rgba(255,167,38,0.3); }
    }
    
    /* Layout responsivo da timeline */
    @media (max-width: 768px) {
        .timeline-row {
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }
        .timeline-line {
            display: none;
        }
        .timeline-circle {
            width: 50px;
            height: 50px;
            font-size: 1.1rem;
        }
        .timeline-title {
            font-size: 0.85rem;
            max-width: 100px;
        }
        .timeline-item {
            flex: 0 0 calc(50% - 0.5rem);
            max-width: 150px;
        }
        .titulo-fases-sprints {
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
    
    /* Accordion Styles */
    .marco-accordion {
        border: none !important;
        border-radius: 16px !important;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 24px rgba(38,50,56,0.08);
        overflow: hidden;
    }
    .marco-accordion-header {
        background: #f8f9fa !important;
        border: none !important;
        padding: 0 !important;
    }
    .marco-accordion-button {
        background: #f8f9fa !important;
        border: none !important;
        padding: 1.2rem 1.5rem !important;
        color: #263238 !important;
        font-weight: 700 !important;
        font-size: 1.2rem !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        position: relative;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        width: 100%;
    }
    .marco-accordion-button:not(.collapsed) {
        background: #43a047 !important;
        color: #fff !important;
    }
    .marco-accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23263238'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e") !important;
    }
    .marco-accordion-button:not(.collapsed)::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e") !important;
    }
    .marco-accordion-body {
        background: #fff !important;
        border: none !important;
        padding: 0 !important;
    }
    .marco-numero {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        background: #43a047;
        color: #fff;
        border-radius: 50%;
        font-weight: 700;
        font-size: 1.1rem;
        margin-right: 1rem;
    }
    .marco-accordion-button:not(.collapsed) .marco-numero {
        background: #fff;
        color: #43a047;
    }
    
    /* Número específico para marcos em execução */
    .marco-numero.andamento {
        background: #ffa726;
        color: #fff;
    }
    .marco-accordion-button:not(.collapsed) .marco-numero.andamento {
        background: #fff;
        color: #ffa726;
    }
    .marco-badge-accordion {
        background: #43a048bd;
        color: #fff;
        font-size: 0.85rem;
        font-weight: 500;
        padding: 0.50em 1.0em;
        border-radius: 5px;
        flex-shrink: 0;
        margin-right: 10px
    }
    .marco-title-group {
        display: flex;
        align-items: center;
        flex: 1;
    }
    .marco-accordion-button:not(.collapsed) .marco-badge-accordion {
        background: #fff;
        color: #43a047;
    }
    
    /* Badge específico para marcos em execução */
    .marco-badge-accordion.andamento {
        background: #ffa726;
        color: #fff;
    }
    .marco-accordion-button:not(.collapsed) .marco-badge-accordion.andamento {
        background: #fff;
        color: #ffa726;
    }
    
    /* Badge específico para marcos em planejamento (5-10) */
    .marco-badge-accordion.planejamento {
        background: #e0e0e0;
        color: #666;
    }
    .marco-accordion-button:not(.collapsed) .marco-badge-accordion.planejamento {
        background: #fff;
        color: #666;
    }
    
    /* Número específico para marcos em planejamento (5-10) */
    .marco-numero.planejamento {
        background: #e0e0e0;
        color: #666;
    }
    .marco-accordion-button:not(.collapsed) .marco-numero.planejamento {
        background: #fff;
        color: #666;
    }
    
    /* Header específico para marcos em planejamento (5-10) */
    .marco-accordion-header.planejamento .marco-accordion-button {
        background: #f5f5f5 !important;
        color: #666 !important;
    }
    .marco-accordion-header.planejamento .marco-accordion-button:not(.collapsed) {
        background: #e0e0e0 !important;
        color: #666 !important;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/andamento-projeto.js') }}"></script>
@endpush

@section('content')
<div class="container py-4" style="max-width: 900px;">
    <!-- Header da Página -->
    <div class="page-header">
        <a href="{{ route('andamento-projeto.index') }}" class="btn-voltar">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
        
        <h1 class="titulo-fases-sprints mb-2">
            <i class="fas fa-tasks"></i> Fases e Sprints
        </h1>
        <p class="page-subtitle">
            Acompanhe o andamento dos principais marcos do sistema.<br>
            Veja o consolidado geral e acesse os detalhes de cada módulo.
        </p>
    </div>

    <!-- Timeline de Marcos -->
    <div class="timeline-container">
        <h3 class="mb-4 text-center" style="color: #263238; font-weight: 700;">Linha do Tempo do Projeto</h3>
        <div class="timeline-wrapper">
            <div class="timeline">
                <!-- Primeira linha: Marcos 1-5 -->
                <div class="timeline-row">
                    <div class="timeline-line"></div>
                    <div class="timeline-item concluido" onclick="toggleAccordion('marco1')">
                        <div class="timeline-circle concluido">1</div>
                        <div class="timeline-title">Implantação da Arquitetura</div>
                    </div>
                    <div class="timeline-item concluido" onclick="toggleAccordion('marco2')">
                        <div class="timeline-circle concluido">2</div>
                        <div class="timeline-title">Pré-Orçamento</div>
                    </div>
                    <div class="timeline-item andamento" onclick="toggleAccordion('marco3')">
                        <div class="timeline-circle andamento">3</div>
                        <div class="timeline-title">Gerenciamento de Orçamentos</div>
                    </div>
                    <div class="timeline-item concluido" onclick="toggleAccordion('marco4')">
                        <div class="timeline-circle concluido">4</div>
                        <div class="timeline-title">Usuários e Permissões</div>
                    </div>
                    <div class="timeline-item pendente" onclick="toggleAccordion('marco5')">
                        <div class="timeline-circle pendente">5</div>
                        <div class="timeline-title">Planejamento das etapas do Orçamento</div>
                    </div>
                </div>
                
                <!-- Segunda linha: Marcos 6-10 -->
                <div class="timeline-row">
                    <div class="timeline-line"></div>
                    <div class="timeline-item pendente" onclick="toggleAccordion('marco6')">
                        <div class="timeline-circle pendente">6</div>
                        <div class="timeline-title">Análise de Orçamento</div>
                    </div>
                    <div class="timeline-item pendente" onclick="toggleAccordion('marco7')">
                        <div class="timeline-circle pendente">7</div>
                        <div class="timeline-title">Cronogramas</div>
                    </div>
                    <div class="timeline-item pendente" onclick="toggleAccordion('marco8')">
                        <div class="timeline-circle pendente">8</div>
                        <div class="timeline-title">Exportação de Documentos e Aprovações</div>
                    </div>
                    <div class="timeline-item pendente" onclick="toggleAccordion('marco9')">
                        <div class="timeline-circle pendente">9</div>
                        <div class="timeline-title">Planilha de Licitação e Medição</div>
                    </div>
                    <div class="timeline-item pendente" onclick="toggleAccordion('marco10')">
                        <div class="timeline-circle pendente">10</div>
                        <div class="timeline-title">Importação de Planilha Ganhadora</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acordeões dos Marcos -->
    <div class="accordion" id="marcosAccordion">
        <!-- Marco 1 -->
        <div class="accordion-item marco-accordion">
            <h2 class="accordion-header marco-accordion-header" id="headingMarco1">
                <button class="accordion-button marco-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#marco1" aria-expanded="false" aria-controls="marco1">
                    <div class="marco-title-group">
                        <span class="marco-numero">1</span>
                        Implantação da Arquitetura
                    </div>
                    <span class="marco-badge-accordion">Marco 1</span>
                </button>
            </h2>
            <div id="marco1" class="accordion-collapse collapse" aria-labelledby="headingMarco1" data-bs-parent="#marcosAccordion">
                <div class="accordion-body marco-accordion-body">
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 1:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Setup Inicial</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip concluido">Análise <span class="fase-status">✔</span></span>
                                <span class="fase-chip concluido">Codificação <span class="fase-status">✔</span></span>
                                <span class="fase-chip concluido">Testes <span class="fase-status">✔</span></span>
                                <span class="fase-chip concluido">Finalizado <span class="fase-status">✔</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">7/7 concluídas</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Configuração do ambiente Laravel 9
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Estrutura inicial de diretórios e arquivos
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Configuração do banco de dados MySQL
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Setup do Vue.js 3 com Bootstrap 5
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Sistema básico de autenticação e middleware
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Layout base da aplicação
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Configuração do padrão de rotas e controllers e componentes
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marco 2 -->
        <div class="accordion-item marco-accordion">
            <h2 class="accordion-header marco-accordion-header" id="headingMarco2">
                <button class="accordion-button marco-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#marco2" aria-expanded="false" aria-controls="marco2">
                    <div class="marco-title-group">
                        <span class="marco-numero">2</span>
                        Pré-Orçamento
                    </div>
                    <span class="marco-badge-accordion">Marco 2</span>
                </button>
            </h2>
            <div id="marco2" class="accordion-collapse collapse" aria-labelledby="headingMarco2" data-bs-parent="#marcosAccordion">
                <div class="accordion-body marco-accordion-body">
                    
                    <!-- Sprint 1 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 1:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Importação de Tabelas Oficiais</h4>
                            
                            <!-- Chips de Status -->
                            <div class="fase-row-compact">
                                <span class="fase-chip concluido">
                                    <span class="fase-status">✓</span> Análise
                                </span>
                                <span class="fase-chip concluido">
                                    <span class="fase-status">✓</span> Codificação
                                </span>
                                <span class="fase-chip concluido">
                                    <span class="fase-status">✓</span> Testes
                                </span>
                                <span class="fase-chip concluido">
                                    <span class="fase-status">✓</span> Finalizado
                                </span>
                            </div>
                            
                            <!-- Barra de Progresso -->
                            <div class="progress-container">
                                <div class="progress-header">
                                    <span class="progress-title">Atividades/Entregas</span>
                                    <span class="progress-count">4 de 4 concluídas</span>
                                </div>
                                <div class="progress-bar-custom">
                                    <div class="progress-fill" style="width: 100%;"></div>
                                </div>
                            </div>
                            
                            <!-- Lista de Atividades -->
                            <div class="entrega-item concluido">
                                <div class="status-icon concluido">✓</div>
                                <span class="entrega-texto">Planejamento e implementação das tabelas no banco de dados</span>
                                <span class="status-badge concluido">Concluído</span>
                            </div>
                            <div class="entrega-item concluido">
                                <div class="status-icon concluido">✓</div>
                                <span class="entrega-texto">Planejamento e implementação dos padrões de projeto</span>
                                <span class="status-badge concluido">Concluído</span>
                            </div>
                            <div class="entrega-item concluido">
                                <div class="status-icon concluido">✓</div>
                                <span class="entrega-texto">Módulo de Importação DERPR</span>
                                <span class="status-badge concluido">Concluído</span>
                            </div>
                            <div class="entrega-item concluido">
                                <div class="status-icon concluido">✓</div>
                                <span class="entrega-texto">Módulo de Importação SINAPI</span>
                                <span class="status-badge concluido">Concluído</span>
                            </div>
                        </div>
                    </div>
                    <!-- Sprint 2 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 2:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Módulos Iniciais do Sistema</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip concluido">Análise <span class="fase-status">✔</span></span>
                                <span class="fase-chip concluido">Codificação <span class="fase-status">✔</span></span>
                                <span class="fase-chip concluido">Testes <span class="fase-status">✔</span></span>
                                <span class="fase-chip concluido">Finalizado <span class="fase-status">✔</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">10/10 concluídas</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Análise e Levantamento de Requisitos
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Planejamento e implementação das tabelas no banco de dados
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Planejamento e implementação dos padrões de projeto
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Gerenciamento de Municípios
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Gerenciamento de Entidades Orçamentárias
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Gerenciamento de Composições Próprias
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Gerenciamento de Cotações
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Gerenciamento de BDI
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Gerenciamento de Estrutura de Orçamentos
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Módulos para cálculo de Custo de Transporte - DMT
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marcos 3-10 com estrutura base -->
        <!-- Marco 3 -->
        <div class="accordion-item marco-accordion">
            <h2 class="accordion-header marco-accordion-header" id="headingMarco3">
                <button class="accordion-button marco-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#marco3" aria-expanded="false" aria-controls="marco3">
                    <div class="marco-title-group">
                        <span class="marco-numero andamento">3</span>
                        Gerenciamento de Orçamentos
                    </div>
                    <span class="marco-badge-accordion andamento">Marco 3</span>
                </button>
            </h2>
            <div id="marco3" class="accordion-collapse collapse" aria-labelledby="headingMarco3" data-bs-parent="#marcosAccordion">
                <div class="accordion-body marco-accordion-body">
                    <!-- Sprint 1 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 1:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Análise</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip andamento">Análise <span class="fase-status">⏳</span></span>
                                <span class="fase-chip pendente">Codificação <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Testes <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Finalizado <span class="fase-status">☐</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">0/5 concluídas</span>
                            </div>
                            <div class="entrega-item execucao">
                                <input class="form-check-input" type="checkbox" disabled>
                                Análise e Levantamento de Requisitos
                                <span class="badge bg-warning text-dark">EM EXECUÇÃO</span>
                            </div>
                            <div class="entrega-item execucao">
                                <input class="form-check-input" type="checkbox" disabled>
                                Desenho do protótipo de telas
                                <span class="badge bg-warning text-dark">EM EXECUÇÃO</span>
                            </div>
                            <div class="entrega-item execucao">
                                <input class="form-check-input" type="checkbox" disabled>
                                Planejamento e implementação das tabelas no banco de dados
                                <span class="badge bg-warning text-dark">EM EXECUÇÃO</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Planejamento e implementação dos padrões de projeto
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Documentação e relatórios
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                        </div>
                    </div>
                    <!-- Sprint 2 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 2:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Implementação do Front-end Básico</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip andamento">Análise <span class="fase-status">⏳</span></span>
                                <span class="fase-chip pendente">Codificação <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Testes <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Finalizado <span class="fase-status">☐</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">0/2 concluídas</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Implementação e codificação dos wireframes projetados
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Implementação básica dos componentes Vue
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                        </div>
                    </div>
                    <!-- Sprint 3 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 3:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Gerenciamento de Orçamentos</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip andamento">Análise <span class="fase-status">⏳</span></span>
                                <span class="fase-chip pendente">Codificação <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Testes <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Finalizado <span class="fase-status">☐</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">0/11 concluídas</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Módulo de importação dos serviços por subgrupos
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Módulo de gerenciamento de planilha orçamentária
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Recurso de gerenciamento e informações do orçamento
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Recurso de importação de DMT / BDI
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Recurso de importação de composições oficiais
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Recurso de importação de cotações
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Recurso de importação de composições próprias
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Recurso de cadastro de memorial de cálculo por composição
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Recurso de gerenciamento/agrupamento por objetos
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Recurso de gerenciamento/agrupamento de lotes
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Módulo de importação de modelos de orçamento
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marco 4 -->
        <div class="accordion-item marco-accordion">
            <h2 class="accordion-header marco-accordion-header" id="headingMarco4">
                <button class="accordion-button marco-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#marco4" aria-expanded="false" aria-controls="marco4">
                    <div class="marco-title-group">
                        <span class="marco-numero">4</span>
                        Gerenciamento de Usuários e Permissões de Acesso
                    </div>
                    <span class="marco-badge-accordion">Marco 4</span>
                </button>
            </h2>
            <div id="marco4" class="accordion-collapse collapse" aria-labelledby="headingMarco4" data-bs-parent="#marcosAccordion">
                <div class="accordion-body marco-accordion-body">
                    <!-- Sprint 1 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 1:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Integração com Active Directory (AD)</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip concluido">Análise <span class="fase-status">✔</span></span>
                                <span class="fase-chip concluido">Codificação <span class="fase-status">✔</span></span>
                                <span class="fase-chip concluido">Testes <span class="fase-status">✔</span></span>
                                <span class="fase-chip concluido">Finalizado <span class="fase-status">✔</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">3/3 concluídas</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Configuração e conexão com o AD
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Sincronização Manual
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Sincronização Automática
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                        </div>
                    </div>
                    <!-- Sprint 2 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 2:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Gerenciamento de Usuários e Permissões de Acesso</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip concluido">Análise <span class="fase-status">✔</span></span>
                                <span class="fase-chip concluido">Codificação <span class="fase-status">✔</span></span>
                                <span class="fase-chip concluido">Testes <span class="fase-status">✔</span></span>
                                <span class="fase-chip concluido">Finalizado <span class="fase-status">✔</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">6/6 concluídas</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Análise e Levantamento de Requisitos
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Planejamento e implementação das tabelas no banco de dados
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Planejamento e implementação dos padrões de projeto
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Gerenciamento de Usuários (Local e AD)
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Gerenciamento de Papeis de Usuários
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                            <div class="entrega-item concluido">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                Gerenciamento de Permissões de Acesso
                                <span class="badge bg-success">CONCLUÍDO</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marco 5 -->
        <div class="accordion-item marco-accordion">
            <h2 class="accordion-header marco-accordion-header planejamento" id="headingMarco5">
                <button class="accordion-button marco-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#marco5" aria-expanded="false" aria-controls="marco5">
                    <div class="marco-title-group">
                        <span class="marco-numero planejamento">5</span>
                        Planejamento das etapas do Orçamento
                    </div>
                    <span class="marco-badge-accordion planejamento">Marco 5</span>
                </button>
            </h2>
            <div id="marco5" class="accordion-collapse collapse" aria-labelledby="headingMarco5" data-bs-parent="#marcosAccordion">
                <div class="accordion-body marco-accordion-body">
                    <!-- Sprint 1 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 1:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Análise</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip andamento">Análise <span class="fase-status">⏳</span></span>
                                <span class="fase-chip pendente">Codificação <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Testes <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Finalizado <span class="fase-status">☐</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">0/5 concluídas</span>
                            </div>
                            <div class="entrega-item execucao">
                                <input class="form-check-input" type="checkbox" disabled>
                                Análise e Levantamento de Requisitos
                                <span class="badge bg-warning text-dark">EM EXECUÇÃO</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Desenho do protótipo de telas
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Planejamento e implementação das tabelas no banco de dados
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Planejamento e implementação dos padrões de projeto
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Documentação e relatórios
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                        </div>
                    </div>
                    <!-- Sprint 2 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 2:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Implementação do Planejamento</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip pendente">Análise <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Codificação <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Testes <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Finalizado <span class="fase-status">☐</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">0/0 concluídas</span>
                            </div>
                            <p class="text-muted mb-0">Atividades a serem definidas...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marco 6 -->
        <div class="accordion-item marco-accordion">
            <h2 class="accordion-header marco-accordion-header planejamento" id="headingMarco6">
                <button class="accordion-button marco-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#marco6" aria-expanded="false" aria-controls="marco6">
                    <div class="marco-title-group">
                        <span class="marco-numero planejamento">6</span>
                        Análise de Orçamento
                    </div>
                    <span class="marco-badge-accordion planejamento">Marco 6</span>
                </button>
            </h2>
            <div id="marco6" class="accordion-collapse collapse" aria-labelledby="headingMarco6" data-bs-parent="#marcosAccordion">
                <div class="accordion-body marco-accordion-body">
                    <!-- Sprint 1 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 1:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Análise</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip andamento">Análise <span class="fase-status">⏳</span></span>
                                <span class="fase-chip pendente">Codificação <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Testes <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Finalizado <span class="fase-status">☐</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">0/5 concluídas</span>
                            </div>
                            <div class="entrega-item execucao">
                                <input class="form-check-input" type="checkbox" disabled>
                                Análise e Levantamento de Requisitos
                                <span class="badge bg-warning text-dark">EM EXECUÇÃO</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Desenho do protótipo de telas
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Planejamento e implementação das tabelas no banco de dados
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Planejamento e implementação dos padrões de projeto
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Documentação e relatórios
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                        </div>
                    </div>
                    <!-- Sprint 2 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 2:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Ferramentas de Análise</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip pendente">Análise <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Codificação <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Testes <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Finalizado <span class="fase-status">☐</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">0/0 concluídas</span>
                            </div>
                            <p class="text-muted mb-0">Atividades a serem definidas...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marco 7 -->
        <div class="accordion-item marco-accordion">
            <h2 class="accordion-header marco-accordion-header planejamento" id="headingMarco7">
                <button class="accordion-button marco-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#marco7" aria-expanded="false" aria-controls="marco7">
                    <div class="marco-title-group">
                        <span class="marco-numero planejamento">7</span>
                        Cronogramas
                    </div>
                    <span class="marco-badge-accordion planejamento">Marco 7</span>
                </button>
            </h2>
            <div id="marco7" class="accordion-collapse collapse" aria-labelledby="headingMarco7" data-bs-parent="#marcosAccordion">
                <div class="accordion-body marco-accordion-body">
                    <!-- Sprint 1 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 1:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Análise</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip andamento">Análise <span class="fase-status">⏳</span></span>
                                <span class="fase-chip pendente">Codificação <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Testes <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Finalizado <span class="fase-status">☐</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">0/5 concluídas</span>
                            </div>
                            <div class="entrega-item execucao">
                                <input class="form-check-input" type="checkbox" disabled>
                                Análise e Levantamento de Requisitos
                                <span class="badge bg-warning text-dark">EM EXECUÇÃO</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Desenho do protótipo de telas
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Planejamento e implementação das tabelas no banco de dados
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Planejamento e implementação dos padrões de projeto
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Documentação e relatórios
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                        </div>
                    </div>
                    <!-- Sprint 2 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 2:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Gerenciamento de Cronogramas</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip pendente">Análise <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Codificação <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Testes <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Finalizado <span class="fase-status">☐</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">0/0 concluídas</span>
                            </div>
                            <p class="text-muted mb-0">Atividades a serem definidas...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marco 8 -->
        <div class="accordion-item marco-accordion">
            <h2 class="accordion-header marco-accordion-header planejamento" id="headingMarco8">
                <button class="accordion-button marco-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#marco8" aria-expanded="false" aria-controls="marco8">
                    <div class="marco-title-group">
                        <span class="marco-numero planejamento">8</span>
                        Geração de Documentos e Alçada de Aprovação
                    </div>
                    <span class="marco-badge-accordion planejamento">Marco 8</span>
                </button>
            </h2>
            <div id="marco8" class="accordion-collapse collapse" aria-labelledby="headingMarco8" data-bs-parent="#marcosAccordion">
                <div class="accordion-body marco-accordion-body">
                    <!-- Sprint 1 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 1:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Análise</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip andamento">Análise <span class="fase-status">⏳</span></span>
                                <span class="fase-chip pendente">Codificação <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Testes <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Finalizado <span class="fase-status">☐</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">0/5 concluídas</span>
                            </div>
                            <div class="entrega-item execucao">
                                <input class="form-check-input" type="checkbox" disabled>
                                Análise e Levantamento de Requisitos
                                <span class="badge bg-warning text-dark">EM EXECUÇÃO</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Desenho do protótipo de telas
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Planejamento e implementação das tabelas no banco de dados
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Planejamento e implementação dos padrões de projeto
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Documentação e relatórios
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                        </div>
                    </div>
                    <!-- Sprint 2 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 2:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Sistema de Documentos e Aprovações</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip pendente">Análise <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Codificação <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Testes <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Finalizado <span class="fase-status">☐</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">0/0 concluídas</span>
                            </div>
                            <p class="text-muted mb-0">Atividades a serem definidas...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marco 9 -->
        <div class="accordion-item marco-accordion">
            <h2 class="accordion-header marco-accordion-header planejamento" id="headingMarco9">
                <button class="accordion-button marco-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#marco9" aria-expanded="false" aria-controls="marco9">
                    <div class="marco-title-group">
                        <span class="marco-numero planejamento">9</span>
                        Geração de Arquivos para Licitação e Medição
                    </div>
                    <span class="marco-badge-accordion planejamento">Marco 9</span>
                </button>
            </h2>
            <div id="marco9" class="accordion-collapse collapse" aria-labelledby="headingMarco9" data-bs-parent="#marcosAccordion">
                <div class="accordion-body marco-accordion-body">
                    <!-- Sprint 1 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 1:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Análise</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip andamento">Análise <span class="fase-status">⏳</span></span>
                                <span class="fase-chip pendente">Codificação <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Testes <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Finalizado <span class="fase-status">☐</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">0/5 concluídas</span>
                            </div>
                            <div class="entrega-item execucao">
                                <input class="form-check-input" type="checkbox" disabled>
                                Análise e Levantamento de Requisitos
                                <span class="badge bg-warning text-dark">EM EXECUÇÃO</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Desenho do protótipo de telas
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Planejamento e implementação das tabelas no banco de dados
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Planejamento e implementação dos padrões de projeto
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                            <div class="entrega-item pendente">
                                <input class="form-check-input" type="checkbox" disabled>
                                Documentação e relatórios
                                <span class="badge bg-secondary">PENDENTE</span>
                            </div>
                        </div>
                    </div>
                    <!-- Sprint 2 -->
                    <div class="card sprint-card mb-4">
                        <div class="card-body">
                            <div class="sprint-title mb-1">Sprint 2:</div>
                            <h4 class="fw-bold mb-3" style="color:#263238;">Exportação para Licitação e Medição</h4>
                            <div class="fase-row-compact mb-3 align-items-center">
                                <span class="fase-chip pendente">Análise <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Codificação <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Testes <span class="fase-status">☐</span></span>
                                <span class="fase-chip pendente">Finalizado <span class="fase-status">☐</span></span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center entregas-header mb-2">
                                <input class="form-check-input" type="checkbox" disabled>
                                Atividades/Entregas:
                                <span class="entregas-contador">0/0 concluídas</span>
                            </div>
                            <p class="text-muted mb-0">Atividades a serem definidas...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marco 10 -->
        <div class="accordion-item marco-accordion">
            <h2 class="accordion-header marco-accordion-header planejamento" id="headingMarco10">
                <button class="accordion-button marco-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#marco10" aria-expanded="false" aria-controls="marco10">
                    <div class="marco-title-group">
                        <span class="marco-numero planejamento">10</span>
                        Importação de Planilha Ganhadora
                    </div>
                    <span class="marco-badge-accordion planejamento">Marco 10</span>
                </button>
            </h2>
            <div id="marco10" class="accordion-collapse collapse" aria-labelledby="headingMarco10" data-bs-parent="#marcosAccordion">
                <div class="accordion-body marco-accordion-body">
                    <div class="p-4 text-center">
                        <div class="mb-3">
                            <i class="fas fa-file-import fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">Marco em Desenvolvimento</h5>
                        <p class="text-muted mb-0">As sprints e atividades deste marco estão sendo detalhadas...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 