<template>
  <span v-if="!inputGroup">
    <button v-if="!abrirImediato" class="btn btn-outline-secondary" :class="{'btn-sm': small}" type="button" @click="abrirModal">
      <i class="fas fa-ellipsis-h"></i>
    </button>
    <div class="modal fade zoom-modal-wide" :class="{ show: showModal }" tabindex="-1" style="display: block;" v-if="showModal">
      <div class="modal-dialog modal-xxl modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ titulo }}</h5>
            <button type="button" class="btn-close" @click="fecharModal"></button>
          </div>
          <div class="modal-body zoom-list-modal-body">
            <div class="mb-3">
              <div class="input-group">
                <input type="text" class="form-control" v-model="filtro" @input="debouncedBuscar" placeholder="Buscar por código ou descrição..." autofocus />
                <select class="form-select" v-model="desoneracao" @change="onDesoneracaoChange" style="max-width:160px">
                  <option value="com">Com desoneração</option>
                  <option value="sem">Sem desoneração</option>
                </select>
                <button class="btn btn-outline-primary" type="button" @click="buscar"><i class="fas fa-search"></i></button>
              </div>
            </div>
            <div v-if="servicos.data.length === 0" class="text-center text-muted py-4">Nenhum serviço encontrado.</div>
            <div v-else>
              <div v-for="servico in servicos.data" :key="servico.codigo" class="zoom-list-item">
                <div class="zoom-list-top">
                  <span class="zoom-list-codigo">{{ servico.codigo }}</span>
                  <span class="zoom-list-unidade">{{ servico.unidade }}</span>
                  <span class="zoom-list-valores">Mão de obra: <b>{{ formatCurrency(servico.valor_mao_obra) }}</b></span>
                  <span class="zoom-list-valores">| Mat/Equip: <b>{{ formatCurrency(servico.valor_mat_equip) }}</b></span>
                  <span class="zoom-list-valores">| <span class="zoom-list-total-label">Total:</span> <span class="zoom-list-total">{{ formatCurrency(servico.valor_total) }}</span></span>
                  <span v-if="servico.data_base" class="zoom-list-databases ms-3">{{ formatDataBase(servico.data_base) }}</span>
                  <span v-if="servico.desoneracao" class="zoom-list-desoneracao ms-2">{{ formatDesoneracao(servico.desoneracao) }}</span>
                  <button class="btn btn-outline-primary btn-sm zoom-list-btn" @click="selecionar(servico)"><i class="fas fa-check"></i> Selecionar</button>
                </div>
                <div class="zoom-list-desc">{{ servico.descricao }}</div>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <div class="text-muted small">Página {{ servicos.current_page }} de {{ servicos.last_page }}</div>
              <nav aria-label="Navegação de páginas">
                <ul class="pagination pagination-sm mb-0">
                  <li class="page-item" :class="{ disabled: servicos.current_page === 1 }">
                    <button type="button" class="page-link" @click="mudarPagina(1)" :disabled="servicos.current_page === 1">Primeira</button>
                  </li>
                  <li class="page-item" :class="{ disabled: servicos.current_page === 1 }">
                    <button type="button" class="page-link" @click="mudarPagina(servicos.current_page - 1)" :disabled="servicos.current_page === 1">&laquo;</button>
                  </li>
                  <li v-for="page in paginasPaginacao" :key="page" class="page-item" :class="{ active: servicos.current_page === page }">
                    <button type="button" class="page-link" @click="mudarPagina(page)">{{ page }}</button>
                  </li>
                  <li class="page-item" :class="{ disabled: servicos.current_page === servicos.last_page }">
                    <button type="button" class="page-link" @click="mudarPagina(servicos.current_page + 1)" :disabled="servicos.current_page === servicos.last_page">&raquo;</button>
                  </li>
                  <li class="page-item" :class="{ disabled: servicos.current_page === servicos.last_page }">
                    <button type="button" class="page-link" @click="mudarPagina(servicos.last_page)" :disabled="servicos.current_page === servicos.last_page">Última</button>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-backdrop fade show"></div>
    </div>
  </span>
  <div v-else class="input-group">
    <input type="text" class="form-control" :class="{'form-control-sm': small}" :value="displayValue" readonly style="background:#f5f5f5; color:#888; cursor:not-allowed;" :placeholder="placeholder" @focus="abrirModal" @click="abrirModal" />
    <button class="btn btn-outline-secondary" :class="{'btn-sm': small}" type="button" @click="abrirModal">
      <i class="fas fa-ellipsis-h"></i>
    </button>
    <div class="modal fade zoom-modal-wide" :class="{ show: showModal }" tabindex="-1" style="display: block;" v-if="showModal">
      <div class="modal-dialog modal-xxl modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ titulo }}</h5>
            <button type="button" class="btn-close" @click="fecharModal"></button>
          </div>
          <div class="modal-body zoom-list-modal-body">
            <div class="mb-3">
              <div class="input-group">
                <input type="text" class="form-control" v-model="filtro" @input="debouncedBuscar" placeholder="Buscar por código ou descrição..." autofocus />
                <select class="form-select" v-model="desoneracao" @change="onDesoneracaoChange" style="max-width:160px">
                  <option value="com">Com desoneração</option>
                  <option value="sem">Sem desoneração</option>
                </select>
                <button class="btn btn-outline-primary" type="button" @click="buscar"><i class="fas fa-search"></i></button>
              </div>
            </div>
            <div v-if="servicos.data.length === 0" class="text-center text-muted py-4">Nenhum serviço encontrado.</div>
            <div v-else>
              <div v-for="servico in servicos.data" :key="servico.codigo" class="zoom-list-item">
                <div class="zoom-list-top">
                  <span class="zoom-list-codigo">{{ servico.codigo }}</span>
                  <span class="zoom-list-unidade">{{ servico.unidade }}</span>
                  <span class="zoom-list-valores">Mão de obra: <b>{{ formatCurrency(servico.valor_mao_obra) }}</b></span>
                  <span class="zoom-list-valores">| Mat/Equip: <b>{{ formatCurrency(servico.valor_mat_equip) }}</b></span>
                  <span class="zoom-list-valores">| <span class="zoom-list-total-label">Total:</span> <span class="zoom-list-total">{{ formatCurrency(servico.valor_total) }}</span></span>
                  <span v-if="servico.data_base" class="zoom-list-databases ms-3">{{ formatDataBase(servico.data_base) }}</span>
                  <span v-if="servico.desoneracao" class="zoom-list-desoneracao ms-2">{{ formatDesoneracao(servico.desoneracao) }}</span>
                  <button class="btn btn-outline-primary btn-sm zoom-list-btn" @click="selecionar(servico)"><i class="fas fa-check"></i> Selecionar</button>
                </div>
                <div class="zoom-list-desc">{{ servico.descricao }}</div>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <div class="text-muted small">Página {{ servicos.current_page }} de {{ servicos.last_page }}</div>
              <nav aria-label="Navegação de páginas">
                <ul class="pagination pagination-sm mb-0">
                  <li class="page-item" :class="{ disabled: servicos.current_page === 1 }">
                    <button type="button" class="page-link" @click="mudarPagina(1)" :disabled="servicos.current_page === 1">Primeira</button>
                  </li>
                  <li class="page-item" :class="{ disabled: servicos.current_page === 1 }">
                    <button type="button" class="page-link" @click="mudarPagina(servicos.current_page - 1)" :disabled="servicos.current_page === 1">&laquo;</button>
                  </li>
                  <li v-for="page in paginasPaginacao" :key="page" class="page-item" :class="{ active: servicos.current_page === page }">
                    <button type="button" class="page-link" @click="mudarPagina(page)">{{ page }}</button>
                  </li>
                  <li class="page-item" :class="{ disabled: servicos.current_page === servicos.last_page }">
                    <button type="button" class="page-link" @click="mudarPagina(servicos.current_page + 1)" :disabled="servicos.current_page === servicos.last_page">&raquo;</button>
                  </li>
                  <li class="page-item" :class="{ disabled: servicos.current_page === servicos.last_page }">
                    <button type="button" class="page-link" @click="mudarPagina(servicos.last_page)" :disabled="servicos.current_page === servicos.last_page">Última</button>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-backdrop fade show"></div>
    </div>
  </div>
</template>

<script>
import debounce from 'lodash.debounce';
export default {
  props: {
    tipo: { type: String, required: true },
    value: { type: Object, default: null },
    placeholder: { type: String, default: '' },
    titulo: { type: String, default: 'Selecione o Serviço' },
    desoneracaoPadrao: { type: String, default: 'com' },
    inputGroup: { type: Boolean, default: true },
    small: { type: Boolean, default: false },
    abrirImediato: { type: Boolean, default: false }
  },
  data() {
    return {
      showModal: false,
      filtro: '',
      desoneracao: this.desoneracaoPadrao,
      servicos: { data: [], current_page: 1, last_page: 1 },
      loading: false
    };
  },
  computed: {
    displayValue() {
      if (!this.value) return '';
      return `${this.value.codigo} - ${this.value.descricao}`;
    },
    paginasPaginacao() {
      const total = this.servicos.last_page || 1;
      const atual = this.servicos.current_page || 1;
      const delta = 2;
      let pages = [];
      let start = Math.max(1, atual - delta);
      let end = Math.min(total, atual + delta);
      if (start > 1) {
        pages.push(1);
        if (start > 2) pages.push('...');
      }
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      if (end < total) {
        if (end < total - 1) pages.push('...');
        pages.push(total);
      }
      // Remove duplicados e '...' consecutivos
      return pages.filter((v, i, arr) => v !== '...' || arr[i - 1] !== '...');
    }
  },
  methods: {
    abrirModal() {
      this.showModal = true;
      this.filtro = '';
      this.buscar();
    },
    fecharModal() {
      this.showModal = false;
    },
    async buscar(page = 1) {
      //console.log('Buscando serviços:', { termo: this.filtro, desoneracao: this.desoneracao, page });
      this.loading = true;
      let url = this.tipo === 'sinapi' ? '/api/sinapi/servicos/zoom' : '/api/derpr/servicos/zoom';
      try {
        const response = await axios.get(url, {
          params: {
            termo: this.filtro,
            desoneracao: this.desoneracao,
            page
          }
        });
        this.servicos = response.data;
      } catch (e) {
        this.servicos = { data: [], current_page: 1, last_page: 1 };
      } finally {
        this.loading = false;
      }
    },
    mudarPagina(page) {
      if (typeof page !== 'number' || isNaN(page) || page < 1 || page === '...') return;
      this.buscar(page);
    },
    selecionar(servico) {
      this.$emit('input', servico);
      this.$emit('select', servico);
      this.fecharModal();
    },
    formatCurrency(value) {
      if (typeof value !== 'number') value = parseFloat(value) || 0;
      return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    },
    debouncedBuscar: debounce(function() { this.buscar(); }, 400),
    onDesoneracaoChange() {
      this.buscar();
    },
    formatDataBase(dataBase) {
      if (!dataBase) return '';
      // Espera formato 'YYYY-MM-DD' ou 'YYYY-MM' ou 'YYYY/MM'
      const meses = [
        '', 'JANEIRO', 'FEVEREIRO', 'MARÇO', 'ABRIL', 'MAIO', 'JUNHO',
        'JULHO', 'AGOSTO', 'SETEMBRO', 'OUTUBRO', 'NOVEMBRO', 'DEZEMBRO'
      ];
      let ano, mes;
      if (/^\d{4}-\d{2}/.test(dataBase)) {
        [ano, mes] = dataBase.split('-');
      } else if (/^\d{2}\/\d{4}/.test(dataBase)) {
        [mes, ano] = dataBase.split('/');
      } else {
        return dataBase;
      }
      mes = parseInt(mes, 10);
      return meses[mes] + ' / ' + ano;
    },
    formatDesoneracao(desoneracao) {
      if (!desoneracao) return '';
      return desoneracao.toLowerCase() === 'com' ? 'Com Desoneração' : 'Sem Desoneração';
    }
  },
  mounted() {
    if (this.abrirImediato) {
      this.showModal = true;
      this.filtro = '';
      this.buscar();
    }
  }
};
</script>

<style scoped>
.modal { display: block; }
.modal-backdrop { z-index: 1040; }
.modal-dialog { z-index: 1050; }

.zoom-modal-wide .modal-dialog {
  min-width: 900px;
  max-width: 1200px;
  width: 55vw;
}

.zoom-list-modal-body {
  background: #fff;
  padding: 0.5rem 0.5rem 0.5rem 0.5rem;
}
.zoom-list-item {
  border-radius: 6px;
  border: 1px solid #bbb;
  margin-bottom: 14px;
  padding: 8px 14px 6px 14px;
  background: #fff;
  transition: box-shadow 0.15s, border 0.15s;
  box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.zoom-list-item:hover {
  border: 1px solid #1a6;
  box-shadow: 0 2px 8px rgba(0,0,0,0.07);
  background: #f7faff;
}
.zoom-list-top {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  font-size: 1em;
  margin-bottom: 2px;
}
.zoom-list-codigo {
  font-weight: 700;
  font-size: 1.08em;
  color: #222;
  margin-right: 4px;
}
.zoom-list-unidade {
  font-size: 0.98em;
  color: #888;
  background: #f2f2f2;
  border-radius: 4px;
  padding: 1px 7px;
  margin-right: 10px;
}
.zoom-list-valores {
  font-size: 0.97em;
  color: #444;
  margin-right: 8px;
}
.zoom-list-total-label {
  color: #222;
  font-weight: 500;
}
.zoom-list-total {
  font-weight: 700;
  color: #1a6;
  font-size: 1.08em;
  margin-left: 2px;
}
.zoom-list-btn {
  margin-left: auto;
  min-width: 110px;
}
.zoom-list-desc {
  font-size: 0.98em;
  color: #444;
  margin: 2px 0 0 0;
  line-height: 1.3;
  word-break: break-word;
}
.zoom-list-databases {
  font-weight: 500;
  color: #444;
  background: #f2f2f2;
  border-radius: 4px;
  padding: 2px 10px;
  margin-left: 10px;
}
.zoom-list-desoneracao {
  font-weight: 500;
  color: #18578A;
  background: #eaf6ff;
  border-radius: 4px;
  padding: 2px 10px;
  margin-left: 6px;
}
</style> 