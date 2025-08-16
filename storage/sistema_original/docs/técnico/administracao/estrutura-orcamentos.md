# Funcionalidade Estrutura de Orçamentos

---

## 1. Visão Geral
- **Objetivo:** Gerenciar a estrutura hierárquica de orçamentos através de Grandes Itens e Subgrupos, permitindo importação em massa via Excel e visualização integrada
- **Contexto:** Sistema central para definição da estrutura base de orçamentos municipais, substituindo o sistema antigo fragmentado
- **Público-alvo:** Administradores do sistema, técnicos de orçamento e usuários responsáveis pela configuração estrutural

---

## 2. Rotas/API

### Rotas Web (`routes/web.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /administracao/estrutura-orcamento | GestaoEstruturaOrcamento | Página principal com abas integradas |

### Rotas API (`routes/api.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /api/administracao/estrutura-orcamento/tipos | TipoOrcamentoController@index | Listar tipos de orçamento |
| POST | /api/administracao/estrutura-orcamento/tipos | TipoOrcamentoController@store | Criar tipo de orçamento |
| PUT | /api/administracao/estrutura-orcamento/tipos/{id} | TipoOrcamentoController@update | Atualizar tipo de orçamento |
| DELETE | /api/administracao/estrutura-orcamento/tipos/{id} | TipoOrcamentoController@destroy | Excluir tipo de orçamento |
| GET | /api/administracao/estrutura-orcamento/grandes-itens | GrandeItemController@index | Listar grandes itens |
| POST | /api/administracao/estrutura-orcamento/grandes-itens | GrandeItemController@store | Criar grande item |
| PUT | /api/administracao/estrutura-orcamento/grandes-itens/{id} | GrandeItemController@update | Atualizar grande item |
| DELETE | /api/administracao/estrutura-orcamento/grandes-itens/{id} | GrandeItemController@destroy | Excluir grande item |
| POST | /api/administracao/estrutura-orcamento/grandes-itens/reordenar | GrandeItemController@reordenar | Reordenar grandes itens |
| GET | /api/administracao/estrutura-orcamento/subgrupos | SubGrupoController@index | Listar subgrupos |
| POST | /api/administracao/estrutura-orcamento/subgrupos | SubGrupoController@store | Criar subgrupo |
| PUT | /api/administracao/estrutura-orcamento/subgrupos/{id} | SubGrupoController@update | Atualizar subgrupo |
| DELETE | /api/administracao/estrutura-orcamento/subgrupos/{id} | SubGrupoController@destroy | Excluir subgrupo |
| POST | /api/administracao/estrutura-orcamento/subgrupos/reordenar | SubGrupoController@reordenar | Reordenar subgrupos |
| POST | /api/administracao/estrutura-orcamento/importar | ImportacaoController@importar | Importar estrutura via Excel |
| DELETE | /api/administracao/estrutura-orcamento/limpar/{tipo_id} | LimparEstruturaController@limpar | Limpar estrutura completa |

**Exemplo de retorno da API:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "descricao": "SERVIÇOS PRELIMINARES",
    "ordem": 1,
    "sub_grupos": [
      {
        "id": 1,
        "descricao": "LIMPEZA DE TERRENO",
        "ordem": 1
      }
    ]
  }
}
```

---

## 3. Arquivos Envolvidos
- **Controllers API:** 
  - `app/Http/Controllers/Api/Administracao/EstruturaOrcamento/TipoOrcamentoController.php`
  - `app/Http/Controllers/Api/Administracao/EstruturaOrcamento/GrandeItemController.php`
  - `app/Http/Controllers/Api/Administracao/EstruturaOrcamento/SubGrupoController.php`
  - `app/Http/Controllers/Api/Administracao/EstruturaOrcamento/ImportacaoController.php`
  - `app/Http/Controllers/Api/Administracao/EstruturaOrcamento/LimparEstruturaController.php`
- **Models:** 
  - `app/Models/Orcamento/TipoOrcamento.php`
  - `app/Models/Orcamento/GrandeItem.php`
  - `app/Models/Orcamento/SubGrupo.php`
- **Migrations:** 
  - `database/migrations/01A-user_e_auth_create_users_table.php`
  - `database/migrations/08A_create_orcamentos_table.php`
  - `database/migrations/08B_create_bdis_table.php`
- **Componentes Vue:** 
  - `resources/js/components/administracao/estrutura-orcamento/GestaoEstruturaOrcamento.vue`
  - `resources/js/components/administracao/estrutura-orcamento/ListaTipoOrcamento.vue`
  - `resources/js/components/administracao/estrutura-orcamento/EstruturaOrcamento.vue`
  - `resources/js/components/administracao/estrutura-orcamento/VisualizacaoIntegrada.vue`
- **CSS Compartilhado:** 
  - `resources/css/estrutura-orcamento-shared.css`
- **Rotas:** `routes/web.php`, `routes/api.php`
- **App.js:** `resources/js/app.js`

---

## 4. Estrutura de Dados

### Tabela: `tipos_orcamentos`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| descricao | varchar(255) | Nome do tipo de orçamento |
| status | enum('ativo','inativo') | Status do tipo (padrão: ativo) |
| created_at | timestamp | Data de criação |
| updated_at | timestamp | Data de atualização |

### Tabela: `grandes_itens`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| tipo_orcamento_id | bigint | FK para tipos_orcamentos |
| descricao | varchar(500) | Descrição do grande item |
| ordem | int | Ordem de exibição |
| created_at | timestamp | Data de criação |
| updated_at | timestamp | Data de atualização |

### Tabela: `sub_grupos`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| grande_item_id | bigint | FK para grandes_itens |
| descricao | varchar(500) | Descrição do subgrupo |
| ordem | int | Ordem de exibição |
| created_at | timestamp | Data de criação |
| updated_at | timestamp | Data de atualização |

---

## 5. Regras de Negócio
- **Campos obrigatórios:** descricao, tipo_orcamento_id (grandes_itens), grande_item_id (sub_grupos)
- **Validações:** descricao única por tipo de orçamento, ordem sequencial
- **Relacionamentos:** Grande Item deve pertencer a um Tipo de Orçamento, Subgrupo deve pertencer a um Grande Item
- **Integridade:** Exclusão em cascata (excluir tipo de orçamento remove grandes itens e subgrupos)
- **Importação:** Estratégia "replace all" - remove estrutura existente antes de inserir nova

---

## 6. Funcionalidades
- **CRUD Completo:** Criação, leitura, atualização e exclusão de tipos, grandes itens e subgrupos
- **Gestão de Ordem:** Reordenação via drag & drop (preparado para implementação futura)
- **Importação Excel:** Upload de arquivo Excel para criação em massa da estrutura
- **Limpeza Estrutural:** Remoção completa de toda estrutura de um tipo de orçamento
- **Visualização Integrada:** Aba dedicada para visualização hierárquica da estrutura
- **Sincronização em Tempo Real:** Event Bus para sincronização entre abas
- **Validações:** Validação de dados no frontend e backend

---

## 7. Fluxo de Uso

### **Fluxo Principal:**
1. **Acesso:** Usuário acessa `/administracao/estrutura-orcamento`
2. **Seleção:** Escolhe tipo de orçamento no seletor integrado
3. **Gestão:** Utiliza Aba 1 para gerenciar tipos de orçamento
4. **Estrutura:** Utiliza Aba 2 para gerenciar grandes itens e subgrupos
5. **Visualização:** Utiliza Aba 3 para visualizar estrutura hierárquica

### **Fluxo de Importação:**
1. **Seleção:** Escolhe tipo de orçamento
2. **Upload:** Seleciona arquivo Excel
3. **Preview:** Sistema processa e mostra resumo
4. **Confirmação:** Usuário confirma substituição
5. **Processamento:** Sistema executa importação
6. **Resultado:** Estrutura é atualizada

### **Fluxo de Limpeza:**
1. **Seleção:** Escolhe tipo de orçamento
2. **Confirmação:** Clica em "Limpar Estrutura"
3. **Validação:** Sistema confirma ação destrutiva
4. **Execução:** Remove todos os dados relacionados

---

## 8. Interface/UX/UI

### **Layout Principal:**
- **Header Integrado:** Seletor de tipo de orçamento com gradiente e sombra
- **Sistema de Abas:** 3 abas principais com navegação intuitiva
- **Design Responsivo:** Adaptação para diferentes tamanhos de tela

### **Componentes Principais:**
- **Seletor de Tipo:** Campo select estilizado com floating label
- **Tabelas Hierárquicas:** Exibição organizada de grandes itens e subgrupos
- **Modais Bootstrap:** Padrão visual consistente com a aplicação
- **Árvore de Visualização:** Estrutura hierárquica limpa e organizada

### **Feedback Visual:**
- **Toast Notifications:** Mensagens de sucesso e erro
- **Progress Bars:** Indicadores de progresso para operações longas
- **Estados de Loading:** Spinners e mensagens de carregamento
- **Validações Visuais:** Bordas coloridas e mensagens de erro

### **Responsividade:**
- **Mobile First:** Design adaptável para dispositivos móveis
- **Breakpoints:** Adaptação para tablets e desktops
- **Flexbox:** Layout flexível e responsivo

---

## 9. Dependências e Integrações

### **Funcionalidades Dependentes:**
- **Sistema de Autenticação:** Controle de acesso baseado em permissões
- **Upload de Arquivos:** Sistema de upload para arquivos Excel
- **Validação de Dados:** Sistema de validação Laravel

### **Funcionalidades Dependentes Desta:**
- **Orçamentos:** Utiliza estrutura para composição de orçamentos
- **Composições:** Base para composições de preços
- **Relatórios:** Estrutura utilizada em relatórios de orçamento

### **APIs Externas:**
- **Nenhuma integração externa** - sistema totalmente interno

### **Bibliotecas:**
- **PhpSpreadsheet:** Processamento de arquivos Excel
- **Axios:** Comunicação HTTP no frontend
- **Bootstrap:** Framework CSS para interface
- **Vue.js:** Framework JavaScript para componentes

---

## 10. Processos Automáticos
- **Nenhum processo automático** implementado
- **Todas as operações** são iniciadas pelo usuário
- **Validações** executadas em tempo real durante interação

---

## 11. Testes

### **Testes Manuais Recomendados:**

#### **Funcionalidades Básicas:**
- [ ] Criar novo tipo de orçamento
- [ ] Editar tipo de orçamento existente
- [ ] Excluir tipo de orçamento
- [ ] Criar grande item
- [ ] Editar grande item
- [ ] Excluir grande item
- [ ] Criar subgrupo
- [ ] Editar subgrupo
- [ ] Excluir subgrupo

#### **Funcionalidades Especiais:**
- [ ] Importação de arquivo Excel
- [ ] Limpeza completa da estrutura
- [ ] Visualização hierárquica
- [ ] Sincronização entre abas
- [ ] Reordenação de itens

#### **Validações:**
- [ ] Campos obrigatórios
- [ ] Unicidade de descrições
- [ ] Relacionamentos obrigatórios
- [ ] Validação de arquivos Excel
- [ ] Confirmações de ações destrutivas

#### **Responsividade:**
- [ ] Teste em dispositivos móveis
- [ ] Teste em tablets
- [ ] Teste em diferentes resoluções
- [ ] Teste de navegação por abas

---

## 12. Exemplos Práticos

### **Payload para Criação de Grande Item:**
```json
{
  "tipo_orcamento_id": 1,
  "descricao": "SERVIÇOS PRELIMINARES E ADMINISTRAÇÃO DA OBRA",
  "ordem": 1
}
```

### **Resposta da API:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "tipo_orcamento_id": 1,
    "descricao": "SERVIÇOS PRELIMINARES E ADMINISTRAÇÃO DA OBRA",
    "ordem": 1,
    "created_at": "2025-01-13T12:00:00.000000Z",
    "updated_at": "2025-01-13T12:00:00.000000Z"
  }
}
```

### **Estrutura de Arquivo Excel:**
- **Coluna A:** Ordem do Grande Item
- **Coluna B:** Descrição do Grande Item
- **Coluna C:** Ordem do Subgrupo
- **Coluna D:** Descrição do Subgrupo

---

## 13. Checklist de Verificação

### **Funcionalidades Implementadas:**
- [x] CRUD de Tipos de Orçamento
- [x] CRUD de Grandes Itens
- [x] CRUD de Subgrupos
- [x] Importação via Excel
- [x] Limpeza de estrutura
- [x] Visualização hierárquica
- [x] Sincronização entre abas
- [x] Validações frontend/backend
- [x] Interface responsiva
- [x] Sistema de notificações

### **Arquivos Verificados:**
- [x] Controllers API implementados
- [x] Models com relacionamentos
- [x] Componentes Vue funcionais
- [x] Rotas configuradas
- [x] CSS compartilhado
- [x] Integração com app.js

### **Qualidade do Código:**
- [x] Sem código duplicado
- [x] Sem logs desnecessários
- [x] Comentários objetivos
- [x] Estrutura organizada
- [x] Padrões seguidos

---

## 14. Conclusão

### **🎯 Status da Funcionalidade:**
**COMPLETAMENTE FUNCIONAL** - Sistema integrado e operacional

### **✅ Pontos Fortes:**
- Interface moderna e intuitiva
- Funcionalidades completas de CRUD
- Importação em massa via Excel
- Visualização hierárquica clara
- Sincronização em tempo real
- Código limpo e bem organizado

### **🚀 Próximos Passos Sugeridos:**
- Implementar drag & drop para reordenação
- Adicionar histórico de alterações
- Implementar backup automático antes de importações
- Adicionar validação de estrutura Excel mais robusta

---

> **IMPORTANTE**: Esta funcionalidade substitui completamente o sistema antigo de gestão de estrutura de orçamentos, oferecendo uma solução integrada e moderna para administradores do sistema.
