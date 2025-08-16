<?php

namespace App\Models\Importacao\Sinapi;

use Illuminate\Database\Eloquent\Model;

class SinapiComposicao extends Model
{
    protected $table = 'sinapi_composicoes';

    protected $fillable = [
        'grupo',
        'codigo_composicao',
        'descricao',
        'unidade',
        'custo_pr',
        'data_base',
        'data_emissao',
        'desoneracao',
        'log_erro'
    ];

    protected $casts = [
        'data_base' => 'date',
        'data_emissao' => 'date',
    ];
}
