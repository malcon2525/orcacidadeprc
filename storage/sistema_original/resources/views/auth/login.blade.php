@extends('layouts.app')

@section('content')
<div id="app">
    <login-component :csrf_token="'{{ csrf_token() }}'"></login-component>
</div>
@endsection

@push('scripts')
<script>
    // Configuração global da API
    window.apiURL = '{{ config('app.url') }}';
</script>
@endpush
