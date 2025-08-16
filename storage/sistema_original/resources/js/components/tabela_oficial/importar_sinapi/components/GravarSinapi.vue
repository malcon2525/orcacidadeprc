<!-- 
 * Componente de Gravação de Dados SINAPI no Banco
 * 
 * OBJETIVO: Interface para detecção automática e gravação no banco de dados
 * dos arquivos processados nas Abas 1 (Composições e Insumos) e 2 (Mão de Obra)
 * 
 * ESTRUTURA:
 * - Header com título e descrição da funcionalidade
 * - Progress tracker visual com 3 etapas (Detecção, Gravação, Concluído)
 * - Cards de workflow para cada etapa do processo
 * - Área de detecção automática de arquivos processados
 * - Área de gravação com indicadores visuais
 * - Área de conclusão com resumo da operação
 * 
 * FLUXO DE PROCESSAMENTO:
 * 1. Detecção: Verificação automática de arquivos processados
 * 2. Gravação: Inserção dos dados no banco de dados via API
 * 3. Concluído: Confirmação e resumo da operação
 * 
 * DETECÇÃO AUTOMÁTICA:
 * - Verifica arquivos processados das Abas 1 e 2
 * - Valida se todos os arquivos necessários estão disponíveis
 * - Exibe lista de arquivos detectados e status
 * 
 * GRAVAÇÃO NO BANCO:
 * - Processa composições e insumos (5 tipos: ISD, ICD, CSD, CCD, Analítico)
 * - Processa percentagens de mão de obra (2 tipos: SEM/COM Desoneração)
 * - Atualiza views e tabelas relacionadas
 * - Fornece feedback em tempo real do progresso
 * 
 * ESTADOS VISUAIS:
 * - card-current: Etapa atual ativa
 * - card-completed: Etapa concluída com sucesso
 * - card-processing: Etapa em processamento
 * 
 * INTEGRAÇÃO:
 * - API: /api/tabela-oficial/importar-sinapi/verificar-arquivos-disponiveis
 * - API: /api/tabela-oficial/importar-sinapi/gravar
 * - Backend: ImportarSinapiController@verificarArquivosDisponiveis, @gravar
 -->
<template>
  <div class="gravar-sinapi">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h5 class="mb-1 fw-bold" style="color: #18578A;">
          <i class="fas fa-database me-2"></i>Gravação de Dados SINAPI no Banco
        </h5>
        <p class="text-muted mb-0 small">Detecção automática e gravação dos arquivos processados nas Abas 1 e 2</p>
      </div>
    </div>

    <div class="progress-tracker mb-3">
      <div class="progress-line">
        <div class="progress-fill" :style="{ width: progressoGeral + '%' }"></div>
      </div>
      <div class="progress-steps">
        <div class="progress-step" :class="{ 
          'completed': arquivosDetectados, 
          'current': !arquivosDetectados && !isProcessing 
        }">
          <div class="step-circle">
            <i v-if="arquivosDetectados" class="fas fa-check"></i>
            <span v-else>1</span>
          </div>
          <span class="step-label">Detecção</span>
        </div>
        <div class="progress-step" :class="{ 
          'completed': gravaçãoConcluida, 
          'current': arquivosDetectados && !isProcessing && !gravaçãoConcluida,
          'processing': isProcessing
        }">
          <div class="step-circle">
            <i v-if="gravaçãoConcluida" class="fas fa-check"></i>
            <i v-else-if="isProcessing" class="fas fa-spinner fa-spin"></i>
            <span v-else>2</span>
          </div>
          <span class="step-label">Gravação</span>
        </div>
        <div class="progress-step" :class="{ 
          'completed': gravaçãoConcluida, 
          'current': gravaçãoConcluida 
        }">
          <div class="step-circle">
            <i v-if="gravaçãoConcluida" class="fas fa-check"></i>
            <span v-else>3</span>
          </div>
          <span class="step-label">Concluído</span>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <div class="workflow-card" :class="{ 
          'card-completed': arquivosDetectados, 
          'card-current': !arquivosDetectados && !isProcessing 
        }">
          <div class="card-header-section">
            <div class="step-indicator">
              <div class="step-number" :class="{ 'completed': arquivosDetectados }">
                <i v-if="arquivosDetectados" class="fas fa-check"></i>
                <span v-else>1</span>
              </div>
            </div>
            <div class="card-title">
              <h6 class="mb-1 fw-bold">
                <i class="fas fa-search me-2"></i>Detecção de Arquivos
              </h6>
              <p class="mb-0 text-muted small">Verificando arquivos processados nas Abas 1 e 2</p>
            </div>
            <div class="status-badge" v-if="arquivosDetectados">
              <i class="fas fa-check me-1"></i>Concluído
            </div>
          </div>
          
          <div class="card-content">
            <div v-if="carregandoDetecção" class="detection-info">
              <div class="progress mb-3">
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     :style="{ width: '100%' }"></div>
              </div>
              <p class="text-muted mb-0">Detectando arquivos...</p>
            </div>
            
            <div v-else-if="arquivosDetectados" class="files-info">
              <div class="alert alert-success mb-3">
                <h6 class="mb-2">Arquivos Detectados!</h6>
                <ul class="list-unstyled mb-0 small">
                  <li><strong>Total:</strong> {{ arquivosInfo.total_disponiveis }}/{{ arquivosInfo.total_esperados }}</li>
                  <li><strong>Status:</strong> {{ getStatusText(arquivosInfo.status) }}</li>
                  <li><strong>Diretório:</strong> {{ arquivosInfo.diretorio }}</li>
                </ul>
              </div>
              
              <h6 class="mb-2"><div class="ms-2">Arquivos Disponíveis:</div></h6>
              <div class="files-list">
                <div 
                  v-for="arquivo in arquivosInfo.arquivos_disponiveis" 
                  :key="arquivo.nome"
                  class="file-item"
                >
                  <i class="fas fa-file-excel text-success me-2"></i>
                  <span class="file-name">{{ arquivo.nome }}</span>
                  <small class="text-muted ms-2">({{ formatarTamanho(arquivo.tamanho) }})</small>
                </div>
              </div>
              
              <div v-if="arquivosInfo.arquivos_faltantes.length > 0" class="mt-3">
                <h6 class="text-warning mb-2">Arquivos Faltantes:</h6>
                <div class="files-list">
                  <div 
                    v-for="arquivo in arquivosInfo.arquivos_faltantes" 
                    :key="arquivo.nome"
                    class="file-item missing"
                  >
                    <i class="fas fa-times-circle text-danger me-2"></i>
                    <span class="file-name">{{ arquivo.nome }}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-else class="placeholder-content">
              <div class="placeholder-icon">
                <i class="fas fa-search"></i>
              </div>
              <p class="text-muted mb-0">Clique em "Detectar Arquivos" para verificar</p>
            </div>
            
            <div v-if="!arquivosDetectados && !carregandoDetecção" class="mt-3">
              <button class="btn btn-primary w-100" @click="detectarArquivos">
                <i class="fas fa-search me-2"></i>Detectar Arquivos
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="workflow-card" :class="{ 
          'card-completed': gravaçãoConcluida, 
          'card-current': arquivosDetectados && !isProcessing && !gravaçãoConcluida,
          'card-processing': isProcessing
        }">
          <div class="card-header-section">
            <div class="step-indicator">
              <div class="step-number" :class="{ 'completed': gravaçãoConcluida, 'processing': isProcessing }">
                <i v-if="gravaçãoConcluida" class="fas fa-check"></i>
                <i v-else-if="isProcessing" class="fas fa-spinner fa-spin"></i>
                <span v-else>2</span>
              </div>
            </div>
            <div class="card-title">
              <h6 class="mb-1 fw-bold">
                <i class="fas fa-database me-2"></i>Gravação no Banco
              </h6>
              <p class="mb-0 text-muted small">Processando e gravando dados no banco de dados</p>
            </div>
            <div class="status-badge" v-if="isProcessing">
              <i class="fas fa-spinner fa-spin me-1"></i>Processando
            </div>
            <div class="status-badge" v-else-if="gravaçãoConcluida">
              <i class="fas fa-check me-1"></i>Concluído
            </div>
          </div>
          
          <div class="card-content">
            <div v-if="isProcessing" class="processing-info">
              <div class="progress mb-3">
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     :style="{ width: processingProgress + '%' }"></div>
              </div>
              <p class="text-muted mb-0">{{ processingMessage }}</p>
              
              <div v-if="processingProgress > 0 && processingProgress < 100" class="time-estimate mt-2">
                <small class="text-muted">
                  <i class="fas fa-clock me-1"></i>
                  Pode levar alguns minutos para completar o processamento
                </small>
              </div>
            </div>
            
            <div v-else-if="gravaçãoConcluida" class="results-info">
              <div class="alert alert-success">
                <h6 class="mb-2">Gravação Concluída!</h6>
                <ul class="list-unstyled mb-0 small">
                  <li><strong>Total de registros:</strong> {{ processingResults.total_registros }}</li>
                  <li><strong>Arquivos processados:</strong> {{ processingResults.arquivos_processados }}</li>
                  <li><strong>Tempo de processamento:</strong> {{ processingResults.tempo_processamento }}</li>
                </ul>
              </div>
              
              <div v-if="processingResults.details" class="mt-3">
                <h6 class="text-primary mb-2">Detalhes:</h6>
                <ul class="list-unstyled mb-0 small">
                  <li 
                    v-for="(detail, index) in processingResults.details" 
                    :key="index"
                    class="processing-detail"
                  >
                    <i class="fas fa-check-circle text-success me-2"></i>
                    {{ detail }}
                  </li>
                </ul>
              </div>
            </div>
            
            <div v-else class="placeholder-content">
              <div class="placeholder-icon">
                <i class="fas fa-lock"></i>
              </div>
              <p class="text-muted mb-0">Aguardando detecção de arquivos</p>
            </div>


            
            <div v-if="arquivosDetectados && arquivosInfo.pode_gravar && !isProcessing && !gravaçãoConcluida" class="mt-3">
              <button class="btn btn-primary w-100" @click="iniciarGravação">
                <i class="fas fa-play me-2"></i>Iniciar Gravação
              </button>
            </div>
            
            <div v-else-if="arquivosDetectados && !arquivosInfo.pode_gravar" class="mt-3">
              <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Atenção:</strong> Execute o processamento das Abas 1 e 2 primeiro.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="error" class="alert alert-danger mt-3">
      <i class="fas fa-exclamation-triangle me-2"></i>
      {{ error }}
    </div>
  </div>
</template>

<script>
/**
 * Script do Componente de Gravação de Dados SINAPI no Banco
 * 
 * RESPONSABILIDADES:
 * - Detectar automaticamente arquivos processados das Abas 1 e 2
 * - Validar disponibilidade e integridade dos arquivos
 * - Controlar o fluxo de gravação no banco de dados
 * - Manter estados visuais do progress tracker
 * - Fornecer feedback detalhado durante a gravação
 * - Exibir resultados finais da operação
 * 
 * DADOS PRINCIPAIS:
 * - carregandoDetecção: Flag indicando detecção em andamento
 * - arquivosDetectados: Flag indicando se arquivos foram detectados
 * - arquivosInfo: Informações dos arquivos detectados
 * - isProcessing: Flag indicando gravação em andamento
 * - processingProgress: Percentual de progresso da gravação (0-100)
 * - processingMessage: Mensagem de status da gravação
 * - processingResults: Resultados detalhados da gravação
 * - gravaçãoConcluida: Flag indicando conclusão da gravação
 * 
 * MÉTODOS PRINCIPAIS:
 * - detectarArquivos(): Verifica arquivos processados via API
 * - iniciarGravação(): Inicia processo de gravação no banco
 * - formatarTamanho(): Converte bytes em formato legível
 * - getStatusText(): Converte código de status em texto legível
 * 
 * INTEGRAÇÃO API:
 * - GET /api/tabela_oficial/importar_sinapi/verificar_arquivos
 * - POST /api/tabela_oficial/importar_sinapi/gravar
 */
import axios from 'axios';

export default {
  name: 'GravarSinapi',
  data() {
    return {
      carregandoDetecção: false,
      arquivosDetectados: false,
      arquivosInfo: null,
      isProcessing: false,
      processingProgress: 0,
      processingMessage: '',
      processingResults: null,
      gravaçãoConcluida: false,
      error: null
    }
  },
  computed: {
    progressoGeral() {
      if (this.gravaçãoConcluida) return 100
      if (this.isProcessing) return 66
      if (this.arquivosDetectados) return 33
      return 0
    }
  },
  methods: {
    async detectarArquivos() {
      this.carregandoDetecção = true
      this.error = null
      
      try {
        const response = await axios.get('/api/tabela_oficial/importar_sinapi/verificar_arquivos')
        
        if (response.data.success) {
          this.arquivosInfo = response.data
          this.arquivosDetectados = true
        } else {
          throw new Error(response.data.message)
        }
        
      } catch (error) {
        this.error = 'Erro ao detectar arquivos: ' + (error.response?.data?.message || error.message)
        this.arquivosDetectados = false
      } finally {
        this.carregandoDetecção = false
      }
    },

    async iniciarGravação() {
      if (!this.arquivosInfo?.pode_gravar) {
        this.error = 'Não é possível gravar. Verifique se todos os arquivos estão disponíveis.'
        return
      }

      this.isProcessing = true
      this.processingProgress = 0
      this.processingMessage = 'Iniciando gravação...'
      this.error = null

      try {
        this.processingProgress = 10
        this.processingMessage = 'Enviando requisição de gravação...'

        const response = await axios.post('/api/tabela_oficial/importar_sinapi/gravar')

        if (response.data.success) {
          this.processingResults = response.data.results
          this.processingProgress = 100
          this.processingMessage = 'Gravação concluída!'
          this.gravaçãoConcluida = true
        } else {
          throw new Error(response.data.message)
        }

      } catch (error) {
        this.error = 'Erro ao gravar dados: ' + (error.response?.data?.message || error.message)
      } finally {
        this.isProcessing = false
      }
    },

    formatarTamanho(bytes) {
      if (bytes === 0) return '0 Bytes'
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    },

    getStatusText(status) {
      const statusMap = {
        'completo': 'Todos os arquivos disponíveis',
        'incompleto': 'Alguns arquivos faltando',
        'sem_arquivos': 'Nenhum arquivo encontrado',
        'sem_diretorio': 'Diretório não encontrado',
        'diretorio_inexistente': 'Diretório não existe',
        'erro': 'Erro na verificação'
      }
      return statusMap[status] || 'Status desconhecido'
    }
  }
}
</script>

<style scoped>
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
}

.progress-tracker {
  position: relative;
  margin: 20px 0;
}

.progress-line {
  position: absolute;
  top: 35px;
  left: 0;
  right: 0;
  height: 2px;
  background: #e9ecef;
  z-index: 1;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #5EA853 0%, #18578A 100%);
  transition: width 0.3s ease;
}

.progress-steps {
  display: flex;
  justify-content: space-between;
  position: relative;
  z-index: 2;
}

.progress-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
}

.step-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: white;
  border: 2px solid #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: #6c757d;
  transition: all 0.3s ease;
}

.progress-step.completed .step-circle {
  background: #5EA853;
  border-color: #5EA853;
  color: white;
}

.progress-step.current .step-circle {
  background: #18578A;
  border-color: #18578A;
  color: white;
}

.progress-step.processing .step-circle {
  background: #18578A;
  border-color: #18578A;
  color: white;
}

.step-label {
  margin-top: 8px;
  font-size: 12px;
  font-weight: 500;
  color: #6c757d;
  text-align: center;
}

.progress-step.completed .step-label {
  color: #5EA853;
}

.progress-step.current .step-label {
  color: #18578A;
}

.card-header-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 15px 20px 12px;
  border-bottom: 1px solid #f8f9fa;
  position: relative;
  min-height: 60px;
}

.step-indicator {
  position: absolute;
  top: 15px;
  right: 15px;
  flex-shrink: 0;
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

.card-title {
  flex: 1;
  margin-right: 15px;
}

.status-badge {
  position: absolute;
  top: 15px;
  right: 55px;
  background: #5EA853;
  color: white;
  padding: 3px 8px;
  border-radius: 15px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

.status-badge.processing {
  background: #18578A;
  color: white;
}

.alert-success {
  background: #D1E7DD;
  border: none;
  color: #666;
  box-shadow: 0 2px 8px rgba(94, 168, 83, 0.2);
}

.card-content {
  padding: 0 20px 20px;
  overflow: visible;
}

.placeholder-content {
  text-align: center;
  padding: 30px 20px;
}

.placeholder-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 15px;
  color: #6c757d;
  font-size: 24px;
}

.files-list {
  max-height: 200px;
  overflow-y: auto;
}

.file-item {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 6px;
  margin-bottom: 8px;
  border-left: 3px solid #5EA853;
  font-size: 13px;
  color: #495057;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

.file-item.missing {
  background: #fff3cd;
  border: 1px solid #ffeaa7;
}

.file-name {
  flex: 1;
  min-width: 0;
  word-wrap: break-word;
  overflow-wrap: break-word;
  white-space: normal;
  font-weight: 500;
  color: #495057;
}

.progress {
  height: 8px;
  border-radius: 4px;
  background: rgba(24, 87, 138, 0.1);
  overflow: hidden;
}

.progress-bar {
  background: linear-gradient(90deg, #18578A 0%, #5EA853 100%);
  transition: width 0.3s ease;
}

.btn {
  border-radius: 8px;
  font-weight: 500;
  padding: 10px 20px;
  border: none;
  transition: all 0.3s ease;
}

.btn-primary {
  background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
  color: white;
}

.btn-success {
  background: linear-gradient(135deg, #5EA853 0%, #28a745 100%);
  color: white;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.alert {
  border-radius: 10px;
  border: none;
  padding: 15px;
}

.alert-warning {
  background: #fff3cd;
  color: #856404;
}

.processing-detail {
  margin-bottom: 8px;
  font-size: 13px;
  padding: 8px 12px;
  background: rgba(94, 168, 83, 0.1);
  border-radius: 6px;
  border-left: 3px solid #5EA853;
}

.time-estimate {
  background: rgba(24, 87, 138, 0.1);
  padding: 8px 12px;
  border-radius: 6px;
  border-left: 3px solid #18578A;
  margin-top: 10px;
}

.processing-info {
  background: rgba(24, 87, 138, 0.05);
  border: 1px solid rgba(24, 87, 138, 0.2);
  border-radius: 8px;
  padding: 15px;
  margin-top: 15px;
}

@media (max-width: 768px) {
  .card-header-section {
    flex-direction: column;
    align-items: flex-start;
    min-height: auto;
  }
  
  .step-indicator {
    margin-right: 0;
    margin-bottom: 10px;
  }
  
  .card-title {
    margin-right: 0;
    width: 100%;
  }
  
  .status-badge {
    position: static;
    margin-top: 10px;
    align-self: flex-end;
  }
  
  .progress-steps {
    flex-direction: column;
    gap: 1rem;
  }
  
  .progress-line {
    display: none;
  }
  
  .step-circle {
    width: 35px;
    height: 35px;
    font-size: 14px;
  }
  
  .step-label {
    font-size: 11px;
  }
}
</style> 