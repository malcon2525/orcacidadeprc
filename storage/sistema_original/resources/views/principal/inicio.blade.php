@extends('layouts.app')

@section('content')

<div class="container">
    <div class="tudo fundo-imagem">
        {{-- <img src="{{ asset('/assets/images/prototipo/inicio.png') }}" alt="inicio"> --}}
        <div class="tela">
            <div class="linha1"></div>
            <div class="linha2">
                <div class="linha21"> </div>
                <div class="linha22">
                    <div></div>
                    <a href="{{ route('projeto.cotacao') }}"></a>
                    <a href="{{ route('projeto.composicao') }}"></a>
                    <a href=""></a>
                </div>
            </div>
            <a href="{{ route('lote-municipio.create') }}">
                <div class="linha3"></div>
            </a>
            <a href="{{ route('lote-municipio.create') }}">
                <div class="linha4"></div>
            </a>
            
            {{-- <a href="{{ route('lote.create') }}"><div class="linha2"></div></a>
            <a href="{{ route('projeto.create') }}"><div class="linha3"></div></a>
            <a href="{{ route('prioridade.create') }}"><div class="linha4"></div></a> --}}
         
            
        </div>

    </div>

</div>

    {{-- <consulta-derpr-component csrf_token = "{{ @csrf_token() }}"></consulta-derpr-component> --}}

@endsection

<style scoped>
    .linha1{
        height: 400px;
        #background: #312f2f75;
    }
    .linha2{
        height: 250px;
        #background: #5f3ed62d;
        
    }
        .linha21{
            height: 120px;
            width: 100%;
            #background: #15ad4354;
        }
        .linha22{
            display: flex;
            
        }
        .linha22 div{
            width: 180px;
            height: 50px;
            #background: #00000070;
        }
        .linha22 a{
            display:inline-block;
            width: 100px;
            #background: rgba(255, 0, 0, 0.301);
            #border: 2px solid #000;
            height: 130px;
        }
    .linha3{
        height: 260px;
        #background: #e5ff002d;
    }
    .linha4{
        height: 300px;
        #background: #43d63e2d;
    }
    .tudo{
        height: 1500px!important;
        #background: #989
    }
    .fundo-imagem {
            /* Definindo a imagem de fundo */
            background-image: url('{{ asset('/assets/images/prototipo/inicio.png') }}');
            
            /* Outros estilos opcionais */
            #background-size: auto; /* Faz a imagem cobrir todo o fundo */
            background-position: top center; /* Centraliza a imagem */
            background-repeat: no-repeat; /* Impede a repetição da imagem */
            width: 90%;
            #height: 100%; /* Ajuste conforme necessário */
        }
</style>