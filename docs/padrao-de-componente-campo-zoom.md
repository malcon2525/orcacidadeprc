# Padrão de Campo Zoom para Fornecedores

Este documento define o padrão de implementação do campo "zoom" para seleção/cadastro de fornecedores em CRUDs do sistema. Siga este padrão para garantir consistência visual, funcional e de código em todos os módulos.

---

## 1. Objetivo
Permitir ao usuário buscar, selecionar e cadastrar fornecedores de forma rápida e intuitiva, sem sair do fluxo principal do formulário.

---

## 2. Layout e UX
- O campo zoom deve ser apresentado como um input de texto com botão de ação (ícone de lupa ou reticências).
- Ao clicar no botão, abrir um **modal centralizado** com backdrop escuro e leve blur, destacando o modal do restante da tela.
- O modal deve conter:
  - Campo de busca (input) para filtrar fornecedores por nome fantasia ou CNPJ/CPF.
  - Tabela paginada com os resultados.
  - Botão para cadastrar novo fornecedor inline, exibindo um mini-formulário no próprio modal.
  - Botão de seleção para cada fornecedor listado.
- O modal deve ser responsivo e ter fundo cinza claro, sombra e bordas arredondadas (ver CSS global).

---

## 3. Estrutura de Código (Vue + Laravel)
### Frontend (Vue)
- O campo zoom deve ser um componente reutilizável ou um bloco bem isolado no formulário.
- O modal deve ser aberto via Bootstrap Modal JS ou equivalente.
- A busca deve ser feita via API, com debounce para evitar requisições excessivas.
- A tabela de resultados deve ser paginada (ex: 10 itens por página), com navegação de páginas.
- O cadastro inline deve validar campos obrigatórios e atualizar a lista imediatamente após salvar.
- Ao selecionar um fornecedor, os dados devem ser preenchidos automaticamente no formulário principal.
- O modal deve fechar ao selecionar ou cadastrar um fornecedor.

### Backend (Laravel)
- Endpoint GET `/api/fornecedores/buscar-select` para busca paginada, aceitando parâmetros `termo`, `page`, `per_page`.
- Endpoint POST `/api/fornecedores` para cadastro inline.
- Retornar sempre os dados essenciais: id, cnpj_cpf, nome_fantasia, telefone, email, site.

---

## 4. Paginação
- A busca deve ser paginada no backend e frontend.
- O modal deve exibir controles de navegação (próxima, anterior, página atual).
- O backend deve retornar `data`, `current_page`, `last_page`, `total`.

---

## 5. Integração e Reutilização
- O CSS do modal de zoom deve estar no arquivo global (`/resources/css/crud-styles.css`).
- O componente/modal deve ser facilmente adaptável para outros tipos de zoom (ex: produtos, clientes).
- O padrão de API e UX deve ser mantido para todos os campos zoom do sistema.

---

## 6. Dicas de UX
- Sempre destacar o modal com backdrop escuro e blur.
- Garantir feedback visual ao buscar, cadastrar e selecionar.
- Validar e exibir mensagens de erro amigáveis no cadastro inline.
- Focar automaticamente no campo de busca ao abrir o modal.

---

## 7. Como usar este padrão com a IA
- Sempre que precisar implementar um novo campo zoom, peça para a IA: **"Leia o arquivo doc/padrao-campo-zoom-fornecedor.md e siga o padrão descrito para implementar um campo zoom para [entidade]"**.
- Se precisar adaptar para outro tipo de entidade (ex: produtos), peça para a IA adaptar o padrão mantendo a estrutura e UX.
- Use este arquivo como referência para revisões de código, testes e validação de UX.

---

## 8. Exemplo de chamada para a IA
> "Por favor, implemente um campo zoom para produtos seguindo o padrão do arquivo docs/padrao-campo-zoom-fornecedor.md. Adapte o backend e frontend conforme necessário."

---

**Mantenha este arquivo atualizado sempre que houver melhorias no padrão!** 