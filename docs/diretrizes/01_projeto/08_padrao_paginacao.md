# Padrão de Paginação - OrçaCidade

> **ESCOPO**: Este documento define padrões visuais para paginação do projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para manter consistência visual.

> **APLICAÇÃO**: Todos os sistemas de paginação do sistema, incluindo interfaces simples e com abas.

> **BASE**: Este padrão segue os padrões universais definidos em `02_padrao_layout_interface.md`.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Estabelecer padrões visuais para paginação, garantindo consistência e funcionalidade adequada em todo o sistema.

### 🎨 **Características**
- **Posicionamento fora do card principal** (obrigatório)
- **Informações de registros** sempre visíveis
- **Navegação entre páginas** funcional
- **Estados de loading** implementados
- **Responsividade** para todas as telas

### 📋 **Tipos de Paginação**
- **Paginação Simples** (interfaces sem abas)
- **Paginação Centralizada** (interfaces com abas)

---

## 2. Tipos de Paginação

### 📋 **Paginação Simples (Interfaces sem Abas)**
- **Localizada no próprio componente**
- **Gerenciada internamente**
- **Dados locais** (não compartilhados)
- **Exemplo**: CRUD único, lista simples

### 📋 **Paginação Centralizada (Interfaces com Abas)**
- **Localizada no componente pai**
- **Gerenciada pelo componente pai**
- **Dados compartilhados** entre abas
- **Exemplo**: Módulos com múltiplas abas

---

## 3. Estrutura Visual Obrigatória

### 📋 **Container da Paginação (Obrigatório)**
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
            <ul class="pagination pagination-generic mb-0">
                <!-- Navegação aqui -->
            </ul>
        </nav>
    </div>
</div>
```

---

## 4. Paginação Simples (Interfaces sem Abas)

### 📋 **Estrutura Completa (Obrigatória)**
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
            <ul class="pagination pagination-generic mb-0">
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

### 📋 **Script para Paginação Simples (Obrigatório)**
```javascript
export default {
    data() {
        return {
            dados: [],
            paginaAtual: 1,
            itensPorPagina: 10,
            // ... outras variáveis
        }
    },
    computed: {
        paginasVisiveis() {
            const pages = []
            const maxPages = 5
            let start = Math.max(1, this.paginaAtual - Math.floor(maxPages / 2))
            let end = Math.min(this.totalPaginas, start + maxPages - 1)

            if (end - start + 1 < maxPages) {
                start = Math.max(1, end - maxPages + 1)
            }

            for (let i = start; i <= end; i++) {
                pages.push(i)
            }

            return pages
        },
        
        totalPaginas() {
            return Math.ceil(this.dados.length / this.itensPorPagina)
        },
        
        dadosPaginados() {
            const inicio = (this.paginaAtual - 1) * this.itensPorPagina
            const fim = inicio + this.itensPorPagina
            return this.dados.slice(inicio, fim)
        }
    },
    methods: {
        mudarPagina(pagina) {
            if (pagina >= 1 && pagina <= this.totalPaginas) {
                this.paginaAtual = pagina
                // Scroll para o topo da tabela (opcional)
                this.$nextTick(() => {
                    const tabela = this.$el.querySelector('.table-responsive')
                    if (tabela) {
                        tabela.scrollIntoView({ behavior: 'smooth' })
                    }
                })
            }
        }
    }
}
```

---

## 5. Paginação Centralizada (Interfaces com Abas)

### 📋 **Estrutura no Componente Pai (Obrigatória)**
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
            <ul class="pagination pagination-generic mb-0">
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

### 📋 **Script do Componente Pai (Obrigatório)**
```javascript
export default {
    data() {
        return {
            abaAtiva: 'aba1',
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
        onPaginacaoUpdated(dados) {
            this.paginacao = dados
        },
        
        mudarPagina(pagina) {
            if (this.abaAtiva === 'aba1' && this.$refs.componenteAba1) {
                this.$refs.componenteAba1.mudarPaginaExterna(pagina)
            } else if (this.abaAtiva === 'aba2' && this.$refs.componenteAba2) {
                this.$refs.componenteAba2.mudarPaginaExterna(pagina)
            }
            // ... outras abas
        }
    }
}
```

### 📋 **Script do Componente Filho (Obrigatório)**
```javascript
export default {
    methods: {
        async carregarDados(pagina = 1) {
            this.loading = true
            try {
                // ... lógica de carregamento
                // Após carregar, emitir dados para o pai
                this.$emit('paginacao-updated', {
                    data: this.registros,
                    current_page: pagina,
                    last_page: this.totalPaginas,
                    from: (pagina - 1) * this.itensPorPagina + 1,
                    to: Math.min(pagina * this.itensPorPagina, this.registros.length),
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
        }
    }
}
```

---

## 6. CSS Obrigatório para Paginação

### 🎨 **Estilos da Paginação (Obrigatório)**
```css
/* Paginação admin */
.pagination-generic {
    margin: 0;
}

.pagination-generic .page-link {
    color: #18578A;
    border: 1px solid #e9ecef;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.pagination-generic .page-link:hover {
    background-color: #e3f2fd;
    border-color: #bbdefb;
    color: #1976d2;
}

.pagination-generic .page-item.active .page-link {
    background-color: #18578A;
    border-color: #18578A;
    color: white;
}

.pagination-generic .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #e9ecef;
}

/* Container da paginação */
.paginacao-container {
    background-color: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    padding: 1rem 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
```

---

## 7. Informações de Registros

### 📋 **Formato das Informações (Obrigatório)**
```html
<!-- Para Paginação Simples -->
<div class="text-muted fw-medium">
    Mostrando {{ (paginaAtual - 1) * itensPorPagina + 1 }} até {{ Math.min(paginaAtual * itensPorPagina, dados.length) }} de {{ dados.length }} registros
</div>

<!-- Para Paginação Centralizada -->
<div class="text-muted fw-medium">
    Mostrando {{ paginacao.from }} até {{ paginacao.to }} de {{ paginacao.total }} registros
</div>
```

**Regras para Informações:**
- ✅ **SEMPRE mostrar** informações de registros
- ✅ **Formato**: "Mostrando X até Y de Z registros"
- ✅ **Estilo**: `text-muted fw-medium`
- ✅ **Posicionamento**: Lado esquerdo da paginação

---

## 8. Navegação entre Páginas

### 📋 **Estrutura da Navegação (Obrigatória)**
```html
<nav v-if="totalPaginas > 1">
    <ul class="pagination pagination-generic mb-0">
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
```

**Regras para Navegação:**
- ✅ **SEMPRE usar** `pagination-generic` para estilos
- ✅ **SEMPRE incluir** botões anterior e próximo
- ✅ **SEMPRE usar** ícones Font Awesome para setas
- ✅ **SEMPRE implementar** estados disabled
- ✅ **SEMPRE mostrar** página ativa

---

## 9. Estados de Loading

### 📋 **Loading na Paginação (Obrigatório)**
```html
<!-- Botão com Loading -->
<button class="btn btn-primary" @click="mudarPagina(pagina)" :disabled="loading">
    <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
    <i v-else class="fas fa-chevron-right"></i>
    {{ loading ? 'Carregando...' : 'Próxima' }}
</button>
```

**Regras para Loading:**
- ✅ **SEMPRE desabilitar** botões durante carregamento
- ✅ **SEMPRE mostrar** spinner de loading
- ✅ **SEMPRE alterar** texto durante loading
- ✅ **SEMPRE usar** `spinner-border-sm` para tamanho

---

## 10. Responsividade

### 📱 **Breakpoints Obrigatórios**
```css
/* Mobile First */
@media (max-width: 576px) {
    .paginacao-container {
        padding: 0.75rem 1rem !important;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
        align-items: center;
    }
    
    .pagination-generic {
        flex-wrap: wrap;
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .paginacao-container {
        padding: 1rem !important;
    }
    
    .text-muted.fw-medium {
        text-align: center;
        font-size: 0.875rem;
    }
    
    .pagination-generic .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
}

@media (max-width: 992px) {
    .paginacao-container {
        margin-top: 1rem !important;
    }
}
```

---

## 11. Estados Especiais

### 📋 **Estado Vazio (Sem Dados)**
```html
<!-- Estado Vazio -->
<div v-if="!loading && dados.length === 0" class="text-center py-5">
    <i class="fas fa-[icon] text-muted mb-3" style="font-size: 3rem;"></i>
    <h6 class="text-muted mb-2">Nenhum [item] encontrado</h6>
    <p class="text-muted small mb-0">Tente ajustar os filtros ou criar um novo [item].</p>
</div>
```

### 📋 **Estado de Carregamento**
```html
<!-- Estado de Carregamento -->
<div v-if="loading" class="text-center py-4">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
    <p class="mt-2 text-muted">Carregando [itens]...</p>
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

## 12. Checklist de Implementação

### 📋 **Estrutura Base**
- [ ] Paginação FORA do card principal
- [ ] Container com `paginacao-container mt-4`
- [ ] Layout flexível com `justify-content-between`
- [ ] Informações de registros visíveis

### 📋 **Informações de Registros**
- [ ] Formato "Mostrando X até Y de Z registros"
- [ ] Estilo `text-muted fw-medium`
- [ ] Posicionamento à esquerda
- [ ] Cálculos corretos implementados

### 📋 **Navegação**
- [ ] Classes `pagination-generic` aplicadas
- [ ] Botões anterior e próximo implementados
- [ ] Ícones Font Awesome para setas
- [ ] Estados disabled funcionais
- [ ] Página ativa destacada

### 📋 **Funcionalidade**
- [ ] Método `mudarPagina` implementado
- [ ] Computed `paginasVisiveis` funcional
- [ ] Estados de loading implementados
- [ ] Scroll para topo (opcional)

### 📋 **Responsividade**
- [ ] Breakpoints implementados
- [ ] Layout adaptativo para mobile
- [ ] Paginação centralizada em telas pequenas
- [ ] Texto adaptativo para diferentes telas

---

## 13. Exemplo Completo

### 📋 **Paginação Simples Completa**
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
                <!-- Tabela -->
                <div class="table-responsive" v-if="dadosPaginados.length > 0">
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
                            <tr v-for="usuario in dadosPaginados" :key="usuario.id" class="usuario-row">
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
                    <ul class="pagination pagination-generic mb-0">
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

<script>
export default {
    name: 'ListaUsuarios',
    data() {
        return {
            dados: [],
            paginaAtual: 1,
            itensPorPagina: 10,
            loading: false
        }
    },
    computed: {
        paginasVisiveis() {
            const pages = []
            const maxPages = 5
            let start = Math.max(1, this.paginaAtual - Math.floor(maxPages / 2))
            let end = Math.min(this.totalPaginas, start + maxPages - 1)

            if (end - start + 1 < maxPages) {
                start = Math.max(1, end - maxPages + 1)
            }

            for (let i = start; i <= end; i++) {
                pages.push(i)
            }

            return pages
        },
        
        totalPaginas() {
            return Math.ceil(this.dados.length / this.itensPorPagina)
        },
        
        dadosPaginados() {
            const inicio = (this.paginaAtual - 1) * this.itensPorPagina
            const fim = inicio + this.itensPorPagina
            return this.dados.slice(inicio, fim)
        }
    },
    mounted() {
        this.carregarDados()
    },
    methods: {
        async carregarDados() {
            this.loading = true
            try {
                // Simular carregamento de dados
                this.dados = [
                    { id: 1, nome: 'João Silva', email: 'joao@email.com', ativo: true },
                    { id: 2, nome: 'Maria Santos', email: 'maria@email.com', ativo: false },
                    // ... mais dados
                ]
            } catch (error) {
                console.error('Erro ao carregar dados:', error)
            } finally {
                this.loading = false
            }
        },
        
        mudarPagina(pagina) {
            if (pagina >= 1 && pagina <= this.totalPaginas) {
                this.paginaAtual = pagina
                // Scroll para o topo da tabela (opcional)
                this.$nextTick(() => {
                    const tabela = this.$el.querySelector('.table-responsive')
                    if (tabela) {
                        tabela.scrollIntoView({ behavior: 'smooth' })
                    }
                })
            }
        }
    }
}
</script>

<style>
/* Paginação */
.pagination-generic {
    margin: 0;
}

.pagination-generic .page-link {
    color: #18578A;
    border: 1px solid #e9ecef;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.pagination-generic .page-link:hover {
    background-color: #e3f2fd;
    border-color: #bbdefb;
    color: #1976d2;
}

.pagination-generic .page-item.active .page-link {
    background-color: #18578A;
    border-color: #18578A;
    color: white;
}

.pagination-generic .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #e9ecef;
}

/* Container da paginação */
.paginacao-container {
    background-color: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    padding: 1rem 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

/* Responsividade */
@media (max-width: 576px) {
    .paginacao-container {
        padding: 0.75rem 1rem !important;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
        align-items: center;
    }
    
    .pagination-generic {
        flex-wrap: wrap;
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .paginacao-container {
        padding: 1rem !important;
    }
    
    .text-muted.fw-medium {
        text-align: center;
        font-size: 0.875rem;
    }
    
    .pagination-generic .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
}
</style>
```

---

## 14. Conclusão

### 📋 **Resumo dos Padrões de Paginação**
1. **Posicionamento**: Sempre FORA do card principal
2. **Tipos**: Simples (local) e Centralizada (compartilhada)
3. **Informações**: Sempre mostrar registros visíveis
4. **Navegação**: Botões anterior, páginas e próximo
5. **Estilos**: Classes `pagination-generic` obrigatórias
6. **Responsividade**: Breakpoints para todas as telas
7. **Estados**: Loading, vazio e erro implementados

### 🔗 **Próximos Passos**
- **Para interfaces simples**: Consulte `05_padrao_interface_simples.md`
- **Para interfaces com abas**: Consulte `06_padrao_interface_com_abas.md`
- **Para padrões universais**: Consulte `02_padrao_layout_interface.md`

---

> **IMPORTANTE**: Este documento define padrões para paginação. Para outros tipos de interface, consulte os arquivos correspondentes.
