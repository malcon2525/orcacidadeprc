# Padrão CRUD Universal - OrçaCidade

> **DOCUMENTO MESTRE**: Este documento define padrões UNIVERSAIS para criação de CRUDs no projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para manter consistência e qualidade.

> **ATUALIZADO EM 2025**: Padrão evoluído para Vue.js + API para melhor UX e interatividade.

> **ESCOPO**: Este arquivo contém APENAS padrões universais. Para padrões específicos de interface, consulte os arquivos especializados.

---

## 📋 **EXEMPLO DE USO DESTE ARQUIVO**

### 🎯 **Instrução para IA:**
```
"Baseado no migrate X, seguindo o padrão deste arquivo '01_padrao_crud.md', implemente o CRUD Y"
```

### 📝 **Como Funciona:**
1. **Analise** o migrate para entender a estrutura da tabela
2. **Aplique** os padrões universais definidos neste documento
3. **Consulte** o arquivo especializado para o tipo de interface (com/sem abas)
4. **Gere** código completo e funcional
5. **Mantenha** consistência visual e estrutural

### ✅ **Resultado Esperado:**
- CRUD completo (Controllers Web/Api, Model, View container, Componente Vue)
- Interface dinâmica com Vue.js + API
- Código organizado e padronizado
- Funcionalidade pronta para uso
- **Modal obrigatório** para criar/editar (dentro do componente Vue)
- **Filtros ocultos** por padrão com toggle
- **Paginação** implementada conforme padrão da interface
- **Componente Vue** com toda a lógica de interface
- **Modal de confirmação personalizado** para exclusões (NÃO usar confirm() nativo)

---

## ⚠️ **ARQUIVOS ESPECIALIZADOS OBRIGATÓRIOS**

### 📁 **Para Interfaces SEM Abas:**
- **Consulte**: `02_padrao_crud_sem_abas.md`
- **Exemplos**: ListaUsuarios.vue, ListaEntidadesOrcamentarias.vue
- **Características**: Componente monolítico, paginação local

### 📁 **Para Interfaces COM Abas:**
- **Consulte**: `03_padrao_crud_com_abas.md`
- **Exemplos**: GestaoEstruturaOrcamento.vue
- **Características**: Estrutura parent-child, paginação centralizada

---

## 2. Estrutura de Arquivos

### 📁 **Estrutura Padrão**
```
app/
├── Http/Controllers/
│   ├── Web/
│   │   └── [Modulo]/
│   │       └── [Funcionalidade]/
│   │           └── [Funcionalidade]Controller.php
│   └── Api/
│       └── [Modulo]/
│           └── [Funcionalidade]/
│               └── [Funcionalidade]Controller.php
├── Services/
│   └── [Modulo]/
│       └── [Funcionalidade]Service.php (quando necessário)
├── Models/
│   └── [Modulo]/
│       └── [Funcionalidade].php
└── resources/
    ├── views/
    │   └── [modulo]/
    │       └── [funcionalidade]/
    │           └── index.blade.php (container Vue)
    └── js/
        └── components/
            └── [modulo]/
                └── [funcionalidade]/
                    └── Lista[Funcionalidade].vue
```

### 📋 **Exemplo Prático**
```
app/
├── Http/Controllers/
│   ├── Web/
│   │   └── Administracao/
│   │       └── Usuarios/
│   │           └── UsuariosController.php
│   └── Api/
│       └── Administracao/
│           └── Usuarios/
│               └── UsuariosController.php
├── Services/
│   └── Administracao/
│       └── UsuariosService.php
├── Models/
│   ├── User.php (core)
│   └── Administracao/
│       └── Usuarios.php
└── resources/
    ├── views/
    │   └── administracao/
    │       └── usuarios/
    │           └── index.blade.php (container Vue)
    └── js/
        └── components/
            └── administracao/
                └── usuarios/
                    └── ListaUsuarios.vue
```

---

## 3. Controllers

### 📋 **Controller Web (Obrigatório)**
```php
<?php

namespace App\Http\Controllers\Web\[Modulo]\[Funcionalidade];

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\[Modulo]\[Funcionalidade];

class [Funcionalidade]Controller extends Controller
{
    /**
     * Exibe a interface principal
     */
    public function index()
    {
        return view('[modulo].[funcionalidade].index');
    }
}
```

### 📋 **Controller API (Obrigatório)**
```php
<?php

namespace App\Http\Controllers\Api\[Modulo]\[Funcionalidade];

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\[Modulo]\[Funcionalidade];
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class [Funcionalidade]Controller extends Controller
{
    /**
     * Lista registros com paginação
     */
    public function index(Request $request)
    {
        $query = [Funcionalidade]::query();
        
        // Filtros
        if ($request->filled('busca')) {
            $query->where('nome', 'like', '%' . $request->busca . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $registros = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return response()->json($registros);
    }

    /**
     * Armazena um novo recurso
     */
    public function store(Request $request)
    {
        Log::info('Iniciando criação de [funcionalidade]', $request->all());
        
        // VALIDAÇÃO OBRIGATÓRIA - SEMPRE USAR ESTE PADRÃO
        $mensagens = [
            'required' => 'O campo :attribute é obrigatório',
            'string' => 'O campo :attribute deve ser um texto',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres',
            'email' => 'O campo :attribute deve ser um e-mail válido',
            'unique' => 'Este :attribute já está cadastrado',
            'integer' => 'O campo :attribute deve ser um número inteiro',
            'in' => 'O valor selecionado para :attribute é inválido',
            'numeric' => 'O campo :attribute deve ser um número',
            'date' => 'O campo :attribute deve ser uma data válida'
        ];

        $atributos = [
            'nome' => 'nome',
            'email' => 'e-mail',
            'status' => 'status',
            'descricao' => 'descrição'
        ];

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:[tabela]',
            'status' => 'required|in:ativo,inativo',
            'descricao' => 'nullable|string|max:500'
        ], $mensagens, $atributos);

        $[funcionalidade] = [Funcionalidade]::create($validated);
        
        Log::info('[Funcionalidade] criado com sucesso', ['id' => $[funcionalidade]->id]);

        return response()->json($[funcionalidade], 201);
    }

    /**
     * Atualiza um recurso existente
     */
    public function update(Request $request, $id)
    {
        Log::info('Iniciando atualização de [funcionalidade]', ['id' => $id, 'dados' => $request->all()]);
        
        $[funcionalidade] = [Funcionalidade]::findOrFail($id);

        // VALIDAÇÃO OBRIGATÓRIA - SEMPRE USAR ESTE PADRÃO
        $mensagens = [
            'required' => 'O campo :attribute é obrigatório',
            'string' => 'O campo :attribute deve ser um texto',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres',
            'email' => 'O campo :attribute deve ser um e-mail válido',
            'unique' => 'Este :attribute já está cadastrado',
            'integer' => 'O campo :attribute deve ser um número inteiro',
            'in' => 'O valor selecionado para :attribute é inválido',
            'numeric' => 'O campo :attribute deve ser um número',
            'date' => 'O campo :attribute deve ser uma data válida'
        ];

        $atributos = [
            'nome' => 'nome',
            'email' => 'e-mail',
            'status' => 'status',
            'descricao' => 'descrição'
        ];

        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:[tabela],nome,' . $id,
            'email' => 'required|email|max:255|unique:[tabela],email,' . $id,
            'status' => 'required|in:ativo,inativo',
            'descricao' => 'nullable|string|max:500'
        ], $mensagens, $atributos);

        $[funcionalidade]->update($validated);
        
        Log::info('[Funcionalidade] atualizado com sucesso', ['id' => $id]);

        return response()->json($[funcionalidade]);
    }

    /**
     * Remove um recurso
     */
    public function destroy($id)
    {
        Log::info('Iniciando exclusão de [funcionalidade]', ['id' => $id]);
        
        $[funcionalidade] = [Funcionalidade]::findOrFail($id);
        $[funcionalidade]->delete();
        
        Log::info('[Funcionalidade] excluído com sucesso', ['id' => $id]);

        return response()->json(null, 204);
    }
}
```

---

## 4. Model

### 📋 **Model Padrão**
```php
<?php

namespace App\Models\[Modulo];

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class [Funcionalidade] extends Model
{
    use HasFactory;
    
    /**
     * A tabela associada ao modelo.
     */
    protected $table = '[tabela]';
    
    /**
     * Os atributos que são atribuíveis em massa.
     */
    protected $fillable = [
        'nome',
        'email',
        'status',
        'descricao'
    ];

    /**
     * Os atributos que devem ser convertidos.
     */
    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Os atributos que devem ser ocultos para arrays.
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Scope para registros ativos
     */
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para registros inativos
     */
    public function scopeInativo($query)
    {
        return $query->where('ativo', false);
    }
}
```

---

## 5. View Container

### 📋 **View Index (Obrigatória)**
```php
@extends('layouts.app')

@section('content')
  <div class="container-fluid px-4">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
      <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                <i class="fas fa-[icon] me-2"></i>[Título da Funcionalidade]
            </h6>
      </div>
      <div class="card-body">
            <!-- Componente Vue será renderizado aqui -->
            <div id="app">
                <lista-[funcionalidade]></lista-[funcionalidade]>
        </div>
        </div>
      </div>
    </div>

@push('scripts')
<script>
    // Registro do componente Vue
    app.component('lista-[funcionalidade]', {
        template: `@include('components.administracao.[funcionalidade].Lista[Funcionalidade]')`
    });
</script>
@endpush
@endsection
```

---

## 6. Rotas

### 📋 **Rotas Web (Obrigatórias)**
```php
// routes/web.php

// Grupo de rotas para [Módulo]
Route::prefix('[modulo]')->name('[modulo].')->group(function () {
    
    // Rotas para [Funcionalidade]
    Route::prefix('[funcionalidade]')->name('[funcionalidade].')->group(function () {
        
        // Rota principal (interface)
        Route::get('/', [App\Http\Controllers\Web\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller::class, 'index'])
            ->name('index');
    });
});
```

### 📋 **Rotas API (Obrigatórias)**
```php
// routes/web.php (NÃO em api.php)

// Grupo de rotas API para [Módulo]
Route::prefix('api/[modulo]')->name('api.[modulo].')->group(function () {
    
    // Rotas API para [Funcionalidade]
    Route::prefix('[funcionalidade]')->name('[funcionalidade].')->group(function () {
        
        // CRUD API
        Route::get('/', [App\Http\Controllers\Api\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller::class, 'index'])
            ->name('index');
        Route::post('/', [App\Http\Controllers\Api\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller::class, 'store'])
            ->name('store');
        Route::put('/{id}', [App\Http\Controllers\Api\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller::class, 'update'])
            ->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Api\[Modulo]\[Funcionalidade]\[Funcionalidade]Controller::class, 'destroy'])
            ->name('destroy');
    });
});
```

---

## 7. Validação Obrigatória

### 🚫 **PROIBIÇÕES CRÍTICAS**
- **NUNCA** usar `required` no HTML
- **NUNCA** usar `pattern` no HTML  
- **NUNCA** usar `minlength`/`maxlength` no HTML
- **NUNCA** usar `type="email"` no HTML (usar `type="text"`)
- **SEMPRE** validar no backend Laravel
- **SEMPRE** retornar JSON com erros (422)

### ✅ **PADRÃO OBRIGATÓRIO DE VALIDAÇÃO**
```php
// SEMPRE usar este padrão exato
$mensagens = [
    'required' => 'O campo :attribute é obrigatório',
    'string' => 'O campo :attribute deve ser um texto',
    'max' => 'O campo :attribute não pode ter mais que :max caracteres',
    'email' => 'O campo :attribute deve ser um e-mail válido',
    'unique' => 'Este :attribute já está cadastrado',
    'integer' => 'O campo :attribute deve ser um número inteiro',
    'in' => 'O valor selecionado para :attribute é inválido',
    'numeric' => 'O campo :attribute deve ser um número',
    'date' => 'O campo :attribute deve ser uma data válida'
];

$atributos = [
    'nome' => 'nome',
    'email' => 'e-mail',
    'status' => 'status'
];

$validated = $request->validate([
    'nome' => 'required|string|max:255',
    'email' => 'required|email|max:255|unique:[tabela]',
    'status' => 'required|in:ativo,inativo'
], $mensagens, $atributos);
```

---

## 8. Mensagens Padronizadas

### 📝 **Mensagens de Sucesso (Obrigatórias)**
```javascript
// ✅ PADRÃO CORRETO - SEMPRE USAR
'[Funcionalidade] criado com sucesso!'
'[Funcionalidade] atualizado com sucesso!'
'[Funcionalidade] excluído com sucesso!'

// ❌ NUNCA USAR
'Item salvo!'
'Registro atualizado!'
'Deletado com sucesso!'
```

### 📝 **Mensagens de Erro (Obrigatórias)**
```javascript
// ✅ PADRÃO CORRETO - SEMPRE USAR
'Erro ao criar [funcionalidade]'
'Erro ao atualizar [funcionalidade]'
'Erro ao excluir [funcionalidade]'

// ❌ NUNCA USAR
'Erro ao salvar!'
'Falha na operação!'
'Deu erro!'
```

---

## 9. Proibições Críticas

### 🚫 **O que NUNCA fazer:**
1. **Validação HTML5**: NUNCA usar `required`, `pattern`, `minlength`, etc.
2. **Confirm nativo**: NUNCA usar `confirm()` para exclusões
3. **Alert nativo**: NUNCA usar `alert()` para feedback
4. **Rotas API em api.php**: SEMPRE usar `web.php` para rotas API
5. **Mensagens genéricas**: SEMPRE usar mensagens específicas da funcionalidade
6. **Validação no frontend**: SEMPRE validar no backend Laravel
7. **Console.log em produção**: NUNCA deixar logs de debug

### ✅ **O que SEMPRE fazer:**
1. **Validação Laravel**: SEMPRE usar `$request->validate()`
2. **Modal personalizado**: SEMPRE implementar modal Bootstrap
3. **Toast notifications**: SEMPRE usar sistema de toast
4. **Logs estruturados**: SEMPRE usar `Log::info()` com contexto
5. **Mensagens personalizadas**: SEMPRE usar mensagens específicas
6. **Tratamento de erros**: SEMPRE usar try-catch
7. **Respostas JSON**: SEMPRE retornar JSON para API

---

## 10. Checklist de Implementação Universal

### 📋 **Backend (Obrigatório)**
- [ ] Controller Web criado com método index
- [ ] Controller API criado com métodos CRUD
- [ ] Model criado com fillable e casts
- [ ] Rotas Web configuradas
- [ ] Rotas API configuradas em web.php
- [ ] Validação Laravel implementada
- [ ] Mensagens personalizadas configuradas
- [ ] Atributos personalizados configurados
- [ ] Logs estruturados implementados

### 📋 **Frontend (Obrigatório)**
- [ ] View container criada
- [ ] Componente Vue registrado
- [ ] Modal obrigatório implementado
- [ ] Modal de confirmação implementado
- [ ] Toast notifications implementadas
- [ ] Validação visual implementada
- [ ] Estados de loading implementados
- [ ] Tratamento de erros implementado

### 📋 **Interface (Consultar arquivos especializados)**
- [ ] Para interfaces SEM abas: Consulte `02_padrao_crud_sem_abas.md`
- [ ] Para interfaces COM abas: Consulte `03_padrao_crud_com_abas.md`

---

## 11. Conclusão

### 🎉 **RESULTADO FINAL**

**Este documento define claramente os padrões UNIVERSAIS para CRUDs no projeto!**

**Qualquer desenvolvedor (ou IA) consegue criar CRUDs sem dúvidas sobre:**

- ✅ **Estrutura** - Como organizar arquivos e diretórios (Web + Api)
- ✅ **Controllers** - Como implementar separação Web/Api
- ✅ **Models** - Como estruturar modelos Eloquent
- ✅ **Validação** - Como validar dados corretamente (Laravel)
- ✅ **Mensagens** - Como padronizar mensagens de sucesso/erro
- ✅ **Rotas** - Como configurar rotas Web e API
- ✅ **Proibições** - O que NÃO fazer
- ✅ **Arquivos Especializados** - Onde encontrar padrões de interface

**Com este documento, qualquer novo CRUD será consistente, organizado e de alta qualidade!**

---

> **IMPORTANTE**: Este documento contém APENAS padrões universais. Para padrões específicos de interface, consulte os arquivos especializados:
> - **Interfaces SEM abas**: `02_padrao_crud_sem_abas.md`
> - **Interfaces COM abas**: `03_padrao_crud_com_abas.md` 