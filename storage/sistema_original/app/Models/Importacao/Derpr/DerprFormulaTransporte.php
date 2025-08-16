<?php

namespace App\Models\Importacao\Derpr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DerprFormulaTransporte extends Model
{
    use HasFactory;

    /**
     * A tabela associada ao model.
     *
     * @var string
     */
    protected $table = 'derpr_formula_transportes';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'data_base',
        'desoneracao',
        'codigo',
        'descricao',
        'unidade',
        'formula_transporte',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_base' => 'date',
        'desoneracao' => 'string',
        'codigo' => 'string',
        'descricao' => 'string',
        'unidade' => 'string',
        'formula_transporte' => 'string',
    ];

    /**
     * Os atributos que devem ser ocultados para arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Escopo para filtrar por data base.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $dataBase
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorDataBase($query, $dataBase)
    {
        return $query->where('data_base', $dataBase);
    }

    /**
     * Escopo para filtrar por tipo de desoneração.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $desoneracao
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorDesoneracao($query, $desoneracao)
    {
        return $query->where('desoneracao', $desoneracao);
    }

    /**
     * Escopo para filtrar por código.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $codigo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo', $codigo);
    }
}
