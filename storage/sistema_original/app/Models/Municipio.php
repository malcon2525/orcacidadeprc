<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'municipios';

    protected $fillable = [
        'nome',
        'prefeito',
        'email',
        'endereco_prefeitura',
        'codigo_ibge',
        'populacao',
        'cep',
        'telefone',
        'cnpj'
    ];

    protected $casts = [
    ];
} 