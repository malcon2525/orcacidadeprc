<?php

namespace App\Models\Orcamento;

use Illuminate\Database\Eloquent\Model;

class Cotacao extends Model
{
    protected $table = 'cotacoes';

    protected $fillable = [
        'codigo',
        'descricao',
        'entidade_orcamentaria_id',
        'orcamento_id',
        'origem',
        'unidade',
        'valor_final',
        'tipo_valor_final',
    ];

    public function entidadeOrcamentaria()
    {
        return $this->belongsTo(\App\Models\Gerais\EntidadeOrcamentaria::class, 'entidade_orcamentaria_id');
    }

    public function fornecedores()
    {
        return $this->hasMany(\App\Models\Orcamento\CotacaoFornecedor::class, 'cotacao_id')->with('fornecedor');
    }
} 