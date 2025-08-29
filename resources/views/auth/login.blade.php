@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/login.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="login-container">
                <!-- Seção Esquerda - Branding -->
                <div class="login-branding">
                    <div class="branding-content">
                        <div class="brand-logo">
                            <i class="fas fa-city"></i>
                            <h1 class="brand-title">OrçaCidade</h1>
                        </div>
                        
                        <div class="brand-tagline">
                            <h2>Sistema de Preços para Orçamento de Obras</h2>
                            <p>Orçamentos de Obras a partir de Bases Oficiais</p>
                        </div>
                        
                        <div class="brand-features">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Bases Oficiais</h4>
                                    <p>DER-PR e SINAPI</p>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-calculator"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Cálculos Precisos</h4>
                                    <p>Orçamentos detalhados e confiáveis</p>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Para Técnicos</h4>
                                    <p>Interface intuitiva para profissionais</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Elementos Visuais Decorativos -->
                    <div class="branding-decoration">
                        <div class="decoration-circle circle-1"></div>
                        <div class="decoration-circle circle-2"></div>
                        <div class="decoration-circle circle-3"></div>
                    </div>
                </div>
                
                <!-- Seção Direita - Formulário -->
                <div class="login-form-section">
                    <div class="form-container">
                        <div class="form-header">
                            <h2 class="form-title">Acesse sua conta</h2>
                            <p class="form-subtitle">Entre com suas credenciais para continuar</p>
                        </div>
                        
                        <form method="POST" action="{{ route('api.auth.login') }}">
                            @csrf

                            <!-- Campo Email -->
                            <div class="form-group">
                                <div class="input-container">
                                    <div class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <input 
                                        id="email" 
                                        type="text" 
                                        class="form-input @error('email') is-invalid @enderror" 
                                        name="email" 
                                        placeholder="Digite seu usuário ou e-mail"
                                        value="{{ old('email') }}"
                                        required 
                                        autocomplete="username" 
                                        autofocus 
                                    >
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Campo Senha -->
                            <div class="form-group">
                                <div class="input-container">
                                    <div class="input-icon">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <input 
                                        id="password" 
                                        type="password" 
                                        class="form-input @error('password') is-invalid @enderror" 
                                        name="password" 
                                        placeholder="Digite sua senha"
                                        required 
                                        autocomplete="current-password" 
                                    >
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Opções -->
                            <div class="form-options">
                                <label class="checkbox-wrapper">
                                    <input class="checkbox-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-label">Lembrar de mim</span>
                                </label>
                                
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="forgot-link">Esqueci a senha</a>
                                @endif
                            </div>

                            <!-- Botão de Login -->
                            <button type="submit" class="btn-login">
                                <i class="fas fa-arrow-right"></i>
                                Entrar no Sistema
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection