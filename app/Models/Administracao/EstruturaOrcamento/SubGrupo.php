<?php

namespace App\Models\Administracao\EstruturaOrcamento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubGrupo extends Model
{
    protected $table = 'eo_sub_grupos';
    
    protected $fillable = [
        'eo_grande_item_id',
        'descricao',
        'ordem'
    ];

    /**
     * Obtém o grande item associado ao subgrupo.
     */
    public function grandeItem(): BelongsTo
    {
        return $this->belongsTo(GrandeItem::class, 'eo_grande_item_id');
    }
}
