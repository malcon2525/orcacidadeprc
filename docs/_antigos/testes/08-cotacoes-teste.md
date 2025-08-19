# Roteiro de Testes — Módulo Cotações

## Informações Gerais
- **Módulo:** Cotações
- **Objetivo:** Gerenciar processo de cotação de preços de insumos e serviços com múltiplos fornecedores
- **URL Base:** `/cotacoes`
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

## Funcionalidades Específicas - Cotações

### Campos da Cotação
- [ ] Campo código está funcionando?
- [ ] Campo descrição está funcionando?
- [ ] Seleção de entidade orçamentária está funcionando?
- [ ] Campo valor final está funcionando?
- [ ] Campo tipo valor final está funcionando?

### Tipos de Valor Final
- [ ] Cálculo por média está funcionando?
- [ ] Cálculo por mediana está funcionando?
- [ ] Cálculo por menor valor está funcionando?
- [ ] Valor manual está funcionando?

### Filtros
- [ ] Filtro por entidade orçamentária está funcionando?
- [ ] Filtro por ID do orçamento está funcionando?
- [ ] Filtros em tempo real estão ok?

---

## Funcionalidades Específicas - Fornecedores

### Busca de Fornecedores
- [ ] Busca por CNPJ/CPF está funcionando?
- [ ] Busca para select está funcionando?
- [ ] Cadastro "on-the-fly" está funcionando?

### Validação de Fornecedores
- [ ] CNPJ/CPF único está sendo validado?
- [ ] Fornecedor duplicado é evitado?
- [ ] Fornecedor novo é salvo globalmente?

### Quantidade de Fornecedores
- [ ] Exatamente 3 fornecedores são obrigatórios?
- [ ] Validação de quantidade mínima está ok?
- [ ] Validação de quantidade máxima está ok?

---

## Upload de Arquivos

### Upload de Propostas
- [ ] Upload de arquivo PDF está funcionando?
- [ ] Validação de tipo de arquivo está ok?
- [ ] Validação de tamanho de arquivo está ok?
- [ ] Upload é opcional?

### Gerenciamento de Arquivos
- [ ] Arquivo é salvo no diretório correto?
- [ ] Nomenclatura do arquivo está correta?
- [ ] Substituição de arquivo está funcionando?
- [ ] Exclusão de arquivo está funcionando?

### Estrutura de Diretórios
- [ ] Diretório da entidade orçamentária está sendo criado?
- [ ] Nome do diretório está sanitizado?
- [ ] Arquivos são acessíveis pela web?

---

## Cálculos Automáticos

### Cálculo de Valor Final
- [ ] Cálculo por média está correto?
- [ ] Cálculo por mediana está correto?
- [ ] Cálculo por menor valor está correto?
- [ ] Valor manual sobrescreve cálculos?

### Cenários de Teste
- [ ] Cotação com 3 fornecedores iguais?
- [ ] Cotação com valores muito diferentes?
- [ ] Cotação com valores decimais?
- [ ] Cotação com valores zero?

---

## Validações Específicas

### Validação de Dados
- [ ] Código obrigatório está sendo validado?
- [ ] Descrição obrigatória está sendo validada?
- [ ] Entidade orçamentária obrigatória está sendo validada?
- [ ] Valores negativos são rejeitados?
- [ ] Valores zero são aceitos?

### Validação de Negócio
- [ ] Exatamente 3 fornecedores são obrigatórios?
- [ ] CNPJ/CPF único é validado?
- [ ] Valor final é calculado corretamente?
- [ ] Exclusão remove arquivos físicos?

---

## Performance e Usabilidade

### Tempo de Resposta
- [ ] Páginas carregam em tempo razoável?
- [ ] Ações (salvar, editar, excluir) são rápidas?
- [ ] Upload de arquivos é rápido?
- [ ] Busca de fornecedores é rápida?

### Usabilidade
- [ ] Interface está intuitiva?
- [ ] Busca de fornecedores é fácil?
- [ ] Upload de arquivos é claro?
- [ ] Cálculos são transparentes?

---

## Integração com Outros Módulos

### Entidades Orçamentárias
- [ ] Seleção de entidades está funcionando?
- [ ] Validação de entidades existentes está ok?
- [ ] Filtro por entidade está funcionando?

### Fornecedores Globais
- [ ] Fornecedor novo é salvo globalmente?
- [ ] Busca global de fornecedores está ok?
- [ ] Evita duplicação de fornecedores?

---

## Exclusão e Limpeza

### Exclusão de Cotação
- [ ] Cotação é excluída do banco?
- [ ] Arquivos físicos são removidos?
- [ ] Relacionamentos são limpos?
- [ ] Diretório vazio é removido?

### Exclusão de Arquivos
- [ ] Arquivo antigo é removido na substituição?
- [ ] Arquivo é removido quando desmarcado?
- [ ] Espaço em disco é liberado?

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