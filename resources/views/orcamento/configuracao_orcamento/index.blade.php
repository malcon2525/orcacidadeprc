@extends('layouts.app')

@section('title', 'Configuração Orçamento')

@section('content')
<div class="container-fluid px-4">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <!-- Cabeçalho Compacto -->
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                <i class="fas fa-cog me-2"></i>Configuração de Contexto Orçamentário
            </h6>
        </div>

        <!-- Corpo -->
        <div class="card-body">
            <!-- Componente Vue.js -->
            <configuracao-contexto-orcamento></configuracao-contexto-orcamento>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Dados do usuário autenticado para o componente Vue
window.userData = {
    id: {{ auth()->user()->id }},
    name: '{{ auth()->user()->name }}',
    email: '{{ auth()->user()->email }}'
};
</script>
@endsection
