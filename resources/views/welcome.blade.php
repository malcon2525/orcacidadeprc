<!DOCTYPE html>
<html lang="pt-br">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Oça Cidade - Home</title>

    <link rel="stylesheet" href="./assets/css/welcome.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
                                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#modalSolicitarCadastro">
                                        <i class="fas fa-user-plus me-1"></i>Solicitar Cadastro
                                    </a>
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
                            </div>  --}}
                            
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

    <!-- Modal Solicitar Cadastro -->
    <div class="modal fade" id="modalSolicitarCadastro" tabindex="-1" aria-labelledby="modalSolicitarCadastroLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                                 <div class="modal-header custom-modal-header" style="background: linear-gradient(135deg, #18578A 0%, #5EA853 100%); color: white; border-bottom: none; padding: 1.5rem; border-radius: 0.5rem 0.5rem 0 0;">
                     <div class="d-flex align-items-center">
                         <div class="header-icon" style="width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 1.2rem; color: white;">
                             <i class="fas fa-user-plus"></i>
                         </div>
                         <h5 class="modal-title mb-0">
                             Solicitação de Cadastro
                         </h5>
                     </div>
                     <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
                 </div>
                                 <div class="modal-body" style="background: white; padding: 1.25rem 1.25rem 0 1.25rem;">
                     <div class="text-center mb-4">
                         <p class="text-muted">Preencha os dados abaixo para solicitar acesso ao sistema OrçaCidade</p>
                     </div>
                    
                                         <form id="formSolicitarCadastro" method="POST" action="/api/publico/solicitar-cadastro" class="needs-validation" novalidate>
                        @csrf
                        <div class="row g-3">
                                                         <!-- Dados Pessoais -->
                             <div class="col-12">
                                 <h6 class="fw-semibold mb-2" style="color: #5EA853;">
                                     <i class="fas fa-user me-2"></i>Dados Pessoais
                                 </h6>
                             </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           id="name" 
                                           name="name"
                                           placeholder="Nome completo"
                                           required>
                                    <label for="name">Nome Completo *</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email"
                                           placeholder="Email"
                                           required>
                                    <label for="email">Email *</label>
                                </div>
                            </div>
                            
                                                         <!-- Dados de Acesso -->
                             <div class="col-12 mt-3">
                                 <h6 class="fw-semibold mb-2" style="color: #5EA853;">
                                     <i class="fas fa-lock me-2"></i>Dados de Acesso
                                 </h6>
                             </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password"
                                           placeholder="Senha"
                                           required>
                                    <label for="password">Senha *</label>
                                </div>
                                <div class="form-text">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Mínimo 3 caracteres
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation"
                                           placeholder="Confirmar senha"
                                           required>
                                    <label for="password_confirmation">Confirmar Senha *</label>
                                </div>
                            </div>
                            
                                                                                                                 <!-- Sua Localização -->
                            <div class="col-12 mt-3">
                                <h6 class="fw-semibold mb-2" style="color: #5EA853;">
                                    <i class="fas fa-map-marker-alt me-2"></i>Sua Localização
                                </h6>
                            </div>
                           
                           <div class="col-md-6">
                               <div class="form-floating">
                                   <input type="text" 
                                          class="form-control" 
                                          id="visitante_municipio" 
                                          name="visitante_municipio"
                                          placeholder="Seu município"
                                          required>
                                   <label for="visitante_municipio">Seu Município *</label>
                               </div>
                           </div>
                           
                           <div class="col-md-6">
                               <div class="form-floating">
                                   <input type="text" 
                                          class="form-control" 
                                          id="visitante_uf" 
                                          name="visitante_uf"
                                          placeholder="Sua UF"
                                          maxlength="2"
                                          style="text-transform: uppercase"
                                          required>
                                   <label for="visitante_uf">Sua UF *</label>
                               </div>
                           </div>
                           
                           <!-- Entidade Solicitada -->
                           <div class="col-12 mt-3">
                               <h6 class="fw-semibold mb-2" style="color: #5EA853;">
                                   <i class="fas fa-building me-2"></i>Entidade Orçamentária Solicitada
                               </h6>
                           </div>
                           
                           <div class="col-12">
                               <div class="form-floating">
                                   <select class="form-control" 
                                           id="entidade_orcamentaria_id" 
                                           name="entidade_orcamentaria_id"
                                           required>
                                       <option value="">Selecione uma entidade orçamentária...</option>
                                   </select>
                                   <label for="entidade_orcamentaria_id">Entidade Orçamentária *</label>
                               </div>
                               <div class="form-text">
                                   <small class="text-muted">
                                       <i class="fas fa-info-circle me-1"></i>
                                       Escolha a entidade orçamentária para a qual você precisa de acesso no sistema
                                   </small>
                               </div>
                           </div>
                            
                                                         <!-- Justificativa -->
                             <div class="col-12 mt-3">
                                 <div class="form-floating">
                                     <textarea class="form-control" 
                                               id="justificativa" 
                                               name="justificativa"
                                               placeholder="Justifique sua necessidade de acesso ao sistema..."
                                               style="height: 100px;"
                                               required
                                               maxlength="1000"></textarea>
                                     <label for="justificativa">Justificativa para Acesso ao Sistema *</label>
                                 </div>
                                 <div class="form-text">
                                     <small class="text-muted">
                                         <i class="fas fa-info-circle me-1"></i>
                                         Explique por que você precisa acessar o OrçaCidade e como vai utilizá-lo
                                     </small>
                                 </div>
                             </div>
                        </div>
                    </form>
                </div>
                                 <div class="modal-footer" style="background: transparent; border: none; padding: 1.5rem 1.25rem 1.25rem 1.25rem; gap: 0.6rem; justify-content: center;">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 0.375rem; font-weight: 500; padding: 0.75rem 1.5rem; border: none; font-size: 1rem; background: #6c757d; color: white;">
                         <i class="fas fa-times me-2"></i>Cancelar
                     </button>
                     <button type="submit" form="formSolicitarCadastro" class="btn" style="background: linear-gradient(135deg, #18578A 0%, #5EA853 100%); color: white; border: none; font-weight: 600; border-radius: 0.375rem; padding: 0.75rem 1.5rem; font-size: 1rem;">
                         <i class="fas fa-paper-plane me-2"></i>Enviar Solicitação
                     </button>
                 </div>
            </div>
        </div>
    </div>

         <!-- Formulário de logout oculto -->
     <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
         @csrf
     </form>
     
           <script>
          // Aguardar DOM estar completamente carregado
          document.addEventListener('DOMContentLoaded', function() {
              // Carregar dados quando o modal abrir
              document.getElementById('modalSolicitarCadastro').addEventListener('show.bs.modal', function () {
                  carregarDadosFormulario();
                  limparErros(); // Limpar erros ao abrir modal
              });
              
              // Gerenciar envio do formulário
              document.getElementById('formSolicitarCadastro').addEventListener('submit', function(e) {
                  e.preventDefault();
                  enviarSolicitacao();
              });
          });
         
         // Funções para mostrar toasts elegantes
        function mostrarToastSucesso(titulo, mensagem) {
            // Criar container se não existir
            if (!document.getElementById('toast-container')) {
                const container = document.createElement('div');
                container.id = 'toast-container';
                container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
                document.body.appendChild(container);
            }
            
            // Criar toast
            const toast = document.createElement('div');
            toast.className = 'toast show';
            toast.setAttribute('role', 'alert');
            toast.style.cssText = 'margin-bottom: 10px; min-width: 300px;';
            
            toast.innerHTML = `
                <div class="toast-header" style="background: linear-gradient(135deg, #28a745 0%, #5EA853 100%); color: white; border: none;">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong class="me-auto">${titulo}</strong>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
                </div>
                <div class="toast-body" style="background: #f8f9fa; color: #333;">
                    ${mensagem}
                </div>
            `;
            
            document.getElementById('toast-container').appendChild(toast);
            
            // Auto-remover após 5 segundos
            setTimeout(() => {
                toast.remove();
            }, 5000);
        }
        
        function mostrarToastErro(titulo, mensagem) {
            // Criar container se não existir
            if (!document.getElementById('toast-container')) {
                const container = document.createElement('div');
                container.id = 'toast-container';
                container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
                document.body.appendChild(container);
            }
            
            // Criar toast
            const toast = document.createElement('div');
            toast.className = 'toast show';
            toast.setAttribute('role', 'alert');
            toast.style.cssText = 'margin-bottom: 10px; min-width: 300px;';
            
            toast.innerHTML = `
                <div class="toast-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong class="me-auto">${titulo}</strong>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
                </div>
                <div class="toast-body" style="background: #f8f9fa; color: #333;">
                    ${mensagem}
                </div>
            `;
            
            document.getElementById('toast-container').appendChild(toast);
            
            // Auto-remover após 7 segundos (erros ficam mais tempo)
            setTimeout(() => {
                toast.remove();
            }, 7000);
        }

        async function carregarDadosFormulario() {
             try {
                 const response = await fetch('/api/publico/solicitar-cadastro/dados-formulario');
                 const data = await response.json();
                 
                                                                           if (response.ok) {
                        // Preencher entidades orçamentárias
                        const selectEntidade = document.getElementById('entidade_orcamentaria_id');
                        selectEntidade.innerHTML = '<option value="">Selecione uma entidade orçamentária...</option>';
                        data.entidades.forEach(entidade => {
                            const option = document.createElement('option');
                            option.value = entidade.id;
                            option.textContent = `${entidade.nome} (${entidade.tipo_organizacao} - ${entidade.nivel_administrativo})`;
                            selectEntidade.appendChild(option);
                        });
                 } else {
                     console.error('Erro ao carregar dados:', data.message);
                 }
             } catch (error) {
                 console.error('Erro ao carregar dados:', error);
             }
         }
         
                   // Função de filtro removida - selects funcionam independentemente
          
          function limparErros() {
              // Limpar classes de erro de todos os campos
              const campos = ['name', 'email', 'password', 'password_confirmation', 'visitante_municipio', 'visitante_uf', 'entidade_orcamentaria_id', 'justificativa'];
              campos.forEach(campo => {
                  const elemento = document.getElementById(campo);
                  if (elemento) {
                      elemento.classList.remove('is-invalid');
                  }
                  
                  // Remover mensagens de erro
                  const feedback = elemento?.parentNode?.querySelector('.invalid-feedback');
                  if (feedback) {
                      feedback.remove();
                  }
              });
          }
          
                     function aplicarErros(errors) {
               // Aplicar classes de erro e mensagens
               Object.keys(errors).forEach(campo => {
                   const elemento = document.getElementById(campo);
                   if (elemento) {
                       // Adicionar classe de erro
                       elemento.classList.add('is-invalid');
                       
                       // Remover mensagem de erro anterior se existir
                       const feedbackAnterior = elemento.parentNode.querySelector('.invalid-feedback');
                       if (feedbackAnterior) {
                           feedbackAnterior.remove();
                       }
                       
                       // Adicionar mensagem de erro
                       const feedback = document.createElement('div');
                       feedback.className = 'invalid-feedback';
                       feedback.textContent = Array.isArray(errors[campo]) ? errors[campo][0] : errors[campo];
                       
                       // Inserir após o elemento
                       elemento.parentNode.appendChild(feedback);
                   }
               });
               
               // Focar no primeiro campo com erro
               const primeiroCampoComErro = Object.keys(errors)[0];
               if (primeiroCampoComErro) {
                   document.getElementById(primeiroCampoComErro)?.focus();
               }
           }
          
          async function enviarSolicitacao() {
             const form = document.getElementById('formSolicitarCadastro');
             const formData = new FormData(form);
             
             // DEBUG: Ver dados enviados
             
             
             try {
                 const response = await fetch('/api/publico/solicitar-cadastro', {
                     method: 'POST',
                     body: formData,
                     headers: {
                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                         'Accept': 'application/json'
                     }
                 });
                 
                                                   if (response.ok) {
                    // Fechar modal primeiro
                    bootstrap.Modal.getInstance(document.getElementById('modalSolicitarCadastro')).hide();
                    form.reset();
                    limparErros();
                    
                    // Mostrar toast de sucesso
                    mostrarToastSucesso('Solicitação Enviada!', 'Sua solicitação foi enviada com sucesso. Você receberá um retorno em breve.');
                                     } else {
                       const data = await response.json();
                       
                       if (response.status === 422 && data.errors) {
                           // Erros de validação - aplicar padrão visual
                           
                           aplicarErros(data.errors);
                                               } else {
                            mostrarToastErro('Erro ao Enviar', data.message || 'Erro desconhecido ao enviar solicitação');
                        }
                   }
                         } catch (error) {
                console.error('Erro ao enviar:', error);
                mostrarToastErro('Erro de Conexão', 'Erro ao enviar solicitação. Verifique sua conexão e tente novamente.');
            }
                 }
    </script>
    </body>
</html>