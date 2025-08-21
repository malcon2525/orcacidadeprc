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
