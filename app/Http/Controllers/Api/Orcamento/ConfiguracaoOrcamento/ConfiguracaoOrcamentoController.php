<?php

namespace App\Http\Controllers\Api\Orcamento\ConfiguracaoOrcamento;

use App\Http\Controllers\Controller;
use App\Models\Orcamento\UserOrcamentoContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ConfiguracaoOrcamentoController extends Controller
{
    /**
     * Retorna os dados necessários para configuração do contexto
     */
    public function dados()
    {
        try {
            $user = Auth::user();

            // 1. Buscar entidades orçamentárias vinculadas ao usuário
            $entidadesVinculadas = DB::table('user_entidades_orcamentarias')
                ->join('entidades_orcamentarias', 'user_entidades_orcamentarias.entidade_orcamentaria_id', '=', 'entidades_orcamentarias.id')
                ->where('user_entidades_orcamentarias.user_id', $user->id)
                ->where('user_entidades_orcamentarias.ativo', true)
                ->select([
                    'entidades_orcamentarias.id',
                    'entidades_orcamentarias.jurisdicao_razao_social as nome',
                    'entidades_orcamentarias.jurisdicao_nome_fantasia as nome_fantasia',
                    'entidades_orcamentarias.tipo_organizacao',
                    'entidades_orcamentarias.nivel_administrativo',
                    'entidades_orcamentarias.jurisdicao_uf as uf'
                ])
                ->get()
                ->map(function ($entidade) {
                    return [
                        'id' => $entidade->id,
                        'nome' => $entidade->nome ?: $entidade->nome_fantasia,
                        'nome_completo' => $entidade->nome,
                        'tipo' => $entidade->tipo_organizacao,
                        'nivel' => $entidade->nivel_administrativo,
                        'uf' => $entidade->uf
                    ];
                });

            if ($entidadesVinculadas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não possui vínculos ativos com entidades orçamentárias.'
                ], 403);
            }

            // 2. Buscar datas base disponíveis SINAPI
            $datasBaseSinapi = DB::table('sinapi_composicoes')
                ->select('data_base')
                ->distinct()
                ->orderBy('data_base', 'desc')
                ->get()
                ->map(function ($item) {
                    $data = \Carbon\Carbon::parse($item->data_base);
                    return [
                        'value' => $data->format('Y-m-d'),
                        'label' => $data->format('m/Y'),
                        'label_completo' => $data->format('d/m/Y')
                    ];
                });

            // 3. Buscar datas base disponíveis DERPR
            $datasBaseDerpr = DB::table('derpr_composicoes')
                ->select('data_base')
                ->distinct()
                ->orderBy('data_base', 'desc')
                ->get()
                ->map(function ($item) {
                    $data = \Carbon\Carbon::parse($item->data_base);
                    return [
                        'value' => $data->format('Y-m-d'),
                        'label' => $data->format('m/Y'),
                        'label_completo' => $data->format('d/m/Y')
                    ];
                });

            // 4. Buscar contexto atual do usuário (se existe)
            $contextoAtual = UserOrcamentoContext::getContextoUsuario($user->id);

            return response()->json([
                'success' => true,
                'dados' => [
                    'entidades_vinculadas' => $entidadesVinculadas,
                    'datas_base_sinapi' => $datasBaseSinapi,
                    'datas_base_derpr' => $datasBaseDerpr,
                    'contexto_atual' => $contextoAtual ? $contextoAtual->getContextoFormatado() : null,
                    'tem_contexto_definido' => $contextoAtual !== null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados de configuração orçamentária: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao buscar dados de configuração.'
            ], 500);
        }
    }

    /**
     * Salva ou atualiza o contexto orçamentário do usuário
     */
    public function salvar(Request $request)
    {
        try {
            $user = Auth::user();

            // 1. Validação dos dados
            $validator = Validator::make($request->all(), [
                'entidade_orcamentaria_id' => 'required|integer|exists:entidades_orcamentarias,id',
                'data_base_sinapi' => 'required|date',
                'data_base_derpr' => 'required|date'
            ], [
                'entidade_orcamentaria_id.required' => 'A entidade orçamentária é obrigatória.',
                'entidade_orcamentaria_id.exists' => 'Entidade orçamentária não encontrada.',
                'data_base_sinapi.required' => 'A data base SINAPI é obrigatória.',
                'data_base_sinapi.date' => 'Data base SINAPI deve ser uma data válida.',
                'data_base_derpr.required' => 'A data base DERPR é obrigatória.',
                'data_base_derpr.date' => 'Data base DERPR deve ser uma data válida.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 2. Verificar se o usuário tem vínculo com a entidade selecionada
            $temVinculo = DB::table('user_entidades_orcamentarias')
                ->where('user_id', $user->id)
                ->where('entidade_orcamentaria_id', $request->entidade_orcamentaria_id)
                ->where('ativo', true)
                ->exists();

            if (!$temVinculo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não possui vínculo ativo com a entidade selecionada.'
                ], 403);
            }

            // 3. Verificar se as datas existem nas tabelas
            $dataExisteSinapi = DB::table('sinapi_composicoes')
                ->where('data_base', $request->data_base_sinapi)
                ->exists();

            $dataExisteDerpr = DB::table('derpr_composicoes')
                ->where('data_base', $request->data_base_derpr)
                ->exists();

            if (!$dataExisteSinapi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data base SINAPI não encontrada nas tabelas oficiais.'
                ], 422);
            }

            if (!$dataExisteDerpr) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data base DERPR não encontrada nas tabelas oficiais.'
                ], 422);
            }

            // 4. Salvar contexto
            DB::beginTransaction();

            $contexto = UserOrcamentoContext::setContextoUsuario(
                $user->id,
                $request->entidade_orcamentaria_id,
                $request->data_base_sinapi,
                $request->data_base_derpr
            );

            // 5. Armazenar na sessão também
            session([
                'orcamento_context' => [
                    'entidade_id' => $contexto->entidade_orcamentaria_id,
                    'entidade_nome' => $contexto->entidadeOrcamentaria->jurisdicao_razao_social,
                    'data_base_sinapi' => $contexto->data_base_sinapi->format('Y-m-d'),
                    'data_base_derpr' => $contexto->data_base_derpr->format('Y-m-d'),
                    'definido_em' => now()->format('Y-m-d H:i:s')
                ]
            ]);

            DB::commit();

            Log::info("Contexto orçamentário definido pelo usuário ID: {$user->id}", [
                'entidade_id' => $contexto->entidade_orcamentaria_id,
                'data_sinapi' => $contexto->data_base_sinapi->format('Y-m-d'),
                'data_derpr' => $contexto->data_base_derpr->format('Y-m-d')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contexto orçamentário configurado com sucesso!',
                'dados' => $contexto->getContextoFormatado()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar contexto orçamentário: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao salvar configuração.'
            ], 500);
        }
    }

    /**
     * Retorna o contexto atual do usuário
     */
    public function contextoAtual()
    {
        try {
            $user = Auth::user();
            $contexto = UserOrcamentoContext::getContextoUsuario($user->id);

            if (!$contexto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não possui contexto orçamentário definido.',
                    'tem_contexto' => false
                ]);
            }

            return response()->json([
                'success' => true,
                'tem_contexto' => true,
                'dados' => $contexto->getContextoFormatado()
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar contexto atual: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao buscar contexto.'
            ], 500);
        }
    }

    /**
     * Remove o contexto orçamentário do usuário
     */
    public function limpar()
    {
        try {
            $user = Auth::user();

            DB::beginTransaction();

            // Remove do banco
            $removido = UserOrcamentoContext::removeContextoUsuario($user->id);

            // Remove da sessão
            session()->forget('orcamento_context');

            DB::commit();

            if ($removido) {
                Log::info("Contexto orçamentário removido pelo usuário ID: {$user->id}");
                
                return response()->json([
                    'success' => true,
                    'message' => 'Contexto orçamentário removido com sucesso.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum contexto encontrado para remover.'
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao limpar contexto orçamentário: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao limpar contexto.'
            ], 500);
        }
    }
}
