@extends('layouts.app')

@section('title', 'Active Directory - Administração')

@section('content')
<div id="active-directory-app">
    <active-directory-main></active-directory-main>
</div>
@endsection

@section('scripts')
<script>
// Registrar componente Vue do Active Directory
if (typeof Vue !== 'undefined') {
    // O componente será registrado quando for criado
    console.log('Active Directory module loaded');
}
</script>
@endsection
