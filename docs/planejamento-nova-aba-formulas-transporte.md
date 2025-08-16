# 📋 Planejamento: Nova Aba "Fórmulas de Transporte" - DER-PR

---

## 📅 **Informações do Projeto**

- **Data de Criação:** 13/08/2025
- **Módulo:** Tabela Oficial
- **Funcionalidade:** Importar DER-PR
- **Nova Funcionalidade:** Aba "Fórmulas de Transporte"
- **Status:** Planejamento e Implementação por Etapas

---

## 🎯 **OBJETIVO DA NOVA FUNCIONALIDADE**

### **Visão Geral:**
Implementar uma nova aba na funcionalidade "Importar DER-PR" para processar e importar tabelas de fórmulas de transporte do DER-PR, seguindo o mesmo padrão das abas existentes.

### **Contexto:**
- **PDF Fonte:** "Referencial Março 25 (Com desoneração) Serviços de Transporte.pdf"
- **Localização:** `docs/Referencial Março 25 (Com desoneração) Serviços de Transporte.pdf`
- **Tipo:** Tabela de custos de transporte com fórmulas matemáticas

---

## 📊 **ANÁLISE COMPLETA DO PDF**

### **Estrutura do Documento:**
1. **Cabeçalho:** Metadados (data base, tipo de desoneração)
2. **Variáveis:** Definições de x, x1, x2
3. **Tabela Principal:** 12 linhas de serviços com 4 colunas
4. **Fórmulas:** Expressões matemáticas complexas

### **Metadados Identificados:**
- **Órgão:** DERPR (Departamento de Estradas de Rodagem do Paraná)
- **Tipo:** Custo Referencial de Serviços de Transporte (sem Bonificação)
- **Data Base:** 31/03/2025
- **Data Emissão:** 06/05/2025
- **Valores:** Expressos em Reais (R$)

### **Variáveis Definidas:**
- **`x`** = DMT em Km (Distância Média de Transporte)
- **`x1`** = DMT em Km (rodovia pavimentada)
- **`x2`** = DMT em Km (rodovia não pavimentada)

### **Estrutura da Tabela Principal:**
| Campo | Descrição | Tipo | Tamanho | Exemplo |
|-------|-----------|------|---------|---------|
| **Código** | Identificador único do serviço | string | 10 | 972000 |
| **Descrição do Serviço** | Nome detalhado do serviço | string | 255 | "Comercial - caminhão basculante" |
| **Unidade** | Unidade de medida | string | 5 | "t" |
| **Fórmula de transporte (R$/T)** | Fórmula matemática para cálculo | string | 30 | "0,74x1 + 0,89x2" |

### **Categorias de Serviços Identificadas:**
1. **Comercial:** 4 serviços (972000, 972200, 972400, 972080)
2. **Local:** 6 serviços (972100, 972300, 972500, 973100, 973000, 972180)
3. **Material Asfáltico:** 2 serviços (974100, 974000)

### **Fórmulas Identificadas:**
- **Padrão Comercial:** `ax1 + bx2` (sem taxa fixa)
- **Padrão Local:** `ax1 + bx2 + c` (com taxa fixa)
- **Material Asfáltico:** `ax + c` (distância total + taxa fixa)

---

## 🗄️ **ESTRUTURA TÉCNICA - BANCO DE DADOS**

### **Nova Tabela:**
```sql
CREATE TABLE derpr_formula_transportes (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    data_base DATE,
    desoneracao ENUM('com', 'sem'),
    codigo VARCHAR(10),
    descricao VARCHAR(255),
    unidade VARCHAR(5),
    formula_transporte VARCHAR(30)
);
```

### **Campos da Tabela:**
| Campo | Tipo | Tamanho | Descrição | Exemplo |
|-------|------|---------|-----------|---------|
| `id` | BIGINT | - | Chave primária auto-increment | 1, 2, 3... |
| `data_base` | DATE | - | Data base da tabela | 2025-03-31 |
| `desoneracao` | ENUM | 3 | Tipo de desoneração | 'com', 'sem' |
| `codigo` | VARCHAR | 10 | Código do serviço | '972000' |
| `descricao` | VARCHAR | 255 | Descrição do serviço | 'Comercial - caminhão basculante' |
| `unidade` | VARCHAR | 5 | Unidade de medida | 't' |
| `formula_transporte` | VARCHAR | 30 | Fórmula matemática | '0,74x1 + 0,89x2' |

### **Model a ser Criado:**
- **Arquivo:** `app/Models/Importacao/Derpr/DerprFormulaTransporte.php`
- **Namespace:** `App\Models\Importacao\Derpr`
- **Tabela:** `derpr_formula_transportes`

---

## 🔄 **FLUXO DA NOVA FUNCIONALIDADE**

### **Estrutura de Abas (Após Implementação):**
1. **Aba 1:** Serviços (Tabela Sintética) - ✅ **EXISTENTE**
2. **Aba 2:** Insumos (Tabela Analítica) - ✅ **EXISTENTE**
3. **Aba 3:** Fórmulas de Transporte - 🆕 **NOVA**
4. **Aba 4:** Gravar no Banco - ✅ **EXISTENTE (MODIFICADA)**

### **Fluxo da Nova Aba 3:**
```
PDF da Tabela de Transporte 
    ↓
Upload na nova aba "Fórmulas de Transporte"
    ↓
Processamento via rotina Python (reutilizando código existente)
    ↓
Geração de arquivo Excel no diretório temp
    ↓
Arquivo Excel passa a ser exigido na aba 4 "Gravar no Banco"
    ↓
Dados gravados no banco na tabela `derpr_formula_transportes`
```

### **Arquivo Excel Gerado:**
- **Nome:** `derpr_formulas_transporte.xlsx`
- **Estrutura:** 6 colunas (data_base, desoneracao, codigo, descricao, unidade, formula_transporte)
- **Localização:** Diretório temp do sistema

---

## 🎨 **PADRÕES VISUAIS A SEGUIR**

### **Consistência Visual:**
- **✅ Usar exatamente** as mesmas cores das abas 1 e 2
- **✅ Usar exatamente** os mesmos estilos de cards
- **✅ Usar exatamente** a mesma estrutura de interface
- **✅ Usar exatamente** os mesmos componentes

### **Componentes a Reutilizar:**
- **Progress bars** e indicadores visuais
- **Cards de workflow** com indicadores de etapa
- **Upload zones** com drag & drop
- **Modais** para visualização de dados
- **Alertas** e mensagens de status

### **Arquivos de Referência:**
- **Aba 1:** `resources/js/components/tabela_oficial/importar_derpr/components/ServicosGerais.vue`
- **Aba 2:** `resources/js/components/tabela_oficial/importar_derpr/components/Insumos.vue`

---

## 📁 **ARQUIVOS A SEREM CRIADOS/MODIFICADOS**

### **Novos Arquivos:**
1. **Migration:** `database/migrations/XX_create_derpr_formula_transportes_table.php`
2. **Model:** `app/Models/Importacao/Derpr/DerprFormulaTransporte.php`
3. **Controller Web:** `app/Http/Controllers/Web/TabelaOficial/ImportarDerprController.php` (modificar)
4. **Controller API:** `app/Http/Controllers/Api/TabelaOficial/ImportarDerprController.php` (modificar)
5. **Componente Vue:** `resources/js/components/tabela_oficial/importar_derpr/components/FormulasTransporte.vue`
6. **Script Python:** `01_python/importacao_DERPR/03.Importar-DER-PR-Formulas-Transporte.py`

### **Arquivos a Modificar:**
1. **Index.vue:** Adicionar nova aba
2. **Controller API:** Incluir validação do novo arquivo na aba 4
3. **Rotas:** Adicionar rotas para nova funcionalidade

---

## 🚀 **PLANO DE IMPLEMENTAÇÃO POR ETAPAS**

### **ETAPA 1: INFRAESTRUTURA DO BANCO** ✅ **PRÓXIMA**
- [ ] Criar migration para `derpr_formula_transportes`
- [ ] Criar model `DerprFormulaTransporte`
- **Teste:** Verificar se tabela foi criada corretamente

### **ETAPA 2: BACKEND - CONTROLLER E ROTAS** ✅ **CONCLUÍDA**
- [x] Criar controller para nova funcionalidade
- [x] Configurar rotas (web e API)
- **Teste:** Verificar se rotas estão funcionando

### **ETAPA 3: FRONTEND - NOVA ABA VUE** ✅ **CONCLUÍDA**
- [x] Criar componente Vue para "Fórmulas de Transporte"
- [x] Implementar interface seguindo padrão das abas 1 e 2
- **Teste:** Verificar se interface está funcionando

### **ETAPA 4: SCRIPT PYTHON** ✅ **CONCLUÍDA**
- [x] Criar script Python para processar PDF
- [x] Gerar arquivo Excel com estrutura correta
- **Teste:** Verificar se PDF é processado e Excel é gerado

### **ETAPA 5: INTEGRAÇÃO COM ABA 4** ✅ **CONCLUÍDA**
- [x] Incluir arquivo de fórmulas na validação da aba 4
- [x] Integrar no processamento de gravação em lote
- **Teste:** Verificar se dados são gravados no banco

---

## 🔧 **DETALHES TÉCNICOS**

### **Reutilização de Código:**
- **Padrão de Controllers:** Seguir estrutura das abas 1 e 2
- **Padrão de Vue:** Reutilizar componentes e estilos
- **Padrão de Python:** Adaptar scripts existentes
- **Padrão de Validação:** Mesmas regras de arquivo

### **Validações Necessárias:**
- **Arquivo PDF:** Extensão `.pdf`, tamanho máximo 50MB
- **Estrutura Excel:** 6 colunas obrigatórias
- **Fórmulas:** Sintaxe matemática válida
- **Códigos:** Únicos por data_base + desoneracao

### **Logs e Auditoria:**
- **Arquivo:** `storage/logs/importacao_derpr.log`
- **Registro:** Todas as operações de processamento
- **Usuário:** Informações de quem executou
- **Timestamp:** Data/hora de cada operação

---

## ❓ **PONTOS DE ATENÇÃO**

### **Desafios Técnicos:**
1. **Fórmulas complexas** com variáveis x, x1, x2
2. **Estrutura tabular** que pode variar no PDF
3. **Metadados** no cabeçalho (data base, desoneração)
4. **Validação** de sintaxe matemática

### **Integrações:**
1. **Arquivo obrigatório** na aba 4 "Gravar no Banco"
2. **Validação** de estrutura e conteúdo
3. **Logs** de processamento
4. **Tratamento de erros** robusto

---

## 📝 **NOTAS IMPORTANTES**

### **Contexto Preservado:**
- ✅ **PDF analisado** e compreendido
- ✅ **Estrutura técnica** definida
- ✅ **Padrões visuais** estabelecidos
- ✅ **Plano de implementação** detalhado
- ✅ **Divisão em etapas** para teste incremental

### **Próximo Passo:**
**ETAPA 1: Criar migration e model para `derpr_formula_transportes`**

---

## 🔄 **COMO USAR ESTE ARQUIVO**

### **Em caso de perda de contexto:**
1. **Ler este arquivo** completamente
2. **Entender a estrutura** planejada
3. **Verificar o status** das etapas
4. **Continuar** de onde parou

### **Para referência durante implementação:**
1. **Consultar** estrutura técnica
2. **Verificar** padrões visuais
3. **Validar** arquivos envolvidos
4. **Confirmar** fluxo de implementação

---

**📅 Última Atualização:** 13/08/2025  
**👤 Criado por:** Assistant  
**🎯 Status:** Planejamento Completo - Pronto para Implementação
