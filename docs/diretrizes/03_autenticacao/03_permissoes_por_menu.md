# 🔐 PERMISSÕES POR MENU - SISTEMA DE CONTROLE DE ACESSO

## **📋 VISÃO GERAL**

Este documento define **todas as permissões necessárias** para cada menu do sistema, seguindo o padrão de controle de acesso implementado.

**⚠️ IMPORTANTE:** Apenas documentamos menus que foram **definidos e implementados**. Outros menus serão documentados conforme forem desenvolvidos.

---

## **🎯 REGRA GERAL (APLICA-SE A TODO O SISTEMA)**

```
(REGRA GERAL PARA TODOS OS MENUS, CONTROLLERS E COMPONENTES)
├─ É papel 'super'?
│   ├─ SIM → Acesso permitido ✅ (ignora tudo)
│   └─ NÃO → Continua verificação
```

**Nota:** Usuários com papel `super` têm acesso total a todas as funcionalidades, ignorando verificações de permissão.

---

## **📱 MENUS DO SISTEMA**

### **1. 👥 GERENCIAMENTO DE USUÁRIOS** ✅ IMPLEMENTADO
```
[MENU] acesso se tiver papel 'super' OU papel 'gerenciar_usuarios'

[CONTROLLERS E COMPONENTES]

(ABA USUARIOS) 
🔑 usuario_crud → Inserir/Editar/Excluir + Visualizar
👁️ usuario_consultar → Apenas Visualizar (sem botões de ação)

(ABA PAPÉIS ) 
🔑 papel_crud → Inserir/Editar/Excluir + Visualizar
👁️ papel_consultar → Apenas Visualizar (sem botões de ação)

(ABA PERMISSÕES) 
🔑 permissao_crud → Inserir/Editar/Excluir + Visualizar
👁️ permissao_consultar → Apenas Visualizar (sem botões de ação)

---

## **📋 REGRAS DE APLICAÇÃO DETALHADAS**

### **🔑 PERMISSÃO '_crud' (ACESSO TOTAL)**
- ✅ **Visualiza** botões de ação CRUD nos componentes
- ✅ **Acesso** aos métodos CRUD no controller (store, update, destroy)
- ✅ **Pode consultar** tudo (index, show, listagens)

### **👁️ PERMISSÃO '_consultar' (ACESSO LIMITADO)**
- ❌ **NÃO visualiza** botões de ação CRUD nos componentes
- ❌ **NÃO tem acesso** aos métodos CRUD no controller
- ✅ **Pode consultar** tudo (index, show, listagens)

---

## **🎯 IMPLEMENTAÇÃO NOS CONTROLLERS**

### **📋 MÉTODOS DE CONSULTA (Visualizar)**
```php
// AMBOS podem acessar: usuario_crud OU usuario_consultar
public function index() {
    $this->checkAccess(['usuario_crud', 'usuario_consultar']);
}

public function show($id) {
    $this->checkAccess(['usuario_crud', 'usuario_consultar']);
}
```

### **📋 MÉTODOS DE CRUD (Modificar)**
```php
// APENAS CRUD pode acessar: usuario_crud
public function store() {
    $this->checkAccess('usuario_crud');
}

public function update() {
    $this->checkAccess('usuario_crud');
}

public function destroy() {
    $this->checkAccess('usuario_crud');
}
```

---

## **🎨 IMPLEMENTAÇÃO NOS COMPONENTES VUE**

### **📋 COMPUTED PROPERTIES**
```javascript
computed: {
    // Pode executar ações CRUD
    canPerformActions() {
        if (this.isSuper) return true;
        return this.hasPermission('usuario_crud'); // Só CRUD
    },
    
    // Pode visualizar o módulo
    canViewModule() {
        if (this.isSuper) return true;
        return this.hasPermission('usuario_crud') || 
               this.hasPermission('usuario_consultar'); // Ambos
    }
}
```

### **📋 TEMPLATE**
```vue
<!-- Botões de ação - só aparecem para CRUD -->
<button v-if="canPerformActions" @click="criarUsuario">
    <i class="fas fa-plus"></i> Novo Usuário
</button>

<!-- Conteúdo - todos podem ver -->
<div v-if="canViewModule">
    <!-- Lista de usuários -->
</div>
```
```

**Permissões necessárias:** `usuario_crud`, `usuario_consultar`, `papel_crud`, `papel_consultar`, `permissao_crud`, `permissao_consultar`

---
## **🔑 RESUMO DE PERMISSÕES IMPLEMENTADAS**

### **MÓDULO GERENCIAMENTO DE USUÁRIOS:** ✅ COMPLETO
- `usuario_crud`, `usuario_consultar`
- `papel_crud`, `papel_consultar`
- `permissao_crud`, `permissao_consultar`

**Total:** 6 permissões implementadas

---

## **📋 IMPLEMENTAÇÃO GRADUAL**

### **FASE 1: ✅ COMPLETO**
- ✅ Gerenciamento de Usuários (6 permissões)

### **FASE 2: 📋 A SEREM DEFINIDAS**
- 📋 Outros módulos serão documentados conforme forem desenvolvidos

---

## **💡 PADRÃO DE NOMENCLATURA**

```
{modulo}_{acao}

Exemplos implementados:
- usuario_crud
- papel_consultar
- permissao_crud
```

---

## **⚠️ IMPORTANTE**

1. **Menu Lateral:** Controlado por **PAPÉIS** (`gerenciar_usuario`, etc.)
2. **Funcionalidades:** Controladas por **PERMISSÕES** (`usuario_crud`, etc.)
3. **Papel 'super':** Bypass total em todas as verificações
4. **Implementação:** Gradual, módulo por módulo
5. **Documentação:** Apenas do que foi definido e implementado

---

## **🔄 PRÓXIMOS PASSOS**

1. **Definir** o próximo menu a ser implementado
2. **Estabelecer** suas permissões específicas
3. **Implementar** o controle de acesso
4. **Documentar** neste arquivo
5. **Repetir** o processo

---

*Documento atualizado em: {{ date('d/m/Y H:i:s') }}*
*Status: Apenas Gerenciamento de Usuários implementado*
