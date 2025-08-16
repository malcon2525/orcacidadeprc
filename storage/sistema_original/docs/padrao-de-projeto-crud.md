# Template para CRUDs

Este template fornece uma estrutura completa para implementação de CRUDs no sistema, incluindo frontend (Vue.js) e backend (Laravel).

## 1. Estrutura de Arquivos

```
app/
├── Http/
│   └── Controllers/
│       └── [Namespace]/
│           └── [Nome]Controller.php
├── Models/
│   └── [Nome].php
└── resources/
    └── js/
        └── components/
            └── [namespace]/
                └── [nome]/
                    └── Lista[Nome].vue
```

## Observação sobre estilos

Todos os CRUDs devem utilizar o arquivo de estilos global `resources/css/crud-styles.css` para padronização visual.

- O cabeçalho das tabelas deve usar a classe `text-custom` nos `<th>`, que aplica o azul padrão do sistema (`#18578A`).
- Não defina cores inline para cabeçalhos de tabela, utilize sempre a classe global.
- Isso garante consistência visual entre todos os módulos do sistema.

## 2. Rotas (routes/web.php)

```php
// Rotas para [Nome]
Route::get('/[nome-plural]', [App\Http\Controllers\[Namespace]\[Nome]Controller::class, 'index'])->name('[nome-plural].index');
Route::get('/[nome-plural]/listar', [App\Http\Controllers\[Namespace]\[Nome]Controller::class, 'listar'])->name('[nome-plural].listar');
Route::get('/[nome-plural]/create', [App\Http\Controllers\[Namespace]\[Nome]Controller::class, 'create'])->name('[nome-plural].create');
Route::post('/[nome-plural]', [App\Http\Controllers\[Namespace]\[Nome]Controller::class, 'store'])->name('[nome-plural].store');
Route::get('/[nome-plural]/{id}/edit', [App\Http\Controllers\[Namespace]\[Nome]Controller::class, 'edit'])->name('[nome-plural].edit');
Route::put('/[nome-plural]/{id}', [App\Http\Controllers\[Namespace]\[Nome]Controller::class, 'update'])->name('[nome-plural].update');
Route::delete('/[nome-plural]/{id}', [App\Http\Controllers\[Namespace]\[Nome]Controller::class, 'destroy'])->name('[nome-plural].destroy');
```

## 3. Controller

```php
<?php

namespace App\Http\Controllers\[Namespace];

use App\Http\Controllers\Controller;
use App\Models\[Nome];
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class [Nome]Controller extends Controller
{
    /**
     * Exibe a listagem do recurso
     */
    public function index()
    {
        return view('[namespace].[nome-plural].index');
    }

    /**
     * Retorna os dados para a listagem
     */
    public function listar(Request $request)
    {
        Log::info('Iniciando listagem de [nome-plural]');
        
        $query = [Nome]::query();

        // Aplicar filtros
        if ($request->has('campo') && !empty($request->campo)) {
            $query->where('campo', 'like', '%' . $request->campo . '%');
        }

        // Ordenação padrão
        $query->orderBy('campo', 'asc');

        // Paginação
        $result = $query->paginate(100);
        
        Log::info('Dados encontrados:', ['total' => $result->total()]);
        
        return response()->json($result);
    }

    /**
     * Armazena um novo recurso
     */
    public function store(Request $request)
    {
        Log::info('Iniciando criação de [nome]', $request->all());
        
        $mensagens = [
            'required' => 'O campo :attribute é obrigatório',
            'string' => 'O campo :attribute deve ser um texto',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres',
            'email' => 'O campo :attribute deve ser um e-mail válido',
            'unique' => 'Este :attribute já está cadastrado',
            'integer' => 'O campo :attribute deve ser um número inteiro',
            'in' => 'O valor selecionado para :attribute é inválido'
        ];

        $atributos = [
            'campo' => 'nome do campo',
            // Adicione outros campos aqui
        ];

        $validated = $request->validate([
            'campo' => 'required|string|max:255',
            // Adicione outras regras aqui
        ], $mensagens, $atributos);

        $item = [Nome]::create($validated);
        
        Log::info('[Nome] criado com sucesso', ['id' => $item->id]);

        return response()->json($item, 201);
    }

    /**
     * Atualiza um recurso existente
     */
    public function update(Request $request, $id)
    {
        Log::info('Iniciando atualização de [nome]', ['id' => $id, 'dados' => $request->all()]);
        
        $mensagens = [
            'required' => 'O campo :attribute é obrigatório',
            'string' => 'O campo :attribute deve ser um texto',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres',
            'email' => 'O campo :attribute deve ser um e-mail válido',
            'unique' => 'Este :attribute já está cadastrado',
            'integer' => 'O campo :attribute deve ser um número inteiro',
            'in' => 'O valor selecionado para :attribute é inválido'
        ];

        $atributos = [
            'campo' => 'nome do campo',
            // Adicione outros campos aqui
        ];

        $item = [Nome]::findOrFail($id);

        $validated = $request->validate([
            'campo' => 'required|string|max:255',
            // Adicione outras regras aqui
        ], $mensagens, $atributos);

        $item->update($validated);
        
        Log::info('[Nome] atualizado com sucesso', ['id' => $id]);

        return response()->json($item);
    }

    /**
     * Remove um recurso
     */
    public function destroy($id)
    {
        Log::info('Iniciando exclusão de [nome]', ['id' => $id]);
        
        $item = [Nome]::findOrFail($id);
        $item->delete();
        
        Log::info('[Nome] excluído com sucesso', ['id' => $id]);

        return response()->json(null, 204);
    }
}
```

## 4. Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class [Nome] extends Model
{
    protected $table = '[nome-plural]';
    
    protected $fillable = [
        'campo1',
        'campo2',
        // Adicione outros campos aqui
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // Adicione outros casts aqui
    ];
}
```

## 5. Componente Vue

```vue
<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho -->
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold" style="color: #5EA853;">
                    <i class="fas fa-[icone] me-2"></i>[Nome Plural]
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" @click="abrirModalCriar">
                        <i class="fas fa-plus me-2"></i>Novo [Nome]
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
                                   id="filtroCampo" 
                                   v-model="filtros.campo" 
                                   @input="filtrarDados"
                                   placeholder="Filtrar por campo">
                            <label for="filtroCampo">Campo</label>
                        </div>
                    </div>
                </div>

                <!-- Tabela -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="fw-semibold text-custom">Nome</th>
                                <th class="fw-semibold text-custom">Descrição</th>
                                <th class="fw-semibold text-custom">Status</th>
                                <th class="fw-semibold text-end text-custom">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in items.data" :key="item.id">
                                <td>{{ item.nome }}</td>
                                <td>{{ item.descricao }}</td>
                                <td>
                                    <span class="badge" :class="item.status === 'ativo' ? 'bg-success' : 'bg-danger'">
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-outline-secondary btn-sm me-2" 
                                            @click="editarItem(item)" 
                                            title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" 
                                            @click="excluirItem(item)" 
                                            title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
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
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-semibold">
                            <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
                            {{ modoEdicao ? 'Editar' : 'Novo' }} [Nome]
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <form @submit.prevent="salvarItem" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.campo }"
                                               id="campo" 
                                               v-model="form.campo" 
                                               required>
                                        <label for="campo">Campo</label>
                                        <div class="invalid-feedback">{{ errors.campo }}</div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" @click="salvarItem" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i v-else class="fas fa-save me-2"></i>
                            {{ loading ? 'Salvando...' : 'Salvar' }}
                        </button>
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
    </div>
</template>

<script>
export default {
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
                campo: ''
            },
            // Form
            form: {
                campo: ''
            },
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
        async carregarDados() {
            try {
                console.log('Iniciando carregamento de dados...');
                const response = await axios.get('/[nome-plural]/listar', {
                    params: {
                        page: this.items.current_page,
                        ...this.filtros
                    }
                });
                
                console.log('Resposta bruta da API:', response);
                console.log('Dados da resposta:', response.data);
                
                // Verifica se a resposta tem a estrutura esperada
                if (response.data && response.data.data) {
                    // Atualiza os dados da listagem
                    this.items = {
                        data: response.data.data || [],
                        current_page: response.data.current_page || 1,
                        from: response.data.from || 0,
                        to: response.data.to || 0,
                        total: response.data.total || 0,
                        last_page: response.data.last_page || 1
                    };
                    
                    console.log('Dados atualizados:', this.items);
                    
                    // Se não houver dados na página atual e houver mais de uma página,
                    // volta para a primeira página
                    if (this.items.data.length === 0 && this.items.current_page > 1) {
                        console.log('Nenhum dado encontrado, voltando para primeira página...');
                        this.items.current_page = 1;
                        await this.carregarDados();
                    }
                } else {
                    console.error('Estrutura de dados inválida:', response.data);
                    this.mostrarErro('Erro ao carregar dados: estrutura inválida');
                }
            } catch (error) {
                console.error('Erro ao carregar dados:', error);
                console.error('Detalhes do erro:', {
                    message: error.message,
                    response: error.response,
                    request: error.request
                });
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
            this.limparFormulario();
            this.itemModal.show();
        },
        editarItem(item) {
            this.modoEdicao = true;
            this.form = { ...item };
            this.itemModal.show();
        },
        limparFormulario() {
            this.form = {
                campo: ''
            };
            this.errors = {};
        },
        async salvarItem() {
            this.loading = true;
            this.errors = {};

            try {
                console.log('Iniciando salvamento...', this.form);
                
                const headers = {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                };
                
                if (this.modoEdicao) {
                    console.log('Atualizando item...');
                    const response = await axios.put(`/[nome-plural]/${this.form.id}`, this.form, { headers });
                    console.log('Resposta da atualização:', response.data);
                    this.mostrarSucesso('[Nome] atualizado com sucesso!');
                } else {
                    console.log('Criando novo item...');
                    const response = await axios.post('/[nome-plural]', this.form, { headers });
                    console.log('Resposta da criação:', response.data);
                    this.mostrarSucesso('[Nome] criado com sucesso!');
                }

                this.itemModal.hide();
                this.limparFormulario();
                
                // Força a atualização da listagem
                console.log('Recarregando dados após salvar...');
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
                    
                    // Mostrar mensagem de erro específica para o primeiro campo com erro
                    const primeiroErro = Object.values(this.errors)[0];
                    this.mostrarErro(primeiroErro);
                } else {
                    this.mostrarErro('Erro ao salvar [nome]');
                }
            } finally {
                this.loading = false;
            }
        },
        async excluirItem(item) {
            if (!confirm('Tem certeza que deseja excluir este [nome]?')) {
                return;
            }

            try {
                await axios.delete(`/[nome-plural]/${item.id}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                this.mostrarSucesso('[Nome] excluído com sucesso!');
                this.carregarDados();
            } catch (error) {
                console.error('Erro ao excluir:', error);
                this.mostrarErro('Erro ao excluir [nome]');
            }
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
        }
    }
}
</script>
```

## 6. Instruções de Uso

1. **Substitua os Placeholders**:
   - `[Nome]`: Nome singular do recurso (ex: EntidadeOrcamentaria)
   - `[namespace]`: Namespace do controlador (ex: App\Http\Controllers\Orcamento)
   - `[nome-plural]`: Nome plural do recurso (ex: orcamentos)

## 7. Registrar o Componente Vue
No arquivo `resources/js/app.js`, adicione o registro do componente:

```javascript
// Importar o componente
import ListaTiposOrcamentos from './components/orcamento/tipos-orcamentos/ListaTiposOrcamentos.vue';

// Registrar o componente
app.component('lista-tipos-orcamentos', ListaTiposOrcamentos);
```

O nome do componente deve seguir o padrão kebab-case (ex: `lista-tipos-orcamentos`) e corresponder à tag usada na view Blade.