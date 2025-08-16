# Sistema de Autenticação - FASE 1

## 📋 Visão Geral

Este documento descreve o sistema de autenticação implementado na **FASE 1** do projeto OrçaCidade, que estabelece uma base sólida e segura para o controle de acesso ao sistema usando **autenticação baseada em sessões**.

## 🎯 Objetivos da FASE 1

### ✅ Implementados:
- **Sistema de autenticação session-based** (sem JWT)
- **Estrutura de controllers** seguindo nosso padrão (Web/Api)
- **Middleware de autenticação** limpo e eficiente
- **Rotas de autenticação** organizadas em `web.php` com prefixo `/api`
- **Proteção de rotas** baseada em sessão
- **Exibição de dados do usuário** nas interfaces
- **Sistema híbrido** que funciona tanto com formulários quanto com API

### ❌ Não Implementados (FASE 2):
- Gerenciamento de usuários
- Gerenciamento de roles/permissions
- Integração com Active Directory
- Funcionalidades avançadas de autorização

---

## 🚀 Justificativa da Nossa Abordagem

### **🎯 Por Que Escolhemos Sessões em Vez de JWT?**

#### **1. Segurança Superior para Aplicações Internas**

**Sessões são MUITO mais seguras que JWT para sistemas web internos:**

| Aspecto | Sessões | JWT |
|---------|---------|-----|
| **Dados sensíveis** | ❌ No servidor | ✅ No cliente |
| **Modificação** | ❌ Impossível | ⚠️ Possível |
| **Invalidação** | ✅ Imediata | ❌ Difícil |
| **CSRF** | ✅ Protegido | ❌ Vulnerável |
| **Tamanho** | ✅ Pequeno | ❌ Grande |
| **Performance** | ✅ Rápido | ❌ Lento |

#### **2. Arquitetura Híbrida Inteligente**

**Nosso sistema funciona perfeitamente para ambos os casos:**

```php
// ✅ Formulário HTML (redireciona)
POST /api/auth/login → Redireciona para /home

// ✅ API JSON (retorna dados)
POST /api/auth/login → Retorna JSON com mensagem
```

**Detecção automática:**
- **Formulário HTML** → `$request->expectsJson() = false` → Redireciona
- **API/JavaScript** → `$request->expectsJson() = true` → Retorna JSON

#### **3. Simplicidade e Manutenibilidade**

**Todas as rotas em um só arquivo (`web.php`):**
- **Sem confusão** sobre onde colocar rotas
- **Middleware único** para autenticação
- **Configuração centralizada** de segurança
- **Debugging simplificado**

---

## 🏗️ Arquitetura do Sistema

### **Fluxo de Autenticação:**
```
Usuário → /login → Web\AuthController::showLoginForm() → Formulário
Formulário → POST /api/auth/login → Api\AuthController::login() → Sessão → /home
```

### **Proteção de Rotas:**
```
Rota Protegida → Middleware 'auth' → Verifica Sessão → Acesso/Redirecionamento
```

### **Estrutura Híbrida:**
```
📁 Controllers:
├── 📁 Web\Auth\AuthController.php (interface)
└── 📁 Api\Auth\AuthController.php (dados)

 Rotas (web.php):
├── GET  /login           → Mostra formulário
├── POST /api/auth/login  → Processa login
├── POST /api/auth/logout → Processa logout
└── GET  /api/auth/me     → Dados do usuário
```

---

## 📁 Estrutura de Arquivos

### **Controllers:**
```
app/Http/Controllers/
├── Web/
│   └── Auth/
│       └── AuthController.php          # Interface (formulário)
└── Api/
    └── Auth/
        └── AuthController.php          # Dados (login/logout/me)
```

### **Middleware:**
```
app/Http/Middleware/
├── Authenticate.php            # Protege rotas que precisam de login
└── RedirectIfAuthenticated.php # Redireciona usuários já logados
```

### **Rotas:**
```
routes/
├── web.php                     # TODAS as rotas (Web + API)
└── api.php                     # Vazio (apenas para integração externa)
```

### **Configuração:**
```
config/
└── auth.php                    # Configuração de autenticação
```

---

## 🔧 Implementação Técnica

### **1. Controllers de Autenticação**

#### **Web\AuthController (Interface):**
```php
class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Se já estiver autenticado, redireciona para home
        if (Auth::check()) {
            return redirect()->route('home');
        }
        
        return view('auth.login');
    }
}
```

#### **Api\AuthController (Dados):**
```php
class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validação e autenticação...
        Auth::login($user, $request->boolean('remember'));

        // Detecção inteligente: API ou Web?
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Login realizado com sucesso',
                'user' => $user,
                'redirect' => route('home')
            ]); // API
        }
        return redirect()->intended(route('home')); // Web
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Detecção inteligente: API ou Web?
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Logout realizado com sucesso'
            ]); // API
        }
        return redirect()->route('login'); // Web
    }

    public function me(Request $request)
    {
        // Sempre retorna JSON para API
        return response()->json(Auth::user());
    }
}
```

### **2. Middleware de Autenticação**

#### **Authenticate:**
```php
protected function redirectTo(Request $request): ?string
{
    if ($request->expectsJson()) {
        return null; // API: sem redirecionamento
    }
    return route('login'); // Web: redireciona para login
}
```

#### **RedirectIfAuthenticated:**
```php
public function handle(Request $request, Closure $next, string ...$guards): Response
{
    foreach ($guards as $guard) {
        if (Auth::guard($guard)->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Usuário já autenticado',
                    'redirect' => RouteServiceProvider::HOME
                ], 400); // API: erro JSON
            }
            return redirect(RouteServiceProvider::HOME); // Web: redireciona
        }
    }
    return $next($request);
}
```

### **3. Sistema de Rotas**

#### **Rotas Web (Interface):**
```php
// Públicas
GET  /                    → Página inicial
GET  /login              → Formulário de login

// Protegidas
GET  /home               → Dashboard (protegida)
GET  /user-data          → Dados do usuário (API local)
```

#### **Rotas API (Dados):**
```php
POST /api/auth/login     → Autenticação (redireciona ou JSON)
POST /api/auth/logout    → Logout (redireciona ou JSON)
GET  /api/auth/me        → Dados do usuário autenticado (JSON)
```

---

## 🔧 Implementação Técnica

### **1. Controllers de Autenticação**

#### **Web\AuthController (Interface):**
```php
class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Se já estiver autenticado, redireciona para home
        if (Auth::check()) {
            return redirect()->route('home');
        }
        
        return view('auth.login');
    }
}
```

#### **Api\AuthController (Dados):**
```php
class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validação e autenticação...
        Auth::login($user, $request->boolean('remember'));

        // Detecção inteligente: API ou Web?
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Login realizado com sucesso',
                'user' => $user,
                'redirect' => route('home')
            ]); // API
        }
        return redirect()->intended(route('home')); // Web
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Detecção inteligente: API ou Web?
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Logout realizado com sucesso'
            ]); // API
        }
        return redirect()->route('login'); // Web
    }

    public function me(Request $request)
    {
        // Sempre retorna JSON para API
        return response()->json(Auth::user());
    }
}
```

### **2. Middleware de Autenticação**

#### **Authenticate:**
```php
protected function redirectTo(Request $request): ?string
{
    if ($request->expectsJson()) {
        return null; // API: sem redirecionamento
    }
    return route('login'); // Web: redireciona para login
}
```

#### **RedirectIfAuthenticated:**
```php
public function handle(Request $request, Closure $next, string ...$guards): Response
{
    foreach ($guards as $guard) {
        if (Auth::guard($guard)->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Usuário já autenticado',
                    'redirect' => RouteServiceProvider::HOME
                ], 400); // API: erro JSON
            }
            return redirect(RouteServiceProvider::HOME); // Web: redireciona
        }
    }
    return $next($request);
}
```

---

## ⚙️ Configuração do Sistema

### **1. Configuração de Autenticação (`config/auth.php`)**

```php
<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
```

### **2. Configuração de Sessão (`config/session.php`)**

```php
<?php

return [
    'driver' => env('SESSION_DRIVER', 'file'),
    'lifetime' => env('SESSION_LIFETIME', 120), // 2 horas
    'expire_on_close' => true,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => 'sessions',
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100],
    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN'),
    'secure' => env('SESSION_SECURE_COOKIE', true), // HTTPS apenas
    'http_only' => true,                            // JavaScript não acessa
    'same_site' => 'lax',                           // Proteção CSRF
];
```

### **3. Configuração de Middleware (`app/Http/Kernel.php`)**

```php
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],

    'api' => [
        \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];

protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
];
```

---

## 🔐 Segurança e Por Que Sessões São Mais Seguras

### **🛡️ Vantagens de Segurança das Sessões:**

#### **1. Dados Sensíveis no Servidor**
```php
// ✅ SESSÃO: Dados ficam no servidor
$_SESSION = [
    'user_id' => 123,
    'ip_address' => '192.168.1.100',
    'user_agent' => 'Mozilla/5.0...',
    'last_activity' => '2025-01-27 10:30:00'
];

// ❌ JWT: Dados ficam no cliente
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoxMjMsImVtYWlsIjoiZXhhbXBsZUBnbWFpbC5jb20iLCJyb2xlcyI6WyJhZG1pbiJdLCJpYXQiOjE2MzI4NzY4MDAsImV4cCI6MTYzMjg4MDQwMH0.signature
```

#### **2. Invalidação Imediata**
```php
// ✅ SESSÃO: Logout invalida TUDO instantaneamente
Auth::logout();
$request->session()->invalidate();
$request->session()->regenerateToken();

// ❌ JWT: Logout não invalida imediatamente
// Precisa de blacklist complexa ou esperar expiração
```

#### **3. Proteção CSRF Nativa**
```php
// ✅ SESSÃO: Token CSRF único por sessão
<meta name="csrf-token" content="{{ csrf_token() }}">

// ❌ JWT: Sem proteção CSRF nativa
// Precisa implementar manualmente
```

#### **4. Validação Completa no Servidor**
```php
// ✅ SESSÃO: Laravel verifica TUDO automaticamente
- ID da sessão existe?
- Sessão não expirou?
- IP é o mesmo?
- User-Agent é o mesmo?
- Última atividade é recente?

// ❌ JWT: Apenas verifica assinatura e expiração
// Não verifica contexto da requisição
```

### **🚨 Cenários de Ataque (Sessão vs JWT):**

#### **1. Ataque XSS (Cross-Site Scripting)**
```javascript
// ✅ SESSÃO: Cookie http_only = true
// JavaScript NÃO consegue acessar

// ❌ JWT: Pode ser roubado via JavaScript
// Dados ficam expostos no localStorage/sessionStorage
```

#### **2. Ataque CSRF (Cross-Site Request Forgery)**
```javascript
// ✅ SESSÃO: Token CSRF único
// Cada formulário tem token diferente

// ❌ JWT: Sem proteção nativa
// Requisições podem ser forjadas
```

#### **3. Roubo de Credenciais**
```javascript
// ✅ SESSÃO: Apenas ID (inútil sem servidor)
// Cookie: laravel_session = "abc123def456"

// ❌ JWT: Dados completos do usuário
// Role, permissões, informações sensíveis
```

### **⚙️ Configurações de Segurança das Sessões:**

```php
// config/session.php
'secure' => env('SESSION_SECURE_COOKIE', true),     // HTTPS apenas
'http_only' => true,                                // JavaScript não acessa
'same_site' => 'lax',                               // Proteção CSRF
'expire_on_close' => true,                          // Expira ao fechar browser
'lifetime' => env('SESSION_LIFETIME', 120),         // 2 horas de inatividade
```

---

## 🎨 Interface do Usuário

### **Header:**
- **Nome do usuário** exibido dinamicamente via `/api/auth/me`
- **Data e hora** atualizadas em tempo real
- **Botão de logout** funcional via formulário oculto
- **Busca automática** de dados via API

### **Card Central:**
- **Saudação personalizada** com nome do usuário
- **Data atual** em português brasileiro
- **Horário** atualizado em tempo real
- **Status do sistema** visual

---

## 🧪 Testes e Validação

### **Testes de Funcionalidade:**
1. **Acesso sem autenticação** → Redirecionamento para login
2. **Login válido** → Redirecionamento para home
3. **Acesso a rotas protegidas** → Verificação de autenticação
4. **Logout** → Invalidação de sessão
5. **Exibição de dados** → Nome do usuário nas interfaces

### **Testes de Segurança:**
1. **Tentativa de acesso direto** a rotas protegidas
2. **Validação de credenciais** inválidas
3. **Proteção contra** usuários inativos
4. **Invalidação de sessão** após logout
5. **Proteção CSRF** em formulários

---

## 📊 Logs e Monitoramento

### **Logs de Autenticação:**
```php
// Exemplo de log de login
Log::info('=== INÍCIO TENTATIVA DE LOGIN ===', [
    'email' => $request->email,
    'timestamp' => now()
]);

// Exemplo de log de sucesso
Log::info('Login bem-sucedido', [
    'user_id' => $user->id,
    'email' => $user->email
]);
```

### **Informações Monitoradas:**
- Tentativas de login (sucesso/falha)
- Logout de usuários
- Acessos a rotas protegidas
- Erros de autenticação
- Timestamps de todas as operações

---

## 🚀 Próximos Passos (FASE 2)

### **Funcionalidades a Implementar:**
1. **Sistema RBAC** (Users, Roles, Permissions)
2. **Integração com Active Directory**
3. **Gerenciamento avançado** de usuários
4. **Controle granular** de permissões
5. **Auditoria avançada** de acesso

### **Arquitetura Futura:**
```
Autenticação (FASE 1) + RBAC (FASE 2) = Sistema Completo
```

---

## 📝 Notas de Implementação

### **Decisões Técnicas:**
- **Session-based** em vez de JWT para aplicações internas
- **Estrutura Web/Api** seguindo nosso padrão documentado
- **Middleware único** para proteção de rotas
- **Detecção inteligente** de tipo de requisição
- **Todas as rotas em web.php** para simplicidade

### **Compatibilidade:**
- **Laravel 10+** compatível
- **Vue.js** integrado via componentes
- **Bootstrap** para estilização
- **Responsivo** para diferentes dispositivos

---

## 🔍 Troubleshooting

### **Problemas Comuns e Soluções:**

#### **1. Usuário não aparece no header:**
```bash
# Verificar se a rota /api/auth/me está funcionando
curl -X GET http://localhost/api/auth/me -H "Accept: application/json"

# Verificar logs do Laravel
tail -f storage/logs/laravel.log
```

#### **2. Logout não funciona:**
```bash
# Verificar se o formulário oculto está correto
# Deve usar: action="{{ route('api.auth.logout') }}"

# Verificar se o middleware auth está aplicado
# Rota deve estar dentro de Route::middleware(['auth'])
```

#### **3. Redirecionamento infinito:**
```bash
# Verificar middleware RedirectIfAuthenticated
# Deve redirecionar usuários logados para /home

# Verificar se a rota /home existe e está protegida
php artisan route:list | grep home
```

#### **4. Sessão perdida frequentemente:**
```bash
# Verificar configurações de sessão
php artisan config:show session

# Verificar permissões da pasta storage
chmod -R 775 storage/framework/sessions

# Verificar se o driver de sessão está funcionando
php artisan session:table
php artisan migrate
```

#### **5. Erro CSRF Token:**
```bash
# Verificar se o token está sendo enviado
# Formulário deve ter: @csrf

# Verificar se a sessão está funcionando
php artisan session:status

# Limpar cache de configuração
php artisan config:clear
```

#### **6. Problemas de Performance:**
```bash
# Verificar se as sessões estão sendo limpas
php artisan session:gc

# Verificar tamanho da pasta de sessões
du -sh storage/framework/sessions

# Configurar limpeza automática no cron
# Adicionar ao crontab: * * * * * php /path/to/artisan session:gc
```

### **Comandos Úteis para Debug:**

```bash
# Listar todas as rotas
php artisan route:list

# Verificar status da autenticação
php artisan tinker
>>> Auth::check()

# Verificar configurações
php artisan config:show auth
php artisan config:show session

# Limpar caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Verificar logs em tempo real
tail -f storage/logs/laravel.log
```

---

## 📚 Referências

### **Documentação Laravel:**
- [Authentication](https://laravel.com/docs/authentication)
- [Middleware](https://laravel.com/docs/middleware)
- [Session](https://laravel.com/docs/session)

### **Padrões Utilizados:**
- **MVC** (Model-View-Controller)
- **Middleware Pattern**
- **Session-based Authentication**
- **RESTful API Design**
- **Hybrid Web/API Architecture**

---

## 🎯 Conclusão

### **✅ Nossa Abordagem é Superior Porque:**

1. **Segurança Máxima** - Dados sensíveis no servidor
2. **Simplicidade** - Todas as rotas em um arquivo
3. **Flexibilidade** - Funciona com formulários e API
4. **Manutenibilidade** - Estrutura clara e organizada
5. **Performance** - Sessões são mais rápidas que JWT
6. **Padrão Laravel** - Usa recursos nativos do framework

### **🚀 Resultado Final:**
**Um sistema de autenticação robusto, seguro e flexível que segue 100% nossos padrões documentados e oferece segurança superior às alternativas JWT.**

---

**📅 Data de Criação:** 27/01/2025  
**👨‍💻 Desenvolvedor:** Sistema de Autenticação - FASE 1  
**🏷️ Versão:** 2.0.0  
**📋 Status:** ✅ IMPLEMENTADO, TESTADO E FUNCIONANDO  
**🔒 Segurança:** ✅ SUPERIOR A JWT PARA APLICAÇÕES INTERNAS
