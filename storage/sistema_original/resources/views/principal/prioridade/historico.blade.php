@extends('layouts.app')

@section('content')

<div class="container">
    <div class="tudo fundo-imagem">
        {{-- <img src="{{ asset('/assets/images/prototipo/do.png') }}" alt="inicio"> --}}
        <div class="tela">
            <div class="linha1"></div>
            <div class="linha12">
                <div></div>
                <a href="{{ route('prioridade.create') }}"></a>
                <a href="{{ route('prioridade.documentacao') }}"></a>
                <a href="{{ route('prioridade.historico') }}"></a>
            </div>
            <a href="{{ route('prioridade.create') }}"><div class="linha2"></div></a>
            <a href=""><div class="linha3"></div></a>
            <a href=""><div class="linha4"></div></a>
        </div>
    </div>
</div>

    {{-- <consulta-derpr-component csrf_token = "{{ @csrf_token() }}"></consulta-derpr-component> --}}

@endsection

<style scoped>
    .linha1{
        height: 170px;
        #background: #77777754;
    }

    .linha12{
        display: flex
    }
    .linha12 div{
        width: 260px;
        #background: #000;
    }
    .linha12 a{
        display:inline-block;
        width: 120px;
        #background: rgba(255, 0, 0, 0.301);
        #border: 2px solid #000;
        height: 100px;
    }



    .linha2{
        height: 600px;
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
            background-image: url('{{ asset('/assets/images/prototipo/prioridade-historico.jpg') }}');
            
            /* Outros estilos opcionais */
            #background-size: auto; /* Faz a imagem cobrir todo o fundo */
            background-position: top center; /* Centraliza a imagem */
            background-repeat: no-repeat; /* Impede a repetição da imagem */
            width: 100%;
            #height: 100%; /* Ajuste conforme necessário */
        }
</style>