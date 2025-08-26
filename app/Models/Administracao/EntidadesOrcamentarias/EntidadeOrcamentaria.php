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
        'nivel_administrativo',
        'jurisdicao_nome',
        'jurisdicao_codigo_ibge',
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
        'ativo' => 'boolean',
        'populacao' => 'integer'
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
        if ($this->isMunicipal() && $this->municipio) {
            return $this->municipio->nome . ' - PR';
        }
        
        return $this->jurisdicao_nome;
    }

    /**
     * Retorna o tipo formatado para exibição
     */
    public function getTipoFormatadoAttribute(): string
    {
        $tipos = [
            'municipio' => 'MUNICÍPIO',
            'secretaria' => 'SECRETARIA',
            'órgão' => 'ÓRGÃO',
            'autarquia' => 'AUTARQUIA',
            'outros' => 'OUTROS'
        ];

        return $tipos[$this->tipo_organizacao] ?? strtoupper($this->tipo_organizacao);
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
     * Scope para filtrar por jurisdição (código IBGE)
     */
    public function scopeDaJurisdicao($query, string $codigoIbge)
    {
        return $query->where('jurisdicao_codigo_ibge', $codigoIbge);
    }

    /**
     * Scope para entidades municipais de um município específico
     */
    public function scopeDoMunicipio($query, int $municipioId)
    {
        return $query->where('nivel_administrativo', 'municipal')
                    ->whereHas('municipio', function ($q) use ($municipioId) {
                        $q->where('id', $municipioId);
                    });
    }
}
