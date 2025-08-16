<template>
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>{{ composicao && composicao.id ? 'Editar Composição' : 'Nova Composição' }}</span>
      <button class="btn btn-sm btn-secondary" @click="$emit('fechar')">Fechar</button>
    </div>
    <div class="card-body">
      <form @submit.prevent="salvar" class="needs-validation" novalidate>
        <div class="row g-3">
          <div class="col-md-2">
            <div class="form-floating">
              <input type="text" 
                     class="form-control" 
                     :class="{ 'is-invalid': errors.codigo }"
                     id="codigo" 
                     v-model="form.codigo" 
                     maxlength="20"
                     required>
              <label for="codigo">Código</label>
              <div class="invalid-feedback">{{ errors.codigo }}</div>
            </div>
          </div>
          <div class="col-md-7">
            <div class="form-floating">
              <input type="text" 
                     class="form-control" 
                     :class="{ 'is-invalid': errors.descricao }"
                     id="descricao" 
                     v-model="form.descricao" 
                     maxlength="255" 
                     required>
              <label for="descricao">Descrição da Composição</label>
              <div class="invalid-feedback">{{ errors.descricao }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <input type="text" 
                     class="form-control" 
                     :class="{ 'is-invalid': errors.unidade }"
                     id="unidade" 
                     v-model="form.unidade" 
                     maxlength="10" 
                     required>
              <label for="unidade">Unidade</label>
              <div class="invalid-feedback">{{ errors.unidade }}</div>
            </div>
          </div>
        </div>
        <div class="row g-3 mt-1">
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" 
                     class="form-control text-end" 
                     :value="formatCurrency(form.valor_total_mat_equip)" 
                     readonly>
              <label>Mat/Equip</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" 
                     class="form-control text-end" 
                     :value="formatCurrency(form.valor_total_mao_obra)" 
                     readonly>
              <label>Mão de Obra</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" 
                     class="form-control text-end fw-bold" 
                     :value="formatCurrency(form.valor_total_geral)" 
                     readonly>
              <label>Total</label>
            </div>
          </div>
        </div>

        <div class="table-responsive mt-4">
          <table class="table table-bordered align-middle fonte-menor">
            <tbody>
              <tr v-for="(item, idx) in form.itens" :key="idx" class="align-top border-bottom">
                <td colspan="12" class="p-2">
                  <div class="fw-bold text-custom mb-2">ITEM {{ idx + 1 }}</div>
                  <div class="d-flex flex-row gap-2 align-items-start mb-2">
                    <!-- Referência -->
                    <div style="min-width: 110px;">
                      <label class="form-label form-label-sm mb-1">REFERÊNCIA</label>
                      <select v-model="item.referencia" class="form-select form-select-sm" :class="{ 'is-invalid': item.erro_referencia }">
                        <option value="SINAPI">SINAPI</option>
                        <option value="DERPR">DERPR</option>
                        <option value="PERSONALIZADA">PERSONALIZADA</option>
                      </select>
                      <div v-if="item.erro_referencia" class="invalid-feedback d-block">{{ item.erro_referencia }}</div>
                    </div>
                    <!-- Código + Zoom -->
                    <div style="min-width: 110px;">
                      <label class="form-label form-label-sm mb-1">CÓDIGO</label>
                      <div class="input-group input-group-sm">
                        <input type="text"
                               class="form-control form-control-sm"
                               v-model="item.codigo_item"
                               :readonly="item.referencia !== 'PERSONALIZADA'"
                               :class="[item.referencia !== 'PERSONALIZADA' ? 'campo-oficial' : '', item.erro_codigo_item ? 'is-invalid' : '']"
                               style="max-width: 80px;" />
                        <button class="btn btn-outline-secondary btn-sm" type="button" @click="abrirZoom(idx)" v-if="item.referencia !== 'PERSONALIZADA'">
                          <i class="fas fa-ellipsis-h"></i>
                        </button>
                      </div>
                      <div v-if="item.erro_codigo_item" class="invalid-feedback d-block">{{ item.erro_codigo_item }}</div>
                    </div>
                    <!-- Descrição -->
                    <div class="flex-grow-1">
                      <label class="form-label form-label-sm mb-1">DESCRIÇÃO</label>
                      <textarea class="form-control form-control-sm"
                        v-model="item.descricao" rows="2"
                        :readonly="item.referencia !== 'PERSONALIZADA'"
                        :class="[item.referencia !== 'PERSONALIZADA' ? 'campo-oficial' : '', item.erro_descricao ? 'is-invalid' : '']"
                        style="min-width: 220px; resize: vertical;"></textarea>
                      <div v-if="item.erro_descricao" class="invalid-feedback d-block">{{ item.erro_descricao }}</div>
                    </div>
                    <!-- Remover -->
                    <div class="d-flex align-items-start pt-4 ps-2">
                      <button type="button" class="btn btn-outline-danger btn-sm" @click="removerItem(idx)" title="Excluir item">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </div>
                  <div class="d-flex flex-row gap-2 align-items-end">
                    <!-- Unidade -->
                    <div style="min-width: 70px;">
                      <label class="form-label form-label-sm mb-1">UNIDADE</label>
                      <input type="text" class="form-control form-control-sm"
                        v-model="item.unidade"
                        maxlength="5"
                        :readonly="item.referencia !== 'PERSONALIZADA'"
                        :class="[item.referencia !== 'PERSONALIZADA' ? 'campo-oficial' : '', item.erro_unidade ? 'is-invalid' : '']" />
                      <div v-if="item.erro_unidade" class="invalid-feedback d-block">{{ item.erro_unidade }}</div>
                    </div>
                    <!-- mat/equip -->
                    <div style="min-width: 110px;">
                      <label class="form-label form-label-sm mb-1">mat/equip</label>
                      <input type="number" class="form-control form-control-sm text-end"
                        v-model.number="item.valor_mat_equip"
                        :readonly="item.referencia !== 'PERSONALIZADA'"
                        :class="item.referencia !== 'PERSONALIZADA' ? 'campo-oficial' : ''" />
                    </div>
                    <!-- mão de obra -->
                    <div style="min-width: 110px;">
                      <label class="form-label form-label-sm mb-1">mão de obra</label>
                      <input type="number" class="form-control form-control-sm text-end"
                        v-model.number="item.valor_mao_obra"
                        :readonly="item.referencia !== 'PERSONALIZADA'"
                        :class="item.referencia !== 'PERSONALIZADA' ? 'campo-oficial' : ''" />
                    </div>
                    <!-- total -->
                    <div style="min-width: 110px;">
                      <label class="form-label form-label-sm mb-1">total</label>
                      <input type="text" class="form-control form-control-sm text-end campo-oficial"
                        :value="formatCurrency(item.valor_total, false)"
                        readonly />
                    </div>
                    <!-- Coeficiente -->
                    <div style="min-width: 90px;">
                      <label class="form-label form-label-sm mb-1">COEF.</label>
                      <input type="number" class="form-control form-control-sm text-end"
                        v-model.number="item.coeficiente"
                        step="0.00001"
                        min="0"
                        @input="atualizarTotais()"
                        :class="{ 'is-invalid': item.erro_coeficiente }" />
                      <div v-if="item.erro_coeficiente" class="invalid-feedback d-block">{{ item.erro_coeficiente }}</div>
                    </div>
                    <!-- mat/equip final -->
                    <div style="min-width: 110px;">
                      <label class="form-label form-label-sm mb-1">mat/equip final</label>
                      <input type="text" class="form-control form-control-sm text-end campo-final"
                        :value="formatCurrency(item.valor_mat_equip_ajustado, false)"
                        readonly />
                    </div>
                    <!-- mão de obra final -->
                    <div style="min-width: 110px;">
                      <label class="form-label form-label-sm mb-1">mão de obra final</label>
                      <input type="text" class="form-control form-control-sm text-end campo-final"
                        :value="formatCurrency(item.valor_mao_obra_ajustado, false)"
                        readonly />
                    </div>
                    <!-- total final -->
                    <div style="min-width: 110px;">
                      <label class="form-label form-label-sm mb-1">total final</label>
                      <input type="text" class="form-control form-control-sm text-end campo-final fw-bold"
                        :value="formatCurrency(item.valor_total_ajustado, false)"
                        readonly />
                    </div>
                  </div>
                  <ZoomServico
                    v-if="showZoomIdx === idx"
                    :tipo="item.referencia.toLowerCase()"
                    :value="item.zoomServico"
                    @select="servico => preencherItemComServico(idx, servico)"
                    placeholder="Buscar serviço..."
                    :titulo="item.referencia + ' - SELECIONE O SERVIÇO'"
                    :input-group="false"
                    :small="true"
                    :abrir-imediato="true"
                  />
                </td>
              </tr>
            </tbody>
          </table>
          <button type="button" 
                  class="btn btn-outline-primary" 
                  @click="adicionarItem">
            <i class="fas fa-plus me-2"></i>Adicionar Item
          </button>
        </div>

        <div class="text-end mt-4">
          <button type="button" 
                  class="btn btn-light me-2" 
                  @click="$emit('fechar')">
            Cancelar
          </button>
          <button type="submit" 
                  class="btn btn-primary" 
                  :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
            <i v-else class="fas fa-save me-2"></i>
            {{ loading ? 'Salvando...' : 'Salvar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import ZoomServico from './ZoomServico.vue';

function brToNumber(valor) {
  if (typeof valor === 'number') return valor;
  if (!valor) return 0;
  // Remove pontos de milhar apenas se houver vírgula depois
  if (/\.[0-9]{3},/.test(valor)) {
    valor = valor.replace(/\./g, '');
  }
  valor = valor.replace(',', '.');
  return parseFloat(valor) || 0;
}

export default {
  props: {
    composicao: { type: Object, default: null }
  },
  data() {
    return {
      form: this.composicao ? { ...this.composicao, itens: [...(this.composicao.itens || [])] } : this.novoFormulario(),
      loading: false,
      errors: {},
      showZoomIdx: null,
    };
  },
  watch: {
    composicao: {
      handler(newVal) {
        if (newVal) {
          this.form = { ...newVal, itens: [...(newVal.itens || [])] };
        } else {
          this.form = this.novoFormulario();
        }
      },
      immediate: true,
      deep: true
    },
    'form.itens': {
      handler() {
        this.atualizarTotais();
      },
      deep: true
    }
  },
  methods: {
    novoFormulario() {
      return {
        codigo: '',
        descricao: '',
        unidade: '',
        valor_total_mat_equip: 0,
        valor_total_mao_obra: 0,
        valor_total_geral: 0,
        itens: [],
      };
    },
    adicionarItem() {
      this.form.itens.push({
        referencia: 'SINAPI',
        codigo_item: '',
        descricao: '',
        unidade: '',
        quantidade: 1,
        coeficiente: 0,
        valor_unitario: 0,
        valor_mat_equip: 0,
        valor_mao_obra: 0,
        valor_total: 0,
        valor_mat_equip_ajustado: 0,
        valor_mao_obra_ajustado: 0,
        valor_total_ajustado: 0,
      });
    },
    removerItem(idx) {
      this.form.itens.splice(idx, 1);
      this.atualizarTotais();
    },
    atualizarTotais() {
      let totalMatEquip = 0;
      let totalMaoObra = 0;
      let totalGeral = 0;
      this.form.itens.forEach(item => {
        // Usa a função utilitária para garantir conversão correta
        const mat = brToNumber(item.valor_mat_equip);
        const mao = brToNumber(item.valor_mao_obra);
        const coef = brToNumber(item.coeficiente);
        item.valor_mat_equip_ajustado = mat * coef;
        item.valor_mao_obra_ajustado = mao * coef;
        item.valor_total_ajustado = (mat + mao) * coef;
        totalMatEquip += item.valor_mat_equip_ajustado || 0;
        totalMaoObra += item.valor_mao_obra_ajustado || 0;
        totalGeral += item.valor_total_ajustado || 0;
      });
      this.form.valor_total_mat_equip = totalMatEquip;
      this.form.valor_total_mao_obra = totalMaoObra;
      this.form.valor_total_geral = totalGeral;
    },
    formatCurrency(value, withSymbol = true) {
      // Usa a função utilitária para garantir conversão correta
      value = brToNumber(value);
      return value.toLocaleString('pt-BR', { 
        style: 'currency', 
        currency: 'BRL',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).replace(withSymbol ? '' : /^R\$\s?/, '');
    },
    salvar() {
      this.errors = {};
      let hasError = false;
      // Validação dos campos principais
      if (!this.form.codigo || !this.form.codigo.trim()) {
        this.errors.codigo = 'O código é obrigatório.';
        hasError = true;
      }
      if (!this.form.descricao || !this.form.descricao.trim()) {
        this.errors.descricao = 'A descrição é obrigatória.';
        hasError = true;
      }
      if (!this.form.unidade || !this.form.unidade.trim()) {
        this.errors.unidade = 'A unidade é obrigatória.';
        hasError = true;
      }
      // Validação dos itens
      if (!this.form.itens || !this.form.itens.length) {
        this.errors.itens = 'Adicione pelo menos um item.';
        hasError = true;
      } else {
        this.form.itens.forEach((item, idx) => {
          if (!item.referencia) {
            item.erro_referencia = 'Referência obrigatória';
            hasError = true;
          } else {
            delete item.erro_referencia;
          }
          if (!item.codigo_item || !item.codigo_item.toString().trim()) {
            item.erro_codigo_item = 'Código obrigatório';
            hasError = true;
          } else {
            delete item.erro_codigo_item;
          }
          if (!item.descricao || !item.descricao.trim()) {
            item.erro_descricao = 'Descrição obrigatória';
            hasError = true;
          } else {
            delete item.erro_descricao;
          }
          if (!item.unidade || !item.unidade.trim()) {
            item.erro_unidade = 'Unidade obrigatória';
            hasError = true;
          } else {
            delete item.erro_unidade;
          }
          if (item.coeficiente === null || item.coeficiente === undefined || isNaN(item.coeficiente) || Number(item.coeficiente) <= 0) {
            item.erro_coeficiente = 'Coeficiente obrigatório e maior que zero';
            hasError = true;
          } else {
            delete item.erro_coeficiente;
          }
        });
      }
      if (hasError) return;
      this.$emit('salvar', this.form);
    },
    abrirZoom(idx) {
      this.showZoomIdx = idx;
    },
    preencherItemComServico(idx, servico) {
      const item = this.form.itens[idx];
      item.codigo_item = servico.codigo;
      item.descricao = servico.descricao;
      item.unidade = servico.unidade;
      // Garante que os valores vindos do serviço sejam convertidos corretamente
      item.valor_mat_equip = brToNumber(servico.valor_mat_equip);
      item.valor_mao_obra = brToNumber(servico.valor_mao_obra);
      item.valor_total = brToNumber(servico.valor_total);
      item.zoomServico = servico;
      this.showZoomIdx = null;
    }
  },
  mounted() {
    this.atualizarTotais();
  },
  directives: {
    moeda: {
      beforeMount(el) {
        el.addEventListener('input', function(e) {
          let v = e.target.value.replace(/\D/g, '');
          v = (parseInt(v, 10) / 100).toFixed(2) + '';
          v = v.replace('.', ',');
          v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
          e.target.value = v;
        });
      }
    }
  },
  components: { ZoomServico }
};
</script>

<style scoped>
.text-custom {
  color: #18578A;
}
.card {
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.input-moeda {
  font-size: 0.95em;
  padding-right: 0.5em;
}
.fonte-menor {
  font-size: 0.85em;
}
.campo-oficial {
  background: #f5f5f5 !important;
  color: #888 !important;
  cursor: not-allowed !important;
}
.campo-final {
  background: #e6f7ea !important;
  color: #18578A !important;
  font-weight: 500;
  border: 1.5px solid #bcd;
}
</style> 