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

    /**
     * Relacionamento com município
     */
    public function municipio()
    {
        return $this->belongsTo(\App\Models\Administracao\Municipio::class, 'codigo_ibge', 'codigo_ibge');
    }

    /**
     * Relacionamento com usuários através da tabela pivot
     */
    public function usuarios()
    {
        return $this->belongsToMany(\App\Models\Administracao\User::class, 'user_entidades_orcamentarias', 'entidade_orcamentaria_id', 'user_id')
                    ->withPivot(['ativo', 'data_vinculacao', 'vinculado_por_user_id'])
                    ->withTimestamps();
    }

    /**
     * Relacionamento com usuários ativos
     */
    public function usuariosAtivos()
    {
        return $this->belongsToMany(\App\Models\Administracao\User::class, 'user_entidades_orcamentarias', 'entidade_orcamentaria_id', 'user_id')
                    ->wherePivot('ativo', true)
                    ->withPivot(['ativo', 'data_vinculacao', 'vinculado_por_user_id'])
                    ->withTimestamps();
    }
}
