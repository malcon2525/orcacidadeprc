<?php

namespace App\Http\Controllers\Api\Administracao\EstruturaOrcamento;

use App\Http\Controllers\Controller;
use App\Models\Orcamento\GrandeItem;
use App\Models\Orcamento\SubGrupo;
use App\Models\Orcamento\TipoOrcamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LimparEstruturaController extends Controller
{
    /**
     * Limpa toda a estrutura de orçamento de um tipo específico
     */
    public function limpar($tipoOrcamentoId)
    {
        try {
            // Verificar se o tipo de orçamento existe
            $tipoOrcamento = TipoOrcamento::findOrFail($tipoOrcamentoId);
            
            // Contar itens antes da limpeza para o log
            $totalGrandesItens = GrandeItem::where('tipo_orcamento_id', $tipoOrcamentoId)->count();
            $totalSubgrupos = SubGrupo::whereHas('grandeItem', function($query) use ($tipoOrcamentoId) {
                $query->where('tipo_orcamento_id', $tipoOrcamentoId);
            })->count();
            
            DB::beginTransaction();
            
            // Limpar estrutura existente
            $this->limparEstruturaExistente($tipoOrcamentoId);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Estrutura limpa com sucesso!',
                'data' => [
                    'tipo_orcamento' => $tipoOrcamento->descricao,
                    'grandes_itens_removidos' => $totalGrandesItens,
                    'subgrupos_removidos' => $totalSubgrupos
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar estrutura: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove toda a estrutura existente do tipo de orçamento
     */
    private function limparEstruturaExistente($tipoOrcamentoId)
    {
        // Excluir subgrupos primeiro (devido à chave estrangeira)
        SubGrupo::whereHas('grandeItem', function($query) use ($tipoOrcamentoId) {
            $query->where('tipo_orcamento_id', $tipoOrcamentoId);
        })->delete();
        
        // Excluir grandes itens
        GrandeItem::where('tipo_orcamento_id', $tipoOrcamentoId)->delete();
    }
}
