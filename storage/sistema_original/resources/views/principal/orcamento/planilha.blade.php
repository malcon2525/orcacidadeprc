@extends('layouts.app')

@section('content')

<div class="containere box">
    <div class="tudo fundo-imagem">
        {{-- <img src="{{ asset('/assets/images/prototipo/inicio.png') }}" alt="inicio"> --}}
        <div class="tela">
            <div class="linha1">
                <div class="linha11"></div>
                <div class="linha12">
                    <div></div>
                    <a href="{{ route('orcamento.inicio') }}"></a>
                    <a href="{{ route('orcamento.bdi') }}"></a>
                    <a href="{{ route('orcamento.dmt') }}"></a>
                    <a href="{{ route('orcamento.planilha') }}"></a>
                    <a href="{{ route('orcamento.resumo') }}"></a>
                </div>
            </div>

            <div class="linha2">
                <div class="linha21"></div>
                <div class="linha22">
                    <div></div>
                    <a href=""></a>
                    <a id="detalhes_obj" href=""></a>
                    <a id="add_obj" href=""></a>
                </div>
            </div>

            <a id="add_composicao" href=""><div class="linha3"></div></a>


            <a href="{{ route('orcamento.inicio') }}"><div class="linha4"></div></a>
         
            
        </div>

    </div>

</div>

<div id="mostra_detalhe_objeto">

    <div>
        
    </div>   
</div>


<div id="adiciona_objeto">
    <div>
    </div>   
</div>


<div id="adiciona_composicao">
    <div>
    </div>   
</div>




    {{-- <consulta-derpr-component csrf_token = "{{ @csrf_token() }}"></consulta-derpr-component> --}}

@endsection

<style scoped>
    .linha1{
        height: 245px;
        #background: #77777754;
    }

        .linha11{
            height: 90px;
            #background: #11a71854;
        }

        .linha12{
            display: flex
        }
        .linha12 div{
            width: 5px;
            #background: #000;
        }
        .linha12 a{
            display:inline-block;
            width: 100px;
            #background: rgba(255, 0, 0, 0.301);
            #border: 2px solid #000;
            height: 150px;
        }


    .linha2{
        height: 80px;
        #background: #ff00802d;
    }

        .linha21{
            height: 5px;
            #background: #11a71854;
        }

        .linha22{
            display: flex
        }
        .linha22 div{
            width: 50px;
            #background: #000;
        }
        .linha22 a{
            #background: rgba(85, 63, 182, 0.301);
            #border: 2px solid #000;
            height: 50px;
        }
        .linha22 a:nth-child(2){
            width: 91px;
            #background: rgba(176, 194, 19, 0.678);
        }
        .linha22 a:nth-child(3){
            width: 120px;
        }
        .linha22 a:nth-child(4){
            width: 120px;
        }
     

    .linha3{
        height: 650px;
        #background: #483ed62d;
    }
    .linha4{
        height: 100px;
        #background: #43d63e2d;
    }
    .tudo{
        height: 1300px!important;
        overflow: auto;
        #background: #989
    }
    .box{
        width: 2000px!important;
    }
    .fundo-imagem {
            /* Definindo a imagem de fundo */
            background-image: url('{{ asset('/assets/images/prototipo/orcamento-planilha.png') }}');
            
            /* Outros estilos opcionais */
            #background-size: auto; /* Faz a imagem cobrir todo o fundo */
            background-position: top center; /* Centraliza a imagem */
            background-repeat: no-repeat; /* Impede a repetição da imagem */
            width: 100%;
            #height: 100%; /* Ajuste conforme necessário */
    }

    #mostra_detalhe_objeto{
        background: #00000054;
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0px;
          
        display:none; 
        
    }
    #mostra_detalhe_objeto div{
        /* Definindo a imagem de fundo */
        background-image: url('{{ asset('/assets/images/prototipo/orcamento_mostra_detalhe_objeto.png') }}');
        background-position: center 100px; /* Centraliza a imagem */
        background-repeat: no-repeat; /* Impede a repetição da imagem */
        background-size: 700px; /* Define o tamanho exato da imagem */
        width: 100%;
        height: 550px;
    }


    #adiciona_objeto{
        background: #00000054;
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0px;
          
        display:none; 
        
    }
    #adiciona_objeto div{
        /* Definindo a imagem de fundo */
        background-image: url('{{ asset('/assets/images/prototipo/orcamento_adiciona_objeto.png') }}');
        background-position: center 100px; /* Centraliza a imagem */
        background-repeat: no-repeat; /* Impede a repetição da imagem */
        background-size: 700px; /* Define o tamanho exato da imagem */
        width: 100%;
        height: 550px;
    }





    #adiciona_composicao{
        background: #00000054;
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0px;
          
        display:none; 
        
    }
    #adiciona_composicao div{
        /* Definindo a imagem de fundo */
        background-image: url('{{ asset('/assets/images/prototipo/orcamento_adiona_composicao.png') }}');
        background-position: center 400px; /* Centraliza a imagem */
        background-repeat: no-repeat; /* Impede a repetição da imagem */
        background-size: 1000px; /* Define o tamanho exato da imagem */
        width: 100%;
        height: 950px;
    }

</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let link = document.getElementById('detalhes_obj');
         link.addEventListener('click', function (event) {
             event.preventDefault(); // Evita comportamento padrão do link
 
             const minhaDiv = document.querySelector('#mostra_detalhe_objeto');
             minhaDiv.style.display ='block';
 
             minhaDiv.addEventListener('click', function (event) {
                 minhaDiv.style.display = 'none';
 
             });
         });
     });

     document.addEventListener('DOMContentLoaded', function () {
        let link = document.getElementById('add_obj');
         link.addEventListener('click', function (event) {
             event.preventDefault(); // Evita comportamento padrão do link
 
             const minhaDiv = document.querySelector('#adiciona_objeto');
             minhaDiv.style.display ='block';
 
             minhaDiv.addEventListener('click', function (event) {
                 minhaDiv.style.display = 'none';
 
             });
         });
     });


     document.addEventListener('DOMContentLoaded', function () {
        let link = document.getElementById('add_composicao');
         link.addEventListener('click', function (event) {
             event.preventDefault(); // Evita comportamento padrão do link
 
             const minhaDiv = document.querySelector('#adiciona_composicao');
             minhaDiv.style.display ='block';
 
             minhaDiv.addEventListener('click', function (event) {
                 minhaDiv.style.display = 'none';
 
             });
         });
     });

     
 </script>