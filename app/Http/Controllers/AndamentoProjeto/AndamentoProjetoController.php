<?php

namespace App\Http\Controllers\AndamentoProjeto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Blade;

class AndamentoProjetoController extends Controller
{
    /**
     * Exibe a página principal de andamento do projeto
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Carregar dados dos arquivos JSON
        $phases = $this->loadJsonData('phases.json');
        $backlog = $this->loadJsonData('backlog.json');
        $meetings = $this->loadJsonData('meetings.json');
        $concepts = $this->loadJsonData('concepts.json');

        // Calcular métricas gerais
        $metrics = $this->calculateMetrics($phases, $backlog, $meetings, $concepts);

        return view('andamento-projeto.index', compact('phases', 'backlog', 'meetings', 'concepts', 'metrics'));
    }

    private function loadJsonData($filename)
    {
        $path = 'andamento-projeto/' . $filename;
        
        if (Storage::exists($path)) {
            $content = Storage::get($path);
            return json_decode($content, true);
        }

        // Retornar dados padrão se arquivo não existir
        return $this->getDefaultData($filename);
    }

    private function getDefaultData($filename)
    {
        switch ($filename) {
            case 'phases.json':
                return [
                    'project_name' => 'OrçaCidade',
                    'total_phases' => 4,
                    'current_phase' => 2,
                    'phases' => [
                        [
                            'id' => 1,
                            'name' => 'Fase 1 - Configuração e Base',
                            'status' => 'completed',
                            'progress' => 100
                        ],
                        [
                            'id' => 2,
                            'name' => 'Fase 2 - Módulos de Orçamento',
                            'status' => 'in_progress',
                            'progress' => 75
                        ],
                        [
                            'id' => 3,
                            'name' => 'Fase 3 - Integração e Testes',
                            'status' => 'planned',
                            'progress' => 0
                        ],
                        [
                            'id' => 4,
                            'name' => 'Fase 4 - Finalização e Deploy',
                            'status' => 'planned',
                            'progress' => 0
                        ]
                    ]
                ];

            case 'backlog.json':
                return [
                    'total_items' => 45,
                    'completed_items' => 28,
                    'in_progress_items' => 12,
                    'planned_items' => 5,
                    'backlog' => [
                        [
                            'id' => 1,
                            'title' => 'Configuração do ambiente Laravel',
                            'sprint' => '1.1',
                            'status' => 'completed',
                            'priority' => 'high',
                            'assignee' => 'Equipe Dev'
                        ],
                        [
                            'id' => 2,
                            'title' => 'Módulo Municípios',
                            'sprint' => '1.2',
                            'status' => 'completed',
                            'priority' => 'high',
                            'assignee' => 'Equipe Dev'
                        ],
                        [
                            'id' => 3,
                            'title' => 'Importação DER-PR',
                            'sprint' => '1.3',
                            'status' => 'completed',
                            'priority' => 'high',
                            'assignee' => 'Equipe Dev'
                        ],
                        [
                            'id' => 4,
                            'title' => 'Custos de Transporte',
                            'sprint' => '2.3',
                            'status' => 'in_progress',
                            'priority' => 'high',
                            'assignee' => 'Equipe Dev'
                        ],
                        [
                            'id' => 5,
                            'title' => 'Integração entre módulos',
                            'sprint' => '3.1',
                            'status' => 'planned',
                            'priority' => 'high',
                            'assignee' => 'Equipe Dev'
                        ]
                    ]
                ];

            case 'meetings.json':
                return [
                    'total_meetings' => 15,
                    'upcoming_meetings' => 2,
                    'meetings' => [
                        [
                            'id' => 1,
                            'title' => 'Kick-off do Projeto',
                            'date' => '2024-01-15',
                            'type' => 'kickoff',
                            'status' => 'completed',
                            'participants' => ['Equipe Dev', 'Cliente', 'Gerente']
                        ],
                        [
                            'id' => 2,
                            'title' => 'Reunião de Planejamento Sprint 2.3',
                            'date' => '2024-05-20',
                            'type' => 'planning',
                            'status' => 'completed',
                            'participants' => ['Equipe Dev', 'PO']
                        ],
                        [
                            'id' => 3,
                            'title' => 'Reunião de Review Sprint 2.2',
                            'date' => '2024-06-10',
                            'type' => 'review',
                            'status' => 'scheduled',
                            'participants' => ['Equipe Dev', 'Cliente', 'PO']
                        ],
                        [
                            'id' => 4,
                            'title' => 'Reunião de Planejamento Sprint 3.1',
                            'date' => '2024-06-20',
                            'type' => 'planning',
                            'status' => 'scheduled',
                            'participants' => ['Equipe Dev', 'PO']
                        ]
                    ]
                ];

            case 'concepts.json':
                return [
                    'total_concepts' => 8,
                    'approved_concepts' => 6,
                    'pending_concepts' => 2,
                    'concepts' => [
                        [
                            'id' => 1,
                            'title' => 'Arquitetura Laravel + Vue.js',
                            'description' => 'Definição da arquitetura tecnológica',
                            'status' => 'approved',
                            'date' => '2024-01-10'
                        ],
                        [
                            'id' => 2,
                            'title' => 'Sistema de Importação Modular',
                            'description' => 'Estrutura para importação de dados externos',
                            'status' => 'approved',
                            'date' => '2024-01-15'
                        ],
                        [
                            'id' => 3,
                            'title' => 'Cálculo de Transporte Automático',
                            'description' => 'Automatização do cálculo de custos de transporte',
                            'status' => 'approved',
                            'date' => '2024-03-20'
                        ],
                        [
                            'id' => 4,
                            'title' => 'Interface Responsiva',
                            'description' => 'Design responsivo para diferentes dispositivos',
                            'status' => 'approved',
                            'date' => '2024-02-10'
                        ],
                        [
                            'id' => 5,
                            'title' => 'Sistema de Relatórios',
                            'description' => 'Geração de relatórios em PDF e Excel',
                            'status' => 'pending',
                            'date' => '2024-05-15'
                        ],
                        [
                            'id' => 6,
                            'title' => 'Integração com Sistemas Externos',
                            'description' => 'APIs para integração com outros sistemas',
                            'status' => 'pending',
                            'date' => '2024-06-01'
                        ]
                    ]
                ];

            default:
                return [];
        }
    }

    private function calculateMetrics($phases, $backlog, $meetings, $concepts)
    {
        $totalItems = $backlog['total_items'] ?? 0;
        $completedItems = $backlog['completed_items'] ?? 0;
        $inProgressItems = $backlog['in_progress_items'] ?? 0;
        $plannedItems = $backlog['planned_items'] ?? 0;

        $progressPercentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;

        return [
            'total_items' => $totalItems,
            'completed_items' => $completedItems,
            'in_progress_items' => $inProgressItems,
            'planned_items' => $plannedItems,
            'progress_percentage' => $progressPercentage,
            'total_phases' => $phases['total_phases'] ?? 0,
            'current_phase' => $phases['current_phase'] ?? 0,
            'total_meetings' => $meetings['total_meetings'] ?? 0,
            'upcoming_meetings' => $meetings['upcoming_meetings'] ?? 0,
            'total_concepts' => $concepts['total_concepts'] ?? 0,
            'approved_concepts' => $concepts['approved_concepts'] ?? 0,
            'pending_concepts' => $concepts['pending_concepts'] ?? 0
        ];
    }

    /**
     * Retorna dados em formato JSON para APIs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dados()
    {
        $dados = [
            'projeto' => [
                'nome' => 'Sistema OrçaCidade',
                'descricao' => 'Sistema de Preços para Orçamento de Obras',
                'versao' => '1.0.0',
                'status' => 'Em Desenvolvimento',
                'progresso' => 75,
            ],
            'estatisticas' => [
                'modulos_desenvolvidos' => 14,
                'pontos_funcao' => 362,
                'horas_trabalhadas' => 2896,
                'valor_investido' => 289600,
            ],
            'modulos' => [
                // Aqui serão adicionados os dados dos módulos
            ]
        ];

        return response()->json($dados);
    }

    public function escopo() { return view('andamento-projeto.escopo'); }
    public function backlogGlobal() { return view('andamento-projeto.backlog-global'); }
    public function fasesESprints() { return view('andamento-projeto.fases-e-sprints'); }
    public function conceitos() { return view('andamento-projeto.conceitos'); }
    
    /**
     * Exibe a página de relatórios consolidados
     *
     * @return \Illuminate\View\View
     */
    public function relatorios()
    {
        // Carregar dados do consolidado.json
        $consolidado = $this->loadRelatorioConsolidado();
        
        return view('andamento-projeto.relatorios', compact('consolidado'));
    }
    
    /**
     * Exibe relatório individual de um módulo
     *
     * @param string $modulo
     * @return \Illuminate\View\View
     */
    public function relatorioIndividual($modulo)
    {
        // Carregar dados do módulo específico
        $dadosModulo = $this->loadRelatorioModulo($modulo);
        
        if (!$dadosModulo) {
            abort(404, 'Módulo não encontrado');
        }
        
        return view('andamento-projeto.relatorio-individual', compact('dadosModulo'));
    }
    
    /**
     * Carrega dados do arquivo consolidado.json
     *
     * @return array
     */
    private function loadRelatorioConsolidado()
    {
        $path = 'relatorios-projeto-json/consolidado.json';
        
        if (Storage::exists($path)) {
            $content = Storage::get($path);
            return json_decode($content, true);
        }
        
        // Retornar dados padrão se arquivo não existir
        return [
            'titulo' => 'Consolidado Geral - Relatórios de Andamento do Projeto',
            'data_atualizacao' => date('Y-m-d'),
            'total_modulos' => 0,
            'metricas_gerais' => [
                'total_rotas_web' => 0,
                'total_rotas_api' => 0,
                'total_controllers' => 0,
                'total_componentes' => 0,
                'total_tabelas_banco' => 0,
                'total_services' => 0,
                'total_models' => 0,
                'total_pf' => 0
            ],
            'modulos_disponiveis' => [],
            'resumo_por_complexidade' => [
                'baixa_moderada' => 0,
                'alta' => 0,
                'muito_alta' => 0
            ],
            'observacoes_gerais' => 'Nenhum módulo disponível'
        ];
    }
    
    /**
     * Carrega dados de um módulo específico
     *
     * @param string $modulo
     * @return array|null
     */
    private function loadRelatorioModulo($modulo)
    {
        $path = "relatorios-projeto-json/{$modulo}.json";
        
        if (Storage::exists($path)) {
            $content = Storage::get($path);
            return json_decode($content, true);
        }
        
        return null;
    }
    
    /**
     * Exibe o protótipo da planilha orçamentária
     *
     * @return \Illuminate\View\View
     */
    public function planilhaPrototipo()
    {
        return view('andamento-projeto.planilha-prototipo');
    }
}
