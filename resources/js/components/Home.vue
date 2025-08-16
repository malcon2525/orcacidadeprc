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
    </div>
</template>

<script>
export default {
    name: 'HomeComponent',
    data() {
        return {
            nomeUsuario: 'Usuário',
            currentDate: '',
            currentTime: '',
            user: null
        }
    },
    mounted() {
        this.updateDateTime();
        this.fetchUserData();
        
        // Atualizar horário a cada segundo
        setInterval(() => {
            this.updateDateTime();
        }, 1000);
    },
    methods: {
        updateDateTime() {
            const now = new Date();
            
            // Formatar data
            this.currentDate = now.toLocaleDateString('pt-BR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            // Formatar horário
            this.currentTime = now.toLocaleTimeString('pt-BR');
        },
        
        async fetchUserData() {
            try {
                // Fetch user data from session
                const response = await fetch('/user-data', {
                    credentials: 'include'
                });
                
                if (response.ok) {
                    const userData = await response.json();
                    this.user = userData;
                    this.nomeUsuario = userData.name || 'Usuário';
                }
            } catch (error) {
                console.error('Erro ao buscar dados do usuário:', error);
            }
        }
    }
}
</script>

<style scoped>
.welcome-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.welcome-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.welcome-header {
    display: flex;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #f0f0f0;
}

.welcome-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1.5rem;
    color: white;
    font-size: 2rem;
    box-shadow: 0 8px 20px rgba(24, 87, 138, 0.3);
}

.welcome-text {
    flex: 1;
}

.welcome-title {
    font-size: 2rem;
    font-weight: 700;
    color: #18578A;
    margin-bottom: 0.5rem;
}

.user-name {
    color: #5EA853;
    font-weight: 800;
}

.welcome-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
    margin: 0;
}

.welcome-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.info-card {
    background: white;
    padding: 1.5rem;
    border-radius: 15px;
    border: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.info-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.info-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: white;
    font-size: 1.2rem;
}

.info-content {
    flex: 1;
}

.info-label {
    display: block;
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 600;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    display: block;
    font-size: 1.1rem;
    font-weight: 700;
    color: #18578A;
}

.system-status {
    text-align: center;
    padding: 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    border: 1px solid #e9ecef;
}

.status-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.status-dot {
    width: 12px;
    height: 12px;
    background: #5EA853;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.status-text {
    font-weight: 600;
    color: #5EA853;
    font-size: 1rem;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(94, 168, 83, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(94, 168, 83, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(94, 168, 83, 0);
    }
}

/* Responsividade */
@media (max-width: 768px) {
    .welcome-container {
        padding: 1rem;
    }
    
    .welcome-card {
        padding: 1.5rem;
    }
    
    .welcome-header {
        flex-direction: column;
        text-align: center;
    }
    
    .welcome-icon {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .welcome-title {
        font-size: 1.5rem;
    }
    
    .welcome-info {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .info-card {
        padding: 1rem;
    }
}
</style>
