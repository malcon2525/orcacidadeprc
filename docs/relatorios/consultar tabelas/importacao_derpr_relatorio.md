# Relatório de Atividades — Módulo: Importação DER-PR

| Item                        | Descrição/Quantidade                                                                                 |
|-----------------------------|-----------------------------------------------------------------------------------------------------|
| **Nome do Módulo**          | Importação DER-PR                                                                                   |
| **Objetivo**                | Permitir a importação, validação e processamento de tabelas oficiais do DER-PR (PDF), convertendo para dados estruturados e alimentando o banco de dados do sistema, com logs centralizados e interface amigável. |
| **Rotas Web**               | 4 (index, processarServicosGerais, processarInsumos, importarLote)                                  |
| **Rotas API**               | 0 (não há rotas API específicas para este módulo)                                                   |
| **Componentes Vue**         | 3 (ServicosGerais.vue, Insumos.vue, ImportarLoteDerpr.vue)                                         |
| **Views Blade**             | 1 (importar_tabelas/derpr.blade.php)                                                                |
| **Controllers**             | 1 (ImportarDERPRController.php)                                                                     |
| **Models**                  | 7 (DerprComposicao, DerprMaoDeObra, DerprEquipamento, DerprMaterial, DerprServico, DerprItemIncidencia, DerprTransporte) |
| **Tabelas no Banco (migrations)** | 7 principais (derpr_composicoes, derpr_equipamentos, derpr_mao_de_obra, derpr_materiais, derpr_servicos, derpr_itens_incidencia, derpr_transportes) + 1 view materializada (derpr_composicoes_view) |
| **Complexidade**            | Alta (integração PHP+Python, múltiplos formatos, validação, logs, atualização seletiva de view, interface rica) |
| **Pontos de Função (PF)**   | 34 (ver detalhamento e justificativas abaixo)                                                                        |

---

## Detalhamento dos Pontos de Função (PF)

| Tipo de Função                        | Quantidade | PF por função | Total PF | Justificativa |
|---------------------------------------|------------|---------------|----------|---------------|
| Entradas Externas (EE)                | 3 (upload Serviços, Insumos, Lote) | 4             | 12       | Cada upload envolve validação, integração Python, logs e processamento distinto. |
| Saídas Externas (SE)                  | 2 (feedback de importação, exportação Excel) | 5             | 10       | Feedback detalhado (sucesso/erro/logs) e exportação estruturada para Excel. |
| Consultas Externas (CE)               | 1 (consulta dados importados/logs) | 4             | 4        | Exibição tabular dos dados importados, com validação e logs. |
| Arquivos Lógicos Internos (ALI)       | 8 (7 tabelas + 1 view)             | 1             | 8        | Cada tabela/modelo persistente e a view materializada são ALIs distintos. |
| Arquivos de Interface Externa (AIE)   | 0                                  | 0             | 0        | Não há integração com sistemas externos. |
| **Total**                             |            |               | **34**   |               |

### Critérios e Observações:
- **EE:** Cada upload/processamento é uma entrada distinta, com regras e validações próprias.
- **SE:** Feedback detalhado e exportação são saídas relevantes e exigem lógica de negócio.
- **CE:** Consulta dos dados importados é relevante para o usuário e auditoria.
- **ALI:** Cada tabela/modelo persistente e a view são arquivos lógicos internos.
- **AIE:** Não se aplica neste módulo.

---

## Justificativa da Complexidade e Custo

- **Complexidade Alta:**
  - O módulo não é um CRUD simples. Envolve integração entre PHP e Python, múltiplos formatos de entrada, validação pesada, logs detalhados, atualização seletiva de view materializada, e interface rica em feedback.
  - A atualização da view é seletiva (por data_base/desoneracao), exigindo lógica adicional.
  - O sistema garante rastreabilidade e auditoria, o que aumenta o esforço de desenvolvimento.

- **Cálculo de PF:**
  - O valor de 34 PF está dentro do padrão para um módulo de importação robusto, com múltiplas tabelas, integrações e funcionalidades.
  - Se fosse um CRUD simples, teria entre 10 e 15 PF.

- **Conversão para horas/custo:**
  - O mercado utiliza entre 8 e 12 horas por PF para sistemas desse porte. Usando 10h como média:
    - 34 PF × 10h = **340 horas**
  - O custo médio de mercado é de R$ 800 a R$ 1.200 por PF. Usando R$ 1.000 como referência:
    - 34 PF × R$ 1.000 = **R$ 34.000**
  - Esses valores podem ser ajustados conforme a produtividade e custo/hora da equipe.

- **Resumo:**
  - O valor não está superestimado para o escopo e robustez do módulo.
  - Se o orçamento for alto para sua realidade, pode-se ajustar o valor de PF/hora ou revisar funcionalidades.
  - O cálculo é transparente e justificado, seguindo a metodologia de Pontos de Função.

--- 