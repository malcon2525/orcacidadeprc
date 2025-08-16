<?php

namespace App\Models\Orcamento;

use App\Models\Gerais\EntidadeOrcamentaria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BDI extends Model
{
    protected $table = 'bdis';

    protected $fillable = [
        'nome',
        'origem',
        'orcamento_id',
        'entidade_orcamentaria_id',
        'analisado',
        // Serviços
        'adm_central_servico',
        'riscos_servico',
        'seguros_garantia_servico',
        'desp_financeira_servico',
        'lucro_servico',
        // Materiais
        'adm_central_material',
        'riscos_material',
        'seguros_garantia_material',
        'desp_financeira_material',
        'lucro_material',
        // Equipamentos
        'adm_central_equipamento',
        'riscos_equipamento',
        'seguros_garantia_equipamento',
        'desp_financeira_equipamento',
        'lucro_equipamento',
        // Impostos
        'iss_municipio',
        'base_calculo_mao_obra',
        'iss_calculado',
        'pis',
        'cofins',
        'cprb',
        'impostos_total',
        // Resultado final
        'bdi_servico',
        'bdi_material',
        'bdi_equipamento'
    ];

    protected $casts = [
        'adm_central_servico' => 'decimal:2',
        'riscos_servico' => 'decimal:2',
        'seguros_garantia_servico' => 'decimal:2',
        'desp_financeira_servico' => 'decimal:2',
        'lucro_servico' => 'decimal:2',
        'adm_central_material' => 'decimal:2',
        'riscos_material' => 'decimal:2',
        'seguros_garantia_material' => 'decimal:2',
        'desp_financeira_material' => 'decimal:2',
        'lucro_material' => 'decimal:2',
        'adm_central_equipamento' => 'decimal:2',
        'riscos_equipamento' => 'decimal:2',
        'seguros_garantia_equipamento' => 'decimal:2',
        'desp_financeira_equipamento' => 'decimal:2',
        'lucro_equipamento' => 'decimal:2',
        'iss_municipio' => 'decimal:2',
        'base_calculo_mao_obra' => 'decimal:2',
        'iss_calculado' => 'decimal:2',
        'pis' => 'decimal:2',
        'cofins' => 'decimal:2',
        'cprb' => 'decimal:2',
        'impostos_total' => 'decimal:2',
        'bdi_servico' => 'decimal:2',
        'bdi_material' => 'decimal:2',
        'bdi_equipamento' => 'decimal:2'
    ];

    public function orcamento(): BelongsTo
    {
        return $this->belongsTo(Orcamento::class);
    }

    public function entidadeOrcamentaria(): BelongsTo
    {
        return $this->belongsTo(EntidadeOrcamentaria::class);
    }
} 