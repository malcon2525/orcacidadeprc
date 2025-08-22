<template>
  <div class="result-display">
    <div class="result-content">
      <h6 class="mb-2 fw-semibold">{{ title }}</h6>
      <p class="mb-3 text-muted small">{{ description }}</p>
      
      <!-- Ações disponíveis -->
      <div v-if="actions && actions.length > 0" class="actions-container">
        <button 
          v-for="action in actions" 
          :key="action.key"
          :class="['btn', action.class]"
          @click="$emit('action', action.key, data)"
        >
          <i :class="['fas', action.icon, 'me-2']"></i>
          {{ action.label }}
        </button>
      </div>
      
      <!-- Dados adicionais -->
      <div v-if="data && Object.keys(data).length > 0" class="data-summary">
        <div v-if="type === 'success'" class="alert alert-success">
          <i class="fas fa-check-circle me-2"></i>
          <strong>Sucesso!</strong> {{ data.message || 'Operação concluída com sucesso.' }}
        </div>
        
        <div v-else-if="type === 'processing'" class="alert alert-info">
          <i class="fas fa-spinner fa-spin me-2"></i>
          <strong>Processando...</strong> {{ data.message || 'Aguarde o processamento.' }}
        </div>
        
        <div v-else-if="type === 'error'" class="alert alert-danger">
          <i class="fas fa-exclamation-triangle me-2"></i>
          <strong>Erro:</strong> {{ data.message || 'Ocorreu um erro durante a operação.' }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    type: { type: String, required: true }, // 'success', 'processing', 'error'
    title: { type: String, required: true },
    description: { type: String, required: true },
    actions: { type: Array, default: () => [] },
    data: { type: Object, default: () => ({}) }
  },
  
  emits: ['action']
}
</script>
