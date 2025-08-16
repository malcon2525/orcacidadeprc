# Funcionalidade Gerenciamento de Usuários e Permissões

---

## 1. Visão Geral
- **Objetivo:** Sistema completo de RBAC (Role-Based Access Control) para gerenciar usuários, papéis e permissões de acesso, com interface integrada e suporte a autenticação híbrida (local + Active Directory)
- **Contexto:** Sistema central de controle de acesso e permissões do OrçaCidade, permitindo gestão granular de funcionalidades por usuário através de papéis
- **Público-alvo:** Administradores do sistema, gestores de segurança e usuários responsáveis pela configuração de permissões de acesso

---

## 2. Rotas/API

### Rotas Web (`routes/web.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /administracao/usuarios | UsuariosController@index | Interface principal do gerenciamento de usuários |

### Rotas API (`routes/api.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /api/administracao/usuarios | UsuariosController@index | Listar usuários com filtros |
| POST | /api/administracao/usuarios | UsuariosController@store | Criar novo usuário |
| PUT | /api/administracao/usuarios/{id} | UsuariosController@update | Atualizar usuário existente |
| DELETE | /api/administracao/usuarios/{id} | UsuariosController@destroy | Excluir usuário |
| GET | /api/administracao/usuarios/{id}/permissoes | UsuariosController@permissoes | Buscar permissões de um usuário |
| GET | /api/administracao/roles | RolesController@index | Listar papéis com estatísticas |
| POST | /api/administracao/roles | RolesController@store | Criar novo papel |
| PUT | /api/administracao/roles/{id} | RolesController@update | Atualizar papel existente |
| DELETE | /api/administracao/roles/{id} | RolesController@destroy | Excluir papel |
| POST | /api/administracao/roles/{id}/permissions | RolesController@updatePermissions | Atualizar permissões de um papel |
| GET | /api/administracao/roles/{id}/users | RolesController@getUsers | Listar usuários de um papel |
| GET | /api/administracao/roles/{id}/permissions | RolesController@getPermissions | Listar permissões de um papel |
| POST | /api/administracao/roles/{id}/users | RolesController@addUser | Adicionar usuário a um papel |
| DELETE | /api/administracao/roles/{id}/users/{userId} | RolesController@removeUser | Remover usuário de um papel |
| GET | /api/administracao/permissions | PermissionsController@index | Listar permissões com estatísticas |
| POST | /api/administracao/permissions | PermissionsController@store | Criar nova permissão |
| PUT | /api/administracao/permissions/{id} | PermissionsController@update | Atualizar permissão existente |
| DELETE | /api/administracao/permissions/{id} | PermissionsController@destroy | Excluir permissão |
| GET | /api/administracao/permissions/{id}/roles | PermissionsController@getRoles | Listar papéis de uma permissão |
| GET | /api/administracao/permissions/{id}/users | PermissionsController@getUsers | Listar usuários de uma permissão |
| POST | /api/administracao/permissions/{id}/roles/{roleId} | PermissionsController@attachRole | Vincular papel a uma permissão |
| DELETE | /api/administracao/permissions/{id}/roles/{roleId} | PermissionsController@detachRole | Desvincular papel de uma permissão |
| GET | /api/administracao/permissions/{id}/available-roles | PermissionsController@availableRoles | Listar papéis disponíveis para uma permissão |
| GET | /api/busca-global | BuscaGlobalController@buscar | Busca unificada em usuários, papéis e permissões |

**Exemplo de retorno da API de usuários:**
```json
{
  "id": 1,
  "name": "João Silva",
  "email": "joao@exemplo.com",
  "username": "joao.silva",
  "is_active": true,
  "login_type": "local",
  "last_login_at": "2025-01-13T12:00:00.000000Z",
  "roles": [
    {
      "id": 1,
      "name": "gerente",
      "display_name": "Gerente",
      "permissions": [
        {
          "name": "exibir-orcamento",
          "display_name": "Exibir Orçamentos"
        }
      ]
    }
  ]
}
```

---

## 3. Arquivos Envolvidos
- **Controllers Web:** 
  - `app/Http/Controllers/Web/Administracao/Usuarios/UsuariosController.php`
- **Controllers API:** 
  - `app/Http/Controllers/Api/Administracao/Usuarios/UsuariosController.php`
  - `app/Http/Controllers/Api/Administracao/Roles/RolesController.php`
  - `app/Http/Controllers/Api/Administracao/PermissionsController.php`
  - `app/Http/Controllers/Api/Administracao/BuscaGlobalController.php`
- **Models:** 
  - `app/Models/User.php`
  - `app/Models/Administracao/Role.php`
  - `app/Models/Administracao/Permission.php`
- **Migrations:** 
  - `database/migrations/01A-user_e_auth_create_users_table.php`
  - `database/migrations/01F_administracao_add_ad_fields_to_users_table.php`
  - `database/migrations/01G_administracao_create_roles_table.php`
  - `database/migrations/01H_administracao_create_permissions_table.php`
  - `database/migrations/01I_administracao_create_user_roles_table.php`
  - `database/migrations/01J_administracao_create_role_permissions_table.php`
- **Componentes Vue:** 
  - `resources/js/components/administracao/usuarios/ListaUsuarios.vue`
  - `resources/js/components/administracao/usuarios/BuscaGlobal.vue`
- **Views Blade:** 
  - `resources/views/administracao/usuarios/index.blade.php`
- **Rotas:** `routes/web.php`, `routes/api.php`

---

## 4. Estrutura de Dados

### Tabela: `users`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| name | varchar(255) | Nome completo do usuário |
| email | varchar(255) | Email único do usuário |
| username | varchar(255) | Nome de usuário único |
| password | varchar(255) | Senha criptografada |
| is_active | boolean | Status ativo/inativo (padrão: true) |
| last_login_at | timestamp | Último login do usuário |
| ad_user_id | varchar(255) | ID do usuário no Active Directory |
| ad_domain | varchar(255) | Domínio do Active Directory |
| ad_sync_at | timestamp | Última sincronização com AD |
| login_type | enum | Tipo de login: local, ad, hybrid |
| email_verified_at | timestamp | Verificação de email |
| remember_token | varchar(100) | Token de "lembrar-me" |
| created_at | timestamp | Data de criação |
| updated_at | timestamp | Data de atualização |

### Tabela: `roles`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| name | varchar(255) | Nome interno único (ex: admin, gerente) |
| display_name | varchar(255) | Nome de exibição (ex: Administrador, Gerente) |
| description | text | Descrição do papel |
| is_active | boolean | Status ativo/inativo (padrão: true) |
| created_at | timestamp | Data de criação |
| updated_at | timestamp | Data de atualização |

### Tabela: `permissions`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| name | varchar(255) | Nome da permissão único (ex: exibir-orcamento) |
| display_name | varchar(255) | Nome de exibição (ex: Exibir Orçamentos) |
| description | text | Descrição da permissão |
| is_active | boolean | Status ativo/inativo (padrão: true) |
| created_at | timestamp | Data de criação |
| updated_at | timestamp | Data de atualização |

### Tabela: `user_roles`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| user_id | bigint | FK para users (cascade delete) |
| role_id | bigint | FK para roles (cascade delete) |
| created_at | timestamp | Data de criação |
| updated_at | timestamp | Data de atualização |
| **Constraints:** | unique(['user_id', 'role_id']) | Usuário não pode ter o mesmo papel duas vezes |

### Tabela: `role_permissions`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| role_id | bigint | FK para roles (cascade delete) |
| permission_id | bigint | FK para permissions (cascade delete) |
| created_at | timestamp | Data de criação |
| updated_at | timestamp | Data de atualização |
| **Constraints:** | unique(['role_id', 'permission_id']) | Papel não pode ter a mesma permissão duas vezes |

---

## 5. Regras de Negócio
- **Campos obrigatórios:** name, email, username, password (usuários); name, display_name (papéis e permissões)
- **Validações:** email único, username único, name único por tabela
- **Relacionamentos:** Usuário pode ter múltiplos papéis, papel pode ter múltiplas permissões
- **Integridade:** Exclusão em cascata para relacionamentos (excluir usuário remove vínculos com papéis)
- **Autenticação:** Suporte híbrido (local + Active Directory) com sincronização automática
- **Segurança:** Não é possível excluir o próprio usuário logado

---

## 6. Funcionalidades
- **CRUD Completo:** Criação, leitura, atualização e exclusão de usuários, papéis e permissões
- **Sistema de Abas:** Interface integrada com 4 abas (Usuários, Papéis, Permissões, Busca Global)
- **Gestão de Relacionamentos:** Adicionar/remover usuários de papéis e permissões de papéis
- **Busca Global:** Busca unificada em todas as entidades com filtros avançados
- **Filtros Dinâmicos:** Filtros colapsáveis para cada aba com validação e persistência
- **Sincronização AD:** Integração com Active Directory para autenticação e sincronização
- **Validações:** Validação de dados no frontend e backend com mensagens de erro
- **Paginação:** Sistema de paginação frontend e backend com sincronização entre abas

---

## 7. Fluxo de Uso

### **Fluxo Principal:**
1. **Acesso:** Usuário acessa `/administracao/usuarios`
2. **Navegação:** Utiliza sistema de abas para acessar diferentes funcionalidades
3. **Gestão:** Utiliza Aba Usuários para gerenciar usuários do sistema
4. **Papéis:** Utiliza Aba Papéis para gerenciar papéis e suas permissões
5. **Permissões:** Utiliza Aba Permissões para visualizar impacto e gerenciar permissões
6. **Busca:** Utiliza Aba Busca Global para busca unificada

### **Fluxo de Criação de Usuário:**
1. **Seleção:** Usuário clica em "Novo Usuário"
2. **Preenchimento:** Preenche formulário com dados obrigatórios
3. **Validação:** Sistema valida dados e exibe erros se necessário
4. **Criação:** Sistema cria usuário e exibe mensagem de sucesso
5. **Atualização:** Lista de usuários é atualizada automaticamente

### **Fluxo de Gestão de Papéis:**
1. **Seleção:** Usuário seleciona papel na lista
2. **Ações:** Utiliza botões inline para editar, excluir ou gerenciar
3. **Gerenciamento:** Modal permite adicionar/remover usuários e permissões
4. **Confirmação:** Sistema confirma alterações e atualiza dados
5. **Sincronização:** Todas as abas são atualizadas automaticamente

---

## 8. Interface/UX/UI

### **Layout Principal:**
- **Sistema de Abas:** 4 abas principais com navegação intuitiva e transições suaves
- **Header Compacto:** Cabeçalho com título e navegação principal
- **Design Responsivo:** Adaptação para diferentes tamanhos de tela

### **Componentes Principais:**
- **Tabelas Interativas:** Tabelas com filtros, paginação e ações inline
- **Modais Bootstrap:** Modais para criação, edição e gerenciamento de relacionamentos
- **Filtros Dinâmicos:** Filtros colapsáveis com validação e persistência
- **Badges Discretos:** Sistema visual para exibição de papéis e permissões
- **Botões Contextuais:** Botões de ação específicos para cada contexto

### **Feedback Visual:**
- **Toast Notifications:** Mensagens de sucesso e erro
- **Estados de Loading:** Spinners e mensagens de carregamento
- **Validações Visuais:** Bordas coloridas e mensagens de erro
- **Confirmações:** Modais de confirmação para ações destrutivas

### **Responsividade:**
- **Mobile First:** Design adaptável para dispositivos móveis
- **Breakpoints:** Adaptação para tablets e desktops
- **Flexbox:** Layout flexível e responsivo

---

## 9. Dependências e Integrações

### **Funcionalidades Dependentes:**
- **Sistema de Autenticação:** Controle de acesso baseado em permissões
- **Active Directory:** Integração para sincronização de usuários
- **Validação de Dados:** Sistema de validação Laravel

### **Funcionalidades Dependentes Desta:**
- **Todas as funcionalidades do sistema:** Utilizam permissões para controle de acesso
- **Sistema de Login:** Autenticação baseada em papéis e permissões
- **Relatórios:** Controle de acesso baseado em permissões

### **APIs Externas:**
- **Active Directory:** Integração LDAP para sincronização de usuários e autenticação

### **Bibliotecas:**
- **Axios:** Comunicação HTTP no frontend
- **Bootstrap:** Framework CSS para interface
- **Vue.js:** Framework JavaScript para componentes
- **JWT:** Autenticação baseada em tokens

---

## 10. Processos Automáticos
- **Sincronização AD:** Processo automático de sincronização com Active Directory
- **Validação de Sessão:** Verificação automática de permissões em cada requisição
- **Limpeza de Tokens:** Limpeza automática de tokens expirados

---

## 11. Testes

### **Testes Manuais Recomendados:**

#### **Funcionalidades Básicas:**
- [ ] Criar novo usuário
- [ ] Editar usuário existente
- [ ] Excluir usuário
- [ ] Criar novo papel
- [ ] Editar papel existente
- [ ] Excluir papel
- [ ] Criar nova permissão
- [ ] Editar permissão existente
- [ ] Excluir permissão

#### **Funcionalidades Especiais:**
- [ ] Gerenciar usuários em papéis
- [ ] Gerenciar permissões em papéis
- [ ] Busca global unificada
- [ ] Filtros dinâmicos
- [ ] Sincronização entre abas
- [ ] Validações de formulário

#### **Validações:**
- [ ] Campos obrigatórios
- [ ] Unicidade de emails e usernames
- [ ] Relacionamentos obrigatórios
- [ ] Confirmações de ações destrutivas
- [ ] Validação de permissões

#### **Responsividade:**
- [ ] Teste em dispositivos móveis
- [ ] Teste em tablets
- [ ] Teste em diferentes resoluções
- [ ] Teste de navegação por abas

---

## 12. Exemplos Práticos

### **Payload para Criação de Usuário:**
```json
{
  "name": "João Silva",
  "email": "joao@exemplo.com",
  "username": "joao.silva",
  "password": "senha123",
  "is_active": true,
  "login_type": "local",
  "roles": [1, 2]
}
```

### **Resposta da API:**
```json
{
  "id": 1,
  "name": "João Silva",
  "email": "joao@exemplo.com",
  "username": "joao.silva",
  "is_active": true,
  "login_type": "local",
  "created_at": "2025-01-13T12:00:00.000000Z",
  "updated_at": "2025-01-13T12:00:00.000000Z",
  "roles": [
    {
      "id": 1,
      "name": "gerente",
      "display_name": "Gerente"
    }
  ]
}
```

### **Payload para Atualização de Permissões de Papel:**
```json
{
  "permissions": [1, 2, 3, 5]
}
```

---

## 13. Checklist de Verificação

### **Funcionalidades Implementadas:**
- [x] CRUD de Usuários
- [x] CRUD de Papéis
- [x] CRUD de Permissões
- [x] Sistema de Abas Integrado
- [x] Gestão de Relacionamentos
- [x] Busca Global Unificada
- [x] Filtros Dinâmicos
- [x] Sincronização entre Abas
- [x] Validações Frontend/Backend
- [x] Interface Responsiva
- [x] Sistema de Notificações
- [x] Integração com Active Directory

### **Arquivos Verificados:**
- [x] Controllers Web e API implementados
- [x] Models com relacionamentos
- [x] Componentes Vue funcionais
- [x] Rotas configuradas
- [x] Migrations do banco de dados
- [x] Views Blade funcionais

### **Qualidade do Código:**
- [x] Sem código duplicado
- [x] Comentários objetivos
- [x] Estrutura organizada
- [x] Padrões seguidos
- [x] Relacionamentos bem definidos

---

## 14. Conclusão

### **🎯 Status da Funcionalidade:**
**COMPLETAMENTE FUNCIONAL** - Sistema RBAC completo e operacional

### **✅ Pontos Fortes:**
- Sistema RBAC completo e robusto
- Interface integrada com sistema de abas
- Busca global unificada
- Filtros dinâmicos e responsivos
- Sincronização automática entre abas
- Integração com Active Directory
- Validações rigorosas de segurança
- Código bem organizado e documentado

### **🚀 Próximos Passos Sugeridos:**
- Implementar auditoria de alterações
- Adicionar relatórios de permissões
- Implementar backup automático de configurações
- Adicionar notificações por email para alterações críticas
- Implementar sistema de aprovação para alterações de permissões

---

> **IMPORTANTE**: Esta funcionalidade é o sistema central de controle de acesso do OrçaCidade, implementando um modelo RBAC completo e seguro para gestão de usuários, papéis e permissões.
