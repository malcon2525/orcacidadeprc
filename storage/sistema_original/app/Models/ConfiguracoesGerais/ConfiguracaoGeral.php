<?php

namespace App\Models\ConfiguracoesGerais;

use Illuminate\Database\Eloquent\Model;

class ConfiguracaoGeral extends Model
{
    protected $table = 'configuracoes_gerais';

    protected $fillable = [
        'user_id',
        'entidade_orcamentaria_id',
        'data_base_derpr',
        'derpr_desoneracao',
        'data_base_sinapi',
        'sinapi_desoneracao',
    ];

    protected $casts = [
        'data_base_derpr' => 'date',
        'data_base_sinapi' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function entidadeOrcamentaria()
    {
        return $this->belongsTo(\App\Models\Gerais\EntidadeOrcamentaria::class, 'entidade_orcamentaria_id');
    }
} 