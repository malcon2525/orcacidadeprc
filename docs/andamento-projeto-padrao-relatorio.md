# Sistema de Relatórios - Guia Operacional Completo

> **MANUAL DE OPERAÇÃO**: Este documento orienta completamente o processo de criação e atualização de relatórios no módulo Andamento do Projeto. Seguindo este guia, qualquer IA pode gerar relatórios precisos e atualizados automaticamente.

---

## 1. Visão Geral do Sistema de Relatórios

### 🎯 **Como Funciona**

O sistema de relatórios do módulo Andamento do Projeto é **dinâmico** e baseado em arquivos JSON:

- **Entrada**: Documentação técnica dos módulos (`/docs/técnico/`)
- **Processamento**: Extração de métricas e cálculo de pontos de função
- **Saída**: JSONs estruturados que alimentam as views automaticamente
- **Visualização**: Interface web que lê os JSONs e monta HTML dinamicamente

### 📊 **Tipos de Arquivos JSON**

1. **`consolidado.json`** - Métricas gerais do projeto inteiro
2. **`[modulo].json`** - Relatório específico de cada módulo
3. **`padrao-json-relatorio-andamento-projeto.json`** - Template obrigatório

### 🔄 **Fluxo de Atualização**

```
Documentação Técnica → Extração de Dados → Geração JSON → Atualização Consolidado → Interface Web
```

---

## 2. Pré-requisitos e Contextualização

### 📚 **Leitura Obrigatória Antes de Começar**

**SEMPRE** seguir esta sequência:

1. **Ler `andamento-projeto-padrao-criacao-do-modulo.md`**
   - Entender estrutura geral do módulo
   - Conhecer padrões visuais e técnicos
   - Compreender sistema dinâmico de dados

2. **Ler este documento (`andamento-projeto-padrao-relatorio.md`)**
   - Entender processo específico de relatórios
   - Conhecer estrutura obrigatória dos JSONs
   - Compreender cálculos de pontos de função

### 🎯 **Informações Necessárias do Dev**

Quando o desenvolvedor solicitar criação/atualização de relatório, ele deve informar:

- **Nome do módulo** (ex: "municipios", "cotacoes", "bdi")
- **Ação desejada** ("criar" ou "atualizar")

**IMPORTANTE**: O nome do módulo deve corresponder exatamente ao arquivo em `/docs/técnico/[modulo].md`

---

## 3. Estrutura Obrigatória dos JSONs

### 📋 **Template Padrão (NUNCA ALTERAR)**

**SEMPRE** usar como base o arquivo: `storage/app/relatorios-projeto-json/padrao-json-relatorio-andamento-projeto.json`

```json
{
  "titulo": "[Título do Relatório]",
  "nome_modulo": "[Nome do Módulo]",
  "objetivo": "[Descrição clara do objetivo do módulo]",
  "caracteristicas_quantitativas": {
    "rotas_web": 0,
    "rotas_api": 0,
    "controllers": 0,
    "componentes": 0,
    "tabelas_banco": 0,
    "services": 0,
    "models": 0
  },
  "detalhamento_pontos_funcao": [
    {
      "tipo_funcao": "Entradas Externas (EE)",
      "quantidade": 0,
      "pf_por_funcao": 0,
      "total_pf": 0,
      "justificativa": ""
    }
  ],
  "total_pf": 0,
  "observacao_pf": "",
  "principais_funcionalidades": [
    "[Funcionalidade 1]",
    "[Funcionalidade 2]"
  ]
}
```

### ⚠️ **Regras OBRIGATÓRIAS**

- **NUNCA** inventar, alterar ou omitir chaves
- **SEMPRE** usar os nomes exatos do padrão
- **SEMPRE** manter a estrutura de arrays e objetos
- **TODOS** os campos devem estar presentes (mesmo que vazios)

### 🚨 **ERROS CRÍTICOS A EVITAR**

#### **1. Estrutura de Relatórios vs Documentação**
- **❌ ERRADO**: Usar estrutura de documentação técnica para relatórios
- **✅ CORRETO**: Usar estrutura específica de relatórios

#### **2. Campos Obrigatórios de Relatórios**
- **❌ ERRADO**: `"modulo"`, `"categoria"`, `"rotas"`, `"arquivos"`
- **✅ CORRETO**: `"nome_modulo"`, `"objetivo"`, `"caracteristicas_quantitativas"`, `"detalhamento_pontos_funcao"`

#### **3. Estrutura de Características Quantitativas**
- **❌ ERRADO**: 
  ```json
  "caracteristicas_quantitativas": {
    "rotas": 10,
    "controllers": 5
  }
  ```
- **✅ CORRETO**:
  ```json
  "caracteristicas_quantitativas": {
    "rotas_web": 6,
    "rotas_api": 4,
    "controllers": 5,
    "componentes": 2,
    "tabelas_banco": 3,
    "services": 1,
    "models": 4,
    "views_blade": 0,
    "migrations": 3
  }
  ```

#### **4. Estrutura de Detalhamento PF**
- **❌ ERRADO**: 
  ```json
  "detalhamento_pontos_funcao": {
    "entradas": 10,
    "saidas": 15
  }
  ```
- **✅ CORRETO**:
  ```json
  "detalhamento_pontos_funcao": [
    {
      "tipo_funcao": "Entradas Externas (EE)",
      "quantidade": 2,
      "pf_por_funcao": 4,
      "total_pf": 8,
      "justificativa": "Formulários de cadastro e edição"
    }
  ]
  ```

#### **5. Campos de Funcionalidades**
- **❌ ERRADO**: `"funcionalidades"` (objeto complexo)
- **✅ CORRETO**: `"principais_funcionalidades"` (array de strings)

#### **6. Campos de Observações**
- **❌ ERRADO**: `"observacoes"` (array)
- **✅ CORRETO**: `"observacao_pf"` (string única)

---

## 4. Processo Completo de Criação/Atualização

### 🔍 **Passo 1: Localizar e Ler Documentação**

1. **Buscar arquivo**: `/docs/técnico/[nome_modulo].md`
2. **Se não existir**: Informar ao dev e solicitar criação da documentação
3. **Ler completamente** a documentação técnica
4. **Identificar seções relevantes**:
   - Rotas/API
   - Arquivos envolvidos
   - Tabelas do banco
   - Funcionalidades
   - Regras de negócio

### 📊 **Passo 2: Buscar Relatório Existente**

1. **Verificar** se existe `/docs/relatorios/[nome_modulo]_relatorio.md`
2. **Se existir**: Ler para obter informações de pontos de função
3. **Se não existir**: Calcular pontos de função baseado na documentação técnica

### ⚙️ **Passo 3: Extrair Métricas Quantitativas**

**Da documentação técnica, extrair:**

- **rotas_web**: Contar rotas em `routes/web.php`
- **rotas_api**: Contar rotas em `routes/api.php`
- **controllers**: Contar arquivos de controller
- **componentes**: Contar componentes Vue.js
- **tabelas_banco**: Contar tabelas principais do módulo
- **services**: Contar arquivos de service
- **models**: Contar arquivos de model

### 🧮 **Passo 4: Calcular Pontos de Função**

#### **Tipos de Função (usar exatamente estes nomes):**

1. **"Entradas Externas (EE)"** - Formulários, inputs de dados
2. **"Saídas Externas (SE)"** - Relatórios, exportações
3. **"Consultas Externas (CE)"** - Consultas, filtros, pesquisas
4. **"Arquivos Lógicos Internos (ALI)"** - Tabelas principais do módulo
5. **"Arquivos de Interface Externa (AIE)"** - Integrações, APIs externas

#### **Valores Padrão de PF:**

- **EE (Entradas)**: 4 PF cada
- **SE (Saídas)**: 5 PF cada  
- **CE (Consultas)**: 4 PF cada
- **ALI (Tabelas)**: 10 PF cada
- **AIE (Integrações)**: 7 PF cada

#### **Como Calcular:**

1. **Identificar** cada funcionalidade do módulo
2. **Classificar** por tipo de função
3. **Contar** quantidade de cada tipo
4. **Multiplicar** pela pontuação padrão
5. **Somar** tudo para obter total_pf

### 📝 **Passo 5: Preencher JSON**

```json
{
  "titulo": "Relatório de [Nome do Módulo]",
  "nome_modulo": "[nome_exato_do_modulo]",
  "objetivo": "[extrair da documentação técnica]",
  "caracteristicas_quantitativas": {
    // Preencher com dados extraídos
  },
  "detalhamento_pontos_funcao": [
    {
      "tipo_funcao": "Entradas Externas (EE)",
      "quantidade": 5,
      "pf_por_funcao": 4,
      "total_pf": 20,
      "justificativa": "Formulários de cadastro: municipal, entidade, etc."
    }
    // Repetir para cada tipo de função
  ],
  "total_pf": 85, // Soma de todos os total_pf acima
  "observacao_pf": "Cálculo baseado na documentação técnica...",
  "principais_funcionalidades": [
    // Extrair da documentação
  ]
}
```

### 💾 **Passo 6: Salvar JSON do Módulo**

1. **Validar JSON** antes de salvar
2. **Salvar** em: `storage/app/relatorios-projeto-json/[nome_modulo].json`
3. **Verificar** se todas as chaves estão presentes

#### **6.1 Validação Crítica Antes de Salvar**

**SEMPRE verificar estas estruturas críticas:**

```json
// ✅ ESTRUTURA CORRETA DE RELATÓRIO
{
  "titulo": "Relatório de Andamento - Módulo de [Nome]",
  "nome_modulo": "[Nome do Módulo]",  // ← "nome_modulo" (não "modulo")
  "objetivo": "[Descrição do objetivo]",
  "caracteristicas_quantitativas": {  // ← Estrutura específica
    "rotas_web": 6,
    "rotas_api": 4,
    "controllers": 5,
    "componentes": 2,
    "tabelas_banco": 3,
    "services": 1,
    "models": 4,
    "views_blade": 0,
    "migrations": 3
  },
  "detalhamento_pontos_funcao": [  // ← Array de objetos
    {
      "tipo_funcao": "Entradas Externas (EE)",
      "quantidade": 2,
      "pf_por_funcao": 4,
      "total_pf": 8,
      "justificativa": "Descrição da justificativa"
    }
  ],
  "total_pf": 54,  // ← Soma dos total_pf acima
  "observacao_pf": "Observação sobre complexidade",  // ← String única
  "principais_funcionalidades": [  // ← Array de strings
    "Funcionalidade 1",
    "Funcionalidade 2"
  ]
}
```

### 🔄 **Passo 7: Atualizar Consolidado**

1. **Ler** `storage/app/relatorios-projeto-json/consolidado.json`
2. **Atualizar métricas gerais**:
   - Incrementar `total_modulos`
   - Somar valores em `metricas_gerais`
   - Adicionar módulo em `modulos_disponiveis`

#### **Estrutura do Consolidado:**

```json
{
  "titulo": "Consolidado Geral do Projeto",
  "total_modulos": 5,
  "metricas_gerais": {
    "total_controllers": 15,
    "total_services": 8,
    "total_models": 12,
    "total_componentes": 25,
    "total_pf": 450
  },
  "modulos_disponiveis": [
    {
      "nome": "Municípios",
      "arquivo": "municipios.json"
    }
  ]
}
```

### ✅ **Passo 8: Validação Final**

1. **Verificar** se JSON é válido
2. **Testar** se página de relatórios carrega
3. **Verificar** se botão do módulo aparece
4. **Testar** relatório individual do módulo
5. **Confirmar** que consolidado está correto

---

## 5. Comandos e Ferramentas

### 🔧 **Validação de JSON**

```bash
# Validar JSON localmente (se disponível)
python -m json.tool storage/app/relatorios-projeto-json/municipios.json

# Ou usar ferramenta online
# https://jsonlint.com/
```

### 📂 **Estrutura de Arquivos para Verificar**

```bash
# Documentação técnica
ls docs/técnico/

# Relatórios existentes  
ls docs/relatorios/

# JSONs de relatórios
ls storage/app/relatorios-projeto-json/

# Controllers para contar
ls app/Http/Controllers/

# Models para contar
ls app/Models/

# Componentes Vue para contar
find resources/js/components -name "*.vue"
```

### 🌐 **URLs para Testar**

- **Consolidado**: `/andamento-projeto/relatorios`
- **Individual**: `/andamento-projeto/relatorios/[nome_modulo]`

---

## 6. Tratamento de Erros e Casos Especiais

### ❌ **Documentação Não Existe**

```
SE docs/técnico/[modulo].md NÃO EXISTIR:
1. Informar ao dev: "Documentação técnica não encontrada"
2. Solicitar criação da documentação primeiro
3. NÃO prosseguir sem documentação
```

### ❌ **Módulo Já Existe**

```
SE [modulo].json JÁ EXISTIR:
1. Perguntar ao dev se quer atualizar
2. Se SIM: sobrescrever e atualizar consolidado
3. Se NÃO: cancelar operação
```

### ❌ **Consolidado Corrompido**

```
SE consolidado.json ESTIVER INVÁLIDO:
1. Fazer backup do arquivo atual
2. Recriar do zero baseado nos módulos existentes
3. Recalcular todas as métricas
```

### ⚠️ **Módulo Sem Pontos de Função**

```
SE NÃO CONSEGUIR CALCULAR PF:
1. Usar valores mínimos baseados na estrutura
2. Adicionar observação explicativa
3. Solicitar revisão posterior
```

---

## 7. Checklist de Validação

### ✅ **Antes de Gerar JSON**

- [ ] Documentação técnica lida completamente
- [ ] Métricas quantitativas extraídas
- [ ] Pontos de função calculados
- [ ] Funcionalidades principais identificadas
- [ ] Template padrão consultado
- [ ] **VERIFICAR**: Estrutura é de relatório (não documentação)
- [ ] **VERIFICAR**: Campos obrigatórios de relatório identificados
- [ ] **VERIFICAR**: Características quantitativas estruturadas corretamente
- [ ] **VERIFICAR**: Detalhamento PF é array de objetos
- [ ] **VERIFICAR**: Funcionalidades são array de strings

### ✅ **Após Gerar JSON**

- [ ] JSON válido (sem erros de sintaxe)
- [ ] Todas as chaves obrigatórias presentes
- [ ] Valores numéricos consistentes
- [ ] Total de PF calculado corretamente
- [ ] Funcionalidades descritas claramente
- [ ] **VALIDAR**: `"nome_modulo"` presente (não `"modulo"`)
- [ ] **VALIDAR**: `"caracteristicas_quantitativas"` com campos corretos
- [ ] **VALIDAR**: `"detalhamento_pontos_funcao"` é array de objetos
- [ ] **VALIDAR**: `"principais_funcionalidades"` é array de strings
- [ ] **VALIDAR**: `"observacao_pf"` é string única
- [ ] **VALIDAR**: `"total_pf"` soma corretamente os valores

### ✅ **Após Atualizar Consolidado**

- [ ] Total de módulos correto
- [ ] Métricas gerais atualizadas
- [ ] Novo módulo na lista disponível
- [ ] JSON consolidado válido

### ✅ **Teste Final**

- [ ] Página de relatórios carrega sem erro
- [ ] Botão do novo módulo aparece
- [ ] Relatório individual abre corretamente
- [ ] Dados são exibidos corretamente
- [ ] Layout está consistente

---

## 8. Exemplos Práticos

### 📋 **Exemplo de Solicitação do Dev**

```
Dev: "Criar relatório do módulo municipios"
```

### 🔍 **Processo de Execução**

1. **Buscar**: `docs/técnico/municipios.md`
2. **Ler**: Documentação completa
3. **Extrair**: 
   - 3 rotas web
   - 1 controller
   - 2 models
   - 1 tabela principal
   - 5 funcionalidades
4. **Calcular PF**:
   - 3 EE × 4 = 12 PF
   - 2 CE × 4 = 8 PF  
   - 1 ALI × 10 = 10 PF
   - Total: 30 PF
5. **Gerar**: `municipios.json`
6. **Atualizar**: `consolidado.json`
7. **Testar**: Interface web

### 📊 **Exemplo de JSON Resultante**

```json
{
  "titulo": "Relatório de Municípios",
  "nome_modulo": "municipios",
  "objetivo": "Gerenciar cadastro de municípios com importação em massa",
  "caracteristicas_quantitativas": {
    "rotas_web": 3,
    "rotas_api": 0,
    "controllers": 1,
    "componentes": 2,
    "tabelas_banco": 1,
    "services": 0,
    "models": 2
  },
  "detalhamento_pontos_funcao": [
    {
      "tipo_funcao": "Entradas Externas (EE)",
      "quantidade": 3,
      "pf_por_funcao": 4,
      "total_pf": 12,
      "justificativa": "Formulários de cadastro, edição e importação"
    },
    {
      "tipo_funcao": "Consultas Externas (CE)",
      "quantidade": 2,
      "pf_por_funcao": 4,
      "total_pf": 8,
      "justificativa": "Listagem com filtros e busca"
    },
    {
      "tipo_funcao": "Arquivos Lógicos Internos (ALI)",
      "quantidade": 1,
      "pf_por_funcao": 10,
      "total_pf": 10,
      "justificativa": "Tabela municipios"
    }
  ],
  "total_pf": 30,
  "observacao_pf": "Cálculo baseado na documentação técnica e estrutura atual do módulo",
  "principais_funcionalidades": [
    "Cadastro de municípios",
    "Listagem com filtros",
    "Importação em massa",
    "Integração com IBGE",
    "Validação de dados"
  ]
}
```

---

## 9. Troubleshooting Comum

### 🔧 **Problema**: JSON inválido
**Solução**: Validar em jsonlint.com e corrigir sintaxe

### 🔧 **Problema**: Página não carrega
**Solução**: Verificar permissões do arquivo e estrutura JSON

### 🔧 **Problema**: Botão não aparece
**Solução**: Verificar se módulo está em `modulos_disponiveis` do consolidado

### 🔧 **Problema**: Métricas incorretas
**Solução**: Revisar cálculos e recalcular consolidado

### 🔧 **Problema**: PF muito baixo/alto
**Solução**: Revisar classificação das funcionalidades e valores padrão

### 🔧 **Problema**: "Undefined array key 'objetivo'"
**Solução**: Verificar se JSON usa estrutura de relatório (não documentação)

### 🔧 **Problema**: "Undefined array key 'nome_modulo'"
**Solução**: Verificar se usa `"nome_modulo"` (não `"modulo"`)

### 🔧 **Problema**: Características quantitativas não aparecem
**Solução**: Verificar se estrutura é `"caracteristicas_quantitativas"` com campos corretos

### 🔧 **Problema**: Detalhamento PF não aparece
**Solução**: Verificar se `"detalhamento_pontos_funcao"` é array de objetos

### 🔧 **Problema**: Funcionalidades não aparecem
**Solução**: Verificar se usa `"principais_funcionalidades"` (array de strings)

---

## 10. Manutenção e Evolução

### 🔄 **Atualizações Periódicas**

- Revisar relatórios quando módulos evoluem
- Atualizar pontos de função conforme novas funcionalidades
- Manter consolidado sempre atualizado

### 📈 **Evolução do Sistema**

- Novos tipos de métricas podem ser adicionados
- Padrão de PF pode ser refinado
- Interface pode ser aprimorada

---

## 11. Lições Aprendidas e Melhorias

### 🎯 **Principais Lições dos Erros Encontrados**

#### **1. Confusão entre Documentação e Relatórios**
- **Problema**: Usar estrutura de documentação técnica para relatórios
- **Impacto**: Erro "Undefined array key 'objetivo'" na view
- **Solução**: **SEMPRE** usar estrutura específica de relatórios

#### **2. Campos Obrigatórios Diferentes**
- **Problema**: Usar `"modulo"` em vez de `"nome_modulo"`
- **Impacto**: View não conseguia acessar dados
- **Solução**: **SEMPRE** usar campos específicos de relatórios

#### **3. Estrutura de Características Quantitativas**
- **Problema**: Estrutura incorreta de métricas
- **Impacto**: Dados não apareciam na interface
- **Solução**: **SEMPRE** usar estrutura específica com campos corretos

#### **4. Detalhamento de Pontos de Função**
- **Problema**: Estrutura incorreta do array de PF
- **Impacto**: Cálculos não apareciam
- **Solução**: **SEMPRE** usar array de objetos com estrutura específica

#### **5. Funcionalidades e Observações**
- **Problema**: Usar estruturas complexas em vez de arrays simples
- **Impacto**: Dados não eram exibidos corretamente
- **Solução**: **SEMPRE** usar arrays de strings para funcionalidades

### 📋 **Checklist de Validação Final**

**ANTES de salvar qualquer JSON de relatório, verificar:**

- [ ] Estrutura é de relatório (não documentação)
- [ ] Usa `"nome_modulo"` (não `"modulo"`)
- [ ] `"caracteristicas_quantitativas"` com campos corretos
- [ ] `"detalhamento_pontos_funcao"` é array de objetos
- [ ] `"principais_funcionalidades"` é array de strings
- [ ] `"observacao_pf"` é string única
- [ ] `"total_pf"` soma corretamente os valores
- [ ] JSON é válido (sem erros de sintaxe)

### 🔄 **Processo de Validação Recomendado**

1. **Gerar JSON** seguindo o template de relatório
2. **Validar sintaxe** em jsonlint.com
3. **Verificar estruturas críticas** (campos obrigatórios, arrays)
4. **Testar na interface** antes de finalizar
5. **Corrigir erros** se encontrados
6. **Salvar apenas após validação completa**

### 🎯 **Diferenças Críticas: Documentação vs Relatórios**

| Campo | Documentação | Relatórios |
|-------|--------------|------------|
| Identificação | `"modulo"` | `"nome_modulo"` |
| Categorização | `"categoria"` | `"objetivo"` |
| Rotas | `"rotas"` (objeto) | `"caracteristicas_quantitativas"` |
| Arquivos | `"arquivos"` (objeto) | Não aplicável |
| Tabelas | `"tabelas_banco"` (array) | Não aplicável |
| Funcionalidades | `"funcionalidades"` (array) | `"principais_funcionalidades"` |
| Observações | `"observacoes"` (array) | `"observacao_pf"` (string) |
| PF | Não aplicável | `"detalhamento_pontos_funcao"` |

---

**Este documento deve ser consultado SEMPRE antes de trabalhar com relatórios. Seguindo este guia, o sistema funcionará perfeitamente e de forma consistente.** 