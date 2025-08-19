# Manual do Usuário - Gerenciamento de Usuários e Permissões

## 📋 Visão Geral

### **O que é este módulo?**
Sistema de **Gerenciamento de Usuários e Permissões de Acesso** do OrcaCidade.

### **Quem pode acessar?**
- ✅ **Super Administrador** - Acesso total
- ✅ **Gerenciador de Usuários** - CRUD completo
- ✅ **Visualizador de Usuários** - Apenas visualização

---

## 🔐 Sistema de Permissões

### **📊 Matriz de Permissões por Abas**

#### **👥 ABA "USUÁRIOS"**
| Funcionalidade | Permissão `_crud` | Permissão `_consultar` |
|----------------|-------------------|------------------------|
| **Listar usuários** | `usuario_crud` | `usuario_consultar` |
| **Filtrar usuários** | `usuario_crud` | `usuario_consultar` |
| **Botões CRUD (Novo/Editar/Excluir)** | `usuario_crud` | ❌ Não pode |

#### **🏷️ ABA "PAPÉIS"**
| Funcionalidade | Permissão `_crud` | Permissão `_consultar` |
|----------------|-------------------|------------------------|
| **Listar papéis** | `papel_crud` | `papel_consultar` |
| **Filtrar papéis** | `papel_crud` | `papel_consultar` |
| **Botões CRUD (Novo/Editar/Excluir)** | `papel_crud` | ❌ Não pode |
| **Botão "Gerenciar Permissões"** | `papel_crud` | `papel_consultar` |
| **Botão "Gerenciar Usuários"** | `papel_crud` | `papel_consultar` |
| **DENTRO do modal "Gerenciar Permissões":** | | |
| - Ver permissões do papel | `papel_crud` | `papel_consultar` |
| - **Adicionar/Remover permissões** | `papel_crud` | ❌ Não pode |
| **DENTRO do modal "Gerenciar Usuários":** | | |
| - Ver usuários do papel | `papel_crud` | `papel_consultar` |
| - **Adicionar/Remover usuários** | `papel_crud` | ❌ Não pode |

#### **🔑 ABA "PERMISSÕES"**
| Funcionalidade | Permissão `_crud` | Permissão `_consultar` |
|----------------|-------------------|------------------------|
| **Listar permissões** | `permissao_crud` | `permissao_consultar` |
| **Filtrar permissões** | `permissao_crud` | `permissao_consultar` |
| **Botões CRUD (Nova/Editar/Excluir)** | `permissao_crud` | ❌ Não pode |
| **Botão "Visualizar Detalhes"** | `permissao_crud` | `permissao_consultar` |

#### **🔍 ABA "BUSCA GLOBAL"**
| Funcionalidade | Permissão `_crud` | Permissão `_consultar` |
|----------------|-------------------|------------------------|
| **Buscar usuários** | `usuario_crud` | `usuario_consultar` |
| **Buscar papéis** | `usuario_crud` | `usuario_consultar` |
| **Buscar permissões** | `usuario_crud` | `usuario_consultar` |

---

### **🎭 Papéis Predefinidos**

| Papel | Permissões | Acesso |
|-------|------------|---------|
| **`super`** | Todas (bypass total) | Sistema completo |
| **`gerenciar_usuarios`** | `usuario_crud` + `papel_crud` + `permissao_crud` | CRUD completo em todas as abas |
| **`visualizar_usuarios`** | `usuario_consultar` + `papel_consultar` + `permissao_consultar` | Apenas visualização |

---

