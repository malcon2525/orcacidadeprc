<template>
    <div>
        <form @submit.prevent="handleFileUpload" class="mb-4">
            <div class="mb-3">
                <label for="arquivo" class="form-label">Selecione o arquivo PDF:</label>
                <input type="file" 
                       class="form-control" 
                       id="arquivo" 
                       ref="arquivoInput"
                       accept=".pdf" 
                       required
                       @change="validarArquivo">
            </div>
            <div class="d-inline-block" 
                 ref="tooltipContainer"
                 :data-bs-toggle="!arquivoValido ? 'tooltip' : null"
                 :data-bs-title="!arquivoValido ? 'Selecione um arquivo PDF válido' : null">
                <button type="submit" 
                        class="btn"
                        :class="{
                            'btn-primary': arquivoValido,
                            'btn-outline-secondary': !arquivoValido
                        }"
                        :disabled="!arquivoValido">
                    <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    {{ loading ? 'Processando...' : 'Importar' }}
                </button>
            </div>
        </form>

        <!-- Mensagens de Alerta -->
        <div v-if="mensagem" 
             :class="['alert', mensagem.tipo === 'erro' ? 'alert-danger' : 'alert-success']"
             class="mb-4">
            {{ mensagem.texto }}
        </div>

        <!-- Overlay de Carregamento -->
        <div v-if="loading" class="position-relative">
            <div class="position-absolute top-50 start-50 translate-middle text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <div class="mt-2">
                    <div>Processando arquivo...</div>
                    <div class="small text-muted mt-1">{{ statusProcessamento }}</div>
                </div>
            </div>
        </div>

        <div v-if="equipamentos.length > 0" class="mt-4">
            <h4>Tabela de Equipamentos</h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover tabela-derpr">
                    <thead class="table-dark">
                        <tr>
                            <th>Código Serviço</th>
                            <th>Descrição Serviço</th>
                            <th>Unidade</th>
                            <th>Descrição Equipamento</th>
                            <th>Código Equipamento</th>
                            <th>Qtde</th>
                            <th>Ut Pr</th>
                            <th>Ut. Impr</th>
                            <th class="text-end">Vl. Hr. Prod.</th>
                            <th class="text-end">Vl. Hr. Imp.</th>
                            <th class="text-end">Custo Horário</th>
                            <th>Data Base</th>
                            <th>Honerado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(equip, index) in equipamentos" :key="index">
                            <td>{{ equip.codigo_servico }}</td>
                            <td>{{ equip.descricao_servico }}</td>
                            <td>{{ equip.unidade_servico }}</td>
                            <td>{{ equip.descricao_equipamento }}</td>
                            <td>{{ equip.codigo_equipamento }}</td>
                            <td>{{ equip.quantidade }}</td>
                            <td>{{ equip.ut_pr }}</td>
                            <td>{{ equip.ut_impr }}</td>
                            <td class="text-end">{{ formatarValor(equip.vl_hr_prod) }}</td>
                            <td class="text-end">{{ formatarValor(equip.vl_hr_imp) }}</td>
                            <td class="text-end">{{ formatarValor(equip.custo_horario) }}</td>
                            <td>{{ equip.data_base }}</td>
                            <td>{{ equip.honerado }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ImportarDERPR',
    data() {
        return {
            loading: false,
            mensagem: null,
            equipamentos: [],
            arquivoValido: false,
            statusProcessamento: 'Iniciando processamento...'
        }
    },
    watch: {
        arquivoValido() {
            this.$nextTick(() => {
                this.updateTooltip();
            });
        }
    },
    mounted() {
        this.updateTooltip();
    },
    methods: {
        validarArquivo(event) {
            const arquivo = event.target.files[0]
            this.arquivoValido = arquivo && arquivo.type === 'application/pdf'
            
            if (!this.arquivoValido) {
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Por favor, selecione um arquivo PDF válido.'
                }
            } else {
                this.mensagem = null
            }
        },
        async handleFileUpload(event) {
            event.preventDefault();
            const file = this.$refs.arquivoInput.files[0];
            if (!file) {
                this.mensagem = {
                    tipo: 'erro',
                    texto: 'Por favor, selecione um arquivo.'
                };
                return;
            }

            this.loading = true;
            this.mensagem = null;
            this.equipamentos = [];
            this.statusProcessamento = 'Iniciando processamento...';

            const formData = new FormData();
            formData.append('arquivo', file);

            try {
                console.log('Iniciando upload do arquivo:', file.name);
                
                // Configurar o interceptor para atualizar o status
                const interceptor = axios.interceptors.response.use(
                    response => {
                        console.log('Resposta recebida:', response.data);
                        if (response.data.status) {
                            this.statusProcessamento = response.data.status;
                        }
                        return response;
                    },
                    error => {
                        console.error('Erro na requisição:', error);
                        return Promise.reject(error);
                    }
                );

                const response = await axios.post('/api/preco/importar-der-pr/equipamentos', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                // Remover o interceptor após a conclusão
                axios.interceptors.response.eject(interceptor);

                console.log('Resposta completa:', response);

                if (response.data.equipamentos && response.data.equipamentos.length > 0) {
                    this.equipamentos = response.data.equipamentos;
                    this.mensagem = {
                        tipo: 'sucesso',
                        texto: `Arquivo importado com sucesso! ${response.data.equipamentos.length} equipamentos encontrados.`
                    };
                } else {
                    this.mensagem = {
                        tipo: 'erro',
                        texto: response.data.message || 'Nenhum dado encontrado no arquivo'
                    };
                }
            } catch (error) {
                console.error('Erro completo:', error);
                this.mensagem = {
                    tipo: 'erro',
                    texto: error.response?.data?.message || 'Erro ao processar o arquivo'
                };
            } finally {
                this.loading = false;
                this.statusProcessamento = 'Processamento concluído';
            }
        },
        formatarValor(valor) {
            if (!valor) return '0,00'
            if (valor.toString().includes(',')) {
                return valor
            }
            return Number(valor.toString().replace(',', '.')).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })
        },
        updateTooltip() {
            const tooltipElement = this.$refs.tooltipContainer;
            if (tooltipElement) {
                const tooltip = bootstrap.Tooltip.getInstance(tooltipElement);
                if (tooltip) {
                    tooltip.dispose();
                }
                if (!this.arquivoValido) {
                    new bootstrap.Tooltip(tooltipElement, {
                        trigger: 'hover',
                        html: true
                    });
                }
            }
        }
    }
}
</script>

<style scoped>
.table th {
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 1;
    background-color: #212529;
    color: white;
}

.table td {
    vertical-align: middle;
    white-space: nowrap;
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.table-responsive {
    max-height: 70vh;
    overflow-y: auto;
}

.tabela-derpr {
    font-size: 0.9rem;
}

.tabela-derpr th {
    font-weight: 500;
}

.tabela-derpr td {
    padding: 0.5rem;
}
</style> 