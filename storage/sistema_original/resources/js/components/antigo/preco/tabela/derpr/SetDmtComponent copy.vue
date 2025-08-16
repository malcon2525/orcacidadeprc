<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <!-- titulo do formulário -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-titulo">
                            <h1>Dados do DMT</h1>
                        </div>
                    </div>
                </div>
                <!-- formulario DMT -->
                <div class="row ">
                    <form method="POST" action="" @submit.prevent="gravarDMT($event)">
                        <input type="hidden" name="_token" :value="csrf_token">
                        
                        <div class="col-md-12">
                            
                            
                            
                            <div class="row justify-content-center" v-for="(item, chave) in dadosDmtUsuario.dmt" :key="item.base_der_dmt_id" >
                                <!-- <div>{{ item.base_der_dmt_id }}</div> -->
                                
                                <input type="hidden" class="form-control" :id="'base_der_dmt_id' + item.base_der_dmt_id" name="base_der_dmt_id[]">
                                <input type="hidden" class="form-control" :id="'tipo' + item.base_der_dmt_id" name="tipo[]">

                                <div class="mb-3 col-1">
                                    <label for="codigo" class="form-label">codigo</label>
                                    <input type="text" class="form-control" id="codigo1" name="codigo[]" readonly :value="chave">
                                </div>
    
                                <div class="mb-3 col-4">
                                    <label for="descricao1" class="form-label">descricao</label>
                                    <input type="text" class="form-control" id="descricao1" name="descricao[]" value="descricao" readonly>
                                </div>
                                
                                <div class="mb-3 col-1">
                                    <label for="x1_pavimentado1" class="form-label">x1 (Km)</label>
                                    <input type="text" class="form-control" id="x1_pavimentado1" name="x1_pavimentado[]" value="88">
                                </div>
                                <div class="mb-3 col-1">
                                    <label for="x2_nao_pavimentado1" class="form-label">x2 (Km)</label>
                                    <input type="text" class="form-control" id="x2_nao_pavimentado1" name="x2_nao_pavimentado[]" value="88">
                                </div>
                                <div class="mb-3 col-1">
                                    <label for="limiteKmLocal1" class="form-label"> Local (Km)</label>
                                    <input type="text" class="form-control" id="limiteKmLocal1" name="limiteKmLocal[]" value="88">
                                </div>

                            </div>





                        </div>
                        <!-- botões de ação do formulário -->
                        <div class="row text-center">
                            <div class="col">
                                <button type="submit" class="btn bnt-padrao btn-gravar mt-3" >Gravar</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- componente para exibição de mensagens do sistema -->
                <msgsystem-component :mostrar="mostrarMensagem" :tipo="tipoMensagem" :mensagem="mensagem"></msgsystem-component>
            </div>
        </div>
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
                dadosDmtUsuario: [],
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
            exibirMensagemSistema(tipo, conteudo) {
                this.tipoMensagem = tipo === 'sucesso' ? 'sucesso' : 'erro';
                this.mensagem = conteudo;
                this.mostrarMensagem = true;
                setTimeout(() => {
                    this.mostrarMensagem = false;
                }, 3000); // Espera antes de recolher a mensagem
            },
            consultaDmtUsuario(){
                /*REQUISIÇÃO PARA OBTER TODOS OS DADOS*/
                let baseUrl = window.location.origin;    
                let urlDerPR = baseUrl + '/api/v1/derpr/dmt?resumo'

                // Obtém o token do cookie
                const token = this.getCookie('token');

                //console.log(token)

                let configuracao = {
                    method: 'get',
                    headers: {
                        'Authorization': `Bearer ${token}` // Adiciona o token JWT ao cabeçalho 'Authorization'
                    }
                    
                }
                fetch(urlDerPR, configuracao)
                    .then(response => response.json())
                    .then(data => {
                        if(data.token){
                            document.cookie = 'token=' + data.token + ';SameSite=Lax';
                        }

                        //atualiza com as informações do banco de dados
                        this.dadosDmtUsuario = data.dmt_usuario;
                        //console.log(this.dadosDmtUsuario)
                        
                    })
                    .catch(error => {
                        console.error('Erro ao fazer a requisição:', error);
                        // Aqui você pode lidar com o erro de acordo com a sua lógica de negócio
                    });

            },
            removerLetrasEConverterParaNumero(valor) {
                // Remove letras e símbolos com uma expressão regular
                const apenasNumeros = valor.replace(/[^0-9]/g, '');
                // Converte para número ou retorna zero se for vazio
                return apenasNumeros === '' ? 0 : parseFloat(apenasNumeros);
            },
            pegarDadosDoFormulario(){
                // Selecionar todos os campos base_der_dmt_id
                const camposBaseDerDmtId = document.querySelectorAll('input[name^="base_der_dmt_id"]');
                
                // Array para armazenar os resultados
                const resultados = [];

                // Iterar sobre cada campo base_der_dmt_id
                camposBaseDerDmtId.forEach(campoBase => {
                    // Encontrar a linha (div.row) correspondente ao campo base_der_dmt_id
                    const linha = campoBase.closest('.row'); 

                    // Obter o valor do campo base_der_dmt_id
                    const baseDerDmtId = campoBase.value.trim() || 0;
                    
                    // Obter os valores dos outros campos da linha
                    // caso não exista ou seja nulo ou seja vazio assume o valor '0'
                    const codigo = (linha.querySelector(`input[name="codigo[]"]`).value || '').trim() || 0;
                    const descricao = (linha.querySelector(`input[name="descricao[]"]`).value || '').trim() || 0;
                    const tipo = (linha.querySelector(`input[name="tipo[]"]`).value || '').trim() || 0;
                    
                    const x1_pavimentado = this.removerLetrasEConverterParaNumero(linha.querySelector(`input[name="x1_pavimentado[]"]`).value);
                    const x2_nao_pavimentado = this.removerLetrasEConverterParaNumero(linha.querySelector(`input[name="x2_nao_pavimentado[]"]`).value);
                    const limiteKmLocal = this.removerLetrasEConverterParaNumero(linha.querySelector(`input[name="limiteKmLocal[]"]`).value);
                    
                    // Criar um objeto com os dados da linha
                    const dadosLinha = {
                        user_id: 1,
                        base_der_dmt_id: baseDerDmtId,
                        codigo,
                        descricao,
                        tipo,
                        x1_pavimentado,
                        x2_nao_pavimentado,
                        limiteKmLocal
                    };
                    // Adicionar os dados da linha ao array de resultados
                    resultados.push(dadosLinha);
                });

                return resultados
            },
            montarDadosParaGravacao(dadosFormulario){
                 //para melhorar a segurança 
                // os campos baseDerDmtId, codigo, descricao não devem ser pegos do formulário e sim das informações vindas no banco de dados.
                // os campos x1_pavimentado, x2_nao_pavimentado e limiteKmLocal devem vir do formulario
                
                // Array para armazenar os resultados
                const resultados = [];


                let cod = dadosFormulario[0].codigo
                //verifica se o código vindo do formulário existe nas informações recebidas do banco de dados
                if (cod in this.dadosDmtUsuario.dmt) {
                    
                    const baseDerDmtId = this.dadosDmtUsuario.dmt[cod]['base_der_dmt_id'];
                    const codigo = cod;
                    const descricao = this.dadosDmtUsuario.dmt[cod]['descricao'];
                    const tipo = 'local';                
                    const x1_pavimentado = dadosFormulario[0]['x1_pavimentado'];
                    const x2_nao_pavimentado = dadosFormulario[0]['x2_nao_pavimentado'];
                    const limiteKmLocal = dadosFormulario[0]['limiteKmLocal'];

                    const dadosLinha = {
                        base_der_dmt_id: baseDerDmtId,
                        //codigo,
                        //descricao,
                        tipo,
                        x1_pavimentado,
                        x2_nao_pavimentado,
                        limiteKmLocal
                    };

                    // Adicionar os dados da linha ao array de resultados
                    resultados.push(dadosLinha);
                }
                return resultados
            },
            gravarDMT(event){
                // Impedir o comportamento padrão do formulário de ser acionado
                event.preventDefault();

                let dadosFormulario = this.pegarDadosDoFormulario();

                let dadosFormatados = this.montarDadosParaGravacao(dadosFormulario)

                //console.log(dadosFormatados)

                // // Converter o array de resultados em uma string JSON
                // const jsonString = JSON.stringify(dadosFormatados);


                // /** GRAVANDO */
                // let baseUrl = window.location.origin;    
                // let url = baseUrl + '/api/v1/derpr/dmt'

                // // Obtém o token do cookie
                // const token = this.getCookie('token');

                // fetch(url, {
                // method: 'POST', 
                // headers: {
                //     'Content-Type': 'application/json', // Indica que o corpo da requisição é um JSON
                //     'Authorization': `Bearer ${token}`,
                //     'Accept': 'application/json',
                // },
                // body: jsonString
                // })
                // .then(response => {
                //     if (!response.ok) {
                //         throw new Error('Erro ao enviar dados para a API');
                //     }
                //     return response.json(); // Converte a resposta da API em JSON
                //     })
                // .then(data => {
                //     console.log('Resposta da API:', data);
                //     })
                // .catch(error => {
                //     console.error('Erro:', error);
                //     });


            }
           
        },
        
        mounted() {
            this.consultaDmtUsuario()

            
        }
    }
</script>