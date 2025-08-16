<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho -->
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold" style="color: #5EA853;">
                    <i class="fas fa-truck me-2"></i>DMT - Padrão PRC
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" @click="abrirModalCriar">
                        <i class="fas fa-plus me-2"></i>Novo Material
                    </button>
                    <input ref="inputImportar" type="file" accept=".xlsx,.xls" style="display:none" @change="importarArquivo" />
                    <button class="btn btn-success" @click="$refs.inputImportar.click()">
                        <i class="fas fa-file-import me-2"></i>Importar
                    </button>
                </div>
            </div>

            <!-- Corpo -->
            <div class="card-body p-0">
                <!-- Tabela -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th class="fw-semibold text-custom" style="width:100px;">Código</th>
                                <th class="fw-semibold text-custom">Nome</th>
                                <th class="fw-semibold text-custom" style="width:300px;">Origem</th>
                                <th class="fw-semibold text-custom" style="width:240px;">Destino</th>
                                <th class="fw-semibold text-custom" style="width:90px;">Sigla</th>
                                <th class="fw-semibold text-custom coluna-dmt" style="width:100px;">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <i class="fas fa-road me-1"></i>X1 (Km)
                                    </div>
                                </th>
                                <th class="fw-semibold text-custom coluna-dmt" style="width:100px;">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <i class="fas fa-road me-1"></i>X2 (Km)
                                    </div>
                                </th>
                                <th class="fw-semibold text-custom" style="width:100px;">Tipo</th>
                                <th class="fw-semibold text-end text-custom" style="width:110px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(item, idx) in dados" :key="item.id">
                                <tr v-if="idx === 0 || item.destino !== dados[idx-1].destino" class="grupo-destino">
                                    <td colspan="9" class="grupo-destino-header">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            <span class="fw-semibold">{{ item.destino || 'Destino não informado' }}</span>
                                            <span class="badge bg-light text-secondary ms-2 rounded-pill">
                                                {{ contarItensDestino(item.destino) }} itens
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="linha-item">
                                    <td class="ps-4">{{ item.codigo_material }}</td>
                                    <td>{{ item.nome_material }}</td>
                                    <td>{{ item.origem || 'não informado' }}</td>
                                    <td>{{ item.destino }}</td>
                                    <td>{{ item.sigla_transporte }}</td>
                                    <td class="text-end coluna-dmt valor-dmt">
                                        {{ Number(item.x1).toFixed(2) }}
                                    </td>
                                    <td class="text-end coluna-dmt valor-dmt">
                                        {{ Number(item.x2).toFixed(2) }}
                                    </td>
                                    <td>
                                        <span :class="['badge', item.tipo === 'local' ? 'bg-info' : 'bg-warning', 'text-dark']">
                                            {{ item.tipo }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary me-2" @click="editar(item)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" @click="confirmarExclusao(item)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-between align-items-center p-3 border-top">
                    <div>
                        Mostrando {{ paginacao.from }} até {{ paginacao.to }} de {{ paginacao.total }} registros
                    </div>
                    <nav>
                        <ul class="pagination mb-0">
                            <li class="page-item" :class="{ disabled: !paginacao.prev_page_url }">
                                <a class="page-link" href="#" @click.prevent="carregarPagina(paginacao.current_page - 1)">
                                    Anterior
                                </a>
                            </li>
                            <li class="page-item" :class="{ disabled: !paginacao.next_page_url }">
                                <a class="page-link" href="#" @click.prevent="carregarPagina(paginacao.current_page + 1)">
                                    Próxima
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Modal de Formulário -->
        <div class="modal fade" id="modalForm" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ form.id ? 'Editar' : 'Novo' }} Material de Transporte</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="salvar">
                            <div class="mb-3">
                                <label class="form-label">Código do Material</label>
                                <input type="text" class="form-control" v-model="form.codigo_material" maxlength="10" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nome do Material</label>
                                <input type="text" class="form-control" v-model="form.nome_material" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Origem</label>
                                <input type="text" class="form-control" v-model="form.origem" maxlength="150">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Destino</label>
                                <input type="text" class="form-control" v-model="form.destino" maxlength="150">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sigla do Transporte</label>
                                <input type="text" class="form-control" v-model="form.sigla_transporte" maxlength="3" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">X1 (Km)</label>
                                <input type="number" class="form-control" v-model="form.x1" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">X2 (Km)</label>
                                <input type="number" class="form-control" v-model="form.x2" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo</label>
                                <select class="form-select" v-model="form.tipo" required>
                                    <option value="local">Local</option>
                                    <option value="comercial">Comercial</option>
                                </select>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação de Exclusão -->
        <div class="modal fade" id="modalConfirmacao" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Exclusão</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza que deseja excluir este material de transporte?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" @click="excluir">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data() {
        return {
            dados: [],
            paginacao: {
                current_page: 1,
                from: 0,
                to: 0,
                total: 0,
                prev_page_url: null,
                next_page_url: null
            },
            form: {
                id: null,
                codigo_material: '',
                nome_material: '',
                origem: '',
                destino: '',
                sigla_transporte: '',
                x1: 0,
                x2: 0,
                tipo: 'local'
            },
            itemParaExcluir: null,
            modalForm: null,
            modalConfirmacao: null
        }
    },
    mounted() {
        this.carregarDados();
        this.modalForm = new bootstrap.Modal(document.getElementById('modalForm'));
        this.modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
    },
    methods: {
        async carregarDados(pagina = 1) {
            try {
                const response = await axios.get('/dmt-default/listar', {
                    params: { page: pagina }
                });
                this.dados = response.data.data;
                this.paginacao = {
                    current_page: response.data.current_page,
                    from: response.data.from,
                    to: response.data.to,
                    total: response.data.total,
                    prev_page_url: response.data.prev_page_url,
                    next_page_url: response.data.next_page_url
                };
            } catch (error) {
                console.error('Erro ao carregar dados:', error);
                alert('Erro ao carregar os dados. Por favor, tente novamente.');
            }
        },
        carregarPagina(pagina) {
            this.carregarDados(pagina);
        },
        abrirModalCriar() {
            this.form = {
                id: null,
                codigo_material: '',
                nome_material: '',
                origem: '',
                destino: '',
                sigla_transporte: '',
                x1: 0,
                x2: 0,
                tipo: 'local'
            };
            this.modalForm.show();
        },
        editar(item) {
            this.form = { ...item };
            this.modalForm.show();
        },
        async salvar() {
            try {
                if (this.form.id) {
                    await axios.put(`/dmt-default/${this.form.id}`, this.form);
                } else {
                    await axios.post('/dmt-default', this.form);
                }
                this.modalForm.hide();
                this.carregarDados(this.paginacao.current_page);
            } catch (error) {
                console.error('Erro ao salvar:', error);
                alert('Erro ao salvar os dados. Por favor, verifique os campos e tente novamente.');
            }
        },
        confirmarExclusao(item) {
            this.itemParaExcluir = item;
            this.modalConfirmacao.show();
        },
        async excluir() {
            if (!this.itemParaExcluir) return;

            try {
                await axios.delete(`/dmt-default/${this.itemParaExcluir.id}`);
                this.modalConfirmacao.hide();
                this.carregarDados(this.paginacao.current_page);
            } catch (error) {
                console.error('Erro ao excluir:', error);
                alert('Erro ao excluir o registro. Por favor, tente novamente.');
            }
        },
        async importarArquivo(event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await axios.post('/dmt-default/importar', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    alert(response.data.message);
                    this.carregarDados();
                } else {
                    alert('Erro ao importar arquivo: ' + response.data.message);
                }
            } catch (error) {
                console.error('Erro ao importar arquivo:', error);
                alert('Erro ao importar arquivo. Verifique se o arquivo está no formato correto.');
            }

            event.target.value = '';
        },
        contarItensDestino(destino) {
            return this.dados.filter(item => item.destino === destino).length;
        }
    }
}
</script>

<style scoped>
.text-custom {
    color: #666;
}

.grupo-destino-header {
    background-color: #f8f9fa;
    color: #2c3e50;
    padding: 0.75rem 1rem !important;
    border-top: 1px solid #e9ecef;
}

.grupo-destino-header i {
    color: #5EA853;
    font-size: 1.1em;
}

.linha-item {
    background-color: white;
}

.linha-item:hover {
    background-color: #f8f9fa;
}

.linha-item td {
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #dee2e6;
}

/* Ajustes visuais para melhor hierarquia */
.table > :not(caption) > * > * {
    padding: 0.75rem;
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.75em;
}

.bg-info {
    background-color: #e3f2fd !important;
}

.bg-warning {
    background-color: #fff3cd !important;
}

/* Estilos para as colunas DMT */
.coluna-dmt {
    background-color: #f8f9fa;
    border-left: 1px solid #dee2e6;
    border-right: 1px solid #dee2e6;
}

.valor-dmt {
    font-family: 'Consolas', monospace;
    font-weight: 500;
    color: #2c3e50;
}

/* Quando o valor for zero, deixar mais suave */
.valor-dmt:has(span:contains('0.00')) {
    color: #6c757d;
}

.fa-road {
    color: #5EA853;
    font-size: 0.9em;
}

/* Hover na linha mantendo o destaque das colunas DMT */
.linha-item:hover .coluna-dmt {
    background-color: #edf2ff;
}
</style> 