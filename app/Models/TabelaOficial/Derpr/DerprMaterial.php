<?php

namespace App\Models\TabelaOficial\Derpr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DerprMaterial extends Model
{
    use HasFactory;

    protected $table = 'derpr_materiais';

    protected $fillable = [
        'codigo_servico',
        'descricao_servico',
        'unidade_servico',
        'data_base',
        'desoneracao',
        'descricao',
        'codigo',
        'unidade',
        'custo_unitario',
        'consumo',
        'custo_unitario_final'
    ];

    protected $casts = [
        'data_base' => 'date',
        'custo_unitario' => 'decimal:2',
        'consumo' => 'decimal:4',
        'custo_unitario_final' => 'decimal:2'
    ];
}
