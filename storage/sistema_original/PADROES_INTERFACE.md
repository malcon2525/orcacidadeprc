# 🎯 PADRÕES OFICIAIS DE INTERFACE - ORÇACIDADE

## ⚠️ **REGRA ABSOLUTA: NUNCA QUEBRE OS CAMPOS FORM-FLOATING!**

### 📋 **CAMPO FORM-FLOATING - PADRÃO OFICIAL**

#### **✅ IMPLEMENTAÇÃO CORRETA (SEMPRE USAR ASSIM):**

```html
<div class="form-floating">
    <input type="text" 
           class="form-control" 
           id="campoId" 
           v-model="campoModel"
           placeholder="Placeholder do campo">
    <label for="campoId">Label do Campo</label>
</div>
```

#### **🎨 CSS OBRIGATÓRIO (SEMPRE INCLUIR):**

```css
/* ===== PADRÃO OFICIAL - FORM-FLOATING ===== */
/* ⚠️ NUNCA ALTERAR ESTAS REGRAS - ELAS GARANTEM FUNCIONAMENTO PERFEITO */
.form-floating .form-control {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
    line-height: 1.5 !important;
    min-height: 58px !important;
}

/* Alinhamento dos campos em linha */
.row.g-3 {
    align-items: end;
}
```

---

## 🚫 **O QUE NUNCA FAZER:**

1. **❌ NUNCA** sobrescrever as regras do `.form-floating .form-control`
2. **❌ NUNCA** usar `!important` em outras regras que possam conflitar
3. **❌ NUNCA** alterar `padding-top`, `padding-bottom`, `line-height` ou `min-height`
4. **❌ NUNCA** criar regras CSS que possam afetar estes campos

---

## ✅ **RESULTADO GARANTIDO:**

- **Label flutuante** funcionando perfeitamente
- **Texto digitado** 100% visível (sem cortes)
- **Espaçamento interno** otimizado
- **Altura do campo** consistente
- **Sem conflitos** de CSS
- **Responsivo** em todos os dispositivos

---

## 🔧 **IMPLEMENTAÇÃO EM NOVOS COMPONENTES:**

1. **SEMPRE** incluir o CSS padrão
2. **SEMPRE** usar a estrutura HTML exata
3. **SEMPRE** testar se o texto não está sendo cortado
4. **SEMPRE** verificar se o label está flutuando corretamente

---

## 📱 **RESPONSIVIDADE:**

O padrão já inclui responsividade automática. **NÃO** adicionar regras extras para mobile.

---

## 🎯 **EXEMPLO COMPLETO DE USO:**

```vue
<template>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" 
                       class="form-control" 
                       id="nome" 
                       v-model="form.nome"
                       placeholder="Nome completo">
                <label for="nome">Nome Completo</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="email" 
                       class="form-control" 
                       id="email" 
                       v-model="form.email"
                       placeholder="Email">
                <label for="email">Email</label>
            </div>
        </div>
    </div>
</template>

<style>
/* ⚠️ SEMPRE INCLUIR ESTE CSS PADRÃO */
.form-floating .form-control {
    padding-top: 1.625rem !important;
    padding-bottom: 0.625rem !important;
    line-height: 1.5 !important;
    min-height: 58px !important;
}

.row.g-3 {
    align-items: end;
}
</style>
```

---

## 🚨 **EM CASO DE PROBLEMAS:**

1. **Verificar** se o CSS padrão está incluído
2. **Confirmar** se a estrutura HTML está exata
3. **Testar** se não há CSS conflitante
4. **Usar** as regras `!important` para garantir prioridade

---

## 📝 **CHECKLIST DE IMPLEMENTAÇÃO:**

- [ ] CSS padrão incluído
- [ ] Estrutura HTML correta
- [ ] Label flutuando corretamente
- [ ] Texto 100% visível
- [ ] Sem cortes na parte inferior
- [ ] Responsivo funcionando
- [ ] Sem conflitos de CSS

---

## 🎉 **RESULTADO FINAL:**

**CAMPOS PERFEITOS, SEM CONFLITOS, SEM CONVERSAS INFINDÁVEIS!**

---

*Última atualização: [DATA] - Padrão testado e aprovado*
*Desenvolvedor responsável: [NOME]*
*Status: ✅ APROVADO PARA PRODUÇÃO*
