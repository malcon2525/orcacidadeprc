# Padrão de Relatório de Atividades — Funcionalidade

> **DOCUMENTO OBRIGATÓRIO**: Este documento define o padrão para criação de relatórios de análise de funcionalidades no projeto OrçaCidade.

---

## 1. Exemplo de Uso

### 📋 **INSTRUÇÃO PARA IA:**

```
"Baseado na funcionalidade [NOME_FUNCIONALIDADE], seguindo a orientação do arquivo '03_padrao_relatorio.md', crie um relatório completo de análise de Pontos de Função (PF)."
```

### 🎯 **RESULTADO ESPERADO:**

**Após seguir este padrão, você deve gerar:**
- ✅ **Análise completa** da funcionalidade
- ✅ **Cálculo preciso** de Pontos de Função
- ✅ **Justificativas detalhadas** de complexidade
- ✅ **Estimativa de custos** baseada em critérios técnicos

---

## 2. Checklist de Análise Obrigatória

**⚠️ ANTES de iniciar o relatório, execute esta análise completa para garantir precisão:**

- [ ] **Acesse as rotas** (`routes/web.php` e `routes/api.php`)
- [ ] **Acesse a documentação** técnica da funcionalidade (`docs/técnico/`)
- [ ] **Acesse os controllers** principais e secundários
- [ ] **Acesse os componentes** Vue e views Blade
- [ ] **Acesse os services** (se houver)
- [ ] **Acesse as migrações** (`database/migrations/`) 
- [ ] **Acesse `docs/criterio_calculo_pf.md`** para aplicar critérios corretos

**⚠️ IMPORTANTE:** Apenas com análise completa de TODOS os arquivos é possível fazer um cálculo preciso de PF.

---

## 3. Estrutura do Relatório

### 📊 **Tabela de Resumo da Funcionalidade**

| Item                        | Descrição/Quantidade                                                                                 |
|-----------------------------|-----------------------------------------------------------------------------------------------------|
| **Nome da Funcionalidade**  | [Nome da funcionalidade]                                                                             |
| **Objetivo**                | [Descrição dos objetivos da funcionalidade, incluindo contexto e propósito no sistema]              |
| **Rotas Web**               | [Quantidade e breve descrição das rotas web]                                                        |
| **Rotas API**               | [Quantidade e breve descrição das rotas API, se houver]                                             |
| **Componentes Vue**         | [Quantidade e nomes dos componentes Vue principais]                                                 |
| **Views Blade**             | [Quantidade e nomes das views Blade principais]                                                     |
| **Controllers**             | [Quantidade e nomes dos controllers principais]                                                     |
| **Models**                  | [Quantidade e nomes dos models principais]                                                          |
| **Services**                | [Quantidade e nomes dos services, se houver]                                                        |
| **Tabelas no Banco (migrations)** | [Quantidade e nomes das tabelas e views materializadas principais]                              |
| **Complexidade**            | [Baixa, Moderada, Alta, Muito Alta — justifique]                                                    |

---

## 4. Detalhamento dos Pontos de Função (PF)

### 📈 **Tabela de Cálculo PF**

| Tipo de Função                        | Quantidade | PF por função | Total PF | Justificativa |
|---------------------------------------|------------|---------------|----------|---------------|
| Entradas Externas (EE)                |            |               |          |               |
| Saídas Externas (SE)                  |            |               |          |               |
| Consultas Externas (CE)               |            |               |          |               |
| Arquivos Lógicos Internos (ALI)       |            |               |          |               |
| Arquivos de Interface Externa (AIE)   |            |               |          |               |
| **Total**                             |            |               | **XX**   |               |

### 📋 **Critérios e Observações Detalhadas:**

#### **Entradas Externas (EE):**
- **[Nome da funcionalidade 1] (X PF):** [Descrição detalhada da complexidade e justificativa]
- **[Nome da funcionalidade 2] (X PF):** [Descrição detalhada da complexidade e justificativa]
- **[Nome da funcionalidade 3] (X PF):** [Descrição detalhada da complexidade e justificativa]

#### **Saídas Externas (SE):**
- **[Nome da funcionalidade 1] (X PF):** [Descrição detalhada da complexidade e justificativa]
- **[Nome da funcionalidade 2] (X PF):** [Descrição detalhada da complexidade e justificativa]
- **[Nome da funcionalidade 3] (X PF):** [Descrição detalhada da complexidade e justificativa]

#### **Consultas Externas (CE):**
- **[Nome da funcionalidade 1] (X PF):** [Descrição detalhada da complexidade e justificativa]
- **[Nome da funcionalidade 2] (X PF):** [Descrição detalhada da complexidade e justificativa]
- **[Nome da funcionalidade 3] (X PF):** [Descrição detalhada da complexidade e justificativa]

#### **Arquivos Lógicos Internos (ALI):**
- **[Nome da tabela/arquivo 1] (X PF):** [Descrição dos campos, relacionamentos e complexidade]
- **[Nome da tabela/arquivo 2] (X PF):** [Descrição dos campos, relacionamentos e complexidade]
- **[Nome da tabela/arquivo 3] (X PF):** [Descrição dos campos, relacionamentos e complexidade]

#### **Arquivos de Interface Externa (AIE):**
- **[X PF]:** [Explique integrações externas ou indique "Não há integrações com sistemas externos"]

---

## 5. Justificativa da Complexidade e Custo

### 🎯 **Análise de Complexidade:**

- **Complexidade: [BAIXA/MODERADA/ALTA/MUITO ALTA]**
  - **[Fator 1]:** [Descrição específica do que adiciona complexidade]
  - **[Fator 2]:** [Descrição específica do que adiciona complexidade]
  - **[Fator 3]:** [Descrição específica do que adiciona complexidade]
  - **[Fator 4]:** [Descrição específica do que adiciona complexidade]
  - **[Fator 5]:** [Descrição específica do que adiciona complexidade]

### 💰 **Cálculo de PF e Custos:**

- **Cálculo de PF:**
  - **[X PF está adequado/inadequado]** para uma funcionalidade com esta robustez técnica
  - Comparado a funcionalidades [simples/complexas] ([X-Y PF]), esta possui recursos [mais/menos] complexos
  - [Componente/Service/Funcionalidade específica] sozinho justifica a [baixa/moderada/alta] complexidade
  - [Funcionalidade única] é uma funcionalidade [comum/única/diferenciada] no sistema

- **Conversão para horas/custo:**
  - **Fator de conversão:** [X horas/PF] e [R$ X/PF] (conforme `docs/criterio_calculo_pf.md`)
  - **Cálculo de horas:** [X PF] × [X horas/PF] = **[X horas]**
  - **Cálculo de custo:** [X PF] × [R$ X/PF] = **R$ [X]**

### 📊 **Resumo Final:**

- **O valor está adequado/inadequado** ao escopo e robustez da funcionalidade
- [Funcionalidades específicas] justificam a [baixa/alta] pontuação
- [Componente/Service] especializado com [X métodos/funcionalidades] adiciona valor significativo
- Interface [simples/complexa] com [X linhas/componentes] indica [baixa/alta] complexidade de desenvolvimento
- **[X PF] = [X horas] = R$ [X]** é condizente com a sofisticação técnica implementada

---

## 6. Proibições Essenciais

### ❌ **NUNCA FAÇA:**

- **Relatórios sem análise completa** de todos os arquivos
- **Cálculos de PF sem justificativas detalhadas**
- **Estimativas sem considerar complexidade técnica**
- **Relatórios baseados apenas em suposições**
- **Análises sem acesso aos controllers e models**
- **Cálculos sem consultar `docs/criterio_calculo_pf.md`**
- **Salvar relatórios na raiz** de `docs/relatorios/` (sempre usar pasta do módulo)
- **Usar nomenclatura incorreta** (sempre seguir padrão `[nome]-relatorio.md`)
- **Não verificar estrutura de pastas** antes de criar relatórios

---

## 7. Checklist de Implementação

### ✅ **ANTES DE ENTREGAR O RELATÓRIO:**

- [ ] **Análise completa** de todos os arquivos da funcionalidade
- [ ] **Checklist obrigatório** preenchido
- [ ] **Tabela de resumo** completa
- [ ] **Cálculo de PF** detalhado e justificado
- [ ] **Análise de complexidade** fundamentada
- [ ] **Estimativa de custos** baseada em critérios técnicos
- [ ] **Resumo final** conclusivo
- [ ] **Verificação** de todas as proibições
- [ ] **Localização correta** do arquivo na pasta do módulo
- [ ] **Nomenclatura padronizada** do arquivo

---

## 8. Localização e Organização dos Relatórios

### 📁 **Estrutura de Pastas:**
- **Localização:** `docs/relatorios/[nome-do-modulo]/`
- **Exemplo:** `docs/relatorios/administracao/gerenciamento-usuarios-relatorio.md`
- **Padrão:** Seguir a estrutura de módulos do projeto (administracao, transporte, orcamento, clientes, etc.)

### 📝 **Nomenclatura:**
- **Formato:** `[nome-funcionalidade]-relatorio.md`
- **Exemplo:** `gerenciamento-usuarios-permissoes-relatorio.md`
- **Padrão:** Usar hífens para separar palavras, terminar com `-relatorio.md`

### 🔍 **Organização por Módulos:**
- **Módulo Administração:** `docs/relatorios/administracao/`
- **Módulo Transporte:** `docs/relatorios/transporte/`
- **Módulo Orçamento:** `docs/relatorios/orcamento/`
- **Módulo Clientes:** `docs/relatorios/clientes/`
- **Consultas de Tabelas:** `docs/relatorios/consultar tabelas/`

### ⚠️ **IMPORTANTE:**
- **SEMPRE** criar relatórios na pasta do módulo correspondente
- **NUNCA** deixar relatórios na raiz de `docs/relatorios/`
- **SEMPRE** seguir a nomenclatura padronizada
- **SEMPRE** verificar se a pasta do módulo existe antes de criar o relatório

---

## 9. Conclusão

### 🎯 **RESULTADO ESPERADO:**

**Um relatório de funcionalidade deve:**
- ✅ **Ser técnico e preciso** em seus cálculos
- ✅ **Justificar todas as decisões** de complexidade
- ✅ **Baseiar-se em análise completa** dos arquivos
- ✅ **Fornecer estimativas realistas** de custos
- ✅ **Seguir rigorosamente** os critérios de PF
- ✅ **Ser salvo na localização correta** seguindo a estrutura de módulos
- ✅ **Usar nomenclatura padronizada** para facilitar organização

**Este padrão garante relatórios consistentes, precisos e profissionais para todas as funcionalidades do projeto OrçaCidade.** 