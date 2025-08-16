# Módulo Andamento do Projeto - Guia Mestre de Criação e Compreensão

> **DOCUMENTO MESTRE**: Este é o guia definitivo do módulo Andamento do Projeto do OrçaCidade. Serve para criar o módulo do zero e para contextualizar completamente qualquer pessoa (desenvolvedor ou IA) sobre toda a estrutura, padrões e filosofia do módulo.

---

## 1. Visão Geral e Propósito

### 🎯 **Por que este módulo existe?**

O módulo **Andamento do Projeto** foi criado para centralizar e organizar todas as informações sobre o desenvolvimento do sistema OrçaCidade. Ele resolve problemas fundamentais de gestão de projetos de software:

- **Transparência**: Todos os stakeholders têm visibilidade do progresso
- **Organização**: Documentação, relatórios e marcos em um só lugar
- **Padronização**: Interface e processos consistentes
- **Comunicação**: Ferramenta única para acompanhamento do projeto
- **Institucionalidade**: Apresentação profissional para gestores e clientes

### 🏛️ **Filosofia do Módulo**

Este módulo segue princípios fundamentais:

1. **Centralização** - Um só lugar para tudo relacionado ao andamento
2. **Dinamismo** - Conteúdo gerado automaticamente via JSON
3. **Institucionalidade** - Visual profissional e padronizado
4. **Usabilidade** - Interface intuitiva e responsiva
5. **Escalabilidade** - Estrutura que cresce com o projeto
6. **Documentação Viva** - Sempre atualizada e relevante

---

## 2. Estrutura Completa de Diretórios

### 📂 **Organização de Arquivos**

```
📁 app/Http/Controllers/AndamentoProjeto/
   └── AndamentoProjetoController.php    # Controller principal
   └── DocumentacaoController.php        # Controller da documentação

📁 resources/views/andamento-projeto/
   ├── _menu.blade.php                   # Menu de navegação (partial)
   ├── index.blade.php                   # Página inicial do módulo
   ├── escopo.blade.php                  # Página do escopo do projeto
   ├── backlog-global.blade.php          # Página do backlog (se ativo)
   ├── fases-e-sprints.blade.php         # Timeline e fases do projeto
   ├── conceitos.blade.php               # Definições e conceitos
   ├── relatorios.blade.php              # Consolidado de relatórios
   ├── relatorio-individual.blade.php    # Template para relatórios individuais
   └── documentacao/
       ├── index.blade.php               # Lista de documentação
       └── show.blade.php                # Visualização de documentação

📁 public/assets/
   ├── css/andamento-projeto.css         # CSS específico do módulo
   └── images/
       ├── contexto.png                  # Imagem do contexto (escopo)
       └── escopo.png                    # Imagem do fluxo (escopo)

📁 storage/app/
   ├── relatorios-projeto-json/          # JSONs dos relatórios
   │   ├── consolidado.json              # Métricas gerais
   │   ├── padrao-json-relatorio-andamento-projeto.json
   │   └── [modulo].json                 # Relatório de cada módulo
   └── documentacao-tecnica-json/        # JSONs da documentação
       └── [modulo].json                 # Documentação de cada módulo

📁 routes/
   └── web.php                           # Rotas do módulo (seção específica)

📁 docs/
   ├── andamento-projeto_padrao.md                    # Padrão completo
   ├── andamento-projeto-documentacao.md             # Guia de documentação
   └── andamento-projeto-padrao-criacao-do-modulo.md # Este documento
```

### 🎯 **Convenções de Nomenclatura**

- **Controllers**: `AndamentoProjetoController`, `DocumentacaoController`
- **Views**: `kebab-case` (ex: `fases-e-sprints.blade.php`)
- **Rotas**: Prefixo `andamento-projeto.` (ex: `andamento-projeto.escopo`)
- **CSS**: Classes prefixadas com módulo (ex: `.orcacidade-tab-link`)
- **JSONs**: Nome do módulo em `snake_case` (ex: `municipios.json`)

---

## 3. Padrões Visuais e UX

### 🎨 **Identidade Visual**

#### **Cores Institucionais (OBRIGATÓRIAS)**
- **Verde Principal**: `#43a047` - Destaques, ícones, números, botões primários
- **Cinza Escuro**: `#263238` - Títulos, textos importantes
- **Cinza Claro**: `#f5f5f5`, `#e0e0e0` - Fundos, bordas, respiros
- **Laranja Acento**: `#ffa726` - Botões secundários, alertas
- **❌ NUNCA use azul forte** - Quebra a identidade visual

#### **Tipografia**
- **Fonte Principal**: `'Inter', 'Segoe UI', 'Roboto', Arial, sans-serif`
- **Títulos**: Peso 800, letter-spacing 1px
- **Corpo**: Peso 400-500, line-height 1.5-1.8
- **Tamanhos**: 2.1rem (títulos), 1.13rem (subtítulos), 1rem (corpo)

#### **Layout e Componentes**
- **Cards**: Fundo branco, sombra suave, border-radius 1rem, padding 2rem
- **Botões**: Border-radius 14px, padding generoso, transições suaves
- **Espaçamento**: Margens generosas, respiro visual, alinhamento central
- **Responsividade**: Mobile-first, breakpoint em 768px

### 🔧 **Padrão do Botão "Voltar"**

**Estrutura obrigatória** em todas as páginas:

```php
<!-- Header da Página -->
<div class="page-header">
    <a href="{{ route('andamento-projeto.index') }}" class="btn-voltar">
        <i class="fas fa-arrow-left"></i>
        Voltar
    </a>
    
    <h1 class="titulo-[pagina] mb-2">
        <i class="fas fa-[icone]"></i> [Título]
    </h1>
    <p class="page-subtitle">
        [Descrição da página]
    </p>
</div>
```

**CSS obrigatório**:
```css
.btn-voltar {
    background: none;
    border: none;
    color: #666;
    position: absolute;
    top: 0;
    left: 0;
    /* ... outros estilos padrão */
}
```

---

## 4. Padrões Técnicos

### 🛣️ **Estrutura de Rotas**

**Sempre agrupadas** em `routes/web.php`:

```php
Route::prefix('andamento-projeto')->group(function () {
    Route::get('/', [AndamentoProjetoController::class, 'index'])->name('andamento-projeto.index');
    Route::get('/escopo', [AndamentoProjetoController::class, 'escopo'])->name('andamento-projeto.escopo');
    Route::get('/fases-e-sprints', [AndamentoProjetoController::class, 'fasesESprints'])->name('andamento-projeto.fases-e-sprints');
    Route::get('/conceitos', [AndamentoProjetoController::class, 'conceitos'])->name('andamento-projeto.conceitos');
    Route::get('/relatorios', [AndamentoProjetoController::class, 'relatorios'])->name('andamento-projeto.relatorios');
    Route::get('/relatorios/{modulo}', [AndamentoProjetoController::class, 'relatorioIndividual'])->name('andamento-projeto.relatorio.individual');
});

// Rotas da documentação
Route::get('/andamento-projeto/documentacao', [DocumentacaoController::class, 'index'])->name('documentacao.index');
Route::get('/andamento-projeto/documentacao/{modulo}', [DocumentacaoController::class, 'show'])->name('documentacao.show');
```

### 🎛️ **Padrão dos Controllers**

```php
class AndamentoProjetoController extends Controller
{
    public function index() {
        return view('andamento-projeto.index');
    }
    
    public function [metodo]() {
        return view('andamento-projeto.[view]');
    }
    
    public function relatorios() {
        $consolidado = json_decode(file_get_contents(storage_path('app/relatorios-projeto-json/consolidado.json')), true);
        return view('andamento-projeto.relatorios', compact('consolidado'));
    }
}
```

### 📄 **Padrão das Views**

**Estrutura obrigatória** de qualquer view do módulo:

```blade
@extends('layouts.app')
@section('title', '[Título] - Andamento do Projeto')

@push('styles')
<style>
    /* Header Styles - SEMPRE INCLUIR */
    .page-header { /* ... */ }
    .btn-voltar { /* ... */ }
    .titulo-[pagina] { /* ... */ }
    .page-subtitle { /* ... */ }
    
    /* Layout responsivo - SEMPRE INCLUIR */
    @media (max-width: 768px) { /* ... */ }
</style>
@endpush

@section('content')
<div class="container py-4" style="max-width: [800-1200px];">
    <!-- Header padrão -->
    <!-- Conteúdo específico -->
</div>
@endsection
```

---

## 5. Sistema Dinâmico de Dados

### 📊 **Relatórios via JSON**

O módulo usa um **sistema dinâmico** baseado em arquivos JSON:

- **`consolidado.json`**: Métricas gerais e lista de módulos
- **`[modulo].json`**: Dados específicos de cada módulo
- **Geração automática**: Views leem JSONs e montam HTML dinamicamente

### 📚 **Documentação via JSON**

Similar aos relatórios, mas para documentação técnica:

- **`[modulo].json`**: Documentação completa de cada módulo
- **Categorização**: Por tipo de módulo (clientes, orçamento, etc.)
- **Estrutura padronizada**: Sempre seguir o padrão definido

---

## 6. Como Criar o Módulo do Zero

### 🚀 **Passo 1: Estrutura Inicial**

1. **Criar diretórios**:
```bash
mkdir -p app/Http/Controllers/AndamentoProjeto
mkdir -p resources/views/andamento-projeto/documentacao
mkdir -p storage/app/relatorios-projeto-json
mkdir -p storage/app/documentacao-tecnica-json
```

2. **Criar arquivo CSS**:
```bash
touch public/assets/css/andamento-projeto.css
```

### 🎯 **Passo 2: Controllers Base**

Criar `app/Http/Controllers/AndamentoProjeto/AndamentoProjetoController.php`:

```php
<?php

namespace App\Http\Controllers\AndamentoProjeto;

use App\Http\Controllers\Controller;

class AndamentoProjetoController extends Controller
{
    public function index() {
        return view('andamento-projeto.index');
    }
    
    public function escopo() {
        return view('andamento-projeto.escopo');
    }
    
    // ... outros métodos
}
```

### 🛣️ **Passo 3: Rotas**

Adicionar em `routes/web.php`:

```php
Route::prefix('andamento-projeto')->group(function () {
    Route::get('/', [AndamentoProjetoController::class, 'index'])->name('andamento-projeto.index');
    // ... outras rotas
});
```

### 📄 **Passo 4: Views Base**

Criar views seguindo o padrão estabelecido (ver seção 4).

### 📊 **Passo 5: JSONs Iniciais**

Criar `storage/app/relatorios-projeto-json/consolidado.json`:

```json
{
    "titulo": "Consolidado Geral do Projeto",
    "total_modulos": 0,
    "metricas_gerais": {
        "total_controllers": 0,
        "total_services": 0,
        "total_models": 0,
        "total_componentes": 0,
        "total_pf": 0
    },
    "modulos_disponiveis": []
}
```

---

## 7. Como Entender o Módulo Existente

### 🔍 **Para IA/Assistentes**

Quando receber a instrução **"leia este documento"**, você deve entender:

1. **Arquitetura**: Estrutura de diretórios e organização
2. **Padrões Visuais**: Cores, tipografia, layout obrigatórios
3. **Padrões Técnicos**: Rotas, controllers, views, nomenclatura
4. **Sistema Dinâmico**: Como funcionam relatórios e documentação via JSON
5. **Filosofia**: Centralização, institucionalidade, usabilidade

### 📋 **Para Desenvolvedores**

1. **Analise a estrutura** de diretórios para entender a organização
2. **Examine as views** para entender os padrões visuais
3. **Estude os JSONs** para entender o sistema dinâmico
4. **Siga os padrões** estabelecidos ao fazer modificações
5. **Consulte a documentação** complementar quando necessário

---

## 8. Evolução e Manutenção

### 🔄 **Adicionando Novas Páginas**

1. Criar view seguindo padrão visual
2. Adicionar método no controller
3. Adicionar rota no grupo existente
4. Atualizar menu se necessário

### 📊 **Adicionando Novos Relatórios**

1. Criar JSON do módulo seguindo padrão
2. Atualizar `consolidado.json`
3. Testar na interface

### 📚 **Adicionando Nova Documentação**

1. Criar JSON seguindo padrão de documentação
2. Testar na interface
3. Categorizar adequadamente

---

## 9. Dependências e Requisitos

### 🔧 **Técnicos**

- Laravel 9+
- Bootstrap 5
- FontAwesome
- Vue.js 3 (para componentes interativos)

### 🎨 **Visuais**

- Fonte Inter (carregada via CDN ou local)
- Ícones FontAwesome
- Padrão de cores institucional

### 📁 **Estruturais**

- Diretório `storage/app/` com permissões de escrita
- Estrutura de views Laravel padrão
- Sistema de rotas Laravel

---

## 10. Checklist de Implementação

### ✅ **Criação Inicial**

- [ ] Estrutura de diretórios criada
- [ ] Controllers base implementados
- [ ] Rotas configuradas
- [ ] Views base criadas com padrão visual
- [ ] CSS do módulo implementado
- [ ] JSONs iniciais criados

### ✅ **Validação**

- [ ] Navegação entre páginas funcionando
- [ ] Padrão visual consistente
- [ ] Responsividade testada
- [ ] Sistema dinâmico funcionando
- [ ] Documentação atualizada

### ✅ **Finalização**

- [ ] Testes em diferentes dispositivos
- [ ] Validação com stakeholders
- [ ] Documentação completa
- [ ] Controle de versão atualizado

---

## 11. Troubleshooting Comum

### ❌ **Problemas Visuais**

- **Cores erradas**: Verificar se está usando o padrão institucional
- **Layout quebrado**: Verificar CSS e responsividade
- **Botão voltar fora do lugar**: Verificar CSS do `.btn-voltar`

### ❌ **Problemas Técnicos**

- **Rotas não funcionam**: Verificar agrupamento e nomenclatura
- **JSONs não carregam**: Verificar permissões e estrutura
- **Views não encontradas**: Verificar nomenclatura e estrutura

### ❌ **Problemas de Dados**

- **Relatórios vazios**: Verificar estrutura dos JSONs
- **Documentação não aparece**: Verificar categorização
- **Consolidado incorreto**: Verificar cálculos e totais

---

**Este documento é a referência DEFINITIVA do módulo Andamento do Projeto. Mantenha-o sempre atualizado e use-o como primeira fonte de consulta.** 