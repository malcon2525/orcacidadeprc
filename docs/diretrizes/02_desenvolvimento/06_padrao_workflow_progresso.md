# Padrão de Workflow de Progresso - Sistema OrcaCidade

## 📋 **OBJETIVO**
Este documento define o padrão para implementação de workflows de progresso no sistema OrcaCidade, baseado na implementação das 4 abas do módulo DER-PR (Serviços Gerais, Insumos, Fórmulas de Transporte e Gravação no Banco).

## 🏗️ **ARQUITETURA BASE**

### **1. Estrutura Geral**
```
┌─────────────────────────────────────────────────────────────┐
│                    Cabeçalho da Funcionalidade              │
├─────────────────────────────────────────────────────────────┤
│              Barra de Progresso Horizontal                  │
│  [1] ──── [2] ──── [3] ──── [4] ──── [N]                 │
├─────────────────────────────────────────────────────────────┤
│  Card 1    Card 2    Card 3    Card 4    Card N            │
│  [Etapa1]  [Etapa2]  [Etapa3]  [Etapa4]  [EtapaN]         │
├─────────────────────────────────────────────────────────────┤
│              Sistema de Progresso Detalhado                 │
│  (quando necessário para etapas complexas)                  │
├─────────────────────────────────────────────────────────────┤
│              Mensagens de Status/Erro                      │
└─────────────────────────────────────────────────────────────┘
```

### **2. Componentes Obrigatórios**
- **`useWorkflowState.js`** - Composable para gerenciar estados
- **`WorkflowCard.vue`** - Card individual de cada etapa
- **`ResultDisplay.vue`** - Exibição de resultados/estados
- **Barra de Progresso Horizontal** - Indicador visual geral

## 🧩 **COMPONENTES BASE**

### **1. useWorkflowState.js (Composable)**
```javascript
// Gerenciamento centralizado dos estados do workflow
export function useWorkflowState() {
    const stepStates = ref({
        step1: { status: 'available', data: null },
        step2: { status: 'available', data: null },
        step3: { status: 'available', data: null },
        step4: { status: 'available', data: null }
    })
    
    const currentProcessingStep = computed(() => {
        return Object.entries(stepStates.value).find(([_, step]) => 
            step.status === 'processing'
        )?.[0] || null
    })
    
    const overallProgress = computed(() => {
        const totalSteps = 4
        const completedSteps = Object.values(stepStates.value)
            .filter(step => step.completed).length
        
        // Garantir 100% quando todos os passos estiverem concluídos
        if (completedSteps === totalSteps) {
            return 100
        }
        
        return Math.round((completedSteps / totalSteps) * 100)
    })
    
    // Métodos para manipular estados
    const setStepCompleted = (stepKey, data = null) => { ... }
    const setStepProcessing = (stepKey) => { ... }
    const setStepAvailable = (stepKey) => { ... }
    const resetWorkflow = () => { ... }
    
    return {
        stepStates,
        currentProcessingStep,
        computedStepStates,
        overallProgress,
        setStepCompleted,
        setStepProcessing,
        setStepAvailable,
        resetWorkflow
    }
}
```

### **2. WorkflowCard.vue**
```vue
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
        'card-processing': this.status === 'processing'
      }
    },
    
    stepClasses() {
      return {
        'completed': this.status === 'completed',
        'processing': this.status === 'processing'
      }
    }
  }
}
</script>
```

### **3. ResultDisplay.vue**
```vue
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
        <!-- Conteúdo específico baseado no tipo -->
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
```

## 📊 **ESTRUTURA DE ESTADOS**

### **1. Estados das Etapas**
```javascript
// Estados possíveis para cada etapa
const STEP_STATUSES = {
  'available': 'Disponível para execução',
  'processing': 'Em processamento',
  'completed': 'Concluída com sucesso',
  'error': 'Erro durante execução'
}
```

### **2. Transições de Estado**
```
available → processing → completed
     ↓           ↓
   error      error
```

### **3. Progresso Geral**
```javascript
// Cálculo do progresso geral
const overallProgress = computed(() => {
  const totalSteps = 4
  const completedSteps = Object.values(stepStates.value)
    .filter(step => step.completed).length
  
  // Garantir 100% quando todos os passos estiverem concluídos
  if (completedSteps === totalSteps) {
    return 100
  }
  
  return Math.round((completedSteps / totalSteps) * 100)
})
```

## 🎯 **PADRÕES DE IMPLEMENTAÇÃO**

### **1. Estrutura do Componente Principal**
```vue
<template>
  <div>
    <!-- 1. Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h5 class="mb-1 fw-bold text-custom">
          <i class="fas fa-[icon] me-2"></i>[Título da Funcionalidade]
        </h5>
        <p class="text-muted mb-0 small">[Descrição da funcionalidade]</p>
      </div>
    </div>

    <!-- 2. Barra de Progresso Horizontal -->
    <div class="progress-tracker mb-4">
      <div class="progress-line">
        <div class="progress-fill" :style="{ width: overallProgress + '%' }"></div>
      </div>
      <div class="progress-steps">
        <div v-for="step in totalSteps" :key="step" 
             class="progress-step" 
             :class="getStepClasses(step)">
          <div class="step-circle">
            <i v-if="isStepCompleted(step)" class="fas fa-check"></i>
            <i v-else-if="isStepProcessing(step)" class="fas fa-spinner fa-spin"></i>
            <span v-else>{{ step }}</span>
          </div>
          <span class="step-label">{{ getStepLabel(step) }}</span>
        </div>
      </div>
    </div>

    <!-- 3. Sistema de Cards -->
    <div class="row g-4 mb-4">
      <div v-for="step in totalSteps" :key="step" 
           class="col-md-6 col-lg-3">
        <WorkflowCard
          :step="step"
          :status="computedStepStates[`step${step}`].status"
          :title="getStepTitle(step)"
          :description="getStepDescription(step)"
          :icon="getStepIcon(step)"
          :step-data="stepStates[`step${step}`].data"
        >
          <template #default="{ status }">
            <!-- Conteúdo específico de cada etapa -->
            <div v-if="status === 'available'">
              <!-- Estado inicial -->
            </div>
            
            <div v-else-if="status === 'processing'">
              <ResultDisplay
                type="processing"
                title="[Título do Processamento]"
                description="[Descrição do que está sendo feito]"
              />
            </div>
            
            <div v-else-if="status === 'completed'">
              <ResultDisplay
                type="success"
                title="[Título de Sucesso]"
                :description="[Descrição com dados]"
                :actions="[Ações disponíveis]"
                :data="stepStates[`step${step}`].data"
                @action="handleAction"
              />
            </div>
          </template>
        </WorkflowCard>
      </div>
    </div>

    <!-- 4. Sistema de Progresso Detalhado (opcional) -->
    <div v-if="currentProcessingStep" class="processing-details mb-4">
      <!-- Detalhes do processamento atual -->
    </div>

    <!-- 5. Mensagens de Status -->
    <div v-if="mensagem" 
         :class="['alert', 'alert-custom', mensagem.tipo === 'erro' ? 'alert-danger' : 'alert-success']"
         class="mb-4">
      <!-- Mensagens de sucesso/erro -->
    </div>
  </div>
</template>
```

### **2. Script Setup**
```javascript
import WorkflowCard from './base/WorkflowCard.vue'
import ResultDisplay from './base/ResultDisplay.vue'
import { useWorkflowState } from '../composables/useWorkflowState.js'

export default {
  name: '[NomeDoComponente]',
  
  components: {
    WorkflowCard,
    ResultDisplay
  },
  
  setup() {
    const {
      stepStates,
      currentProcessingStep,
      computedStepStates,
      overallProgress,
      setStepCompleted,
      setStepProcessing,
      setStepAvailable,
      resetWorkflow
    } = useWorkflowState()
    
    return {
      stepStates,
      currentProcessingStep,
      computedStepStates,
      overallProgress,
      setStepCompleted,
      setStepProcessing,
      setStepAvailable,
      resetWorkflow
    }
  },
  
  data() {
    return {
      // Dados específicos da funcionalidade
      mensagem: null,
      // ... outros dados
    }
  },

  methods: {
    // Métodos específicos da funcionalidade
    async executarEtapa(stepKey) {
      this.setStepProcessing(stepKey)
      
      try {
        // Lógica da etapa
        const resultado = await this.processarEtapa()
        
        this.setStepCompleted(stepKey, resultado)
        
        this.mensagem = {
          tipo: 'sucesso',
          texto: 'Etapa executada com sucesso!'
        }
        
      } catch (error) {
        this.setStepAvailable(stepKey)
        
        this.mensagem = {
          tipo: 'erro',
          texto: 'Erro na execução: ' + error.message
        }
      }
    },
    
    // Manipulador de ações dos ResultDisplay
    handleAction(actionKey, data) {
      switch (actionKey) {
        case 'view':
          this.visualizarDetalhes(data)
          break
        case 'download':
          this.downloadArquivo(data)
          break
        case 'reset':
          this.resetWorkflow()
          break
        default:
          console.log('Ação não implementada:', actionKey)
      }
    },
    
    // Métodos auxiliares
    getStepLabel(step) {
      const labels = {
        1: 'Etapa 1',
        2: 'Etapa 2',
        3: 'Etapa 3',
        4: 'Etapa 4'
      }
      return labels[step] || `Etapa ${step}`
    },
    
    getStepTitle(step) {
      const titles = {
        1: 'Título da Etapa 1',
        2: 'Título da Etapa 2',
        3: 'Título da Etapa 3',
        4: 'Título da Etapa 4'
      }
      return titles[step] || `Etapa ${step}`
    },
    
    getStepDescription(step) {
      const descriptions = {
        1: 'Descrição da Etapa 1',
        2: 'Descrição da Etapa 2',
        3: 'Descrição da Etapa 3',
        4: 'Descrição da Etapa 4'
      }
      return descriptions[step] || `Descrição da etapa ${step}`
    },
    
    getStepIcon(step) {
      const icons = {
        1: 'fa-search',
        2: 'fa-check-circle',
        3: 'fa-database',
        4: 'fa-chart-bar'
      }
      return icons[step] || 'fa-circle'
    }
  }
}
```

## 📚 **EXEMPLOS PRÁTICOS (4 ABAS IMPLEMENTADAS)**

### **1. Aba 1: Serviços Gerais**
- **Objetivo:** Processamento de PDF para serviços
- **Etapas:** Upload → Processamento → Validação → Resultado
- **Características:** Processamento de arquivo, geração de Excel
- **Adaptações:** Botão "Exportar Excel" discreto, sem botão "Finalizar"

### **2. Aba 2: Insumos**
- **Objetivo:** Processamento de PDF para insumos
- **Etapas:** Upload → Processamento → Validação → Resultado
- **Características:** Idêntico à Aba 1, mas com dados de insumos
- **Adaptações:** Mesma estrutura, dados específicos

### **3. Aba 3: Fórmulas de Transporte**
- **Objetivo:** Processamento de PDF para fórmulas de transporte
- **Etapas:** Upload → Processamento → Validação → Resultado
- **Características:** Idêntico às Abas 1 e 2
- **Adaptações:** Endpoint específico (`formulas-transporte`)

### **4. Aba 4: Gravação no Banco**
- **Objetivo:** Importação em lote no banco de dados
- **Etapas:** Verificação → Validação → Gravação → Resultado
- **Características:** Depende das abas anteriores, não processa arquivos
- **Adaptações:** 
  - Não usa `useWorkflowState` diretamente
  - Foco na tabela `derpr_composicoes`
  - Estatísticas de registros criados/atualizados

## ✅ **CHECKLIST DE IMPLEMENTAÇÃO**

### **1. Preparação**
- [ ] Definir número de etapas do workflow
- [ ] Identificar estados de cada etapa
- [ ] Definir dados necessários para cada etapa
- [ ] Planejar transições entre estados

### **2. Componentes Base**
- [ ] Importar `useWorkflowState` no setup
- [ ] Importar `WorkflowCard` e `ResultDisplay`
- [ ] Configurar props dos componentes

### **3. Template**
- [ ] Implementar cabeçalho da funcionalidade
- [ ] Criar barra de progresso horizontal
- [ ] Implementar sistema de cards com `WorkflowCard`
- [ ] Adicionar sistema de progresso detalhado (se necessário)
- [ ] Implementar mensagens de status

### **4. Lógica**
- [ ] Implementar métodos de execução de cada etapa
- [ ] Configurar `setStepCompleted`, `setStepProcessing`, etc.
- [ ] Implementar `handleAction` para ações dos ResultDisplay
- [ ] Adicionar métodos auxiliares (`getStepLabel`, `getStepTitle`, etc.)

### **5. Estados e Progresso**
- [ ] Configurar estados iniciais das etapas
- [ ] Implementar lógica de progresso geral
- [ ] Configurar transições entre estados
- [ ] Testar progresso de 0% a 100%

### **6. Estilização**
- [ ] Aplicar classes CSS padrão
- [ ] Configurar responsividade
- [ ] Testar em diferentes tamanhos de tela

### **7. Testes**
- [ ] Testar fluxo completo do workflow
- [ ] Verificar progresso visual
- [ ] Testar estados de erro
- [ ] Validar responsividade

## 🔧 **ADAPTAÇÕES COMUNS**

### **1. Workflow com Mais Etapas**
```javascript
// Ajustar useWorkflowState para N etapas
const stepStates = ref({
  step1: { status: 'available', data: null },
  step2: { status: 'available', data: null },
  // ... adicionar mais etapas
  stepN: { status: 'available', data: null }
})
```

### **2. Workflow com Menos Etapas**
```javascript
// Reduzir para 2-3 etapas
const stepStates = ref({
  step1: { status: 'available', data: null },
  step2: { status: 'available', data: null },
  step3: { status: 'available', data: null }
})
```

### **3. Workflow com Dependências**
```javascript
// Verificar se etapa anterior foi concluída
async executarEtapa(stepKey) {
  if (stepKey > 1 && !this.stepStates[`step${stepKey - 1}`].completed) {
    this.mensagem = {
      tipo: 'erro',
      texto: 'Complete a etapa anterior primeiro'
    }
    return
  }
  
  // ... execução da etapa
}
```

### **4. Workflow com Validações Específicas**
```javascript
// Adicionar validações customizadas
async validarEtapa(stepKey, dados) {
  const validacoes = {
    step1: () => this.validarUpload(dados),
    step2: () => this.validarProcessamento(dados),
    step3: () => this.validarResultado(dados)
  }
  
  if (validacoes[stepKey]) {
    return await validacoes[stepKey]()
  }
  
  return true
}
```

## 🎨 **ESTILOS PADRÃO**

### **1. Classes CSS Obrigatórias**
```css
/* Barra de Progresso */
.progress-tracker { /* ... */ }
.progress-line { /* ... */ }
.progress-fill { /* ... */ }
.progress-steps { /* ... */ }
.progress-step { /* ... */ }
.step-circle { /* ... */ }
.step-label { /* ... */ }

/* Cards */
.workflow-card { /* ... */ }
.card-header-section { /* ... */ }
.step-indicator { /* ... */ }
.step-number { /* ... */ }
.card-title { /* ... */ }
.status-badge { /* ... */ }
.card-content { /* ... */ }

/* Estados */
.card-completed { /* ... */ }
.card-current { /* ... */ }
.card-processing { /* ... */ }
```

### **2. Cores Padrão**
```css
:root {
  --primary-color: #18578A;
  --success-color: #5EA853;
  --warning-color: #FFC107;
  --danger-color: #DC3545;
  --text-muted: #6c757d;
  --border-color: #e9ecef;
  --background-light: #f8f9fa;
}
```

## 📝 **NOTAS IMPORTANTES**

### **1. Consistência Visual**
- **Sempre usar** os mesmos componentes base
- **Manter** a mesma estrutura de layout
- **Aplicar** as mesmas classes CSS
- **Seguir** o mesmo padrão de ícones

### **2. Gerenciamento de Estado**
- **Centralizar** toda lógica no `useWorkflowState`
- **Não criar** estados locais duplicados
- **Sempre usar** os métodos `setStep*` para mudanças
- **Manter** consistência entre frontend e backend

### **3. Responsividade**
- **Testar** em diferentes tamanhos de tela
- **Usar** classes Bootstrap responsivas
- **Adaptar** layout para mobile quando necessário

### **4. Performance**
- **Evitar** re-renders desnecessários
- **Usar** `computed` para cálculos complexos
- **Lazy load** de componentes pesados se necessário

## 🚀 **CONCLUSÃO**

Este padrão de workflow de progresso fornece uma base sólida e consistente para implementação de funcionalidades que requerem múltiplas etapas de processamento. 

**Principais benefícios:**
- ✅ **Consistência visual** em todo o sistema
- ✅ **Reutilização** de componentes testados
- ✅ **Manutenibilidade** com lógica centralizada
- ✅ **Flexibilidade** para adaptações específicas
- ✅ **Experiência do usuário** padronizada

**Para implementar um novo workflow:**
1. **Ler** este documento
2. **Adaptar** a estrutura para o caso específico
3. **Reutilizar** componentes base
4. **Personalizar** conforme necessário
5. **Testar** fluxo completo

---

**Última atualização:** 21/08/2025  
**Versão:** 1.0  
**Autor:** Sistema OrcaCidade  
**Status:** ✅ Implementado e Testado
