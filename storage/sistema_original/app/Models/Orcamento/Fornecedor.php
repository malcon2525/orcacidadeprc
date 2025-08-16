<?php

namespace App\Models\Orcamento;

use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    protected $table = 'fornecedores';

    protected $fillable = [
        'nome_fantasia',
        'cnpj_cpf',
        'telefone',
        'email',
        'site',
        'observacao'
    ];
} 