<?php

namespace App\Models\Administracao\EstruturaOrcamento;

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
    protected $table = 'eo_grandes_itens';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'eo_tipo_orcamento_id',
        'descricao',
        'ordem'
    ];

    /**
     * Obtém o tipo de orçamento associado ao grande item.
     */
    public function tipoOrcamento(): BelongsTo
    {
        return $this->belongsTo(TipoOrcamento::class, 'eo_tipo_orcamento_id');
    }

    /**
     * Obtém os sub grupos associados ao grande item.
     */
    public function subItens(): HasMany
    {
        return $this->hasMany(SubGrupo::class, 'eo_grande_item_id');
    }
}
