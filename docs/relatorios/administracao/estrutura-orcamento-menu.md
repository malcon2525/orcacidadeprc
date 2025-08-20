# Menu: Estrutura de Orçamento

## 📋 Visão Geral

O menu **Estrutura de Orçamento** permite gerenciar a estrutura hierárquica de orçamentos, incluindo tipos de orçamento, grandes itens e subgrupos. Este sistema é fundamental para organizar e categorizar itens orçamentários de forma hierárquica.

## 🎯 Funcionalidades Principais

### 1. **Tipos de Orçamento**
- **Criar** novos tipos de orçamento com versões
- **Editar** tipos existentes
- **Excluir** tipos não utilizados
- **Visualizar** lista de todos os tipos
- **Filtrar** por descrição e status

### 2. **Estrutura de Orçamento**
- **Gerenciar Grandes Itens** dentro de cada tipo
- **Criar Subgrupos** dentro dos grandes itens
- **Reordenar** itens via drag & drop
- **Editar** descrições e ordens
- **Excluir** itens e subgrupos

### 3. **Importação Excel**
- **Importar** estrutura completa via arquivo Excel
- **Substituir** estrutura existente
- **Validar** formato do arquivo
- **Preview** antes da importação

### 4. **Visualização Integrada**
- **Visualizar** estrutura hierárquica completa
- **Navegar** entre tipos, grandes itens e subgrupos
- **Consultar** informações detalhadas

## 🔐 Sistema de Permissões

### **Permissões Disponíveis:**

| Permissão | Descrição | Acesso |
|-----------|-----------|---------|
| `estrutura_orcamento_crud` | Gerenciar Estrutura de Orçamento | Criação, edição, exclusão e visualização completa |
| `estrutura_orcamento_consultar` | Consultar Estrutura de Orçamento | Apenas visualização e consulta |
| `estrutura_orcamento_importar` | Importar Estrutura de Orçamento | Importação via Excel |

### **Roles Disponíveis:**

| Role | Permissões | Descrição |
|------|------------|-----------|
| `gerenciar_estrutura_orcamento` | Todas | Acesso completo ao sistema |
| `visualizar_estrutura_orcamento` | Apenas consulta | Acesso somente para visualização |

## 🚀 Como Acessar

### **URL do Menu:**
```
/admin/estrutura-orcamento
```

### **Condição de Acesso:**
```php
// No layout principal (app.blade.php)
@if(Auth::user()->hasRole('gerenciar_estrutura_orcamento') || 
     Auth::user()->hasRole('visualizar_estrutura_orcamento'))
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.estrutura-orcamento.index') }}">
            <i class="fas fa-sitemap me-2"></i>Estrutura de Orçamentos
        </a>
    </li>
@endif
```

## 📱 Interface do Usuário

### **Sistema de Abas:**
1. **Tipo de Orçamento** - Gerenciar tipos e versões
2. **Estrutura de Orçamento** - Gerenciar hierarquia
3. **Visualização Integrada** - Visualizar estrutura completa

### **Funcionalidades por Aba:**

#### **Aba 1: Tipo de Orçamento**
- Lista paginada de tipos
- Filtros por descrição e status
- CRUD completo (se tiver permissão)
- Paginação centralizada

#### **Aba 2: Estrutura de Orçamento**
- Seletor de tipo de orçamento
- Estrutura hierárquica visual
- Drag & drop para reordenação
- Botões de ação contextuais

#### **Aba 3: Visualização Integrada**
- Visão consolidada da estrutura
- Navegação entre níveis
- Informações detalhadas

## 🔧 Configuração Técnica

### **Controllers:**
- `EstruturaOrcamentoController` (Web) - Interface principal
- `TipoOrcamentoController` (API) - CRUD de tipos
- `GrandeItemController` (API) - CRUD de grandes itens
- `SubGrupoController` (API) - CRUD de subgrupos
- `ImportacaoController` (API) - Importação Excel
- `LimparEstruturaController` (API) - Limpeza de estrutura

### **Models:**
- `TipoOrcamento` - Tipos de orçamento
- `GrandeItem` - Grandes itens dentro dos tipos
- `SubGrupo` - Subgrupos dentro dos grandes itens

### **Componentes Vue:**
- `GestaoEstruturaOrcamento.vue` - Componente principal com abas
- `ListaTipoOrcamento.vue` - Lista de tipos de orçamento
- `EstruturaOrcamento.vue` - Gerenciamento da estrutura hierárquica
- `VisualizacaoIntegrada.vue` - Visualização consolidada

### **Rotas:**
```php
// Web Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('estrutura-orcamento', [EstruturaOrcamentoController::class, 'index'])
         ->name('estrutura-orcamento.index');
});

// API Routes
Route::prefix('administracao/estrutura-orcamento')->group(function () {
    Route::apiResource('tipo-orcamento', TipoOrcamentoController::class);
    Route::apiResource('grande-item', GrandeItemController::class);
    Route::apiResource('subgrupo', SubGrupoController::class);
    Route::post('importar', [ImportacaoController::class, 'importar']);
    Route::delete('limpar/{tipoOrcamentoId}', [LimparEstruturaController::class, 'limpar']);
});
```

## 📊 Estrutura do Banco de Dados

### **Tabelas:**
- `eo_tipos_orcamentos` - Tipos de orçamento
- `eo_grandes_itens` - Grandes itens
- `eo_sub_grupos` - Subgrupos

### **Relacionamentos:**
- Tipo de Orçamento → Grandes Itens (1:N)
- Grande Item → Subgrupos (1:N)

## 🎨 Estilos e CSS

### **Classes CSS Utilizadas:**
- `admin-tabs-container` - Container das abas
- `admin-tabs` - Estilo das abas
- `admin-tab-content` - Conteúdo das abas
- `custom-modal-header` - Header dos modais
- `header-icon` - Ícones dos headers

### **Responsividade:**
- Layout adaptativo para dispositivos móveis
- Abas colapsam em telas pequenas
- Tabelas responsivas com scroll horizontal

## 🚨 Considerações Importantes

### **Segurança:**
- Todas as operações são verificadas por permissões
- Validação de dados em todos os formulários
- Proteção CSRF ativa
- Middleware de autenticação

### **Performance:**
- Paginação para listas grandes
- Lazy loading de relacionamentos
- Índices de banco otimizados
- Cache de consultas frequentes

### **Usabilidade:**
- Interface intuitiva com drag & drop
- Feedback visual para todas as ações
- Validação em tempo real
- Mensagens de erro claras

## 📝 Exemplos de Uso

### **Criar Tipo de Orçamento:**
1. Acessar aba "Tipo de Orçamento"
2. Clicar em "Novo Tipo"
3. Preencher descrição, versão e status
4. Salvar

### **Importar Estrutura:**
1. Selecionar tipo de orçamento
2. Clicar em "Importar Excel"
3. Arrastar arquivo ou selecionar
4. Confirmar importação

### **Reordenar Itens:**
1. Na aba "Estrutura de Orçamento"
2. Arrastar itens para nova posição
3. Ordem é salva automaticamente

## 🔄 Manutenção

### **Limpeza de Estrutura:**
- Botão "Limpar Estrutura" remove todos os itens
- Confirmação obrigatória antes da exclusão
- Log de todas as operações

### **Backup:**
- Estrutura pode ser exportada via Excel
- Dados são preservados em histórico
- Rollback disponível via seeder

---

**Última atualização:** {{ date('d/m/Y H:i:s') }}  
**Versão:** 1.0  
**Autor:** Sistema de Documentação Automática
