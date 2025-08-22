<?php

namespace App\Models\TabelaOficial\Sinapi;

use Illuminate\Database\Eloquent\Model;

class SinapiInsumo extends Model
{
    protected $table = 'sinapi_insumos';

    protected $fillable = [
        'classificacao',
        'codigo_insumo',
        'descricao',
        'unidade',
        'custo_pr',
        'data_base',
        'data_emissao',
        'desoneracao',
    ];

    protected $casts = [
        'data_base' => 'date',
        'data_emissao' => 'date',
        'custo_pr' => 'decimal:2',
    ];
}
