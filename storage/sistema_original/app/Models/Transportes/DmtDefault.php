<?php

namespace App\Models\Transportes;

use Illuminate\Database\Eloquent\Model;

class DmtDefault extends Model
{
    protected $table = 'dmt_default';
    
    protected $fillable = [
        'codigo_material',
        'nome_material',
        'origem',
        'destino',
        'sigla_transporte',
        'tipo',
        'x1',
        'x2'
    ];

    protected $casts = [
        'x1' => 'decimal:2',
        'x2' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
} 