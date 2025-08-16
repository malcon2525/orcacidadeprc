@extends('layouts.app')

@section('content')
<div class="home-dashboard">
    <div class="dashboard-card">
        <!-- Seção de boas-vindas - CENTRALIZADA E PROMINENTE -->
        <div class="welcome-header">
            <div class="welcome-icon-large">
                <i class="fas fa-cog"></i>
            </div>
            <h1 class="welcome-title">Olá, {{ Auth::user()->name ?? 'Usuário' }}</h1>
            <p class="welcome-subtitle">Nosso sistema está pronto para você</p>
        </div>

        <!-- Cards de informação - LATERAIS E EQUILIBRADOS -->
        <div class="info-section">
            <div class="info-card-left">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <span class="card-label">DATA ATUAL</span>
                </div>
                <div class="card-content">
                    <span class="card-value">{{ now()->locale('pt_BR')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>
                </div>
            </div>
            
            <div class="info-card-right">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <span class="card-label">HORÁRIO</span>
                </div>
                <div class="card-content">
                    <span class="card-value" id="current-time">{{ now()->format('H:i:s') }}</span>
                </div>
            </div>
        </div>

        <!-- Status do sistema - CENTRALIZADO E ELEGANTE -->
        <div class="status-section">
            <div class="status-badge">
                <div class="status-indicator"></div>
                <span>Sistema Online</span>
            </div>
        </div>
    </div>
</div>
@endsection