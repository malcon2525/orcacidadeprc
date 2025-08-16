# Consolidação Geral dos Relatórios de Módulos

---

## Objetivo de Cada Módulo

| Módulo                                      | Objetivo resumido                                                        |
|----------------------------------------------|--------------------------------------------------------------------------|
| Consulta de Tabela SINAPI                    | Consulta, filtro, exportação de composições SINAPI                      |
| Importação SINAPI                           | Importação, validação, processamento de tabelas SINAPI                  |
| Importação DER-PR                           | Importação, validação, processamento de tabelas DER-PR                  |
| Entidades Orçamentárias                     | Cadastro, consulta, importação de entidades orçamentárias               |
| Municípios                                  | Cadastro, consulta, importação de municípios                            |
| Consulta DER-PR                             | Consulta e visualização das tabelas oficiais DER-PR, incluindo cálculo de transporte com múltiplas fórmulas |
| Tipos de Orçamento                          | Cadastro e gestão dos tipos de orçamento usados como referência         |
| Estrutura de Orçamento (Grandes Itens e Subgrupos) | Cadastro, importação, exportação e organização da estrutura do orçamento |
| BDI                                         | Cálculo, registro e gestão dos percentuais de BDI para entidades/orçamentos |
| Cotações                                    | Gerenciamento de cotações de preços de insumos/serviços, múltiplos fornecedores, upload de propostas, cálculo automático/manual do valor final |
| Composições Próprias                        | Cadastro, edição, consulta e exclusão de composições orçamentárias personalizadas, com múltiplos itens, podendo ser baseadas em referências oficiais (SINAPI, DERPR) ou personalizadas |
| Custos de Transporte                        | Gerenciar custos e coeficientes de transporte utilizados no sistema de orçamentação, permitindo cadastro, consulta, edição, exclusão e importação em lote via Excel, garantindo rastreabilidade, validação robusta e aderência às regras de negócio do domínio de transportes |
| DMT PRC                                     | Gerenciar materiais de transporte padrão (PRC) que servem como referência para os DMTs de cada entidade orçamentária, facilitando padronização e reaproveitamento de dados |
| DMT                                         | Gerenciar dados de materiais de transporte específicos por entidade orçamentária, permitindo edição de distâncias (X1 e X2) e informações de origem para cálculo de custos de transporte |
| **Total de módulos**                        | **14**                                                                  |

---

## Tabela Comparativa dos Módulos (Resumo)

| Módulo                                      | PF   | Complexidade | Custo Estimado* |
|----------------------------------------------|------|--------------|-----------------|
| Consulta de Tabela SINAPI                    | 16   | Moderada     | R$ 12.800,00    |
| Importação SINAPI                           | 65   | Muito Alta   | R$ 52.000,00    |
| Importação DER-PR                           | 34   | Alta         | R$ 27.200,00    |
| Entidades Orçamentárias                     | 15.5 | Moderada     | R$ 12.400,00    |
| Municípios                                  | 14.5 | Baixa/Mod.   | R$ 11.600,00    |
| Consulta DER-PR                             | 35   | Alta         | R$ 28.000,00    |
| Tipos de Orçamento                          | 13   | Baixa        | R$ 10.400,00    |
| Estrutura de Orçamento (Grandes Itens e Subgrupos) | 28   | Moderada     | R$ 22.400,00    |
| BDI                                         | 7    | Moderada/Alta| R$ 5.600,00     |
| Cotações                                    | 50   | Alta         | R$ 40.000,00    |
| Composições Próprias                        | 52   | Muito Alta   | R$ 41.600,00    |
| Custos de Transporte                        | 27   | Alta         | R$ 21.600,00    |
| DMT PRC                                     | 10   | Moderada     | R$ 8.000,00     |
| DMT                                         | 22   | Moderada     | R$ 17.600,00    |
| **Total (14 módulos)**                      | **362** |              | **R$ 289.600,00** |

*Considerando R$ 800/PF como referência. Ajuste conforme política do projeto.

---

## Detalhes Técnicos por Módulo

| Módulo                                      | Rotas Web/API         | Componentes Vue <br> Blade | Models/Controllers /Tabelas         |
|----------------------------------------------|-----------------------|----------------------|------------------------------------|
| Consulta de Tabela SINAPI                    | Web: 4                | Vue: 1<br> Blade: 1    | Models: 2 - Ctrl: 1 <br> Tabelas: 2+views |
| Importação SINAPI                           | Web: 6 <br> API: 1       | Vue: 4 <br> Blade: 1    | Models: 5 - Ctrl: 1 <br> Tabelas: 6       |
| Importação DER-PR                           | Web: 4                | Vue: 3 <br> Blade: 1    | Models: 7 - Ctrl: 1 <br> Tabelas: 8       |
| Entidades Orçamentárias                     | Web: 8 <br> API: 6       | Vue: 1 <br> Blade: 1    | Models: 1 - Ctrl: 1 <br> Tabelas: 1       |
| Municípios                                  | Web: 6 <br> API: 5       | Vue: 1 <br> Blade: 1    | Models: 1 - Ctrl: 1 <br> Tabelas: 1       |
| Consulta DER-PR                             | Web: 3 <br> API: 4       | Vue: 2 <br> Blade: 1    | Models: 2 - Ctrl: 2 - Services: 1 <br> Tabelas: 4 |
| Tipos de Orçamento                          | Web: 7                | Vue: 1 <br> Blade: 1    | Models: 1 - Ctrl: 1 <br> Tabelas: 1       |
| Estrutura de Orçamento (Grandes Itens e Subgrupos) | Web: 18               | Vue: 2 <br> Blade: 2    | Models: 2  Ctrl: 2 <br> Tabelas: 2       |
| BDI                                         | Web: 6                | Vue: 1 <br> Blade: 1    | Models: 1 - Ctrl: 1 <br> Tabelas: 1      |
| Cotações                                    | Web: 1 <br> API: 7     | Vue: 1 <br> Blade: 1    | Models: 3 - Ctrl: 2 <br> Tabelas: 3      |
| Composições Próprias                        | Web: 9 <br> API: 2     | Vue: 3 <br> Blade: 1    | Models: 2 - Ctrl: 3 <br> Tabelas: 2      |
| Custos de Transporte                        | Web: 9 <br> API: 2     | Vue: 3 <br> Blade: 1    | Models: 2 - Ctrl: 3 <br> Tabelas: 2      |
| DMT PRC                                     | Web: 6                | Vue: 1 <br> Blade: 1    | Models: 1 - Ctrl: 1 <br> Tabelas: 1       |
| DMT                                         | Web: 7 <br> API: 1     | Vue: 1 <br> Blade: 1    | Models: 1 - Ctrl: 1 <br> Tabelas: 1       |
| **Totais**                                  | **Web: 94 <br> API: 26** | **Vue: 23 <br> Blade: 14** | **Models: 30 - Ctrl: 18 - Services: 1 <br> Tabelas: 34+** |

---

## Pontos de Função (PF) — Consolidação

- **Total de Pontos de Função (PF):** 362
- **Total estimado de horas (8h/PF):** 2.896h
- **Total estimado de custo (R$ 800/PF):** R$ 289.600,00

---
