# Manual do Usuário - Gerenciamento de Entidades Orçamentárias

## 📋 Visão Geral

### **O que é este módulo?**
Sistema de **Gerenciamento de Entidades Orçamentárias** do OrcaCidade, permitindo cadastro, edição, consulta e importação de dados de entidades como municípios, secretarias, órgãos, autarquias e outros.

### **Quem pode acessar?**
- ✅ **Super Administrador** - Acesso total
- ✅ **Gerenciador de Entidade Orçamentária** - CRUD completo + importação
- ✅ **Visualizador de Entidade Orçamentária** - Apenas visualização

---

## 🔐 Sistema de Permissões

### **📊 Matriz de Permissões por Funcionalidades**

#### **👥 FUNCIONALIDADES PRINCIPAIS**
| Funcionalidade | Permissão `_crud` | Permissão `_consultar` | Permissão `_importar` |
|----------------|-------------------|------------------------|------------------------|
| **Listar entidades** | `entidade_orcamentaria_crud` | `entidade_orcamentaria_consultar` | `entidade_orcamentaria_importar` |
| **Filtrar entidades** | `entidade_orcamentaria_crud` | `entidade_orcamentaria_consultar` | `entidade_orcamentaria_importar` |
| **Botões CRUD (Novo/Editar/Excluir)** | `entidade_orcamentaria_crud` | ❌ Não pode | ❌ Não pode |
| **Botão "Importar Municípios"** | ❌ Não pode | ❌ Não pode | `entidade_orcamentaria_importar` |
| **Visualizar detalhes** | `entidade_orcamentaria_crud` | `entidade_orcamentaria_consultar` | `entidade_orcamentaria_importar` |

#### **📝 FORMULÁRIOS**
| Funcionalidade | Permissão `_crud` | Permissão `_consultar` | Permissão `_importar` |
|----------------|-------------------|------------------------|------------------------|
| **Modal "Nova Entidade"** | `entidade_orcamentaria_crud` | ❌ Não pode | ❌ Não pode |
| **Modal "Editar Entidade"** | `entidade_orcamentaria_crud` | ❌ Não pode | ❌ Não pode |
| **Modal "Confirmar Exclusão"** | `entidade_orcamentaria_crud` | ❌ Não pode | ❌ Não pode |
| **Modal "Importar Municípios"** | ❌ Não pode | ❌ Não pode | `entidade_orcamentaria_importar` |

#### **🔍 FILTROS E BUSCA**
| Funcionalidade | Permissão `_crud` | Permissão `_consultar` | Permissão `_importar` |
|----------------|-------------------|------------------------|------------------------|
| **Filtro por razão social** | `entidade_orcamentaria_crud` | `entidade_orcamentaria_consultar` | `entidade_orcamentaria_importar` |
| **Filtro por nome fantasia** | `entidade_orcamentaria_crud` | `entidade_orcamentaria_consultar` | `entidade_orcamentaria_importar` |
| **Filtro por tipo de organização** | `entidade_orcamentaria_crud` | `entidade_orcamentaria_consultar` | `entidade_orcamentaria_importar` |
| **Busca em tempo real** | `entidade_orcamentaria_crud` | `entidade_orcamentaria_consultar` | `entidade_orcamentaria_importar` |

---

### **🎭 Papéis Predefinidos**

| Papel | Permissões | Acesso |
|-------|------------|---------|
| **`super`** | Todas (bypass total) | Sistema completo |
| **`gerenciar_entidade_orcamentaria`** | `entidade_orcamentaria_crud` + `entidade_orcamentaria_consultar` + `entidade_orcamentaria_importar` | CRUD completo + importação |
| **`visualizar_entidade_orcamentaria`** | `entidade_orcamentaria_consultar` | Apenas visualização |

---

### **📋 Campos da Entidade Orçamentária**

#### **Campos Obrigatórios:**
- Razão Social
- Nome Fantasia
- Tipo de Organização (municipio, secretaria, órgão, autarquia, outros)
- Email
- Telefone
- Responsável
- Cargo do Responsável

#### **Campos Opcionais:**
- Endereço
- Código IBGE
- População
- CEP
- CNPJ
- Telefone do Responsável
- Email do Responsável

---

### **🚀 Funcionalidades Especiais**

#### **Importação de Municípios:**
- **Fonte:** Banco PostgreSQL externo
- **Tabela:** `municipio`
- **Campos importados:** nome, prefeito, email, endereço_prefeitura, código_ibge, população, cep, telefone, cnpj
- **Permissão necessária:** `entidade_orcamentaria_importar`
- **Comportamento:** Cria novas entidades ou atualiza existentes baseado no nome do município

#### **Tipos de Organização:**
- **Município** - Prefeituras municipais
- **Secretaria** - Secretarias de governo
- **Órgão** - Órgãos públicos
- **Autarquia** - Autarquias públicas
- **Outros** - Demais tipos de entidades

#### **Validações:**
- Razão social única
- Nome fantasia único
- Email único
- Código IBGE único
- CNPJ único
- Email válido
- Telefone obrigatório

---

### **💡 Dicas de Uso**

1. **Para visualizar apenas:** Use o papel `visualizar_entidade_orcamentaria`
2. **Para gerenciar:** Use o papel `gerenciar_entidade_orcamentaria`
3. **Para importar municípios:** Necessário ter permissão `entidade_orcamentaria_importar`
4. **Filtros:** Use os filtros para encontrar entidades específicas
5. **Paginação:** Navegue pelos resultados usando a paginação
6. **Tipo de organização:** Use os badges coloridos para identificar rapidamente o tipo

---

### **⚠️ Observações Importantes**

- **Usuários com apenas `entidade_orcamentaria_consultar`** não podem criar, editar ou excluir entidades
- **Usuários com apenas `entidade_orcamentaria_importar`** não podem fazer CRUD, apenas importar municípios
- **A importação de municípios** cria entidades do tipo "municipio" automaticamente
- **Dados duplicados** são atualizados baseado no nome do município
- **Todos os usuários** podem visualizar a lista de entidades (se tiverem acesso ao menu)
- **O campo "Responsável"** é obrigatório para todas as entidades
- **CNPJ e Código IBGE** são únicos no sistema

---

### **🔗 Integrações**

#### **Com Municípios:**
- Importação automática de dados municipais
- Conversão de municípios em entidades orçamentárias
- Mapeamento de campos específicos

#### **Com Sistema de Usuários:**
- Controle de acesso baseado em papéis
- Permissões granulares por funcionalidade
- Auditoria de ações realizadas
