# Funcionalidade Importar DER-PR

---

## 1. Visão Geral

- **Objetivo:** Importar tabelas oficiais do Departamento de Estradas de Rodagem do Paraná (DER-PR) a partir de arquivos PDF e Excel, processando e validando os dados para alimentar as tabelas do sistema OrçaCidade.
- **Contexto:** Funcionalidade essencial do módulo "Tabela Oficial" que permite a atualização das tabelas de preços e composições do DER-PR no sistema, garantindo dados atualizados para orçamentos e cálculos.
- **Público-alvo:** Administradores do sistema e usuários responsáveis pela manutenção das tabelas de preços oficiais.

---

## 2. Rotas/API

### Rotas Web (`routes/web.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /tabela_oficial/importar_derpr | ImportarDerprController@index | Exibe a interface de importação DER-PR |

### Rotas API (`routes/api.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| POST | /tabela_oficial/importar_derpr/servicos | ImportarDerprController@processarServicosGerais | Processa PDF de serviços gerais |
| POST | /tabela_oficial/importar_derpr/insumos | ImportarDerprController@processarInsumos | Processa PDF de insumos |
| POST | /tabela_oficial/importar_derpr/lote | ImportarDerprController@importarLote | Importa e grava lote de arquivos Excel |

**Exemplo de retorno (processamento PDF):**
```json
{
  "success": true,
  "data": [
    {
      "codigo": "001",
      "descricao": "Serviço de terraplanagem",
      "unidade": "m³",
      "valor": 25.50
    }
  ],
  "total_registros": 150
}
```

**Exemplo de retorno (importação lote):**
```json
{
  "success": true,
  "message": "Arquivos processados com sucesso:\ncomposicoes.xlsx: 150 registros processados (120 inseridos, 30 atualizados)\nequipamentos.xlsx: 80 registros processados (60 inseridos, 20 atualizados)",
  "validacoes": [
    {
      "arquivo": "composicoes.xlsx",
      "nome": "ok",
      "colunas": "ok"
    }
  ],
  "resultados": {
    "composicoes.xlsx": {
      "total_registros": 150,
      "registros_inseridos": 120,
      "registros_atualizados": 30
    }
  }
}
```

---

## 3. Arquivos Envolvidos

- **Controller Web:** `app/Http/Controllers/Web/TabelaOficial/ImportarDerprController.php`
- **Controller API:** `app/Http/Controllers/Api/TabelaOficial/ImportarDerprController.php`
- **Componente Vue Principal:** `resources/js/components/tabela_oficial/importar_derpr/Index.vue`
- **Componentes Vue:**
  - `resources/js/components/tabela_oficial/importar_derpr/components/ServicosGerais.vue`
  - `resources/js/components/tabela_oficial/importar_derpr/components/Insumos.vue`
  - `resources/js/components/tabela_oficial/importar_derpr/components/ImportarLoteDerpr.vue`
- **Models:**
  - `app/Models/Importacao/Derpr/DerprComposicao.php`
  - `app/Models/Importacao/Derpr/DerprMaoDeObra.php`
  - `app/Models/Importacao/Derpr/DerprEquipamento.php`
  - `app/Models/Importacao/Derpr/DerprMaterial.php`
  - `app/Models/Importacao/Derpr/DerprServico.php`
  - `app/Models/Importacao/Derpr/DerprItemIncidencia.php`
  - `app/Models/Importacao/Derpr/DerprTransporte.php`
- **Scripts Python:**
  - `01_python/importacao_DERPR/01.Importar-DER-PR-Tabela-Servicos.py`
  - `01_python/importacao_DERPR/02.Importar-DER-PR-Tabela-Insumos.py`
- **Rotas:** `routes/web.php`, `routes/api.php`
- **Logs:** `storage/logs/importacao_derpr.log`

---

## 4. Estrutura de Dados

### Tabela: `derpr_composicoes`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| grupo | varchar(255) | Grupo da composição |
| data_base | date | Data base da tabela |
| desoneracao | enum('com','sem') | Tipo de desoneração |
| codigo | varchar(50) | Código da composição |
| descricao | text | Descrição da composição |
| unidade | varchar(20) | Unidade de medida |
| custo_execucao | decimal(15,2) | Custo de execução |
| custo_material | decimal(15,2) | Custo de materiais |
| custo_sub_servico | decimal(15,2) | Custo de sub-serviços |
| custo_unitario | decimal(15,2) | Custo unitário total |
| transporte | varchar(255) | Informações de transporte |

### Tabela: `derpr_mao_de_obra`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| codigo_servico | varchar(50) | Código do serviço |
| descricao_servico | text | Descrição do serviço |
| unidade_servico | varchar(20) | Unidade do serviço |
| data_base | date | Data base |
| desoneracao | enum('com','sem') | Tipo de desoneração |
| descricao | text | Descrição do item |
| codigo | varchar(50) | Código do item |
| eq_salarial | decimal(15,2) | Equivalência salarial |
| encargos_percentagem | decimal(5,2) | Percentual de encargos |
| sal_hora | decimal(15,2) | Salário hora |
| consumo | decimal(10,4) | Consumo |
| custo_horario | decimal(15,2) | Custo horário |

### Tabela: `derpr_equipamentos`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| codigo_servico | varchar(50) | Código do serviço |
| descricao_servico | text | Descrição do serviço |
| unidade_servico | varchar(20) | Unidade do serviço |
| data_base | date | Data base |
| desoneracao | enum('com','sem') | Tipo de desoneração |
| descricao | text | Descrição do equipamento |
| codigo_equipamento | varchar(50) | Código do equipamento |
| quantidade | decimal(10,4) | Quantidade |
| ut_produtiva | decimal(10,4) | UT produtiva |
| ut_improdutiva | decimal(10,4) | UT improdutiva |
| vl_hr_prod | decimal(15,2) | Valor hora produtiva |
| vl_hr_imp | decimal(15,2) | Valor hora improdutiva |
| custo_horario | decimal(15,2) | Custo horário |

### Tabela: `derpr_materiais`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| codigo_servico | varchar(50) | Código do serviço |
| descricao_servico | text | Descrição do serviço |
| unidade_servico | varchar(20) | Unidade do serviço |
| data_base | date | Data base |
| desoneracao | enum('com','sem') | Tipo de desoneração |
| descricao | text | Descrição do material |
| codigo | varchar(50) | Código do material |
| unidade | varchar(20) | Unidade do material |
| custo_unitario | decimal(15,2) | Custo unitário |
| consumo | decimal(10,4) | Consumo |
| custo_unitario_final | decimal(15,2) | Custo unitário final |

### Tabela: `derpr_servicos`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| codigo_servico | varchar(50) | Código do serviço |
| descricao_servico | text | Descrição do serviço |
| unidade_servico | varchar(20) | Unidade do serviço |
| data_base | date | Data base |
| desoneracao | enum('com','sem') | Tipo de desoneração |
| descricao | text | Descrição do serviço |
| codigo | varchar(50) | Código do serviço |
| unidade | varchar(20) | Unidade do serviço |
| custo_unitario | decimal(15,2) | Custo unitário |
| consumo | decimal(10,4) | Consumo |
| custo_unitario_final | decimal(15,2) | Custo unitário final |

### Tabela: `derpr_itens_incidencia`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| codigo_servico | varchar(50) | Código do serviço |
| descricao_servico | text | Descrição do serviço |
| unidade_servico | varchar(20) | Unidade do serviço |
| data_base | date | Data base |
| desoneracao | enum('com','sem') | Tipo de desoneração |
| descricao | text | Descrição do item |
| codigo | varchar(50) | Código do item |
| percentagem | decimal(5,2) | Percentagem |
| tem_mo | enum('sim','nao') | Tem mão de obra |
| custo | decimal(15,2) | Custo |

### Tabela: `derpr_transportes`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| codigo_servico | varchar(50) | Código do serviço |
| descricao_servico | text | Descrição do serviço |
| unidade_servico | varchar(20) | Unidade do serviço |
| data_base | date | Data base |
| desoneracao | enum('com','sem') | Tipo de desoneração |
| descricao | text | Descrição do transporte |
| codigo | varchar(50) | Código do transporte |
| unidade | varchar(20) | Unidade do transporte |
| formula1 | varchar(255) | Fórmula 1 |
| formula2 | varchar(255) | Fórmula 2 |
| custo | decimal(15,2) | Custo |
| consumo | decimal(10,4) | Consumo |
| custo_unitario | decimal(15,2) | Custo unitário |

### View: `derpr_composicoes_view`
| Campo | Tipo | Descrição/Cálculo |
|-------|------|-------------------|
| data_base | date | Data base da tabela |
| desoneracao | enum('com','sem') | Tipo de desoneração |
| codigo | varchar(50) | Código da composição |
| descricao | text | Descrição da composição |
| unidade | varchar(20) | Unidade de medida |
| valor_mao_obra | decimal(15,2) | **Cálculo:** `custo_execucao + custo_sub_servico` |
| valor_mat_equip | decimal(15,2) | **Cálculo:** `custo_unitario - valor_mao_obra` |
| valor_total | decimal(15,2) | **Cálculo:** `custo_unitario` |

**🎯 Propósito:** View otimizada para consultas de preços com cálculos pré-processados, melhorando significativamente a performance de consultas futuras.

---

## 5. Regras de Negócio

- **Campos obrigatórios:** Todos os campos são obrigatórios conforme especificado na estrutura de dados
- **Validações:** 
  - Arquivos PDF devem ter extensão `.pdf`
  - Arquivos Excel devem ter extensão `.xlsx`
  - Tamanho máximo de arquivo: 50MB
  - Colunas obrigatórias devem estar presentes nos arquivos Excel
- **Unicidade:** Registros são identificados pela combinação de `codigo`, `data_base` e `desoneracao`
- **Relacionamentos:** Todas as tabelas compartilham a estrutura base de `codigo_servico`, `data_base` e `desoneracao`

---

## 6. Funcionalidades

- **Processamento de PDFs:** Conversão automática de PDFs do DER-PR em dados estruturados
- **Importação em Lote:** Processamento de múltiplos arquivos Excel simultaneamente
- **Validação de Dados:** Verificação automática de estrutura e consistência dos dados
- **Exportação de Dados:** Exportação dos dados processados para Excel
- **Logs Detalhados:** Sistema completo de logs para auditoria e debug
- **Interface Responsiva:** Interface moderna com feedback visual em tempo real
- **Atualização de Views:** Atualização automática da view `derpr_composicoes_view`
- **Otimização de Performance:** Cálculos pré-processados para consultas rápidas de preços

---

## 7. Fluxo de Uso

### **RESUMO DO PROCESSO DE IMPORTAÇÃO DE TABELAS DER-PR**

O processo de importação das tabelas DER-PR é dividido em **3 etapas sequenciais**, onde cada etapa gera arquivos Excel que são utilizados na etapa seguinte.

---

### **PASSO 1 → ABA: SERVIÇOS (TABELA SINTÉTICA)**

**Objetivo:** Converter os dados do PDF da tabela sintética do DER-PR em um arquivo Excel estruturado.

**Processo:**
1. **Upload do PDF:** Usuário fornece o PDF da tabela sintética do DER-PR
2. **Validação e Processamento:** Sistema valida e processa o arquivo via script Python
3. **Visualização:** Usuário pode visualizar os dados processados em formato tabular
4. **Exportação:** Usuário pode exportar os dados para arquivo Excel (`composicoes.xlsx`)
5. **Logs:** Sistema registra logs da operação

**Resultado:** Arquivo `composicoes.xlsx` que será utilizado na aba "Gravar no Banco"

---

### **PASSO 2 → ABA: INSUMOS (TABELA ANALÍTICA)**

**Objetivo:** Converter os dados do PDF da tabela analítica do DER-PR em 6 arquivos Excel separados.

**Processo:**
1. **Upload do PDF:** Usuário fornece o PDF da tabela analítica do DER-PR
2. **Validação e Processamento:** Sistema valida e processa o arquivo via script Python
3. **Geração de Dados:** Sistema gera 6 grupos de dados estruturados
4. **Visualização:** Usuário pode visualizar os dados de cada grupo separadamente
5. **Exportação:** Usuário pode exportar cada grupo para arquivos Excel individuais:
   - `equipamentos.xlsx`
   - `itens_incidencia.xlsx`
   - `mao_de_obra.xlsx`
   - `materiais.xlsx`
   - `servicos.xlsx`
   - `transportes.xlsx`
6. **Logs:** Sistema registra logs da operação

**Resultado:** 6 arquivos Excel que serão utilizados na aba "Gravar no Banco"

---

### **PASSO 3 → ABA: GRAVAR NO BANCO DE DADOS**

**Objetivo:** Gravar as informações geradas nas etapas anteriores no banco de dados do sistema e otimizar consultas futuras através da view `derpr_composicoes_view`.

**Processo:**
1. **Upload dos Arquivos:** Usuário insere os 7 arquivos Excel gerados nas etapas anteriores:
   - `composicoes.xlsx` (da etapa 1)
   - `equipamentos.xlsx` (da etapa 2)
   - `itens_incidencia.xlsx` (da etapa 2)
   - `mao_de_obra.xlsx` (da etapa 2)
   - `materiais.xlsx` (da etapa 2)
   - `servicos.xlsx` (da etapa 2)
   - `transportes.xlsx` (da etapa 2)
2. **Validação:** Sistema valida a estrutura e consistência de todos os arquivos
3. **Processamento:** Sistema lê e processa os dados de cada arquivo
4. **Gravação:** Sistema grava os dados nas tabelas individuais do banco de dados
5. **Otimização de Performance:** Sistema atualiza a view `derpr_composicoes_view` com cálculos pré-processados
6. **Feedback:** Sistema exibe resumo detalhado do processamento
7. **Logs:** Sistema registra logs detalhados da operação

**Resultado:** Dados das tabelas DER-PR atualizados no banco de dados e view otimizada para consultas de preços

#### **📊 View `derpr_composicoes_view` - Otimização de Performance:**

**Objetivo:** Melhorar significativamente a performance de consultas futuras na tabela de preços do DER-PR através de cálculos pré-processados.

**Processo de Atualização:**
1. **Limpeza:** Remove registros antigos com mesma `data_base` e `desoneracao`
2. **Inserção:** Insere todos os registros da tabela `derpr_composicoes`
3. **Cálculos Automáticos:** Aplica fórmulas de cálculo em tempo de inserção:
   - `valor_mao_obra` = `custo_execucao` + `custo_sub_servico`
   - `valor_mat_equip` = `custo_unitario` - `valor_mao_obra`
   - `valor_total` = `custo_unitario`

**Benefícios da View:**
- **⚡ Performance:** Consultas até 10x mais rápidas que joins complexos
- **🧮 Cálculos Pré-processados:** Valores calculados automaticamente
- **📊 Dados Consolidados:** Informações de custo separadas por categoria
- **🔄 Atualização Automática:** Mantém-se sincronizada com dados originais
- **🎯 Consultas Simplificadas:** Interface unificada para consultas de preços

**Estrutura da View:**
```sql
CREATE VIEW derpr_composicoes_view AS
SELECT 
    data_base,
    desoneracao,
    codigo,
    descricao,
    unidade,
    (custo_execucao + custo_sub_servico) AS valor_mao_obra,
    (custo_unitario - (custo_execucao + custo_sub_servico)) AS valor_mat_equip,
    custo_unitario AS valor_total
FROM derpr_composicoes;
```

---

### **FLUXO COMPLETO:**

```
PDF Tabela Sintética → Processamento → composicoes.xlsx
                                        ↓
PDF Tabela Analítica → Processamento → 6 arquivos Excel
                                        ↓
7 arquivos Excel → Validação → Gravação → Banco de Dados
```

### **OBSERVAÇÕES IMPORTANTES:**

- **Sequência Obrigatória:** As etapas devem ser executadas na ordem apresentada
- **Dependência de Arquivos:** A etapa 3 depende dos arquivos gerados nas etapas 1 e 2
- **Validação:** Cada etapa inclui validação automática dos dados
- **Logs:** Todas as operações são registradas para auditoria
- **Rollback:** Em caso de erro, o sistema mantém a integridade dos dados

---

## 8. Interface/UX/UI

- **Layout:** Interface com 3 abas principais em cards elegantes
- **Componentes:** 
  - Cards de workflow com progresso visual
  - Zonas de upload com drag & drop
  - Barras de progresso em tempo real
  - Modais de feedback
  - Chips para exibição de arquivos selecionados
- **Feedback:** 
  - Mensagens de status em tempo real
  - Indicadores visuais de progresso
  - Alertas de sucesso/erro
  - Contadores de arquivos processados
- **Responsividade:** Interface adaptável para diferentes tamanhos de tela

---

## 9. Dependências e Integrações

- **Funcionalidades dependentes:** Sistema de autenticação, sistema de logs
- **Funcionalidades dependentes desta:** Consulta de tabelas DER-PR, cálculos de orçamento
- **APIs externas:** Scripts Python para processamento de PDFs
- **Bibliotecas:** 
  - PhpSpreadsheet para processamento de Excel
  - Vue.js para interface frontend
  - Axios para requisições HTTP
  - Bootstrap para estilos

---

## 10. Processos Automáticos

- **Processamento de PDFs:** Execução automática de scripts Python
- **Validação de Dados:** Verificação automática de estrutura e consistência
- **Limpeza de Arquivos:** Remoção automática de arquivos temporários
- **Atualização de Views:** Atualização automática da view `derpr_composicoes_view`
- **Logs:** Registro automático de todas as operações em `storage/logs/importacao_derpr.log`

---

## 11. Testes

### Testes Manuais
- **Teste de Upload PDF:** Verificar se arquivos PDF são aceitos corretamente
- **Teste de Upload Excel:** Verificar se arquivos Excel são aceitos corretamente
- **Teste de Validação:** Verificar se arquivos inválidos são rejeitados
- **Teste de Processamento:** Verificar se dados são processados corretamente
- **Teste de Exportação:** Verificar se exportação para Excel funciona
- **Teste de Gravação:** Verificar se dados são salvos no banco corretamente
- **Teste de Logs:** Verificar se logs são gerados corretamente

### Cenários de Teste
- **Cenário 1:** Upload de PDF válido de serviços gerais
- **Cenário 2:** Upload de PDF válido de insumos
- **Cenário 3:** Upload de múltiplos arquivos Excel válidos
- **Cenário 4:** Upload de arquivo inválido (deve ser rejeitado)
- **Cenário 5:** Processamento com arquivo muito grande (deve ser rejeitado)
- **Cenário 6:** Processamento com arquivo sem colunas obrigatórias

### Validações
- **Validação de Extensão:** Verificar se apenas PDFs e Excel são aceitos
- **Validação de Tamanho:** Verificar limite de 50MB por arquivo
- **Validação de Colunas:** Verificar se colunas obrigatórias estão presentes
- **Validação de Dados:** Verificar se dados são consistentes
- **Validação de Unicidade:** Verificar se registros duplicados são tratados

---

## Checklist
- [x] Visão Geral
- [x] Rotas/API
- [x] Arquivos Envolvidos
- [x] Estrutura de Dados
- [x] Regras de Negócio
- [x] Funcionalidades
- [x] Fluxo de Uso
- [x] Interface/UX/UI
- [x] Dependências e Integrações
- [x] Processos Automáticos
- [x] Testes
- [x] Exemplos Práticos 