<?php

namespace App\Models\Orcamento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComposicaoPropria extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'descricao',
        'unidade',
        'valor_total_mat_equip',
        'valor_total_mao_obra',
        'valor_total_geral',
    ];

    public function itens()
    {
        return $this->hasMany(ComposicaoPropriaItem::class, 'composicao_propria_id');
    }
}
