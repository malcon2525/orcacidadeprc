# Manual do Usuário - Gerenciamento de Municípios

## 📋 Visão Geral

### **O que é este módulo?**
Sistema de **Gerenciamento de Municípios** do OrcaCidade, permitindo cadastro, edição, consulta e importação de dados municipais.

### **Quem pode acessar?**
- ✅ **Super Administrador** - Acesso total
- ✅ **Gerenciador de Municípios** - CRUD completo + importação
- ✅ **Visualizador de Municípios** - Apenas visualização

---

## 🔐 Sistema de Permissões

### **📊 Matriz de Permissões por Funcionalidades**

#### **👥 FUNCIONALIDADES PRINCIPAIS**
| Funcionalidade | Permissão `_crud` | Permissão `_consultar` | Permissão `_importar` |
|----------------|-------------------|------------------------|------------------------|
| **Listar municípios** | `municipio_crud` | `municipio_consultar` | `municipio_importar` |
| **Filtrar municípios** | `municipio_crud` | `municipio_consultar` | `municipio_importar` |
| **Botões CRUD (Novo/Editar/Excluir)** | `municipio_crud` | ❌ Não pode | ❌ Não pode |
| **Botão "Importar"** | ❌ Não pode | ❌ Não pode | `municipio_importar` |
| **Visualizar detalhes** | `municipio_crud` | `municipio_consultar` | `municipio_importar` |

#### **📝 FORMULÁRIOS**
| Funcionalidade | Permissão `_crud` | Permissão `_consultar` | Permissão `_importar` |
|----------------|-------------------|------------------------|------------------------|
| **Modal "Novo Município"** | `municipio_crud` | ❌ Não pode | ❌ Não pode |
| **Modal "Editar Município"** | `municipio_crud` | ❌ Não pode | ❌ Não pode |
| **Modal "Confirmar Exclusão"** | `municipio_crud` | ❌ Não pode | ❌ Não pode |
| **Modal "Importar Municípios"** | ❌ Não pode | ❌ Não pode | `municipio_importar` |

#### **🔍 FILTROS E BUSCA**
| Funcionalidade | Permissão `_crud` | Permissão `_consultar` | Permissão `_importar` |
|----------------|-------------------|------------------------|------------------------|
| **Filtro por nome** | `municipio_crud` | `municipio_consultar` | `municipio_importar` |
| **Filtro por estado** | `municipio_crud` | `municipio_consultar` | `municipio_importar` |
| **Filtro por código IBGE** | `municipio_crud` | `municipio_consultar` | `municipio_importar` |
| **Busca em tempo real** | `municipio_crud` | `municipio_consultar` | `municipio_importar` |

---

### **🎭 Papéis Predefinidos**

| Papel | Permissões | Acesso |
|-------|------------|---------|
| **`super`** | Todas (bypass total) | Sistema completo |
| **`gerenciar_municipios`** | `municipio_crud` + `municipio_consultar` + `municipio_importar` | CRUD completo + importação |
| **`visualizar_municipios`** | `municipio_consultar` | Apenas visualização |

---

### **📋 Campos do Município**

#### **Campos Obrigatórios:**
- Nome do Município
- Estado
- Código IBGE
- Prefeito
- Email
- Telefone

#### **Campos Opcionais:**
- Endereço da Prefeitura
- População
- CEP
- CNPJ

---

### **🚀 Funcionalidades Especiais**

#### **Importação de Dados:**
- **Fonte:** Banco PostgreSQL externo
- **Tabela:** `municipio`
- **Campos importados:** nome, prefeito, email, endereço, código IBGE, população, CEP, telefone, CNPJ
- **Permissão necessária:** `municipio_importar`

#### **Validações:**
- Nome único por estado
- Código IBGE único
- Email válido
- Telefone obrigatório

---

### **💡 Dicas de Uso**

1. **Para visualizar apenas:** Use o papel `visualizar_municipios`
2. **Para gerenciar:** Use o papel `gerenciar_municipios`
3. **Para importar dados:** Necessário ter permissão `municipio_importar`
4. **Filtros:** Use os filtros para encontrar municípios específicos
5. **Paginação:** Navegue pelos resultados usando a paginação

---

### **⚠️ Observações Importantes**

- **Usuários com apenas `municipio_consultar`** não podem criar, editar ou excluir municípios
- **Usuários com apenas `municipio_importar`** não podem fazer CRUD, apenas importar dados
- **A importação sobrescreve** dados existentes com base no código IBGE
- **Todos os usuários** podem visualizar a lista de municípios (se tiverem acesso ao menu)
