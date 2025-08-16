# Relatório de Atividades — Módulo Custos de Transporte

---

| Item                        | Descrição/Quantidade                                                                                 |
|-----------------------------|-----------------------------------------------------------------------------------------------------|
| **Nome do Módulo**          | Custos de Transporte                                                                                |
| **Objetivo**                | Gerenciar custos e coeficientes de transporte utilizados no sistema de orçamentação, permitindo cadastro, consulta, edição, exclusão e importação em lote via Excel, garantindo rastreabilidade, validação robusta e aderência às regras de negócio do domínio de transportes. |
| **Rotas Web**               | 8 — Listagem, criação, edição, exclusão, importação, consulta de coeficientes, salvamento em lote e página de gerenciamento |
| **Rotas API**               | 0 — Todas as rotas são web, mas retornam JSON para integração frontend                              |
| **Componentes Vue**         | 1 — CustoTransporteCrud.vue                                                                         |
| **Views Blade**             | 1 — transporte/custos.blade.php                                                                     |
| **Controllers**             | 3 — CustoTransporteController, CoeficienteCustoTransporteController, CustoTransportePageController |
| **Models**                  | 2 — CustoTransporte, CoeficienteCustoTransporte                                                    |
| **Tabelas no Banco (migrations)** | 2 — custo_transporte, coeficiente_custo_transporte                                           |

---

## Detalhamento dos Pontos de Função (PF)

| Tipo de Função                        | Quantidade | PF por função | Total PF | Justificativa                                                                 |
|---------------------------------------|------------|---------------|----------|-------------------------------------------------------------------------------|
| Entradas Externas (EE)                | 4          | 3             | 12       | CRUD de custos, importação Excel, edição inline de coeficientes, salvamento em lote |
| Saídas Externas (SE)                  | 2          | 2             | 4        | Exclusão de custos com cascade, listagem com filtros complexos               |
| Consultas Externas (CE)               | 3          | 2             | 6        | Busca de custos, coeficientes por data/desoneração, fórmulas de transporte   |
| Arquivos Lógicos Internos (ALI)       | 2          | 2             | 4        | Tabelas custo_transporte e coeficiente_custo_transporte com relacionamentos  |
| Arquivos de Interface Externa (AIE)   | 1          | 1             | 1        | Integração com tabela derpr_transportes para fórmulas                        |
| **Total**                             |            |               | **27**   |                                                                               |

### Critérios e Observações:
- **EE:** Consideradas as telas de CRUD, importação Excel com validação complexa, edição inline de coeficientes e salvamento em lote, todas com múltiplos campos e validações rigorosas.
- **SE:** Exclusão de custos com verificação de relacionamentos e listagem com filtros por data base e desoneração.
- **CE:** Consulta de custos, busca de coeficientes por critérios múltiplos e obtenção de fórmulas de transporte.
- **ALI:** Tabelas `custo_transporte` e `coeficiente_custo_transporte` armazenam dados principais com relacionamentos e constraints.
- **AIE:** Integração com tabela `derpr_transportes` para fórmulas de transporte local e comercial.

**Complexidade:** Alta — Devido a cálculos matemáticos complexos, importação robusta, validações rigorosas, mapeamento de campos, logs detalhados e integração com sistema de orçamentação.

**Total de Pontos de Função:** 27 PF

---

## Justificativa da Complexidade e Custo

- **Complexidade:**
  - Classificado como Alta devido à necessidade de cálculos matemáticos complexos (fórmulas de transporte), importação robusta via Excel com validação transacional, mapeamento de campos entre frontend e backend, logs detalhados, validações rigorosas de dados e integração com sistema de orçamentação.
  - O módulo exige lógica de negócio sofisticada para garantir precisão dos cálculos, integridade dos dados e rastreabilidade completa das operações.

- **Cálculo de PF:**
  - O valor de 27 PF está adequado para módulos com cálculos complexos, importação robusta e múltiplas integrações.
  - Módulos mais simples (CRUD puro) ficam entre 3 e 5 PF; módulos com importação e cálculos podem superar 20 PF.
  - A complexidade da importação Excel, validação transacional e mapeamento de campos justifica o valor elevado.

- **Conversão para horas/custo:**
  - Fator de conversão: 8 horas por PF; custo: R$ 800 por PF.
  - Cálculo: 27 PF × 8h = 216 horas; 27 PF × R$ 800 = R$ 21.600,00.
  - Valores podem ser ajustados conforme produtividade e custo/hora da equipe.

- **Resumo:**
  - O valor está adequado ao escopo e robustez do módulo, considerando a complexidade matemática, importação robusta e integrações.
  - Não há indícios de superdimensionamento, considerando as funcionalidades avançadas implementadas.
  - O cálculo é transparente e justificado, seguindo a metodologia de Pontos de Função.

---

## Observações e Sugestões
- O módulo está bem estruturado, com lógica de cálculo robusta, importação segura e interface intuitiva.
- Possui potencial para evoluções como histórico de alterações, exportação de relatórios, validações adicionais e integração com mais sistemas externos.
- Recomenda-se testes automatizados para garantir a precisão dos cálculos, validação da importação e integridade dos dados.
- O Service `DerprTransporteService` demonstra boa arquitetura, centralizando lógica complexa e facilitando manutenção.

---

## Conclusão
O módulo de Custos de Transporte entrega precisão matemática, controle robusto de dados e integração eficiente ao processo orçamentário, com lógica de cálculo sofisticada, importação segura e pronta para evoluções conforme as necessidades do negócio. A arquitetura bem estruturada com Service dedicado garante manutenibilidade e escalabilidade. 