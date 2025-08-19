# Roteiro de Testes — Consulta DER-PR

## Informações Gerais
- **Módulo:** Consulta DER-PR (Consulta de Tabela DER-PR)
- **Objetivo:** Consultar e visualizar tabelas oficiais de preços do DER-PR para serviços, materiais e equipamentos
- **URL Base:** /preco/consultar-derpr
- **Data:** [Data]
- **Testador:** [Nome]

---

## Funcionalidades Básicas

### Consulta e Visualização
- [ ] Acesso à tela de consulta DER-PR está funcionando?
- [ ] Seleção de tabela (data-base + desoneração) está funcionando?
- [ ] Carregamento de dados da tabela está funcionando?
- [ ] Exibição dos dados em grid está funcionando?

### Validações
- [ ] Seleção obrigatória de tabela está sendo validada?
- [ ] Validação de data-base válida está funcionando?
- [ ] Validação de desoneração (com/sem) está funcionando?
- [ ] Validação de filtros está funcionando?

### Interface
- [ ] Botões (consultar, exportar) estão funcionando?
- [ ] Navegação entre telas está ok?
- [ ] Mensagens de erro/sucesso aparecem?
- [ ] Filtros estão funcionando corretamente?
- [ ] Grid de dados exibe informações corretamente?

---

## Funcionalidades Específicas

### Seleção de Tabela
- [ ] Select de data-base está funcionando?
- [ ] Select de desoneração está funcionando?
- [ ] Combinação data-base + desoneração está funcionando?
- [ ] Carregamento de tabelas disponíveis está funcionando?

### Filtros
- [ ] Filtro por código está funcionando?
- [ ] Filtro por descrição está funcionando?
- [ ] Aplicação de múltiplos filtros está funcionando?
- [ ] Limpeza de filtros está funcionando?

### Paginação
- [ ] Paginação dos resultados (20 itens por página) está funcionando?
- [ ] Navegação entre páginas está funcionando?
- [ ] Quantidade de registros por página está correta?

### Cálculos Automáticos
- [ ] Cálculo de mão de obra está funcionando?
- [ ] Cálculo de material/equipamento está funcionando?
- [ ] Fórmula mão de obra (custo_execucao + custo_sub_servico) está correta?
- [ ] Fórmula material/equipamento (custo_unitario - mão de obra) está correta?

### Exportação
- [ ] Botão de exportar Excel está funcionando?
- [ ] Exportação para Excel está funcionando?
- [ ] Arquivo Excel está sendo gerado corretamente?
- [ ] Dados exportados estão completos?

### Cálculo de Transporte
- [ ] Ícone de transporte aparece para itens com "A acrescer"?
- [ ] Modal de cálculo de transporte abre corretamente?
- [ ] Carregamento de fórmulas de transporte está funcionando?
- [ ] Carregamento de itens de transporte está funcionando?

### Modal de Transporte
- [ ] Tipos de cálculo (Local, Comercial, Outro DER-PR, Manual) estão funcionando?
- [ ] Campos x1 e x2 para fórmulas estão funcionando?
- [ ] Campo valor manual está funcionando?
- [ ] Cálculo automático de fórmulas está funcionando?
- [ ] Cálculo final (valor × consumo) está funcionando?
- [ ] Soma total de transporte está funcionando?

---

## Performance e Usabilidade

### Tempo de Resposta
- [ ] Página de consulta carrega em tempo razoável?
- [ ] Carregamento de dados da tabela é rápido?
- [ ] Filtros respondem rapidamente?
- [ ] Modal de transporte carrega rapidamente?
- [ ] Exportação é rápida?

### Usabilidade
- [ ] Interface está intuitiva?
- [ ] Layout responsivo está funcionando?
- [ ] Grid de dados está organizado?
- [ ] Filtros estão acessíveis?
- [ ] Modal de transporte está bem organizado?

---

## Checklist Final

### Problemas Encontrados
| Problema | Descrição | Gravidade |
|----------|-----------|-----------|
| [Problema 1] | [Descrição] | [Alta/Média/Baixa] |
| [Problema 2] | [Descrição] | [Alta/Média/Baixa] |

### Observações
- Cada composição é única por: código + data_base + desoneração
- Valores sempre em Reais (R$) com 2 casas decimais
- Campo "transporte" indica se há custo adicional de transporte
- Cálculos realizados em tempo real via SQL
- Fórmulas de transporte: valor = (a × x1) + (b × x2) + c × consumo
- Modal de transporte com 4 tipos de cálculo disponíveis

### Status Final
- [ ] **OK** - Tudo funcionando
- [ ] **Com problemas** - Alguns itens com problemas
- [ ] **Crítico** - Muitos problemas encontrados

**Data de Conclusão:** [Data]
**Testador:** [Nome] 