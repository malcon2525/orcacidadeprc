## 7. Registrar o Componente Vue
No arquivo `resources/js/app.js`, adicione o registro do componente:

```javascript
// Importar o componente
import ListaTiposOrcamentos from './components/orcamento/tipos-orcamentos/ListaTiposOrcamentos.vue';

// Registrar o componente
app.component('lista-tipos-orcamentos', ListaTiposOrcamentos);
```

O nome do componente deve seguir o padrão kebab-case (ex: `lista-tipos-orcamentos`) e corresponder à tag usada na view Blade. 