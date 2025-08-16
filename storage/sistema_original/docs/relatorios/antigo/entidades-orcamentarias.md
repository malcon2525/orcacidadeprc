# Relatório do CRUD de Entidades Orçamentárias

## Resumo Técnico
- **Rotas Web**: 6 (index, listar, listar-select, create, edit, destroy)
- **Rotas API**: 5 (listar, listar-select, store, update, destroy)
- **Componentes Vue**: 1 (ListaEntidadesOrcamentarias)
- **Views Blade**: 1 (index)
- **Models**: 1 (EntidadeOrcamentaria)
- **Controllers**: 1 (EntidadeOrcamentariaController)
- **Tabelas**: 1 (entidades_orcamentarias)
- **Migrations**: 1 (06B_create_entidades_orcamentarias_table)
- **Complexidade**: Média (devido às validações e importação)

## Pontos de Função Consumidos

| Ação                        | PF Declarado | Justificativa                                                                 |
|-----------------------------|--------------|------------------------------------------------------------------------------|
| Cadastro de entidade        | 1            | Cadastro com validações e unicidade de campos                                |
| Edição de entidade          | 1            | Atualização com validações e integridade                                     |
| Exclusão de entidade        | 1            | Exclusão lógica, com checagem de vínculos                                    |
| Listagem/pesquisa de entidades | 1         | Filtros por razão social, nome fantasia e tipo                               |
| Importação em lote          | 2            | Integração com PostgreSQL, validações e transação                            |
| **Total**                   | **6**        | Cobertura completa do ciclo de vida e integração externa                     |

## Objetivo
O módulo de Entidades Orçamentárias centraliza o cadastro, consulta e gestão de entidades, permitindo importação em lote de dados externos e garantindo integridade e padronização das informações institucionais.

## Regras de Negócio (Resumo)
- Cadastro e edição exigem validação de campos obrigatórios, unicidade de razão social, nome fantasia, email, código IBGE e CNPJ.
- Importação em lote com verificação de duplicidade, mapeamento de campos e tratamento de erros.
- Exclusão de entidade checa vínculos e mantém integridade referencial.

## Funcionalidades Principais
- Cadastro, edição, exclusão e consulta de entidades orçamentárias.
- Importação em lote de municípios do PostgreSQL.
- Filtros por razão social, nome fantasia e tipo de organização.
- Paginação, validações em tempo real e interface responsiva.

## Integrações
- Integração com PostgreSQL para importação.
- Relacionamento com orçamentos, cotações e BDI.

## Observações e Sugestões (PARA FÁBRICA)
- Possibilidade de implementar validação de formato de CNPJ, máscaras para telefone/CEP e histórico de importações.
*(VALIDAR NECESSIDADE COM EQUIPE)*

## Conclusão
O módulo de Entidades Orçamentárias garante controle, padronização e integração institucional, com recursos de importação e validação que fortalecem a qualidade dos dados no sistema.

---

> **Este relatório pode ser lapidado/adaptado conforme evolução do sistema ou feedback do usuário.** 