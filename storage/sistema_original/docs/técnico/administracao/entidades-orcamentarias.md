# Funcionalidade Entidades Orçamentárias

---

## 1. Visão Geral
- **Objetivo:** Gerenciar entidades orçamentárias (municípios, secretarias, órgãos, autarquias e outros) que podem realizar orçamentos e contratos no sistema
- **Contexto:** Funcionalidade essencial para o cadastro e manutenção de clientes e parceiros do sistema, permitindo a criação de orçamentos e projetos para essas entidades
- **Público-alvo:** Administradores do sistema, usuários responsáveis pelo cadastro de clientes e parceiros, equipe de orçamentos

---

## 2. Rotas/API

### Rotas Web (`routes/web.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /administracao/entidades-orcamentarias | EntidadeOrcamentariaController@index | Interface principal da funcionalidade |

### Rotas API (`routes/web.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /api/administracao/entidades-orcamentarias | EntidadeOrcamentariaController@listar | Lista registros com filtros e paginação |
| GET | /api/administracao/entidades-orcamentarias/listar-select | EntidadeOrcamentariaController@listarSelect | Lista para campos de seleção |
| POST | /api/administracao/entidades-orcamentarias | EntidadeOrcamentariaController@store | Cria nova entidade |
| PUT | /api/administracao/entidades-orcamentarias/{id} | EntidadeOrcamentariaController@update | Atualiza entidade existente |
| DELETE | /api/administracao/entidades-orcamentarias/{id} | EntidadeOrcamentariaController@destroy | Remove entidade |
| POST | /api/administracao/entidades-orcamentarias/importar-municipios | EntidadeOrcamentariaController@importarMunicipios | Importa municípios da tabela municipios |

**Exemplo de retorno da listagem:**
```json
{
  "data": [
    {
      "id": 1,
      "razao_social": "Prefeitura Municipal de Curitiba",
      "nome_fantasia": "Prefeitura de Curitiba",
      "tipo_organizacao": "municipio",
      "email": "contato@curitiba.pr.gov.br",
      "telefone": "(41) 3350-8000",
      "responsavel": "João Silva",
      "responsavel_cargo": "Prefeito",
      "ativo": true
    }
  ],
  "current_page": 1,
  "last_page": 1,
  "per_page": 15,
  "total": 1
}
```

---

## 3. Arquivos Envolvidos
- **Controller Web:** `app/Http/Controllers/Web/Administracao/EntidadesOrcamentarias/EntidadeOrcamentariaController.php`
- **Controller API:** `app/Http/Controllers/Api/Administracao/EntidadesOrcamentarias/EntidadeOrcamentariaController.php`
- **Model:** `app/Models/Gerais/EntidadeOrcamentaria.php`
- **Migration Principal:** `database/migrations/06B_create_entidades_orcamentarias_table.php`
- **Migration Email Obrigatório:** `database/migrations/2024_01_01_000000_make_email_required_in_entidades_orcamentarias.php`
- **Componente Vue:** `resources/js/components/administracao/entidades-orcamentarias/ListaEntidadesOrcamentariasAdmin.vue`
- **View Blade:** `resources/views/administracao/entidades-orcamentarias/index.blade.php`
- **Rotas:** `routes/web.php` (rotas web e API internas)

---

## 4. Estrutura de Dados

### Tabela: `entidades_orcamentarias`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| razao_social | string(255) | obrigatório, único |
| nome_fantasia | string(255) | obrigatório, único |
| tipo_organizacao | enum | obrigatório, valores: municipio, secretaria, órgão, autarquia, outros |
| email | string(255) | obrigatório, único |
| endereco | string(255) | opcional |
| codigo_ibge | string(20) | único, opcional |
| populacao | integer | opcional |
| cep | string(10) | opcional |
| telefone | string(20) | obrigatório |
| cnpj | string(20) | único, opcional |
| responsavel | string(255) | obrigatório |
| responsavel_cargo | string(100) | obrigatório |
| responsavel_telefone | string(20) | opcional |
| responsavel_email | string(100) | opcional |
| ativo | boolean | padrão: true |
| created_at | timestamp | automático |
| updated_at | timestamp | automático |

---

## 5. Regras de Negócio
- **Campos obrigatórios:** razão social, nome fantasia, tipo de organização, email, telefone, responsável, cargo do responsável
- **Unicidade:** razão social, nome fantasia, email, código IBGE, CNPJ
- **Tipo de organização:** valores fixos (`municipio`, `secretaria`, `órgão`, `autarquia`, `outros`)
- **Email:** campo obrigatório (não pode ser nulo)
- **Status:** campo `ativo` controla se a entidade está ativa no sistema
- **Importação de municípios:** funcionalidade especial para criar entidades orçamentárias a partir da tabela de municípios

---

## 6. Funcionalidades
- **CRUD completo:** Criar, visualizar, editar e excluir entidades orçamentárias
- **Filtros avançados:** Busca por razão social, nome fantasia e tipo de organização
- **Paginação:** Listagem paginada com 15 registros por página
- **Importação de municípios:** Criação automática de entidades a partir da tabela de municípios
- **Validação robusta:** Validação completa de todos os campos obrigatórios e únicos
- **Modal de confirmação personalizado:** Substitui o `confirm()` nativo por modal Bootstrap customizado
- **Feedback visual:** Toast notifications para todas as operações
- **Estado de loading:** Indicadores visuais durante operações assíncronas

---

## 7. Fluxo de Uso
1. **Acesso:** Usuário acessa a funcionalidade através do menu "Administração > Entidades Orçamentárias"
2. **Visualização:** Sistema carrega lista paginada de entidades existentes
3. **Filtros:** Usuário pode ativar filtros para buscar entidades específicas
4. **Criação:** Usuário clica em "Nova Entidade" para abrir modal de criação
5. **Edição:** Usuário clica no botão de editar para modificar entidade existente
6. **Exclusão:** Usuário clica no botão de excluir, confirma no modal personalizado
7. **Importação:** Usuário pode importar municípios da tabela de municípios
8. **Feedback:** Sistema exibe toast notifications para confirmar operações

---

## 8. Interface/UX/UI
- **Layout responsivo:** Interface adaptável a diferentes tamanhos de tela
- **Card principal:** Container com header gradiente e botões de ação
- **Filtros colapsáveis:** Seção de filtros que pode ser expandida/recolhida
- **Tabela moderna:** Tabela responsiva com classes `usuarios-table` e `usuario-row`
- **Botões de ação:** Botões sólidos (amarelo para editar, vermelho para excluir)
- **Modal fullscreen:** Modal de criação/edição com design consistente
- **Modal de confirmação:** Modal personalizado para exclusões com estado de loading
- **Paginação externa:** Paginação posicionada fora do card principal
- **Toast notifications:** Feedback visual para todas as operações
- **Estados visuais:** Loading, vazio e erro com ícones e mensagens apropriadas

---

## 9. Dependências e Integrações
- **Funcionalidades dependentes:** 
  - Tabela de municípios (para importação)
  - Sistema de autenticação (para controle de acesso)
- **Funcionalidades dependentes desta:** 
  - Orçamentos (criação de projetos para entidades)
  - Contratos (vinculação de contratos às entidades)
  - Relatórios (dados de entidades para relatórios)
- **APIs externas:** Nenhuma integração externa
- **Bibliotecas:** Bootstrap 5, FontAwesome, Vue.js 3

---

## 10. Processos Automáticos
- **Validação automática:** Validação em tempo real dos campos obrigatórios
- **Logs de auditoria:** Registro automático de todas as operações CRUD
- **Paginação automática:** Cálculo automático de páginas baseado no total de registros
- **Filtros em tempo real:** Aplicação automática de filtros conforme usuário digita
- **Importação em lote:** Processo automático de criação de entidades a partir de municípios

---

## 11. Testes

### **Testes de Funcionalidade**
- [ ] **Criação:** Testar criação de nova entidade com todos os campos obrigatórios
- [ ] **Edição:** Testar edição de entidade existente
- [ ] **Exclusão:** Testar exclusão com modal de confirmação
- [ ] **Validação:** Testar validação de campos obrigatórios e únicos
- [ ] **Filtros:** Testar funcionamento dos filtros de busca
- [ ] **Paginação:** Testar navegação entre páginas
- [ ] **Importação:** Testar importação de municípios

### **Testes de Interface**
- [ ] **Responsividade:** Testar em diferentes tamanhos de tela
- [ ] **Filtros colapsáveis:** Testar expansão/recolhimento dos filtros
- [ ] **Modal de confirmação:** Testar funcionamento do modal personalizado
- [ ] **Toast notifications:** Testar exibição de mensagens de feedback
- [ ] **Estados de loading:** Testar indicadores visuais durante operações

### **Testes de Validação**
- [ ] **Email obrigatório:** Verificar que campo email é obrigatório
- [ ] **Unicidade:** Testar validação de campos únicos
- [ ] **Formato de email:** Testar validação de formato de email
- [ ] **Campos obrigatórios:** Testar todos os campos obrigatórios
- [ ] **Tipo de organização:** Testar valores permitidos no enum

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
- [x] Exemplos Práticos (Incluídos)

---

> **IMPORTANTE**: Esta documentação deve ser atualizada sempre que houver mudanças na funcionalidade. Todos os desenvolvedores devem seguir estas diretrizes para manter consistência.
