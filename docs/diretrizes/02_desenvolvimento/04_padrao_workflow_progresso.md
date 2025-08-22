# Padrão de Workflow com Progresso - Guia Completo

## 📋 Visão Geral

Este documento define o padrão para implementação de workflows com indicadores de progresso visual no sistema OrçaCidade, baseado na implementação bem-sucedida do módulo DER-PR e refatoração do módulo SINAPI.

## 🎯 Objetivos

- **Padronização visual** entre todos os módulos de workflow
- **Experiência do usuário consistente** e intuitiva
- **Indicadores de progresso claros** e informativos
- **Componentes reutilizáveis** e manuteníveis
- **Tratamento de erros robusto** e informativo

## 🏗️ Arquitetura do Workflow

### Estrutura de 4 Etapas Padrão

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Etapa 1   │───▶│   Etapa 2   │───▶│   Etapa 3   │───▶│   Etapa 4   │
│ Verificação │    │ Validação   │    │ Processo    │    │ Resultado   │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

### Estados de Cada Etapa

1. **`available`** - Etapa disponível para execução
2. **`processing`** - Etapa em execução
3. **`completed`** - Etapa concluída com sucesso
4. **`error`** - Etapa com erro (opcional)

## 🧩 Componentes Base

### 1. WorkflowCard.vue

Componente base para cada etapa do workflow.

```vue
<WorkflowCard
  :step="1"
  :status="computedStepStates.step1.status"
  title="Verificação de Arquivos"
  description="Detecta arquivos disponíveis para processamento"
  icon="fa-search"
  :step-data="stepStates.step1.data"
>
  <template #default="{ status }">
    <!-- Conteúdo específico da etapa -->
  </template>
</WorkflowCard>
```

**Propriedades:**
- `step`: Número da etapa (1-4)
- `status`: Estado atual da etapa
- `title`: Título da etapa
- `description`: Descrição da funcionalidade
- `icon`: Ícone FontAwesome
- `step-data`: Dados específicos da etapa

### 2. ResultDisplay.vue

Componente para exibição de resultados padronizados.

```vue
<ResultDisplay
  type="success"
  title="Operação Concluída!"
  description="Processamento realizado com sucesso"
/>
```

**Tipos disponíveis:**
- `success` - Operação bem-sucedida
- `processing` - Operação em andamento
- `error` - Erro na operação

### 3. useWorkflowState.js

Composable para gerenciamento centralizado do estado do workflow.

```javascript
import { useWorkflowState } from '@/composables/useWorkflowState'

export default {
  setup() {
    const {
      stepStates,
      computedStepStates,
      setStepProcessing,
      setStepCompleted,
      setStepAvailable,
      overallProgress
    } = useWorkflowState()

    return {
      stepStates,
      computedStepStates,
      setStepProcessing,
      setStepCompleted,
      setStepAvailable,
      overallProgress
    }
  }
}
```

## 🎨 Implementação Visual

### Progress Tracker

```vue
<div class="progress-tracker">
  <div class="progress-steps">
    <div 
      v-for="step in 4" 
      :key="step"
      class="progress-step"
      :class="getStepClass(step)"
    >
      <div class="step-icon">
        <i :class="getStepIcon(step)"></i>
      </div>
      <div class="step-label">{{ getStepLabel(step) }}</div>
    </div>
  </div>
  <div class="progress-line"></div>
</div>
```

### CSS Padrão

```scss
.progress-tracker {
  margin-bottom: 2rem;
  
  .progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 2;
  }
  
  .progress-line {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: #e9ecef;
    z-index: 1;
    transform: translateY(-50%);
  }
  
  .progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    
    &.completed .step-icon {
      background: #28a745;
      color: white;
    }
    
    &.processing .step-icon {
      background: #007bff;
      color: white;
      animation: pulse 2s infinite;
    }
    
    &.available .step-icon {
      background: #e9ecef;
      color: #6c757d;
    }
  }
}
```

## 🔄 Fluxo de Dados

### 1. Inicialização

```javascript
data() {
  return {
    stepStates: {
      step1: { status: 'available', data: null },
      step2: { status: 'available', data: null },
      step3: { status: 'available', data: null },
      step4: { status: 'available', data: null }
    }
  }
}
```

### 2. Execução de Etapa

```javascript
async executarEtapa1() {
  this.setStepProcessing('step1')
  
  try {
    const response = await axios.get('/api/endpoint')
    
    if (response.data.success) {
      this.setStepCompleted('step1', response.data)
    } else {
      throw new Error(response.data.message)
    }
  } catch (error) {
    this.setStepAvailable('step1')
    this.mensagem = { tipo: 'erro', texto: error.message }
  }
}
```

### 3. Transição Entre Etapas

```javascript
// Etapa 1 deve estar completa para executar etapa 2
async executarEtapa2() {
  if (!this.stepStates.step1.data) {
    this.mensagem = {
      tipo: 'erro',
      texto: 'Execute a etapa 1 primeiro.'
    }
    return
  }
  
  this.setStepProcessing('step2')
  // ... lógica da etapa 2
}
```

## 📊 Exibição de Resultados

### Template para Etapa Concluída

```vue
<div v-else-if="status === 'completed'">
  <div class="result-success text-center">
    <h6 class="mt-0 mb-1">{{ getSuccessTitle() }}</h6>
    <p class="text-muted mb-3 small">
      {{ getSuccessMessage() }}
    </p>
    
    <!-- Dados específicos da etapa -->
    <div v-if="stepData?.detalhes" class="result-details">
      <!-- Conteúdo específico -->
    </div>
  </div>
</div>
```

### Mensagens de Sucesso Padrão

```javascript
getSuccessTitle() {
  const titles = {
    step1: 'Arquivos Detectados!',
    step2: 'Validação Concluída!',
    step3: 'Processamento Concluído!',
    step4: 'Workflow Finalizado!'
  }
  return titles[`step${this.step}`] || 'Operação Concluída!'
}
```

## 🚨 Tratamento de Erros

### Estrutura de Mensagens

```javascript
this.mensagem = {
  tipo: 'erro', // 'sucesso', 'erro', 'aviso'
  texto: 'Descrição detalhada do erro'
}
```

### Exibição de Erros

```vue
<div v-if="mensagem" 
     :class="`alert alert-${mensagem.tipo === 'sucesso' ? 'success' : 'danger'}`">
  <i :class="`fas fa-${mensagem.tipo === 'sucesso' ? 'check-circle' : 'exclamation-triangle'}`"></i>
  {{ mensagem.texto }}
</div>
```

## 🔧 Padrões de API

### Estrutura de Resposta Padrão

```json
{
  "success": true,
  "message": "Operação realizada com sucesso",
  "data": {
    "total_items": 150,
    "processed_items": 150,
    "errors": []
  },
  "results": {
    "total_registros": 150,
    "total_criados": 120,
    "total_atualizados": 30
  }
}
```

### Tratamento de Resposta

```javascript
if (response.data.success) {
  // Processar dados de sucesso
  const dados = response.data
  
  // Extrair contadores padrão
  const totalRegistros = dados.results?.total_registros || 0
  const registrosCriados = dados.results?.total_criados || 0
  const registrosAtualizados = dados.results?.total_atualizados || 0
  
  // Atualizar estado
  this.setStepCompleted('step3', {
    ...dados,
    resumo_gravacao: {
      total_registros: totalRegistros,
      registros_criados: registrosCriados,
      registros_atualizados: registrosAtualizados
    }
  })
}
```

## 📱 Responsividade

### Breakpoints Padrão

```scss
@media (max-width: 768px) {
  .progress-steps {
    flex-direction: column;
    gap: 15px;
  }
  
  .progress-line {
    display: none;
  }
  
  .workflow-card {
    margin-bottom: 15px;
  }
}
```

### Layout Mobile

```vue
<div class="row">
  <div class="col-md-6 col-lg-3">
    <!-- Card responsivo -->
  </div>
</div>
```

## 🎯 Boas Práticas

### 1. Nomenclatura Consistente

- **Etapas**: Sempre numeradas de 1 a 4
- **Estados**: `available`, `processing`, `completed`, `error`
- **Métodos**: `executarEtapa1()`, `executarEtapa2()`, etc.
- **Dados**: `stepStates.step1.data`, `stepStates.step2.data`, etc.

### 2. Validação de Dependências

```javascript
// Sempre validar se etapa anterior foi concluída
if (!this.stepStates.step1.data) {
  this.mensagem = {
    tipo: 'erro',
    texto: 'Execute a etapa anterior primeiro.'
  }
  return
}
```

### 3. Tratamento de Estados Vazios

```vue
<!-- Mostrar mensagem quando não há dados -->
<div v-if="!stepData?.dados" class="text-muted small">
  <i class="fas fa-info-circle me-2"></i>
  Nenhum dado disponível. Execute a etapa anterior primeiro.
</div>
```

### 4. Feedback Visual Consistente

- **Ícones**: Sempre usar FontAwesome
- **Cores**: Verde para sucesso, azul para processamento, vermelho para erro
- **Animações**: Transições suaves e indicadores de loading

## 🔄 Migração de Módulos Existentes

### Passos para Refatoração

1. **Analisar estrutura atual** do módulo
2. **Identificar etapas** do workflow
3. **Implementar `useWorkflowState`**
4. **Substituir componentes** por `WorkflowCard` e `ResultDisplay`
5. **Aplicar CSS padrão** do progress tracker
6. **Testar fluxo completo** de todas as etapas

### Exemplo de Migração

```javascript
// ANTES (estrutura antiga)
data() {
  return {
    etapa1: false,
    etapa2: false,
    etapa3: false
  }
}

// DEPOIS (estrutura padrão)
import { useWorkflowState } from '@/composables/useWorkflowState'

export default {
  setup() {
    const {
      stepStates,
      computedStepStates,
      setStepProcessing,
      setStepCompleted
    } = useWorkflowState()

    return {
      stepStates,
      computedStepStates,
      setStepProcessing,
      setStepCompleted
    }
  }
}
```

## 📋 Checklist de Implementação

### ✅ Estrutura Base
- [ ] 4 etapas definidas e numeradas
- [ ] `useWorkflowState` implementado
- [ ] Estados `available`, `processing`, `completed` configurados

### ✅ Componentes
- [ ] `WorkflowCard` para cada etapa
- [ ] `ResultDisplay` para estados de processamento
- [ ] Progress tracker visual implementado

### ✅ Funcionalidades
- [ ] Validação de dependências entre etapas
- [ ] Tratamento de erros robusto
- [ ] Mensagens de feedback claras
- [ ] Estados vazios tratados adequadamente

### ✅ Visual
- [ ] CSS padrão aplicado
- [ ] Responsividade implementada
- [ ] Ícones e cores consistentes
- [ ] Animações e transições suaves

### ✅ API
- [ ] Estrutura de resposta padronizada
- [ ] Tratamento de dados consistente
- [ ] Contadores e métricas corretos

## 🎉 Conclusão

Este padrão garante que todos os módulos de workflow do sistema OrçaCidade tenham:

- **Consistência visual** e funcional
- **Experiência do usuário** intuitiva e agradável
- **Manutenibilidade** e reutilização de código
- **Robustez** no tratamento de erros e estados
- **Escalabilidade** para futuras funcionalidades

Seguindo este padrão, novos módulos podem ser implementados rapidamente e módulos existentes podem ser modernizados de forma consistente.
