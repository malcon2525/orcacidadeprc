# Roteiro de Testes — Módulo Estrutura de Orçamento

## Informações Gerais
- **Módulo:** Estrutura de Orçamento (Grandes Itens e Subgrupos)
- **Objetivo:** Cadastro, edição, importação, exportação e organização dos Grandes Itens e Subgrupos
- **URL Base:** `/grandes-itens`
- **Data:** [Data]
- **Testador:** [Nome]

---

## Funcionalidades Básicas

### Cadastro e Edição
- [ ] Cadastro está funcionando?
- [ ] Edição está funcionando?
- [ ] Exclusão está funcionando?
- [ ] Listagem está funcionando?

### Validações
- [ ] Campos obrigatórios estão sendo validados?
- [ ] Validações de formato estão ok?
- [ ] Validações de negócio estão funcionando?

### Interface
- [ ] Botões estão funcionando?
- [ ] Navegação está ok?
- [ ] Mensagens de erro/sucesso aparecem?
- [ ] Modais abrem e fecham corretamente?

---

## Funcionalidades Específicas - Grandes Itens

### Campos do Formulário
- [ ] Campo tipo de orçamento está funcionando?
- [ ] Campo descrição está funcionando?
- [ ] Campo ordem está funcionando?
- [ ] Validação de descrição obrigatória está ok?
- [ ] Validação de tipo de orçamento obrigatório está ok?

### Filtros e Ordenação
- [ ] Filtro por descrição está funcionando?
- [ ] Filtro por tipo de orçamento está funcionando?
- [ ] Ordenação por ordem está funcionando?
- [ ] Ordenação por descrição está funcionando?
- [ ] Filtros em tempo real estão ok?

### Funcionalidades Especiais
- [ ] Contagem de subgrupos está sendo exibida?
- [ ] Navegação para subgrupos está funcionando?
- [ ] Importação de estrutura via Excel está funcionando?
- [ ] Exportação PDF da estrutura está funcionando?

---

## Funcionalidades Específicas - Subgrupos

### Campos do Formulário
- [ ] Campo descrição está funcionando?
- [ ] Campo ordem está funcionando?
- [ ] Vinculação ao grande item está funcionando?
- [ ] Validação de descrição obrigatória está ok?

### Filtros e Ordenação
- [ ] Filtro por descrição está funcionando?
- [ ] Ordenação por ordem está funcionando?
- [ ] Ordenação por descrição está funcionando?
- [ ] Filtros em tempo real estão ok?

### Navegação
- [ ] Navegação entre grandes itens e subgrupos está ok?
- [ ] Breadcrumb está funcionando?
- [ ] Voltar para grandes itens está funcionando?

---

## Validações Específicas

### Validação de Dados
- [ ] Descrição duplicada no mesmo tipo de orçamento é rejeitada?
- [ ] Descrição duplicada no mesmo grande item é rejeitada?
- [ ] Ordem com valor negativo é rejeitada?
- [ ] Ordem com valor zero é aceita?
- [ ] Descrição vazia é rejeitada?

### Validação de Negócio
- [ ] Exclusão de grande item com subgrupos pede confirmação?
- [ ] Exclusão de subgrupo é permitida?
- [ ] Ordem é mantida após edições?
- [ ] Relacionamentos são preservados?

---

## Importação e Exportação

### Importação de Estrutura
- [ ] Upload de arquivo Excel está funcionando?
- [ ] Validação de colunas obrigatórias está ok?
- [ ] Validação de dados está funcionando?
- [ ] Importação em lote está funcionando?
- [ ] Logs de importação são gerados?

### Exportação PDF
- [ ] Geração de PDF está funcionando?
- [ ] Estrutura hierárquica está correta no PDF?
- [ ] Formatação do PDF está adequada?
- [ ] Download do arquivo está funcionando?

---

## Performance e Usabilidade

### Tempo de Resposta
- [ ] Páginas carregam em tempo razoável?
- [ ] Ações (salvar, editar, excluir) são rápidas?
- [ ] Filtros respondem rapidamente?
- [ ] Importação/exportação são rápidas?

### Usabilidade
- [ ] Interface está intuitiva?
- [ ] Filtros funcionam corretamente?
- [ ] Paginação está funcionando?
- [ ] Navegação entre níveis está clara?

---

## Integração com Outros Módulos

### Tipos de Orçamento
- [ ] Seleção de tipo de orçamento está funcionando?
- [ ] Validação de tipos existentes está ok?
- [ ] Filtro por tipo está funcionando?

### Outros Módulos
- [ ] Grandes itens aparecem em outros módulos?
- [ ] Subgrupos aparecem em outros módulos?
- [ ] Validação de itens existentes está ok?

---

## Checklist Final

### Problemas Encontrados
| Problema | Descrição | Gravidade |
|----------|-----------|-----------|
| [Problema 1] | [Descrição] | [Alta/Média/Baixa] |
| [Problema 2] | [Descrição] | [Alta/Média/Baixa] |

### Observações
[Observações importantes sobre o teste, sugestões de melhoria, etc.]

### Status Final
- [ ] **OK** - Tudo funcionando
- [ ] **Com problemas** - Alguns itens com problemas
- [ ] **Crítico** - Muitos problemas encontrados

**Data de Conclusão:** [Data]
**Testador:** [Nome] 