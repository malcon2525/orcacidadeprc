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
import JWTUtils from '../utils/jwt.js';

export default {
    data() {
        return {
            isAuthenticated: false,
            userName: 'Usuário',
            currentDateTime: '',
            dateTimeInterval: null
        }
    },
    
    mounted() {
        this.checkAuth();
        this.updateDateTime();
        this.dateTimeInterval = setInterval(this.updateDateTime, 60000);
    },
    
    beforeUnmount() {
        if (this.dateTimeInterval) {
            clearInterval(this.dateTimeInterval);
        }
    },
    
    methods: {
        checkAuth() {
            const token = JWTUtils.getToken();
            if (token && !JWTUtils.isTokenExpired()) {
                this.isAuthenticated = true;
                const userData = localStorage.getItem('user_data');
                if (userData) {
                    const user = JSON.parse(userData);
                    this.userName = user.name || 'Usuário';
                }
            } else {
                this.isAuthenticated = false;
                this.userName = 'Usuário';
            }
        },
        
        updateDateTime() {
            const now = new Date();
            this.currentDateTime = now.toLocaleDateString('pt-BR') + ' ' + now.toLocaleTimeString('pt-BR', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        },
        
        logout() {
            fetch('/api/logout', {
                method: 'POST',
                headers: JWTUtils.getAuthHeaders()
            }).finally(() => {
                JWTUtils.logout();
                window.location.href = '/login';
            });
        }
    }
}
</script>

<style scoped>
/* ===== HEADER SUPER SIMPLES ===== */
.header {
    height: 60px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #f8f9fa 100%);
    border-bottom: 1px solid #e0e0e0;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 10000;
}

/* Barra colorida no topo */
.header-top {
    height: 3px;
    background: linear-gradient(90deg, #5EA853 0%, #19578a 100%);
}

/* Conteúdo principal */
.header-content {
    height: 57px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
}

/* Logo */
.logo {
    display: flex;
    align-items: center;
    gap: 8px;
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

/* Usuário */
.user {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 6px;
    border: 1px solid #e9ecef;
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
}

.logout:hover {
    background: #e9ecef;
    color: #495057;
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
</style> 