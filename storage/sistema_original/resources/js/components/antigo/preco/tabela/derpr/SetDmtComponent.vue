<template>
    <div class="container">
        <div class="row justify-content-center  ">
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
                <form  ref="frmDMT" id="frmDMT" method="POST" action="" @submit.prevent="gravarDMT($event)">
                    <div class="row form-container frm_dmt" @keydown.ctrl.s.prevent="gravarDMT($event)">
                        <div class="col-md-12 ">
                            <input type="hidden" name="_token" :value="csrf_token">
                                
                            <div class="row justify-content-center">
                                <div class="mb-4 col-md-2 input_dmt input_dmt_codigo"><strong>Código</strong></div>
                                <div class="mb-4 col-md-4"><strong>Descrição</strong></div>
                                <div class="mb-4 col-md-2"><strong>Pav(Km)</strong></div>
                                <div class="mb-4 col-md-2"><strong>n-Pav(Km)</strong></div>
                                <div class="mb-4 col-md-1 input_dmt input_dmt_kmlocal"><strong>Local(Km)</strong></div>
                            </div>

                            <div 
                                class="row justify-content-center align-items-center, centraliza-vertical" 
                                v-for="(item, index) in dadosDmtUsuario" 
                                :key="index" 
                                id="linhaRegistro"
                                
                                ref="linha"
                            >
                                    <input type="hidden" class="form-control input_ro" id="base_der_dmt_id" name="base_der_dmt_id[]" :value="item.base_der_dmt_id">
                                    <input type="hidden" class="form-control" id="tipo1" name="tipo[]">
                                
                                    <div class="mb-2 col-md-2 input_dmt input_dmt_codigo">
                                        <input type="hidden" class="form-control input_ro" id="codigo1" name="codigo[]" readonly :value="item.codigo_servico" @focus="bloquearFoco" @keydown.tab.prevent>
                                        {{ item.codigo_servico }}
                                    </div>
                                    <div class="mb-2 col-md-4 input_dmt">
                                        <input type="hidden" class="form-control input_ro" id="descricao1" name="descricao[]" readonly :value="item.descricao" @focus="bloquearFoco" @keydown.tab.prevent>
                                        {{ item.descricao }}
                                    </div>

                                    <!-- Campo de x1_pavimentado -->
                                    <div class="mb-2 col-md-2 input_dmt">
                                        <input 
                                            type="number" 
                                            class="form-control" 
                                            id="x1_pavimentado1" 
                                            name="x1_pavimentado[]" 
                                            :value="item.x1_pavimentado" 
                                            
                                            @focus="destacaLinhaForm(index)" 
                                        
                                            @keyup="impedeNumeroNegativo"
                                        >
                                    </div>

                                    <!-- Campo de x2_nao_pavimentado -->
                                    <div class="mb-2 col-md-2 input_dmt">
                                        <input 
                                            type="number" 
                                            class="form-control" 
                                            id="x2_nao_pavimentado1" 
                                            name="x2_nao_pavimentado[]" 
                                            :value="item.x2_nao_pavimentado" 
                                            
                                            @focus="destacaLinhaForm(index)" 
                                            
                                            @keyup="impedeNumeroNegativo"
                                        >
                                    </div>

                                    <!-- Campo de limiteKmLocal -->
                                    <div class="mb-2 col-md-1 input_dmt input_dmt_kmlocal">
                                        <input 
                                            type="number" 
                                            class="form-control" 
                                            id="limiteKmLocal1" 
                                            name="limiteKmLocal[]" 
                                            :value="item.limiteKmLocal" 
                                            
                                            @focus="destacaLinhaForm(index)" 
                                            
                                            @keyup="impedeNumeroNegativo"                                    
                                        >
                                    </div>

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
                onerado: '1',
                //linhaEmFoco: null
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
                let baseUrl = apiURL //apiURL é definida em app.blade.php;    
                let urlDerPR = baseUrl + '/api/v1/derpr/dmt'

                

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
                        this.dadosDmtUsuario = data.dmts;

                        //console.log(this.dadosDmtUsuario[0])

                        
                        
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
                
                // Array para armazenar os resultados
                const resultados = [];


                const linhaRegistro = document.querySelectorAll('#linhaRegistro');

                linhaRegistro.forEach(linha => {

                    // Obter os valores dos campos
                    const baseDerDmtId = (linha.querySelector(`input[name="base_der_dmt_id[]"]`).value || '').trim() || 0;
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
                
                // Array para armazenar os resultados
                const resultados = [];

                
                
                /**
                 * para melhorar a segurança 
                 * os campos baseDerDmtId, codigo, descricao não devem ser pegos do formulário e sim das informações vindas no banco de dados que está armazenda na propriedade 'this.dadosDmtUsuario.dmt'.
                 * os campos x1_pavimentado, x2_nao_pavimentado e limiteKmLocal devem vir do formulario
                 */
   
                //console.log(dadosFormulario)
                //console.log(this.dadosDmtUsuario)
                
                //itera nos dados do formulário
                dadosFormulario.forEach((value, index) => {
                   // console.log(index)
                    //console.log(value.codigo)
                   let cod = value.codigo
                    

                   // Código que será verificado em 'this.dadosDmtUsuario'
                   let codigoServicoProcurado = value.codigo;

                    // Verifica se existe o código passado no formulário nos dados que vieram do backend
                    //let existeCodigoServico = this.dadosDmtUsuario.some(item => item.codigo_servico === codigoServicoProcurado);

                    // Encontra o índice do objeto com o codigo_servico especificado
                    let indice = this.dadosDmtUsuario.findIndex(item => item.codigo_servico === codigoServicoProcurado);
                    

                    if (indice !== -1) {
                        //console.log('gravando...')
                        //se existe então..
                        // insere em 'resultados' que por sua vez será enviado para gravação no banco
                        const baseDerDmtId = this.dadosDmtUsuario[indice]['base_der_dmt_id'];
                        const codigo = cod;
                        const descricao = this.dadosDmtUsuario[indice]['descricao'];
                        const tipo = 'local';                
                        const x1_pavimentado = value.x1_pavimentado;
                        const x2_nao_pavimentado = value.x2_nao_pavimentado;
                        const limiteKmLocal = value.limiteKmLocal;

                        //atualiza dadosDmtUsuario
                        this.dadosDmtUsuario[indice]['x1_pavimentado'] = value.x1_pavimentado 
                        this.dadosDmtUsuario[indice]['x2_nao_pavimentado'] = value.x2_nao_pavimentado 
                        this.dadosDmtUsuario[indice]['limiteKmLocal'] = value.limiteKmLocal 
                        
                        // insere em 'resultados' que por sua vez será enviado para gravação no banco
                        //atualiza


                        let dadosLinha = {
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
                });
                //console.log(this.dadosDmtUsuario)

                //console.log(resultados)

                return resultados
            },
            gravarDMT(event){
                // Impedir o comportamento padrão do formulário de ser acionado
                event.preventDefault();

                let dadosFormulario = this.pegarDadosDoFormulario();
                //console.log(dadosFormulario)
                
                let dadosFormatados = this.montarDadosParaGravacao(dadosFormulario)

                // Converter o array de resultados em uma string JSON
                const jsonString = JSON.stringify(dadosFormatados);

                /** GRAVANDO */
                let baseUrl = apiURL //apiURL é definida em app.blade.php;     
                let url = baseUrl + '/api/v1/derpr/dmt'

                // Obtém o token do cookie
                const token = this.getCookie('token');

                fetch(url, {
                method: 'POST', 
                headers: {
                    'Content-Type': 'application/json', // Indica que o corpo da requisição é um JSON
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                },
                body: jsonString
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao enviar dados para a API');
                    }
                    return response.json(); // Converte a resposta da API em JSON
                    })
                .then(
                    //this.consultaDmtUsuario()                    
                )
                .then(                     
                        this.exibirMensagemSistema('sucesso', 'Gravação realizada com sucesso')
                    )
                .catch(error => {
                    console.error('Erro:', error);
                        this.exibirMensagemSistema('erro', 'Erro durante a gravação')
                    });
                


            },
            
            removeFocus(event) {
                //por algum motivo, se o usuário deixar o cursor em um campo e clicar no botão gravar, a atualização dos campos, após o clique, fica comprometida. Por isso, antes de executar a gravação devemos tirar o foco dos campos do formulário.
                event.target.blur();
                //console.log('Mouse saiu de:', event.target);
            },
            bloquearFoco(event) {
                event.target.blur(); // Desfoca o campo
            },
            destacaLinhaForm(index) {
                const linhaElement = this.$refs.linha[index]

                // remover classe css:'linha-foco' para todos os elementos de ref
                this.$refs.linha.forEach(element => {
                    element.classList.remove('linha-foco');
                });
                // adicionar classe css: 'linha-foco' para 'linhaElement'
                if (linhaElement) {
                    linhaElement.classList.add('linha-foco');
                }
                
            },
            
            impedeNumeroNegativo(e) {
                //console.log(e.target.value)
                if (e.target.value < 0 ){
                    e.target.value = 0
                }
            }
           
        },
        
        
        mounted() {
            this.consultaDmtUsuario()


            
        }
    }
</script>

<style>
    .linha-foco {
        background-color: #d3d3d32a; /* cor cinza */
    }
    .centraliza-vertical{
        
        padding-top: 10px;
    }
</style>