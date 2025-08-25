# Relatório de Atividades — Módulo Consulta DER-PR

---

| Item                        | Descrição/Quantidade                                                                                 |
|-----------------------------|-----------------------------------------------------------------------------------------------------|
| **Nome do Módulo**          | Consulta DER-PR                                                                                      |
| **Objetivo**                | Permitir consulta e visualização das tabelas oficiais de preços do Departamento de Estradas de Rodagem do Paraná (DER-PR) |
| **Rotas Web**               | 3 rotas: index, buscarTabelas, buscarDados                                                          |
| **Rotas API**               | 3 rotas: buscarTabelas, buscarDados, exportarExcel                                                  |
| **Componentes Vue**         | 2 componentes: Index.vue, ModalTabelaDados.vue                                                      |
| **Views Blade**             | 1 view: index.blade.php                                                                             |
| **Controllers**             | 2 controllers: ConsultarDerprController (Web), ConsultarDerprController (API)                       |
| **Models**                  | 2 models relacionados: DerprComposicao, DerprComposicaoView                                         |
| **Services**                | Nenhum service específico                                                                             |
| **Tabelas no Banco (migrations)** | 1 tabela: derpr_composicoes                                                                          |
| **Complexidade**            | **Média** — Funcionalidades básicas: consulta, visualização, exportação e filtros                      |

---

## Detalhamento dos Pontos de Função (PF)

| Tipo de Função                        | Quantidade | PF por função | Total PF | Justificativa |
|---------------------------------------|------------|---------------|----------|---------------|
| Entradas Externas (EE)                | 2          | 3             | 6        | Filtros consulta, seleção tabela |
| Saídas Externas (SE)                  | 2          | 2             | 4        | Listagem paginada, exportação Excel |
| Consultas Externas (CE)               | 3          | 2             | 6        | Buscar tabelas, buscar dados, exportar dados |
| Arquivos Lógicos Internos (ALI)       | 1          | 2             | 2        | derpr_composicoes |
| Arquivos de Interface Externa (AIE)   | 0          | 1             | 0        | Nenhuma integração externa identificada |
| **Total**                             | **8**      |               | **18**   | **Módulo de média complexidade** |

### Critérios e Observações:

- **EE (6 PF):** 
  - Filtros de consulta (3 PF): Filtros por código e descrição com busca em tempo real
  - Seleção de tabela (3 PF): Processamento de data_base + desoneração com validações

- **SE (4 PF):** 
  - Listagem paginada com cálculos (2 PF): Exibe dados com cálculos automáticos (mão de obra, material/equipamento)
  - Exportação Excel (2 PF): Funcionalidade completa de exportação

- **CE (6 PF):** 
  - Buscar tabelas disponíveis (2 PF): Consulta com distinct e formatação complexa
  - Buscar dados da tabela (2 PF): Query complexa com cálculos SQL em tempo real
  - Exportar dados (2 PF): Processamento e formatação para Excel

- **ALI (2 PF):** 
  - derpr_composicoes (2 PF): Tabela principal com 12 campos e cálculos complexos

- **AIE (0 PF):** Não há integrações com sistemas externos

---

## Justificativa da Complexidade e Custo

- **Complexidade: MÉDIA**
  - **Funcionalidades básicas:** Consulta, visualização, exportação e filtros
  - **Interface padrão:** Modal de dados com paginação e filtros
  - **Integração simples:** Apenas com tabela derpr_composicoes
  - **Processamento básico:** Cálculos automáticos de valores (mão de obra, material/equipamento)
  - **Funcionalidades padrão:** Paginação, filtros, exportação

- **Cálculo de PF:**
  - **18 PF está adequado** para um módulo com esta funcionalidade
  - Comparado a módulos CRUD simples (8-15 PF), este possui funcionalidades básicas de consulta
  - Sem cálculos matemáticos complexos ou integrações avançadas
  - Interface Vue padrão sem componentes complexos

- **Conversão para horas/custo:**
  - **Fator de conversão:** 8 horas/PF e R$ 800/PF (conforme criterio_calculo_pf.md)
  - **Cálculo de horas:** 18 PF × 8 horas/PF = **144 horas**
  - **Cálculo de custo:** 18 PF × R$ 800/PF = **R$ 14.400,00**

- **Resumo:**
  - **O valor está adequado** ao escopo e funcionalidade do módulo
  - Funcionalidades básicas de consulta e visualização justificam a complexidade média
  - Sem service especializado ou cálculos complexos
  - Interface Vue padrão sem componentes avançados
  - **18 PF = 144 horas = R$ 14.400,00** é condizente com a funcionalidade implementada

---