<?php

namespace App\Models\Orcamento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComposicaoPropriaItem extends Model
{
    use HasFactory;

    /**
     * Nome da tabela
     */
    protected $table = 'OC01B_composicao_propria_items';

    /**
     * Campos preenchíveis
     */
    protected $fillable = [
        'composicao_propria_id',
        'referencia',
        'codigo_item',
        'descricao',
        'unidade',
        'valor_mat_equip',
        'valor_mao_obra',
        'valor_total',
        'coeficiente',
        'valor_mat_equip_ajustado',
        'valor_mao_obra_ajustado',
        'valor_total_ajustado',
    ];

    /**
     * Casts para campos específicos
     */
    protected $casts = [
        'valor_mat_equip' => 'decimal:2',
        'valor_mao_obra' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'coeficiente' => 'decimal:5',
        'valor_mat_equip_ajustado' => 'decimal:2',
        'valor_mao_obra_ajustado' => 'decimal:2',
        'valor_total_ajustado' => 'decimal:2',
    ];

    /**
     * Obtém a composição pai
     */
    public function composicaoPropria(): BelongsTo
    {
        return $this->belongsTo(ComposicaoPropria::class, 'composicao_propria_id');
    }

    /**
     * Escopo para buscar por referência
     */
    public function scopePorReferencia($query, $referencia)
    {
        if ($referencia) {
            return $query->where('referencia', $referencia);
        }
        return $query;
    }

    /**
     * Escopo para buscar por código do item
     */
    public function scopePorCodigoItem($query, $codigoItem)
    {
        if ($codigoItem) {
            return $query->where('codigo_item', 'like', "%{$codigoItem}%");
        }
        return $query;
    }

    /**
     * Calcula o valor total ajustado
     */
    public function calcularValoresAjustados(): void
    {
        $this->valor_mat_equip_ajustado = $this->valor_mat_equip * $this->coeficiente;
        $this->valor_mao_obra_ajustado = $this->valor_mao_obra * $this->coeficiente;
        $this->valor_total_ajustado = $this->valor_total * $this->coeficiente;
    }

    /**
     * Verifica se o item é de referência oficial
     */
    public function isReferenciaOficial(): bool
    {
        return in_array($this->referencia, ['SINAPI', 'DERPR']);
    }

    /**
     * Verifica se o item é personalizado
     */
    public function isPersonalizado(): bool
    {
        return $this->referencia === 'PERSONALIZADA';
    }
}
