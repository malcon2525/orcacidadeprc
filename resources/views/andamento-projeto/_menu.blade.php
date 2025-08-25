<div class="orcacidade-tabs">
    <a href="{{ route('andamento-projeto.escopo') }}" class="orcacidade-tab-link{{ request()->routeIs('andamento-projeto.escopo') ? ' active' : '' }}">
        <i class="fas fa-bullseye"></i> Escopo
    </a>
    {{-- <a href="{{ route('andamento-projeto.backlog-global') }}" class="orcacidade-tab-link{{ request()->routeIs('andamento-projeto.backlog-global') ? ' active' : '' }}">
        <i class="fas fa-list-check"></i> Backlog Global
    </a> --}}
    <a href="{{ route('andamento-projeto.fases-e-sprints') }}" class="orcacidade-tab-link{{ request()->routeIs('andamento-projeto.fases-e-sprints') ? ' active' : '' }}">
        <i class="fas fa-layer-group"></i> Fases e Sprints
    </a>
    <a href="{{ route('andamento-projeto.conceitos') }}" class="orcacidade-tab-link{{ request()->routeIs('andamento-projeto.conceitos') ? ' active' : '' }}">
        <i class="fas fa-lightbulb"></i> Conceitos
    </a>
    <a href="{{ route('documentacao.index') }}" class="orcacidade-tab-link{{ request()->routeIs('documentacao.index') ? ' active' : '' }}">
        <i class="fas fa-file-alt"></i> Documentação
    </a>
    <a href="/andamento-projeto/relatorios" class="orcacidade-tab-link{{ request()->is('andamento-projeto/relatorios') ? ' active' : '' }}">
        <i class="fas fa-chart-bar"></i> Relatórios
    </a>
</div> 