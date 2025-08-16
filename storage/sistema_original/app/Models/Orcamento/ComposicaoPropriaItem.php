<?php

namespace App\Models\Orcamento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComposicaoPropriaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'composicao_propria_id',
        'referencia',
        'codigo_item',
        'descricao',
        'unidade',
        'valor_mat_equip',
        'valor_mao_obra',
        'valor_total',
        'coeficiente',
        'valor_mat_equip_ajustado',
        'valor_mao_obra_ajustado',
        'valor_total_ajustado',
    ];

    protected $casts = [
        'valor_mat_equip' => 'float',
        'valor_mao_obra' => 'float',
        'valor_total' => 'float',
        'coeficiente' => 'float',
        'valor_mat_equip_ajustado' => 'float',
        'valor_mao_obra_ajustado' => 'float',
        'valor_total_ajustado' => 'float',
    ];

    public function composicaoPropria()
    {
        return $this->belongsTo(ComposicaoPropria::class, 'composicao_propria_id');
    }
}
