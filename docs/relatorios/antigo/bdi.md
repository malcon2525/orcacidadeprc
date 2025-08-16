# Relatório do CRUD de BDI

## Resumo Técnico
- **Rotas Web**: 6 (index, listar, create, edit, update, destroy)
- **Rotas API**: 5 (listar, store, update, destroy)
- **Componentes Vue**: 1 (ListaBDI)
- **Views Blade**: 1 (index)
- **Models**: 1 (BDI)
- **Controllers**: 1 (BDIController)
- **Tabelas**: 1 (bdis)
- **Migrations**: 1 (08B_create_bdis_table)
- **Complexidade**: Alta (devido a cálculos, integrações e validações específicas)

## Pontos de Função Consumidos

| Ação                        | PF Declarado | Justificativa                                                                 |
|-----------------------------|--------------|------------------------------------------------------------------------------|
| Cadastro de BDI             | 2            | Cadastro com múltiplos campos, fórmulas e validações complexas               |
| Edição de BDI               | 2            | Atualização com cálculos, regras e integrações                               |
| Exclusão de BDI             | 1            | Exclusão lógica, com checagem de vínculos                                    |
| Listagem/pesquisa de BDI    | 1            | Filtros por entidade, tipo e paginação                                       |
| Cálculo dinâmico            | 1            | Cálculo automático de percentuais e valores                                  |
| **Total**                   | **7**        | Cobertura completa do ciclo de vida e lógica de cálculo                      |

## Objetivo
O módulo de BDI centraliza o cálculo, registro e gestão dos percentuais de BDI para diferentes entidades, tipos e insumos, garantindo padronização, rastreabilidade e integração com orçamentos e cotações.

## Regras de Negócio (Resumo)
- Cadastro e edição exigem validação de campos obrigatórios, fórmulas específicas e consistência de percentuais.
- Cálculo automático de BDI para serviços, materiais e equipamentos, com atualização em tempo real.
- Exclusão checa vínculos e mantém integridade referencial.

## Funcionalidades Principais
- Cadastro, edição, exclusão e consulta de BDI.
- Cálculo dinâmico de percentuais e valores, com interface responsiva.
- Filtros por entidade, tipo e insumo, paginação e feedback visual.
- Logs detalhados e tratamento de erros.

## Integrações
- Relacionamento com entidades orçamentárias, orçamentos, cotações e tipos de orçamento.

## Observações e Sugestões (PARA FÁBRICA)
- Possibilidade de implementar histórico de alterações, exportação de relatórios e validações adicionais.
*(VALIDAR NECESSIDADE COM EQUIPE)*

## Conclusão
O módulo de BDI entrega precisão, controle e integração ao processo orçamentário, com lógica de cálculo robusta e pronta para evoluções conforme as necessidades do negócio.
