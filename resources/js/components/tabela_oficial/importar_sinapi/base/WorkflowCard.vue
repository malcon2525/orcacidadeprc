<template>
  <div class="workflow-card" :class="cardClasses">
    <div class="card-header-section">
      <div class="step-indicator">
        <div class="step-number" :class="stepClasses">
          <i v-if="status === 'completed'" class="fas fa-check"></i>
          <i v-else-if="status === 'processing'" class="fas fa-spinner fa-spin"></i>
          <span v-else>{{ step }}</span>
        </div>
      </div>
      
      <div class="card-title">
        <h6 class="mb-1 fw-bold">
          <i :class="['fas', icon, 'me-2']"></i>{{ title }}
        </h6>
        <p class="mb-0 text-muted small">{{ description }}</p>
      </div>
      
      <div class="status-badge" v-if="status === 'completed'">
        <i class="fas fa-check me-1"></i>Concluído
      </div>
      <div class="status-badge" v-else-if="status === 'processing'">
        <i class="fas fa-spinner fa-spin me-1"></i>Processando
      </div>
      <div class="status-badge" v-else-if="status === 'error'">
        <i class="fas fa-exclamation-triangle me-1"></i>Erro
      </div>
    </div>
    
    <div class="card-content">
      <slot :status="status" :step-data="stepData"></slot>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    step: { type: Number, required: true },
    status: { type: String, required: true },
    title: { type: String, required: true },
    description: { type: String, required: true },
    icon: { type: String, required: true },
    stepData: { type: Object, default: () => ({}) }
  },
  
  computed: {
    cardClasses() {
      return {
        'card-completed': this.status === 'completed',
        'card-current': this.status === 'available',
        'card-processing': this.status === 'processing',
        'card-error': this.status === 'error'
      }
    },
    
    stepClasses() {
      return {
        'completed': this.status === 'completed',
        'processing': this.status === 'processing',
        'error': this.status === 'error'
      }
    }
  }
}
</script>
