<template>
    <input 
        type="text" 
        :value="displayValue" 
        @input="onInput"
        @blur="onBlur"
        @focus="onFocus"

        class="form-control"
    >
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                isFocused: false // Para controlar o estado de foco
            };
        },
        computed: {
            displayValue() {
                if (this.isFocused) {
                    let valor = this.modelValue.replace(/\./g, ',');
                    return valor;
                }
                return this.formatCurrency(this.modelValue);
            }
        },
      
        methods: {
            onInput(event) {
                let valor = event.target.value;

                // Substitui pontos por vírgulas
                valor = valor.replace(/\./g, ',');

                // Verifica se há uma vírgula
                const posVirgula = valor.indexOf(',');

                if (posVirgula !== -1) {
                    // Limita a 10 dígitos antes da vírgula
                    let antesVirgula = valor.slice(0, posVirgula).replace(/\D/g, '').slice(0, 10);

                    // Limita a 2 dígitos após a vírgula
                    let depoisVirgula = valor.slice(posVirgula + 1).replace(/\D/g, '').slice(0, 2);

                    // Recompõe o valor com a vírgula
                    valor = antesVirgula + ',' + depoisVirgula;
                } else {
                    // Se não há vírgula, limita a 10 dígitos
                    valor = valor.replace(/\D/g, '').slice(0, 10);
                }

                // Atualiza diretamente o valor do campo de entrada
                event.target.value = valor;

                // Emite o valor filtrado para o componente pai
                this.$emit('update:modelValue', valor);
            },
            onBlur() {
                this.isFocused = false;
            },
            onFocus() {
                this.isFocused = true;
            },
            formatCurrency(value) {
                if (!value) return '';
                
                // Remove a vírgula temporariamente para conversão numérica
                let numericValue = parseFloat(value.replace(',', '.'));

                // Formata o número como moeda brasileira
                return new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL',
                    minimumFractionDigits: 2
                }).format(numericValue);
            }
        },
        mounted() {
            //console.log('Component mounted.')
        }
    }
</script>
