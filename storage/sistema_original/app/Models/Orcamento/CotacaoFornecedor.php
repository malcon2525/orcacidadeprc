<?php

namespace App\Models\Orcamento;

use Illuminate\Database\Eloquent\Model;

class CotacaoFornecedor extends Model
{
    protected $table = 'cotacao_fornecedores';

    protected $fillable = [
        'cotacao_id',
        'fornecedor_id',
        'valor_total',
        'mao_obra',
        'mat_equip',
        'data',
        'arquivo',
        'observacoes',
    ];

    public function fornecedor()
    {
        return $this->belongsTo(\App\Models\Orcamento\Fornecedor::class, 'fornecedor_id');
    }
} 