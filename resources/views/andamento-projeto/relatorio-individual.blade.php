@extends('layouts.app')

@section('title', $dadosModulo['titulo'] . ' | OrçaCidade')

@push('styles')
<style>
    .relatorio-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .relatorio-titulo {
        text-align: center;
        color: #263238;
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    
    .relatorio-objetivo {
        text-align: center;
        color: #263238;
        font-size: 1.1rem;
        margin-bottom: 3rem;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }
    
    .cards-container {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(44,79,161,0.07);
        border: 1.5px solid #e0e0e0;
        padding: 2rem;
        margin-bottom: 3rem;
    }
    
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }
    
    .card-item {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem 1rem;
        text-align: center;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .card-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .card-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #43a047;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .card-label {
        font-size: 0.95rem;
        color: #263238;
        font-weight: 600;
    }
    
    .tabela-container {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(44,79,161,0.07);
        border: 1.5px solid #e0e0e0;
        padding: 2rem;
        margin-bottom: 3rem;
        overflow-x: auto;
    }
    
    .tabela-pf {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95rem;
    }
    
    .tabela-pf th {
        background: #f8f9fa;
        color: #263238;
        font-weight: 700;
        padding: 1rem;
        text-align: center;
        border-bottom: 2px solid #dee2e6;
    }
    
    .tabela-pf td {
        padding: 0.8rem;
        text-align: center;
        border-bottom: 1px solid #dee2e6;
    }
    
    .tabela-pf tr:nth-child(even) td {
        background: #f8f9fa;
    }
    
    .tabela-pf .total-row td {
        background: #e8f5e8;
        font-weight: 700;
        color: #43a047;
    }
    
    .funcionalidades-container {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(44,79,161,0.07);
        border: 1.5px solid #e0e0e0;
        padding: 2rem;
        margin-bottom: 3rem;
    }
    
    .funcionalidades-lista {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .funcionalidades-lista li {
        padding: 0.8rem 0;
        border-bottom: 1px solid #f0f0f0;
        color: #263238;
        font-size: 1rem;
        position: relative;
        padding-left: 2rem;
    }
    
    .funcionalidades-lista li:before {
        content: "•";
        color: #43a047;
        font-weight: bold;
        font-size: 1.5rem;
        position: absolute;
        left: 0;
        top: 0.5rem;
    }
    
    .funcionalidades-lista li:last-child {
        border-bottom: none;
    }
    
    .btn-voltar {
        background: #6c757d;
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .btn-voltar:hover {
        background: #5a6268;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }
    
    @media (max-width: 768px) {
        .cards-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .relatorio-titulo {
            font-size: 1.8rem;
        }
        
        .relatorio-objetivo {
            font-size: 1rem;
        }
    }
    
    @media (max-width: 480px) {
        .cards-grid {
            grid-template-columns: 1fr;
        }
        
        .cards-container,
        .tabela-container,
        .funcionalidades-container {
            padding: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="relatorio-container">
    <h1 class="relatorio-titulo">
        <i class="fas fa-chart-bar"></i> {{ $dadosModulo['titulo'] }}
    </h1>
    
    <p class="relatorio-objetivo">
        {{ $dadosModulo['objetivo'] }}
    </p>

    <!-- Cards Quantitativos -->
    <div class="cards-container">
        <div class="cards-grid">
            <div class="card-item">
                <span class="card-number">{{ $dadosModulo['caracteristicas_quantitativas']['rotas_web'] }}</span>
                <div class="card-label">Rotas Web</div>
            </div>
            <div class="card-item">
                <span class="card-number">{{ $dadosModulo['caracteristicas_quantitativas']['rotas_api'] }}</span>
                <div class="card-label">Rotas API</div>
            </div>
            <div class="card-item">
                <span class="card-number">{{ $dadosModulo['caracteristicas_quantitativas']['controllers'] }}</span>
                <div class="card-label">Controllers</div>
            </div>
            <div class="card-item">
                <span class="card-number">{{ $dadosModulo['caracteristicas_quantitativas']['componentes'] }}</span>
                <div class="card-label">Componentes</div>
            </div>
            <div class="card-item">
                <span class="card-number">{{ $dadosModulo['caracteristicas_quantitativas']['tabelas_banco'] }}</span>
                <div class="card-label">Tabelas Banco</div>
            </div>
            <div class="card-item">
                <span class="card-number">{{ $dadosModulo['caracteristicas_quantitativas']['services'] }}</span>
                <div class="card-label">Services</div>
            </div>
            <div class="card-item">
                <span class="card-number">{{ $dadosModulo['caracteristicas_quantitativas']['models'] }}</span>
                <div class="card-label">Models</div>
            </div>
            <div class="card-item">
                <span class="card-number">{{ $dadosModulo['total_pf'] }}</span>
                <div class="card-label">Pontos de Função</div>
            </div>
        </div>
    </div>

    <!-- Tabela de Pontos de Função -->
    <div class="tabela-container">
        <table class="tabela-pf">
            <thead>
                <tr>
                    <th>Tipo de Função</th>
                    <th>Quantidade</th>
                    <th>PF por função</th>
                    <th>Total PF</th>
                    <th>Justificativa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dadosModulo['detalhamento_pontos_funcao'] as $pf)
                <tr>
                    <td>{{ $pf['tipo_funcao'] }}</td>
                    <td>{{ $pf['quantidade'] }}</td>
                    <td>{{ $pf['pf_por_funcao'] }}</td>
                    <td>{{ $pf['total_pf'] }}</td>
                    <td>{{ $pf['justificativa'] }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;"><strong>Total</strong></td>
                    <td><strong>{{ $dadosModulo['total_pf'] }}</strong></td>
                    <td><strong>{{ $dadosModulo['observacao_pf'] }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Principais Funcionalidades -->
    <div class="funcionalidades-container">
        <h3 style="color: #263238; margin-bottom: 1.5rem; font-weight: 600;">Principais Funcionalidades:</h3>
        <ul class="funcionalidades-lista">
            @foreach($dadosModulo['principais_funcionalidades'] as $funcionalidade)
            <li>{{ $funcionalidade }}</li>
            @endforeach
        </ul>
    </div>

    <div style="text-align: center;">
        <a href="{{ route('andamento-projeto.relatorios') }}" class="btn-voltar">
            <i class="fas fa-arrow-left"></i>
            Voltar para Relatórios
        </a>
    </div>
</div>
@endsection 