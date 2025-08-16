@extends('layouts.app')

@section('content')

<div class="container">
   <home-component></home-component>
</div>

@endsection

@push('scripts')
<script>
    // Configuração global da API
    window.apiURL = '{{ config('app.url') }}';
</script>
@endpush

