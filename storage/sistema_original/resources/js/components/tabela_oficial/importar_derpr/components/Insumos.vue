<template>
    <div>
        <!-- Cabeçalho Elegante -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1 fw-bold text-custom">
                    <i class="fas fa-file-pdf me-2"></i>Conversão para Excel da Tabela de Serviços e Insumos do DER-PR (Analítico)
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
                            <p class="mb-0 text-muted small">Selecione o PDF da tabela analítica</p>
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
                                    <p class="text-success mb-0 small">
                                        <i class="fas fa-check-circle me-1"></i>Arquivo carregado com sucesso!
                                    </p>
                                </div>
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
                    'card-processing': carregando,
                    'card-disabled': !arquivoValido
                }">
                    <div class="card-header-section">
                        <div class="step-indicator">
                            <div class="step-number" :class="{ 
                                'completed': dadosProcessados,
                                'processing': carregando,
                                'disabled': !arquivoValido
                            }">
                                <i v-if="dadosProcessados" class="fas fa-check"></i>
                                <i v-else-if="carregando" class="fas fa-spinner fa-spin"></i>
                                <span v-else>2</span>
                        </div>
                        </div>
                        <div class="card-title">
                            <h6 class="mb-1 fw-bold" :class="{ 'text-muted': !arquivoValido }">
                                <i class="fas fa-cogs me-2" :class="{ 'fa-spin': carregando }"></i>Processamento
                            </h6>
                            <p class="mb-0 text-muted small">Inicie o processamento da tabela</p>
                        </div>
                        <div class="status-badge" v-if="carregando">
                            <i class="fas fa-spinner fa-spin me-1"></i>Processando
                    </div>
                        <div class="status-badge ready" v-else-if="arquivoValido && !dadosProcessados">
                            <i class="fas fa-play me-1"></i>Pronto
                                    </div>
                        <div class="status-badge disabled" v-else>
                            <i class="fas fa-lock me-1"></i>Bloqueado
                                </div>
                            </div>
                    
                    <div class="card-content">
                        <div class="processing-section">
                            <div class="processing-info">
                                <p class="mb-2 small" :class="{ 'text-muted': !arquivoValido }">Inicie o processamento da tabela analítica. Este processo pode levar alguns minutos.</p>
                        </div>
                            <button class="btn btn-primary btn-lg w-100 fw-semibold" 
                                @click="processarArquivo"
                                :disabled="!arquivoValido || carregando"
                                    :class="{ 'btn-disabled': !arquivoValido }">
                                <span v-if="carregando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                <i v-else class="fas fa-play me-2"></i>
                                {{ carregando ? 'Processando...' : 'Iniciar Processamento' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Resultado -->
            <div class="col-md-4">
                <div class="workflow-card" :class="{ 
                    'card-completed': dadosProcessados, 
                    'card-current': dadosProcessados,
                    'card-disabled': !dadosProcessados
                }">
                    <div class="card-header-section">
                        <div class="step-indicator">
                            <div class="step-number" :class="{ 
                                'completed': dadosProcessados,
                                'disabled': !dadosProcessados
                            }">
                                <i v-if="dadosProcessados" class="fas fa-check"></i>
                                <span v-else>3</span>
                        </div>
                        </div>
                        <div class="card-title">
                            <h6 class="mb-1 fw-bold" :class="{ 'text-muted': !dadosProcessados }">
                                <i class="fas fa-check-circle me-2"></i>Resultado
                            </h6>
                            <p class="mb-0 text-muted small">Visualize e exporte os dados</p>
                        </div>
                        <div class="status-badge" v-if="dadosProcessados">
                            <i class="fas fa-check me-1"></i>Disponível
                    </div>
                        <div class="status-badge disabled" v-else>
                            <i class="fas fa-lock me-1"></i>Bloqueado
                            </div>
                        </div>
                    
                    <div class="card-content">
                        <div v-if="dadosProcessados" class="result-section">
                            <div class="success-message">
                                <div class="success-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h6 class="mt-2 mb-1">Processamento Concluído!</h6>
                                <p class="text-muted mb-3 small">{{ totalRegistros }} registros em 6 categorias</p>
                            </div>
                            <div class="action-buttons">
                                <button class="btn btn-success w-100" @click="exportarTodos">
                                    <i class="fas fa-file-archive me-2"></i>Exportar Todos (6 arquivos)
                                </button>
                            </div>
                        </div>
                        <div v-else class="waiting-section">
                            <div class="waiting-icon disabled">
                                <i class="fas fa-lock"></i>
                    </div>
                            <h6 class="mt-2 mb-1 text-muted">Aguardando Processamento</h6>
                            <p class="text-muted mb-0 small">Complete os passos anteriores</p>
                </div>
            </div>
        </div>
                    </div>
                        </div>
                        
        <!-- Sistema de Progresso Detalhado -->
        <div v-if="carregando" class="processing-details mb-3">
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
             class="mb-3">
            <div class="d-flex align-items-center">
                <i :class="['fas', 'me-2', mensagem.tipo === 'erro' ? 'fa-exclamation-triangle' : 'fa-check-circle']"></i>
                <span class="small">{{ mensagem.texto }}</span>
                                </div>
                            </div>

        <!-- Modal Resumo -->
        <div class="modal fade" id="modalResumo" tabindex="-1" aria-labelledby="modalResumoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-white">
                        <h5 class="modal-title fw-semibold text-custom" id="modalResumoLabel">
                            <i class="fas fa-chart-bar me-2"></i>Resumo dos Dados Processados
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6" v-for="(tab, index) in tabs" :key="index">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="mb-2">
                                            <i :class="getCategoriaIcon(index)" style="font-size: 2rem; color: #18578A;"></i>
                                </div>
                                        <h6 class="card-title mb-1">{{ tab.titulo }}</h6>
                                        <p class="text-muted mb-0">{{ tab.contagem }} registros</p>
                                </div>
                            </div>
                                    </div>
                                </div>
                                </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-success fw-semibold" @click="exportarTodos">
                            <i class="fas fa-file-archive me-1"></i>Exportar Todos (6 arquivos)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import * as XLSX from 'xlsx'
import { Modal } from 'bootstrap'

export default {
    name: 'Insumos',
    data() {
        return {
            arquivoValido: false,
            arquivoSelecionado: null,
            dados: {
                equipamentos: [],
                mao_de_obra: [],
                itens_incidencia: [],
                materiais: [],
                servicos: [],
                transportes: []
            },
            dadosProcessados: false,
            carregando: false,
            progresso: 0,
            progressoEtapa: '',
            nomeArquivo: '',
            mensagem: null,
            horaInicio: '',
            tempoDecorrido: '',
            intervaloTempo: null,
            tabs: [
                { titulo: 'Equipamentos', contagem: 0 },
                { titulo: 'Mão de Obra', contagem: 0 },
                { titulo: 'Itens de Incidência', contagem: 0 },
                { titulo: 'Materiais', contagem: 0 },
                { titulo: 'Serviços', contagem: 0 },
                { titulo: 'Transportes', contagem: 0 }
            ],
            etapas: [
                { nome: 'Upload do arquivo PDF', completa: false, ativa: false },
                { nome: 'Validação do arquivo', completa: false, ativa: false },
                { nome: 'Executando script Python', completa: false, ativa: false },
                { nome: 'Processando dados extraídos', completa: false, ativa: false },
                { nome: 'Estruturando informações', completa: false, ativa: false },
                { nome: 'Dados prontos para visualização', completa: false, ativa: false }
            ]
        }
    },
    computed: {
        progressoGeral() {
            if (this.dadosProcessados) return 100
            if (this.carregando) return 66
            if (this.arquivoValido) return 33
            return 0
        },
        totalRegistros() {
            return (
                (this.dados.equipamentos?.length || 0) +
                (this.dados.mao_de_obra?.length || 0) +
                (this.dados.itens_incidencia?.length || 0) +
                (this.dados.materiais?.length || 0) +
                (this.dados.servicos?.length || 0) +
                (this.dados.transportes?.length || 0)
            )
        }
    },
    methods: {
        validarArquivo(event) {
            const arquivo = event.target.files[0]
            this.arquivoSelecionado = arquivo
            this.arquivoValido = arquivo && arquivo.type === 'application/pdf'
            this.nomeArquivo = arquivo ? arquivo.name : ''
            this.dadosProcessados = false
            
            if (!this.arquivoValido) {
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Por favor, selecione um arquivo PDF válido.'
                }
                this.dados = {
                    equipamentos: [],
                    mao_de_obra: [],
                    itens_incidencia: [],
                    materiais: [],
                    servicos: [],
                    transportes: []
                }
                this.tabs.forEach(tab => tab.contagem = 0)
            } else {
                this.mensagem = null
            }
        },

        iniciarProgresso() {
            this.progresso = 0
            this.horaInicio = new Date().toLocaleTimeString('pt-BR')
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
        
        async processarArquivo() {
            if (!this.arquivoValido || !this.arquivoSelecionado) {
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Por favor, selecione um arquivo PDF válido'
                }
                return
            }

            this.carregando = true
            this.dadosProcessados = false
            this.iniciarProgresso()
            const formData = new FormData()
            formData.append('arquivo', this.arquivoSelecionado)

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
                
                // Fazer a requisição real
                const response = await axios.post('/tabela_oficial/importar_derpr/insumos', formData)

                // Etapa 5: Finalização (95-100%)
                this.atualizarEtapa(5)
                this.progressoEtapa = 'Dados processados com sucesso!'
                this.progresso = 100
                await this.delay(500)
                
                this.dados = response.data

                this.tabs[0].contagem = this.dados.equipamentos?.length || 0
                this.tabs[1].contagem = this.dados.mao_de_obra?.length || 0
                this.tabs[2].contagem = this.dados.itens_incidencia?.length || 0
                this.tabs[3].contagem = this.dados.materiais?.length || 0
                this.tabs[4].contagem = this.dados.servicos?.length || 0
                this.tabs[5].contagem = this.dados.transportes?.length || 0

                this.dadosProcessados = true
                this.mensagem = {
                    tipo: 'sucesso',
                    texto: `PDF processado com sucesso! ${this.totalRegistros} registros extraídos em 6 categorias.`
                }
                
                // Marcar todas as etapas como completas
                this.etapas.forEach(etapa => {
                    etapa.completa = true
                    etapa.ativa = false
                })

            } catch (error) {
                this.mensagem = {
                    tipo: 'erro',
                    texto: error.response?.data?.message || 'Erro ao processar arquivo PDF.'
                }
            } finally {
                this.carregando = false
                if (this.intervaloTempo) {
                    clearInterval(this.intervaloTempo)
                }
            }
        },

        delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms))
        },

        getCategoriaIcon(index) {
            const icons = [
                'fas fa-cogs',
                'fas fa-users', 
                'fas fa-percentage',
                'fas fa-box',
                'fas fa-tools',
                'fas fa-truck'
            ]
            return icons[index] || 'fas fa-chart-bar'
        },

        async exportarTodos() {
            try {
                const tabelas = [
                    { dados: this.dados.equipamentos, nome: 'equipamentos' },
                    { dados: this.dados.mao_de_obra, nome: 'mao_de_obra' },
                    { dados: this.dados.itens_incidencia, nome: 'itens_incidencia' },
                    { dados: this.dados.materiais, nome: 'materiais' },
                    { dados: this.dados.servicos, nome: 'servicos' },
                    { dados: this.dados.transportes, nome: 'transportes' }
                ]

                let arquivosExportados = 0

                for (const tabela of tabelas) {
                    if (tabela.dados && tabela.dados.length > 0) {
                        this.exportarParaExcel(tabela.dados, tabela.nome)
                        arquivosExportados++
                    }
                }

                if (arquivosExportados > 0) {
                    this.mensagem = {
                        tipo: 'sucesso',
                        texto: `${arquivosExportados} arquivos Excel exportados com sucesso!`
                    }
                } else {
                    this.mensagem = {
                        tipo: 'erro',
                        texto: 'Nenhum dado encontrado para exportar.'
                    }
                }

            } catch (error) {
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Erro ao exportar arquivos. Tente novamente.'
                }
            }
        },

        exportarParaExcel(dados, nomeArquivo) {
            const ws = XLSX.utils.json_to_sheet(dados)
            const colWidths = Object.keys(dados[0]).map(key => ({
                wch: Math.max(key.length, 15)
            }))
            ws['!cols'] = colWidths

            const wb = XLSX.utils.book_new()
            XLSX.utils.book_append_sheet(wb, ws, 'Dados')
            XLSX.writeFile(wb, `${nomeArquivo}.xlsx`)
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
    top: 35px;
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