# Padrão de Relatório de Atividades — Módulo

| Item                        | Descrição/Quantidade                                                                                 |
|-----------------------------|-----------------------------------------------------------------------------------------------------|
| **Nome do Módulo**          | Municípios                                                                                          |
| **Objetivo**                | Permitir cadastro, consulta, edição, exclusão e importação em massa de municípios, com interface Vue, validação robusta e integração com fonte externa. |
| **Rotas Web**               | 6 rotas principais:<br>- `/municipios` (index)<br>- `/municipios/create` (criação)<br>- `/municipios` (POST, store)<br>- `/municipios/{id}/edit` (edição)<br>- `/municipios/{id}` (PUT, update)<br>- `/municipios/{id}` (DELETE, destroy) |
| **Rotas API**               | 5 rotas principais:<br>- `/api/municipios/` (listar)<br>- `/api/municipios/` (POST, criar)<br>- `/api/municipios/{id}` (PUT, atualizar)<br>- `/api/municipios/{id}` (DELETE, remover)<br>- `/api/municipios/importar` (importar em massa) |
| **Componentes Vue**         | 1 principal:<br>- `ListaMunicipios.vue`                                                             |
| **Views Blade**             | 1 principal:<br>- `municipios/index.blade.php`                                                      |
| **Controllers**             | 1 principal:<br>- `MunicipioController.php`                                                         |
| **Models**                  | 1 principal:<br>- `Municipio.php`                                                                   |
| **Tabelas no Banco (migrations)** | 1 principal:<br>- `municipios` (migration: 06A_create_municipios_table.php)                      |
| **Complexidade**            | Baixa/Moderada — CRUD padrão, importação em massa, interface SPA, validação robusta, sem integrações externas complexas. |
| **Pontos de Função (PF)**   | 14 PF (ver detalhamento abaixo)                                                                    |

---

## Detalhamento dos Pontos de Função (PF)

| Tipo de Função                        | Quantidade | PF por função | Total PF | Justificativa |
|---------------------------------------|------------|---------------|----------|---------------|
| Entradas Externas (EE)                | 2          | 3             | 6        | Cadastro/edição de município, importação em massa |
| Saídas Externas (SE)                  | 2          | 4             | 8        | Listagem paginada (JSON), resposta de importação |
| Consultas Externas (CE)               | 0          | 0             | 0        | —             |
| Arquivos Lógicos Internos (ALI)       | 1          | 0.5           | 0.5      | Tabela municipios |
| Arquivos de Interface Externa (AIE)   | 0          | 0             | 0        | —             |
| **Total**                             | —          | —             | **14.5** |               |

### Critérios e Observações:
- **EE:** Cadastro/edição de município (formulário), importação em massa (API).
- **SE:** Listagem paginada (JSON para Vue), resposta de importação (JSON).
- **ALI:** Tabela principal do módulo.
- **AIE:** Não há integração com arquivos externos.

---

## Justificativa da Complexidade e Custo

- **Complexidade:**  
  - Classificada como Baixa/Moderada. O módulo é um CRUD padrão com importação em massa, interface SPA e validação robusta, mas sem integrações externas complexas.
  - Fatores: uso de Vue.js, API RESTful, importação em massa, mas sem workflows assíncronos ou múltiplos relacionamentos.

- **Cálculo de PF:**  
  - O valor de 14,5 PF está dentro do padrão para módulos CRUD com importação e interface SPA.
  - Módulos CRUD simples geralmente ficam entre 10-15 PF; módulos com integrações externas ou regras complexas podem passar de 20 PF.

- **Conversão para horas/custo:**  
  - Exemplo: 6 horas/PF → 14,5 PF × 6 = 87 horas.
  - Exemplo: R$ 100/PF → 14,5 PF × R$ 100 = R$ 1.450,00.
  - Ajuste conforme produtividade e custo/hora da equipe.

- **Resumo:**  
  - O valor está adequado ao escopo e robustez do módulo. Não há indícios de superdimensionamento ou subdimensionamento.
  - Recomenda-se revisão periódica caso o módulo evolua para incluir integrações externas, relatórios avançados ou regras de negócio adicionais.

--- 