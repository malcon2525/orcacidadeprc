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
                    {{-- <a href="{{ route('lote.create') }}"></a> --}}
                    <a href="{{ route('lote-municipio.create') }}"></a>
                    <a href="{{ route('lote-prc.create') }}"></a>
                    <a href="{{ route('lote-analise.create') }}"></a>
                    <a href="{{ route('lote-conclusao.create') }}"></a>
                </div>
            </div>




            <div class="linha2">
                <div class="linha21"></div>
                <div class="linha22">
                    <div></div>
                    <a href=""></a>
                    <a href=""></a>
                    <a href=""></a>
                </div>
            </div>

            <a href="{{ route('inicio') }}"><div class="linha3"></div></a>

            <div class="linha4">
                <div class="linha41"></div>
                <div class="linha42">
                    <div></div>
                    <a href=""></a>
                    <a href="{{ route('orcamento.inicio') }}"></a>
                    <a href=""></a>
                    <a href=""></a>
                </div>
            </div>

            
         
            
        </div>

    </div>

</div>

    {{-- <consulta-derpr-component csrf_token = "{{ @csrf_token() }}"></consulta-derpr-component> --}}

@endsection

<style scoped>
    .linha1{
        height: 200px;
        #background: #77777754;
        display: flex;
    }

        .linha11{
            height: 180px;
            width: 150px;
            #background: #11a71854;
        }

        .linha12{
            display: flex
        }
        .linha12 div{
            width: 20px;
            #background: #000;
        }
        .linha12 a{
            display:inline-block;
            width: 120px;
            #background: rgba(255, 0, 0, 0.301);
            #border: 2px solid #000;
            height: 100%;
        }



    .linha2{
        height: 760px;
        #background: #d63eca2d;
    }

        .linha21{
            height: 5px;
            #background: #11a71854;
        }

        .linha22{
            display: flex
        }
        .linha22 div{
            width: 230px;
            #background: #000;
        }
        .linha22 a{
            display:inline-block;
            width: 105px;
            #background: rgba(255, 0, 0, 0.301);
            #border: 2px solid #000;
            height: 150px;
        }


    .linha3{
        height: 100px;
        #background: #43d63e2d;
    }



    .linha4{
        height: 300px;
        #background: #483ed62d;
        display: flex
    }

        .linha41{
            height: 100%;
            width: 300px;
            #background: #060e0654;
        }

        .linha42{
            display: flex
        }
        .linha42 div{
            width: 10px;
            #background: #000;
        }
        .linha42 a{
            display:inline-block;
            width: 105px;
            #background: rgba(255, 0, 0, 0.301);
            #border: 2px solid #000;
            height: 100%;
        }



    
    .tudo{
        height: 1800px!important;
        #background: #989
    }
    .fundo-imagem {
            /* Definindo a imagem de fundo */
            background-image: url('{{ asset('/assets/images/prototipo/lote-cadastro-analise.png') }}');
            
            /* Outros estilos opcionais */
            #background-size: auto; /* Faz a imagem cobrir todo o fundo */
            background-position: top center; /* Centraliza a imagem */
            background-repeat: no-repeat; /* Impede a repetição da imagem */
            width: 100%;
            #height: 100%; /* Ajuste conforme necessário */
        }
</style>