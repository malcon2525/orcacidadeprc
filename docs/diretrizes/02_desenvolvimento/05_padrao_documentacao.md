# Padrão de Documentação - OrçaCidade

> **DOCUMENTO MESTRE**: Este documento define padrões para documentação de funcionalidades no projeto OrçaCidade. **OBRIGATÓRIO** seguir estas diretrizes para manter consistência e qualidade.

---

## 📋 **EXEMPLO DE USO DESTE ARQUIVO**

### 🎯 **Instrução para IA:**
```
"Baseado na funcionalidade X, seguindo a orientação do arquivo '05_padrao_documentacao.md', crie a documentação completa"
```

### 📝 **Como Funciona:**
1. **Leia** as diretrizes do projeto nos arquivos da pasta `docs/diretrizes/01_projeto`
2. **Analise** a funcionalidade para entender sua estrutura
3. **Aplique** os padrões definidos neste documento
4. **Gere** documentação completa e funcional
5. **Mantenha** consistência visual e estrutural

### ✅ **Resultado Esperado:**
- Documentação técnica completa
- Informações práticas e úteis
- Estrutura consistente e organizada
- Exemplos práticos incluídos
- Checklist de verificação

---

## 2. Diretrizes do Projeto

### ⚠️ **PRÉ-REQUISITO OBRIGATÓRIO:**

**Para o desenvolvimento, utilize as diretrizes do projeto que podem ser compreendidas pela análise do arquivo:**
```
docs/diretrizes/01_projeto/*.*
```

**Este arquivo orienta sobre:**
- Estrutura de diretórios
- Padrões visuais
- Bibliotecas utilizadas
- Estrutura de rotas
- Nomenclatura e comentários

---

## 3. Estrutura Obrigatória

### 📚 **Seções Principais (Obrigatórias)**

#### **1. Visão Geral**
- **Objetivo:** Descreva claramente o objetivo da funcionalidade
- **Contexto:** Explique o papel da funcionalidade no sistema
- **Público-alvo:** Usuários finais e desenvolvedores

#### **2. Rotas/API**
- **Rotas Web:** Tabela com método, endpoint, controller/action, descrição
- **Rotas API:** Tabela separada para endpoints de API
- **Exemplos:** Payloads e respostas JSON quando relevante

#### **3. Arquivos Envolvidos**
- **Controller:** Caminho completo do arquivo
- **Model:** Caminho completo do arquivo
- **Migration:** Nome do arquivo de migração
- **Views Blade:** Caminho das views
- **Componentes Vue:** Caminho dos componentes
- **Rotas:** Arquivos de rotas utilizados

#### **4. Estrutura de Dados**
- **Tabelas:** Estrutura SQL resumida com campos principais
- **Campos-chave:** PK, FKs, campos obrigatórios
- **Views:** Views materializadas (se houver)
- **Índices:** Índices importantes (se houver)

#### **5. Regras de Negócio**
- **Campos obrigatórios:** Lista de campos obrigatórios
- **Validações:** Regras de validação específicas
- **Unicidade:** Campos que devem ser únicos
- **Relacionamentos:** Relacionamentos obrigatórios

#### **6. Funcionalidades**
- **CRUD:** Operações básicas disponíveis
- **Funcionalidades especiais:** Diferenciais da funcionalidade
- **Integrações:** Integrações com outras funcionalidades
- **Validações:** Validações implementadas

#### **7. Fluxo de Uso**
- **Passo a passo:** Sequência típica de uso
- **Exemplos:** Casos de uso práticos
- **Decisões:** Pontos de decisão no fluxo

#### **8. Interface/UX/UI**
- **Layout:** Descrição do layout principal
- **Componentes:** Principais componentes utilizados
- **Feedback:** Feedbacks visuais implementados
- **Responsividade:** Comportamento responsivo

#### **9. Dependências e Integrações**
- **Funcionalidades dependentes:** Funcionalidades que esta funcionalidade utiliza
- **Funcionalidades dependentes desta:** Funcionalidades que utilizam esta funcionalidade
- **APIs externas:** Integrações com sistemas externos
- **Bibliotecas:** Bibliotecas específicas utilizadas

#### **10. Processos Automáticos**
- **Rotinas:** Processos automáticos executados
- **Agendamento:** Frequência de execução
- **Critérios:** Critérios de execução
- **Logs:** Logs gerados pelos processos

#### **11. Testes**
- **Testes manuais:** Testes que o usuário deve fazer
- **Cenários:** Cenários de teste recomendados
- **Validações:** Validações que devem ser testadas
- **Funcionalidades:** Funcionalidades que devem ser verificadas

---

## 4. Seções Opcionais

### 📋 **Exemplos Práticos (Opcional)**
- **Payloads:** Exemplos de dados de entrada
- **Respostas:** Exemplos de dados de saída
- **Casos de uso:** Exemplos práticos de uso
- **Configurações:** Exemplos de configuração

---

## 5. Exemplo de Estrutura

### 📋 **Template Base**

```markdown
# Funcionalidade [Nome da Funcionalidade]

---

## 1. Visão Geral
- **Objetivo:** [Descreva o objetivo da funcionalidade]
- **Contexto:** [Explique o papel no sistema]
- **Público-alvo:** [Usuários finais e desenvolvedores]

---

## 2. Rotas/API

### Rotas Web (`routes/web.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /[endpoint] | [Controller]@[action] | [Descrição] |

### Rotas API (`routes/api.php`)
| Método | Endpoint | Controller/Action | Descrição |
|--------|----------|-------------------|-----------|
| GET | /api/[endpoint] | [Controller]@[action] | [Descrição] |

**Exemplo de retorno:**
```json
{
  "data": [...],
  "current_page": 1,
  "last_page": 1
}
```

---

## 3. Arquivos Envolvidos
- **Controller:** `app/Http/Controllers/[Namespace]/[Controller].php`
- **Model:** `app/Models/[Namespace]/[Model].php`
- **Migration:** `database/migrations/[migration].php`
- **Componente Vue:** `resources/js/components/[namespace]/[component].vue`
- **Views Blade:** `resources/views/[namespace]/[view].blade.php`
- **Rotas:** `routes/web.php`, `routes/api.php`

---

## 4. Estrutura de Dados

Tabela: `[nome_tabela]`
| Campo | Tipo | Regras/Descrição |
|-------|------|------------------|
| id | bigint | PK, auto-increment |
| [campo] | [tipo] | [regras] |

---

## 5. Regras de Negócio
- [Campo obrigatório]
- [Validação específica]
- [Relacionamento obrigatório]

---

## 6. Funcionalidades
- [Funcionalidade principal]
- [Funcionalidade especial]
- [Integração com funcionalidade]

---

## 7. Fluxo de Uso
1. [Passo 1]
2. [Passo 2]
3. [Passo 3]

---

## 8. Interface/UX/UI
- [Descrição do layout]
- [Componentes principais]
- [Feedbacks visuais]

---

## 9. Dependências e Integrações
- **Funcionalidades dependentes:** [Lista de funcionalidades]
- **Funcionalidades dependentes desta:** [Lista de funcionalidades]
- **APIs externas:** [Integrações externas]

---

## 10. Processos Automáticos
- [Processo automático]
- [Frequência de execução]
- [Critérios de execução]

---

## 11. Testes
- [Teste de criação]
- [Teste de edição]
- [Teste de exclusão]
- [Teste de validação]

---

## Checklist
- [ ] Visão Geral
- [ ] Rotas/API
- [ ] Arquivos Envolvidos
- [ ] Estrutura de Dados
- [ ] Regras de Negócio
- [ ] Funcionalidades
- [ ] Fluxo de Uso
- [ ] Interface/UX/UI
- [ ] Dependências e Integrações
- [ ] Processos Automáticos
- [ ] Testes
- [ ] Exemplos Práticos (Opcional)
```

---

## 7. Proibições Essenciais

### 🚫 **NÃO Fazer**

#### **Conteúdo**
- **NÃO** usar comentários entre `<!-- -->`
- **NÃO** incluir seções desnecessárias
- **NÃO** usar exemplos genéricos
- **NÃO** deixar seções vazias

#### **Formato**
- **NÃO** inventar novas seções
- **NÃO** usar formatação inconsistente
- **NÃO** usar padrões visuais diferentes das diretrizes
- **NÃO** omitir seções obrigatórias
- **NÃO** usar "módulo" em vez de "funcionalidade"

#### **Informações**
- **NÃO** incluir informações desatualizadas
- **NÃO** omitir detalhes técnicos importantes
- **NÃO** usar linguagem muito técnica para usuários finais
- **NÃO** usar linguagem muito simples para desenvolvedores

#### **Organização**
- **NÃO** salvar documentação na raiz de `docs/técnico/` (sempre usar pasta do módulo)
- **NÃO** usar nomenclatura incorreta (sempre seguir padrão `[nome].md`)
- **NÃO** não verificar estrutura de pastas antes de criar documentação

---

## 8. Checklist de Implementação

### 📋 **Para Nova Documentação**

#### **Estrutura**
- [ ] Seguir estrutura obrigatória
- [ ] Usar padrão visual estabelecido
- [ ] Incluir todas as seções obrigatórias
- [ ] Adicionar seções opcionais quando relevante

#### **Conteúdo**
- [ ] Informações técnicas completas
- [ ] Exemplos práticos incluídos
- [ ] Fluxo de uso detalhado
- [ ] Testes manuais documentados

#### **Formato**
- [ ] Seguir formatação consistente
- [ ] Incluir checklist de verificação
- [ ] Usar padrões visuais das diretrizes
- [ ] Seguir estrutura obrigatória
- [ ] Manter consistência terminológica (funcionalidade, não módulo)

#### **Qualidade**
- [ ] Revisar informações técnicas
- [ ] Testar exemplos fornecidos
- [ ] Verificar links e referências
- [ ] Validar estrutura completa
- [ ] **Localização correta** do arquivo na pasta do módulo
- [ ] **Nomenclatura padronizada** do arquivo

---

## 9. Localização e Organização dos Arquivos de Documentação

### 📁 **Estrutura de Pastas:**
- **Localização:** `docs/técnico/[nome-do-modulo]/`
- **Exemplo:** `docs/técnico/administracao/gerenciamento-usuarios-permissoes.md`
- **Padrão:** Seguir a estrutura de módulos do projeto (administracao, transporte, orcamento, clientes, etc.)

### 📝 **Nomenclatura:**
- **Formato:** `[nome-funcionalidade].md`
- **Exemplo:** `gerenciamento-usuarios-permissoes.md`
- **Padrão:** Usar hífens para separar palavras, terminar com `.md`

### 🔍 **Organização por Módulos:**
- **Módulo Administração:** `docs/técnico/administracao/`
- **Módulo Transporte:** `docs/técnico/transporte/`
- **Módulo Orçamento:** `docs/técnico/orcamento/`
- **Módulo Clientes:** `docs/técnico/clientes/`
- **Consultas de Tabelas:** `docs/técnico/consultar tabelas/`

### ⚠️ **IMPORTANTE:**
- **SEMPRE** criar documentação na pasta do módulo correspondente
- **NUNCA** deixar documentação na raiz de `docs/técnico/`
- **SEMPRE** seguir a nomenclatura padronizada
- **SEMPRE** verificar se a pasta do módulo existe antes de criar a documentação

---

## 10. Conclusão

### 🎉 **RESULTADO FINAL**

**Este documento define claramente como criar documentação no projeto!**

**Qualquer desenvolvedor (ou IA) consegue criar documentação sem dúvidas sobre:**

- ✅ **Estrutura** - Como organizar seções
- ✅ **Conteúdo** - Que informações incluir
- ✅ **Formato** - Como formatar visualmente
- ✅ **Exemplos** - Como incluir exemplos práticos
- ✅ **Testes** - Como documentar testes manuais
- ✅ **Proibições** - O que NÃO fazer
- ✅ **Funcionalidades** - Como documentar funcionalidades específicas
- ✅ **Localização** - Onde salvar os arquivos de documentação
- ✅ **Organização** - Como organizar por módulos

**Com este documento, qualquer nova documentação será consistente, organizada e de alta qualidade!**

---

> **IMPORTANTE**: Este documento deve ser atualizado sempre que houver mudanças nos padrões de documentação. Todos os desenvolvedores devem seguir estas diretrizes para manter consistência. 