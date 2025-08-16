# Padrão de Tabelas - OrçaCidade

> **ESCOPO**: Este documento define padrões visuais para tabelas do projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para manter consistência visual.

> **APLICAÇÃO**: Todas as tabelas do sistema, incluindo listas, relatórios e exibição de dados.

> **BASE**: Este padrão segue os padrões universais definidos em `02_padrao_layout_interface.md`.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Estabelecer padrões visuais para tabelas, garantindo consistência e funcionalidade adequada em todo o sistema.

### 🎨 **Características**
- **Classes obrigatórias** para todas as tabelas
- **Hover nas linhas** com borda esquerda verde
- **Cabeçalhos padronizados** com cor azul
- **Botões de ação** sólidos (amarelo para editar, vermelho para excluir)
- **Responsividade** para todas as telas

### 📋 **Tipos de Tabela**
- **Tabela de Lista** (CRUD padrão)
- **Tabela de Relatório** (dados estáticos)
- **Tabela de Seleção** (múltipla escolha)
- **Tabela de Comparação** (dados lado a lado)

---

## 2. Estrutura Visual Obrigatória

### 📋 **Tabela Base (Obrigatório)**
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
                    <span class="badge badge-status" :class="item.status === 'ativo' ? 'badge-ativo' : 'badge-inativo'">
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

---

## 3. Classes CSS Obrigatórias

### 🎨 **Classes da Tabela (Obrigatórias)**
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
    position: relative;
    transition: all 0.2s ease;
}

/* Hover do cabeçalho */
.usuarios-table th:hover {
    background-color: #f3f4f6;
    color: #18578A;
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

---

## 4. Cabeçalhos da Tabela

### 📋 **Estrutura dos Cabeçalhos (Obrigatória)**
```html
<thead>
    <tr>
        <th class="fw-semibold text-custom">Nome</th>
        <th class="fw-semibold text-custom">Email</th>
        <th class="fw-semibold text-custom">Status</th>
        <th class="fw-semibold text-end text-custom" style="width: 150px;">Ações</th>
    </tr>
</thead>
```

**Regras para Cabeçalhos:**
- ✅ **SEMPRE usar** `fw-semibold text-custom` para todos os cabeçalhos
- ✅ **SEMPRE usar** `text-end` para coluna de ações
- ✅ **SEMPRE definir** largura fixa para coluna de ações (150px)
- ✅ **SEMPRE usar** cor azul (`#18578A`) via classe `text-custom`

---

## 5. Células da Tabela

### 📋 **Estrutura das Células (Obrigatória)**
```html
<tbody>
    <tr v-for="item in dados" :key="item.id" class="usuario-row">
        <!-- Célula de Texto -->
        <td class="align-middle">
            <div class="fw-medium">{{ item.nome }}</div>
        </td>
        
        <!-- Célula com Badge -->
        <td class="align-middle">
            <span class="badge badge-status" :class="item.status === 'ativo' ? 'badge-ativo' : 'badge-inativo'">
                <i class="fas" :class="item.status === 'ativo' ? 'fa-check-circle' : 'fa-times-circle'"></i>
                {{ item.status.toUpperCase() }}
            </span>
        </td>
        
        <!-- Célula com Ações -->
        <td class="text-end">
            <div class="d-flex gap-1 justify-content-end">
                <!-- Botões aqui -->
            </div>
        </td>
    </tr>
</tbody>
```

**Regras para Células:**
- ✅ **SEMPRE usar** `align-middle` para alinhamento vertical
- ✅ **SEMPRE usar** `fw-medium` para texto em células
- ✅ **SEMPRE usar** `text-end` para coluna de ações
- ✅ **SEMPRE usar** `usuario-row` para todas as linhas

---

## 6. Botões de Ação

### 🎨 **Padrão dos Botões (Obrigatório)**
```html
<div class="d-flex gap-1 justify-content-end">
    <!-- Botão Editar: AMARELO SÓLIDO -->
    <button class="btn btn-sm btn-warning" @click="editar(item)" title="Editar">
        <i class="fas fa-edit"></i>
    </button>
    
    <!-- Botão Excluir: VERMELHO SÓLIDO -->
    <button class="btn btn-sm btn-danger" @click="confirmarExclusao(item)" title="Excluir">
        <i class="fas fa-trash"></i>
    </button>
    
    <!-- Botão Visualizar: AZUL SÓLIDO -->
    <button class="btn btn-sm btn-info" @click="visualizar(item)" title="Visualizar">
        <i class="fas fa-eye"></i>
    </button>
</div>
```

**Regras para Botões de Ação:**
- ✅ **SEMPRE usar** `btn btn-sm` para tamanho
- ✅ **SEMPRE usar** cores sólidas (NÃO outline)
- ✅ **Editar**: `btn-warning` (amarelo)
- ✅ **Excluir**: `btn-danger` (vermelho)
- ✅ **Visualizar**: `btn-info` (azul)
- ✅ **SEMPRE usar** `d-flex gap-1 justify-content-end` para container
- ✅ **SEMPRE incluir** `title` para acessibilidade

---

## 7. Badges e Status

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

<!-- Status Pendente -->
<span class="badge badge-status badge-pendente">
    <i class="fas fa-clock"></i>
    PENDENTE
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

/* Status pendente - Amarelo discreto com gradiente sutil */
.badge-pendente {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    color: #d97706;
    border-color: #fed7aa;
}

.badge-pendente:hover {
    background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(217, 119, 6, 0.15);
}
```

---

## 8. Estados da Tabela

### 📋 **Estado de Carregamento (Loading)**
```html
<!-- Estado de Carregamento -->
<div v-if="loading" class="text-center py-4">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
    <p class="mt-2 text-muted">Carregando [itens]...</p>
</div>
```

### 📋 **Estado Vazio (Sem Dados)**
```html
<!-- Estado Vazio -->
<div v-if="!loading && dados.length === 0" class="text-center py-5">
    <i class="fas fa-[icon] text-muted mb-3" style="font-size: 3rem;"></i>
    <h6 class="text-muted mb-2">Nenhum [item] encontrado</h6>
    <p class="text-muted small mb-0">Tente ajustar os filtros ou criar um novo [item].</p>
</div>
```

### 📋 **Estado de Erro**
```html
<!-- Estado de Erro -->
<div v-if="erro" class="alert alert-danger" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Erro:</strong> {{ erro }}
</div>
```

---

## 9. Responsividade

### 📱 **Breakpoints Obrigatórios**
```css
/* Mobile First */
@media (max-width: 576px) {
    .table-responsive {
        font-size: 13px;
    }
    
    .usuarios-table th,
    .usuarios-table td {
        padding: 0.375rem 0.25rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

@media (max-width: 768px) {
    .usuarios-table {
        font-size: 13px;
    }
    
    .usuarios-table th,
    .usuarios-table td {
        padding: 0.5rem 0.375rem;
    }
    
    .badge-status {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
}

@media (max-width: 992px) {
    .usuarios-table th,
    .usuarios-table td {
        padding: 0.625rem 0.5rem;
    }
}
```

---

## 10. Tipos de Células Especiais

### 📋 **Célula com Avatar/Imagem**
```html
<td class="align-middle">
    <div class="d-flex align-items-center">
        <div class="avatar me-3">
            <img :src="item.avatar" :alt="item.nome" class="rounded-circle" width="32" height="32">
        </div>
        <div>
            <div class="fw-medium">{{ item.nome }}</div>
            <div class="text-muted small">{{ item.cargo }}</div>
        </div>
    </div>
</td>
```

### 📋 **Célula com Progresso**
```html
<td class="align-middle">
    <div class="d-flex align-items-center">
        <div class="progress me-3" style="width: 100px; height: 8px;">
            <div class="progress-bar" :style="{ width: item.progresso + '%' }" role="progressbar"></div>
        </div>
        <span class="text-muted small">{{ item.progresso }}%</span>
    </div>
</td>
```

### 📋 **Célula com Data**
```html
<td class="align-middle">
    <div class="d-flex flex-column">
        <div class="fw-medium">{{ formatarData(item.data) }}</div>
        <div class="text-muted small">{{ formatarHora(item.data) }}</div>
    </div>
</td>
```

---

## 11. Seleção Múltipla

### 📋 **Tabela com Seleção (Obrigatório)**
```html
<div class="table-responsive" v-if="dados.length > 0">
    <table class="table table-hover align-middle usuarios-table">
        <thead>
            <tr>
                <th class="fw-semibold text-custom" style="width: 50px;">
                    <input type="checkbox" 
                           class="form-check-input" 
                           :checked="todosSelecionados"
                           @change="selecionarTodos">
                </th>
                <th class="fw-semibold text-custom">Nome</th>
                <th class="fw-semibold text-custom">Email</th>
                <th class="fw-semibold text-custom">Status</th>
                <th class="fw-semibold text-end text-custom" style="width: 150px;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="item in dados" :key="item.id" class="usuario-row">
                <td class="align-middle">
                    <input type="checkbox" 
                           class="form-check-input" 
                           :checked="item.selecionado"
                           @change="selecionarItem(item)">
                </td>
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
                        <button class="btn btn-sm btn-warning" @click="editar(item)" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
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

---

## 12. Checklist de Implementação

### 📋 **Estrutura Base**
- [ ] Container com `table-responsive`
- [ ] Tabela com classes `table table-hover align-middle usuarios-table`
- [ ] Cabeçalhos com `fw-semibold text-custom`
- [ ] Linhas com classe `usuario-row`

### 📋 **Cabeçalhos**
- [ ] Todos os cabeçalhos com `fw-semibold text-custom`
- [ ] Coluna de ações com `text-end` e largura fixa
- [ ] Cores azuis (`#18578A`) aplicadas via `text-custom`
- [ ] Padding e bordas corretos

### 📋 **Células**
- [ ] Todas as células com `align-middle`
- [ ] Texto com `fw-medium` quando aplicável
- [ ] Coluna de ações com `text-end`
- [ ] Badges com classes corretas

### 📋 **Botões de Ação**
- [ ] Container com `d-flex gap-1 justify-content-end`
- [ ] Botões com `btn btn-sm` e cores sólidas
- [ ] Editar: `btn-warning` (amarelo)
- [ ] Excluir: `btn-danger` (vermelho)
- [ ] Títulos (`title`) para acessibilidade

### 📋 **Badges**
- [ ] Badges discretos (NÃO sólidos Bootstrap)
- [ ] Classes obrigatórias: `badge-status`, `badge-ativo`, `badge-inativo`
- [ ] Ícones Font Awesome apropriados
- [ ] Texto em maiúsculo

### 📋 **Responsividade**
- [ ] Breakpoints implementados
- [ ] Tamanho de fonte adaptativo
- [ ] Padding adaptativo para diferentes telas
- [ ] Botões responsivos

---

## 13. Exemplo Completo

### 📋 **Tabela Completa com Todos os Elementos**
```vue
<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho -->
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                    <i class="fas fa-users me-2"></i>Gerenciamento de Usuários
                </h6>
                <button class="btn btn-outline-success" @click="abrirModal">
                    <i class="fas fa-plus me-2"></i>Novo Usuário
                </button>
            </div>

            <!-- Corpo -->
            <div class="card-body">
                <!-- Estado de Carregamento -->
                <div v-if="loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando usuários...</p>
                </div>

                <!-- Estado de Erro -->
                <div v-else-if="erro" class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Erro:</strong> {{ erro }}
                </div>

                <!-- Tabela -->
                <div v-else-if="dados.length > 0" class="table-responsive">
                    <table class="table table-hover align-middle usuarios-table">
                        <thead>
                            <tr>
                                <th class="fw-semibold text-custom">Nome</th>
                                <th class="fw-semibold text-custom">Email</th>
                                <th class="fw-semibold text-custom">Cargo</th>
                                <th class="fw-semibold text-custom">Status</th>
                                <th class="fw-semibold text-custom">Último Acesso</th>
                                <th class="fw-semibold text-end text-custom" style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="usuario in dados" :key="usuario.id" class="usuario-row">
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <img :src="usuario.avatar" :alt="usuario.nome" class="rounded-circle" width="32" height="32">
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ usuario.nome }}</div>
                                            <div class="text-muted small">{{ usuario.departamento }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-medium">{{ usuario.email }}</div>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-medium">{{ usuario.cargo }}</div>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-status" :class="usuario.ativo ? 'badge-ativo' : 'badge-inativo'">
                                        <i class="fas" :class="usuario.ativo ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                        {{ usuario.ativo ? 'ATIVO' : 'INATIVO' }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex flex-column">
                                        <div class="fw-medium">{{ formatarData(usuario.ultimo_acesso) }}</div>
                                        <div class="text-muted small">{{ formatarHora(usuario.ultimo_acesso) }}</div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <button class="btn btn-sm btn-info" @click="visualizar(usuario)" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </button>
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
                <div v-else class="text-center py-5">
                    <i class="fas fa-users text-muted mb-3" style="font-size: 3rem;"></i>
                    <h6 class="text-muted mb-2">Nenhum usuário encontrado</h6>
                    <p class="text-muted small mb-0">Tente ajustar os filtros ou criar um novo usuário.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
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
    position: relative;
    transition: all 0.2s ease;
}

/* Hover do cabeçalho */
.usuarios-table th:hover {
    background-color: #f3f4f6;
    color: #18578A;
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

/* Avatar */
.avatar img {
    object-fit: cover;
}

/* Responsividade */
@media (max-width: 576px) {
    .table-responsive {
        font-size: 13px;
    }
    
    .usuarios-table th,
    .usuarios-table td {
        padding: 0.375rem 0.25rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

@media (max-width: 768px) {
    .usuarios-table {
        font-size: 13px;
    }
    
    .usuarios-table th,
    .usuarios-table td {
        padding: 0.5rem 0.375rem;
    }
    
    .badge-status {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
}

@media (max-width: 992px) {
    .usuarios-table th,
    .usuarios-table td {
        padding: 0.625rem 0.5rem;
    }
}
</style>
```

---

## 14. Conclusão

### 📋 **Resumo dos Padrões de Tabelas**
1. **Estrutura**: Classes obrigatórias para todas as tabelas
2. **Cabeçalhos**: Cor azul padronizada via `text-custom`
3. **Linhas**: Hover com borda esquerda verde
4. **Botões**: Cores sólidas padronizadas para ações
5. **Badges**: Discretos com gradientes sutis
6. **Responsividade**: Breakpoints para todas as telas
7. **Estados**: Loading, vazio e erro implementados

### 🔗 **Próximos Passos**
- **Para interfaces simples**: Consulte `05_padrao_interface_simples.md`
- **Para interfaces com abas**: Consulte `06_padrao_interface_com_abas.md`
- **Para padrões universais**: Consulte `02_padrao_layout_interface.md`

---

> **IMPORTANTE**: Este documento define padrões para tabelas. Para outros tipos de interface, consulte os arquivos correspondentes.
