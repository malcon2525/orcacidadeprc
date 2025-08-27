@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
<div id="app">
    <lista-meu-perfil :permissoes="{{ json_encode($permissoes ?? []) }}"></lista-meu-perfil>
</div>
@endsection
