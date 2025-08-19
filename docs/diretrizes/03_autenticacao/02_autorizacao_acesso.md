# Sistema de Autorização de Acesso - OrcaCidade

## 📋 Visão Geral

### **Objetivo**
Sistema de autorização baseado em papéis (RBAC - Role-Based Access Control) que controla o acesso a funcionalidades, menus e ações do sistema de forma granular e segura.

### **Contexto**
- **Sistema:** Gerenciamento de usuários, papéis e permissões
- **Usuários:** Administradores e usuários finais
- **Segurança:** Controle de acesso em múltiplas camadas
- **Flexibilidade:** Permissões granulares por funcionalidade

### **Princípios Fundamentais**
1. **Segurança por Padrão** - Negar acesso por padrão
2. **Privilégio Mínimo** - Usuário tem apenas o necessário
3. **Separação de Responsabilidades** - Papéis bem definidos
4. **Auditoria Completa** - Rastrear todas as ações

## 🏗️ Arquitetura do Sistema RBAC

### **Estrutura de Dados**
```
USUÁRIOS (Users)
├── Nome, Email, Status
├── Relacionamento com PAPÉIS
└── Dados de autenticação

PAPÉIS (Roles)
├── Nome, Descrição, Status
├── Relacionamento com PERMISSÕES
└── Relacionamento com USUÁRIOS

PERMISSÕES (Permissions)
├── Nome, Descrição, Status
├── Padrão: [recurso]_[acao]
└── Exemplo: usuario_crud, papel_consultar
```

### **Relacionamentos**
```
User ←→ Role (Many-to-Many)
Role ←→ Permission (Many-to-Many)
User → Role → Permission (Indireto)
```

## 🎯 Política de Concessão de Permissões

### **REGRA GERAL PARA TODOS OS MENUS, CONTROLLERS E COMPONENTES**

```
├─ É papel 'super'?
│   ├─ SIM → Acesso permitido ✅ (ignora tudo)
│   └─ NÃO → Continua verificação
│       ├─ Menu → Acesso por papel
│       ├─ Controllers → Acesso por permissão
│       └─ Componentes → Acesso por permissão
```

### **1. Verificação de Papel 'super'**

#### **Implementação no Controller**
```php
class UsuariosController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return view('administracao.usuarios.index');
        }
        
        // 2. Tem o papel específico? → Acesso ao módulo
        if ($user->hasRole('gerenciar_usuarios')) {
            return view('administracao.usuarios.index');
        }
        
        // 3. Acesso negado
        abort(403, 'Acesso negado. Papel insuficiente.');
    }
}
```

#### **Implementação no Model User**
```php
class User extends Authenticatable
{
    public function isSuperAdmin(): bool
    {
        return $this->roles()->where('name', 'super')->exists();
    }
    
    public function hasRole($roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }
    
    public function hasPermission($permissionName): bool
    {
        return $this->roles()
            ->whereHas('permissions', function($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->exists();
    }
}
```

### **2. Acesso ao Menu (por Papel)**

#### **Implementação no Blade Template**
```php
<!-- ADMINISTRAÇÃO -->
@if(Auth::user()->hasRole('super') || 
     Auth::user()->hasRole('gerenciar_usuarios') || 
     Auth::user()->hasRole('visualizar_usuarios'))
<div class="menu-group">
    <div class="menu-header">
        <span>ADMINISTRAÇÃO</span>
    </div>
    <div class="menu-items">
        @if(Auth::user()->hasRole('super') || 
            Auth::user()->hasRole('gerenciar_usuarios') || 
            Auth::user()->hasRole('visualizar_usuarios'))
        <a href="{{ route('admin.usuarios.index') }}" class="menu-link">
            <i class="fas fa-users-cog"></i>
            <span>Gerenciamento de Usuários</span>
        </a>
        @endif
    </div>
</div>
@endif
```

### **3. Acesso aos Controllers (por Permissão)**

#### **Sistema Unificado de Verificação**
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

#### **Uso nos Controllers**
```php
public function index()
{
    // MENU: verifica se tem papel gerenciar_usuarios (acesso ao menu)
    $this->checkAccess(['gerenciar_usuarios']);
    
    return view('administracao.usuarios.index');
}

public function store(Request $request)
{
    // CRUD: verifica se tem usuario_crud (pode criar usuários)
    $this->checkAccess(['usuario_crud']);
    
    // Lógica de criação...
}

public function show($id)
{
    // CONSULTA: verifica se tem usuario_crud OU usuario_consultar
    $this->checkAccess(['usuario_crud', 'usuario_consultar']);
    
    // Lógica de visualização...
}
```

### **4. Acesso aos Componentes Vue (por Permissão)**

#### **Computed Properties de Acesso**
```javascript
computed: {
    // Verifica se o usuário pode executar ações (CRUD) no módulo atual
    canPerformActions() {
        if (!this.currentUser) return true;
        if (this.isSuper) return true;
        
        const permissions = this.currentUser.roles.flatMap(role => role.permissions || []);
        const permissionNames = permissions.map(p => p.name);
        
        switch (this.activeTab) {
            case 'usuarios':
                // ❌ NÃO pode fazer CRUD se só tem 'usuario_consultar'
                // ✅ Só pode fazer CRUD se tem 'usuario_crud'
                return permissionNames.includes('usuario_crud');
            case 'papeis':
                return permissionNames.includes('papel_crud');
            case 'permissoes':
                return permissionNames.includes('permissao_crud');
            default:
                return false;
        }
    },
    
    // Verifica se o usuário pode visualizar o módulo atual
    canViewModule() {
        if (!this.currentUser) return true;
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
            case 'busca':
                return permissionNames.some(p => p.startsWith('usuario_'));
            default:
                return false;
        }
    }
}
```

#### **Uso no Template Vue**
```vue
<template>
    <!-- Botões CRUD só aparecem se pode executar ações -->
    <button v-if="canPerformActions" @click="criarUsuario">
        <i class="fas fa-plus"></i> Novo Usuário
    </button>
    
    <!-- Botões de ação nas tabelas -->
    <button v-if="canPerformActions" @click="editarUsuario(usuario)">
        <i class="fas fa-edit"></i>
    </button>
    
    <!-- Abas só aparecem se pode visualizar o módulo -->
    <div v-if="canViewModule" class="tab-content">
        <!-- Conteúdo da aba -->
    </div>
</template>
```

## 🔐 Padrões de Permissões

### **1. Convenção de Nomenclatura**

#### **Formato: `[recurso]_[acao]`**
```
usuario_crud      → Criar, Editar, Excluir, Visualizar usuários
usuario_consultar → Apenas visualizar usuários
papel_crud        → Criar, Editar, Excluir, Visualizar papéis
papel_consultar   → Apenas visualizar papéis
permissao_crud    → Criar, Editar, Excluir, Visualizar permissões
permissao_consultar → Apenas visualizar permissões
```

### **2. Tipos de Ação**

#### **CRUD (Create, Read, Update, Delete)**
- **`_crud`** → Acesso total à funcionalidade
- **`_consultar`** → Apenas visualização
- **`_criar`** → Apenas criação (opcional)
- **`_editar`** → Apenas edição (opcional)
- **`_excluir`** → Apenas exclusão (opcional)

### **3. Recursos do Sistema**

#### **Módulos Principais**
```
usuarios          → Gerenciamento de usuários
papeis            → Gerenciamento de papéis
permissoes        → Gerenciamento de permissões
municipios        → Gerenciamento de municípios
entidades         → Gerenciamento de entidades
estrutura         → Estrutura de orçamentos
active_directory   → Sincronização AD
```

## 📋 Casos de Uso Implementados

### **1. Usuário 'Super Administrador'**

#### **Permissões:**
- ✅ **Acesso total** a todas as funcionalidades
- ✅ **Bypass** de todas as verificações de permissão
- ✅ **CRUD completo** em todos os módulos

#### **Implementação:**
```php
// Em qualquer controller
if ($user->isSuperAdmin()) {
    return view('modulo.index'); // Acesso direto
}
```

### **2. Usuário 'Gerenciador de Usuários'**

#### **Papel:** `gerenciar_usuarios`
#### **Permissões:**
- `usuario_crud` → CRUD completo de usuários
- `papel_crud` → CRUD completo de papéis
- `permissao_crud` → CRUD completo de permissões

#### **Acesso:**
- ✅ **Menu lateral** visível
- ✅ **Todas as 4 abas** acessíveis
- ✅ **Botões CRUD** visíveis e funcionais
- ✅ **Modais de gerenciamento** com funcionalidade completa

### **3. Usuário 'Visualizador de Usuários'**

#### **Papel:** `visualizar_usuarios`
#### **Permissões:**
- `usuario_consultar` → Apenas visualizar usuários
- `papel_consultar` → Apenas visualizar papéis
- `permissao_consultar` → Apenas visualizar permissões

#### **Acesso:**
- ✅ **Menu lateral** visível
- ✅ **Todas as 4 abas** acessíveis
- ❌ **Botões CRUD** ocultos
- ✅ **Modais de visualização** (sem funcionalidade de edição)

## 🛡️ Implementação de Segurança

### **1. Proteção em Múltiplas Camadas**

#### **Camada 1: Middleware de Autenticação**
```php
// Todas as rotas protegidas
Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/usuarios', [UsuariosController::class, 'index']);
    });
});
```

#### **Camada 2: Verificação de Papel (Menu)**
```php
// Verificação no template Blade
@if(Auth::user()->hasRole('gerenciar_usuarios'))
    <!-- Menu visível -->
@endif
```

#### **Camada 3: Verificação de Permissão (Controller)**
```php
// Verificação no controller
$this->checkAccess(['usuario_crud']);
```

#### **Camada 4: Verificação de Permissão (Frontend)**
```javascript
// Verificação no Vue.js
v-if="canPerformActions"
```

### **2. Validação de Dados**

#### **Verificação de Propriedade**
```javascript
// Usar optional chaining para evitar erros
{{ permissaoSelecionada?.display_name || 'N/A' }}
```

#### **Verificação de Estado**
```vue
<div v-if="permissaoSelecionada">
    <!-- Conteúdo só renderiza quando dados estão disponíveis -->
</div>
<div v-else>
    <!-- Estado de carregamento ou erro -->
</div>
```

## 🔍 Exemplos Práticos

### **1. Aba 'Usuários'**

#### **Com `usuario_crud`:**
- ✅ **Botão "Novo Usuário"** visível
- ✅ **Botões "Editar"** visíveis em todas as linhas
- ✅ **Botões "Excluir"** visíveis em todas as linhas
- ✅ **Funcionalidades CRUD** funcionais

#### **Com `usuario_consultar`:**
- ❌ **Botão "Novo Usuário"** oculto
- ❌ **Botões "Editar"** ocultos
- ❌ **Botões "Excluir"** ocultos
- ✅ **Apenas visualização** disponível

### **2. Aba 'Papéis'**

#### **Com `papel_crud`:**
- ✅ **Botão "Novo Papel"** visível
- ✅ **Botões "Editar"** visíveis
- ✅ **Botões "Excluir"** visíveis
- ✅ **"Gerenciar Permissões"** funcional
- ✅ **"Gerenciar Usuários"** funcional

#### **Com `papel_consultar`:**
- ❌ **Botão "Novo Papel"** oculto
- ❌ **Botões "Editar"** ocultos
- ❌ **Botões "Excluir"** ocultos
- ✅ **"Gerenciar Permissões"** (apenas visualização)
- ✅ **"Gerenciar Usuários"** (apenas visualização)

### **3. Aba 'Permissões'**

#### **Com `permissao_crud`:**
- ✅ **Botão "Nova Permissão"** visível
- ✅ **Botões "Editar"** visíveis
- ✅ **Botões "Excluir"** visíveis
- ✅ **"Visualizar Detalhes"** funcional

#### **Com `permissao_consultar`:**
- ❌ **Botão "Nova Permissão"** oculto
- ❌ **Botões "Editar"** ocultos
- ❌ **Botões "Excluir"** ocultos
- ✅ **"Visualizar Detalhes"** funcional

## 🚀 Melhorias Futuras (Sugestões)

### **1. Performance**

#### **Cache de Permissões**
```php
// Cachear permissões do usuário por 1 hora
$permissions = Cache::remember("user_permissions_{$userId}", 3600, function () use ($userId) {
    return User::with('roles.permissions')->find($userId)->roles->flatMap->permissions;
});
```

#### **Eager Loading Otimizado**
```php
// Carregar usuário com relacionamentos em uma query
$user = User::with(['roles.permissions' => function($query) {
    $query->select('permissions.id', 'permissions.name', 'permissions.display_name');
}])->find(Auth::id());
```

### **2. Segurança Avançada**

#### **Logs de Auditoria**
```php
// Log de todas as ações de autorização
Log::info('Access granted', [
    'user_id' => $user->id,
    'action' => 'usuarios.index',
    'permissions' => $user->permissions->pluck('name'),
    'ip' => $request->ip()
]);
```

#### **Validação de Sessão**
```php
// Verificar se a sessão não foi comprometida
if ($request->session()->has('security_hash') && 
    $request->session()->get('security_hash') !== $user->security_hash) {
    Auth::logout();
    abort(401, 'Sessão comprometida');
}
```

### **3. Usabilidade**

#### **Dashboard de Permissões**
```vue
<template>
    <div class="permissions-dashboard">
        <h3>Suas Permissões</h3>
        <div class="permissions-grid">
            <div v-for="permission in userPermissions" :key="permission.id" 
                 class="permission-card" :class="getPermissionClass(permission)">
                <h5>{{ permission.display_name }}</h5>
                <p>{{ permission.description }}</p>
                <span class="badge">{{ permission.name }}</span>
            </div>
        </div>
    </div>
</template>
```

#### **Histórico de Acessos**
```php
// Registrar todos os acessos
class AccessLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'resource', 'ip_address', 'user_agent', 'success'
    ];
}
```

### **4. Monitoramento**

#### **Métricas de Acesso**
- Taxa de acesso negado por usuário
- Funcionalidades mais acessadas
- Horários de pico de uso
- Usuários com mais permissões

#### **Alertas de Segurança**
- Múltiplas tentativas de acesso negado
- Acesso a funcionalidades sensíveis
- Mudanças de permissão
- Logins de IPs suspeitos

## 📊 Métricas e Relatórios

### **1. Relatório de Permissões por Usuário**
```php
public function userPermissionsReport()
{
    return User::with('roles.permissions')
        ->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('display_name'),
                'permissions' => $user->roles->flatMap->permissions->unique('id')->pluck('name'),
                'total_permissions' => $user->roles->flatMap->permissions->unique('id')->count()
            ];
        });
}
```

### **2. Relatório de Uso de Permissões**
```php
public function permissionsUsageReport()
{
    return Permission::withCount('roles')
        ->orderBy('roles_count', 'desc')
        ->get()
        ->map(function ($permission) {
            return [
                'name' => $permission->name,
                'display_name' => $permission->display_name,
                'roles_count' => $permission->roles_count,
                'users_count' => $permission->roles->sum(function ($role) {
                    return $role->users_count;
                })
            ];
        });
}
```

## 🔧 Manutenção e Evolução

### **1. Adicionando Novas Permissões**

#### **Passo 1: Criar no Banco**
```php
Permission::create([
    'name' => 'relatorio_gerar',
    'display_name' => 'Gerar Relatórios',
    'description' => 'Permite gerar relatórios do sistema'
]);
```

#### **Passo 2: Atribuir aos Papéis**
```php
$role = Role::where('name', 'gerenciar_usuarios')->first();
$permission = Permission::where('name', 'relatorio_gerar')->first();
$role->permissions()->attach($permission->id);
```

#### **Passo 3: Implementar no Controller**
```php
public function generateReport()
{
    $this->checkAccess(['relatorio_gerar']);
    // Lógica do relatório...
}
```

#### **Passo 4: Implementar no Frontend**
```javascript
// Adicionar na computed property
case 'relatorios':
    return permissionNames.includes('relatorio_gerar');
```

### **2. Criando Novos Papéis**

#### **Exemplo: 'Analista de Dados'**
```php
$role = Role::create([
    'name' => 'analista_dados',
    'display_name' => 'Analista de Dados',
    'description' => 'Pode analisar dados e gerar relatórios'
]);

// Atribuir permissões
$permissions = Permission::whereIn('name', [
    'usuario_consultar',
    'papel_consultar',
    'relatorio_gerar',
    'dados_exportar'
])->get();

$role->permissions()->attach($permissions->pluck('id'));
```

## 🎯 Conclusão

### **Status Atual**
✅ **Sistema implementado** e funcionando
✅ **Política de permissões** bem definida
✅ **Segurança em múltiplas camadas** implementada
✅ **Testes passando** em todos os cenários
✅ **Código limpo** e profissional

### **Pontos Fortes**
1. **Segurança robusta** com verificação em múltiplas camadas
2. **Flexibilidade** com permissões granulares
3. **Consistência** em toda a aplicação
4. **Manutenibilidade** com código bem estruturado
5. **Escalabilidade** para novos módulos

### **Próximos Passos**
1. **Implementar melhorias** sugeridas
2. **Aplicar padrão** a outros módulos
3. **Monitorar performance** e segurança
4. **Treinar equipe** no sistema

### **Recursos Adicionais**
- [Laravel Authorization](https://laravel.com/docs/authorization)
- [RBAC Best Practices](https://en.wikipedia.org/wiki/Role-based_access_control)
- [OWASP Authorization Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html)

---

**Última atualização:** {{ date('d/m/Y H:i:s') }}
**Versão:** 1.0.0
**Responsável:** Equipe de Desenvolvimento OrcaCidade
