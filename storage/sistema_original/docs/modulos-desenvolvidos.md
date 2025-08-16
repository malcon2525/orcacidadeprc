# Módulos Desenvolvidos — Sistema OrçaCidade

Este documento lista todos os módulos desenvolvidos no sistema OrçaCidade, organizados por categoria e com informações básicas sobre cada um.

---

## 📊 Resumo Geral

- **Total de Módulos:** 20
- **Categorias:** 6
- **Status:** Todos desenvolvidos e documentados

---

## 🏢 Módulos de Clientes

### 1. **Municípios**
- **Objetivo:** Cadastro, consulta, edição, exclusão e importação em massa de municípios
- **URL Base:** `/municipios`
- **Complexidade:** Baixa
- **Pontos de Função:** 15 PF

### 2. **Entidades Orçamentárias**
- **Objetivo:** Cadastro, consulta, edição, exclusão e importação de entidades orçamentárias
- **URL Base:** `/entidades-orcamentarias`
- **Complexidade:** Baixa
- **Pontos de Função:** 13 PF

---

## 💰 Módulos de Orçamento

### 3. **Tipos de Orçamento**
- **Objetivo:** Cadastro, consulta, edição e exclusão dos tipos de orçamento
- **URL Base:** `/tipos-orcamentos`
- **Complexidade:** Baixa
- **Pontos de Função:** 13 PF

### 4. **Estrutura de Orçamento**
- **Objetivo:** Gerenciamento da estrutura hierárquica de orçamentos
- **URL Base:** `/estrutura-orcamento`
- **Complexidade:** Média
- **Pontos de Função:** 18 PF

### 5. **Grandes Itens**
- **Objetivo:** Cadastro e gerenciamento de grandes itens de orçamento
- **URL Base:** `/grandes-itens`
- **Complexidade:** Média
- **Pontos de Função:** 20 PF

### 6. **Sub Grupos**
- **Objetivo:** Cadastro e gerenciamento de sub grupos dentro dos grandes itens
- **URL Base:** `/grandes-itens/{id}/sub-grupos`
- **Complexidade:** Média
- **Pontos de Função:** 18 PF

### 7. **BDI (Benefícios e Despesas Indiretas)**
- **Objetivo:** Cálculo e gerenciamento de BDI para orçamentos
- **URL Base:** `/bdis`
- **Complexidade:** Média
- **Pontos de Função:** 22 PF

### 8. **Composições Próprias**
- **Objetivo:** Criação e gerenciamento de composições de custos próprias
- **URL Base:** `/composicao-proprias`
- **Complexidade:** Alta
- **Pontos de Função:** 35 PF

### 9. **Cotações**
- **Objetivo:** Sistema de cotações de preços
- **URL Base:** `/cotacoes`
- **Complexidade:** Média
- **Pontos de Função:** 25 PF

---

## 📋 Módulos de Preços e Tabelas Oficiais

### 10. **Importação DER-PR**
- **Objetivo:** Importar tabelas oficiais do DER-PR a partir de arquivos PDF
- **URL Base:** `/preco/importar-derpr`
- **Complexidade:** Alta
- **Pontos de Função:** 35 PF

### 11. **Importação SINAPI**
- **Objetivo:** Importar tabelas oficiais do SINAPI a partir de arquivos Excel
- **URL Base:** `/preco/importar-sinapi`
- **Complexidade:** Alta
- **Pontos de Função:** 40 PF

### 12. **Consulta DER-PR**
- **Objetivo:** Consulta e exportação de tabelas DER-PR
- **URL Base:** `/preco/consultar-derpr`
- **Complexidade:** Média
- **Pontos de Função:** 20 PF

### 13. **Consulta SINAPI**
- **Objetivo:** Consulta e exportação de tabelas SINAPI
- **URL Base:** `/preco/consultar-sinapi`
- **Complexidade:** Média
- **Pontos de Função:** 20 PF

---

## 🚚 Módulos de Transporte

### 14. **Custos de Transporte**
- **Objetivo:** Cálculo e gerenciamento de custos de transporte
- **URL Base:** `/transporte/custos`
- **Complexidade:** Alta
- **Pontos de Função:** 35 PF

### 15. **Coeficientes de Custo de Transporte**
- **Objetivo:** Gerenciamento de coeficientes para cálculo de transporte
- **URL Base:** `/transporte/coeficientes`
- **Complexidade:** Média
- **Pontos de Função:** 25 PF

### 16. **DMT (Distância Média de Transporte)**
- **Objetivo:** Gerenciamento de distâncias médias de transporte
- **URL Base:** `/dmt`
- **Complexidade:** Média
- **Pontos de Função:** 25 PF

### 17. **DMT Default (PRC)**
- **Objetivo:** Valores padrão de DMT conforme PRC
- **URL Base:** `/dmt-default`
- **Complexidade:** Baixa
- **Pontos de Função:** 15 PF

---

## ⚙️ Módulos de Configuração

### 18. **Configurações Gerais**
- **Objetivo:** Configurações gerais do sistema
- **URL Base:** `/configuracoes-gerais`
- **Complexidade:** Baixa
- **Pontos de Função:** 10 PF

---

## 🎯 Módulos de Gestão

### 19. **Central de Tarefas**
- **Objetivo:** Central de gerenciamento de tarefas e projetos
- **URL Base:** `/central_tarefas`
- **Complexidade:** Média
- **Pontos de Função:** 30 PF

### 20. **Gestão de Projetos**
- **Objetivo:** Sistema de gestão de projetos e prioridades
- **URL Base:** `/projeto`
- **Complexidade:** Alta
- **Pontos de Função:** 45 PF

---

## 📈 Estatísticas por Categoria

| Categoria | Quantidade | Total PF | Média PF |
|-----------|------------|----------|----------|
| **Clientes** | 2 | 28 | 14 |
| **Orçamento** | 7 | 153 | 21.9 |
| **Preços/Tabelas** | 4 | 115 | 28.8 |
| **Transporte** | 4 | 100 | 25 |
| **Configuração** | 1 | 10 | 10 |
| **Gestão** | 2 | 75 | 37.5 |
| **TOTAL** | **20** | **481** | **24.1** |

---

## 🔧 Tecnologias Utilizadas

- **Backend:** Laravel (PHP)
- **Frontend:** Vue.js + Blade Templates
- **Banco de Dados:** MySQL
- **Scripts:** Python (para importações)
- **Documentação:** Markdown

---

## 📋 Status de Documentação

- ✅ **Documentação Técnica:** Todos os módulos
- ✅ **Relatórios:** Todos os módulos
- ✅ **Roteiros de Teste:** 4 módulos (em desenvolvimento)
- ✅ **Consolidação Geral:** Disponível

---

## 🎯 Próximos Passos

1. **Completar roteiros de teste** para todos os módulos
2. **Atualizar documentação** conforme evolução do sistema
3. **Implementar testes automatizados** baseados nos roteiros
4. **Revisar e otimizar** módulos conforme feedback dos usuários

---

**Última atualização:** [Data]
**Responsável:** Equipe de Desenvolvimento OrçaCidade 