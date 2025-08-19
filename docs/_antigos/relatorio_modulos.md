# Relatório de Módulos do Sistema

> **Observação:** Não foram considerados os menus da sessão "Principal" nem os menus "Usuários" e "Permissões".

---

| **#** | **Módulo**                  | **Descrição**                                                                                                                                                                                                                                    | **PF** |
|-------|-----------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-------|
| 1     | **Importar DER-PR**         | Importa tabelas oficiais do DER-PR a partir de arquivos PDF, processando e validando os dados para alimentar as tabelas do sistema. Permite importação de serviços gerais, insumos e lote de arquivos, com integração a scripts Python para conversão e validação de dados. | 10    |
| 2     | **Importar SINAPI**         | Importa tabelas do SINAPI a partir de arquivos Excel, processando e validando os dados para alimentar as tabelas do sistema. Permite importação de composições, insumos e mão de obra, com validação de estrutura e dados.                         | 8     |
| 3     | **DER-PR (Consulta)**       | Permite consultar, filtrar e exportar composições e preços do DER-PR. Inclui detalhamento de composições, cálculo dinâmico de transporte (DMT) por item, visualização de fórmulas, consumo e valores finais. Permite exportação de dados e integração com outros módulos. | 14    |
| 4     | **SINAPI-PR (Consulta)**    | Permite consultar, filtrar e exportar composições e preços do SINAPI-PR. Inclui detalhamento de composições com cálculo de percentagem de mão de obra, visualização de insumos e exportação de dados.                                                                       | 6     |
| 5     | **Tipos de Orçamentos**     | Permite cadastrar, editar e excluir tipos de orçamento, que serão utilizados como base para a estruturação dos orçamentos no sistema.                                                                                                            | 4     |
| 6     | **Estrutura de Orçamentos** | Permite cadastrar e gerenciar a estrutura dos orçamentos, incluindo grandes itens e subgrupos. Suporta importação de estruturas via Excel e organização hierárquica dos componentes do orçamento.                                                 | 8     |
| 7     | **BDI**                     | Permite cadastrar e calcular o BDI (Benefícios e Despesas Indiretas) para cada entidade orçamentária, incluindo fórmulas, parâmetros e simulações.                                                                                                | 6     |
| 8     | **Cotações**                | Permite cadastrar, editar e consultar cotações de preços de insumos e serviços, que serão utilizadas nos orçamentos e composições.                                                                         | 6     |
| 9     | **Composições Próprias**    | Permite cadastrar composições personalizadas com integração a serviços SINAPI e DER-PR via campo zoom, cálculos automáticos de valores, múltiplos itens por composição e estrutura complexa de validações.                                                                  | 10    |
| 10    | **DMT - Fórmulas**          | Permite cadastrar e gerenciar as fórmulas do DMT (Distância Média de Transporte) para cada tipo de transporte, incluindo coeficientes e parâmetros utilizados nos cálculos de transporte.                    | 5     |
| 11    | **DMT PRC**                 | Permite cadastrar o DMT padrão (PRC) que poderá ser utilizado como referência para os DMTs de cada entidade orçamentária, facilitando padronização e reaproveitamento de dados.                             | 4     |
| 12    | **DMT**                     | Permite cadastrar o DMT específico para cada entidade orçamentária, com possibilidade de customização dos parâmetros e fórmulas de transporte.                                                             | 5     |
| 13    | **Municípios**              | Permite importar e gerenciar municípios, que poderão ser associados a entidades orçamentárias e orçamentos.                                                                                               | 4     |
| 14    | **Entidades Orçamentárias** | Permite cadastrar e gerenciar entidades orçamentárias, que são responsáveis pela criação e gestão dos orçamentos no sistema.                                                                               | 5     |

---

## Tabela Técnica dos Módulos

| **Módulo**                | **Rotas Web** | **Rotas API** | **Componentes Vue** | **Views Blade** | **Models** | **Controllers** | **Tabelas** | **Migrations** | **Complexidade** |
|---------------------------|--------------|--------------|--------------------|----------------|------------|-----------------|-------------|----------------|------------------|
| Importar DER-PR           | 4            | 0            | 4                  | 1              | 7          | 1               | 7           | 7              | **Muito Alta**   |
| Importar SINAPI           | 8            | 0            | 4                  | 1              | 4          | 1               | 4           | 4              | **Alta**         |
| DER-PR (Consulta)         | 3            | 3            | 2                  | 1              | 1          | 1               | 1           | 1              | **Muito Alta**   |
| SINAPI-PR (Consulta)      | 5            | 2            | 2                  | 1              | 1          | 1               | 1           | 1              | **Baixa**        |
| Tipos de Orçamentos       | 6            | 2            | 1                  | 1              | 1          | 1               | 1           | 1              | **Baixa**        |
| Estrutura de Orçamentos   | 8            | 2            | 2                  | 2              | 2          | 2               | 2           | 2              | **Média**        |
| BDI                       | 6            | 2            | 1                  | 1              | 1          | 1               | 1           | 1              | **Média**        |
| Cotações                  | 5            | 4            | 1                  | 1              | 1          | 1               | 1           | 1              | **Média**        |
| Composições Próprias      | 6            | 2            | 1                  | 1              | 1          | 1               | 1           | 1              | **Alta**         |
| DMT - Fórmulas            | 3            | 2            | 1                  | 1              | 2          | 1               | 2           | 2              | **Média**        |
| DMT PRC                   | 3            | 2            | 1                  | 1              | 1          | 1               | 1           | 1              | **Baixa**        |
| DMT                       | 3            | 2            | 1                  | 1              | 1          | 1               | 1           | 1              | **Alta**         |
| Municípios                | 6            | 5            | 1                  | 1              | 1          | 1               | 1           | 1              | **Baixa**        |
| Entidades Orçamentárias   | 9            | 6            | 1                  | 1              | 1          | 1               | 1           | 1              | **Baixa**        |
| **Total**                 | **75**       | **34**       | **22**             | **15**         | **25**     | **15**          | **25**      | **25**         |                  |

---

## Critérios de Complexidade

### **Muito Alta**
- **Importar DER-PR**: Processamento de PDF, scripts Python, múltiplas tabelas, validações complexas
- **DER-PR (Consulta)**: Cálculos matemáticos em tempo real, modal complexo, integração com transporte

### **Alta**
- **Importar SINAPI**: Processamento de Excel, validações de estrutura, múltiplas tabelas
- **Composições Próprias**: Campo zoom complexo, integração com SINAPI/DER-PR, cálculos automáticos, múltiplos itens
- **DMT**: Geração em lote, atualização em lote, relacionamentos complexos, integração com cálculos

### **Média**
- **Estrutura de Orçamentos**: CRUD hierárquico, importação de Excel
- **BDI**: Cálculos matemáticos, fórmulas
- **Cotações**: CRUD com relacionamentos
- **DMT - Fórmulas**: CRUD com duas tabelas relacionadas, fórmulas matemáticas

### **Baixa**
- **SINAPI-PR (Consulta)**: Consulta simples com JOIN, exportação de dados
- **Tipos de Orçamentos**: CRUD simples
- **DMT PRC**: CRUD simples
- **Municípios**: CRUD com importação básica
- **Entidades Orçamentárias**: CRUD simples

---

|   |   |   |   |
|---|---|---|---|
|   |   | **Total de Pontos de Função (PF):** | **97** |

--- 