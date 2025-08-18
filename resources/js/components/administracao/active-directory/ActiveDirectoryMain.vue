<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho -->
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold" style="color: #5EA853;">
                    <i class="fas fa-sync-alt me-2"></i>Sincronização Active Directory
                </h5>
                <div class="d-flex gap-2">
                    <button 
                        class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 py-2" 
                        @click="testarConexao"
                        :disabled="testando"
                    >
                        <i class="fas fa-plug" :class="{ 'fa-spin': testando }"></i>
                        <span>{{ testando ? 'Testando...' : 'Testar Conexão' }}</span>
                    </button>
                    <button 
                        class="btn btn-outline-success d-flex align-items-center gap-2 px-3 py-2" 
                        @click="executarSincronizacao"
                        :disabled="sincronizando"
                    >
                        <i class="fas fa-sync-alt" :class="{ 'fa-spin': sincronizando }"></i>
                        <span>{{ sincronizando ? 'Sincronizando...' : 'Sincronizar AD' }}</span>
                    </button>
                </div>
        </div>

            <!-- Corpo -->
            <div class="card-body">
                <!-- Status da Conexão -->
        <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                            <div class="status-indicator me-3" :class="getStatusClass()"></div>
                            <div>
                                <div class="fw-semibold text-custom">{{ getStatusText() }}</div>
                                <small class="text-muted">{{ getStatusDescription() }}</small>
                    </div>
                </div>
            </div>
            
                    <div class="col-md-6">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded-3">
                                    <div class="h4 mb-1 text-primary fw-bold">{{ stats.total_users || 0 }}</div>
                                    <small class="text-muted fw-medium">Total de Usuários</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded-3">
                                    <div class="h4 mb-1 text-success fw-bold">{{ stats.ad_users || 0 }}</div>
                                    <small class="text-muted fw-medium">Usuários AD</small>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            
                <!-- Progresso da Sincronização -->
                <div v-if="sincronizando" class="mt-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="spinner-border spinner-border-sm me-2" style="color: #5EA853;" role="status">
                            <span class="visually-hidden">Carregando...</span>
                                </div>
                        <span class="fw-medium text-custom">{{ mensagemProgresso }}</span>
                            </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             :style="{ width: progresso + '%', backgroundColor: '#5EA853' }">
                    </div>
                </div>
            </div>
            
                <!-- Resultados da Sincronização -->
                <div v-if="resultadosSincronizacao && !sincronizando" class="mt-4">
                    <div class="alert border-0 rounded-3" :class="resultadosSincronizacao.erros && resultadosSincronizacao.erros.length > 0 ? 'alert-warning' : 'alert-success'">
                        <div class="d-flex align-items-start">
                            <i class="fas me-2 mt-1" :class="resultadosSincronizacao.erros && resultadosSincronizacao.erros.length > 0 ? 'fa-exclamation-triangle' : 'fa-check-circle'" style="color: #5EA853;"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fw-medium">{{ resultadosSincronizacao.erros && resultadosSincronizacao.erros.length > 0 ? 'Sincronização Concluída com Avisos' : 'Sincronização Concluída' }}</div>
                                    <button @click="limparResultados" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Limpar
                                    </button>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="h5 mb-1 text-primary fw-bold">{{ resultadosSincronizacao.usuarios_processados || 0 }}</div>
                                            <small class="text-muted">Processados</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="h5 mb-1 text-success fw-bold">{{ resultadosSincronizacao.usuarios_criados || 0 }}</div>
                                            <small class="text-muted">Criados</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="h5 mb-1 text-info fw-bold">{{ resultadosSincronizacao.usuarios_atualizados || 0 }}</div>
                                            <small class="text-muted">Atualizados</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="h5 mb-1 text-warning fw-bold">{{ resultadosSincronizacao.usuarios_desativados || 0 }}</div>
                                            <small class="text-muted">Desativados</small>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="resultadosSincronizacao.erros && resultadosSincronizacao.erros.length > 0" class="mt-3">
                                    <div class="alert alert-warning border-0 rounded-3">
                                        <div class="fw-medium mb-2">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Avisos ({{ resultadosSincronizacao.erros.length }})
                                        </div>
                                        <div class="small">
                                            <div v-for="(erro, index) in resultadosSincronizacao.erros" :key="index" class="mb-1">
                                                • {{ erro }}
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



                <!-- Configurações da Sincronização Automática -->
                <div class="mt-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                <h6 class="mb-0 fw-semibold text-custom">
                                        <i class="fas fa-cog me-2"></i>Configurações da Sincronização Automática
                </h6>
                                    <small class="text-muted">
                                        Última atualização: {{ ultimaAtualizacao || 'Nunca' }}
                                    </small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div v-if="configuracao.sync_enabled" class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Habilitada
                                    </div>
                                    <div v-else class="badge bg-secondary">
                                        <i class="fas fa-pause me-1"></i>Desabilitada
            </div>
                                    <button 
                                        @click="testarConfiguracao" 
                                        class="btn btn-sm btn-outline-info"
                                        :disabled="testandoConfig"
                                    >
                                        <i class="fas fa-vial me-1" :class="{ 'fa-spin': testandoConfig }"></i>
                                        {{ testandoConfig ? 'Testando...' : 'Testar' }}
                                    </button>
                                    <button 
                                        @click="salvarConfiguracao" 
                                        class="btn btn-sm btn-success"
                                        :disabled="salvando"
                                    >
                                        <i class="fas fa-save me-1" :class="{ 'fa-spin': salvando }"></i>
                                        {{ salvando ? 'Salvando...' : 'Salvar' }}
                                    </button>
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Frequência <span class="text-danger">*</span></label>
                                    <select v-model="configuracao.sync_frequency" class="form-select">
                                        <option value="daily">Diária</option>
                                        <option value="weekly">Semanal</option>
                                        <option value="monthly">Mensal</option>
                                    </select>
                    </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Horário <span class="text-danger">*</span></label>
                                    <input 
                                        v-model="configuracao.sync_time" 
                                        type="time" 
                                        class="form-control"
                                    >
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input 
                                            v-model="configuracao.sync_enabled" 
                                            class="form-check-input" 
                                            type="checkbox" 
                                            id="syncEnabled"
                                        >
                                        <label class="form-check-label" for="syncEnabled">
                                            {{ configuracao.sync_enabled ? 'Habilitada' : 'Desabilitada' }}
                                        </label>
                            </div>
                        </div>
                    </div>

                            <!-- Resumo da Configuração -->
                            <div class="mt-3 p-3 bg-white rounded-3 border">
                                <div class="fw-medium mb-2 text-custom">
                                    <i class="fas fa-info-circle me-2"></i>Resumo da Configuração
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <small class="text-muted">Próxima execução:</small>
                                        <div class="fw-medium">
                                            {{ getProximaExecucao() }}
                            </div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Frequência atual:</small>
                                        <div class="fw-medium">
                                            {{ getFrequenciaTexto() }} às {{ configuracao.sync_time }}h
                        </div>
                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// Reactive data
const sincronizando = ref(false)
const testando = ref(false)
const progresso = ref(0)
const mensagemProgresso = ref('')
const statusConexao = ref('not_tested')
const stats = ref({})
const resultadosSincronizacao = ref(null) // Adicionado para armazenar resultados
const configuracao = ref({
    sync_enabled: false,
    sync_frequency: 'daily',
    sync_time: '02:00'
})
const salvando = ref(false)
const ultimaAtualizacao = ref(null)
const testandoConfig = ref(false)

// Lifecycle
onMounted(() => {
    carregarStatus()
    carregarConfiguracao()
})

// Methods
const carregarStatus = async () => {
    try {
        const response = await axios.get('/api/administracao/active-directory/sync/status')
        const data = response.data
        
        // Verificar se a resposta tem a estrutura esperada
        if (data.success && data.data && data.data.estatisticas) {
            stats.value = data.data.estatisticas || {}
            statusConexao.value = data.data.estatisticas?.ad_habilitado ? 'enabled' : 'disabled'
        } else if (data.estatisticas) {
            stats.value = data.estatisticas || {}
            statusConexao.value = data.estatisticas?.ad_habilitado ? 'enabled' : 'disabled'
        } else {
            console.error('Estrutura de dados inesperada no status:', data)
            stats.value = {}
            statusConexao.value = 'error'
        }
        
        // Mapear campos para compatibilidade com o template
        if (stats.value.usuarios_total !== undefined) {
            stats.value.total_users = stats.value.usuarios_total
        }
        if (stats.value.usuarios_ad !== undefined) {
            stats.value.ad_users = stats.value.usuarios_ad
        }
        
    } catch (error) {
        console.error('Erro ao carregar status:', error)
        statusConexao.value = 'error'
    }
}

const executarSincronizacao = async () => {
    try {
        sincronizando.value = true
        progresso.value = 0
        mensagemProgresso.value = 'Iniciando sincronização...'
        
        // Simular progresso
        const progressInterval = setInterval(() => {
            if (progresso.value < 90) {
                progresso.value += Math.random() * 10
                mensagemProgresso.value = 'Sincronizando usuários do Active Directory...'
            }
        }, 500)
        
        const response = await axios.post('/api/administracao/active-directory/sync')
        
        // Armazenar resultados
        console.log('Resposta da API:', response.data)
        
        if (response.data && response.data.data && response.data.data.resultados) {
            console.log('Usando estrutura data.resultados')
            resultadosSincronizacao.value = response.data.data.resultados
        } else if (response.data && response.data.resultados) {
            console.log('Usando estrutura direta resultados')
            resultadosSincronizacao.value = response.data.resultados
        } else {
            console.error('Estrutura de dados inesperada:', response.data)
            // Criar estrutura padrão para evitar erro
            resultadosSincronizacao.value = {
                usuarios_processados: 0,
                usuarios_criados: 0,
                usuarios_atualizados: 0,
                usuarios_desativados: 0,
                erros: ['Estrutura de dados inesperada']
            }
        }
        
        console.log('Resultados finais:', resultadosSincronizacao.value)
        
        clearInterval(progressInterval)
        progresso.value = 100
        mensagemProgresso.value = 'Sincronização concluída com sucesso!'
        
        // Mostrar toast de sucesso
        if (window.showToast) {
            window.showToast('Sincronização executada com sucesso!', 'success')
        }
        
        // Atualizar estatísticas
        await carregarStatus()
        
        // Aguardar um pouco antes de esconder o progresso
        setTimeout(() => {
            sincronizando.value = false
            progresso.value = 0
            mensagemProgresso.value = ''
            // Removido: resultadosSincronizacao.value = null // Não limpar mais automaticamente
        }, 2000)
        
    } catch (error) {
        console.error('Erro na sincronização:', error)
        
        if (window.showToast) {
            window.showToast('Erro na sincronização: ' + (error.response?.data?.message || error.message), 'error')
        }
        
        sincronizando.value = false
        progresso.value = 0
        mensagemProgresso.value = ''
        // Removido: resultadosSincronizacao.value = null // Não limpar mais em caso de erro
    }
}

const testarConexao = async () => {
    try {
        testando.value = true
        
        const response = await axios.get('/api/administracao/active-directory/sync/test-connection')
        const data = response.data
        
        // Verificar se a resposta tem a estrutura esperada
        if (data.success && data.data && data.data.success) {
            if (window.showToast) {
                window.showToast('Conexão com AD estabelecida com sucesso!', 'success')
            }
            statusConexao.value = 'enabled'
        } else if (data.success) {
            if (window.showToast) {
                window.showToast('Conexão com AD estabelecida com sucesso!', 'success')
            }
            statusConexao.value = 'enabled'
        } else {
            const errorMessage = data.message || data.data?.message || 'Erro desconhecido na conexão'
            if (window.showToast) {
                window.showToast('Erro na conexão: ' + errorMessage, 'error')
            }
            statusConexao.value = 'error'
        }
        
    } catch (error) {
        console.error('Erro no teste de conexão:', error)
        
        if (window.showToast) {
            window.showToast('Erro no teste de conexão: ' + (error.response?.data?.message || error.message), 'error')
        }
        
        statusConexao.value = 'error'
    } finally {
        testando.value = false
    }
}

const limparResultados = () => {
    resultadosSincronizacao.value = null
    if (window.showToast) {
        window.showToast('Resultados da sincronização limpos.', 'info')
    }
}

const carregarConfiguracao = async () => {
    try {
        const response = await axios.get('/api/administracao/active-directory/config')
        let data = response.data
        
        // Verificar se a resposta tem a estrutura esperada
        if (data.success && data.data) {
            data = data.data
        }
        
        // Usar dados da resposta ou valores padrão
        configuracao.value = data || {
            sync_enabled: true,
            sync_frequency: 'daily',
            sync_time: '02:00'
        }
        
        ultimaAtualizacao.value = data?.updated_at ? new Date(data.updated_at).toLocaleString('pt-BR') : null
        
    } catch (error) {
        console.error('Erro ao carregar configuração:', error)
        // Usar valores padrão em caso de erro
        configuracao.value = {
            sync_enabled: true,
            sync_frequency: 'daily',
            sync_time: '02:00'
        }
        ultimaAtualizacao.value = null
    }
}

const salvarConfiguracao = async (showToast = true) => {
    salvando.value = true
    try {
        const response = await axios.post('/api/administracao/active-directory/config', configuracao.value)
        
        // Atualizar data de última modificação
        let responseData = response.data
        if (responseData.success && responseData.data) {
            responseData = responseData.data
        }
        
        if (responseData && responseData.updated_at) {
            ultimaAtualizacao.value = new Date(responseData.updated_at).toLocaleString('pt-BR')
        } else {
            ultimaAtualizacao.value = new Date().toLocaleString('pt-BR')
        }
        
        if (showToast && window.showToast) {
            window.showToast('Configurações salvas com sucesso!', 'success')
        }
        
        return true
    } catch (error) {
        console.error('Erro ao salvar configuração:', error)
        if (showToast && window.showToast) {
            window.showToast('Erro ao salvar configuração: ' + (error.response?.data?.message || error.message), 'error')
        }
        return false
    } finally {
        salvando.value = false
    }
}

const testarConfiguracao = async () => {
    testandoConfig.value = true
    try {
        // Primeiro, salvar as configurações atuais (sem toast)
        const saved = await salvarConfiguracao(false)
        if (!saved) {
            alert('❌ Erro ao salvar configurações antes do teste')
            return
        }
        
        // Depois testar
        const response = await axios.get('/api/administracao/active-directory/config/test')
        let data = response.data
        
        // Verificar se a resposta tem a estrutura esperada
        if (data.success && data.data) {
            data = data.data
        }
        
        if (data.tests) {
            const tests = data.tests
            const status = data.status || 'info'
            const message = data.message || 'Teste concluído'
            
            let alertMessage = ''
            
            // Cabeçalho baseado no status
            if (status === 'success') {
                alertMessage += '✅ ' + message + '\n\n'
            } else if (status === 'warning') {
                alertMessage += '⚠️ ' + message + '\n\n'
            } else {
                alertMessage += 'ℹ️ ' + message + '\n\n'
            }
            
            // Próxima execução
            if (tests.next_execution) {
                alertMessage += `📅 Próxima execução: ${tests.next_execution}\n\n`
            }
            
            // Detalhes dos testes
            alertMessage += '🔍 Detalhes dos testes:\n'
            
            if (tests.frequency_exists) alertMessage += '✅ Frequência salva\n'
            else alertMessage += '❌ Frequência não encontrada\n'
            
            if (tests.time_exists) alertMessage += '✅ Horário salvo\n'
            else alertMessage += '❌ Horário não encontrado\n'
            
            if (tests.enabled_exists) alertMessage += '✅ Status salvo\n'
            else alertMessage += '❌ Status não encontrado\n'
            
            if (tests.frequency_valid) alertMessage += '✅ Frequência válida\n'
            else alertMessage += '❌ Frequência inválida\n'
            
            if (tests.time_valid) alertMessage += '✅ Formato de horário válido\n'
            else alertMessage += '❌ Formato de horário inválido\n'
            
            if (tests.enabled_valid) alertMessage += '✅ Status válido\n'
            else alertMessage += '❌ Status inválido\n'
            
            // Status geral
            if (tests.overall_valid) {
                alertMessage += '\n🎉 Todas as configurações estão válidas!'
            } else {
                alertMessage += '\n⚠️ Algumas configurações precisam ser ajustadas.'
            }
            
            alert(alertMessage)
        }
        
    } catch (error) {
        console.error('Erro ao testar configuração:', error)
        alert('❌ Erro ao testar configurações: ' + (error.response?.data?.message || error.message))
    } finally {
        testandoConfig.value = false
    }
}

// Helpers
const getStatusClass = () => {
    const classes = {
        'enabled': 'status-success',
        'disabled': 'status-warning',
        'error': 'status-error',
        'not_tested': 'status-neutral'
    }
    return classes[statusConexao.value] || 'status-neutral'
}

const getStatusText = () => {
    const texts = {
        'enabled': 'Conectado',
        'disabled': 'Desabilitado',
        'error': 'Erro de Conexão',
        'not_tested': 'Não testado'
    }
    return texts[statusConexao.value] || 'Desconhecido'
}

const getStatusDescription = () => {
    const descriptions = {
        'enabled': 'Conexão com AD estabelecida',
        'disabled': 'AD não está habilitado',
        'error': 'Falha na conexão com AD',
        'not_tested': 'Conexão ainda não foi testada'
    }
    return descriptions[statusConexao.value] || 'Status desconhecido'
}

const getFrequenciaTexto = () => {
    const frequencias = {
        'daily': 'Diariamente',
        'weekly': 'Semanalmente',
        'monthly': 'Mensalmente'
    }
    return frequencias[configuracao.value.sync_frequency] || 'Diariamente'
}

const getProximaExecucao = () => {
    if (!configuracao.value.sync_enabled) {
        return 'Desabilitada'
    }
    
    const agora = new Date()
    const [horas, minutos] = configuracao.value.sync_time.split(':')
    const horaExecucao = parseInt(horas)
    const minutoExecucao = parseInt(minutos)
    
    let proximaExecucao = new Date()
    proximaExecucao.setHours(horaExecucao, minutoExecucao, 0, 0)
    
    // Se já passou do horário hoje, calcular para amanhã
    if (proximaExecucao <= agora) {
        proximaExecucao.setDate(proximaExecucao.getDate() + 1)
    }
    
    // Ajustar para frequência semanal/mensal
    if (configuracao.value.sync_frequency === 'weekly') {
        // Próximo domingo
        const diasParaDomingo = (7 - proximaExecucao.getDay()) % 7
        proximaExecucao.setDate(proximaExecucao.getDate() + diasParaDomingo)
    } else if (configuracao.value.sync_frequency === 'monthly') {
        // Próximo mês
        proximaExecucao.setMonth(proximaExecucao.getMonth() + 1)
    }
    
    return proximaExecucao.toLocaleDateString('pt-BR') + ' às ' + configuracao.value.sync_time + 'h'
}
</script>

<style scoped>
.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.status-success {
    background-color: #28a745;
}

.status-warning {
    background-color: #ffc107;
}

.status-error {
    background-color: #dc3545;
}

.status-neutral {
    background-color: #6c757d;
}

.text-custom {
    color: #18578A !important;
}

.progress {
    border-radius: 4px;
}

.progress-bar {
    border-radius: 4px;
}
</style>
