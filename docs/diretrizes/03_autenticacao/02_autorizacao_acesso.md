# Diretriz de Autorização de Acesso - OrçaCidade

> **DOCUMENTO MESTRE**: Este documento define como funciona o sistema de autorização e controle de acesso no projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para manter segurança e consistência.

> **ATUALIZADO EM 2025**: Sistema RBAC evoluído para Vue.js + API com verificação de permissões em múltiplas camadas.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Estabelecer padrões para controle de acesso baseado em papéis (RBAC), garantindo segurança consistente em rotas, menus, controllers e componentes.

### 🏗️ **Arquitetura RBAC**
- **Sistema**: Role-Based Access Control (RBAC)
- **Estrutura**: Papéis → Permissões → Usuários
- **Verificação**: Múltiplas camadas (Backend + Frontend)
- **Hierarquia**: Super Admin → Papéis → Permissões específicas

---

## 2. Regra Geral Universal

### 🚀 **Bypass do Super Admin**

#### **Implementação Padrão**
```php
// SEMPRE implementar primeiro
if ($user->isSuperAdmin()) {
    return true; // Acesso total - ignora todas as verificações
}
```

#### **Lógica de Verificação**
```
├─ É papel 'super'?
│   ├─ SIM → Acesso permitido ✅ (ignora tudo)
│   └─ NÃO → Continua verificação
```

#### **Código de Exemplo**
```php
private function checkPermissions()
{
    $user = Auth::user();
    
    // 1. É super admin? → Acesso total
    if ($user->isSuperAdmin()) {
        return true;
    }
    
    // 2. Continua verificação normal...
}
```

---

## 3. Estrutura de Permissões

### 🔑 **Nomenclatura Padrão**

#### **Formato Geral**
```
[modulo]_[acao]
```

#### **Exemplos de Permissões**
- **usuarios**: `usuario_crud`, `usuario_consultar`
- **papeis**: `papel_crud`, `papel_consultar`
- **permissoes**: `permissao_crud`, `permissao_consultar`
- **menus**: `gerenciar_usuarios`, `gerenciar_orcamento`

#### **Tipos de Acesso**
- **`_crud`**: Inserir, Editar, Excluir + Visualizar
- **`_consultar`**: Apenas Visualizar (sem botões de ação)

---

## 4. Proteção de Rotas

### 🛣️ **Middleware de Autenticação**

#### **1. Rotas Públicas**
```php
// Rotas que não precisam de autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
```

#### **2. Rotas Protegidas por Autenticação**
```php
Route::middleware(['auth'])->group(function () {
    // Todas as rotas aqui precisam de login
    Route::prefix('admin')->name('admin.')->group(function () {
        // Rotas administrativas
    });
});
```

#### **3. Rotas Protegidas por Permissão**
```php
// Implementar verificação no controller
Route::prefix('api/administracao')->name('api.administracao.')->group(function () {
    Route::apiResource('usuarios', UsuariosController::class);
    // Verificação de permissões feita no controller
});
```

---

## 5. Verificação em Controllers

### 🎮 **Padrão de Verificação Flexível**

#### **1. Sistema Unificado de Verificação**
```php
private function checkAccess($permissions, $requireAll = false)
{
    $user = Auth::user();
    
    // 1. É super admin? → Acesso total
    if ($user->isSuperAdmin()) {
        return true;
    }
    
    // 2. Verificação flexível de permissões
    if (is_string($permissions)) {
        $permissions = [$permissions];
    }
    
    if ($requireAll) {
        // Todas as permissões são obrigatórias (AND)
        foreach ($permissions as $permission) {
            if (!$user->hasPermission($permission)) {
                abort(403, "Permissão obrigatória: {$permission}");
            }
        }
    } else {
        // Pelo menos uma permissão é suficiente (OR)
        $hasAnyPermission = false;
        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                $hasAnyPermission = true;
                break;
            }
        }
        
        if (!$hasAnyPermission) {
            abort(403, 'Acesso negado. Permissão insuficiente.');
        }
    }
    
    return true;
}
```

#### **2. Aplicação nos Métodos**
```php
public function index(Request $request)
{
    // Visualizar: usuario_crud OU usuario_consultar
    $this->checkAccess(['usuario_crud', 'usuario_consultar']);
    // Lógica de listagem
}

public function store(Request $request)
{
    // CRUD: apenas usuario_crud
    $this->checkAccess('usuario_crud');
    // Lógica de criação
}

public function update(Request $request, $id)
{
    // CRUD: apenas usuario_crud
    $this->checkAccess('usuario_crud');
    // Lógica de atualização
}

public function destroy($id)
{
    // CRUD: apenas usuario_crud
    $this->checkAccess('usuario_crud');
    // Lógica de exclusão
}

public function relatorio(Request $request)
{
    // PERMISSÕES CONDICIONAIS: usuario_consultar E relatorio_usuarios
    $this->checkAccess(['usuario_consultar', 'relatorio_usuarios'], true);
    // Lógica de relatório
}
```

#### **3. Exemplos de Uso Flexível**
```php
// Uma permissão
$this->checkAccess('usuario_crud');

// Múltiplas permissões (OR) - pelo menos uma
$this->checkAccess(['usuario_crud', 'usuario_consultar']);

// Múltiplas permissões (AND) - todas obrigatórias
$this->checkAccess(['usuario_crud', 'relatorio_usuarios'], true);

// Permissões condicionais complexas
$this->checkAccess(['usuario_crud', 'admin_sistema'], true);
```

---

## 6. Verificação em Componentes Vue.js

### 🎨 **Computed Properties de Acesso**

#### **1. Verificação de Super Admin**
```javascript
computed: {
    // Verifica se o usuário é super admin
    isSuper() {
        return this.currentUser && this.currentUser.roles && 
               this.currentUser.roles.some(role => role.name === 'super');
    }
}
```

#### **2. Verificação de Ações (CRUD)**
```javascript
computed: {
    // Verifica se o usuário pode executar ações (CRUD) no módulo atual
    canPerformActions() {
        // Se ainda não carregou o usuário, permite temporariamente
        if (!this.currentUser) return true;
        
        if (this.isSuper) return true;
        
        if (!this.currentUser.roles) return false;
        
        // Verifica se tem qualquer permissão do módulo atual
        const permissions = this.currentUser.roles.flatMap(role => role.permissions || []);
        const permissionNames = permissions.map(p => p.name);
        
        switch (this.activeTab) {
            case 'usuarios':
                return permissionNames.some(p => p.startsWith('usuario_'));
            case 'papeis':
                return permissionNames.some(p => p.startsWith('papel_'));
            case 'permissoes':
                return permissionNames.some(p => p.startsWith('permissao_'));
            default:
                return false;
        }
    }
}
```

#### **3. Verificação de Visualização**
```javascript
computed: {
    // Verifica se o usuário pode visualizar o módulo atual
    canViewModule() {
        // Se ainda não carregou o usuário, permite temporariamente
        if (!this.currentUser) return true;
        
        if (this.isSuper) return true;
        
        if (!this.currentUser.roles) return false;
        
        // Verifica se tem qualquer permissão do módulo atual
        const permissions = this.currentUser.roles.flatMap(role => role.permissions || []);
        const permissionNames = permissions.map(p => p.name);
        
        switch (this.activeTab) {
            case 'usuarios':
                return permissionNames.some(p => p.startsWith('usuario_'));
            case 'papeis':
                return permissionNames.some(p => p.startsWith('papel_'));
            case 'permissoes':
                return permissionNames.some(p => p.startsWith('permissao_'));
            default:
                return false;
        }
    }
}
```

#### **4. Verificação Específica por Módulo**
```javascript
computed: {
    // Verifica se o usuário pode gerenciar papéis (CRUD completo)
    canManagePapeis() {
        if (!this.currentUser) return true;
        if (this.isSuper) return true;
        
        const permissions = this.currentUser.roles.flatMap(role => role.permissions || []);
        const permissionNames = permissions.map(p => p.name);
        
        return permissionNames.includes('papel_crud');
    },
    
    // Verifica se o usuário pode apenas consultar papéis
    canViewPapeis() {
        if (!this.currentUser) return true;
        if (this.isSuper) return true;
        
        const permissions = this.currentUser.roles.flatMap(role => role.permissions || []);
        const permissionNames = permissions.map(p => p.name);
        
        return permissionNames.includes('papel_consultar');
    }
}
```

---

## 7. Aplicação no Template Vue.js

### 🎯 **Controle de Visibilidade**

#### **1. Botões de Ação (CRUD)**
```vue
<!-- Botão Novo Usuário -->
<button 
    v-if="canPerformActions"
    class="btn btn-outline-success" 
    @click="abrirModalCriarUsuario"
>
    <i class="fas fa-plus"></i>
    <span>Novo Usuário</span>
</button>

<!-- Botões de Editar/Excluir -->
<button 
    v-if="canPerformActions"
    class="btn btn-sm btn-warning" 
    @click="editarUsuario(usuario)" 
    title="Editar"
>
    <i class="fas fa-edit"></i>
</button>

<button 
    v-if="canPerformActions"
    class="btn btn-sm btn-danger" 
    @click="excluirUsuario(usuario)" 
    title="Excluir"
>
    <i class="fas fa-trash"></i>
</button>
```

#### **2. Abas e Seções**
```vue
<!-- Sistema de Abas -->
<ul class="nav nav-tabs admin-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button 
            v-if="canViewModule"
            class="nav-link admin-tab" 
            :class="{ active: activeTab === 'usuarios' }"
            @click="changeTab('usuarios')"
        >
            <i class="fas fa-users me-2"></i>
            Usuários
        </button>
    </li>
</ul>

<!-- Conteúdo das Abas -->
<div class="tab-pane fade" :class="{ 'show active': activeTab === 'usuarios' }" role="tabpanel" v-if="canViewModule">
    <!-- Conteúdo da aba -->
</div>
```

#### **3. Funcionalidades Específicas**
```vue
<!-- Gerenciar Permissões do Papel -->
<button 
    v-if="canManagePapeis || canViewPapeis"
    class="btn btn-sm btn-info" 
    @click="abrirModalGerenciarPermissoes(papel)" 
    title="Gerenciar Permissões"
>
    <i class="fas fa-key"></i>
</button>

<!-- Gerenciar Usuários do Papel -->
<button 
    v-if="canManagePapeis || canViewPapeis"
    class="btn btn-sm btn-info" 
    @click="abrirModalGerenciarUsuarios(papel)" 
    title="Gerenciar Usuários"
>
    <i class="fas fa-users-cog"></i>
</button>
```

---

## 8. Sistema de Menus

### 📋 **Controle de Acesso aos Menus**

#### **1. Verificação de Acesso ao Menu**
```php
// No controller Web
public function index()
{
    $user = Auth::user();
    
    // 1. É super admin? → Acesso total
    if ($user->isSuperAdmin()) {
        return view('administracao.usuarios.index');
    }
    
    // 2. Tem permissão específica?
    if ($user->hasPermission('gerenciar_usuarios')) {
        return view('administracao.usuarios.index');
    }
    
    // 3. Nenhuma das opções → Acesso negado
    abort(403, 'Acesso negado. Permissão insuficiente.');
}
```

#### **2. Estrutura de Permissões por Menu**
```
GERENCIAMENTO DE USUÁRIOS
├─ Menu: gerenciar_usuarios
├─ Aba Usuários: usuario_crud | usuario_consultar
├─ Aba Papéis: papel_crud | papel_consultar
└─ Aba Permissões: permissao_crud | permissao_consultar
```

---

## 9. Exemplos Práticos por Módulo

### 🎯 **Módulo: Gerenciamento de Usuários**

#### **1. Permissões Necessárias**
- **Menu**: `gerenciar_usuarios`
- **Aba Usuários**: `usuario_crud` (CRUD) ou `usuario_consultar` (visualizar)
- **Aba Papéis**: `papel_crud` (CRUD) ou `papel_consultar` (visualizar)
- **Aba Permissões**: `permissao_crud` (CRUD) ou `permissao_consultar` (visualizar)

#### **2. Verificação no Controller**
```php
class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        $this->checkAccess(['usuario_crud', 'usuario_consultar']); // Visualizar
        // Lógica de listagem
    }
    
    public function store(Request $request)
    {
        $this->checkAccess('usuario_crud'); // CRUD
        // Lógica de criação
    }
    
    public function relatorio(Request $request)
    {
        $this->checkAccess(['usuario_consultar', 'relatorio_usuarios'], true); // Condicional
        // Lógica de relatório
    }
}
```

#### **3. Verificação no Componente Vue**
```javascript
computed: {
    canPerformActions() {
        if (this.isSuper) return true;
        
        const permissions = this.currentUser.roles.flatMap(role => role.permissions || []);
        const permissionNames = permissions.map(p => p.name);
        
        switch (this.activeTab) {
            case 'usuarios':
                return permissionNames.some(p => p.startsWith('usuario_'));
            case 'papeis':
                return permissionNames.some(p => p.startsWith('papel_'));
            case 'permissoes':
                return permissionNames.some(p => p.startsWith('permissao_'));
            default:
                return false;
        }
    }
}
```

---

## 10. Padrões de Implementação

### 🔧 **Checklist Obrigatório**

#### **1. Backend (Controllers)**
- [ ] **Implementar** verificação de Super Admin primeiro
- [ ] **Criar** método `checkAccess()` flexível e reutilizável
- [ ] **Aplicar** verificação em todos os métodos
- [ ] **Usar** sistema flexível de permissões (OR/AND)
- [ ] **Retornar** erro 403 com mensagem clara

#### **2. Frontend (Vue.js)**
- [ ] **Implementar** computed property `isSuper`
- [ ] **Implementar** computed property `canPerformActions`
- [ ] **Implementar** computed property `canViewModule`
- [ ] **Aplicar** `v-if` em todos os botões de ação
- [ ] **Aplicar** `v-if` em abas e seções sensíveis

#### **3. Segurança**
- [ ] **Verificar** permissões no backend SEMPRE
- [ ] **Usar** frontend apenas para UX (não para segurança)
- [ ] **Implementar** logs de tentativas de acesso negado
- [ ] **Testar** todos os cenários de permissão

---

## 11. Tratamento de Erros

### ⚠️ **Respostas de Acesso Negado**

#### **1. Backend (API)**
```php
// Retorna erro 403 com mensagem clara
abort(403, 'Acesso negado. Permissão insuficiente.');

// Ou para requisições JSON
return response()->json([
    'error' => 'Acesso negado',
    'message' => 'Permissão insuficiente para esta operação'
], 403);
```

#### **2. Frontend (Vue.js)**
```javascript
// Capturar erro 403
try {
    const response = await axios.get('/api/administracao/usuarios');
} catch (error) {
    if (error.response && error.response.status === 403) {
        this.mostrarToast('Acesso Negado', 'Você não tem permissão para acessar esta funcionalidade', 'fa-ban text-danger');
    }
}
```

---

## 12. Logs e Auditoria

### 📝 **Registro de Acessos**

#### **1. Logs de Acesso Negado**
```php
Log::warning('Tentativa de acesso negado', [
    'user_id' => Auth::id(),
    'user_email' => Auth::user()->email,
    'route' => $request->route()->getName(),
    'method' => $request->method(),
    'permission_required' => 'usuario_crud',
    'timestamp' => now()
]);
```

#### **2. Logs de Acesso Concedido**
```php
Log::info('Acesso concedido', [
    'user_id' => Auth::id(),
    'user_email' => Auth::user()->email,
    'route' => $request->route()->getName(),
    'permission_used' => 'usuario_crud',
    'timestamp' => now()
]);
```

---

## 13. Testes de Segurança

### 🧪 **Cenários de Teste**

#### **1. Usuário Sem Permissões**
- [ ] **Tentar acessar** rota protegida
- [ ] **Verificar** retorno de erro 403
- [ ] **Confirmar** que dados não são retornados
- [ ] **Verificar** logs de acesso negado

#### **2. Usuário com Permissão de Consulta**
- [ ] **Acessar** rota de listagem
- [ ] **Verificar** que dados são retornados
- [ ] **Confirmar** que botões de ação não aparecem
- [ ] **Testar** tentativa de CRUD (deve falhar)

#### **3. Usuário com Permissão de CRUD**
- [ ] **Acessar** todas as funcionalidades
- [ ] **Verificar** que botões de ação aparecem
- [ ] **Testar** operações de CRUD
- [ ] **Confirmar** que tudo funciona

#### **4. Super Admin**
- [ ] **Acessar** todas as funcionalidades
- [ ] **Verificar** que bypass funciona
- [ ] **Testar** operações sem permissões específicas
- [ ] **Confirmar** acesso total

---

## 14. Conclusão

### 🎉 **Sistema RBAC Robusto, Seguro e Flexível**

**Nossa implementação oferece:**

- ✅ **Verificação em múltiplas camadas** (Backend + Frontend)
- ✅ **Bypass automático** para Super Admin
- ✅ **Sistema flexível** de permissões (OR/AND)
- ✅ **Permissões condicionais** complexas
- ✅ **Arquitetura unificada** com `checkAccess()`
- ✅ **Controle de acesso** consistente e escalável
- ✅ **Logs completos** para auditoria
- ✅ **Tratamento de erros** robusto
- ✅ **UX adaptativa** baseada em permissões
- ✅ **Segurança máxima** com verificação backend

**Este sistema garante controle de acesso seguro, flexível e auditável para o projeto OrçaCidade!**

---

> **IMPORTANTE**: Esta diretriz deve ser seguida para todas as funcionalidades que precisam de controle de acesso. Qualquer desvio deve ser documentado e justificado.

> **ÚLTIMA ATUALIZAÇÃO**: Janeiro 2025 - Sistema RBAC evoluído para Vue.js + API com verificação em múltiplas camadas
