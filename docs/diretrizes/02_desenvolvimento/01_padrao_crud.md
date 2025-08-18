# Padrão CRUD - OrçaCidade

> **DOCUMENTO ESPECIALIZADO**: Este documento define padrões para operações CRUD (Create, Read, Update, Delete) do sistema OrçaCidade.

> **ATUALIZADO EM 2025**: Padrão evoluído para classes genéricas reutilizáveis e CSS global consolidado.

> **ESCOPO**: Todas as operações CRUD do sistema devem seguir estes padrões para manter consistência funcional e visual.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Estabelecer padrões UNIVERSAIS para operações CRUD, garantindo consistência funcional, visual e de UX em todo o sistema.

### 🎨 **Princípios Fundamentais**
- **Consistência Funcional** - Mesmo padrão para todas as operações
- **Reutilização** - Classes genéricas para qualquer CRUD
- **CSS Global** - Todos os estilos em `modern-interface.css`
- **UX Moderna** - Interface limpa e intuitiva
- **Validação** - Sempre implementar validação visual

### ⚠️ **IMPORTANTE - CSS GLOBAL**
- **Todos os estilos CRUD estão em `public/assets/css/modern-interface.css`**
- **NÃO criar estilos locais para operações CRUD**
- **Usar sempre as classes genéricas definidas**

---

## 2. Operações CRUD

### 📋 **Create (Criar)**
- **Modal de formulário** com header gradiente
- **Validação visual** obrigatória
- **Loading states** nos botões
- **Toast de feedback** obrigatório

### 📋 **Read (Ler)**
- **Tabelas responsivas** com classes globais
- **Filtros colapsáveis** padronizados
- **Paginação centralizada** (se aplicável)
- **Estados vazios** com ícones e mensagens

### 📋 **Update (Atualizar)**
- **Modal de formulário** pré-preenchido
- **Validação visual** obrigatória
- **Loading states** nos botões
- **Toast de feedback** obrigatório

### 📋 **Delete (Excluir)**
- **Modal de confirmação** obrigatório
- **NUNCA** usar `confirm()` nativo
- **Loading states** nos botões
- **Toast de feedback** obrigatório

---

## 3. Modal de Confirmação (Exclusão) - OBRIGATÓRIO

### 🎯 **Uso Obrigatório**
- **SEMPRE** usar este modal para confirmações de exclusão
- **NUNCA** usar `confirm()` nativo do navegador
- **OBRIGATÓRIO** para todas as operações destrutivas

### 🏗️ **Estrutura HTML (OBRIGATÓRIA)**

```html
<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade modal-confirmacao" id="modalConfirmacaoExclusao" tabindex="-1" ref="modalConfirmacaoRef">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <div class="header-icon" aria-hidden="true">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h5 class="modal-title mb-0">
                        Confirmar Exclusão
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body text-center">
                <p class="confirm-text mb-1">Tem certeza que deseja excluir o [tipo]</p>
                <p class="target-entity fs-5 mb-3">
                    <span id="nomeEntidade">"{{ itemParaExcluir?.name || itemParaExcluir?.display_name }}"</span>
                </p>

                <!-- Caixa de Aviso -->
                <div class="irreversible mb-1" role="status" aria-live="polite">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span>Esta ação é permanente e não poderá ser desfeita.</span>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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

### 🎨 **Classes CSS (OBRIGATÓRIAS)**

#### **Classe Principal do Modal**
```css
.modal-confirmacao {
    border-radius: 0.75rem !important;
    overflow: hidden !important;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
}
```

#### **Header com Gradiente**
```css
.modal-confirmacao .modal-header {
    background: linear-gradient(135deg, #18578A 0%, #5EA853 100%) !important;
    color: white !important;
    border-bottom: none !important;
    padding: 1.5rem !important;
    border-radius: 0.5rem 0.5rem 0 0 !important;
}
```

#### **Ícone do Header**
```css
.modal-confirmacao .header-icon {
    width: 40px !important;
    height: 40px !important;
    background-color: rgba(255, 255, 255, 0.2) !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-right: 1rem !important;
    font-size: 1.2rem !important;
    color: white !important;
}
```

#### **Botão Fechar**
```css
.modal-confirmacao .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%) !important;
}
```

#### **Corpo do Modal**
```css
.modal-confirmacao .modal-body {
    background: white !important;
    padding: 1.25rem 1.25rem 0 1.25rem !important;
    text-align: center !important;
}
```

#### **Tipografia**
```css
.modal-confirmacao .confirm-text {
    color: #3E4653 !important;
    font-size: 1.1rem !important;
    margin: 0 0 1rem 0 !important;
    line-height: 1.6 !important;
}

.modal-confirmacao .target-entity {
    color: #2E77D0 !important;
    font-weight: 700 !important;
    font-size: 1.25rem !important;
    display: block !important;
    margin: 0.5rem 0 1.5rem 0 !important;
}
```

#### **Caixa de Aviso**
```css
.modal-confirmacao .irreversible {
    background: #FFF5F5 !important;
    border: 1px solid #FAD4D4 !important;
    color: #5f6b7a !important;
    border-radius: 0.75rem !important;
    padding: 0.75rem 0.9rem !important;
    margin: 0 0 2rem 0 !important;
    display: flex !important;
    gap: 0.5rem !important;
    align-items: flex-start !important;
    text-align: left !important;
}

.modal-confirmacao .irreversible i {
    color: #D64545 !important;
    font-size: 1.2rem !important;
    flex-shrink: 0 !important;
}

.modal-confirmacao .irreversible span {
    color: #5f6b7a !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
}
```

#### **Footer e Botões**
```css
.modal-confirmacao .modal-footer {
    background: transparent !important;
    border: none !important;
    padding: 1.5rem 1.25rem 1.25rem 1.25rem !important;
    gap: 0.6rem !important;
    justify-content: center !important;
}

.modal-confirmacao .btn {
    border-radius: 0.375rem !important;
    font-weight: 500 !important;
    padding: 0.5rem 1rem !important;
    border: none !important;
    font-size: 0.875rem !important;
}

.modal-confirmacao .btn-secondary {
    background: #6c757d !important;
    color: white !important;
}

.modal-confirmacao .btn-danger {
    background: #dc3545 !important;
    color: white !important;
}
```

### 🔧 **Implementação Vue.js (OBRIGATÓRIA)**

#### **Data Properties**
```javascript
data() {
    return {
        itemParaExcluir: null,
        excluindo: false
    }
}
```

#### **Método de Abertura**
```javascript
excluirItem(item) {
    this.itemParaExcluir = item;
    const modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacaoExclusao'));
    modalConfirmacao.show();
}
```

#### **Método de Confirmação**
```javascript
async confirmarExclusao() {
    if (!this.itemParaExcluir) return;
    
    this.excluindo = true;
    try {
        // Lógica de exclusão aqui
        const response = await axios.delete(`/api/[endpoint]/${this.itemParaExcluir.id}`);
        
        if (response.ok) {
            const modalConfirmacao = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacaoExclusao'));
            modalConfirmacao.hide();
            
            // Mostrar toast de sucesso
            this.mostrarToast('Sucesso', 'Item excluído com sucesso!', 'fa-check-circle text-success');
            
            // Recarregar dados
            this.carregarDados();
        }
    } catch (error) {
        console.error('Erro:', error);
        this.mostrarToast('Erro', 'Erro ao excluir', 'fa-exclamation-circle text-danger');
    } finally {
        this.excluindo = false;
        this.itemParaExcluir = null;
    }
}
```

---

## 4. Tabelas CRUD

### 🎯 **Estrutura Obrigatória**
- **SEMPRE** usar `table-admin` para cabeçalhos
- **SEMPRE** usar `table-admin-row` para linhas
- **SEMPRE** usar badges discretos para status
- **SEMPRE** usar botões de ação padronizados

### 🏗️ **Estrutura HTML**

```html
<div class="table-responsive">
    <table class="table table-hover align-middle table-admin">
        <thead>
            <tr>
                <th class="fw-semibold text-custom">Nome</th>
                <th class="fw-semibold text-custom">Status</th>
                <th class="fw-semibold text-custom text-end w-180px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="item in dados" :key="item.id" class="table-admin-row">
                <td>
                    <div class="fw-medium">{{ item.name }}</div>
                </td>
                <td>
                    <span class="badge badge-status" :class="item.is_active ? 'badge-ativo' : 'badge-inativo'">
                        <i class="fas" :class="item.is_active ? 'fa-check-circle' : 'fa-times-circle'"></i>
                        {{ item.is_active ? 'ATIVO' : 'INATIVO' }}
                    </span>
                </td>
                <td class="text-end">
                    <div class="d-flex gap-1 justify-content-end">
                        <button class="btn btn-sm btn-warning" @click="editarItem(item)" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" @click="excluirItem(item)" title="Excluir">
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

## 5. Formulários CRUD

### 🎯 **Estrutura Obrigatória**
- **SEMPRE** usar `form-floating` para campos
- **SEMPRE** usar classes de validação
- **SEMPRE** usar loading states
- **SEMPRE** usar toast para feedback

### 🏗️ **Estrutura HTML**

```html
<form @submit.prevent="salvarItem">
    <div class="row g-3">
        <!-- Campo Nome -->
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" 
                       class="form-control" 
                       :class="{ 'is-invalid': errors.name }"
                       id="name" 
                       v-model="form.name"
                       placeholder="Nome do item"
                       required>
                <label for="name">Nome *</label>
            </div>
            <div class="invalid-feedback" v-if="errors.name">
                {{ errors.name[0] }}
            </div>
        </div>
        
        <!-- Campo Status -->
        <div class="col-md-6">
            <div class="form-floating">
                <select class="form-control" 
                        :class="{ 'is-invalid': errors.is_active }"
                        id="is_active" 
                        v-model="form.is_active" 
                        required>
                    <option value="">Selecione...</option>
                    <option :value="true">Ativo</option>
                    <option :value="false">Inativo</option>
                </select>
                <label for="is_active">Status *</label>
            </div>
            <div class="invalid-feedback" v-if="errors.is_active">
                {{ errors.is_active[0] }}
            </div>
        </div>
        
        <!-- Campo Descrição -->
        <div class="col-12">
            <div class="form-floating">
                <textarea class="form-control min-h-58 h-58 resize-vertical overflow-auto" 
                          id="description" 
                          v-model="form.description"
                          placeholder="Descrição do item"
                          rows="3"></textarea>
                <label for="description">Descrição</label>
            </div>
        </div>
    </div>
</form>
```

---

## 6. Classes Utilitárias (OBRIGATÓRIO usar)

### 🎨 **Classes de Largura**
```css
.w-100px { width: 100px !important; }
.w-180px { width: 180px !important; }
.max-w-200px { max-width: 200px !important; }
```

### 🎨 **Classes de Altura**
```css
.min-h-58 { min-height: 58px !important; }
.h-58 { height: 58px !important; }
```

### 🎨 **Classes de Interação**
```css
.resize-vertical { resize: vertical !important; }
.overflow-auto { overflow: auto !important; }
.cursor-pointer { cursor: pointer !important; }
.z-1000 { z-index: 1000 !important; }
```

---

## 7. Regras Gerais

### ✅ **Obrigatório**
- **SEMPRE** usar as classes CSS definidas
- **SEMPRE** implementar loading states
- **SEMPRE** usar toast para feedback
- **SEMPRE** usar validação visual
- **SEMPRE** usar CSS global

### ❌ **Proibido**
- **NUNCA** criar estilos locais para CRUD
- **NUNCA** usar `confirm()` nativo
- **NUNCA** usar estilos inline
- **NUNCA** usar classes Bootstrap padrão para badges

### 🔗 **Referências**
- **CSS Global**: `public/assets/css/modern-interface.css`
- **Padrão de Layout**: `02_padrao_layout_interface.md`
- **Padrão de Modais**: `07_padrao_modais.md`
- **Bootstrap**: [Documentação oficial](https://getbootstrap.com/docs/5.3/)

---

## 8. Checklist de Implementação

### ✅ **Modal de Confirmação**
- [ ] HTML com estrutura correta
- [ ] Classe `modal-confirmacao` aplicada
- [ ] Vue.js data properties configuradas
- [ ] Métodos de abertura e confirmação implementados
- [ ] Loading states funcionando
- [ ] Toast de feedback implementado

### ✅ **Tabelas CRUD**
- [ ] Classe `table-admin` aplicada
- [ ] Classe `table-admin-row` aplicada
- [ ] Badges discretos implementados
- [ ] Botões de ação padronizados
- [ ] Responsividade testada

### ✅ **Formulários CRUD**
- [ ] Form-floating implementado
- [ ] Validação visual funcionando
- [ ] Loading states implementados
- [ ] Toast de feedback funcionando
- [ ] Classes utilitárias aplicadas

### ✅ **CSS Global**
- [ ] Estilos aplicados em `modern-interface.css`
- [ ] Nenhum estilo local criado
- [ ] Classes utilitárias funcionando
- [ ] Responsividade testada

---

> **ÚLTIMA ATUALIZAÇÃO**: Janeiro 2025 - Classes genéricas e CSS global consolidado 