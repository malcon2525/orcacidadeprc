# Funcionalidade Municípios

---

## 1. Visão Geral
- **Objetivo:** Gerenciar cadastros de municípios do Paraná com informações completas como nome, prefeito, dados de contato, código IBGE e população
- **Contexto:** Funcionalidade essencial para administração de dados municipais, permitindo CRUD completo e importação em massa de dados
- **Público-alvo:** Administradores do sistema e usuários com permissões de gestão municipal

---

## 2. Rotas/API

### Rotas Web (`routes/web.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /administracao/municipios | MunicipioController@index | Interface principal do CRUD |

### Rotas API (`routes/web.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /api/administracao/municipios | MunicipioController@listar | Lista registros com filtros e paginação |
| POST | /api/administracao/municipios | MunicipioController@store | Cria novo município |
| PUT | /api/administracao/municipios/{id} | MunicipioController@update | Atualiza município existente |
| DELETE | /api/administracao/municipios/{id} | MunicipioController@destroy | Remove município |
| POST | /api/administracao/municipios/importar | MunicipioController@importar | Importa municípios em massa |

**Exemplo de retorno da listagem:**
```json
{
  "data": [
    {
      "id": 1,
      "nome": "Curitiba",
      "prefeito": "Rafael Greca",
      "email": "prefeito@curitiba.pr.gov.br",
      "endereco_prefeitura": "Rua da Cidadania",
      "codigo_ibge": "4106902",
      "populacao": 1948626,
      "cep": "80000-000",
      "telefone": "(41) 3350-8484",
      "cnpj": "76.937.123/0001-00"
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
- **Controller Web:** `app/Http/Controllers/Web/Administracao/Municipios/MunicipioController.php`
- **Controller API:** `app/Http/Controllers/Api/Administracao/Municipios/MunicipioController.php`
- **Model:** `app/Models/Municipio.php`
- **Migration:** `database/migrations/temp/06A_create_municipios_table.php`
- **Componente Vue:** `resources/js/components/administracao/municipios/ListaMunicipios.vue`
- **Views Blade:** `resources/views/administracao/municipios/index.blade.php`
- **Rotas:** `routes/web.php` (rotas web e API internas)

---

## 4. Estrutura de Dados

Tabela: `municipios`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| nome | varchar(255) | Nome do município (obrigatório) |
| prefeito | varchar(255) | Nome do prefeito (obrigatório) |
| email | varchar(255) | Email de contato (único, opcional) |
| endereco_prefeitura | varchar(255) | Endereço da prefeitura (obrigatório) |
| codigo_ibge | varchar(20) | Código IBGE do município (único, obrigatório) |
| populacao | integer | População do município (obrigatório, >= 0) |
| cep | varchar(10) | CEP do município (obrigatório) |
| telefone | varchar(20) | Telefone de contato (obrigatório) |
| cnpj | varchar(20) | CNPJ da prefeitura (único, obrigatório) |
| created_at | timestamp | Data de criação |
| updated_at | timestamp | Data de atualização |

**Campos-chave:**
- **PK:** `id`
- **Campos únicos:** `email`, `codigo_ibge`, `cnpj`
- **Campos obrigatórios:** `nome`, `prefeito`, `endereco_prefeitura`, `codigo_ibge`, `populacao`, `cep`, `telefone`, `cnpj`

---

## 5. Regras de Negócio
- **Campos obrigatórios:** Nome, prefeito, endereço da prefeitura, código IBGE, população, CEP, telefone e CNPJ
- **Validações específicas:** 
  - Email deve ser válido quando informado
  - População deve ser maior ou igual a zero
  - Código IBGE deve ser único no sistema
  - CNPJ deve ser único no sistema
  - Email deve ser único no sistema
- **Unicidade:** Código IBGE, CNPJ e email devem ser únicos
- **Relacionamentos:** Não possui relacionamentos obrigatórios

---

## 6. Funcionalidades
- **CRUD completo:** Criar, listar, editar e excluir municípios
- **Funcionalidades especiais:** 
  - Filtros por nome e prefeito
  - Paginação automática (15 registros por página)
  - Importação em massa de dados do PostgreSQL
  - Validação em tempo real
- **Integrações:** Conexão com banco PostgreSQL para importação
- **Validações:** Validação completa de campos com mensagens personalizadas

---

## 7. Fluxo de Uso
1. **Acesso:** Usuário acessa `/administracao/municipios`
2. **Visualização:** Sistema carrega lista paginada de municípios
3. **Filtros:** Usuário pode aplicar filtros por nome e prefeito
4. **Criação:** Usuário clica em "Novo Município" e preenche formulário
5. **Validação:** Sistema valida dados e retorna erros se necessário
6. **Edição:** Usuário pode editar registros existentes
7. **Exclusão:** Usuário pode remover registros com confirmação
8. **Importação:** Usuário pode importar dados em massa do PostgreSQL

---

## 8. Interface/UX/UI
- **Layout:** Interface única com tabela responsiva, filtros colapsáveis e botões de ação
- **Componentes principais:** 
  - Tabela com classes `usuarios-table` e `usuario-row`
  - Filtros colapsáveis com `form-floating` e `form-control-lg`
  - Modal para criação/edição
  - Paginação personalizada sem combo box de registros por página
- **Feedback:** 
  - Toast notifications para operações
  - Loading states nos botões
  - Validação visual com `is-invalid`
  - Mensagens de erro personalizadas
- **Responsividade:** Layout responsivo com Bootstrap

---

## 9. Dependências e Integrações
- **Funcionalidades dependentes:** Nenhuma funcionalidade específica
- **Funcionalidades dependentes desta:** Pode ser referenciada por outras funcionalidades do sistema
- **APIs externas:** Conexão com banco PostgreSQL para importação
- **Bibliotecas:** Bootstrap, FontAwesome, Vue.js

---

## 10. Processos Automáticos
- **Rotinas:** Importação em massa de municípios do PostgreSQL
- **Agendamento:** Execução manual pelo usuário
- **Critérios:** Dados existentes na tabela `municipio` do PostgreSQL
- **Logs:** Registro de todas as operações com detalhes de sucesso e erro

---

## 11. Testes
- **Teste de criação:** Criar município com todos os campos obrigatórios
- **Teste de edição:** Editar município existente e verificar validações
- **Teste de exclusão:** Excluir município e confirmar remoção
- **Teste de validação:** Tentar criar/editar com campos inválidos
- **Teste de filtros:** Aplicar filtros por nome e prefeito
- **Teste de paginação:** Navegar entre páginas de resultados
- **Teste de importação:** Executar importação em massa
- **Teste de unicidade:** Tentar criar com código IBGE, CNPJ ou email duplicados

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
