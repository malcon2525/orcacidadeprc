<template>
   <div class="container" @keydown.ctrl.s.prevent="gravarBDI($event)">
        <div class="row justify-content-center">
            <div class="col-md-12">
                
                <!-- titulo do formulário -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-titulo">
                                <h1>Dados para cálculo do BDI</h1>
                            </div>
                    </div>
                </div>
                <!-- conteúdo do formulário -->
                <div class="row" >

                    <!-- coluna que contém os campos imputs   -->
                    <div class="col-md-9">
                        <div class="form-container" >
                            
                            <div class="form-conteudo" >
                                <form method="POST" action="" @submit.prevent="gravarBDI($event)" >
                                    <div >
                                        <input type="hidden" name="_token" :value="csrf_token">
                                        <!-- {{ dadosBdiUsuario  }}  -->
                                        <div class="row">
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2 pb-1"><strong>Impostos(%)</strong></div>
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2 pb-1"><strong>Obras(%)</strong></div>
                                            <div class="col-md-2 pb-1"><strong>Materiais(%)</strong></div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-1 text-end pt-2">ISS</div>

                                            <div class="col-md-2"><input  class="form-control mb-2" type="text"  name="iss" id="iss"  :value="formatarValorParaBR(dadosBdiUsuario.iss)" ref="iss" @keydown="validarTeclaNumerica($event)" @blur="formatarValorParaUS($event.target.value, 'iss')" ></div>


                                            <div class="col-md-3 text-end pt-2 ">ADM. CENTRAL</div>

                                            <div class="col-md-2"><input  class="form-control mb-2" type="text" name="obras_adm_central" id="obras_adm_central" :value="formatarValorParaBR(dadosBdiUsuario.obras_adm_central)" ref="obras_adm_central" @keydown="validarTeclaNumerica($event)"  @blur="formatarValorParaUS($event.target.value, 'obras_adm_central')" ></div>


                                            <div class="col-md-2"><input  class="form-control mb-2" type="text" name="material_adm_central" id="material_adm_central" :value="formatarValorParaBR(dadosBdiUsuario.material_adm_central)" ref="material_adm_central" @keydown="validarTeclaNumerica($event)"  @blur="formatarValorParaUS($event.target.value, 'material_adm_central')" ></div>

                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-md-1 text-end pt-2">PIS</div>

                                            <div class="col-md-2"><input  class="form-control mb-2" type="text" name="pis" id="pis" :value="formatarValorParaBR(dadosBdiUsuario.pis)" ref="pis" @keydown="validarTeclaNumerica($event)"  @blur="formatarValorParaUS($event.target.value, 'pis')" ></div>

                                            
                                            <div class="col-md-3 text-end pt-2 ">RISCOS</div>

                                            <div class="col-md-2"><input  class="form-control mb-2" type="text" name="obras_riscos" id="obras_riscos" :value="formatarValorParaBR(dadosBdiUsuario.obras_riscos)" ref="obras_riscos" @keydown="validarTeclaNumerica($event)"  @blur="formatarValorParaUS($event.target.value, 'obras_riscos')" ></div>
                                            
                                            

                                            <div class="col-md-2"><input  class="form-control mb-2" type="text" name="material_riscos" id="material_riscos" :value="formatarValorParaBR(dadosBdiUsuario.material_riscos)" ref="material_riscos" @keydown="validarTeclaNumerica($event)"  @blur="formatarValorParaUS($event.target.value, 'material_riscos')" ></div>

                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-md-1 text-end pt-2">COFINS</div>

                                            <div class="col-md-2"><input  class="form-control mb-2" type="text" name="cofins" id="cofins" :value="formatarValorParaBR(dadosBdiUsuario.cofins)" ref="cofins" @keydown="validarTeclaNumerica($event)"  @blur="formatarValorParaUS($event.target.value, 'cofins')" ></div>

                                            <div class="col-md-3 text-end pt-2 ">SEG/GARANTIA</div>

                                            <div class="col-md-2"><input  class="form-control mb-2" type="text" name="obras_seguros_garantias" id="obras_seguros_garantias" :value="formatarValorParaBR(dadosBdiUsuario.obras_seguros_garantias)" ref="obras_seguros_garantias" @keydown="validarTeclaNumerica($event)"  @blur="formatarValorParaUS($event.target.value, 'obras_seguros_garantias')" ></div>

                                            <div class="col-md-2"><input  class="form-control mb-2" type="text" name="material_seguros_garantias" id="material_seguros_garantias" :value="formatarValorParaBR(dadosBdiUsuario.material_seguros_garantias)" ref="material_seguros_garantias" @keydown="validarTeclaNumerica($event)"  @blur="formatarValorParaUS($event.target.value, 'material_seguros_garantias')" ></div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-1 text-end pt-2">CPRB</div>

                                            <div class="col-md-2"><input  class="form-control mb-2" type="text" name="cprb" id="cprb" :value="formatarValorParaBR(dadosBdiUsuario.cprb)" ref="cprb" @keydown="validarTeclaNumerica($event)"  @blur="formatarValorParaUS($event.target.value, 'cprb')" ></div>

                                            <div class="col-md-3 text-end pt-2 ">DESPESAS</div>

                                            <div class="col-md-2"><input  class="form-control mb-2" type="text" name="obras_despesas_financeiras" id="obras_despesas_financeiras" :value="formatarValorParaBR(dadosBdiUsuario.obras_despesas_financeiras)" ref="obras_despesas_financeiras" @keydown="validarTeclaNumerica($event)"  @blur="formatarValorParaUS($event.target.value, 'obras_despesas_financeiras')" ></div>

                                            <div class="col-md-2"><input  class="form-control mb-2" type="text" name="material_despesas_financeiras" id="material_despesas_financeiras" :value="formatarValorParaBR(dadosBdiUsuario.material_despesas_financeiras)" ref="material_despesas_financeiras" @keydown="validarTeclaNumerica($event)"  @blur="formatarValorParaUS($event.target.value, 'material_despesas_financeiras')" ></div>

                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-md-1 text-end pt-2"></div>
                                            <div class="col-md-2"></div>
                                            <div class="col-md-3 text-end pt-2 ">LUCRO</div>

                                            <div class="col-md-2"><input  class="form-control mb-2" type="text" name="obras_lucro" id="obras_lucro" :value="formatarValorParaBR(dadosBdiUsuario.obras_lucro)" ref="obras_lucro" @keydown="validarTeclaNumerica($event)"  @blur="formatarValorParaUS($event.target.value, 'obras_lucro')" ></div>
                                            
                                            <div class="col-md-2"><input  class="form-control mb-2" type="text" name="material_lucro" id="material_lucro" :value="formatarValorParaBR(dadosBdiUsuario.material_lucro)" ref="material_lucro" @keydown="validarTeclaNumerica($event)"  @blur="formatarValorParaUS($event.target.value, 'material_lucro')" ></div>

                                        </div>
                                    </div>
                                    
                                </form>
                            </div>
                            
                
                        </div>
                        
                    </div>
                    <!-- coluna que apresetna o resultado do calculo do bdi -->
                    <div class="col-md-3">

                        <div class="form-container bdi-total-container">
                           <div class="bdi-total-card">
                                <div class="bdi-total-card-titulo">BDI OBRA</div>
                                <div class="bdi-total-card-valor"> <span id="total_bdi_obra">{{ formatarValorParaBR(dadosBdiUsuario.total_bdi_obra) }}</span> %</div>
                           </div>
                           <div class="bdi-total-card">
                                <div class="bdi-total-card-titulo">BDI MATERIAL</div>
                                <div class="bdi-total-card-valor"><span id="total_bdi_material">{{ formatarValorParaBR(dadosBdiUsuario.total_bdi_material) }}</span> %</div>
                           </div>
                        </div>
                    </div>
                </div>

                <!-- botões de ação do formulário -->
                <div class="row text-center">
                    <div class="col">
                        <button type="button" class="btn bnt-padrao btn-gravar mt-3" @click="gravarBDI()">Gravar</button>
                    </div>
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
                //propriedade que armazena os dados do DBI do usuário corrente
                dadosBdiUsuario: [],
                //propriedades nessárias para exibição de mensagem na tela
                mostrarMensagem: false,
                tipoMensagem: '',
                mensagem: ''
            }
        },
        computed: {
        
        },
        methods: {
            // Função para obter o valor de um cookie pelo nome
            getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
            },
            gravarBDI(){
                //console.log(this.dadosBdiUsuario)
                
                let formData = new FormData();


                formData.append('iss' , this.dadosBdiUsuario.iss)
                formData.append('cofins' , this.dadosBdiUsuario.cofins)
                formData.append('cprb' , this.dadosBdiUsuario.cprb)
                formData.append('pis' , this.dadosBdiUsuario.pis)
                formData.append('total_impostos' , this.dadosBdiUsuario.total_impostos)
                formData.append('obras_adm_central' , this.dadosBdiUsuario.obras_adm_central)
                formData.append('obras_riscos' , this.dadosBdiUsuario.obras_riscos)
                formData.append('obras_seguros_garantias' , this.dadosBdiUsuario.obras_seguros_garantias)
                formData.append('obras_despesas_financeiras' , this.dadosBdiUsuario.obras_despesas_financeiras)
                formData.append('obras_lucro' , this.dadosBdiUsuario.obras_lucro)
                formData.append('total_bdi_obra' , this.dadosBdiUsuario.total_bdi_obra)

                formData.append('material_adm_central' , this.dadosBdiUsuario.material_adm_central)
                formData.append('material_riscos' , this.dadosBdiUsuario.material_riscos)
                formData.append('material_seguros_garantias' , this.dadosBdiUsuario.material_seguros_garantias)
                formData.append('material_despesas_financeiras' , this.dadosBdiUsuario.material_despesas_financeiras)
                formData.append('material_lucro' , this.dadosBdiUsuario.material_lucro)
                formData.append('total_bdi_material' , this.dadosBdiUsuario.total_bdi_material)

                // outra forma de popular formData
                // Iterar sobre as chaves e valores de this.dadosBdiUsuario
                // for (const [key, value] of Object.entries(this.dadosBdiUsuario)) {
                //     // Adicionar cada par chave-valor ao FormData
                //     //console.log(`Adicionando ${key}: ${value} ao FormData`);
                //     formData.append(key, value);
                //     formData.append('teste', 'teset');
                // }

                //imprime os dados de formData
                // for (let pair of formData.entries()) {
                //     console.log(pair[0] + ', ' + pair[1]);
                // }

                let baseUrl = apiURL //apiURL é definida em app.blade.php;     
                let url = baseUrl + '/api/v1/bdi'

                // Obtém o token do cookie
                const token = this.getCookie('token');


                // configuração para a requisição Axios
                const config = {
                    headers: {
                        //_method: 'PUT', 
                        'Content-Type': 'multipart/form-data',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                    }
                };
                
                // Enviar a requisição 
                axios.post(url, formData, config)
                    .then(response => {
                        //console.log(response.data);
                    })
                    .then( 
                        this.exibirMensagemSistema('sucesso', 'Gravação realizada com sucesso')
                    )
                    .catch(error => {
                        this.exibirMensagemSistema('erro', 'Gravação não foi concluída')
                        console.error('Erro:', error);
                    });



                // const options = {
                // method: 'PUT',
                // headers: {
                //     'Content-Type' : 'multipart/form-data',
                //     'Accept': 'application/json',
                //     // Adiciona o token JWT ao cabeçalho 'Authorization'
                //     'Authorization': `Bearer ${token}`
                // },
                // body: formData // Utiliza o objeto FormData como corpo da requisição
                // };

                // //Realiza a requisição
                // fetch(url, options)
                // .then(response => {
                //     if (!response.ok) {
                //         throw new Error('Erro ao realizar a requisição.');
                //     }
                //     return response.json();
                // })
                // .then(data => {
                //     // Manipula a resposta da API conforme necessário
                //     console.log(data);
                // })
                // .catch(error => {
                //     console.error('Erro:', error);
                //     // Manipula o erro conforme necessário
                // });
                                
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
            calcularTotalBDI(){
                let totalBDIObra, totalBDIMaterial 

                let iss = parseFloat(this.dadosBdiUsuario.iss)
                let pis = parseFloat(this.dadosBdiUsuario.pis)
                let cofins = parseFloat(this.dadosBdiUsuario.cofins)
                let cprb = parseFloat(this.dadosBdiUsuario.cprb)

                let totalImpostos = iss + pis + cofins + cprb 

                //BDI OBRA
                let obras_adm_central = parseFloat(this.dadosBdiUsuario.obras_adm_central)
                let obras_riscos = parseFloat(this.dadosBdiUsuario.obras_riscos)
                let obras_seguros_garantias = parseFloat(this.dadosBdiUsuario.obras_seguros_garantias)
                let obras_despesas_financeiras = parseFloat(this.dadosBdiUsuario.obras_despesas_financeiras)
                let obras_lucro = parseFloat(this.dadosBdiUsuario.obras_lucro)
              
                totalBDIObra = (( ( ( (1+(obras_adm_central+obras_riscos+obras_seguros_garantias)/100) * (1+obras_despesas_financeiras/100) * (1+obras_lucro/100) ) / (1-totalImpostos/100) ) -1 ) * 100);

                totalBDIObra = totalBDIObra.toFixed(2)

                //BDI MATERIAL
                let material_adm_central = parseFloat(this.dadosBdiUsuario.material_adm_central)
                let material_riscos = parseFloat(this.dadosBdiUsuario.material_riscos)
                let material_seguros_garantias = parseFloat(this.dadosBdiUsuario.material_seguros_garantias)
                let material_despesas_financeiras = parseFloat(this.dadosBdiUsuario.material_despesas_financeiras)
                let material_lucro = parseFloat(this.dadosBdiUsuario.material_lucro)
                
                totalBDIMaterial = (( ( ( (1+(material_adm_central+material_riscos+material_seguros_garantias)/100) * (1+material_despesas_financeiras/100) * (1+material_lucro/100) ) / (1-(totalImpostos-this.dadosBdiUsuario.iss)/100) ) -1 ) * 100);

                totalBDIMaterial = totalBDIMaterial.toFixed(2)

                //atualizando na propriedade 'dadosBdiUsuario'
                this.dadosBdiUsuario.total_bdi_obra = totalBDIObra
                this.dadosBdiUsuario.total_bdi_material = totalBDIMaterial
            },
            exibirMensagemSistema(tipo, conteudo) {
                this.tipoMensagem = tipo === 'sucesso' ? 'sucesso' : 'erro';
                this.mensagem = conteudo;
                this.mostrarMensagem = true;
                setTimeout(() => {
                    this.mostrarMensagem = false;
                }, 3000); // Espera antes de recolher a mensagem
            },
            
           
        },


        mounted() {

            let baseUrl = window.location.origin;    
            let urlLogin = baseUrl + '/api/v1/bdi'

            // Obtém o token do cookie
            const token = this.getCookie('token');

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



<style>

/* bdi */
.bdi-total-container{
     display: flex;
     justify-content: center;
     flex-direction: column;
     align-items: center;
     height: 100%;
}
.bdi-total-card{
     border: 1px solid #5ea85344;
     border-radius: 10px;
     padding: 20px;
     margin-bottom: 10px;
     text-align: center;
     width: 180px;
     background-color: #5ea8530e;
}
.bdi-total-card-titulo{
     color:#5EA853;
     font-weight: 700;
}
.bdi-total-card-valor{
     font-size: 1.5em;
}

</style>




