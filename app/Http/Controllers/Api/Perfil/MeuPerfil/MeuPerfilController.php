<?php

namespace App\Http\Controllers\Api\Perfil\MeuPerfil;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MeuPerfilController extends Controller
{
    /**
     * Retorna os dados completos do perfil do usuário
     */
    public function dados()
    {
        try {
            $user = Auth::user();
            
            // 1. Dados pessoais básicos
            $dadosPessoais = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'is_ad_user' => $user->isADUser(),
                'login_type' => $user->login_type,
                'ad_domain' => $user->ad_domain
            ];

            // 2. Vínculos organizacionais ativos
            $vinculosAtivos = DB::table('user_entidades_orcamentarias')
                ->join('entidades_orcamentarias', 'user_entidades_orcamentarias.entidade_orcamentaria_id', '=', 'entidades_orcamentarias.id')
                ->where('user_entidades_orcamentarias.user_id', $user->id)
                ->where('user_entidades_orcamentarias.ativo', true)
                ->select([
                    'entidades_orcamentarias.id',
                    'entidades_orcamentarias.jurisdicao_razao_social as nome_entidade',
                    'entidades_orcamentarias.jurisdicao_nome_fantasia as nome_fantasia',
                    'entidades_orcamentarias.tipo_organizacao',
                    'entidades_orcamentarias.nivel_administrativo',
                    'entidades_orcamentarias.jurisdicao_uf as uf',
                    'user_entidades_orcamentarias.data_vinculacao',
                    'user_entidades_orcamentarias.ativo as status_vinculo'
                ])
                ->get()
                ->map(function ($vinculo) {
                    return [
                        'id' => $vinculo->id,
                        'nome_entidade' => $vinculo->nome_entidade ?: $vinculo->nome_fantasia,
                        'razao_social' => $vinculo->nome_entidade,
                        'tipo_organizacao' => $vinculo->tipo_organizacao,
                        'nivel_administrativo' => $vinculo->nivel_administrativo,
                        'uf' => $vinculo->uf,
                        'data_vinculacao' => $vinculo->data_vinculacao,
                        'status_vinculo' => $vinculo->status_vinculo ? 'Ativo' : 'Inativo'
                    ];
                });

            // 3. Histórico completo de vínculos
            $historicoVinculos = DB::table('user_entidades_orcamentarias')
                ->join('entidades_orcamentarias', 'user_entidades_orcamentarias.entidade_orcamentaria_id', '=', 'entidades_orcamentarias.id')
                ->where('user_entidades_orcamentarias.user_id', $user->id)
                ->select([
                    'entidades_orcamentarias.id',
                    'entidades_orcamentarias.jurisdicao_razao_social as nome_entidade',
                    'entidades_orcamentarias.jurisdicao_nome_fantasia as nome_fantasia',
                    'entidades_orcamentarias.tipo_organizacao',
                    'entidades_orcamentarias.nivel_administrativo',
                    'entidades_orcamentarias.jurisdicao_uf as uf',
                    'user_entidades_orcamentarias.data_vinculacao',
                    'user_entidades_orcamentarias.updated_at as data_alteracao',
                    'user_entidades_orcamentarias.ativo as status_vinculo'
                ])
                ->orderBy('user_entidades_orcamentarias.data_vinculacao', 'desc')
                ->get()
                ->map(function ($vinculo) {
                    return [
                        'id' => $vinculo->id,
                        'nome_entidade' => $vinculo->nome_entidade ?: $vinculo->nome_fantasia,
                        'razao_social' => $vinculo->nome_entidade,
                        'tipo_organizacao' => $vinculo->tipo_organizacao,
                        'nivel_administrativo' => $vinculo->nivel_administrativo,
                        'uf' => $vinculo->uf,
                        'data_vinculacao' => $vinculo->data_vinculacao,
                        'data_alteracao' => $vinculo->data_alteracao,
                        'status_vinculo' => $vinculo->status_vinculo ? 'Ativo' : 'Inativo'
                    ];
                });

            // 4. Papéis atribuídos
            $papeisAtribuidos = DB::table('user_roles')
                ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                ->where('user_roles.user_id', $user->id)
                ->select([
                    'roles.id',
                    'roles.name',
                    'roles.display_name',
                    'roles.description',
                    'user_roles.created_at as data_atribuicao'
                ])
                ->get()
                ->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'nome_papel' => $role->display_name ?: $role->name,
                        'descricao' => $role->description,
                        'data_atribuicao' => $role->data_atribuicao
                    ];
                });

            // 5. Permissões específicas (agrupadas por módulo)
            $permissoesEspecificas = DB::table('role_permissions')
                ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                ->join('user_roles', 'role_permissions.role_id', '=', 'user_roles.role_id')
                ->where('user_roles.user_id', $user->id)
                ->select([
                    'permissions.id',
                    'permissions.name',
                    'permissions.display_name',
                    'permissions.description'
                ])
                ->distinct()
                ->get()
                ->groupBy(function ($permission) {
                    // Agrupar por primeira palavra do name (módulo)
                    $parts = explode('_', $permission->name);
                    return ucfirst($parts[0] ?? 'Geral');
                })
                ->map(function ($permissoes, $modulo) {
                    return [
                        'modulo' => $modulo,
                        'permissoes' => $permissoes->map(function ($perm) {
                            return [
                                'id' => $perm->id,
                                'name' => $perm->name,
                                'nome_permissao' => $perm->display_name ?: $perm->name,
                                'descricao' => $perm->description
                            ];
                        })->values()
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'dados' => [
                    'dados_pessoais' => $dadosPessoais,
                    'vinculos_ativos' => $vinculosAtivos,
                    'historico_vinculos' => $historicoVinculos,
                    'papeis_atribuidos' => $papeisAtribuidos,
                    'permissoes_especificas' => $permissoesEspecificas
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados do perfil: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao buscar dados do perfil.'
            ], 500);
        }
    }

    /**
     * Atualiza os dados pessoais do usuário
     */
    public function atualizarDados(Request $request)
    {
        try {
            $user = Auth::user();

            // 1. Verificar se usuário pode alterar dados pessoais
            if ($user->isADUser()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuários do Active Directory não podem alterar dados pessoais. Os dados são gerenciados centralmente pelo AD.',
                    'tipo_erro' => 'ad_user_restriction'
                ], 403);
            }

            // 2. Validação
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => [
                    'required', 
                    'email', 
                    'max:255',
                    Rule::unique('users', 'email')->ignore($user->id)
                ]
            ], [
                'name.required' => 'O nome é obrigatório.',
                'name.max' => 'O nome não pode ter mais de 255 caracteres.',
                'email.required' => 'O email é obrigatório.',
                'email.email' => 'Digite um email válido.',
                'email.max' => 'O email não pode ter mais de 255 caracteres.',
                'email.unique' => 'Este email já está sendo usado por outro usuário.'
            ]);

            // 3. Atualização
            DB::beginTransaction();

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'updated_at' => now()
                ]);
            
            // Refresh do usuário
            $user = User::find($user->id);

            DB::commit();

            Log::info("Dados pessoais atualizados pelo usuário ID: {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Dados pessoais atualizados com sucesso!',
                'dados' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'updated_at' => $user->updated_at
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar dados pessoais: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao atualizar dados.'
            ], 500);
        }
    }

    /**
     * Altera a senha do usuário
     */
    public function alterarSenha(Request $request)
    {
        try {
            $user = Auth::user();

            // 1. Verificar se usuário pode alterar senha
            if ($user->isADUser()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuários do Active Directory não podem alterar a senha. A senha é gerenciada centralmente pelo AD.',
                    'tipo_erro' => 'ad_user_restriction'
                ], 403);
            }

            // 2. Validação
            $validated = $request->validate([
                'senha_atual' => 'required|string',
                'nova_senha' => 'required|string|min:8|confirmed',
                'nova_senha_confirmation' => 'required|string'
            ], [
                'senha_atual.required' => 'A senha atual é obrigatória.',
                'nova_senha.required' => 'A nova senha é obrigatória.',
                'nova_senha.min' => 'A nova senha deve ter pelo menos 8 caracteres.',
                'nova_senha.confirmed' => 'A confirmação da nova senha não confere.',
                'nova_senha_confirmation.required' => 'A confirmação da nova senha é obrigatória.'
            ]);

            // 3. Verificar senha atual
            if (!Hash::check($validated['senha_atual'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha atual incorreta.',
                    'errors' => [
                        'senha_atual' => ['A senha atual está incorreta.']
                    ]
                ], 422);
            }

            // 4. Verificar se a nova senha é diferente da atual
            if (Hash::check($validated['nova_senha'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'A nova senha deve ser diferente da senha atual.',
                    'errors' => [
                        'nova_senha' => ['A nova senha deve ser diferente da senha atual.']
                    ]
                ], 422);
            }

            // 5. Atualização
            DB::beginTransaction();

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'password' => Hash::make($validated['nova_senha']),
                    'updated_at' => now()
                ]);
            
            // Refresh do usuário
            $user = User::find($user->id);

            DB::commit();

            Log::info("Senha alterada pelo usuário ID: {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Senha alterada com sucesso!',
                'dados' => [
                    'ultima_alteracao' => $user->updated_at
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao alterar senha: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao alterar senha.'
            ], 500);
        }
    }
}
