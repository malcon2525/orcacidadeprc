<?php

namespace App\Models\Transportes;

use Illuminate\Database\Eloquent\Model;

class Dmt extends Model
{
    protected $table = 'dmts';
    protected $fillable = [
        'codigo_material',
        'nome_material',
        'origem',
        'destino',
        'sigla_transporte',
        'tipo',
        'x1',
        'x2',
        'id_municipio',
        'id_entidade_orcamentaria',
    ];

    protected $casts = [
        'x1' => 'decimal:2',
        'x2' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function municipio()
    {
        return $this->belongsTo(\App\Models\Municipio::class, 'id_municipio');
    }

    public function entidadeOrcamentaria()
    {
        return $this->belongsTo(\App\Models\Gerais\EntidadeOrcamentaria::class, 'id_entidade_orcamentaria');
    }
} 