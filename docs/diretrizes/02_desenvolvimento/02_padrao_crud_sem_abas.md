# Padrão CRUD Sem Abas - OrçaCidade

> **ESCOPO**: Este documento define padrões para CRUDs SEM abas no projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para manter consistência visual.

> **APLICAÇÃO**: Interfaces simples como usuários, entidades orçamentárias, tipos de orçamento, etc.

> **BASE**: Este padrão segue os padrões universais definidos em `01_padrao_crud.md`.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Estabelecer padrões para CRUDs sem abas, garantindo consistência e funcionalidade adequada em todo o sistema.

### 🎨 **Características**
- **Componente Vue monolítico** (tudo em um arquivo)
- **Paginação local** no próprio componente
- **Modal obrigatório** para criar/editar
- **Modal de confirmação** para exclusões
- **Filtros colapsáveis** por padrão
- **Toast notifications** para feedback

### 📋 **Exemplos de Uso**
- ListaUsuarios.vue
- ListaEntidadesOrcamentarias.vue
- ListaTiposOrcamento.vue
- Qualquer CRUD simples sem navegação por abas

---

## 2. Estrutura do Componente Vue

### 📋 **Estrutura Base (Obrigatória)**
```vue
<template>
    <div>
        <!-- Header com Botão Criar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="text-custom mb-0">
                <i class="fas fa-[icon] me-2"></i>[Título da Funcionalidade]
            </h5>
            <button class="btn btn-success" @click="abrirModalCriar">
                <i class="fas fa-plus me-2"></i>Novo [Item]
            </button>
        </div>

        <!-- Filtros Colapsáveis -->
        <div class="card mb-4">
            <div class="card-header bg-light py-2">
                <button class="btn btn-link text-decoration-none p-0" @click="toggleFiltros">
                    <i class="fas fa-filter me-2"></i>
                    {{ filtrosVisiveis ? 'Ocultar' : 'Mostrar' }} Filtros
                    <i class="fas" :class="filtrosVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
            </div>
            <div class="card-body" v-show="filtrosVisiveis">
                <!-- Filtros aqui -->
            </div>
        </div>

        <!-- Tabela de Dados -->
        <div class="card">
            <div class="card-body p-0">
                <!-- Tabela aqui -->
            </div>
        </div>

        <!-- Paginação Local -->
        <div class="mt-4 pt-3 border-top">
            <!-- Paginação aqui -->
        </div>

        <!-- Modais -->
        <!-- Modal de Formulário -->
        <!-- Modal de Confirmação -->
    </div>
</template>

<script>
export default {
    name: 'Lista[Funcionalidade]',
    data() {
        return {
            // Dados principais
            dados: [],
            registros: {
                data: [],
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 0
            },
            
            // Estados de interface
            loading: false,
            filtrosVisiveis: false,
            modoEdicao: false,
            itemSelecionado: null,
            
            // Formulário
            form: {
                nome: '',
                email: '',
                status: ''
            },
            
            // Validação
            errors: {},
            
            // Estados de modal
            salvando: false,
            excluindo: false,
            itemParaExcluir: null,
            
            // Toast
            toastTitle: '',
            toastMessage: '',
            toastIcon: '',
            
            // Filtros
            filtros: {
                busca: '',
                status: ''
            }
        }
    },
    
    mounted() {
        this.carregarDados();
        this.toast = new bootstrap.Toast(document.getElementById('toast'));
    },
    
    methods: {
        // Métodos principais
        async carregarDados() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.registros.current_page,
                    ...this.filtros
                });
                
                const response = await fetch(`/api/[modulo]/[funcionalidade]?${params}`);
                const data = await response.json();
                
                this.registros = data;
                this.dados = data.data;
                
            } catch (error) {
                console.error('Erro ao carregar dados:', error);
                this.mostrarToast('Erro', 'Erro ao carregar dados', 'fa-exclamation-circle text-danger');
            } finally {
                this.loading = false;
            }
        },
        
        // Filtros
        filtrarDados() {
            this.registros.current_page = 1;
            this.carregarDados();
        },
        
        toggleFiltros() {
            this.filtrosVisiveis = !this.filtrosVisiveis;
        },
        
        // Modal de formulário
        abrirModalCriar() {
            this.modoEdicao = false;
            this.limparFormulario();
            this.funcionalidadeModal.show();
        },
        
        editarItem(item) {
            this.modoEdicao = true;
            this.itemSelecionado = item;
            this.form = { ...item };
            this.funcionalidadeModal.show();
        },
        
        limparFormulario() {
            this.form = {
                nome: '',
                email: '',
                status: ''
            };
            this.errors = {};
        },
        
        // Salvar
        async salvar[Funcionalidade]() {
            this.salvando = true;
            this.errors = {};

            try {
                const url = this.modoEdicao 
                    ? `/api/[modulo]/[funcionalidade]/${this.form.id}`
                    : '/api/[modulo]/[funcionalidade]';
                
                const method = this.modoEdicao ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors;
                        throw new Error('Por favor, corrija os erros no formulário');
                    }
                    throw new Error(data.message || 'Erro ao salvar');
                }

                this.funcionalidadeModal.hide();
                this.mostrarToast('Sucesso', this.modoEdicao ? '[Funcionalidade] atualizado com sucesso!' : '[Funcionalidade] criado com sucesso!', 'fa-check-circle text-success');
                this.carregarDados();
                
            } catch (error) {
                console.error('Erro ao salvar [funcionalidade]:', error);
                this.mostrarToast('Erro', error.message, 'fa-exclamation-circle text-danger');
            } finally {
                this.salvando = false;
            }
        },
        
        // Exclusão
        excluirItem(item) {
            this.itemParaExcluir = item;
            const modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacaoExclusao'));
            modalConfirmacao.show();
        },
        
        async confirmarExclusao() {
            if (!this.itemParaExcluir) return;
            
            this.excluindo = true;
            try {
                const response = await fetch(`/api/[modulo]/[funcionalidade]/${this.itemParaExcluir.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const modalConfirmacao = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacaoExclusao'));
                    modalConfirmacao.hide();
                    
                    this.mostrarToast('Sucesso', '[Funcionalidade] excluído com sucesso!', 'fa-check-circle text-success');
                    this.carregarDados();
                } else {
                    const data = await response.json();
                    this.mostrarToast('Erro', data.error || 'Erro ao excluir', 'fa-exclamation-circle text-danger');
                }
            } catch (error) {
                console.error('Erro:', error);
                this.mostrarToast('Erro', 'Erro ao excluir', 'fa-exclamation-circle text-danger');
            } finally {
                this.excluindo = false;
                this.itemParaExcluir = null;
            }
        },
        
        // Paginação local
        mudarPagina(page) {
            this.registros.current_page = page;
            this.carregarDados();
        },
        
        // Toast
        mostrarToast(title, message, icon) {
            this.toastTitle = title;
            this.toastMessage = message;
            this.toastIcon = icon;
            this.toast.show();
        }
    }
}
</script>
```

---

## 3. Filtros Colapsáveis

### 📋 **Estrutura dos Filtros (Obrigatória)**
```vue
<!-- Filtros Colapsáveis -->
<div class="card mb-4">
    <div class="card-header bg-light py-2">
        <button class="btn btn-link text-decoration-none p-0" @click="toggleFiltros">
            <i class="fas fa-filter me-2"></i>
            {{ filtrosVisiveis ? 'Ocultar' : 'Mostrar' }} Filtros
            <i class="fas" :class="filtrosVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
        </button>
    </div>
    <div class="card-body" v-show="filtrosVisiveis">
        <div class="row g-3">
            <!-- Campo de Busca -->
            <div class="col-md-4">
                <div class="form-floating">
                    <input type="text" 
                           class="form-control" 
                           id="busca" 
                           v-model="filtros.busca"
                           placeholder="Buscar por nome..."
                           @keyup.enter="filtrarDados">
                    <label for="busca">Buscar</label>
                </div>
            </div>
            
            <!-- Filtro de Status -->
            <div class="col-md-3">
                <div class="form-floating">
                    <select class="form-control" id="filtroStatus" v-model="filtros.status">
                        <option value="">Todos</option>
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
                    <label for="filtroStatus">Status</label>
                </div>
            </div>
            
            <!-- Botões de Ação -->
            <div class="col-md-5 d-flex align-items-end gap-2">
                <button class="btn btn-primary" @click="filtrarDados">
                    <i class="fas fa-search me-2"></i>Filtrar
                </button>
                <button class="btn btn-secondary" @click="limparFiltros">
                    <i class="fas fa-times me-2"></i>Limpar
                </button>
            </div>
        </div>
    </div>
</div>
```

**Métodos para Filtros:**
```javascript
methods: {
    limparFiltros() {
        this.filtros = {
            busca: '',
            status: ''
        };
        this.filtrarDados();
    },
    
    filtrarDados() {
        this.registros.current_page = 1;
        this.carregarDados();
    }
}
```

---

## 4. Tabela de Dados

### 📋 **Estrutura da Tabela (Obrigatória)**
```vue
<!-- Tabela de Dados -->
<div class="card">
    <div class="card-body p-0">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-2 text-muted">Carregando dados...</p>
        </div>
        
        <!-- Tabela -->
        <div v-else-if="dados.length > 0" class="table-responsive">
            <table class="table table-hover align-middle usuarios-table">
                <thead>
                    <tr>
                        <th class="fw-semibold text-custom">Nome</th>
                        <th class="fw-semibold text-custom">Email</th>
                        <th class="fw-semibold text-custom">Status</th>
                        <th class="fw-semibold text-end text-custom" style="width: 150px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in dados" :key="item.id" class="usuario-row">
                        <td class="align-middle">
                            <div class="fw-medium">{{ item.nome }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="fw-medium">{{ item.email }}</div>
                        </td>
                        <td class="align-middle">
                            <span class="badge badge-status" :class="item.status === 'ativo' ? 'badge-ativo' : 'badge-inativo'">
                                <i class="fas" :class="item.status === 'ativo' ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                {{ item.status.toUpperCase() }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <!-- Botão Editar: AMARELO SÓLIDO -->
                                <button class="btn btn-sm btn-warning" @click="editarItem(item)" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- Botão Excluir: VERMELHO SÓLIDO -->
                                <button class="btn btn-sm btn-danger" @click="excluirItem(item)" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Estado Vazio -->
        <div v-else class="text-center py-5">
            <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
            <h6 class="mt-3 text-muted">Nenhum registro encontrado</h6>
            <p class="text-muted">Não há [funcionalidades] cadastrados ainda.</p>
        </div>
    </div>
</div>
```

---

## 5. Paginação Local

### 📋 **Estrutura da Paginação (Obrigatória)**
```vue
<!-- Paginação Local -->
<div class="mt-4 pt-3 border-top">
    <nav v-if="registros.last_page > 1" aria-label="Navegação de páginas">
        <ul class="pagination justify-content-center mb-0">
            <!-- Botão Anterior -->
            <li class="page-item" :class="{ disabled: registros.current_page === 1 }">
                <button class="page-link" @click="mudarPagina(registros.current_page - 1)" :disabled="registros.current_page === 1">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </li>
            
            <!-- Páginas -->
            <li v-for="page in getPaginasVisiveis()" :key="page" class="page-item" :class="{ active: page === registros.current_page }">
                <button class="page-link" @click="mudarPagina(page)">
                    {{ page }}
                </button>
            </li>
            
            <!-- Botão Próximo -->
            <li class="page-item" :class="{ disabled: registros.current_page === registros.last_page }">
                <button class="page-link" @click="mudarPagina(registros.current_page + 1)" :disabled="registros.current_page === registros.last_page">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </li>
        </ul>
    </nav>
    
    <!-- Informações da Paginação -->
    <div class="text-center text-muted small mt-2">
        Mostrando {{ ((registros.current_page - 1) * registros.per_page) + 1 }} 
        a {{ Math.min(registros.current_page * registros.per_page, registros.total) }} 
        de {{ registros.total }} registros
    </div>
</div>
```

**Método para Paginação:**
```javascript
methods: {
    getPaginasVisiveis() {
        const total = this.registros.last_page;
        const atual = this.registros.current_page;
        const delta = 2;
        
        let inicio = Math.max(1, atual - delta);
        let fim = Math.min(total, atual + delta);
        
        if (fim - inicio < 4) {
            if (inicio === 1) {
                fim = Math.min(total, inicio + 4);
            } else {
                inicio = Math.max(1, fim - 4);
            }
        }
        
        const paginas = [];
        for (let i = inicio; i <= fim; i++) {
            paginas.push(i);
        }
        
        return paginas;
    }
}
```

---

## 6. Modal de Formulário

### 📋 **Modal de Formulário (Obrigatório)**
```vue
<!-- Modal de Formulário -->
<div class="modal fade" id="modal[Funcionalidade]" tabindex="-1" ref="modal[Funcionalidade]Ref">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Header com Gradiente -->
            <div class="modal-header custom-modal-header">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
                    </div>
                    <h5 class="modal-title mb-0">
                        {{ modoEdicao ? 'Editar [Funcionalidade]' : 'Novo [Funcionalidade]' }}
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body">
                <form @submit.prevent="salvar[Funcionalidade]">
                    <div class="row g-3">
                        <!-- Campo Nome -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control" 
                                       :class="{ 'is-invalid': errors.nome }"
                                       id="nome" 
                                       v-model="form.nome"
                                       placeholder="Nome completo"
                                       required>
                                <label for="nome">Nome Completo</label>
                            </div>
                            <div class="invalid-feedback" v-if="errors.nome">
                                {{ errors.nome[0] }}
                            </div>
                        </div>
                        
                        <!-- Campo Email -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control" 
                                       :class="{ 'is-invalid': errors.email }"
                                       id="email" 
                                       v-model="form.email"
                                       placeholder="Email"
                                       required>
                                <label for="email">Email</label>
                            </div>
                            <div class="invalid-feedback" v-if="errors.email">
                                {{ errors.email[0] }}
                            </div>
                        </div>
                        
                        <!-- Campo Status -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-control" 
                                        :class="{ 'is-invalid': errors.status }"
                                        id="status" 
                                        v-model="form.status" 
                                        required>
                                    <option value="">Selecione...</option>
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                </select>
                                <label for="status">Status</label>
                            </div>
                            <div class="invalid-feedback" v-if="errors.status">
                                {{ errors.status[0] }}
                            </div>
                        </div>
                        
                        <!-- Campo Observações -->
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" 
                                          id="observacoes" 
                                          v-model="form.observacoes"
                                          placeholder="Observações"
                                          style="height: 100px"></textarea>
                                <label for="observacoes">Observações</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Rodapé do Modal -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" @click="salvar[Funcionalidade]" :disabled="salvando">
                    <span v-if="salvando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    <i v-else class="fas fa-save me-2"></i>
                    {{ salvando ? 'Salvando...' : 'Salvar' }}
                </button>
            </div>
        </div>
    </div>
</div>
```

---

## 7. Modal de Confirmação

### 📋 **Modal de Confirmação (Obrigatório)**
```vue
<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalConfirmacaoExclusao" tabindex="-1" ref="modalConfirmacaoRef">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Header com Gradiente -->
            <div class="modal-header custom-modal-header">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h5 class="modal-title mb-0">
                        Confirmar Exclusão
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                    <h6 class="mb-3">Tem certeza que deseja excluir?</h6>
                    <p class="text-muted mb-0">
                        <strong>"{{ itemParaExcluir?.nome }}"</strong> será removido permanentemente.
                    </p>
                    <p class="text-muted small mt-2">
                        Esta ação não pode ser desfeita.
                    </p>
                </div>
            </div>

            <!-- Rodapé do Modal -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" @click="confirmarExclusao" :disabled="excluindo">
                    <span v-if="excluindo" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    <i v-else class="fas fa-trash me-2"></i>
                    {{ excluindo ? 'Excluindo...' : 'Excluir' }}
                </button>
            </div>
        </div>
    </div>
</div>
```

---

## 8. Toast Notifications

### 📋 **Toast de Notificação (Obrigatório)**
```vue
<!-- Toast de Notificação -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i :class="toastIcon + ' me-2'"></i>
            <strong class="me-auto">{{ toastTitle }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            {{ toastMessage }}
        </div>
    </div>
</div>
```

---

## 9. CSS Obrigatório

### 🎨 **CSS para Form-Floating (Obrigatório)**
```css
<style scoped>
/* ===== PADRÃO OFICIAL - FORM-FLOATING ===== */
/* ⚠️ NUNCA ALTERAR ESTAS REGRAS - ELAS GARANTEM FUNCIONAMENTO PERFEITO */
.form-floating .form-control {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
    line-height: 1.5 !important;
    min-height: 58px !important;
}

.form-floating .form-control:focus {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

.form-floating .form-control:not(:placeholder-shown) {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

/* Alinhamento dos campos em linha */
.row.g-3 {
    align-items: end;
}

/* Header personalizado do modal */
.custom-modal-header {
    background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
    color: white;
    border-bottom: none;
    padding: 1.5rem;
    border-radius: 0.5rem 0.5rem 0 0;
}

/* Ícone do header */
.header-icon {
    width: 40px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.2rem;
    color: white;
}

/* Título do modal */
.custom-modal-header .modal-title {
    color: white;
    font-weight: 600;
    font-size: 1.25rem;
}

/* Botão fechar */
.custom-modal-header .btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* Validação */
.form-control.is-valid {
    border-color: #5EA853 !important;
    box-shadow: 0 0 0 0.2rem rgba(94, 168, 83, 0.25) !important;
}

.form-control.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.invalid-feedback {
    color: #dc3545 !important;
    font-size: 0.875rem !important;
}
</style>
```

---

## 10. Checklist de Implementação

### 📋 **Estrutura Base**
- [ ] Componente Vue monolítico criado
- [ ] Header com botão "Novo [Item]" implementado
- [ ] Filtros colapsáveis implementados
- [ ] Tabela responsiva implementada
- [ ] Paginação local implementada

### 📋 **Funcionalidades**
- [ ] Carregamento de dados via API
- [ ] Filtros funcionando
- [ ] Paginação funcionando
- [ ] Modal de formulário implementado
- [ ] Modal de confirmação implementado
- [ ] Toast notifications implementadas

### 📋 **Validação e Estados**
- [ ] Validação visual implementada
- [ ] Estados de loading implementados
- [ ] Estados de erro implementados
- [ ] Estados vazios implementados

### 📋 **Modais**
- [ ] Header com gradiente implementado
- [ ] Form-floating implementado
- [ ] Validação visual implementada
- [ ] Estados de loading implementados
- [ ] Botões padronizados implementados

---

## 11. Conclusão

### 📋 **Resumo dos Padrões para CRUDs Sem Abas**
1. **Estrutura**: Componente Vue monolítico
2. **Paginação**: Local no próprio componente
3. **Filtros**: Colapsáveis por padrão
4. **Modais**: Obrigatórios para criar/editar e confirmar exclusão
5. **Validação**: Visual com estados de erro
6. **Toast**: Notificações para feedback
7. **Responsividade**: Tabela responsiva e layout adaptativo

### 🔗 **Próximos Passos**
- **Para padrões universais**: Consulte `01_padrao_crud.md`
- **Para interfaces com abas**: Consulte `03_padrao_crud_com_abas.md`
- **Para padrões de projeto**: Consulte `docs/diretrizes/01_projeto/`

---

> **IMPORTANTE**: Este documento define padrões para CRUDs sem abas. Para outros tipos de interface, consulte os arquivos correspondentes.
