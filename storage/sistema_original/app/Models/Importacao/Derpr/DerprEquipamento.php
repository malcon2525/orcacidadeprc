<?php

namespace App\Models\Importacao\Derpr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DerprEquipamento extends Model
{
    use HasFactory;

    protected $table = 'derpr_equipamentos';

    protected $fillable = [
        'codigo_servico',
        'descricao_servico',
        'unidade_servico',
        'data_base',
        'desoneracao',
        'descricao',
        'codigo_equipamento',
        'quantidade',
        'ut_produtiva',
        'ut_improdutiva',
        'vl_hr_prod',
        'vl_hr_imp',
        'custo_horario'
    ];

    protected $casts = [
        'data_base' => 'date',
        'quantidade' => 'decimal:2',
        'ut_produtiva' => 'decimal:2',
        'ut_improdutiva' => 'decimal:2',
        'vl_hr_prod' => 'decimal:2',
        'vl_hr_imp' => 'decimal:2',
        'custo_horario' => 'decimal:2'
    ];
}
