<template>
  <div class="workflow-card" :class="cardClasses">
    <!-- Badge de Status - Posicionado acima do card -->
    <div class="status-badge-absolute" :class="statusBadgeClasses">
      <i :class="['fas', statusIcon, 'me-1']"></i>
      {{ statusText }}
    </div>

    <!-- Header do Card -->
    <div class="card-header-section">
      <!-- Indicador de Etapa -->
      <div class="step-indicator">
        <div class="step-number" :class="stepNumberClasses">
          <i v-if="status === 'completed'" class="fas fa-check"></i>
          <i v-else-if="status === 'processing'" class="fas fa-spinner fa-spin"></i>
          <span v-else>{{ step }}</span>
        </div>
      </div>

      <!-- Título e Descrição -->
      <div class="card-title">
        <h6 class="mb-1 fw-bold" :class="titleClasses">
          <i :class="['fas', icon, 'me-2', iconClasses]"></i>
          {{ title }}
        </h6>
        <p class="mb-0 text-muted small">{{ description }}</p>
      </div>
    </div>

    <!-- Conteúdo do Card -->
    <div class="card-content">
      <!-- Slot para conteúdo personalizado -->
      <slot :status="status" :step="step"></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'WorkflowCard',
  
  props: {
    // Número da etapa (1, 2, 3, 4)
    step: {
      type: Number,
      required: true
    },
    
    // Status da etapa: 'blocked' | 'available' | 'processing' | 'completed'
    status: {
      type: String,
      required: true,
      validator: value => ['blocked', 'available', 'processing', 'completed'].includes(value)
    },
    
    // Título do card
    title: {
      type: String,
      required: true
    },
    
    // Descrição do card
    description: {
      type: String,
      required: true
    },
    
    // Ícone do card (classe FontAwesome)
    icon: {
      type: String,
      required: true
    },
    
    // Dados da etapa (opcional)
    stepData: {
      type: [Object, Array, null],
      default: null
    }
  },

  computed: {
    // Classes CSS para o card principal
    cardClasses() {
      return {
        'workflow-card': true,
        [`card-${this.status}`]: true
      }
    },

    // Classes CSS para o número da etapa
    stepNumberClasses() {
      return {
        'step-number': true,
        [`step-${this.status}`]: true
      }
    },

    // Classes CSS para o título
    titleClasses() {
      return {
        'text-muted': this.status === 'blocked'
      }
    },

    // Classes CSS para o ícone
    iconClasses() {
      return {
        'fa-spin': this.status === 'processing'
      }
    },

    // Classes CSS para o badge de status
    statusBadgeClasses() {
      return {
        'status-badge': true,
        [`status-${this.status}`]: true
      }
    },

    // Ícone do status
    statusIcon() {
      switch (this.status) {
        case 'completed':
          return 'fa-check'
        case 'processing':
          return 'fa-spinner fa-spin'
        case 'available':
          return 'fa-play'
        case 'blocked':
          return 'fa-lock'
        default:
          return 'fa-circle'
      }
    },

    // Texto do status
    statusText() {
      switch (this.status) {
        case 'completed':
          return 'Concluído'
        case 'processing':
          return 'Processando'
        case 'available':
          return 'Pronto'
        case 'blocked':
          return 'Bloqueado'
        default:
          return 'Desconhecido'
      }
    }
  }
}
</script>

<style scoped>
/* Card Base */
.workflow-card {
  background: white;
  border-radius: 16px;
  border: 2px solid #e9ecef;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  padding: 20px;
  height: 100%;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: visible;
  padding-top: 35px;
}

/* Estados do Card */
.workflow-card.card-blocked {
  background: #f8f9fa;
  border-color: #e9ecef;
  opacity: 0.7;
  cursor: not-allowed;
}

.workflow-card.card-available {
  background: white;
  border-color: #18578A;
  box-shadow: 0 8px 30px rgba(24, 87, 138, 0.15);
  transform: translateY(-2px);
}

.workflow-card.card-processing {
  background: white;
  border-color: #18578A;
  box-shadow: 0 8px 30px rgba(24, 87, 138, 0.2);
  animation: pulse 2s infinite;
}

.workflow-card.card-completed {
  background: white;
  border-color: #5EA853;
  box-shadow: 0 8px 30px rgba(94, 168, 83, 0.15);
}

/* Header do Card */
.card-header-section {
  display: flex;
  align-items: flex-start;
  margin-bottom: 20px;
  gap: 15px;
  position: relative;
}

/* Indicador de Etapa */
.step-indicator {
  flex-shrink: 0;
  z-index: 2;
}

.step-number {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 16px;
  color: white;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.step-number.step-blocked {
  background: #6c757d;
}

.step-number.step-available {
  background: #18578A;
}

.step-number.step-processing {
  background: #18578A;
}

.step-number.step-completed {
  background: #5EA853;
}

/* Título do Card */
.card-title {
  flex: 1;
  min-width: 0;
  z-index: 1;
}

.card-title h6 {
  color: #18578A;
  margin-bottom: 4px;
  font-size: 16px;
  line-height: 1.3;
}

.card-title .text-muted {
  color: #6c757d !important;
}

.card-title p {
  color: #6c757d;
  font-size: 13px;
  line-height: 1.4;
  margin: 0;
}

/* Badge de Status - Novo posicionamento absoluto */
.status-badge-absolute {
  position: absolute;
  top: -15px;
  right: 20px;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  white-space: nowrap;
  z-index: 10;
  box-shadow: 0 3px 12px rgba(0,0,0,0.15);
  min-width: 90px;
  text-align: center;
  background: white;
  border: 2px solid;
}

/* Badge de Status - Mantido para compatibilidade */
.status-badge {
  padding: 8px 16px;
  border-radius: 25px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  white-space: nowrap;
  flex-shrink: 0;
  z-index: 3;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  min-width: 100px;
  text-align: center;
}

/* Estilos dos badges absolutos para cada estado */
.status-badge-absolute.status-blocked {
  background: white;
  color: #6c757d;
  border-color: #dee2e6;
  box-shadow: 0 3px 12px rgba(108, 117, 125, 0.2);
}

.status-badge-absolute.status-available {
  background: white;
  color: #18578A;
  border-color: #18578A;
  box-shadow: 0 3px 12px rgba(24, 87, 138, 0.25);
}

.status-badge-absolute.status-processing {
  background: white;
  color: #18578A;
  border-color: #18578A;
  box-shadow: 0 3px 12px rgba(24, 87, 138, 0.25);
}

.status-badge-absolute.status-completed {
  background: white;
  color: #5EA853;
  border-color: #5EA853;
  box-shadow: 0 3px 12px rgba(94, 168, 83, 0.25);
}

/* Estilos dos badges originais para cada estado */
.status-badge.status-blocked {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  color: #495057;
  border: 2px solid #dee2e6;
  box-shadow: 0 2px 8px rgba(108, 117, 125, 0.15);
}

.status-badge.status-available {
  background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
  color: #1565c0;
  border: 2px solid #1976d2;
  box-shadow: 0 4px 12px rgba(24, 87, 138, 0.25);
}

.status-badge.status-processing {
  background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
  color: #2e7d32;
  border: 2px solid #388e3c;
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.25);
}

.status-badge.status-completed {
  background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
  color: #2e7d32;
  border: 2px solid #388e3c;
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.25);
}

/* Conteúdo do Card */
.card-content {
  min-height: 140px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding-top: 10px;
  position: relative;
  z-index: 1;
}

/* Animações */
@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.02);
  }
  100% {
    transform: scale(1);
  }
}

/* Hover Effects */
.workflow-card.card-available:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 40px rgba(24, 87, 138, 0.2);
}

.workflow-card.card-completed:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 40px rgba(94, 168, 83, 0.2);
}

/* Responsividade */
@media (max-width: 768px) {
  .workflow-card {
    padding: 16px;
    padding-top: 30px;
  }
  
  .card-header-section {
    flex-direction: column;
    gap: 15px;
    align-items: center;
    text-align: center;
    margin-bottom: 25px;
  }
  
  .status-badge-absolute {
    top: -12px;
    right: 15px;
    min-width: 80px;
    padding: 5px 12px;
    font-size: 9px;
  }
  
  .status-badge {
    align-self: center;
    min-width: 120px;
    padding: 10px 20px;
    font-size: 12px;
  }
  
  .step-number {
    width: 36px;
    height: 36px;
    font-size: 14px;
  }
  
  .card-content {
    min-height: 120px;
    padding-top: 15px;
  }
}

/* Responsividade para telas muito pequenas */
@media (max-width: 480px) {
  .workflow-card {
    padding: 14px;
    padding-top: 28px;
  }
  
  .card-header-section {
    gap: 12px;
    margin-bottom: 20px;
  }
  
  .status-badge-absolute {
    top: -10px;
    right: 10px;
    min-width: 70px;
    padding: 4px 10px;
    font-size: 8px;
  }
  
  .status-badge {
    min-width: 100px;
    padding: 8px 16px;
    font-size: 11px;
  }
  
  .step-number {
    width: 32px;
    height: 32px;
    font-size: 12px;
  }
}
</style>
