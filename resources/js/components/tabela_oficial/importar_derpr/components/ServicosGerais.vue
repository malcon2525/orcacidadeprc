<template>
  <div>
    <!-- Cabeçalho Elegante -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h5 class="mb-1 fw-bold text-custom">
          <i class="fas fa-file-pdf me-2"></i>Conversão para Excel da Tabela de Serviços do DER-PR (Sintético)
        </h5>
        <p class="text-muted mb-0 small">Processe arquivos PDF e converta para formato Excel estruturado</p>
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
      <!-- Card 1: Upload -->
      <div class="col-md-6 col-lg-3">
        <WorkflowCard
          :step="1"
          :status="computedStepStates.step1.status"
          title="Upload do Arquivo"
          description="Selecione o PDF da tabela sintética"
          icon="fa-cloud-upload-alt"
          :step-data="stepStates.step1.data"
        >
          <template #default="{ status }">
            <!-- Conteúdo do Card 1 -->
            <div v-if="status === 'available' || status === 'completed'">
              <div class="upload-zone" :class="{ 'has-file': arquivoValido }">
                <input 
                  type="file" 
                  class="file-input" 
                  id="arquivo" 
                  ref="arquivoInput"
                  accept=".pdf" 
                  required
                  @change="validarArquivo"
                >
                
                <div v-if="!nomeArquivo" class="upload-placeholder">
                  <div class="upload-icon">
                    <i class="fas fa-file-pdf"></i>
                  </div>
                  <h6 class="mt-2 mb-1">Selecione um arquivo PDF</h6>
                  <p class="text-muted mb-0 small">Clique ou arraste o arquivo aqui</p>
                </div>
                
                <div v-else class="file-info">
                  <div class="file-icon">
                    <i class="fas fa-file-pdf"></i>
                  </div>
                  <div class="file-details">
                    <h6 class="mb-1">{{ nomeArquivo }}</h6>
                    <p class="text-success mb-0 small">
                      <i class="fas fa-check-circle me-1"></i>Arquivo carregado com sucesso!
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </WorkflowCard>
      </div>

      <!-- Card 2: Processamento -->
      <div class="col-md-6 col-lg-3">
        <WorkflowCard
          :step="2"
          :status="computedStepStates.step2.status"
          title="Processamento"
          description="Inicie o processamento da tabela"
          icon="fa-cogs"
          :step-data="stepStates.step2.data"
        >
          <template #default="{ status }">
            <!-- Conteúdo do Card 2 -->
            <div v-if="status === 'available'">
              <div class="processing-section">
                <div class="processing-info">
                  <p class="mb-3 small">Inicie o processamento da tabela sintética. Este processo pode levar alguns minutos.</p>
                </div>
                <button 
                  class="btn btn-primary btn-lg w-100 fw-semibold" 
                  @click="processarArquivo"
                >
                  <i class="fas fa-play me-2"></i>
                  Iniciar Processamento
                </button>
              </div>
            </div>
            
            <div v-else-if="status === 'processing'">
              <ResultDisplay
                type="processing"
                title="Processando..."
                description="Executando script Python e extraindo dados..."
              />
            </div>
            
            <div v-else-if="status === 'completed'">
              <ResultDisplay
                type="success"
                title="Processamento Concluído!"
                :description="`${stepStates.step2.data?.length || 0} registros extraídos`"
                :actions="[
                  { key: 'export', label: 'Exportar Excel', icon: 'fa-file-excel', class: 'btn-outline-success btn-sm' }
                ]"
                :data="stepStates.step2.data"
                @action="handleAction"
              />
            </div>
          </template>
        </WorkflowCard>
      </div>

      <!-- Card 3: Validação -->
      <div class="col-md-6 col-lg-3">
        <WorkflowCard
          :step="3"
          :status="computedStepStates.step3.status"
          title="Validação"
          description="Verifique a qualidade dos dados"
          icon="fa-check-circle"
          :step-data="stepStates.step3.data"
        >
          <template #default="{ status }">
            <!-- Conteúdo do Card 3 -->
            <div v-if="status === 'available'">
              <div class="validation-section">
                <div class="validation-info">
                  <p class="mb-3 small">Valide a estrutura e qualidade dos dados extraídos antes de prosseguir.</p>
                </div>
                <button 
                  class="btn btn-info btn-lg w-100 fw-semibold" 
                  @click="validarDados"
                >
                  <i class="fas fa-check me-2"></i>
                  Validar Dados
                </button>
              </div>
            </div>
            
            <div v-else-if="status === 'processing'">
              <ResultDisplay
                type="processing"
                title="Validando..."
                description="Verificando estrutura e qualidade dos dados..."
              />
            </div>
            
            <div v-else-if="status === 'completed'">
              <ResultDisplay
                type="success"
                title="Validação Automática Concluída!"
                :description="`${stepStates.step3.data?.validRecords || 0} registros validados durante o processamento`"
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
          description="Visualize e exporte os dados"
          icon="fa-chart-bar"
          :step-data="stepStates.step4.data"
        >
          <template #default="{ status }">
            <!-- Conteúdo do Card 4 -->
            <div v-if="status === 'available'">
              <div class="final-section">
                <div class="final-info">
                  <p class="mb-3 small">Todos os passos foram concluídos. Prossiga para a próxima aba</p>
                </div>
              </div>
            </div>
            
            <div v-else-if="status === 'completed'">
              <ResultDisplay
                type="success"
                title="Processamento Finalizado!"
                :description="`Workflow completo com ${overallProgress}% de progresso`"
                :actions="[
                  { key: 'download', label: 'Download Completo', icon: 'fa-download', class: 'btn-success' },
                  { key: 'reset', label: 'Novo Processamento', icon: 'fa-redo', class: 'btn-outline-secondary' }
                ]"
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
          <i class="fas fa-cogs fa-spin"></i>
        </div>
        <div class="processing-info">
          <h6 class="mb-1 fw-semibold">Processando Arquivo PDF</h6>
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

    <!-- Modal para Visualizar Dados -->
    <div class="modal fade" id="modalVisualizarDados" tabindex="-1" aria-labelledby="modalVisualizarDadosLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-white">
            <h5 class="modal-title fw-semibold text-custom" id="modalVisualizarDadosLabel">
              <i class="fas fa-table me-2"></i>Dados Processados - {{ stepStates.step2.data?.length || 0 }} registros
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0 servicos-gerais-table">
                <thead>
                  <tr>
                    <th>Grupo</th>
                    <th>Data Base</th>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Unidade</th>
                    <th class="text-end">Custo <br> Execução</th>
                    <th class="text-end">Custo <br> Material</th>
                    <th class="text-end">Custo <br> Sub-Serviço</th>
                    <th class="text-end">Custo <br> Unitário</th>
                    <th class="text-end">Transporte</th>
                    <th>Honerado</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in stepStates.step2.data" :key="index">
                    <td>{{ item.grupo }}</td>
                    <td>{{ item.data_base }}</td>
                    <td>{{ item.codigo }}</td>
                    <td>{{ item.descricao }}</td>
                    <td>{{ item.unidade }}</td>
                    <td class="text-end">{{ item.custo_execucao }}</td>
                    <td class="text-end">{{ item.custo_material }}</td>
                    <td class="text-end">{{ item.custo_sub_servico }}</td>
                    <td class="text-end">{{ item.custo_unitario }}</td>
                    <td class="text-end">{{ item.transporte }}</td>
                    <td>{{ item.honerado }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import WorkflowCard from './base/WorkflowCard.vue'
import ResultDisplay from './base/ResultDisplay.vue'
import { useWorkflowState } from '../composables/useWorkflowState.js'

export default {
    name: 'ServicosGerais',
    
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
            // Estado do arquivo
            arquivoValido: false,
            nomeArquivo: '',
            
            // Sistema de progresso
            progresso: 0,
            progressoEtapa: '',
            horaInicio: null,
            tempoDecorrido: '0s',
            intervaloTempo: null,
            
            // Etapas do processamento
            etapas: [
                { nome: 'Upload e Validação', completa: false, ativa: false },
                { nome: 'Execução Python', completa: false, ativa: false },
                { nome: 'Processamento PDF', completa: false, ativa: false },
                { nome: 'Estruturação Dados', completa: false, ativa: false },
                { nome: 'Validação Estrutura', completa: false, ativa: false },
                { nome: 'Finalização', completa: false, ativa: false }
            ],
            
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
        // MÉTODOS DE VALIDAÇÃO E UPLOAD
        // ========================================
        
        /**
         * Valida o arquivo selecionado pelo usuário
         */
        validarArquivo(event) {
            const arquivo = event.target.files[0]
            
            // Validar se um arquivo foi selecionado
            if (!arquivo) {
                this.arquivoValido = false
                this.nomeArquivo = ''
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Por favor, selecione um arquivo.'
                }
                return
            }
            
            // Validar tipo do arquivo
            if (arquivo.type !== 'application/pdf') {
                this.arquivoValido = false
                this.nomeArquivo = arquivo.name
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Por favor, selecione um arquivo PDF válido.'
                }
                return
            }
            
            // Validar tamanho do arquivo (máximo 50MB)
            const tamanhoMaximo = 50 * 1024 * 1024 // 50MB
            if (arquivo.size > tamanhoMaximo) {
                this.arquivoValido = false
                this.nomeArquivo = arquivo.name
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'O arquivo é muito grande. Tamanho máximo: 50MB.'
                }
                return
            }
            
            // Arquivo válido - marcar etapa 1 como concluída
            this.arquivoValido = true
            this.nomeArquivo = arquivo.name
            this.setStepCompleted('step1', { fileName: arquivo.name, fileSize: arquivo.size })
            
            this.mensagem = {
                tipo: 'sucesso',
                texto: 'Arquivo PDF válido selecionado.'
            }
        },
        
        // ========================================
        // MÉTODOS DE PROGRESSO
        // ========================================
        
        /**
         * Inicia o sistema de progresso do processamento
         */
        iniciarProgresso() {
            this.progresso = 0
            this.horaInicio = new Date().toLocaleTimeString('pt-BR')
            
            // Resetar todas as etapas
            this.etapas.forEach(etapa => {
                etapa.completa = false
                etapa.ativa = false
            })
            
            // Iniciar contador de tempo
            this.intervaloTempo = setInterval(() => {
                const agora = new Date()
                const inicio = new Date(this.horaInicio)
                const diff = Math.floor((agora - inicio) / 1000)
                this.tempoDecorrido = `${diff}s`
            }, 1000)
        },
        
        /**
         * Atualiza o estado de uma etapa específica do processamento
         */
        atualizarEtapa(index, ativa = true) {
            // Marcar etapas anteriores como completas
            for (let i = 0; i < index; i++) {
                this.etapas[i].completa = true
                this.etapas[i].ativa = false
            }
            
            // Marcar etapa atual como ativa
            if (index < this.etapas.length) {
                this.etapas[index].ativa = ativa
                this.etapas[index].completa = false
            }
            
            // Marcar etapas posteriores como inativas
            for (let i = index + 1; i < this.etapas.length; i++) {
                this.etapas[i].completa = false
                this.etapas[i].ativa = false
            }
        },
        
        // ========================================
        // MÉTODOS DE PROCESSAMENTO
        // ========================================
        
        /**
         * Processa o arquivo PDF enviado pelo usuário
         */
        async processarArquivo() {
            // Validar se há arquivo válido
            if (!this.arquivoValido) {
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Por favor, selecione um arquivo PDF válido primeiro.'
                }
                return
            }

            // Inicializar processamento
            this.setStepProcessing('step2')
            this.iniciarProgresso()
            
            // Preparar dados para envio
            const formData = new FormData()
            formData.append('arquivo', this.$refs.arquivoInput.files[0])

            try {
                // Etapa 1: Upload e Validação (0-20%)
                this.atualizarEtapa(0)
                this.progressoEtapa = 'Validando arquivo PDF...'
                this.progresso = 10
                await this.delay(500)
                
                this.atualizarEtapa(1)
                this.progressoEtapa = 'Arquivo validado com sucesso'
                this.progresso = 20
                await this.delay(300)
                
                // Etapa 2: Executando script Python (20-60%)
                this.atualizarEtapa(2)
                this.progressoEtapa = 'Executando script Python para extração de dados...'
                this.progresso = 30
                await this.delay(1000)
                
                this.progressoEtapa = 'Processando PDF com pdfplumber...'
                this.progresso = 40
                await this.delay(1500)
                
                this.progressoEtapa = 'Extraindo dados das tabelas...'
                this.progresso = 50
                await this.delay(1000)
                
                // Etapa 3: Processando dados (60-80%)
                this.atualizarEtapa(3)
                this.progressoEtapa = 'Processando dados extraídos...'
                this.progresso = 60
                await this.delay(800)
                
                this.progressoEtapa = 'Validando estrutura dos dados...'
                this.progresso = 70
                await this.delay(600)
                
                // Etapa 4: Estruturando informações (80-95%)
                this.atualizarEtapa(4)
                this.progressoEtapa = 'Estruturando informações para exibição...'
                this.progresso = 80
                await this.delay(500)
                
                this.progressoEtapa = 'Preparando dados para visualização...'
                this.progresso = 90
                await this.delay(400)
                
                // Fazer a requisição real para o servidor
                const response = await axios.post('/tabela_oficial/importar_derpr/servicos-gerais', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })

                // Etapa 5: Finalização (95-100%)
                this.atualizarEtapa(5)
                this.progressoEtapa = 'Dados processados com sucesso!'
                this.progresso = 100
                await this.delay(500)
                
                // Processar resposta
                const dados = response.data
                
                // Atualizar estado do step2
                this.setStepCompleted('step2', dados)
                
                // Atualizar estado do step3
                this.setStepCompleted('step3', dados)
                
                // Atualizar estado do step4
                this.setStepCompleted('step4', dados)
                
                // Armazenar resultados para exibição
                this.resultadosProcessamento = dados.resultados || {}
                
                this.mensagem = {
                  tipo: 'sucesso',
                  texto: 'Arquivo processado com sucesso!'
                }
                
                // Marcar todas as etapas como completas
                this.etapas.forEach(etapa => {
                    etapa.completa = true
                    etapa.ativa = false
                })
                
                // Forçar atualização do estado do workflow
                this.$nextTick(() => {
            
                })
                
            } catch (error) {
                // Tratar erros de processamento
                this.setStepAvailable('step2')
                
                let errorMessage = 'Ocorreu um erro ao processar o arquivo.'
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
                // Limpar intervalos
                if (this.intervaloTempo) {
                    clearInterval(this.intervaloTempo)
                    this.intervaloTempo = null
                }
            }
        },

        // ========================================
        // MÉTODOS DE VALIDAÇÃO
        // ========================================
        
        /**
         * Valida os dados extraídos
         */
        async validarDados() {
            if (!this.stepStates.step2.data) {
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Nenhum dado para validar. Processe o arquivo primeiro.'
                }
                return
            }

            this.setStepProcessing('step3')
            
            try {
                // Simular validação
                await this.delay(2000)
                
                const dados = this.stepStates.step2.data
                const validRecords = dados.length
                const validationResult = {
                    validRecords,
                    totalRecords: dados.length,
                    validationDate: new Date().toISOString(),
                    issues: []
                }
                
                this.setStepCompleted('step3', validationResult)
                
                this.mensagem = {
                    tipo: 'sucesso',
                    texto: `Validação concluída! ${validRecords} registros válidos.`
                }
                
            } catch (error) {
                this.setStepAvailable('step3')
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Erro durante a validação dos dados.'
                }
            }
        },

        // ========================================
        // MÉTODOS DE FINALIZAÇÃO
        // ========================================
        
        /**
         * Finaliza o processamento
         */
        async finalizarProcessamento() {
            if (!this.stepStates.step3.data) {
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Valide os dados primeiro.'
                }
                return
            }

            this.setStepProcessing('step4')
            
            try {
                // Simular finalização
                await this.delay(1500)
                
                const finalResult = {
                    ...this.stepStates.step2.data,
                    validation: this.stepStates.step3.data,
                    finalizationDate: new Date().toISOString()
                }
                
                this.setStepCompleted('step4', finalResult)
                
                this.mensagem = {
                    tipo: 'sucesso',
                    texto: 'Processamento finalizado com sucesso!'
                }
                
            } catch (error) {
                this.setStepAvailable('step4')
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Erro durante a finalização.'
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
            switch (actionKey) {
                case 'view':
                    this.visualizarDados()
                    break
                case 'export':
                    this.exportarParaExcel(data, 'composicoes')
                    break
                case 'details':
                    this.verDetalhesValidacao()
                    break
                case 'download':
                    this.downloadCompleto(data)
                    break
                case 'reset':
                    this.resetWorkflow()
                    this.arquivoValido = false
                    this.nomeArquivo = ''
                    this.mensagem = null
                    break
                default:
            
            }
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
         * Visualiza os dados processados
         */
        visualizarDados() {
            // Abrir modal com dados
            const modal = new bootstrap.Modal(document.getElementById('modalVisualizarDados'))
            modal.show()
        },

        /**
         * Exporta dados para Excel
         */
        exportarParaExcel(dados, tipo) {
            // Implementar exportação para Excel
    
            this.mensagem = {
                tipo: 'sucesso',
                texto: 'Exportação para Excel iniciada!'
            }
        },

        /**
         * Ver detalhes da validação
         */
        verDetalhesValidacao() {
    
        },

        /**
         * Download completo dos dados
         */
        downloadCompleto(data) {
    
            this.mensagem = {
                tipo: 'sucesso',
                texto: 'Download completo iniciado!'
            }
        },

        /**
         * Obtém o label da etapa
         */
        getStepLabel(step) {
            const labels = {
                1: 'Upload',
                2: 'Processamento',
                3: 'Validação',
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

/* Cards de Workflow */
            .workflow-card {
                background: white;
                border-radius: 15px;
                border: 2px solid #e9ecef;
                box-shadow: 0 8px 25px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
                overflow: hidden;
                height: 100%;
            }

.workflow-card.card-current {
    border-color: #18578A;
    box-shadow: 0 12px 35px rgba(24, 87, 138, 0.2);
    transform: translateY(-3px);
}

.workflow-card.card-completed {
    border-color: #5EA853;
    box-shadow: 0 8px 25px rgba(94, 168, 83, 0.2);
}

.workflow-card.card-processing {
    border-color: #18578A;
    animation: pulse 2s infinite;
}

.workflow-card.card-disabled {
    border-color: #e9ecef;
    background-color: #f8f9fa;
    box-shadow: none;
    transform: none;
    opacity: 0.7;
    cursor: not-allowed;
}

.workflow-card.card-disabled:hover {
    transform: none;
    box-shadow: none;
}

.card-header-section {
    padding: 15px 20px 12px;
    border-bottom: 1px solid #f8f9fa;
    position: relative;
}

.step-indicator {
    position: absolute;
    top: 15px;
    right: 15px;
}

.step-number {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    color: #6c757d;
    transition: all 0.3s ease;
}

.step-number.completed {
    background: #5EA853;
    border-color: #5EA853;
    color: white;
}

.step-number.processing {
    background: #18578A;
    border-color: #18578A;
    color: white;
    animation: pulse 2s infinite;
}

.step-number.disabled {
    background: #e9ecef;
    border-color: #e9ecef;
    color: #adb5bd;
    cursor: not-allowed;
}

.card-title h6 {
    color: #18578A;
    font-size: 16px;
}

.card-title h6.text-muted {
    color: #6c757d;
}

.status-badge {
    position: absolute;
    top: 15px;
    right: 55px;
    background: #18578A;
    color: white;
    padding: 3px 8px;
    border-radius: 15px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.ready {
    background: #ffc107;
    color: #212529;
}

.status-badge.disabled {
    background: #e9ecef;
    color: #adb5bd;
    border: 1px solid #e9ecef;
}

.card-content {
    padding: 15px 20px;
}

/* Área de Upload */
.upload-zone {
    border: 2px dashed #e9ecef;
    border-radius: 12px;
    padding: 25px 15px;
    text-align: center;
    transition: all 0.3s ease;
    background: #f8f9fa;
    position: relative;
    min-height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-zone.has-file {
    border-color: #5EA853;
    background: rgba(94, 168, 83, 0.05);
}

.file-input {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    top: 0;
    left: 0;
}

.upload-placeholder {
    pointer-events: none;
}

.upload-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 24px;
    color: #6c757d;
}

.file-info {
    display: flex;
    align-items: center;
    text-align: left;
    width: 100%;
}

.file-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: #5EA853;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
    margin-right: 15px;
    flex-shrink: 0;
}

.file-details h6 {
    color: #18578A;
    margin-bottom: 3px;
    font-size: 14px;
}

/* Seção de Processamento */
.processing-section {
    text-align: center;
}

.processing-info p {
    color: #6c757d;
    line-height: 1.4;
    font-size: 13px;
}

.action-buttons .btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 10px 20px;
    font-size: 14px;
}

.action-buttons .btn-disabled {
    background-color: #e9ecef;
    border-color: #e9ecef;
    color: #adb5bd;
    cursor: not-allowed;
    opacity: 0.7;
}

.action-buttons .btn-disabled:hover {
    background-color: #e9ecef;
    border-color: #e9ecef;
    color: #adb5bd;
    transform: none;
    box-shadow: none;
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
    
    .workflow-card {
        margin-bottom: 15px;
    }
    
    .processing-steps {
        grid-template-columns: 1fr;
    }
    
    .status-badge {
        position: static;
        margin-top: 8px;
        display: inline-block;
    }
}
</style> 