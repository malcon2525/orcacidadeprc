<template>
  <div class="row g-4">
    <div class="col-md-6">
      <div class="card p-4 shadow-sm">
        <div class="mb-3">
          <label class="form-label">ENTIDADE ORÇAMENTÁRIA</label>
          <select class="form-select" v-model="form.entidade_orcamentaria_id">
            <option value="">ESCOLHA</option>
            <option v-for="e in selects.entidades_orcamentarias" :key="e.id" :value="e.id">{{ e.nome }}</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">tabela DERPR</label>
          <select class="form-select" v-model="form.derpr">
            <option value="">ESCOLHA</option>
            <option v-for="d in selects.datas_base_derpr" :key="d.data + '-' + d.desoneracao" :value="d.data + '|' + d.desoneracao">
              {{ formatarData(d.data) }} - {{ d.desoneracao === 'com' ? 'Com desoneração' : 'Sem desoneração' }}
            </option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">tabela SINAPI</label>
          <select class="form-select" v-model="form.sinapi">
            <option value="">ESCOLHA</option>
            <option v-for="d in selects.datas_base_sinapi" :key="d.data + '-' + d.desoneracao" :value="d.data + '|' + d.desoneracao">
              {{ formatarData(d.data) }} - {{ d.desoneracao === 'com' ? 'Com desoneração' : 'Sem desoneração' }}
            </option>
          </select>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
          <button class="btn btn-secondary" @click="resetar">Sair</button>
          <button class="btn btn-primary" @click="salvar" :disabled="salvando">
            Gravar
          </button>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-4 bg-light">
        <h6 class="fw-bold mb-3">CONFIGURAÇÕES ATUAIS</h6>
        <div v-if="configAtual">
          <div class="mb-2">
            <span class="fw-semibold">Entidade Orçamentária:</span><br>
            {{ entidadeNome(configAtual.entidade_orcamentaria_id) }}
          </div>
          <div class="mb-2">
            <span class="fw-semibold">tabela DERPR</span><br>
            {{ formatarData(configAtual.data_base_derpr) }} - {{ configAtual.derpr_desoneracao === 'com' ? 'Com desoneração' : 'Sem desoneração' }}
          </div>
          <div class="mb-2">
            <span class="fw-semibold">tabela SINAPI</span><br>
            {{ formatarData(configAtual.data_base_sinapi) }} - {{ configAtual.sinapi_desoneracao === 'com' ? 'Com desoneração' : 'Sem desoneração' }}
          </div>
        </div>
        <div v-else class="text-muted">Nenhuma configuração cadastrada.</div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      form: {
        entidade_orcamentaria_id: '',
        derpr: '', // formato: data|desoneracao
        sinapi: '', // formato: data|desoneracao
      },
      selects: {
        entidades_orcamentarias: [],
        datas_base_derpr: [],
        datas_base_sinapi: []
      },
      configAtual: null,
      salvando: false
    };
  },
  mounted() {
    this.carregarSelects();
    this.carregarConfigAtual();
  },
  methods: {
    carregarSelects() {
      axios.get('/configuracoes-gerais/selects').then(res => {
        this.selects = res.data;
      });
    },
    carregarConfigAtual() {
      axios.get('/configuracoes-gerais/show').then(res => {
        if (res.data) {
          this.configAtual = res.data;
          this.form.entidade_orcamentaria_id = res.data.entidade_orcamentaria_id;
          this.form.derpr = res.data.data_base_derpr + '|' + res.data.derpr_desoneracao;
          this.form.sinapi = res.data.data_base_sinapi + '|' + res.data.sinapi_desoneracao;
        }
      });
    },
    salvar() {
      if (!this.form.entidade_orcamentaria_id || !this.form.derpr || !this.form.sinapi) {
        alert('Preencha todos os campos obrigatórios.');
        return;
      }
      const [data_base_derpr, derpr_desoneracao] = this.form.derpr.split('|');
      const [data_base_sinapi, sinapi_desoneracao] = this.form.sinapi.split('|');
      this.salvando = true;
      axios.post('/configuracoes-gerais', {
        entidade_orcamentaria_id: this.form.entidade_orcamentaria_id,
        data_base_derpr,
        derpr_desoneracao,
        data_base_sinapi,
        sinapi_desoneracao
      }).then(() => {
        this.carregarConfigAtual();
        alert('Configuração salva com sucesso!');
      }).finally(() => {
        this.salvando = false;
      });
    },
    resetar() {
      this.carregarConfigAtual();
    },
    formatarData(data) {
      if (!data) return '';
      // Trata tanto 'YYYY-MM-DD' quanto 'YYYY-MM-DDTHH:mm:ss...' (ISO)
      let d = data;
      if (d.includes('T')) d = d.split('T')[0];
      const [y, m, day] = d.split('-');
      return `${day}/${m}/${y}`;
    },
    entidadeNome(id) {
      const ent = this.selects.entidades_orcamentarias.find(e => e.id == id);
      return ent ? ent.nome : '';
    }
  }
}
</script>

<style scoped>
.card {
  border-radius: 10px;
}
label {
  font-weight: 500;
  color: #444;
}
.bg-light {
  background: #f8f9fa !important;
}
</style> 