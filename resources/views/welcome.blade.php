<!DOCTYPE html>
<html lang="pt-br">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Oça Cidade - Home</title>

    <link rel="stylesheet" href="./assets/css/welcome.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    </head>
<body>


    

    <div class="foto_predio">
        <div class="tudo">
            <div class="container">

                <nav class="navbar navbar-expand-lg ">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="#">
                            <img src="./assets/images/logo-oc.png" alt=""><span>Orça</span>Cidade
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
            @if (Route::has('login'))
                        <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup" >
                            <div class="navbar-nav">
                    @auth
                                    <a class="nav-link" href="{{ route('home') }}">Dashboard</a>
                                    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    @else
                                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                                @endauth
                            </div>
                        </div>
                        @endif
                    </div>
                </nav>

                {{-- Mensagens de Status --}}
                @if (session('status'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error_type') === 'csrf_expired')
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Sessão Expirada:</strong> Sua sessão expirou por inatividade. Por favor, faça login novamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-flex flex-row justify-content-between flex-wrap">
                    
                    {{-- inicio coluna da esquerda --}}
                    <div class="ocapresentacao">
                        <div class="ocapresentacaot pb-2 pt-2"><h1>Sistema de Preços para Orçamento de Obras</h1></div>
                        <div class="ocapresentacaotd pb-5 pt-2">Orçamentos de Obras a partir de Bases Oficiais. Sistema voltado para técnicos municipais criarem suas planilhas de custos de obras.</div>
                        <div class="row g-2">
                            {{-- <div class="col-md-6">
                                <button type="button" class="btn btn-success ocbtn ocbtlogin mb-1 w-100" style="height: 80px; padding: 10px;" onclick="window.location.href='{{ route('login') }}';">Login</button>
                            </div> --}}
                            <div class="col-md-6">
                                <button type="button" class="btn btn-info ocbtn mb-1 w-100" style="height: 80px; padding: 10px;" onclick="window.location.href='{{ route('andamento-projeto.index') }}';">📊 Andamento<br>do Projeto</button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary ocbtn mb-1 w-100" style="height: 80px; padding: 10px;" onclick="window.location.href='{{ route('planilha-prototipo') }}';">📋 Protótipo<br>Planilha Orçamentária</button>
                            </div>
                        </div>
                    </div>

                    {{-- incio coluna da direita --}}
                    <div class="oclogostabelas">
                        <div class="occol2fontes">Fontes:</div>
                        <div class="oclcontainer">
                            <div class="oclimg"><a href="https://www.der.pr.gov.br/Pagina/Normas-e-Custos-Rodoviarios" target="blank"><img src="./assets/images/derpr2.png" alt=""></a></div>
                            <div class="ocltxt"> Departamento de Estrada e Rodagem - PR </div>
                        </div>
                        <div class="oclcontainer ">
                            <div class="oclimg"><a href="https://www.caixa.gov.br/site/Paginas/downloads.aspx#categoria_655" target="blank"><img src="./assets/images/sinap.jpg" alt=""></a></div>
                            <div class="ocltxt"> Sistema Nacional de Pesquisa de Custos e Índices da Construção Civil </div>
                        </div>
                        <div class="oclcontainer ">
                            <div class="oclimg"><a href="https://www.curitiba.pr.gov.br/conteudo/tabela-de-custos-smop/3297" target="blank"><img src="./assets/images/smop.png" alt=""></a></div>
                            <div class="ocltxt"> Prefeitura de Curitiba </div>
                        </div>
                    </div>
                </div>


     
            </div>
                </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Formulário de logout oculto -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    </body>
</html>