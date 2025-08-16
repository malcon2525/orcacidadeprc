<template>
    <div class="welcome-container">
        <div class="welcome-card">
            <!-- Header com ícone e mensagem -->
            <div class="welcome-header">
                <div class="welcome-icon">
                    <i class="fas fa-sun"></i>
                </div>
                <div class="welcome-text">
                    <h1 class="welcome-title">
                        Olá, <span class="user-name">{{ nomeUsuario }}</span>
                    </h1>
                    <p class="welcome-subtitle">
                        Nosso sistema está pronto para você
                    </p>
                </div>
            </div>
            
            <!-- Informações do dia -->
            <div class="welcome-info">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Data Atual</span>
                        <span class="info-value">{{ currentDate }}</span>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Horário</span>
                        <span class="info-value">{{ currentTime }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Status do sistema -->
            <div class="system-status">
                <div class="status-indicator">
                    <div class="status-dot"></div>
                    <span class="status-text">Sistema Online</span>
                </div>
            </div>
        </div>
        
        <!-- Elementos decorativos sutis -->
        <div class="decoration-elements">
            <div class="decoration-line line-1"></div>
            <div class="decoration-line line-2"></div>
        </div>
    </div>
</template>

<script>
import JWTUtils from '../utils/jwt.js';
import authMiddleware from '../middleware/auth.js';

export default {
    data() {
        return {
            nomeUsuario: 'Usuário',
            currentTime: '',
            currentDate: ''
        }
    },
    
    methods: {
        setCookie(nome, valor, dias) {
            let data = "";
            if (dias) {
                const d = new Date();
                d.setTime(d.getTime() + (dias * 24 * 60 * 60 * 1000));
                data = "expires=" + d.toUTCString();
            }
            document.cookie = `${nome}=${valor};${data};path=/`;
        },

        getCookie(nome) {
            const nomeEQ = nome + "=";
            const cookies = document.cookie.split(';');
            for (let i = 0; i < cookies.length; i++) {
                let c = cookies[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(nomeEQ) == 0) {
                    return c.substring(nomeEQ.length, c.length);
                }
            }
            return "";
        },
        
        updateDateTime() {
            const now = new Date();
            
            // Formatar hora
            this.currentTime = now.toLocaleTimeString('pt-BR', {
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Formatar data
            this.currentDate = now.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }
    },
    
    async mounted() {
        // Verificar autenticação
        if (!authMiddleware.requireAuth()) {
            return;
        }

        // Atualizar data e hora
        this.updateDateTime();
        setInterval(this.updateDateTime, 60000); // Atualizar a cada minuto

        // Buscar dados do usuário
        try {
            const userData = localStorage.getItem('user_data');
            if (userData) {
                const user = JSON.parse(userData);
                this.nomeUsuario = user.name || 'Usuário';
            } else {
                // Se não tem dados no localStorage, buscar da API
                const response = await fetch('/api/me', {
                    headers: JWTUtils.getAuthHeaders()
                });
                
                if (response.ok) {
                    const user = await response.json();
                    this.nomeUsuario = user.name || 'Usuário';
                    localStorage.setItem('user_data', JSON.stringify(user));
                }
            }
        } catch (error) {
            console.error('Erro ao buscar dados do usuário:', error);
            this.nomeUsuario = 'Usuário';
        }
    }
}
</script>

<style scoped>
/* Container Principal */
.welcome-container {
    height: 50vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    position: relative;
    overflow: hidden;
}

/* Card Principal */
.welcome-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    padding: 2.5rem;
    max-width: 500px;
    width: 100%;
    position: relative;
    z-index: 2;
    border: 1px solid rgba(255, 255, 255, 0.8);
}

/* Header */
.welcome-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.welcome-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #5EA853 0%, #4a8a44 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 15px rgba(94, 168, 83, 0.2);
}

.welcome-icon i {
    font-size: 1.5rem;
    color: white;
    animation: pulse 3s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.welcome-text {
    flex: 1;
}

.welcome-title {
    font-size: 2rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
    line-height: 1.2;
}

.user-name {
    color: #5EA853;
    font-weight: 700;
    position: relative;
}

.user-name::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #5EA853, transparent);
    border-radius: 1px;
}

.welcome-subtitle {
    font-size: 1.1rem;
    color: #6b7280;
    margin: 0;
    font-weight: 400;
}

/* Informações */
.welcome-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.info-card {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.info-card:hover {
    border-color: #5EA853;
    background: #f0fdf4;
    transform: translateY(-1px);
}

.info-icon {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #18578A 0%, #134a73 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.info-icon i {
    font-size: 0.9rem;
    color: white;
}

.info-content {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-label {
    font-size: 0.75rem;
    color: #9ca3af;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1rem;
    color: #374151;
    font-weight: 600;
}

/* Status do Sistema */
.system-status {
    text-align: center;
    padding-top: 1rem;
    border-top: 1px solid #f1f5f9;
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 20px;
}

.status-dot {
    width: 8px;
    height: 8px;
    background: #22c55e;
    border-radius: 50%;
    animation: blink 2s ease-in-out infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.status-text {
    font-size: 0.875rem;
    color: #166534;
    font-weight: 500;
}

/* Elementos Decorativos */
.decoration-elements {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: 1;
}

.decoration-line {
    position: absolute;
    background: linear-gradient(90deg, transparent, rgba(94, 168, 83, 0.1), transparent);
    height: 1px;
}

.line-1 {
    top: 20%;
    left: -50%;
    right: -50%;
    animation: slideRight 8s linear infinite;
}

.line-2 {
    bottom: 30%;
    left: -50%;
    right: -50%;
    animation: slideLeft 10s linear infinite;
}

@keyframes slideRight {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

@keyframes slideLeft {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}

/* Responsividade */
@media (max-width: 768px) {
    .welcome-container {
        padding: 1rem;
        height: 60vh;
    }
    
    .welcome-card {
        padding: 2rem;
        border-radius: 12px;
    }
    
    .welcome-header {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .welcome-icon {
        width: 50px;
        height: 50px;
    }
    
    .welcome-icon i {
        font-size: 1.25rem;
    }
    
    .welcome-title {
        font-size: 1.75rem;
    }
    
    .welcome-subtitle {
        font-size: 1rem;
    }
    
    .welcome-info {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
}

@media (max-width: 480px) {
    .welcome-card {
        padding: 1.5rem;
    }
    
    .welcome-title {
        font-size: 1.5rem;
    }
    
    .info-card {
        padding: 0.75rem;
    }
    
    .info-value {
        font-size: 0.9rem;
    }
}

/* Animações de Entrada */
.welcome-card {
    animation: slideInUp 0.6s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.info-card {
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
}

.info-card:nth-child(1) { animation-delay: 0.2s; }
.info-card:nth-child(2) { animation-delay: 0.3s; }
</style>
