# Relatório de Atividades — Módulo DMT

Preencha cada campo de forma objetiva e precisa, justificando os cálculos e classificações. Use tabelas para facilitar a consolidação de múltiplos módulos.

---

| Item                        | Descrição/Quantidade                                                                                 |
|-----------------------------|-----------------------------------------------------------------------------------------------------|
| **Nome do Módulo**          | DMT - Dados de Materiais de Transporte                                                              |
| **Objetivo**                | Gerenciar dados de materiais de transporte específicos por entidade orçamentária, permitindo edição de distâncias (X1 e X2) e informações de origem para cálculo de custos de transporte |
| **Rotas Web**               | 7 rotas (CRUD completo, listagem, selects, geração automática)                                      |
| **Rotas API**               | 1 rota (atualização em lote)                                                                         |
| **Componentes Vue**         | 1 componente (ListaDmt.vue)                                                                          |
| **Views Blade**             | 1 view (transporte/dmt/index.blade.php)                                                              |
| **Controllers**             | 1 controller (DmtController.php)                                                                     |
| **Models**                  | 1 model (Dmt.php)                                                                                    |
| **Tabelas no Banco (migrations)** | 1 tabela (dmts)                                                                                  |
| **Complexidade**            | Moderada — interface com edição inline, processamento em lote, integração com tabela externa        |

---

## Detalhamento dos Pontos de Função (PF)

| Tipo de Função                        | Quantidade | PF por função | Total PF | Justificativa |
|---------------------------------------|------------|---------------|----------|---------------|
| Entradas Externas (EE)                | 4          | 3             | 12       | CRUD individual (3) + geração automática (1) |
| Saídas Externas (SE)                  | 2          | 2             | 4        | Listagem com filtros (1) + edição em lote (1) |
| Consultas Externas (CE)               | 2          | 2             | 4        | Busca de dados (1) + selects (1) |
| Arquivos Lógicos Internos (ALI)       | 1          | 2             | 2        | Tabela dmts com relacionamentos |
| Arquivos de Interface Externa (AIE)   | 0          | 0             | 0        | Não há integração externa |
| **Total**                             | 9          | -             | **22**   | - |

### Critérios e Observações:
- **EE:** CRUD individual (3), geração automática de DMTs (1)
- **SE:** Listagem com filtros complexos (1), edição em lote (1)
- **CE:** Busca de dados com filtros (1), dados para selects (1)
- **ALI:** Tabela dmts com relacionamentos com municipios e entidades_orcamentarias (1)
- **AIE:** Não há integração com sistemas externos

---

## Justificativa da Complexidade e Custo

- **Complexidade:**
  - Interface com edição inline na tabela
  - Processamento em lote via API
  - Integração com tabela dmt_default para geração automática
  - Validações complexas de dados
  - Agrupamento visual por destino
  - Filtros obrigatórios e opcionais

- **Cálculo de PF:**
  - 22 PF está dentro do padrão para módulo de complexidade moderada
  - Comparável a outros módulos CRUD com funcionalidades avançadas
  - Interface complexa e processamento em lote justificam PF elevado

- **Conversão para horas/custo:**
  - Fator de conversão: 1 PF = 8 horas de desenvolvimento
  - Cálculo: 22 PF × 8 horas = 176 horas
  - Custo por PF: R$ 800,00
  - Valor estimado: 22 PF × R$ 800,00 = R$ 17.600,00

- **Resumo:**
  - Valor adequado ao escopo e robustez do módulo
  - Interface avançada e funcionalidades complexas justificam o investimento
  - Cálculo transparente seguindo metodologia de Pontos de Função 