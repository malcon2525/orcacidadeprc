# Padrão de Estrutura de Diretórios - OrçaCidade

> **DOCUMENTO MESTRE**: Este documento define onde cada arquivo deve ser colocado no projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para que qualquer desenvolvedor (ou IA) saiba exatamente onde criar cada arquivo de uma nova funcionalidade.

> **ATUALIZADO EM 2025**: Padrão evoluído para Vue.js + API para melhor UX e interatividade.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Estabelecer uma estrutura hierárquica clara e consistente para organização de arquivos, facilitando manutenção, escalabilidade e colaboração da equipe.

### 🏗️ **Estrutura Hierárquica**
```
MÓDULO → FUNCIONALIDADE → ARQUIVOS_ESPECÍFICOS
```

### 🎯 **Princípios Fundamentais**
- **Separação Web/Api** - Controller Web para view, Controller Api para dados
- **Vue.js + API** - Interface dinâmica com Vue.js e API para dados
- **Modal Obrigatório** - Criar/editar sempre em modal (dentro do componente Vue)
- **Responsabilidade Única** - Um CRUD = Dois Controllers (Web + Api)
- **Interface Consistente** - Front-end padronizado e uniforme

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
    ├── 📁 Models: app/Models/
    │   └── [Funcionalidade].php
    │
    ├── 📁 Views: resources/views/[modulo]/[funcionalidade]/
    │   └── index.blade.php (container Vue)
    │
    └── 📁 Vue: resources/js/components/[modulo]/[funcionalidade]/
        └── Lista[Funcionalidade].vue
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
    ├── 📁 Models: app/Models/
    │   └── User.php
    │
    ├── 📁 Views: resources/views/administracao/usuarios/
    │   └── index.blade.php (container Vue)
    │
    └── 📁 Vue: resources/js/components/administracao/usuarios/
        └── ListaUsuarios.vue
```

---

## 4. Padrões de Nomenclatura

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
- **Namespace:** `App\Models\[Model]`
- **Exemplo:** `App\Models\User`

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

---

## 5. Registro de Componentes Vue

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

## 6. Princípios Fundamentais

### ✅ **Regras Obrigatórias**

1. **Sempre usar separação Web/Api** - Dois controllers por funcionalidade
2. **Sempre usar Vue.js + API** - Interface dinâmica
3. **Sempre usar modal para criar/editar** - Dentro do componente Vue
4. **Manter estrutura consistente** em todos os módulos
5. **Seguir nomenclatura padronizada**
6. **Organizar hierarquicamente**
7. **Registrar componentes Vue no app.js**

### 🚫 **Proibições**

1. **NÃO criar arquivos soltos** (sempre dentro da estrutura)
2. **NÃO usar nomenclatura inconsistente**
3. **NÃO misturar padrões** (ex: camelCase e snake_case)
4. **NÃO usar apenas um controller** (sempre Web + Api)
5. **NÃO usar páginas separadas para criar/editar** (sempre modal)
6. **NÃO esquecer de registrar componentes Vue**

---

## 7. Checklist de Implementação

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
- [ ] Testar se o componente carrega sem erros

---

## 8. Conclusão

### 🎉 **RESULTADO FINAL**

**Este documento define claramente onde colocar cada arquivo no projeto!**

**Qualquer desenvolvedor (ou IA) consegue criar funcionalidades sem dúvidas sobre:**

- ✅ **Estrutura de diretórios** - Onde colocar cada arquivo
- ✅ **Nomenclatura** - Como nomear arquivos e pastas
- ✅ **Namespaces** - Estrutura completa de namespaces
- ✅ **Separação Web/Api** - Dois controllers por funcionalidade
- ✅ **Vue.js + API** - Interface dinâmica com modal
- ✅ **Registro de componentes** - Como registrar no app.js

**Com este documento, qualquer nova funcionalidade será criada de forma consistente, organizada e escalável!**

---

> **IMPORTANTE**: Este documento deve ser revisado e atualizado sempre que houver mudanças na estrutura do projeto. Todos os desenvolvedores devem seguir rigorosamente estas diretrizes. 