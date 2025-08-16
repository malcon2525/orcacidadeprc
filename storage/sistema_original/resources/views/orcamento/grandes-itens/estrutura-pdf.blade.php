<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estrutura de Orçamentos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .tipo-orcamento {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .grande-item {
            font-size: 14px;
            margin-left: 20px;
            margin-top: 10px;
            color: #34495e;
        }
        .subgrupo {
            font-size: 12px;
            margin-left: 40px;
            color: #7f8c8d;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Estrutura de Orçamentos</h1>
        <p>Data de geração: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <h2>Tipos de Orçamento Ativos</h2>
    @forelse($tiposAtivos as $tipo)
        <div class="tipo-orcamento">
            {{ $tipo->descricao }} <span style="font-size:12px; color:#16a085;">(Versão: {{ $tipo->versao }}, Ativo)</span>
        </div>
        @foreach($tipo->grandesItens as $grandeItem)
            <div class="grande-item">
                {{ $grandeItem->ordem }}. {{ $grandeItem->descricao }}
            </div>
            @foreach($grandeItem->subGrupos as $subGrupo)
                <div class="subgrupo">
                    {{ $grandeItem->ordem }}.{{ $subGrupo->ordem }} {{ $subGrupo->descricao }}
                </div>
            @endforeach
        @endforeach
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @empty
        <p>Nenhum tipo de orçamento ativo encontrado.</p>
    @endforelse

    <h2 style="margin-top:40px;">Tipos de Orçamento Inativos</h2>
    @forelse($tiposInativos as $tipo)
        <div class="tipo-orcamento">
            {{ $tipo->descricao }} <span style="font-size:12px; color:#c0392b;">(Versão: {{ $tipo->versao }}, Inativo)</span>
        </div>
        @foreach($tipo->grandesItens as $grandeItem)
            <div class="grande-item">
                {{ $grandeItem->ordem }}. {{ $grandeItem->descricao }}
            </div>
            @foreach($grandeItem->subGrupos as $subGrupo)
                <div class="subgrupo">
                    {{ $grandeItem->ordem }}.{{ $subGrupo->ordem }} {{ $subGrupo->descricao }}
                </div>
            @endforeach
        @endforeach
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @empty
        <p>Nenhum tipo de orçamento inativo encontrado.</p>
    @endforelse
</body>
</html> 