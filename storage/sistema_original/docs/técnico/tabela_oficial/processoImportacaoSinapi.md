# Processo de Importação SINAPI - Documentação Técnica

## Status do Projeto

- **Primeira Aba (Composições e Insumos)**: ✅ COMPLETAMENTE FUNCIONAL E DOCUMENTADA
- **Segunda Aba (Percentagens de Mão de Obra)**: ✅ COMPLETAMENTE FUNCIONAL E DOCUMENTADA  
- **Terceira Aba (Gravar no Banco)**: ✅ COMPLETAMENTE FUNCIONAL E DOCUMENTADA

## Arquitetura e Componentes

### 1. Controller Principal
**Arquivo**: `app/Http/Controllers/Api/TabelaOficial/ImportarSinapiController.php`

**Objetivo**: Gerenciar todo o processo de importação SINAPI, incluindo processamento de arquivos, gravação no banco de dados e geração de relatórios.

**Funcionalidades Principais**:
- Processamento de arquivos Excel (Aba 1 e 2)
- Gerenciamento de diretórios temporários
- Gravação no banco de dados com transações
- Logging detalhado para debugging
- Validação de arquivos e dados
- Atualização de views materializadas

**Melhorias Implementadas**:
- ✅ Logging granular e estruturado para debugging
- ✅ Contagem correta de registros criados vs atualizados
- ✅ Tratamento robusto de erros com fallbacks
- ✅ Uso de datas do lote em vez de datas atuais
- ✅ Validação de estrutura de arquivos
- ✅ Transações de banco de dados para atomicidade

### 2. Componentes Vue.js

#### 2.1 ComposicoesInsumos.vue
**Localização**: `resources/js/components/tabela_oficial/importar_sinapi/components/ComposicoesInsumos.vue`

**Objetivo**: Interface para processamento de composições e insumos SINAPI (Aba 1)

**Funcionalidades**:
- Upload de arquivo Excel com 5 abas
- Processamento via Python script
- Download de arquivos processados
- Interface de progresso visual
- Layout padronizado com DER-PR

#### 2.2 PercentagensMaoDeObra.vue
**Localização**: `resources/js/components/tabela_oficial/importar_sinapi/components/PercentagensMaoDeObra.vue`

**Objetivo**: Interface para processamento de percentagens de mão de obra (Aba 2)

**Funcionalidades**:
- Upload de arquivo Excel com 2 abas (SEM/COM Desoneração)
- Processamento via Python script
- Download de arquivos processados
- Interface de progresso visual
- Layout padronizado com DER-PR

#### 2.3 GravarSinapi.vue
**Localização**: `resources/js/components/tabela_oficial/importar_sinapi/components/GravarSinapi.vue`

**Objetivo**: Interface para gravação de dados no banco (Aba 3)

**Funcionalidades**:
- Detecção automática de arquivos processados
- Validação de arquivos necessários
- Gravação no banco com feedback visual
- Interface de progresso detalhado
- Layout padronizado com DER-PR

### 3. Scripts Python

#### 3.1 01.Importar-SINAPI-Tabela-Servicos.py
**Localização**: `01_python/importacao_SINAPI/01.Importar-SINAPI-Tabela-Servicos.py`

**Objetivo**: Processar arquivo de composições e insumos SINAPI

**Funcionalidades**:
- Leitura de 5 abas do Excel
- Processamento de dados sintéticos e analíticos
- Geração de 5 arquivos temporários
- Criação de metadados JSON

#### 3.2 Processamento de Mão de Obra (Aba 2)

**Localização**: `app/Http/Controllers/Api/TabelaOficial/ImportarSinapiController.php` - método `processarPercentagensMaoDeObra`

**Objetivo**: Processar arquivo Excel de percentagens de mão de obra SINAPI diretamente no PHP (sem script Python)

**Funcionalidade**:
- Recebe arquivo Excel com 2 abas (SEM Desoneração, COM Desoneração)
- Processa diretamente no PHP usando PhpSpreadsheet
- Extrai dados das colunas: Código, Descrição, Unidade, PR (coluna V)
- Gera 2 arquivos processados no diretório compartilhado da Aba 1
- Cria arquivo de metadados específico para mão de obra

**Arquivos Gerados**:
- `sinapi_mao_obra_SEM_DESONERACAO.xlsx`
- `sinapi_mao_obra_COM_DESONERACAO.xlsx`
- `sinapi_mao_obra_metadata.json`

**Estrutura dos Arquivos Processados**:
```
codigo_composicao | descricao | unidade | percentagem_pr | data_emissao | data_base | desoneracao
```

**Observações**:
- Processamento feito diretamente no PHP (não usa script Python)
- Usa o mesmo diretório de processamento da Aba 1
- Extrai datas das células B2 (mês referência) e B4 (data emissão)
- Converte percentagens para formato decimal (0-1)

### 4. Estrutura de Dados

#### 4.1 Arquivos de Entrada (Aba 1)
- **Composições e Insumos**: Arquivo Excel com 5 abas
  - Composições Sintéticas (COM Desoneração)
  - Composições Sintéticas (SEM Desoneração)
  - Insumos (COM Desoneração)
  - Insumos (SEM Desoneração)
  - Analítico

#### 4.2 Arquivos de Entrada (Aba 2)
- **Mão de Obra**: Arquivo Excel com 2 abas
  - SEM Desoneração
  - COM Desoneração

#### 4.3 Arquivos de Saída Processados
- `sinapi_processado_CCD.xlsx` (Composições COM Desoneração)
- `sinapi_processado_CSD.xlsx` (Composições SEM Desoneração)
- `sinapi_processado_ICD.xlsx` (Insumos COM Desoneração)
- `sinapi_processado_ISD.xlsx` (Insumos SEM Desoneração)
- `sinapi_processado_Analítico.xlsx` (Dados Analíticos)
- `sinapi_mao_obra_SEM_DESONERACAO.xlsx` (Mão de Obra SEM Desoneração)
- `sinapi_mao_obra_COM_DESONERACAO.xlsx` (Mão de Obra COM Desoneração)

#### 4.4 Metadados (sinapi_metadata.json)
```json
{
  "mes_referencia": "Janeiro/2024",
  "data_emissao": "2024-01-15",
  "abas_processadas": ["SEM Desoneração", "COM Desoneração"],
  "total_registros": 1500,
  "registros_por_tipo": {
    "sem_desoneracao": 750,
    "com_desoneracao": 750
  },
  "data_processamento": "2024-01-15T10:30:00"
}
```

### 5. Fluxo Detalhado

#### 5.1 Primeira Aba - Composições e Insumos
1. **Upload**: Usuário seleciona arquivo Excel com 5 abas
2. **Validação**: Sistema valida extensão e tamanho do arquivo
3. **Processamento**: Python script processa cada aba
4. **Geração**: Cria 5 arquivos temporários + metadados
5. **Download**: Usuário pode baixar arquivos processados
6. **Diretório**: Arquivos salvos em `processado_sinapi_[hash]`

#### 5.2 Segunda Aba - Percentagens de Mão de Obra
1. **Upload**: Usuário seleciona arquivo Excel com 2 abas
2. **Validação**: Sistema valida extensão e tamanho do arquivo
3. **Processamento**: Python script processa cada aba
4. **Geração**: Cria 2 arquivos temporários + metadados
5. **Download**: Usuário pode baixar arquivos processados
6. **Diretório**: Arquivos salvos no mesmo diretório da Aba 1

#### 5.3 Terceira Aba - Gravar no Banco
1. **Detecção**: Sistema detecta automaticamente arquivos processados
2. **Validação**: Verifica existência dos 7 arquivos necessários
3. **Gravação**: Processa e grava dados no banco de dados
4. **Feedback**: Exibe estatísticas detalhadas do processamento
5. **View**: Atualiza view materializada para consultas

### 6. Melhorias e Refatorações Implementadas

#### 6.1 Logging e Debugging
- ✅ Logging estruturado em todos os métodos de gravação
- ✅ Detalhamento de arquivos encontrados e processados
- ✅ Logs de erro com contexto completo
- ✅ Estatísticas de processamento por arquivo
- ✅ Rastreamento de linhas com erro

#### 6.2 Contagem de Registros
- ✅ Separação entre registros criados e atualizados
- ✅ Contagem correta usando `updateOrCreate` vs `create`/`update`
- ✅ Estatísticas detalhadas por tipo de dado
- ✅ Total de registros processados vs salvos

#### 6.3 Tratamento de Erros
- ✅ Fallback para datas quando arquivo de referência não encontrado
- ✅ Validação robusta de estrutura de arquivos
- ✅ Tratamento de linhas vazias ou malformadas
- ✅ Rollback de transações em caso de erro

#### 6.4 Gerenciamento de Diretórios
- ✅ Limpeza automática de diretórios anteriores
- ✅ Uso de diretório único para todo o processo
- ✅ Persistência do nome do diretório na sessão
- ✅ Fallback para diretório mais recente

### 7. Integração com o Sistema

#### 7.1 Rotas API
```php
// Aba 1 - Composições e Insumos
POST /api/tabela_oficial/importar_sinapi/composicoes_insumos
GET /api/tabela_oficial/importar_sinapi/download_arquivo_processado/{tipo}

// Aba 2 - Mão de Obra
POST /api/tabela_oficial/importar_sinapi/percentagens_mao_de_obra
GET /api/tabela_oficial/importar_sinapi/download_arquivo_processado_mao_obra/{tipo}

// Aba 3 - Gravar no Banco
GET /api/tabela_oficial/importar_sinapi/verificar_arquivos
POST /api/tabela_oficial/importar_sinapi/gravar
```

#### 7.2 Modelos de Dados
- `SinapiMaoDeObra`: Tabela `sinapi_mao_de_obra`
- `SinapiComposicao`: Tabela `sinapi_composicoes`
- `SinapiInsumo`: Tabela `sinapi_insumos`
- `SinapiComposicaoAnalitico`: Tabela `sinapi_composicoes_analitico`
- `SinapiItemAnalitico`: Tabela `sinapi_itens_analitico`
- `SinapiComposicaoView`: View materializada `sinapi_composicoes_view`

#### 7.3 Status dos Componentes
- ✅ `ComposicoesInsumos.vue`: Funcional e testado
- ✅ `PercentagensMaoDeObra.vue`: Funcional e testado
- ✅ `GravarSinapi.vue`: Funcional e testado
- ✅ `ImportarSinapiController.php`: Funcional com logging melhorado

### 8. Próximos Passos

#### 8.1 Melhorias Futuras
- [ ] Implementar cache para consultas frequentes
- [ ] Adicionar validação de integridade de dados
- [ ] Implementar sistema de backup automático
- [ ] Adicionar relatórios de auditoria

#### 8.2 Otimizações
- [ ] Processamento em lotes para arquivos grandes
- [ ] Índices de banco de dados otimizados
- [ ] Compressão de arquivos temporários
- [ ] Limpeza automática de arquivos antigos

### 9. Troubleshooting

#### 9.1 Problemas Comuns
1. **Arquivos não encontrados**: Verificar se Abas 1 e 2 foram processadas
2. **Erro de datas**: Verificar formato das datas nos arquivos Excel
3. **Erro de permissão**: Verificar permissões do diretório `storage/app/temp`
4. **Erro de memória**: Verificar tamanho dos arquivos de entrada

#### 9.2 Logs Importantes
- `storage/logs/laravel.log`: Logs de aplicação Laravel
- Logs específicos de gravação com detalhes de cada etapa
- Logs de erro com stack trace completo

#### 9.3 Comandos Úteis
```bash
# Limpar cache
php artisan cache:clear

# Limpar logs
php artisan log:clear

# Verificar permissões
chmod -R 755 storage/app/temp

# Verificar espaço em disco
df -h storage/app/temp
```

---

**Última Atualização**: Janeiro 2024
**Versão**: 2.0 (Com logging melhorado e correções de gravação)
**Responsável**: Equipe de Desenvolvimento OrçaCidade 