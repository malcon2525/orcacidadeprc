import { ref, computed } from 'vue'

/**
 * Composable para gerenciar o estado do workflow
 * Gerencia dependências entre etapas e estado visual dos cards
 */
export function useWorkflowState() {
  // Estado das etapas
  const stepStates = ref({
    step1: { status: 'available', data: null, completed: false },
    step2: { status: 'blocked', data: null, completed: false },
    step3: { status: 'blocked', data: null, completed: false },
    step4: { status: 'blocked', data: null, completed: false }
  })

  // Etapa atual sendo processada
  const currentProcessingStep = ref(null)

  // Computed para calcular o status de cada etapa baseado nas dependências
  const computedStepStates = computed(() => {
    const states = {}
    
    // Etapa 1: Sempre disponível
    states.step1 = {
      ...stepStates.value.step1,
      status: stepStates.value.step1.completed ? 'completed' : 'available'
    }
    
    // Etapa 2: Disponível se etapa 1 estiver concluída
    states.step2 = {
      ...stepStates.value.step2,
      status: stepStates.value.step1.completed 
        ? (stepStates.value.step2.completed ? 'completed' : 'available')
        : 'blocked'
    }
    
    // Etapa 3: Disponível se etapas 1 e 2 estiverem concluídas
    states.step3 = {
      ...stepStates.value.step3,
      status: (stepStates.value.step1.completed && stepStates.value.step2.completed)
        ? (stepStates.value.step3.completed ? 'completed' : 'available')
        : 'blocked'
    }
    
    // Etapa 4: Disponível se etapas 1, 2 e 3 estiverem concluídas
    states.step4 = {
      ...stepStates.value.step4,
      status: (stepStates.value.step1.completed && stepStates.value.step2.completed && stepStates.value.step3.completed)
        ? (stepStates.value.step4.completed ? 'completed' : 'available')
        : 'blocked'
    }
    
    // Aplicar estado de processamento se houver (apenas se não estiver completed)
    if (currentProcessingStep.value && !states[currentProcessingStep.value].completed) {
      states[currentProcessingStep.value].status = 'processing'
    }
    
    return states
  })

  // Progresso geral (0-100%)
  const overallProgress = computed(() => {
    const totalSteps = 4
    const completedSteps = Object.values(stepStates.value).filter(step => step.completed).length
    return Math.round((completedSteps / totalSteps) * 100)
  })

  // Métodos para gerenciar o estado
  const setStepStatus = (step, status, data = null) => {
    if (stepStates.value[step]) {
      stepStates.value[step] = {
        ...stepStates.value[step],
        status,
        data,
        completed: status === 'completed'
      }
    }
  }

  const setStepCompleted = (step, data = null) => {
    // Limpar currentProcessingStep se esta etapa estava sendo processada
    if (currentProcessingStep.value === step) {
      currentProcessingStep.value = null
    }
    setStepStatus(step, 'completed', data)
  }

  const setStepProcessing = (step) => {
    currentProcessingStep.value = step
    setStepStatus(step, 'processing')
  }

  const setStepAvailable = (step) => {
    if (currentProcessingStep.value === step) {
      currentProcessingStep.value = null
    }
    setStepStatus(step, 'available')
  }

  const setStepBlocked = (step) => {
    if (currentProcessingStep.value === step) {
      currentProcessingStep.value = null
    }
    setStepStatus(step, 'blocked')
  }

  // Resetar workflow
  const resetWorkflow = () => {
    stepStates.value = {
      step1: { status: 'available', data: null, completed: false },
      step2: { status: 'blocked', data: null, completed: false },
      step3: { status: 'blocked', data: null, completed: false },
      step4: { status: 'blocked', data: null, completed: false }
    }
    currentProcessingStep.value = null
  }

  // Verificar se uma etapa pode ser executada
  const canExecuteStep = (step) => {
    const states = computedStepStates.value
    return states[step]?.status === 'available'
  }

  // Verificar se workflow está completo
  const isWorkflowComplete = computed(() => {
    return Object.values(stepStates.value).every(step => step.completed)
  })

  // Obter próxima etapa disponível
  const getNextAvailableStep = computed(() => {
    const states = computedStepStates.value
    for (let i = 1; i <= 4; i++) {
      const stepKey = `step${i}`
      if (states[stepKey]?.status === 'available') {
        return stepKey
      }
    }
    return null
  })

  // Obter etapas pendentes
  const getPendingSteps = computed(() => {
    const states = computedStepStates.value
    return Object.entries(states)
      .filter(([_, step]) => step.status === 'blocked')
      .map(([stepKey, _]) => stepKey)
  })

  // Obter etapas concluídas
  const getCompletedSteps = computed(() => {
    const states = computedStepStates.value
    return Object.entries(states)
      .filter(([_, step]) => step.completed)
      .map(([stepKey, _]) => stepKey)
  })

  return {
    // Estado
    stepStates,
    currentProcessingStep,
    
    // Computed
    computedStepStates,
    overallProgress,
    isWorkflowComplete,
    getNextAvailableStep,
    getPendingSteps,
    getCompletedSteps,
    
    // Métodos
    setStepStatus,
    setStepCompleted,
    setStepProcessing,
    setStepAvailable,
    setStepBlocked,
    resetWorkflow,
    canExecuteStep
  }
}
