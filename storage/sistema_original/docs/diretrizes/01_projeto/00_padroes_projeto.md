# Padrões de Projeto - OrçaCidade

> **DOCUMENTO CENTRAL**: Este documento define a estrutura e organização do projeto OrçaCidade. **OBRIGATÓRIO** consultar os documentos de padrão para implementação.

> **ATUALIZADO EM 2025**: Projeto organizado com arquitetura Vue.js + API e separação clara de responsabilidades.

---

## 1. Visão Geral

### 🎯 **Objetivo**
Definir a estrutura organizacional e arquitetural do projeto OrçaCidade, estabelecendo princípios de organização e separação de responsabilidades.

### 🏗️ **Princípios Organizacionais**
- **Separação de Responsabilidades** - Cada camada com função específica
- **Modularidade** - Organização por módulos e funcionalidades
- **Escalabilidade** - Estrutura preparada para crescimento
- **Manutenibilidade** - Organização que facilita manutenção
- **Consistência** - Padrões uniformes em todo o projeto

---

## 2. Arquitetura do Projeto

### 🏛️ **Estrutura Geral**

#### **Camadas da Aplicação**
```
┌─────────────────────────────────────┐
│           Interface (Vue.js)        │ ← Camada de Apresentação
├─────────────────────────────────────┤
│         Controllers (Web/Api)       │ ← Camada de Controle
├─────────────────────────────────────┤
│           Models (Eloquent)         │ ← Camada de Dados
├─────────────────────────────────────┤
│         Services (Lógica)           │ ← Camada de Negócio
├─────────────────────────────────────┤
│         Database (MySQL)            │ ← Camada de Persistência
└─────────────────────────────────────┘
```

#### **Separação de Responsabilidades**
- **Interface:** Componentes Vue.js para interação com usuário
- **Controllers Web:** Servem views container para Vue.js
- **Controllers API:** Processam dados e retornam JSON
- **Models:** Representam entidades e regras de dados
- **Services:** Encapsulam lógica de negócio complexa
- **Database:** Armazena dados de forma estruturada

---

## 3. Organização de Módulos

### 📁 **Estrutura Modular**

#### **Princípio de Organização**
O projeto OrçaCidade está organizado em **módulos funcionais** que agrupam funcionalidades relacionadas:

```
Projeto OrçaCidade
├── Módulo Administrativo
│   └── Funcionalidades de gestão e controle
├── Módulo de Orçamento
│   └── Funcionalidades de cálculo e análise
├── Módulo de Tabelas Oficiais
│   └── Funcionalidades de consulta e importação
└── Módulo de Transporte
    └── Funcionalidades de cálculo de custos
```

#### **Características dos Módulos**
- **Independência:** Cada módulo pode funcionar isoladamente
- **Coesão:** Funcionalidades relacionadas agrupadas
- **Baixo Acoplamento:** Mínima dependência entre módulos
- **Escalabilidade:** Fácil adição de novos módulos

---

## 4. Documentos de Padrão

### 📚 **Estrutura de Documentação**

O projeto possui **4 documentos de padrão** que definem como implementar funcionalidades:

#### **1. Estrutura de Diretórios (`01_padrao_estrutura_diretorios.md`)**
- **Propósito:** Organização de arquivos e pastas
- **Cobertura:** Estrutura de controllers, models, views, componentes
- **Status:** ✅ **ATUALIZADO** - Estrutura Vue.js + API

#### **2. Layout e Interface (`02_padrao_layout_interface.md`)**
- **Propósito:** Padrões visuais e de UX
- **Cobertura:** Cores, CSS, componentes visuais, responsividade
- **Status:** ✅ **ATUALIZADO** - Interface moderna

#### **3. Bibliotecas e Tecnologias (`03_padrao_bibliotecas.md`)**
- **Propósito:** Stack tecnológico aprovado
- **Cobertura:** Framework, bibliotecas, ferramentas, versões
- **Status:** ✅ **MANTIDO** - Tecnologias atuais

#### **4. Rotas (`04_padrao_rotas.md`)**
- **Propósito:** Estrutura e nomenclatura de rotas
- **Cobertura:** Rotas Web, rotas API, middlewares
- **Status:** ✅ **ATUALIZADO** - Rotas Vue.js + API

---

## 5. Princípios de Organização

### 🎯 **Organização de Código**

#### **Separação por Tipo**
- **Controllers:** Lógica de controle e roteamento
- **Models:** Entidades e regras de dados
- **Views:** Templates de interface
- **Components:** Componentes Vue.js reutilizáveis
- **Services:** Lógica de negócio complexa

#### **Organização por Módulo**
- **Módulos:** Agrupamento por área funcional
- **Funcionalidades:** Subdivisões dentro dos módulos
- **Recursos:** Arquivos específicos de cada funcionalidade

#### **Organização por Responsabilidade**
- **Web:** Interface e apresentação
- **API:** Dados e processamento
- **Core:** Lógica central e regras de negócio

---

## 6. Evolução da Arquitetura

### 📈 **Histórico de Mudanças**

#### **2025 - Vue.js + API**
- **Mudança:** Migração para arquitetura moderna
- **Motivo:** Melhor organização e separação de responsabilidades
- **Impacto:** Estrutura mais escalável e manutenível
- **Resultado:** Projeto bem organizado e preparado para crescimento

#### **Princípios Mantidos**
- **Separação de responsabilidades**
- **Modularidade**
- **Escalabilidade**
- **Manutenibilidade**

---

## 7. Conclusão

### 🎉 **RESULTADO FINAL**

**Este documento define claramente como o projeto está organizado!**

**Qualquer desenvolvedor (ou IA) entende a estrutura consultando:**

- ✅ **Arquitetura Geral** - Como o projeto está estruturado
- ✅ **Organização Modular** - Como os módulos estão organizados
- ✅ **Separação de Responsabilidades** - Como as camadas funcionam
- ✅ **Documentos de Padrão** - Onde encontrar diretrizes de implementação
- ✅ **Princípios Organizacionais** - Como manter a organização

**Com esta documentação, o projeto tem estrutura clara, organizada e escalável!**

---

> **IMPORTANTE**: Este documento define a organização do projeto. Para implementação, consulte os documentos de padrão específicos.

