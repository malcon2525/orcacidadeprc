<template>
  <div class="result-display" :class="displayClasses">
    <!-- Estado de Sucesso -->
    <div v-if="type === 'success'" class="result-success">
      <h6 class="mt-0 mb-1">{{ title || 'Processamento Concluído!' }}</h6>
      <p class="text-muted mb-3 small">{{ description || `${dataCount} registros processados` }}</p>
      
      <!-- Ações disponíveis -->
      <div class="action-buttons" v-if="actions.length > 0">
        <button 
          v-for="action in actions" 
          :key="action.key"
          :class="['btn', action.class || 'btn-outline-primary']"
          @click="$emit('action', action.key, data)"
          :disabled="action.disabled"
        >
          <i :class="['fas', action.icon, 'me-2']"></i>
          {{ action.label }}
        </button>
      </div>
    </div>

    <!-- Estado de Erro -->
    <div v-else-if="type === 'error'" class="result-error">
      <div class="error-icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <h6 class="mt-2 mb-1 text-danger">{{ title || 'Erro no Processamento' }}</h6>
      <p class="text-muted mb-3 small">{{ description || 'Ocorreu um erro durante o processamento' }}</p>
      
      <!-- Botão de tentar novamente -->
      <button 
        v-if="showRetry"
        class="btn btn-outline-danger"
        @click="$emit('retry')"
      >
        <i class="fas fa-redo me-2"></i>
        Tentar Novamente
      </button>
    </div>

    <!-- Estado de Informação -->
    <div v-else-if="type === 'info'" class="result-info">
      <div class="info-icon">
        <i class="fas fa-info-circle"></i>
      </div>
      <h6 class="mt-2 mb-1">{{ title || 'Informação' }}</h6>
      <p class="text-muted mb-3 small">{{ description }}</p>
      
      <!-- Ações disponíveis -->
      <div class="action-buttons" v-if="actions.length > 0">
        <button 
          v-for="action in actions" 
          :key="action.key"
          :class="['btn', action.class || 'btn-outline-info']"
          @click="$emit('action', action.key, data)"
          :disabled="action.disabled"
        >
          <i :class="['fas', action.icon, 'me-2']"></i>
          {{ action.label }}
        </button>
      </div>
    </div>

    <!-- Estado de Aguardando -->
    <div v-else-if="type === 'waiting'" class="result-waiting">
      <div class="waiting-icon">
        <i class="fas fa-clock"></i>
      </div>
      <h6 class="mt-2 mb-1 text-muted">{{ title || 'Aguardando Processamento' }}</h6>
      <p class="text-muted mb-0 small">{{ description || 'Complete os passos anteriores' }}</p>
    </div>

    <!-- Estado de Processando -->
    <div v-else-if="type === 'processing'" class="result-processing">
      <div class="processing-icon">
        <i class="fas fa-spinner fa-spin"></i>
      </div>
      <h6 class="mt-2 mb-1">{{ title || 'Processando...' }}</h6>
      <p class="text-muted mb-0 small">{{ description || 'Aguarde enquanto processamos os dados' }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ResultDisplay',
  
  props: {
    // Tipo de resultado: 'success' | 'error' | 'info' | 'waiting' | 'processing'
    type: {
      type: String,
      required: true,
      validator: value => ['success', 'error', 'info', 'waiting', 'processing'].includes(value)
    },
    
    // Título do resultado
    title: {
      type: String,
      default: ''
    },
    
    // Descrição do resultado
    description: {
      type: String,
      default: ''
    },
    
    // Dados do resultado
    data: {
      type: [Object, Array, null],
      default: null
    },
    
    // Ações disponíveis
    actions: {
      type: Array,
      default: () => []
    },
    
    // Mostrar botão de tentar novamente em caso de erro
    showRetry: {
      type: Boolean,
      default: true
    }
  },

  computed: {
    // Classes CSS para o display
    displayClasses() {
      return {
        'result-display': true,
        [`result-${this.type}`]: true
      }
    },

    // Contador de dados
    dataCount() {
      if (!this.data) return 0
      if (Array.isArray(this.data)) return this.data.length
      if (typeof this.data === 'object') return Object.keys(this.data).length
      return 0
    }
  },

  emits: ['action', 'retry']
}
</script>

<style scoped>
/* Display Base */
.result-display {
  text-align: center;
  padding: 32px 24px;
  border-radius: 12px;
  min-height: 200px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

/* Estados de Resultado */
.result-success {
  /* Sem background diferenciado - mesma cor dos outros cards */
}

.result-error {
  background: rgba(220, 53, 69, 0.05);
  border: 1px solid rgba(220, 53, 69, 0.2);
}

.result-info {
  background: rgba(24, 87, 138, 0.05);
  border: 1px solid rgba(24, 87, 138, 0.2);
}

.result-waiting {
  background: rgba(108, 117, 125, 0.05);
  border: 1px solid rgba(108, 117, 125, 0.2);
}

.result-processing {
  background: rgba(24, 87, 138, 0.05);
  border: 1px solid rgba(24, 87, 138, 0.2);
}

/* Ícones */
.success-icon,
.error-icon,
.info-icon,
.waiting-icon,
.processing-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  font-size: 24px;
  color: white;
}

.success-icon {
  background: #5EA853;
}

.error-icon {
  background: #DC3545;
}

.info-icon {
  background: #18578A;
}

.waiting-icon {
  background: #6c757d;
}

.processing-icon {
  background: #18578A;
}

/* Títulos */
.result-display h6 {
  font-weight: 600;
  margin-bottom: 16px;
  line-height: 1.3;
}

.result-display p {
  line-height: 1.4;
  margin-bottom: 24px;
}

/* Botões de Ação */
.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 16px;
  width: 100%;
  max-width: 280px;
  margin-top: 8px;
}

.action-buttons .btn {
  border-radius: 12px;
  font-weight: 700;
  padding: 14px 24px;
  font-size: 15px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: none;
  position: relative;
  overflow: hidden;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.action-buttons .btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: left 0.5s;
}

.action-buttons .btn:hover::before {
  left: 100%;
}

.action-buttons .btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.action-buttons .btn:active {
  transform: translateY(0);
  box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

/* Estilos específicos para cada tipo de botão */
.btn-primary {
  background: linear-gradient(135deg, #18578A 0%, #0d4b6b 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(24, 87, 138, 0.3);
}

.btn-primary:hover {
  background: linear-gradient(135deg, #0d4b6b 0%, #18578A 100%);
  box-shadow: 0 8px 25px rgba(24, 87, 138, 0.4);
}

.btn-success {
  background: linear-gradient(135deg, #5EA853 0%, #4c8a3f 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(94, 168, 83, 0.3);
}

.btn-success:hover {
  background: linear-gradient(135deg, #4c8a3f 0%, #5EA853 100%);
  box-shadow: 0 8px 25px rgba(94, 168, 83, 0.4);
}

.btn-outline-primary {
  background: white;
  color: #18578A;
  border: 2px solid #18578A;
  box-shadow: 0 4px 15px rgba(24, 87, 138, 0.2);
}

.btn-outline-primary:hover {
  background: #18578A;
  color: white;
  box-shadow: 0 8px 25px rgba(24, 87, 138, 0.3);
}

.btn-outline-info {
  background: white;
  color: #17a2b8;
  border: 2px solid #17a2b8;
  box-shadow: 0 4px 15px rgba(23, 162, 184, 0.2);
}

.btn-outline-info:hover {
  background: #17a2b8;
  color: white;
  box-shadow: 0 8px 25px rgba(23, 162, 184, 0.3);
}

/* Botão de Tentar Novamente */
.btn-outline-danger {
  background: white;
  color: #DC3545;
  border: 2px solid #DC3545;
  box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
}

.btn-outline-danger:hover {
  background: #DC3545;
  color: white;
  box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
}

/* Responsividade */
@media (max-width: 768px) {
  .result-display {
    padding: 16px;
    min-height: 180px;
  }
  
  .action-buttons {
    max-width: 100%;
  }
  
  .success-icon,
  .error-icon,
  .info-icon,
  .waiting-icon,
  .processing-icon {
    width: 50px;
    height: 50px;
    font-size: 20px;
  }
}
</style>
