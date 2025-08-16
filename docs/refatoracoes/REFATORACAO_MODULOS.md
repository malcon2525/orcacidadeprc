# 🔄 Refatoração de Módulos - OrçaCidade

## 📋 **Contexto Geral**

Este documento registra o processo de **refatoração sistemática** de todos os módulos desenvolvidos no projeto OrçaCidade para adaptá-los aos novos padrões de projeto e desenvolvimento estabelecidos.

### **Objetivo**
- Padronizar todos os módulos existentes
- Garantir consistência com as diretrizes estabelecidas
- Melhorar manutenibilidade e escalabilidade do código
- Documentar o processo para referência futura

---

## 🚨 **INSTRUÇÕES CRÍTICAS PARA REFATORAÇÃO**

### **⚠️ ANTES DE COMEÇAR QUALQUER REFATORAÇÃO:**

**1. LEITURA OBRIGATÓRIA E COMPLETA:**
- **NÃO LEIA SUPERFICIALMENTE** - leia com atenção total
- **NÃO PULE SEÇÕES** - leia do início ao fim
- **NÃO ASSUMA NADA** - baseie-se apenas no que está documentado

**2. ARQUIVOS OBRIGATÓRIOS PARA LEITURA:**
```
docs/diretrizes/01_projeto/
├── 00_padroes_projeto.md          ← ARQUITETURA GERAL
├── 01_padrao_estrutura_diretorios.md ← ESTRUTURA DE PASTAS
├── 02_padrao_layout_interface.md  ← LAYOUT, CORES, CSS
├── 03_padrao_bibliotecas.md       ← STACK TECNOLÓGICO
└── 04_padrao_rotas.md             ← ESTRUTURA DE ROTAS

docs/diretrizes/02_desenvolvimento/
├── 01_padrao_crud.md              ← PADRÃO CRUD (NÃO ESQUECER!)
└── 02_padrao_documentacao.md      ← PADRÕES DE DOCUMENTAÇÃO
```

**3. CSS GLOBAL OBRIGATÓRIO:**
- **LEIA COMPLETAMENTE:** `resources/css/modern-interface.css`
- **ENTENDA TODAS AS CLASSES** disponíveis
- **NÃO CRIE NOVAS CLASSES** - reutilize as existentes

---

## 📚 **GUIA COMPLETO DE PADRÕES - REFATORAÇÃO PERFEITA**

### **🏗️ 1. ARQUITETURA GERAL (00_padroes_projeto.md)**

#### **✅ SEPARAÇÃO DE RESPONSABILIDADES:**
- **Web Controllers:** Apenas servem views (não processam dados)
- **API Controllers:** Processam dados e retornam JSON
- **Vue Components:** Interface e lógica frontend
- **Models:** Interação com banco de dados
- **Services:** Lógica de negócio complexa

#### **✅ ORGANIZAÇÃO MODULAR:**
- Cada funcionalidade em seu módulo específico
- Estrutura hierárquica clara
- Separação entre funcionalidades

### **📁 2. ESTRUTURA DE DIRETÓRIOS (01_padrao_estrutura_diretorios.md)**

#### **✅ ESTRUTURA OBRIGATÓRIA:**
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Web/
│   │   │   └── [Modulo]/
│   │   │       └── [Funcionalidade]/
│   │   │           └── [Funcionalidade]Controller.php
│   │   └── Api/
│   │       └── [Modulo]/
│   │           └── [Funcionalidade]/
│   │               └── [Funcionalidade]Controller.php
│   └── Middleware/
├── Models/
│   └── [Modulo]/
│       └── [Funcionalidade].php
└── Services/
    └── [Modulo]/
        └── [Funcionalidade]Service.php

resources/
├── js/
│   └── components/
│       └── [modulo]/
│           └── [funcionalidade]/
│               └── [Funcionalidade].vue
└── views/
    └── [modulo]/
        └── [funcionalidade]/
            └── index.blade.php
```

#### **✅ REGRAS CRÍTICAS:**
- **Web Controller:** Apenas método `index()` que retorna view
- **API Controller:** Métodos `listar`, `store`, `update`, `destroy`
- **View:** Apenas `index.blade.php` (sem `create.blade.php` ou `edit.blade.php`)
- **Vue Component:** Nome em PascalCase, tag em kebab-case

### **🎨 3. LAYOUT E INTERFACE (02_padrao_layout_interface.md)**

#### **✅ CORES OBRIGATÓRIAS:**
- **Título Principal:** `style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;"`
- **Títulos Secundários:** `class="text-custom"` (cor #18578A)
- **NÃO USE:** `text-primary` ou cores não padronizadas

#### **✅ ESTRUTURA DE CARD:**
```html
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
            <i class="fas fa-[icon] me-2"></i>[Título]
        </h5>
        <div class="d-flex gap-2">
            <!-- Botões de ação -->
        </div>
    </div>
    <div class="card-body">
        <!-- Conteúdo -->
    </div>
</div>
```

#### **✅ FILTROS COLABSÁVEIS:**
```html
<div class="filtros-aba-container mb-3" v-if="filtrosVisiveis">
    <div class="filtros-aba-content" :class="{ 'show': filtrosVisiveis }">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <div class="form-floating">
                    <input type="text" class="form-control form-control-lg" id="filtroNome" v-model="filtros.nome">
                    <label for="filtroNome">Nome</label>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-outline-secondary w-100 h-100" @click="limparFiltros">
                    <i class="fas fa-times me-2"></i>Limpar Filtros
                </button>
            </div>
        </div>
    </div>
</div>
```

#### **✅ TABELA PADRÃO:**
```html
<div class="table-responsive" v-if="dados.data.length > 0">
    <table class="table table-hover align-middle usuarios-table">
        <thead>
            <tr>
                <th class="fw-semibold text-custom">Coluna</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="item in dados.data" :key="item.id" class="usuario-row">
                <td class="align-middle">
                    <!-- Conteúdo da célula -->
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

#### **✅ PAGINAÇÃO (FORA DO CARD):**
```html
<div v-if="dados.data.length > 0" class="paginacao-container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted fw-medium">
            Mostrando {{ dados.from }} até {{ dados.to }} de {{ dados.total }} registros
        </div>
        <nav v-if="dados.last_page > 1">
            <ul class="pagination admin-pagination mb-0">
                <!-- Paginação -->
            </ul>
        </nav>
    </div>
</div>
```

#### **✅ MODAL PADRÃO:**
```html
<div class="modal fade" id="modalId" tabindex="-1" ref="modalRef">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header custom-modal-header">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas fa-[icon]"></i>
                    </div>
                    <h5 class="modal-title mb-0">{{ titulo }}</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <!-- Formulário -->
            </div>
            <div class="modal-footer border-0 pt-0">
                <!-- Botões -->
            </div>
        </div>
    </div>
</div>
```

#### **✅ CSS PROIBIDO:**
- **NÃO USE:** `<style scoped>` (exceto para estilos muito específicos)
- **USE SEMPRE:** Classes do `modern-interface.css`
- **REUTILIZE:** Classes existentes como `usuarios-table`, `usuario-row`, `btn-action`, etc.

### **🛣️ 4. ROTAS (04_padrao_rotas.md)**

#### **✅ REGRA CRÍTICA:**
- **Rotas Internas (Vue.js):** `routes/web.php` (com autenticação de sessão)
- **Rotas Externas (APIs):** `routes/api.php` (com JWT)
- **NUNCA coloque rotas internas em `routes/api.php`**

#### **✅ ESTRUTURA DE ROTAS:**
```php
// Rotas Web (para views)
Route::prefix('modulo')->name('modulo.')->group(function () {
    Route::prefix('funcionalidade')->name('funcionalidade.')->group(function () {
        Route::get('/', [WebController::class, 'index'])->name('index');
    });
});

// Rotas API (para Vue.js - INTERNAS)
Route::prefix('api/modulo')->name('api.modulo.')->group(function () {
    Route::prefix('funcionalidade')->name('funcionalidade.')->group(function () {
        Route::get('/', [ApiController::class, 'listar'])->name('listar');
        Route::post('/', [ApiController::class, 'store'])->name('store');
        Route::put('/{id}', [ApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [ApiController::class, 'destroy'])->name('destroy');
    });
});
```

### **🔧 5. PADRÃO CRUD (01_padrao_crud.md) - NÃO ESQUECER!**

#### **✅ ESTRUTURA VUE COMPONENT:**
```vue
<template>
    <div class="container-fluid px-4">
        <!-- Card principal -->
        <!-- Filtros colapsáveis -->
        <!-- Tabela ou estado vazio -->
        <!-- Paginação (fora do card) -->
        <!-- Modais -->
        <!-- Toast -->
    </div>
</template>

<script>
export default {
    data() {
        return {
            dados: { data: [], current_page: 1, total: 0, last_page: 1 },
            filtros: {},
            form: {},
            errors: {},
            loading: false,
            filtrosVisiveis: false
        }
    },
    mounted() {
        this.carregarDados();
        this.inicializarModais();
    },
    methods: {
        async carregarDados() { /* ... */ },
        toggleFilters() { this.filtrosVisiveis = !this.filtrosVisiveis; },
        limparFiltros() { /* ... */ },
        abrirModalCriar() { /* ... */ },
        editarItem(item) { /* ... */ },
        salvarItem() { /* ... */ },
        excluirItem(item) { /* ... */ }
    }
}
</script>
```

#### **✅ ESTADOS OBRIGATÓRIOS:**
- **Carregamento:** Spinner com texto "Carregando..."
- **Vazio:** Ícone + texto "Nenhum item encontrado"
- **Erro:** Toast de notificação
- **Sucesso:** Toast de confirmação

---

## 🚨 **CHECKLIST COMPLETO PARA REFATORAÇÃO PERFEITA**

### **📋 ANTES DE COMEÇAR:**
- [ ] **Ler TODOS os arquivos de padrão** (não superficialmente)
- [ ] **Ler completamente** `modern-interface.css`
- [ ] **Entender a estrutura** do módulo atual
- [ ] **Identificar** funcionalidades existentes

### **🏗️ ESTRUTURA:**
- [ ] **Web Controller:** Apenas método `index()`
- [ ] **API Controller:** Métodos CRUD completos
- [ ] **View:** Apenas `index.blade.php`
- [ ] **Vue Component:** Estrutura padrão
- [ ] **Rotas:** Internas em `web.php`, externas em `api.php`

### **🎨 LAYOUT:**
- [ ] **Cores:** #5EA853 para título principal, text-custom para secundários
- [ ] **Card:** Estrutura padrão com header e body
- [ ] **Filtros:** Colapsáveis com `form-floating` e botão limpar
- [ ] **Tabela:** Classes `usuarios-table` e `usuario-row`
- [ ] **Paginação:** Fora do card com `paginacao-container`
- [ ] **Modal:** Header com ícone e botão fechar branco

### **🔧 FUNCIONALIDADE:**
- [ ] **CRUD completo:** Create, Read, Update, Delete
- [ ] **Filtros funcionais:** Com busca em tempo real
- [ ] **Validação:** Backend com retorno JSON (422)
- [ ] **Feedback:** Toast para sucesso/erro
- [ ] **Loading states:** Durante operações assíncronas

### **📱 RESPONSIVIDADE:**
- [ ] **Mobile-first:** Funciona em todos os dispositivos
- [ ] **Grid responsivo:** `col-md-4`, `col-md-6`, etc.
- [ ] **Tabela responsiva:** `table-responsive`

---

## 📚 **LIÇÕES APRENDIDAS - NÃO REPETIR ERROS**

### **🚨 PROBLEMAS CRÍTICOS IDENTIFICADOS:**

#### **1. ESTRUTURA DE ROTAS:**
- **❌ ERRADO:** Colocar rotas internas em `routes/api.php`
- **✅ CORRETO:** Rotas internas em `routes/web.php`

#### **2. VIEWS EXTRAS:**
- **❌ ERRADO:** Criar `create.blade.php` e `edit.blade.php`
- **✅ CORRETO:** Apenas `index.blade.php` + modal

#### **3. CSS INLINE:**
- **❌ ERRADO:** Usar `style="color: #5EA853"`
- **✅ CORRETO:** Usar classes CSS existentes

#### **4. ALTURA DE CAMPOS:**
- **❌ ERRADO:** Campos cortando texto
- **✅ CORRETO:** `form-control-lg` + CSS específico para altura

#### **5. CLASSES CSS:**
- **❌ ERRADO:** Criar novas classes específicas
- **✅ CORRETO:** Reutilizar classes existentes (`usuarios-table`, `usuario-row`)

#### **6. SEPARAÇÃO DE RESPONSABILIDADES:**
- **❌ ERRADO:** Web Controller processando dados
- **✅ CORRETO:** Web Controller apenas serve view, API Controller processa dados

#### **7. Botões de Ação com Padrão Incorreto**
- ❌ **Erro:** Usar `btn-outline-primary` e `btn-outline-danger` para botões de ação
- ✅ **Correção:** Usar `btn btn-sm btn-warning` (amarelo sólido) para editar e `btn btn-sm btn-danger` (vermelho sólido) para excluir
- ❌ **Erro:** Usar `btn-action` ou classes customizadas para botões de ação
- ✅ **Correção:** Sempre usar classes Bootstrap padrão (`btn-warning`, `btn-danger`)
- ❌ **Erro:** Usar `btn-group-actions` ou containers customizados
- ✅ **Correção:** Sempre usar `d-flex gap-1 justify-content-end` para container dos botões

#### **8. Modal de Confirmação com Alert Nativo**
- ❌ **Erro:** Usar `confirm()` nativo do JavaScript para confirmações de exclusão
- ✅ **Correção:** Sempre implementar modal Bootstrap personalizado com design consistente
- ❌ **Erro:** Usar `alert()` para feedback de operações
- ✅ **Correção:** Usar toast notifications ou modais personalizados
- ❌ **Erro:** Não implementar estado de loading durante exclusão
- ✅ **Correção:** Sempre incluir spinner e botão desabilitado durante operação

---

## 🎯 **PROCESSO DE REFATORAÇÃO PERFEITA**

### **📋 FLUXO OBRIGATÓRIO:**

1. **📖 LEITURA COMPLETA:**
   - Ler TODOS os arquivos de padrão
   - Ler `modern-interface.css`
   - Entender estrutura atual

2. **🔍 ANÁLISE:**
   - Identificar problemas
   - Planejar mudanças
   - Validar com usuário

3. **🚀 IMPLEMENTAÇÃO:**
   - Seguir checklist completo
   - Aplicar padrões exatos
   - Testar funcionalidade

4. **✅ VALIDAÇÃO:**
   - Verificar layout
   - Testar funcionalidades
   - Confirmar padrões

---

## 📊 **Progresso Atual**

### **✅ FUNCIONALIDADES CONCLUÍDAS:**

#### **1. Importar DER-PR (Tabela Oficial)**
- **Status:** ✅ **REFATORAÇÃO COMPLETA**
- **Data:** 06/08/2025
- **Lições:** Estrutura modular, logs, interface moderna

#### **2. Importar SINAPI (Tabela Oficial)**
- **Status:** ✅ **REFATORAÇÃO COMPLETA**
- **Data:** 08/08/2025
- **Lições:** Consistência visual, limpeza de código

#### **3. Consultar DER-PR (Tabela Oficial)**
- **Status:** ✅ **REFATORAÇÃO COMPLETA**
- **Data:** 08/08/2025
- **Lições:** Modal fullscreen, filtros, paginação

#### **4. Consultar SINAPI (Tabela Oficial)**
- **Status:** ✅ **REFATORAÇÃO COMPLETA**
- **Data:** 08/08/2025
- **Lições:** Backend otimizado, modal seguro

#### **5. Municípios (Administração)**
- **Status:** ✅ **REFATORAÇÃO COMPLETA**
- **Data:** 08/08/2025
- **Lições:** Layout perfeito, filtros funcionais, CSS correto

---

## 🎯 **Próximos Passos**

### **📋 TO-DO List:**

#### **1. Próximas Funcionalidades para Refatoração**
Aguardando usuário fornecer:
- [ ] Nome do menu
- [ ] Nome do módulo
- [ ] Nome da funcionalidade
- [ ] Análise completa
- [ ] Aprovação para refatoração

#### **2. Padrões a Aplicar em Todas as Funcionalidades**
- [ ] Estrutura de diretórios padronizada
- [ ] Separação Web/API Controllers
- [ ] Views com container padrão
- [ ] Componentes Vue sem `<style scoped>`
- [ ] Classes CSS centralizadas
- [ ] Nomenclatura consistente
- [ ] Código comentado adequadamente
- [ ] Sistema de logs implementado
- [ ] Documentação técnica completa

---

## 🔗 **Referências**

### **Arquivos de Padrão:**
- `docs/diretrizes/01_projeto/00_padroes_projeto.md`
- `docs/diretrizes/01_projeto/01_padrao_estrutura_diretorios.md`
- `docs/diretrizes/01_projeto/02_padrao_layout_interface.md`
- `docs/diretrizes/01_projeto/03_padrao_bibliotecas.md`
- `docs/diretrizes/01_projeto/04_padrao_rotas.md`
- `docs/diretrizes/02_desenvolvimento/01_padrao_crud.md`
- `docs/diretrizes/02_desenvolvimento/02_padrao_documentacao.md`

### **CSS Global:**
- `resources/css/modern-interface.css`

### **📚 Documentação Criada:**
- `docs/refatoracoes/tabela_oficial/importar-derpr.md`
- `docs/refatoracoes/tabela_oficial/importar-sinapi.md`
- `docs/refatoracoes/tabela_oficial/consultar-derpr.md`
- `docs/refatoracoes/tabela_oficial/consultar-sinapi.md`
- `docs/refatoracoes/administracao/municipios.md`

---

## 📅 **Histórico de Atualizações**

- **Data:** 06/08/2025  
  **Módulo:** Tabela Oficial  
  **Funcionalidade:** Importar DER-PR  
  **Status:** ✅ **REFATORAÇÃO COMPLETA**

- **Data:** 08/08/2025  
  **Módulo:** Tabela Oficial  
  **Funcionalidade:** Importar SINAPI  
  **Status:** ✅ **REFATORAÇÃO COMPLETA**

- **Data:** 08/08/2025  
  **Módulo:** Tabela Oficial  
  **Funcionalidade:** Consultar DER-PR  
  **Status:** ✅ **REFATORAÇÃO COMPLETA**

- **Data:** 08/08/2025  
  **Módulo:** Tabela Oficial  
  **Funcionalidade:** Consultar SINAPI  
  **Status:** ✅ **REFATORAÇÃO COMPLETA**

- **Data:** 08/08/2025  
  **Módulo:** Administração  
  **Funcionalidade:** Municípios  
  **Status:** ✅ **REFATORAÇÃO COMPLETA**

---

*Este documento serve como guia de referência e TO-DO list para o processo de refatoração. Deve ser atualizado conforme o progresso avança.* 