# Roteiro de Testes — Módulo BDI

## Informações Gerais
- **Módulo:** BDI (Benefícios e Despesas Indiretas)
- **Objetivo:** Cálculo, registro e gestão dos percentuais de BDI para entidades orçamentárias e orçamentos
- **URL Base:** `/bdis`
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

## Funcionalidades Específicas

### Campos de Identificação
- [ ] Campo nome está funcionando?
- [ ] Campo origem (entidade_orcamentaria/orcamento) está funcionando?
- [ ] Seleção de entidade orçamentária está funcionando?
- [ ] Seleção de orçamento está funcionando?
- [ ] Campo analisado (checkbox) está funcionando?

### Campos de Serviços
- [ ] Adm central serviço está funcionando?
- [ ] Riscos serviço está funcionando?
- [ ] Seguros serviço está funcionando?
- [ ] Despesas financeiras serviço está funcionando?
- [ ] Lucro serviço está funcionando?

### Campos de Materiais
- [ ] Adm central material está funcionando?
- [ ] Riscos material está funcionando?
- [ ] Seguros material está funcionando?
- [ ] Despesas financeiras material está funcionando?
- [ ] Lucro material está funcionando?

### Campos de Equipamentos
- [ ] Adm central equipamento está funcionando?
- [ ] Riscos equipamento está funcionando?
- [ ] Seguros equipamento está funcionando?
- [ ] Despesas financeiras equipamento está funcionando?
- [ ] Lucro equipamento está funcionando?

### Campos de Impostos
- [ ] ISS município está funcionando?
- [ ] PIS está funcionando?
- [ ] COFINS está funcionando?
- [ ] Outros impostos estão funcionando?

### Resultados Calculados
- [ ] BDI serviço está sendo calculado automaticamente?
- [ ] BDI material está sendo calculado automaticamente?
- [ ] BDI equipamento está sendo calculado automaticamente?
- [ ] Cálculos estão corretos?

---

## Filtros e Busca

### Filtros
- [ ] Filtro por nome está funcionando?
- [ ] Filtro por entidade orçamentária está funcionando?
- [ ] Filtro por analisado está funcionando?
- [ ] Filtros em tempo real estão ok?

### Paginação
- [ ] Paginação está funcionando?
- [ ] Navegação entre páginas está ok?
- [ ] Contadores de registros estão corretos?

---

## Validações Específicas

### Validação de Dados
- [ ] Percentuais negativos são rejeitados?
- [ ] Percentuais com valor zero são aceitos?
- [ ] Percentuais muito altos são validados?
- [ ] Campos obrigatórios são validados?
- [ ] Origem obrigatória está funcionando?

### Validação de Negócio
- [ ] Entidade orçamentária obrigatória quando origem = entidade_orcamentaria?
- [ ] Orçamento obrigatório quando origem = orcamento?
- [ ] Cálculos são precisos?
- [ ] Exclusão de BDI usado em outros módulos é permitida?

---

## Cálculos Automáticos

### Fórmulas de BDI
- [ ] Cálculo de BDI serviço está correto?
- [ ] Cálculo de BDI material está correto?
- [ ] Cálculo de BDI equipamento está correto?
- [ ] Cálculos são feitos em tempo real?
- [ ] Cálculos são precisos com 2 casas decimais?

### Cenários de Teste
- [ ] BDI com todos os percentuais zerados?
- [ ] BDI com percentuais altos?
- [ ] BDI com impostos altos?
- [ ] BDI com valores decimais?

---

## Performance e Usabilidade

### Tempo de Resposta
- [ ] Páginas carregam em tempo razoável?
- [ ] Ações (salvar, editar, excluir) são rápidas?
- [ ] Filtros respondem rapidamente?
- [ ] Cálculos são instantâneos?

### Usabilidade
- [ ] Interface está intuitiva?
- [ ] Campos estão bem organizados?
- [ ] Feedback visual dos cálculos está claro?
- [ ] Validações são claras?

---

## Integração com Outros Módulos

### Entidades Orçamentárias
- [ ] Seleção de entidades está funcionando?
- [ ] Validação de entidades existentes está ok?
- [ ] Filtro por entidade está funcionando?

### Orçamentos
- [ ] Seleção de orçamentos está funcionando?
- [ ] Validação de orçamentos existentes está ok?
- [ ] Filtro por orçamento está funcionando?

### Outros Módulos
- [ ] BDI aparece em outros módulos?
- [ ] Seleção de BDI em formulários está funcionando?
- [ ] Validação de BDI existente está ok?

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