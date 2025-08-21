# 📋 Padrão de Logs do Sistema

## 🎯 **OBJETIVO**

Este documento define o padrão de logs implementado no sistema, servindo como referência para implementação de logs em novas funcionalidades. O padrão foi desenvolvido para ser **objetivo, limpo e focado**, priorizando informações essenciais para auditoria e troubleshooting.

**NOVA ARQUITETURA:** Logs separados por módulo/funcionalidade + Log central consolidado para auditoria.

---

## 🏗️ **ARQUITETURA DE LOGS**

### **📁 ESTRUTURA DE ARQUIVOS:**
```
storage/logs/
├── tabela_oficial/
│   ├── importar_derpr.log        # Funcionalidade: Importar DER-PR
│   ├── importar_sinapi.log       # Funcionalidade: Importar SINAPI
│   └── outras_tabelas.log        # Funcionalidade: Outras tabelas
├── orcamento/
│   ├── composicoes_proprias.log  # Funcionalidade: Composições próprias
│   ├── bdi.log                   # Funcionalidade: BDI
│   └── orcamento_consolidado.log # Funcionalidade: Orçamento consolidado
├── administracao/
│   ├── gerenciar_usuarios.log    # Funcionalidade: Gerenciar usuários
│   ├── gerenciar_permissoes.log  # Funcionalidade: Gerenciar permissões
│   └── gerenciar_municipios.log  # Funcionalidade: Gerenciar municípios
└── sistema/
    └── sistema_master.log        # Log central consolidado para auditoria
```

### **🎯 CONCEITO HÍBRIDO:**
- **✅ Logs Específicos:** Cada funcionalidade tem seu próprio arquivo `[modulo]/[funcionalidade].log`
- **✅ Log Central:** Operações críticas vão para `sistema/sistema_master.log`
- **✅ Performance:** Arquivos menores, busca mais rápida
- **✅ Organização:** Separação clara por módulo e funcionalidade

---

## 🏗️ **ESTRUTURA BASE DO LOG**

### **Formato Padrão:**
```log
[Data/Hora] [MODULO] [TIPO] [Usuario: ID (Nome)] [IP: xxx.xxx.xxx.xxx] - Operação | Contexto
```

### **Exemplo Prático:**
```log
[2025-08-21 13:39:49] [DERPR] [INICIO_OPERACAO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - Iniciando: ABA_1_SERVICOS_GERAIS
[2025-08-21 13:39:54] [DERPR] [SUCESSO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - Concluído com sucesso: ABA_1_SERVICOS_GERAIS | Contexto: {"total_composicoes":706}
```

---

## 🏷️ **PADRÃO DE NOMENCLATURA**

### **1. Funcionalidades Principais:**
- **`ABA_X_FUNCIONALIDADE`** - Para abas/tabs de interface
- **`FUNCIONALIDADE_OPERACAO`** - Para operações específicas
- **`MODULO_ACAO`** - Para ações dentro de módulos

### **2. Exemplos Implementados:**
```php
// Abas de processamento
'ABA_1_SERVICOS_GERAIS'
'ABA_2_INSUMOS'
'ABA_3_FORMULAS_TRANSPORTE'

// Operações críticas
'GRAVACAO_BANCO_DERPR'
'GRAVACAO_ARQUIVO'
'VERIFICAR_ARQUIVOS'
```

---

## 📊 **TIPOS DE LOG**

### **1. INICIO_OPERACAO**
**Quando usar:** Início de qualquer operação significativa
**Estrutura:** `[INICIO_OPERACAO] - Iniciando: FUNCAO`
**Contexto:** Dados iniciais relevantes (opcional)
**Log Central:** ✅ SIM (sempre)

```php
$this->logger->inicioOperacao('FUNCIONALIDADE_OPERACAO', [
    'parametro' => $valor,
    'usuario' => auth()->user()->name
]);
```

### **2. PROGRESSO**
**Quando usar:** Etapas intermediárias importantes (apenas para operações críticas)
**Estrutura:** `[PROGRESSO] - OPERACAO: Status`
**Contexto:** Informações de progresso relevantes
**Log Central:** ❌ NÃO (apenas log específico)

```php
$this->logger->progresso('GRAVACAO_ARQUIVO', "Processando: {$nomeArquivo}");
```

### **3. SUCESSO**
**Quando usar:** Conclusão bem-sucedida de operações
**Estrutura:** `[SUCESSO] - Concluído com sucesso: FUNCAO`
**Contexto:** Resultados e métricas importantes
**Log Central:** ✅ SIM (sempre)

```php
$this->logger->sucesso('FUNCIONALIDADE_OPERACAO', [
    'total_registros' => $count,
    'tempo_processamento' => $tempo
]);
```

### **4. ERRO_VALIDACAO**
**Quando usar:** Erros de validação de dados (não críticos)
**Estrutura:** `[VALIDACAO] - Erro de validação em FUNCAO: Descrição`
**Contexto:** Dados do erro para debugging
**Log Central:** ❌ NÃO (apenas log específico)

```php
$this->logger->erroValidacao('FUNCIONALIDADE_OPERACAO', $mensagem, [
    'dados' => $contexto
]);
```

### **5. ERRO_CRITICO**
**Quando usar:** Erros críticos do sistema
**Estrutura:** `[ERRO_CRITICO] - Erro crítico em FUNCAO: Descrição`
**Contexto:** Informações para troubleshooting
**Log Central:** ✅ SIM (sempre)

```php
$this->logger->erroCritico('FUNCIONALIDADE_OPERACAO', $e->getMessage(), [
    'arquivo' => $nomeArquivo
]);
```

---

## 🎨 **REGRAS DE VERBOSIDADE**

### **✅ OPERAÇÕES SIMPLES (Logs Mínimos):**
- **Apenas:** INICIO_OPERACAO + SUCESSO
- **Exemplo:** Processamento de PDF → Excel
- **Justificativa:** Baixo risco, operação rápida
- **Log Central:** ✅ SIM (apenas operações críticas)

### **⚠️ OPERAÇÕES CRÍTICAS (Logs Detalhados):**
- **Incluir:** INICIO_OPERACAO + PROGRESSO + SUCESSO
- **Exemplo:** Gravação no banco de dados
- **Justificativa:** Alto risco, necessidade de auditoria completa
- **Log Central:** ✅ SIM (todas as operações)

---

## 🔧 **IMPLEMENTAÇÃO TÉCNICA - NOVA ARQUITETURA**

### **1. Service Base de Log:**
```php
<?php

namespace App\Services\Logging;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

abstract class LogService
{
    protected string $module;
    protected string $logFile;
    protected string $masterLogFile = 'sistema/sistema_master.log';
    
    public function __construct(string $module, string $logFile)
    {
        $this->module = strtoupper($module);
        $this->logFile = $logFile;
    }
    
    /**
     * Log centralizado para funcionalidade específica
     */
    protected function log($level, $origin, $message, $context = [])
    {
        // Log específico da funcionalidade
        $this->logToFile($this->logFile, $level, $origin, $message, $context);
        
        // Log central para auditoria (apenas operações críticas)
        if ($this->isCriticalOperation($origin)) {
            $this->logToFile($this->masterLogFile, $level, $origin, $message, $context);
        }
    }
    
    /**
     * Determina se a operação deve ir para o log central
     */
    private function isCriticalOperation($origin): bool
    {
        return in_array($origin, [
            'INICIO_OPERACAO',
            'SUCESSO',
            'ERRO_CRITICO'
        ]);
    }
    
    /**
     * Escreve log em arquivo específico
     */
    private function logToFile($logFile, $level, $origin, $message, $context = [])
    {
        $user = Auth::user();
        $userInfo = $user ? "Usuario: {$user->id} ({$user->name})" : "Usuario: N/A (Não autenticado)";
        $ip = Request::ip();
        $timestamp = now()->format('Y-m-d H:i:s');

        $formattedMessage = "[{$timestamp}] [{$this->module}] [{$origin}] [{$userInfo}] [IP: {$ip}] - {$message}";

        $logPath = storage_path("logs/{$logFile}");
        $logDir = dirname($logPath);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logEntry = $formattedMessage;
        if (!empty($context)) {
            $logEntry .= " | Contexto: " . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        $logEntry .= "\n";

        file_put_contents($logPath, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log de início de operação
     */
    public function inicioOperacao($operation, $context = [])
    {
        $this->log('info', 'INICIO_OPERACAO', "Iniciando: {$operation}", $context);
    }
    
    /**
     * Log de progresso da operação
     */
    public function progresso($operation, $status, $context = [])
    {
        $this->log('info', 'PROGRESSO', "{$operation}: {$status}", $context);
    }
    
    /**
     * Log de sucesso da operação
     */
    public function sucesso($operation, $resultados = [], $context = [])
    {
        $this->log('info', 'SUCESSO', "Concluído com sucesso: {$operation}", array_merge($resultados, $context));
    }
    
    /**
     * Log de erro de validação
     */
    public function erroValidacao($operation, $error, $context = [])
    {
        $this->log('error', 'VALIDACAO', "Erro de validação em {$operation}: {$error}", $context);
    }
    
    /**
     * Log de erro crítico
     */
    public function erroCritico($operation, $error, $context = [])
    {
        $this->log('error', 'ERRO_CRITICO', "Erro crítico em {$operation}: {$error}", $context);
        Log::error("Erro crítico na funcionalidade: {$operation} - {$error}", $context);
    }
}
```

### **2. Service Específico para DER-PR:**
```php
<?php

namespace App\Services\Logging;

class ImportarDerprLogService extends LogService
{
    public function __construct()
    {
        parent::__construct('DERPR', 'tabela_oficial/importar_derpr.log');
    }
    
    /**
     * Logs específicos para DER-PR
     */
    public function inicioAba($numero, $funcionalidade, $context = [])
    {
        $this->inicioOperacao("ABA_{$numero}_{$funcionalidade}", $context);
    }
    
    public function sucessoAba($numero, $funcionalidade, $context = [])
    {
        $this->sucesso("ABA_{$numero}_{$funcionalidade}", $context);
    }
    
    public function gravacaoBanco($context = [])
    {
        $this->inicioOperacao('GRAVACAO_BANCO_DERPR', $context);
    }
    
    public function gravacaoArquivo($nomeArquivo)
    {
        $this->progresso('GRAVACAO_ARQUIVO', "Processando: {$nomeArquivo}");
    }
}
```

### **3. Uso no Controller:**
```php
class ImportarDerprController extends Controller
{
    protected ImportarDerprLogService $logger;
    
    public function __construct(ImportarDerprLogService $logger)
    {
        $this->middleware('auth');
        $this->logger = $logger;
    }
    
    public function processarServicosGerais(Request $request)
    {
        $this->logger->inicioAba(1, 'SERVICOS_GERAIS', [
            'arquivo' => $request->file('arquivo')->getClientOriginalName()
        ]);
        
        // ... lógica de negócio limpa
        
        $this->logger->sucessoAba(1, 'SERVICOS_GERAIS', [
            'total_composicoes' => count($dados)
        ]);
    }
}
```

---

## 📋 **CHECKLIST DE IMPLEMENTAÇÃO**

### **✅ ANTES DE IMPLEMENTAR:**
- [ ] Definir nomenclatura da funcionalidade
- [ ] Identificar operações críticas vs. simples
- [ ] Definir quais etapas merecem logs de PROGRESSO
- [ ] Escolher arquivo de log apropriado: `[modulo]/[funcionalidade].log`
- [ ] Decidir se vai para log central

### **✅ IMPLEMENTAÇÃO:**
- [ ] Criar service base de log
- [ ] Criar service específico da funcionalidade
- [ ] Injetar service no controller
- [ ] Implementar logs de INICIO_OPERACAO
- [ ] Implementar logs de SUCESSO
- [ ] Implementar logs de PROGRESSO (se crítico)
- [ ] Implementar logs de ERRO (validação e crítico)

### **✅ VALIDAÇÃO:**
- [ ] Testar operação completa
- [ ] Verificar formato dos logs
- [ ] Confirmar rastreabilidade
- [ ] Validar contexto das mensagens
- [ ] Verificar log central
- [ ] Validar arquivos separados

---

## 🎯 **EXEMPLOS PRÁTICOS IMPLEMENTADOS**

### **📊 DER-PR Import (Nova Arquitetura):**

#### **Arquivo: `storage/logs/tabela_oficial/importar_derpr.log`**
```log
[2025-08-21 13:39:49] [DERPR] [INICIO_OPERACAO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - Iniciando: ABA_1_SERVICOS_GERAIS
[2025-08-21 13:39:54] [DERPR] [SUCESSO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - Concluído com sucesso: ABA_1_SERVICOS_GERAIS | Contexto: {"total_composicoes":706}
[2025-08-21 13:40:29] [DERPR] [INICIO_OPERACAO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - Iniciando: ABA_2_INSUMOS
[2025-08-21 13:41:33] [DERPR] [SUCESSO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - Concluído com sucesso: ABA_2_INSUMOS | Contexto: {"total_insumos":4769}
[2025-08-21 13:42:11] [DERPR] [INICIO_OPERACAO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - Iniciando: GRAVACAO_BANCO_DERPR
[2025-08-21 13:42:37] [DERPR] [PROGRESSO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - GRAVACAO_ARQUIVO: Processando: derpr_composicoes.xlsx
[2025-08-21 13:42:38] [DERPR] [SUCESSO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - Concluído com sucesso: GRAVACAO_BANCO_DERPR | Contexto: {"tempo_processamento":"25.4s","total_arquivos":7}
```

#### **Arquivo: `storage/logs/sistema/sistema_master.log`**
```log
[2025-08-21 13:39:49] [DERPR] [INICIO_OPERACAO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - Iniciando: ABA_1_SERVICOS_GERAIS
[2025-08-21 13:39:54] [DERPR] [SUCESSO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - Concluído com sucesso: ABA_1_SERVICOS_GERAIS | Contexto: {"total_composicoes":706}
[2025-08-21 13:40:29] [DERPR] [INICIO_OPERACAO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - Iniciando: ABA_2_INSUMOS
[2025-08-21 13:41:33] [DERPR] [SUCESSO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - Concluído com sucesso: ABA_2_INSUMOS | Contexto: {"total_insumos":4769}
[2025-08-21 13:42:11] [DERPR] [INICIO_OPERACAO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - Iniciando: GRAVACAO_BANCO_DERPR
[2025-08-21 13:42:38] [DERPR] [SUCESSO] [Usuario: 1 (Super Administrador)] [IP: 127.0.0.1] - Concluído com sucesso: GRAVACAO_BANCO_DERPR | Contexto: {"tempo_processamento":"25.4s","total_arquivos":7}
```

**Nota:** O log central contém apenas operações críticas (INICIO_OPERACAO, SUCESSO, ERRO_CRITICO), enquanto o log específico contém todas as operações incluindo PROGRESSO.

---

## 🚨 **ANTI-PADRÕES (NÃO FAZER)**

### **❌ Logs Verbosos Desnecessários:**
```php
// NÃO FAZER - Log de cada linha processada
$this->logger->progresso('PROCESSAMENTO', "Processando linha {$linha} do arquivo {$arquivo}");

// NÃO FAZER - Log de operações triviais
$this->logger->progresso('ARQUIVO', 'Arquivo aberto com sucesso');
$this->logger->progresso('DIRETORIO', 'Diretório criado com sucesso');
```

### **❌ Contexto Excessivo:**
```php
// NÃO FAZER - Contexto muito detalhado
$this->logger->sucesso('OPERACAO', [
    'usuario_id' => $user->id,
    'usuario_email' => $user->email,
    'usuario_role' => $user->role,
    'ip_origem' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'timestamp_inicio' => $inicio,
    'timestamp_fim' => $fim,
    'memoria_consumida' => memory_get_usage(),
    'tempo_cpu' => microtime(true)
]);

// FAZER - Contexto relevante
$this->logger->sucesso('OPERACAO', [
    'total_registros' => $count,
    'tempo_processamento' => round($tempo, 2) . 's'
]);
```

### **❌ Não Usar Service de Log:**
```php
// NÃO FAZER - Métodos de log no controller
class SeuController extends Controller
{
    private function logInicioOperacao($operation, $context = [])
    {
        // 30+ linhas de código duplicado
    }
}

// FAZER - Usar service de log
class SeuController extends Controller
{
    protected SeuLogService $logger;
    
    public function __construct(SeuLogService $logger)
    {
        $this->logger = $logger;
    }
    
    public function operacao()
    {
        $this->logger->inicioOperacao('OPERACAO');
    }
}
```

---

## 🔍 **TROUBLESHOOTING DE LOGS**

### **Problema: Log não aparece**
- [ ] Verificar permissões do diretório `storage/logs/`
- [ ] Confirmar se o arquivo de log foi criado
- [ ] Verificar se o usuário está autenticado
- [ ] Confirmar se o service foi injetado corretamente

### **Problema: Formato incorreto**
- [ ] Verificar se o service de log foi criado corretamente
- [ ] Confirmar nomenclatura do módulo
- [ ] Validar estrutura do contexto
- [ ] Verificar se o service base foi estendido

### **Problema: Logs muito verbosos**
- [ ] Revisar regras de verbosidade
- [ ] Remover logs de PROGRESSO desnecessários
- [ ] Simplificar contexto das mensagens
- [ ] Verificar se operações não críticas vão para log central

### **Problema: Log central não funciona**
- [ ] Verificar se o diretório `sistema/` foi criado
- [ ] Confirmar se operações críticas estão sendo identificadas
- [ ] Validar método `isCriticalOperation()`
- [ ] Verificar permissões do diretório central

---

## 📚 **REFERÊNCIAS**

- **Arquivo de Log DER-PR:** `storage/logs/tabela_oficial/importar_derpr.log`
- **Log Central:** `storage/logs/sistema/sistema_master.log`
- **Controller de Referência:** `app/Http/Controllers/Api/TabelaOficial/ImportarDerprController.php`
- **Service de Log Base:** `app/Services/Logging/LogService.php`
- **Service DER-PR:** `app/Services/Logging/ImportarDerprLogService.php`
- **Padrão de Workflow:** `docs/diretrizes/02_desenvolvimento/04_padrao_workflow_progresso.md`

---

## 🎯 **CONCLUSÃO**

Esta nova arquitetura de logs foi desenvolvida para ser:
- **🎯 Objetivo:** Cada linha tem propósito claro
- **🧹 Limpo:** Sem ruído ou informações redundantes
- **📊 Rastreável:** Auditoria completa quando necessário
- **⚡ Eficiente:** Performance otimizada com arquivos separados
- **🔄 Reutilizável:** Fácil de implementar em novas funcionalidades
- **📁 Organizada:** Separação clara por módulo e funcionalidade
- **🔍 Consolidada:** Log central para auditoria geral

**Para implementar logs em uma nova funcionalidade:**
1. **Ler este documento** para entender o padrão
2. **Criar service específico** estendendo `LogService`
3. **Injetar no controller** via dependency injection
4. **Seguir o checklist** de implementação

**A arquitetura híbrida oferece o melhor dos dois mundos: logs específicos para debugging e log central para auditoria geral.**

**Estrutura de arquivos: `[modulo]/[funcionalidade].log` + `sistema/sistema_master.log`**
