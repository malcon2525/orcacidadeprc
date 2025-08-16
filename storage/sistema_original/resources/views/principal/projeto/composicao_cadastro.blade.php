@extends('layouts.app')

@section('content')

<div class="containers">
    <div class="tudo fundo-imagem">
        {{-- <img src="{{ asset('/assets/images/prototipo/inicio.png') }}" alt="inicio"> --}}
        <div class="tela">
            <div class="linha1">
                <div class="linha11"></div>
                <div class="linha12">
                    <div></div>
                    <a id="add" href=""></a>
                    <a href=""></a>
                    <a href=""></a>
                </div>
            </div>
            <a href="{{ route('projeto.composicao') }}"><div class="linha2"></div></a>
            <a href="{{ route('projeto.composicao') }}"><div class="linha3"></div></a>
            <a href=""><div class="linha4"></div></a>
         
            
        </div>

    </div>

</div>

<div id="add_comp">
    <div>

    </div>
    
</div>

    {{-- <consulta-derpr-component csrf_token = "{{ @csrf_token() }}"></consulta-derpr-component> --}}

@endsection



<style scoped>
    .linha1{
        height: 550px;
        #background: #77777754;
    }

    .linha11{
        height: 390px;
        #background: #11a71854;
    }

    .linha12{
        display: flex
    }
    .linha12 div{
        width: 350px;
        #background: #000;
    }
    .linha12 a{
        display:inline-block;
        width: 400px;
        #background: rgba(255, 0, 0, 0.301);
        #border: 2px solid #000;
        height: 150px;
    }


    .linha2{
        height: 110px;
        #background: #d63eca2d;
    }
    .linha3{
        height: 100px;
        #background: #483ed62d;
    }
    .linha4{
        height: 100px;
        #background: #43d63e2d;
    }
    .tudo{
        width: 1700px!important;
        height: 1500px!important;
        #background: #989
    }
    .fundo-imagem {
            /* Definindo a imagem de fundo */
            background-image: url('{{ asset('/assets/images/prototipo/projeto-composicao-cadastro.png') }}');
            
            /* Outros estilos opcionais */
            #background-size: auto; /* Faz a imagem cobrir todo o fundo */
            background-position: top center; /* Centraliza a imagem */
            background-repeat: no-repeat; /* Impede a repetição da imagem */
            width: 100%;
            #height: 100%; /* Ajuste conforme necessário */
        }

    #add_comp{
        background: #00000054;
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0px;
          
        display:none; 
        
    }
    #add_comp div{
        /* Definindo a imagem de fundo */
        background-image: url('{{ asset('/assets/images/prototipo/projeto-composicao-cadastro-modal.png') }}');
        background-position: top center; /* Centraliza a imagem */
        background-repeat: no-repeat; /* Impede a repetição da imagem */
        background-size: 1000px; /* Define o tamanho exato da imagem */
        width: 100%;
        height: 550px;
          
        
    }
</style>

<script>
   document.addEventListener('DOMContentLoaded', function () {
        let link = document.getElementById('add');
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Evita comportamento padrão do link

            const minhaDiv = document.querySelector('#add_comp');
            minhaDiv.style.display ='block';

            minhaDiv.addEventListener('click', function (event) {
                minhaDiv.style.display = 'none';

            });
        });
    });
</script>