# Consultar SINAPI

---

## 1. Visão Geral
- **Objetivo:** Permitir a consulta das tabelas oficiais do SINAPI (com e sem desoneração), visualização detalhada dos itens e exportação para Excel.
- **Contexto:** Funcionalidade da área `tabela_oficial` que consulta diretamente a view consolidada `sinapi_composicoes_view`, sem cálculos no backend.
- **Público‑alvo:** Usuários do sistema que precisam consultar preços oficiais e desenvolvedores que dão manutenção.

---

## 2. Rotas/API

### Rotas Web (`routes/web.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | `/tabela_oficial/consultar_sinapi` | `App\Http\Controllers\Web\TabelaOficial\ConsultarSinapiController@index` | Página principal da consulta (carrega o componente Vue). |

### Rotas API (`routes/api.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | `/api/tabela_oficial/consultar_sinapi/tabelas` | `App\Http\Controllers\Api\TabelaOficial\ConsultarSinapiController@buscarTabelas` | Lista datas-base disponíveis e status de desoneração (cartões da tela). |
| GET | `/api/tabela_oficial/consultar_sinapi/dados` | `App\Http\Controllers\Api\TabelaOficial\ConsultarSinapiController@buscarDados` | Retorna itens de uma tabela (param `tabela=YYYY-MM-DD|com|sem`). |
| GET | `/api/tabela_oficial/consultar_sinapi/exportar-excel` | `App\Http\Controllers\Api\TabelaOficial\ConsultarSinapiController@exportarExcel` | Exporta os dados filtrados da tabela em Excel. |
| GET | `/api/tabela_oficial/consultar_sinapi/zoom-servicos` | `App\Http\Controllers\Api\TabelaOficial\ConsultarSinapiController@zoomServicos` | Zoom de serviços (apoio a buscas/auto‑complete). |

**Exemplo de retorno (dados):**
```json
{
  "data": [
    {
      "grupo": "Acessibilidade",
      "codigo": "104658",
      "descricao": "PISO PODOTÁtil...",
      "unidade": "M2",
      "valor_mao_obra": 45.33,
      "valor_mat_equip": 108.70,
      "valor_total": 154.03,
      "desoneracao": "sem"
    }
  ],
  "total": 1234
}
```

---

## 3. Arquivos Envolvidos
- **Controllers (Web):** `app/Http/Controllers/Web/TabelaOficial/ConsultarSinapiController.php`
- **Controllers (API):** `app/Http/Controllers/Api/TabelaOficial/ConsultarSinapiController.php`
- **Models relevantes:** `app/Models/SinapiComposicaoView.php`
- **Views Blade:** `resources/views/tabela_oficial/consultar_sinapi/index.blade.php`
- **Componentes Vue:**
  - `resources/js/components/tabela_oficial/consultar_sinapi/ConsultarSinapi.vue`
  - `resources/js/components/tabela_oficial/consultar_sinapi/components/ModalTabelaDados.vue`
- **Rotas:** `routes/web.php`, `routes/api.php`

---

## 4. Estrutura de Dados
- **View consultada:** `sinapi_composicoes_view`

| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| `data_base` | date | Data-base da composição |
| `desoneracao` | enum('com','sem') | Indica desoneração |
| `grupo` | varchar | Grupo/segmento |
| `codigo` | varchar | Código SINAPI (chave de exibição) |
| `descricao` | text | Descrição do serviço |
| `unidade` | varchar | Unidade de medida |
| `valor_mao_obra` | decimal | Valor de mão de obra |
| `valor_mat_equip` | decimal | Valor de materiais/equipamentos |
| `valor_total` | decimal | Custo total (pré‑calculado na view) |

> Observação: a funcionalidade não realiza cálculos; todos os valores são retornados prontos pela view.

---

## 5. Regras de Negócio
- Consulta sempre pela view `sinapi_composicoes_view` (proibido calcular no backend).
- Filtro de tabela via parâmetro `tabela` no formato `YYYY-MM-DD|com|sem`.
- Exportação em Excel espelha os dados exibidos (mesmas colunas e filtros aplicados no modal).
- Paginação/segmentação é feita no frontend (com controle de "itens por página").

---

## 6. Funcionalidades
- Listagem de tabelas (cards) por data-base e desoneração.
- Abertura de modal fullscreen com:
  - Filtros client-side: `grupo`, `codigo`, `descricao` (form-floating).
  - Tabela com colunas: Grupo, Código, Descrição, Unidade, Mão de Obra, Mat./Equip., Custo Total, Desoneração.
  - Cabeçalho fixo, rolagem vertical, paginação client-side (10/25/50/100/150).
  - Exportação para Excel.

---

## 7. Fluxo de Uso
1. Usuário acessa: Menu → Tabelas Oficiais → Consultar SINAPI.
2. A tela lista os cartões (datas-base × desoneração) via `buscarTabelas`.
3. Ao clicar em um cartão, o modal é aberto e os dados são buscados por `buscarDados`.
4. Usuário aplica filtros locais, navega nas páginas, e pode exportar para Excel.
5. O modal é fechado (tecla ESC, X ou botão fechar), restaurando o scroll da página.

---

## 8. Interface/UX/UI
- Layout idêntico ao padrão da consulta DER‑PR:
  - Modal fullscreen com header em gradiente azul→verde (ícone + título).
  - Filtros form-floating no topo.
  - Tabela responsiva com cabeçalho fixo e hover nas linhas.
  - Badges de desoneração (verde = Com, azul = Sem).
- Comportamento do modal:
  - Aberto apenas quando uma tabela é selecionada.
  - Fechamento garante remoção do backdrop e restauração do scroll.

---

## 9. Dependências e Integrações
- **Bibliotecas:** Vue 3 (Composition API), Bootstrap 5 (modal), Axios, PhpSpreadsheet (exportação).
- **Serviços internos:** nenhuma integração externa; leitura direta de BD via Laravel/DB.

---

## 10. Processos Automáticos
- Não há rotinas agendadas. Todas as operações são sob demanda, via requisições do usuário.

---

## 11. Testes
- Abrir tela e verificar renderização dos cartões (datas e desoneração).
- Abrir modal (cada cartão) e validar:
  - Carregamento dos dados (colunas e formatação monetária BRL).
  - Filtros `grupo`, `codigo`, `descricao` funcionando.
  - Paginação client-side atualizando contadores (from/to/total).
  - Exportação Excel abrindo novo arquivo.
  - Fechamento do modal sem travar a página (sem `.modal-open`/backdrop restantes e com scroll restaurado).

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
