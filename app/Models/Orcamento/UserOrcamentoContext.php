<?php

namespace App\Models\Orcamento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Administracao\User;
use App\Models\Administracao\EntidadesOrcamentarias\EntidadeOrcamentaria;

class UserOrcamentoContext extends Model
{
    /**
     * Nome da tabela
     */
    protected $table = 'oc00a_user_orcamento_context';

    /**
     * Campos que podem ser preenchidos em massa
     */
    protected $fillable = [
        'user_id',
        'entidade_orcamentaria_id',
        'data_base_sinapi',
        'data_base_derpr'
    ];

    /**
     * Campos que devem ser tratados como datas
     */
    protected $casts = [
        'data_base_sinapi' => 'date',
        'data_base_derpr' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com EntidadeOrcamentaria
     */
    public function entidadeOrcamentaria(): BelongsTo
    {
        return $this->belongsTo(EntidadeOrcamentaria::class);
    }

    /**
     * Busca o contexto de um usuário específico
     */
    public static function getContextoUsuario(int $userId): ?self
    {
        return self::where('user_id', $userId)
            ->with(['entidadeOrcamentaria'])
            ->first();
    }

    /**
     * Define ou atualiza o contexto de um usuário
     */
    public static function setContextoUsuario(
        int $userId,
        int $entidadeOrcamentariaId,
        string $dataBaseSinapi,
        string $dataBaseDerpr
    ): self {
        return self::updateOrCreate(
            ['user_id' => $userId],
            [
                'entidade_orcamentaria_id' => $entidadeOrcamentariaId,
                'data_base_sinapi' => $dataBaseSinapi,
                'data_base_derpr' => $dataBaseDerpr
            ]
        );
    }

    /**
     * Remove o contexto de um usuário
     */
    public static function removeContextoUsuario(int $userId): bool
    {
        return self::where('user_id', $userId)->delete();
    }

    /**
     * Verifica se o usuário tem contexto definido
     */
    public static function usuarioTemContexto(int $userId): bool
    {
        return self::where('user_id', $userId)->exists();
    }

    /**
     * Retorna dados formatados para exibição
     */
    public function getContextoFormatado(): array
    {
        return [
            'entidade' => [
                'id' => $this->entidade_orcamentaria_id,
                'nome' => $this->entidadeOrcamentaria->jurisdicao_razao_social ?? 'N/A'
            ],
            'sinapi' => [
                'data' => $this->data_base_sinapi->format('Y-m-d'),
                'data_formatada' => $this->data_base_sinapi->format('d/m/Y')
            ],
            'derpr' => [
                'data' => $this->data_base_derpr->format('Y-m-d'),
                'data_formatada' => $this->data_base_derpr->format('d/m/Y')
            ],
            'atualizado_em' => $this->updated_at->format('d/m/Y H:i:s')
        ];
    }

    /**
     * Scope para buscar por entidade
     */
    public function scopePorEntidade($query, int $entidadeId)
    {
        return $query->where('entidade_orcamentaria_id', $entidadeId);
    }

    /**
     * Scope para buscar por data SINAPI
     */
    public function scopePorDataSinapi($query, string $data)
    {
        return $query->where('data_base_sinapi', $data);
    }

    /**
     * Scope para buscar por data DERPR
     */
    public function scopePorDataDerpr($query, string $data)
    {
        return $query->where('data_base_derpr', $data);
    }
}
