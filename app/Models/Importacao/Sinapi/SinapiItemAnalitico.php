<?php

namespace App\Models\Importacao\Sinapi;

use Illuminate\Database\Eloquent\Model;

class SinapiItemAnalitico extends Model
{
    protected $table = 'sinapi_itens_analitico';

    protected $fillable = [
        'codigo_composicao',
        'tipo_item',
        'codigo_item',
        'descricao',
        'unidade',
        'coeficiente',
        'situacao',
        'data_base',
        'data_emissao'
    ];

    protected $casts = [
        'data_base' => 'date',
        'data_emissao' => 'date',
    ];

    public function composicao()
    {
        return $this->belongsTo(SinapiComposicaoAnalitico::class, 'codigo_composicao', 'codigo_composicao')
                    ->whereColumn('data_base', 'sinapi_composicoes_analitico.data_base');
    }
}
