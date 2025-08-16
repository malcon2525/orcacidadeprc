<template>
    <div class="header">
        <!-- Barra colorida no topo -->
        <div class="header-top"></div>
        
        <!-- Conteúdo principal -->
        <div class="header-content">
            <!-- Logo simples -->
            <div class="logo">
                <span class="orca">Orça</span><span class="cidade">Cidade</span>
                <span class="subtitle">ORÇAMENTO DE OBRAS</span>
            </div>
            
            <!-- Usuário simples -->
            <div class="user">
                <div class="avatar">👤</div>
                <div class="info">
                    <div class="name">{{ userName }}</div>
                    <div class="time">{{ currentDateTime }}</div>
                </div>
                <button @click="logout" class="logout" title="Sair do Sistema">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            userName: 'Usuário',
            currentDateTime: '',
            dateTimeInterval: null
        }
    },
    
    mounted() {
        this.loadUserData();
        this.updateDateTime();
        this.dateTimeInterval = setInterval(this.updateDateTime, 60000);
    },
    
    beforeUnmount() {
        if (this.dateTimeInterval) {
            clearInterval(this.dateTimeInterval);
        }
    },
    
    methods: {
        loadUserData() {
            // Buscar dados do usuário via API
            fetch('/api/auth/me', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data && data.name) {
                    this.userName = data.name;
                }
            })
            .catch(error => {
                console.log('Erro ao carregar dados do usuário:', error);
                this.userName = 'Usuário';
            });
        },
        
        updateDateTime() {
            const now = new Date();
            this.currentDateTime = now.toLocaleDateString('pt-BR') + ' ' + now.toLocaleTimeString('pt-BR', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        },
        
        logout() {
            // Submeter o formulário de logout oculto
            const logoutForm = document.getElementById('logout-form');
            if (logoutForm) {
                logoutForm.submit();
            }
        }
    }
}
</script>

<style scoped>
/* ===== HEADER HARMÔNICO E SUTIL ===== */
.header {
    height: 60px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #f8f9fa 100%);
    border-bottom: 1px solid #e0e0e0;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 10000;
    display: flex;
    flex-direction: column;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

/* Barra colorida no topo - SUTIL */
.header-top {
    height: 3px;
    background: linear-gradient(90deg, #5EA853 0%, #19578a 100%);
    flex-shrink: 0;
}

/* Conteúdo principal */
.header-content {
    height: 57px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    background: white;
    flex: 1;
}

/* Logo - PEQUENO E SUTIL */
.logo {
    display: flex;
    align-items: center;
    gap: 8px;
    user-select: none;
}

.orca {
    font-size: 20px;
    font-weight: bold;
    color: #5EA853;
    margin-right: -2px;
}

.cidade {
    font-size: 20px;
    font-weight: bold;
    color: #19578a;
    margin-left: -2px;
}

.subtitle {
    font-size: 9px;
    color: #666;
    background: rgba(240, 240, 240, 0.8);
    padding: 3px 6px;
    border-radius: 4px;
    text-transform: uppercase;
    font-weight: bold;
}

/* Usuário - BALA PEQUENA E ELEGANTE */
.user {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.user:hover {
    background: #e9ecef;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.avatar {
    width: 24px;
    height: 24px;
    background: #19578a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    flex-shrink: 0;
}

.info {
    display: flex;
    flex-direction: column;
    gap: 1px;
}

.name {
    font-size: 11px;
    font-weight: 600;
    color: #495057;
    line-height: 1;
}

.time {
    font-size: 9px;
    color: #6c757d;
    line-height: 1;
}

.logout {
    background: none;
    border: none;
    color: #6c757d;
    font-size: 12px;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.logout:hover {
    background: #e9ecef;
    color: #495057;
    transform: scale(1.05);
}

/* Responsivo simples */
@media (max-width: 768px) {
    .header-content {
        padding: 0 15px;
    }
    
    .orca, .cidade {
        font-size: 18px;
    }
    
    .subtitle {
        font-size: 8px;
        padding: 2px 5px;
    }
    
    .user {
        padding: 3px 6px;
        gap: 6px;
    }
    
    .avatar {
        width: 20px;
        height: 20px;
        font-size: 10px;
    }
    
    .name {
        font-size: 10px;
    }
    
    .time {
        font-size: 8px;
    }
    
    .logout {
        padding: 3px;
        font-size: 10px;
    }
}

@media (max-width: 480px) {
    .info {
        display: none;
    }
    
    .user {
        padding: 3px;
        gap: 4px;
    }
}
</style>
