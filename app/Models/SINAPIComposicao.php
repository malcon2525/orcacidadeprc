<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SINAPIComposicao extends Model
{
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'sinapi_composicoes';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'codigo',
        'descricao',
        'unidade',
        'custo_unitario',
        'data_base',
        'desoneracao'
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'custo_unitario' => 'decimal:2',
        'data_base' => 'date'
    ];

    /**
     * Os atributos que devem ser ocultados para arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
} 