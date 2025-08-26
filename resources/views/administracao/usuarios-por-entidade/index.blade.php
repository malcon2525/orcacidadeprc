@extends('layouts.app')

@section('title', 'Usuários por Entidade')

@section('content')
<div class="container-fluid px-4">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold" style="color: #5EA853; font-size: 1.2rem; padding: 5px 0;">
                <i class="fas fa-users-cog me-2"></i>Usuários por Entidade Orçamentária
            </h6>
        </div>
        <div class="card-body">
            <!-- Componente Vue.js será montado aqui -->
            <div id="usuarios-por-entidade-app">
                <lista-usuarios-por-entidade></lista-usuarios-por-entidade>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    import { createApp } from 'vue';
    import ListaUsuariosPorEntidade from '@/components/administracao/usuarios-por-entidade/ListaUsuariosPorEntidade.vue';

    // Criar aplicação Vue específica para esta página
    const app = createApp({
        components: {
            ListaUsuariosPorEntidade
        }
    });

    // Montar no elemento específico
    app.mount('#usuarios-por-entidade-app');
</script>
@endpush
