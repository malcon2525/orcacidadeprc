<template>
  <div class="card shadow-sm border-0 rounded-3 mb-4">
    <!-- Cabeçalho -->
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-semibold" style="color: #5EA853;">
        <i class="fas fa-calculator me-2"></i>Composições Próprias
      </h5>
      <div class="d-flex gap-2">
        <button class="btn btn-primary" @click="abrirModalCriar">
          <i class="fas fa-plus me-2"></i>Nova Composição
        </button>
      </div>
    </div>

    <!-- Corpo -->
    <div class="card-body">
      <!-- Filtros -->
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="form-floating">
            <input type="text" 
                   class="form-control" 
                   id="filtroDescricao" 
                   v-model="filtros.descricao" 
                   @input="filtrarDados"
                   placeholder="Filtrar por descrição">
            <label for="filtroDescricao">Descrição</label>
          </div>
        </div>
      </div>

      <!-- Tabela -->
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th class="fw-semibold text-custom" style="width: 100px;">Código</th>
              <th class="fw-semibold text-custom">Descrição</th>
              <th class="fw-semibold text-custom" style="width: 100px;">Unidade</th>
              <th class="fw-semibold text-custom" style="width: 120px;">Mat/Equip</th>
              <th class="fw-semibold text-custom" style="width: 120px;">Mão de Obra</th>
              <th class="fw-semibold text-custom" style="width: 120px;">Total</th>
              <th class="fw-semibold text-end text-custom" style="width: 100px;">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="composicao in items.data" :key="composicao.id">
              <td class="text-nowrap">{{ composicao.codigo || composicao.id }}</td>
              <td>{{ composicao.descricao }}</td>
              <td class="text-nowrap">{{ composicao.unidade }}</td>
              <td class="text-nowrap text-end">{{ formatCurrencyBRL(composicao.valor_total_mat_equip) }}</td>
              <td class="text-nowrap text-end">{{ formatCurrencyBRL(composicao.valor_total_mao_obra) }}</td>
              <td class="text-nowrap text-end">{{ formatCurrencyBRL(composicao.valor_total_geral) }}</td>
              <td class="text-end">
                <button class="btn btn-outline-secondary btn-sm me-2" 
                        @click="editarItem(composicao)" 
                        title="Editar">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-outline-secondary btn-sm" 
                        @click="excluirItem(composicao)" 
                        title="Excluir">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
            <tr v-if="items.data.length === 0">
              <td colspan="7" class="text-center">Nenhuma composição cadastrada.</td>
            </tr>
          </tbody>
        </table>

        <!-- Paginação -->
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div class="text-muted">
            Mostrando {{ items.from }} até {{ items.to }} de {{ items.total }} registros
          </div>
          <nav v-if="items.last_page > 1">
            <ul class="pagination pagination-sm mb-0">
              <li class="page-item" :class="{ disabled: items.current_page === 1 }">
                <a class="page-link" href="#" @click.prevent="mudarPagina(items.current_page - 1)">
                  <i class="fas fa-chevron-left"></i>
                </a>
              </li>
              <li v-for="page in items.last_page" 
                  :key="page" 
                  class="page-item" 
                  :class="{ active: page === items.current_page }">
                <a class="page-link" href="#" @click.prevent="mudarPagina(page)">{{ page }}</a>
              </li>
              <li class="page-item" :class="{ disabled: items.current_page === items.last_page }">
                <a class="page-link" href="#" @click.prevent="mudarPagina(items.current_page + 1)">
                  <i class="fas fa-chevron-right"></i>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Criar/Editar -->
  <div class="modal fade" id="itemModal" tabindex="-1" ref="itemModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-semibold">
            <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
            {{ modoEdicao ? 'Editar' : 'Nova' }} Composição
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body pt-3">
          <composicao-propria-form 
            :composicao="form" 
            @fechar="fecharModal"
            @salvar="salvarItem" />
        </div>
      </div>
    </div>
  </div>

  <!-- Toast de Sucesso/Erro -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
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
</template>

<script>
import ComposicaoPropriaForm from './ComposicaoPropriaForm.vue';

export default {
  components: { ComposicaoPropriaForm },
  data() {
    return {
      // Dados da listagem
      items: {
        data: [],
        current_page: 1,
        from: 0,
        to: 0,
        total: 0,
        last_page: 1
      },
      // Filtros
      filtros: {
        descricao: ''
      },
      // Form
      form: null,
      // Estado
      modoEdicao: false,
      errors: {},
      loading: false,
      itemModal: null,
      toast: null,
      toastType: 'success',
      toastTitle: '',
      toastMessage: ''
    }
  },
  mounted() {
    // Inicializa o modal
    this.itemModal = new bootstrap.Modal(document.getElementById('itemModal'), {
      backdrop: 'static',
      keyboard: false
    });

    // Inicializa o toast
    this.toast = new bootstrap.Toast(document.getElementById('toast'), {
      autohide: true,
      delay: 3000
    });

    // Carrega os dados iniciais
    this.carregarDados();
  },
  methods: {
    formatCurrency(value) {
      if (typeof value !== 'number') return value;
      return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    },
    formatCurrencyBRL(value) {
      if (typeof value !== 'number') value = Number(value) || 0;
      return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    },
    async carregarDados() {
      try {
        const response = await axios.get('/orcamento/composicao-propria/listar', {
          params: {
            page: this.items.current_page,
            ...this.filtros
          }
        });
        
        if (response.data && response.data.data) {
          this.items = {
            data: response.data.data || [],
            current_page: response.data.current_page || 1,
            from: response.data.from || 0,
            to: response.data.to || 0,
            total: response.data.total || 0,
            last_page: response.data.last_page || 1
          };
          
          if (this.items.data.length === 0 && this.items.current_page > 1) {
            this.items.current_page = 1;
            await this.carregarDados();
          }
        }
      } catch (error) {
        console.error('Erro ao carregar dados:', error);
        this.mostrarErro('Erro ao carregar dados');
      }
    },
    filtrarDados() {
      this.items.current_page = 1;
      this.carregarDados();
    },
    async mudarPagina(page) {
      this.items.current_page = page;
      await this.carregarDados();
    },
    abrirModalCriar() {
      this.modoEdicao = false;
      this.form = {
        codigo: '',
        descricao: '',
        unidade: '',
        valor_total_mat_equip: 0,
        valor_total_mao_obra: 0,
        valor_total_geral: 0,
        itens: []
      };
      this.itemModal.show();
    },
    async editarItem(item) {
      this.modoEdicao = true;
      this.loading = true;
      try {
        const response = await axios.get(`/orcamento/composicao-propria/${item.id}/edit`);
        this.form = response.data;
        this.itemModal.show();
      } catch (error) {
        this.mostrarErro('Erro ao carregar dados para edição');
      } finally {
        this.loading = false;
      }
    },
    async salvarItem(formData) {
      this.loading = true;
      this.errors = {};

      // Converter campos de moeda para número
      const parseMoeda = v => {
        if (typeof v === 'number') return v;
        if (!v) return 0;
        return parseFloat(v.replace(/\./g, '').replace(',', '.')) || 0;
      };
      const itensConvertidos = (formData.itens || []).map(item => ({
        referencia: item.referencia,
        codigo_item: item.codigo_item,
        descricao: item.descricao,
        unidade: item.unidade,
        valor_mat_equip: parseMoeda(item.valor_mat_equip),
        valor_mao_obra: parseMoeda(item.valor_mao_obra),
        valor_total: parseMoeda(item.valor_total),
        coeficiente: parseMoeda(item.coeficiente),
        valor_mat_equip_ajustado: parseMoeda(item.valor_mat_equip_ajustado),
        valor_mao_obra_ajustado: parseMoeda(item.valor_mao_obra_ajustado),
        valor_total_ajustado: parseMoeda(item.valor_total_ajustado),
      }));
      const payload = {
        ...formData,
        itens: itensConvertidos
      };

      try {
        const headers = {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
        if (this.modoEdicao) {
          await axios.put(`/orcamento/composicao-propria/${formData.id}`, payload, { headers });
          this.mostrarSucesso('Composição atualizada com sucesso!');
        } else {
          await axios.post('/orcamento/composicao-propria', payload, { headers });
          this.mostrarSucesso('Composição criada com sucesso!');
        }
        this.itemModal.hide();
        this.items.current_page = 1;
        await this.carregarDados();
      } catch (error) {
        console.error('Erro ao salvar:', error);
        if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          this.errors = Object.keys(errors).reduce((acc, key) => {
            acc[key] = errors[key][0];
            return acc;
          }, {});
          const primeiroErro = Object.values(this.errors)[0];
          this.mostrarErro(primeiroErro);
        } else {
          this.mostrarErro('Erro ao salvar composição');
        }
      } finally {
        this.loading = false;
      }
    },
    async excluirItem(item) {
      if (!confirm('Tem certeza que deseja excluir esta composição?')) {
        return;
      }
      try {
        await axios.delete(`/orcamento/composicao-propria/${item.id}`, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        this.mostrarSucesso('Composição excluída com sucesso!');
        await this.carregarDados();
      } catch (error) {
        console.error('Erro ao excluir:', error);
        this.mostrarErro('Erro ao excluir composição');
      }
    },
    fecharModal() {
      this.itemModal.hide();
      this.form = null;
    },
    mostrarToast(type, title, message) {
      this.toastType = type;
      this.toastTitle = title;
      this.toastMessage = message;
      this.toast.show();
    },
    mostrarSucesso(message) {
      this.mostrarToast('success', 'Sucesso!', message);
    },
    mostrarErro(message) {
      this.mostrarToast('error', 'Erro!', message);
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
      });
    }
  }
}
</script>

<style scoped>
.text-custom {
  color: #18578A;
}
</style> 