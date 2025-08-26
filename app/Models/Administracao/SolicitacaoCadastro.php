<?php

namespace App\Models\Administracao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitacaoCadastro extends Model
{
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'solicitacoes_cadastro';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'entidade_orcamentaria_id',
        'visitante_nome',
        'visitante_email',
        'visitante_telefone',
        'visitante_cpf',
        'visitante_cargo',
        'visitante_municipio',
        'visitante_uf',
        'status',
        'justificativa',
        'observacoes_aprovacao',
        'data_solicitacao',
        'data_aprovacao',
        'aprovado_por_user_id'
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'data_solicitacao' => 'datetime',
        'data_aprovacao' => 'datetime',
    ];

    /**
     * Os atributos que devem ser ocultados para arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Relacionamento com o usuário solicitante
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



    /**
     * Relacionamento com entidade orçamentária escolhida
     */
    public function entidadeOrcamentaria(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Administracao\EntidadesOrcamentarias\EntidadeOrcamentaria::class, 'entidade_orcamentaria_id');
    }

    /**
     * Relacionamento com quem aprovou a solicitação
     */
    public function aprovadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprovado_por_user_id');
    }

    /**
     * Scopes para filtrar por status
     */
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeAprovadas($query)
    {
        return $query->where('status', 'aprovado');
    }

    public function scopeRejeitadas($query)
    {
        return $query->where('status', 'rejeitado');
    }

    /**
     * Scope para ordenar por data mais recente
     */
    public function scopeRecentes($query)
    {
        return $query->orderBy('data_solicitacao', 'desc');
    }

    /**
     * Scopes para filtros da tela de aprovação
     */
    public function scopePorNome($query, $nome)
    {
        return $query->where('visitante_nome', 'like', '%' . $nome . '%');
    }

    public function scopePorEmail($query, $email)
    {
        return $query->where('visitante_email', 'like', '%' . $email . '%');
    }

    public function scopePorUf($query, $uf)
    {
        return $query->where('visitante_uf', $uf);
    }

    public function scopePorMunicipio($query, $municipio)
    {
        return $query->where('visitante_municipio', 'like', '%' . $municipio . '%');
    }

    public function scopePorEntidade($query, $entidadeId)
    {
        return $query->where('entidade_orcamentaria_id', $entidadeId);
    }

    /**
     * Mutator para definir data_solicitacao automaticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->data_solicitacao) {
                $model->data_solicitacao = now();
            }
        });
    }
}
