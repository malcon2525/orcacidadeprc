# Padrão de Interface Simples - OrçaCidade

> **ESCOPO**: Este documento define padrões visuais para interfaces SIMPLES (sem abas) do projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para manter consistência visual.

> **APLICAÇÃO**: CRUDs simples, listas únicas, formulários isolados, funcionalidades que não precisam de abas.

> **BASE**: Este padrão segue os padrões universais definidos em `02_padrao_layout_interface.md`.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Estabelecer padrões visuais para interfaces simples (sem abas), garantindo consistência com o resto do sistema.

### 🎨 **Características**
- **Uma funcionalidade** por tela
- **Sem sistema de abas**
- **Layout direto** e objetivo
- **Paginação local** no componente
- **Estrutura simplificada**

### 📋 **Casos de Uso**
- CRUD de entidade única
- Lista simples de registros
- Formulário isolado
- Dashboard simples
- Relatório único

---

## 2. Estrutura Visual Obrigatória

### 📋 **Container Principal (Obrigatório)**
```html
<div class="container-fluid px-4">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <!-- Cabeçalho Compacto -->
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                <i class="fas fa-[icon] me-2"></i>[Título da Funcionalidade]
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

        <!-- Corpo -->
        <div class="card-body">
            <!-- Filtros Colapsáveis -->
            <div class="filtros-aba-container mb-3" v-if="filtrosVisiveis">
                <div class="filtros-aba-content" :class="{ 'show': filtrosVisiveis }">
                    <div class="row g-3 align-items-end">
                        <!-- Campos de filtro aqui -->
                    </div>
                </div>
            </div>

            <!-- Conteúdo Principal -->
            <!-- Tabela, formulário ou outro conteúdo aqui -->
        </div>
    </div>
    
    <!-- PAGINAÇÃO FORA DO CARD - OBRIGATÓRIO -->
    <div v-if="dados.length > 0" class="paginacao-container mt-4">
        <!-- Paginação aqui -->
    </div>
</div>
```

---

## 3. Cabeçalho da Funcionalidade

### 🎨 **Título Principal (Obrigatório)**
```html
<h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
    <i class="fas fa-[icon] me-2"></i>[Título da Funcionalidade]
</h6>
```

**Características Obrigatórias:**
- ✅ **Cor**: `#5EA853` (verde principal)
- ✅ **Tamanho**: `font-size: 1.2rem`
- ✅ **Padding**: `padding: 5px 0`
- ✅ **Peso**: `font-weight: 600`
- ✅ **Ícone**: Font Awesome apropriado

### 🎨 **Botões de Ação (Obrigatórios)**
```html
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
```

**Características Obrigatórias:**
- ✅ **Filtros**: `btn-outline-secondary` com ícone de filtro
- ✅ **Novo Item**: `btn-outline-success` com ícone de plus
- ✅ **Tamanho**: `btn-sm` para filtros, tamanho normal para novo item
- ✅ **Ícones**: Font Awesome apropriados
- ✅ **Espaçamento**: `gap-2` entre botões

---

## 4. Filtros Colapsáveis

### 🎨 **Estrutura dos Filtros (Obrigatória)**
```html
<div class="filtros-aba-container mb-3" v-if="filtrosVisiveis">
    <div class="filtros-aba-content" :class="{ 'show': filtrosVisiveis }">
        <div class="row g-3 align-items-end">
            <!-- Campo de Filtro -->
            <div class="col-md-4">
                <div class="form-floating">
                    <input type="text" 
                           class="form-control form-control-lg" 
                           id="filtroNome" 
                           v-model="filtros.nome"
                           placeholder="Nome...">
                    <label for="filtroNome">Nome</label>
                </div>
            </div>
            
            <!-- Botão Limpar (MESMA ALTURA DOS CAMPOS) -->
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-outline-secondary w-100" 
                        style="height: 58px; line-height: 1.5;" 
                        @click="limparFiltros">
                    <i class="fas fa-times me-2"></i>Limpar Filtros
                </button>
            </div>
        </div>
    </div>
</div>
```

**CSS Obrigatório para Filtros:**
```css
/* Container de filtros */
.filtros-aba-container {
    background-color: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    padding: 1rem;
}

.filtros-aba-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.filtros-aba-content.show {
    max-height: 200px;
}

/* Campos de filtro - ALTURA CORRETA */
.form-floating .form-control-lg {
    height: 58px !important;
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

.form-floating .form-control-lg:focus {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

.form-floating .form-control-lg:not(:placeholder-shown) {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}
```

**Regras Críticas para Filtros:**
- ✅ **SEMPRE usar** `form-control-lg` nos campos de filtro
- ✅ **SEMPRE definir** altura de 58px no botão limpar
- ✅ **SEMPRE usar** `align-items-end` na row para alinhar botão
- ✅ **SEMPRE usar** `w-100` no botão para largura total da coluna
- ✅ **SEMPRE usar** fundo branco (`#ffffff`) para filtros

---

## 5. Conteúdo Principal

### 📋 **Tabela (Quando Aplicável)**
```html
<div class="table-responsive" v-if="dados.length > 0">
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
            <tr v-for="item in dados" :key="item.id" class="usuario-row">
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
```

**CSS Obrigatório para Tabelas:**
```css
/* Tabela padrão - REUTILIZAR SEMPRE */
.usuarios-table {
    font-size: 14px;
    font-weight: 400;
    line-height: 1.5;
    table-layout: fixed;
}

/* Cabeçalhos da tabela */
.usuarios-table th {
    font-weight: 600;
    font-size: 14px;
    text-transform: none;
    letter-spacing: normal;
    color: #18578A;
    padding: 0.75rem 0.5rem;
    border-bottom: 2px solid #e5e7eb;
    background-color: #f9fafb;
    vertical-align: middle;
}

/* Células da tabela */
.usuarios-table td {
    padding: 0.5rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f4;
    font-weight: 400;
    font-size: 14px;
}

/* Linhas da tabela - REUTILIZAR SEMPRE */
.usuario-row {
    background-color: #ffffff;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
    cursor: default;
}

/* Hover da linha */
.usuario-row:hover {
    background-color: #f8f9fa;
    border-left: 3px solid #5EA853;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
```

**Regras Críticas para Tabelas:**
- ✅ **SEMPRE usar** `usuarios-table` para todas as tabelas
- ✅ **SEMPRE usar** `usuario-row` para todas as linhas
- ✅ **SEMPRE usar** `text-custom` para cabeçalhos
- ✅ **SEMPRE usar** `btn btn-sm btn-warning` para botão editar
- ✅ **SEMPRE usar** `btn btn-sm btn-danger` para botão excluir
- ✅ **SEMPRE usar** `d-flex gap-1 justify-content-end` para container dos botões

---

## 6. Estados de Interface

### 📱 **Estado de Carregamento (Loading)**
```html
<div v-if="loading" class="text-center py-4">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
    <p class="mt-2 text-muted">Carregando [itens]...</p>
</div>
```

### 📱 **Estado Vazio (Sem Dados)**
```html
<div v-if="!loading && dados.length === 0" class="text-center py-5">
    <i class="fas fa-[icon] text-muted mb-3" style="font-size: 3rem;"></i>
    <h6 class="text-muted mb-2">Nenhum [item] encontrado</h6>
    <p class="text-muted small mb-0">Tente ajustar os filtros ou criar um novo [item].</p>
</div>
```

### 📱 **Estado de Erro**
```html
<div v-if="erro" class="alert alert-danger" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Erro:</strong> {{ erro }}
</div>
```

---

## 7. Paginação Local

### 📋 **Estrutura da Paginação (Obrigatória)**
```html
<!-- PAGINAÇÃO FORA DO CARD - OBRIGATÓRIO -->
<div v-if="dados.length > 0" class="paginacao-container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <!-- Informações de Registros -->
        <div class="text-muted fw-medium">
            Mostrando {{ (paginaAtual - 1) * itensPorPagina + 1 }} até {{ Math.min(paginaAtual * itensPorPagina, dados.length) }} de {{ dados.length }} registros
        </div>
        
        <!-- Navegação -->
        <nav v-if="totalPaginas > 1">
            <ul class="pagination admin-pagination mb-0">
                <!-- Botão Anterior -->
                <li class="page-item" :class="{ disabled: paginaAtual === 1 }">
                    <a class="page-link" href="#" @click.prevent="mudarPagina(paginaAtual - 1)" aria-label="Anterior">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                
                <!-- Páginas -->
                <li v-for="pagina in paginasVisiveis" 
                    :key="pagina" 
                    class="page-item" 
                    :class="{ active: pagina === paginaAtual }">
                    <a class="page-link" href="#" @click.prevent="mudarPagina(pagina)">
                        {{ pagina }}
                    </a>
                </li>
                
                <!-- Botão Próximo -->
                <li class="page-item" :class="{ disabled: paginaAtual === totalPaginas }">
                    <a class="page-link" href="#" @click.prevent="mudarPagina(paginaAtual + 1)" aria-label="Próximo">
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

**Computed Property Obrigatória:**
```javascript
computed: {
    paginasVisiveis() {
        const pages = [];
        const maxPages = 5;
        let start = Math.max(1, this.paginaAtual - Math.floor(maxPages / 2));
        let end = Math.min(this.totalPaginas, start + maxPages - 1);

        if (end - start + 1 < maxPages) {
            start = Math.max(1, end - maxPages + 1);
        }

        for (let i = start; i <= end; i++) {
            pages.push(i);
        }

        return pages;
    },
    
    totalPaginas() {
        return Math.ceil(this.dados.length / this.itensPorPagina);
    }
}
```

**Regras Críticas para Paginação:**
- ✅ **SEMPRE colocar** paginação FORA do card principal
- ✅ **SEMPRE usar** `admin-pagination` para estilos
- ✅ **SEMPRE mostrar** informações de registros
- ✅ **SEMPRE implementar** navegação entre páginas
- ✅ **SEMPRE usar** ícones Font Awesome para setas

---

## 8. Badges e Status

### 🎨 **Badges DISCRETOS (Obrigatório)**
```html
<!-- Status Ativo -->
<span class="badge badge-status badge-ativo">
    <i class="fas fa-check-circle"></i>
    ATIVO
</span>

<!-- Status Inativo -->
<span class="badge badge-status badge-inativo">
    <i class="fas fa-times-circle"></i>
    INATIVO
</span>
```

**CSS Obrigatório para Badges:**
```css
/* Badge de status - PADRÃO DISCRETO */
.badge-status {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 0.25rem;
    border: 1px solid;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    transition: all 0.2s ease;
}

/* Status ativo - Verde discreto com gradiente sutil */
.badge-ativo {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    color: #0369a1;
    border-color: #bae6fd;
}

.badge-ativo:hover {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(3, 105, 161, 0.15);
}

/* Status inativo - Vermelho discreto com gradiente sutil */
.badge-inativo {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    color: #dc2626;
    border-color: #fecaca;
}

.badge-inativo:hover {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.15);
}
```

**Regras Críticas para Badges:**
- ✅ **SEMPRE usar** badges discretos (NÃO sólidos Bootstrap)
- ✅ **SEMPRE usar** `badge-status` como classe base
- ✅ **SEMPRE usar** `badge-ativo` para status positivo
- ✅ **SEMPRE usar** `badge-inativo` para status negativo
- ✅ **SEMPRE incluir** ícones Font Awesome apropriados
- ✅ **SEMPRE usar** texto em maiúsculo (ATIVO, INATIVO)

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
    
    .card-body {
        padding: 1rem !important;
    }
}

@media (max-width: 768px) {
    .filtros-aba-container .row {
        flex-direction: column;
    }
    
    .filtros-aba-container .col-md-4 {
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .table-responsive {
        font-size: 13px;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 992px) {
    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .d-flex.gap-2 {
        width: 100%;
        justify-content: stretch;
    }
}
```

---

## 10. Checklist de Implementação

### 📋 **Estrutura Base**
- [ ] Container principal com `container-fluid px-4`
- [ ] Card com `shadow-sm border-0 rounded-3 mb-4`
- [ ] Cabeçalho com título verde (`#5EA853`) e botões de ação
- [ ] Corpo do card com filtros e conteúdo
- [ ] Paginação FORA do card principal

### 📋 **Cabeçalho**
- [ ] Título com cor `#5EA853`, `font-size: 1.2rem`, `padding: 5px 0`
- [ ] Botão filtros com `btn-outline-secondary` e ícone
- [ ] Botão novo item com `btn-outline-success` e ícone
- [ ] Espaçamento correto entre botões (`gap-2`)

### 📋 **Filtros**
- [ ] Container com fundo branco (`#ffffff`)
- [ ] Campos com `form-control-lg` e altura 58px
- [ ] Botão limpar com altura 58px
- [ ] Transição suave ao mostrar/ocultar
- [ ] Alinhamento correto dos elementos (`align-items-end`)

### 📋 **Tabela**
- [ ] Classes obrigatórias: `usuarios-table`, `usuario-row`
- [ ] Cabeçalhos com `text-custom` (cor `#18578A`)
- [ ] Botões de ação: `btn-warning` (editar) e `btn-danger` (excluir)
- [ ] Container dos botões: `d-flex gap-1 justify-content-end`
- [ ] Hover das linhas com borda esquerda verde

### 📋 **Paginação**
- [ ] Posicionada FORA do card principal
- [ ] Classes obrigatórias: `admin-pagination`
- [ ] Informações de registros visíveis
- [ ] Navegação entre páginas funcional
- [ ] Ícones Font Awesome para setas

### 📋 **Badges**
- [ ] Badges discretos (NÃO sólidos Bootstrap)
- [ ] Classes obrigatórias: `badge-status`, `badge-ativo`, `badge-inativo`
- [ ] Ícones Font Awesome apropriados
- [ ] Texto em maiúsculo (ATIVO, INATIVO)

### 📋 **Responsividade**
- [ ] Breakpoints implementados
- [ ] Layout adaptativo para mobile
- [ ] Botões empilhados em telas pequenas
- [ ] Tabela com scroll horizontal em mobile

---

## 11. Exemplo Completo

### 📋 **Estrutura Completa de Interface Simples**
```vue
<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho Compacto -->
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                    <i class="fas fa-users me-2"></i>Gerenciamento de Usuários
                </h6>
                <div class="d-flex gap-2">
                    <!-- Botão Filtros -->
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" @click="toggleFiltros">
                        <i class="fas fa-filter"></i>
                        <span>Filtros</span>
                        <i class="fas" :class="filtrosVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <!-- Botão Novo Usuário -->
                    <button class="btn btn-outline-success d-flex align-items-center gap-2 px-3 py-2" @click="abrirModal">
                        <i class="fas fa-plus"></i>
                        <span>Novo Usuário</span>
                    </button>
                </div>
            </div>

            <!-- Corpo -->
            <div class="card-body">
                <!-- Filtros Colapsáveis -->
                <div class="filtros-aba-container mb-3" v-if="filtrosVisiveis">
                    <div class="filtros-aba-content" :class="{ 'show': filtrosVisiveis }">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="filtroNome" 
                                           v-model="filtros.nome"
                                           placeholder="Nome...">
                                    <label for="filtroNome">Nome</label>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button class="btn btn-outline-secondary w-100" 
                                        style="height: 58px; line-height: 1.5;" 
                                        @click="limparFiltros">
                                    <i class="fas fa-times me-2"></i>Limpar Filtros
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela -->
                <div class="table-responsive" v-if="dados.length > 0">
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
                            <tr v-for="usuario in dados" :key="usuario.id" class="usuario-row">
                                <td class="align-middle">
                                    <div class="fw-medium">{{ usuario.nome }}</div>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-medium">{{ usuario.email }}</div>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-status" :class="usuario.ativo ? 'badge-ativo' : 'badge-inativo'">
                                        <i class="fas" :class="usuario.ativo ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                        {{ usuario.ativo ? 'ATIVO' : 'INATIVO' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <button class="btn btn-sm btn-warning" @click="editar(usuario)" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" @click="confirmarExclusao(usuario)" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Estado Vazio -->
                <div v-if="!loading && dados.length === 0" class="text-center py-5">
                    <i class="fas fa-users text-muted mb-3" style="font-size: 3rem;"></i>
                    <h6 class="text-muted mb-2">Nenhum usuário encontrado</h6>
                    <p class="text-muted small mb-0">Tente ajustar os filtros ou criar um novo usuário.</p>
                </div>
            </div>
        </div>
        
        <!-- PAGINAÇÃO FORA DO CARD - OBRIGATÓRIO -->
        <div v-if="dados.length > 0" class="paginacao-container mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Informações de Registros -->
                <div class="text-muted fw-medium">
                    Mostrando {{ (paginaAtual - 1) * itensPorPagina + 1 }} até {{ Math.min(paginaAtual * itensPorPagina, dados.length) }} de {{ dados.length }} registros
                </div>
                
                <!-- Navegação -->
                <nav v-if="totalPaginas > 1">
                    <ul class="pagination admin-pagination mb-0">
                        <!-- Botão Anterior -->
                        <li class="page-item" :class="{ disabled: paginaAtual === 1 }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(paginaAtual - 1)" aria-label="Anterior">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        
                        <!-- Páginas -->
                        <li v-for="pagina in paginasVisiveis" 
                            :key="pagina" 
                            class="page-item" 
                            :class="{ active: pagina === paginaAtual }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(pagina)">
                                {{ pagina }}
                            </a>
                        </li>
                        
                        <!-- Botão Próximo -->
                        <li class="page-item" :class="{ disabled: paginaAtual === totalPaginas }">
                            <a class="page-link" href="#" @click.prevent="mudarPagina(paginaAtual + 1)" aria-label="Próximo">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<style>
/* ⚠️ SEMPRE INCLUIR ESTE CSS PADRÃO */
.form-floating .form-control-lg {
    height: 58px !important;
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

.form-floating .form-control-lg:focus {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

.form-floating .form-control-lg:not(:placeholder-shown) {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

/* Filtros */
.filtros-aba-container {
    background-color: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    padding: 1rem;
}

.filtros-aba-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.filtros-aba-content.show {
    max-height: 200px;
}

/* Tabela */
.usuarios-table {
    font-size: 14px;
    font-weight: 400;
    line-height: 1.5;
    table-layout: fixed;
}

.usuarios-table th {
    font-weight: 600;
    font-size: 14px;
    text-transform: none;
    letter-spacing: normal;
    color: #18578A;
    padding: 0.75rem 0.5rem;
    border-bottom: 2px solid #e5e7eb;
    background-color: #f9fafb;
    vertical-align: middle;
}

.usuarios-table td {
    padding: 0.5rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f4;
    font-weight: 400;
    font-size: 14px;
}

.usuario-row {
    background-color: #ffffff;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
    cursor: default;
}

.usuario-row:hover {
    background-color: #f8f9fa;
    border-left: 3px solid #5EA853;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

/* Badges */
.badge-status {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 0.25rem;
    border: 1px solid;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    transition: all 0.2s ease;
}

.badge-ativo {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    color: #0369a1;
    border-color: #bae6fd;
}

.badge-inativo {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    color: #dc2626;
    border-color: #fecaca;
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
</style>
```

---

## 12. Conclusão

### 📋 **Resumo dos Padrões de Interface Simples**
1. **Estrutura**: Container principal com card único
2. **Cabeçalho**: Título verde com botões de ação
3. **Filtros**: Colapsáveis com altura correta dos campos
4. **Tabela**: Classes obrigatórias e botões sólidos
5. **Paginação**: Local e fora do card principal
6. **Badges**: Discretos com gradientes sutis
7. **Responsividade**: Breakpoints obrigatórios

### 🔗 **Próximos Passos**
- **Para interfaces com abas**: Consulte `06_padrao_interface_com_abas.md`
- **Para modais**: Consulte `07_padrao_modais.md`
- **Para padrões universais**: Consulte `02_padrao_layout_interface.md`

---

> **IMPORTANTE**: Este documento define padrões para interfaces SIMPLES (sem abas). Para interfaces com abas, consulte o arquivo correspondente.
