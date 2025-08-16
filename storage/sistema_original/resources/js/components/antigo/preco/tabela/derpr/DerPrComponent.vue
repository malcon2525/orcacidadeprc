<template>
    <div class="container">
        <div class="dadosConsultaContainer">
            <h1>DER-PR</h1>

            <div class="tbl-complemento">

                <div class="row">
                    <div class="col-md-2">
                        <label for="selectDatabase" class="ms-1">Selecione a Data Base:</label>
                        <select @change="consultaDERPR" v-model="database" id="selectDatabase" class="form-select">                    
                            <option v-for="item in listaDataBase" :key="item[0]" :value="item[0]">{{ item[1] }}</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label for="selectOnerado" class="ms-1">Desoneração</label><!-- onerado -->
                        <select @change="consultaDERPR" v-model="onerado" id="selectOnerado" class="form-select">                    
                            <option value="1">Sem</option>  <!-- significa: campo onerado=1 (true)   -->
                            <option value="0">Com</option>  <!-- significa: campo onerado=0 (false) -->
                        </select>
                    </div>
                </div>

                
            </div>

            <div class="tbl-btn">
                <button @click="exportarParaExcel" class="btn">Exportar para Excel</button>
                <!-- <button class="btn">Filtrar</button> -->
            </div>
            
            <table class="table table-striped tbl-consulta ">
            <thead>
                <tr id="tblFiltro">
                    <td></td>
                    <td></td>
                    <td>
                                              
                        <input  class="form-control mb-2 col-md-1" type="number" name="flt_codigo" id="flt_codigo" v-model="fltCodigo" @keydown="filtrarConsulta('codigo', $event)">
                    </td>
                    <td>
                                               
                        <input  class="form-control mb-2 col-md-1" type="text" name="flt_descricao" id="flt_descricao" v-model="fltDescricao" @keydown="filtrarConsulta('descricao', $event)">
                    </td>
                    <td>
                                                
                        <input  class="form-control mb-2 col-md-1" type="text" name="flt_grupo" id="flt_grupo" v-model="fltGrupo" @keydown="filtrarConsulta('grupo', $event)">
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                </tr>
                <tr>
                    <th>Tipo</th>
                    <th @click="ordenar('idUnico', 'idUnico')">ID

                        <span v-if="colunaOrdenada === 'idUnico' && ordemAscendente">&#9650;</span>
                        <span v-else-if="colunaOrdenada === 'idUnico' && !ordemAscendente">&#9660;</span>
                    </th>
                    <th @click="ordenar('codigo', 'idUnico')">Código
                        <span v-if="colunaOrdenada === 'codigo' && ordemAscendente">&#9650;</span>
                        <span v-else-if="colunaOrdenada === 'codigo' && !ordemAscendente">&#9660;</span>
                    </th>
                    <th @click="ordenar('descricao', 'idUnico')">Descrição
                        <span v-if="colunaOrdenada === 'descricao' && ordemAscendente">&#9650;</span>
                        <span v-else-if="colunaOrdenada === 'descricao' && !ordemAscendente">&#9660;</span>
                    </th>
                    <th @click="ordenar('grupo', 'idUnico')">Grupo
                        <span v-if="colunaOrdenada === 'grupo' && ordemAscendente">&#9650;</span>
                        <span v-else-if="colunaOrdenada === 'grupo' && !ordemAscendente">&#9660;</span>
                    </th>
                    <th>Transporte</th>
                    <th>Mão de Obra</th>
                    <th>Material</th>
                    <th>Equip.</th>
                    <th>Preço S/BDI</th>
                    <th>Preço C/BDI</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item) in dadosDerPRResumoOrdenados" :key="item.idUnico">
                    <td>
                        <div class="custom-select">
                            <select name="tipoComposicao" id="tipoComposicao" class="tipoComposicao">
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="E">E</option>
                            </select>
                        </div>
                    </td>
                    <td>{{ item.idUnico }}</td> 
                    <td>{{ item.codigo }}</td>
                    <td>{{ item.descricao }}</td>
                    <td>{{ item.grupo }}</td>
                    <td class="text-end">{{ formatarValorParaBR(item.total_transporte) }}</td>
                    <td class="text-end">{{ formatarValorParaBR(item.total_mao_de_obra) }}</td>
                    <td class="text-end">{{ formatarValorParaBR(item.total_material) }}</td>
                    <td class="text-end">{{ formatarValorParaBR('0') }}</td>
                    <td class="text-end">{{ formatarValorParaBR(item.total_preco_sem_bdi) }}</td>
                    <td class="text-end">{{ formatarValorParaBR(item.total_preco_com_bdi) }}</td>
                </tr>
            </tbody>
            </table>
        </div>
    </div>

            <!-- componente para exibição de mensagens do sistema -->
            <msgsystem-component :mostrar="mostrarMensagem" :tipo="tipoMensagem" :mensagem="mensagem"></msgsystem-component>


            <div v-if="erroConexao" class="container p-5 text-center">

                <span style="color:red"> Erro de Conexão com o servidor.... </span>

            </div>

</template>

<script>
    import * as XLSX from 'xlsx';
    // a técnica de debounce serve para atrasar a execução da função de filtragem até que o usuário pare de digitar por um curto período de tempo
    // npm install lodash.debounce

    import debounce from 'lodash.debounce';
    export default {
        //dados => vem do controller
        //csrf_token => vem de setbdi.blade.php
        props: ['dados','csrf_token'] ,
        data() {
            return {

                erroConexao:true,

                //propriedades que armazenam os dados da consulta
                dadosDerPR: [], //contém todos os dados, inclusive a composição
                dadosDerPRResumo: [], //contem somente os dados necessários para exibição na tela
                //propriedades nessárias para exibição de mensagem na tela
                mostrarMensagem: false,
                tipoMensagem: '',
                mensagem: '',
                //propriedades para filtro na consulta
                database: '2023-09-30',
                onerado: '1',
                fltCodigo : '',
                fltDescricao : '',
                fltGrupo : '',
                dadosDerPRSemFiltro: [],
                //propriedades com os dados da consulta de forma ordenada
                colunaOrdenada: '', // Nome da coluna atualmente ordenada
                ordemAscendente: true, // Ordem de classificação ascendente ou descendente
                //propriedade que contém as datas base cadastradas na tabela 'base_der_servicos'
                listaDataBase: []
            }
        },
        computed: {
            dadosDerPRResumoOrdenados() {
                // Retorna os dados ordenados com base na coluna atualmente ordenada
                if (!this.colunaOrdenada) {
                    return this.dadosDerPRResumo; // Se nenhum cabeçalho de coluna foi clicado, retorna os dados sem ordenação
                }

                // Função para comparar dois itens com base na coluna atualmente ordenada
                const comparar = (a, b) => {
                    const valorA = a[this.colunaOrdenada];
                    const valorB = b[this.colunaOrdenada];

                    // Verifica se os valores são numéricos
                    const numA = parseFloat(valorA);
                    const numB = parseFloat(valorB);

                    if (!isNaN(numA) && !isNaN(numB)) {
                        // Se forem numéricos, realiza comparação numérica
                        return this.ordemAscendente ? numA - numB : numB - numA;
                    } else {
                        // Se não forem numéricos, realiza comparação de strings
                        return this.ordemAscendente ? valorA.localeCompare(valorB) : valorB.localeCompare(valorA);
                    }
                };
                // Clona e classifica os dados com base na coluna e na ordem de classificação atual
                return [...this.dadosDerPRResumo].sort(comparar);
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
                

                var valorNumericoBR =parseFloat(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }).replace('R$', '').trim();
                return valorNumericoBR; 
            },
            converterDataUSParaBR(data) {
                // Separa a data em ano, mês e dia
                const [ano, mes, dia] = data.split('-');

                // Retorna a data no formato "dd-mm-yyyy"
                return `${dia}-${mes}-${ano}`;
            },
            
            exibirMensagemSistema(tipo, conteudo) {
                this.tipoMensagem = tipo === 'sucesso' ? 'sucesso' : 'erro';
                this.mensagem = conteudo;
                this.mostrarMensagem = true;
                setTimeout(() => {
                    this.mostrarMensagem = false;
                }, 3000); // Espera antes de recolher a mensagem
            },
            ordenar(coluna, colunaOrdenadaAtual) {
                // Verifica se a mesma coluna está sendo clicada novamente
                if (this.colunaOrdenada === coluna) {
                    // Inverte a ordem de classificação se a mesma coluna for clicada novamente
                    this.ordemAscendente = !this.ordemAscendente;
                } else {
                    // Se não, atualiza a coluna atualmente ordenada e redefine a ordem para ascendente
                    this.colunaOrdenada = coluna;
                    this.ordemAscendente = true;
                }

                // Atualiza a coluna atualmente ordenada na visualização
                this.colunaOrdenadaAtual = colunaOrdenadaAtual;
            },
            exportarParaExcel() {
                const ws = XLSX.utils.json_to_sheet(this.dadosDerPRResumo);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'DER-PR');

                /* Salvar o arquivo */
                XLSX.writeFile(wb, 'DER-PR.xlsx');
            },
            consultaDerPRBruto(){
                /*REQUISIÇÃO PARA OBTER TODOS OS DADOS*/                  
                let baseUrl = apiURL //apiURL é definida em app.blade.php;  
                let urlDerPR = baseUrl + '/api/v1/derpr/servico/' + this.database + '/' + this.onerado

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
                        this.dadosDerPR = data.baseDerServicos;
                        
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
                            this.exibirMensagemSistema('erro', 'Erro requisição nao foi aceita pelo servidor.');
                        }
                        console.error('Erro:', error);
                    });

            },
            consultaDerPrResumo(){
                /*REQUISIÇÃO PARA OBTER TODOS OS DADOS - RESUMO*/
                let baseUrl = apiURL //apiURL é definida em app.blade.php;     
                let urlDerPRResumo = baseUrl + '/api/v1/derpr/servico/' + this.database + '/' + this.onerado + '?resumo'

                // Obtém o token do cookie
                const token = this.getCookie('token');

                let configuracao = {
                    method: 'get',
                    headers: {
                        'Authorization': `Bearer ${token}` // Adiciona o token JWT ao cabeçalho 'Authorization'
                    }
                    
                }

                fetch(urlDerPRResumo, configuracao)
                    .then(response => response.json())
                    .then(data => {
                        if(data.token){
                            document.cookie = 'token=' + data.token + ';SameSite=Lax';
                        }

                        //atualiza com as informações do banco de dados
                        this.dadosDerPRResumo = data.baseDerServicos;
                        this.dadosDerPRSemFiltro = data.baseDerServicos;
                        
                    })
                    .then(data=>{
                        // Adicione um identificador único a cada registro
                        this.dadosDerPRResumo.forEach((item, index) => {
                            item.idUnico = index + 1; 
                        });
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
                            this.exibirMensagemSistema('erro', 'Erro requisição nao foi aceita pelo servidor.');
                        }
                        console.error('Erro:', error);
                    });

            },
            consultaBaseDerServicos(){
                /*REQUISIÇÃO PARA OBTER AS DIFERENTES DADAS BASE CADASTRADAS NA TABELA 'base_der_servicos'*/
                let baseUrl = apiURL //apiURL é definida em app.blade.php;  
                let urlDataBase = baseUrl + '/api/v1/derpr/listadatabase'

                // Obtém o token do cookie
                const token = this.getCookie('token');

                let configuracao = {
                    method: 'get',
                    headers: {
                        'Authorization': `Bearer ${token}` // Adiciona o token JWT ao cabeçalho 'Authorization'
                    }
                    
                }

                fetch(urlDataBase, configuracao)
                    .then(response => response.json())
                    .then(data => {
                        if(data.token){
                            document.cookie = 'token=' + data.token + ';SameSite=Lax';
                        }

                        //atualiza com as informações do banco de dados
                        let datasExistentes = data.listadatas;

                        //no banco de dados vem datas no formato 'yyyy-mm-dd'
                        //quero armazenar também no formato 'dd-mm-yyyy' para facilitar a montagem do campo select
                        let listaDataBase = []
                        datasExistentes.forEach(data => {

                            let dateBr = this.converterDataUSParaBR(data);
                            let array = [data, dateBr]
                            listaDataBase.push(array);
                            
                            
                        });
                        this.listaDataBase = listaDataBase;
                        
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
            },
            consultaDERPR(){
                
                this.consultaDerPRBruto();
                this.consultaDerPrResumo();
            },
            filtrarConsulta: debounce(function() {
                const dadosFiltrados = this.dadosDerPRSemFiltro.filter(item => {
                    
                    // Verifica se o valor do atributo 'codigo' começa com fltCodigo
                    const codigoValido = item.codigo.startsWith(this.fltCodigo);

                    // Verifica se o valor do atributo 'descricao' contém fltDescricao
                    const descricaoValida = item.descricao.toLowerCase().includes(this.fltDescricao.toLowerCase());

                    // Verifica se o valor do atributo 'grupo' começa com fltGrupo
                    const grupoValido = item.grupo.toLowerCase().startsWith(this.fltGrupo.toLowerCase());

                    // Retorna true se todas as condições forem atendidas
                    return codigoValido && descricaoValida && grupoValido;
                });

                this.dadosDerPRResumo = dadosFiltrados;
            }, 500) // Tempo de espera em milissegundos
           
        },
        
        mounted() {

            
            
            this.consultaDERPR();
            this.consultaBaseDerServicos();



                
                
        }
    }
</script>

<style>

.tipoComposicao{
    width: 50px;
    float: left;
   height: 34px;
   overflow: hidden;
   
   border: 1px solid #ccc;
   background-color: transparent;
   
   border-radius: 10px;
   padding-left: 5px;
   }
.tipoComposicao select{
    width: 50px;
    margin-left: 16px;
    wbackground-color: #f2f2f2;
    height: 38px;
    line-height: 38px;
    font-family: "Trebuchet MS", Helvetica, sans-serif!important;   
    color: #515151;
    font-size: 12px;
    background: transparent;
}


</style>