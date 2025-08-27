@extends('layouts.app')

@section('title', 'Aprovação de Cadastros')

@section('content')
<!-- Componente Vue.js será montado aqui -->
<div id="app">
    <lista-aprovacao-cadastros :permissoes="{{ json_encode($permissoes ?? []) }}"></lista-aprovacao-cadastros>
</div>
@endsection


