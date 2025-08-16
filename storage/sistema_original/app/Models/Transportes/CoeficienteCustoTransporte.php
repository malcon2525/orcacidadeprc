<?php

namespace App\Models\Transportes;

use Illuminate\Database\Eloquent\Model;

class CoeficienteCustoTransporte extends Model
{
    protected $table = 'coeficiente_custo_transporte';
    protected $fillable = [
        'data_base',
        'desoneracao',
        'custo_transporte_id',
        'coeficiente_x1',
        'coeficiente_x2',
        'termo_independente',
    ];

    public function custoTransporte()
    {
        return $this->belongsTo(CustoTransporte::class, 'custo_transporte_id', 'id');
    }
} 