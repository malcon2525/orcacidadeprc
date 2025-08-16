<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protótipo Planilha Orçamentária - OrçaCidade</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bs-primary: #1976D2;
            --bs-primary-rgb: 25, 118, 210;
            --bs-primary-tint: rgba(25, 118, 210, 0.07);
            --bs-secondary: #6c757d;
            --bs-success: #43A047;
            --bs-body-bg: #ffffff;
            --bs-border-color: #dee2e6;
            --bs-gray-100: #f8f9fa;
        }
        body {
            background-color: var(--bs-body-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            padding-bottom: 80px;
        }

        .breadcrumb { --bs-breadcrumb-padding-x: 0; --bs-breadcrumb-margin-bottom: 0; }
        .breadcrumb a { text-decoration: none; }
        .nav-tabs { border-bottom: 1px solid var(--bs-border-color); }
        .nav-tabs .nav-link { color: var(--bs-secondary); background-color: transparent; border: 1px solid transparent; border-bottom: none; margin-bottom: -1px; }
        .nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link { color: var(--bs-primary); font-weight: 600; background-color: var(--bs-body-bg); border-color: var(--bs-border-color) var(--bs-border-color) var(--bs-body-bg); }
        .nav-tabs .nav-link i { margin-right: 0.5rem; }
        .total-global-widget { background-color: #f8f9fa; padding: .5rem 1rem; border-radius: .375rem; border-left: 4px solid var(--bs-primary); }

        .table-wrapper { border: 1px solid var(--bs-border-color); border-radius: .375rem; overflow-x: auto; }
        .table { margin-bottom: 0; font-size: 14px; white-space: nowrap; }
        .table > thead th { vertical-align: middle; }
        .table > thead .main-header-row th { background-color: var(--bs-gray-100); font-weight: 600; border-bottom-width: 1px; padding-top: .75rem; padding-bottom: .75rem; }
        .table > thead .sub-header-row th { background-color: var(--bs-gray-100); font-weight: 600; border-top: 1px solid var(--bs-border-color); }
        
        .table > thead [data-group-color="base"] { color: var(--bs-secondary); }
        .table > thead [data-group-color="transp"] { color: var(--bs-success); }
        .table > thead [data-group-color="orcamento"] { color: var(--bs-primary); }

        .group-header { background-color: var(--bs-primary-tint); font-weight: bold; font-size: 1em; color: var(--bs-primary); }
        .subgroup-header { font-style: italic; font-weight: 600; color: var(--bs-secondary); }
        .table input[readonly].form-control { background-color: transparent !important; border: none !important; box-shadow: none !important; cursor: default; }
        .table .form-control, .table .form-select { font-size: inherit; }
        
        .table.font-size-sm { font-size: 12px; }
        .table.font-size-md { font-size: 14px; }
        .table.font-size-lg { font-size: 16px; }

        .footer-tabs { background-color: #f8f9fa; border-top: 1px solid var(--bs-border-color); padding: 0 .5rem; z-index: 1030; }
        .description-col { white-space: normal; min-width: 300px; }
        .btn-action { --bs-btn-padding-y: .1rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem; }

        /* Estilos para o Modo de Edição em Massa */
        .mass-edit-active .group-header,
        .mass-edit-active .subgroup-header,
        .mass-edit-active .insert-service-cell {
            display: none;
        }
        
        /* Botão Voltar */
        .btn-voltar {
            background: none;
            border: none;
            color: #666;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .btn-voltar:hover {
            background-color: #f8f9fa;
            color: #333;
            text-decoration: none;
        }
        
        .btn-voltar i {
            font-size: 0.875rem;
        }
        
        .page-header {
            position: relative;
            margin-bottom: 2rem;
        }
        
        .titulo-planilha {
            font-size: 2.1rem;
            font-weight: 800;
            color: #263238;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            font-size: 1.13rem;
            color: #666;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Header da Página -->
        <div class="page-header">
            <a href="{{ url('/') }}" class="btn-voltar">
                <i class="bi bi-arrow-left"></i>
                Voltar
            </a>
            
            <h1 class="titulo-planilha mb-2">
                <i class="bi bi-table"></i> Protótipo Planilha Orçamentária
            </h1>
            <p class="page-subtitle">
                Visualização do protótipo da planilha orçamentária desenvolvida para o OrçaCidade
            </p>
        </div>
        
        <!-- Conteúdo da Planilha -->
        <div id="planilha-orcamentaria-component">
            <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item"><a href="#">Projetos</a></li><li class="breadcrumb-item"><a href="#">Orçamentos</a></li><li class="breadcrumb-item active" aria-current="page">Hospital</li></ol></nav>

            <ul class="nav nav-tabs my-3">
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-house-door"></i>Início</a></li><li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-percent"></i>BDI</a></li><li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-truck"></i>DMT</a></li><li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-table"></i>Planilha</a></li><li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-file-earmark-text"></i>Resumo</a></li><li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-calendar-week"></i>Planejamento</a></li><li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-graph-up"></i>Análise</a></li>
            </ul>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 p-3 mb-3 bg-white rounded border">
                <div class="d-flex align-items-center flex-wrap gap-4">
                    <div class="d-flex align-items-center gap-2"><label for="lote" class="form-label small mb-0 text-muted">LOTE:</label><input type="text" id="lote" class="form-control form-control-sm" value="01" style="width: 60px;"></div>
                    <div class="d-flex align-items-center gap-2"><label for="versao" class="form-label small mb-0 text-muted">Versão:</label><input type="text" id="versao" class="form-control form-control-sm" value="1 - Inicial" style="width: 120px;"></div>
                    <div class="btn-group" role="group">
                      <input type="radio" class="btn-check" name="desoneracao" id="comDesoneracao" autocomplete="off" checked><label class="btn btn-sm btn-outline-primary" for="comDesoneracao">Com Desoneração</label>
                      <input type="radio" class="btn-check" name="desoneracao" id="semDesoneracao" autocomplete="off"><label class="btn btn-sm btn-outline-primary" for="semDesoneracao">Sem Desoneração</label>
                    </div>
                    <div class="btn-group" role="group" id="viewModeToggle">
                      <input type="radio" class="btn-check" name="viewmode" id="hierarchical-view" value="hierarchical" autocomplete="off" checked>
                      <label class="btn btn-sm btn-outline-secondary" for="hierarchical-view"><i class="bi bi-list-nested"></i> Visão Hierárquica</label>
                      <input type="radio" class="btn-check" name="viewmode" id="mass-edit-view" value="mass-edit" autocomplete="off">
                      <label class="btn btn-sm btn-outline-secondary" for="mass-edit-view"><i class="bi bi-pencil-square"></i> Edição em Massa</label>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="alert alert-warning p-2 m-0 d-flex align-items-center" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i><small>Alterações não salvas. <a href="#" class="alert-link">Salvar agora</a></small></div>
                    <div class="total-global-widget text-end"><small class="text-muted d-block mb-0">TOTAL GLOBAL</small><strong class="fs-5">R$ 28.308,00</strong></div>
                    <div class="btn-group">
                      <button type="button" class="btn btn-light" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><i class="bi bi-gear-fill"></i></button>
                      <div class="dropdown-menu dropdown-menu-end p-3" style="width: 320px;">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tamanho do Texto</label>
                            <div class="btn-group w-100" role="group" id="fontSizeGroup">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-font-size="sm">Pequeno</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary active" data-font-size="md">Médio</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-font-size="lg">Grande</button>
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-bold">Grupos de Colunas Visíveis</label>
                            <div class="form-check form-switch"><input class="form-check-input column-toggle" type="checkbox" role="switch" id="toggle-info" data-group="info" checked><label class="form-check-label" for="toggle-info">Informações Básicas</label></div>
                            <div class="form-check form-switch"><input class="form-check-input column-toggle" type="checkbox" role="switch" id="toggle-transp" data-group="transp" checked><label class="form-check-label" for="toggle-transp">Transporte</label></div>
                            <div class="form-check form-switch"><input class="form-check-input column-toggle" type="checkbox" role="switch" id="toggle-preco-tabela" data-group="preco-tabela" checked><label class="form-check-label" for="toggle-preco-tabela">Preço Tabela</label></div>
                            <div class="form-check form-switch"><input class="form-check-input column-toggle" type="checkbox" role="switch" id="toggle-preco-bdi" data-group="preco-bdi" checked><label class="form-check-label" for="toggle-preco-bdi">Preço Unitário c/ BDI</label></div>
                            <div class="form-check form-switch"><input class="form-check-input column-toggle" type="checkbox" role="switch" id="toggle-orcamento" data-group="orcamento" checked><label class="form-check-label" for="toggle-orcamento">Preço no Orçamento</label></div>
                        </div>
                      </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive table-wrapper mb-5">
                <table id="main-table" class="table table-bordered table-hover table-sm bg-white font-size-md">
                    <thead class="text-center">
                        <tr class="main-header-row">
                            <th rowspan="2" style="width: 40px;"></th><th rowspan="2" style="width: 40px;"></th>
                            <th data-group="info" data-group-color="base" colspan="5">INFORMAÇÕES BÁSICAS SOBRE O SERVIÇO</th>
                            <th data-group="transp" data-group-color="transp" colspan="4">TRANSPORTE</th>
                            <th data-group="preco-tabela" data-group-color="base" colspan="2">PREÇO TABELA</th>
                            <th data-group="preco-bdi" data-group-color="orcamento" colspan="3">PREÇO UNITÁRIO COM BDI</th>
                            <th data-group="orcamento" data-group-color="orcamento" colspan="3">PREÇO NO ORÇAMENTO</th>
                        </tr>
                        <tr class="sub-header-row">
                            <th data-group="info" data-group-color="base">DATA BASE</th><th data-group="info" data-group-color="base">FONTE</th><th data-group="info" data-group-color="base">CÓDIGO</th><th data-group="info" data-group-color="base">DESCRIÇÃO DO SERVIÇO</th><th data-group="info" data-group-color="base">UNID.</th>
                            <th data-group="transp" data-group-color="transp"></th><th data-group="transp" data-group-color="transp">X1 (Km)</th><th data-group="transp" data-group-color="transp">X2 (Km)</th><th data-group="transp" data-group-color="transp">VALOR DO TRANSP.</th>
                            <th data-group="preco-tabela" data-group-color="base">M.O.</th><th data-group="preco-tabela" data-group-color="base">CUSTO</th>
                            <th data-group="preco-bdi" data-group-color="orcamento">TIPO BDI</th><th data-group="preco-bdi" data-group-color="orcamento">VALOR BDI</th><th data-group="preco-bdi" data-group-color="orcamento">PREÇO</th>
                            <th data-group="orcamento" data-group-color="orcamento">QUANT.</th><th data-group="orcamento" data-group-color="orcamento">SUBTOTAL</th><th data-group="orcamento" data-group-color="orcamento">ABC %</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle">
                        <tr class="group-header" data-bs-toggle="collapse" data-bs-target=".grande-item-1" aria-expanded="true">
                            <td class="text-center"><a href="#"><i class="bi bi-chevron-down"></i></a></td>
                            <td class="group-title" colspan="17">1. SERVIÇOS PRELIMINARES E ADMINISTRATIVOS DA OBRA</td>
                            <td class="text-end pe-3">25.168,00</td><td class="text-end pe-3">89%</td>
                        </tr>
                        <tr class="subgroup-header collapse show grande-item-1" data-bs-toggle="collapse" data-bs-target=".subgrupo-1-1" aria-expanded="true">
                            <td></td><td class="text-center"><a href="#"><i class="bi bi-chevron-down"></i></a></td>
                            <td class="subgroup-title" colspan="16">1.1 INSTALAÇÕES PARA CANTEIROS DE OBRAS</td>
                            <td class="text-end pe-3"><strong>15.979,00</strong></td><td class="text-end pe-3"><strong>53%</strong></td>
                        </tr>
                        <tr class="collapse show subgrupo-1-1 grande-item-1">
                            <td></td><td class="text-center"><button class="btn btn-outline-danger btn-sm btn-action"><i class="bi bi-trash"></i></button></td>
                            <td data-group="info"><input type="text" class="form-control form-control-sm text-center" value="2020-04" readonly></td>
                            <td data-group="info"><input type="text" class="form-control form-control-sm text-center" value="SINAPI" readonly></td>
                            <td data-group="info"><input type="text" class="form-control form-control-sm text-center" value="10398" readonly></td>
                            <td data-group="info" class="description-col"><input type="text" class="form-control form-control-sm" value="FORNECIMENTO E INSTALAÇÃO DE PLACA DE OBRA..." readonly></td>
                            <td data-group="info"><input type="text" class="form-control form-control-sm text-center" value="M2" readonly></td>
                            <td data-group="transp"></td><td data-group="transp"><input type="number" class="form-control form-control-sm text-end"></td><td data-group="transp"><input type="number" class="form-control form-control-sm text-end"></td><td data-group="transp"><input type="text" class="form-control form-control-sm text-end" value="-" readonly></td>
                            <td data-group="preco-tabela"><input type="text" class="form-control form-control-sm text-end" value="33,00" readonly></td><td data-group="preco-tabela"><input type="text" class="form-control form-control-sm text-end" value="150,00" readonly></td>
                            <td data-group="preco-bdi"><select class="form-select form-select-sm"><option selected>Serviço</option><option>Material</option></select></td><td data-group="preco-bdi"><input type="text" class="form-control form-control-sm text-end" value="22,00" readonly></td><td data-group="preco-bdi"><input type="text" class="form-control form-control-sm text-end" value="157,00" readonly></td>
                            <td data-group="orcamento"><input type="number" class="form-control form-control-sm text-end" value="2.00"></td><td data-group="orcamento"><input type="text" class="form-control form-control-sm text-end" value="314,00" readonly></td><td data-group="orcamento"><input type="text" class="form-control form-control-sm text-end" value="1%" readonly></td>
                        </tr>
                        <tr class="collapse show subgrupo-1-1 grande-item-1 insert-service-cell">
                            <td></td><td class="text-start ps-3" colspan="18"><a href="#" class="btn-insert-link"><i class="bi bi-plus-circle me-2"></i>Inserir serviço</a></td>
                        </tr>
                        
                        <tr class="group-header" data-bs-toggle="collapse" data-bs-target=".grande-item-2" aria-expanded="false">
                            <td class="text-center"><a href="#"><i class="bi bi-chevron-right"></i></a></td>
                            <td class="group-title" colspan="17">2. FUNDAÇÕES E ESTRUTURA</td>
                            <td class="text-end pe-3">12.500,00</td><td class="text-end pe-3">10%</td>
                        </tr>
                        <tr class="subgroup-header collapse grande-item-2" data-bs-toggle="collapse" data-bs-target=".subgrupo-2-1" aria-expanded="false">
                            <td></td><td class="text-center"><a href="#"><i class="bi bi-chevron-right"></i></a></td>
                            <td class="subgroup-title" colspan="16">2.1 ESCAVAÇÃO E SERVIÇOS DE TERRA</td>
                            <td class="text-end pe-3"><strong>12.500,00</strong></td><td class="text-end pe-3"><strong>10%</strong></td>
                        </tr>
                        <tr class="collapse subgrupo-2-1 grande-item-2">
                             <td></td><td class="text-center"><button class="btn btn-outline-danger btn-sm btn-action"><i class="bi bi-trash"></i></button></td><td data-group="info"><input type="text" class="form-control form-control-sm text-center" value="2022-08" readonly></td><td data-group="info"><input type="text" class="form-control form-control-sm text-center" value="ORSE" readonly></td><td data-group="info"><input type="text" class="form-control form-control-sm text-center" value="22113" readonly></td><td data-group="info" class="description-col"><input type="text" class="form-control form-control-sm" value="ESCAVAÇÃO MANUAL DE VALAS" readonly></td><td data-group="info"><input type="text" class="form-control form-control-sm text-center" value="M³" readonly></td>
                            <td data-group="transp"></td><td data-group="transp"><input type="number" class="form-control form-control-sm text-end"></td><td data-group="transp"><input type="number" class="form-control form-control-sm text-end"></td><td data-group="transp"><input type="text" class="form-control form-control-sm text-end" value="-" readonly></td>
                            <td data-group="preco-tabela"><input type="text" class="form-control form-control-sm text-end" value="45,00" readonly></td><td data-group="preco-tabela"><input type="text" class="form-control form-control-sm text-end" value="80,00" readonly></td>
                            <td data-group="preco-bdi"><select class="form-select form-select-sm"><option selected>Serviço</option><option>Material</option></select></td><td data-group="preco-bdi"><input type="text" class="form-control form-control-sm text-end" value="15,00" readonly></td><td data-group="preco-bdi"><input type="text" class="form-control form-control-sm text-end" value="95,00" readonly></td>
                            <td data-group="orcamento"><input type="number" class="form-control form-control-sm text-end" value="100.00"></td><td data-group="orcamento"><input type="text" class="form-control form-control-sm text-end" value="9.500,00" readonly></td><td data-group="orcamento"><input type="text" class="form-control form-control-sm text-end" value="8%" readonly></td>
                        </tr>
                        <tr class="collapse subgrupo-2-1 grande-item-2 insert-service-cell">
                            <td></td><td class="text-start ps-3" colspan="18"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <footer class="footer-tabs fixed-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center ps-2"><button class="btn btn-sm text-white" style="background-color: var(--bs-success);">GLOBAL</button><button class="btn btn-sm btn-outline-secondary ms-2">INSTALAÇÃO</button></div>
            <div class="d-flex align-items-center">
                <ul class="nav nav-tabs"><li class="nav-item"><a class="nav-link active" href="#">RUA 1 <i class="bi bi-x-circle ms-1"></i></a></li><li class="nav-item"><a class="nav-link" href="#">ATL_1 <i class="bi bi-x-circle ms-1"></i></a></li></ul>
                <button class="btn btn-link text-decoration-none" title="Adicionar nova aba" style="color: var(--bs-success);"><i class="bi bi-plus-circle-fill fs-5"></i></button>
            </div>
        </footer>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mainTable = document.getElementById('main-table');

            // --- LÓGICA DO MODO DE EDIÇÃO EM MASSA ---
            const viewModeToggle = document.getElementById('viewModeToggle');
            const hierarchicalView = document.getElementById('hierarchical-view');
            const massEditView = document.getElementById('mass-edit-view');

            viewModeToggle.addEventListener('change', function(event) {
                const selectedMode = event.target.value;
                
                if (selectedMode === 'mass-edit') {
                    mainTable.classList.add('mass-edit-active');
                } else {
                    mainTable.classList.remove('mass-edit-active');
                }
            });

            // --- LÓGICA DE CONTROLE DE VISIBILIDADE DE COLUNAS ---
            const columnToggles = document.querySelectorAll('.column-toggle');
            
            columnToggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const group = this.dataset.group;
                    const cells = mainTable.querySelectorAll(`[data-group="${group}"]`);
                    
                    cells.forEach(cell => {
                        if (this.checked) {
                            cell.style.display = '';
                        } else {
                            cell.style.display = 'none';
                        }
                    });
                });
            });

            // --- LÓGICA DE CONTROLE DE TAMANHO DE FONTE ---
            const fontSizeButtons = document.querySelectorAll('#fontSizeGroup button');
            
            fontSizeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    fontSizeButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Get font size
                    const fontSize = this.dataset.fontSize;
                    
                    // Remove all font size classes
                    mainTable.classList.remove('font-size-sm', 'font-size-md', 'font-size-lg');
                    
                    // Add selected font size class
                    mainTable.classList.add(`font-size-${fontSize}`);
                });
            });

            // --- LÓGICA DE COLAPSO/EXPANSÃO DE GRUPOS ---
            const collapseButtons = document.querySelectorAll('[data-bs-toggle="collapse"]');
            
            collapseButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    
                    // Toggle icon
                    setTimeout(() => {
                        if (icon.classList.contains('bi-chevron-down')) {
                            icon.classList.remove('bi-chevron-down');
                            icon.classList.add('bi-chevron-right');
                        } else {
                            icon.classList.remove('bi-chevron-right');
                            icon.classList.add('bi-chevron-down');
                        }
                    }, 150);
                });
            });

            // --- LÓGICA PARA CALCULAR SUBTOTAIS ---
            const quantityInputs = document.querySelectorAll('input[type="number"]');
            
            quantityInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const row = this.closest('tr');
                    const priceCell = row.querySelector('[data-group="preco-bdi"] input[readonly]');
                    const subtotalCell = row.querySelector('[data-group="orcamento"] input[readonly]');
                    
                    if (priceCell && subtotalCell) {
                        const quantity = parseFloat(this.value) || 0;
                        const price = parseFloat(priceCell.value.replace(',', '.')) || 0;
                        const subtotal = quantity * price;
                        
                        subtotalCell.value = subtotal.toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                });
            });

            // --- LÓGICA PARA INSERIR NOVOS SERVIÇOS ---
            const insertLinks = document.querySelectorAll('.btn-insert-link');
            
            insertLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Aqui você pode adicionar lógica para inserir novos serviços
                    alert('Funcionalidade de inserção de serviços seria implementada aqui');
                });
            });

            // --- LÓGICA PARA BOTÕES DE REMOÇÃO ---
            const deleteButtons = document.querySelectorAll('.btn-action');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('Tem certeza que deseja remover este serviço?')) {
                        const row = this.closest('tr');
                        row.remove();
                    }
                });
            });

            // --- LÓGICA PARA ATUALIZAR TOTAL GLOBAL ---
            function updateGlobalTotal() {
                const subtotalCells = document.querySelectorAll('[data-group="orcamento"] input[readonly]');
                let total = 0;
                
                subtotalCells.forEach(cell => {
                    const value = parseFloat(cell.value.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
                    total += value;
                });
                
                const totalWidget = document.querySelector('.total-global-widget strong');
                if (totalWidget) {
                    totalWidget.textContent = `R$ ${total.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })}`;
                }
            }

            // Atualizar total quando há mudanças
            quantityInputs.forEach(input => {
                input.addEventListener('input', updateGlobalTotal);
            });

            // --- LÓGICA PARA ALERTAS DE ALTERAÇÕES NÃO SALVAS ---
            let hasUnsavedChanges = false;
            
            const formInputs = document.querySelectorAll('input, select');
            formInputs.forEach(input => {
                input.addEventListener('change', function() {
                    hasUnsavedChanges = true;
                    const alertElement = document.querySelector('.alert-warning');
                    if (alertElement) {
                        alertElement.style.display = 'flex';
                    }
                });
            });

            // --- LÓGICA PARA SALVAR ALTERAÇÕES ---
            const saveLink = document.querySelector('.alert-link');
            if (saveLink) {
                saveLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Aqui você pode adicionar lógica para salvar as alterações
                    alert('Funcionalidade de salvamento seria implementada aqui');
                    
                    hasUnsavedChanges = false;
                    const alertElement = document.querySelector('.alert-warning');
                    if (alertElement) {
                        alertElement.style.display = 'none';
                    }
                });
            }

            // --- PREVENÇÃO DE PERDA DE DADOS ---
            window.addEventListener('beforeunload', function(e) {
                if (hasUnsavedChanges) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        });
    </script>
</body>
</html> 