<?php

namespace App\Models\Orcamento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GrandeItem extends Model
{
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'grandes_itens';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'tipo_orcamento_id',
        'descricao',
        'ordem'
    ];

    /**
     * Obtém o tipo de orçamento associado ao grande item.
     */
    public function tipoOrcamento(): BelongsTo
    {
        return $this->belongsTo(TipoOrcamento::class);
    }

    /**
     * Obtém os sub grupos associados ao grande item.
     */
    public function subGrupos(): HasMany
    {
        return $this->hasMany(SubGrupo::class, 'grande_item_id');
    }
} 