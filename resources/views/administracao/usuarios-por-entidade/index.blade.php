@extends('layouts.app')

@section('content')
<div id="app-usuarios-por-entidade">
    <lista-usuarios-por-entidade></lista-usuarios-por-entidade>
</div>
@endsection

@section('scripts')
<script>
    const { createApp } = Vue;
    createApp({
        components: {
            'lista-usuarios-por-entidade': window.ListaUsuariosPorEntidade
        }
    }).mount('#app-usuarios-por-entidade');
</script>
@endsection