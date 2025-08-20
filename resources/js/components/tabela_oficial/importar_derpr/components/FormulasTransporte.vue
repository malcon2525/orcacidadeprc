<template>
    <div>
        <!-- Cabeçalho Elegante -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1 fw-bold text-custom">
                    <i class="fas fa-truck me-2"></i>Conversão para Excel da Tabela de Fórmulas de Transporte do DER-PR
                </h5>
                <p class="text-muted mb-0 small">Processe arquivos PDF e converta para formato Excel estruturado</p>
            </div>
        </div>

        <!-- Barra de Progresso Horizontal -->
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

        <!-- Sistema de Cards Impactante -->
        <div class="row g-3 mb-3">
            <!-- Card 1: Upload -->
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
                            <p class="mb-0 text-muted small">Selecione o PDF da tabela de fórmulas</p>
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
                                   accept=".pdf" 
                                   required
                                   @change="validarArquivo">
                            
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
                                    <p class="text-muted mb-0 small">{{ tamanhoArquivo }}</p>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" @click="removerArquivo">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Processamento -->
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
                            <p class="mb-0 text-muted small">Conversão PDF para Excel</p>
                        </div>
                        <div class="status-badge" v-if="dadosProcessados">
                            <i class="fas fa-check me-1"></i>Concluído
                        </div>
                        <div class="status-badge processing" v-else-if="carregando">
                            <i class="fas fa-spinner fa-spin me-1"></i>Processando
                        </div>
                    </div>
                    
                    <div class="card-content">
                        <div v-if="!arquivoValido" class="step-disabled">
                            <i class="fas fa-lock text-muted"></i>
                            <p class="text-muted mb-0 small">Complete o upload primeiro</p>
                        </div>
                        
                        <div v-else-if="carregando" class="processing-status">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Processando...</span>
                            </div>
                            <p class="mt-2 mb-0 text-primary">Processando arquivo...</p>
                        </div>
                        
                        <div v-else-if="dadosProcessados" class="step-completed">
                            <i class="fas fa-check-circle text-success"></i>
                            <p class="text-success mb-0">Arquivo processado com sucesso!</p>
                        </div>
                        
                        <div v-else class="step-ready">
                            <button class="btn btn-primary w-100" @click="processarArquivo">
                                <i class="fas fa-play me-2"></i>Iniciar Processamento
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Resultado -->
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
                                <i class="fas fa-file-excel me-2"></i>Resultado
                            </h6>
                            <p class="mb-0 text-muted small">Arquivo Excel gerado</p>
                        </div>
                        <div class="status-badge" v-if="dadosProcessados">
                            <i class="fas fa-check me-1"></i>Concluído
                        </div>
                    </div>
                    
                    <div class="card-content">
                        <div v-if="!dadosProcessados" class="step-disabled">
                            <i class="fas fa-lock text-muted"></i>
                            <p class="text-muted mb-0 small">Aguarde o processamento</p>
                        </div>
                        
                        <div v-else class="step-completed">
                            <div class="result-info">
                                <i class="fas fa-file-excel text-success mb-2"></i>
                                <h6 class="text-success mb-1">Arquivo Excel Gerado!</h6>
                                <p class="text-muted mb-2 small">derpr_formulas_transporte.xlsx</p>
                                <div class="result-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Status:</span>
                                        <span class="stat-value text-success">Sucesso</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Tempo:</span>
                                        <span class="stat-value">{{ tempoProcessamento }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Área de Resultados -->
        <div v-if="dadosProcessados" class="results-section">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Processamento Concluído com Sucesso!
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-file-excel me-2"></i>Arquivo Excel Gerado
                            </h6>
                            <ul class="list-unstyled">
                                <li><strong>Nome:</strong> derpr_formulas_transporte.xlsx</li>
                                <li><strong>Localização:</strong> Diretório temp do sistema</li>
                                <li><strong>Status:</strong> <span class="badge bg-success">Pronto para uso</span></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>Próximos Passos
                            </h6>
                            <div class="alert alert-info">
                                <i class="fas fa-arrow-right me-2"></i>
                                <strong>Proceda para a Aba 4</strong> para gravar os dados no banco de dados.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Área de Erro -->
        <div v-if="erro" class="error-section mt-3">
            <div class="alert alert-danger">
                <h6 class="alert-heading">
                    <i class="fas fa-exclamation-triangle me-2"></i>Erro no Processamento
                </h6>
                <p class="mb-0">{{ erro }}</p>
                <hr>
                <button class="btn btn-outline-danger btn-sm" @click="limparErro">
                    <i class="fas fa-times me-2"></i>Fechar
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'FormulasTransporte',
    
    data() {
        return {
            arquivoValido: false,
            carregando: false,
            dadosProcessados: false,
            nomeArquivo: '',
            tamanhoArquivo: '',
            arquivo: null,
            erro: null,
            tempoProcessamento: '0s'
        }
    },

    computed: {
        progressoGeral() {
            if (this.dadosProcessados) return 100;
            if (this.carregando) return 66;
            if (this.arquivoValido) return 33;
            return 0;
        }
    },

    methods: {
        validarArquivo(event) {
            const arquivo = event.target.files[0];
            
            if (!arquivo) {
                this.arquivoValido = false;
                this.nomeArquivo = '';
                this.tamanhoArquivo = '';
                this.arquivo = null;
                return;
            }

            // Validar extensão
            if (arquivo.type !== 'application/pdf') {
                this.mostrarErro('Por favor, selecione um arquivo PDF válido.');
                this.limparArquivo();
                return;
            }

            // Validar tamanho (50MB)
            const tamanhoMaximo = 50 * 1024 * 1024; // 50MB em bytes
            if (arquivo.size > tamanhoMaximo) {
                this.mostrarErro('O arquivo é muito grande. Tamanho máximo: 50MB.');
                this.limparArquivo();
                return;
            }

            // Arquivo válido
            this.arquivo = arquivo;
            this.nomeArquivo = arquivo.name;
            this.tamanhoArquivo = this.formatarTamanho(arquivo.size);
            this.arquivoValido = true;
            this.erro = null;
        },

        removerArquivo() {
            this.limparArquivo();
            if (this.$refs.arquivoInput) {
                this.$refs.arquivoInput.value = '';
            }
        },

        limparArquivo() {
            this.arquivo = null;
            this.nomeArquivo = '';
            this.tamanhoArquivo = '';
            this.arquivoValido = false;
            this.dadosProcessados = false;
        },

        async processarArquivo() {
            if (!this.arquivo) {
                this.mostrarErro('Nenhum arquivo selecionado.');
                return;
            }

            this.carregando = true;
            this.erro = null;
            const tempoInicio = Date.now();

            try {
                const formData = new FormData();
                formData.append('arquivo', this.arquivo);

                const response = await fetch('/tabela_oficial/importar_derpr/formulas-transporte', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const resultado = await response.json();

                if (response.ok && resultado.success) {
                    this.dadosProcessados = true;
                    this.tempoProcessamento = this.calcularTempoProcessamento(tempoInicio);
                } else {
                    throw new Error(resultado.message || 'Erro desconhecido no processamento');
                }

            } catch (error) {
                this.mostrarErro('Erro ao processar arquivo: ' + error.message);
            } finally {
                this.carregando = false;
            }
        },

        formatarTamanho(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        calcularTempoProcessamento(tempoInicio) {
            const tempoTotal = Date.now() - tempoInicio;
            const segundos = Math.floor(tempoTotal / 1000);
            return `${segundos}s`;
        },

        mostrarErro(mensagem) {
            this.erro = mensagem;
        },

        limparErro() {
            this.erro = null;
        }
    }
}
</script>

<style scoped>
/* Estilos específicos para este componente seguindo o padrão das abas existentes */
.workflow-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
}

.workflow-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.card-header-section {
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.step-indicator {
    flex-shrink: 0;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.step-number.completed {
    background: #28a745;
    color: white;
}

.step-number.processing {
    background: #007bff;
    color: white;
}

.card-title {
    flex: 1;
}

.card-title h6 {
    color: #495057;
    margin-bottom: 0.25rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    background: #d4edda;
    color: #155724;
}

.status-badge.processing {
    background: #cce7ff;
    color: #004085;
}

.card-content {
    padding: 1.5rem;
}

.upload-zone {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
}

.upload-zone:hover {
    border-color: #007bff;
    background: #f8f9fa;
}

.upload-zone.has-file {
    border-color: #28a745;
    background: #f8fff9;
}

.file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.upload-placeholder {
    pointer-events: none;
}

.upload-icon {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.file-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    pointer-events: none;
}

.file-icon {
    font-size: 2rem;
    color: #28a745;
}

.file-details {
    flex: 1;
    text-align: left;
}

.step-disabled {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
}

.step-disabled i {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.processing-status {
    text-align: center;
    padding: 2rem;
}

.step-completed {
    text-align: center;
    padding: 2rem;
    color: #28a745;
}

.step-completed i {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.step-ready {
    text-align: center;
    padding: 2rem;
}

.result-info {
    text-align: center;
}

.result-info i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.result-stats {
    margin-top: 1rem;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 4px;
}

.stat-label {
    font-weight: 500;
    color: #6c757d;
}

.stat-value {
    font-weight: bold;
}

.progress-tracker {
    position: relative;
}

.progress-line {
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    margin-bottom: 1rem;
    position: relative;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #007bff, #28a745);
    border-radius: 2px;
    transition: width 0.5s ease;
}

.progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.step-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.progress-step.completed .step-circle {
    background: #28a745;
    color: white;
}

.progress-step.current .step-circle {
    background: #007bff;
    color: white;
}

.progress-step.processing .step-circle {
    background: #ffc107;
    color: #212529;
}

.step-label {
    font-size: 0.8rem;
    font-weight: 500;
    color: #6c757d;
    text-align: center;
}

.progress-step.completed .step-label,
.progress-step.current .step-label {
    color: #495057;
    font-weight: 600;
}

.results-section {
    margin-top: 2rem;
}

.error-section {
    margin-top: 1rem;
}

.text-custom {
    color: #007bff;
}
</style>

