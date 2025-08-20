<?php

namespace App\Models\TabelaOficial\Derpr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DerprMaoDeObra extends Model
{
    use HasFactory;

    protected $table = 'derpr_mao_de_obra';

    protected $fillable = [
        'codigo_servico',
        'descricao_servico',
        'unidade_servico',
        'data_base',
        'desoneracao',
        'descricao',
        'codigo',
        'eq_salarial',
        'encargos_percentagem',
        'sal_hora',
        'consumo',
        'custo_horario'
    ];

    protected $casts = [
        'data_base' => 'date',
        'eq_salarial' => 'decimal:2',
        'encargos_percentagem' => 'decimal:2',
        'sal_hora' => 'decimal:2',
        'consumo' => 'decimal:2',
        'custo_horario' => 'decimal:2'
    ];
}
