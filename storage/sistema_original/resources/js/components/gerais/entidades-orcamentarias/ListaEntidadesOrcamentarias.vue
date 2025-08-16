<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho -->
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold" style="color: #5EA853;">
                    <i class="fas fa-building me-2"></i>Entidades Orçamentárias
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary me-2" @click="importarMunicipios">
                        <i class="fas fa-file-import me-1"></i> Importar Municípios
                    </button>
                    <button class="btn btn-primary" @click="abrirModalCriar">
                        <i class="fas fa-plus me-2"></i>Nova Entidade
                    </button>
                </div>
            </div>

            <!-- Corpo -->
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control" 
                                   id="filtroRazaoSocial" 
                                   v-model="filtros.razao_social" 
                                   @input="filtrarDados"
                                   placeholder="Filtrar por razão social">
                            <label for="filtroRazaoSocial">Razão Social</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control" 
                                   id="filtroNomeFantasia" 
                                   v-model="filtros.nome_fantasia" 
                                   @input="filtrarDados"
                                   placeholder="Filtrar por nome fantasia">
                            <label for="filtroNomeFantasia">Nome Fantasia</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select class="form-select" 
                                    id="filtroTipoOrganizacao" 
                                    v-model="filtros.tipo_organizacao" 
                                    @change="filtrarDados">
                                <option value="">Todos os tipos</option>
                                <option value="municipio">Município</option>
                                <option value="secretaria">Secretaria</option>
                                <option value="órgão">Órgão</option>
                                <option value="autarquia">Autarquia</option>
                                <option value="outros">Outros</option>
                            </select>
                            <label for="filtroTipoOrganizacao">Tipo de Organização</label>
                        </div>
                    </div>
                </div>

                <!-- Tabela -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="fw-semibold text-custom">Razão Social</th>
                                <th class="fw-semibold text-custom">Nome Fantasia</th>
                                <th class="fw-semibold text-custom">Tipo</th>
                                <th class="fw-semibold text-custom">CNPJ</th>
                                <th class="fw-semibold text-custom">Telefone</th>
                                <th class="fw-semibold text-end text-custom">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in items.data" :key="item.id">
                                <td>{{ item.razao_social }}</td>
                                <td>{{ item.nome_fantasia }}</td>
                                <td>{{ item.tipo_organizacao }}</td>
                                <td>{{ item.cnpj }}</td>
                                <td>{{ item.telefone }}</td>
                                <td class="text-end">
                                    <button class="btn btn-outline-secondary btn-sm me-2" 
                                            @click="editarItem(item)" 
                                            title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" 
                                            @click="excluirItem(item)" 
                                            title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Paginação -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Mostrando {{ items.from }} até {{ items.to }} de {{ items.total }} registros
                        </div>
                        <nav v-if="items.last_page > 1">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item" :class="{ disabled: items.current_page === 1 }">
                                    <a class="page-link" href="#" @click.prevent="mudarPagina(items.current_page - 1)">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                                <li v-for="page in items.last_page" 
                                    :key="page" 
                                    class="page-item" 
                                    :class="{ active: page === items.current_page }">
                                    <a class="page-link" href="#" @click.prevent="mudarPagina(page)">{{ page }}</a>
                                </li>
                                <li class="page-item" :class="{ disabled: items.current_page === items.last_page }">
                                    <a class="page-link" href="#" @click.prevent="mudarPagina(items.current_page + 1)">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Criar/Editar -->
        <div class="modal fade" id="itemModal" tabindex="-1" ref="itemModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-semibold">
                            <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
                            {{ modoEdicao ? 'Editar' : 'Nova' }} Entidade Orçamentária
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <form @submit.prevent="salvarItem" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.razao_social }"
                                               id="razao_social" 
                                               v-model="form.razao_social" 
                                               required>
                                        <label for="razao_social">Razão Social</label>
                                        <div class="invalid-feedback">{{ errors.razao_social }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.nome_fantasia }"
                                               id="nome_fantasia" 
                                               v-model="form.nome_fantasia" 
                                               required>
                                        <label for="nome_fantasia">Nome Fantasia</label>
                                        <div class="invalid-feedback">{{ errors.nome_fantasia }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" 
                                                :class="{ 'is-invalid': errors.tipo_organizacao }"
                                                id="tipo_organizacao" 
                                                v-model="form.tipo_organizacao" 
                                                required>
                                            <option value="">Selecione...</option>
                                            <option value="municipio">Município</option>
                                            <option value="secretaria">Secretaria</option>
                                            <option value="órgão">Órgão</option>
                                            <option value="autarquia">Autarquia</option>
                                            <option value="outros">Outros</option>
                                        </select>
                                        <label for="tipo_organizacao">Tipo de Organização</label>
                                        <div class="invalid-feedback">{{ errors.tipo_organizacao }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.cnpj }"
                                               id="cnpj" 
                                               v-model="form.cnpj">
                                        <label for="cnpj">CNPJ</label>
                                        <div class="invalid-feedback">{{ errors.cnpj }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.email }"
                                               id="email" 
                                               v-model="form.email">
                                        <label for="email">E-mail</label>
                                        <div class="invalid-feedback">{{ errors.email }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.telefone }"
                                               id="telefone" 
                                               v-model="form.telefone" 
                                               required>
                                        <label for="telefone">Telefone</label>
                                        <div class="invalid-feedback">{{ errors.telefone }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.cep }"
                                               id="cep" 
                                               v-model="form.cep">
                                        <label for="cep">CEP</label>
                                        <div class="invalid-feedback">{{ errors.cep }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.endereco }"
                                               id="endereco" 
                                               v-model="form.endereco">
                                        <label for="endereco">Endereço</label>
                                        <div class="invalid-feedback">{{ errors.endereco }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.codigo_ibge }"
                                               id="codigo_ibge" 
                                               v-model="form.codigo_ibge">
                                        <label for="codigo_ibge">Código IBGE</label>
                                        <div class="invalid-feedback">{{ errors.codigo_ibge }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.populacao }"
                                               id="populacao" 
                                               v-model="form.populacao">
                                        <label for="populacao">População</label>
                                        <div class="invalid-feedback">{{ errors.populacao }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.responsavel }"
                                               id="responsavel" 
                                               v-model="form.responsavel" 
                                               required>
                                        <label for="responsavel">Responsável</label>
                                        <div class="invalid-feedback">{{ errors.responsavel }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.responsavel_cargo }"
                                               id="responsavel_cargo" 
                                               v-model="form.responsavel_cargo" 
                                               required>
                                        <label for="responsavel_cargo">Cargo do Responsável</label>
                                        <div class="invalid-feedback">{{ errors.responsavel_cargo }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.responsavel_telefone }"
                                               id="responsavel_telefone" 
                                               v-model="form.responsavel_telefone">
                                        <label for="responsavel_telefone">Telefone do Responsável</label>
                                        <div class="invalid-feedback">{{ errors.responsavel_telefone }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.responsavel_email }"
                                               id="responsavel_email" 
                                               v-model="form.responsavel_email">
                                        <label for="responsavel_email">E-mail do Responsável</label>
                                        <div class="invalid-feedback">{{ errors.responsavel_email }}</div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" @click="salvarItem" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-save me-2"></i>
                            {{ loading ? 'Salvando...' : 'Salvar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast de Sucesso/Erro -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header" :class="toastType === 'success' ? 'bg-success text-white' : 'bg-danger text-white'">
                    <i class="fas" :class="toastType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
                    <strong class="ms-2 me-auto">{{ toastTitle }}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
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
    data() {
        return {
            // Dados da listagem
            items: {
                data: [],
                current_page: 1,
                from: 0,
                to: 0,
                total: 0,
                last_page: 1
            },
            // Filtros
            filtros: {
                razao_social: '',
                nome_fantasia: '',
                tipo_organizacao: ''
            },
            // Form
            form: {
                razao_social: '',
                nome_fantasia: '',
                tipo_organizacao: '',
                email: '',
                endereco: '',
                codigo_ibge: '',
                populacao: '',
                cep: '',
                telefone: '',
                cnpj: '',
                responsavel: '',
                responsavel_cargo: '',
                responsavel_telefone: '',
                responsavel_email: ''
            },
            // Estado
            modoEdicao: false,
            errors: {},
            loading: false,
            itemModal: null,
            toast: null,
            toastType: 'success',
            toastTitle: '',
            toastMessage: ''
        }
    },
    mounted() {
        // Inicializa o modal
        this.itemModal = new bootstrap.Modal(document.getElementById('itemModal'), {
            backdrop: 'static',
            keyboard: false
        });

        // Inicializa o toast
        this.toast = new bootstrap.Toast(document.getElementById('toast'), {
            autohide: true,
            delay: 3000
        });

        // Carrega os dados iniciais
        this.carregarDados();
    },
    methods: {
        async carregarDados() {
            try {
                //console.log('Iniciando carregamento de dados...');
                const response = await axios.get('/entidades-orcamentarias/listar', {
                    params: {
                        page: this.items.current_page,
                        ...this.filtros
                    }
                });
                
                // console.log('Resposta bruta da API:', response);
                // console.log('Dados da resposta:', response.data);
                
                // Verifica se a resposta tem a estrutura esperada
                if (response.data && response.data.data) {
                    // Atualiza os dados da listagem
                    this.items = {
                        data: response.data.data || [],
                        current_page: response.data.current_page || 1,
                        from: response.data.from || 0,
                        to: response.data.to || 0,
                        total: response.data.total || 0,
                        last_page: response.data.last_page || 1
                    };
                    
                    //console.log('Dados atualizados:', this.items);
                    
                    // Se não houver dados na página atual e houver mais de uma página,
                    // volta para a primeira página
                    if (this.items.data.length === 0 && this.items.current_page > 1) {
                        console.log('Nenhum dado encontrado, voltando para primeira página...');
                        this.items.current_page = 1;
                        await this.carregarDados();
                    }
                } else {
                    console.error('Estrutura de dados inválida:', response.data);
                    this.mostrarErro('Erro ao carregar dados: estrutura inválida');
                }
            } catch (error) {
                console.error('Erro ao carregar dados:', error);
                console.error('Detalhes do erro:', {
                    message: error.message,
                    response: error.response,
                    request: error.request
                });
                this.mostrarErro('Erro ao carregar dados');
            }
        },
        filtrarDados() {
            this.items.current_page = 1;
            this.carregarDados();
        },
        async mudarPagina(page) {
            this.items.current_page = page;
            await this.carregarDados();
        },
        abrirModalCriar() {
            this.modoEdicao = false;
            this.limparFormulario();
            this.itemModal.show();
        },
        editarItem(item) {
            this.modoEdicao = true;
            this.form = { ...item };
            this.itemModal.show();
        },
        limparFormulario() {
            this.form = {
                razao_social: '',
                nome_fantasia: '',
                tipo_organizacao: '',
                email: '',
                endereco: '',
                codigo_ibge: '',
                populacao: '',
                cep: '',
                telefone: '',
                cnpj: '',
                responsavel: '',
                responsavel_cargo: '',
                responsavel_telefone: '',
                responsavel_email: ''
            };
            this.errors = {};
        },
        async salvarItem() {
            this.loading = true;
            this.errors = {};

            try {
                console.log('Iniciando salvamento...', this.form);
                
                const headers = {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                };
                
                if (this.modoEdicao) {
                    console.log('Atualizando entidade...');
                    const response = await axios.put(`/entidades-orcamentarias/${this.form.id}`, this.form, { headers });
                    console.log('Resposta da atualização:', response.data);
                    this.mostrarSucesso('Entidade atualizada com sucesso!');
                } else {
                    console.log('Criando nova entidade...');
                    const response = await axios.post('/entidades-orcamentarias', this.form, { headers });
                    console.log('Resposta da criação:', response.data);
                    this.mostrarSucesso('Entidade criada com sucesso!');
                }

                this.itemModal.hide();
                this.limparFormulario();
                
                // Força a atualização da listagem
                console.log('Recarregando dados após salvar...');
                this.items.current_page = 1;
                await this.carregarDados();
            } catch (error) {
                console.error('Erro ao salvar:', error);
                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    this.errors = Object.keys(errors).reduce((acc, key) => {
                        acc[key] = errors[key][0];
                        return acc;
                    }, {});
                    
                    // Mostrar mensagem de erro específica para o primeiro campo com erro
                    const primeiroErro = Object.values(this.errors)[0];
                    this.mostrarErro(primeiroErro);
                } else {
                    this.mostrarErro('Erro ao salvar entidade');
                }
            } finally {
                this.loading = false;
            }
        },
        async excluirItem(item) {
            if (!confirm('Tem certeza que deseja excluir esta entidade?')) {
                return;
            }

            try {
                await axios.delete(`/entidades-orcamentarias/${item.id}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                this.mostrarSucesso('Entidade excluída com sucesso!');
                this.carregarDados();
            } catch (error) {
                console.error('Erro ao excluir:', error);
                this.mostrarErro('Erro ao excluir entidade');
            }
        },
        mostrarToast(type, title, message) {
            this.toastType = type;
            this.toastTitle = title;
            this.toastMessage = message;
            this.toast.show();
        },
        mostrarSucesso(message) {
            this.mostrarToast('success', 'Sucesso!', message);
        },
        mostrarErro(message) {
            this.mostrarToast('error', 'Erro!', message);
        },
        async importarMunicipios() {
            try {
                const response = await axios.post('/api/entidades-orcamentarias/importar-municipios');
                if (response.data.success) {
                    this.mostrarSucesso('Municípios importados com sucesso!');
                    this.carregarDados();
                } else {
                    this.mostrarErro(response.data.message || 'Erro ao importar municípios');
                }
            } catch (error) {
                console.error('Erro ao importar municípios:', error);
                this.mostrarErro(error.response?.data?.message || 'Erro ao importar municípios');
            }
        }
    }
}
</script> 