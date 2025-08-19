# Roteiro de Testes — Módulo Tipos de Orçamento

## Informações Gerais
- **Módulo:** Tipos de Orçamento
- **Objetivo:** Cadastro, consulta, edição e exclusão dos tipos de orçamento utilizados como referência em outros módulos
- **URL Base:** `/tipos-orcamentos`
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
### Conceitos:
**VERSÃO DE ORÇAMENTO:** permite lidar com mudanças na estrutura (grandes intes, subgrupos) de orçamento. Na criação de orçamentos o sitema sempre irá pegar as informações da última versão ativa. Os orçamentos com versões anteriores continuarão funcionando. 

- [ ] Conceito de Versão está ok?


### Filtros
- [ ] Filtro por descrição está funcionando?
- [ ] Filtros em tempo real estão ok?

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