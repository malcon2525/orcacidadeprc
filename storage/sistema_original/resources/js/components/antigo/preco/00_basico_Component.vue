<template>
    <div class="container">
        <div class="testedr">componente derpr</div>
    </div>
</template>

<script>
    export default {
        //dados => vem do controller
        //csrf_token => vem de setbdi.blade.php
        props: ['dados','csrf_token'] ,
        data() {
            return {
                //propriedade que armazena os dados da consulta
                dadosBdiUsuario: [],
                //propriedades nessárias para exibição de mensagem na tela
                mostrarMensagem: false,
                tipoMensagem: '',
                mensagem: '',
                //propriedades para filtro na consulta
                database: '2023-09-30',
                onerado: '1'
            }
        },
        methods: {
            // Função para obter o valor de um cookie pelo nome
            getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
            },
            validarTeclaNumerica(event) {
                // Obtém o código da tecla pressionada
                let key = event.key;

                // Verifica se a tecla pressionada é válida
                if (
                    (key >= '0' && key <= '9') || // Números de 0 a 9
                    //key === '.' ||                // Ponto
                    key === ',' ||                // Vírgula
                    key === 'Tab' ||              // Tab
                    key === 'Delete' ||           // Delete
                    key === 'Backspace' ||        // Backspace
                    key === 'ArrowUp' ||          // Seta para cima
                    key === 'ArrowDown' ||        // Seta para baixo
                    key === 'ArrowLeft' ||        // Seta para a esquerda
                    key === 'ArrowRight'          // Seta para a direita
                ) {
                    // Se for uma tecla válida, permite a ação padrão
                    return true;
                } else {
                    // Se não for uma tecla válida, impede a ação padrão
                    event.preventDefault();
                }
            },
            formatarValorParaUS(newValue, refName) {   
                let valorNumericoBR, valorNumericoUS
                
                if(newValue == ''){
                    valorNumericoUS = 0;
                    valorNumericoBR = 0;
                } else {
                    // Converte para número formato americado com duas casas decimais
                    valorNumericoUS = parseFloat(newValue.replace(/\./g, '').replace(',', '.')).toFixed(2);               
                    // Converte para número formato brasileiro com duas casas decimais
                    valorNumericoBR =parseFloat(valorNumericoUS).toLocaleString('pt-BR', { maximumFractionDigits: 2 });
                }            
                    
                //atualiza o valor no campo html (mostra na tela com formato brasileiro)
                this.$refs[refName].value = valorNumericoBR;

                //atualiza o valor em dadosBdiUsuario (armazena em formato americano)
                this.dadosBdiUsuario[refName] = valorNumericoUS 

                //calculaTotalBDI
                this.calcularTotalBDI()
                
                
            },
            formatarValorParaBR(valor) {
                // Converte para número formato brasileiro com duas casas decimais               
                var valorNumericoBR =parseFloat(valor).toLocaleString('pt-BR', { maximumFractionDigits: 2 });
                return valorNumericoBR; 
            },
            
            exibirMensagemSistema(tipo, conteudo) {
                this.tipoMensagem = tipo === 'sucesso' ? 'sucesso' : 'erro';
                this.mensagem = conteudo;
                this.mostrarMensagem = true;
                setTimeout(() => {
                    this.mostrarMensagem = false;
                }, 3000); // Espera antes de recolher a mensagem
            }
           
        },
        
        mounted() {
            let baseUrl = apiURL //apiURL é definida em app.blade.php;      
            let urlLogin = baseUrl + '/api/v1/bdi'

            console.log(urlLogin)


            // Obtém o token do cookie
             const token = this.getCookie('token');

             console.log(token)

            let configuracao = {
                method: 'get',
                headers: {
                    'Authorization': `Bearer ${token}` // Adiciona o token JWT ao cabeçalho 'Authorization'
                }
                
            }
            fetch(urlLogin, configuracao)
                .then(response => response.json())
                .then(data => {
                    if(data.token){
                        document.cookie = 'token=' + data.token + ';SameSite=Lax';
                    }

                    //atualiza com as informações do banco de dados
                    this.dadosBdiUsuario = data.bdi[0];
                })
                .catch(error => {
                    console.error('Erro ao fazer a requisição:', error);
                    // Aqui você pode lidar com o erro de acordo com a sua lógica de negócio
                });
        }
    }
</script>