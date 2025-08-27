<?php

namespace App\Models\Orcamento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComposicaoPropria extends Model
{
    use HasFactory;

    /**
     * Nome da tabela
     */
    protected $table = 'OC01A_composicoes_proprias';

    /**
     * Campos preenchíveis
     */
    protected $fillable = [
        'codigo',
        'descricao',
        'unidade',
        'valor_total_mat_equip',
        'valor_total_mao_obra',
        'valor_total_geral',
    ];

    /**
     * Casts para campos específicos
     */
    protected $casts = [
        'valor_total_mat_equip' => 'decimal:2',
        'valor_total_mao_obra' => 'decimal:2',
        'valor_total_geral' => 'decimal:2',
    ];

    /**
     * Obtém os itens da composição
     */
    public function itens(): HasMany
    {
        return $this->hasMany(ComposicaoPropriaItem::class, 'composicao_propria_id');
    }

    /**
     * Calcula o total de itens
     */
    public function getTotalItensAttribute(): int
    {
        return $this->itens()->count();
    }

    /**
     * Verifica se a composição tem itens
     */
    public function hasItens(): bool
    {
        return $this->itens()->exists();
    }

    /**
     * Escopo para buscar por descrição
     */
    public function scopePorDescricao($query, $descricao)
    {
        if ($descricao) {
            return $query->where('descricao', 'like', "%{$descricao}%");
        }
        return $query;
    }

    /**
     * Escopo para buscar por código
     */
    public function scopePorCodigo($query, $codigo)
    {
        if ($codigo) {
            return $query->where('codigo', 'like', "%{$codigo}%");
        }
        return $query;
    }
}
