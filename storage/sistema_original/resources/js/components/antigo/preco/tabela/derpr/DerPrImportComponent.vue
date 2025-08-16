<template>
    <div class="container">
        <div class="row justify-content-center  ">
            <div class="col-md-12">
                <!-- titulo do formulário -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-titulo">
                            <h1>Selecione a Fonte de Dados</h1>
                        </div>
                    </div>
                </div>

                <!-- formulario DMT -->
                <form method="POST" action="" @submit.prevent="importarTabelaPreco($event)" enctype="multipart/form-data">
                    
                    <div class="row form-container frm_dmt justify-content-center">

                        <div class="mb-3 col-md-6 pt-3">

                            <div class="row mb-3">
                                <input type="hidden" name="_token" :value="csrf_token">
                                <input type="hidden" name="tabela_filha" value="n/a" v-model="tabela_filha">
                                <div class="col">
                                    <!-- <label for="formFile" class="form-label">Default file input example</label> -->
                                    <input class="form-control" type="file" id="formFile" @change="carregarArquivo($event)">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="mb-3 col-3" >
                                    <label for="fonte_id" class="form-label">Fonte de Dados:</label>
                                    <select id="fonte_id" class="form-select" v-model="fonte_id">
                                        <option value="1" selected>DER-PR</option>
                                        <option value="2">SINAP</option>
                                        <option value="3">PREFEITURA CURITIBA</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-3" >
                                    <label for="data_base" class="form-label">Data Base</label>
                                    <input type="date" class="form-control" id="data_base" name="data_base" v-model="data_base" >
                                </div>
                                <div class="mb-3 col-6" >
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <input type="text" class="form-control" id="descricao" name="descricao" v-model="descricao" >
                                </div>
                            </div>

                        </div>
                        
                    </div>

                    

                    

                    <!-- botões de ação do formulário -->
                    <div class="row text-center">
                        <div class="col">
                            <button type="submit" class="btn bnt-padrao btn-gravar mt-3" >Importar</button>
                        </div>
                    </div>
                </form>
                <!-- aquivo: {{ arquivoParaImportar }}<br>
                descricao: {{ descricao }}<br>
                tbl filha:{{ tabela_filha }}<br>
                data base:{{ data_base }}<br>
                fonte ID:{{ fonte_id }}<br>
                id usuario: {{ user_id }} -->


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
        props: ['dados','csrf_token', 'user_id'] ,
        data() {
            return {
                //propriedade que armazena o retorno da importação
                dadosDERPRImport: [],
                //propriedades nessárias para exibição de mensagem na tela
                mostrarMensagem: false,
                tipoMensagem: '',
                mensagem: '',
                //propriedades que virão do formulário e que serão gravadas no banco de dados
                arquivoParaImportar: null, //armazena um único arquivo
                fonte_id: '',
                descricao: '',
                data_base: '',
                tabela_filha: 'n/a',
            }
        },
        methods: {
            // Função para obter o valor de um cookie pelo nome
            getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
            },
                     
            exibirMensagemSistema(tipo, conteudo, tempo=3000) {
                this.tipoMensagem = tipo;
                this.mensagem = conteudo;
                this.mostrarMensagem = true;
                setTimeout(() => {
                    this.mostrarMensagem = false;
                }, tempo); // Espera antes de recolher a mensagem
            },
            carregarArquivo(e){
                this.arquivoParaImportar = e.target.files[0]; // Seleciona o primeiro arquivo
            },
            importarTabelaPreco(event){
                event.preventDefault();

                switch (this.fonte_id) {
                    case '1': 
                        this.importarDERPR()
                        break;
                    case '2': 
                        console.log('importar SINAP')
                        break;
                    case '3': 
                        console.log('importar CTBA')
                        break;
                
                    default:
                        break;
                }
                //console.log(this.fonte)
            },
            importarDERPR(){
                if (!this.arquivoParaImportar || !(this.arquivoParaImportar instanceof File)) {
                    this.exibirMensagemSistema('erro', 'Por favor, selecione um arquivo válido.');
                    return;
                }

                //montando os dados para requisição 
                let formData = new FormData();
                formData.append('arquivo' , this.arquivoParaImportar)
                formData.append('fonte_id' , this.fonte_id)
                formData.append('descricao' , this.descricao)
                formData.append('data_base' , this.data_base)
                formData.append('tabela_filha' , this.tabela_filha)
                formData.append('user_id' , this.user_id)

                //console.log(formData)

                

                
                /*REQUISIÇÃO PARA OBTER AS DIFERENTES DADAS BASE CADASTRADAS NA TABELA 'base_der_servicos'*/
                let baseUrl = apiURL //apiURL é definida em app.blade.php;  
                let url = baseUrl + '/api/v1/derpr/origem/'

                // Obtém o token do cookie
                const token = this.getCookie('token');

                // configuração para a requisição Axios
                const config = {
                    headers: {
                         _method: 'POST', 
                        'Content-Type': 'multipart/form-data',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                    }
                };
                

                // enviar a requisição 
                this.exibirMensagemSistema('info', 'importando...', 20000);
                axios.post(url, formData, config)
                    .then(response => {
                        //console.log(response.data);
                        this.exibirMensagemSistema('sucesso', 'Gravação realizada com sucesso'); // Mover para dentro deste then
                    })
                    .catch(error => {
                        if (error.response) {
                            // A requisição foi feita e o servidor respondeu com um código de status fora do intervalo 2xx
                            if (error.response.status === 404) {
                                this.exibirMensagemSistema('erro', 'Recurso não encontrado (404).');
                            } else {
                                this.exibirMensagemSistema('erro', `Erro: ${error.response.status}`);
                            }
                        } else if (error.request) {
                            // A requisição foi feita, mas nenhuma resposta foi recebida
                            this.exibirMensagemSistema('erro', 'Nenhuma resposta do servidor.');
                        } else {
                            // Algo aconteceu na configuração da requisição que acionou um erro
                            this.exibirMensagemSistema('erro', 'Erro ao configurar a requisição.');
                        }
                        console.error('Erro:', error);
                    });



            }
        },
        
        mounted() {
            
            
        }
    }
</script>