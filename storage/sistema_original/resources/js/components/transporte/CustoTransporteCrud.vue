<template>
  <div class="container-fluid px-4">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
      <!-- Cabeçalho -->
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold" style="color: #5EA853;">
          <i class="fas fa-truck me-2"></i>Custos de Transporte
        </h5>
        <div class="d-flex gap-2">
          <button class="btn btn-primary" @click="abrirModalCusto()">
            <i class="fas fa-plus me-2"></i>Novo Custo
          </button>
          <button class="btn btn-success" @click="abrirInputImportar">
            <i class="fas fa-file-import me-2"></i>Importar coeficientes
          </button>
          <input ref="inputImportar" type="file" accept=".xls,.xlsx" style="display:none" @change="importarCoeficientes" />
        </div>
      </div>
      <!-- Corpo -->
      <div class="card-body">
        <!-- Seletor de Data Base -->
        <div class="d-flex align-items-center mb-3">
          <label class="me-2">Data Base:</label>
          <select v-model="dataBaseSelecionada" @change="onDataBaseChange" class="form-control w-auto">
            <option value="" disabled>Selecione a Data Base...</option>
            <option v-for="db in databases" :key="db.value" :value="db.value">
              {{ db.label }}
            </option>
          </select>
          <!-- <button class="btn btn-success ms-3" @click="exportarExcel">Exportar Excel</button> -->
        </div>
        <!-- Mensagem de orientação -->
        <div v-if="!dataBaseSelecionada" class="alert alert-info mt-3">
          Selecione uma <b>Data Base</b> para visualizar os coeficientes de transporte.
        </div>
        <!-- Tabela -->
        <div class="table-responsive" v-if="dataBaseSelecionada">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th class="fw-semibold text-custom text-center" style="width: 80px;">Sigla</th>
                <th class="fw-semibold text-custom text-center" style="width: 100px;">Código</th>
                <th class="fw-semibold text-custom" style="min-width: 260px;">Descrição do Serviço</th>
                <th class="fw-semibold text-custom text-center" style="width: 90px;">Unidade</th>
                <th class="fw-semibold text-custom text-center" style="width: 90px;">X1</th>
                <th class="fw-semibold text-custom text-center" style="width: 90px;">X2</th>
                <th class="fw-semibold text-custom text-center" style="width: 90px;">K</th>
                <th class="fw-semibold text-custom" style="min-width: 180px;">Fórmula de transporte</th>
                <th class="fw-semibold text-end text-custom" style="width: 90px;">Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="custo in custosTransporte" :key="custo.id">
                <td class="text-center">{{ custo.sigla }}</td>
                <td class="text-center">{{ custo.codigo }}</td>
                <td>{{ custo.descricao }}</td>
                <td class="text-center">{{ custo.unidade }}</td>
                <td class="text-center">
                  <input v-model="getCoeficiente(custo.id).x1" class="form-control form-control-sm text-center" maxlength="5" style="width: 80px; margin: 0 auto;" type="text"
                    @input="onCoefInput(getCoeficiente(custo.id), 'x1')" />
                </td>
                <td class="text-center">
                  <input v-model="getCoeficiente(custo.id).x2" class="form-control form-control-sm text-center" maxlength="5" style="width: 80px; margin: 0 auto;" type="text"
                    @input="onCoefInput(getCoeficiente(custo.id), 'x2')" />
                </td>
                <td class="text-center">
                  <input v-model="getCoeficiente(custo.id).k" class="form-control form-control-sm text-center" maxlength="5" style="width: 80px; margin: 0 auto;" type="text"
                    @input="onCoefInput(getCoeficiente(custo.id), 'k')" />
                </td>
                <td v-html="montarFormulaTransporteHTML(getCoeficiente(custo.id))"></td>
                <td class="text-end" style="width: 90px;">
                  <div class="d-flex justify-content-end align-items-center gap-1">
                    <button class="btn btn-outline-secondary btn-sm me-1" @click="abrirModalCusto(custo)" title="Editar">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm" @click="excluirCustoTransporte(custo)" title="Excluir">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="custosTransporte.length === 0">
                <td colspan="9" class="text-center">Nenhum custo cadastrado.</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
          <button class="btn btn-primary" @click="salvarTodosCoeficientes" :disabled="!dataBaseSelecionada">
            <i class="fas fa-save me-2"></i>Salvar coeficientes
          </button>
        </div>
      </div>
    </div>
    <!-- Modal de cadastro/edição de custo de transporte -->
    <div v-if="modalCustoAberto" class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-semibold">
              <i class="fas" :class="custoEditando.id ? 'fa-edit' : 'fa-plus'"></i>
              {{ custoEditando.id ? 'Editar' : 'Novo' }} Custo de Transporte
            </h5>
            <button type="button" class="btn-close" @click="fecharModalCusto"></button>
          </div>
          <div class="modal-body pt-3">
            <div class="mb-2">
              <label class="form-label">Sigla</label>
              <input v-model="custoEditando.sigla" class="form-control" maxlength="3" />
              <div v-if="errosCusto.sigla" class="invalid-feedback d-block">{{ errosCusto.sigla }}</div>
            </div>
            <div class="mb-2">
              <label class="form-label">Código</label>
              <input v-model="custoEditando.codigo" class="form-control" maxlength="10" />
              <div v-if="errosCusto.codigo" class="invalid-feedback d-block">{{ errosCusto.codigo }}</div>
            </div>
            <div class="mb-2">
              <label class="form-label">Descrição</label>
              <input v-model="custoEditando.descricao" class="form-control" maxlength="255" />
              <div v-if="errosCusto.descricao" class="invalid-feedback d-block">{{ errosCusto.descricao }}</div>
            </div>
            <div class="mb-2">
              <label class="form-label">Unidade</label>
              <input v-model="custoEditando.unidade" class="form-control" maxlength="5" />
              <div v-if="errosCusto.unidade" class="invalid-feedback d-block">{{ errosCusto.unidade }}</div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="fecharModalCusto">Cancelar</button>
            <button class="btn btn-primary" @click="salvarCustoTransporte">Salvar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Toast de Sucesso/Erro -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
      <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header" :class="toastType === 'success' ? 'bg-success text-white' : 'bg-danger text-white'">
          <i class="fas" :class="toastType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
          <strong class="ms-2 me-auto">{{ toastTitle }}</strong>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
          {{ toastMessage }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import axios from 'axios';

const custosTransporte = ref([]);
const coeficientes = ref([]);
const databases = ref([]);
const dataBaseSelecionada = ref('');
const modalCustoAberto = ref(false);
const custoEditando = reactive({});
const errosCusto = reactive({ sigla: '', codigo: '', descricao: '', unidade: '' });
const toastType = ref('success');
const toastTitle = ref('');
const toastMessage = ref('');
const inputImportar = ref(null);

function buscarDatabases() {
  axios.get('/transporte/databases').then(r => databases.value = r.data);
}

function onDataBaseChange() {
  buscarCustosTransporte();
  buscarCoeficientes();
}

function buscarCustosTransporte() {
  axios.get('/transporte/custos').then(r => custosTransporte.value = r.data);
}

function buscarCoeficientes() {
  if (!dataBaseSelecionada.value) return;
  const [data_base, desoneracao] = dataBaseSelecionada.value.split('|');
  axios.get('/transporte/coeficientes', { params: { data_base, desoneracao } })
    .then(r => {
      // Mapear nomes do backend para os nomes usados no frontend
      coeficientes.value = r.data.map(c => ({
        ...c,
        x1: formatDecimalBR(c.coeficiente_x1),
        x2: formatDecimalBR(c.coeficiente_x2),
        k: formatDecimalBR(c.termo_independente),
      }));
      custosTransporte.value.forEach(custo => {
        if (!coeficientes.value.find(c => c.custo_transporte_id === custo.id)) {
          coeficientes.value.push({
            custo_transporte_id: custo.id,
            x1: '', x2: '', k: '',
            data_base,
            desoneracao
          });
        }
      });
    });
}

function formatDecimalBR(val) {
  if (val === undefined || val === null || val === '') return '';
  // Se já tem vírgula, retorna como está
  if (typeof val === 'string' && val.includes(',')) return val;
  // Se for número, formata para 2 casas decimais e troca ponto por vírgula
  const num = Number(val);
  if (isNaN(num)) return '';
  return num.toFixed(2).replace('.', ',');
}

function getCoeficiente(id) {
  let coef = coeficientes.value.find(c => c.custo_transporte_id === id);
  if (!coef) {
    coef = { custo_transporte_id: id, x1: '', x2: '', k: '', data_base: dataBaseSelecionada.value };
    coeficientes.value.push(coef);
  } else {
    coef.data_base = dataBaseSelecionada.value;
  }
  return coef;
}

function onCoefInput(coef, field) {
  // Permite apenas números, vírgula e no máximo duas casas decimais
  let val = coef[field];
  val = val.replace(/[^0-9,]/g, '');
  // Só uma vírgula
  val = val.replace(/(,.*),/g, '$1');
  // Máximo duas casas decimais
  if (val.includes(',')) {
    const [int, dec] = val.split(',');
    coef[field] = int + ',' + (dec ? dec.slice(0, 2) : '');
  } else {
    coef[field] = val;
  }
}

function validarCoeficientes() {
  for (const coef of coeficientes.value) {
    for (const campo of ['x1', 'x2', 'k']) {
      if (coef[campo] && !/^\d{1,3}(,\d{0,2})?$/.test(coef[campo])) {
        return false;
      }
    }
  }
  return true;
}

function salvarTodosCoeficientes() {
  if (!dataBaseSelecionada.value) return;
  if (!validarCoeficientes()) {
    toastTitle.value = 'Erro';
    toastMessage.value = 'Preencha os coeficientes apenas com números e até 2 casas decimais (use vírgula).';
    toastType.value = 'danger';
    return;
  }
  const [data_base, desoneracao] = dataBaseSelecionada.value.split('|');
  // Converter vírgula para ponto e mapear para os nomes do backend
  const coeficientesParaSalvar = coeficientes.value.map(c => ({
    ...c,
    coeficiente_x1: c.x1 ? c.x1.replace(',', '.') : '',
    coeficiente_x2: c.x2 ? c.x2.replace(',', '.') : '',
    termo_independente: c.k ? c.k.replace(',', '.') : '',
    data_base,
    desoneracao
  }));
  axios.post('/transporte/coeficientes/lote', {
    data_base,
    desoneracao,
    coeficientes: coeficientesParaSalvar
  }).then(() => {
    buscarCoeficientes();
    toastTitle.value = 'Sucesso';
    toastMessage.value = 'Coeficientes salvos com sucesso!';
    toastType.value = 'success';
  });
}

function montarFormulaTransporteHTML(coef) {
  const partes = [];
  if (coef.x1 && parseFloat(coef.x1.replace(',', '.')) !== 0) partes.push(`<span class='coef'>${coef.x1}</span><span class='dot'>·</span><span class='var-x'>x₁</span>`);
  if (coef.x2 && parseFloat(coef.x2.replace(',', '.')) !== 0) partes.push(`<span class='coef'>${coef.x2}</span><span class='dot'>·</span><span class='var-x'>x₂</span>`);
  if (coef.k && parseFloat(coef.k.replace(',', '.')) !== 0) partes.push(`<span class='constante'>${coef.k}</span>`);
  return partes.length ? `<span class='formula-math'>${partes.join(' <span class="plus">+</span> ')}</span>` : '-';
}

function exportarExcel() {
  window.open(`/transporte/exportar?data_base=${dataBaseSelecionada.value}`);
}

function abrirModalCusto(custo = null) {
  modalCustoAberto.value = true;
  Object.assign(custoEditando, custo || { sigla: '', codigo: '', descricao: '', unidade: '' });
}

function fecharModalCusto() {
  modalCustoAberto.value = false;
  Object.assign(custoEditando, {});
}

function validarCusto() {
  let valido = true;
  errosCusto.sigla = '';
  errosCusto.codigo = '';
  errosCusto.descricao = '';
  errosCusto.unidade = '';

  if (!custoEditando.sigla || custoEditando.sigla.length > 3) {
    errosCusto.sigla = 'Sigla obrigatória (máx. 3 caracteres)';
    valido = false;
  }
  if (!custoEditando.codigo || custoEditando.codigo.length > 10) {
    errosCusto.codigo = 'Código obrigatório (máx. 10 caracteres)';
    valido = false;
  }
  if (!custoEditando.descricao || custoEditando.descricao.length > 255) {
    errosCusto.descricao = 'Descrição obrigatória (máx. 255 caracteres)';
    valido = false;
  }
  if (!custoEditando.unidade || custoEditando.unidade.length > 5) {
    errosCusto.unidade = 'Unidade obrigatória (máx. 5 caracteres)';
    valido = false;
  }
  return valido;
}

function salvarCustoTransporte() {
  if (!validarCusto()) return;
  axios.post('/transporte/custos', custoEditando)
    .then(() => {
      buscarCustosTransporte();
      fecharModalCusto();
      toastTitle.value = 'Sucesso';
      toastMessage.value = 'Custo de transporte salvo com sucesso!';
      toastType.value = 'success';
    })
    .catch(error => {
      if (error.response && error.response.status === 500 && error.response.data && error.response.data.message && error.response.data.message.includes('Duplicate entry')) {
        errosCusto.codigo = 'Já existe um custo de transporte com este código.';
      } else if (error.response && error.response.data && error.response.data.errors) {
        // Validação Laravel
        Object.assign(errosCusto, error.response.data.errors);
      } else {
        toastTitle.value = 'Erro';
        toastMessage.value = 'Erro ao salvar custo de transporte.';
        toastType.value = 'danger';
      }
    });
}

function excluirCustoTransporte(custo) {
  if (confirm('Tem certeza que deseja excluir este custo de transporte e todos os coeficientes relacionados?')) {
    axios.delete(`/transporte/custos/${custo.id}`).then(() => {
      buscarCustosTransporte();
      toastTitle.value = 'Sucesso';
      toastMessage.value = 'Custo de transporte excluído com sucesso!';
      toastType.value = 'success';
    });
  }
}

function abrirInputImportar() {
  inputImportar.value && inputImportar.value.click();
}

async function importarCoeficientes(event) {
  const file = event.target.files[0];
  if (!file) return;
  const formData = new FormData();
  formData.append('file', file);
  try {
    await axios.post('/transporte/importar-coeficientes', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    toastType.value = 'success';
    toastTitle.value = 'Importação';
    toastMessage.value = 'Coeficientes importados com sucesso!';
    buscarCustosTransporte();
    buscarCoeficientes();
  } catch (e) {
    toastType.value = 'danger';
    toastTitle.value = 'Erro';
    toastMessage.value = e.response?.data?.message || 'Erro ao importar coeficientes.';
  }
  // Exibir o toast manualmente
  setTimeout(() => {
    toastMessage.value = '';
  }, 5000);
  const toastEl = document.getElementById('toast');
  if (toastEl && window.bootstrap) {
    const toast = window.bootstrap.Toast.getOrCreateInstance(toastEl);
    toast.show();
  }
  // Reset input para permitir novo upload igual
  event.target.value = '';
}

onMounted(() => {
  buscarDatabases();
  // buscarCustosTransporte(); // Removido: só carrega após seleção da Data Base
});
</script>

<style scoped>
.modal { background: rgba(0,0,0,0.3); }
.formula-math {
  font-family: 'Georgia', 'Times New Roman', Times, serif;
  font-size: 1.1em;
  letter-spacing: 0.5px;
}
.coef {
  color: #18578A;
  font-weight: bold;
}
.var-x {
  color: #388e3c;
  font-style: italic;
}
.constante {
  color: #6c757d;
  font-weight: bold;
}
.dot {
  color: #888;
  font-weight: bold;
  margin: 0 2px;
}
.plus {
  color: #222;
  margin: 0 4px;
  font-weight: bold;
}
</style> 