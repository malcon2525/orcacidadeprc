# Padrão de Relatório de Atividades — Módulo

Preencha cada campo de forma objetiva e precisa, justificando os cálculos e classificações. Use tabelas para facilitar a consolidação de múltiplos módulos.

## 📋 Checklist de Análise Obrigatória

**ANTES de iniciar o relatório, execute esta análise completa para garantir precisão:**

- [ ] **Acesse as rotas** (`routes/web.php` e `routes/api.php`)
- [ ] **Acesse a documentação** técnica do módulo (`docs/técnico/`)
- [ ] **Acesse os controllers** principais e secundários
- [ ] **Acesse os componentes** Vue e views Blade
- [ ] **Acesse os services** (se houver)
- [ ] **Acesse as migrações** (`database/migrations/`) 
- [ ] **Acesse `docs/padrao-relatorio.md`** (este arquivo)
- [ ] **Acesse `docs/criterio_calculo_pf.md`** para aplicar critérios corretos

**⚠️ IMPORTANTE:** Apenas com análise completa de TODOS os arquivos é possível fazer um cálculo preciso de PF.

---

| Item                        | Descrição/Quantidade                                                                                 |
|-----------------------------|-----------------------------------------------------------------------------------------------------|
| **Nome do Módulo**          | [Nome do módulo]                                                                                    |
| **Objetivo**                | [Descrição dos objetivos do módulo, incluindo contexto e propósito no sistema]                      |
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

## Detalhamento dos Pontos de Função (PF)

| Tipo de Função                        | Quantidade | PF por função | Total PF | Justificativa |
|---------------------------------------|------------|---------------|----------|---------------|
| Entradas Externas (EE)                |            |               |          |               |
| Saídas Externas (SE)                  |            |               |          |               |
| Consultas Externas (CE)               |            |               |          |               |
| Arquivos Lógicos Internos (ALI)       |            |               |          |               |
| Arquivos de Interface Externa (AIE)   |            |               |          |               |
| **Total**                             |            |               | **XX**   |               |

### Critérios e Observações:

- **EE (X PF):** 
  - [Nome da funcionalidade 1] (X PF): [Descrição detalhada da complexidade e justificativa]
  - [Nome da funcionalidade 2] (X PF): [Descrição detalhada da complexidade e justificativa]
  - [Nome da funcionalidade 3] (X PF): [Descrição detalhada da complexidade e justificativa]

- **SE (X PF):** 
  - [Nome da funcionalidade 1] (X PF): [Descrição detalhada da complexidade e justificativa]
  - [Nome da funcionalidade 2] (X PF): [Descrição detalhada da complexidade e justificativa]
  - [Nome da funcionalidade 3] (X PF): [Descrição detalhada da complexidade e justificativa]

- **CE (X PF):** 
  - [Nome da funcionalidade 1] (X PF): [Descrição detalhada da complexidade e justificativa]
  - [Nome da funcionalidade 2] (X PF): [Descrição detalhada da complexidade e justificativa]
  - [Nome da funcionalidade 3] (X PF): [Descrição detalhada da complexidade e justificativa]

- **ALI (X PF):** 
  - [Nome da tabela/arquivo 1] (X PF): [Descrição dos campos, relacionamentos e complexidade]
  - [Nome da tabela/arquivo 2] (X PF): [Descrição dos campos, relacionamentos e complexidade]
  - [Nome da tabela/arquivo 3] (X PF): [Descrição dos campos, relacionamentos e complexidade]

- **AIE (X PF):** [Explique integrações externas ou indique "Não há integrações com sistemas externos"]

---

## Justificativa da Complexidade e Custo

- **Complexidade: [BAIXA/MODERADA/ALTA/MUITO ALTA]**
  - **[Fator 1]:** [Descrição específica do que adiciona complexidade]
  - **[Fator 2]:** [Descrição específica do que adiciona complexidade]
  - **[Fator 3]:** [Descrição específica do que adiciona complexidade]
  - **[Fator 4]:** [Descrição específica do que adiciona complexidade]
  - **[Fator 5]:** [Descrição específica do que adiciona complexidade]

- **Cálculo de PF:**
  - **[X PF está adequado/inadequado]** para um módulo com esta robustez técnica
  - Comparado a módulos [simples/complexos] ([X-Y PF]), este possui funcionalidades [mais/menos] complexas
  - [Componente/Service/Funcionalidade específica] sozinho justifica a [baixa/moderada/alta] complexidade
  - [Funcionalidade única] é uma funcionalidade [comum/única/diferenciada] no sistema

- **Conversão para horas/custo:**
  - **Fator de conversão:** [X horas/PF] e [R$ X/PF] (conforme criterio_calculo_pf.md)
  - **Cálculo de horas:** [X PF] × [X horas/PF] = **[X horas]**
  - **Cálculo de custo:** [X PF] × [R$ X/PF] = **R$ [X]**

- **Resumo:**
  - **O valor está adequado/inadequado** ao escopo e robustez do módulo
  - [Funcionalidades específicas] justificam a [baixa/alta] pontuação
  - [Componente/Service] especializado com [X métodos/funcionalidades] adiciona valor significativo
  - Interface [simples/complexa] com [X linhas/componentes] indica [baixa/alta] complexidade de desenvolvimento
  - **[X PF] = [X horas] = R$ [X]** é condizente com a sofisticação técnica implementada

--- 