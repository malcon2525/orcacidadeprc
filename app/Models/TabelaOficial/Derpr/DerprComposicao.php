<?php

namespace App\Models\TabelaOficial\Derpr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DerprComposicao extends Model
{
    use HasFactory;

    protected $table = 'derpr_composicoes';

    protected $fillable = [
        'grupo',
        'data_base',
        'desoneracao',
        'codigo',
        'descricao',
        'unidade',
        'custo_execucao',
        'custo_material',
        'custo_sub_servico',
        'custo_unitario',
        'transporte' 
    ];

    protected $casts = [
        'data_base' => 'date',
        'custo_execucao' => 'decimal:2',
        'custo_material' => 'decimal:2',
        'custo_sub_servico' => 'decimal:2',
        'custo_unitario' => 'decimal:2',
    ];
}
