# Relatório de Atividades — Módulo BDI

---

| Item                        | Descrição/Quantidade                                                                                 |
|-----------------------------|-----------------------------------------------------------------------------------------------------|
| **Nome do Módulo**          | BDI (Benefícios e Despesas Indiretas)                                                                |
| **Objetivo**                | Centralizar o cálculo, registro e gestão dos percentuais de BDI para entidades orçamentárias e orçamentos, garantindo padronização, rastreabilidade e integração ao processo orçamentário. |
| **Rotas Web**               | 6 — Listagem, criação, edição, exclusão, filtro e paginação de BDIs                                  |
| **Rotas API**               | 0 — Todas as rotas são web, mas retornam JSON para integração frontend                              |
| **Componentes Vue**         | 1 — ListaBDI.vue                                                                                    |
| **Views Blade**             | 1 — orcamento/bdi/index.blade.php                                                                   |
| **Controllers**             | 1 — BDIController                                                                                   |
| **Models**                  | 1 — BDI                                                                                            |
| **Tabelas no Banco (migrations)** | 1 — bdis                                                                                       |
| **Complexidade**            | Moderada/Alta — Devido a cálculos dinâmicos, integrações, validações e interface responsiva         |
| **Pontos de Função (PF)**   | 7                                                                                                   |

---

## Detalhamento dos Pontos de Função (PF)

| Tipo de Função                        | Quantidade | PF por função | Total PF | Justificativa                                                                 |
|---------------------------------------|------------|---------------|----------|-------------------------------------------------------------------------------|
| Entradas Externas (EE)                | 2          | 2             | 4        | Cadastro e edição de BDI com múltiplos campos, validações e cálculos          |
| Saídas Externas (SE)                  | 1          | 1             | 1        | Exclusão de BDI com checagem de vínculos                                      |
| Consultas Externas (CE)               | 1          | 1             | 1        | Listagem/pesquisa de BDIs com filtros, paginação e integração                 |
| Arquivos Lógicos Internos (ALI)       | 1          | 1             | 1        | Tabela bdis com estrutura e relacionamentos                                   |
| Arquivos de Interface Externa (AIE)   | 0          | 0             | 0        | Não há integração direta com sistemas externos                                |
| **Total**                             |            |               | **7**    |                                                                               |

### Critérios e Observações:
- **EE:** Consideradas as telas/modal de cadastro e edição, ambas com múltiplos campos, validações e lógica de cálculo.
- **SE:** Exclusão de BDI, com checagem de integridade e feedback ao usuário.
- **CE:** Listagem e pesquisa de BDIs, com filtros por entidade, nome e analisado, além de paginação.
- **ALI:** Tabela `bdis` armazena todos os dados do módulo, incluindo relacionamentos.
- **AIE:** Não há integração direta com sistemas externos neste módulo.

---

## Justificativa da Complexidade e Custo

- **Complexidade:**
  - Classificado como Moderada/Alta devido à necessidade de cálculos dinâmicos de percentuais, integrações com entidades orçamentárias e orçamentos, validações de campos, interface responsiva e feedback visual detalhado.
  - O módulo exige lógica de negócio robusta para garantir precisão dos cálculos e integridade dos dados.

- **Cálculo de PF:**
  - O valor de 7 PF está dentro do padrão para módulos de cadastro com cálculos e integrações.
  - Módulos mais simples (CRUD puro) ficam entre 3 e 5 PF; módulos mais complexos (importação, múltiplas integrações) podem superar 10 PF.

- **Conversão para horas/custo:**
  - Fator de conversão: 8 horas por PF; custo: R$ 800 por PF.
  - Cálculo: 7 PF × 8h = 56 horas; 7 PF × R$ 800 = R$ 5.600,00.
  - Valores podem ser ajustados conforme produtividade e custo/hora da equipe.

- **Resumo:**
  - O valor está adequado ao escopo e robustez do módulo, considerando a lógica de cálculo, integrações e interface.
  - Não há indícios de superdimensionamento ou subdimensionamento.
  - O cálculo é transparente e justificado, seguindo a metodologia de Pontos de Função.

---

## Observações e Sugestões
- O módulo está bem estruturado, com lógica de cálculo robusta e integração com outros módulos.
- Possui potencial para evoluções como histórico de alterações, exportação de relatórios e validações adicionais.
- Recomenda-se testes manuais e automatizados para garantir a precisão dos cálculos e integrações.

---

## Conclusão
O módulo de BDI entrega precisão, controle e integração ao processo orçamentário, com lógica de cálculo robusta e pronta para evoluções conforme as necessidades do negócio. 