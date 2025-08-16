@extends('layouts.app')
@section('title', 'Conceitos - Andamento do Projeto')

@push('styles')
<style>
    /* Header Styles */
    .page-header {
        margin-bottom: 3rem;
        position: relative;
        padding-top: 1rem;
    }
    .btn-voltar {
        background: none;
        border: none;
        color: #666;
        padding: 0.5rem 0;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        font-size: 0.95rem;
        position: absolute;
        top: 0;
        left: 0;
    }
    .btn-voltar:hover {
        color: #43a047;
        text-decoration: none;
    }
    .titulo-conceitos {
        color: #263238;
        font-weight: 800;
        letter-spacing: 1px;
        text-align: center;
        margin-bottom: 0.5rem;
        margin-top: 1rem;
        font-size: 2.1rem;
        line-height: 1.2;
    }
    .titulo-conceitos i { 
        color: #43a047; 
        font-size: 2.3rem; 
        margin-right: 0.5rem; 
        vertical-align: -2px; 
    }
    .page-subtitle {
        color: #263238;
        font-size: 1.13rem;
        margin-bottom: 2.5rem;
        line-height: 1.5;
        text-align: center;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    
    /* Layout responsivo */
    @media (max-width: 768px) {
        .titulo-conceitos {
            font-size: 1.7rem;
        }
        .page-subtitle {
            font-size: 1rem;
            margin-bottom: 2rem;
        }
        .page-header {
            margin-bottom: 2rem;
        }
        .btn-voltar {
            position: static;
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-4" style="max-width: 900px;">
    <!-- Header da Página -->
    <div class="page-header">
        <a href="{{ route('andamento-projeto.index') }}" class="btn-voltar">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
        
        <h1 class="titulo-conceitos mb-2">
            <i class="fas fa-lightbulb"></i> Conceitos
        </h1>
        <p class="page-subtitle">
            Definições e conceitos fundamentais do sistema OrçaCidade.<br>
            Compreenda os termos técnicos utilizados no sistema.
        </p>
    </div>

    <!-- Conceito: Orçamento -->
    <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>Orçamento</b></h4>
            <span class="badge bg-warning text-dark">Em discussão</span>
        </div>
        <ul>
            <li>LOTE
                <ul>
                    <li>DADOS BÁSICOS DO ORÇAMENTO</li>
                    <li>BDI</li>
                    <li>DMT</li>
                    <li>PLANILHA ORÇAMENTÁRIA
                        <ul>
                            <li>OBJETO
                                <ul>
                                    <li>GRANDE ITEM
                                        <ul>
                                            <li>SUBGRUPO
                                                <ul>
                                                    <li>COTAÇÃO</li>
                                                    <li>COMPOSIÇÃO PERSONALIZADA</li>
                                                    <li>COMPOSIÇÃO OFICIAL</li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>CRONOGRAMA FÍSICO-FINANCEIRO</li>
                    <li>COMPOSIÇÃO DOS RECURSOS (TESOURO E COMTRAPARTIDA)???</li>
                    <li>DOCUMENTOS PARA ANÁLISE/APROVAÇÃO</li>
                    <li>PLANILHA PARA LICITAÇÃO</li>
                    <li>PLANILHA PARA MEDIÇÃO</li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Conceito: Lote -->
    <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>Lote</b></h4>
            <span class="badge bg-warning text-dark">Em discussão</span>
        </div>
        <ul>
            <li>Um orçamento pode ser dividido em lotes.</li>
            <li>É o lote que efetivamente vai para licitação.</li>
         </ul>
    </div>

    <!-- Conceito: Dados Básicos -->
    <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>Dados Básicos</b></h4>
            <span class="badge bg-warning text-dark">Em discussão</span>
        </div>
        <ul>
            <li>ver protótipo de tela</li>
         </ul>
    </div>


    
    <!-- Conceito: BDI -->
    <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>BDI</b></h4>
            <span class="badge bg-warning text-dark">Em discussão</span>
        </div>
        <ul>
            <li>BDI é a percentagem (de Benefícios e Despesas Indiretas) que irá incidir sobre um determinado serviço na planilha orçamentária.</li>
            <li>Irão existir percentuais para mão de obra, materiais e equipamentos.</li>
            <li>Como regra geral será aplicado o percentual de mão de obra nos custos de mão de obra de um serviço.</li>
            <li>Um BDI sempre irá pertencer a um orçamento ou a uma entidade orçamentária. Se o BDI for de uma entidade orçamentária, o campo entidade_orçamentária deverá estar preenchido. Se o BDI for de um orçamento o campo id_orcamento deteverá estar preenchido.</li>
        </ul>
    </div>


     <!-- Conceito: DMT -->
     <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>DMT</b></h4>
            <span class="badge bg-warning text-dark">Em discussão</span>
        </div>
        <ul>
            <li>São as distâncias de transporte de materiais da tabela DERPR.</li>
            <li>X1 = distância percorrida em estrada pavimentada</li>
            <li>X2 = distância percorrida em estrada não pavimentada</li>
            <li>Essas distâncias são utilizadas para calcular o custo de transporte de materiais.</li>
         </ul>
    </div>

     <!-- Conceito: Objeto -->
     <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>Objeto</b></h4>
            <span class="badge bg-warning text-dark">Em discussão</span>
        </div>
        <ul>
            <li>Uma planilha orçamentária pode ser composta por vários objetos.</li>
            <li>Para os orçamentos de Pavimentação um objeto irá corresponder a um trecho de estrada ou um rua<li>
            <li>Para os orçamentos de Construção Civil um objeto irá corresponder uma parte da obra (exemplo: praça, quadra, etc)</li>
         </ul>
    </div>

    <!-- Conceito: Cronograma Físico-Financeiro -->
    <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>Cronograma Físico-Financeiro</b></h4>
            <span class="badge bg-warning text-dark">Em discussão</span>
        </div>
        <ul>
            <li>.........</li>
         </ul>
    </div>

    <!-- Conceito: Documentação para Análise/Aprovação -->
    <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>Documentação para Análise/Aprovação</b></h4>
            <span class="badge bg-warning text-dark">Em discussão</span>
        </div>
        <ul>
            <li>....</li>
         </ul>
    </div>

    <!-- Conceito: Planilha para Licitação -->
    <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>Planilha para Licitação</b></h4>
            <span class="badge bg-warning text-dark">Em discussão</span>
        </div>
        <ul>
            <li>....</li>
         </ul>
    </div>

    <!-- Conceito: Planilha para Medição -->
    <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>Planilha para Medição</b></h4>
            <span class="badge bg-warning text-dark">Em discussão</span>
        </div>
        <ul>
            <li>....</li>
         </ul>
    </div>





    <!-- Conceito: Versão de Tipo de Orçamento -->
    <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>Versão de Tipo de Orçamento</b></h4>
            <span class="badge bg-warning text-dark">Em discussão</span>
        </div>
        <ul>
            <li>Permite lidar com mudanças na estrutura de orçamento. </li>
            <li>Na criação de orçamentos o sistema sempre irá pegar as informações da última versão ativa. Os orçamentos com versões anteriores continuarão funcionando.</li>
        </ul>
    </div>

    <!-- Conceito: Estrutura de Orçamento -->
    <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>Estrutura de Orçamento</b></h4>
            <span class="badge bg-warning text-dark">Em discussão</span>
        </div>
        <ul>
            <li>Um tipo de orçamento pode ter Grandes Itens.</li>
            <li>Um Grande Item possui Subgrupos.</li>
            <li>Um Subgrupo possui Serviços(Composições).</li>
            <li>Os Grandes Itens, Subgrupos são fixos no sistema e não podem ser alterados pelo orçamentista</li>
        </ul>
    </div>

    <!-- Conceito: Cotações -->
    <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>Cotações</b></h4>
            <span class="badge bg-warning text-dark">Em discussão</span>
        </div>
        <ul>
            <li>Uma cotação será utilizada para compor uma composição própria. (nunca poderá ser associada diretamente na planilha orçamentária).</li>
            <li>Sempre deverá ser composta por preços de três fornecedores.</li>
            <li>O valor final poderá ser: Média, Mediana, Menor Valor ou Manual.</li>
            <li>O orçamentista cadastra sua cotação. Nesse momento a cotação irá pertencer a uma entidade orçamentária e o campo ‘entidade_orcamentária’ deverá ser preenchido.Nesse estado essa cotação poderá ser utilizada em qualquer orçamento feito pela entidade orçamentária</li>
            <li>Quando o técnico municipal utiliza a cotação em uma determinada composição própria o sistema irá ‘duplicar’ a cotação. Mas dessa vez preenchendo também o campo ‘orcamento_id’ (nesse caso a edição do valor campos serão bloqueados)</li>
            <li>Assim, uma cotação com o campo ‘orcamento_id’ preenchido significa que a cotação pertence a uma determinada composição própria de um determinado orçamento. </li>
        </ul>
    </div>

     <!-- Conceito: Composições Próprias -->
     <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>Composições Próprias</b></h4>
            <span class="badge bg-warning text-dark">Em discussão</span>
        </div>
        <ul>
            <li>É um serviço(composição), cadastrado pela própria entidade orçamentária, que poderá ser utilizado na planilha orçamentária.</li>
            <li>Deverá ser composta por pelo menos um item.</li>
            <li>Um item pode vir de uma tabela oficial (SINAPI ou DERPR) ou de uma cotação.</li>
            <li>Quando vier de uma tabela oficial os campos poderão ser editados.</li>
            <li>Quando vier de uma cotação os campos NÃO poderão ser editados.</li>
         </ul>
    </div>

    

   

    




    <!-- Conceito: Versão de Orçamento -->
    <div class="concept-box mb-4 p-3" style="border:1px solid #bbb; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0"><b>Versão de Orçamento</b></h4>
            <span class="badge bg-success">Aprovado pela equipe</span>
        </div>
        <ul>
            <li>Este é um exemplo de conceito já aprovado.</li>
            <li>Você pode adicionar quantos tópicos forem necessários.</li>
        </ul>
    </div>

</div>
@endsection 