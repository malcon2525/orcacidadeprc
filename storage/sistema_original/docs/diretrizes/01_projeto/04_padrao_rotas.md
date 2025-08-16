# Padrão de Rotas - OrçaCidade

> **DOCUMENTO MESTRE**: Este documento define como estruturar rotas no projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para manter consistência e organização.

> **ATUALIZADO EM 2025**: Padrão evoluído para Vue.js + API com separação clara entre interface e dados.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Definir padrões para estrutura e nomenclatura de rotas, facilitando manutenção e colaboração da equipe.

### 🛣️ **Princípios Fundamentais**
- **Separação Web/Api** - Rotas Web para interface, rotas API para dados
- **Vue.js + API** - Interface dinâmica com Vue.js e API para dados
- **Consistência** - Mesmo padrão para todas as rotas
- **Clareza** - Nomes descritivos e intuitivos
- **Organização** - Separação clara entre Web e API

---

## 2. Estrutura de Rotas

### 📋 **Estrutura Padrão**

#### **Rotas Web (Interface)**
```php
// routes/web.php

// ✅ PADRÃO CORRETO - Vue.js + API (implementado em 2025)
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

// ✅ PADRÃO CORRETO - API para Vue.js (implementado em 2025)
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

### 📝 **Exemplo Prático**
```php
// routes/web.php

// Rotas Web - Interface
Route::prefix('administracao')->name('administracao.')->group(function () {
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [Web\Administracao\Usuarios\UsuariosController::class, 'index'])
            ->name('index');
    });
});

// Rotas API - Dados para Vue.js
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
```

---

## 3. Separação Web vs API

### 🌐 **Rotas Web (`routes/web.php`)**
- **Propósito:** Interface do usuário (Vue.js)
- **Autenticação:** Sessão (session-based)
- **Controller:** `Web\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller`
- **Método:** `index()` - Retorna view container Vue
- **URL:** `/[modulo]/[funcionalidade]`
- **Exemplo:** `/administracao/usuarios`

### 🔌 **Rotas API (`routes/web.php`)**
- **Propósito:** Dados para Vue.js (CRUD operations)
- **Autenticação:** Sessão (session-based)
- **Controller:** `Api\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller`
- **Métodos:** `listar()`, `store()`, `update()`, `destroy()`
- **URL:** `/api/[modulo]/[funcionalidade]`
- **Exemplo:** `/api/administracao/usuarios`

### ⚠️ **IMPORTANTE: Separação de Rotas**
- **SEMPRE** usar `routes/web.php` para rotas de interface (Vue.js)
- **SEMPRE** usar `routes/web.php` para rotas API internas (Vue.js)
- **NUNCA** usar `routes/api.php` para CRUD interno (causa erro 401)
- **SEMPRE** usar autenticação de sessão para CRUD interno
- **SEMPRE** usar `routes/api.php` apenas para APIs externas (JWT)

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
- **Prefixos de URL Web:** `[modulo]/[funcionalidade]`
- **Prefixos de URL API:** `api/[modulo]/[funcionalidade]`
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
    // Rotas Web - Interface
    Route::prefix('administracao')->name('administracao.')->group(function () {
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [Web\Administracao\Usuarios\UsuariosController::class, 'index'])
                ->name('index');
        });
    });

    // Rotas API - Dados
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
- **NÃO** usar rotas API em `routes/api.php` para CRUD interno
- **NÃO** criar rotas sem agrupamento por módulo
- **NÃO** usar nomes genéricos como `page`, `action`, `do`

#### **Nomenclatura**
- **NÃO** usar nomes de rotas inconsistentes
- **NÃO** esquecer de incluir `->name()` nas rotas
- **NÃO** usar abreviações não claras
- **NÃO** misturar padrões de nomenclatura

#### **Organização**
- **NÃO** deixar rotas soltas sem agrupamento
- **NÃO** usar middlewares desnecessários
- **NÃO** criar rotas duplicadas
- **NÃO** usar autenticação JWT para CRUD interno

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
- [ ] Colocar rotas API em `routes/web.php` (não `api.php`)
- [ ] Testar todas as rotas criadas
- [ ] Verificar que rotas API retornam JSON

---

## 9. Conclusão

### 🎉 **RESULTADO FINAL**

**Este documento define claramente como estruturar rotas no projeto!**

**Qualquer desenvolvedor (ou IA) consegue criar rotas sem dúvidas sobre:**

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