# Roteiro de Testes — DMT

## Informações Gerais
- **Módulo:** DMT (Dados de Materiais de Transporte)
- **Objetivo:** Gerenciar dados de materiais de transporte específicos por entidade orçamentária
- **URL Base:** /dmt
- **Data:** [Data]
- **Testador:** [Nome]

---

## Funcionalidades Básicas

### Cadastro e Edição
- [ ] Cadastro de DMTs está funcionando?
- [ ] Edição de DMTs está funcionando?
- [ ] Exclusão de DMTs está funcionando?
- [ ] Listagem de DMTs está funcionando?

### Validações
- [ ] Campos obrigatórios (código_material, nome_material, sigla_transporte, tipo, x1, x2, entidade_orcamentaria) estão sendo validados?
- [ ] Validação de tipo (apenas 'local' ou 'comercial') está funcionando?
- [ ] Validação de campos numéricos (x1, x2) está ok?
- [ ] Validação de tamanho máximo dos campos está funcionando?
- [ ] Campo entidade orçamentária obrigatório está sendo validado?

### Interface
- [ ] Botões (pesquisar, gerar DMTs, salvar alterações) estão funcionando?
- [ ] Navegação entre telas está ok?
- [ ] Mensagens de erro/sucesso aparecem?
- [ ] Filtros estão funcionando corretamente?
- [ ] Tabela de DMTs exibe dados corretamente?

---

## Funcionalidades Específicas

### Seleção de Entidade Orçamentária
- [ ] Select de entidade orçamentária está funcionando?
- [ ] Seleção obrigatória está sendo validada?
- [ ] Filtro por entidade está funcionando?
- [ ] Carregamento de DMTs após seleção está funcionando?

### Filtros
- [ ] Filtro por entidade orçamentária (obrigatório) está funcionando?
- [ ] Filtro por destino está funcionando?
- [ ] Filtro por código está funcionando?
- [ ] Filtro por nome está funcionando?
- [ ] Filtro por tipo está funcionando?
- [ ] Filtro por município está funcionando?

### Geração Automática de DMTs
- [ ] Botão "Gerar DMTs para Entidade" está funcionando?
- [ ] Geração baseada na tabela padrão está funcionando?
- [ ] Cópia de materiais da dmt_default está funcionando?
- [ ] Atualização de registros existentes está funcionando?
- [ ] Feedback de quantidade gerada está funcionando?

### Edição em Lote
- [ ] Edição inline de origem está funcionando?
- [ ] Edição inline de X1 está funcionando?
- [ ] Edição inline de X2 está funcionando?
- [ ] Botão "Salvar alterações" está funcionando?
- [ ] Atualização em lote via API está funcionando?
- [ ] Validação durante edição inline está funcionando?

### Agrupamento Visual
- [ ] Agrupamento por destino está funcionando?
- [ ] Ordenação por destino está funcionando?
- [ ] Visualização agrupada está clara?

---

## Performance e Usabilidade

### Tempo de Resposta
- [ ] Página de DMT carrega em tempo razoável?
- [ ] Filtros respondem rapidamente?
- [ ] Geração automática é rápida?
- [ ] Edição em lote é rápida?

### Usabilidade
- [ ] Interface está intuitiva?
- [ ] Filtro obrigatório de entidade está destacado?
- [ ] Tabela está organizada e legível?
- [ ] Campos editáveis estão claros?
- [ ] Agrupamento visual facilita a visualização?

---

## Checklist Final

### Problemas Encontrados
| Problema | Descrição | Gravidade |
|----------|-----------|-----------|
| [Problema 1] | [Descrição] | [Alta/Média/Baixa] |
| [Problema 2] | [Descrição] | [Alta/Média/Baixa] |

### Observações
- Filtro obrigatório por entidade orçamentária
- Geração automática copia materiais da tabela dmt_default
- Edição em lote apenas para origem, X1 e X2
- Agrupamento visual por destino
- Atualização em lote via API PUT
- Validação de entidade orçamentária obrigatória

### Status Final
- [ ] **OK** - Tudo funcionando
- [ ] **Com problemas** - Alguns itens com problemas
- [ ] **Crítico** - Muitos problemas encontrados

**Data de Conclusão:** [Data]
**Testador:** [Nome] 