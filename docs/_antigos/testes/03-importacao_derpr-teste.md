# Roteiro de Testes — Módulo Importação DER-PR

## Informações Gerais
- **Módulo:** Importação DER-PR
- **Objetivo:** Importar tabelas oficiais do DER-PR a partir de arquivos PDF (serviços gerais, insumos e lote completo)
- **URL Base:** `/preco/importar-derpr`
- **Data:** [Data]
- **Testador:** [Nome]

---

## Funcionalidades Básicas

### Interface e Navegação
- [ ] Interface com 3 abas está funcionando?
- [ ] Navegação entre abas está ok?
- [ ] Botões estão funcionando?
- [ ] Mensagens de erro/sucesso aparecem?

### Upload de Arquivos
- [ ] Upload de arquivos PDF está funcionando?
- [ ] Validação de tipo de arquivo está ok?
- [ ] Validação de tamanho de arquivo está ok?
- [ ] Feedback de upload está funcionando?

---

## Funcionalidades Específicas

### Aba 1 - Serviços Gerais
- [ ] Upload de PDF de serviços gerais está funcionando?
- [ ] Processamento do arquivo está ok?
- [ ] Dados extraídos são exibidos corretamente?
- [ ] Exportação para Excel está funcionando?
- [ ] Logs de processamento são gerados?

### Aba 2 - Insumos
- [ ] Upload de PDF de insumos está funcionando?
- [ ] Processamento do arquivo está ok?
- [ ] Dados extraídos são exibidos corretamente?
- [ ] Exportação para Excel está funcionando?
- [ ] Logs de processamento são gerados?

### Aba 3 - Importar Lote DER-PR
- [ ] Upload de PDF para lote está funcionando?
- [ ] Processamento completo do lote está ok?
- [ ] Geração dos arquivos Excel temporários está funcionando?
- [ ] Importação para o banco de dados está ok?
- [ ] Atualização da view `derpr_composicoes_view` está funcionando?
- [ ] Logs de importação são gerados?

### Scripts Python
- [ ] Script `01.Importar-DER-PR-Tabela-Servicos.py` está funcionando?
- [ ] Script `02.Importar-DER-PR-Tabela-Insumos.py` está funcionando?
- [ ] Script `importacao.py` está funcionando?
- [ ] Retorno JSON dos scripts está correto?

---

## Validações e Tratamento de Erros

### Validação de Arquivos
- [ ] Arquivos PDF inválidos são rejeitados?
- [ ] Arquivos com estrutura incorreta são identificados?
- [ ] Mensagens de erro são claras e informativas?

### Validação de Dados
- [ ] Dados obrigatórios são validados?
- [ ] Formato dos dados é verificado?
- [ ] Duplicatas são tratadas corretamente?

### Tratamento de Erros
- [ ] Erros de processamento são capturados?
- [ ] Logs de erro são gerados?
- [ ] Sistema não trava em caso de erro?

---

## Performance e Usabilidade

### Tempo de Processamento
- [ ] Processamento de arquivos pequenos é rápido?
- [ ] Processamento de arquivos grandes é aceitável?
- [ ] Feedback de progresso é exibido?

### Usabilidade
- [ ] Interface está intuitiva?
- [ ] Instruções são claras?
- [ ] Resultados são fáceis de entender?

---

## Logs e Auditoria

### Geração de Logs
- [ ] Logs são gerados em `storage/logs/importacao_tabelas_oficiais.log`?
- [ ] Formato dos logs está correto?
- [ ] Logs incluem timestamp e origem?

### Rastreabilidade
- [ ] Operações são rastreáveis?
- [ ] Dados de auditoria são mantidos?

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