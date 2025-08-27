@extends('layouts.app')

@section('title', 'Composições Próprias')

@section('content')
<div class="container-fluid px-4">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                <i class="fas fa-cogs me-2"></i>Composições Próprias
            </h6>
        </div>
        <div class="card-body">
            <!-- Componente Vue.js -->
            <lista-composicao-propria :permissoes="{{ json_encode($permissoes) }}"></lista-composicao-propria>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Dados passados do controller para o componente Vue
    window.permissoesComposicaoPropria = @json($permissoes);
</script>
@endpush
