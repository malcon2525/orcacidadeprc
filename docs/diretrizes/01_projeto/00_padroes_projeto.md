# Padrões do Projeto - OrçaCidade

> **DOCUMENTO MESTRE**: Este arquivo contém o resumo de TODOS os padrões do projeto OrçaCidade.

> **ATUALIZADO EM 2025**: Padrões evoluídos para Vue.js + API com interface moderna e interativa. CSS global consolidado em `modern-interface.css`.

> **ESCOPO**: Resumo executivo de todos os padrões. Para detalhes, consulte os arquivos especializados.

---

## 📋 **PADRÕES PRINCIPAIS ATUALIZADOS**

### 🎨 **Layout e Interface (02_padrao_layout_interface.md)**
- **Cores padronizadas**: Verde `#5EA853`, Azul `#18578A`, Cinza `#6c757d`
- **Classes utilitárias globais**: `.w-100px`, `.w-180px`, `.min-h-58`, `.h-58`, `.resize-vertical`, `.overflow-auto`, `.cursor-pointer`, `.z-1000`
- **Container de badges com rolagem**: `.scrollable-badges` com scrollbar estilizada
- **CSS global obrigatório**: Todos os estilos devem estar em `modern-interface.css`

### 🖼️ **Modais (07_padrao_modais.md)**
- **Modal de confirmação**: Classe genérica `.modal-confirmacao` reutilizável
- **Header com gradiente**: Verde para azul com ícone circular
- **Classes genéricas**: Substituem IDs específicos para melhor reutilização
- **Implementação Vue.js**: Data properties, métodos de abertura e confirmação

### 🔧 **CRUD (01_padrao_crud.md)**
- **Modal de confirmação obrigatório**: NUNCA usar `confirm()` nativo
- **Tabelas padronizadas**: Classes `table-admin` e `table-admin-row`
- **Formulários com form-floating**: Validação visual obrigatória
- **Classes utilitárias**: Substituem estilos inline

### 🏗️ **Estrutura de Componentes**
- **Parent-Child pattern**: Componentes filhos emitem eventos para pai
- **Paginação centralizada**: Gerenciada pelo componente pai
- **CSS global**: Nenhum estilo local para componentes
- **Reutilização**: Classes genéricas para qualquer funcionalidade

---

## 🎯 **PRINCÍPIOS FUNDAMENTAIS**

### ✅ **Obrigatório**
- **SEMPRE** usar CSS global (`modern-interface.css`)
- **SEMPRE** usar classes genéricas definidas
- **SEMPRE** implementar loading states
- **SEMPRE** usar toast para feedback
- **SEMPRE** usar validação visual

### ❌ **Proibido**
- **NUNCA** criar estilos locais para componentes
- **NUNCA** usar `confirm()` nativo
- **NUNCA** usar estilos inline
- **NUNCA** usar classes Bootstrap padrão para badges

---

## 🔗 **ARQUIVOS DE PADRÕES**

### 📁 **01_projeto/**
- **`00_padroes_projeto.md`** ← Este arquivo (resumo executivo)
- **`01_padrao_estrutura_diretorios.md`** - Estrutura de pastas
- **`02_padrao_layout_interface.md`** - Padrões visuais universais
- **`03_padrao_autenticacao.md`** - Sistema de autenticação
- **`04_padrao_autorizacao.md`** - Sistema de autorização RBAC
- **`05_padrao_interface_simples.md`** - Interfaces sem abas
- **`06_padrao_interface_com_abas.md`** - Interfaces com abas
- **`07_padrao_modais.md`** - Padrões para todos os modais
- **`08_padrao_paginacao.md`** - Sistema de paginação
- **`09_padrao_tabelas.md`** - Padrões para tabelas

### 📁 **02_desenvolvimento/**
- **`01_padrao_crud.md`** - Operações CRUD padronizadas
- **`02_padrao_crud_sem_abas.md`** - CRUD em interface simples
- **`03_padrao_crud_com_abas.md`** - CRUD em interface com abas

---

## 🎨 **CLASSES UTILITÁRIAS GLOBAIS**

### 📏 **Larguras**
```css
.w-100px { width: 100px !important; }
.w-180px { width: 180px !important; }
.max-w-200px { max-width: 200px !important; }
```

### 📐 **Alturas**
```css
.min-h-58 { min-height: 58px !important; }
.h-58 { height: 58px !important; }
```

### 🔧 **Funcionalidades**
```css
.resize-vertical { resize: vertical !important; }
.overflow-auto { overflow: auto !important; }
.cursor-pointer { cursor: pointer !important; }
.z-1000 { z-index: 1000 !important; }
```

### 🏷️ **Container de Badges**
```css
.scrollable-badges {
    max-height: 200px;
    overflow-y: auto;
    padding-right: 10px;
}
```

---

## 🚀 **IMPLEMENTAÇÃO RÁPIDA**

### 📋 **Para Novos Componentes**
1. **Usar sempre** classes CSS globais
2. **Implementar** loading states
3. **Usar** toast para feedback
4. **Aplicar** validação visual
5. **Seguir** padrões de modais

### 📋 **Para Modais de Confirmação**
1. **Classe obrigatória**: `modal-confirmacao`
2. **HTML padrão**: Estrutura definida em `07_padrao_modais.md`
3. **Vue.js**: Data properties e métodos obrigatórios
4. **CSS**: Aplicado automaticamente via classes genéricas

### 📋 **Para Operações CRUD**
1. **Modal de confirmação**: Obrigatório para exclusões
2. **Tabelas**: Classes `table-admin` e `table-admin-row`
3. **Formulários**: Form-floating com validação
4. **Feedback**: Toast para todas as operações

---

## 🔄 **ATUALIZAÇÕES RECENTES**

### 📅 **Janeiro 2025**
- ✅ **CSS Global consolidado**: Todos os estilos em `modern-interface.css`
- ✅ **Classes genéricas**: Substituem IDs específicos para reutilização
- ✅ **Classes utilitárias**: Substituem estilos inline
- ✅ **Modal de confirmação**: Classe `.modal-confirmacao` reutilizável
- ✅ **Container de badges**: `.scrollable-badges` com scrollbar estilizada
- ✅ **Padrões atualizados**: Todos os documentos refletem as mudanças

---

## 📚 **PRÓXIMOS PASSOS**

### 🎯 **Para Desenvolvedores**
1. **Ler** `02_padrao_layout_interface.md` para padrões visuais
2. **Consultar** `07_padrao_modais.md` para modais
3. **Seguir** `01_padrao_crud.md` para operações CRUD
4. **Usar** sempre classes CSS globais

### 🎯 **Para Arquitetos**
1. **Manter** CSS global consolidado
2. **Expandir** classes utilitárias conforme necessário
3. **Documentar** novos padrões criados
4. **Validar** conformidade com padrões existentes

---

> **IMPORTANTE**: Este documento é o resumo executivo. Para implementação detalhada, consulte os arquivos especializados correspondentes.

> **ÚLTIMA ATUALIZAÇÃO**: Janeiro 2025 - CSS global consolidado e classes genéricas implementadas

