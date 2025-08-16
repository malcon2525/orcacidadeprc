<?php

namespace App\Models\Transportes;

use Illuminate\Database\Eloquent\Model;

class CustoTransporte extends Model
{
    protected $table = 'custo_transporte';
    protected $fillable = [
        'sigla',
        'codigo',
        'descricao',
        'unidade',
    ];

    public function coeficientes()
    {
        return $this->hasMany(CoeficienteCustoTransporte::class, 'custo_transporte_id', 'id');
    }
} 