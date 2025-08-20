<?php

namespace App\Models\TabelaOficial\Derpr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DerprTransporte extends Model
{
    use HasFactory;

    protected $table = 'derpr_transportes';

    protected $fillable = [
        'codigo_servico',
        'descricao_servico',
        'unidade_servico',
        'data_base',
        'desoneracao',
        'descricao',
        'codigo',
        'unidade',
        'formula1',
        'formula2',
        'custo',
        'consumo',
        'custo_unitario'
    ];

    protected $casts = [
        'data_base' => 'date',
        'custo' => 'decimal:2',
        'consumo' => 'decimal:4',
        'custo_unitario' => 'decimal:2'
    ];
}
