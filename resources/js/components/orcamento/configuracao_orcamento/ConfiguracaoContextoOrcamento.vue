<template>
    <div>
        <!-- Estado de Carregamento -->
        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-2 text-muted">Carregando dados de configuração...</p>
        </div>

        <!-- Estado de Erro -->
        <div v-else-if="erro" class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Erro:</strong> {{ erro }}
        </div>

        <!-- Formulário de Configuração -->
        <div v-else>
            <!-- Contexto Atual (se existe) -->
            <div v-if="temContextoAtual" class="alert alert-info mb-4" role="alert">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="alert-heading mb-2">
                            <i class="fas fa-info-circle me-2"></i>Contexto Atual Definido
                        </h6>
                        <p class="mb-1"><strong>Entidade:</strong> {{ contextoAtual?.entidade?.nome }}</p>
                        <p class="mb-1"><strong>Data Base SINAPI:</strong> {{ contextoAtual?.sinapi?.data_formatada }}</p>
                        <p class="mb-1"><strong>Data Base DERPR:</strong> {{ contextoAtual?.derpr?.data_formatada }}</p>
                        <p class="mb-0 small text-muted">Última atualização: {{ contextoAtual?.atualizado_em }}</p>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" @click="limparContexto" :disabled="limpando">
                        <span v-if="limpando" class="spinner-border spinner-border-sm me-2"></span>
                        <i v-else class="fas fa-trash me-2"></i>
                        {{ limpando ? 'Removendo...' : 'Remover' }}
                    </button>
                </div>
            </div>

            <!-- Formulário -->
            <form @submit.prevent="salvarConfiguracao" class="needs-validation" novalidate>
                <div class="row g-4">
                    <!-- Entidade Orçamentária -->
                    <div class="col-12">
                        <div class="form-floating">
                            <select class="form-control" 
                                    :class="{ 'is-invalid': errors.entidade_orcamentaria_id }"
                                    id="entidade_orcamentaria_id" 
                                    v-model="form.entidade_orcamentaria_id"
                                    required>
                                <option value="">Selecione uma entidade orçamentária</option>
                                <option v-for="entidade in entidadesVinculadas" 
                                        :key="entidade.id" 
                                        :value="entidade.id">
                                    {{ entidade.nome }} ({{ entidade.uf }})
                                </option>
                            </select>
                            <label for="entidade_orcamentaria_id">Entidade Orçamentária *</label>
                            <div class="invalid-feedback" v-if="errors.entidade_orcamentaria_id">
                                {{ Array.isArray(errors.entidade_orcamentaria_id) ? errors.entidade_orcamentaria_id[0] : errors.entidade_orcamentaria_id }}
                            </div>
                        </div>
                    </div>

                    <!-- Data Base SINAPI -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-control" 
                                    :class="{ 'is-invalid': errors.data_base_sinapi }"
                                    id="data_base_sinapi" 
                                    v-model="form.data_base_sinapi"
                                    required>
                                <option value="">Selecione uma data base</option>
                                <option v-for="data in datasBaseSinapi" 
                                        :key="data.value" 
                                        :value="data.value">
                                    {{ data.label }} - {{ data.label_completo }}
                                </option>
                            </select>
                            <label for="data_base_sinapi">Data Base SINAPI *</label>
                            <div class="invalid-feedback" v-if="errors.data_base_sinapi">
                                {{ Array.isArray(errors.data_base_sinapi) ? errors.data_base_sinapi[0] : errors.data_base_sinapi }}
                            </div>
                        </div>
                    </div>

                    <!-- Data Base DERPR -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-control" 
                                    :class="{ 'is-invalid': errors.data_base_derpr }"
                                    id="data_base_derpr" 
                                    v-model="form.data_base_derpr"
                                    required>
                                <option value="">Selecione uma data base</option>
                                <option v-for="data in datasBaseDerpr" 
                                        :key="data.value" 
                                        :value="data.value">
                                    {{ data.label }} - {{ data.label_completo }}
                                </option>
                            </select>
                            <label for="data_base_derpr">Data Base DERPR *</label>
                            <div class="invalid-feedback" v-if="errors.data_base_derpr">
                                {{ Array.isArray(errors.data_base_derpr) ? errors.data_base_derpr[0] : errors.data_base_derpr }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                    <button type="button" class="btn btn-secondary" @click="limparFormulario">
                        <i class="fas fa-times me-2"></i>Limpar
                    </button>
                    <button type="submit" class="btn btn-success" :disabled="salvando">
                        <span v-if="salvando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        <i v-else class="fas fa-save me-2"></i>
                        {{ salvando ? 'Salvando...' : 'Salvar Configuração' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Toast Container -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div ref="toastRef" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i :class="toastIcon" class="me-2"></i>
                    <strong class="me-auto">{{ toastTitle }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ toastMessage }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ConfiguracaoContextoOrcamento',
    data() {
        return {
            loading: true,
            salvando: false,
            limpando: false,
            erro: null,
            errors: {},
            
            // Dados do formulário
            form: {
                entidade_orcamentaria_id: '',
                data_base_sinapi: '',
                data_base_derpr: ''
            },
            
            // Dados para os selects
            entidadesVinculadas: [],
            datasBaseSinapi: [],
            datasBaseDerpr: [],
            
            // Contexto atual
            contextoAtual: null,
            temContextoAtual: false,
            
            // Toast
            toastTitle: '',
            toastMessage: '',
            toastIcon: '',
            toast: null
        }
    },
    mounted() {
        this.inicializarToast();
        this.carregarDados();
    },
    methods: {
        inicializarToast() {
            this.toast = new bootstrap.Toast(this.$refs.toastRef);
        },
        
        async carregarDados() {
            this.loading = true;
            this.erro = null;
            
            try {
                const response = await fetch('/api/orcamento/configuracao-orcamento/dados', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Erro ao carregar dados');
                }

                // Carregar dados para os selects
                this.entidadesVinculadas = data.dados.entidades_vinculadas || [];
                this.datasBaseSinapi = data.dados.datas_base_sinapi || [];
                this.datasBaseDerpr = data.dados.datas_base_derpr || [];
                
                // Configurar contexto atual
                this.contextoAtual = data.dados.contexto_atual;
                this.temContextoAtual = data.dados.tem_contexto_definido;
                
                // Pré-preencher formulário se houver contexto ou uma única entidade
                if (this.temContextoAtual && this.contextoAtual) {
                    this.form.entidade_orcamentaria_id = this.contextoAtual.entidade.id;
                    this.form.data_base_sinapi = this.contextoAtual.sinapi.data;
                    this.form.data_base_derpr = this.contextoAtual.derpr.data;
                } else if (this.entidadesVinculadas.length === 1) {
                    // Se houver apenas uma entidade, pré-selecionar
                    this.form.entidade_orcamentaria_id = this.entidadesVinculadas[0].id;
                }

            } catch (error) {
                console.error('Erro ao carregar dados:', error);
                this.erro = error.message;
            } finally {
                this.loading = false;
            }
        },
        
        async salvarConfiguracao() {
            this.salvando = true;
            this.errors = {};
            
            try {
                const response = await fetch('/api/orcamento/configuracao-orcamento/salvar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        this.errors = data.errors;
                        this.focarPrimeiroCampoInvalido();
                        throw new Error('Por favor, corrija os erros no formulário');
                    }
                    throw new Error(data.message || 'Erro ao salvar configuração');
                }

                // Sucesso
                this.mostrarToast('Sucesso', 'Configuração salva com sucesso!', 'fas fa-check-circle text-success');
                
                // Recarregar dados para atualizar contexto atual
                await this.carregarDados();

            } catch (error) {
                console.error('Erro ao salvar:', error);
                this.mostrarToast('Erro', error.message, 'fas fa-exclamation-circle text-danger');
            } finally {
                this.salvando = false;
            }
        },
        
        async limparContexto() {
            if (!confirm('Tem certeza que deseja remover o contexto orçamentário atual?')) {
                return;
            }
            
            this.limpando = true;
            
            try {
                const response = await fetch('/api/orcamento/configuracao-orcamento/limpar', {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Erro ao remover contexto');
                }

                this.mostrarToast('Sucesso', 'Contexto removido com sucesso!', 'fas fa-check-circle text-success');
                
                // Recarregar dados
                await this.carregarDados();

            } catch (error) {
                console.error('Erro ao limpar contexto:', error);
                this.mostrarToast('Erro', error.message, 'fas fa-exclamation-circle text-danger');
            } finally {
                this.limpando = false;
            }
        },
        
        limparFormulario() {
            this.form = {
                entidade_orcamentaria_id: '',
                data_base_sinapi: '',
                data_base_derpr: ''
            };
            this.errors = {};
            
            // Se houver apenas uma entidade, manter selecionada
            if (this.entidadesVinculadas.length === 1) {
                this.form.entidade_orcamentaria_id = this.entidadesVinculadas[0].id;
            }
        },
        
        focarPrimeiroCampoInvalido() {
            const campos = ['entidade_orcamentaria_id', 'data_base_sinapi', 'data_base_derpr'];
            
            for (const campo of campos) {
                if (this.errors[campo]) {
                    document.getElementById(campo)?.focus();
                    break;
                }
            }
        },
        
        mostrarToast(title, message, icon) {
            this.toastTitle = title;
            this.toastMessage = message;
            this.toastIcon = icon;
            this.toast.show();
        }
    }
}
</script>

<style scoped>
/* Sem estilos locais - tudo no CSS global */
</style>
