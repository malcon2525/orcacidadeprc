# Padrão de Relatório de Atividades — Módulo

| Item                        | Descrição/Quantidade                                                                                 |
|-----------------------------|-----------------------------------------------------------------------------------------------------|
| **Nome do Módulo**          | Entidades Orçamentárias                                                                             |
| **Objetivo**                | Permitir cadastro, consulta, edição, exclusão, filtro, paginação e importação de entidades orçamentárias (municípios, secretarias, órgãos, autarquias, outros), com interface Vue, validação robusta e integração com municípios. |
| **Rotas Web**               | 8 rotas principais:<br>- `/entidades-orcamentarias` (index)<br>- `/entidades-orcamentarias/listar` (JSON)<br>- `/entidades-orcamentarias/listar-select` (JSON)<br>- `/entidades-orcamentarias/create` (criação)<br>- `/entidades-orcamentarias` (POST, store)<br>- `/entidades-orcamentarias/{id}/edit` (edição)<br>- `/entidades-orcamentarias/{id}` (PUT, update)<br>- `/entidades-orcamentarias/{id}` (DELETE, destroy) |
| **Rotas API**               | 6 rotas principais:<br>- `/api/entidades-orcamentarias/` (listar)<br>- `/api/entidades-orcamentarias/` (POST, criar)<br>- `/api/entidades-orcamentarias/{id}` (GET, show)<br>- `/api/entidades-orcamentarias/{id}` (PUT, atualizar)<br>- `/api/entidades-orcamentarias/{id}` (DELETE, remover)<br>- `/api/entidades-orcamentarias/importar-municipios` (importar em massa) |
| **Componentes Vue**         | 1 principal:<br>- `ListaEntidadesOrcamentarias.vue`                                                 |
| **Views Blade**             | 1 principal:<br>- `gerais/entidades-orcamentarias/index.blade.php`                                   |
| **Controllers**             | 1 principal:<br>- `EntidadeOrcamentariaController.php`                                               |
| **Models**                  | 1 principal:<br>- `EntidadeOrcamentaria.php`                                                        |
| **Tabelas no Banco (migrations)** | 1 principal:<br>- `entidades_orcamentarias` (migration: 06B_create_entidades_orcamentarias_table.php) |
| **Complexidade**            | Moderada — CRUD padrão, importação em massa, interface SPA, validação robusta, múltiplos filtros, sem integrações externas complexas. |
| **Pontos de Função (PF)**   | 16 PF (ver detalhamento abaixo)                                                                    |

---

## Detalhamento dos Pontos de Função (PF)

| Tipo de Função                        | Quantidade | PF por função | Total PF | Justificativa |
|---------------------------------------|------------|---------------|----------|---------------|
| Entradas Externas (EE)                | 2          | 3             | 6        | Cadastro/edição de entidade, importação em massa |
| Saídas Externas (SE)                  | 2          | 4             | 8        | Listagem paginada (JSON), resposta de importação |
| Consultas Externas (CE)               | 0          | 0             | 0        | —             |
| Arquivos Lógicos Internos (ALI)       | 1          | 1.5           | 1.5      | Tabela entidades_orcamentarias |
| Arquivos de Interface Externa (AIE)   | 0          | 0             | 0        | —             |
| **Total**                             | —          | —             | **15.5** |               |

### Critérios e Observações:
- **EE:** Cadastro/edição de entidade (formulário), importação em massa (API).
- **SE:** Listagem paginada (JSON para Vue), resposta de importação (JSON).
- **ALI:** Tabela principal do módulo.
- **AIE:** Não há integração com arquivos externos.

---

## Justificativa da Complexidade e Custo

- **Complexidade:**  
  - Classificada como Moderada. O módulo é um CRUD padrão com importação em massa, interface SPA, múltiplos filtros e validação robusta, mas sem integrações externas complexas.
  - Fatores: uso de Vue.js, API RESTful, importação em massa, múltiplos filtros, mas sem workflows assíncronos ou múltiplos relacionamentos.

- **Cálculo de PF:**  
  - O valor de 15,5 PF está dentro do padrão para módulos CRUD com importação, múltiplos filtros e interface SPA.
  - Módulos CRUD simples geralmente ficam entre 10-15 PF; módulos com integrações externas ou regras complexas podem passar de 20 PF.

- **Conversão para horas/custo:**  
  - Exemplo: 6 horas/PF → 15,5 PF × 6 = 93 horas.
  - Exemplo: R$ 100/PF → 15,5 PF × R$ 100 = R$ 1.550,00.
  - Ajuste conforme produtividade e custo/hora da equipe.

- **Resumo:**  
  - O valor está adequado ao escopo e robustez do módulo. Não há indícios de superdimensionamento ou subdimensionamento.
  - Recomenda-se revisão periódica caso o módulo evolua para incluir integrações externas, relatórios avançados ou regras de negócio adicionais.

--- 