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
        'tipo_organizacao',
        'nivel_administrativo',
        'jurisdicao_razao_social',
        'jurisdicao_nome_fantasia',
        'jurisdicao_uf',
        'jurisdicao_codigo_ibge',
        'cep',
        'endereco',
        'telefone',
        'email',
        'cnpj',
        'ativo',
        'observacao',
        'responsavel',
        'responsavel_cargo'
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'ativo' => 'boolean'
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
     * Relacionamento com município (apenas para entidades municipais)
     */
    public function municipio()
    {
        return $this->belongsTo(\App\Models\Administracao\Municipio::class, 'jurisdicao_codigo_ibge', 'codigo_ibge');
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

    /**
     * Verifica se a entidade é municipal
     */
    public function isMunicipal(): bool
    {
        return $this->nivel_administrativo === 'municipal';
    }

    /**
     * Verifica se a entidade é estadual
     */
    public function isEstadual(): bool
    {
        return $this->nivel_administrativo === 'estadual';
    }

    /**
     * Verifica se a entidade é federal
     */
    public function isFederal(): bool
    {
        return $this->nivel_administrativo === 'federal';
    }

    /**
     * Retorna a jurisdição formatada para exibição
     */
    public function getJurisdicaoFormatadaAttribute(): string
    {
        return $this->jurisdicao_nome_fantasia . ' - ' . $this->jurisdicao_uf;
    }

    /**
     * Retorna o tipo formatado para exibição
     */
    public function getTipoFormatadoAttribute(): string
    {
        return strtoupper($this->tipo_organizacao);
    }

    /**
     * Retorna o nível administrativo formatado
     */
    public function getNivelFormatadoAttribute(): string
    {
        $niveis = [
            'municipal' => 'MUNICIPAL',
            'estadual' => 'ESTADUAL',
            'federal' => 'FEDERAL'
        ];

        return $niveis[$this->nivel_administrativo] ?? strtoupper($this->nivel_administrativo);
    }

    /**
     * Scope para filtrar por nível administrativo
     */
    public function scopeDoNivel($query, string $nivel)
    {
        return $query->where('nivel_administrativo', $nivel);
    }

    /**
     * Scope para filtrar por tipo de organização
     */
    public function scopeDoTipo($query, string $tipo)
    {
        return $query->where('tipo_organizacao', $tipo);
    }

    /**
     * Scope para filtrar por UF
     */
    public function scopeDaUf($query, string $uf)
    {
        return $query->where('jurisdicao_uf', $uf);
    }

    /**
     * Scope para filtrar por código IBGE
     */
    public function scopeDaJurisdicao($query, string $codigoIbge)
    {
        return $query->where('jurisdicao_codigo_ibge', $codigoIbge);
    }

    /**
     * Scope para entidades ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para busca por razão social ou nome fantasia
     */
    public function scopeBuscarPorNome($query, string $termo)
    {
        return $query->where(function ($q) use ($termo) {
            $q->where('jurisdicao_razao_social', 'like', '%' . $termo . '%')
              ->orWhere('jurisdicao_nome_fantasia', 'like', '%' . $termo . '%');
        });
    }
}