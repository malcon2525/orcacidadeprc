@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <lista-municipios 
        :permissoes='@json($permissoes ?? ["crud" => true, "consultar" => true, "importar" => true])'>
    </lista-municipios>
</div>
@endsection
