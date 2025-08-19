# Diretriz de Autenticação Técnica - OrçaCidade

> **DOCUMENTO MESTRE**: Este documento define como funciona o sistema de autenticação no projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para manter consistência e segurança.

> **ATUALIZADO EM 2025**: Sistema session-based evoluído para Vue.js + API com autenticação híbrida e Active Directory.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Estabelecer padrões técnicos para o sistema de autenticação, garantindo segurança, consistência e facilidade de manutenção.

### 🏗️ **Arquitetura Geral**
- **Sistema**: Session-based (sem JWT)
- **Estrutura**: Controllers Web/Api separados
- **Middleware**: Autenticação limpa e eficiente
- **Rotas**: Todas organizadas em `web.php`
- **Proteção**: Baseada em sessão para todas as rotas

---

## 2. Sistema Session-Based

### 🚫 **Por Que NÃO JWT?**

#### **1. Segurança Superior para Aplicações Internas**
- **Sessões são MUITO mais seguras** que JWT para sistemas web internos
- **Controle total** sobre sessões ativas
- **Invalidação imediata** em caso de comprometimento
- **Sem exposição** de tokens em localStorage/sessionStorage

#### **2. Simplicidade Operacional**
- **Todas as rotas em um só arquivo** (`web.php`)
- **Sem confusão** sobre onde colocar rotas
- **Middleware único** para autenticação
- **Configuração centralizada** de segurança
- **Debugging simplificado**

#### **3. Integração com Laravel**
- **Middleware nativo** do Laravel
- **Sistema de sessões** robusto e testado
- **Integração perfeita** com Vue.js
- **Sem dependências** externas complexas

---

## 3. Estrutura de Controllers

### 📁 **Separação Web/Api**

#### **Controller Web (Interface)**
```php
// app/Http/Controllers/Web/Auth/AuthController.php
class AuthController extends Controller
{
    /**
     * Exibe a página de login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }
}
```

#### **Controller API (Dados)**
```php
// app/Http/Controllers/Api/Auth/AuthController.php
class AuthController extends Controller
{
    /**
     * Processa o login
     */
    public function login(Request $request)
    {
        // Validação e autenticação
        // Retorna JSON para API ou redireciona para web
    }
    
    /**
     * Processa o logout
     */
    public function logout(Request $request)
    {
        // Logout da sessão
        // Retorna JSON para API ou redireciona para web
    }
    
    /**
     * Verifica usuário autenticado
     */
    public function me(Request $request)
    {
        // Retorna dados do usuário em JSON
    }
}
```

---

## 4. Middleware de Autenticação

### 🛡️ **Middleware Padrão**

#### **Configuração Base**
```php
// app/Http/Middleware/Authenticate.php
class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        // API: sem redirecionamento (retorna 401)
        if ($request->expectsJson()) {
            return null;
        }
        
        // Web: redireciona para login
        return route('login');
    }
}
```

#### **Aplicação nas Rotas**
```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    // Todas as rotas protegidas aqui
    Route::prefix('admin')->name('admin.')->group(function () {
        // Rotas administrativas
    });
    
    Route::prefix('api/administracao')->name('api.administracao.')->group(function () {
        // Rotas API administrativas
    });
});
```

---

## 5. Rotas de Autenticação

### 🛣️ **Organização em web.php**

#### **Rotas Públicas**
```php
// Rotas de autenticação (sem middleware)
Route::get('/login', [Web\Auth\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [Api\Auth\AuthController::class, 'login'])->name('login.process');
```

#### **Rotas Protegidas**
```php
Route::middleware(['auth'])->group(function () {
    // Rotas que precisam de autenticação
    Route::post('/logout', [Api\Auth\AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [Api\Auth\AuthController::class, 'me'])->name('me');
    
    // Rotas administrativas
    Route::prefix('admin')->name('admin.')->group(function () {
        // Interface administrativa
    });
    
    // Rotas API
    Route::prefix('api/administracao')->name('api.administracao.')->group(function () {
        // Dados administrativos
    });
});
```

---

## 6. Configuração de Autenticação

### ⚙️ **config/auth.php**

#### **Guards Padrão**
```php
'guards' => [
    'web' => [
        'driver' => 'session',  // SEMPRE session
        'provider' => 'users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\Administracao\User::class,
    ],
],
```

#### **Configurações de Senha**
```php
'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60,        // 60 minutos
        'throttle' => 60,      // 60 segundos entre tentativas
    ],
],

'password_timeout' => 10800,  // 3 horas para confirmação
```

---

## 7. Sistema Híbrido de Autenticação

### 🔄 **Funcionamento Dual**

#### **1. Formulários Web (Tradicional)**
- **Controller**: `Web\Auth\AuthController`
- **View**: `auth.login.blade.php`
- **Fluxo**: Login → Redirecionamento para home
- **Sessão**: Criada automaticamente pelo Laravel

#### **2. API (Vue.js)**
- **Controller**: `Api\Auth\AuthController`
- **Endpoint**: `/api/auth/login`
- **Fluxo**: Login → Retorna JSON com dados do usuário
- **Sessão**: Criada automaticamente pelo Laravel

#### **3. Detecção Automática**
```php
// Se for requisição API, retorna JSON
if ($request->expectsJson()) {
    return response()->json([
        'message' => 'Login realizado com sucesso',
        'user' => $user,
        'redirect' => route('home')
    ]);
}

// Se for requisição web, redireciona
return redirect()->intended(route('home'));
```

---

## 8. Autenticação Active Directory

### 🏢 **Sistema Híbrido AD + Local**

#### **Tipos de Login**
```php
// Model User
'login_type' => 'local' | 'ad'

// Verificação no login
if ($user->login_type === 'ad') {
    $passwordValid = $this->authenticateWithAD($request->email, $request->password);
} else {
    $passwordValid = Hash::check($request->password, $user->password);
}
```

#### **Serviço AD**
```php
// app/Services/ActiveDirectoryService.php
class ActiveDirectoryService
{
    public function authenticateUser($username, $password): bool
    {
        // Autenticação no Active Directory
        // Retorna true/false
    }
}
```

---

## 9. Proteção de Rotas

### 🛡️ **Estratégias de Proteção**

#### **1. Middleware de Autenticação**
```php
Route::middleware(['auth'])->group(function () {
    // Todas as rotas aqui precisam de login
});
```

#### **2. Verificação de Permissões**
```php
// No controller
private function checkPermissions()
{
    $user = Auth::user();
    
    if ($user->isSuperAdmin()) {
        return true;
    }
    
    if ($user->hasPermission('gerenciar_usuarios')) {
        return true;
    }
    
    abort(403, 'Acesso negado. Permissão insuficiente.');
}
```

#### **3. Verificação no Frontend (Vue.js)**
```javascript
// Computed properties
computed: {
    canPerformActions() {
        if (this.isSuper) return true;
        
        const permissions = this.currentUser.roles.flatMap(role => role.permissions || []);
        const permissionNames = permissions.map(p => p.name);
        
        return permissionNames.some(p => p.startsWith('usuario_'));
    }
}
```

---

## 10. Exibição de Dados do Usuário

### 👤 **Dados Disponíveis**

#### **1. Endpoint /me**
```php
// Retorna usuário com roles e permissions
public function me(Request $request)
{
    if (!Auth::check()) {
        return response()->json(['message' => 'Usuário não autenticado'], 401);
    }

    $user = User::with('roles.permissions')->find(Auth::id());
    return response()->json($user);
}
```

#### **2. Endpoint /user-data**
```php
// Dados básicos para compatibilidade
Route::get('/user-data', function () {
    if (Auth::check()) {
        return response()->json([
            'id' => Auth::user()->id,
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'authenticated' => true
        ]);
    }
    
    return response()->json(['authenticated' => false], 401);
});
```

---

## 11. Logout e Invalidação

### 🚪 **Processo de Logout**

#### **1. Logout da Sessão**
```php
public function logout(Request $request)
{
    // Fazer logout da sessão
    Auth::logout();
    
    // Invalidar sessão
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    // Retornar resposta apropriada
    if ($request->expectsJson()) {
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }
    
    return redirect()->route('login');
}
```

#### **2. Segurança**
- **Sessão invalidada** completamente
- **Token CSRF regenerado**
- **Cookies de sessão removidos**
- **Redirecionamento para login**

---

## 12. Logs e Auditoria

### 📝 **Sistema de Logs**

#### **1. Logs de Autenticação**
```php
Log::info('=== INÍCIO TENTATIVA DE LOGIN ===', [
    'email' => $request->email,
    'timestamp' => now()
]);

Log::info('Login bem-sucedido', [
    'user_id' => $user->id,
    'email' => $user->email
]);
```

#### **2. Logs de Segurança**
- **Tentativas de login** (sucesso/falha)
- **Logout** de usuários
- **Tentativas de acesso** negado
- **Erros de autenticação** AD

---

## 13. Tratamento de Erros

### ⚠️ **Validações e Mensagens**

#### **1. Validação de Login**
```php
$request->validate([
    'email' => 'required|email',
    'password' => 'required',
]);
```

#### **2. Mensagens de Erro**
```php
// Usuário não encontrado
throw ValidationException::withMessages([
    'email' => ['Não foi possível encontrar uma conta com este endereço de e-mail.'],
]);

// Senha incorreta
throw ValidationException::withMessages([
    'email' => ['A senha informada está incorreta.'],
]);

// Usuário inativo
throw ValidationException::withMessages([
    'email' => ['Esta conta está temporariamente desativada.'],
]);
```

---

## 14. Segurança e Boas Práticas

### 🔒 **Medidas de Segurança**

#### **1. Proteção de Sessão**
- **Timeout configurável** para sessões
- **Regeneração de token** CSRF
- **Invalidação completa** no logout

#### **2. Rate Limiting**
- **Throttle de senhas** (60 segundos)
- **Expiração de tokens** (60 minutos)
- **Logs de tentativas** suspeitas

#### **3. Validação de Usuário**
- **Verificação de status** (ativo/inativo)
- **Tipo de login** (local/AD)
- **Último login** registrado

---

## 15. Checklist de Implementação

### 📋 **Para Novas Funcionalidades**

- [ ] **Usar middleware `auth`** para rotas protegidas
- [ ] **Verificar permissões** no controller
- [ ] **Implementar logs** de segurança
- [ ] **Usar validações** apropriadas
- [ ] **Retornar respostas** consistentes (JSON/Redirect)
- [ ] **Aplicar verificação** de permissões no frontend
- [ ] **Manter sessões** seguras
- [ ] **Documentar** endpoints de autenticação

---

## 16. Conclusão

### 🎉 **Sistema Robusto e Seguro**

**Nossa abordagem session-based oferece:**

- ✅ **Segurança superior** para aplicações internas
- ✅ **Simplicidade operacional** com todas as rotas em web.php
- ✅ **Integração perfeita** Laravel + Vue.js
- ✅ **Sistema híbrido** AD + Local
- ✅ **Middleware limpo** e eficiente
- ✅ **Logs completos** para auditoria
- ✅ **Tratamento de erros** robusto
- ✅ **Proteção de rotas** consistente

**Este sistema garante autenticação segura, manutenível e escalável para o projeto OrçaCidade!**

---

> **IMPORTANTE**: Esta diretriz deve ser seguida para todas as funcionalidades de autenticação. Qualquer desvio deve ser documentado e justificado.

> **ÚLTIMA ATUALIZAÇÃO**: Janeiro 2025 - Sistema session-based evoluído para Vue.js + API
