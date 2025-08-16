# Relatório de Atividades — Módulo Consulta DER-PR

---

| Item                        | Descrição/Quantidade                                                                                 |
|-----------------------------|-----------------------------------------------------------------------------------------------------|
| **Nome do Módulo**          | Consulta DER-PR                                                                                      |
| **Objetivo**                | Permitir consulta e visualização das tabelas oficiais de preços do Departamento de Estradas de Rodagem do Paraná (DER-PR), incluindo funcionalidade de cálculo de transporte com múltiplas fórmulas |
| **Rotas Web**               | 3 rotas: index, buscarTabelas, buscarDados                                                          |
| **Rotas API**               | 4 rotas: obterFormulas, calcular, itensPorComposicao, zoomServicos                                  |
| **Componentes Vue**         | 2 componentes: ConsultarDERPRComponent.vue (674 linhas), ModalCalculoTransporte.vue (298 linhas)    |
| **Views Blade**             | 1 view: index.blade.php                                                                             |
| **Controllers**             | 2 controllers: ConsultarDERPRController (127 linhas), DerprTransporteController (80 linhas)         |
| **Models**                  | 2 models relacionados: DerprComposicao, DerprComposicaoView                                         |
| **Services**                | 1 service: DerprTransporteService (202 linhas) com 4 métodos complexos                              |
| **Tabelas no Banco (migrations)** | 4 tabelas: derpr_composicoes, derpr_transportes, custo_transporte, coeficiente_custo_transporte |
| **Complexidade**            | **Alta** — Funcionalidades avançadas: cálculos matemáticos de transporte, múltiplas fórmulas, modal complexo, integração entre 4 tabelas |

---

## Detalhamento dos Pontos de Função (PF)

| Tipo de Função                        | Quantidade | PF por função | Total PF | Justificativa |
|---------------------------------------|------------|---------------|----------|---------------|
| Entradas Externas (EE)                | 3          | 3             | 9        | Modal transporte (complexo), filtros consulta, seleção tabela |
| Saídas Externas (SE)                  | 4          | 2             | 8        | Listagem paginada, exportação Excel, modal resultado, zoom servicos |
| Consultas Externas (CE)               | 5          | 2             | 10       | Buscar tabelas, buscar dados, formulas transporte, itens transporte, zoom |
| Arquivos Lógicos Internos (ALI)       | 4          | 2             | 8        | derpr_composicoes, derpr_transportes, custo_transporte, coeficiente_custo_transporte |
| Arquivos de Interface Externa (AIE)   | 0          | 1             | 0        | Nenhuma integração externa identificada |
| **Total**                             | **16**     |               | **35**   | **Módulo de alta complexidade** |

### Critérios e Observações:

- **EE (9 PF):** 
  - Modal de cálculo de transporte (3 PF): Interface complexa com múltiplos tipos de cálculo, validações, fórmulas matemáticas
  - Filtros de consulta (3 PF): Filtros por código e descrição com busca em tempo real
  - Seleção de tabela (3 PF): Processamento de data_base + desoneração com validações

- **SE (8 PF):** 
  - Listagem paginada com cálculos (2 PF): Exibe dados com cálculos automáticos (mão de obra, material/equipamento)
  - Exportação Excel (2 PF): Funcionalidade completa de exportação
  - Modal resultado transporte (2 PF): Apresentação complexa com múltiplos valores calculados
  - Zoom de serviços paginado (2 PF): Busca rápida com paginação para seleção

- **CE (10 PF):** 
  - Buscar tabelas disponíveis (2 PF): Consulta com distinct e formatação complexa
  - Buscar dados da tabela (2 PF): Query complexa com cálculos SQL em tempo real
  - Obter fórmulas de transporte (2 PF): Consulta em 3 tabelas com relacionamentos complexos
  - Itens de transporte por composição (2 PF): Consulta específica com múltiplas condições
  - Zoom de serviços (2 PF): Busca paginada com filtros e relacionamentos

- **ALI (8 PF):** 
  - derpr_composicoes (2 PF): Tabela principal com 12 campos e cálculos complexos
  - derpr_transportes (2 PF): Tabela de transporte com fórmulas e relacionamentos
  - custo_transporte (2 PF): Tabela de configuração de tipos de transporte
  - coeficiente_custo_transporte (2 PF): Tabela de coeficientes com relacionamentos

- **AIE (0 PF):** Não há integrações com sistemas externos

---

## Justificativa da Complexidade e Custo

- **Complexidade: ALTA**
  - **Cálculos matemáticos complexos:** Service com 4 métodos para processamento de fórmulas de transporte
  - **Interface sofisticada:** Modal com acordeão, múltiplos tipos de cálculo, validações em tempo real
  - **Múltiplas integrações de dados:** 4 tabelas inter-relacionadas com consultas complexas
  - **Processamento em tempo real:** Cálculos automáticos de valores, extração de coeficientes de fórmulas
  - **Funcionalidades avançadas:** Paginação, filtros, exportação, zoom de serviços

- **Cálculo de PF:**
  - **35 PF está adequado** para um módulo com esta robustez técnica
  - Comparado a módulos CRUD simples (8-15 PF), este possui funcionalidades muito mais complexas
  - O service DerprTransporteService sozinho justifica a alta complexidade
  - Modal de transporte é uma funcionalidade única no sistema

- **Conversão para horas/custo:**
  - **Fator de conversão:** 8 horas/PF e R$ 800/PF (conforme criterio_calculo_pf.md)
  - **Cálculo de horas:** 35 PF × 8 horas/PF = **280 horas**
  - **Cálculo de custo:** 35 PF × R$ 800/PF = **R$ 28.000,00**

- **Resumo:**
  - **O valor está adequado** ao escopo e robustez do módulo
  - Funcionalidades de cálculo matemático e modal complexo justificam a alta pontuação
  - Service especializado com 4 métodos complexos adiciona valor significativo
  - Interface Vue com 674 linhas no componente principal indica alta complexidade de desenvolvimento
  - **35 PF = 280 horas = R$ 28.000,00** é condizente com a sofisticação técnica implementada

---