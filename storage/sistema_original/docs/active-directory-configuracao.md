# Configuração da Sincronização Automática do Active Directory

## Visão Geral

Este documento explica como configurar a sincronização automática do Active Directory no sistema.

## Configuração no Kernel

Para configurar a sincronização automática, edite o arquivo `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    // Sincronização automática do Active Directory
    // Configurações podem ser alteradas via interface web em /administracao/active-directory
    $schedule->job(new \App\Jobs\SyncActiveDirectoryJob())
        ->daily()
        ->at('02:00')
        ->withoutOverlapping()
        ->runInBackground()
        ->onFailure(function () {
            Log::error('Sincronização automática AD falhou');
        });
}
```

## Configurações Disponíveis

### Frequência
- `daily` - Diária (padrão)
- `weekly` - Semanal
- `monthly` - Mensal

### Horário
- Formato: `HH:MM` (24 horas)
- Padrão: `02:00`

### Status
- `true` - Habilitada
- `false` - Desabilitada

## Logs de Sincronização

### Informações Registradas

O sistema registra logs detalhados de todas as sincronizações no arquivo `storage/logs/ad.log`:

#### Sincronização Manual (via Interface Web)
```json
{
    "message": "Sincronização AD concluída",
    "context": {
        "tipo": "manual",
        "executado_por": {
            "id": 1,
            "nome": "João Silva",
            "email": "joao.silva@exemplo.com"
        },
        "resultados": {
            "usuarios_processados": 150,
            "usuarios_criados": 5,
            "usuarios_atualizados": 145,
            "usuarios_desativados": 0,
            "erros": []
        },
        "executado_em": "2024-01-15T10:30:00.000000Z"
    }
}
```

#### Sincronização Automática (via Job)
```json
{
    "message": "Job de sincronização AD concluído",
    "context": {
        "executado_por": "sistema (agendamento)",
        "resultado": {
            "usuarios_processados": 150,
            "usuarios_criados": 2,
            "usuarios_atualizados": 148,
            "usuarios_desativados": 0,
            "erros": []
        }
    }
}
```

#### Sincronização via Comando CLI
```json
{
    "message": "Sincronização AD executada via comando CLI",
    "context": {
        "executado_por": "CLI (comando artisan)",
        "tipo": "completa"
    }
}
```

### Campos de Log

- **executado_por**: Identifica quem executou a sincronização
  - Usuário autenticado (manual)
  - Sistema (automática)
  - CLI (comando artisan)
- **tipo**: Tipo de sincronização (manual/completa)
- **resultados**: Estatísticas da sincronização
- **executado_em**: Timestamp da execução
- **duracao**: Tempo de execução em segundos
- **erros**: Lista de erros encontrados

## Verificação de Configurações

### Via Interface Web
Acesse `/administracao/active-directory` e use o botão "Testar" para verificar as configurações.

### Via Comando Artisan
```bash
# Verificar configurações atuais
php artisan ad:check-config

# Testar configurações
php artisan ad:test-config
```

### Via API
```bash
# Obter configurações
GET /api/administracao/active-directory/config

# Testar configurações
GET /api/administracao/active-directory/config/test
```

## Próximos Passos

Para implementar sincronização dinâmica baseada nas configurações da interface web, será necessário:

1. Modificar o `Kernel.php` para ler configurações do cache
2. Implementar lógica de agendamento dinâmico
3. Adicionar validação de configurações
4. Implementar notificações de falha 