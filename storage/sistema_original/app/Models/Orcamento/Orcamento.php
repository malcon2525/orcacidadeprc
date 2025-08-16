<?php

namespace App\Models\Orcamento;

use Illuminate\Database\Eloquent\Model;

class Orcamento extends Model
{
    protected $table = 'orcamentos';

    protected $fillable = [
        'descricao'
    ];
} 