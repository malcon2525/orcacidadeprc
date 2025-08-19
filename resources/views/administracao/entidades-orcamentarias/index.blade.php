@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <lista-entidades-orcamentarias-admin 
        :permissoes='@json($permissoes ?? ["crud" => true, "consultar" => true, "importar" => true])'>
    </lista-entidades-orcamentarias-admin>
</div>
@endsection
