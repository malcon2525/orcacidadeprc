# Relatório de Atividades — Módulo DMT PRC

---

| Item                        | Descrição/Quantidade                                                                                 |
|-----------------------------|-----------------------------------------------------------------------------------------------------|
| **Nome do Módulo**          | DMT PRC                                                                                             |
| **Objetivo**                | Gerenciar materiais de transporte padrão (PRC) que servem como referência para os DMTs de cada entidade orçamentária, facilitando padronização e reaproveitamento de dados. |
| **Rotas Web**               | 6 — Listagem, criação, edição, exclusão, importação via Excel e página de gerenciamento            |
| **Rotas API**               | 0 — Todas as rotas são web, mas retornam JSON para integração frontend                              |
| **Componentes Vue**         | 1 — ListaDmtDefault.vue                                                                             |
| **Views Blade**             | 1 — transporte/dmt-default/index.blade.php                                                         |
| **Controllers**             | 1 — DmtDefaultController                                                                            |
| **Models**                  | 1 — DmtDefault                                                                                      |
| **Tabelas no Banco (migrations)** | 1 — dmt_default                                                                                |

---

## Detalhamento dos Pontos de Função (PF)

| Tipo de Função                        | Quantidade | PF por função | Total PF | Justificativa                                                                 |
|---------------------------------------|------------|---------------|----------|-------------------------------------------------------------------------------|
| Entradas Externas (EE)                | 3          | 2             | 6        | CRUD de materiais, importação Excel com validação complexa, edição inline     |
| Saídas Externas (SE)                  | 2          | 1             | 2        | Exclusão de materiais, listagem paginada com filtros                          |
| Consultas Externas (CE)               | 1          | 1             | 1        | Busca de materiais com ordenação por destino                                  |
| Arquivos Lógicos Internos (ALI)       | 1          | 1             | 1        | Tabela dmt_default com estrutura e relacionamentos                            |
| Arquivos de Interface Externa (AIE)   | 0          | 0             | 0        | Não há integração direta com sistemas externos                                |
| **Total**                             |            |               | **10**   |                                                                               |

### Critérios e Observações:
- **EE:** Consideradas as telas de CRUD, importação Excel com mapeamento dinâmico de colunas e validação robusta, e edição inline de campos, todas com múltiplos campos e validações.
- **SE:** Exclusão de materiais com verificação de integridade e listagem paginada com ordenação por destino.
- **CE:** Consulta de materiais com ordenação padrão e paginação.
- **ALI:** Tabela `dmt_default` armazena todos os dados do módulo, incluindo campos de distância e tipo de transporte.
- **AIE:** Não há integração direta com sistemas externos neste módulo.

**Complexidade:** Moderada — Devido a importação robusta via Excel com mapeamento dinâmico, validações rigorosas, logs detalhados e tratamento de dados numéricos.

**Total de Pontos de Função:** 10 PF

---

## Justificativa da Complexidade e Custo

- **Complexidade:**
  - Classificado como Moderada devido à necessidade de importação robusta via Excel com mapeamento dinâmico de colunas, validações rigorosas de dados, tratamento de números (conversão vírgula/ponto), logs detalhados e paginação com ordenação.
  - O módulo exige lógica de negócio para garantir integridade dos dados importados, rastreabilidade das operações e interface intuitiva para o usuário.

- **Cálculo de PF:**
  - O valor de 10 PF está adequado para módulos com importação robusta e validações complexas.
  - Módulos mais simples (CRUD puro) ficam entre 3 e 5 PF; módulos com importação podem variar entre 8 e 15 PF.
  - A complexidade da importação Excel com mapeamento dinâmico e validações justifica o valor.

- **Conversão para horas/custo:**
  - Fator de conversão: 8 horas por PF; custo: R$ 800 por PF.
  - Cálculo: 10 PF × 8h = 80 horas; 10 PF × R$ 800 = R$ 8.000,00.
  - Valores podem ser ajustados conforme produtividade e custo/hora da equipe.

- **Resumo:**
  - O valor está adequado ao escopo e robustez do módulo, considerando a importação complexa e validações.
  - Não há indícios de superdimensionamento, considerando as funcionalidades implementadas.
  - O cálculo é transparente e justificado, seguindo a metodologia de Pontos de Função.

---

## Observações e Sugestões
- O módulo está bem estruturado, com importação segura, validações robustas e interface intuitiva.
- Possui potencial para evoluções como histórico de alterações, exportação de relatórios, validações adicionais e integração com mais sistemas externos.
- Recomenda-se testes automatizados para garantir a precisão da importação, validação de dados e integridade dos registros.
- O mapeamento dinâmico de colunas Excel demonstra boa arquitetura, facilitando manutenção e flexibilidade.

---

## Conclusão
O módulo de DMT PRC entrega controle eficiente de materiais de transporte padrão, com importação robusta, validações rigorosas e interface intuitiva, garantindo padronização e reaproveitamento de dados para o processo orçamentário. A arquitetura bem estruturada com mapeamento dinâmico garante manutenibilidade e escalabilidade. 