<template>
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-semibold text-custom">
                <i class="fas fa-cog me-2"></i>Configuração Active Directory
            </h6>
        </div>
        <div class="card-body p-4">
            <!-- Status da Conexão -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-semibold mb-1">Status da Conexão</h6>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-circle me-2" :class="statusConexao.class"></i>
                                        <span class="fw-bold" :class="statusConexao.class">{{ statusConexao.texto }}</span>
                                    </div>
                                </div>
                                <button @click="testarConexao" :disabled="testando" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plug me-1" :class="{ 'fa-spin': testando }"></i>
                                    {{ testando ? 'Testando...' : 'Testar Conexão' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulário de Configuração -->
            <form @submit.prevent="salvarConfiguracao">
                <div class="row g-3">
                    <!-- Host e Porta -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Servidor LDAP</label>
                        <input 
                            v-model="configuracao.host" 
                            type="text" 
                            class="form-control"
                            placeholder="ex: 10.51.10.46"
                            required
                        >
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Porta</label>
                        <input 
                            v-model="configuracao.port" 
                            type="number" 
                            class="form-control"
                            placeholder="389"
                            required
                        >
                    </div>

                    <!-- Base DN -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Base DN</label>
                        <input 
                            v-model="configuracao.base_dn" 
                            type="text" 
                            class="form-control"
                            placeholder="ex: OU=Empregados,DC=prcidade,DC=br"
                            required
                        >
                    </div>

                    <!-- Credenciais -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Usuário LDAP</label>
                        <input 
                            v-model="configuracao.username" 
                            type="text" 
                            class="form-control"
                            placeholder="ex: desen@paranacidade.org.br"
                            required
                        >
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Senha LDAP</label>
                        <div class="input-group">
                            <input 
                                v-model="configuracao.password" 
                                :type="mostrarSenha ? 'text' : 'password'" 
                                class="form-control"
                                placeholder="Senha do usuário LDAP"
                                required
                            >
                            <button 
                                @click="mostrarSenha = !mostrarSenha" 
                                type="button" 
                                class="btn btn-outline-secondary"
                            >
                                <i class="fas" :class="mostrarSenha ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Configurações de Segurança -->
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="fw-semibold mb-3">Configurações de Segurança</h6>
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input 
                                                v-model="configuracao.use_tls" 
                                                class="form-check-input" 
                                                type="checkbox"
                                                id="useTls"
                                            >
                                            <label class="form-check-label" for="useTls">
                                                Usar TLS
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input 
                                                v-model="configuracao.use_ssl" 
                                                class="form-check-input" 
                                                type="checkbox"
                                                id="useSsl"
                                            >
                                            <label class="form-check-label" for="useSsl">
                                                Usar SSL
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input 
                                                v-model="configuracao.logging" 
                                                class="form-check-input" 
                                                type="checkbox"
                                                id="logging"
                                            >
                                            <label class="form-check-label" for="logging">
                                                Logging Detalhado
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeout -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Timeout (segundos)</label>
                        <input 
                            v-model="configuracao.timeout" 
                            type="number" 
                            class="form-control"
                            placeholder="5"
                            min="1"
                            max="60"
                        >
                    </div>

                    <!-- Frequência de Sincronização -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Frequência de Sincronização</label>
                        <select v-model="configuracao.sync_frequency" class="form-select">
                            <option value="disabled">Desabilitada</option>
                            <option value="hourly">A cada hora</option>
                            <option value="daily">Diária</option>
                            <option value="weekly">Semanal</option>
                        </select>
                    </div>
                </div>

                <!-- Botões -->
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" @click="resetarFormulario" class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-2"></i>Resetar
                    </button>
                    <button type="submit" :disabled="salvando" class="btn btn-primary">
                        <i class="fas fa-save me-2" :class="{ 'fa-spin': salvando }"></i>
                        {{ salvando ? 'Salvando...' : 'Salvar Configuração' }}
                    </button>
                </div>
            </form>

            <!-- Informações Adicionais -->
            <div class="mt-4">
                <div class="alert alert-info border-0">
                    <h6 class="fw-semibold">
                        <i class="fas fa-info-circle me-2"></i>Informações Importantes
                    </h6>
                    <ul class="mb-0 mt-2">
                        <li>Certifique-se de que o servidor LDAP está acessível na rede</li>
                        <li>O usuário LDAP deve ter permissões para consultar o diretório</li>
                        <li>Recomenda-se usar TLS para conexões seguras</li>
                        <li>A sincronização automática pode ser desabilitada se necessário</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

// Estados
const salvando = ref(false)
const testando = ref(false)
const mostrarSenha = ref(false)

// Status da conexão
const statusConexao = ref({
    texto: 'Não testado',
    class: 'text-muted'
})

// Configuração
const configuracao = ref({
    host: '',
    port: 389,
    base_dn: '',
    username: '',
    password: '',
    use_tls: false,
    use_ssl: false,
    timeout: 5,
    logging: false,
    sync_frequency: 'daily'
})

// Lifecycle
onMounted(() => {
    carregarConfiguracao()
})

// Methods
const carregarConfiguracao = async () => {
    try {
        const response = await axios.get('/api/administracao/active-directory/config')
        const data = response.data.data
        
        configuracao.value = {
            host: data.host || '',
            port: data.port || 389,
            base_dn: data.base_dn || '',
            username: data.username || '',
            password: data.password || '',
            use_tls: data.use_tls || false,
            use_ssl: data.use_ssl || false,
            timeout: data.timeout || 5,
            logging: data.logging || false,
            sync_frequency: data.sync_frequency || 'daily'
        }
        
        // Atualizar status da conexão
        if (data.status_conexao) {
            statusConexao.value = {
                texto: data.status_conexao === 'conectado' ? 'Conectado' : 'Erro',
                class: data.status_conexao === 'conectado' ? 'text-success' : 'text-danger'
            }
        }
    } catch (error) {
        console.error('Erro ao carregar configuração:', error)
    }
}

const testarConexao = async () => {
    try {
        testando.value = true
        statusConexao.value = { texto: 'Testando...', class: 'text-warning' }
        
        const response = await axios.post('/api/administracao/active-directory/config/test', configuracao.value)
        
        if (response.data.status === 'success') {
            statusConexao.value = { texto: 'Conectado', class: 'text-success' }
        } else {
            statusConexao.value = { texto: 'Erro de Conexão', class: 'text-danger' }
        }
    } catch (error) {
        console.error('Erro ao testar conexão:', error)
        statusConexao.value = { texto: 'Erro de Conexão', class: 'text-danger' }
    } finally {
        testando.value = false
    }
}

const salvarConfiguracao = async () => {
    try {
        salvando.value = true
        
        const response = await axios.post('/api/administracao/active-directory/config', configuracao.value)
        
        if (response.data.status === 'success') {
            // Mostrar toast de sucesso
            console.log('Configuração salva com sucesso')
        }
    } catch (error) {
        console.error('Erro ao salvar configuração:', error)
    } finally {
        salvando.value = false
    }
}

const resetarFormulario = () => {
    carregarConfiguracao()
}
</script>

<style scoped>
.text-custom {
    color: #18578A !important;
}
</style>