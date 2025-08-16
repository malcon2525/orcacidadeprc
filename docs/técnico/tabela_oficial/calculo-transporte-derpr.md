# Funcionalidade Cálculo de Transporte DER-PR

---

## 1. Visão Geral
- **Objetivo:** Calcular custos de transporte para composições DER-PR que possuem campo "transporte" = "A acrescer", oferecendo 4 tipos de cálculo (Local, Comercial, Outro DER-PR, Manual)
- **Contexto:** Funcionalidade integrada ao módulo de consulta DER-PR, permitindo cálculo dinâmico de custos de transporte com fórmulas matemáticas
- **Público-alvo:** Usuários finais que precisam calcular custos de transporte para orçamentos, desenvolvedores que mantêm a funcionalidade

---

## 2. Rotas/API

### Rotas API (`routes/api.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /api/preco/derpr/transporte/formulas | DerprTransporteController@obterFormulas | Busca fórmulas disponíveis para cálculo |
| GET | /api/preco/derpr/transporte/itens | DerprTransporteController@itensPorComposicao | Busca itens de transporte da composição |
| POST | /api/preco/derpr/transporte/calcular | DerprTransporteController@calcular | Calcula valor do transporte (não usado pelo frontend) |

**Exemplo de retorno - Fórmulas:**
```json
{
  "local": {
    "formula": "0,71x1 + 0,85x2 + 7,14",
    "coeficientes": {
      "x1": 0.71,
      "x2": 0.85,
      "termo_independente": 7.14
    }
  },
  "comercial": {
    "formula": "0,71x1 + 0,85x2",
    "coeficientes": {
      "x1": 0.71,
      "x2": 0.85,
      "termo_independente": 0
    }
  },
  "outros_derpr": {
    "CCB": {
      "descricao": "Cimento CPB",
      "formula": "0,72x1 + 0,87x2 + 7,27",
      "coeficientes": {
        "x1": 0.72,
        "x2": 0.87,
        "termo_independente": 7.27
      }
    }
  }
}
```

**Exemplo de retorno - Itens:**
```json
[
  {
    "id": 1,
    "codigo_servico": "400500",
    "descricao_servico": "Colchão drenante de areia",
    "unidade_servico": "m3",
    "data_base": "2024-04-30",
    "desoneracao": "sem",
    "descricao": "Areia (Trecho)",
    "codigo": "19010",
    "unidade": "t",
    "formula1": "0,99x1 + 1,19x2 + 2,48",
    "formula2": "0,71x1 + 0,85x2 + 7,14",
    "consumo": 1.5
  }
]
```

---

## 3. Arquivos Envolvidos
- **Controller:** `app/Http/Controllers/Api/DerprTransporteController.php`
- **Service:** `app/Services/DerprTransporteService.php`
- **Componente Vue:** `resources/js/components/tabela_oficial/consultar_derpr/components/ModalCalculoTransporte.vue`
- **Rotas:** `routes/api.php`

---

## 4. Estrutura de Dados

### Tabela: `derpr_transportes`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| codigo_servico | varchar | FK para composição DER-PR |
| descricao_servico | varchar | Descrição da composição |
| unidade_servico | varchar | Unidade da composição |
| data_base | date | Data base para cálculo |
| desoneracao | enum | Valores: 'com', 'sem' |
| descricao | varchar | Descrição do item de transporte |
| codigo | varchar | Código do item de transporte |
| unidade | varchar | Unidade do item de transporte |
| formula1 | varchar | Fórmula para cálculo comercial |
| formula2 | varchar | Fórmula para cálculo local |
| consumo | decimal | Quantidade consumida do item |

### Tabela: `custo_transporte`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| sigla | varchar | Sigla do tipo de transporte (ex: CCB, CCC) |
| descricao | varchar | Descrição do tipo de transporte |

### Tabela: `coeficiente_custo_transporte`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| custo_transporte_id | bigint | FK para custo_transporte |
| data_base | date | Data base para cálculo |
| desoneracao | enum | Valores: 'com', 'sem' |
| x1 | decimal | Coeficiente para X1 |
| x2 | decimal | Coeficiente para X2 |
| k | decimal | Termo independente |

---

## 5. Regras de Negócio
- **Campo obrigatório:** `codigo_servico`, `data_base`, `desoneracao` para busca de itens
- **Validação específica:** `desoneracao` deve ser 'com' ou 'sem'
- **Relacionamento obrigatório:** `coeficiente_custo_transporte` com `custo_transporte`
- **Fórmula obrigatória:** Pelo menos uma das fórmulas (formula1 ou formula2) deve existir
- **Cálculo obrigatório:** Valor final = (resultado da fórmula) × consumo

---

## 6. Funcionalidades
- **Cálculo dinâmico:** 4 tipos de cálculo (Local, Comercial, Outro DER-PR, Manual)
- **Fórmulas matemáticas:** Processamento de fórmulas no formato "ax1 + bx2 + c"
- **Interface responsiva:** Modal com accordion para múltiplos itens
- **Cálculo em tempo real:** Recalcula valores conforme usuário altera parâmetros
- **Validação de entrada:** Campos numéricos com step 0.01
- **Formatação monetária:** Exibição de valores em formato brasileiro (R$)

---

## 7. Fluxo de Uso
1. **Acesso:** Usuário acessa consulta DER-PR e clica em "Calcular Transporte" para composição com campo "transporte" = "A acrescer"
2. **Carregamento:** Sistema carrega itens de transporte da composição via API
3. **Seleção de tipo:** Para cada item, usuário escolhe tipo de cálculo (Local/Comercial/Outro DER-PR/Manual)
4. **Entrada de dados:** 
   - Para fórmulas: usuário informa X1 e X2 (distâncias em km)
   - Para Outro DER-PR: usuário seleciona sigla e informa X1/X2
   - Para Manual: usuário informa valor diretamente
5. **Cálculo automático:** Sistema aplica fórmula e multiplica pelo consumo
6. **Visualização:** Usuário vê resultado individual e total geral
7. **Confirmação:** Usuário confirma cálculo e retorna ao sistema principal

---

## 8. Interface/UX/UI
- **Layout:** Modal responsivo com accordion para múltiplos itens
- **Componentes principais:** 
  - Select para tipo de cálculo
  - Inputs numéricos para X1/X2
  - Campo de valor manual
  - Área de exibição do cálculo
  - Resumo dos totais
- **Feedbacks visuais:** 
  - Loading spinner durante carregamento
  - Badges coloridos para valores
  - Formatação monetária brasileira
  - Estados vazios informativos
- **Responsividade:** Layout adaptável com Bootstrap grid

---

## 9. Dependências e Integrações
- **Funcionalidades dependentes:** Consulta DER-PR (módulo principal)
- **Funcionalidades dependentes desta:** Relatórios que incluam custos de transporte
- **APIs externas:** Nenhuma integração externa
- **Bibliotecas:** Vue.js 3, Axios, Bootstrap 5

---

## 10. Processos Automáticos
- **Carregamento automático:** Itens carregados automaticamente ao abrir modal
- **Recálculo automático:** Valores recalculados em tempo real via watchers Vue
- **Critérios de execução:** Mudança de props (código, data base, desoneração)
- **Logs:** Logs detalhados no service para debug de cálculos

---

## 11. Testes
- **Teste de carregamento:** Verificar se itens carregam corretamente
- **Teste de tipos de cálculo:** Testar todos os 4 tipos (Local, Comercial, Outro DER-PR, Manual)
- **Teste de fórmulas:** Verificar se cálculos matemáticos estão corretos
- **Teste de validação:** Testar campos obrigatórios e formatos
- **Teste de responsividade:** Verificar comportamento em diferentes tamanhos de tela
- **Teste de integração:** Verificar se dados retornam corretamente para sistema principal

---

## 12. Exemplos Práticos

### Exemplo 1: Cálculo Local
- **Item:** Areia (Trecho)
- **Fórmula:** `0,71x1 + 0,85x2 + 7,14`
- **X1:** 10 km, **X2:** 5 km, **Consumo:** 1,5 t
- **Cálculo:** `(0,71 × 10) + (0,85 × 5) + 7,14 = 7,10 + 4,25 + 7,14 = 18,49`
- **Valor final:** `18,49 × 1,5 = R$ 27,74`

### Exemplo 2: Cálculo Manual
- **Item:** Cimento CPB
- **Tipo:** Manual
- **Valor manual:** R$ 50,00
- **Consumo:** 2,0 t
- **Valor final:** `50,00 × 2,0 = R$ 100,00`

### Exemplo 3: Cálculo Outro DER-PR
- **Item:** Brita
- **Sigla:** CCB
- **Fórmula:** `0,72x1 + 0,87x2 + 7,27`
- **X1:** 15 km, **X2:** 8 km, **Consumo:** 3,0 t
- **Cálculo:** `(0,72 × 15) + (0,87 × 8) + 7,27 = 10,80 + 6,96 + 7,27 = 25,03`
- **Valor final:** `25,03 × 3,0 = R$ 75,09`

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
