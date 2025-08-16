# Funcionalidade Importar SINAPI

---

## 1. Visão Geral
- **Objetivo**: Importar arquivos Excel da tabela SINAPI, processar composições, insumos, analítico e percentuais de mão de obra, e gravar os dados no banco, atualizando a view de consulta.
- **Contexto**: Módulo Tabela Oficial. Fluxo em 3 abas: (1) Composições/Insumos/Analítico (Python), (2) Percentagens de Mão de Obra (PHP), (3) Gravação no banco e atualização de view.
- **Público-alvo**: Desenvolvedores, analistas e QA.

---

## 2. Rotas/API

### Rotas Web (`routes/web.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /tabela_oficial/importar_sinapi | Web\TabelaOficial\ImportarSinapiController@index | Página principal (3 abas em Vue) |

### Rotas API (`routes/api.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| POST | /api/tabela_oficial/importar_sinapi/composicoes_insumos | Api\TabelaOficial\ImportarSinapiController@processarComposicoesInsumos | Aba 1: processa Excel (ISD, ICD, CSD, CCD, Analítico) via Python |
| GET | /api/tabela_oficial/importar_sinapi/exportar_composicoes_insumos/{tipo} | Api\TabelaOficial\ImportarSinapiController@downloadArquivoProcessado | Download processados (tipos: isd, icd, csd, ccd, analitico) |
| POST | /api/tabela_oficial/importar_sinapi/percentagens_mao_de_obra | Api\TabelaOficial\ImportarSinapiController@processarPercentagensMaoDeObra | Aba 2: processa Excel de mão de obra (SEM/COM) |
| GET | /api/tabela_oficial/importar_sinapi/download_arquivo_processado_mao_de_obra/{tipo} | Api\TabelaOficial\ImportarSinapiController@downloadArquivoProcessadoMaoDeObra | Download mão de obra (tipos: sem_desoneracao, com_desoneracao) |
| GET | /api/tabela_oficial/importar_sinapi/verificar_arquivos | Api\TabelaOficial\ImportarSinapiController@verificarArquivosDisponiveis | Aba 3: verifica presença dos 7 arquivos necessários |
| POST | /api/tabela_oficial/importar_sinapi/gravar | Api\TabelaOficial\ImportarSinapiController@gravar | Aba 3: grava dados em transação e atualiza view |

- **Exemplo (Aba 1 - multipart)**:
  - Requisição: arquivo Excel com abas ISD, ICD, CSD, CCD, Analítico
  - Resposta de sucesso (resumo):
  ```json
  {
    "success": true,
    "message": "Arquivo processado com sucesso! 5 abas processadas.",
    "saida": "processado_sinapi_XXXXXXXX",
    "metadados": {
      "mes_referencia": "01/2024",
      "data_emissao": "2024-01-15",
      "abas_processadas": ["ISD","ICD","CSD","CCD","Analítico"]
    }
  }
  ```

---

## 3. Arquivos Envolvidos
- **Controllers (API)**:
  - `app/Http/Controllers/Api/TabelaOficial/ImportarSinapiController.php`
- **Controller (Web)**:
  - `app/Http/Controllers/Web/TabelaOficial/ImportarSinapiController.php`
- **Models**:
  - `app/Models/Importacao/Sinapi/SinapiComposicao.php`
  - `app/Models/Importacao/Sinapi/SinapiInsumo.php`
  - `app/Models/Importacao/Sinapi/SinapiMaoDeObra.php`
  - `app/Models/Importacao/Sinapi/SinapiComposicaoAnalitico.php`
  - `app/Models/Importacao/Sinapi/SinapiItemAnalitico.php`
  - `app/Models/SinapiComposicaoView.php`
- **Migrations**:
  - `database/migrations/05A-importacao-sinapi-create_sinapi_composicoes_table.php`
  - `database/migrations/05B-importacao-sinapi-create_sinapi_insumos_table.php`
  - `database/migrations/05C-importacao-sinapi-create_sinapi_composicoes_analitico_table.php`
  - `database/migrations/05D-importacao-sinapi-create_sinapi_itens_analitico_table.php`
  - `database/migrations/05E-importacao-sinapi-create_sinapi_mao_de_obra_table.php`
  - `database/migrations/05G_sinapi_composicoes_view_table.php`
- **Views Blade**:
  - `resources/views/tabela_oficial/importar_sinapi/index.blade.php`
- **Componentes Vue**:
  - `resources/js/components/tabela_oficial/importar_sinapi/components/ComposicoesInsumos.vue`
  - `resources/js/components/tabela_oficial/importar_sinapi/components/PercentagensMaoDeObra.vue`
  - `resources/js/components/tabela_oficial/importar_sinapi/components/GravarSinapi.vue`
- **Rotas**:
  - `routes/web.php`, `routes/api.php`
- **Script Python**:
  - `01_python/importacao_SINAPI/01.Importar-SINAPI-Tabela-Servicos.py`

---

## 4. Estrutura de Dados

- Tabela: `sinapi_composicoes`
  - Campos principais: `id`, `grupo`, `codigo_composicao` (unique por data_base+desoneracao), `descricao`, `unidade`, `custo_pr`, `data_base`, `data_emissao`, `desoneracao`, `log_erro`, timestamps
  - Índice de unicidade: (`codigo_composicao`, `data_base`, `desoneracao`)

- Tabela: `sinapi_insumos`
  - Campos: `id`, `classificacao`, `codigo_insumo` (unique por data_base+desoneracao), `descricao`, `unidade`, `custo_pr`, `data_base`, `data_emissao`, `desoneracao`, timestamps
  - Índice de unicidade: (`codigo_insumo`, `data_base`, `desoneracao`)

- Tabela: `sinapi_composicoes_analitico`
  - Campos: `id`, `grupo`, `codigo_composicao` (unique por data_base), `descricao`, `unidade`, `situacao`, `data_base`, `data_emissao`, timestamps

- Tabela: `sinapi_itens_analitico`
  - Campos: `id`, `codigo_composicao`, `tipo_item`, `codigo_item` (unique por data_base + composição), `descricao`, `unidade`, `coeficiente`, `situacao`, `data_base`, `data_emissao`, timestamps

- Tabela: `sinapi_mao_de_obra`
  - Campos: `id`, `codigo_composicao` (unique por data_base+desoneracao), `descricao`, `unidade`, `percentagem_pr` (0–1), `data_emissao`, `data_base`, `desoneracao`, timestamps
  - Índice de unicidade: (`codigo_composicao`, `data_base`, `desoneracao`)

- View: `sinapi_composicoes_view`
  - Campos: `id`, `data_base`, `desoneracao`, `grupo`, `codigo`, `descricao`, `unidade`, `valor_mao_obra`, `valor_mat_equip`, `valor_total`, timestamps
  - Atualização: via `ImportarSinapiController@atualizarViewComposicoes()`

---

## 5. Regras de Negócio
- **Campos obrigatórios**:
  - Em `sinapi_composicoes`: `grupo`, `codigo_composicao`, `descricao`, `data_base`, `desoneracao`
  - Em `sinapi_insumos`: `classificacao`, `codigo_insumo`, `descricao`, `data_base`, `desoneracao`
  - Em `sinapi_mao_de_obra`: `codigo_composicao`, `descricao`, `unidade`, `percentagem_pr`, `data_base`, `data_emissao`, `desoneracao`
- **Validações**:
  - Uploads devem ser `.xlsx`; tratamento de datas (células B3/B4) e percentuais (coluna V) na Aba 2.
  - Percentual de mão de obra convertido para decimal [0,1].
- **Unicidade**:
  - Conforme índices únicos por tabela (ver Estrutura de Dados).
- **Relacionamentos implícitos**:
  - `sinapi_composicoes` e `sinapi_mao_de_obra` se relacionam por (`codigo_composicao`, `data_base`, `desoneracao`) para cálculo da view.

---

## 6. Funcionalidades
- **Processamento (Aba 1)**: Executa Python para gerar 5 arquivos processados + `sinapi_metadata.json`.
- **Processamento (Aba 2)**: Processa Excel de percentagens diretamente em PHP, gera 2 arquivos + metadados específicos.
- **Gravação (Aba 3)**: Detecta 7 arquivos (5 + 2), grava com transação, contabiliza criados/atualizados, atualiza view.
- **Download**: Endpoints dedicados para baixar arquivos processados.
- **Logging**: Logs estruturados em `storage/logs/importacao_sinapi.log` e `importacao_tabelas_oficiais.log` (Python).

---

## 7. Fluxo de Uso
1. Usuário acessa `/tabela_oficial/importar_sinapi`.
2. Aba 1:
   - Faz upload do Excel (5 abas).
   - Backend roda Python e salva saídas no diretório `storage/app/temp/processado_sinapi_*`.
   - Baixa arquivos processados, se necessário.
3. Aba 2:
   - Faz upload do Excel (2 abas mão de obra).
   - Backend processa e salva arquivos SEM/COM no mesmo diretório da Aba 1.
4. Aba 3:
   - Verifica os 7 arquivos esperados.
   - Inicia gravação; ao final, atualiza `sinapi_composicoes_view` para a `data_base` atual.

---

## 8. Interface/UX/UI
- **Layout**: 3 cards por aba com progress tracker (upload/processamento/resultado).
- **Componentes**:
  - `ComposicoesInsumos.vue`, `PercentagensMaoDeObra.vue`, `GravarSinapi.vue`.
- **Feedbacks**: Barras de progresso, alerts de sucesso/erro, lista de arquivos detectados.
- **Responsividade**: Ajustes de layout para ≤768px nos componentes.

---

## 9. Dependências e Integrações
- **Bibliotecas Laravel/PHP**:
  - `PhpOffice\PhpSpreadsheet` (leitura/escrita Excel), `Carbon`, `DB`, `Storage`, `Log`.
- **Python**:
  - `pandas`, `openpyxl`.
- **APIs externas**: Não há.
- **Integrações internas**:
  - View materializada atualizada a partir de `sinapi_composicoes` + `sinapi_mao_de_obra`.

---

## 10. Processos Automáticos
- **Rotinas**:
  - Geração de diretório único por processamento e limpeza de diretórios antigos na Aba 1.
  - Atualização da view `sinapi_composicoes_view` após gravação.
- **Logs**:
  - API: `storage/logs/importacao_sinapi.log`
  - Python: `storage/logs/importacao_tabelas_oficiais.log`

---

## 11. Testes
- **Aba 1**:
  - Upload válido `.xlsx` com abas ISD/ICD/CSD/CCD/Analítico.
  - Verificar geração de `sinapi_processado_*.xlsx` e `sinapi_metadata.json`.
- **Aba 2**:
  - Upload válido `.xlsx` com abas SEM/COM.
  - Verificar `sinapi_mao_obra_SEM_DESONERACAO.xlsx` e `COM_DESONERACAO.xlsx`, percentuais em [0,1], datas corretas.
- **Aba 3**:
  - Verificar endpoint de verificação retorna `pode_gravar = true`.
  - Gravação conclui com contadores coerentes (criados/atualizados/total) e view atualizada.
- **Downloads**:
  - Todos endpoints de download retornam arquivo existente e Content-Type correto.
- **Cenários de erro**:
  - Extensão inválida; faltam abas; arquivos não encontrados; diretório ausente.

---

## Checklist
- [ ] Visão Geral
- [ ] Rotas/API
- [ ] Arquivos Envolvidos
- [ ] Estrutura de Dados
- [ ] Regras de Negócio
- [ ] Funcionalidades
- [ ] Fluxo de Uso
- [ ] Interface/UX/UI
- [ ] Dependências e Integrações
- [ ] Processos Automáticos
- [ ] Testes
- [ ] Exemplos Práticos (quando aplicável)


