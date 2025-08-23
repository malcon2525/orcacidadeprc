@extends('layouts.app')

@section('content')
<div class="modern-interface">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="content-wrapper">
                    <div class="content-body">
                        <consultar-sinapi-component :permissoes="{{ json_encode($permissoes) }}"></consultar-sinapi-component>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
