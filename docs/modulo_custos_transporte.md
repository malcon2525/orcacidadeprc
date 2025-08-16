# Módulo: Custos de Transporte

---

## 1. Visão Geral
O módulo de Custos de Transporte permite o cadastro, consulta, edição, exclusão e importação em lote dos custos e coeficientes de transporte utilizados no sistema de orçamentação. Ele é fundamental para o cálculo de composições e orçamentos, garantindo rastreabilidade, validação robusta e aderência às regras de negócio do domínio.

---

## 2. Rotas/API

### Web/API
- **GET /transporte/custos**
  - Lista todos os custos de transporte cadastrados.
  - Controller: `CustoTransporteController@index`
  - Retorno: JSON com lista de custos.

- **POST /transporte/custos**
  - Cria ou atualiza um custo de transporte.
  - Controller: `CustoTransporteController@store`
  - Parâmetros: sigla, código, descrição, unidade
  - Retorno: JSON do custo criado/atualizado.

- **DELETE /transporte/custos/{id}**
  - Remove um custo de transporte.
  - Controller: `CustoTransporteController@destroy`

- **GET /transporte/databases**
  - Lista as datas base e desonerações disponíveis.
  - Controller: `CustoTransporteController@databases`

- **POST /transporte/importar-coeficientes**
  - Importa coeficientes de transporte via Excel.
  - Controller: `CustoTransporteController@importarCoeficientes`
  - Parâmetro: arquivo Excel (.xls/.xlsx)
  - Retorno: JSON de sucesso ou erros detalhados.

- **GET /transporte/coeficientes**
  - Lista coeficientes para uma data base e desoneração.
  - Controller: `CoeficienteCustoTransporteController@index`
  - Parâmetros: data_base, desoneracao

- **POST /transporte/coeficientes/lote**
  - Salva coeficientes em lote para uma data base e desoneração.
  - Controller: `CoeficienteCustoTransporteController@storeLote`
  - Parâmetros: data_base, desoneracao, coeficientes[]

---

## 3. Arquivos Envolvidos
- **Migrations**:
  - `database/migrations/11A_custo_transporte_table.php`
  - `database/migrations/11B_coeficiente_custo_transporte_table.php`
- **Models**:
  - `app/Models/Transportes/CustoTransporte.php`
  - `app/Models/Transportes/CoeficienteCustoTransporte.php`
- **Controllers**:
  - `app/Http/Controllers/Web/Transporte/CustoTransporteController.php`
  - `app/Http/Controllers/Web/Transporte/CoeficienteCustoTransporteController.php`
- **Componentes Vue**:
  - `resources/js/components/transporte/CustoTransporteCrud.vue`
- **Views Blade**:
  - (Se aplicável) `resources/views/transporte/custos.blade.php`
- **Rotas**:
  - `routes/web.php`

---

## 4. Tabelas do Banco de Dados

### custo_transporte
| Campo   | Tipo    | Descrição         |
|---------|---------|-------------------|
| id      | bigint  | PK                |
| sigla   | string  | Sigla do transporte|
| codigo  | string  | Código único      |
| descricao | string| Descrição         |
| unidade | string  | Unidade           |
| ...     |         |                   |

### coeficiente_custo_transporte
| Campo             | Tipo     | Descrição                        |
|-------------------|----------|----------------------------------|
| id                | bigint   | PK                               |
| custo_transporte_id| bigint   | FK para custo_transporte         |
| data_base         | date     | Data base do coeficiente         |
| desoneracao       | string   | 'com' ou 'sem'                   |
| coeficiente_x1    | decimal  | Coeficiente X1                   |
| coeficiente_x2    | decimal  | Coeficiente X2                   |
| termo_independente| decimal  | Termo independente (K)           |
| ...               |          |                                  |

- Chave única: (custo_transporte_id, data_base, desoneracao)

---

## 5. Regras de Negócio
- Todos os campos são obrigatórios na importação e cadastro.
- `sigla`, `codigo`, `descricao`, `unidade` não podem ser vazios.
- `codigo` é único por transporte.
- `coeficiente_x1`, `coeficiente_x2`, `termo_independente` devem ser numéricos e obrigatórios na importação.
- `data_base` deve ser uma data válida.
- `desoneracao` só aceita 'com' ou 'sem'.
- Não é permitido gravar coeficientes com campos ausentes ou inválidos.
- Importação em lote é transacional: se houver erro em qualquer linha, nada é gravado.
- Logs detalhados são gerados para cada etapa da importação.

---

## 6. Funcionalidades
- Cadastro, edição e exclusão de custos de transporte.
- Importação de coeficientes via Excel, com validação robusta.
- Consulta de coeficientes por data base e desoneração.
- Edição em lote dos coeficientes diretamente na interface.
- Feedback visual e mensagens de erro detalhadas.
- Geração automática da fórmula de transporte (X1·x₁ + X2·x₂ + K).

---

## 7. Fluxo de Uso
1. O usuário acessa a tela de gerenciamento de custos de transporte.
2. É exibida uma mensagem orientando a seleção da Data Base.
3. Após selecionar a Data Base, a tabela de custos e coeficientes é exibida.
4. O usuário pode editar coeficientes diretamente na tabela ou importar em lote via Excel.
5. Ao salvar, os dados são validados e gravados; erros são exibidos em destaque.
6. O usuário pode cadastrar, editar ou excluir custos de transporte.

**Exemplo de payload de importação:**
```json
{
  "file": "coeficientes.xlsx"
}
```

**Exemplo de payload de edição em lote:**
```json
{
  "data_base": "2024-04-30",
  "desoneracao": "sem",
  "coeficientes": [
    { "custo_transporte_id": 1, "coeficiente_x1": 0.82, "coeficiente_x2": 0.00, "termo_independente": 40.26 },
    ...
  ]
}
```

---

## 8. Interface/UX/UI
- Tela principal exibe tabela de custos e coeficientes, mas só após seleção da Data Base.
- Mensagem de orientação clara é exibida enquanto nenhuma Data Base está selecionada.
- Campos X1, X2, K são editáveis diretamente na tabela.
- Importação de coeficientes via botão destacado.
- Feedback visual para sucesso e erro (toasts, alertas).
- Placeholder e obrigatoriedade visual no campo Data Base.
- Ações de editar/excluir custo são acessíveis por ícones na tabela.

---

## 9. Processos Especiais e Atualizações de Dados
- **Importação de coeficientes:**
  - Arquivo Excel deve conter todos os campos obrigatórios.
  - Validação linha a linha, com logs detalhados.
  - Se houver erro, nenhum dado é gravado.
- **Atualização em lote:**
  - Edição de coeficientes diretamente na interface, com validação antes do envio.
- **Logs:**
  - Todas as etapas críticas (importação, erros, gravação) são logadas para rastreabilidade.

---

## 10. Extras
### Cálculos e Fórmulas
- Fórmula de transporte exibida: `X1·x₁ + X2·x₂ + K` (apenas termos não nulos).

### Exemplos de Uso
- Ver prints e exemplos de payloads acima.

### Testes
- Recomenda-se testes manuais de importação, edição e validação de erros.

### Histórico de Alterações
- 2024-06-26: Refatoração completa do fluxo de importação e UX da tela principal.
- 2024-06-25: Ajuste de nomes de campos e validação rigorosa na importação.
- 2024-06-24: Implementação inicial do módulo. 