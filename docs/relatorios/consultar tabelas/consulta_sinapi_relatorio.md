# Padrão de Relatório de Atividades — Módulo

| Item                        | Descrição/Quantidade                                                                                 |
|-----------------------------|-----------------------------------------------------------------------------------------------------|
| **Nome do Módulo**          | Consulta de Tabela SINAPI                                                                           |
| **Objetivo**                | Permitir consulta, filtro, paginação e exportação das composições de preços oficiais do SINAPI, segmentadas por data-base e desoneração. Atende usuários finais e desenvolvedores para análise e integração de dados. |
| **Rotas Web**               | 4 rotas principais:<br>- `/preco/consultar-sinapi/` (view)<br>- `/preco/consultar-sinapi/tabelas` (listar tabelas)<br>- `/preco/consultar-sinapi/dados` (dados filtrados)<br>- `/preco/consultar-sinapi/exportar-excel` (exportação Excel) |
| **Rotas API**               | Não possui rotas API RESTful (todas são web, retornando JSON ou arquivos)                          |
| **Componentes Vue**         | 1 principal:<br>- `ConsultarSINAPIComponent.vue`                                                   |
| **Views Blade**             | 1 principal:<br>- `web/preco/consultas/sinapi/index.blade.php`                                     |
| **Controllers**             | 1 principal:<br>- `ConsultarSINAPIController.php`                                                  |
| **Models**                  | Utiliza Eloquent/DB para:<br>- `sinapi_composicoes`<br>- `sinapi_mao_de_obra` (não há model dedicado, uso direto via DB) |
| **Tabelas no Banco (migrations)** | 2 principais:<br>- `sinapi_composicoes`<br>- `sinapi_mao_de_obra`<br>+ views auxiliares (ex: `sinapi_composicoes_view`) |
| **Complexidade**            | Moderada — Justificativa: fluxo CRUD de consulta, filtros dinâmicos, exportação, cálculos SQL, integração Vue+Laravel, mas sem integrações externas ou regras de negócio altamente complexas. |
| **Pontos de Função (PF)**   | 16 PF (ver detalhamento abaixo)                                                                    |

---

## Detalhamento dos Pontos de Função (PF)

| Tipo de Função                        | Quantidade | PF por função | Total PF | Justificativa |
|---------------------------------------|------------|---------------|----------|---------------|
| Entradas Externas (EE)                | 1          | 3             | 3        | Seleção de tabela/filtros pelo usuário |
| Saídas Externas (SE)                  | 2          | 4             | 8        | `/dados` (JSON processado), `/exportar-excel` (Excel) |
| Consultas Externas (CE)               | 1          | 4             | 4        | Consulta de tabelas disponíveis (distinct) |
| Arquivos Lógicos Internos (ALI)       | 2          | 0.5           | 1        | Tabelas sinapi_composicoes e sinapi_mao_de_obra |
| Arquivos de Interface Externa (AIE)   | 0          | 0             | 0        | —             |
| **Total**                             | —          | —             | **16**   |               |

### Critérios e Observações:
- **EE:** Seleção de tabela e filtros pelo usuário.
- **SE:** Exportação para Excel, resposta JSON de dados processados.
- **CE:** Consulta de tabelas disponíveis (distinct, sem processamento).
- **ALI:** Tabelas principais do módulo.
- **AIE:** Não há integração com arquivos externos.

---

## Justificativa da Complexidade e Custo

- **Complexidade:**  
  - Classificada como Moderada. O módulo envolve lógica de consulta, filtros dinâmicos, cálculos SQL, exportação de dados e integração Vue+Laravel, mas não possui integrações externas, regras de negócio altamente complexas ou múltiplos relacionamentos.
  - Fatores: interface amigável, logs de debug, exportação, mas sem workflows assíncronos ou integrações externas.

- **Cálculo de PF:**  
  - O valor de 16 PF está dentro do padrão para módulos de consulta com exportação e filtros dinâmicos.
  - Módulos CRUD simples geralmente ficam entre 10-15 PF; módulos com integrações externas ou regras complexas podem passar de 25 PF.

- **Conversão para horas/custo:**  
  - Exemplo: 6 horas/PF → 16 PF × 6 = 96 horas.
  - Exemplo: R$ 100/PF → 16 PF × R$ 100 = R$ 1.600,00.
  - Ajuste conforme produtividade e custo/hora da equipe.

- **Resumo:**  
  - O valor está adequado ao escopo e robustez do módulo. Não há indícios de superdimensionamento ou subdimensionamento.
  - Recomenda-se revisão periódica caso o módulo evolua para incluir integrações externas, relatórios avançados ou regras de negócio adicionais.

--- 