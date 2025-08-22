<?php

namespace App\Models\Importacao\Sinapi;

use Illuminate\Database\Eloquent\Model;

class SinapiComposicaoAnalitico extends Model
{
    protected $table = 'sinapi_composicoes_analitico';

    protected $fillable = [
        'grupo',
        'codigo_composicao',
        'descricao',
        'unidade',
        'situacao',
        'data_base',
        'data_emissao'
    ];

    protected $casts = [
        'data_base' => 'date',
        'data_emissao' => 'date',
    ];

    public function itens()
    {
        return $this->hasMany(SinapiItemAnalitico::class, 'codigo_composicao', 'codigo_composicao')
                    ->whereColumn('data_base', 'sinapi_composicoes_analitico.data_base');
    }
}
