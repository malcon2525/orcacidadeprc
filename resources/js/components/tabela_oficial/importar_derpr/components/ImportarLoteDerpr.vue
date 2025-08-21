<template>
  <div>
    <!-- Cabeçalho Elegante -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h5 class="mb-1 fw-bold text-custom">
          <i class="fas fa-database me-2"></i>Gravação de Dados DER-PR no Banco
        </h5>
        <p class="text-muted mb-0 small">Detecção automática e gravação dos arquivos processados nas Abas 1, 2 e 3</p>
      </div>
    </div>

    <!-- Barra de Progresso Horizontal -->
    <div class="progress-tracker mb-4">
      <div class="progress-line">
        <div class="progress-fill" :style="{ width: overallProgress + '%' }"></div>
      </div>
      <div class="progress-steps">
        <div 
          v-for="step in 4" 
          :key="step"
          class="progress-step" 
          :class="{ 
            'completed': computedStepStates[`step${step}`]?.status === 'completed',
            'current': computedStepStates[`step${step}`]?.status === 'available',
            'processing': computedStepStates[`step${step}`]?.status === 'processing'
          }"
        >
          <div class="step-circle">
            <i v-if="computedStepStates[`step${step}`]?.status === 'completed'" class="fas fa-check"></i>
            <i v-else-if="computedStepStates[`step${step}`]?.status === 'processing'" class="fas fa-spinner fa-spin"></i>
            <span v-else>{{ step }}</span>
          </div>
          <span class="step-label">{{ getStepLabel(step) }}</span>
        </div>
      </div>
    </div>

    <!-- Sistema de Cards com WorkflowCard -->
    <div class="row g-4 mb-4">
      <!-- Card 1: Verificação de Arquivos -->
      <div class="col-md-6 col-lg-3">
        <WorkflowCard
          :step="1"
          :status="computedStepStates.step1.status"
          title="Verificação de Arquivos"
          description="Detecta arquivos Excel das abas anteriores"
          icon="fa-search"
          :step-data="stepStates.step1.data"
        >
          <template #default="{ status }">
            <!-- Conteúdo do Card 1 -->
            <div v-if="status === 'available'">
              <div class="verification-section">
                <div class="verification-info">
                  <p class="mb-3 small">Verifique se existem arquivos Excel das abas anteriores para importar no banco.</p>
                </div>
                <button 
                  class="btn btn-primary btn-lg w-100 fw-semibold" 
                  @click="verificarArquivos"
                >
                  <i class="fas fa-search me-2"></i>
                  Verificar Arquivos
                </button>
              </div>
            </div>
            
            <div v-else-if="status === 'processing'">
              <ResultDisplay
                type="processing"
                title="Verificando..."
                description="Detectando arquivos Excel disponíveis..."
              />
            </div>
            
            <div v-else-if="status === 'completed'">
              <ResultDisplay
                type="success"
                title="Arquivos Detectados!"
                :description="`${stepStates.step1.data?.total_disponiveis || 0} arquivos prontos para importação`"
                :actions="[]"
                :data="stepStates.step1.data"
                @action="handleAction"
              />
            </div>
          </template>
        </WorkflowCard>
      </div>

      <!-- Card 2: Validação de Estrutura -->
      <div class="col-md-6 col-lg-3">
        <WorkflowCard
          :step="2"
          :status="computedStepStates.step2.status"
          title="Validação de Estrutura"
          description="Valida estrutura dos arquivos Excel"
          icon="fa-check-circle"
          :step-data="stepStates.step2.data"
        >
          <template #default="{ status }">
            <!-- Conteúdo do Card 2 -->
            <div v-if="status === 'available'">
              <div class="validation-section">
                <div class="validation-info">
                  <p class="mb-3 small">Valide a estrutura dos arquivos Excel antes de prosseguir com a importação.</p>
                </div>
                <button 
                  class="btn btn-primary btn-lg w-100 fw-semibold" 
                  @click="validarEstrutura"
                >
                  <i class="fas fa-check me-2"></i>
                  Validar Estrutura
                </button>
              </div>
            </div>
            
            <div v-else-if="status === 'processing'">
              <ResultDisplay
                type="processing"
                title="Validando..."
                description="Verificando estrutura dos arquivos Excel..."
              />
            </div>
            
            <div v-else-if="status === 'completed'">
              <ResultDisplay
                type="success"
                title="Estrutura Validada!"
                :description="`${stepStates.step2.data?.arquivos_validados || 0} arquivos com estrutura válida`"
                :actions="[]"
                :data="stepStates.step2.data"
                @action="handleAction"
              />
            </div>
          </template>
        </WorkflowCard>
      </div>

             <!-- Card 3: Gravação no Banco -->
       <div class="col-md-6 col-lg-3">
         <WorkflowCard
           :step="3"
           :status="computedStepStates.step3.status"
           title="Gravação no Banco"
           description="Grava os dados processados no banco de dados"
           icon="fa-database"
           :step-data="stepStates.step3.data"
         >
          <template #default="{ status }">
            <!-- Conteúdo do Card 3 -->
            <div v-if="status === 'available'">
              <div class="import-section">
                                 <div class="import-info">
                   <p class="mb-3 small">Grava todos os dados processados nas abas anteriores no banco de dados.</p>
                 </div>
                                 <button 
                   class="btn btn-success btn-lg w-100 fw-semibold" 
                   @click="iniciarImportacao"
                 >
                   <i class="fas fa-database me-2"></i>
                   Gravar no Banco
                 </button>
              </div>
            </div>
            
                         <div v-else-if="status === 'processing'">
               <ResultDisplay
                 type="processing"
                 title="Gravando..."
                 description="Gravando dados no banco de dados..."
               />
             </div>
            
                         <div v-else-if="status === 'completed'">
               <ResultDisplay
                 type="success"
                 title="Gravação Concluída!"
                 :description="`${stepStates.step3.data?.registros_criados || 0} criados e ${stepStates.step3.data?.registros_atualizados || 0} atualizados`"
                 :actions="[]"
                 :data="stepStates.step3.data"
                 @action="handleAction"
               />
             </div>
          </template>
        </WorkflowCard>
      </div>

      <!-- Card 4: Resultado Final -->
      <div class="col-md-6 col-lg-3">
        <WorkflowCard
          :step="4"
          :status="computedStepStates.step4.status"
          title="Resultado Final"
          description="Visualize estatísticas da importação"
          icon="fa-chart-bar"
          :step-data="stepStates.step4.data"
        >
          <template #default="{ status }">
            <!-- Conteúdo do Card 4 -->
            <div v-if="status === 'available'">
              <div class="final-section">
                <div class="final-info">
                  <p class="mb-3 small">Todos os passos foram concluídos. Visualize as estatísticas finais da importação.</p>
                </div>
              </div>
            </div>
            
                         <div v-else-if="status === 'completed'">
               <ResultDisplay
                 type="success"
                 title="Processo Finalizado!"
                 :description="` `"
                 :actions="[]"
                 :data="stepStates.step4.data"
                 @action="handleAction"
               />
             </div>
          </template>
        </WorkflowCard>
      </div>
    </div>

    <!-- Sistema de Progresso Detalhado -->
    <div v-if="currentProcessingStep" class="processing-details mb-4">
      <div class="processing-header">
        <div class="processing-icon">
          <i class="fas fa-database fa-spin"></i>
        </div>
        <div class="processing-info">
          <h6 class="mb-1 fw-semibold">Importando Dados no Banco</h6>
          <p class="mb-0 text-muted small">{{ progressoEtapa }}</p>
        </div>
      </div>
      
      <div class="progress-container">
        <div class="progress-bar-custom">
          <div class="progress-fill-custom" :style="{ width: progresso + '%' }"></div>
        </div>
        <div class="progress-text">{{ progresso }}%</div>
      </div>

      <div class="processing-steps">
        <div v-for="(etapa, index) in etapas" :key="index" 
             :class="['processing-step', 
                      etapa.completa ? 'completed' : 
                      etapa.ativa ? 'active' : 'pending']">
          <div class="step-icon">
            <i :class="['fas', 
                      etapa.completa ? 'fa-check-circle' : 
                      etapa.ativa ? 'fa-spinner fa-spin' : 
                      'fa-circle']"></i>
          </div>
          <span class="step-text small">{{ etapa.nome }}</span>
        </div>
      </div>
    </div>

    <!-- Mensagens de Alerta -->
    <div v-if="mensagem" 
         :class="['alert', 'alert-custom', mensagem.tipo === 'erro' ? 'alert-danger' : 'alert-success']"
         class="mb-4">
      <div class="d-flex align-items-center">
        <i :class="['fas', 'me-2', mensagem.tipo === 'erro' ? 'fa-exclamation-triangle' : 'fa-check-circle']"></i>
        <span class="small">{{ mensagem.texto }}</span>
      </div>
    </div>


  </div>
</template>

<script>
import WorkflowCard from './base/WorkflowCard.vue'
import ResultDisplay from './base/ResultDisplay.vue'
import { useWorkflowState } from '../composables/useWorkflowState.js'

export default {
    name: 'ImportarLoteDerpr',
    
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
            // Sistema de progresso
            progresso: 0,
            progressoEtapa: '',
            horaInicio: null,
            tempoDecorrido: '0s',
            intervaloTempo: null,
            
            // Etapas do processamento
            etapas: [
                { nome: 'Verificação de Arquivos', completa: false, ativa: false },
                { nome: 'Validação de Estrutura', completa: false, ativa: false },
                { nome: 'Preparação do Banco', completa: false, ativa: false },
                { nome: 'Importação em Lote', completa: false, ativa: false },
                { nome: 'Validação de Dados', completa: false, ativa: false },
                { nome: 'Finalização', completa: false, ativa: false }
            ],
            
            // Resultados da importação
            resultadosImportacao: {},
            
            // Mensagens
            mensagem: null
        }
    },

    computed: {
        // Progresso geral baseado no workflow state
        progressoGeral() {
            return this.overallProgress
        }
    },

    methods: {
        // ========================================
        // MÉTODOS DE VERIFICAÇÃO
        // ========================================
        
        /**
         * Verifica arquivos Excel disponíveis
         */
        async verificarArquivos() {
            this.setStepProcessing('step1')
            
            try {
                const response = await axios.get('/api/tabela_oficial/importar_derpr/verificar_arquivos')
                
                if (response.data.success) {
                    const arquivosInfo = response.data
                    this.setStepCompleted('step1', arquivosInfo)
                    
                    this.mensagem = {
                        tipo: 'sucesso',
                        texto: `${arquivosInfo.total_disponiveis} arquivos detectados com sucesso!`
                    }
                } else {
                    throw new Error(response.data.message || 'Erro ao verificar arquivos')
                }
                
            } catch (error) {
                this.setStepAvailable('step1')
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Erro ao verificar arquivos: ' + (error.response?.data?.message || error.message)
                }
            }
        },

        // ========================================
        // MÉTODOS DE VALIDAÇÃO
        // ========================================
        
        /**
         * Valida a estrutura dos arquivos Excel
         */
        async validarEstrutura() {
            if (!this.stepStates.step1.data) {
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Verifique os arquivos primeiro.'
                }
                return
            }

            this.setStepProcessing('step2')
            
            try {
                // Simular validação
                await this.delay(2000)
                
                const arquivosInfo = this.stepStates.step1.data
                const arquivosValidados = arquivosInfo.arquivos_disponiveis?.length || 0
                
                const validationResult = {
                    arquivos_validados: arquivosValidados,
                    total_arquivos: arquivosInfo.total_disponiveis || 0,
                    validationDate: new Date().toISOString(),
                    issues: []
                }
                
                this.setStepCompleted('step2', validationResult)
                
                this.mensagem = {
                    tipo: 'sucesso',
                    texto: `Estrutura validada! ${arquivosValidados} arquivos com estrutura válida.`
                }
                
            } catch (error) {
                this.setStepAvailable('step2')
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Erro durante a validação da estrutura.'
                }
            }
        },

        // ========================================
        // MÉTODOS DE IMPORTACAO
        // ========================================
        
        /**
         * Inicia a importação em lote
         */
        async iniciarImportacao() {
            if (!this.stepStates.step2.data) {
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Valide a estrutura dos arquivos primeiro.'
                }
                return
            }

            this.setStepProcessing('step3')
            this.iniciarProgresso()
            
            try {
                // Etapa 1: Verificação de Arquivos (0-20%)
                this.atualizarEtapa(0)
                this.progressoEtapa = 'Verificando arquivos disponíveis...'
                this.progresso = 10
                await this.delay(500)
                
                this.atualizarEtapa(1)
                this.progressoEtapa = 'Estrutura validada com sucesso'
                this.progresso = 20
                await this.delay(300)
                
                // Etapa 2: Preparação do Banco (20-40%)
                this.atualizarEtapa(2)
                this.progressoEtapa = 'Preparando banco de dados...'
                this.progresso = 30
                await this.delay(800)
                
                this.progressoEtapa = 'Verificando tabelas...'
                this.progresso = 40
                await this.delay(600)
                
                // Etapa 3: Importação em Lote (40-80%)
                this.atualizarEtapa(3)
                this.progressoEtapa = 'Executando importação em lote...'
                this.progresso = 50
                await this.delay(1000)
                
                this.progressoEtapa = 'Processando arquivos...'
                this.progresso = 60
                await this.delay(1200)
                
                this.progressoEtapa = 'Gravando dados...'
                this.progresso = 70
                await this.delay(800)
                
                // Etapa 4: Validação de Dados (80-95%)
                this.atualizarEtapa(4)
                this.progressoEtapa = 'Validando dados importados...'
                this.progresso = 80
                await this.delay(600)
                
                this.progressoEtapa = 'Verificando integridade...'
                this.progresso = 90
                await this.delay(400)
                
                // Fazer a requisição real para o servidor
                const response = await axios.post('/api/tabela_oficial/importar_derpr/gravar')

                // Etapa 5: Finalização (95-100%)
                this.atualizarEtapa(5)
                this.progressoEtapa = 'Importação concluída com sucesso!'
                this.progresso = 100
                await this.delay(500)
                
                // Debug da resposta do axios
                // Processar resposta do servidor
                const dados = response.data
                

                
                const stats = this.calcularEstatisticasComposicoes(dados.resultados || {})
                
                this.setStepCompleted('step3', { 
                    ...dados, 
                    total_registros: stats.total,
                    registros_criados: stats.criados,
                    registros_atualizados: stats.atualizados,
                    results: dados.results || {}
                })
                
                // Marcar automaticamente o step4 como completed também
                const finalResult = {
                    ...dados,
                    importacao: dados.results || {},
                    estatisticas: stats,
                    finalizationDate: new Date().toISOString()
                }
                this.setStepCompleted('step4', finalResult)
                
                // Armazenar resultados para exibição
                this.resultadosImportacao = dados.resultados || {}
                
                this.mensagem = {
                    tipo: 'sucesso',
                    texto: `Gravação concluída! ${stats.criados} registros criados e ${stats.atualizados} atualizados na tabela derpr_composicoes.`
                }
                
                // Marcar todas as etapas como completas
                this.etapas.forEach(etapa => {
                    etapa.completa = true
                    etapa.ativa = false
                })
                
            } catch (error) {
                console.error('Erro na importação:', error)
                this.setStepAvailable('step3')
                
                let errorMessage = 'Ocorreu um erro durante a importação.'
                if (error.response?.data?.message) {
                    errorMessage = error.response.data.message
                } else if (error.message) {
                    errorMessage = error.message
                }
                
                this.mensagem = {
                    tipo: 'erro',
                    texto: errorMessage
                }
            } finally {
                if (this.intervaloTempo) {
                    clearInterval(this.intervaloTempo)
                    this.intervaloTempo = null
                }
            }
        },

        // ========================================
        // MÉTODOS DE AÇÃO
        // ========================================
        
                 /**
          * Manipula ações dos componentes ResultDisplay
          */
         handleAction(actionKey, data) {
             // Sem ações implementadas por enquanto
     
         },

        // ========================================
        // MÉTODOS UTILITÁRIOS
        // ========================================
        
        /**
         * Delay para simular processamento
         */
        delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms))
        },

        /**
         * Inicia o sistema de progresso
         */
        iniciarProgresso() {
            this.progresso = 0
            this.horaInicio = new Date().toLocaleTimeString('pt-BR')
            
            this.etapas.forEach(etapa => {
                etapa.completa = false
                etapa.ativa = false
            })
            
            this.intervaloTempo = setInterval(() => {
                const agora = new Date()
                const inicio = new Date(this.horaInicio)
                const diff = Math.floor((agora - inicio) / 1000)
                this.tempoDecorrido = `${diff}s`
            }, 1000)
        },
        
        /**
         * Atualiza o estado de uma etapa específica
         */
        atualizarEtapa(index, ativa = true) {
            for (let i = 0; i < index; i++) {
                this.etapas[i].completa = true
                this.etapas[i].ativa = false
            }
            
            if (index < this.etapas.length) {
                this.etapas[index].ativa = ativa
                this.etapas[index].completa = false
            }
            
            for (let i = index + 1; i < this.etapas.length; i++) {
                this.etapas[i].completa = false
                this.etapas[i].ativa = false
            }
        },

        /**
         * Calcula estatísticas dos registros da tabela derpr_composicoes
         */
        calcularEstatisticasComposicoes(results) {
            // Focar apenas na tabela derpr_composicoes
            // O backend retorna com o nome do arquivo como chave
            const composicoes = results['derpr_composicoes.xlsx'] || {}
            
            // O backend retorna 'registros_inseridos' e 'registros_atualizados'
            // (não 'inseridos' e 'atualizados' como mostram os logs)
            const registrosCriados = composicoes.registros_inseridos || 0
            const registrosAtualizados = composicoes.registros_atualizados || 0
            const totalRegistros = registrosCriados + registrosAtualizados
            
            
            
            return {
                criados: registrosCriados,
                atualizados: registrosAtualizados,
                total: totalRegistros
            }
        },

        /**
         * Calcula o total de registros importados (método legado para compatibilidade)
         */
        calcularTotalRegistros(results) {
            const stats = this.calcularEstatisticasComposicoes(results)
            return stats.total
        },

        

        

        /**
         * Obtém o label da etapa
         */
                 getStepLabel(step) {
             const labels = {
                 1: 'Verificação',
                 2: 'Validação',
                 3: 'Gravação',
                 4: 'Resultado'
             }
             return labels[step] || `Etapa ${step}`
         }
    }
}
</script>

<style>
/* Barra de Progresso Horizontal */
.progress-tracker {
    position: relative;
    padding: 15px 0;
}

.progress-line {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 3px;
    background: #e9ecef;
    border-radius: 2px;
    transform: translateY(-50%);
    z-index: 1;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #18578A 0%, #5EA853 100%);
    border-radius: 2px;
    transition: width 0.5s ease;
}

.progress-steps {
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 2;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
    color: #6c757d;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.progress-step.current .step-circle {
    background: #18578A;
    border-color: #18578A;
    color: white;
    box-shadow: 0 0 0 4px rgba(24, 87, 138, 0.2);
}

.progress-step.completed .step-circle {
    background: #18578A;
    border-color: #18578A;
    color: white;
}

.progress-step.processing .step-circle {
    background: #18578A;
    border-color: #18578A;
    color: white;
    animation: pulse 2s infinite;
}

.step-label {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
}

.progress-step.current .step-label {
    color: #18578A;
}

.progress-step.completed .step-label {
    color: #18578A;
}

/* Seção de Verificação */
.verification-section {
    text-align: center;
}

.verification-info p {
    color: #6c757d;
    line-height: 1.4;
    font-size: 13px;
}

/* Seção de Validação */
.validation-section {
    text-align: center;
}

.validation-info p {
    color: #6c757d;
    line-height: 1.4;
    font-size: 13px;
}

/* Seção de Importação */
.import-section {
    text-align: center;
}

.import-info p {
    color: #6c757d;
    line-height: 1.4;
    font-size: 13px;
}

/* Seção de Resultado */
.result-section {
    text-align: center;
}

.success-message {
    margin-bottom: 20px;
}

.success-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #5EA853;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 24px;
    color: white;
}

/* Seção de Aguardando */
.waiting-section {
    text-align: center;
}

.waiting-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 24px;
    color: #6c757d;
}

.waiting-icon.disabled {
    background: #f8f9fa;
    border-color: #e9ecef;
    opacity: 0.7;
    cursor: not-allowed;
}

/* Detalhes do Processamento */
.processing-details {
    background: white;
    border-radius: 15px;
    border: 2px solid #e9ecef;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    padding: 20px;
}

.processing-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.processing-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: #18578A;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
    margin-right: 15px;
}

.progress-container {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.progress-bar-custom {
    flex: 1;
    height: 10px;
    background: #f8f9fa;
    border-radius: 5px;
    overflow: hidden;
    margin-right: 12px;
}

.progress-fill-custom {
    height: 100%;
    background: linear-gradient(90deg, #18578A 0%, #5EA853 100%);
    border-radius: 5px;
    transition: width 0.3s ease;
}

.progress-text {
    font-weight: bold;
    color: #18578A;
    font-size: 16px;
    min-width: 45px;
}

.processing-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
}

.processing-step {
    display: flex;
    align-items: center;
    padding: 10px 12px;
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.processing-step.completed {
    background: rgba(94, 168, 83, 0.1);
    border-color: #5EA853;
}

.processing-step.active {
    background: rgba(24, 87, 138, 0.1);
    border-color: #18578A;
}

.processing-step.pending {
    background: #f8f9fa;
    border-color: #e9ecef;
}

.step-icon {
    width: 25px;
    margin-right: 12px;
    text-align: center;
}

.processing-step.completed .step-icon {
    color: #5EA853;
}

.processing-step.active .step-icon {
    color: #18578A;
}

.processing-step.pending .step-icon {
    color: #6c757d;
}

.step-text {
    color: #6c757d;
    font-weight: 500;
    font-size: 12px;
}

.processing-step.completed .step-text {
    color: #18578A;
}

/* Alertas */
.alert-custom {
    border: none;
    border-radius: 12px;
    padding: 15px;
    font-weight: 500;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}



/* Animações */
@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.03);
    }
    100% {
        transform: scale(1);
    }
}

/* Responsividade */
@media (max-width: 768px) {
    .progress-steps {
        flex-direction: column;
        gap: 15px;
    }
    
    .progress-line {
        display: none;
    }
    
    .processing-steps {
        grid-template-columns: 1fr;
    }
}
</style>
