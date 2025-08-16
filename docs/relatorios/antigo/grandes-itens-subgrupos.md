# Relatório do CRUD de Grandes Itens e Subgrupos

## Resumo Técnico
- **Rotas Web**: 13 (6 para grandes itens, 7 para subgrupos)
- **Rotas API**: 10 (5 para cada recurso)
- **Componentes Vue**: 2 (ListaGrandesItens, ListaSubGrupos)
- **Views Blade**: 2 (grandes-itens/index, sub-grupos/index)
- **Models**: 2 (GrandeItem, SubGrupo)
- **Controllers**: 2 (GrandeItemController, SubGrupoController)
- **Tabelas**: 2 (grandes_itens, sub_grupos)
- **Migrations**: 2 (07A_create_grandes_itens_table, 07B_create_sub_grupos_table)
- **Complexidade**: Alta (devido à hierarquia e ordenação)

## Pontos de Função Consumidos

| Ação                        | PF Declarado | Justificativa                                                                 |
|-----------------------------|--------------|------------------------------------------------------------------------------|
| Cadastro de grande item     | 1            | Cadastro com validações e vínculo com tipo de orçamento                      |
| Edição de grande item       | 1            | Atualização com validações e vínculo                                         |
| Exclusão de grande item     | 1            | Exclusão em cascata com subgrupos                                            |
| Listagem/pesquisa de grandes itens | 1     | Filtros por tipo, descrição e paginação                                      |
| Cadastro de subgrupo        | 1            | Cadastro com vínculo obrigatório ao grande item                               |
| Edição de subgrupo          | 1            | Atualização com validações e vínculo                                         |
| Exclusão de subgrupo        | 1            | Exclusão lógica, com checagem de vínculos                                    |
| Listagem/pesquisa de subgrupos | 1        | Filtros por descrição, ordenação e paginação                                 |
| **Total**                   | **8**        | Cobertura completa do ciclo de vida e hierarquia                             |

## Objetivo
O módulo de Grandes Itens e Subgrupos estrutura categorias hierárquicas para orçamentos e cotações, permitindo organização, ordenação e integração entre módulos.

## Regras de Negócio (Resumo)
- Cada grande item pertence a um tipo de orçamento; cada subgrupo pertence a um grande item.
- Exclusão em cascata, validações de formato e ordenação dinâmica.
- Contagem de subgrupos por grande item e histórico de alterações.

## Funcionalidades Principais
- Cadastro, edição, exclusão e consulta de grandes itens e subgrupos.
- Filtros por tipo, descrição e ordenação, paginação e interface responsiva.
- Validações em tempo real e feedback visual.

## Integrações
- Relacionamento com tipos de orçamento, orçamentos, cotações e BDI.

## Observações e Sugestões (PARA FÁBRICA)
- Possibilidade de implementar drag-and-drop, visualização em árvore e cache de hierarquia.
*(VALIDAR NECESSIDADE COM EQUIPE)*

## Conclusão
O módulo de Grandes Itens e Subgrupos garante organização, flexibilidade e integração hierárquica, fortalecendo a estruturação dos dados orçamentários e de cotações.

---

> **Este relatório pode ser lapidado/adaptado conforme evolução do sistema ou feedback do usuário.** 