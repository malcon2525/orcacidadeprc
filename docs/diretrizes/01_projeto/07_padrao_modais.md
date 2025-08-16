# Padrão de Modais - OrçaCidade

> **ESCOPO**: Este documento define padrões visuais para modais do projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para manter consistência visual.

> **APLICAÇÃO**: Todos os modais do sistema, incluindo formulários, confirmações e exibições de dados.

> **BASE**: Este padrão segue os padrões universais definidos em `02_padrao_layout_interface.md`.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Estabelecer padrões visuais para modais, garantindo consistência e funcionalidade adequada em todo o sistema.

### 🎨 **Características**
- **Header com gradiente** (verde para azul)
- **Botões padronizados** (verde para salvar, cinza para cancelar)
- **Formulários com form-floating** quando aplicável
- **Responsividade** para todas as telas
- **Z-index** configurado automaticamente

### 📋 **Tipos de Modal**
- **Modal de Formulário** (criar/editar)
- **Modal de Confirmação** (exclusão)
- **Modal de Visualização** (detalhes)
- **Modal de Seleção** (múltipla escolha)

---

## 2. Estrutura Visual Obrigatória

### 📋 **Modal Base (Obrigatório)**
```html
<!-- Modal Base -->
<div class="modal fade" id="modalId" tabindex="-1" ref="modalRef">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Header com Gradiente -->
            <div class="modal-header custom-modal-header">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas fa-[icon]"></i>
                    </div>
                    <h5 class="modal-title mb-0">
                        [Título do Modal]
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body">
                <!-- Conteúdo aqui -->
            </div>

            <!-- Rodapé do Modal -->
            <div class="modal-footer border-0">
                <!-- Botões aqui -->
            </div>
        </div>
    </div>
</div>
```

---

## 3. Header do Modal

### 🎨 **Header com Gradiente (Obrigatório)**
```html
<div class="modal-header custom-modal-header">
    <div class="d-flex align-items-center">
        <div class="header-icon">
            <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
        </div>
        <h5 class="modal-title mb-0">
            {{ modoEdicao ? 'Editar [Item]' : 'Novo [Item]' }}
        </h5>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
```

**CSS Obrigatório para Header:**
```css
/* Header personalizado do modal */
.custom-modal-header {
    background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
    color: white;
    border-bottom: none;
    padding: 1.5rem;
    border-radius: 0.5rem 0.5rem 0 0;
}

/* Ícone do header */
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

/* Título do modal */
.custom-modal-header .modal-title {
    color: white;
    font-weight: 600;
    font-size: 1.25rem;
}

/* Botão fechar */
.custom-modal-header .btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}
```

---

## 4. Tipos de Modal

### 📋 **Modal de Formulário (Criar/Editar)**
```html
<!-- Modal de Formulário -->
<div class="modal fade" id="modalFormulario" tabindex="-1" ref="modalFormularioRef">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Header com Gradiente -->
            <div class="modal-header custom-modal-header">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
                    </div>
                    <h5 class="modal-title mb-0">
                        {{ modoEdicao ? 'Editar Usuário' : 'Novo Usuário' }}
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body">
                <form @submit.prevent="salvar">
                    <div class="row g-3">
                        <!-- Campo Nome -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control" 
                                       id="nome" 
                                       v-model="form.nome"
                                       placeholder="Nome completo"
                                       required>
                                <label for="nome">Nome Completo</label>
                            </div>
                        </div>
                        
                        <!-- Campo Email -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       v-model="form.email"
                                       placeholder="Email"
                                       required>
                                <label for="email">Email</label>
                            </div>
                        </div>
                        
                        <!-- Campo Status -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-control" id="status" v-model="form.status" required>
                                    <option value="">Selecione...</option>
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                </select>
                                <label for="status">Status</label>
                            </div>
                        </div>
                        
                        <!-- Campo Observações -->
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" 
                                          id="observacoes" 
                                          v-model="form.observacoes"
                                          placeholder="Observações"
                                          style="height: 100px"></textarea>
                                <label for="observacoes">Observações</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Rodapé do Modal -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" @click="salvar" :disabled="salvando">
                    <span v-if="salvando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    <i v-else class="fas fa-save me-2"></i>
                    {{ salvando ? 'Salvando...' : 'Salvar' }}
                </button>
            </div>
        </div>
    </div>
</div>
```

### 📋 **Modal de Confirmação (Exclusão)**
```html
<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalConfirmacaoExclusao" tabindex="-1" ref="modalConfirmacaoRef">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Header com Gradiente -->
            <div class="modal-header custom-modal-header">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h5 class="modal-title mb-0">
                        Confirmar Exclusão
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                    <h6 class="mb-3">Tem certeza que deseja excluir?</h6>
                    <p class="text-muted mb-0">
                        <strong>"{{ itemParaExcluir?.nome }}"</strong> será removido permanentemente.
                    </p>
                    <p class="text-muted small mt-2">
                        Esta ação não pode ser desfeita.
                    </p>
                </div>
            </div>

            <!-- Rodapé do Modal -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
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

### 📋 **Modal de Visualização (Detalhes)**
```html
<!-- Modal de Visualização -->
<div class="modal fade" id="modalVisualizacao" tabindex="-1" ref="modalVisualizacaoRef">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Header com Gradiente -->
            <div class="modal-header custom-modal-header">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h5 class="modal-title mb-0">
                        Detalhes do Usuário
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-custom">Nome</label>
                        <p class="form-control-plaintext">{{ item.nome }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-custom">Email</label>
                        <p class="form-control-plaintext">{{ item.email }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-custom">Status</label>
                        <div>
                            <span class="badge badge-status" :class="item.status === 'ativo' ? 'badge-ativo' : 'badge-inativo'">
                                <i class="fas" :class="item.status === 'ativo' ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                {{ item.status.toUpperCase() }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-custom">Data de Criação</label>
                        <p class="form-control-plaintext">{{ formatarData(item.created_at) }}</p>
                    </div>
                    <div class="col-12" v-if="item.observacoes">
                        <label class="form-label fw-semibold text-custom">Observações</label>
                        <p class="form-control-plaintext">{{ item.observacoes }}</p>
                    </div>
                </div>
            </div>

            <!-- Rodapé do Modal -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Fechar
                </button>
            </div>
        </div>
    </div>
</div>
```

---

## 5. Botões dos Modais

### 🎨 **Padrão de Botões (Obrigatório)**
```html
<!-- Rodapé do Modal -->
<div class="modal-footer border-0">
    <!-- Botão Cancelar/Fechar: SEMPRE CINZA -->
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        <i class="fas fa-times me-2"></i>Cancelar
    </button>
    
    <!-- Botão Salvar: SEMPRE VERDE -->
    <button type="button" class="btn btn-success" @click="salvar" :disabled="salvando">
        <span v-if="salvando" class="spinner-border spinner-border-sm me-2" role="status"></span>
        <i v-else class="fas fa-save me-2"></i>
        {{ salvando ? 'Salvando...' : 'Salvar' }}
    </button>
</div>
```

**Cores Obrigatórias dos Botões:**
- ✅ **Cancelar/Fechar**: `btn-secondary` (cinza `#5F6368`)
- ✅ **Salvar**: `btn-success` (verde `#5EA853`)
- ✅ **Excluir**: `btn-danger` (vermelho `#dc3545`)
- ✅ **Editar**: `btn-warning` (amarelo `#ffc107`)

---

## 6. Formulários nos Modais

### 📋 **Campos Form-Floating (Obrigatório)**
```html
<div class="row g-3">
    <!-- Campo Texto -->
    <div class="col-md-6">
        <div class="form-floating">
            <input type="text" 
                   class="form-control" 
                   id="campoId" 
                   v-model="form.campo"
                   placeholder="Placeholder do campo"
                   required>
            <label for="campoId">Label do Campo</label>
        </div>
    </div>
    
    <!-- Campo Select -->
    <div class="col-md-6">
        <div class="form-floating">
            <select class="form-control" id="status" v-model="form.status" required>
                <option value="">Selecione...</option>
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
            </select>
            <label for="status">Status</label>
        </div>
    </div>
    
    <!-- Campo Textarea -->
    <div class="col-12">
        <div class="form-floating">
            <textarea class="form-control" 
                      id="observacoes" 
                      v-model="form.observacoes"
                      placeholder="Observações"
                      style="height: 100px"></textarea>
            <label for="observacoes">Observações</label>
        </div>
    </div>
</div>
```

**CSS Obrigatório para Form-Floating:**
```css
/* ===== PADRÃO OFICIAL - FORM-FLOATING ===== */
/* ⚠️ NUNCA ALTERAR ESTAS REGRAS - ELAS GARANTEM FUNCIONAMENTO PERFEITO */
.form-floating .form-control {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
    line-height: 1.5 !important;
    min-height: 58px !important;
}

.form-floating .form-control:focus {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

.form-floating .form-control:not(:placeholder-shown) {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

/* Alinhamento dos campos em linha */
.row.g-3 {
    align-items: end;
}
```

---

## 7. Validação nos Modais

### 📋 **Validação Visual (Obrigatória)**
```html
<!-- Campo com Validação -->
<div class="col-md-6">
    <div class="form-floating">
        <input type="text" 
               class="form-control" 
               :class="{ 'is-invalid': errors.nome }"
               id="nome" 
               v-model="form.nome"
               placeholder="Nome completo"
               required>
        <label for="nome">Nome Completo</label>
    </div>
    <div class="invalid-feedback" v-if="errors.nome">
        {{ errors.nome[0] }}
    </div>
</div>

<!-- Select com Validação -->
<div class="col-md-6">
    <div class="form-floating">
        <select class="form-control" 
                :class="{ 'is-invalid': errors.status }"
                id="status" 
                v-model="form.status"
                required>
            <option value="">Selecione...</option>
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>
        </select>
        <label for="status">Status</label>
    </div>
    <div class="invalid-feedback" v-if="errors.status">
        {{ errors.status[0] }}
    </div>
</div>
```

**CSS Obrigatório para Validação:**
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

## 8. Estados de Loading

### 📋 **Botões com Loading (Obrigatório)**
```html
<!-- Botão Salvar com Loading -->
<button type="button" class="btn btn-success" @click="salvar" :disabled="salvando">
    <span v-if="salvando" class="spinner-border spinner-border-sm me-2" role="status"></span>
    <i v-else class="fas fa-save me-2"></i>
    {{ salvando ? 'Salvando...' : 'Salvar' }}
</button>

<!-- Botão Excluir com Loading -->
<button type="button" class="btn btn-danger" @click="confirmarExclusao" :disabled="excluindo">
    <span v-if="excluindo" class="spinner-border spinner-border-sm me-2" role="status"></span>
    <i v-else class="fas fa-trash me-2"></i>
    {{ excluindo ? 'Excluindo...' : 'Excluir' }}
</button>
```

---

## 9. Tamanhos dos Modais

### 📋 **Tamanhos Padronizados (Obrigatório)**
```html
<!-- Modal Pequeno (formulários simples) -->
<div class="modal-dialog">

<!-- Modal Médio (formulários com mais campos) -->
<div class="modal-dialog modal-lg">

<!-- Modal Grande (formulários complexos ou visualização) -->
<div class="modal-dialog modal-xl">

<!-- Modal Extra Grande (dashboards ou relatórios) -->
<div class="modal-dialog modal-fullscreen">
```

**Regras de Tamanho:**
- ✅ **Modal Pequeno**: Formulários simples (1-3 campos)
- ✅ **Modal Médio**: Formulários padrão (4-6 campos)
- ✅ **Modal Grande**: Formulários complexos (7+ campos)
- ✅ **Modal Extra Grande**: Dashboards ou visualizações especiais

---

## 10. Responsividade

### 📱 **Breakpoints Obrigatórios**
```css
/* Mobile First */
@media (max-width: 576px) {
    .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
    }
    
    .modal-body {
        padding: 1rem !important;
    }
    
    .modal-footer {
        padding: 1rem !important;
    }
}

@media (max-width: 768px) {
    .modal-header {
        padding: 1rem !important;
    }
    
    .modal-body {
        padding: 1.5rem !important;
    }
    
    .row.g-3 {
        margin: 0;
    }
    
    .col-md-6 {
        width: 100%;
        margin-bottom: 1rem;
    }
}

@media (max-width: 992px) {
    .modal-dialog.modal-lg {
        max-width: 95%;
    }
    
    .modal-dialog.modal-xl {
        max-width: 95%;
    }
}
```

---

## 11. Z-Index e Sobreposições

### 🎯 **Z-Index Automático (Obrigatório)**
```css
/* Z-index configurado automaticamente via modern-interface.css */
/* NÃO é necessário configurar manualmente */

/* Modais principais */
.modal {
    z-index: 1055 !important;
}

/* Modais de confirmação */
.modal-backdrop {
    z-index: 1054 !important;
}
```

**Regras de Z-Index:**
- ✅ **Z-index aplicado automaticamente** via CSS global
- ✅ **Modais sempre acima do header fixo** (z-index: 10001+)
- ✅ **Compatível com todos os modais Bootstrap** existentes e futuros
- ✅ **NÃO requer alterações** nos componentes Vue.js

---

## 12. Checklist de Implementação

### 📋 **Estrutura Base**
- [ ] Modal com `modal fade` e `tabindex="-1"`
- [ ] Header com `custom-modal-header` e gradiente
- [ ] Corpo com `modal-body`
- [ ] Rodapé com `modal-footer border-0`

### 📋 **Header do Modal**
- [ ] Gradiente de verde para azul implementado
- [ ] Ícone circular com fundo semi-transparente
- [ ] Título branco com peso 600
- [ ] Botão fechar branco configurado

### 📋 **Botões dos Modais**
- [ ] Botão cancelar com `btn-secondary` (cinza)
- [ ] Botão salvar com `btn-success` (verde)
- [ ] Botão excluir com `btn-danger` (vermelho)
- [ ] Estados de loading implementados

### 📋 **Formulários**
- [ ] Campos com `form-floating` implementados
- [ ] CSS padrão para form-floating incluído
- [ ] Validação visual com `is-invalid` implementada
- [ ] Feedback de erro com `invalid-feedback` visível

### 📋 **Responsividade**
- [ ] Breakpoints implementados
- [ ] Layout adaptativo para mobile
- [ ] Campos empilhados em telas pequenas
- [ ] Padding adaptativo para diferentes telas

---

## 13. Exemplo Completo

### 📋 **Modal Completo de Formulário**
```vue
<template>
    <!-- Modal de Formulário -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" ref="modalUsuarioRef">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Header com Gradiente -->
                <div class="modal-header custom-modal-header">
                    <div class="d-flex align-items-center">
                        <div class="header-icon">
                            <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
                        </div>
                        <h5 class="modal-title mb-0">
                            {{ modoEdicao ? 'Editar Usuário' : 'Novo Usuário' }}
                        </h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body">
                    <form @submit.prevent="salvar">
                        <div class="row g-3">
                            <!-- Campo Nome -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           :class="{ 'is-invalid': errors.nome }"
                                           id="nome" 
                                           v-model="form.nome"
                                           placeholder="Nome completo"
                                           required>
                                    <label for="nome">Nome Completo</label>
                                </div>
                                <div class="invalid-feedback" v-if="errors.nome">
                                    {{ errors.nome[0] }}
                                </div>
                            </div>
                            
                            <!-- Campo Email -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" 
                                           class="form-control" 
                                           :class="{ 'is-invalid': errors.email }"
                                           id="email" 
                                           v-model="form.email"
                                           placeholder="Email"
                                           required>
                                    <label for="email">Email</label>
                                </div>
                                <div class="invalid-feedback" v-if="errors.email">
                                    {{ errors.email[0] }}
                                </div>
                            </div>
                            
                            <!-- Campo Status -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-control" 
                                            :class="{ 'is-invalid': errors.status }"
                                            id="status" 
                                            v-model="form.status" 
                                            required>
                                        <option value="">Selecione...</option>
                                        <option value="ativo">Ativo</option>
                                        <option value="inativo">Inativo</option>
                                    </select>
                                    <label for="status">Status</label>
                                </div>
                                <div class="invalid-feedback" v-if="errors.status">
                                    {{ errors.status[0] }}
                                </div>
                            </div>
                            
                            <!-- Campo Observações -->
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" 
                                              id="observacoes" 
                                              v-model="form.observacoes"
                                              placeholder="Observações"
                                              style="height: 100px"></textarea>
                                    <label for="observacoes">Observações</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Rodapé do Modal -->
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" @click="salvar" :disabled="salvando">
                        <span v-if="salvando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        <i v-else class="fas fa-save me-2"></i>
                        {{ salvando ? 'Salvando...' : 'Salvar' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* ⚠️ SEMPRE INCLUIR ESTE CSS PADRÃO */
.form-floating .form-control {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
    line-height: 1.5 !important;
    min-height: 58px !important;
}

.form-floating .form-control:focus {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

.form-floating .form-control:not(:placeholder-shown) {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
}

/* Header personalizado do modal */
.custom-modal-header {
    background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
    color: white;
    border-bottom: none;
    padding: 1.5rem;
    border-radius: 0.5rem 0.5rem 0 0;
}

/* Ícone do header */
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

/* Título do modal */
.custom-modal-header .modal-title {
    color: white;
    font-weight: 600;
    font-size: 1.25rem;
}

/* Botão fechar */
.custom-modal-header .btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* Validação */
.form-control.is-valid {
    border-color: #5EA853 !important;
    box-shadow: 0 0 0 0.2rem rgba(94, 168, 83, 0.25) !important;
}

.form-control.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.invalid-feedback {
    color: #dc3545 !important;
    font-size: 0.875rem !important;
}

/* Responsividade */
@media (max-width: 576px) {
    .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
    }
    
    .modal-body {
        padding: 1rem !important;
    }
    
    .modal-footer {
        padding: 1rem !important;
    }
}

@media (max-width: 768px) {
    .modal-header {
        padding: 1rem !important;
    }
    
    .modal-body {
        padding: 1.5rem !important;
    }
    
    .row.g-3 {
        margin: 0;
    }
    
    .col-md-6 {
        width: 100%;
        margin-bottom: 1rem;
    }
}
</style>
```

---

## 14. Conclusão

### 📋 **Resumo dos Padrões de Modais**
1. **Header**: Gradiente verde para azul com ícone circular
2. **Botões**: Cores padronizadas (verde para salvar, cinza para cancelar)
3. **Formulários**: Form-floating com CSS obrigatório
4. **Validação**: Visual com estados de erro e sucesso
5. **Loading**: Estados de carregamento nos botões
6. **Responsividade**: Breakpoints para todas as telas
7. **Z-index**: Configurado automaticamente

### 🔗 **Próximos Passos**
- **Para interfaces simples**: Consulte `05_padrao_interface_simples.md`
- **Para interfaces com abas**: Consulte `06_padrao_interface_com_abas.md`
- **Para padrões universais**: Consulte `02_padrao_layout_interface.md`

---

> **IMPORTANTE**: Este documento define padrões para modais. Para outros tipos de interface, consulte os arquivos correspondentes.
