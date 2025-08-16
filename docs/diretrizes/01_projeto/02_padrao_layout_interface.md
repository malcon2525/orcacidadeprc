# Padrão de Layout e Interface - OrçaCidade

> **DOCUMENTO MESTRE**: Este documento define padrões visuais UNIVERSAIS do projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para manter consistência visual e UX.

> **ATUALIZADO EM 2025**: Padrão evoluído para Vue.js + API com interface moderna e interativa. CSS centralizado em `modern-interface.css`.

> **ESCOPO**: Este arquivo contém APENAS padrões universais (cores, tipografia, estrutura base). Para padrões específicos, consulte os arquivos especializados.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Estabelecer padrões visuais UNIVERSAIS para interface do sistema, garantindo UX uniforme e profissional em todas as funcionalidades.

### 🎨 **Princípios Fundamentais**
- **Consistência Visual** - Mesmo padrão em todas as telas
- **UX Moderna** - Interface limpa e intuitiva
- **Responsividade** - Adaptação a diferentes dispositivos
- **Acessibilidade** - Interface acessível para todos os usuários
- **Eficiência** - Layout otimizado para notebooks 14"

### ⚠️ **IMPORTANTE - ARQUIVOS ESPECIALIZADOS**
- **Para interfaces SEM abas**: Consulte `05_padrao_interface_simples.md`
- **Para interfaces COM abas**: Consulte `06_padrao_interface_com_abas.md`
- **Para modais**: Consulte `07_padrao_modais.md`
- **Para paginação**: Consulte `08_padrao_paginacao.md`
- **Para tabelas**: Consulte `09_padrao_tabelas.md`

---

## 2. Cores Padronizadas

### 🎨 **Paleta Principal**

#### **Cores Obrigatórias**
- **Verde Principal:** `#5EA853` - Títulos principais do card header
- **Azul Secundário:** `#18578A` - Títulos de abas, texto custom, elementos secundários
- **Cinza Escuro:** `#374151` - Cabeçalhos de tabela (NÃO USAR - usar `text-custom`)
- **Cinza Médio:** `#6c757d` - Texto secundário, labels, ícones
- **Branco:** `#ffffff` - Fundos, cards, filtros
- **Cinza Claro:** `#f8f9fa` - Fundos de abas (NÃO USAR para filtros)

#### **CORREÇÃO CRÍTICA - Cores de Títulos**
- **Título Principal (Card Header):** `style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;"` - APENAS para o título principal
- **Títulos de Abas:** `class="text-custom"` - Para todos os títulos dentro das abas
- **Cabeçalhos de Tabela:** `class="text-custom"` - NÃO usar `style="color: #374151;"`

#### **Cores de Status (Badges DISCRETOS - NÃO Sólidos)**
- **Papel:** `class="badge-papel"` - NÃO usar `badge bg-primary`
- **Status Ativo:** `class="badge-status badge-ativo"` - NÃO usar `badge bg-success`
- **Status Inativo:** `class="badge-status badge-inativo"` - NÃO usar `badge bg-danger`
- **Tipo Local:** `class="badge-tipo badge-local"` - NÃO usar `badge bg-info`
- **Tipo AD:** `class="badge-tipo badge-ad"` - NÃO usar `badge bg-warning`

#### **Cores de Botões de Ação**
- **Info (Azul):** `#0dcaf0` (bg) + `white` (text)
- **Warning (Amarelo):** `#ffc107` (bg) + `#000` (text)
- **Danger (Vermelho):** `#dc3545` (bg) + `white` (text)

### 🚫 **Proibições de Cores**
- **NÃO** usar cores fora da paleta definida
- **NÃO** usar `style="color: #374151;"` para cabeçalhos de tabela
- **NÃO** usar badges sólidos Bootstrap - usar sempre badges discretos
- **NÃO** usar `#f8f9fa` para fundo de filtros - usar `#ffffff`

---

## 3. Estrutura Visual Base

### 📋 **Container Principal (Obrigatório)**
```html
<div class="container-fluid px-4">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <!-- Cabeçalho Compacto -->
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                <i class="fas fa-[icon] me-2"></i>[Título da Funcionalidade]
            </h6>
        </div>

        <!-- Corpo -->
        <div class="card-body">
            <!-- Conteúdo específico aqui -->
        </div>
    </div>
</div>
```

### 🎨 **Classes CSS Universais**

#### **Texto Custom (Obrigatório para títulos de abas)**
```css
.text-custom {
    color: #18578A !important;
    font-weight: 600;
}
```

#### **Sistema de Abas (Base)**
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

## 4. Z-Index Hierarchy

### 🎯 **Regras Obrigatórias**
```css
/* Modais principais */
.modal {
    z-index: 1055 !important;
}

/* Modais de confirmação */
.modal-backdrop {
    z-index: 1054 !important;
}

/* Dropdowns e tooltips */
.dropdown-menu {
    z-index: 1000 !important;
}

/* Toast notifications */
.toast-container {
    z-index: 1060 !important;
}
```

---

## 5. Responsividade Base

### 📱 **Breakpoints Obrigatórios**
```css
/* Mobile First */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem !important;
    }
}

@media (max-width: 992px) {
    .admin-tab-content {
        padding: 1rem !important;
    }
}
```

---

## 6. Ícones e Fontes

### 🎨 **Font Awesome 6.5.1 (Obrigatório)**
- **Usuários:** `fas fa-users`, `fas fa-user`, `fas fa-user-cog`
- **Configurações:** `fas fa-cog`, `fas fa-settings`, `fas fa-wrench`
- **Ações:** `fas fa-plus`, `fas fa-edit`, `fas fa-trash`, `fas fa-save`
- **Status:** `fas fa-check-circle`, `fas fa-times-circle`, `fas fa-exclamation-triangle`
- **Navegação:** `fas fa-chevron-left`, `fas fa-chevron-right`, `fas fa-chevron-up`, `fas fa-chevron-down`

### 📝 **Tipografia**
- **Títulos principais:** `font-size: 1.2rem`, `font-weight: 600`
- **Títulos de abas:** `font-size: 1rem`, `font-weight: 600`
- **Texto de tabela:** `font-size: 14px`, `font-weight: 400`
- **Labels:** `font-size: 14px`, `font-weight: 500`

---

## 7. Espaçamentos e Margens

### 📏 **Sistema de Espaçamento (Obrigatório)**
```css
/* Margens entre seções */
.mb-3 { margin-bottom: 1rem !important; }
.mb-4 { margin-bottom: 1.5rem !important; }

/* Padding interno */
.py-2 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
.py-3 { padding-top: 1rem !important; padding-bottom: 1rem !important; }

/* Espaçamento entre elementos */
.gap-1 { gap: 0.25rem !important; }
.gap-2 { gap: 0.5rem !important; }
.gap-3 { gap: 1rem !important; }
```

---

## 8. Sombras e Bordas

### 🎨 **Sistema de Sombras (Obrigatório)**
```css
/* Cards principais */
.shadow-sm {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
}

/* Hover de elementos */
.hover-shadow:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
}

/* Bordas */
.border-0 {
    border: none !important;
}

.rounded-3 {
    border-radius: 0.5rem !important;
}
```

---

## 9. Estados e Transições

### ⚡ **Transições (Obrigatório)**
```css
/* Transições suaves */
.transition-all {
    transition: all 0.3s ease !important;
}

/* Hover de botões */
.btn:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
}

/* Hover de linhas de tabela */
.usuario-row:hover {
    background-color: #f8f9fa !important;
    border-left: 3px solid #5EA853 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
}
```

---

## 10. Validação e Feedback

### ✅ **Estados de Validação (Obrigatório)**
```css
/* Campos válidos */
.form-control.is-valid {
    border-color: #5EA853 !important;
    box-shadow: 0 0 0 0.2rem rgba(94, 168, 83, 0.25) !important;
}

/* Campos inválidos */
.form-control.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

/* Feedback de validação */
.invalid-feedback {
    color: #dc3545 !important;
    font-size: 0.875rem !important;
}
```

---

## 11. Acessibilidade

### ♿ **Requisitos Obrigatórios**
- **Contraste**: Mínimo 4.5:1 para texto normal, 3:1 para texto grande
- **Foco**: Sempre visível com `outline` ou `box-shadow`
- **Labels**: Sempre associados aos campos de formulário
- **Alt text**: Para todas as imagens e ícones decorativos
- **ARIA**: Atributos apropriados para elementos interativos

---

## 12. Conclusão

### 📋 **Resumo dos Padrões Universais**
1. **Cores**: Paleta fixa e obrigatória
2. **Estrutura**: Container principal com card
3. **Tipografia**: Classes e tamanhos padronizados
4. **Espaçamento**: Sistema consistente de margens e padding
5. **Sombras**: Sistema unificado de elevação
6. **Transições**: Animações suaves e consistentes
7. **Z-index**: Hierarquia clara para sobreposições
8. **Responsividade**: Breakpoints padronizados
9. **Acessibilidade**: Requisitos mínimos obrigatórios

### 🔗 **Próximos Passos**
- **Para interfaces específicas**: Consulte os arquivos especializados
- **Para implementação**: Consulte os padrões de desenvolvimento
- **Para dúvidas**: Este arquivo é a fonte única da verdade para padrões universais

---

> **IMPORTANTE**: Este documento contém APENAS padrões universais. Para padrões específicos de interface, consulte os arquivos especializados correspondentes.