# Padrão de Validação de Formulários

## **📋 VISÃO GERAL**

Este documento define o padrão padrão de validação de formulários utilizado em todo o sistema. O padrão segue o princípio de **"validação no backend, exibição no frontend"**, garantindo consistência, segurança e experiência do usuário.

---

## **🎯 PRINCÍPIOS FUNDAMENTAIS**

### **1. Validação Centralizada no Backend**
- ✅ **TODA validação** é feita no backend (Laravel)
- ❌ **NENHUMA validação** JavaScript no frontend
- 🔒 **Segurança garantida** pela validação server-side
- 📱 **Consistência** em todas as interfaces

### **2. Exibição Automática de Erros**
- 🎨 **Feedback visual automático** usando classes Bootstrap
- 🎯 **Foco automático** no primeiro campo inválido
- 📝 **Mensagens claras** e específicas por campo
- 🔄 **Atualização em tempo real** do estado de erro

---

## **🏗️ ARQUITETURA TÉCNICA**

### **Fluxo de Validação**
```
1. Usuário preenche formulário
2. Frontend envia dados para backend
3. Backend valida e retorna:
   - Status 200: Sucesso
   - Status 422: Erros de validação
4. Frontend captura resposta e:
   - Exibe erros automaticamente
   - Foca no primeiro campo inválido
   - Atualiza classes CSS
```

---

## **💻 IMPLEMENTAÇÃO NO FRONTEND (Vue.js)**

### **1. Estrutura do Formulário**

```vue
<template>
  <form @submit.prevent="salvarDados" class="needs-validation" novalidate>
    <div class="form-floating">
      <input type="text" 
             class="form-control" 
             :class="{ 'is-invalid': errors.nome }"
             id="nome" 
             v-model="form.nome" 
             required>
      <label for="nome">Nome</label>
      <div class="invalid-feedback" v-if="errors.nome">
        {{ Array.isArray(errors.nome) ? errors.nome[0] : errors.nome }}
      </div>
    </div>
  </form>
</template>
```

### **2. Objeto de Erros**

```javascript
data() {
  return {
    errors: {}, // Objeto vazio para armazenar erros
    form: {
      nome: '',
      email: ''
    }
  }
}
```

### **3. Método de Salvamento**

```javascript
async salvarDados() {
  this.loading = true;
  this.errors = {}; // Limpa erros anteriores

  try {
    const response = await fetch('/api/endpoint', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      },
      body: JSON.stringify(this.form)
    });

    const data = await response.json();

    if (!response.ok) {
      if (response.status === 422 && data.errors) {
        // Erro de validação - aplicar padrão
        this.errors = data.errors;
        
        // Focar no primeiro campo inválido
        this.$nextTick(() => {
          this.focarPrimeiroCampoInvalido();
        });
        
        throw new Error('Por favor, corrija os erros no formulário');
      }
      throw new Error(data.message || 'Erro ao salvar dados');
    }

    // Sucesso - limpar formulário e fechar modal
    this.limparFormulario();
    this.mostrarToast('Sucesso', 'Dados salvos com sucesso!', 'fa-check-circle text-success');
    
  } catch (error) {
    console.error('Erro ao salvar:', error);
    this.mostrarToast('Erro', error.message, 'fa-exclamation-circle text-danger');
  } finally {
    this.loading = false;
  }
}
```

### **4. Foco Automático no Primeiro Campo Inválido**

```javascript
focarPrimeiroCampoInvalido() {
  // Ordem de prioridade para foco
  const campos = ['nome', 'email', 'telefone', 'endereco'];
  
  for (const campo of campos) {
    if (this.errors[campo]) {
      document.getElementById(campo)?.focus();
      break;
    }
  }
}
```

---

## **🔧 IMPLEMENTAÇÃO NO BACKEND (Laravel)**

### **1. Controller com Validação**

```php
public function store(Request $request)
{
    // Validação com regras específicas
    $validator = Validator::make($request->all(), [
        'nome' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'telefone' => 'required|string|max:20',
        'endereco' => 'required|string|max:500'
    ], [
        // Mensagens personalizadas (opcional)
        'nome.required' => 'O nome é obrigatório',
        'email.unique' => 'Este email já está em uso'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Erro de validação',
            'errors' => $validator->errors()
        ], 422);
    }

    // Dados válidos - prosseguir com criação
    $user = User::create($request->validated());
    
    return response()->json([
        'message' => 'Usuário criado com sucesso',
        'data' => $user
    ], 201);
}
```

### **2. Model com Regras de Validação (Alternativo)**

```php
class User extends Model
{
    protected $fillable = ['nome', 'email', 'telefone', 'endereco'];

    // Regras de validação centralizadas no model
    public static $rules = [
        'nome' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'telefone' => 'required|string|max:20',
        'endereco' => 'required|string|max:500'
    ];

    public static $messages = [
        'nome.required' => 'O nome é obrigatório',
        'email.unique' => 'Este email já está em uso'
    ];
}
```

---

## **🎨 CLASSES CSS UTILIZADAS**

### **Bootstrap Classes**
- `is-invalid`: Aplicada aos campos com erro
- `invalid-feedback`: Container para mensagens de erro
- `needs-validation`: Aplicada ao formulário
- `novalidate`: Desabilita validação nativa do HTML5

### **Exemplo de Aplicação**
```vue
<input type="text" 
       class="form-control" 
       :class="{ 'is-invalid': errors.nome }"
       id="nome" 
       v-model="form.nome">
```

---

## **📱 COMPONENTES REUTILIZÁVEIS**

### **1. Toast de Notificação**
```javascript
mostrarToast(title, message, icon) {
  this.toastTitle = title;
  this.toastMessage = message;
  this.toastIcon = icon;
  this.toast.show();
}
```

### **2. Limpeza de Formulário**
```javascript
limparFormulario() {
  this.form = {
    nome: '',
    email: '',
    telefone: '',
    endereco: ''
  };
  this.errors = {}; // IMPORTANTE: limpar erros também
}
```

---

## **✅ CHECKLIST DE IMPLEMENTAÇÃO**

### **Frontend (Vue.js)**
- [ ] Objeto `errors` vazio no `data()`
- [ ] Classes `:class="{ 'is-invalid': errors.campo }"` nos inputs
- [ ] `invalid-feedback` com `v-if="errors.campo"`
- [ ] Tratamento de erro 422 no método de salvamento
- [ ] Atribuição `this.errors = data.errors`
- [ ] Foco automático no primeiro campo inválido
- [ ] Limpeza de erros ao abrir modal/novo formulário
- [ ] Toast de notificação para feedback

### **Backend (Laravel)**
- [ ] Validação com `Validator::make()`
- [ ] Retorno de erro 422 com `errors`
- [ ] Mensagens de erro personalizadas (opcional)
- [ ] Tratamento de exceções
- [ ] Log de operações (se aplicável)

---

## **🚨 CASOS ESPECIAIS**

### **1. Validação Condicional**
```php
$rules = [
    'email' => 'required|email',
    'telefone' => 'required_if:email,null|string'
];
```

### **2. Validação de Arrays**
```php
$rules = [
    'items.*.nome' => 'required|string',
    'items.*.quantidade' => 'required|integer|min:1'
];
```

### **3. Validação de Arquivos**
```php
$rules = [
    'arquivo' => 'required|file|mimes:pdf,doc,docx|max:2048'
];
```

---

## **🔍 DEBUGGING E TESTES**

### **1. Verificar Resposta do Backend**
```javascript
console.log('Status:', response.status);
console.log('Dados:', data);
console.log('Erros:', data.errors);
```

### **2. Verificar Estado do Frontend**
```javascript
console.log('Errors object:', this.errors);
console.log('Form data:', this.form);
```

### **3. Testes Recomendados**
- [ ] Formulário vazio (deve mostrar erros)
- [ ] Dados inválidos (email mal formatado, etc.)
- [ ] Dados duplicados (se aplicável)
- [ ] Campos obrigatórios vazios
- [ ] Limite de caracteres
- [ ] Tipos de dados incorretos

---

## **📚 EXEMPLOS PRÁTICOS**

### **Exemplo 1: Usuários**
- **Arquivo**: `ListaUsuarios.vue`
- **Endpoint**: `/api/administracao/usuarios`
- **Campos**: nome, email, senha, papel

### **Exemplo 2: Municípios**
- **Arquivo**: `ListaMunicipios.vue`
- **Endpoint**: `/api/administracao/municipios`
- **Campos**: nome, prefeito, email, código IBGE

### **Exemplo 3: Entidades Orçamentárias**
- **Arquivo**: `ListaEntidadesOrcamentarias.vue`
- **Endpoint**: `/api/administracao/entidades-orcamentarias`
- **Campos**: nome, tipo, município

---

## **⚠️ PONTOS DE ATENÇÃO**

### **1. Segurança**
- ✅ **SEMPRE** validar no backend
- ❌ **NUNCA** confiar apenas na validação frontend
- 🔒 **SEMPRE** usar CSRF token
- 🛡️ **SEMPRE** sanitizar dados de entrada

### **2. Performance**
- 🚀 **Evitar** validações desnecessárias
- 📊 **Usar** regras de validação eficientes
- 🔄 **Cachear** validações complexas quando possível

### **3. UX (Experiência do Usuário)**
- 🎯 **Focar** automaticamente no primeiro erro
- 📝 **Mensagens** claras e específicas
- 🎨 **Feedback visual** imediato
- 🔄 **Estado** consistente entre operações

---

## **🔄 MANUTENÇÃO E EVOLUÇÃO**

### **1. Atualizações de Regras**
- 📝 **Documentar** mudanças nas regras de validação
- 🧪 **Testar** todas as validações após mudanças
- 📱 **Verificar** comportamento no frontend

### **2. Novos Campos**
- ➕ **Adicionar** validação no backend
- 🎨 **Atualizar** frontend com novo campo
- 🔍 **Testar** validação do novo campo

### **3. Refatoração**
- 🏗️ **Manter** padrão consistente
- 📚 **Reutilizar** componentes existentes
- 🧹 **Limpar** código obsoleto

---

## **📖 REFERÊNCIAS**

- [Laravel Validation](https://laravel.com/docs/validation)
- [Bootstrap Form Validation](https://getbootstrap.com/docs/5.3/forms/validation/)
- [Vue.js Form Handling](https://vuejs.org/guide/essentials/forms.html)
- [Padrão CRUD do Sistema](docs/diretrizes/02_desenvolvimento/01_padrao_crud.md)

---

## **🎯 CONCLUSÃO**

Este padrão garante:
- ✅ **Consistência** em todo o sistema
- 🔒 **Segurança** através de validação server-side
- 🎨 **UX otimizada** com feedback visual automático
- 🚀 **Manutenibilidade** com código padronizado
- 📱 **Responsividade** em todas as interfaces

**SEMPRE** siga este padrão ao implementar novos formulários no sistema!
