# Refatoração – Consultar SINAPI

## 1. Contexto
A funcionalidade de consulta do SINAPI utilizava código e layout antigos, inclusive realizando cálculos no backend via JOINs. Foi padronizada para seguir o layout da consulta DER‑PR e consultar apenas a view consolidada `sinapi_composicoes_view`.

## 2. Objetivos
- Unificar layout com DER‑PR (cards + modal fullscreen)
- Eliminar cálculos no backend
- Garantir colunas corretas (mão de obra, mat./equip., total, desoneração)
- Corrigir problemas de modal e registro de componente

## 3. Escopo e Mudanças
- Novas rotas Web/API para namespace `tabela_oficial/consultar_sinapi`
- Novo Controller Web (apenas `index`) e API (tabelas, dados, exportação, zoom)
- Novo Blade com container padrão
- Novo componente `ConsultarSinapi.vue` (cards + filtros + abertura de modal)
- Novo `ModalTabelaDados.vue` (fullscreen, form‑floating, cabeçalho fixo, paginação, exportação)
- Import do componente ajustado em `resources/js/app.js`
- Remoção dos artefatos legados (controller antigo, componente antigo, blade antigo)
- Ajuste de ID do modal para evitar conflito (`modal-tabela-dados-sinapi`)
- Correção do fechamento do modal (remoção de `backdrop`/`modal-open`)

## 4. Fonte de Dados
- Uso exclusivo da view `sinapi_composicoes_view`.
- Parâmetro `tabela` no formato `YYYY-MM-DD|com|sem` para buscar os dados.

## 5. Arquivos Alterados/Criados
- `app/Http/Controllers/Web/TabelaOficial/ConsultarSinapiController.php` (novo)
- `app/Http/Controllers/Api/TabelaOficial/ConsultarSinapiController.php` (novo)
- `resources/views/tabela_oficial/consultar_sinapi/index.blade.php` (novo)
- `resources/js/components/tabela_oficial/consultar_sinapi/ConsultarSinapi.vue` (novo)
- `resources/js/components/tabela_oficial/consultar_sinapi/components/ModalTabelaDados.vue` (novo)
- `routes/web.php` (rotas Web)
- `routes/api.php` (rotas API)
- `resources/js/app.js` (registro do componente)

## 6. Testes Manuais
- Renderização dos cards (duas opções: com/sem desoneração)
- Abertura do modal ao clicar no card
- Carregamento e exibição das colunas corretas
- Filtros `grupo`, `codigo`, `descricao`
- Paginação client‑side (10/25/50/100/150)
- Exportação Excel
- Fechamento sem travar a página

## 7. Resultados
- Layout idêntico ao DER‑PR
- Backend simplificado (consulta direta à view)
- Correções de UX (modal, IDs, imports)
- Documentação técnica criada: `docs/técnico/tabela_oficial/consultar-sinapi.md`

## 8. Pendências
- Nenhuma
