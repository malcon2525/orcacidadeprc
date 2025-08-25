# Funcionalidade Consultar DER-PR

---

## 1. Visão Geral
- **Objetivo:** Permitir consulta e visualização das tabelas oficiais de preços do Departamento de Estradas de Rodagem do Paraná (DER-PR), fornecendo acesso rápido aos custos de serviços, materiais e equipamentos utilizados em obras rodoviárias
- **Contexto:** Funcionalidade integrada ao sistema de orçamentação, fornecendo dados de referência para composição de custos e análise de preços em projetos de infraestrutura rodoviária
- **Público-alvo:** Usuários finais que precisam consultar preços DER-PR para orçamentos, desenvolvedores que mantêm a funcionalidade

---

## 2. Rotas/API

### Rotas Web (`routes/web.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /preco/consultar-derpr | ConsultarDERPRController@index | Página principal de consulta |
| GET | /preco/consultar-derpr/tabelas | ConsultarDERPRController@buscarTabelas | Lista tabelas disponíveis |
| GET | /preco/consultar-derpr/dados | ConsultarDERPRController@buscarDados | Retorna dados da tabela selecionada |

### Rotas API (`routes/api.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | `/api/tabela_oficial/consultar_derpr/buscar_tabelas` | ConsultarDerprController@buscarTabelas | Retorna tabelas disponíveis |
| GET | `/api/tabela_oficial/consultar_derpr/buscar_dados` | ConsultarDerprController@buscarDados | Retorna dados da tabela selecionada |
| GET | `/api/tabela_oficial/consultar_derpr/exportar_excel` | ConsultarDerprController@exportarExcel | Exporta dados para Excel |

**Parâmetros da rota de dados:**
- `tabela`: String no formato "DATA_BASE_DESONERACAO" (ex: "2024-04-30_sem")
- `exportar`: Boolean (opcional, para exportação completa)

**Parâmetros das rotas de transporte:**
- `codigo`: Código da composição DER-PR
- `data_base`: Data base da tabela
- `desoneracao`: Tipo de desoneração (com/sem)
- `codigo_servico`: Código do serviço (para rota de itens)

**Exemplo de retorno - Tabelas disponíveis:**
```json
[
    {
        "id": "2024-04-30_sem",
        "data_base": "2024-04-30",
        "desoneracao": "sem"
    }
]
```

**Exemplo de retorno - Dados da tabela:**
```json
{
    "data": [
        {
            "codigo": "400500",
            "descricao": "Colchão drenante de areia para fundação de aterros",
            "unidade": "m3",
            "mao_de_obra": 1.09,
            "material_equipamento": 86.60,
            "custo_total": 87.69,
            "transporte": "A acrescer"
        }
    ],
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 1
}
```

---

## 3. Arquivos Envolvidos
- **Controller Web:** `app/Http/Controllers/Web/TabelaOficial/ConsultarDerpr/ConsultarDerprController.php`
- **Controller API:** `app/Http/Controllers/Api/TabelaOficial/ConsultarDerpr/ConsultarDerprController.php`
- **Model:** `app/Models/TabelaOficial/Derpr/DerprComposicao.php`
- **View Blade:** `resources/views/tabela_oficial/consultar_derpr/index.blade.php`
- **Componente Vue:** `resources/js/components/tabela_oficial/consultar_derpr/Index.vue`
- **Componente Modal:** `resources/js/components/tabela_oficial/consultar_derpr/components/ModalTabelaDados.vue`
- **Rotas:** `routes/web.php`

---

## 4. Estrutura de Dados

### Tabela: `derpr_composicoes`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| grupo | varchar | Grupo da composição |
| data_base | date | Data base da tabela |
| desoneracao | varchar | Tipo de desoneração (com/sem) |
| codigo | varchar | Código da composição DER-PR |
| descricao | text | Descrição da composição |
| unidade | varchar | Unidade de medida |
| custo_execucao | decimal | Custo de execução |
| custo_material | decimal | Custo de material |
| custo_sub_servico | decimal | Custo de sub-serviço |
| custo_unitario | decimal | Custo unitário total |
| transporte | varchar | Indica se há transporte adicional |

### View: `derpr_composicoes_view`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK da view materializada |
| grupo | varchar | Grupo da composição |
| data_base | date | Data base da tabela |
| desoneracao | varchar | Tipo de desoneração |
| codigo | varchar | Código da composição |
| descricao | text | Descrição da composição |
| unidade | varchar | Unidade de medida |
| mao_de_obra | decimal | Custo calculado de mão de obra |
| material_equipamento | decimal | Custo calculado de material/equipamento |
| custo_total | decimal | Custo total calculado |
| transporte | varchar | Indica se há transporte adicional |

---

## 5. Regras de Negócio
- **Campo obrigatório:** `codigo`, `data_base`, `desoneracao` para identificação única
- **Validação específica:** `desoneracao` deve ser 'com' ou 'sem'
- **Cálculo obrigatório:** 
  - Mão de obra = `custo_execucao + custo_sub_servico`
  - Material/Equipamento = `custo_unitario - (custo_execucao + custo_sub_servico)`
  - Custo total = `custo_unitario`
- **Transporte:** Campo "transporte" = "A acrescer" indica necessidade de cálculo adicional
- **Paginação:** 20 itens por página por padrão

---

## 6. Funcionalidades
- **Consulta de tabelas:** Listagem de tabelas disponíveis por data base e desoneração
- **Visualização de dados:** Grid responsivo com paginação
- **Filtros de busca:** Por código e descrição
- **Exportação:** Para Excel com dados completos
- **Cálculo automático:** Valores de mão de obra, material/equipamento e custo total
- **Cálculo de transporte:** Modal especializado para itens com transporte adicional
- **Busca rápida:** Zoom de serviços para localização rápida
- **Responsividade:** Interface adaptável para diferentes dispositivos

---

## 7. Fluxo de Uso
1. **Acesso:** Usuário acessa `/preco/consultar-derpr`
2. **Carregamento:** Sistema carrega lista de tabelas disponíveis automaticamente
3. **Seleção:** Usuário escolhe tabela (data base + desoneração)
4. **Consulta:** Usuário clica em "Consultar" para buscar dados
5. **Visualização:** Sistema exibe dados em grid com paginação
6. **Filtros:** Usuário pode filtrar por código ou descrição
7. **Exportação:** Usuário pode exportar dados para Excel
8. **Transporte:** Para itens com campo "transporte" = "A acrescer":
   - Usuário clica no ícone de transporte
   - Sistema abre modal de cálculo
   - Usuário define parâmetros (X1, X2) ou valores manuais
   - Sistema calcula custo total com transporte
9. **Resultado:** Usuário visualiza custos calculados e pode confirmar

---

## 8. Interface/UX/UI
- **Layout:** Página responsiva com grid de dados
- **Componentes principais:** 
  - Select para escolha da tabela
  - Campo de busca por código/descrição
  - Grid de dados com paginação
  - Botões de exportação
  - Modal de cálculo de transporte
- **Feedbacks visuais:** 
  - Loading spinner durante carregamento
  - Ícones para transporte adicional
  - Formatação monetária brasileira
  - Estados vazios informativos
- **Responsividade:** Layout adaptável com Bootstrap grid
- **Cores:** Padrão azul (#18578A) e cinza

---

## 9. Dependências e Integrações
- **Funcionalidades dependentes:** Nenhuma (funcionalidade independente)
- **Funcionalidades dependentes desta:** 
  - Cálculo de Transporte DER-PR (modal integrado)
  - Relatórios que utilizem dados DER-PR
- **APIs externas:** Nenhuma integração externa
- **Bibliotecas:** Vue.js 3, Axios, Bootstrap 5, DataTables

---

## 10. Processos Automáticos
- **Carregamento automático:** Tabelas disponíveis carregadas ao abrir página
- **Cálculo automático:** Valores calculados em tempo real via SQL
- **Paginação automática:** 20 itens por página
- **Logs:** Logs de consultas para auditoria
- **Cache:** View materializada para otimização de performance

---

## 11. Testes
- **Teste de carregamento:** Verificar se tabelas carregam corretamente
- **Teste de consulta:** Testar busca com diferentes filtros
- **Teste de paginação:** Verificar navegação entre páginas
- **Teste de exportação:** Verificar se Excel é gerado corretamente
- **Teste de responsividade:** Verificar comportamento em diferentes dispositivos
- **Teste de cálculo:** Verificar se valores são calculados corretamente
- **Teste de transporte:** Verificar modal de cálculo de transporte

---

## 12. Exemplos Práticos

### Exemplo 1: Consulta Básica
- **URL:** `/preco/consultar-derpr`
- **Tabela selecionada:** "2024-04-30_sem"
- **Filtro:** Código "400500"
- **Resultado:** Composição "Colchão drenante de areia" com custo total R$ 87,69

### Exemplo 2: Cálculo de Transporte
- **Item:** Composição com campo "transporte" = "A acrescer"
- **Ação:** Clicar no ícone de transporte
- **Modal:** Abre com itens de transporte disponíveis
- **Cálculo:** Usuário define X1=10km, X2=5km
- **Resultado:** Custo adicional de transporte calculado

### Exemplo 3: Exportação
- **Ação:** Clicar em "Exportar Excel"
- **Resultado:** Arquivo Excel com todos os dados da tabela
- **Formato:** Dados organizados em planilha com formatação

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
