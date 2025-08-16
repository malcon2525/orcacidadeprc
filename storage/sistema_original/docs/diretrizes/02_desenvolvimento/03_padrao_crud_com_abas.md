# Padrão CRUD Com Abas - OrçaCidade

> **ESCOPO**: Este documento define padrões para CRUDs COM abas no projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para manter consistência visual.

> **APLICAÇÃO**: Interfaces complexas como gestão de estrutura orçamentária, configurações avançadas, etc.

> **BASE**: Este padrão segue os padrões universais definidos em `01_padrao_crud.md`.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Estabelecer padrões para CRUDs com abas, garantindo consistência e funcionalidade adequada em todo o sistema.

### 🎨 **Características**
- **Estrutura parent-child** (componente pai gerencia abas e paginação)
- **Paginação centralizada** no componente pai
- **Tabs management** com navegação entre funcionalidades
- **Componentes filhos** para cada aba específica
- **Estado compartilhado** entre abas quando necessário

### 📋 **Exemplos de Uso**
- GestaoEstruturaOrcamento.vue (pai)
  - ListaTipoOrcamento.vue (aba 1)
  - EstruturaOrcamento.vue (aba 2)
  - VisualizacaoIntegrada.vue (aba 3)

---

## 2. Estrutura de Componentes

### 📋 **Componente Pai (Obrigatório)**
```vue
<template>
    <div>
        <!-- Navegação de Abas -->
        <div class="admin-tabs-container">
            <ul class="nav admin-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link admin-tab active" 
                            @click="changeTab(1)" 
                            :class="{ active: abaAtiva === 1 }"
                            type="button" 
                            role="tab">
                        <i class="fas fa-list me-2"></i>Tipo de Orçamento
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link admin-tab" 
                            @click="changeTab(2)" 
                            :class="{ active: abaAtiva === 2 }"
                            type="button" 
                            role="tab">
                        <i class="fas fa-sitemap me-2"></i>Estrutura de Orçamento
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link admin-tab" 
                            @click="changeTab(3)" 
                            :class="{ active: abaAtiva === 3 }"
                            type="button" 
                            role="tab">
                        <i class="fas fa-eye me-2"></i>Visualização Integrada
                    </button>
                </li>
            </ul>
        </div>

        <!-- Conteúdo das Abas -->
        <div class="admin-tab-content">
            <!-- Aba 1: Tipo de Orçamento -->
            <div v-show="abaAtiva === 1" class="tab-pane">
                <lista-tipo-orcamento 
                    ref="listaTipoOrcamentoRef"
                    @paginacao-updated="onPaginacaoUpdated">
                </lista-tipo-orcamento>
            </div>

            <!-- Aba 2: Estrutura de Orçamento -->
            <div v-show="abaAtiva === 2" class="tab-pane">
                <estrutura-orcamento 
                    ref="estruturaOrcamentoRef">
                </estrutura-orcamento>
            </div>

            <!-- Aba 3: Visualização Integrada -->
            <div v-show="abaAtiva === 3" class="tab-pane">
                <visualizacao-integrada 
                    ref="visualizacaoIntegradaRef">
                </visualizacao-integrada>
            </div>
        </div>

        <!-- Paginação Centralizada -->
        <div v-if="paginacao && paginacao.last_page > 1" class="mt-4 pt-3 border-top">
            <nav aria-label="Navegação de páginas">
                <ul class="pagination justify-content-center mb-0">
                    <!-- Botão Anterior -->
                    <li class="page-item" :class="{ disabled: paginacao.current_page === 1 }">
                        <button class="page-link" @click="mudarPagina(paginacao.current_page - 1)" :disabled="paginacao.current_page === 1">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </li>
                    
                    <!-- Páginas -->
                    <li v-for="page in getPaginasVisiveis()" :key="page" class="page-item" :class="{ active: page === paginacao.current_page }">
                        <button class="page-link" @click="mudarPagina(page)">
                            {{ page }}
                        </button>
                    </li>
                    
                    <!-- Botão Próximo -->
                    <li class="page-item" :class="{ disabled: paginacao.current_page === paginacao.last_page }">
                        <button class="page-link" @click="mudarPagina(paginacao.current_page + 1)" :disabled="paginacao.current_page === paginacao.last_page">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </li>
                </ul>
            </nav>
            
            <!-- Informações da Paginação -->
            <div class="text-center text-muted small mt-2">
                Mostrando {{ ((paginacao.current_page - 1) * paginacao.per_page) + 1 }} 
                a {{ Math.min(paginacao.current_page * paginacao.per_page, paginacao.total) }} 
                de {{ paginacao.total }} registros
            </div>
        </div>
    </div>
</template>

<script>
import ListaTipoOrcamento from './ListaTipoOrcamento.vue'
import EstruturaOrcamento from './EstruturaOrcamento.vue'
import VisualizacaoIntegrada from './VisualizacaoIntegrada.vue'

export default {
    name: 'GestaoEstruturaOrcamento',
    components: {
        ListaTipoOrcamento,
        EstruturaOrcamento,
        VisualizacaoIntegrada
    },
    
    data() {
        return {
            abaAtiva: 1,
            paginacao: null
        }
    },
    
    methods: {
        changeTab(aba) {
            this.abaAtiva = aba;
            // Reset da paginação ao trocar de aba
            this.paginacao = null;
        },
        
        onPaginacaoUpdated(paginacaoData) {
            this.paginacao = paginacaoData;
        },
        
        mudarPagina(page) {
            if (this.abaAtiva === 1 && this.$refs.listaTipoOrcamentoRef) {
                this.$refs.listaTipoOrcamentoRef.mudarPaginaExterna(page);
            }
            // Adicionar outras abas conforme necessário
        },
        
        getPaginasVisiveis() {
            if (!this.paginacao) return [];
            
            const total = this.paginacao.last_page;
            const atual = this.paginacao.current_page;
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
}
</script>
```

---

## 3. Componente Filho (Aba Específica)

### 📋 **Estrutura do Componente Filho (Obrigatória)**
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
                descricao: '',
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
                
                // EMITIR para o componente pai
                this.$emit('paginacao-updated', this.registros);
                
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
                descricao: '',
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
        
        // Método para controle externo da paginação
        mudarPaginaExterna(page) {
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

## 4. Navegação de Abas

### 📋 **Estrutura das Abas (Obrigatória)**
```vue
<!-- Navegação de Abas -->
<div class="admin-tabs-container">
    <ul class="nav admin-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link admin-tab" 
                    @click="changeTab(1)" 
                    :class="{ active: abaAtiva === 1 }"
                    type="button" 
                    role="tab">
                <i class="fas fa-list me-2"></i>Tipo de Orçamento
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link admin-tab" 
                    @click="changeTab(2)" 
                    :class="{ active: abaAtiva === 2 }"
                    type="button" 
                    role="tab">
                <i class="fas fa-sitemap me-2"></i>Estrutura de Orçamento
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link admin-tab" 
                    @click="changeTab(3)" 
                    :class="{ active: abaAtiva === 3 }"
                    type="button" 
                    role="tab">
                <i class="fas fa-eye me-2"></i>Visualização Integrada
            </button>
        </li>
    </ul>
</div>
```

**Método para Troca de Abas:**
```javascript
methods: {
    changeTab(aba) {
        this.abaAtiva = aba;
        // Reset da paginação ao trocar de aba
        this.paginacao = null;
        
        // Lógica adicional se necessário
        if (aba === 2) {
            // Carregar dados específicos da aba 2
            this.carregarDadosAba2();
        }
    }
}
```

---

## 5. Paginação Centralizada

### 📋 **Paginação no Componente Pai (Obrigatória)**
```vue
<!-- Paginação Centralizada -->
<div v-if="paginacao && paginacao.last_page > 1" class="mt-4 pt-3 border-top">
    <nav aria-label="Navegação de páginas">
        <ul class="pagination justify-content-center mb-0">
            <!-- Botão Anterior -->
            <li class="page-item" :class="{ disabled: paginacao.current_page === 1 }">
                <button class="page-link" @click="mudarPagina(paginacao.current_page - 1)" :disabled="paginacao.current_page === 1">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </li>
            
            <!-- Páginas -->
            <li v-for="page in getPaginasVisiveis()" :key="page" class="page-item" :class="{ active: page === paginacao.current_page }">
                <button class="page-link" @click="mudarPagina(page)">
                    {{ page }}
                </button>
            </li>
            
            <!-- Botão Próximo -->
            <li class="page-item" :class="{ disabled: paginacao.current_page === paginacao.last_page }">
                <button class="page-link" @click="mudarPagina(paginacao.current_page + 1)" :disabled="paginacao.current_page === paginacao.last_page">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </li>
        </ul>
    </nav>
    
    <!-- Informações da Paginação -->
    <div class="text-center text-muted small mt-2">
        Mostrando {{ ((paginacao.current_page - 1) * paginacao.per_page) + 1 }} 
        a {{ Math.min(paginacao.current_page * paginacao.per_page, paginacao.total) }} 
        de {{ paginacao.total }} registros
    </div>
</div>
```

**Métodos para Paginação Centralizada:**
```javascript
methods: {
    onPaginacaoUpdated(paginacaoData) {
        this.paginacao = paginacaoData;
    },
    
    mudarPagina(page) {
        if (this.abaAtiva === 1 && this.$refs.listaTipoOrcamentoRef) {
            this.$refs.listaTipoOrcamentoRef.mudarPaginaExterna(page);
        }
        // Adicionar outras abas conforme necessário
        if (this.abaAtiva === 2 && this.$refs.estruturaOrcamentoRef) {
            this.$refs.estruturaOrcamentoRef.mudarPaginaExterna(page);
        }
    },
    
    getPaginasVisiveis() {
        if (!this.paginacao) return [];
        
        const total = this.paginacao.last_page;
        const atual = this.paginacao.current_page;
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

## 6. Comunicação Entre Componentes

### 📋 **Eventos e Refs (Obrigatórios)**
```vue
<!-- No componente pai -->
<lista-tipo-orcamento 
    ref="listaTipoOrcamentoRef"
    @paginacao-updated="onPaginacaoUpdated">
</lista-tipo-orcamento>

<!-- No componente filho -->
<script>
export default {
    methods: {
        async carregarDados() {
            // ... lógica de carregamento
            
            // EMITIR para o componente pai
            this.$emit('paginacao-updated', this.registros);
        },
        
        // Método para controle externo da paginação
        mudarPaginaExterna(page) {
            this.registros.current_page = page;
            this.carregarDados();
        }
    }
}
</script>
```

---

## 7. CSS para Abas

### 🎨 **CSS das Abas (Obrigatório)**
```css
<style scoped>
/* Sistema de Abas */
.admin-tabs-container {
    background-color: #ffffff;
    border-radius: 0.5rem;
    overflow: visible;
}

.admin-tabs {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 0 1rem;
    margin: 0;
}

.admin-tab {
    border: none;
    background: transparent;
    color: #6c757d;
    font-weight: 500;
    padding: 0.75rem 1.25rem;
    transition: all 0.3s ease;
    position: relative;
    font-size: 0.9rem;
}

.admin-tab:hover {
    color: #18578A;
    background-color: rgba(24, 87, 138, 0.1);
}

.admin-tab.active {
    color: #18578A;
    background-color: #ffffff;
    border-bottom: 3px solid #18578A;
    box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
}

.admin-tab-content {
    padding: 2rem;
    min-height: auto;
}

/* Conteúdo das abas */
.tab-pane {
    display: block;
}
</style>
```

---

## 8. Checklist de Implementação

### 📋 **Componente Pai**
- [ ] Navegação de abas implementada
- [ ] Componentes filhos importados e registrados
- [ ] Sistema de refs implementado
- [ ] Paginação centralizada implementada
- [ ] Métodos de controle de abas implementados

### 📋 **Componentes Filhos**
- [ ] Estrutura base implementada
- [ ] Eventos emitidos para o pai
- [ ] Método `mudarPaginaExterna` implementado
- [ ] Comunicação com componente pai funcionando

### 📋 **Paginação Centralizada**
- [ ] Estado de paginação no componente pai
- [ ] Eventos de atualização funcionando
- [ ] Controle de paginação entre abas funcionando
- [ ] Informações de paginação exibidas corretamente

### 📋 **Navegação**
- [ ] Troca de abas funcionando
- [ ] Estados ativos das abas funcionando
- [ ] Reset de paginação ao trocar de aba
- [ ] CSS das abas implementado

---

## 9. Conclusão

### 📋 **Resumo dos Padrões para CRUDs Com Abas**
1. **Estrutura**: Parent-child com componentes separados
2. **Paginação**: Centralizada no componente pai
3. **Navegação**: Sistema de abas com estados ativos
4. **Comunicação**: Eventos e refs entre componentes
5. **Modularidade**: Cada aba em componente separado
6. **Estado**: Compartilhado quando necessário
7. **Responsividade**: Layout adaptativo para todas as telas

### 🔗 **Próximos Passos**
- **Para padrões universais**: Consulte `01_padrao_crud.md`
- **Para interfaces sem abas**: Consulte `02_padrao_crud_sem_abas.md`
- **Para padrões de projeto**: Consulte `docs/diretrizes/01_projeto/`

---

> **IMPORTANTE**: Este documento define padrões para CRUDs com abas. Para outros tipos de interface, consulte os arquivos correspondentes.
