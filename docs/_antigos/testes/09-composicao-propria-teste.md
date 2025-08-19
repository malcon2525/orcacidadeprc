# Roteiro de Testes — Módulo Composições Próprias

## Informações Gerais
- **Módulo:** Composições Próprias
- **Objetivo:** Cadastro, edição, consulta e exclusão de composições orçamentárias personalizadas
- **URL Base:** `/composicao-proprias`
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

## Funcionalidades Específicas - Composição

### Campos da Composição
- [ ] Campo código está funcionando?
- [ ] Campo descrição está funcionando?
- [ ] Campo unidade está funcionando?
- [ ] Campo valor total mat/equip está funcionando?
- [ ] Campo valor total mão de obra está funcionando?
- [ ] Campo valor total geral está funcionando?

### Cálculos Automáticos
- [ ] Valor total geral é calculado automaticamente?
- [ ] Valores totais são somados dos itens?
- [ ] Cálculos são precisos com 2 casas decimais?

### Filtros
- [ ] Filtro por descrição está funcionando?
- [ ] Filtros em tempo real estão ok?

---

## Funcionalidades Específicas - Itens

### Adição de Itens
- [ ] Adicionar item está funcionando?
- [ ] Remover item está funcionando?
- [ ] Pelo menos 1 item é obrigatório?
- [ ] Múltiplos itens são permitidos?

### Campos dos Itens
- [ ] Campo referência (SINAPI/DERPR/PERSONALIZADA) está funcionando?
- [ ] Campo código do item está funcionando?
- [ ] Campo descrição está funcionando?
- [ ] Campo unidade está funcionando?
- [ ] Campo valor mat/equip está funcionando?
- [ ] Campo valor mão de obra está funcionando?
- [ ] Campo valor total está funcionando?
- [ ] Campo coeficiente está funcionando?

### Valores Ajustados
- [ ] Valor mat/equip ajustado está sendo calculado?
- [ ] Valor mão de obra ajustado está sendo calculado?
- [ ] Valor total ajustado está sendo calculado?
- [ ] Cálculos consideram o coeficiente?

---

## Zoom de Serviços

### Zoom SINAPI
- [ ] Busca de serviços SINAPI está funcionando?
- [ ] Filtro por termo está funcionando?
- [ ] Filtro por desoneração está funcionando?
- [ ] Paginação está funcionando?
- [ ] Seleção de serviço está funcionando?
- [ ] Preenchimento automático dos campos está ok?

### Zoom DERPR
- [ ] Busca de serviços DERPR está funcionando?
- [ ] Filtro por termo está funcionando?
- [ ] Filtro por desoneração está funcionando?
- [ ] Paginação está funcionando?
- [ ] Seleção de serviço está funcionando?
- [ ] Preenchimento automático dos campos está ok?

### Funcionamento do Zoom
- [ ] Modal de zoom abre corretamente?
- [ ] Busca é feita em tempo real?
- [ ] Seleção preenche os campos do item?
- [ ] Modal fecha após seleção?

---

## Itens Personalizados

### Cadastro Manual
- [ ] Item personalizado pode ser criado?
- [ ] Campos são editáveis?
- [ ] Validações funcionam?
- [ ] Cálculos automáticos funcionam?

### Validações
- [ ] Campos obrigatórios são validados?
- [ ] Valores negativos são rejeitados?
- [ ] Valores zero são aceitos?
- [ ] Coeficiente é validado?

---

## Validações Específicas

### Validação de Dados
- [ ] Código obrigatório está sendo validado?
- [ ] Descrição obrigatória está sendo validada?
- [ ] Unidade obrigatória está sendo validada?
- [ ] Pelo menos 1 item é obrigatório?
- [ ] Valores negativos são rejeitados?

### Validação de Negócio
- [ ] Composição sem itens é rejeitada?
- [ ] Item sem referência é rejeitado?
- [ ] Código de item obrigatório está sendo validado?
- [ ] Descrição de item obrigatória está sendo validada?

---

## Cálculos e Fórmulas

### Cálculo de Itens
- [ ] Valor total = valor mat/equip + valor mão de obra?
- [ ] Valor ajustado = valor * coeficiente?
- [ ] Cálculos são feitos em tempo real?

### Cálculo da Composição
- [ ] Total mat/equip = soma dos itens?
- [ ] Total mão de obra = soma dos itens?
- [ ] Total geral = total mat/equip + total mão de obra?

### Cenários de Teste
- [ ] Composição com 1 item?
- [ ] Composição com múltiplos itens?
- [ ] Composição com coeficientes diferentes de 1?
- [ ] Composição com valores decimais?

---

## Performance e Usabilidade

### Tempo de Resposta
- [ ] Páginas carregam em tempo razoável?
- [ ] Ações (salvar, editar, excluir) são rápidas?
- [ ] Zoom responde rapidamente?
- [ ] Cálculos são instantâneos?

### Usabilidade
- [ ] Interface está intuitiva?
- [ ] Adição/remoção de itens é fácil?
- [ ] Zoom é fácil de usar?
- [ ] Cálculos são transparentes?

---

## Integração com Outros Módulos

### SINAPI
- [ ] Zoom busca dados atualizados do SINAPI?
- [ ] Validação de serviços existentes está ok?
- [ ] Preenchimento automático está correto?

### DERPR
- [ ] Zoom busca dados atualizados do DERPR?
- [ ] Validação de serviços existentes está ok?
- [ ] Preenchimento automático está correto?

---

## Exclusão e Limpeza

### Exclusão de Composição
- [ ] Composição é excluída do banco?
- [ ] Itens são removidos em cascata?
- [ ] Relacionamentos são limpos?

### Transações
- [ ] Transações garantem integridade?
- [ ] Rollback funciona em caso de erro?
- [ ] Dados não ficam inconsistentes?

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