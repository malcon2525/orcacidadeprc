# Padrão de Interface com Abas - OrçaCidade

> **ESCOPO**: Este documento define padrões visuais para interfaces COM ABAS do projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para manter consistência visual.

> **APLICAÇÃO**: Módulos complexos que precisam de múltiplas funcionalidades organizadas em abas, como "Estrutura de Orçamento".

> **BASE**: Este padrão segue os padrões universais definidos em `02_padrao_layout_interface.md` e usa o padrão CORRETO do `GestaoEstruturaOrcamento.vue`.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Estabelecer padrões visuais para interfaces com abas, garantindo consistência e funcionalidade adequada.

### 🎨 **Características**
- **Múltiplas funcionalidades** organizadas em abas
- **Sistema de abas** com navegação clara
- **Paginação centralizada** no componente pai
- **Componentes filhos** para cada aba
- **Estrutura hierárquica** bem definida

### 📋 **Casos de Uso**
- Módulos administrativos complexos
- Sistemas com múltiplas funcionalidades relacionadas
- Interfaces que precisam de organização por categorias
- Módulos que compartilham dados entre abas

---

## 2. Estrutura Visual Obrigatória

### 📋 **Container Principal (Obrigatório)**
```html
<div class="container-fluid px-4">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <!-- Cabeçalho Compacto -->
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                <i class="fas fa-[icon] me-2"></i>[Título do Módulo]
            </h6>
        </div>

        <!-- Corpo -->
        <div class="card-body">
            <!-- Sistema de Abas -->
            <div class="admin-tabs-container">
                <!-- Navegação das Abas -->
                <ul class="nav nav-tabs admin-tabs" role="tablist">
                    <!-- Abas aqui -->
                </ul>

                <!-- Conteúdo das Abas -->
                <div class="tab-content admin-tab-content">
                    <!-- Conteúdo aqui -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- PAGINAÇÃO FORA DO CARD - OBRIGATÓRIO -->
    <div v-if="paginacao && paginacao.data && paginacao.data.length > 0" class="paginacao-container mt-4">
        <!-- Paginação aqui -->
    </div>
</div>
```

---

## 3. Sistema de Abas

### 🎨 **Navegação das Abas (Obrigatória)**
```html
<ul class="nav nav-tabs admin-tabs" role="tablist">
    <!-- Aba 1 -->
    <li class="nav-item" role="presentation">
        <button class="nav-link admin-tab" 
                :class="{ active: abaAtiva === 'aba1' }"
                @click="changeTab('aba1')"
                type="button"
                role="tab">
            <i class="fas fa-[icon] me-2"></i>
            [Nome da Aba 1]
        </button>
    </li>
    
    <!-- Aba 2 -->
    <li class="nav-item" role="presentation">
        <button class="nav-link admin-tab" 
                :class="{ active: abaAtiva === 'aba2' }"
                @click="changeTab('aba2')"
                type="button"
                role="tab">
            <i class="fas fa-[icon] me-2"></i>
            [Nome da Aba 2]
        </button>
    </li>
    
    <!-- Aba 3 -->
    <li class="nav-item" role="presentation">
        <button class="nav-link admin-tab" 
                :class="{ active: abaAtiva === 'aba3' }"
                @click="changeTab('aba3')"
                type="button"
                role="tab">
            <i class="fas fa-[icon] me-2"></i>
            [Nome da Aba 3]
        </button>
    </li>
</ul>
```

### 🎨 **Conteúdo das Abas (Obrigatório)**
```html
<div class="tab-content admin-tab-content">
    <!-- Aba 1 -->
    <div class="tab-pane fade" 
         :class="{ 'show active': abaAtiva === 'aba1' }" 
         role="tabpanel">
        <componente-aba-1 
            ref="componenteAba1"
            @paginacao-updated="onPaginacaoUpdated">
        </componente-aba-1>
    </div>
    
    <!-- Aba 2 -->
    <div class="tab-pane fade" 
         :class="{ 'show active': abaAtiva === 'aba2' }" 
         role="tabpanel">
        <componente-aba-2 
            ref="componenteAba2"
            @paginacao-updated="onPaginacaoUpdated">
        </componente-aba-2>
    </div>
    
    <!-- Aba 3 -->
    <div class="tab-pane fade" 
         :class="{ 'show active': abaAtiva === 'aba3' }" 
         role="tabpanel">
        <componente-aba-3 
            ref="componenteAba3"
            @paginacao-updated="onPaginacaoUpdated">
        </componente-aba-3>
    </div>
</div>
```

---

## 4. CSS Obrigatório para Abas

### 🎨 **Sistema de Abas (Base)**
```css
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
```

---

## 5. Estrutura de Componentes

### 📋 **Componente Pai (Obrigatório)**
```vue
<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho Compacto -->
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                    <i class="fas fa-chart-pie me-2"></i>Estrutura de Orçamento
                </h6>
            </div>

            <!-- Corpo -->
            <div class="card-body">
                <!-- Sistema de Abas -->
                <div class="admin-tabs-container">
                    <!-- Navegação das Abas -->
                    <ul class="nav nav-tabs admin-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link admin-tab" 
                                    :class="{ active: abaAtiva === 'tipo-orcamento' }"
                                    @click="changeTab('tipo-orcamento')"
                                    type="button"
                                    role="tab">
                                <i class="fas fa-tags me-2"></i>
                                Tipo de Orçamento
                            </button>
                        </li>
                        
                        <li class="nav-item" role="presentation">
                            <button class="nav-link admin-tab" 
                                    :class="{ active: abaAtiva === 'estrutura-orcamento' }"
                                    @click="changeTab('estrutura-orcamento')"
                                    type="button"
                                    role="tab">
                                <i class="fas fa-sitemap me-2"></i>
                                Estrutura de Orçamento
                            </button>
                        </li>
                        
                        <li class="nav-item" role="presentation">
                            <button class="nav-link admin-tab" 
                                    :class="{ active: abaAtiva === 'visualizacao-integrada' }"
                                    @click="changeTab('visualizacao-integrada')"
                                    type="button"
                                    role="tab">
                                <i class="fas fa-eye me-2"></i>
                                Visualização Integrada
                            </button>
                        </li>
                    </ul>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content admin-tab-content">
                        <!-- Aba 1: Tipo de Orçamento -->
                        <div class="tab-pane fade" 
                             :class="{ 'show active': abaAtiva === 'tipo-orcamento' }" 
                             role="tabpanel">
                            <lista-tipo-orcamento 
                                ref="listaTipoOrcamento"
                                @paginacao-updated="onPaginacaoUpdated">
                            </lista-tipo-orcamento>
                        </div>
                        
                        <!-- Aba 2: Estrutura de Orçamento -->
                        <div class="tab-pane fade" 
                             :class="{ 'show active': abaAtiva === 'estrutura-orcamento' }" 
                             role="tabpanel">
                            <lista-estrutura-orcamento 
                                ref="listaEstruturaOrcamento"
                                @paginacao-updated="onPaginacaoUpdated">
                            </lista-estrutura-orcamento>
                        </div>
                        
                        <!-- Aba 3: Visualização Integrada -->
                        <div class="tab-pane fade" 
                             :class="{ 'show active': abaAtiva === 'visualizacao-integrada' }" 
                             role="tabpanel">
                            <visualizacao-integrada 
                                ref="visualizacaoIntegrada"
                                @paginacao-updated="onPaginacaoUpdated">
                            </visualizacao-integrada>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- PAGINAÇÃO FORA DO CARD - OBRIGATÓRIO -->
        <div v-if="paginacao && paginacao.data && paginacao.data.length > 0" class="paginacao-container mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Informações de Registros -->
                <div class="text-muted fw-medium">
                    Mostrando {{ paginacao.from }} até {{ paginacao.to }} de {{ paginacao.total }} registros
                </div>
                
                <!-- Navegação -->
                <nav v-if="paginacao.last_page > 1">
                    <ul class="pagination admin-pagination mb-0">
                        <!-- Botão Anterior -->
                        <li class="page-item" :class="{ disabled: paginacao.current_page === 1 }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(paginacao.current_page - 1)" aria-label="Anterior">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        
                        <!-- Páginas -->
                        <li v-for="pagina in paginasVisiveis" 
                            :key="pagina" 
                            class="page-item" 
                            :class="{ active: pagina === paginacao.current_page }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(pagina)">
                                {{ pagina }}
                            </a>
                        </li>
                        
                        <!-- Botão Próximo -->
                        <li class="page-item" :class="{ disabled: paginacao.current_page === paginacao.last_page }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(paginacao.current_page + 1)" aria-label="Próximo">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>
```

### 📋 **Script do Componente Pai (Obrigatório)**
```javascript
<script>
import ListaTipoOrcamento from './ListaTipoOrcamento.vue'
import ListaEstruturaOrcamento from './ListaEstruturaOrcamento.vue'
import VisualizacaoIntegrada from './VisualizacaoIntegrada.vue'

export default {
    name: 'GestaoEstruturaOrcamento',
    components: {
        ListaTipoOrcamento,
        ListaEstruturaOrcamento,
        VisualizacaoIntegrada
    },
    data() {
        return {
            abaAtiva: 'tipo-orcamento',
            paginacao: null
        }
    },
    computed: {
        paginasVisiveis() {
            if (!this.paginacao) return []
            
            const pages = []
            const maxPages = 5
            let start = Math.max(1, this.paginacao.current_page - Math.floor(maxPages / 2))
            let end = Math.min(this.paginacao.last_page, start + maxPages - 1)

            if (end - start + 1 < maxPages) {
                start = Math.max(1, end - maxPages + 1)
            }

            for (let i = start; i <= end; i++) {
                pages.push(i)
            }

            return pages
        }
    },
    methods: {
        changeTab(aba) {
            this.abaAtiva = aba
            this.paginacao = null
        },
        
        onPaginacaoUpdated(dados) {
            this.paginacao = dados
        },
        
        mudarPagina(pagina) {
            if (this.abaAtiva === 'tipo-orcamento' && this.$refs.listaTipoOrcamento) {
                this.$refs.listaTipoOrcamento.mudarPaginaExterna(pagina)
            } else if (this.abaAtiva === 'estrutura-orcamento' && this.$refs.listaEstruturaOrcamento) {
                this.$refs.listaEstruturaOrcamento.mudarPaginaExterna(pagina)
            } else if (this.abaAtiva === 'visualizacao-integrada' && this.$refs.visualizacaoIntegrada) {
                this.$refs.visualizacaoIntegrada.mudarPaginaExterna(pagina)
            }
        }
    }
}
</script>
```

---

## 6. Componentes Filhos

### 📋 **Estrutura do Componente Filho (Obrigatória)**
```vue
<template>
    <div>
        <!-- Cabeçalho da Aba -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 text-custom">
                <i class="fas fa-[icon] me-2"></i>[Título da Lista]
            </h6>
            <div class="d-flex gap-2">
                <!-- Botão Filtros -->
                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" @click="toggleFiltros">
                    <i class="fas fa-filter"></i>
                    <span>Filtros</span>
                    <i class="fas" :class="filtrosVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <!-- Botão Novo Item -->
                <button class="btn btn-outline-success d-flex align-items-center gap-2 px-3 py-2" @click="abrirModal">
                    <i class="fas fa-plus"></i>
                    <span>Novo [Item]</span>
                </button>
            </div>
        </div>

        <!-- Filtros (Compactos) -->
        <div class="filtros-aba-container mb-3" v-if="filtrosVisiveis">
            <div class="filtros-aba-content" :class="{ 'show': filtrosVisiveis }">
                <div class="row g-3">
                    <!-- Campos de filtro aqui -->
                </div>
            </div>
        </div>

        <!-- Tabela -->
        <div class="table-responsive" v-if="registros.length > 0">
            <table class="table table-hover align-middle usuarios-table">
                <thead>
                    <tr>
                        <th class="fw-semibold text-custom">[Coluna 1]</th>
                        <th class="fw-semibold text-custom">[Coluna 2]</th>
                        <th class="fw-semibold text-custom">[Coluna 3]</th>
                        <th class="fw-semibold text-end text-custom" style="width: 150px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in registros" :key="item.id" class="usuario-row">
                        <td class="align-middle">
                            <div class="fw-medium">{{ item.nome }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="fw-medium">{{ item.descricao }}</div>
                        </td>
                        <td class="align-middle">
                            <span class="badge-status" :class="item.status === 'ativo' ? 'badge-ativo' : 'badge-inativo'">
                                {{ item.status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <!-- Botão Editar: AMARELO SÓLIDO -->
                                <button class="btn btn-sm btn-warning" @click="editar(item)" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- Botão Excluir: VERMELHO SÓLIDO -->
                                <button class="btn btn-sm btn-danger" @click="confirmarExclusao(item)" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Estado Vazio -->
        <div v-if="!loading && registros.length === 0" class="text-center py-5">
            <i class="fas fa-[icon] text-muted mb-3" style="font-size: 3rem;"></i>
            <h6 class="text-muted mb-2">Nenhum [item] encontrado</h6>
            <p class="text-muted small mb-0">Tente ajustar os filtros ou criar um novo [item].</p>
        </div>

        <!-- Paginação Interna (para comunicação com pai) -->
        <div class="mt-4 pt-3 border-top" v-if="registros.length > 0">
            <!-- Esta div é apenas para emitir dados para o pai -->
            <!-- A paginação real fica no componente pai -->
        </div>
    </div>
</template>
```

### 📋 **Script do Componente Filho (Obrigatório)**
```javascript
<script>
export default {
    name: 'ListaTipoOrcamento',
    data() {
        return {
            registros: [],
            loading: false,
            filtrosVisiveis: false,
            // ... outras variáveis
        }
    },
    mounted() {
        this.carregarDados()
    },
    methods: {
        async carregarDados() {
            this.loading = true
            try {
                // ... lógica de carregamento
                // Após carregar, emitir dados para o pai
                this.$emit('paginacao-updated', {
                    data: this.registros,
                    current_page: 1,
                    last_page: 1,
                    from: 1,
                    to: this.registros.length,
                    total: this.registros.length
                })
            } catch (error) {
                console.error('Erro ao carregar dados:', error)
            } finally {
                this.loading = false
            }
        },
        
        mudarPaginaExterna(pagina) {
            // Método chamado pelo componente pai
            this.carregarDados(pagina)
        },
        
        // ... outros métodos
    }
}
</script>
```

---

## 7. Paginação Centralizada

### 📋 **Estrutura da Paginação (Obrigatória)**
```html
<!-- PAGINAÇÃO FORA DO CARD - OBRIGATÓRIO -->
<div v-if="paginacao && paginacao.data && paginacao.data.length > 0" class="paginacao-container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <!-- Informações de Registros -->
        <div class="text-muted fw-medium">
            Mostrando {{ paginacao.from }} até {{ paginacao.to }} de {{ paginacao.total }} registros
        </div>
        
        <!-- Navegação -->
        <nav v-if="paginacao.last_page > 1">
            <ul class="pagination admin-pagination mb-0">
                <!-- Botão Anterior -->
                <li class="page-item" :class="{ disabled: paginacao.current_page === 1 }">
                    <a class="page-link" href="#" @click.prevent="mudarPagina(paginacao.current_page - 1)" aria-label="Anterior">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                
                <!-- Páginas -->
                <li v-for="pagina in paginasVisiveis" 
                    :key="pagina" 
                    class="page-item" 
                    :class="{ active: pagina === paginacao.current_page }">
                    <a class="page-link" href="#" @click.prevent="mudarPagina(pagina)">
                        {{ pagina }}
                    </a>
                </li>
                
                <!-- Botão Próximo -->
                <li class="page-item" :class="{ disabled: paginacao.current_page === paginacao.last_page }">
                    <a class="page-link" href="#" @click.prevent="mudarPagina(paginacao.current_page + 1)" aria-label="Próximo">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
```

**CSS Obrigatório para Paginação:**
```css
/* Paginação admin */
.admin-pagination {
    margin: 0;
}

.admin-pagination .page-link {
    color: #18578A;
    border: 1px solid #e9ecef;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.admin-pagination .page-link:hover {
    background-color: #e3f2fd;
    border-color: #bbdefb;
    color: #1976d2;
}

.admin-pagination .page-item.active .page-link {
    background-color: #18578A;
    border-color: #18578A;
    color: white;
}

.admin-pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #e9ecef;
}
```

---

## 8. Comunicação Entre Componentes

### 📋 **Fluxo de Dados (Obrigatório)**
```javascript
// 1. Componente filho emite dados de paginação
this.$emit('paginacao-updated', {
    data: this.registros,
    current_page: 1,
    last_page: 1,
    from: 1,
    to: this.registros.length,
    total: this.registros.length
})

// 2. Componente pai recebe os dados
onPaginacaoUpdated(dados) {
    this.paginacao = dados
}

// 3. Componente pai chama método do filho
mudarPagina(pagina) {
    if (this.abaAtiva === 'tipo-orcamento' && this.$refs.listaTipoOrcamento) {
        this.$refs.listaTipoOrcamento.mudarPaginaExterna(pagina)
    }
    // ... outras abas
}
```

---

## 9. Responsividade

### 📱 **Breakpoints Obrigatórios**
```css
/* Mobile First */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    .admin-tab-content {
        padding: 1rem !important;
    }
}

@media (max-width: 768px) {
    .admin-tabs {
        flex-direction: column;
        padding: 0;
    }
    
    .admin-tab {
        width: 100%;
        text-align: left;
        border-radius: 0;
    }
    
    .card-body {
        padding: 1rem !important;
    }
}

@media (max-width: 992px) {
    .admin-tab-content {
        padding: 1.5rem !important;
    }
}
```

---

## 10. Checklist de Implementação

### 📋 **Estrutura Base**
- [ ] Container principal com `container-fluid px-4`
- [ ] Card com `shadow-sm border-0 rounded-3 mb-4`
- [ ] Cabeçalho com título verde (`#5EA853`)
- [ ] Sistema de abas com `admin-tabs-container`
- [ ] Paginação FORA do card principal

### 📋 **Sistema de Abas**
- [ ] Navegação com `admin-tabs` e `admin-tab`
- [ ] Conteúdo com `admin-tab-content`
- [ ] Classes CSS obrigatórias implementadas
- [ ] Transições suaves entre abas
- [ ] Estado ativo com borda azul

### 📋 **Componentes**
- [ ] Componente pai gerencia abas e paginação
- [ ] Componentes filhos para cada aba
- [ ] Refs configurados para comunicação
- [ ] Eventos `paginacao-updated` implementados
- [ ] Métodos `mudarPaginaExterna` nos filhos

### 📋 **Paginação**
- [ ] Centralizada no componente pai
- [ ] Dados emitidos pelos filhos
- [ ] Navegação funcional entre páginas
- [ ] Informações de registros visíveis
- [ ] Classes `admin-pagination` aplicadas

### 📋 **Responsividade**
- [ ] Breakpoints implementados
- [ ] Abas empilhadas em mobile
- [ ] Padding adaptativo
- [ ] Layout responsivo para todas as telas

---

## 11. Exemplo Completo

### 📋 **Estrutura Completa de Interface com Abas**
```vue
<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho Compacto -->
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                    <i class="fas fa-chart-pie me-2"></i>Estrutura de Orçamento
                </h6>
            </div>

            <!-- Corpo -->
            <div class="card-body">
                <!-- Sistema de Abas -->
                <div class="admin-tabs-container">
                    <!-- Navegação das Abas -->
                    <ul class="nav nav-tabs admin-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link admin-tab" 
                                    :class="{ active: abaAtiva === 'tipo-orcamento' }"
                                    @click="changeTab('tipo-orcamento')"
                                    type="button"
                                    role="tab">
                                <i class="fas fa-tags me-2"></i>
                                Tipo de Orçamento
                            </button>
                        </li>
                        
                        <li class="nav-item" role="presentation">
                            <button class="nav-link admin-tab" 
                                    :class="{ active: abaAtiva === 'estrutura-orcamento' }"
                                    @click="changeTab('estrutura-orcamento')"
                                    type="button"
                                    role="tab">
                                <i class="fas fa-sitemap me-2"></i>
                                Estrutura de Orçamento
                            </button>
                        </li>
                        
                        <li class="nav-item" role="presentation">
                            <button class="nav-link admin-tab" 
                                    :class="{ active: abaAtiva === 'visualizacao-integrada' }"
                                    @click="changeTab('visualizacao-integrada')"
                                    type="button"
                                    role="tab">
                                <i class="fas fa-eye me-2"></i>
                                Visualização Integrada
                            </button>
                        </li>
                    </ul>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content admin-tab-content">
                        <!-- Aba 1: Tipo de Orçamento -->
                        <div class="tab-pane fade" 
                             :class="{ 'show active': abaAtiva === 'tipo-orcamento' }" 
                             role="tabpanel">
                            <lista-tipo-orcamento 
                                ref="listaTipoOrcamento"
                                @paginacao-updated="onPaginacaoUpdated">
                            </lista-tipo-orcamento>
                        </div>
                        
                        <!-- Aba 2: Estrutura de Orçamento -->
                        <div class="tab-pane fade" 
                             :class="{ 'show active': abaAtiva === 'estrutura-orcamento' }" 
                             role="tabpanel">
                            <lista-estrutura-orcamento 
                                ref="listaEstruturaOrcamento"
                                @paginacao-updated="onPaginacaoUpdated">
                            </lista-estrutura-orcamento>
                        </div>
                        
                        <!-- Aba 3: Visualização Integrada -->
                        <div class="tab-pane fade" 
                             :class="{ 'show active': abaAtiva === 'visualizacao-integrada' }" 
                             role="tabpanel">
                            <visualizacao-integrada 
                                ref="visualizacaoIntegrada"
                                @paginacao-updated="onPaginacaoUpdated">
                            </visualizacao-integrada>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- PAGINAÇÃO FORA DO CARD - OBRIGATÓRIO -->
        <div v-if="paginacao && paginacao.data && paginacao.data.length > 0" class="paginacao-container mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Informações de Registros -->
                <div class="text-muted fw-medium">
                    Mostrando {{ paginacao.from }} até {{ paginacao.to }} de {{ paginacao.total }} registros
                </div>
                
                <!-- Navegação -->
                <nav v-if="paginacao.last_page > 1">
                    <ul class="pagination admin-pagination mb-0">
                        <!-- Botão Anterior -->
                        <li class="page-item" :class="{ disabled: paginacao.current_page === 1 }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(paginacao.current_page - 1)" aria-label="Anterior">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        
                        <!-- Páginas -->
                        <li v-for="pagina in paginasVisiveis" 
                            :key="pagina" 
                            class="page-item" 
                            :class="{ active: pagina === paginacao.current_page }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(pagina)">
                                {{ pagina }}
                            </a>
                        </li>
                        
                        <!-- Botão Próximo -->
                        <li class="page-item" :class="{ disabled: paginacao.current_page === paginacao.last_page }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(paginacao.current_page + 1)" aria-label="Próximo">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
import ListaTipoOrcamento from './ListaTipoOrcamento.vue'
import ListaEstruturaOrcamento from './ListaEstruturaOrcamento.vue'
import VisualizacaoIntegrada from './VisualizacaoIntegrada.vue'

export default {
    name: 'GestaoEstruturaOrcamento',
    components: {
        ListaTipoOrcamento,
        ListaEstruturaOrcamento,
        VisualizacaoIntegrada
    },
    data() {
        return {
            abaAtiva: 'tipo-orcamento',
            paginacao: null
        }
    },
    computed: {
        paginasVisiveis() {
            if (!this.paginacao) return []
            
            const pages = []
            const maxPages = 5
            let start = Math.max(1, this.paginacao.current_page - Math.floor(maxPages / 2))
            let end = Math.min(this.paginacao.last_page, start + maxPages - 1)

            if (end - start + 1 < maxPages) {
                start = Math.max(1, end - maxPages + 1)
            }

            for (let i = start; i <= end; i++) {
                pages.push(i)
            }

            return pages
        }
    },
    methods: {
        changeTab(aba) {
            this.abaAtiva = aba
            this.paginacao = null
        },
        
        onPaginacaoUpdated(dados) {
            this.paginacao = dados
        },
        
        mudarPagina(pagina) {
            if (this.abaAtiva === 'tipo-orcamento' && this.$refs.listaTipoOrcamento) {
                this.$refs.listaTipoOrcamento.mudarPaginaExterna(pagina)
            } else if (this.abaAtiva === 'estrutura-orcamento' && this.$refs.listaEstruturaOrcamento) {
                this.$refs.listaEstruturaOrcamento.mudarPaginaExterna(pagina)
            } else if (this.abaAtiva === 'visualizacao-integrada' && this.$refs.visualizacaoIntegrada) {
                this.$refs.visualizacaoIntegrada.mudarPaginaExterna(pagina)
            }
        }
    }
}
</script>

<style>
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

/* Paginação */
.admin-pagination {
    margin: 0;
}

.admin-pagination .page-link {
    color: #18578A;
    border: 1px solid #e9ecef;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.admin-pagination .page-link:hover {
    background-color: #e3f2fd;
    border-color: #bbdefb;
    color: #1976d2;
}

.admin-pagination .page-item.active .page-link {
    background-color: #18578A;
    border-color: #18578A;
    color: white;
}

.admin-pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #e9ecef;
}

/* Responsividade */
@media (max-width: 576px) {
    .admin-tab-content {
        padding: 1rem !important;
    }
}

@media (max-width: 768px) {
    .admin-tabs {
        flex-direction: column;
        padding: 0;
    }
    
    .admin-tab {
        width: 100%;
        text-align: left;
        border-radius: 0;
    }
    
    .card-body {
        padding: 1rem !important;
    }
}

@media (max-width: 992px) {
    .admin-tab-content {
        padding: 1.5rem !important;
    }
}
</style>
```

---

## 12. Conclusão

### 📋 **Resumo dos Padrões de Interface com Abas**
1. **Estrutura**: Container principal com sistema de abas
2. **Sistema de Abas**: Navegação clara com transições suaves
3. **Componentes**: Pai gerencia abas, filhos gerenciam conteúdo
4. **Paginação**: Centralizada no componente pai
5. **Comunicação**: Eventos e refs para comunicação entre componentes
6. **Responsividade**: Breakpoints para todas as telas

### 🔗 **Próximos Passos**
- **Para interfaces simples**: Consulte `05_padrao_interface_simples.md`
- **Para modais**: Consulte `07_padrao_modais.md`
- **Para padrões universais**: Consulte `02_padrao_layout_interface.md`

---

> **IMPORTANTE**: Este documento define padrões para interfaces COM ABAS. Para interfaces simples, consulte o arquivo correspondente.
