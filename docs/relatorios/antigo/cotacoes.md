# Relatório do CRUD de Cotações

## Resumo Técnico
- **Rotas Web**: 1 (index)
- **Rotas API**: 4 (index, store, update, destroy)
- **Componentes Vue**: 1 (ListaCotacoes)
- **Views Blade**: 1 (index)
- **Models**: 3 (Cotacao, CotacaoFornecedor, Fornecedor)
- **Controllers**: 1 (CotacaoController)
- **Tabelas**: 3 (cotacoes, cotacao_fornecedores, fornecedores)
- **Migrations**: 3 (create_cotacoes_table, create_cotacao_fornecedores_table, create_fornecedores_table)
- **Complexidade**: Alta (devido ao gerenciamento de arquivos, múltiplos fornecedores e campo zoom)

## Pontos de Função Consumidos

| Ação                        | PF Declarado | Justificativa                                                                 |
|-----------------------------|--------------|------------------------------------------------------------------------------|
| Cadastro de cotação         | 2            | Cadastro complexo com múltiplos fornecedores e arquivo                       |
| Edição de cotação           | 2            | Semelhante ao cadastro, com atualizações de múltiplos vínculos               |
| Exclusão de cotação         | 1            | Exclusão lógica/física de arquivos, mas não exige PF extra                   |
| Listagem/pesquisa de cotações | 1          | Com múltiplos filtros e paginação                                            |
| Campo zoom de fornecedor    | 1            | Integração dinâmica via modal e busca/cadastro inline                        |
| Download/exclusão de arquivo| 1            | Operações específicas e tratamento de erro                                   |
| **Total**                   | **8**        | Completo e compatível com regras de APF                                      |

## Objetivo
O módulo de Cotações centraliza e padroniza o processo de registro, consulta e gestão de cotações de preços, garantindo agilidade, rastreabilidade e segurança na comparação de propostas de fornecedores para itens orçamentários.

## Regras de Negócio (Resumo)
- Cada cotação exige exatamente 3 fornecedores, com cadastro global e busca/cadastro rápido via campo zoom.
- Permite upload, download e exclusão física de arquivos PDF por fornecedor, com organização automática e preservação ao editar.
- Validações automáticas garantem integridade dos dados e feedback imediato ao usuário.
- Cálculo automático do valor final da cotação (média, mediana, menor valor ou manual).
- Exclusão de cotação remove todos os vínculos e arquivos relacionados.

## Funcionalidades Principais
- Cadastro, edição, exclusão e consulta de cotações com múltiplos fornecedores.
- Campo zoom para busca/cadastro inline de fornecedores, otimizando o fluxo e reduzindo retrabalho.
- Upload e gerenciamento de arquivos PDF por fornecedor, com controle de versão e segurança.
- Filtros avançados, paginação e interface responsiva.
- Mensagens claras, feedback visual e logs detalhados para auditoria.

## Integrações
- Entidades Orçamentárias e Orçamentos (relacionamento obrigatório/opcional)
- Fornecedores globais (campo zoom)
- Módulo BDI (cálculos)

## Observações e Sugestões (PARA FÁBRICA)
- O módulo está pronto para expansão, podendo receber melhorias como: preview de PDF, exportação de dados, gráficos comparativos, cache de fornecedores e histórico de versões de arquivos.
*(VALIDAR NECESSIDADE COM EQUIPE)*

## Conclusão
O módulo de Cotações entrega controle, agilidade e segurança ao processo de comparação de propostas, com alto grau de automação e integração. Sua estrutura robusta e flexível permite evoluções futuras conforme as necessidades do negócio. 