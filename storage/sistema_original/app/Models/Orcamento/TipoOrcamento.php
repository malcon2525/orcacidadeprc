<?php

namespace App\Models\Orcamento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoOrcamento extends Model
{
    use HasFactory;
    
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'tipos_orcamentos';

    protected $fillable = [
        'versao',
        'descricao',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Obtém os grandes itens associados a este tipo de orçamento.
     */
    public function grandesItens()
    {
        return $this->hasMany(GrandeItem::class, 'tipo_orcamento_id');
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeInativo($query)
    {
        return $query->where('ativo', false);
    }

    public function scopePorDescricao($query, $descricao)
    {
        return $query->where('descricao', 'like', '%' . $descricao . '%');
    }
} 