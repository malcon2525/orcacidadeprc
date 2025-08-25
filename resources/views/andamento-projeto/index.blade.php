@extends('layouts.app')

@section('title', 'Andamento do Projeto - OrçaCidade')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/andamento-projeto.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/js/andamento-projeto.js') }}"></script>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="card-title titulo-relatorio d-flex justify-content-center align-items-center gap-2">
                        <i class="fas fa-chart-line"></i>
                        Andamento do Projeto OrçaCidade
                    </h1>
                    <p class="card-text text-muted">
                        Acompanhe o progresso, sprints, reuniões, documentação e relatórios do projeto.
                    </p>
                </div>
            </div>
        </div>
    </div>
    @include('andamento-projeto._menu')
    <div class="tab-content" id="projectTabsContent">
        <div class="tab-pane fade show active" id="visao" role="tabpanel">
            <div class="text-center py-5"><h2 class="text-muted">Bem-vindo!</h2></div>
        </div>
    </div>
</div>
@endsection 