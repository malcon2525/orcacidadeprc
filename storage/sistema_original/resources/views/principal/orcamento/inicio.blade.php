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
                    <a href="{{ route('orcamento.inicio') }}"></a>
                    <a href="{{ route('orcamento.bdi') }}"></a>
                    <a href="{{ route('orcamento.dmt') }}"></a>
                    <a href="{{ route('orcamento.planilha') }}"></a>
                    <a href="{{ route('orcamento.resumo') }}"></a>
                </div>
            </div>
            <a href=""><div class="linha2"></div></a>


            <div class="linha3">
                <div class="linha31"></div>
                <div class="linha32">
                    <div></div>
                    {{-- <a href="{{ route('derpr', ['data_base' => '']) }}"></a> --}}
                    <a href="{{ route('derpr', ['data_base' => '']) }}"></a>
                    <a href=""></a>
                    <a href=""></a>
                </div>
            </div>

            <a href="{{ route('lote-municipio.create') }}"><div class="linha4"></div></a>
         
            
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
            height: 150px;
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
        height: 950px;
        #background: #ff00802d;
    }
    .linha3{
        height: 200px;
        #background: #483ed62d;
    }

        .linha31{
            height: 45px;
            #background: #a7118254;
        }
        .linha32{
            display: flex
        }
        .linha32 div{
            width: 230px;
            #background: #000;
        }
        .linha32 a{
            display:inline-block;
            width: 100px;
            #background: rgba(255, 0, 0, 0.301);
            #border: 2px solid #000;
            height: 150px;
        }


    .linha4{
        height: 100px;
        #background: #43d63e2d;
    }
    .tudo{
        height: 2500px!important;
        #background: #989
    }
    .fundo-imagem {
            /* Definindo a imagem de fundo */
            background-image: url('{{ asset('/assets/images/prototipo/orcamento-inicio.png') }}');
            
            /* Outros estilos opcionais */
            #background-size: auto; /* Faz a imagem cobrir todo o fundo */
            background-position: top center; /* Centraliza a imagem */
            background-repeat: no-repeat; /* Impede a repetição da imagem */
            width: 100%;
            #height: 100%; /* Ajuste conforme necessário */
        }
</style>