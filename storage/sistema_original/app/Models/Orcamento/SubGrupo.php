<?php

namespace App\Models\Orcamento;

use App\Models\Orcamento\GrandeItem;
use Illuminate\Database\Eloquent\Model;

class SubGrupo extends Model
{
    protected $table = 'sub_grupos';
    
    protected $fillable = [
        'grande_item_id',
        'descricao',
        'ordem'
    ];

    /**
     * Obtém o grande item associado ao subgrupo.
     */
    public function grandeItem()
    {
        return $this->belongsTo(GrandeItem::class);
    }
} 