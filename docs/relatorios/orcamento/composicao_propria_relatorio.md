# Relatório de Atividades — Módulo Composições Próprias

---

| Item                        | Descrição/Quantidade                                                                                 |
|-----------------------------|-----------------------------------------------------------------------------------------------------|
| **Nome do Módulo**          | Composições Próprias                                                                                |
| **Objetivo**                | Permitir o cadastro, edição, consulta e exclusão de composições orçamentárias personalizadas, compostas por múltiplos itens, podendo ser baseadas em referências oficiais (SINAPI, DERPR) ou personalizadas. |
| **Rotas Web**               | 9 — CRUD completo, listagem paginada, views, integração Vue                                         |
| **Rotas API**               | 2 — Zoom de serviços SINAPI e DERPR                                                                 |
| **Componentes Vue**         | 3 — ListaComposicaoPropria.vue, ComposicaoPropriaForm.vue, ZoomServico.vue                         |
| **Views Blade**             | 1 — index.blade.php                                                                                 |
| **Controllers**             | 3 — ComposicaoPropriaController, ConsultarSINAPIController (zoom), ConsultarDERPRController (zoom)  |
| **Models**                  | 2 — ComposicaoPropria, ComposicaoPropriaItem                                                        |
| **Tabelas no Banco (migrations)** | 2 — composicao_proprias, composicao_propria_items                                               |
| **Complexidade**            | Muito Alta — Justificativa abaixo.                                                                  |

---

## Detalhamento dos Pontos de Função (PF)

| Tipo de Função                        | Quantidade | PF por função | Total PF | Justificativa |
|---------------------------------------|------------|---------------|----------|---------------|
| Entradas Externas (EE)                | 4          | 5             | 20       | CRUD de composições e itens, uso de zoom, payloads complexos |
| Saídas Externas (SE)                  | 2          | 5             | 10       | Listagem paginada, retorno detalhado de composições e itens |
| Consultas Externas (CE)               | 2          | 4             | 8        | Zoom de serviços SINAPI/DERPR, busca paginada e filtrada    |
| Arquivos Lógicos Internos (ALI)       | 2          | 7             | 14       | Tabelas composicao_proprias e composicao_propria_items      |
| Arquivos de Interface Externa (AIE)   | 0          | 0             | 0        | Não há integração externa                                   |
| **Total**                             |            |               | **52**   |                                                       |

### Critérios e Observações:
- **EE:** Entradas incluem cadastro, edição, exclusão e uso do zoom, com payloads aninhados e validação robusta.
- **SE:** Saídas são as listagens paginadas e detalhadas, tanto para composições quanto para itens.
- **CE:** Consultas externas são as buscas de serviços para o zoom, com filtros e paginação.
- **ALI:** As duas tabelas principais do módulo, com relacionamentos e remoção em cascata.
- **AIE:** Não aplicável.

---

## Justificativa da Complexidade e Custo

- **Complexidade: Muito Alta**
  - **Interface Avançada:** Formulários dinâmicos, zoom integrado, validação em tempo real, feedback visual, modais, filtros e paginação.
  - **Integração:** Zoom de serviços SINAPI/DERPR, integração entre frontend e backend, preenchimento automático de campos.
  - **Regras de Negócio:** Validações complexas, cálculo automático de totais, remoção em cascata, transações para garantir integridade.
  - **Flexibilidade:** Permite tanto uso de referências oficiais quanto personalização total dos itens.

- **Cálculo de PF:**
  - O total de **52 Pontos de Função** é adequado para um módulo com múltiplos fluxos, payloads aninhados, integrações e interface rica. É mais complexo que um CRUD simples (15-20 PF) e comparável a módulos centrais do sistema.

- **Conversão para horas/custo (Exemplo):**
  - **Fator de conversão:** 8 horas por PF (padrão para sistemas de gestão com alta complexidade).
  - **Cálculo de horas:** 52 PF × 8 horas/PF = **416 horas**.
  - **Cálculo de custo (exemplo):** Se o custo/hora for R$ 100,00, o valor estimado do módulo seria de **R$ 41.600,00**.
  - Estes valores são estimativas e podem ser ajustados conforme produtividade real da equipe e custo/hora definido no projeto.

- **Resumo:**
  - O custo e a complexidade estimados são justificados pela robustez, criticidade e funcionalidades avançadas do módulo de composições próprias. O valor reflete não apenas o desenvolvimento, mas a garantia de um processo flexível, rastreável e integrado ao restante do sistema.

--- 