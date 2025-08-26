<?php

namespace App\Http\Controllers\Web\Publico\SolicitacaoCadastro;

use App\Http\Controllers\Controller;
use App\Models\Administracao\SolicitacaoCadastro;
use App\Models\Administracao\User;
use App\Models\Administracao\Municipio;
use App\Models\Administracao\EntidadesOrcamentarias\EntidadeOrcamentaria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class SolicitacaoCadastroController extends Controller
{
    /**
     * Exibe o formulário público de solicitação de cadastro
     */
    public function index()
    {
        return view('publico.solicitar-cadastro');
    }

    /**
     * Retorna entidades orçamentárias para o select do formulário
     */
    public function dadosFormulario(): JsonResponse
    {
        try {
            $entidades = EntidadeOrcamentaria::select('id', 'jurisdicao_nome_fantasia as nome', 'tipo_organizacao', 'nivel_administrativo')
                ->where('ativo', true)
                ->orderBy('jurisdicao_nome_fantasia')
                ->get();

            return response()->json([
                'entidades' => $entidades
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar dados do formulário',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Processa a solicitação de cadastro
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Dados do usuário
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'username' => 'nullable|string|max:255|unique:users,username',
            'password' => 'required|confirmed|min:3',
            
            // Dados pessoais do visitante
            'telefone' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:14',
            'cargo' => 'nullable|string|max:255',
            
            // Localização do visitante
            'visitante_municipio' => 'required|string|max:255',
            'visitante_uf' => 'required|string|size:2',
            
            // Entidade solicitada
            'entidade_orcamentaria_id' => 'required|exists:entidades_orcamentarias,id',
            'justificativa' => 'required|string|max:1000'
        ], [
            'name.required' => 'O nome é obrigatório',
            'email.required' => 'O email é obrigatório',
            'email.email' => 'Email deve ter formato válido',
            'email.unique' => 'Este email já está cadastrado no sistema',
            'username.unique' => 'Este nome de usuário já existe',
            'password.required' => 'A senha é obrigatória',
            'password.confirmed' => 'As senhas não conferem',
            'password.min' => 'A senha deve ter pelo menos 3 caracteres',
            'visitante_municipio.required' => 'Seu município é obrigatório',
            'visitante_uf.required' => 'Sua UF é obrigatória',
            'visitante_uf.size' => 'UF deve ter exatamente 2 caracteres',
            'entidade_orcamentaria_id.required' => 'A entidade orçamentária é obrigatória',
            'entidade_orcamentaria_id.exists' => 'Entidade orçamentária inválida',
            'justificativa.required' => 'A justificativa é obrigatória',
            'justificativa.max' => 'A justificativa não pode exceder 1000 caracteres'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Criar usuário (inativo inicialmente)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username ?? $request->email,
                'password' => Hash::make($request->password),
                'is_active' => false, // Inativo até aprovação
                'login_type' => 'local'
            ]);

            // Criar solicitação de cadastro com nova estrutura
            $solicitacao = SolicitacaoCadastro::create([
                'user_id' => $user->id,
                'entidade_orcamentaria_id' => $request->entidade_orcamentaria_id,
                'visitante_nome' => $request->name,
                'visitante_email' => $request->email,
                'visitante_telefone' => $request->telefone,
                'visitante_cpf' => $request->cpf,
                'visitante_cargo' => $request->cargo,
                'visitante_municipio' => $request->visitante_municipio,
                'visitante_uf' => strtoupper($request->visitante_uf),
                'status' => 'pendente',
                'justificativa' => $request->justificativa,
                'data_solicitacao' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Sua solicitação será analisada e você receberá um retorno em breve.',
                'solicitacao_id' => $solicitacao->id
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao processar solicitação',
                'errors' => ['geral' => ['Erro interno: ' . $e->getMessage()]]
            ], 500);
        }
    }

    /**
     * Consulta status de uma solicitação (por email/ID)
     */
    public function consultarStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'solicitacao_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = SolicitacaoCadastro::with([
                'user:id,name,email',
                'entidadeOrcamentaria:id,jurisdicao_nome_fantasia,tipo_organizacao'
            ])
            ->whereHas('user', function ($q) use ($request) {
                $q->where('email', $request->email);
            });

            if ($request->filled('solicitacao_id')) {
                $query->where('id', $request->solicitacao_id);
            }

            $solicitacoes = $query->orderBy('data_solicitacao', 'desc')->get();

            if ($solicitacoes->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhuma solicitação encontrada para este email'
                ], 404);
            }

            return response()->json([
                'solicitacoes' => $solicitacoes->map(function ($solicitacao) {
                    return [
                        'id' => $solicitacao->id,
                        'status' => $solicitacao->status,
                        'status_label' => $this->getStatusLabel($solicitacao->status),
                        'visitante_municipio' => $solicitacao->visitante_municipio,
                        'visitante_uf' => $solicitacao->visitante_uf,
                        'entidade' => $solicitacao->entidadeOrcamentaria->jurisdicao_nome_fantasia,
                        'entidade_tipo' => $solicitacao->entidadeOrcamentaria->tipo_organizacao,
                        'data_solicitacao' => $solicitacao->data_solicitacao->format('d/m/Y H:i'),
                        'data_aprovacao' => $solicitacao->data_aprovacao?->format('d/m/Y H:i'),
                        'observacoes_aprovacao' => $solicitacao->observacoes_aprovacao
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao consultar status',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna label amigável para o status
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pendente' => 'Aguardando Análise',
            'aprovado' => 'Aprovada',
            'rejeitado' => 'Rejeitada',
            default => ucfirst($status)
        };
    }
}
