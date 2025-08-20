<template>
  <div class="gravar-derpr">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h5 class="mb-1 fw-bold" style="color: #18578A;">
          <i class="fas fa-database me-2"></i>Gravação de Dados DER-PR no Banco
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
            <div v-if="!arquivosDetectados" class="placeholder-content">
              <div class="placeholder-icon">
                <i class="fas fa-database"></i>
              </div>
              <p class="text-muted mb-0">Detecte os arquivos primeiro</p>
            </div>
            
            <div v-else-if="isProcessing" class="processing-info">
              <div class="progress mb-3">
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     :style="{ width: processingProgress + '%' }"></div>
              </div>
              <p class="text-muted mb-0">{{ processingMessage }}</p>
            </div>
            
            <div v-else-if="gravaçãoConcluida" class="success-info">
              <div class="alert alert-success mb-3">
                <h6 class="mb-2">Gravação Concluída!</h6>
                <div v-if="processingResults" class="results-summary">
                  <ul class="list-unstyled mb-0 small">
                    <li v-for="(resultado, arquivo) in processingResults" :key="arquivo">
                      <strong>{{ arquivo }}:</strong> 
                      {{ resultado.registros_inseridos }} inseridos, 
                      {{ resultado.registros_atualizados }} atualizados
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            
            <div v-else class="action-content">
              <div class="alert alert-info mb-3">
                <h6 class="mb-2">Pronto para Gravar!</h6>
                <p class="mb-0 small">Todos os arquivos necessários foram detectados. Clique em "Iniciar Gravação" para processar os dados.</p>
              </div>
              
              <button class="btn btn-success w-100" @click="iniciarGravação" :disabled="!arquivosInfo?.pode_gravar">
                <i class="fas fa-database me-2"></i>Iniciar Gravação
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Área de Erros -->
    <div v-if="error" class="alert alert-danger mt-3">
      <h6 class="mb-2">Erro:</h6>
      <p class="mb-0">{{ error }}</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ImportarLoteDerpr',
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
        const response = await axios.get('/api/tabela_oficial/importar_derpr/verificar_arquivos')
        
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

        const response = await axios.post('/api/tabela_oficial/importar_derpr/gravar')

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

.progress-step.completed .step-label,
.progress-step.current .step-label,
.progress-step.processing .step-label {
  color: #18578A;
  font-weight: 600;
}

.card-header-section {
  display: flex;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #e9ecef;
}

.step-indicator {
  margin-right: 15px;
}

.step-number {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  background: #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: #6c757d;
  transition: all 0.3s ease;
}

.step-number.completed {
  background: #5EA853;
  color: white;
}

.step-number.processing {
  background: #18578A;
  color: white;
}

.card-title {
  flex: 1;
}

.card-title h6 {
  margin: 0;
  color: #18578A;
}

.card-title p {
  margin: 0;
  font-size: 12px;
}

.status-badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  background: #e9ecef;
  color: #6c757d;
}

.card-content {
  padding: 20px;
}

.detection-info,
.processing-info {
  text-align: center;
  padding: 20px 0;
}

.files-info {
  margin-bottom: 15px;
}

.files-list {
  max-height: 200px;
  overflow-y: auto;
}

.file-item {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  border-radius: 8px;
  margin-bottom: 5px;
  background: #f8f9fa;
  border: 1px solid #e9ecef;
}

.file-item.missing {
  background: #fff5f5;
  border-color: #fed7d7;
}

.file-name {
  flex: 1;
  font-size: 13px;
  font-weight: 500;
}

.placeholder-content {
  text-align: center;
  padding: 30px 20px;
}

.placeholder-icon {
  font-size: 48px;
  color: #e9ecef;
  margin-bottom: 15px;
}

.action-content {
  text-align: center;
  padding: 20px 0;
}

.success-info {
  text-align: center;
}

.results-summary {
  text-align: left;
  margin-top: 15px;
}

.results-summary li {
  margin-bottom: 5px;
  font-size: 13px;
}
</style>
