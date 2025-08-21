<?php

namespace App\Models\Administracao;

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

    /**
     * Boot do model para aplicar ordenação global
     */
    protected static function boot()
    {
        parent::boot();

        // Ordenação global por nome (alfabética)
        static::addGlobalScope('orderByNome', function ($query) {
            $query->orderBy('nome', 'asc');
        });
    }
}
