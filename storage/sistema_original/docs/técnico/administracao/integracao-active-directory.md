# Integração com Active Directory (AD)

---

## 1. Visão Geral

- **Objetivo:** Sistema de integração com Active Directory para sincronização automática de usuários, autenticação centralizada e gerenciamento unificado de identidades.
- **Contexto:** Funcionalidade que permite conectar o sistema OrçaCidade com o Active Directory da organização, sincronizando usuários, grupos e permissões de forma automática e segura.
- **Público-alvo:** Administradores do sistema, administradores de TI e desenvolvedores que precisam gerenciar a integração com AD.

---

## 2. Rotas/API

### Rotas Web (`routes/web.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /administracao/active-directory | Web\Administracao\ActiveDirectory\ActiveDirectoryController@index | Interface principal da integração AD |

### Rotas API (`routes/web.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| POST | /api/administracao/active-directory/sync | Api\Administracao\ActiveDirectory\SyncController@sync | Executar sincronização manual |
| GET | /api/administracao/active-directory/sync/status | Api\Administracao\ActiveDirectory\SyncController@status | Verificar status da sincronização |
| GET | /api/administracao/active-directory/sync/test-connection | Api\Administracao\ActiveDirectory\SyncController@testConnection | Testar conexão com AD |
| GET | /api/administracao/active-directory/config | Api\Administracao\ActiveDirectory\ConfigController@index | Obter configurações |
| POST | /api/administracao/active-directory/config | Api\Administracao\ActiveDirectory\ConfigController@store | Salvar configurações |
| GET | /api/administracao/active-directory/config/test | Api\Administracao\ActiveDirectory\ConfigController@test | Testar configurações |

**Exemplo de retorno da API de sincronização:**
```json
{
  "success": true,
  "message": "Sincronização manual concluída",
  "data": {
    "executado_em": "2025-01-15T10:30:00.000000Z",
    "executado_por": {
      "id": 1,
      "nome": "Administrador",
      "email": "admin@exemplo.com"
    },
    "status": "concluido",
    "tipo": "manual",
    "resultados": {
      "usuarios_processados": 150,
      "usuarios_criados": 25,
      "usuarios_atualizados": 120,
      "usuarios_desativados": 5,
      "erros": []
    }
  }
}
```

---

## 3. Arquivos Envolvidos

### Controllers
- **Web Controller:** `app/Http/Controllers/Web/Administracao/ActiveDirectory/ActiveDirectoryController.php`
- **API Controllers:** 
  - `app/Http/Controllers/Api/Administracao/ActiveDirectory/SyncController.php`
  - `app/Http/Controllers/Api/Administracao/ActiveDirectory/ConfigController.php`

### Services
- **ActiveDirectoryService:** `app/Services/ActiveDirectoryService.php`
- **ActiveDirectorySyncService:** `app/Services/ActiveDirectorySyncService.php`

### Jobs
- **SyncActiveDirectoryJob:** `app/Jobs/SyncActiveDirectoryJob.php`

### Commands
- **SyncActiveDirectoryCommand:** `app/Console/Commands/SyncActiveDirectoryCommand.php`

### Componentes Vue
- **Componente Principal:** `resources/js/components/administracao/active-directory/ActiveDirectoryMain.vue`
- **Configuração AD:** `resources/js/components/administracao/active-directory/ConfiguracaoAd.vue`
- **Logs de Sincronização:** `resources/js/components/administracao/active-directory/LogsSincronizacao.vue`
- **Usuários AD:** `resources/js/components/administracao/active-directory/UsuariosAd.vue`

### Views Blade
- **View Principal:** `resources/views/administracao/active-directory/index.blade.php`

### Configurações
- **Config AD:** `config/adldap.php`
- **Variáveis de Ambiente:** `.env` (AD_HOST, AD_PORT, AD_BASE_DN, etc.)

### Rotas
- **Rotas Web:** `routes/web.php`
- **Rotas API:** `routes/web.php` (rotas API internas)

---

## 4. Estrutura de Dados

### Tabela: `users` (Extensão para AD)
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| name | varchar(255) | Nome completo do usuário |
| email | varchar(255) | Email único, obrigatório |
| password | varchar(255) | Senha criptografada (nulo para usuários AD) |
| status | enum('ativo','inativo') | Status do usuário |
| tipo | enum('local','ad') | Tipo de usuário (local ou AD) |
| ad_username | varchar(255) | Nome de usuário no AD |
| ad_dn | varchar(500) | Distinguished Name no AD |
| ad_groups | text | Grupos do AD (JSON) |
| last_sync_at | timestamp | Data da última sincronização |
| created_at | timestamp | Data de criação |
| updated_at | timestamp | Data de atualização |

### Cache de Configurações
| Chave | Tipo | Descrição |
|-------|------|-----------|
| ad_sync_frequency | string | Frequência de sincronização (daily/weekly/monthly) |
| ad_sync_time | string | Horário de sincronização (HH:mm) |
| ad_sync_enabled | boolean | Sincronização habilitada |
| ad_sync_updated_at | timestamp | Data da última atualização da configuração |

### Logs de Sincronização
- **Canal de Log:** `storage/logs/ad.log`
- **Formato:** JSON com detalhes de cada sincronização
- **Retenção:** 30 dias

---

## 5. Regras de Negócio

### Configurações Obrigatórias
- **AD_HOST:** Servidor LDAP/AD
- **AD_PORT:** Porta LDAP (389/636)
- **AD_BASE_DN:** Base DN para busca
- **AD_USERNAME:** Usuário de serviço
- **AD_PASSWORD:** Senha do usuário de serviço

### Validações Específicas
- **Conexão AD:** Deve ser testada antes da sincronização
- **Frequência:** Apenas valores 'daily', 'weekly', 'monthly'
- **Horário:** Formato HH:mm válido
- **Usuários AD:** Não podem ter senha local definida
- **Sincronização:** Máximo de 3 tentativas em caso de falha

### Relacionamentos Obrigatórios
- Usuários AD herdam permissões através de grupos
- Sincronização automática respeita configurações de frequência
- Logs de sincronização são mantidos para auditoria

### Unicidade
- Email de usuário deve ser único (mesmo entre AD e local)
- AD_USERNAME deve ser único por usuário
- AD_DN deve ser único por usuário

---

## 6. Funcionalidades

### CRUD Básico
- **Configurações:** Criar, visualizar, editar configurações de sincronização
- **Logs:** Visualizar logs de sincronização
- **Usuários AD:** Visualizar usuários sincronizados do AD

### Funcionalidades Especiais
- **Sincronização Manual:** Executar sincronização sob demanda
- **Sincronização Automática:** Sincronização agendada via job
- **Teste de Conexão:** Verificar conectividade com AD
- **Configuração de Frequência:** Definir frequência e horário de sincronização
- **Monitoramento de Status:** Verificar status da última sincronização
- **Logs Detalhados:** Logs completos de todas as operações
- **Autenticação AD:** Login usando credenciais do AD

### Integrações
- **Active Directory:** Conexão LDAP/LDAPS
- **Sistema de Usuários:** Integração com tabela users
- **Sistema de Jobs:** Agendamento via Laravel Jobs
- **Sistema de Logs:** Logs específicos para AD
- **Sistema de Cache:** Cache de configurações

### Validações Implementadas
- Validação de conectividade com AD
- Validação de configurações de sincronização
- Validação de formato de dados do AD
- Validação de permissões de acesso
- Validação de frequência e horário

---

## 7. Fluxo de Uso

### Configuração Inicial
1. Acessar a interface de configuração AD
2. Configurar parâmetros de conexão (host, porta, base DN)
3. Testar conexão com AD
4. Configurar frequência e horário de sincronização
5. Habilitar sincronização automática

### Sincronização Manual
1. Acessar a interface principal de AD
2. Verificar status da conexão
3. Clicar em "Sincronizar AD"
4. Aguardar conclusão da sincronização
5. Visualizar resultados e logs

### Monitoramento
1. Verificar status da última sincronização
2. Analisar logs de sincronização
3. Verificar estatísticas de usuários
4. Identificar e resolver erros

### Autenticação AD
1. Usuário tenta login com credenciais AD
2. Sistema valida credenciais no AD
3. Se válido, cria/atualiza usuário local
4. Permite acesso ao sistema

---

## 8. Interface/UX/UI

### Layout Principal
- **Container:** `container-fluid px-4` com card principal
- **Cabeçalho:** Card header com título verde e ícones
- **Status:** Indicadores visuais de status da conexão
- **Estatísticas:** Cards com números de usuários
- **Progresso:** Barra de progresso durante sincronização

### Componentes Principais
- **ActiveDirectoryMain.vue:** Componente principal com sincronização
- **ConfiguracaoAd.vue:** Configurações de sincronização
- **LogsSincronizacao.vue:** Visualização de logs
- **UsuariosAd.vue:** Lista de usuários AD

### Feedbacks Visuais
- **Status Indicators:** Indicadores de status da conexão
- **Progress Bars:** Progresso da sincronização
- **Toast Notifications:** Feedback de operações
- **Loading States:** Indicadores de carregamento
- **Alert Messages:** Mensagens de sucesso/erro

### Responsividade
- **Desktop:** Layout completo com todas as funcionalidades
- **Tablet:** Layout adaptado com estatísticas em colunas
- **Mobile:** Layout compacto com navegação otimizada

---

## 9. Dependências e Integrações

### Funcionalidades Dependentes
- **Sistema de Usuários:** Utiliza tabela users
- **Sistema de Autenticação:** Integração com login
- **Sistema de Jobs:** Para agendamento
- **Sistema de Cache:** Para configurações
- **Sistema de Logs:** Para auditoria

### Funcionalidades Dependentes Desta
- **Gerenciamento de Usuários:** Usuários AD aparecem na lista
- **Sistema de Permissões:** Permissões baseadas em grupos AD
- **Relatórios:** Relatórios incluem usuários AD

### APIs Externas
- **Active Directory:** Conexão LDAP/LDAPS
- **Servidor LDAP:** Para autenticação e busca

### Bibliotecas
- **PHP LDAP:** Extensão PHP para LDAP
- **Laravel Jobs:** Para agendamento
- **Laravel Cache:** Para configurações
- **Vue.js:** Interface frontend
- **Bootstrap:** Framework CSS

---

## 10. Processos Automáticos

### Rotinas
- **Sincronização Automática:** Job agendado para sincronizar usuários
- **Limpeza de Logs:** Limpeza automática de logs antigos
- **Verificação de Status:** Verificação periódica de conectividade

### Agendamento
- **Sincronização:** Configurável (diária/semanal/mensal)
- **Horário Padrão:** 02:00 (configurável)
- **Limpeza de Logs:** Semanalmente
- **Verificação de Status:** A cada 6 horas

### Critérios
- **Sincronização:** Apenas usuários ativos no AD
- **Limpeza:** Logs com mais de 30 dias
- **Verificação:** Teste de conectividade básico

### Logs
- **Logs de Sincronização:** Detalhes de cada sincronização
- **Logs de Conexão:** Tentativas de conexão
- **Logs de Erro:** Erros durante sincronização
- **Logs de Autenticação:** Tentativas de login AD

---

## 11. Testes

### Testes de Conexão
- Testar conexão com servidor AD válido
- Testar conexão com credenciais inválidas
- Testar conexão com servidor inacessível
- Testar diferentes portas (389/636)

### Testes de Sincronização
- Sincronizar usuários válidos do AD
- Sincronizar com AD vazio
- Sincronizar com erros de permissão
- Testar sincronização incremental vs completa

### Testes de Configuração
- Salvar configurações válidas
- Salvar configurações inválidas (deve falhar)
- Testar diferentes frequências
- Testar diferentes horários

### Testes de Autenticação
- Login com usuário AD válido
- Login com credenciais AD inválidas
- Login com usuário AD desabilitado
- Testar diferentes formatos de username

### Testes de Funcionalidades Especiais
- Testar sincronização manual
- Testar sincronização automática
- Testar logs de sincronização
- Testar estatísticas de usuários

### Testes de Interface
- Testar responsividade em diferentes dispositivos
- Testar feedback visual (status, progresso)
- Testar navegação entre componentes
- Testar acessibilidade

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