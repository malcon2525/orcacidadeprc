@extends('layouts.app')

@section('content')
    <div id="app">
        <lista-materiais-transporte></lista-materiais-transporte>
    </div>
@endsection

@push('scripts')
    <script>
        window.app = new Vue({
            el: '#app'
        });
    </script>
@endpush 