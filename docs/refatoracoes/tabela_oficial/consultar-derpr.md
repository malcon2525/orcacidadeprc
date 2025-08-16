# Refatoração – Consultar DER-PR

## 1. Contexto
Padronização da funcionalidade de consulta da tabela DER‑PR para o novo layout e boas práticas do projeto.

## 2. Objetivos
- Unificar layout (cards + modal fullscreen)
- Melhorar UX (filtros form‑floating, cabeçalho fixo)
- Garantir exportação fiel ao que é exibido
- Corrigir travamento ao fechar modal

## 3. Escopo e Mudanças
- Cards no componente principal com gradiente azul→verde
- Modal fullscreen com:
  - Header gradiente e ícone
  - Filtros `código` e `descrição` (form‑floating)
  - Tabela com cabeçalho fixo e hover
  - Paginação client‑side (25/50/100)
  - Exportação Excel com colunas: Código, Descrição, Unidade, Mão de Obra, Mat./Equip., Custo Total, Transporte
- Correção de fechamento do modal removendo `backdrop` residual e classe `modal-open`

## 4. Arquivos Alterados
- `resources/js/components/tabela_oficial/consultar_derpr/components/ConsultarDerpr.vue`
- `resources/js/components/tabela_oficial/consultar_derpr/components/ModalTabelaDados.vue`
- `resources/views/web/tabela_oficial/consultar_derpr/index.blade.php`

## 5. Rotas
- Sem alterações de caminho. Mantido padrão atual do projeto para DER‑PR.

## 6. Testes Manuais
- Abrir cada card e validar dados no modal
- Filtros por código e descrição
- Paginação com 25/50/100 itens
- Exportação Excel
- Fechar modal (X e ESC) sem travar a página

## 7. Resultados
- Layout padronizado e consistente
- Melhor responsividade e experiência
- Exportação alinhada às colunas visíveis
- Correção de travamentos

## 8. Pendências
- Nenhuma
