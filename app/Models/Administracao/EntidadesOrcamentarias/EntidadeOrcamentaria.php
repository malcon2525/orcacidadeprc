<?php

namespace App\Models\Administracao\EntidadesOrcamentarias;

use Illuminate\Database\Eloquent\Model;

class EntidadeOrcamentaria extends Model
{
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'entidades_orcamentarias';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'razao_social',
        'nome_fantasia',
        'tipo_organizacao',
        'email',
        'endereco',
        'codigo_ibge',
        'populacao',
        'cep',
        'telefone',
        'cnpj',
        'responsavel',
        'responsavel_cargo',
        'responsavel_telefone',
        'responsavel_email',
        'ativo'
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array
     */
    protected $casts = [
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
