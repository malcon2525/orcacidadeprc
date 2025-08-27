@extends('layouts.app')

@section('title', 'Usuários por Entidade')

@section('content')
<!-- Componente Vue.js será montado aqui -->
<div id="app">
    <lista-usuarios-por-entidade :permissoes="{{ json_encode($permissoes ?? []) }}"></lista-usuarios-por-entidade>
</div>
@endsection