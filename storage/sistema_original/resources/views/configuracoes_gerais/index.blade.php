@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 fw-semibold" style="color: #5EA853;">
        <i class="fas fa-cogs me-2"></i>Configurações Gerais do Usuário
    </h4>
    <div class="mb-3 text-muted">
        Usuário: <span class="fw-semibold">{{ $nomeUsuario }}</span>
    </div>
    <configuracao-geral-form></configuracao-geral-form>
</div>
@endsection 