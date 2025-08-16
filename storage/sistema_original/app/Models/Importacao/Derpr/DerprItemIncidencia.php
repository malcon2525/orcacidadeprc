<?php
namespace App\Models\Importacao\Derpr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DerprItemIncidencia extends Model
{
    use HasFactory;

    protected $table = 'derpr_itens_incidencia';

    protected $fillable = [
        'codigo_servico',
        'descricao_servico',
        'unidade_servico',
        'data_base',
        'desoneracao',
        'descricao',
        'codigo',
        'percentagem',
        'tem_mo',
        'custo'
    ];

    protected $casts = [
        'data_base' => 'date',
        'percentagem' => 'decimal:2',
        'custo' => 'decimal:2'
    ];
}
