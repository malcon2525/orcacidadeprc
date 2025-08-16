<template>
  <!-- Modal de Cálculo de Transporte DER-PR -->
  <div class="modal fade" :id="modalId" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <!-- Header Personalizado do Modal -->
        <div class="modal-header custom-modal-header">
          <div class="d-flex align-items-center">
            <div class="header-icon">
              <i class="fas fa-calculator"></i>
            </div>
            <h5 class="modal-title mb-0" id="modalLabel">
              Cálculo de Transporte
            </h5>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- Loading State -->
          <div v-if="carregando" class="text-center py-5">
            <div class="spinner-border" role="status"></div>
          </div>
          <div v-else>
            <!-- Estado vazio -->
            <div v-if="itens.length === 0" class="alert alert-info">
              Nenhum item de transporte encontrado para esta composição.
            </div>
            <div v-else>
              <!-- Lista de itens de transporte em accordion -->
              <div class="accordion" :id="'accordionTransporte'+uniqueId">
                <div class="accordion-item" v-for="item in itens" :key="item.id">
                  <!-- Header do accordion com resumo do item -->
                  <h2 class="accordion-header" :id="'heading'+item.id">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      :data-bs-target="'#collapse'+item.id" aria-expanded="false" :aria-controls="'collapse'+item.id">
                      <div class="w-100 d-flex flex-wrap align-items-center justify-content-between">
                        <span><strong>{{ item.descricao }}</strong> <small class="text-muted">[{{ item.codigo }}] ({{ item.unidade }})</small></span>
                        <span>
                          <span class="badge bg-success ms-2">Transporte: {{ formatarMoeda(item.valorTransporteFinal) }}</span>
                        </span>
                      </div>
                    </button>
                  </h2>
                  
                  <!-- Conteúdo do accordion com formulário de cálculo -->
                  <div :id="'collapse'+item.id" class="accordion-collapse collapse" :aria-labelledby="'heading'+item.id"
                    :data-bs-parent="'#accordionTransporte'+uniqueId">
                    <div class="accordion-body">
                      <div class="row g-2 align-items-end">
                        <!-- Tipo de Cálculo -->
                        <div class="col-md-3">
                          <label class="form-label">Tipo de Cálculo</label>
                          <select class="form-select" v-model="item.tipoCalculo">
                            <option value="local" v-if="item.formula2">Local</option>
                            <option value="comercial" v-if="item.formula1">Comercial</option>
                            <option value="outro_derpr" v-if="outrosDerprSiglas.length">Outro - DERPR</option>
                            <option value="manual">Manual</option>
                          </select>
                        </div>
                        
                        <!-- Campos X1 e X2 para fórmulas -->
                        <div class="col-md-2" v-if="item.tipoCalculo !== 'manual' && item.tipoCalculo !== 'outro_derpr'">
                          <label class="form-label">X1 (Km)</label>
                          <input type="number" class="form-control" v-model.number="item.x1" step="0.01">
                        </div>
                        <div class="col-md-2" v-if="item.tipoCalculo !== 'manual' && item.tipoCalculo !== 'outro_derpr'">
                          <label class="form-label">X2 (Km)</label>
                          <input type="number" class="form-control" v-model.number="item.x2" step="0.01">
                        </div>
                        
                        <!-- Campo de valor manual -->
                        <template v-if="item.tipoCalculo === 'manual'">
                          <div class="col-md-3">
                            <label class="form-label">Valor Manual (R$)</label>
                            <input type="number" class="form-control" v-model.number="item.valorManual" step="0.01" placeholder="0,00">
                          </div>
                        </template>
                        
                        <!-- Campos para Outro DER-PR -->
                        <template v-if="item.tipoCalculo === 'outro_derpr'">
                          <div class="col-md-3">
                            <label class="form-label">Tipo de Transporte (Sigla)</label>
                            <select class="form-select" v-model="item.siglaOutroDerpr">
                              <option value="">Selecione...</option>
                              <option v-for="sigla in outrosDerprSiglas" :key="sigla" :value="sigla">
                                {{ sigla }} - {{ outrosDerprFormulas[sigla]?.descricao }}
                              </option>
                            </select>
                          </div>
                          <div class="col-md-2">
                            <label class="form-label">X1</label>
                            <input type="number" class="form-control" v-model.number="item.x1" step="0.01">
                          </div>
                          <div class="col-md-2">
                            <label class="form-label">X2</label>
                            <input type="number" class="form-control" v-model.number="item.x2" step="0.01">
                          </div>
                        </template>
                        
                        <!-- Área de exibição do cálculo -->
                        <div class="col-md-5">
                          <label class="form-label">Cálculo</label>
                          <div class="form-control bg-light">
                            <div v-if="item.tipoCalculo === 'manual'">
                              <div>
                                <span class="text-muted">Valor manual:</span> {{ formatarMoeda(item.valorManual || 0) }}
                              </div>
                              <div>
                                <span class="text-muted">Consumo:</span> <strong>{{ item.consumo }}</strong>
                              </div>
                            </div>
                            <div v-else>
                              <div>
                                <span class="text-muted">Fórmula:</span> {{ mostrarFormula(item) }}
                              </div>
                              <div>
                                <span class="text-muted">Resultado da fórmula:</span> {{ formatarMoeda(item.valorFormula) }}
                              </div>
                              <div>
                                <span class="text-muted">Consumo:</span> <strong>{{ item.consumo }}</strong>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <!-- Valor final do transporte -->
                        <div class="col-md-3">
                          <label class="form-label">Valor do Transporte</label>
                          <div class="form-control bg-success bg-opacity-10">
                            {{ formatarMoeda(item.valorTransporteFinal) }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Resumo dos totais -->
              <div class="mt-4 p-3 bg-light rounded">
                <div class="row">
                  <div class="col-md-4">
                    <strong>Soma dos Transportes:</strong> {{ formatarMoeda(totalTransporte) }}
                  </div>
                  <div class="col-md-4">
                    <strong>Custo do Serviço:</strong> {{ formatarMoeda(custoTotal) }}
                  </div>
                  <div class="col-md-4">
                    <strong>Total com Transporte:</strong> {{ formatarMoeda(totalGeral) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer do Modal -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" @click="confirmar" :disabled="!podeConfirmar">Confirmar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import axios from 'axios'

/**
 * Componente Modal para Cálculo de Transporte DER-PR
 * 
 * FUNCIONALIDADE:
 * Permite ao usuário calcular custos de transporte para composições DER-PR
 * que possuem campo "transporte" = "A acrescer". Oferece 4 tipos de cálculo:
 * - Local: usa formula2 do item
 * - Comercial: usa formula1 do item  
 * - Outro DER-PR: fórmulas de outros departamentos
 * - Manual: valor informado diretamente pelo usuário
 * 
 * PROPS:
 * - modalId: ID único do modal
 * - codigo: Código da composição DER-PR
 * - dataBase: Data base para cálculo
 * - desoneracao: Tipo de desoneração (com/sem)
 * - custoTotal: Custo total do serviço (para somar ao transporte)
 * 
 * EMITS:
 * - valor-calculado: Retorna dados do cálculo para o componente pai
 */
export default {
  name: 'ModalCalculoTransporte',
  props: {
    modalId: {
      type: String,
      required: true
    },
    codigo: {
      type: String,
      required: true
    },
    dataBase: {
      type: String,
      required: true
    },
    desoneracao: {
      type: String,
      required: true
    },
    custoTotal: {
      type: Number,
      default: 0
    }
  },
  emits: ['valor-calculado'],
  setup(props, { emit }) {
    // ===== REACTIVE STATE =====
    const carregando = ref(false)
    const itens = ref([])
    const outrosDerprFormulas = ref({})
    const outrosDerprSiglas = ref([])
    const uniqueId = ref(Math.random().toString(36).substr(2, 9))

    // ===== COMPUTED PROPERTIES =====
    /**
     * Calcula o total de transporte somando todos os itens
     */
    const totalTransporte = computed(() => 
      itens.value.reduce((soma, item) => soma + (Number(item.valorTransporteFinal) || 0), 0)
    )

    /**
     * Calcula o total geral (custo do serviço + transporte)
     */
    const totalGeral = computed(() => props.custoTotal + totalTransporte.value)

    /**
     * Verifica se pode confirmar (tem itens carregados)
     */
    const podeConfirmar = computed(() => itens.value.length > 0)

    // ===== METHODS =====
    /**
     * Carrega os itens de transporte da composição
     * Chamado na montagem do componente e quando mudam os props
     */
    const carregarItens = async () => {
      // Valida se os props necessários estão presentes
      if (!props.codigo || !props.dataBase || !props.desoneracao) {
        return
      }

      carregando.value = true
      try {
        const response = await axios.get('/api/preco/derpr/transporte/itens', {
          params: {
            codigo_servico: props.codigo,
            data_base: props.dataBase,
            desoneracao: props.desoneracao
          }
        })
        
        // Inicializa os itens com valores padrão
        itens.value = response.data.map(item => ({
          ...item,
          valorManual: 0,
          tipoCalculo: 'manual' // Define manual como padrão
        }))
        
        // Carrega fórmulas outros DER-PR
        await carregarOutrosDerpr()
        
      } catch (error) {
        console.error('Erro ao carregar itens:', error)
      } finally {
        carregando.value = false
      }
    }

    /**
     * Carrega as fórmulas de outros DER-PR disponíveis
     */
    const carregarOutrosDerpr = async () => {
      try {
        const response = await axios.get('/api/preco/derpr/transporte/formulas', {
          params: {
            codigo: props.codigo,
            data_base: props.dataBase,
            desoneracao: props.desoneracao
          }
        })
        
        outrosDerprFormulas.value = response.data.outros_derpr || {}
        outrosDerprSiglas.value = Object.keys(outrosDerprFormulas.value)
        
      } catch (error) {
        console.error('Erro ao carregar fórmulas outros DER-PR:', error)
      }
    }

    /**
     * Calcula o valor da fórmula aplicando os coeficientes
     * @param {string} formula - Fórmula no formato "0,99x1 + 1,19x2 + 2,48"
     * @param {number} x1 - Valor de X1
     * @param {number} x2 - Valor de X2
     * @returns {number} Resultado do cálculo
     */
    const calcularFormula = (formula, x1, x2) => {
      // Extrai coeficientes da fórmula usando regex
      let a = 0, b = 0, c = 0
      let f = formula.replace(/,/g, '.').replace(/ /g, '')
      
      const x1Match = f.match(/([+-]?\d*\.?\d+)x1/)
      const x2Match = f.match(/([+-]?\d*\.?\d+)x2/)
      const cMatch = f.match(/([+-]\d*\.?\d+)(?!.*x)/)
      
      if (x1Match) a = parseFloat(x1Match[1])
      if (x2Match) b = parseFloat(x2Match[1])
      if (cMatch) c = parseFloat(cMatch[1])
      
      return a * (Number(x1) || 0) + b * (Number(x2) || 0) + c
    }

    /**
     * Retorna a fórmula a ser exibida baseada no tipo de cálculo
     * @param {object} item - Item de transporte
     * @returns {string} Fórmula ou texto descritivo
     */
    const mostrarFormula = (item) => {
      if (item.tipoCalculo === 'local') return item.formula2
      if (item.tipoCalculo === 'comercial') return item.formula1
      if (item.tipoCalculo === 'outro_derpr' && item.siglaOutroDerpr) {
        return outrosDerprFormulas.value[item.siglaOutroDerpr]?.formula
      }
      return 'Manual'
    }

    /**
     * Formata valor para moeda brasileira
     * @param {number} valor - Valor a ser formatado
     * @returns {string} Valor formatado como moeda
     */
    const formatarMoeda = (valor) => {
      return new Intl.NumberFormat('pt-BR', { 
        style: 'currency', 
        currency: 'BRL' 
      }).format(valor)
    }

    /**
     * Confirma o cálculo e emite os dados para o componente pai
     */
    const confirmar = () => {
      emit('valor-calculado', {
        itens: itens.value.map(item => ({
          id: item.id,
          tipoCalculo: item.tipoCalculo,
          x1: item.x1,
          x2: item.x2,
          valorManual: item.valorManual,
          siglaOutroDerpr: item.siglaOutroDerpr,
          valorFormula: item.valorFormula,
          valorTransporteFinal: item.valorTransporteFinal
        })),
        totalTransporte: totalTransporte.value,
        totalGeral: totalGeral.value
      })
    }

    // ===== WATCHERS =====
    /**
     * Recalcula valores sempre que qualquer campo relevante mudar
     */
    watch(itens, (novos) => {
      novos.forEach(item => {
        let valor = 0
        let formula = ''
        
        // Calcula valor baseado no tipo de cálculo
        if (item.tipoCalculo === 'manual') {
          valor = Number(item.valorManual) || 0
          item.valorFormula = valor // Para exibir igual ao manual
        } else if (item.tipoCalculo === 'outro_derpr' && item.siglaOutroDerpr) {
          const outro = outrosDerprFormulas.value[item.siglaOutroDerpr]
          if (outro) {
            formula = outro.formula
            valor = calcularFormula(formula, item.x1, item.x2)
            item.valorFormula = valor
          } else {
            item.valorFormula = 0
            valor = 0
          }
        } else {
          // Local usa formula2, Comercial usa formula1
          formula = item.tipoCalculo === 'local' ? item.formula2 : item.formula1
          if (formula) {
            valor = calcularFormula(formula, item.x1, item.x2)
            item.valorFormula = valor
          } else {
            item.valorFormula = 0
            valor = 0
          }
        }
        
        // Multiplica pelo consumo para obter valor final
        item.valorTransporteFinal = valor * (Number(item.consumo) || 0)
      })
    }, { deep: true })

    /**
     * Recarrega itens quando mudam os props (composição, data base, desoneração)
     */
    watch([
      () => props.codigo,
      () => props.dataBase,
      () => props.desoneracao
    ], ([novoCodigo, novaDataBase, novaDesoneracao], [antigoCodigo, antigaDataBase, antigaDesoneracao]) => {
      if (
        novoCodigo !== antigoCodigo ||
        novaDataBase !== antigaDataBase ||
        novaDesoneracao !== antigaDesoneracao
      ) {
        carregarItens()
      }
    })

    // ===== LIFECYCLE =====
    onMounted(carregarItens)

    // ===== RETURN =====
    return {
      carregando,
      itens,
      outrosDerprFormulas,
      outrosDerprSiglas,
      uniqueId,
      totalTransporte,
      totalGeral,
      formatarMoeda,
      mostrarFormula,
      podeConfirmar,
      confirmar
    }
  }
}
</script>
