@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <gestao-estrutura-orcamento :permissoes="{{ json_encode($permissoes) }}"></gestao-estrutura-orcamento>
</div>
@endsection
