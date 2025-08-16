# Padrão de Log de Auditoria

---

## 1. Visão Geral

Este documento estabelece o padrão para a criação de logs de auditoria e histórico no sistema. O objetivo é garantir a rastreabilidade completa das ações importantes, registrando **quem**, **o quê**, **quando** e **de onde** cada ação foi executada.

## 2. Princípios

1.  **Um Arquivo por Módulo:** Cada módulo principal do sistema (ex: Importação SINAPI, Orçamentos, Usuários) deve ter seu próprio arquivo de log, para manter a organização. Ex: `storage/logs/importacao_sinapi.log`.
2.  **Logs Cumulativos:** Os logs de auditoria devem ser **sempre acrescentados** ao arquivo (`append`). Eles **não devem rotacionar** ou ser apagados, para preservar o histórico.
3.  **Formato de Mensagem Padrão:** Todas as mensagens de log devem seguir um formato consistente para facilitar a leitura e a automação.
4.  **Duplo Log para Exceções:** Erros fatais (exceções não tratadas) devem ser registrados tanto no log de auditoria do módulo quanto no log de erros padrão do Laravel (`laravel.log`).

---

## 3. Implementação

A implementação é centralizada em um método auxiliar (`private function`) dentro do Controller responsável pelo módulo.

### 3.1. O Método `logAuditoria`

Adicione o seguinte método ao seu Controller. Ele cria um logger dinamicamente, formatando a mensagem e direcionando-a para o arquivo correto.

```php
/**
 * Gera um log de auditoria em um arquivo específico do módulo.
 *
 * @param string $level Nível do log (info, error, warning)
 * @param string $origin Módulo/Ação de origem da mensagem (ex: 'CRIAR_ORCAMENTO')
 * @param string $message Mensagem descritiva do evento
 * @param array $context Dados adicionais para o log
 */
private function logAuditoria($level, $origin, $message, $context = [])
{
    $user = \Illuminate\Support\Facades\Auth::user();
    $userInfo = $user ? "Usuario: {$user->id} ({$user->name})" : "Usuario: N/A";
    $ip = request()->ip();

    $formattedMessage = "[{$origin}] [{$userInfo}] [IP: {$ip}] - {$message}";

    // Define o nome do arquivo de log para este módulo
    $logFile = 'auditoria_meu_modulo.log'; // <-- IMPORTANTE: Altere para o nome do seu módulo

    // Cria um logger on-the-fly que aponta para o arquivo no modo 'append'
    $logger = \Illuminate\Support\Facades\Log::build([
        'driver' => 'single',
        'path' => storage_path('logs/' . $logFile),
    ]);

    $logger->{$level}($formattedMessage, $context);
}
```
**Atenção:** Lembre-se de alterar o valor da variável `$logFile` para o nome do arquivo de log do seu módulo.

### 3.2. Padrão de Chamada

Dentro dos métodos do seu controller, chame a função `logAuditoria`:

```php
$this->logAuditoria('info', 'MINHA_ACAO', 'Ação iniciada com sucesso.');
```

### 3.3. Tratamento de Exceções

Para garantir o duplo log de erros, utilize um bloco `try-catch`. Logue a exceção no arquivo de auditoria e, em seguida, **relance-a** (`throw $e;`) para que o handler padrão do Laravel a capture.

---

## 4. Exemplo Completo em um Controller

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MeuModuloController extends Controller
{
    /**
     * Gera um log de auditoria em um arquivo específico do módulo.
     *
     * @param string $level Nível do log (info, error, warning)
     * @param string $origin Módulo/Ação de origem da mensagem (ex: 'CRIAR_ORCAMENTO')
     * @param string $message Mensagem descritiva do evento
     * @param array $context Dados adicionais para o log
     */
    private function logAuditoria($level, $origin, $message, $context = [])
    {
        $user = Auth::user();
        $userInfo = $user ? "Usuario: {$user->id} ({$user->name})" : "Usuario: N/A";
        $ip = request()->ip();

        $formattedMessage = "[{$origin}] [{$userInfo}] [IP: {$ip}] - {$message}";

        // IMPORTANTE: Defina o nome do arquivo de log para este módulo
        $logFile = 'auditoria_meumodulo.log';

        $logger = Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/' . $logFile),
        ]);

        $logger->{$level}($formattedMessage, $context);
    }

    public function minhaAcao(Request $request)
    {
        $origin = 'MINHA_ACAO';
        $this->logAuditoria('info', $origin, 'Processo iniciado.');

        try {
            // Lógica de negócio...
            if (!$request->input('dado_obrigatorio')) {
                $this->logAuditoria('warning', $origin, 'Tentativa de execução sem o dado obrigatório.');
                return response()->json(['error' => 'Dado obrigatório não fornecido.'], 400);
            }

            // Simula uma falha crítica
            // throw new \Exception('Ocorreu um erro fatal!');

            $this->logAuditoria('info', $origin, 'Processo concluído com sucesso.');
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            // Loga a exceção no arquivo de auditoria
            $this->logAuditoria('error', $origin, 'Erro crítico no processo.', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Relança a exceção para ser capturada pelo log padrão do Laravel (laravel.log)
            throw $e;
        }
    }
}
``` 