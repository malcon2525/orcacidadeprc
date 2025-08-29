# Política Geral de Acesso - Módulo Orçamento

## 📋 **Visão Geral**

Este documento define o **padrão obrigatório** para implementar política de acesso em **TODAS** as funcionalidades do módulo orçamento, baseado na implementação bem-sucedida de "Composições Próprias".

## 🎯 **Objetivos**

- ✅ **Padronizar** implementação de controle de acesso
- ✅ **Garantir** consistência entre funcionalidades
- ✅ **Evitar** reimplementação desnecessária
- ✅ **Manter** segurança e rastreabilidade

---

## 🔐 **ESTRUTURA DE ACESSO OBRIGATÓRIA**

### **🎭 Papel Base Obrigatório:**
- **TODOS** os menus do módulo orçamento são acessados **APENAS** por usuários com papel `criar_orcamentos`
- **Sem exceção:** Se o usuário não tiver este papel, não pode acessar nenhuma funcionalidade de orçamento
- **Super admin:** Tem acesso total independente de papéis

### **🔑 Permissões Específicas por Menu:**
- **Cada funcionalidade** possui uma permissão específica
- **Padrão:** `[nome_funcionalidade]_crud`
- **Exemplos:**
  - `composicao_propria_crud` → Menu "Composições Próprias"
  - `orcamento_crud` → Menu "Orçamentos"
  - `analise_custos_crud` → Menu "Análise de Custos"
  - `projeto_crud` → Menu "Projetos"

### **🏢 Vínculo com Entidade Orçamentária:**
- **Obrigatório:** Usuário deve estar vinculado a pelo menos uma entidade orçamentária
- **Verificação automática:** Sistema verifica se existe vínculo ativo
- **Sugestão automática:** Se não tiver vínculo, sistema redireciona para cadastro
- **Implementação:** Ver exemplo em "Composições Próprias"

---

## 📚 **CONCEITOS FUNDAMENTAIS**

### **🔍 O que é o "Contexto Orçamentário"?**

O **Contexto Orçamentário** é uma **configuração obrigatória** que o usuário deve definir antes de acessar qualquer funcionalidade do módulo orçamento. É como um "ambiente de trabalho" personalizado para cada usuário.

### **⚙️ O que é configurado no Contexto:**

1. **🏢 Entidade Orçamentária:**
   - É a organização (prefeitura, secretaria, departamento) para qual o usuário fará orçamentos
   - Exemplo: "Prefeitura Municipal de Paranaguá", "Secretaria de Obras"
   - **Por que é importante:** Evita erro humano de misturar orçamentos de entidades diferentes

2. **📅 Data Base SINAPI:**
   - É a versão da tabela SINAPI (Sistema Nacional de Pesquisa de Custos e Índices da Construção Civil) que será usada
   - Exemplo: Janeiro/2024, Março/2024
   - **Por que é importante:** Garante que todos os cálculos usem a mesma base de custos

3. **📅 Data Base DERPR:**
   - É a versão da tabela DERPR (Departamento de Estradas de Rodagem do Paraná) que será usada
   - Exemplo: Janeiro/2024, Março/2024
   - **Por que é importante:** Garante consistência nos custos de infraestrutura rodoviária

### **🔗 Como funciona na prática:**

1. **Usuário acessa** "Configuração Orçamento"
2. **Escolhe** sua entidade orçamentária
3. **Seleciona** as datas base das tabelas
4. **Sistema salva** essas configurações
5. **Todas as funcionalidades** de orçamento usam essas configurações automaticamente

### **⚠️ Por que é OBRIGATÓRIO:**

- **Evita inconsistências** entre orçamentos
- **Previne erros** de cálculo por datas diferentes
- **Garante rastreabilidade** de qual base foi usada
- **Centraliza configurações** do usuário
- **Padroniza** o processo de orçamentação

---

## 🔒 **1. ESTRUTURA DE CONTROLE DE ACESSO OBRIGATÓRIA**

### **1.1 Controller Web - Método `checkAccess()`**

```php
/**
 * Verifica se o usuário tem acesso à funcionalidade
 */
private function checkAccess()
{
    $user = Auth::user();
    
    if (!$user) {
        abort(401, 'Usuário não autenticado.');
    }
    
    // 1. Super admin tem acesso total
    if ($user->isSuperAdmin()) {
        return true;
    }
    
    // 2. Verificar papel criar_orcamentos
    if (!$user->hasRole('criar_orcamentos')) {
        abort(403, 'Acesso negado. É necessário ter o papel "criar_orcamentos".');
    }
    
    // 3. Verificar permissão específica da funcionalidade
    if (!$user->hasPermission('[nome_funcionalidade]_crud')) {
        abort(403, 'Acesso negado. É necessário ter a permissão "[nome_funcionalidade]_crud".');
    }
    
    // 4. Verificar vínculo com entidade orçamentária
    $temVinculos = DB::table('user_entidades_orcamentarias')
        ->where('user_id', $user->id)
        ->where('ativo', true)
        ->exists();
    
    if (!$temVinculos) {
        abort(403, 'Acesso negado. Usuário não possui vínculos ativos com entidades orçamentárias.');
    }
    
    return true;
}
```

### **1.2 Controller Web - Método `verificarContexto()`**

```php
/**
 * Verifica contexto orçamentário obrigatório
 */
private function verificarContexto()
{
    $resultado = OrcamentoContextHelper::verificarContextoParaModuloOrcamento();
    
    if (is_string($resultado)) {
        if (filter_var($resultado, FILTER_VALIDATE_URL)) {
            // É uma URL de redirecionamento
            return redirect($resultado)->with('warning', 'É necessário configurar o contexto orçamentário antes de acessar esta funcionalidade.');
        } else {
            // É uma mensagem de erro
            abort(403, $resultado);
        }
    }
    
    return null; // Tudo OK
}
```

### **1.3 Controller Web - Método `index()`**

```php
public function index()
{
    $this->checkAccess();
    
    // Verificar contexto orçamentário
    $redirectContexto = $this->verificarContexto();
    if ($redirectContexto) {
        return $redirectContexto;
    }
    
    // Carregar contexto na sessão se necessário
    if (OrcamentoContextHelper::temContextoDefinido() && !OrcamentoContextHelper::getContextoDaSessao()) {
        OrcamentoContextHelper::carregarContextoNaSessao();
    }
    
    // Permissões do usuário (abordagem binária)
    $permissoes = [
        'crud' => true, // Se chegou até aqui, tem acesso completo
        'consultar' => true,
    ];
    
    return view('[modulo].[funcionalidade].index', compact('permissoes'));
}
```

---

## 🔐 **2. CONTROLLER API - CONTROLE DE ACESSO**

### **2.1 Controller API - Método `checkAccess()`**

```php
/**
 * Verifica se o usuário tem acesso à funcionalidade
 */
private function checkAccess()
{
    $user = Auth::user();
    
    if (!$user) {
        abort(401, 'Usuário não autenticado.');
    }
    
    // 1. Super admin tem acesso total
    if ($user->isSuperAdmin()) {
        return true;
    }
    
    // 2. Verificar papel criar_orcamentos
    if (!$user->hasRole('criar_orcamentos')) {
        abort(403, 'Acesso negado. É necessário ter o papel "criar_orcamentos".');
    }
    
    // 3. Verificar permissão específica da funcionalidade
    if (!$user->hasPermission('[nome_funcionalidade]_crud')) {
        abort(403, 'Acesso negado. É necessário ter a permissão "[nome_funcionalidade]_crud".');
    }
    
    // 4. Verificar vínculo com entidade orçamentária
    $temVinculos = DB::table('user_entidades_orcamentarias')
        ->where('user_id', $user->id)
        ->where('ativo', true)
        ->exists();
    
    if (!$temVinculos) {
        abort(403, 'Acesso negado. Usuário não possui vínculos ativos com entidades orçamentárias.');
    }
    
    // 5. Verificar contexto orçamentário
    if (!\App\Helpers\OrcamentoContextHelper::temContextoDefinido()) {
        abort(403, 'Acesso negado. É necessário configurar o contexto orçamentário antes de realizar operações.');
    }
    
    return true;
}
```

### **2.2 Controller API - Captura de Contexto**

```php
/**
 * Captura as datas do contexto orçamentário do usuário
 */
private function capturarDatasContexto(): array
{
    $user = Auth::user();
    $contexto = \App\Models\Orcamento\UserOrcamentoContext::getContextoUsuario($user->id);
    
    if (!$contexto) {
        throw new \Exception('Contexto orçamentário não encontrado para o usuário.');
    }
    
    return [
        'data_base_sinapi' => $contexto->data_base_sinapi->format('Y-m-d'),
        'data_base_derpr' => $contexto->data_base_derpr->format('Y-m-d')
    ];
}
```

---

## 🎨 **3. LAYOUT PRINCIPAL - MENU ORÇAMENTO**

### **3.1 Estrutura do Menu Orçamento**

```blade
<!-- ORÇAMENTO -->
@if(Auth::user()->hasRole('super') || Auth::user()->hasRole('criar_orcamentos'))
<div class="menu-group">
    <div class="menu-header">
        <span>ORÇAMENTO</span>
    </div>
    <div class="menu-items">
        <!-- Configuração Orçamento - Sempre visível para criar_orcamentos -->
        @if(Auth::user()->hasRole('super') || Auth::user()->hasRole('criar_orcamentos'))
        <a href="{{ route('orcamento.configuracao-orcamento.index') }}" class="menu-link {{ request()->routeIs('orcamento.configuracao-orcamento.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Configuração Orçamento</span>
        </a>
        @endif
        
        <!-- Funcionalidades específicas - Com permissão específica -->
        @if(Auth::user()->hasRole('super') || (Auth::user()->hasRole('criar_orcamentos') && Auth::user()->hasPermission('[nome_funcionalidade]_crud')))
        <a href="{{ route('orcamento.[nome-funcionalidade].index') }}" class="menu-link {{ request()->routeIs('orcamento.[nome-funcionalidade].*') ? 'active' : '' }}">
            <i class="fas fa-[icon]"></i>
            <span>[Nome da Funcionalidade]</span>
        </a>
        @endif
    </div>
</div>
@endif
```

---

## 🔧 **4. IMPLEMENTAÇÃO DE CONSULTAS SINAPI/DERPR**

### **4.1 Controller de Consulta - Controle de Acesso**

```php
/**
 * Verifica se o usuário tem acesso à consulta
 */
private function checkAccess()
{
    $user = Auth::user();
    if ($user->hasRole('super') || $user->hasRole('consultar_tabela_[tipo]') || $user->hasRole('criar_orcamentos')) {
        return true;
    }
    abort(403, 'Acesso negado. Papel insuficiente.');
}
```

### **4.2 Controller de Consulta - Uso do Contexto**

```php
public function zoomServicos(Request $request)
{
    $this->checkAccess();
    
    // Obter data base do contexto do usuário
    $user = Auth::user();
    $contexto = \App\Models\Orcamento\UserOrcamentoContext::getContextoUsuario($user->id);
    
    if ($contexto) {
        $dataBaseUsada = $contexto->data_base_sinapi; // ou data_base_derpr
    } else {
        // Fallback para última data base disponível
        $dataBaseUsada = DB::table('datas_bases_sinapi')->max('data_base'); // ou tabela DERPR
    }
    
    // ... lógica de consulta usando $dataBaseUsada ...
    
    // Incluir metadados na resposta
    $response = $servicos->toArray();
    $response['meta'] = array_merge($response['meta'] ?? [], [
        'data_base_utilizada' => $dataBaseUsada,
        'data_base_formatada' => $dataBaseUsada ? \Carbon\Carbon::parse($dataBaseUsada)->format('m/Y') : null,
        'desoneracao' => $request->get('desoneracao', 'sem'),
        'fonte' => 'SINAPI' // ou 'DERPR'
    ]);
    
    return response()->json($response);
}
```

---

## 📋 **5. CHECKLIST DE IMPLEMENTAÇÃO OBRIGATÓRIA**

### **✅ Controller Web:**
- [ ] Método `checkAccess()` com verificação de papel e permissão
- [ ] Método `verificarContexto()` usando `OrcamentoContextHelper`
- [ ] Chamada de ambos os métodos no `index()`
- [ ] Carregamento do contexto na sessão

### **✅ Controller API:**
- [ ] Método `checkAccess()` com verificação completa
- [ ] Verificação de contexto orçamentário
- [ ] Captura de datas do contexto quando necessário
- [ ] Uso de datas do contexto em consultas SINAPI/DERPR

### **✅ Layout Principal:**
- [ ] Menu "ORÇAMENTO" com verificação de papel `criar_orcamentos`
- [ ] Submenu com verificação de permissão específica
- [ ] Estrutura consistente para todas as funcionalidades

### **✅ Consultas SINAPI/DERPR:**
- [ ] Controle de acesso adequado
- [ ] Uso de datas do contexto do usuário
- [ ] Inclusão de metadados na resposta
- [ ] Fallback para última data disponível

---

## ⚠️ **6. PONTOS DE ATENÇÃO**

### **6.1 Ordem das Verificações:**
1. **Autenticação** (usuário logado)
2. **Super Admin** (acesso total)
3. **Papel** (`criar_orcamentos`)
4. **Permissão** (`[funcionalidade]_crud`)
5. **Vínculo** (entidade orçamentária)
6. **Contexto** (configuração definida)

### **6.2 Nomenclatura de Permissões:**
- **Padrão:** `[nome_funcionalidade]_crud`
- **Exemplos:** `composicao_propria_crud`, `orcamento_crud`, `analise_custos_crud`

### **6.3 Tratamento de Erros:**
- **401:** Usuário não autenticado
- **403:** Acesso negado (com mensagem específica)
- **Redirect:** Para configuração de contexto quando necessário

### **6.4 Verificação de Vínculo com Entidade:**
- **Obrigatório:** Usuário deve ter vínculo ativo com entidade orçamentária
- **Implementação:** Ver exemplo em "Composições Próprias"
- **Sugestão automática:** Sistema deve redirecionar para cadastro se não tiver vínculo

---

## 🚀 **7. EXEMPLO DE IMPLEMENTAÇÃO RÁPIDA**

### **7.1 Para Nova Funcionalidade "Análise de Custos":**

1. **Substituir** `[nome_funcionalidade]` por `analise_custos`
2. **Substituir** `[nome-funcionalidade]` por `analise-custos`
3. **Substituir** `[icon]` por `chart-line`
4. **Substituir** `[Nome da Funcionalidade]` por `Análise de Custos`
5. **Criar** permissão `analise_custos_crud` no sistema
6. **Implementar** métodos padrão nos controllers

### **7.2 Estrutura de Arquivos Necessária:**

```
app/Http/Controllers/Web/Orcamento/AnaliseCustos/AnaliseCustosController.php
app/Http/Controllers/Api/Orcamento/AnaliseCustos/AnaliseCustosController.php
resources/views/orcamento/analise_custos/index.blade.php
resources/js/components/orcamento/analise_custos/ListaAnaliseCustos.vue
routes/web.php (adicionar rotas)
```

---

## 🔄 **8. FLUXO COMPLETO DE FUNCIONAMENTO**

### **8.1 Fluxo do Usuário:**
1. **Usuário faz login** no sistema
2. **Sistema verifica** se tem papel `criar_orcamentos`
3. **Sistema verifica** se tem vínculo com entidade orçamentária
4. **Usuário acessa** "Configuração Orçamento"
5. **Usuário define** entidade e datas base
6. **Sistema salva** contexto orçamentário
7. **Usuário pode acessar** funcionalidades de orçamento

### **8.2 Fluxo de Verificação:**
1. **Controller recebe** requisição
2. **Verifica acesso** (papel + permissão + vínculo)
3. **Verifica contexto** orçamentário
4. **Carrega contexto** na sessão
5. **Executa operação** usando contexto
6. **Retorna resultado** com metadados

### **8.3 Fluxo de Tratamento de Erros:**
1. **Sem papel:** Redireciona com mensagem de acesso negado
2. **Sem permissão:** Redireciona com mensagem de permissão insuficiente
3. **Sem vínculo:** Sugere cadastro de vínculo com entidade
4. **Sem contexto:** Redireciona para "Configuração Orçamento"

---

## 📚 **9. REFERÊNCIAS**

- **Helper:** `app/Helpers/OrcamentoContextHelper.php`
- **Model:** `app/Models/Orcamento/UserOrcamentoContext.php`
- **Exemplo Web:** `app/Http/Controllers/Web/Orcamento/ComposicaoPropria/ComposicaoPropriaController.php`
- **Exemplo API:** `app/Http/Controllers/Api/Orcamento/ComposicaoPropria/ComposicaoPropriaController.php`
- **Layout:** `resources/views/layouts/app.blade.php`

---

## 🎯 **10. RESULTADO ESPERADO**

Após seguir estas diretrizes, **TODAS** as funcionalidades do módulo orçamento terão:

- ✅ **Controle de acesso consistente**
- ✅ **Verificação de contexto obrigatória**
- ✅ **Uso de datas base padronizado**
- ✅ **Rastreabilidade completa**
- ✅ **Segurança uniforme**
- ✅ **Experiência do usuário consistente**

---

## 💡 **11. BENEFÍCIOS DA IMPLEMENTAÇÃO**

### **Para o Desenvolvedor:**
- **Padrão claro** e documentado
- **Reutilização** de código existente
- **Consistência** entre funcionalidades
- **Manutenibilidade** simplificada

### **Para o Usuário:**
- **Experiência uniforme** em todas as funcionalidades
- **Configuração centralizada** do contexto
- **Prevenção de erros** por inconsistências
- **Rastreabilidade** completa das operações

### **Para o Sistema:**
- **Segurança robusta** e padronizada
- **Controle de acesso** granular
- **Auditoria** completa das operações
- **Escalabilidade** para novas funcionalidades

---

## 🔍 **12. EXEMPLOS PRÁTICOS DE IMPLEMENTAÇÃO**

### **12.1 Exemplo de Verificação de Vínculo (Composições Próprias):**

```php
// Verificação de vínculo com entidade orçamentária
$temVinculos = DB::table('user_entidades_orcamentarias')
    ->where('user_id', $user->id)
    ->where('ativo', true)
    ->exists();

if (!$temVinculos) {
    abort(403, 'Acesso negado. Usuário não possui vínculos ativos com entidades orçamentárias.');
}
```

### **12.2 Exemplo de Verificação de Contexto:**

```php
// Verificar se usuário tem contexto definido
if (!\App\Helpers\OrcamentoContextHelper::temContextoDefinido()) {
    abort(403, 'Acesso negado. É necessário configurar o contexto orçamentário antes de realizar operações.');
}
```

### **12.3 Exemplo de Captura de Datas do Contexto:**

```php
// Capturar datas do contexto para uso em consultas
$contexto = \App\Models\Orcamento\UserOrcamentoContext::getContextoUsuario($user->id);
$dataBaseSinapi = $contexto->data_base_sinapi->format('Y-m-d');
$dataBaseDerpr = $contexto->data_base_derpr->format('Y-m-d');
```

---

## 🚨 **13. PROBLEMAS COMUNS E SOLUÇÕES**

### **13.1 Usuário sem papel `criar_orcamentos`:**
- **Problema:** Usuário não consegue acessar módulo orçamento
- **Solução:** Verificar se papel foi atribuído corretamente no sistema

### **13.2 Usuário sem permissão específica:**
- **Problema:** Usuário vê menu mas não consegue acessar funcionalidade
- **Solução:** Verificar se permissão `[funcionalidade]_crud` foi criada e atribuída

### **13.3 Usuário sem vínculo com entidade:**
- **Problema:** Sistema bloqueia acesso mesmo com papel e permissão
- **Solução:** Verificar se usuário foi vinculado a uma entidade orçamentária

### **13.4 Usuário sem contexto definido:**
- **Problema:** Sistema redireciona para configuração
- **Solução:** Usuário deve acessar "Configuração Orçamento" e definir contexto

---

> **IMPORTANTE:** Este padrão é **OBRIGATÓRIO** para todas as funcionalidades do módulo orçamento. Não implementar de forma diferente ou incompleta.

> **DICA:** Sempre consulte este documento antes de implementar uma nova funcionalidade no módulo orçamento para garantir consistência total.

> **LEMBRE-SE:** 
> - Papel `criar_orcamentos` é **OBRIGATÓRIO** para todos os menus
> - Cada funcionalidade precisa de permissão específica
> - Vínculo com entidade orçamentária é **OBRIGATÓRIO**
> - Contexto orçamentário deve ser **SEMPRE** verificado
