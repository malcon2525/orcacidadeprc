<template>
    <!-- Alert de Erro -->
    <div v-if="showError" class="alert alert-danger alert-dismissible fade show position-fixed" 
         style="top: 20px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
        <div class="d-flex align-items-start">
            <div class="flex-shrink-0 me-3">
                <i class="fas fa-exclamation-triangle text-danger"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-2">Atenção</h6>
                <p class="mb-0 small">{{ errorMessage }}</p>
            </div>
            <button type="button" class="btn-close" @click="hideError"></button>
        </div>
    </div>

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
                            
                            <form method="POST" action="" @submit.prevent="login($event)">
                                <input type="hidden" name="_token" :value="csrf_token">

                                <!-- Campo Email -->
                                <div class="form-group">
                                    <div class="input-container">
                                        <div class="input-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <input 
                                            id="email" 
                                            type="email" 
                                            class="form-input" 
                                            name="email" 
                                            placeholder="Digite seu e-mail"
                                            required 
                                            autocomplete="email" 
                                            autofocus 
                                            v-model="email"
                                        >
                                    </div>
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
                                            class="form-input" 
                                            name="password" 
                                            placeholder="Digite sua senha"
                                            required 
                                            autocomplete="current-password" 
                                            v-model="password"
                                        >
                                    </div>
                                </div>

                                <!-- Opções -->
                                <div class="form-options">
                                    <label class="checkbox-wrapper">
                                        <input class="checkbox-input" type="checkbox" name="remember" id="remember">
                                        <span class="checkbox-custom"></span>
                                        <span class="checkbox-label">Lembrar de mim</span>
                                    </label>
                                    
                                    <a href="#" class="forgot-link">Esqueci a senha</a>
                                </div>

                                <!-- Botão de Login -->
                                <button type="submit" class="btn-login" :disabled="isLoading">
                                    <span v-if="isLoading" class="loading-spinner"></span>
                                    <i v-else class="fas fa-arrow-right"></i>
                                    {{ isLoading ? 'Entrando...' : 'Entrar no Sistema' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>


                
               
            </div>
        </div>
    </div>



</template>

<script>
    
    
    import authMiddleware from '../../middleware/auth.js';

    export default {

        props: ['csrf_token'],
        
        data() {
            return {
                email: '',
                password: '',
                showError: false,
                errorMessage: '',
                isLoading: false
            }
        },

        mounted() {
            // Verificar se usuário já está logado
            authMiddleware.guest();
        },
       
        methods: {
            login(e){
                e.preventDefault(); // Prevenir comportamento padrão
                
                if (this.isLoading) return;
                
                this.isLoading = true;
                this.hideError();
                
                let baseUrl = window.apiURL || window.location.origin;
                let urlLogin = baseUrl + '/api/login'
                console.log('URL Login:', urlLogin)

                let configuracao = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        'email': this.email,
                        'password': this.password
                    })
                }
                console.log('Tentando login com:', { email: this.email, password: '***' });
                
                fetch(urlLogin, configuracao)
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.message || 'Credenciais inválidas');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Login successful:', data);
                        if(data.token){
                            // Armazenar token JWT no localStorage
                            localStorage.setItem('jwt_token', data.token);
                            localStorage.setItem('user_data', JSON.stringify(data.user));
                            
                            console.log('Token armazenado, redirecionando...');
                            // Redirecionar para dashboard
                            window.location.href = '/home';
                        } else {
                            this.showErrorAlert('Erro no processo de autenticação. Token não recebido. Tente novamente.');
                        }
                    })
                    .catch(error => {
                        console.error('Erro no login:', error);
                        
                        // Extrair mensagem de erro mais específica
                        let errorMessage = 'Ocorreu um erro durante o processo de login. Tente novamente.';
                        
                        if (error.message) {
                            if (error.message.includes('Não foi possível encontrar uma conta')) {
                                errorMessage = 'Não foi possível encontrar uma conta com este endereço de e-mail. Verifique se o e-mail está correto e tente novamente.';
                            } else if (error.message.includes('senha informada está incorreta')) {
                                errorMessage = 'A senha informada está incorreta. Verifique suas credenciais e tente novamente.';
                            } else if (error.message.includes('conta está temporariamente desativada')) {
                                errorMessage = 'Esta conta está temporariamente desativada. Entre em contato com o administrador do sistema para obter assistência.';
                            } else {
                                errorMessage = error.message;
                            }
                        }
                        
                        // Exibir mensagem de erro mais profissional
                        this.showErrorAlert(errorMessage);
                    })
                    .finally(() => {
                        this.isLoading = false;
                    })
            },
            
            showErrorAlert(message) {
                this.errorMessage = message;
                this.showError = true;
                
                // Auto-hide após 8 segundos
                setTimeout(() => {
                    this.showError = false;
                    this.errorMessage = '';
                }, 8000);
            },
            
                        hideError() {
                this.showError = false;
                this.errorMessage = '';
            }
        },
        
        mounted() {
            // Verificar se usuário já está logado
            authMiddleware.guest();
        }
    }
</script>

<style scoped>
/* Container Principal - Layout de Duas Colunas */
.login-container {
    display: flex;
    height: 80vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    overflow: hidden;
    border-radius: 20px;
    
}

/* Seção Esquerda - Branding */
.login-branding {
    flex: 1;
    background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 2rem;
}

.branding-content {
    position: relative;
    z-index: 2;
    color: white;
    text-align: center;
    max-width: 500px;
    padding: 2rem 0;
}

.brand-logo {
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.brand-logo i {
    font-size: 3rem;
    color: rgba(255, 255, 255, 0.9);
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
}

.brand-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    letter-spacing: -0.5px;
    color: #a7f7aa;
}

.brand-tagline {
    margin-bottom: 2rem;
}

.brand-tagline h2 {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    opacity: 0.95;
}

.brand-tagline p {
    font-size: 1.1rem;
    margin: 0;
    opacity: 0.8;
    font-weight: 400;
}

.brand-features {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.feature-item:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateX(5px);
}

.feature-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.feature-icon i {
    font-size: 1.25rem;
    color: white;
}

.feature-text h4 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
    opacity: 0.95;
}

.feature-text p {
    font-size: 0.85rem;
    margin: 0;
    opacity: 0.8;
    font-weight: 400;
}

/* Elementos Decorativos */
.branding-decoration {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
}

.decoration-circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    animation: float 6s ease-in-out infinite;
}

.circle-1 {
    width: 120px;
    height: 120px;
    top: 10%;
    right: 10%;
    animation-delay: 0s;
}

.circle-2 {
    width: 80px;
    height: 80px;
    bottom: 20%;
    left: 15%;
    animation-delay: 2s;
}

.circle-3 {
    width: 60px;
    height: 60px;
    top: 60%;
    right: 20%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

/* Seção Direita - Formulário */
.login-form-section {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: white;
}

.form-container {
    width: 100%;
    max-width: 400px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    padding: 2.5rem;
    position: relative;
}

.form-container::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, #18578A, #5EA853);
    border-radius: 22px;
    z-index: -1;
    opacity: 0.1;
}

.form-header {
    text-align: center;
    margin-bottom: 2rem;
}

.form-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
}

.form-subtitle {
    font-size: 1rem;
    color: #6b7280;
    margin: 0;
    font-weight: 400;
}

/* Grupos de Formulário */
.form-group {
    margin-bottom: 1.5rem;
}

/* Container do Input */
.input-container {
    position: relative;
    display: flex;
    align-items: center;
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.input-container:focus-within {
    border-color: #5EA853;
    background: white;
    box-shadow: 0 0 0 4px rgba(94, 168, 83, 0.1);
    transform: translateY(-2px);
}

/* Ícone do Input */
.input-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    color: #9ca3af;
    transition: all 0.3s ease;
}

.input-container:focus-within .input-icon {
    color: #5EA853;
}

.input-icon i {
    font-size: 1.1rem;
}

/* Input */
.form-input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 1rem 1rem 1rem 0;
    font-size: 1rem;
    color: #374151;
    outline: none;
    font-weight: 500;
}

.form-input::placeholder {
    color: #9ca3af;
    font-weight: 400;
}

/* Opções do Formulário */
.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

/* Checkbox Customizado */
.checkbox-wrapper {
    display: flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    transition: all 0.3s ease;
}

.checkbox-wrapper:hover {
    transform: translateX(2px);
}

.checkbox-input {
    display: none;
}

.checkbox-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    margin-right: 0.75rem;
    position: relative;
    transition: all 0.3s ease;
    background: white;
}

.checkbox-input:checked + .checkbox-custom {
    background: #5EA853;
    border-color: #5EA853;
    transform: scale(1.1);
}

.checkbox-input:checked + .checkbox-custom::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.checkbox-label {
    font-size: 0.9rem;
    color: #6b7280;
    font-weight: 500;
}

/* Link Esqueci a Senha */
.forgot-link {
    color: #5EA853;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.forgot-link:hover {
    color: #4a8a44;
    text-decoration: none;
    transform: translateY(-1px);
}

/* Botão de Login */
.btn-login {
    width: 100%;
    background: linear-gradient(135deg, #18578A 0%, #134a73 100%);
    color: white;
    border: none;
    padding: 1rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    position: relative;
    overflow: hidden;
}

.btn-login::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.btn-login:hover:not(:disabled)::before {
    left: 100%;
}

.btn-login:hover:not(:disabled) {
    background: linear-gradient(135deg, #5EA853 0%, #4a8a44 100%);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(94, 168, 83, 0.4);
}

.btn-login:active:not(:disabled) {
    transform: translateY(-1px);
}

.btn-login:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Spinner de Loading */
.loading-spinner {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsividade */
@media (max-width: 1024px) {
    .login-container {
        flex-direction: column;
    }
    
    .login-branding {
        padding: 2rem 1.5rem;
        min-height: 40vh;
    }
    
    .brand-title {
        font-size: 2rem;
    }
    
    .brand-tagline h2 {
        font-size: 1.25rem;
    }
    
    .brand-features {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
    }
    
    .feature-item {
        flex: 1;
        min-width: 200px;
        max-width: 250px;
    }
}

@media (max-width: 768px) {
    .login-form-section {
        padding: 1rem;
    }
    
    .form-container {
        padding: 2rem;
        border-radius: 16px;
    }
    
    .form-title {
        font-size: 1.5rem;
    }
    
    .brand-features {
        flex-direction: column;
        gap: 1rem;
    }
    
    .feature-item {
        max-width: none;
    }
}

@media (max-width: 480px) {
    .login-branding {
        padding: 1.5rem 1rem;
        min-height: 35vh;
    }
    
    .brand-title {
        font-size: 1.75rem;
    }
    
    .brand-tagline h2 {
        font-size: 1.1rem;
    }
    
    .brand-tagline p {
        font-size: 1rem;
    }
    
    .form-container {
        padding: 1.5rem;
        margin: 0 0.5rem;
    }
    
    .form-options {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .btn-login {
        padding: 0.875rem 1.5rem;
        font-size: 0.95rem;
    }
}

/* Animações de Entrada */
.login-container {
    animation: fadeInUp 0.6s ease-out;
}

.form-container {
    animation: slideInRight 0.6s ease-out 0.2s both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.form-group {
    animation: fadeIn 0.4s ease-out;
    animation-fill-mode: both;
}

.form-group:nth-child(1) { animation-delay: 0.4s; }
.form-group:nth-child(2) { animation-delay: 0.5s; }
.form-options { animation-delay: 0.6s; }
.btn-login { animation-delay: 0.7s; }

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>


