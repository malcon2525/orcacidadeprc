<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Solicitação de Cadastro - OrçaCidade</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS Global do Projeto -->
    <link rel="stylesheet" href="{{ asset('assets/css/modern-interface.css') }}">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
        }
        
        .header-gradient {
            background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .header-gradient h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .header-gradient p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 1rem;
        }
        
        .form-body {
            padding: 2rem;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #18578A 0%, #5EA853 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            color: white;
        }
        
        .form-floating .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-floating .form-control:focus {
            border-color: #5EA853;
            box-shadow: 0 0 0 0.2rem rgba(94, 168, 83, 0.25);
        }
        
        .alert-custom {
            border-radius: 10px;
            border: none;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .loading-content {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="form-card">
            <!-- Header -->
            <div class="header-gradient">
                <h1><i class="fas fa-user-plus me-2"></i>Solicitação de Cadastro</h1>
                <p>Preencha os dados abaixo para solicitar acesso ao OrçaCidade</p>
            </div>
            
            <!-- Formulário -->
            <div class="form-body">
                <div id="app">
                    <formulario-solicitacao-cadastro></formulario-solicitacao-cadastro>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- App JS compilado (inclui Vue e componentes) -->
    @vite(['resources/js/app.js'])
</body>
</html>
