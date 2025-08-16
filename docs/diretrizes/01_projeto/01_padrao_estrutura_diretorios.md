# Padrão de Estrutura de Diretórios - OrçaCidade

> **DOCUMENTO MESTRE**: Este documento define onde cada arquivo deve ser colocado no projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para que qualquer desenvolvedor (ou IA) saiba exatamente onde criar cada arquivo de uma nova funcionalidade.

> **ATUALIZADO EM 2025**: Padrão evoluído para Vue.js + API com TODAS as rotas em web.php para simplicidade e segurança.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Estabelecer uma estrutura hierárquica clara e consistente para organização de arquivos, facilitando manutenção, escalabilidade e colaboração da equipe.

### 🏗️ **Estrutura Hierárquica**
```
MÓDULO → FUNCIONALIDADE → ARQUIVOS_ESPECÍFICOS
```

### 🎯 **Princípios Fundamentais**
- **TODAS as rotas em web.php** - Simplicidade e segurança
- **Separação Web/Api** - Controller Web para view, Controller Api para dados
- **Vue.js + API** - Interface dinâmica com Vue.js e API para dados
- **Modal Obrigatório** - Criar/editar sempre em modal (dentro do componente Vue)
- **Responsabilidade Única** - Um CRUD = Dois Controllers (Web + Api)
- **Interface Consistente** - Front-end padronizado e uniforme
- **Prefixo /api obrigatório** - Para rotas de dados
- **Autenticação session-based** - Para todas as rotas

---

## 2. Estrutura Completa por Funcionalidade

### 📁 **Estrutura Padrão**

```
MÓDULO: [nome_modulo]
└── FUNCIONALIDADE: [nome_funcionalidade]
    ├── 📁 Controllers Web: app/Http/Controllers/Web/[Modulo]/[Funcionalidade]/
    │   └── [Funcionalidade]Controller.php
    │
    ├── 📁 Controllers API: app/Http/Controllers/Api/[Modulo]/[Funcionalidade]/
    │   └── [Funcionalidade]Controller.php
    │
    ├── 📁 Services: app/Services/[Modulo]/
    │   └── [Funcionalidade]Service.php (quando necessário)
    │
    ├── 📁 Models: app/Models/[Modulo]/
    │   └── [Funcionalidade].php
    │
    ├── 📁 Views: resources/views/[modulo]/[funcionalidade]/
    │   └── index.blade.php (container Vue)
    │
    ├── 📁 Vue: resources/js/components/[modulo]/[funcionalidade]/
    │   └── Lista[Funcionalidade].vue
    │
    ├── 📁 Middlewares: app/Http/Middleware/
    │   └── CheckRole.php (quando necessário)
    │
    └── 📁 Rotas: routes/web.php (TODAS as rotas)
        ├── Rotas Web: /[modulo]/[funcionalidade]
        └── Rotas API: /api/[modulo]/[funcionalidade]
```

### 📝 **Quando Usar Services**

- **Use services** para operações reutilizáveis em múltiplos controllers
- **Use services** para lógica de negócio complexa que pode ser compartilhada
- **NÃO use services** para subrotinas específicas de um CRUD
- **NÃO use services** para operações que só servem aquele controller

---

## 3. Exemplo Prático Completo

### 🎯 **Funcionalidade: "usuarios" no módulo "administracao"**

```
MÓDULO: administracao
└── FUNCIONALIDADE: usuarios
    ├── 📁 Controllers Web: app/Http/Controllers/Web/Administracao/Usuarios/
    │   └── UsuariosController.php
    │
    ├── 📁 Controllers API: app/Http/Controllers/Api/Administracao/Usuarios/
    │   └── UsuariosController.php
    │
    ├── 📁 Services: app/Services/Administracao/
    │   └── UsuariosService.php (quando necessário)
    │
    ├── 📁 Models: app/Models/Administracao/
    │   ├── User.php
    │   ├── Role.php
    │   └── Permission.php
    │
    ├── 📁 Views: resources/views/administracao/usuarios/
    │   └── index.blade.php (container Vue)
    │
    ├── 📁 Vue: resources/js/components/administracao/usuarios/
    │   └── ListaUsuarios.vue
    │
    ├── 📁 Middlewares: app/Http/Middleware/
    │   └── CheckRole.php (quando necessário)
    │
    └── 📁 Rotas: routes/web.php
        ├── Rotas Web: /administracao/usuarios
        └── Rotas API: /api/administracao/usuarios
```

---

## 4. Estrutura de Rotas

### 🛣️ **Localização das Rotas**

#### **📁 routes/web.php (TODAS as rotas)**
```php
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
```

#### **📁 routes/api.php (vazio ou mínimo)**
```php
<?php
// ===== ROTAS PARA INTEGRAÇÃO EXTERNA (se necessário) =====
// Route::post('/external/sync', [ExternalController::class, 'sync']);

// ===== ROTAS INTERNAS FICAM EM web.php =====
```

### 🛡️ **Middlewares e Autenticação**

#### **📁 app/Http/Middleware/**
```php
// ===== MIDDLEWARE PADRÃO =====
Route::middleware(['auth'])->group(function () {
    // Todas as rotas protegidas aqui
});

// ===== MIDDLEWARE DE PAPEL =====
Route::middleware(['auth', 'can:gerenciar-[modulo]'])->group(function () {
    // Rotas que precisam de permissão específica
});
```

---

## 5. Padrões de Nomenclatura

### 🏷️ **Tabela de Padrões**

| Elemento | Padrão | Exemplo |
|----------|--------|---------|
| **Módulos** | minúsculo com underscore | `administracao`, `orcamento`, `tabela_oficial` |
| **Funcionalidades** | minúsculo com underscore | `usuarios`, `composicao_propria`, `importar_derpr` |
| **Controllers Web** | PascalCase + Controller | `UsuariosController` |
| **Controllers API** | PascalCase + Controller | `UsuariosController` |
| **Models** | PascalCase | `User`, `ComposicaoPropria` |
| **Services** | PascalCase + Service | `UsuariosService` |
| **Views** | snake_case | `index.blade.php` |
| **Componentes Vue** | `Lista[Funcionalidade].vue` | `ListaUsuarios.vue` |
| **Rotas Web** | /[modulo]/[funcionalidade] | `/administracao/usuarios` |
| **Rotas API** | /api/[modulo]/[funcionalidade] | `/api/administracao/usuarios` |

### 📝 **Regras Específicas**

#### **Controllers Web**
- **Namespace:** `App\Http\Controllers\Web\[Modulo]\[Funcionalidade]\[Controller]`
- **Sufixo:** Sempre `Controller`
- **Exemplo:** `App\Http\Controllers\Web\Administracao\Usuarios\UsuariosController`

#### **Controllers API**
- **Namespace:** `App\Http\Controllers\Api\[Modulo]\[Funcionalidade]\[Controller]`
- **Sufixo:** Sempre `Controller`
- **Exemplo:** `App\Http\Controllers\Api\Administracao\Usuarios\UsuariosController`

#### **Models**
- **Namespace:** `App\Models\[Modulo]\[Model]`
- **Exemplo:** `App\Models\Administracao\User`

#### **Services**
- **Namespace:** `App\Services\[Modulo]\[Service]`
- **Sufixo:** Sempre `Service`
- **Exemplo:** `App\Services\Administracao\UsuariosService`

#### **Views**
- **Estrutura:** `resources/views/[modulo]/[funcionalidade]/index.blade.php`
- **Padrão:** Sempre `index.blade.php` (container Vue)

#### **Componentes Vue**
- **Estrutura:** `resources/js/components/[modulo]/[funcionalidade]/Lista[Funcionalidade].vue`
- **Registro:** Centralizado em `resources/js/app.js`
- **Nomenclatura:** `Lista[Funcionalidade].vue`

#### **Rotas**
- **Localização:** **SEMPRE** em `routes/web.php`
- **Rotas Web:** `/[modulo]/[funcionalidade]` (SEM prefixo)
- **Rotas API:** `/api/[modulo]/[funcionalidade]` (COM prefixo /api)
- **Nomes Web:** `[modulo].[funcionalidade].[acao]`
- **Nomes API:** `api.[modulo].[funcionalidade].[acao]`

---

## 6. Registro de Componentes Vue

### 📝 **Padrão de Registro**

```javascript
// resources/js/app.js

// Componentes [Modulo]/[Funcionalidade]
import Lista[Funcionalidade] from './components/[modulo]/[funcionalidade]/Lista[Funcionalidade].vue';

app.component('lista-[funcionalidade]', Lista[Funcionalidade]);
```

### 🏷️ **Exemplo Prático**

```javascript
// Componentes Administracao/Usuarios
import ListaUsuarios from './components/administracao/usuarios/ListaUsuarios.vue';

app.component('lista-usuarios', ListaUsuarios);
```

---

## 7. Princípios Fundamentais

### ✅ **Regras Obrigatórias**

1. **SEMPRE usar web.php** - Todas as rotas em um só arquivo
2. **SEMPRE usar prefixo /api** - Para rotas de dados
3. **Sempre usar separação Web/Api** - Dois controllers por funcionalidade
4. **Sempre usar Vue.js + API** - Interface dinâmica
5. **Sempre usar modal para criar/editar** - Dentro do componente Vue
6. **Manter estrutura consistente** em todos os módulos
7. **Seguir nomenclatura padronizada**
8. **Organizar hierarquicamente**
9. **Registrar componentes Vue no app.js**
10. **Usar autenticação session-based** para todas as rotas

### 🚫 **Proibições**

1. **NÃO criar arquivos soltos** (sempre dentro da estrutura)
2. **NÃO usar nomenclatura inconsistente**
3. **NÃO misturar padrões** (ex: camelCase e snake_case)
4. **NÃO usar apenas um controller** (sempre Web + Api)
5. **NÃO usar páginas separadas para criar/editar** (sempre modal)
6. **NÃO esquecer de registrar componentes Vue**
7. **NÃO colocar rotas em api.php** (sempre em web.php)
8. **NÃO esquecer do prefixo /api** para rotas de dados
9. **NÃO usar autenticação JWT** para CRUD interno

---

## 8. Checklist de Implementação

### 📋 **Para Nova Funcionalidade**

- [ ] Criar estrutura de pastas completa
- [ ] Definir namespace correto
- [ ] Criar controller Web com nomenclatura padrão
- [ ] Criar controller Api com nomenclatura padrão
- [ ] Criar model necessário
- [ ] Criar service (se necessário)
- [ ] Criar view index (container Vue)
- [ ] Criar componente Vue `Lista[Funcionalidade].vue`
- [ ] Registrar componente no `app.js`
- [ ] **COLOCAR TODAS AS ROTAS em `routes/web.php`**
- [ ] **SEMPRE usar prefixo `/api` para rotas de dados**
- [ ] **NUNCA usar `routes/api.php` para CRUD interno**
- [ ] Testar se o componente carrega sem erros
- [ ] Testar se as rotas funcionam corretamente

---

## 9. Conclusão

### 🎉 **RESULTADO FINAL**

**Este documento define claramente onde colocar cada arquivo no projeto!**

**Qualquer desenvolvedor (ou IA) consegue criar funcionalidades sem dúvidas sobre:**

- ✅ **Estrutura de diretórios** - Onde colocar cada arquivo
- ✅ **Nomenclatura** - Como nomear arquivos e pastas
- ✅ **Namespaces** - Estrutura completa de namespaces
- ✅ **Separação Web/Api** - Dois controllers por funcionalidade
- ✅ **Vue.js + API** - Interface dinâmica com modal
- ✅ **Registro de componentes** - Como registrar no app.js
- ✅ **Localização das rotas** - **TODAS em web.php**
- ✅ **Prefixo /api obrigatório** - Para rotas de dados
- ✅ **Autenticação session-based** - Para todas as rotas

**Com este documento, qualquer nova funcionalidade será criada de forma consistente, organizada e escalável!**

---

> **IMPORTANTE**: Este documento deve ser revisado e atualizado sempre que houver mudanças na estrutura do projeto. Todos os desenvolvedores devem seguir rigorosamente estas diretrizes. 