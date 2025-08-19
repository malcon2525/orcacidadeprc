# Sistema de Autenticação - OrcaCidade

## 📋 Visão Geral

### **Objetivo**
Sistema de autenticação robusto e seguro para aplicação web interna, baseado em sessões (session-based) sem JWT, otimizado para ambientes corporativos.

### **Contexto**
- **Aplicação:** Sistema interno de orçamento municipal
- **Usuários:** Funcionários públicos e administradores
- **Ambiente:** Rede interna corporativa
- **Segurança:** Alto nível de proteção

### **Por Que Sessões em Vez de JWT?**

#### ✅ **Vantagens das Sessões:**
1. **Segurança Superior** para aplicações internas
2. **Controle de Sessão** (revogação imediata)
3. **Menor Overhead** de rede
4. **Debugging Simplificado**
5. **Compatibilidade** com sistemas legados

#### ❌ **Desvantagens do JWT:**
1. **Tamanho** (cada requisição carrega token)
2. **Revogação Complexa** (blacklist necessário)
3. **Segurança Dependente** da chave secreta
4. **Debugging Complexo** (tokens criptografados)

## 🏗️ Arquitetura Técnica

### **Stack Tecnológico**
```
Frontend: Vue.js 3 + Bootstrap 5
Backend: Laravel 10 + PHP 8.1
Banco: MySQL 8.0
Sessões: Laravel Session (padrão)
```

### **Componentes Principais**
```
├── Middleware de Autenticação
├── Sistema de Sessões
├── Controllers de Autenticação
├── Componentes Vue de Login
├── Rotas Protegidas
└── Sistema de Logout
```

### **Fluxo de Autenticação**
```
1. Usuário acessa rota protegida
2. Middleware 'auth' verifica sessão
3. Se não autenticado → redireciona para login
4. Se autenticado → permite acesso
5. Sessão mantida durante navegação
```

## ⚙️ Implementação

### **1. Middleware de Autenticação**

#### **Configuração Padrão do Laravel**
```php
// app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\Authenticate::class,
        // outros middlewares padrão...
    ],
];
```

#### **Middleware de Autenticação Padrão**
```php
// app/Http/Middleware/Authenticate.php
class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
```

### **2. Controllers de Autenticação**

#### **Estrutura Real do Projeto**
```php
// app/Http/Controllers/Web/Auth/LoginController.php
class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Implementação real de login
        // Validação de credenciais
        // Autenticação via sessão
    }
}
```

#### **Logout Controller**
```php
public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect('/');
}
```

### **3. Rotas de Autenticação**

#### **Arquivo `routes/web.php`**
```php
// Rotas Públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Rotas Protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
```

### **4. Sistema de Verificação de Acesso Real**

#### **Método `checkAccess` Implementado**
```php
// Implementado em TODOS os controllers do projeto
private function checkAccess($permissions, $requireAll = false)
{
    $user = Auth::user(); // ou User::find(Auth::id()) em alguns casos
    
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

#### **Uso Real nos Controllers**
```php
// Web Controller - Verificação de papel para acesso ao módulo
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

// API Controller - Verificação de permissão para funcionalidades
public function index(Request $request)
{
    // CONSULTA: verifica se tem usuario_crud OU usuario_consultar
    $this->checkAccess(['usuario_crud', 'usuario_consultar']);
    
    // Lógica de listagem...
}

public function store(Request $request)
{
    // CRUD: verifica se tem usuario_crud (apenas CRUD pode criar)
    $this->checkAccess('usuario_crud');
    
    // Lógica de criação...
}
```

### **5. Componentes Vue**

#### **Login Component (`resources/js/components/Auth/Login.vue`)**
```vue
<template>
  <form @submit.prevent="handleLogin">
    <input v-model="email" type="email" required />
    <input v-model="password" type="password" required />
    <button type="submit">Entrar</button>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const email = ref('')
const password = ref('')

const handleLogin = async () => {
  try {
    await axios.post('/login', {
      email: email.value,
      password: password.value
    })
    window.location.href = '/dashboard'
  } catch (error) {
    console.error('Erro no login:', error)
  }
}
</script>
```

## 🔧 Configuração

### **1. Configuração de Sessões (`config/session.php`)**

#### **Configurações Recomendadas**
```php
return [
    'driver' => env('SESSION_DRIVER', 'file'),
    'lifetime' => env('SESSION_LIFETIME', 120), // 2 horas
    'expire_on_close' => false,
    'encrypt' => true,
    'secure' => env('SESSION_SECURE_COOKIE', false), // true em produção
    'http_only' => true,
    'same_site' => 'lax',
];
```

#### **Variáveis de Ambiente (`.env`)**
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=false
SESSION_DOMAIN=null
```

### **2. Configuração de Autenticação (`config/auth.php`)**

#### **Configurações de Usuário**
```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
],

'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],
```

### **3. Modelo de Usuário Real (`app/Models/Administracao/User.php`)**

#### **Métodos de Verificação Implementados**
```php
class User extends Authenticatable
{
    /**
     * Verifica se o usuário é super admin (tem papel 'super')
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super');
    }
    
    /**
     * Verifica se o usuário tem um papel específico
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }
    
    /**
     * Verifica se o usuário tem uma permissão específica
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->roles()
                    ->whereHas('permissions', function ($query) use ($permissionName) {
                        $query->where('name', $permissionName);
                    })
                    ->exists();
    }
    
    /**
     * Verifica se o usuário tem pelo menos um dos papéis especificados
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }
    
    /**
     * Verifica se o usuário tem pelo menos uma das permissões especificadas
     */
    public function hasAnyPermission(array $permissionNames): bool
    {
        return $this->roles()
                    ->whereHas('permissions', function ($query) use ($permissionNames) {
                        $query->whereIn('name', $permissionNames);
                    })
                    ->exists();
    }
}
```

## 🛡️ Segurança

### **1. Proteções Implementadas**

#### **CSRF Protection**
```php
// Em todos os formulários
@csrf
// Ou em Vue.js
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
```

#### **Validação de Credenciais**
```php
$credentials = $request->validate([
    'email' => 'required|email|exists:users,email',
    'password' => 'required|min:8'
]);
```

#### **Regeneração de Sessão**
```php
$request->session()->regenerate(); // Após login
$request->session()->invalidate(); // Após logout
```

### **2. Melhores Práticas**

#### **Senhas**
- Hash com bcrypt (padrão Laravel)
- Mínimo 8 caracteres
- Validação de força (opcional)

#### **Sessões**
- Tempo de vida limitado (2 horas)
- Regeneração após login
- Invalidação após logout

#### **Cookies**
- HttpOnly (não acessível via JavaScript)
- Secure em produção (HTTPS)
- SameSite para proteção CSRF

## 🔍 Troubleshooting

### **1. Problemas Comuns**

#### **Sessão Expirada**
```php
// Verificar configuração de lifetime
config('session.lifetime')

// Verificar se usuário está autenticado
Auth::check()
```

#### **CSRF Token Mismatch**
```javascript
// Verificar se o token está sendo enviado
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
```

#### **Redirecionamento Infinito**
```php
// Verificar middleware de autenticação
Route::middleware(['auth'])->group(function () {
    // rotas protegidas
});
```

### **2. Debug de Sessão**

#### **Verificar Dados da Sessão**
```php
// Em qualquer controller
dd(session()->all());
dd(Auth::user());
```

#### **Verificar Status de Autenticação**
```php
if (Auth::check()) {
    $user = Auth::user();
    dd($user->toArray());
}
```

## 🚀 Melhorias Futuras (Sugestões)

### **1. Performance**

#### **Cache de Sessões**
```php
// Usar Redis para sessões
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### **Cache de Permissões**
```php
// Cachear permissões do usuário
$permissions = Cache::remember("user_permissions_{$userId}", 3600, function () use ($userId) {
    return User::find($userId)->permissions;
});
```

### **2. Segurança Avançada**

#### **Rate Limiting**
```php
// Limitar tentativas de login
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
});
```

#### **Logs de Auditoria**
```php
// Log de todas as ações de autenticação
Log::info('User logged in', [
    'user_id' => $user->id,
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent()
]);
```

#### **Validação de IP**
```php
// Restringir acesso por IP
if (!in_array($request->ip(), config('auth.allowed_ips'))) {
    abort(403, 'IP não autorizado');
}
```

### **3. Usabilidade**

#### **Lembrar Login**
```php
// Implementar "Lembrar de mim"
if ($request->boolean('remember')) {
    Auth::login($user, true);
}
```

#### **Login com Username/Email**
```php
// Permitir login com username ou email
$field = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
$credentials = [$field => $request->input('login'), 'password' => $request->input('password')];
```

#### **Autenticação de Dois Fatores (2FA)**
```php
// Implementar 2FA com Google Authenticator
use Laravel\Fortify\Fortify;

Fortify::twoFactorChallengeView(function () {
    return view('auth.two-factor-challenge');
});
```

### **4. Monitoramento**

#### **Dashboard de Segurança**
- Usuários ativos
- Tentativas de login falhadas
- Sessões ativas
- Logs de acesso

#### **Alertas de Segurança**
- Login de IPs suspeitos
- Múltiplas tentativas de login
- Sessões longas
- Mudanças de permissão

## 📊 Métricas e Monitoramento

### **1. Métricas de Autenticação**
- Taxa de sucesso de login
- Tempo médio de sessão
- Usuários ativos por período
- Tentativas de acesso negado

### **2. Logs Recomendados**
```php
// Log de login
Log::info('User login successful', ['user_id' => $user->id, 'ip' => $request->ip()]);

// Log de logout
Log::info('User logout', ['user_id' => $user->id, 'ip' => $request->ip()]);

// Log de tentativa falhada
Log::warning('Failed login attempt', ['email' => $request->email, 'ip' => $request->ip()]);
```

## 🎯 Conclusão

### **Status Atual**
✅ **Sistema implementado** e funcionando
✅ **Segurança robusta** implementada
✅ **Testes passando** em todos os cenários
✅ **Código limpo** e profissional

### **Próximos Passos**
1. **Implementar melhorias** sugeridas
2. **Aplicar padrão** a outros módulos
3. **Monitorar performance** e segurança
4. **Treinar equipe** no sistema

### **Recursos Adicionais**
- [Documentação Laravel Authentication](https://laravel.com/docs/authentication)
- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [OWASP Authentication Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html)

---

**Última atualização:** {{ date('d/m/Y H:i:s') }}
**Versão:** 1.0.0
**Responsável:** Equipe de Desenvolvimento OrcaCidade
