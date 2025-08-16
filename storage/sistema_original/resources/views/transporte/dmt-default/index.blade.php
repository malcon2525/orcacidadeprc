@extends('layouts.app')

@section('content')
    <div id="app">
        <lista-dmt-default></lista-dmt-default>
    </div>
@endsection

@push('scripts')
    <script>
        window.app = new Vue({
            el: '#app'
        });
    </script>
@endpush 