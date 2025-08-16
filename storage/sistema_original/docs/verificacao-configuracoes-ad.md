# Verificação de Configurações da Sincronização AD

## Como saber se as configurações foram salvas e funcionam?

### 1. **Interface Web** 📱

Acesse `/administracao/active-directory` e você verá:

- **Indicadores visuais**: Badges mostrando se está habilitada/desabilitada
- **Última atualização**: Data e hora da última modificação
- **Resumo da configuração**: Próxima execução calculada automaticamente
- **Botão "Testar"**: Verifica se as configurações estão válidas

### 2. **Comandos Artisan** ⚡

Execute no terminal:

```bash
# Verificar configurações atuais
php artisan ad:check-config

# Testar se as configurações estão funcionando
php artisan ad:test-config
```

### 3. **API Endpoints** 🔌

```bash
# Obter configurações atuais
GET /api/administracao/active-directory/config

# Testar configurações
GET /api/administracao/active-directory/config/test
```

### 4. **O que é verificado?** ✅

#### **Salvamento no Cache:**
- ✅ Frequência salva (`ad_sync_frequency`)
- ✅ Horário salvo (`ad_sync_time`)
- ✅ Status salvo (`ad_sync_enabled`)
- ✅ Timestamp de atualização (`ad_sync_updated_at`)

#### **Validação dos Dados:**
- ✅ Frequência válida (daily/weekly/monthly)
- ✅ Formato de horário válido (HH:MM)
- ✅ Cálculo da próxima execução

#### **Próxima Execução:**
- **Diária**: Amanhã no horário configurado
- **Semanal**: Próximo domingo no horário configurado
- **Mensal**: Próximo mês no horário configurado

### 5. **Exemplo de Resposta da API** 📋

```json
{
  "success": true,
  "data": {
    "tests": {
      "frequency_exists": true,
      "time_exists": true,
      "enabled_exists": true,
      "updated_at_exists": true,
      "frequency_valid": true,
      "time_valid": true,
      "next_execution": "02/08/2025 às 15:30"
    },
    "config": {
      "frequency": "daily",
      "time": "15:30",
      "enabled": true,
      "updated_at": "2025-08-01T12:30:00.000000Z"
    }
  }
}
```

### 6. **Troubleshooting** 🔧

#### **Se as configurações não aparecem:**
1. Verifique se o cache está funcionando
2. Execute `php artisan cache:clear`
3. Tente salvar novamente

#### **Se o teste falha:**
1. Verifique os logs em `storage/logs/laravel.log`
2. Confirme se as rotas estão registradas
3. Teste via comando Artisan primeiro

#### **Se a próxima execução está incorreta:**
1. Verifique o fuso horário do servidor
2. Confirme se a data/hora estão corretas
3. Teste com diferentes frequências

### 7. **Monitoramento** 📊

Para monitorar se a sincronização automática está funcionando:

```bash
# Ver logs da sincronização
tail -f storage/logs/laravel.log | grep "AD"

# Verificar jobs agendados
php artisan schedule:list

# Executar scheduler manualmente (para teste)
php artisan schedule:run
```

### 8. **Importante** ⚠️

- As configurações são salvas no **cache** do Laravel
- Para que mudanças de frequência/horário tenham efeito na sincronização automática, será necessário implementar leitura dinâmica no scheduler
- Por enquanto, a sincronização automática continua executando **diariamente às 02:00h** conforme configurado no `Kernel.php`

### 9. **Próximos Passos** 🚀

Para implementar sincronização automática totalmente configurável:

1. Modificar o `Kernel.php` para ler configurações dinamicamente
2. Implementar sistema de notificação de mudanças
3. Adicionar logs detalhados de execução
4. Criar dashboard de monitoramento 