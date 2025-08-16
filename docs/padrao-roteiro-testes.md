# Padrão de Roteiro de Testes — Módulo

Este documento estabelece o padrão para elaboração de roteiros de testes de módulos do sistema OrçaCidade. Cada roteiro deve ser específico para um módulo e orientar os usuários na execução de testes de forma simples e objetiva.

## 📋 Checklist de Análise Obrigatória

**ANTES de iniciar a elaboração do roteiro de testes, execute esta análise completa para garantir precisão:**

- [ ] **Acesse as rotas** (`routes/web.php` e `routes/api.php`)
- [ ] **Acesse a documentação** técnica do módulo (`docs/técnico/`)
- [ ] **Acesse os controllers** principais e secundários
- [ ] **Acesse os componentes** Vue e views Blade
- [ ] **Acesse os services** (se houver)
- [ ] **Acesse as migrações** (`database/migrations/`) 
- [ ] **Acesse o relatório** do módulo (`docs/relatorios/`)
- [ ] **Acesse `docs/padrao-roteiro-testes.md`** (este arquivo)

**⚠️ IMPORTANTE:** Apenas com análise completa de TODOS os arquivos é possível criar um roteiro de testes preciso e abrangente.

---

## 📝 Estrutura do Roteiro de Testes

### 1. Cabeçalho do Módulo
```
# Roteiro de Testes — [Nome do Módulo]

## Informações Gerais
- **Módulo:** [Nome do módulo]
- **Objetivo:** [Descrição resumida do objetivo do módulo]
- **URL Base:** [URL principal do módulo]
- **Data:** [Data]
- **Testador:** [Nome]
```

### 2. Funcionalidades Básicas
```
## Funcionalidades Básicas

### Cadastro e Edição
- [ ] Cadastro está funcionando?
- [ ] Edição está funcionando?
- [ ] Exclusão está funcionando?
- [ ] Listagem está funcionando?

### Validações
- [ ] Campos obrigatórios estão sendo validados?
- [ ] Máscaras de campos estão funcionando?
- [ ] Validações de formato estão ok?
- [ ] Validações de negócio estão funcionando?

### Interface
- [ ] Botões estão funcionando?
- [ ] Navegação está ok?
- [ ] Mensagens de erro/sucesso aparecem?
- [ ] Modais abrem e fecham corretamente?
```

### 3. Funcionalidades Específicas do Módulo
```
## Funcionalidades Específicas

### [Funcionalidade Específica 1]
- [ ] [Funcionalidade específica 1] está funcionando?

### [Funcionalidade Específica 2]
- [ ] [Funcionalidade específica 2] está funcionando?

### [Funcionalidade Específica 3]
- [ ] [Funcionalidade específica 3] está funcionando?
```

### 4. Performance e Usabilidade
```
## Performance e Usabilidade

### Tempo de Resposta
- [ ] Páginas carregam em tempo razoável?
- [ ] Ações (salvar, editar, excluir) são rápidas?
- [ ] Filtros respondem rapidamente?

### Usabilidade
- [ ] Interface está intuitiva?
- [ ] Filtros funcionam corretamente?
- [ ] Paginação está funcionando?
- [ ] Busca está funcionando?
```

### 5. Checklist Final
```
## Checklist Final

### Problemas Encontrados
| Problema | Descrição | Gravidade |
|----------|-----------|-----------|
| [Problema 1] | [Descrição] | [Alta/Média/Baixa] |
| [Problema 2] | [Descrição] | [Alta/Média/Baixa] |

### Observações
[Observações importantes sobre o teste, sugestões de melhoria, etc.]

### Status Final
- [ ] **OK** - Tudo funcionando
- [ ] **Com problemas** - Alguns itens com problemas
- [ ] **Crítico** - Muitos problemas encontrados

**Data de Conclusão:** [Data]
**Testador:** [Nome]
```

---

## 📋 Orientações para Elaboração

### 1. Análise do Módulo
- **Identifique todas as funcionalidades** principais e secundárias
- **Mapeie os fluxos** de usuário mais importantes
- **Identifique pontos críticos** que devem ser testados
- **Considere casos de borda** e cenários de erro

### 2. Estruturação dos Testes
- **Use linguagem simples** e direta
- **Formato de perguntas** objetivas
- **Foque no resultado** esperado
- **Evite termos técnicos** complexos

### 3. Cobertura de Testes
- **Teste funcionalidades básicas** (CRUD, navegação)
- **Teste validações** e regras de negócio
- **Teste interface** e usabilidade
- **Teste performance** básica

### 4. Documentação
- **Mantenha registro** de problemas encontrados
- **Classifique problemas** por gravidade
- **Documente observações** importantes
- **Seja objetivo** nas descrições

---

## 🎯 Exemplo de Aplicação

Para cada módulo, adapte este padrão considerando:
- **Funcionalidades específicas** do módulo
- **Regras de negócio** particulares
- **Dados de teste** apropriados
- **Cenários de uso** reais dos usuários

**Lembre-se:** O objetivo é que o usuário final consiga executar os testes de forma simples e objetiva! 🚀
