# Roteiro de Testes — Consulta SINAPI

## Informações Gerais
- **Módulo:** Consulta SINAPI (Consulta de Tabela SINAPI)
- **Objetivo:** Consultar, filtrar, paginar e exportar composições de preços oficiais do SINAPI
- **URL Base:** /preco/consultar-sinapi
- **Data:** [Data]
- **Testador:** [Nome]

---

## Funcionalidades Básicas

### Consulta e Visualização
- [ ] Acesso à tela de consulta SINAPI está funcionando?
- [ ] Seleção de tabela (data-base + desoneração) está funcionando?
- [ ] Carregamento de dados da tabela está funcionando?
- [ ] Exibição dos dados em tabela está funcionando?

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
- [ ] Tabela de dados exibe informações corretamente?

---

## Funcionalidades Específicas

### Seleção de Tabela
- [ ] Select de data-base está funcionando?
- [ ] Select de desoneração está funcionando?
- [ ] Combinação data-base + desoneração está funcionando?
- [ ] Carregamento de tabelas disponíveis está funcionando?

### Filtros
- [ ] Filtro por grupo está funcionando?
- [ ] Filtro por código está funcionando?
- [ ] Filtro por descrição está funcionando?
- [ ] Aplicação de múltiplos filtros está funcionando?
- [ ] Limpeza de filtros está funcionando?

### Paginação
- [ ] Paginação dos resultados está funcionando?
- [ ] Navegação entre páginas está funcionando?
- [ ] Quantidade de registros por página está correta?
- [ ] Paginação customizada está funcionando?

### Cálculos
- [ ] Cálculo de mão de obra está funcionando?
- [ ] Cálculo de material/equipamento está funcionando?
- [ ] Fórmulas estão corretas (mao_de_obra = percentagem_pr * custo_pr)?
- [ ] Fórmulas estão corretas (material_equipamento = custo_pr - mao_de_obra)?

### Exportação
- [ ] Botão de exportar Excel está funcionando?
- [ ] Exportação para Excel está funcionando?
- [ ] Arquivo Excel está sendo gerado corretamente?
- [ ] Headers do Excel estão corretos?
- [ ] Dados exportados estão completos?

---

## Performance e Usabilidade

### Tempo de Resposta
- [ ] Página de consulta carrega em tempo razoável?
- [ ] Carregamento de dados da tabela é rápido?
- [ ] Filtros respondem rapidamente?
- [ ] Exportação é rápida?

### Usabilidade
- [ ] Interface está intuitiva?
- [ ] Layout em cards está organizado?
- [ ] Tabela responsiva está funcionando?
- [ ] Filtros no topo estão acessíveis?
- [ ] Botões de exportação estão destacados?

---

## Checklist Final

### Problemas Encontrados
| Problema | Descrição | Gravidade |
|----------|-----------|-----------|
| [Problema 1] | [Descrição] | [Alta/Média/Baixa] |
| [Problema 2] | [Descrição] | [Alta/Média/Baixa] |

### Observações
- Só são exibidas composições que existam para a data-base e desoneração selecionadas
- Cálculos: mao_de_obra = percentagem_pr * custo_pr
- Cálculos: material_equipamento = custo_pr - mao_de_obra
- Logs de debug em storage/logs/sinapi_debug.log
- Exportação Excel implementada, PDF placeholder
- Filtros aplicados no frontend

### Status Final
- [ ] **OK** - Tudo funcionando
- [ ] **Com problemas** - Alguns itens com problemas
- [ ] **Crítico** - Muitos problemas encontrados

**Data de Conclusão:** [Data]
**Testador:** [Nome] 