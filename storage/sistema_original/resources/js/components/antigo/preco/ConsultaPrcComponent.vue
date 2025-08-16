<!-- APRESENTAÇÃO DO COMPONENTE -->
<!-- Este componente exibe a lista de composições existentes na tabela de preço DERPR -->

<template>

    <div class="container">
        <!-- campos para consulta -->
        <div class="row">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-2">
                        <label for="selectDatabase">Data Base</label>
                        <select @change="setaDataBase" v-model="origemID" id="selectListaDatabase" class="form-select">                    
                            <!-- <option value="22">"11-11-1111"</option> -->
                            <option v-for="(item, index) in dataBaseLista" :key="index" :value="item[0]">{{ item[1] }}</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="selectDesoneracao">Desoneracao</label>
                        <select v-model="desoneracao" id="selectDesoneracao" class="form-select">  
                            <option value=0 selected>Sem</option> 
                            <option value=1 >Com</option>  
                        </select>
                    </div>
                    <div class="col-md-1 align-self-end">
                        <button @click="executarConsulta" class="btn btn-primary">Consultar</button>
                    </div>
                </div>
            </div>
            <div class="col-md-2 align-self-end">
                <div class="row">
                    <div class="col-md-12 ">
                        <button @click="importarItens" class="btn btn-primary">Cadastrar Itens</button>
                    </div>
                </div>
            </div>
        </div>
        
       

        <br>

        <!-- filtros -->
        <div class="filtro-container mt-2 text-center"  v-if="mostraFiltros">

            <div class="messfiltro text-center mb-3 text-danger"  v-if="messFiltro.length>0">{{ messFiltro }}</div>


            
            <div class="row text-center">
                <div class="col-md-5">
                    <input v-model="filtroCodigo" type="text" class="form-control" placeholder="Filtrar por código">
                </div>
                <div class="col-md-6">
                    <input v-model="filtroDescricao" type="text" class="form-control" placeholder="Filtrar por descrição">
                </div>
                <div class="col-md-1">
                    <div class="btn-filtro-container d-flex justify-content-end">
                        <!-- Exemplo de um botão com tooltip -->
                        <button @click="filtrarConsulta" type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Aplicar Filtro">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                                <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z"/>
                            </svg>
                        </button>
                        <button v-if="filtroFoiAplicado" @click="executarConsulta" type="button" class="btn btn-secondary ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Remover Filtro" >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16">
                                <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </button>
                    </div>
                </div>            
            </div>
        
    </div>

     <!-- Botões de navegação -->
    <div class="paginacao-container">
        <div class="row ">
            <div class="col-md-3 text-start mt-1">Registros Exibidos nessa página: <span>{{ data.length }}</span></div>
            <div class="btn_paginacao_container col-md-9 text-end" v-if="!filtroFoiAplicado">

                 <button @click="previousPage" :disabled="currentPage === 1"> Anterior</button>
                 <span> Página {{ currentPage }} de {{ totalPages }} </span>
                 <button @click="nextPage" :disabled="currentPage === totalPages">Próxima </button>
            </div>
        </div>
    </div>

        

      


        <!-- Tabela com os dados da consulta-->
        <div class="row">
            <div class="col">
                <table class="table table-striped tbl-consulta tabela-fixa ">
                    <thead class="">
                        <tr>
                            <td>Código</td>
                            <td>Descrição</td>
                            <td style="width: 100px;">Unidade</td>
                            <td style="width: 100px;">Mão de Obra</td>
                            <td style="width: 100px;">Material</td>
                            <td style="width: 100px;">Equipamento</td>
                            <td style="width: 100px;">Transporte</td>
                            <td style="width: 100px;">Total</td>
                        </tr>
                    </thead>

                    <tbody>
                        <!-- <tr v-for="(item) in dadosFiltrados" :key="item.codigo_compo"> -->
                        <template v-for="(item) in data" :key="item.codigo_compo">
                        <!-- <template v-for="(item, index) in data" :key="item.codigo_compo"> -->
                            <!-- <template v-if="shouldShowDescTipo(index)">
                                <tr>
                                    <td colspan="8">{{ item.desc_classe }} - {{ item.desc_tipo }}</td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr>
                                    <td colspan="8"></td>
                                </tr>
                            </template> -->

                            <tr>
                                <td>{{ item.codigo }}</td>
                                <td>{{ item.descricao  }}</td>
                                <td>{{ item.unidade_medida }}</td>
                                <td>{{ formatNumberMoedaBR(item.custo_mao_de_obra) }}</td>
                                <td>{{ formatNumberMoedaBR(item.custo_material) }}</td>
                                <td>{{ formatNumberMoedaBR(item.custo_equipamento) }}</td>
                                <td>{{ formatNumberMoedaBR(item.custo_transporte) }}</td>
                                <td>{{ formatNumberMoedaBR(item.custo_unitario) }}</td>
                            </tr>
                            
                        </template>
                    </tbody>

                </table>

            </div>
        </div>


         <!-- componente para exibição de mensagens do sistema -->
        <msgsystem-component :mostrar="mostrarMensagem" :tipo="tipoMensagem" :mensagem="mensagem"></msgsystem-component>

    </div>
   



     
</template>

<script>
    import * as XLSX from 'xlsx';
    // a técnica de debounce serve para atrasar a execução da função de filtragem até que o usuário pare de digitar por um curto período de tempo
    // npm install lodash.debounce
    import debounce from 'lodash.debounce';

    export default {
        //dados => vem do controller
        props: ['dados'] ,
        data() {
            return {

                dataBaseLista:[], //irá conter as 'datas-base' disponíveis para consulta
                dataBase:'', //data_base selecionada para consulta
                origemID:'', //origem_id selecionada para consulta
                desoneracao:0,
                
                mostraTabela:false, 
                
                data:[], //conteúdo da página que está sendo exibida
                dataTotal:[], //irá armazenar todos os dados da requisição


                

                //relacionadas aos filtros
                mostraFiltros: false,
                filtroCodigo: '',
                filtroDescricao: '',
                messFiltro: [],
                filtroFoiAplicado: false,

                //propriedades nessárias para exibição de mensagem na tela
                mostrarMensagem: false,
                tipoMensagem: '',
                mensagem: '',

                //paginacao
                currentPage: 1,
                itemsPerPage: 400, // Número de itens por página
                totalPages: 0,


               
            }
        },
        computed: {

         

           
        },
        methods: {

            //mostra um número com 7 casas decimais.
            toFixed7(value) {
                // if (!value) return '0.0000000';
                // return parseFloat(value).toFixed(7);
                if (!value) return '0,0000000';
                return parseFloat(value).toFixed(7).replace('.', ',');
            },

       


            filtrarConsulta(){
                //se as condições para execução do filtro forem atendidas irá executar a função 'aplicarfiltros()'
                let mess = [];
                let podeFiltrar = false;
                let codigo = this.filtroCodigo

                //console.log(this.descClasseFiltroSelecionada)
                //console.log(this.descTipoFiltroSelecionada)
                
                if (codigo.length>0 ) {
                    if (codigo.length>=2 && codigo != 'NaN') {
                        podeFiltrar = true;
                    } else {
                        mess.push('o código deve ser númerico')
                        mess.push('o código deve ter pelo menos dois carateres')
                        podeFiltrar = false;
                    }
                }

                let descricao = this.filtroDescricao
                if (descricao.length>0 ) {
                    if (descricao.length>=4) {
                        podeFiltrar = true;
                    } else {
                        mess.push('a descricao deve ter pelo menos 4 caracteres')
                        podeFiltrar = false;
                    }
                }

                if (descricao.length == 0 && codigo.length == 0) {
                    mess.push('informe um código ou uma descrição')
                }

                if (this.descClasseFiltroSelecionada != "") {
                    podeFiltrar = true
                }


                
                if (podeFiltrar) {
                    this.aplicarFiltros()
                    this.messFiltro = []
                    this.filtroFoiAplicado = true;
                    //console.log(this.messFiltro)
                    //console.log('tem que filtrar')
                } else {
                    this.messFiltro = mess
                    this.filtroFoiAplicado = false;
                    //console.log(mess)
                    //console.log(podeFiltrar)
                }
                
                
                
            },
            extrairNumerosRegex(str) {
                if (str == ""){
                     return ""
                 }
                let numeros = str.replace(/\D/g, ""); // Remove todos os caracteres não numéricos
                let numeroUnico = parseInt(numeros, 10); // Converte a string de números em um número inteiro
                return numeroUnico
            },

            // Função para obter o valor de um cookie pelo nome
            getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
            },

            consultaOrigens(){
                //consulta quantas 'datas-base' estão disponíveis no banco de dados para o SINAP
                
                let baseUrl = apiURL; // apiURL é definida em app.blade.php
                let url = baseUrl + '/api/v1/fonte';

                // Obtém o token do cookie
                const token = this.getCookie('token');

                let configuracao = {
                    method: 'POST',
                    headers: {
                    'Content-Type': 'application/json', // Define o tipo de conteúdo como JSON
                    'Authorization': `Bearer ${token}` // Adiciona o token JWT ao cabeçalho 'Authorization'
                    },
                    body: JSON.stringify({
                        instituicao: "PARANA CIDADE",
                    })
                };

                return fetch(url, configuracao)
                    .then(response => response.json())
                    .then(data => {
                       let lista = data.origens
                       //console.log(lista);
                       lista.forEach(item => {
                            //console.log(item)
                            if (item !== null) {
                                let dataBase = item['data_base']
                                let origemID = item['id']
                                //console.log(origemID);
                                this.dataBaseLista.push([origemID, dataBase]);
                            }
                        });
                        //console.log(this.dataBaseLista)
                    })
                    .catch(error => console.error('Error:', error));

            },


            /** PAGINAÇAÕ */
            async loadPage(page) {


                const dbName = 'TabelaPrc';
                const tableName = 'prc_' + this.desoneracao + '_' + this.origemID;
                const version = await this.getDatabaseVersion(dbName); // Obtenha a versão atual do banco de dados
                const request = indexedDB.open(dbName, version);

                request.onsuccess = (event) => {
                    const db = event.target.result;

                    if (!db.objectStoreNames.contains(tableName)) {
                        console.error('A tabela não existe no IndexedDB.');
                        this.exibirMensagemSistema('erro', 'A tabela não existe no IndexedDB.');
                        return;
                    }

                    const transaction = db.transaction([tableName], 'readonly');
                    const store = transaction.objectStore(tableName);
                    const totalCountRequest = store.count();

                    totalCountRequest.onsuccess = () => {
                        const totalCount = totalCountRequest.result;
                        this.totalPages = Math.ceil(totalCount / this.itemsPerPage);

                        const start = (page - 1) * this.itemsPerPage;
                        const end = start + this.itemsPerPage;

                        let rangeRequest = store.openCursor();
                        let currentIndex = 0;
                        let pageData = [];

                        rangeRequest.onsuccess = (event) => {
                            const cursor = event.target.result;
                            if (cursor && currentIndex < end) {
                                if (currentIndex >= start) {
                                    pageData.push(cursor.value);
                                }
                                currentIndex++;
                                cursor.continue();
                            } else {
                                this.data = pageData;
                                //console.log(this.data)
                            }
                        };

                        rangeRequest.onerror = (event) => {
                            console.error('Erro ao carregar dados da página:', event.target.error);
                        };
                    };

                    totalCountRequest.onerror = (event) => {
                        console.error('Erro ao contar os registros:', event.target.error);
                    };
                   
                };

                request.onerror = (event) => {
                    console.error('Erro ao abrir o IndexedDB:', event.target.error);
                };
            },

            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                    this.loadPage(this.currentPage);
                }
            },

            previousPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.loadPage(this.currentPage);
                }
            },

            /** FILTROS */
            async aplicarFiltros() {



                const dbName = 'TabelaPrc';
                const tableName = 'prc_' + this.desoneracao + '_' + this.origemID;
                const version = await this.getDatabaseVersion(dbName); // Obtenha a versão atual do banco de dados
                const request = indexedDB.open(dbName, version);

                request.onsuccess = (event) => {
                    const db = event.target.result;

                    if (!db.objectStoreNames.contains(tableName)) {
                        console.error('A tabela não existe no IndexedDB.');
                        this.exibirMensagemSistema('erro', 'A tabela não existe no IndexedDB.');
                        return;
                    }

                    const transaction = db.transaction([tableName], 'readonly');
                    const store = transaction.objectStore(tableName);
                    const rangeRequest = store.openCursor();

                    let dadosFiltrados = [];

                    rangeRequest.onsuccess = (event) => {
                        const cursor = event.target.result;
                        if (cursor) {
                            const item = cursor.value;
                            //console.log('item', item);
                            //console.log(this.filtroCodigo);
                            if (
                                (!this.filtroCodigo || item.codigo.includes(this.filtroCodigo)) &&
                                (!this.filtroDescricao || item.descricao.includes(this.filtroDescricao.toUpperCase()))
                            ) {
                                dadosFiltrados.push(item);
                            }
                            cursor.continue();
                        } else {
                            //this.dadosFiltrados = dadosFiltrados;
                            this.data = dadosFiltrados;
                        }
                    };

                    rangeRequest.onerror = (event) => {
                        console.error('Erro ao carregar dados do IndexedDB:', event.target.error);
                    };
                };

                request.onerror = (event) => {
                    console.error('Erro ao abrir o IndexedDB:', event.target.error);
                };
            },
            
            /** CONSULTA E GRAVAÇÃO EM INDEXDB (NO NAVEGADOR) */
            // Verificar se a tabela já existe no IndexedDB
            tabelaExiste() {
                return new Promise((resolve, reject) => {

                    
                    const dbName = 'TabelaPrc';
                    const tableName = 'prc_' + this.desoneracao + '_' + this.origemID;
                    const request = indexedDB.open(dbName);

                    request.onsuccess = (event) => {
                        const db = event.target.result;
                        const exists = db.objectStoreNames.contains(tableName);
                        db.close();
                        resolve(exists);
                    };

                    request.onerror = (event) => {
                        reject(event.target.error);
                    };
                });
            },

            async executarConsulta() {
                this.mostraFiltros = true
                this.filtroDescricao = ""
                this.filtroCodigo = ""
                this.filtroFoiAplicado = false
                this.messFiltro = []
                

                this.exibirMensagemSistema('info', 'Consultando ...');

                const tabelaExiste = await this.tabelaExiste();
                if (tabelaExiste) {
                    this.loadPage(1);
                    
                    this.exibirMensagemSistema('info', 'Dados já estão disponíveis no navegador.');
                    setTimeout(() => {
                        this.retirarMensagemSistema();
                    }, 2000);
                    return;
                } 

                let baseUrl = apiURL; // apiURL é definida em app.blade.php
                let url = baseUrl + '/api/v1/tabelapreco';

                // Obtém o token do cookie
                const token = this.getCookie('token');

                let configuracao = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json', // Define o tipo de conteúdo como JSON
                        'Authorization': `Bearer ${token}` // Adiciona o token JWT ao cabeçalho 'Authorization'
                    },
                    body: JSON.stringify({
                        desoneracao: this.desoneracao,
                        origem_id: this.origemID
                    })
                };

                return fetch(url, configuracao)
                    .then(response => response.json())
                    .then(data => {
                        this.dataTotal = data.data;
                        //console.log(this.dataTotal);

                        // Armazena o resultado no IndexedDB
                        if (Array.isArray(this.dataTotal) && this.dataTotal.length > 0) {
                            this.storeDataInIndexedDB()
                                .then((version) => {

                                    this.loadPage(1, version);  // Carrega a primeira página após a gravação
                                });
                        } else {
                            console.error('Nenhum dado encontrado para armazenar.');
                        }
                    }).then(()=>{
                        //this.loadPage(1);
                    })
                    .then(() => {
                        this.exibirMensagemSistema('sucesso', 'Consulta efetuada com sucesso!');
                        setTimeout(() => {
                            this.retirarMensagemSistema();
                        }, 2000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            },

            

            async storeDataInIndexedDB() {
                const dbName = 'TabelaPrc';
                const tableName = 'prc_' + this.desoneracao + '_' + this.origemID;

                try {
                    let version = await this.getDatabaseVersion(dbName);
                    version += 1;
                    const request = indexedDB.open(dbName, version);

                    return new Promise((resolve, reject) => {
                        request.onupgradeneeded = (event) => {
                            const db = event.target.result;
                            if (!db.objectStoreNames.contains(tableName)) {
                                db.createObjectStore(tableName, { keyPath: 'id', autoIncrement: true });
                            }
                        };

                        request.onsuccess = (event) => {
                            const db = event.target.result;
                            this.addItemsToStore(db, tableName);
                            resolve(version);
                        };

                        request.onerror = (event) => {
                            console.error('Erro ao abrir o IndexedDB:', event.target.error);
                            reject(event.target.error);
                        };
                    });
                } catch (error) {
                    console.error('Erro ao obter a versão do IndexedDB:', error);
                }
            },

            addItemsToStore(db, tableName) {
                const transaction = db.transaction([tableName], 'readwrite');
                const store = transaction.objectStore(tableName);

                // Iterar sobre os dados e adicionar ou atualizar cada item no objectStore
                this.dataTotal.forEach(item => {
                    try {
                    const cleanedItem = this.cleanData(item);
                    // Tenta pegar o item existente
                    const getRequest = store.get(cleanedItem.id);
                    getRequest.onsuccess = () => {
                        if (getRequest.result) {
                        // Se o item já existir, atualiza-o
                        store.put(cleanedItem).onerror = (event) => {
                            console.error('Erro ao atualizar item:', event.target.error);
                        };
                        } else {
                        // Se o item não existir, adiciona-o
                        store.add(cleanedItem).onerror = (event) => {
                            console.error('Erro ao adicionar item:', event.target.error);
                        };
                        }
                    };
                    getRequest.onerror = (event) => {
                        console.error('Erro ao verificar item:', event.target.error);
                    };
                    } catch (e) {
                    console.error('Erro ao processar item:', item, e);
                    }
                });

                transaction.oncomplete = () => {
                    //console.log('Todos os dados foram armazenados no IndexedDB com sucesso.');
                };

                transaction.onerror = (event) => {
                    console.error('Erro ao armazenar dados no IndexedDB:', event.target.error);
                };
            },

            getDatabaseVersion(dbName) {
                return new Promise((resolve, reject) => {
                    const request = indexedDB.open(dbName);
                    request.onsuccess = (event) => {
                        const db = event.target.result;
                        const version = db.version;
                        db.close();
                        resolve(version);
                    };
                    request.onerror = (event) => {
                        reject(event.target.error);
                    };
                });
            },

            cleanData(data) {
                // Função para limpar dados não clonáveis
                return JSON.parse(JSON.stringify(data, (key, value) => {
                    if (typeof value === 'function' || value instanceof Node) {
                        return undefined;
                    }
                    return value;
                }));
            },

        

          
            /** FUNÇÕES AUXILIARES */

            //entrada: 1234.99 
            //saida: R$ 1.123,99
            formatCurrency(valor){
                valor = parseFloat(valor)
                // Verifica se o valor é um número
                if (typeof valor !== 'number') {
                    //console.log('oi');
                    return valor; // Se não for número, retorna o valor original
                }

                // Formata o número para o formato de moeda brasileira
                let formatter = new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL',
                    currencyDisplay: 'narrowSymbol' // 'narrowSymbol' também pode ser usado para um símbolo mais estreito
                    //currencyDisplay: 'code'
                });

                return formatter.format(valor);
            },

            //entrada: "1234.99" 
            //saida: R$ 1.123,99
            formatNumberMoedaBR(valor){
                // Converte a string para número, se possível
                let numero = parseFloat(valor);

                // Verifica se a conversão foi bem sucedida e se o número é válido
                if (!isNaN(numero) && typeof numero === 'number') {
                // Formata o número para o formato de moeda brasileira sem símbolo
                return numero.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                } else {
                // Se não for possível converter para número, retorna o valor original
                return valor;
                }
            },

            //entrada: "1.234,99" 
            //saida: 1234.99
            converterParaNumero(valorBR){
                valorBR = valorBR.replace('.', '');
                return parseFloat(valorBR);
            },

            importarItens(){
                let baseUrl = appURL; // appURL é definida em app.blade.php
                let url = baseUrl + '/prc/cadastrar';
                
                window.location.href = url;


            },

            

            //quando o usuário dá dois cliques em cima de uma composição
            

            exibirMensagemSistema(tipo, conteudo) {
                this.tipoMensagem = tipo;
                this.mensagem = conteudo;
                this.mostrarMensagem = true;                
            },
            retirarMensagemSistema(){
                    this.mostrarMensagem = false;
            }

            

           
        },
        watch: {
            mostraFiltros(newValue) {
                //responsável por 'habilitar' o tooltip do bootstrap quando o filtro é exibido na tela 
                if (newValue) {
                    this.$nextTick(() => {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                    });
                }
            }
        
        },
        
        mounted() {

            // Esta linha inicializa os tooltips no momento que o componente é montado
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            //busca as fontes de dados para o DER-PR
            this.consultaOrigens().then(() => {
                //Seleciona o primeiro elemento do  por padrão
                if (this.dataBaseLista.length > 0) {
                    this.origemID = this.dataBaseLista[0][0];
                    this.dataBase = this.dataBaseLista[0][1]
                }
            });

                
                
        }
    }
</script>
<style>


    thead{
        font-weight: bold ;
    }
    .tabela-fixa thead {
        position: sticky;
        top: 0;
        border-bottom: 4px solid #000;
        background-color: #a12121; /* Cor de fundo inicial */
    }
    .btn_paginacao_container{
        padding-right: 5px;
    }
    .btn_paginacao_container span{
        display: inline-block;
        padding: 0px 10px;
    }
    .btn_paginacao_container button{
        padding: 8px;
        background-color: rgb(255, 255, 255);
        border: 1px solid #eeeeee;
        border-radius: 5px;
    }
    .btn_paginacao_container button:hover{
        background-color: #ececec;

    }
    .filtro-container{
        padding: 20px;
        border: 1px solid #f7f7f7;
        background-color: #f7f7f78c;
        border-radius: 10px;
    }

    .select-classe-filtro-container{
        text-align: left;
    }
    .select-classe-filtro-container select{
        max-width: 100%;
        color: #575757;
    }

    .paginacao-container{
        padding: 5px 20px;
        margin-top: 10px;
        border: 1px solid #f7f7f7;
        background-color: #f7f7f7;
        border-radius: 5px;
    }

 
 
</style>

