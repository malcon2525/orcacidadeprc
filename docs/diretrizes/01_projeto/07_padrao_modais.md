# Padrão de Modais - OrçaCidade

> **DOCUMENTO ESPECIALIZADO**: Este documento define padrões para todos os modais do sistema OrçaCidade.

> **ATUALIZADO EM 2025**: Padrão evoluído para classes genéricas reutilizáveis e compatibilidade com modais existentes.

> **ESCOPO**: Todos os modais do sistema devem seguir estes padrões para manter consistência visual e funcional.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Estabelecer padrões UNIVERSAIS para todos os modais do sistema, garantindo consistência visual, funcional e de UX.

### 🎨 **Princípios Fundamentais**
- **Consistência Visual** - Mesmo padrão em todos os modais
- **Reutilização** - Classes genéricas para qualquer modal
- **Compatibilidade** - Suporte a modais existentes
- **UX Moderna** - Interface limpa e intuitiva
- **Acessibilidade** - Modal acessível para todos os usuários

### ⚠️ **IMPORTANTE - CSS GLOBAL**
- **Todos os estilos de modal estão em `public/assets/css/modern-interface.css`**
- **NÃO criar estilos locais para modais**
- **Usar sempre as classes genéricas definidas**

---

## 2. Modal de Confirmação (Exclusão)

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

### 📱 **Responsividade**
- **Modal sempre centralizado** com `modal-dialog-centered`
- **Largura adaptativa** do Bootstrap
- **Scroll automático** se conteúdo exceder altura da tela

### ♿ **Acessibilidade**
- **ARIA labels** para botões e elementos
- **Role e aria-live** para caixa de aviso
- **Navegação por teclado** completa
- **Contraste adequado** para leitores de tela

---

## 3. Modal de Formulário

### 🎯 **Uso**
- Para criação e edição de registros
- **SEMPRE** usar header com gradiente
- **SEMPRE** usar classes de validação

### 🏗️ **Estrutura HTML**

```html
<div class="modal fade" id="modalFormulario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Header com Gradiente -->
            <div class="modal-header custom-modal-header">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
                    </div>
                    <h5 class="modal-title mb-0">
                        {{ modoEdicao ? 'Editar' : 'Novo' }} [Entidade]
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body">
                <form @submit.prevent="salvarItem">
                    <!-- Campos do formulário aqui -->
                </form>
            </div>

            <!-- Rodapé do Modal -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" @click="salvarItem" :disabled="salvando">
                    <span v-if="salvando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    <i v-else class="fas fa-save me-2"></i>
                    {{ salvando ? 'Salvando...' : 'Salvar' }}
                </button>
            </div>
        </div>
    </div>
</div>
```

### 🎨 **Classes CSS para Formulário**

#### **Header Personalizado**
```css
.custom-modal-header {
    background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
    color: white;
    border-bottom: none;
    padding: 1.5rem;
    border-radius: 0.5rem 0.5rem 0 0;
}

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

.custom-modal-header .modal-title {
    color: white;
    font-weight: 600;
    font-size: 1.25rem;
}

.custom-modal-header .btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}
```

---

## 4. Regras Gerais

### ✅ **Obrigatório**
- **SEMPRE** usar as classes CSS definidas
- **SEMPRE** implementar loading states
- **SEMPRE** usar toast para feedback
- **SEMPRE** centralizar modais
- **SEMPRE** usar ícones FontAwesome

### ❌ **Proibido**
- **NUNCA** criar estilos locais para modais
- **NUNCA** usar `confirm()` nativo
- **NUNCA** usar modais sem header
- **NUNCA** usar botões sem ícones
- **NUNCA** usar modais sem validação

### 🔗 **Referências**
- **CSS Global**: `public/assets/css/modern-interface.css`
- **Padrão de Layout**: `02_padrao_layout_interface.md`
- **Padrão CRUD**: `01_padrao_crud.md`
- **Bootstrap Modals**: [Documentação oficial](https://getbootstrap.com/docs/5.3/components/modal/)

---

## 5. Checklist de Implementação

### ✅ **Modal de Confirmação**
- [ ] HTML com estrutura correta
- [ ] Classe `modal-confirmacao` aplicada
- [ ] Vue.js data properties configuradas
- [ ] Métodos de abertura e confirmação implementados
- [ ] Loading states funcionando
- [ ] Toast de feedback implementado

### ✅ **Modal de Formulário**
- [ ] HTML com estrutura correta
- [ ] Classe `custom-modal-header` aplicada
- [ ] Formulário com validação
- [ ] Loading states funcionando
- [ ] Toast de feedback implementado

### ✅ **CSS Global**
- [ ] Estilos aplicados em `modern-interface.css`
- [ ] Nenhum estilo local criado
- [ ] Classes utilitárias funcionando
- [ ] Responsividade testada

---

> **ÚLTIMA ATUALIZAÇÃO**: Janeiro 2025 - Classes genéricas e CSS global consolidado
