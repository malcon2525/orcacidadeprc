<!-- 
 * Componente de Processamento de Percentagens de Mão de Obra SINAPI
 * 
 * OBJETIVO: Interface para upload e processamento de arquivos Excel da tabela SINAPI
 * contendo percentagens de mão de obra (2 abas: SEM Desoneração e COM Desoneração)
 * 
 * ESTRUTURA:
 * - Header com título e descrição da funcionalidade
 * - Progress tracker visual com 3 etapas (Upload, Processamento, Resultado)
 * - Cards de workflow para cada etapa do processo
 * - Área de upload com drag & drop
 * - Área de processamento com indicadores visuais
 * - Área de resultados com download dos arquivos processados
 * 
 * FLUXO DE PROCESSAMENTO:
 * 1. Upload: Validação e seleção do arquivo Excel com 2 abas de percentagens
 * 2. Processamento: Envio para backend via API, execução de script Python
 * 3. Resultado: Geração de 2 arquivos processados (SEM/COM Desoneração) + metadados
 * 
 * DIFERENÇA PARA COMPOSIÇÕES E INSUMOS:
 * - Processa apenas 2 abas de percentagens (vs 5 abas das composições)
 * - Foco específico em percentagens de mão de obra para composições
 * - Estrutura de dados específica para percentuais
 * 
 * ESTADOS VISUAIS:
 * - card-current: Etapa atual ativa
 * - card-completed: Etapa concluída com sucesso
 * - card-processing: Etapa em processamento
 * 
 * INTEGRAÇÃO:
 * - API: /api/tabela-oficial/importar-sinapi/processar-percentagens-mao-de-obra
 * - Backend: ImportarSinapiController@processarPercentagensMaoDeObra
 * - Script Python: processa_percentagens_mao_de_obra.py
 -->
<template>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1 fw-bold" style="color: #18578A;">
                    <i class="fas fa-percentage me-2"></i>Importação do Valor(%) de Mão de Obra para Composições SINAPI
                </h5>
                <p class="text-muted mb-0 small">Processamento dos valores de percentagem de Mão de Obra em dois arquivos Excel</p>
            </div>
        </div>

        <div class="progress-tracker mb-3">
            <div class="progress-line">
                <div class="progress-fill" :style="{ width: progressoGeral + '%' }"></div>
            </div>
            <div class="progress-steps">
                <div class="progress-step" :class="{ 
                    'completed': arquivoValido, 
                    'current': !arquivoValido && !carregando && !dadosProcessados 
                }">
                    <div class="step-circle">
                        <i v-if="arquivoValido" class="fas fa-check"></i>
                        <span v-else>1</span>
                    </div>
                    <span class="step-label">Upload</span>
                </div>
                <div class="progress-step" :class="{ 
                    'completed': dadosProcessados, 
                    'current': arquivoValido && !carregando && !dadosProcessados,
                    'processing': carregando
                }">
                    <div class="step-circle">
                        <i v-if="dadosProcessados" class="fas fa-check"></i>
                        <i v-else-if="carregando" class="fas fa-spinner fa-spin"></i>
                        <span v-else>2</span>
                    </div>
                    <span class="step-label">Processamento</span>
                </div>
                <div class="progress-step" :class="{ 
                    'completed': dadosProcessados, 
                    'current': dadosProcessados 
                }">
                    <div class="step-circle">
                        <i v-if="dadosProcessados" class="fas fa-check"></i>
                        <span v-else>3</span>
                    </div>
                    <span class="step-label">Resultado</span>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="workflow-card" :class="{ 
                    'card-completed': arquivoValido, 
                    'card-current': !arquivoValido && !carregando && !dadosProcessados 
                }">
                    <div class="card-header-section">
                        <div class="step-indicator">
                            <div class="step-number" :class="{ 'completed': arquivoValido }">
                                <i v-if="arquivoValido" class="fas fa-check"></i>
                                <span v-else>1</span>
                            </div>
                        </div>
                        <div class="card-title">
                            <h6 class="mb-1 fw-bold">
                                <i class="fas fa-cloud-upload-alt me-2"></i>Upload do Arquivo
                            </h6>
                            <p class="mb-0 text-muted small">Selecione o Excel com as 2 abas (SEM Desoneração e COM Desoneração)</p>
                        </div>
                        <div class="status-badge" v-if="arquivoValido">
                            <i class="fas fa-check me-1"></i>Concluído
                        </div>
                    </div>
                    
                    <div class="card-content">
                        <div class="upload-zone" :class="{ 'has-file': arquivoValido }">
                            <input type="file" 
                                   class="file-input" 
                                   id="arquivo" 
                                   ref="arquivoInput"
                                   accept=".xlsx,.xls" 
                                   required
                                   @change="validarArquivo">
                            
                            <div v-if="!nomeArquivo" class="upload-placeholder">
                                <div class="upload-icon">
                                    <i class="fas fa-file-excel"></i>
                                </div>
                                <h6 class="mt-2 mb-1">Selecione um arquivo Excel</h6>
                                <p class="text-muted mb-0 small">Clique ou arraste o arquivo aqui</p>
                            </div>
                            
                            <div v-else class="file-info">
                                <div class="file-icon">
                                    <i class="fas fa-file-excel"></i>
                                </div>
                                <div class="file-details">
                                    <h6 class="mb-1">{{ nomeArquivo }}</h6>
                                    <p class="text-muted mb-0 small">{{ formatarTamanho(tamanhoArquivo) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="arquivoValido && !carregando && !dadosProcessados" class="mt-3">
                            <button class="btn btn-primary w-100" @click="processarArquivo">
                                <i class="fas fa-play me-2"></i>Processar Arquivo
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="workflow-card" :class="{ 
                    'card-completed': dadosProcessados, 
                    'card-current': arquivoValido && !carregando && !dadosProcessados,
                    'card-processing': carregando
                }">
                    <div class="card-header-section">
                        <div class="step-indicator">
                            <div class="step-number" :class="{ 'completed': dadosProcessados, 'processing': carregando }">
                                <i v-if="dadosProcessados" class="fas fa-check"></i>
                                <i v-else-if="carregando" class="fas fa-spinner fa-spin"></i>
                                <span v-else>2</span>
                            </div>
                        </div>
                        <div class="card-title">
                            <h6 class="mb-1 fw-bold">
                                <i class="fas fa-cogs me-2"></i>Processamento
                            </h6>
                            <p class="mb-0 text-muted small">Processando as 2 abas do arquivo Excel</p>
                        </div>
                        <div class="status-badge" v-if="carregando">
                            <i class="fas fa-spinner fa-spin me-1"></i>Processando
                        </div>
                        <div class="status-badge" v-else-if="dadosProcessados">
                            <i class="fas fa-check me-1"></i>Concluído
                        </div>
                    </div>
                    
                    <div class="card-content">
                        <div v-if="carregando" class="processing-info">
                            <div class="progress mb-3">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     :style="{ width: progressoProcessamento + '%' }"></div>
                            </div>
                            <p class="text-muted mb-0">{{ mensagemProcessamento }}</p>
                            
                            <div v-if="progressoProcessamento > 0 && progressoProcessamento < 100" class="time-estimate mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Pode levar alguns minutos para completar o processamento
                                </small>
                            </div>
                        </div>
                        
                        <div v-else-if="dadosProcessados" class="results-info">
                            <div class="alert alert-success">
                                <h6 class="mb-2">Processamento Concluído!</h6>
                                <ul class="list-unstyled mb-0 small">
                                    <li><strong>Arquivo processado:</strong> {{ nomeArquivo }}</li>
                                    <li><strong>Tempo de processamento:</strong> {{ tempoProcessamento }}</li>
                                    <li><strong>Status:</strong> Sucesso</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div v-else class="placeholder-content">
                            <div class="placeholder-icon">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <p class="text-muted mb-0">Aguardando upload do arquivo</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="workflow-card" :class="{ 
                    'card-completed': dadosProcessados, 
                    'card-current': dadosProcessados 
                }">
                    <div class="card-header-section">
                        <div class="step-indicator">
                            <div class="step-number" :class="{ 'completed': dadosProcessados }">
                                <i v-if="dadosProcessados" class="fas fa-check"></i>
                                <span v-else>3</span>
                            </div>
                        </div>
                        <div class="card-title">
                            <h6 class="mb-1 fw-bold">
                                <i class="fas fa-download me-2"></i>Download
                            </h6>
                            <p class="mb-0 text-muted small">Baixe os arquivos processados</p>
                        </div>
                        <div class="status-badge" v-if="dadosProcessados">
                            <i class="fas fa-check me-1"></i>Concluído
                        </div>
                    </div>
                    
                    <div class="card-content">
                        <div v-if="dadosProcessados" class="download-section">
                            <h6 class="mb-3">Arquivos Processados:</h6>
                            <div class="download-buttons">
                                <button class="btn btn-outline-primary" @click="downloadArquivo('SEM_DESONERACAO')">
                                    <i class="fas fa-download me-2"></i>SEM Desoneração
                                </button>
                                <button class="btn btn-outline-primary" @click="downloadArquivo('COM_DESONERACAO')">
                                    <i class="fas fa-download me-2"></i>COM Desoneração
                                </button>
                            </div>
                        </div>
                        
                        <div v-else class="placeholder-content">
                            <div class="placeholder-icon">
                                <i class="fas fa-download"></i>
                            </div>
                            <p class="text-muted mb-0">Aguardando processamento</p>
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
 * Script do Componente de Processamento de Percentagens de Mão de Obra SINAPI
 * 
 * RESPONSABILIDADES:
 * - Gerenciar upload e validação de arquivos Excel com percentagens
 * - Controlar o fluxo de processamento via API
 * - Manter estados visuais do progress tracker
 * - Gerenciar download dos arquivos processados
 * - Fornecer feedback visual durante o processamento
 * 
 * DADOS PRINCIPAIS:
 * - arquivo: Arquivo Excel selecionado pelo usuário
 * - arquivoValido: Flag indicando se arquivo foi validado
 * - carregando: Flag indicando processamento em andamento
 * - dadosProcessados: Flag indicando conclusão do processamento
 * - progressoProcessamento: Percentual de progresso (0-100)
 * - mensagemProcessamento: Mensagem de status atual
 * 
 * MÉTODOS PRINCIPAIS:
 * - validarArquivo(): Valida e armazena arquivo selecionado
 * - processarArquivo(): Envia arquivo para processamento via API
 * - downloadArquivo(tipo): Faz download do arquivo processado
 * - formatarTamanho(): Converte bytes em formato legível
 * 
 * INTEGRAÇÃO API:
 * - POST /api/tabela_oficial/importar_sinapi/percentagens_mao_de_obra
 * - GET /api/tabela_oficial/importar_sinapi/download_arquivo_processado_mao_de_obra/{tipo}
 */
import axios from 'axios';

export default {
    name: 'PercentagensMaoDeObra',
    data() {
        return {
            arquivo: null,
            nomeArquivo: '',
            tamanhoArquivo: 0,
            arquivoValido: false,
            carregando: false,
            dadosProcessados: false,
            progressoProcessamento: 0,
            mensagemProcessamento: '',
            tempoProcessamento: '',
            error: null
        }
    },
    computed: {
        progressoGeral() {
            if (this.dadosProcessados) return 100
            if (this.carregando) return 66
            if (this.arquivoValido) return 33
            return 0
        }
    },
    methods: {
        validarArquivo(event) {
            const file = event.target.files[0]
            if (!file) return

            this.arquivo = file
            this.nomeArquivo = file.name
            this.tamanhoArquivo = file.size
            this.arquivoValido = true
            this.error = null
        },

        async processarArquivo() {
            if (!this.arquivo) return

            this.carregando = true
            this.progressoProcessamento = 0
            this.mensagemProcessamento = 'Iniciando processamento...'
            this.error = null

            const formData = new FormData()
            formData.append('arquivo', this.arquivo)

            try {
                this.progressoProcessamento = 10
                this.mensagemProcessamento = 'Enviando arquivo...'

                const response = await axios.post('/api/tabela_oficial/importar_sinapi/percentagens_mao_de_obra', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })

                if (response.data.success) {
                    this.progressoProcessamento = 100
                    this.mensagemProcessamento = 'Processamento concluído!'
                    this.dadosProcessados = true
                    this.tempoProcessamento = response.data.tempo_processamento || 'N/A'
                } else {
                    throw new Error(response.data.message)
                }

            } catch (error) {
                this.error = 'Erro ao processar arquivo: ' + (error.response?.data?.message || error.message)
            } finally {
                this.carregando = false
            }
        },

        async downloadArquivo(tipo) {
            try {
                const response = await axios.get(`/api/tabela_oficial/importar_sinapi/download_arquivo_processado/${tipo}`, {
                    responseType: 'blob'
                })

                const url = window.URL.createObjectURL(new Blob([response.data]))
                const link = document.createElement('a')
                link.href = url
                link.setAttribute('download', `sinapi_mao_de_obra_${tipo.toLowerCase()}.xlsx`)
                document.body.appendChild(link)
                link.click()
                link.remove()
                window.URL.revokeObjectURL(url)

            } catch (error) {
                this.error = 'Erro ao baixar arquivo: ' + (error.response?.data?.message || error.message)
            }
        },

        formatarTamanho(bytes) {
            if (bytes === 0) return '0 Bytes'
            const k = 1024
            const sizes = ['Bytes', 'KB', 'MB', 'GB']
            const i = Math.floor(Math.log(bytes) / Math.log(k))
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
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
}

.file-details h6 {
    margin: 0;
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
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #134a6f 0%, #4a8a44 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.btn-outline-primary {
    border: 2px solid #18578A;
    color: #18578A;
    background: transparent;
}

.btn-outline-primary:hover {
    background: #18578A;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
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

.alert-success {
    background: #D1E7DD;
    border: none;
    color: #666;
    box-shadow: 0 2px 8px rgba(94, 168, 83, 0.2);
}

.time-estimate {
    background: rgba(24, 87, 138, 0.1);
    padding: 8px 12px;
    border-radius: 6px;
    border-left: 3px solid #18578A;
}

.processing-info {
    background: rgba(24, 87, 138, 0.05);
    border: 1px solid rgba(24, 87, 138, 0.2);
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.download-section {
    text-align: left;
}

.download-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .progress-steps {
        flex-direction: column;
        gap: 1rem;
    }
    
    .progress-line {
        display: none;
    }
    
    .workflow-card {
        margin-bottom: 1rem;
    }
    
    .card-header-section {
        flex-direction: column;
        align-items: flex-start;
        min-height: auto;
    }
    
    .step-indicator {
        position: static;
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