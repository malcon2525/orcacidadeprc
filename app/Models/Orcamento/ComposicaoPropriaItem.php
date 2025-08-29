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
        'data_base_sinapi',
        'data_base_derpr',
        'desoneracao',
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
        'data_base_sinapi' => 'date',
        'data_base_derpr' => 'date',
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

    /**
     * Retorna a data base apropriada baseada na referência
     */
    public function getDataBaseAplicavel(): ?string
    {
        if ($this->referencia === 'SINAPI' && $this->data_base_sinapi) {
            return $this->data_base_sinapi->format('Y-m-d');
        }
        
        if ($this->referencia === 'DERPR' && $this->data_base_derpr) {
            return $this->data_base_derpr->format('Y-m-d');
        }
        
        return null;
    }

    /**
     * Retorna a data base formatada para exibição
     */
    public function getDataBaseFormatada(): ?string
    {
        if ($this->referencia === 'SINAPI' && $this->data_base_sinapi) {
            return $this->data_base_sinapi->format('m/Y');
        }
        
        if ($this->referencia === 'DERPR' && $this->data_base_derpr) {
            return $this->data_base_derpr->format('m/Y');
        }
        
        return null;
    }

    /**
     * Scope para buscar por data base SINAPI
     */
    public function scopePorDataBaseSinapi($query, $data)
    {
        if ($data) {
            return $query->where('data_base_sinapi', $data);
        }
        return $query;
    }

    /**
     * Scope para buscar por data base DERPR
     */
    public function scopePorDataBaseDerpr($query, $data)
    {
        if ($data) {
            return $query->where('data_base_derpr', $data);
        }
        return $query;
    }

    /**
     * Scope para buscar por tipo de desoneração
     */
    public function scopePorDesoneracao($query, $desoneracao)
    {
        if ($desoneracao) {
            return $query->where('desoneracao', $desoneracao);
        }
        return $query;
    }

    /**
     * Retorna a descrição da desoneração para exibição
     */
    public function getDesoneracaoFormatada(): ?string
    {
        if (!$this->desoneracao) {
            return null;
        }
        
        return $this->desoneracao === 'com' ? 'Com Desoneração' : 'Sem Desoneração';
    }

    /**
     * Verifica se o item usa desoneração
     */
    public function usaDesoneracao(): bool
    {
        return in_array($this->referencia, ['SINAPI', 'DERPR']) && $this->desoneracao !== null;
    }
}
