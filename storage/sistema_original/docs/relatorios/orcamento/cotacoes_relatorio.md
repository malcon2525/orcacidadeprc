# Relatório de Atividades — Módulo Cotações

Preencha cada campo de forma objetiva e precisa, justificando os cálculos e classificações. Use tabelas para facilitar a consolidação de múltiplos módulos.

---

| Item                        | Descrição/Quantidade                                                                                 |
|-----------------------------|-----------------------------------------------------------------------------------------------------|
| **Nome do Módulo**          | Cotações                                                                                            |
| **Objetivo**                | Gerenciar o processo de cotação de preços de insumos e serviços, registrando múltiplos fornecedores, anexando propostas e calculando o valor final de forma automatizada (média, mediana, menor valor) ou manual. |
| **Rotas Web**               | 1 - `GET /cotacoes` (Carrega a view principal do módulo)                                              |
| **Rotas API**               | 7 - CRUD de Cotações (4), busca de fornecedores (2), criação de fornecedor (1)                         |
| **Componentes Vue**         | 1 - `ListaCotacoes.vue` (Componente principal que gerencia toda a interface do módulo)                |
| **Views Blade**             | 1 - `index.blade.php` (View que carrega o componente Vue)                                             |
| **Controllers**             | 2 - `CotacaoController`, `FornecedorController`                                                     |
| **Models**                  | 3 - `Cotacao`, `CotacaoFornecedor`, `Fornecedor`                                                      |
| **Tabelas no Banco (migrations)** | 3 - `fornecedores`, `cotacoes`, `cotacao_fornecedores`                                            |
| **Complexidade**            | **Alta** — Justificativa abaixo.                                                                    |

---

## Detalhamento dos Pontos de Função (PF)

| Tipo de Função                        | Quantidade | PF por função | Total PF | Justificativa |
|---------------------------------------|------------|---------------|----------|---------------|
| Entradas Externas (EE)                | 4          | 4             | 16       | CRUD de cotações e fornecedores, upload de arquivos |
| Saídas Externas (SE)                  | 1          | 5             | 5        | Listagem paginada e detalhada de cotações          |
| Consultas Externas (CE)               | 2          | 4             | 8        | Consultas de fornecedores para zoom/autocomplete   |
| Arquivos Lógicos Internos (ALI)       | 3          | 7             | 21       | Tabelas principais do módulo                       |
| Arquivos de Interface Externa (AIE)   | 0          | 0             | 0        | Não há integração externa                          |
| **Total**                             |            |               | **50**   |                                                   |

### Critérios e Observações:
- **EE:** O cadastro e edição de cotação são complexos, pois envolvem múltiplos fornecedores, upload de arquivos, validações e lógica de negócio (exatamente 3 fornecedores, cálculo automático/manual do valor final).
- **SE:** A listagem de cotações é rica, com filtros, paginação e dados agregados.
- **CE:** As buscas de fornecedores são essenciais para a experiência do usuário e integração do fluxo.
- **ALI:** As três tabelas são centrais e possuem relacionamentos e regras de integridade.
- **AIE:** Não aplicável.

---

## Justificativa da Complexidade e Custo

- **Complexidade: Alta**
  - **Interface Rica:** O módulo é centralizado em um único componente Vue (`ListaCotacoes.vue`) que gerencia estado, modais de criação/edição, múltiplos formulários aninhados (fornecedores) e validações em tempo real.
  - **Gerenciamento de Arquivos:** Lógica robusta no backend para upload, armazenamento, exclusão e atualização de arquivos PDF, com nomenclatura e estrutura de diretórios dinâmicas.
  - **Lógica de Negócio:** Regras estritas (ex: 3 fornecedores por cotação), cálculos automáticos (média, mediana, menor valor) e criação de fornecedores "on-the-fly".
  - **Integração:** O valor final da cotação é utilizado por outros módulos de orçamentação, tornando este um ponto crítico do sistema.

- **Cálculo de PF:**
  - O total de **50 Pontos de Função** é considerado adequado para um módulo com as características descritas. É mais complexo que um CRUD simples (que teria entre 15-20 PF) devido à complexidade da interface, regras de negócio e gerenciamento de arquivos.

- **Conversão para horas/custo (Exemplo):**
  - **Fator de conversão:** 8 horas por PF (padrão para sistemas de gestão com complexidade média/alta).
  - **Cálculo de horas:** 50 PF × 8 horas/PF = **400 horas**.
  - **Cálculo de custo (exemplo):** Se o custo/hora for R$ 100,00, o valor estimado do módulo seria de **R$ 40.000,00**.
  - Estes valores são estimativas e podem ser ajustados com base na produtividade real da equipe e no custo/hora definido no projeto.

- **Resumo:**
  - O custo e a complexidade estimados são justificados pela robustez, criticidade e funcionalidades avançadas do módulo de cotações. O valor reflete não apenas o desenvolvimento, mas a garantia de um processo de cotação rastreável, seguro e integrado ao restante do sistema.

--- 