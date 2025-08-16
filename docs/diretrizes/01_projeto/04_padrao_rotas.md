# Padrão de Rotas - OrçaCidade

> **DOCUMENTO MESTRE**: Este documento define como estruturar rotas no projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para manter consistência e organização.

> **ATUALIZADO EM 2025**: Padrão evoluído para Vue.js + API com TODAS as rotas em web.php para simplicidade e segurança.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Definir padrões para estrutura e nomenclatura de rotas, facilitando manutenção e colaboração da equipe.

### 🛣️ **Princípios Fundamentais**
- **TODAS as rotas em web.php** - Simplicidade e segurança
- **Separação Web/Api** - Controllers Web para interface, Controllers API para dados
- **Vue.js + API** - Interface dinâmica com Vue.js e API para dados
- **Prefixo /api** - Rotas de dados sempre com prefixo /api
- **Sessão única** - Autenticação session-based para tudo

---

## 2. Estrutura de Rotas

### 📋 **ESTRUTURA PADRÃO - TODAS EM web.php**

#### **Rotas Web (Interface)**
```php
// routes/web.php

// ✅ PADRÃO CORRETO - Interface (sem prefixo)
Route::prefix('[modulo]')->name('[modulo].')->group(function () {
    Route::prefix('[funcionalidade]')->name('[funcionalidade].')->group(function () {
        Route::get('/', [Web\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller::class, 'index'])
            ->name('index');
    });
});
```

#### **Rotas API (Dados para Vue.js)**
```php
// routes/web.php (NÃO em api.php)

// ✅ PADRÃO CORRETO - Dados com prefixo /api
Route::prefix('api/[modulo]')->name('api.[modulo].')->group(function () {
    Route::prefix('[funcionalidade]')->name('[funcionalidade].')->group(function () {
        Route::get('/', [Api\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller::class, 'listar'])
            ->name('listar');
        Route::post('/', [Api\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller::class, 'store'])
            ->name('store');
        Route::put('/{id}', [Api\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller::class, 'update'])
            ->name('update');
        Route::delete('/{id}', [Api\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller::class, 'destroy'])
            ->name('destroy');
    });
});
```

### 📝 **Exemplo Prático Completo**
```php
// routes/web.php

// ===== ROTAS WEB - INTERFACE =====
Route::prefix('administracao')->name('administracao.')->group(function () {
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [Web\Administracao\Usuarios\UsuariosController::class, 'index'])
            ->name('index');
    });
    
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [Web\Administracao\Roles\RolesController::class, 'index'])
            ->name('index');
    });
});

// ===== ROTAS API - DADOS PARA VUE.JS =====
Route::prefix('api/administracao')->name('api.administracao.')->group(function () {
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [Api\Administracao\Usuarios\UsuariosController::class, 'listar'])
            ->name('listar');
        Route::post('/', [Api\Administracao\Usuarios\UsuariosController::class, 'store'])
            ->name('store');
        Route::put('/{id}', [Api\Administracao\Usuarios\UsuariosController::class, 'update'])
            ->name('update');
        Route::delete('/{id}', [Api\Administracao\Usuarios\UsuariosController::class, 'destroy'])
            ->name('destroy');
    });
    
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [Api\Administracao\Roles\RolesController::class, 'listar'])
            ->name('listar');
        Route::post('/', [Api\Administracao\Roles\RolesController::class, 'store'])
            ->name('store');
        Route::put('/{id}', [Api\Administracao\Roles\RolesController::class, 'update'])
            ->name('update');
        Route::delete('/{id}', [Api\Administracao\Roles\RolesController::class, 'destroy'])
            ->name('destroy');
    });
});
```

---

## 3. Separação Web vs API

### 🌐 **Rotas Web (Interface)**
- **Propósito:** Interface do usuário (Vue.js)
- **Autenticação:** Sessão (session-based)
- **Controller:** `Web\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller`
- **Método:** `index()` - Retorna view container Vue
- **URL:** `/[modulo]/[funcionalidade]` (SEM prefixo)
- **Exemplo:** `/administracao/usuarios`

### 🔌 **Rotas API (Dados)**
- **Propósito:** Dados para Vue.js (CRUD operations)
- **Autenticação:** Sessão (session-based)
- **Controller:** `Api\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller`
- **Métodos:** `listar()`, `store()`, `update()`, `destroy()`
- **URL:** `/api/[modulo]/[funcionalidade]` (COM prefixo /api)
- **Exemplo:** `/api/administracao/usuarios`

### ⚠️ **IMPORTANTE: Estrutura de Arquivos**
- **TODAS as rotas** ficam em `routes/web.php`
- **NUNCA** usar `routes/api.php` para CRUD interno
- **SEMPRE** usar prefixo `/api` para rotas de dados
- **SEMPRE** usar autenticação de sessão para tudo
- **`routes/api.php`** fica vazio ou apenas para integração externa

---

## 4. Padrões de Nomenclatura

### 🏷️ **Tabela de Padrões**

| Elemento | Padrão | Exemplo |
|----------|--------|---------|
| **Módulos** | minúsculo com underscore | `administracao`, `orcamento`, `tabela_oficial` |
| **Funcionalidades** | minúsculo com underscore | `usuarios`, `composicao_propria`, `importar_derpr` |
| **Nomes de Rotas Web** | [modulo].[funcionalidade].[acao] | `administracao.usuarios.index` |
| **Nomes de Rotas API** | api.[modulo].[funcionalidade].[acao] | `api.administracao.usuarios.listar` |
| **Prefixos Web** | [modulo]/[funcionalidade] | `administracao/usuarios` |
| **Prefixos API** | api/[modulo]/[funcionalidade] | `api/administracao/usuarios` |

### 📝 **Regras Específicas**
- **Nomes de Rotas Web:** `[modulo].[funcionalidade].[acao]`
- **Nomes de Rotas API:** `api.[modulo].[funcionalidade].[acao]`
- **Prefixos de URL Web:** `[modulo]/[funcionalidade]` (SEM /api)
- **Prefixos de URL API:** `api/[modulo]/[funcionalidade]` (COM /api)
- **Parâmetros:** `{id}`, `{slug}`, `{id}/{sub_id}`
- **Controllers Web:** `Web\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller`
- **Controllers API:** `Api\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller`

### 📋 **Exemplos de Nomenclatura Completa**

#### **Rotas Web (Interface):**
```php
// URL: /administracao/usuarios
// Nome: administracao.usuarios.index
// Controller: Web\Administracao\Usuarios\UsuariosController
// Método: index()
```

#### **Rotas API (Dados):**
```php
// URL: /api/administracao/usuarios
// Nome: api.administracao.usuarios.listar
// Controller: Api\Administracao\Usuarios\UsuariosController
// Método: listar()

// URL: /api/administracao/usuarios
// Nome: api.administracao.usuarios.store
// Controller: Api\Administracao\Usuarios\UsuariosController
// Método: store()

// URL: /api/administracao/usuarios/{id}
// Nome: api.administracao.usuarios.update
// Controller: Api\Administracao\Usuarios\UsuariosController
// Método: update($id)

// URL: /api/administracao/usuarios/{id}
// Nome: api.administracao.usuarios.destroy
// Controller: Api\Administracao\Usuarios\UsuariosController
// Método: destroy($id)
```

---

## 5. Métodos dos Controllers

### 📋 **Controller Web**
```php
class [Funcionalidade]Controller extends Controller
{
    /**
     * Exibe a interface do CRUD
     */
    public function index()
    {
        return view('[modulo].[funcionalidade].index');
    }
}
```

### 📋 **Controller API**
```php
class [Funcionalidade]Controller extends Controller
{
    /**
     * Lista registros com filtros e paginação
     */
    public function listar(Request $request)
    {
        // Lógica de listagem
        return response()->json($registros);
    }

    /**
     * Cria um novo registro
     */
    public function store(Request $request)
    {
        // Lógica de criação
        return response()->json($registro, 201);
    }

    /**
     * Atualiza um registro existente
     */
    public function update(Request $request, $id)
    {
        // Lógica de atualização
        return response()->json($registro);
    }

    /**
     * Remove um registro
     */
    public function destroy($id)
    {
        // Lógica de exclusão
        return response()->json(['message' => 'Registro excluído']);
    }
}
```

---

## 6. Middlewares

### 🛡️ **Middlewares Padrão**
```php
Route::middleware(['auth'])->group(function () {
    // Rotas que precisam de autenticação
    Route::prefix('[modulo]')->name('[modulo].')->group(function () {
        // Rotas Web e API
    });
});

Route::middleware(['auth', 'can:gerenciar-[modulo]'])->group(function () {
    // Rotas que precisam de permissão específica
});
```

### 🛡️ **Exemplo Prático**
```php
Route::middleware(['auth'])->group(function () {
    // ===== ROTAS WEB - INTERFACE =====
    Route::prefix('administracao')->name('administracao.')->group(function () {
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [Web\Administracao\Usuarios\UsuariosController::class, 'index'])
                ->name('index');
        });
    });

    // ===== ROTAS API - DADOS =====
    Route::prefix('api/administracao')->name('api.administracao.')->group(function () {
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [Api\Administracao\Usuarios\UsuariosController::class, 'listar'])
                ->name('listar');
            Route::post('/', [Api\Administracao\Usuarios\UsuariosController::class, 'store'])
                ->name('store');
            Route::put('/{id}', [Api\Administracao\Usuarios\UsuariosController::class, 'update'])
                ->name('update');
            Route::delete('/{id}', [Api\Administracao\Usuarios\UsuariosController::class, 'destroy'])
                ->name('destroy');
        });
    });
});
```

---

## 7. Proibições Essenciais

### 🚫 **NÃO Fazer**

#### **Estrutura de Rotas**
- **NÃO** usar apenas um controller (sempre Web + Api)
- **NÃO** misturar rotas Web e API no mesmo controller
- **NÃO** colocar rotas API em `routes/api.php` (sempre em web.php)
- **NÃO** criar rotas sem agrupamento por módulo
- **NÃO** usar nomes genéricos como `page`, `action`, `do`
- **NÃO** esquecer do prefixo `/api` para rotas de dados

#### **Nomenclatura**
- **NÃO** usar nomes de rotas inconsistentes
- **NÃO** esquecer de incluir `->name()` nas rotas
- **NÃO** usar abreviações não claras
- **NÃO** misturar padrões de nomenclatura
- **NÃO** usar rotas de dados sem prefixo `/api`

#### **Organização**
- **NÃO** deixar rotas soltas sem agrupamento
- **NÃO** usar middlewares desnecessários
- **NÃO** criar rotas duplicadas
- **NÃO** usar autenticação JWT para CRUD interno
- **NÃO** colocar rotas em arquivos diferentes

---

## 8. Checklist de Implementação

### 📋 **Para Nova Funcionalidade**

- [ ] Definir prefixo Web seguindo padrão `[modulo]/[funcionalidade]`
- [ ] Definir prefixo API seguindo padrão `api/[modulo]/[funcionalidade]`
- [ ] Criar grupo de rotas Web com prefixo
- [ ] Criar grupo de rotas API com prefixo
- [ ] Adicionar rota Web com `->name('[modulo].[funcionalidade].index')`
- [ ] Adicionar rotas API com nomes explicativos
- [ ] Criar controller Web em `Web\[Modulo]\[Funcionalidade]`
- [ ] Criar controller API em `Api\[Modulo]\[Funcionalidade]`
- [ ] Usar nomenclatura padrão para nomes de rotas
- [ ] Aplicar middlewares adequados
- [ ] **COLOCAR TODAS AS ROTAS em `routes/web.php`**
- [ ] **SEMPRE usar prefixo `/api` para rotas de dados**
- [ ] Testar todas as rotas criadas
- [ ] Verificar que rotas API retornam JSON

---

## 9. Estrutura Final dos Arquivos

### 📁 **routes/web.php**
```php
<?php

use Illuminate\Support\Facades\Route;

// ===== ROTAS DE AUTENTICAÇÃO =====
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ===== ROTAS PROTEGIDAS =====
Route::middleware(['auth'])->group(function () {
    
    // ===== ROTAS WEB - INTERFACE =====
    Route::prefix('administracao')->name('administracao.')->group(function () {
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [Web\Administracao\Usuarios\UsuariosController::class, 'index'])->name('index');
        });
    });
    
    // ===== ROTAS API - DADOS =====
    Route::prefix('api/administracao')->name('api.administracao.')->group(function () {
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [Api\Administracao\Usuarios\UsuariosController::class, 'listar'])->name('listar');
            Route::post('/', [Api\Administracao\Usuarios\UsuariosController::class, 'store'])->name('store');
            Route::put('/{id}', [Api\Administracao\Usuarios\UsuariosController::class, 'update'])->name('update');
            Route::delete('/{id}', [Api\Administracao\Usuarios\UsuariosController::class, 'destroy'])->name('destroy');
        });
    });
});
```

### 📁 **routes/api.php**
```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ===== ROTAS PARA INTEGRAÇÃO EXTERNA (se necessário) =====
// Route::post('/external/sync', [ExternalController::class, 'sync']);

// ===== ROTAS INTERNAS FICAM EM web.php =====
```

---

## 10. Conclusão

### 🎉 **RESULTADO FINAL**

**Este documento define claramente como estruturar rotas no projeto!**

**Qualquer desenvolvedor (ou IA) consegue criar rotas sem dúvidas sobre:**

- ✅ **TODAS as rotas em web.php** - Simplicidade e segurança
- ✅ **Prefixo /api obrigatório** - Para rotas de dados
- ✅ **Estrutura Padrão** - Como organizar rotas Web e API
- ✅ **Separação Web vs API** - Onde colocar cada tipo
- ✅ **Nomenclatura** - Como nomear rotas e prefixos
- ✅ **Controllers** - Como estruturar Web e Api controllers
- ✅ **Middlewares** - Como aplicar autenticação e autorização
- ✅ **Proibições** - O que NÃO fazer
- ✅ **Vue.js + API** - Padrão específico para interface dinâmica

**Com este documento, qualquer nova funcionalidade terá rotas organizadas de forma consistente e escalável!**

---

> **IMPORTANTE**: Este documento deve ser atualizado sempre que houver mudanças no padrão de rotas. Todos os desenvolvedores devem seguir estas diretrizes para manter consistência. 