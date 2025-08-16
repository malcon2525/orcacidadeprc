<!-- Menu de Consultas -->
<li class="nav-item">
    <a class="nav-link" data-bs-toggle="collapse" href="#consultas" role="button" aria-expanded="false" aria-controls="consultas">
        <i class="fas fa-search"></i>
        <span class="menu-title">Consultas</span>
        <i class="menu-arrow"></i>
    </a>
    <div class="collapse" id="consultas">
        <ul class="nav flex-column sub-menu">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('derpr.consultar') }}">DER-PR</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('sinapi.consultar') }}">SINAPI</a>
            </li>
        </ul>
    </div>
</li> 