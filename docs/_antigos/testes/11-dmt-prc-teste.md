# Roteiro de Testes — DMT PRC

## Informações Gerais
- **Módulo:** DMT PRC (Materiais de Transporte Padrão)
- **Objetivo:** Gerenciar materiais de transporte padrão (PRC) que servem como referência para os DMTs de cada entidade orçamentária
- **URL Base:** /dmt-default
- **Data:** [Data]
- **Testador:** [Nome]

---

## Funcionalidades Básicas

### Cadastro e Edição
- [ ] Cadastro de materiais de transporte padrão está funcionando?
- [ ] Edição de materiais de transporte está funcionando?
- [ ] Exclusão de materiais de transporte está funcionando?
- [ ] Listagem de materiais de transporte está funcionando?

### Validações
- [ ] Campos obrigatórios (código_material, nome_material, sigla_transporte, tipo, x1, x2) estão sendo validados?
- [ ] Validação de tipo (apenas 'local' ou 'comercial') está funcionando?
- [ ] Validação de campos numéricos (x1, x2) está ok?
- [ ] Validação de tamanho máximo dos campos está funcionando?
- [ ] Campos opcionais (origem, destino) estão funcionando?

### Interface
- [ ] Botões (novo, editar, excluir, importar) estão funcionando?
- [ ] Navegação entre telas está ok?
- [ ] Mensagens de erro/sucesso aparecem?
- [ ] Modal de cadastro/edição abrem e fecham corretamente?
- [ ] Tabela de materiais exibe dados corretamente?

---

## Funcionalidades Específicas

### Listagem e Paginação
- [ ] Listagem paginada (100 registros por página) está funcionando?
- [ ] Ordenação por destino (ascendente) está funcionando?
- [ ] Paginação na parte inferior da tabela está funcionando?
- [ ] Dados são exibidos corretamente na tabela?

### Edição Inline
- [ ] Edição inline de campos está funcionando?
- [ ] Salvamento automático após edição está funcionando?
- [ ] Validação durante edição inline está funcionando?

### Importação via Excel
- [ ] Botão de importar está funcionando?
- [ ] Seleção de arquivo Excel está funcionando?
- [ ] Mapeamento dinâmico de colunas está funcionando?
- [ ] Validação de colunas obrigatórias está funcionando?
- [ ] Tratamento de dados inválidos está funcionando?
- [ ] Conversão de vírgula para ponto decimal está funcionando?
- [ ] Logs detalhados de importação estão sendo gerados?

### Validação de Importação
- [ ] Validação transacional (linha por linha) está funcionando?
- [ ] UpdateOrCreate (evita duplicatas) está funcionando?
- [ ] Mensagens de erro para dados inválidos aparecem?
- [ ] Feedback de sucesso para dados válidos aparece?

---

## Performance e Usabilidade

### Tempo de Resposta
- [ ] Página de DMT padrão carrega em tempo razoável?
- [ ] Ações (salvar, editar, excluir) são rápidas?
- [ ] Importação de Excel é rápida?
- [ ] Paginação responde rapidamente?

### Usabilidade
- [ ] Interface está intuitiva?
- [ ] Tabela está organizada e legível?
- [ ] Campos editáveis estão claros?
- [ ] Feedback visual está adequado?
- [ ] Cabeçalho com ações está acessível?

---

## Checklist Final

### Problemas Encontrados
| Problema | Descrição | Gravidade |
|----------|-----------|-----------|
| [Problema 1] | [Descrição] | [Alta/Média/Baixa] |
| [Problema 2] | [Descrição] | [Alta/Média/Baixa] |

### Observações
- Paginação: 100 registros por página
- Ordenação padrão por destino (ascendente)
- Importação via Excel com mapeamento dinâmico de colunas
- Validação transacional linha por linha
- UpdateOrCreate baseado em código_material + nome_material
- Conversão automática de vírgula para ponto decimal
- Logs detalhados para rastreabilidade

### Status Final
- [ ] **OK** - Tudo funcionando
- [ ] **Com problemas** - Alguns itens com problemas
- [ ] **Crítico** - Muitos problemas encontrados

**Data de Conclusão:** [Data]
**Testador:** [Nome] 