<?php

namespace App\Models\TabelaOficial\Sinapi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SinapiMaoDeObra extends Model
{
    use HasFactory;

    protected $table = 'sinapi_mao_de_obra';

    protected $fillable = [
        'codigo_composicao',
        'descricao',
        'unidade',
        'percentagem_pr',
        'data_emissao',
        'data_base',
        'desoneracao',
    ];
}
