@extends('layouts.app')

@section('title', 'Gerenciamento de Papéis - OrçaCidade')

@section('content')
<div class="container-fluid px-4">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                <i class="fas fa-user-tag me-2"></i>Gerenciamento de Papéis e Permissões
            </h6>
        </div>
        <div class="card-body">
            <!-- Componente Vue será renderizado aqui -->
            <div id="app">
                <lista-papeis></lista-papeis>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Registro do componente Vue
    app.component('lista-papeis', {
        template: `@include('components.administracao.papeis.ListaPapeis')`
    });
</script>
@endpush
@endsection
