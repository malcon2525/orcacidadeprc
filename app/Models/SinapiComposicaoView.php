<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SinapiComposicaoView extends Model
{
    use HasFactory;

    protected $table = 'sinapi_composicoes_view';

    protected $fillable = [
        'data_base',
        'desoneracao',
        'grupo',
        'codigo',
        'descricao',
        'unidade',
        'valor_mao_obra',
        'valor_mat_equip',
        'valor_total',
    ];
} 