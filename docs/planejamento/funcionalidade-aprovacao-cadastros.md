# Planejamento - Funcionalidade "Aprovação de Cadastros"

**Criado em:** Janeiro 2025  
**Status:** Planejamento Concluído - Pronto para Implementação  
**Módulo:** Administração  

---

## 🎯 **OBJETIVO PRINCIPAL**

Permitir que usuários externos solicitem cadastro no ORÇACIDADE, sejam aprovados por administradores e vinculados a entidades orçamentárias para posteriormente criarem orçamentos.

### **Contexto do Negócio:**
- Usuários de diversos municípios precisam acessar o sistema
- Processo de aprovação é necessário para controle de acesso
- Usuários devem estar vinculados a entidades orçamentárias específicas
- Sistema deve permitir vinculação a múltiplas entidades

---

## 🏗️ **ESTRUTURA TÉCNICA DEFINIDA**

### **📍 Localização no Sistema:**
```
MÓDULO: administracao
FUNCIONALIDADES: 
├── aprovacao_cadastros        # Gerenciar solicitações de cadastro
└── usuarios_por_entidade      # Visualizar/gerenciar usuários por entidade
```

### **📁 Estrutura de Arquivos (Seguindo .cursorrules):**

#### **Controllers:**
```
app/Http/Controllers/Web/Administracao/AprovacaoCadastros/AprovacaoCadastrosController.php
app/Http/Controllers/Api/Administracao/AprovacaoCadastros/AprovacaoCadastrosController.php
app/Http/Controllers/Web/Administracao/UsuariosPorEntidade/UsuariosPorEntidadeController.php
app/Http/Controllers/Api/Administracao/UsuariosPorEntidade/UsuariosPorEntidadeController.php
app/Http/Controllers/Web/Publico/SolicitacaoCadastro/SolicitacaoCadastroController.php
```

#### **Models:**
```
app/Models/Administracao/SolicitacaoCadastro.php
```

#### **Views e Componentes:**
```
resources/views/administracao/aprovacao-cadastros/index.blade.php
resources/views/administracao/usuarios-por-entidade/index.blade.php
resources/views/publico/solicitar-cadastro.blade.php
resources/js/components/administracao/aprovacao-cadastros/ListaAprovacaoCadastros.vue
resources/js/components/administracao/usuarios-por-entidade/ListaUsuariosPorEntidade.vue
```

#### **Migrations:**
```
database/migrations/XXXX_create_solicitacoes_cadastro_table.php
database/migrations/XXXX_create_user_entidades_orcamentarias_table.php
database/migrations/XXXX_add_aprovar_cadastros_permission.php
```

---

## 🗄️ **ESTRUTURA DE BANCO DE DADOS**

### **Tabela: `solicitacoes_cadastro`**
```sql
CREATE TABLE solicitacoes_cadastro (
    id BIGINT UNSIGNED PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,                    -- FK para users
    municipio_id BIGINT UNSIGNED NOT NULL,               -- FK para municipios
    entidade_orcamentaria_id BIGINT UNSIGNED NOT NULL,   -- FK para entidades_orcamentarias
    status ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente',
    justificativa TEXT NOT NULL,                         -- Justificativa do usuário
    observacoes_aprovacao TEXT NULL,                     -- Observações do aprovador
    data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_aprovacao TIMESTAMP NULL,
    aprovado_por_user_id BIGINT UNSIGNED NULL,           -- FK para users (quem aprovou)
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (municipio_id) REFERENCES municipios(id),
    FOREIGN KEY (entidade_orcamentaria_id) REFERENCES entidades_orcamentarias(id),
    FOREIGN KEY (aprovado_por_user_id) REFERENCES users(id)
);
```

### **Tabela: `user_entidades_orcamentarias` (Relacionamento N:N)**
```sql
CREATE TABLE user_entidades_orcamentarias (
    id BIGINT UNSIGNED PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,                    -- FK para users
    entidade_orcamentaria_id BIGINT UNSIGNED NOT NULL,   -- FK para entidades_orcamentarias
    ativo BOOLEAN DEFAULT TRUE,
    data_vinculacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    vinculado_por_user_id BIGINT UNSIGNED NOT NULL,      -- FK para users (quem vinculou)
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (entidade_orcamentaria_id) REFERENCES entidades_orcamentarias(id),
    FOREIGN KEY (vinculado_por_user_id) REFERENCES users(id),
    
    UNIQUE KEY unique_user_entidade (user_id, entidade_orcamentaria_id)
);
```

### **Relacionamentos nos Models:**

#### **User.php - Novos Relacionamentos:**
```php
/**
 * Relacionamento com município (onde o usuário atua)
 */
public function municipio()
{
    return $this->belongsTo(\App\Models\Administracao\Municipio::class);
}

/**
 * Relacionamento many-to-many com entidades orçamentárias
 */
public function entidadesOrcamentarias()
{
    return $this->belongsToMany(
        \App\Models\Administracao\EntidadesOrcamentarias\EntidadeOrcamentaria::class,
        'user_entidades_orcamentarias',
        'user_id',
        'entidade_orcamentaria_id'
    )->withPivot('ativo', 'data_vinculacao', 'vinculado_por_user_id')
      ->withTimestamps();
}

/**
 * Relacionamento com solicitações de cadastro
 */
public function solicitacoesCadastro()
{
    return $this->hasMany(\App\Models\Administracao\SolicitacaoCadastro::class);
}

/**
 * Solicitações que este usuário aprovou
 */
public function solicitacoesAprovadas()
{
    return $this->hasMany(\App\Models\Administracao\SolicitacaoCadastro::class, 'aprovado_por_user_id');
}
```

#### **SolicitacaoCadastro.php - Relacionamentos Completos:**
```php
/**
 * Relacionamento com o usuário solicitante
 */
public function user()
{
    return $this->belongsTo(\App\Models\Administracao\User::class);
}

/**
 * Relacionamento com município escolhido
 */
public function municipio()
{
    return $this->belongsTo(\App\Models\Administracao\Municipio::class);
}

/**
 * Relacionamento com entidade orçamentária escolhida
 */
public function entidadeOrcamentaria()
{
    return $this->belongsTo(\App\Models\Administracao\EntidadesOrcamentarias\EntidadeOrcamentaria::class);
}

/**
 * Relacionamento com quem aprovou a solicitação
 */
public function aprovadoPor()
{
    return $this->belongsTo(\App\Models\Administracao\User::class, 'aprovado_por_user_id');
}
```

---

## 🔐 **PERMISSÕES E AUTORIZAÇÃO**

### **Nova Permissão:**
```php
Permission::create([
    'name' => 'aprovar-cadastros', 
    'display_name' => 'Aprovar Cadastros de Usuários',
    'description' => 'Permite aprovar/rejeitar solicitações de cadastro e vincular usuários a entidades'
]);
```

### **Regras de Autorização:**
- **Super Admin**: Acesso total a todas as funcionalidades
- **Role 'aprovador-cadastros'**: 
  - Pode visualizar solicitações pendentes
  - Pode aprovar/rejeitar solicitações
  - Pode vincular/desvincular usuários de entidades
- **Usuários comuns**: Apenas acesso ao formulário público de solicitação

### **Middleware de Verificação:**
```php
// Nos controllers administrativos
private function checkAccess()
{
    $user = Auth::user();
    
    if ($user->isSuperAdmin()) {
        return true;
    }
    
    if (!$user->hasPermission('aprovar-cadastros')) {
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }
    
    return true;
}
```

---

## 🌐 **FLUXOS E FUNCIONALIDADES DETALHADOS**

### **1. ÁREA PÚBLICA - SOLICITAÇÃO DE CADASTRO**

#### **Rota:** `/solicitar-cadastro`
**Acesso:** Público (sem autenticação)

#### **Funcionalidades:**
1. **Formulário de Solicitação:**
   - **Campos Obrigatórios:**
     - Nome completo
     - Email (único no sistema)
     - Senha
     - Confirmação de senha
     - Município (select com todos os municípios)
     - Entidade Orçamentária (select com todas as entidades ativas)
     - Justificativa (textarea, mínimo 50 caracteres)

2. **Validações:**
   - Email deve ser único no sistema
   - Senha deve ter mínimo 8 caracteres
   - Justificativa deve ter mínimo 50 caracteres
   - Município e entidade devem existir no sistema

3. **Processo de Submissão:**
   - Criar usuário com `is_active = false`
   - Criar registro em `solicitacoes_cadastro` com status 'pendente'
   - Exibir mensagem de confirmação
   - Redirecionar para página de login com aviso

4. **Regras Especiais:**
   - Usuário pode fazer múltiplas solicitações (para entidades diferentes)
   - Cada solicitação é independente
   - Sistema permite email duplicado se for solicitação para entidade diferente

---

### **2. ÁREA ADMINISTRATIVA**

#### **Novo Menu Lateral:**
```html
📁 Administração
├── Usuários
├── Entidades Orçamentárias  
├── Municípios
└── 🆕 Gestão de Usuários
    ├── Aprovação de Cadastros      ← Nova funcionalidade
    └── Usuários por Entidade       ← Nova funcionalidade
```

#### **Tela 1: Aprovação de Cadastros**
**Rota:** `/administracao/aprovacao-cadastros`

##### **Funcionalidades da Lista:**
1. **Visualização de Solicitações:**
   - Lista paginada com todas as solicitações
   - Colunas: Nome, Email, Município, Entidade, Status, Data, Ações
   - Filtros: Status (pendente/aprovado/rejeitado), Município, Entidade
   - Ordenação por data (mais recentes primeiro)

2. **Status Indicators:**
   - Badge "PENDENTE" (amarelo)
   - Badge "APROVADO" (verde) 
   - Badge "REJEITADO" (vermelho)

3. **Ações por Solicitação:**
   - Ver detalhes (modal)
   - Aprovar (se pendente)
   - Rejeitar (se pendente)

##### **Modal de Detalhes/Aprovação:**
1. **Informações Exibidas:**
   - Dados completos do usuário
   - Município escolhido (editável)
   - Entidade escolhida (editável)
   - Justificativa completa
   - Data da solicitação

2. **Processo de Aprovação:**
   - Permitir alterar município/entidade se necessário
   - Campo obrigatório para observações
   - Botões: "Aprovar", "Rejeitar", "Cancelar"

3. **Após Aprovação:**
   - Atualizar `users.is_active = true`
   - Criar registro em `user_entidades_orcamentarias`
   - Atualizar status da solicitação para 'aprovado'
   - Registrar data e quem aprovou

#### **Tela 2: Usuários por Entidade**
**Rota:** `/administracao/usuarios-por-entidade`

##### **Funcionalidades:**
1. **Seleção de Entidade:**
   - Dropdown com todas as entidades orçamentárias
   - Filtro por tipo de organização
   - Busca por nome da entidade

2. **Lista de Usuários Vinculados:**
   - Tabela com usuários da entidade selecionada
   - Colunas: Nome, Email, Data Vinculação, Status, Ações
   - Indicador de usuário ativo/inativo

3. **Gestão de Vinculações:**
   - **Adicionar Usuário:** Modal com busca de usuários disponíveis
   - **Remover Usuário:** Confirmação antes de desvincular
   - **Histórico:** Ver quando foi vinculado e por quem

4. **Funcionalidades Especiais:**
   - **Vinculação Manual:** Para usuários existentes (como adm, aaa)
   - **Busca Avançada:** Por nome, email, status
   - **Exportação:** Lista de usuários em PDF/Excel

---

## 📋 **REGRAS DE NEGÓCIO CONFIRMADAS**

### **Solicitações:**
1. ✅ Usuário pode solicitar vinculação a múltiplas entidades simultaneamente
2. ✅ Cada solicitação é independente (uma aprovada não afeta outra)
3. ✅ Após rejeição, usuário pode fazer nova solicitação
4. ✅ Formulário inicial: apenas 1 entidade por solicitação (para simplicidade)
5. ✅ Email deve ser único no sistema
6. ✅ Justificativa é obrigatória e deve ter mínimo 50 caracteres

### **Aprovação:**
1. ✅ Sempre manual, uma solicitação por vez
2. ✅ Aprovador pode alterar município/entidade escolhidos pelo usuário
3. ✅ Aprovador deve adicionar observações obrigatórias
4. ✅ Apenas usuários com permissão 'aprovar-cadastros' ou super admin
5. ✅ Após aprovação, usuário fica automaticamente ativo
6. ✅ Sistema registra quem aprovou e quando

### **Vinculações:**
1. ✅ Usuário pode estar vinculado a múltiplas entidades (relacionamento N:N)
2. ✅ Usuários existentes podem ser vinculados manualmente na tela "Usuários por Entidade"
3. ✅ Cada vinculação tem histórico completo (quem vinculou, quando)
4. ✅ Vinculações podem ser desativadas sem perder histórico
5. ✅ Sistema impede vinculação duplicada (unique constraint)

### **Relacionamentos:**
1. ✅ Usuário ↔ Município: relacionamento N:1 (contexto onde atua)
2. ✅ Usuário ↔ Entidade: relacionamento N:N (onde trabalha)
3. ✅ Não mexer na tabela `users` existente (usar tabelas de relacionamento)
4. ✅ Manter integridade referencial com foreign keys

### **Segurança:**
1. ✅ Formulário público com proteção CSRF
2. ✅ Validação tanto no frontend quanto backend
3. ✅ Sanitização de dados de entrada
4. ✅ Logs de auditoria para aprovações/rejeições

---

## 🎨 **PADRÕES VISUAIS (Seguindo .cursorrules)**

### **Cores Padronizadas:**
- **Verde Principal (#5EA853):** Títulos principais, status aprovado
- **Azul Secundário (#18578A):** Cabeçalhos de seção
- **Amarelo:** Status pendente
- **Vermelho:** Status rejeitado
- **Cinza (#6c757d):** Textos secundários

### **Estrutura Visual Base:**
```html
<div class="container-fluid px-4">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                <i class="fas fa-user-check me-2"></i>Aprovação de Cadastros
            </h6>
        </div>
        <div class="card-body">
            <!-- Conteúdo específico aqui -->
        </div>
    </div>
</div>
```

### **Modal de Confirmação (Rejeição):**
```html
<div class="modal fade modal-confirmacao" id="modalConfirmacaoRejeicao">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h5 class="modal-title mb-0">Confirmar Rejeição</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p class="confirm-text">Tem certeza que deseja rejeitar a solicitação de</p>
                <p class="target-entity">"{{ solicitacao?.user?.name }}"</p>
                <div class="irreversible">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span>O usuário poderá fazer uma nova solicitação.</span>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" @click="confirmarRejeicao" :disabled="rejeitando">
                    <span v-if="rejeitando" class="spinner-border spinner-border-sm me-2"></span>
                    <i v-else class="fas fa-times me-2"></i>
                    {{ rejeitando ? 'Rejeitando...' : 'Rejeitar' }}
                </button>
            </div>
        </div>
    </div>
</div>
```

---

## 🛣️ **ROTAS DETALHADAS**

### **Rotas Públicas (`routes/web.php`):**
```php
// ===== ROTAS PÚBLICAS - SOLICITAÇÃO DE CADASTRO =====
Route::get('/solicitar-cadastro', [\App\Http\Controllers\Web\Publico\SolicitacaoCadastro\SolicitacaoCadastroController::class, 'index'])->name('solicitar-cadastro.index');
Route::post('/solicitar-cadastro', [\App\Http\Controllers\Web\Publico\SolicitacaoCadastro\SolicitacaoCadastroController::class, 'store'])->name('solicitar-cadastro.store');
```

### **Rotas Administrativas (`routes/web.php`):**
```php
// ===== ROTAS ADMINISTRATIVAS - GESTÃO DE USUÁRIOS =====
Route::middleware(['auth'])->group(function () {
    
    // Aprovação de Cadastros
    Route::prefix('administracao/aprovacao-cadastros')->name('administracao.aprovacao-cadastros.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\Administracao\AprovacaoCadastros\AprovacaoCadastrosController::class, 'index'])->name('index');
    });
    
    // Usuários por Entidade  
    Route::prefix('administracao/usuarios-por-entidade')->name('administracao.usuarios-por-entidade.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\Administracao\UsuariosPorEntidade\UsuariosPorEntidadeController::class, 'index'])->name('index');
    });
});
```

### **Rotas API (`routes/web.php`):**
```php
// ===== ROTAS API - APROVAÇÃO DE CADASTROS =====
Route::prefix('api/administracao/aprovacao-cadastros')->name('api.administracao.aprovacao-cadastros.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\Administracao\AprovacaoCadastros\AprovacaoCadastrosController::class, 'listar'])->name('listar');
    Route::put('/{id}/aprovar', [\App\Http\Controllers\Api\Administracao\AprovacaoCadastros\AprovacaoCadastrosController::class, 'aprovar'])->name('aprovar');
    Route::put('/{id}/rejeitar', [\App\Http\Controllers\Api\Administracao\AprovacaoCadastros\AprovacaoCadastrosController::class, 'rejeitar'])->name('rejeitar');
    Route::get('/{id}', [\App\Http\Controllers\Api\Administracao\AprovacaoCadastros\AprovacaoCadastrosController::class, 'show'])->name('show');
});

// ===== ROTAS API - USUÁRIOS POR ENTIDADE =====
Route::prefix('api/administracao/usuarios-por-entidade')->name('api.administracao.usuarios-por-entidade.')->middleware(['auth'])->group(function () {
    Route::get('/entidade/{entidadeId}', [\App\Http\Controllers\Api\Administracao\UsuariosPorEntidade\UsuariosPorEntidadeController::class, 'listarPorEntidade'])->name('listar-por-entidade');
    Route::post('/vincular', [\App\Http\Controllers\Api\Administracao\UsuariosPorEntidade\UsuariosPorEntidadeController::class, 'vincular'])->name('vincular');
    Route::delete('/desvincular/{userId}/{entidadeId}', [\App\Http\Controllers\Api\Administracao\UsuariosPorEntidade\UsuariosPorEntidadeController::class, 'desvincular'])->name('desvincular');
    Route::get('/usuarios-disponiveis', [\App\Http\Controllers\Api\Administracao\UsuariosPorEntidade\UsuariosPorEntidadeController::class, 'usuariosDisponiveis'])->name('usuarios-disponiveis');
});

// ===== ROTAS API AUXILIARES =====
Route::prefix('api/publico')->name('api.publico.')->group(function () {
    Route::get('/municipios', [\App\Http\Controllers\Api\Publico\MunicipiosController::class, 'listar'])->name('municipios.listar');
    Route::get('/entidades-orcamentarias', [\App\Http\Controllers\Api\Publico\EntidadesOrcamentariasController::class, 'listar'])->name('entidades.listar');
});
```

---

## 🔄 **FLUXO COMPLETO DO SISTEMA**

### **1. Solicitação (Usuário Externo):**
```
1. Usuário acessa /solicitar-cadastro
2. Preenche formulário (nome, email, senha, município, entidade, justificativa)
3. Sistema valida dados
4. Cria usuário (is_active = false)
5. Cria solicitação (status = pendente)
6. Exibe confirmação e orienta para aguardar aprovação
```

### **2. Aprovação (Administrador):**
```
1. Admin acessa /administracao/aprovacao-cadastros
2. Visualiza lista de solicitações pendentes
3. Clica em "Ver Detalhes" na solicitação
4. Analisa dados e justificativa
5. Pode alterar município/entidade se necessário
6. Adiciona observações obrigatórias
7. Clica em "Aprovar" ou "Rejeitar"
8. Sistema processa:
   - Se aprovado: ativa usuário + cria vinculação
   - Se rejeitado: apenas atualiza status
```

### **3. Gestão de Vínculos (Administrador):**
```
1. Admin acessa /administracao/usuarios-por-entidade
2. Seleciona uma entidade orçamentária
3. Visualiza usuários vinculados
4. Pode adicionar novos usuários (busca)
5. Pode remover usuários existentes
6. Sistema mantém histórico completo
```

### **4. Uso do Sistema (Usuário Aprovado):**
```
1. Usuário faz login normalmente
2. Sistema verifica vínculos com entidades
3. Usuário pode criar orçamentos (funcionalidade futura)
4. Pode solicitar vínculo a novas entidades
```

---

## 🧪 **PONTOS DE TESTE**

### **Testes Funcionais:**
1. **Formulário Público:**
   - Validação de campos obrigatórios
   - Validação de email único
   - Validação de senha forte
   - Submissão bem-sucedida

2. **Processo de Aprovação:**
   - Lista de solicitações carrega corretamente
   - Filtros funcionam adequadamente
   - Modal de detalhes exibe informações corretas
   - Aprovação cria usuário ativo e vinculação
   - Rejeição atualiza status corretamente

3. **Gestão de Vínculos:**
   - Seleção de entidade carrega usuários corretos
   - Adição de novos vínculos funciona
   - Remoção de vínculos funciona
   - Prevenção de vínculos duplicados

### **Testes de Segurança:**
1. **Autorização:**
   - Usuários sem permissão não acessam telas admin
   - Formulário público não requer autenticação
   - API protegida contra acessos não autorizados

2. **Validação:**
   - Dados são sanitizados adequadamente
   - Validações server-side funcionam
   - CSRF protection ativo

### **Testes de Performance:**
1. **Consultas Otimizadas:**
   - Listagens usam paginação
   - Relacionamentos usam eager loading
   - Índices adequados nas FKs

---

## 📚 **OBSERVAÇÕES IMPORTANTES**

### **Decisões Arquiteturais:**
1. **Não alterar tabela `users`:** Manter estrutura existente intacta
2. **Tabelas separadas:** Para histórico completo e flexibilidade
3. **Relacionamento N:N:** Permitir múltiplas entidades por usuário
4. **Status granular:** Controle fino sobre estados das solicitações

### **Extensibilidade Futura:**
1. **Notificações:** Sistema preparado para observers/events
2. **Workflow complexo:** Base para aprovações em múltiplas etapas
3. **Auditoria:** Histórico completo para compliance
4. **Integração:** Preparado para APIs externas

### **Manutenibilidade:**
1. **Seguir .cursorrules:** Padrões consistentes em todo código
2. **Documentação:** Cada funcionalidade bem documentada
3. **Testes:** Cobertura adequada para funcionalidades críticas
4. **Logs:** Rastreabilidade de ações importantes

---

## 🚀 **PRÓXIMOS PASSOS PARA IMPLEMENTAÇÃO**

### **Fase 1: Base de Dados**
1. ✅ Criar migration `solicitacoes_cadastro`
2. ✅ Criar migration `user_entidades_orcamentarias`
3. ✅ Criar migration para nova permissão
4. ✅ Executar migrations no ambiente

### **Fase 2: Models e Relacionamentos**
1. ✅ Criar model `SolicitacaoCadastro`
2. ✅ Atualizar model `User` com novos relacionamentos
3. ✅ Testar relacionamentos via tinker

### **Fase 3: Controllers e Lógica**
1. ✅ Implementar controller público (solicitação)
2. ✅ Implementar controllers administrativos
3. ✅ Implementar APIs correspondentes
4. ✅ Adicionar middleware de autorização

### **Fase 4: Frontend e Interface**
1. ✅ Criar formulário público de solicitação
2. ✅ Criar telas administrativas
3. ✅ Criar componentes Vue.js
4. ✅ Implementar modais de confirmação

### **Fase 5: Rotas e Navegação**
1. ✅ Configurar rotas públicas e administrativas
2. ✅ Atualizar menu lateral com nova seção
3. ✅ Configurar permissões nas rotas
4. ✅ Testar fluxo completo

### **Fase 6: Testes e Refinamento**
1. ✅ Testes funcionais das principais funcionalidades
2. ✅ Testes de autorização e segurança
3. ✅ Ajustes de UI/UX conforme feedback
4. ✅ Otimizações de performance

---

## 📝 **CONSIDERAÇÕES FINAIS**

Este planejamento está **completo e pronto para implementação**. Todas as decisões arquiteturais foram tomadas com base nas necessidades do negócio e seguem os padrões estabelecidos no projeto ORÇACIDADE.

A estrutura proposta é:
- ✅ **Escalável:** Suporta crescimento futuro
- ✅ **Segura:** Controles de acesso adequados  
- ✅ **Flexível:** Permite múltiplas entidades por usuário
- ✅ **Auditável:** Histórico completo de todas as ações
- ✅ **Consistente:** Segue padrões do .cursorrules

**O sistema está preparado para evoluir conforme novas necessidades surjam, mantendo sempre a integridade dos dados e a experiência do usuário.**

---

**Documento criado em:** Janeiro 2025  
**Última atualização:** Janeiro 2025  
**Responsável:** Equipe de Desenvolvimento ORÇACIDADE  
**Status:** ✅ Aprovado para implementação
