@extends('layouts.app')

@section('content')

<div class="container">
    <div class="tudo fundo-imagem">
        {{-- <img src="{{ asset('/assets/images/prototipo/inicio.png') }}" alt="inicio"> --}}
        <div class="tela">
            <div class="linha1">
                <div class="linha11"></div>
                <div class="linha12">
                    <div></div>
                    <a href="{{ route('projeto.fluxo') }}"></a>
                    <a href="{{ route('projeto.cotacao') }}"></a>
                    <a href="{{ route('projeto.composicao') }}"></a>
                </div>
            </div>
            <a href="{{ route('inicio') }}"><div class="linha2"></div></a>
            <a href=""><div class="linha3"></div></a>
            <a href=""><div class="linha4"></div></a>
         
            
        </div>

    </div>

</div>

    {{-- <consulta-derpr-component csrf_token = "{{ @csrf_token() }}"></consulta-derpr-component> --}}

@endsection

<style scoped>
    .linha1{
        height: 850px;
        #background: #77777754;
    }

    .linha11{
        height: 700px;
        #background: #11a71854;
    }

    .linha12{
        display: flex
    }
    .linha12 div{
        width: 230px;
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
        height: 200px;
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
        height: 1500px!important;
        #background: #989
    }
    .fundo-imagem {
            /* Definindo a imagem de fundo */
            background-image: url('{{ asset('/assets/images/prototipo/projeto-cadastro.png') }}');
            
            /* Outros estilos opcionais */
            #background-size: auto; /* Faz a imagem cobrir todo o fundo */
            background-position: top center; /* Centraliza a imagem */
            background-repeat: no-repeat; /* Impede a repetição da imagem */
            width: 100%;
            #height: 100%; /* Ajuste conforme necessário */
        }
</style>