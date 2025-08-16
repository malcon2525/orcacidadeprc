<template>
    <div class="tudo"  >

        <div class="row">
                <div class="col-md-1 align-self-end mb-2">
                    <button @click="ExibirFrmVazio" type="button" class="btn btn-primary" >
                        Adicionar
                    </button>
                </div>
        </div>

        <div class="row">
            <div class="col-12">
                <table class="tbl_item">
                    <thead class="tbl_item_cabecalho">
                        <tr>
                            <td style="width: 30px ">ID</td>
                            <td style="width: 60px ">Deson.</td>
                            <!-- <td style="max-width: 150px">grupo</td> -->
                            <td style="width: 100px ">cod_comp</td>
                            <td style="">descrição</td>
                            <td style="width: 60px">unid.</td>
                            <td style="width: 100px; ">m.o.</td>
                            <td style="width: 100px; ">equip.</td>
                            <td style="width: 100px; ">material</td>
                            <td style="width: 100px; ">transp.</td>
                            <td style="width: 100px; ">total</td>
                            <td style="width: 100px;" ></td>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <template  v-for="(itemCD, indexCD) in itemsComDesoneracao" :key="indexCD">
                           
                            <tr class="tbl_item_linha">
                                <td>{{ indexCD + 1 }}</td>
                                <td style="text-align: center;">{{ formataDesoneracao(itemCD.desoneracao) }}</td>
                                
                                <td style="text-align: center;">{{ itemCD.codigo_comp }}</td>
                                <td v-html="itemCD.desc_comp"></td>
                                <td style="text-align: center;">{{ itemCD.unidade_medida }}</td>
                                <td style="text-align: right; padding-right: 5px;">{{ formatarValorParaBR(itemCD.custo_mao_obra) }}</td>
                                <td style="text-align: right; padding-right: 5px;">{{ formatarValorParaBR(itemCD.custo_equipamento) }}</td>
                                <td style="text-align: right; padding-right: 5px;">{{ formatarValorParaBR(itemCD.custo_material) }}</td>
                                <td style="text-align: right; padding-right: 5px;">{{ formatarValorParaBR(itemCD.custo_transporte) }}</td>
                                <td style="text-align: right; padding-right: 5px;">{{ formatarValorParaBR(itemCD.custo_total) }}</td>
                                <td style="text-align: right; padding-right: 5px;" class="tbl-acoes">
                                    <div class="d-flex justify-content-center ">

                                        <div class="acoes-icon">

                                            <svg v-if="!mostraDetalhe(itemCD.id)" @click="setIdDetalhe(itemCD.id)" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-file-arrow-down-fill" viewBox="0 0 16 16" id="mostraDetalheItem">
                                                <path d="M12 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M8 5a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 9.293V5.5A.5.5 0 0 1 8 5"/>
                                            </svg>


                                            <svg v-if="mostraDetalhe(itemCD.id)" @click="setIdDetalhe(itemCD.id)" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-file-arrow-up-fill" viewBox="0 0 16 16" id="mostraDetalheItem">
                                                <path d="M12 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M7.5 6.707 6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0z"/>
                                            </svg>
                                            
                                            
                                        </div>
                                        <div class="acoes-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16"  @click="mostrarModalItem(itemCD)">
                                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </td>
                                
                            </tr>
                            <tr>
                                <td colspan="14">
                                    <div class="comp-detalhe-container d-flex" v-if="mostraDetalhe(itemCD.id)">
                                        
                                        <div class="comp-detalhe-detalhe">
                                            <p class="comp-detalhe-tit">Detalhe da Composição</p>
                                            <div v-html="itemCD.detalhe_comp"></div>
                                            
                                        </div>
                                        <div class="comp-detalhe-anexos">
                                            <p class="comp-detalhe-tit">Anexos</p>
                                            
                                            <ul>
                                                <li v-if="itemCD.arquivo1">
                                                    <a :href="`/storage/${itemCD.arquivo1}`" :download="itemCD.arquivo1" class="lnk_arq">
                                                        {{ mostraNomeArquivo(itemCD.arquivo1) }}
                                                    </a>
                                                </li>
                                                <li v-if="itemCD.arquivo2">
                                                    <a :href="`/storage/${itemCD.arquivo2}`" :download="itemCD.arquivo2" class="lnk_arq">
                                                        {{ mostraNomeArquivo(itemCD.arquivo2) }}
                                                    </a>
                                                </li>
                                                <li v-if="itemCD.arquivo3">
                                                    <a :href="`/storage/${itemCD.arquivo3}`" :download="itemCD.arquivo3" class="lnk_arq">
                                                        {{ mostraNomeArquivo(itemCD.arquivo3) }}
                                                    </a>
                                                </li>
                                           </ul>
                                            
                                            
                                            
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        </template>

                    </tbody>
                    <tfoot>
                        <template  v-for="(itemCD, indexCD) in itemsSemDesoneracao" :key="indexCD">
                           
                           <tr class="tbl_item_linha">
                               <td>{{ indexCD + 1 }}</td>
                               <td style="text-align: center;">{{ formataDesoneracao(itemCD.desoneracao) }}</td>
                               
                               <td style="text-align: center;">{{ itemCD.codigo_comp }}</td>
                               <td v-html="itemCD.desc_comp"></td>
                               <td style="text-align: center;">{{ itemCD.unidade_medida }}</td>
                               <td style="text-align: right; padding-right: 5px;">{{ formatarValorParaBR(itemCD.custo_mao_obra) }}</td>
                               <td style="text-align: right; padding-right: 5px;">{{ formatarValorParaBR(itemCD.custo_equipamento) }}</td>
                               <td style="text-align: right; padding-right: 5px;">{{ formatarValorParaBR(itemCD.custo_material) }}</td>
                               <td style="text-align: right; padding-right: 5px;">{{ formatarValorParaBR(itemCD.custo_transporte) }}</td>
                               <td style="text-align: right; padding-right: 5px;">{{ formatarValorParaBR(itemCD.custo_total) }}</td>
                               <td style="text-align: right; padding-right: 5px;" class="tbl-acoes">
                                   <div class="d-flex justify-content-center ">

                                       <div class="acoes-icon">
                                            <svg v-if="!mostraDetalhe(itemCD.id)" @click="setIdDetalhe(itemCD.id)" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-file-arrow-down-fill" viewBox="0 0 16 16" id="mostraDetalheItem">
                                                <path d="M12 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M8 5a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 9.293V5.5A.5.5 0 0 1 8 5"/>
                                            </svg>


                                            <svg v-if="mostraDetalhe(itemCD.id)" @click="setIdDetalhe(itemCD.id)" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-file-arrow-up-fill" viewBox="0 0 16 16" id="mostraDetalheItem">
                                                <path d="M12 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M7.5 6.707 6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0z"/>
                                            </svg>
                                       </div>
                                       <div class="acoes-icon">
                                           <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16"  @click="mostrarModalItem(itemCD)">
                                               <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                           </svg>
                                       </div>
                                   </div>
                               </td>
                               
                           </tr>
                           <tr>
                               <td colspan="14">
                                   <div class="comp-detalhe-container d-flex" v-if="mostraDetalhe(itemCD.id)">
                                       
                                       <div class="comp-detalhe-detalhe">
                                           <p class="comp-detalhe-tit">Detalhe da Composição</p>
                                           <div v-html="itemCD.detalhe_comp"></div>
                                       </div>
                                       <div class="comp-detalhe-anexos">
                                           <p class="comp-detalhe-tit">Anexos</p>
                                           <ul>
                                                <li v-if="itemCD.arquivo1">
                                                    <a :href="`/storage/${itemCD.arquivo1}`" :download="itemCD.arquivo1" class="lnk_arq">
                                                        {{ mostraNomeArquivo(itemCD.arquivo1) }}
                                                    </a>
                                                </li>
                                                <li v-if="itemCD.arquivo2">
                                                    <a :href="`/storage/${itemCD.arquivo2}`" :download="itemCD.arquivo2" class="lnk_arq">
                                                        {{ mostraNomeArquivo(itemCD.arquivo2) }}
                                                    </a>
                                                </li>
                                                <li v-if="itemCD.arquivo3">
                                                    <a :href="`/storage/${itemCD.arquivo3}`" :download="itemCD.arquivo3" class="lnk_arq">
                                                        {{ mostraNomeArquivo(itemCD.arquivo3) }}
                                                    </a>
                                                </li>
                                           </ul>
                                        <div> 
                                                
                                    </div>
                                            
                                            
                                       </div>
                                   </div>
                               </td>
                           </tr>

                       </template>
                    </tfoot>
                </table>
            </div>
        </div>

        




        <!-- Modal para cadastro de intens na tabela de preço -->
        <div class="modal fade" id="cadItemModal" tabindex="-1" aria-labelledby="cadItemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="cadItemModalLabel">Cadastro de Item - Tabela de Preço</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        
                        <!-- formulário de cadastro de intes -->
                        <form enctype="multipart/form-data" name="frmCadItensPreco" id="frmCadItensPreco">

                            <!-- campos hidden -->
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <!-- <label for="id" class="form-label">ID</label> -->
                                        <input type="hidden" class="form-control" id="id" :value="itemPrecoSelecionado.id" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- mensagens de erro -->
                            <div class="row mess_error_frm" v-if="existeErroFrm">
                                <div class="col-md-12">
                                    <div>{{ errorFrm.cod_comp }}</div>
                                    <div>{{ errorFrm.descricao }}</div>
                                    <div>{{ errorFrm.unidade }}</div>
                                    <div>{{ errorFrm.mao_de_obra }}</div>
                                    <div>{{ errorFrm.equipamento }}</div>
                                    <div>{{ errorFrm.material }}</div>
                                    <div>{{ errorFrm.total }}</div>
                                    <div>{{ errorFrm.transporte }}</div>
                                    <div>{{ errorFrm.arquivo1 }}</div>
                                    <div>{{ errorFrm.arquivo2 }}</div>
                                    <div>{{ errorFrm.arquivo3 }}</div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- desoneração -->
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="desoneracao" class="form-label">Desoneração</label>
                                        <select class="form-select" id="desoneracao" v-model="itemPrecoSelecionado.desoneracao">
                                            <option value="true">Com</option>
                                            <option value="false">Sem</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- código da composição -->
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label 
                                            for="cod_comp"
                                            :class="{'form-label': true, 'corFonteErro': errorFrm.cod_comp !== ''}" 
                                        >Cód. Composição * </label>
                                        <input type="text" class="form-control" id="cod_comp" v-model="itemPrecoSelecionado.codigo_comp">
                                    </div>
                                </div>
                                <!-- unidade -->
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label 
                                        for="unidade" 
                                        :class="{'form-label': true, 'corFonteErro': errorFrm.unidade !== ''}" >Unidade *</label>
                                        <input type="text" class="form-control" id="unidade" v-model="itemPrecoSelecionado.unidade_medida">
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <!-- custo de mao de obra -->
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label 
                                            for="mao_de_obra" 
                                            :class="{'form-label': true, 'corFonteErro': errorFrm.mao_de_obra !== ''}">Mão de Obra
                                        </label>
                                        <campo-moeda-component v-model="itemPrecoSelecionado.custo_mao_obra"></campo-moeda-component>
                                    </div>
                                </div>

                                <!-- custo de equipmento -->
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label 
                                            for="equipamento" 
                                            :class="{'form-label': true, 'corFonteErro': errorFrm.equipamento !== ''}">Equipamento
                                        </label>
                                        <campo-moeda-component v-model="itemPrecoSelecionado.custo_equipamento"></campo-moeda-component>
                                    </div>
                                </div>
                                <!-- custo de Material -->
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label 
                                            for="material" 
                                            :class="{'form-label': true, 'corFonteErro': errorFrm.material !== ''}">Material</label>
                                        <campo-moeda-component v-model="itemPrecoSelecionado.custo_material"></campo-moeda-component>
                                    </div>
                                </div>  
                                <!-- custo de transporte -->
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label 
                                            for="transporte" 
                                            :class="{'form-label': true, 'corFonteErro': errorFrm.transporte !== ''}">Transporte</label>
                                        <campo-moeda-component v-model="itemPrecoSelecionado.custo_transporte"></campo-moeda-component>
                                    </div>
                                </div>

                                <!-- custo total -->
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label 
                                            for="total" 
                                            :class="{'form-label': true, 'corFonteErro': errorFrm.total !== ''}" >Total *</label>
                                        <campo-moeda-component v-model="itemPrecoSelecionado.custo_total"></campo-moeda-component>
                                       
                                        
                                    </div>
                                </div>
                            </div>                          
                            
                            <!-- descrição -->
                            <div class="mb-3">
                                <label 
                                    for="descricao" 
                                    :class="{'form-label': true, 'corFonteErro': errorFrm.descricao !== ''}" >Descrição *</label>
                                <textarea class="form-control" id="descricao" rows="3"  v-model="itemPrecoSelecionado.desc_comp" required></textarea>
                            </div>
           
                            <!-- detalhe composição -->
                            <div class="mb-3">
                                <label for="detalhecomposicao" class="form-label">Detalhe da Composição</label>
                                <div id="detalhecomposicao"><p>cccccccccccc cccccccccc cccccccccccccccccc ccccccccccccccccccc</p><p>cccccccccccc cccccccccc cccccccccccccccccc ccccccccccccccccccc</p><p>cccccccccccc cccccccccc cccccccccccccccccc ccccccccccccccccccc</p></div>
                            </div>

                            <label class="form-label">Arquivo 1</label>
                            <campo-file-component  ref="campoFileComponent1" 
                                :arquivo=" itemPrecoSelecionado.arquivo1"
                                :input-id="'arquivo1'"
                                @apagar="apagarArquivo(itemPrecoSelecionado.codigo_comp, itemPrecoSelecionado.arquivo1, 'arquivo1')"
                                @carregar = "carregarArquivo1"
                            />
                            <label class="form-label">Arquivo 2</label>
                            <campo-file-component  ref="campoFileComponent2" 
                                :arquivo=" itemPrecoSelecionado.arquivo2"
                                :input-id="'arquivo2'"
                                @apagar="apagarArquivo(itemPrecoSelecionado.codigo_comp, itemPrecoSelecionado.arquivo2, 'arquivo2')"
                                @carregar = "carregarArquivo2" 
                            />
                            <label class="form-label">Arquivo 3</label>
                            <campo-file-component  ref="campoFileComponent3" 
                                :arquivo=" itemPrecoSelecionado.arquivo3"
                                :input-id="'arquivo3'"
                                @apagar="apagarArquivo(itemPrecoSelecionado.codigo_comp, itemPrecoSelecionado.arquivo3, 'arquivo3')"
                                @carregar = "carregarArquivo3" 
                            />
                            
                        </form>
                        
                        <!-- FIM formulário de cadastro de intes -->


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="excluirItemPreco(itemPrecoSelecionado.codigo_comp)" v-if="itemPrecoSelecionado.id!=0">Excluir</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="cancelarItemPreco">Cancelar</button>
                        <button type="submit" class="btn btn-primary"  @click="gravarItemPreco">Gravar</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- componente para exibição de mensagens do sistema -->

        <div style="z-index: 999999; position:absolute">
            <msgsystem-component :mostrar="mostrarMensagem" :tipo="tipoMensagem" :mensagem="mensagem"></msgsystem-component>
        </div>    



    </div>
</template>

<script>
import CampoFileComponent from '../geral/CampoFileComponent.vue';
    export default {
  components: { CampoFileComponent },
  
        data() {
            return {
                //propriedades nessárias para exibição de mensagem na tela
                mostrarMensagem: false,
                tipoMensagem: '',
                mensagem: '',

         
                tabelaPRC:[],// contém os dados que serão exibidos na tabela
                idDetalhe : 0,
                itemPrecoSelecionado:[ //irá conter o item que foi selecionado (editar - mostrar modal)
                        {id: '0', desoneracao: true, codigo_comp: '', desc_comp: '', unidade_medida: '', custo_mao_obra: '', custo_equipamento: '', custo_material: '', custo_transporte: '', custo_total: '', detalhe_comp: '', arquivo1: '', arquivo2: '', arquivo3: '' },
                    ], 
                itemPrecoBackup:{}, //quando usuario clica em cancelar as alterações feitas em itemPrecoSelecionado serão desfeitas
                quill: null,   //usado no editor de texto rico


                //propriedades que virão do formulário e que serão gravadas no banco de dados
                arquivoParaImportar1: null, //armazena um único arquivo            
                arquivoParaImportar2: null, //armazena um único arquivo            
                arquivoParaImportar3: null, //armazena um único arquivo      
                
                //propriedades de validação do formulário
                errorFrm:{
                    'descricao' : '', 'mao_de_obra':'', 'equipamento':'', 'material':'', 'transporte':'', 'total':'', 'unidade':'', 'cod_comp':'', 'arquivo1':'', 'arquivo2':'', 'arquivo3':'' 
                },
                existeErroFrm : false,
                

            }
        },
        computed: {
            itemsComDesoneracao() {
                return this.tabelaPRC
                .filter(item => (item.desoneracao === true || item.desoneracao === "true"));                   
            },
            itemsSemDesoneracao() {
                return this.tabelaPRC
                    .filter(item => (item.desoneracao === false || item.desoneracao === "false"));                  
            },
            mostraDetalhe() {
                return (id) => {
                    // Lógica para determinar se o detalhe deve ser mostrado
                    if (id == this.idDetalhe) {
                        return true
                    } else {
                        return false
                    }
                    //return this.selectedId === id;
                };
            },
            
        },
        methods: {
            
            exibirMensagemSistema(tipo, conteudo, tempo=0) {
                this.tipoMensagem = tipo; 
                this.mensagem = conteudo;
                this.mostrarMensagem = true;
                if(tempo>0){
                    setTimeout(() => {
                        this.mostrarMensagem = false;
                    }, tempo); // Espera antes de recolher a mensagem
                }
            },
            setIdDetalhe(id){
                if (this.idDetalhe == id) {
                    this.idDetalhe = 0
                } else {
                    this.idDetalhe = id
                }
            },
            handleScroll() {
                //auxliar - o cabeçalho da tabela muda de cor ao chegar no topo
                const posicaoCabecalho = document.querySelector('.tbl_item_cabecalho');
                //console.log(window.scrollY)
                if (window.scrollY > 160) {
                    posicaoCabecalho.classList.add('sticky');
                } else {
                    posicaoCabecalho.classList.remove('sticky');
                }
            },
            iniciarEditor(){
                
                const toolbarOptions = [
                    ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                    //['blockquote', 'code-block'],
                    //['link', 'image', 'video', 'formula'],

                    //[{ 'header': 1 }, { 'header': 2 }],               // custom button values
                    //[{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    //[{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                    [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                    //[{ 'direction': 'rtl' }],                         // text direction

                    //[{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

                    [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                    //[{ 'font': [] }],
                    //[{ 'align': [] }],

                    ['clean']                                         // remove formatting button
                    ];

                    this.quill = new Quill('#detalhecomposicao', {
                        modules: {
                            toolbar: toolbarOptions
                        },
                        theme: 'snow'
                     });
                     // Desabilitar o corretor ortográfico
                     this.quill.root.spellcheck = false;


                    //  // Sincronizar o conteúdo do Quill com a propriedade `detalhe`
                    this.quill.on('text-change', () => {
                        this.itemPrecoSelecionado.detalhe_comp = this.quill.root.innerHTML;
                    });
           

            },
            mostrarModalItem(item) {

                //console.log(item)

                // Backup do item selecionado
                this.itemPrecoBackup = JSON.parse(JSON.stringify(item));;

                //atualizando propriedade itemPrecoSelecionado com o item que foi clicado
                this.itemPrecoSelecionado = item

                //zerando mensagens de erros de validações no formulário
                
                //console.log(this.itemPrecoSelecionado)
                
                //iniciazando o html do editor
                this.quill.root.innerHTML = this.itemPrecoSelecionado.detalhe_comp;
                
                //abrindo o modal
                const modal = new bootstrap.Modal(document.getElementById('cadItemModal'));
                modal.show();

                //zerando os campos dos arquivos anexos do formulário
                this.errorFrm = {
                    'descricao' : '', 'mao_de_obra':'', 'equipamento':'', 'material':'', 'transporte':'', 'total':'', 'unidade':'', 'cod_comp':'', 'arquivo1':'', 'arquivo2':'', 'arquivo3':'' 
                }
                this.existeErroFrm = false

                let fileInputArquivo1 = document.getElementById('arquivo1');
                let fileInputArquivo2 = document.getElementById('arquivo2');
                let fileInputArquivo3 = document.getElementById('arquivo3');
                if (fileInputArquivo1 != null) {
                    fileInputArquivo1.value = "";
                }
                if (fileInputArquivo2 != null) {
                    fileInputArquivo2.value = "";
                }
                if (fileInputArquivo3 != null) {
                    fileInputArquivo3.value = "";
                }


                this.arquivoParaImportar1 = ""
                this.arquivoParaImportar2 = ""
                this.arquivoParaImportar3 = ""

                //zerando a mensagem de erro no componete filho
                // Reseta o erro após um pequeno atraso
                setTimeout(() => {
                    if (this.$refs.campoFileComponent1) {
                        this.$refs.campoFileComponent1.error = '';
                    }
                    if (this.$refs.campoFileComponent2) {
                        this.$refs.campoFileComponent2.error = '';
                    }
                    if (this.$refs.campoFileComponent3) {
                        this.$refs.campoFileComponent3.error = '';
                    }

                }, 100)
                

                
            },
            cancelarItemPreco(){
                // Restaurar o item selecionado a partir do backup
                Object.assign(this.itemPrecoSelecionado, this.itemPrecoBackup);
                //this.itemPrecoSelecionado =[];

            },
            async excluirItemPreco(codComp) {

                
                let baseUrl = apiURL; // apiURL é definida em app.blade.php
                let url = baseUrl + '/api/v1/personalizada/importar/prc/apagar';

                // Obtém o token do cookie
                const token = this.getCookie('token');

                let configuracao = {
                    headers: {
                        'Authorization': `Bearer ${token}`, // Adiciona o token JWT ao cabeçalho 
                        'cod_comp' : codComp,
                    }
                };

                try {
                    const response = await axios.delete(url, configuracao);
                    //console.log(response.data)
                    await this.montarTabela()
                    // atualizando o item selecionado
                    this.itemPrecoBackup = this.itemPrecoSelecionado = []
                    
                    //return response.data;
                } catch (error) {
                    console.error('Error:', error);
                    return null;
                }
                
            },
            carregarArquivo1(file){
                this.arquivoParaImportar1 = file; // Seleciona o primeiro arquivo
            },
            carregarArquivo2(file){
                this.arquivoParaImportar2 = file; // Seleciona o primeiro arquivo
            },
            carregarArquivo3(file){
                this.arquivoParaImportar3 = file; // Seleciona o primeiro arquivo
            },
            // Função para obter o valor de um cookie pelo nome
            getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
            },
            validaFrm(){
                
                let existeErro = false

                let cod_comp = document.getElementById('cod_comp').value
                let unidade = document.getElementById('unidade').value
                let descricao = document.getElementById('descricao').value

                let total       = this.itemPrecoSelecionado.custo_total


  
                if(descricao == ''){
                    existeErro = true
                    this.errorFrm.descricao = 'o campo descrição deve ser preenchido'
                } else {
                    this.errorFrm.descricao = ''
                }
                if(unidade == ''){
                    existeErro = true
                    this.errorFrm.unidade = 'o campo unidade deve ser preenchido'
                }else {
                    this.errorFrm.unidade = ''
                }
                if(cod_comp == ''){
                    existeErro = true
                    this.errorFrm.cod_comp = 'o código da composição deve ser preenchido'
                }else {
                    this.errorFrm.cod_comp = ''
                }
                //validando campos moeda

                if( parseFloat(total) <= 0){
                    existeErro = true
                    this.errorFrm.total = 'o campo total deve maior que zero'
                }else {
                    this.errorFrm.total = ''
                }
                

                //retorno da função
                if (existeErro) {
                    this.existeErroFrm = true
                    return false
                } else {
                    this.existeErroFrm = false
                    return true
                }
                
            },
            async gravarItemPreco() {
                let validaFrm = this.validaFrm()
                if (!validaFrm) {
                    return
                }

                //this.exibirMensagemSistema('erro', 'Por favor, selecione um arquivo válido.');
                this.exibirMensagemSistema('sucesso', 'Gravando informações...');

                let gravar = await this.gravarItemNoBanco()

                await this.montarTabela()

                let gravacaoOK = gravar
                
                if(gravacaoOK == true) {
                    this.exibirMensagemSistema('sucesso', 'Gravação efetuada com sucesso!', 3000);

                    // //fechando o modal
                    const modalElement = document.getElementById('cadItemModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }

                } else {
                    this.exibirMensagemSistema('erro', 'Erro na gravação!', 3000);
                }


            },
            async gravarItemNoBanco(){
               
                let formData = new FormData();

                formData.append('arquivo1' , this.arquivoParaImportar1)
                formData.append('arquivo2' , this.arquivoParaImportar2)
                formData.append('arquivo3' , this.arquivoParaImportar3)
                formData.append('id' , this.itemPrecoSelecionado.id)
                formData.append('codigo_comp' , this.itemPrecoSelecionado.codigo_comp)
                formData.append('desoneracao' , this.itemPrecoSelecionado.desoneracao)
                formData.append('desc_comp' , this.itemPrecoSelecionado.desc_comp)
                formData.append('detalhe_comp' , this.itemPrecoSelecionado.detalhe_comp)
                formData.append('unidade_medida' , this.itemPrecoSelecionado.unidade_medida)
                //formData.append('grupo' , this.itemPrecoSelecionado.grupo)
                formData.append('custo_mao_obra' , this.itemPrecoSelecionado.custo_mao_obra)
                formData.append('custo_material' , this.itemPrecoSelecionado.custo_material)
                formData.append('custo_equipamento' , this.itemPrecoSelecionado.custo_equipamento)
                formData.append('custo_transporte' , this.itemPrecoSelecionado.custo_transporte)
                formData.append('custo_total' , this.itemPrecoSelecionado.custo_total)

                let baseUrl = apiURL; // apiURL é definida em app.blade.php
                let url = baseUrl + '/api/v1/personalizada/importar/prc';

                // Obtém o token do cookie
                const token = this.getCookie('token');

                let configuracao = {
                    headers: {
                        'Authorization': `Bearer ${token}` // Adiciona o token JWT ao cabeçalho 'Authorization'
                    }
                };

                try {
                    const response = await axios.post(url, formData, configuracao);
                    //console.log(response.data)
                    return response.data;
                } catch (error) {
                    console.error('Error:', error);
                    return null;
                }

            },

            async montarTabela(){
                this.tabelaPRC = await this.consultarCompPrc();
               // console.log(this.tabelaPRC) 
            },

            async consultarCompPrc(){
                let baseUrl = apiURL; // apiURL é definida em app.blade.php
                let url = baseUrl + '/api/v1/personalizada/importar/prc';

                // Obtém o token do cookie
                const token = this.getCookie('token');

                let configuracao = {
                    headers: {
                        'Authorization': `Bearer ${token}` // Adiciona o token JWT ao cabeçalho 'Authorization'
                    }
                };

                try {
                    const response = await axios.get(url, configuracao);
                    return response.data;
                } catch (error) {
                    console.error('Error:', error);
                    return null;
                }
            },
            
            ExibirFrmVazio(){
                
                this.itemPrecoSelecionado = { 
                        id: '0', desoneracao: true, codigo_comp: '', desc_comp: '', unidade_medida: '', custo_mao_obra: '0', custo_equipamento: '0', custo_material: '0', custo_transporte: '0', custo_total: '0', detalhe_comp: '', arquivo1: '', arquivo2: '', arquivo3: '', detalhe : '' 
                }

                this.itemPrecoBackup = JSON.parse(JSON.stringify(this.itemPrecoSelecionado));;

                
                //iniciazando o html do editor
                this.quill.root.innerHTML = this.itemPrecoSelecionado.detalhe;
                
                //abrindo o modal
                const modal = new bootstrap.Modal(document.getElementById('cadItemModal'));
                modal.show();


                //zerando a mensagem de erro no componete filho
                // Reseta o erro após um pequeno atraso
                setTimeout(() => {
                    if (this.$refs.campoFileComponent1) {
                        this.$refs.campoFileComponent1.error = '';
                    }
                    if (this.$refs.campoFileComponent2) {
                        this.$refs.campoFileComponent2.error = '';
                    }
                    if (this.$refs.campoFileComponent3) {
                        this.$refs.campoFileComponent3.error = '';
                    }

                }, 100)
                

            },

            formataDesoneracao(desoneracao){
                if (desoneracao == "true" || desoneracao == true){
                    return "Com"
                } else {
                    return "Sem"
                }
            },
            formatarValorParaBR(valor) {
                // Converte para número formato brasileiro com duas casas decimais               
                var valorNumericoBR =parseFloat(valor).toLocaleString('pt-BR', { maximumFractionDigits: 2 });
                return valorNumericoBR; 
            },
            mostraNomeArquivo(arquivo){
                if (arquivo != "" && arquivo != null) {
                    const prefixo = "tabela_preco/prc/";                
                    return arquivo.replace(prefixo, "");
                }
                return ''
            },

            async apagarArquivo(codComp, nomeArquivo, campo){

                let baseUrl = apiURL; // apiURL é definida em app.blade.php
                let url = baseUrl + '/api/v1/personalizada/importar/prc/apagar_arquivo';

                // Obtém o token do cookie
                const token = this.getCookie('token');

                let configuracao = {
                    headers: {
                        'Authorization': `Bearer ${token}`, // Adiciona o token JWT ao cabeçalho 
                        'cod_comp' : codComp,
                        'nome_arquivo': nomeArquivo,
                        'campo': campo
                    }
                };

                try {
                    // console.log(codComp)
                    // console.log(nomeArquivo)
                    // console.log(campo)
                    const response = await axios.delete(url, configuracao);

                    this.exibirMensagemSistema('sucesso', 'Arquivo apagado com sucesso!',1300);
                    //console.log(response.data)
                    await this.montarTabela()
                    
                    // atualizando o item selecionado
                    this.itemPrecoSelecionado[campo] = ''
                    this.itemPrecoBackup = this.itemPrecoSelecionado
                    
                    //return response.data;
                } catch (error) {
                    console.error('Error:', error);
                    return null;
                }
            }

        },
        watch: {
            
        },
        mounted() {
            window.addEventListener('scroll',this.handleScroll);
            this.iniciarEditor()
            this.montarTabela()            
        }
    }
</script>

<style scoped>
.sticky{
    background-color: #5e5e5e!important; /* Nova cor quando fixo no topo */
    color:#fff!important;
} 
.tbl_item thead{
    transition: background-color 0.3s;  
}
.tudo{
    width: 98%;
    margin: auto;
    background-color: #fff;    
}
.tbl_item{
    width: 100%;
    font-size: 14px;
}

.tbl_item_cabecalho{
    position: sticky;
    top: 0;
    z-index: 1; /* Certifique-se de que o cabeçalho fique acima das outras linhas */
    background-color: #fff;
    border-bottom:2px solid #000;

}
.tbl_item_linha:hover{
    background-color: #f1f1f1;
}
.tbl_item thead tr td{
    text-transform:uppercase;
    padding: 10px;
    /* border: 1px solid #c4c4c4; */
    font-weight: 500;
    text-align: center;
}
.tbl_item tbody tr td{
    border: 1px solid #e9e9e9;
    padding: 10px;
}

.tbl_item tbody{
    border-top: 2px solid #000;
}
.tbl_item tfoot{
    border-top: 2px solid #000;
}

.tbl_item tfoot tr td{
    border: 1px solid #e9e9e9;
    padding: 10px;
}


.comp-detalhe-container{
    padding: 10px; 
    position: relative;
    background-color: #fff;   
}
.comp-detalhe-tit{
    font-weight: 500;
}
.comp-detalhe-anexos{
    width: 45%;
    border-left: 1px dashed #ececec;
    padding-left: 10px;
}
.comp-detalhe-detalhe{
    border-left: 1px dashed #f1f1f1;
    padding-left: 10px;
    margin-left: 20px;
    padding-right: 10px;
    width: 70%;
}

.tbl-acoes{
    border-top: 0px!important;
    border-right: 0px!important;
    border-bottom: 0px!important;
    background-color: #fff!important;
}
.acoes-icon{
    margin:5px;
}
.acoes-icon:hover{
    color: rgb(88, 136, 180);
}

#editItem:hover, #mostraDetalheItem:hover{
    cursor: pointer;
}
.mess_error_frm{
    border: solid 1px rgba(255, 0, 0, 0.13);
    margin: 1px 1px 10px 1px;
    padding: 10px;
    border-radius: 10px;
    color: red;
    background-color: rgba(235, 45, 45, 0.027);
}
.corFonteErro{
    color: red!important;
}
.ico_del_arq {
    color: rgb(58, 58, 58);
}

.ico_del_arq:hover {
    cursor: pointer;
    color: red;
}
.lnk_arq {
    text-decoration: none;
    color: #699;
}
.lnk_arq:hover {
    text-decoration: underline;
    color: #000;
}


</style>

