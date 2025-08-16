# Critérios de Cálculo de Pontos de Função (PF) - Padrão do Projeto

---

## Introdução

Este documento estabelece os critérios padronizados para cálculo de Pontos de Função (PF) utilizados no projeto OrçaCidade. **IMPORTANTE:** Esta metodologia é simplificada e subjetiva, **NÃO** segue a metodologia oficial IFPUG.

---

## Tipos de Função e Valores Padrão

### 1. Entradas Externas (EE)
**Valor:** 2-3 PF por função

**Definição:** Processamento de dados que entram no sistema através de interfaces de usuário.

**Exemplos:**
- Formulários de cadastro (CRUD)
- Importação de dados via Excel
- Upload de arquivos
- Edição inline de dados
- Salvamento em lote

**Critérios de Classificação:**
- **2 PF:** Operações simples (CRUD básico, edição simples)
- **3 PF:** Operações complexas (importação, validações rigorosas, múltiplos campos)

### 2. Saídas Externas (SE)
**Valor:** 1-2 PF por função

**Definição:** Processamento de dados que saem do sistema para o usuário.

**Exemplos:**
- Listagens com filtros
- Relatórios
- Exclusões com verificação
- Exportação de dados
- Feedback visual (toasts, alerts)

**Critérios de Classificação:**
- **1 PF:** Saídas simples (listagem básica, exclusão simples)
- **2 PF:** Saídas complexas (filtros múltiplos, relatórios, exclusão com cascade)

### 3. Consultas Externas (CE)
**Valor:** 1-2 PF por função

**Definição:** Recuperação de dados do sistema sem processamento adicional.

**Exemplos:**
- Busca de dados
- Filtros de pesquisa
- Dados para selects/dropdowns
- Consultas de validação
- Verificação de existência

**Critérios de Classificação:**
- **1 PF:** Consultas simples (busca básica, select simples)
- **2 PF:** Consultas complexas (filtros múltiplos, busca com relacionamentos)

### 4. Arquivos Lógicos Internos (ALI)
**Valor:** 1-2 PF por função

**Definição:** Grupos de dados logicamente relacionados mantidos dentro do sistema.

**Exemplos:**
- Tabelas principais do banco de dados
- Views materializadas
- Cache de dados
- Configurações do sistema

**Critérios de Classificação:**
- **1 PF:** Tabelas simples (poucos campos, sem relacionamentos complexos)
- **2 PF:** Tabelas complexas (muitos campos, relacionamentos, constraints)

### 5. Arquivos de Interface Externa (AIE)
**Valor:** 1 PF por função

**Definição:** Grupos de dados referenciados pelo sistema mas mantidos em outro sistema.

**Exemplos:**
- Integração com APIs externas
- Tabelas de sistemas terceiros
- Serviços web
- Integração com bancos de dados externos

---

## Metodologia de Contagem

### Passo 1: Identificação das Funções
1. Analisar todas as rotas (web e API)
2. Identificar funcionalidades principais
3. Mapear operações de entrada, saída e consulta
4. Contar tabelas e integrações

### Passo 2: Classificação por Tipo
1. **EE:** Contar formulários, importações, edições
2. **SE:** Contar listagens, relatórios, exclusões
3. **CE:** Contar buscas, filtros, selects
4. **ALI:** Contar tabelas principais
5. **AIE:** Contar integrações externas

### Passo 3: Aplicação dos Valores
1. Aplicar valores padrão conforme complexidade
2. Justificar valores elevados (3 PF para EE, 2 PF para outros)
3. Documentar critérios de classificação

---

## Exemplos Práticos

### Exemplo 1: Módulo CRUD Simples
**Funcionalidades:**
- Listar registros
- Criar novo registro
- Editar registro
- Excluir registro
- Buscar registros

**Contagem:**
- EE: 2 (criar, editar) × 2 = 4 PF
- SE: 2 (listar, excluir) × 1 = 2 PF
- CE: 1 (buscar) × 1 = 1 PF
- ALI: 1 (tabela principal) × 1 = 1 PF
- **Total: 8 PF**

### Exemplo 2: Módulo com Importação
**Funcionalidades:**
- Listar registros com filtros
- Criar registro
- Editar registro
- Excluir registro
- Importar via Excel
- Buscar registros
- Dados para selects

**Contagem:**
- EE: 4 (criar, editar, excluir, importar) × 3 = 12 PF
- SE: 1 (listar com filtros) × 2 = 2 PF
- CE: 2 (buscar, selects) × 2 = 4 PF
- ALI: 1 (tabela principal) × 2 = 2 PF
- **Total: 20 PF**

### Exemplo 3: Módulo Complexo com Integração
**Funcionalidades:**
- Listar com filtros complexos
- Criar com validações rigorosas
- Editar inline
- Excluir com cascade
- Importar Excel com mapeamento
- Buscar com relacionamentos
- Dados para múltiplos selects
- Integração com sistema externo

**Contagem:**
- EE: 4 (criar, editar, excluir, importar) × 3 = 12 PF
- SE: 2 (listar complexo, exclusão cascade) × 2 = 4 PF
- CE: 3 (busca, selects múltiplos, validação) × 2 = 6 PF
- ALI: 2 (tabelas principais) × 2 = 4 PF
- AIE: 1 (integração externa) × 1 = 1 PF
- **Total: 27 PF**

---

## Conversão para Horas e Custo

### Fatores de Conversão Padrão
- **Horas por PF:** 8 horas
- **Custo por PF:** R$ 800,00

### Fórmulas
```
Total de Horas = Total PF × 8
Custo Estimado = Total PF × R$ 800,00
```

### Exemplos de Conversão
- **10 PF:** 80 horas = R$ 8.000,00
- **20 PF:** 160 horas = R$ 16.000,00
- **27 PF:** 216 horas = R$ 21.600,00
- **35 PF:** 280 horas = R$ 28.000,00

---

## Critérios de Complexidade

### Módulo Simples (5-15 PF)
- CRUD básico
- Interface simples
- Validações básicas
- Sem integrações externas

### Módulo Moderado (15-25 PF)
- CRUD com funcionalidades avançadas
- Interface com filtros
- Validações rigorosas
- Importação de dados
- Edição em lote

### Módulo Complexo (25-40 PF)
- Múltiplas integrações
- Cálculos complexos
- Interface sofisticada
- Processamento em lote
- Logs detalhados
- Múltiplas tabelas

### Módulo Muito Complexo (40+ PF)
- Integração com sistemas externos
- Cálculos matemáticos complexos
- Interface altamente sofisticada
- Múltiplos processos em lote
- Auditoria completa
- Performance crítica

---

## Checklist para Contagem

### ✅ Identificação
- [ ] Todas as rotas mapeadas
- [ ] Funcionalidades principais identificadas
- [ ] Tabelas contadas
- [ ] Integrações externas identificadas

### ✅ Classificação
- [ ] EE classificadas por complexidade
- [ ] SE classificadas por complexidade
- [ ] CE classificadas por complexidade
- [ ] ALI classificadas por complexidade
- [ ] AIE identificadas

### ✅ Validação
- [ ] Valores justificados
- [ ] Total calculado corretamente
- [ ] Conversão para horas/custo aplicada
- [ ] Documentação completa

---

## Observações Importantes

### Padrões do Projeto
- Sempre usar valores **inteiros** para PF
- Justificar valores elevados (3 PF para EE, 2 PF para outros)
- Documentar critérios de classificação
- Manter consistência entre relatórios

### Limitações
- Metodologia simplificada, não oficial
- Valores subjetivos baseados em experiência
- Pode variar conforme complexidade específica
- Requer revisão para casos especiais

### Recomendações
- Revisar contagem com equipe técnica
- Ajustar valores conforme feedback
- Manter histórico de ajustes
- Documentar casos especiais

---

**Última atualização:** Janeiro 2025  
**Responsável:** Equipe de Desenvolvimento  
**Versão:** 1.0 