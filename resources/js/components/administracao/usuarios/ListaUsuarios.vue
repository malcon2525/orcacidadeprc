<template>
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <!-- Cabeçalho Compacto -->
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold titulo-principal">
                    <i class="fas fa-users-cog me-2"></i>Gerenciamento de Usuários e Permissões de Acesso
                </h6>
            </div>

            <!-- Corpo -->
            <div class="card-body">
                <!-- DEBUG TEMPORÁRIO -->
                <div style="background: #f0f0f0; padding: 10px; margin: 10px; border-radius: 5px; font-size: 12px;">
                    <strong>DEBUG ListaUsuarios:</strong><br>
                    currentUser: {{ currentUser ? 'Carregado' : 'Não carregado' }}<br>
                    isSuper: {{ isSuper }}<br>
                    canViewModule: {{ canViewModule }}<br>
                    canPerformActions: {{ canPerformActions }}<br>
                    activeTab: {{ activeTab }}<br>
                    <hr>
                    <strong>Dados do usuário:</strong><br>
                    <pre>{{ JSON.stringify(currentUser, null, 2) }}</pre>
                </div>
                
                <!-- Sistema de Abas -->
                <div class="admin-tabs-container">
                    <!-- Navegação das Abas -->
                    <ul class="nav nav-tabs admin-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button 
                                v-if="canViewModule"
                                class="nav-link admin-tab" 
                                :class="{ active: activeTab === 'usuarios' }"
                                @click="changeTab('usuarios')"
                                type="button"
                                role="tab"
                            >
                                <i class="fas fa-users me-2"></i>
                                Usuários
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button 
                                v-if="canViewModule"
                                class="nav-link admin-tab" 
                                :class="{ active: activeTab === 'papeis' }"
                                @click="changeTab('papeis')"
                                type="button"
                                role="tab"
                            >
                                <i class="fas fa-user-tag me-2"></i>
                                Papéis
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button 
                                v-if="canViewModule"
                                class="nav-link admin-tab" 
                                :class="{ active: activeTab === 'permissoes' }"
                                @click="changeTab('permissoes')"
                                type="button"
                                role="tab"
                            >
                                <i class="fas fa-key me-2"></i>
                                Permissões
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button 
                                v-if="canViewModule"
                                class="nav-link admin-tab" 
                                :class="{ active: activeTab === 'busca' }"
                                @click="changeTab('busca')"
                                type="button"
                                role="tab"
                            >
                                <i class="fas fa-search me-2"></i>
                                Busca Global
                            </button>
                        </li>
                    </ul>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content admin-tab-content">
                        <!-- Aba Usuários -->
                        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'usuarios' }" role="tabpanel" v-if="canViewModule">
                            <!-- Cabeçalho Compacto da Aba Usuários -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-custom">
                                    <i class="fas fa-users me-2"></i>Lista de Usuários
                                </h6>
                                <div class="d-flex gap-2">
                                    <!-- Botão Filtros -->
                                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" @click="toggleFiltrosUsuarios">
                                        <i class="fas fa-filter"></i>
                                        <span>Filtros</span>
                                        <i class="fas" :class="filtrosUsuariosVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                    </button>
                                    <!-- Botão Novo Usuário -->
                                    <button 
                                        v-if="canPerformActions"
                                        class="btn btn-outline-success d-flex align-items-center gap-2 px-3 py-2 btn-novo-usuario" 
                                        @click="abrirModalCriarUsuario"
                                    >
                                        <i class="fas fa-plus"></i>
                                        <span>Novo Usuário</span>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Filtros da Aba Usuários (Compactos) -->
                            <div class="filtros-aba-container mb-3" v-if="filtrosUsuariosVisiveis">
                                
                                <div class="filtros-aba-content" :class="{ 'show': filtrosUsuariosVisiveis }">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="filtroNome" v-model="filtrosUsuarios.nome" @input="filtrarUsuarios" placeholder="Nome...">
                                                <label for="filtroNome">Nome</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="email" class="form-control" id="filtroEmail" v-model="filtrosUsuarios.email" @input="filtrarUsuarios" placeholder="Email...">
                                                <label for="filtroEmail">Email</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating">
                                                <select class="form-select" id="filtroStatusUsuario" v-model="filtrosUsuarios.status" @change="filtrarUsuarios">
                                                    <option value="">Todos</option>
                                                    <option value="ativo">Ativo</option>
                                                    <option value="inativo">Inativo</option>
                                                </select>
                                                <label for="filtroStatusUsuario">Status</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating">
                                                <select class="form-select" id="filtroTipoUsuario" v-model="filtrosUsuarios.tipo" @change="filtrarUsuarios">
                                                    <option value="">Todos</option>
                                                    <option value="local">Local</option>
                                                    <option value="ad">AD</option>
                                                </select>
                                                <label for="filtroTipoUsuario">Tipo</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-outline-secondary w-100 h-100" @click="limparFiltrosUsuarios">
                                                <i class="fas fa-times me-2"></i>Limpar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabela de Usuários -->
                            <div class="table-responsive" v-if="dadosFiltrados.length > 0">
                                <table class="table table-hover align-middle table-admin">
                                    <thead>
                                        <tr>
                                            <th class="fw-semibold text-custom">Usuário</th>
                                            <th class="fw-semibold text-custom">Email</th>
                                            <th class="fw-semibold text-custom">Papéis</th>
                                            <th class="fw-semibold text-custom text-center w-100px">Status</th>
                                            <th class="fw-semibold text-custom text-center w-100px">Tipo</th>
                                            <th class="fw-semibold text-end text-custom w-100px">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="usuario in dadosPaginados" :key="usuario.id" class="table-admin-row">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-admin me-2">
                                                        <i class="fas fa-user-circle text-muted"></i>
                                                    </div>
                                                    <div class="fw-medium">{{ usuario.name }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate max-w-200px" :title="usuario.email">
                                                    {{ usuario.email }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="papeis-badges">
                                                    <span v-for="papel in (usuario.roles || [])" 
                                                          :key="papel.id" 
                                                          class="badge badge-info">
                                                        {{ papel.display_name }}
                                                    </span>
                                                    <span v-if="!usuario.roles || usuario.roles.length === 0" class="text-muted small">
                                                        <em>Nenhum papel</em>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge" :class="usuario.is_active ? 'badge-success' : 'badge-danger'">
                                                    <i class="fas" :class="usuario.is_active ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                                    {{ usuario.is_active ? 'ATIVO' : 'INATIVO' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge" :class="usuario.login_type === 'ad' ? 'badge-warning' : 'badge-info'">
                                                    {{ usuario.login_type === 'ad' ? 'AD' : 'LOCAL' }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <button 
                                                        v-if="canPerformActions"
                                                        class="btn btn-sm btn-warning" 
                                                        @click="editarUsuario(usuario)" 
                                                        title="Editar"
                                                    >
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button 
                                                        v-if="canPerformActions"
                                                        class="btn btn-sm btn-danger" 
                                                        @click="excluirUsuario(usuario)" 
                                                        title="Excluir"
                                                    >
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Estado de Carregamento -->
                            <div v-if="loading" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="mt-2 text-muted">Carregando usuários...</p>
                            </div>

                            <!-- Estado Vazio -->
                            <div v-if="!loading && dadosFiltrados.length === 0" class="text-center py-5">
                                <i class="fas fa-users text-muted mb-3 icon-large"></i>
                                <h6 class="text-muted mb-2">Nenhum usuário encontrado</h6>
                                <p class="text-muted small mb-0">Tente ajustar os filtros ou criar um novo usuário.</p>
                            </div>
                        </div>

                        <!-- Aba Papéis -->
                        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'papeis' }" role="tabpanel" v-if="canViewModule">
                            <!-- Cabeçalho Compacto da Aba Papéis -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-custom">
                                    <i class="fas fa-user-tag me-2"></i>Lista de Papéis
                                </h6>
                                <div class="d-flex gap-2">
                                    <!-- Botão Filtros -->
                                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" @click="toggleFiltrosPapeis">
                                        <i class="fas fa-filter"></i>
                                        <span>Filtros</span>
                                        <i class="fas" :class="filtrosPapeisVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                    </button>
                                    <!-- Botão Novo Papel -->
                                    <button 
                                        v-if="canManagePapeis"
                                        class="btn btn-outline-success d-flex align-items-center gap-2 px-3 py-2" 
                                        @click="abrirModalCriarPapel"
                                    >
                                        <i class="fas fa-plus"></i>
                                        <span>Novo Papel</span>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Filtros da Aba Papéis (Compactos) -->
                            <div class="filtros-aba-container mb-3" v-if="filtrosPapeisVisiveis">
                                <div class="filtros-aba-content" :class="{ 'show': filtrosPapeisVisiveis }">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="filtroNomePapel" v-model="filtrosPapeis.nome" @input="filtrarPapeis" placeholder="Nome...">
                                                <label for="filtroNomePapel">Nome</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="filtroNomeInterno" v-model="filtrosPapeis.nome_interno" @input="filtrarPapeis" placeholder="Nome Interno...">
                                                <label for="filtroNomeInterno">Nome Interno</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select class="form-control" id="filtroStatusPapel" v-model="filtrosPapeis.is_active" @change="filtrarPapeis">
                                                    <option value="">Todos</option>
                                                    <option value="1">Ativo</option>
                                                    <option value="0">Inativo</option>
                                                </select>
                                                <label for="filtroStatusPapel">Status</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabela de Papéis -->
                            <div v-if="!loadingPapeis && papeisFiltrados.length > 0" class="table-responsive">
                                <table class="table table-hover align-middle table-admin">
                                    <thead>
                                        <tr>
                                            <th>Papel</th>
                                            <th>Nome Interno</th>
                                            <th class="w-100px">Permissões</th>
                                            <th class="w-100px">Usuários</th>
                                            <th class="w-100px">Status</th>
                                            <th class="text-end w-180px">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="papel in papeisPaginados" :key="papel.id" class="table-admin-row">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <!-- <div class="papel-avatar me-2">
                                                        <i class="fas fa-user-tag text-muted"></i>
                                                    </div> -->
                                                    <div>
                                                        <div class="fw-medium">{{ papel.display_name }}</div>
                                                        <div class="text-muted small">{{ papel.description }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <code class="text-muted">{{ papel.name }}</code>
                                            </td>
                                            <td>
                                                <span class="badge badge-light">
                                                    {{ papel.permissions_count || 0 }} permissões
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-light">
                                                    {{ papel.users_count || 0 }} usuários
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge" :class="papel.is_active ? 'badge-success' : 'badge-danger'">
                                                    <i class="fas" :class="papel.is_active ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                                    {{ papel.is_active ? 'ATIVO' : 'INATIVO' }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <button 
                                                        v-if="canManagePapeis || canViewPapeis"
                                                        class="btn btn-sm btn-info" 
                                                        @click="abrirModalGerenciarPermissoes(papel)" 
                                                        title="Gerenciar Permissões"
                                                    >
                                                        <i class="fas fa-key"></i>
                                                    </button>
                                                    <button 
                                                        v-if="canManagePapeis || canViewPapeis"
                                                        class="btn btn-sm btn-info" 
                                                        @click="abrirModalGerenciarUsuarios(papel)" 
                                                        title="Gerenciar Usuários"
                                                    >
                                                        <i class="fas fa-users-cog"></i>
                                                    </button>
                                                    <button 
                                                        v-if="canManagePapeis"
                                                        class="btn btn-sm btn-warning" 
                                                        @click="editarPapel(papel)" 
                                                        title="Editar Papel"
                                                    >
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button 
                                                        v-if="canManagePapeis"
                                                        class="btn btn-sm" 
                                                        :class="(papel.users_count > 0 || papel.permissions_count > 0) ? 'btn-excluir-desabilitado' : 'btn-danger'"
                                                        @click="(papel.users_count > 0 || papel.permissions_count > 0) ? mostrarMensagemPapelNaoExcluivel(papel) : handleExcluirPapel(papel)" 
                                                        :title="(papel.users_count > 0 || papel.permissions_count > 0) ? 'Não é possível excluir papel com usuários ou permissões' : `Excluir papel '${papel.display_name}'`"
                                                    >
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Estado de Carregamento -->
                            <div v-if="loadingPapeis" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="mt-2 text-muted">Carregando papéis...</p>
                            </div>

                            <!-- Estado Vazio -->
                            <div v-if="!loadingPapeis && papeisFiltrados.length === 0" class="text-center py-5">
                                <i class="fas fa-user-tag text-muted mb-3 icon-large"></i>
                                <h6 class="text-muted mb-2">Nenhum papel encontrado</h6>
                                <p class="text-muted small mb-0">Tente ajustar os filtros ou criar um novo papel.</p>
                            </div>
                        </div>
                        
                        <!-- Aba Permissões -->
                        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'permissoes' }" role="tabpanel" v-if="canViewModule">
                            <!-- Cabeçalho Compacto da Aba Permissões -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-custom">
                                    <i class="fas fa-key me-2"></i>Relatório de Permissões
                                </h6>
                                <div class="d-flex gap-2">
                                    <!-- Botão Filtros -->
                                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" @click="toggleFiltrosPermissoes">
                                        <i class="fas fa-filter"></i>
                                        <span>Filtros</span>
                                        <i class="fas" :class="filtrosPermissoesVisiveis ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                    </button>
                                    <!-- Botão Nova Permissão -->
                                    <button 
                                        v-if="canPerformActions"
                                        class="btn btn-outline-success d-flex align-items-center gap-2 px-3 py-2" 
                                        @click="abrirModalCriarPermissao"
                                    >
                                        <i class="fas fa-plus"></i>
                                        <span>Nova Permissão</span>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Filtros da Aba Permissões (Compactos) -->
                            <div class="filtros-aba-container mb-3" v-if="filtrosPermissoesVisiveis">
                                <div class="filtros-aba-content" :class="{ 'show': filtrosPermissoesVisiveis }">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="filtroNomePermissao" v-model="filtrosPermissoes.nome" @input="filtrarPermissoes" placeholder="Nome...">
                                                <label for="filtroNomePermissao">Nome</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="filtroNomeInternoPermissao" v-model="filtrosPermissoes.nome_interno" @input="filtrarPermissoes" placeholder="Nome Interno...">
                                                <label for="filtroNomeInternoPermissao">Nome Interno</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select class="form-control" id="filtroStatusPermissao" v-model="filtrosPermissoes.is_active" @change="filtrarPermissoes">
                                                    <option value="">Todos</option>
                                                    <option value="1">Ativo</option>
                                                    <option value="0">Inativo</option>
                                                </select>
                                                <label for="filtroStatusPermissao">Status</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabela de Permissões -->
                            <div v-if="!loadingPermissoes && permissoesFiltradas.length > 0" class="table-responsive">
                                <table class="table table-hover align-middle table-admin">
                                    <thead>
                                        <tr>
                                            <th >Permissão</th>
                                            <th >Nome Interno</th>
                                            <th class="w-100px">Papéis</th>
                                            <th class="w-100px">Usuários Afetados</th>
                                            <th class="w-100px">Status</th>
                                            <th class="text-end w-180px">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="permissao in permissoesPaginadas" :key="permissao.id" class="table-admin-row">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-admin me-2">
                                                        <i class="fas fa-key text-muted"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ permissao.display_name }}</div>
                                                        <div class="text-muted small">{{ permissao.description }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <code class="text-muted">{{ permissao.name }}</code>
                                            </td>
                                            <td>
                                                <span class="badge badge-light">
                                                    {{ permissao.roles_count || 0 }} papéis
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-light">
                                                    {{ permissao.users_count || 0 }} usuários
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge" :class="permissao.is_active ? 'badge-success' : 'badge-danger'">
                                                    <i class="fas" :class="permissao.is_active ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                                    {{ permissao.is_active ? 'ATIVO' : 'INATIVO' }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <button class="btn btn-sm btn-info" @click="abrirModalVisualizarDetalhes(permissao)" title="Visualizar Detalhes">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button 
                                                        v-if="canPerformActions"
                                                        class="btn btn-sm btn-warning" 
                                                        @click="editarPermissao(permissao)" 
                                                        title="Editar"
                                                    >
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button 
                                                        v-if="canPerformActions"
                                                        class="btn btn-sm" 
                                                        :class="(permissao.roles_count > 0) ? 'btn-excluir-desabilitado' : 'btn-danger'"
                                                        @click="(permissao.roles_count > 0) ? mostrarMensagemPermissaoNaoExcluivel(permissao) : excluirPermissao(permissao)" 
                                                        :title="(permissao.roles_count > 0) ? 'Não é possível excluir permissão com papéis vinculados' : `Excluir permissão '${permissao.display_name}'`"
                                                    >
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Estado de Carregamento -->
                            <div v-if="loadingPermissoes" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="mt-2 text-muted">Carregando permissões...</p>
                            </div>

                            <!-- Estado Vazio -->
                            <div v-if="!loadingPermissoes && permissoesFiltradas.length === 0" class="text-center py-5">
                                <i class="fas fa-key text-muted mb-3 icon-large"></i>
                                <h6 class="text-muted mb-2">Nenhuma permissão encontrada</h6>
                                <p class="text-muted small mb-0">Tente ajustar os filtros ou verificar se há permissões cadastradas.</p>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'busca' }" role="tabpanel" v-if="canViewModule">
                            <BuscaGlobal ref="buscaGlobal" :isActive="activeTab === 'busca'" />
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Paginação Dinâmica Fora do Card -->
        <div v-if="dadosFiltrados.length > 0" class="paginacao-container mt-4 z-1000" style="position: relative;">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Informações de Registros -->
                <div class="text-muted fw-medium">
                    Mostrando {{ (paginaAtual - 1) * itensPorPagina + 1 }} até {{ Math.min(paginaAtual * itensPorPagina, dadosFiltrados.length) }} de {{ dadosFiltrados.length }} registros
                </div>
                
                <div class="d-flex align-items-center">
                    <!-- Seletor de Itens por Página -->
                    <!-- <div class="d-flex align-items-center gap-2">
                        <label class="text-muted small mb-0">Itens por página:</label>
                        <select class="form-select form-select-sm" v-model="itensPorPagina" @change="mudarPagina(1)" style="width: auto;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div> -->
                    
                    <!-- Navegação -->
                    <nav>
                        <ul class="pagination admin-pagination mb-0">
                            <!-- Botão Anterior -->
                            <li class="page-item" :class="{ disabled: paginaAtual === 1 }">
                                <button class="page-link page-link-transparent" @click="mudarPagina(paginaAtual - 1)" aria-label="Anterior" :disabled="paginaAtual === 1">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            </li>
                            
                            <!-- Páginas -->
                            <li v-for="pagina in paginasVisiveis" 
                                :key="pagina" 
                                class="page-item" 
                                :class="{ active: pagina === paginaAtual, disabled: pagina === '...' }">
                                <button v-if="pagina !== '...'" 
                                        class="page-link page-link-transparent cursor-pointer" 
                                        @click="mudarPagina(pagina)"
                                        style="width: 100%; text-decoration: none;">
                                    {{ pagina }}
                                </button>
                                <span v-else class="page-link">{{ pagina }}</span>
                            </li>
                            
                            <!-- Botão Próximo -->
                            <li class="page-item" :class="{ disabled: paginaAtual === totalPaginas }">
                                <button class="page-link page-link-transparent" @click="mudarPagina(paginaAtual + 1)" aria-label="Próximo" :disabled="paginaAtual === totalPaginas">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Modal para Criar/Editar Usuário -->
        <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true" ref="modalUsuario">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas" :class="modoEdicao ? 'fa-edit' : 'fa-plus'"></i>
                            </div>
                            <h5 class="modal-title mb-0" id="modalUsuarioLabel">
                                {{ modoEdicao ? 'Editar Usuário' : 'Novo Usuário' }}
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <form @submit.prevent="salvarUsuario" novalidate>
                        <div class="modal-body">
                            <div class="row g-3">
                                <!-- Nome -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="nomeUsuario" 
                                            v-model="formUsuario.nome"
                                            :class="{ 'is-invalid': errors.name }"
                                            placeholder="Nome completo"
                                        >
                                        <label for="nomeUsuario">Nome Completo *</label>
                                        <div class="invalid-feedback" v-if="errors.name">
                                            {{ Array.isArray(errors.name) ? errors.name[0] : errors.name }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input 
                                            type="email" 
                                            class="form-control" 
                                            id="emailUsuario" 
                                            v-model="formUsuario.email"
                                            :class="{ 'is-invalid': errors.email }"
                                            placeholder="email@exemplo.com"
                                        >
                                        <label for="emailUsuario">Email *</label>
                                        <div class="invalid-feedback" v-if="errors.email">
                                            {{ Array.isArray(errors.email) ? errors.email[0] : errors.email }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Tipo de Login (Fixo - Local) -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input 
                                            type="text" 
                                            class="form-control bg-light" 
                                            id="tipoLogin" 
                                            value="Local"
                                            readonly
                                        >
                                        <label for="tipoLogin">Tipo de Login *</label>
                                        <small class="text-muted">Usuários criados manualmente são sempre do tipo Local</small>
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select 
                                            class="form-select" 
                                            id="statusUsuario" 
                                            v-model="formUsuario.is_active"
                                            :class="{ 'is-invalid': errors.is_active }"
                                        >
                                            <option :value="true">Ativo</option>
                                            <option :value="false">Inativo</option>
                                        </select>
                                        <label for="statusUsuario">Status *</label>
                                        <div class="invalid-feedback" v-if="errors.is_active">
                                            {{ Array.isArray(errors.is_active) ? errors.is_active[0] : errors.is_active }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Senha -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input 
                                            type="password" 
                                            class="form-control" 
                                            id="senhaUsuario" 
                                            v-model="formUsuario.password"
                                            :class="{ 'is-invalid': errors.password }"
                                            :placeholder="modoEdicao ? 'Deixe em branco para manter' : 'Senha'"
                                        >
                                        <label for="senhaUsuario">
                                            {{ modoEdicao ? 'Nova Senha (opcional)' : 'Senha *' }}
                                        </label>
                                        <div class="invalid-feedback" v-if="errors.password">
                                            {{ Array.isArray(errors.password) ? errors.password[0] : errors.password }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Confirmação de Senha -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input 
                                            type="password" 
                                            class="form-control" 
                                            id="confirmarSenha" 
                                            v-model="formUsuario.password_confirmation"
                                            :class="{ 'is-invalid': errors.password_confirmation }"
                                            :placeholder="modoEdicao ? 'Confirme a nova senha' : 'Confirme a senha'"
                                        >
                                        <label for="confirmarSenha">
                                            {{ modoEdicao ? 'Confirmar Nova Senha' : 'Confirmar Senha *' }}
                                        </label>
                                        <div class="invalid-feedback" v-if="errors.password_confirmation">
                                            {{ Array.isArray(errors.password_confirmation) ? errors.password_confirmation[0] : errors.password_confirmation }}
                                        </div>
                                    </div>
                                </div>
                                

                                

                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-success" :disabled="salvando">
                                <i class="fas fa-spinner fa-spin me-2" v-if="salvando"></i>
                                <i class="fas fa-save me-2" v-else></i>
                                {{ salvando ? 'Salvando...' : 'Salvar Usuário' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para Criar/Editar Papel -->
        <div class="modal fade" id="modalPapel" tabindex="-1" aria-labelledby="modalPapelLabel" aria-hidden="true" ref="modalPapel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas" :class="modoEdicaoPapel ? 'fa-edit' : 'fa-plus'"></i>
                            </div>
                            <h5 class="modal-title mb-0" id="modalPapelLabel">
                                {{ modoEdicaoPapel ? 'Editar Papel' : 'Novo Papel' }}
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <form @submit.prevent="salvarPapel">
                        <div class="modal-body">
                            <div class="row g-3">
                                <!-- Nome de Exibição -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="nomeExibicao" 
                                            v-model="formPapel.display_name"
                                            :class="{ 'is-invalid': errorsPapel.display_name }"
                                            placeholder="Nome de exibição"
                                            required
                                        >
                                        <label for="nomeExibicao">Nome de Exibição *</label>
                                        <div class="invalid-feedback" v-if="errorsPapel.display_name">
                                            {{ errorsPapel.display_name[0] }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Nome Interno -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="nomeInterno" 
                                            v-model="formPapel.name"
                                            :class="{ 'is-invalid': errorsPapel.name }"
                                            placeholder="nome_interno"
                                            required
                                        >
                                        <label for="nomeInterno">Nome Interno *</label>
                                        <div class="invalid-feedback" v-if="errorsPapel.name">
                                            {{ errorsPapel.name[0] }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Descrição -->
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea 
                                            class="form-control min-h-58 h-58 resize-vertical overflow-auto" 
                                            id="descricaoPapel" 
                                            v-model="formPapel.description"
                                            :class="{ 'is-invalid': errorsPapel.description }"
                                            placeholder="Descrição do papel"
                                            rows="3"
                                            required
                                        ></textarea>
                                        <label for="descricaoPapel">Descrição *</label>
                                        <div class="invalid-feedback" v-if="errorsPapel.description">
                                            {{ errorsPapel.description[0] }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select 
                                            class="form-select" 
                                            id="statusPapel" 
                                            v-model="formPapel.is_active"
                                            :class="{ 'is-invalid': errorsPapel.is_active }"
                                            required
                                        >
                                            <option :value="true">Ativo</option>
                                            <option :value="false">Inativo</option>
                                        </select>
                                        <label for="statusPapel">Status *</label>
                                        <div class="invalid-feedback" v-if="errorsPapel.is_active">
                                            {{ errorsPapel.is_active[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-success" :disabled="salvandoPapel">
                                <i class="fas fa-spinner fa-spin me-2" v-if="salvandoPapel"></i>
                                <i class="fas fa-save me-2" v-else></i>
                                {{ salvandoPapel ? 'Salvando...' : 'Salvar Papel' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para Criar/Editar Permissão -->
        <div class="modal fade" id="modalPermissao" tabindex="-1" aria-labelledby="modalPermissaoLabel" aria-hidden="true" ref="modalPermissao">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas" :class="modoEdicaoPermissao ? 'fa-edit' : 'fa-plus'"></i>
                            </div>
                            <h5 class="modal-title mb-0" id="modalPermissaoLabel">
                                {{ modoEdicaoPermissao ? 'Editar Permissão' : 'Nova Permissão' }}
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <form @submit.prevent="salvarPermissao">
                        <div class="modal-body">
                            <div class="row g-3">
                                <!-- Nome de Exibição -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="nomeExibicaoPermissao" 
                                            v-model="formPermissao.display_name"
                                            :class="{ 'is-invalid': errorsPermissao.display_name }"
                                            placeholder="Nome de exibição"
                                            required
                                        >
                                        <label for="nomeExibicaoPermissao">Nome de Exibição *</label>
                                        <div class="invalid-feedback" v-if="errorsPermissao.display_name">
                                            {{ errorsPermissao.display_name[0] }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Nome Interno -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="nomeInternoPermissao" 
                                            v-model="formPermissao.name"
                                            :class="{ 'is-invalid': errorsPermissao.name }"
                                            placeholder="nome_interno"
                                            required
                                        >
                                        <label for="nomeInternoPermissao">Nome Interno *</label>
                                        <div class="invalid-feedback" v-if="errorsPermissao.name">
                                            {{ errorsPermissao.name[0] }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Descrição -->
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea 
                                            class="form-control min-h-58 h-58 resize-vertical overflow-auto" 
                                            id="descricaoPermissao" 
                                            v-model="formPermissao.description"
                                            :class="{ 'is-invalid': errorsPermissao.description }"
                                            placeholder="Descrição da permissão"
                                            rows="3"
                                            required
                                        ></textarea>
                                        <label for="descricaoPermissao">Descrição *</label>
                                        <div class="invalid-feedback" v-if="errorsPermissao.description">
                                            {{ errorsPermissao.description[0] }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select 
                                            class="form-select" 
                                            id="statusPermissao" 
                                            v-model="formPermissao.is_active"
                                            :class="{ 'is-invalid': errorsPermissao.is_active }"
                                            required
                                        >
                                            <option :value="true">Ativo</option>
                                            <option :value="false">Inativo</option>
                                        </select>
                                        <label for="statusPermissao">Status *</label>
                                        <div class="invalid-feedback" v-if="errorsPermissao.is_active">
                                            {{ errorsPermissao.is_active[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-success" :disabled="salvandoPermissao">
                                <i class="fas fa-spinner fa-spin me-2" v-if="salvandoPermissao"></i>
                                <i class="fas fa-save me-2" v-else></i>
                                {{ salvandoPermissao ? 'Salvando...' : 'Salvar Permissão' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de Gerenciar Permissões do Papel - VERSÃO PADRONIZADA -->
        <div class="modal fade" id="modalGerenciarPermissoes" tabindex="-1" aria-labelledby="modalGerenciarPermissoesLabel" aria-hidden="true" ref="modalGerenciarPermissoes">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <!-- Cabeçalho do Modal -->
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-key"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="modal-title mb-0" id="modalGerenciarPermissoesLabel">
                                    Gerenciar Permissões do Papel
                                </h5>
                                <p class="mb-0 text-white-50" v-if="itemSelecionado">
                                    {{ itemSelecionado.display_name }}
                                </p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Corpo do Modal -->
                    <div class="modal-body">
                        <div v-if="loadingPermissoesPapel" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-2 text-muted">Carregando permissões...</p>
                        </div>
                        
                        <div v-else>
                            <!-- LAYOUT SIMPLES EM DUAS COLUNAS -->
                            <div class="layout-two-columns">
                                <!-- COLUNA 1: Permissões Atuais -->
                                <div class="column-flexible">
                                    <div class="card card-border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-key me-2"></i>
                                                Permissões Atuais ({{ permissoesAtuaisFiltradas.length }})
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Filtro para Permissões Atuais -->
                                            <div class="mb-3">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                    <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        placeholder="Filtrar permissões atuais..."
                                                        v-model="filtroPermissoesAtuais"
                                                    >
                                                </div>
                                            </div>
                                            
                                            <div v-if="permissoesAtuaisFiltradas.length === 0" class="text-center py-3">
                                                <i class="fas fa-key text-muted mb-2 icon-medium"></i>
                                                <p class="text-muted mb-0">Nenhuma permissão encontrada</p>
                                            </div>
                                            <div v-else>
                                                <div v-for="permissao in permissoesAtuaisFiltradas" :key="permissao.id" class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                                    <div>
                                                        <div class="fw-medium">{{ permissao.display_name }}</div>
                                                        <small class="text-muted">{{ permissao.description }}</small>
                                                    </div>
                                                    <button 
                                                        v-if="canManagePapeis"
                                                        class="btn btn-sm btn-danger" 
                                                        @click="removerPermissaoDoPapel(permissao)" 
                                                        title="Remover"
                                                    >
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- COLUNA 2: Adicionar Permissões -->
                                <div class="column-flexible">
                                    <div class="card card-border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-plus me-2"></i>
                                                Adicionar Permissões ({{ permissoesDisponiveisFiltradas.length }})
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Filtro para Permissões Disponíveis -->
                                            <div class="mb-3">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                    <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        placeholder="Filtrar permissões disponíveis..."
                                                        v-model="filtroPermissoesDisponiveis"
                                                    >
                                                </div>
                                            </div>
                                            
                                            <div v-if="permissoesDisponiveisFiltradas.length === 0" class="text-center py-3">
                                                <i class="fas fa-check-circle text-success mb-2 icon-medium"></i>
                                                <p class="text-muted mb-0">Nenhuma permissão disponível</p>
                                            </div>
                                            <div v-else>
                                                <div v-for="permissao in permissoesDisponiveisFiltradas" :key="permissao.id" class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                                    <div>
                                                        <div class="fw-medium">{{ permissao.display_name }}</div>
                                                        <small class="text-muted">{{ permissao.description }}</small>
                                                    </div>
                                                    <button 
                                                        v-if="canManagePapeis"
                                                        class="btn btn-sm btn-info" 
                                                        @click="adicionarPermissaoAoPapel(permissao)" 
                                                        title="Adicionar"
                                                    >
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rodapé do Modal -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="fecharModalPermissoes">
                            <i class="fas fa-times me-2"></i>Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Exclusão Inteligente -->
        <div class="modal fade" id="modalExclusaoInteligente" ref="modalExclusaoInteligente" tabindex="-1" aria-labelledby="modalExclusaoInteligenteLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Cabeçalho do Modal -->
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <h5 class="modal-title" id="modalExclusaoInteligenteLabel">
                                    Exclusão Inteligente de Papel
                                </h5>
                                <p class="mb-0 text-white-50">Confirme os detalhes antes de excluir</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Corpo do Modal -->
                    <div class="modal-body">
                        <div v-if="papelParaExcluir" class="alert alert-warning">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Atenção!
                            </h6>
                            <p class="mb-0">
                                Você está prestes a excluir o papel <strong>'{{ papelParaExcluir.display_name }}'</strong> 
                                que possui vínculos ativos no sistema.
                            </p>
                        </div>
                        
                        <!-- Detalhes dos Usuários Afetados -->
                        <div v-if="detalhesExclusao.usuarios.length > 0" class="mb-4">
                            <h6 class="text-danger mb-3">
                                <i class="fas fa-users me-2"></i>
                                Usuários Afetados ({{ detalhesExclusao.usuarios.length }})
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-admin table-admin-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="usuario in detalhesExclusao.usuarios" :key="usuario.id">
                                            <td>{{ usuario.name }}</td>
                                            <td>{{ usuario.email }}</td>
                                            <td>
                                                                                            <span class="badge" :class="usuario.is_active ? 'badge-success' : 'badge-danger'">
                                                {{ usuario.is_active ? 'ATIVO' : 'INATIVO' }}
                                            </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Detalhes das Permissões Afetadas -->
                        <div v-if="detalhesExclusao.permissoes.length > 0" class="mb-4">
                            <h6 class="text-danger mb-3">
                                <i class="fas fa-key me-2"></i>
                                Permissões Afetadas ({{ detalhesExclusao.permissoes.length }})
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-admin table-admin-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Permissão</th>
                                            <th>Nome Interno</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="permissao in detalhesExclusao.permissoes" :key="permissao.id">
                                            <td>{{ permissao.display_name }}</td>
                                            <td><code>{{ permissao.name }}</code></td>
                                            <td>
                                                                                            <span class="badge" :class="permissao.is_active ? 'badge-success' : 'badge-danger'">
                                                {{ permissao.is_active ? 'ATIVO' : 'INATIVO' }}
                                            </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Resumo da Ação -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                O que acontecerá:
                            </h6>
                            <ul class="mb-0">
                                <li v-if="detalhesExclusao.usuarios.length > 0">
                                    <strong>{{ detalhesExclusao.usuarios.length }} usuário(s)</strong> terão este papel removido automaticamente
                                </li>
                                <li v-if="detalhesExclusao.permissoes.length > 0">
                                    <strong>{{ detalhesExclusao.permissoes.length }} permissão(ões)</strong> serão desvinculadas deste papel
                                </li>
                                <li>O papel será <strong>excluído permanentemente</strong> do sistema</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Rodapé do Modal -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button 
                            type="button" 
                            class="btn btn-danger" 
                            @click="excluirPapelComVinculos"
                            :disabled="excluindoPapel"
                        >
                            <i class="fas fa-trash me-2" v-if="!excluindoPapel"></i>
                            <span class="spinner-border spinner-border-sm me-2" v-if="excluindoPapel" role="status"></span>
                            {{ excluindoPapel ? 'Excluindo...' : 'Excluir e Remover Vínculos' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Visualizar Detalhes da Permissão -->
        <div class="modal fade" id="modalVisualizarDetalhes" tabindex="-1" aria-labelledby="modalVisualizarDetalhesLabel" aria-hidden="true" ref="modalVisualizarDetalhes">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <!-- Cabeçalho do Modal -->
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="modal-title mb-0" id="modalVisualizarDetalhesLabel">
                                    Visualizar Detalhes da Permissão
                                </h5>
                                <p class="mb-0 text-white-50" v-if="permissaoSelecionada">
                                    {{ permissaoSelecionada?.display_name || 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Corpo do Modal -->
                    <div class="modal-body">
                        <div v-if="loadingDetalhesPermissao" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-2 text-muted">Carregando detalhes da permissão...</p>
                        </div>
                        
                        <div v-else-if="permissaoSelecionada">
                            <!-- Informações Básicas da Permissão -->
                            <div class="card card-border-primary mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Informações da Permissão
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nome da Permissão:</label>
                                                <p class="mb-0 text-custom">{{ permissaoSelecionada?.display_name || 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nome Interno:</label>
                                                <p class="mb-0"><code class="text-muted">{{ permissaoSelecionada?.name || 'N/A' }}</code></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Descrição:</label>
                                        <p class="mb-0 text-muted">{{ permissaoSelecionada?.description || 'Sem descrição' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Layout em Duas Colunas -->
                            <div class="layout-two-columns">
                                <!-- COLUNA 1: Papéis que Utilizam -->
                                <div class="column-flexible">
                                    <div class="card card-border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-users me-2"></i>
                                                Papéis que Utilizam ({{ papeisPermissao.length }})
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Filtro por Papel -->
                                            <div class="mb-3">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                    <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        placeholder="Filtrar por papel..."
                                                        v-model="filtroPapelPermissao"
                                                    >
                                                </div>
                                            </div>
                                            
                                            <div v-if="papeisPermissaoFiltrados.length === 0" class="text-center py-3">
                                                <i class="fas fa-user-tag text-muted mb-2 icon-medium"></i>
                                                <p class="text-muted mb-0">Nenhum papel encontrado</p>
                                            </div>
                                            <div v-else>
                                                <div v-for="papel in papeisPermissaoFiltrados" :key="papel.id" class="d-flex align-items-center p-2 border-bottom">
                                                    <div class="avatar-admin me-2">{{ papel?.display_name?.charAt(0)?.toUpperCase() || '?' }}</div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-medium">{{ papel?.display_name || 'N/A' }}</div>
                                                        <small class="text-muted">{{ papel?.description || 'Sem descrição' }}</small>
                                                    </div>
                                                    <span class="badge" :class="papel?.is_active ? 'badge-success' : 'badge-danger'">
                                                        {{ papel?.is_active ? 'ATIVO' : 'INATIVO' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- COLUNA 2: Usuários Afetados -->
                                <div class="column-flexible">
                                    <div class="card card-border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-user-check me-2"></i>
                                                Usuários Afetados ({{ usuariosPermissao.length }})
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Filtro por Usuário -->
                                            <div class="mb-3">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                    <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        placeholder="Filtrar por usuário..."
                                                        v-model="filtroUsuarioPermissao"
                                                    >
                                                </div>
                                            </div>
                                            
                                            <div v-if="usuariosPermissaoFiltrados.length === 0" class="text-center py-3">
                                                <i class="fas fa-users text-muted mb-2 icon-medium"></i>
                                                <p class="text-muted mb-0">Nenhum usuário encontrado</p>
                                            </div>
                                            <div v-else>
                                                <div v-for="usuario in usuariosPermissaoPaginados" :key="usuario.id" class="d-flex align-items-center p-2 border-bottom">
                                                    <div class="avatar-admin me-2">{{ usuario?.name?.charAt(0)?.toUpperCase() || '?' }}</div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-medium">{{ usuario?.name || 'N/A' }}</div>
                                                        <small class="text-muted">{{ usuario?.email || 'N/A' }}</small>
                                                    </div>
                                                    <span class="badge" :class="usuario?.is_active ? 'badge-success' : 'badge-danger'">
                                                        {{ usuario?.is_active ? 'ATIVO' : 'INATIVO' }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Paginação para Usuários -->
                                                <div v-if="totalPaginasUsuarios > 1" class="mt-3">
                                                    <nav>
                                                        <ul class="pagination pagination-sm justify-content-center mb-0">
                                                            <li class="page-item" :class="{ disabled: paginaUsuarios === 1 }">
                                                                <button class="page-link" @click="mudarPaginaUsuarios(paginaUsuarios - 1)" :disabled="paginaUsuarios === 1">
                                                                    <i class="fas fa-chevron-left"></i>
                                                                </button>
                                                            </li>
                                                            <li v-for="pagina in paginasUsuariosVisiveis" :key="pagina" class="page-item" :class="{ active: pagina === paginaUsuarios }">
                                                                <button class="page-link" @click="mudarPaginaUsuarios(pagina)">{{ pagina }}</button>
                                                            </li>
                                                            <li class="page-item" :class="{ disabled: paginaUsuarios === totalPaginasUsuarios }">
                                                                <button class="page-link" @click="mudarPaginaUsuarios(paginaUsuarios + 1)" :disabled="paginaUsuarios === totalPaginasUsuarios">
                                                                    <i class="fas fa-chevron-right"></i>
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </nav>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Gerenciar Usuários do Papel - VERSÃO SIMPLIFICADA -->
        <div class="modal fade" id="modalGerenciarUsuarios" tabindex="-1" aria-labelledby="modalGerenciarUsuariosLabel" aria-hidden="true" ref="modalGerenciarUsuarios">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <!-- Cabeçalho do Modal -->
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="modal-title mb-0" id="modalGerenciarUsuariosLabel">
                                    Gerenciar Usuários do Papel
                                </h5>
                                <p class="mb-0 text-white-50" v-if="papelSelecionado">
                                    {{ papelSelecionado.display_name }}
                                </p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Corpo do Modal -->
                    <div class="modal-body">
                        <div v-if="loadingUsuariosPapel" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-2 text-muted">Carregando usuários...</p>
                        </div>
                        
                        <div v-else>
                            <!-- LAYOUT SIMPLES EM DUAS COLUNAS -->
                            <div class="layout-two-columns">
                                <!-- COLUNA 1: Usuários Atuais -->
                                <div class="column-flexible">
                                    <div class="card card-border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-user-check me-2"></i>
                                                Usuários Atuais ({{ usuariosAtuaisFiltrados.length }})
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Filtro para Usuários Atuais -->
                                            <div class="mb-3">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                    <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        placeholder="Filtrar usuários atuais..."
                                                        v-model="filtroUsuariosAtuais"
                                                    >
                                                </div>
                                            </div>
                                            
                                            <div v-if="usuariosAtuaisFiltrados.length === 0" class="text-center py-3">
                                                <i class="fas fa-users text-muted mb-2 icon-medium"></i>
                                                <p class="text-muted mb-0">Nenhum usuário encontrado</p>
                                            </div>
                                            <div v-else>
                                                <div v-for="usuario in usuariosAtuaisFiltrados" :key="usuario.id" class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-admin me-2">{{ usuario.name.charAt(0).toUpperCase() }}</div>
                                                        <div>
                                                            <div class="fw-medium">{{ usuario.name }}</div>
                                                            <small class="text-muted">{{ usuario.email }}</small>
                                                        </div>
                                                    </div>
                                                    <button 
                                                        v-if="canManagePapeis"
                                                        class="btn btn-sm btn-danger" 
                                                        @click="removerUsuarioDoPapel(usuario)" 
                                                        title="Remover"
                                                    >
                                                        <i class="fas fa-user-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- COLUNA 2: Adicionar Usuários -->
                                <div class="column-flexible">
                                    <div class="card card-border-success">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-user-plus me-2"></i>
                                                Adicionar Usuários ({{ usuariosDisponiveisFiltrados.length }})
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Filtro para Usuários Disponíveis -->
                                            <div class="mb-3">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                    <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        placeholder="Filtrar usuários disponíveis..."
                                                        v-model="filtroUsuariosDisponiveis"
                                                    >
                                                </div>
                                            </div>
                                            
                                            <div v-if="usuariosDisponiveisFiltrados.length === 0" class="text-center py-3">
                                                <i class="fas fa-check-circle text-success mb-2 icon-medium"></i>
                                                <p class="text-muted mb-0">Nenhum usuário disponível</p>
                                            </div>
                                            <div v-else>
                                                <div v-for="usuario in usuariosDisponiveisFiltrados" :key="usuario.id" class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-admin me-2">{{ usuario.name.charAt(0).toUpperCase() }}</div>
                                                        <div>
                                                            <div class="fw-medium">{{ usuario.name }}</div>
                                                            <small class="text-muted">{{ usuario.email }}</small>
                                                        </div>
                                                    </div>
                                                    <button 
                                                        v-if="canManagePapeis"
                                                        class="btn btn-sm btn-info" 
                                                        @click="adicionarUsuarioAoPapel(usuario)" 
                                                        title="Adicionar"
                                                    >
                                                        <i class="fas fa-user-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rodapé do Modal -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast para notificações -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i :class="toastIcon" class="me-2"></i>
                    <strong class="me-auto">{{ toastTitle }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ toastMessage }}
                </div>
            </div>
        </div>

        <!-- Modal Detalhes da Permissão -->
        <div class="modal fade" id="modalDetalhesPermissao" tabindex="-1" aria-labelledby="modalDetalhesPermissaoLabel" aria-hidden="true" ref="modalDetalhesPermissao">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Cabeçalho do Modal -->
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="modal-title mb-0" id="modalDetalhesPermissaoLabel">
                                    Detalhes da Permissão
                                </h5>
                                <p class="mb-0 text-white-50" v-if="permissaoSelecionada">
                                    {{ permissaoSelecionada.display_name }}
                                </p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Corpo do Modal -->
                    <div class="modal-body">
                        <div v-if="loadingDetalhesPermissao" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-2 text-muted">Carregando detalhes...</p>
                        </div>
                        
                        <div v-else-if="permissaoSelecionada">
                            <!-- Informações da Permissão -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações da Permissão</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Nome:</strong> {{ permissaoSelecionada.display_name }}</p>
                                            <p><strong>Nome Interno:</strong> <code>{{ permissaoSelecionada.name }}</code></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Status:</strong> 
                                                                                            <span class="badge" :class="permissaoSelecionada.is_active ? 'badge-success' : 'badge-danger'">
                                                {{ permissaoSelecionada.is_active ? 'ATIVO' : 'INATIVO' }}
                                            </span>
                                            </p>
                                            <p><strong>Descrição:</strong> {{ permissaoSelecionada.description }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Papéis que possuem esta permissão -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-user-tag me-2"></i>Papéis que possuem esta permissão ({{ papeisPermissao.length }})</h6>
                                </div>
                                <div class="card-body">
                                    <div v-if="papeisPermissao.length === 0" class="text-center py-3">
                                        <i class="fas fa-user-tag text-muted mb-2 icon-medium"></i>
                                        <p class="text-muted mb-0">Nenhum papel possui esta permissão</p>
                                    </div>
                                    <div v-else>
                                        <div v-for="papel in papeisPermissao" :key="papel.id" class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                            <div>
                                                <div class="fw-medium">{{ papel.display_name }}</div>
                                                <small class="text-muted">{{ papel.description }}</small>
                                            </div>
                                            <span class="badge" :class="papel.is_active ? 'badge-success' : 'badge-danger'">
                                                {{ papel.is_active ? 'ATIVO' : 'INATIVO' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Usuários que possuem esta permissão -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Usuários que possuem esta permissão ({{ usuariosPermissao.length }})</h6>
                                </div>
                                <div class="card-body">
                                    <div v-if="usuariosPermissao.length === 0" class="text-center py-3">
                                        <i class="fas fa-users text-muted mb-2 icon-medium"></i>
                                        <p class="text-muted mb-0">Nenhum usuário possui esta permissão</p>
                                    </div>
                                    <div v-else>
                                        <div v-for="usuario in usuariosPermissao" :key="usuario.id" class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-admin me-2">{{ usuario.name.charAt(0).toUpperCase() }}</div>
                                                <div>
                                                    <div class="fw-medium">{{ usuario.name }}</div>
                                                    <small class="text-muted">{{ usuario.email }}</small>
                                                </div>
                                            </div>
                                            <span class="badge" :class="usuario.is_active ? 'badge-success' : 'badge-danger'">
                                                {{ usuario.is_active ? 'ATIVO' : 'INATIVO' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Gerenciar Papéis da Permissão -->
        <div class="modal fade" id="modalGerenciarPapeisPermissao" tabindex="-1" aria-labelledby="modalGerenciarPapeisPermissaoLabel" aria-hidden="true" ref="modalGerenciarPapeisPermissao">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <!-- Cabeçalho do Modal -->
                    <div class="modal-header custom-modal-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="modal-title mb-0" id="modalGerenciarPapeisPermissaoLabel">
                                    Gerenciar Papéis da Permissão
                                </h5>
                                <p class="mb-0 text-white-50" v-if="permissaoSelecionada">
                                    {{ permissaoSelecionada.display_name }}
                                </p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Corpo do Modal -->
                    <div class="modal-body">
                        <div v-if="loadingPapeisPermissao" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-2 text-muted">Carregando papéis...</p>
                        </div>
                        
                        <div v-else>
                            <!-- LAYOUT SIMPLES EM DUAS COLUNAS -->
                            <div class="layout-two-columns">
                                <!-- COLUNA 1: Papéis Atuais -->
                                <div class="column-flexible">
                                    <div class="card card-border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-user-tag me-2"></i>
                                                Papéis Atuais ({{ papeisAtuaisPermissao.length }})
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div v-if="papeisAtuaisPermissao.length === 0" class="text-center py-3">
                                                <i class="fas fa-user-tag text-muted mb-2 icon-medium"></i>
                                                <p class="text-muted mb-0">Nenhum papel encontrado</p>
                                            </div>
                                            <div v-else>
                                                <div v-for="papel in papeisAtuaisPermissao" :key="papel.id" class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                                    <div>
                                                        <div class="fw-medium">{{ papel.display_name }}</div>
                                                        <small class="text-muted">{{ papel.description }}</small>
                                                    </div>
                                                    <button class="btn btn-sm btn-danger" @click="removerPapelDaPermissao(papel)" title="Remover">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- COLUNA 2: Adicionar Papéis -->
                                <div class="column-flexible">
                                    <div class="card card-border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-plus me-2"></i>
                                                Adicionar Papéis ({{ papeisDisponiveisPermissao.length }})
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div v-if="papeisDisponiveisPermissao.length === 0" class="text-center py-3">
                                                <i class="fas fa-check-circle text-success mb-2 icon-medium"></i>
                                                <p class="text-muted mb-0">Nenhum papel disponível</p>
                                            </div>
                                            <div v-else>
                                                <div v-for="papel in papeisDisponiveisPermissao" :key="papel.id" class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                                    <div>
                                                        <div class="fw-medium">{{ papel.display_name }}</div>
                                                        <small class="text-muted">{{ papel.description }}</small>
                                                    </div>
                                                    <button class="btn btn-sm btn-info" @click="adicionarPapelAPermissao(papel)" title="Adicionar">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade modal-confirmacao" id="modalConfirmacaoExclusao" tabindex="-1" ref="modalConfirmacaoRef">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex align-items-center">
                        <div class="header-icon" aria-hidden="true">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h5 class="modal-title mb-0">
                            Confirmar Exclusão
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="confirm-text mb-1">Tem certeza que deseja excluir o usuário</p>
                    <p class="target-entity fs-5 mb-3">
                        <span id="nomeUsuario">"{{ itemParaExcluir?.name }}"</span>
                    </p>

                    <!-- Caixa de Aviso -->
                    <div class="irreversible mb-1" role="status" aria-live="polite">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span>Esta ação é permanente e não poderá ser desfeita.</span>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" @click="confirmarExclusao" :disabled="excluindo">
                        <span v-if="excluindo" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        <i v-else class="fas fa-trash me-2"></i>
                        {{ excluindo ? 'Excluindo...' : 'Excluir' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- fechamento do container principal da página -->
    </div>
</template>

<script>
import BuscaGlobal from './BuscaGlobal.vue';

export default {
    name: 'ListaUsuarios',
    components: {
        BuscaGlobal
    },
    data() {
        return {
            activeTab: 'usuarios',
            loading: false,
            modoEdicao: false,
            itemSelecionado: null,
            modalUsuario: null,
            salvando: false,
            toast: null,
            toastTitle: '',
            toastMessage: '',
            toastIcon: '',
            
            // Dados do usuário logado
            currentUser: null,
            
            // Paginação
            paginaAtual: 1,
            itensPorPagina: 10,
            
            // Filtros por aba
            filtrosUsuariosVisiveis: false,
            
            // Filtros de usuários
            filtrosUsuarios: {
                nome: '',
                email: '',
                status: '',
                tipo: ''
            },
            
            // Formulário de usuário
            formUsuario: {
                nome: '',
                email: '',
                login_type: 'local',
                is_active: true,
                password: '',
                password_confirmation: ''
            },
            
            errors: {},
            
            // Dados de usuários (serão carregados da API)
            usuarios: [],
            
            // Dados de papéis (serão carregados da API)
            papeis: [],
            loadingPapeis: false,
            modoEdicaoPapel: false,
            modalPapel: null,
            salvandoPapel: false,
            
            // Filtros por aba
            filtrosPapeisVisiveis: false,
            
            // Filtros de papéis
            filtrosPapeis: {
                nome: '',
                nome_interno: '',
                is_active: ''
            },
            
            // Formulário de papel
            formPapel: {
                display_name: '',
                name: '',
                description: '',
                is_active: true
            },
            
            errorsPapel: {},
            
            // Array de papéis (será carregado da API)
            papeis: [],
            
            // ===== MODAL DE CONFIRMAÇÃO =====
            itemParaExcluir: null,
            excluindo: false,
            tipoExclusao: '', // 'usuario', 'papel', 'permissao', 'usuarioPapel'
            mensagemConfirmacao: '',
            
            // ===== DADOS PARA PERMISSÕES =====
            
            // Dados de permissões (serão carregados da API)
            permissoes: [],
            loadingPermissoes: false,
            
            // Filtros por aba
            filtrosPermissoesVisiveis: false,
            
            // Filtros de permissões
            filtrosPermissoes: {
                nome: '',
                nome_interno: '',
                is_active: ''
            },
            
            // Dados para CRUD de permissões
            modoEdicaoPermissao: false,
            modalPermissao: null,
            salvandoPermissao: false,
            
            // Formulário de permissão
            formPermissao: {
                display_name: '',
                name: '',
                description: '',
                is_active: true
            },
            
            errorsPermissao: {},

            // Dados para gerenciar permissões dos papéis
            modalGerenciarPermissoes: null,
            loadingPermissoesPapel: false,
            salvandoPermissoesPapel: false,
            todasPermissoes: [],
            permissoesSelecionadas: [],
            
            // Dados para exclusão inteligente de papéis
            modalExclusaoInteligente: null,
            papelParaExcluir: null,
            detalhesExclusao: {
                usuarios: [],
                permissoes: []
            },
            excluindoPapel: false,
            
            // Dados para gerenciar usuários dos papéis
            modalGerenciarUsuarios: null,
            modalVisualizarDetalhes: null,
            papelSelecionado: null,
            loadingUsuariosPapel: false,
            
            // Dados para visualizar detalhes da permissão
            permissaoSelecionada: null,
            loadingDetalhesPermissao: false,
            papeisPermissao: [],
            usuariosPermissao: [],
            filtroPapelPermissao: '',
            filtroUsuarioPermissao: '',
            paginaUsuarios: 1,
            itensPorPaginaUsuarios: 20,
            salvandoUsuariosPapel: false,
            usuariosAtuais: [],
            usuariosDisponiveis: [],
            todosUsuarios: [],
            filtroUsuarios: {
                nome: '',
                email: ''
            },
            filtrosUsuariosAtuaisVisiveis: false,
            filtrosUsuariosDisponiveisVisiveis: false,
            
            // Dados para funcionalidades da aba Permissões
            modalDetalhesPermissao: null,
            modalGerenciarPapeisPermissao: null,
            permissaoSelecionada: null,
            loadingDetalhesPermissao: false,
            loadingPapeisPermissao: false,
            papeisPermissao: [],
            usuariosPermissao: [],
            papeisAtuaisPermissao: [],
            papeisDisponiveisPermissao: [],
            
            // Dados para gerenciar permissões dos papéis
            permissoesAtuais: [],
            permissoesDisponiveis: [],
            
            // Filtros para modal Gerenciar Usuários
            filtroUsuariosAtuais: '',
            filtroUsuariosDisponiveis: '',
            
            // Filtros para modal Gerenciar Permissões
            filtroPermissoesAtuais: '',
            filtroPermissoesDisponiveis: ''
        }
    },
    

    
    mounted() {
        this.modalUsuario = new bootstrap.Modal(this.$refs.modalUsuario);
        this.modalPapel = new bootstrap.Modal(this.$refs.modalPapel);
        this.modalPermissao = new bootstrap.Modal(this.$refs.modalPermissao);
        this.modalGerenciarPermissoes = new bootstrap.Modal(this.$refs.modalGerenciarPermissoes);
                    this.modalGerenciarUsuarios = new bootstrap.Modal(this.$refs.modalGerenciarUsuarios);
            this.modalVisualizarDetalhes = new bootstrap.Modal(this.$refs.modalVisualizarDetalhes);
        
        // Inicializar toast
        this.toast = new bootstrap.Toast(document.getElementById('toast'));
        
        // Carregar dados do usuário logado
        this.carregarDadosUsuario();
        
        // Carregar dados iniciais
        this.carregarUsuarios();
        this.carregarPapeis();
        this.carregarPermissoes();
    },
    
    computed: {
        // Verifica se o usuário é super admin
        isSuper() {
            return this.currentUser && this.currentUser.roles && 
                   this.currentUser.roles.some(role => role.name === 'super');
        },
        
        // Verifica se o usuário pode executar ações (CRUD) no módulo atual
        canPerformActions() {
            // Se ainda não carregou o usuário, permite temporariamente
            if (!this.currentUser) return true;
            
            if (this.isSuper) return true;
            
            if (!this.currentUser.roles) return false;
            
            // Verifica se tem qualquer permissão do módulo atual
            const permissions = this.currentUser.roles.flatMap(role => role.permissions || []);
            const permissionNames = permissions.map(p => p.name);
            
            switch (this.activeTab) {
                case 'usuarios':
                    return permissionNames.some(p => p.startsWith('usuario_'));
                case 'papeis':
                    return permissionNames.some(p => p.startsWith('papel_'));
                case 'permissoes':
                    return permissionNames.some(p => p.startsWith('permissao_'));
                default:
                    return false;
            }
        },
        
        // Verifica se o usuário pode visualizar o módulo atual
        canViewModule() {
            // Se ainda não carregou o usuário, permite temporariamente
            if (!this.currentUser) return true;
            
            if (this.isSuper) return true;
            
            if (!this.currentUser.roles) return false;
            
            // Verifica se tem qualquer permissão do módulo atual
            const permissions = this.currentUser.roles.flatMap(role => role.permissions || []);
            const permissionNames = permissions.map(p => p.name);
            
            switch (this.activeTab) {
                case 'usuarios':
                    return permissionNames.some(p => p.startsWith('usuario_'));
                case 'papeis':
                    return permissionNames.some(p => p.startsWith('papel_'));
                case 'permissoes':
                    return permissionNames.some(p => p.startsWith('permissao_'));
                default:
                    return false;
            }
        },
        
        // Verifica se o usuário pode apenas visualizar (não pode executar ações)
        canOnlyView() {
            return this.canViewModule && !this.canPerformActions;
        },
        
        // Verifica se o usuário pode gerenciar papéis (CRUD completo)
        canManagePapeis() {
            if (!this.currentUser) return true;
            if (this.isSuper) return true;
            
            const permissions = this.currentUser.roles.flatMap(role => role.permissions || []);
            const permissionNames = permissions.map(p => p.name);
            
            return permissionNames.includes('papel_crud');
        },
        
        // Verifica se o usuário pode apenas consultar papéis
        canViewPapeis() {
            if (!this.currentUser) return true;
            if (this.isSuper) return true;
            
            const permissions = this.currentUser.roles.flatMap(role => role.permissions || []);
            const permissionNames = permissions.map(p => p.name);
            
            return permissionNames.includes('papel_consultar');
        },
        
        // Dados filtrados por aba
        dadosFiltrados() {
            switch (this.activeTab) {
                case 'usuarios':
                    return this.filtrarUsuariosComputed();
                case 'papeis':
                    return this.filtrarPapeisComputed();
                case 'permissoes':
                    return this.filtrarPermissoesComputed();
                case 'busca':
                    return this.$refs.buscaGlobal ? this.$refs.buscaGlobal.resultadosAgrupados : [];
                default:
                    return [];
            }
        },
        
        // Dados filtrados de papéis
        papeisFiltrados() {
            return this.filtrarPapeisComputed();
        },
        
        // Dados paginados de papéis
        papeisPaginados() {
            const inicio = (this.paginaAtual - 1) * this.itensPorPagina;
            const fim = inicio + this.itensPorPagina;
            return this.papeisFiltrados.slice(inicio, fim);
        },
        
        // Dados filtrados de permissões
        permissoesFiltradas() {
            return this.filtrarPermissoesComputed();
        },
        
        // Dados paginados de permissões
        permissoesPaginadas() {
            const inicio = (this.paginaAtual - 1) * this.itensPorPagina;
            const fim = inicio + this.itensPorPagina;
            return this.permissoesFiltradas.slice(inicio, fim);
        },
        
        // Dados paginados
        dadosPaginados() {
            const inicio = (this.paginaAtual - 1) * this.itensPorPagina;
            const fim = inicio + this.itensPorPagina;
            return this.dadosFiltrados.slice(inicio, fim);
        },
        
        // Total de páginas
        totalPaginas() {
            return Math.ceil(this.dadosFiltrados.length / this.itensPorPagina);
        },
        
        // Páginas visíveis para navegação
        paginasVisiveis() {
            const paginas = [];
            const total = this.totalPaginas;
            const atual = this.paginaAtual;
            
            if (total <= 7) {
                for (let i = 1; i <= total; i++) {
                    paginas.push(i);
                }
            } else {
                paginas.push(1);
                
                if (atual > 4) {
                    paginas.push('...');
                }
                
                const inicio = Math.max(2, atual - 1);
                const fim = Math.min(total - 1, atual + 1);
                
                for (let i = inicio; i <= fim; i++) {
                    if (!paginas.includes(i)) {
                        paginas.push(i);
                    }
                }
                
                if (atual < total - 3) {
                    paginas.push('...');
                }
                
                if (total > 1) {
                    paginas.push(total);
                }
            }
            
            return paginas;
        },
        

        
        // Papéis disponíveis para seleção
        papeisDisponiveis() {
            return this.papeis.filter(papel => papel.is_active);
        },


        
        // Computed properties para gerenciar usuários dos papéis
        totalUsuarios() {
            return this.todosUsuarios.length;
        },
        
        // Usuários atuais filtrados
        usuariosAtuaisFiltrados() {
            if (!this.filtroUsuariosAtuais) {
                return this.usuariosAtuais;
            }
            
            const filtro = this.filtroUsuariosAtuais.toLowerCase();
            return this.usuariosAtuais.filter(usuario => 
                usuario.name.toLowerCase().includes(filtro) ||
                usuario.email.toLowerCase().includes(filtro)
            );
        },
        
        // Usuários disponíveis filtrados
        usuariosDisponiveisFiltrados() {
            if (!this.filtroUsuariosDisponiveis) {
                return this.usuariosDisponiveis;
            }
            
            const filtro = this.filtroUsuariosDisponiveis.toLowerCase();
            return this.usuariosDisponiveis.filter(usuario => 
                usuario.name.toLowerCase().includes(filtro) ||
                usuario.email.toLowerCase().includes(filtro)
            );
        },
        
        // Permissões atuais filtradas
        permissoesAtuaisFiltradas() {
            if (!this.filtroPermissoesAtuais) {
                return this.permissoesAtuais || [];
            }
            
            const filtro = this.filtroPermissoesAtuais.toLowerCase();
            return (this.permissoesAtuais || []).filter(permissao => 
                permissao.display_name.toLowerCase().includes(filtro) ||
                permissao.description.toLowerCase().includes(filtro)
            );
        },
        
        // Permissões disponíveis filtradas
        permissoesDisponiveisFiltradas() {
            if (!this.filtroPermissoesDisponiveis) {
                return this.permissoesDisponiveis || [];
            }
            
            const filtro = this.filtroPermissoesDisponiveis.toLowerCase();
            return (this.permissoesDisponiveis || []).filter(permissao => 
                permissao.display_name.toLowerCase().includes(filtro) ||
                permissao.description.toLowerCase().includes(filtro)
            );
        },
        
        // Filtros para detalhes da permissão
        papeisPermissaoFiltrados() {
            if (!this.filtroPapelPermissao) return this.papeisPermissao;
            const filtro = this.filtroPapelPermissao.toLowerCase();
            return this.papeisPermissao.filter(papel => 
                papel.display_name.toLowerCase().includes(filtro) ||
                (papel.description && papel.description.toLowerCase().includes(filtro))
            );
        },
        
        usuariosPermissaoFiltrados() {
            if (!this.filtroUsuarioPermissao) return this.usuariosPermissao;
            const filtro = this.filtroUsuarioPermissao.toLowerCase();
            return this.usuariosPermissao.filter(usuario => 
                usuario.name.toLowerCase().includes(filtro) ||
                usuario.email.toLowerCase().includes(filtro)
            );
        },
        
        usuariosPermissaoPaginados() {
            const inicio = (this.paginaUsuarios - 1) * this.itensPorPaginaUsuarios;
            const fim = inicio + this.itensPorPaginaUsuarios;
            return this.usuariosPermissaoFiltrados.slice(inicio, fim);
        },
        
        totalPaginasUsuarios() {
            return Math.ceil(this.usuariosPermissaoFiltrados.length / this.itensPorPaginaUsuarios);
        },
        
        paginasUsuariosVisiveis() {
            const total = this.totalPaginasUsuarios;
            const atual = this.paginaUsuarios;
            const paginas = [];
            
            if (total <= 7) {
                for (let i = 1; i <= total; i++) {
                    paginas.push(i);
                }
            } else {
                if (atual <= 4) {
                    for (let i = 1; i <= 5; i++) {
                        paginas.push(i);
                    }
                    paginas.push('...');
                    paginas.push(total);
                } else if (atual >= total - 3) {
                    paginas.push(1);
                    paginas.push('...');
                    for (let i = total - 4; i <= total; i++) {
                        paginas.push(i);
                    }
                } else {
                    paginas.push(1);
                    paginas.push('...');
                    for (let i = atual - 1; i <= atual + 1; i++) {
                        paginas.push(i);
                    }
                    paginas.push('...');
                    paginas.push(total);
                }
            }
            
            return paginas;
        }
    },
    
    methods: {
        changeTab(tabName) {
            this.activeTab = tabName;
            this.paginaAtual = 1;
        },
        
        // Métodos de toggle dos filtros
        toggleFiltrosUsuarios() {
            this.filtrosUsuariosVisiveis = !this.filtrosUsuariosVisiveis;
        },
        
        // Métodos para abrir modais
        abrirModalCriarUsuario() {
            this.modoEdicao = false;
            this.limparFormularioUsuario();
            this.errors = {};
            this.modalUsuario.show();
        },
        
        editarUsuario(usuario) {
            this.modoEdicao = true;
            this.itemSelecionado = usuario;
            this.carregarUsuarioParaEdicao(usuario);
            this.errors = {};
            this.modalUsuario.show();
        },
        
        carregarUsuarioParaEdicao(usuario) {
            this.formUsuario = {
                nome: usuario.name,
                email: usuario.email,
                login_type: usuario.login_type,
                is_active: usuario.is_active,
                password: '',
                password_confirmation: ''
            };
        },
        
        limparFormularioUsuario() {
            this.formUsuario = {
                nome: '',
                email: '',
                login_type: 'local',
                is_active: true,
                password: '',
                password_confirmation: ''
            };
            this.errors = {};
        },
        
        async salvarUsuario() {
            this.salvando = true;
            this.errors = {};
            
            try {
                const url = this.modoEdicao 
                    ? `/api/administracao/usuarios/${this.itemSelecionado.id}` 
                    : '/api/administracao/usuarios';
                
                const method = this.modoEdicao ? 'PUT' : 'POST';
                
                // Preparar dados para envio
                const dados = {
                    name: this.formUsuario.nome,
                    email: this.formUsuario.email,
                    login_type: 'local', // Sempre local para usuários criados manualmente
                    is_active: this.formUsuario.is_active
                };
                
                // Adicionar senha apenas se fornecida (edição) ou obrigatória (criação)
                if (!this.modoEdicao || this.formUsuario.password) {
                    dados.password = this.formUsuario.password;
                    dados.password_confirmation = this.formUsuario.password_confirmation;
                }
                
                const response = await axios({
                    method: method,
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: dados
                });
                
                this.modalUsuario.hide();
                this.mostrarToast('Sucesso', 'Usuário salvo com sucesso!', 'fa-check-circle text-success');
                this.carregarUsuarios();
                this.carregarPapeis();
                
            } catch (error) {
                if (error.response && error.response.status === 422 && error.response.data.errors) {
                    this.errors = error.response.data.errors;
                    console.log('Erros de validação:', this.errors);
                    
                    // focar no primeiro campo inválido
                    this.$nextTick(() => {
                        if (this.errors.name) {
                            document.getElementById('nomeUsuario')?.focus();
                        } else if (this.errors.email) {
                            document.getElementById('emailUsuario')?.focus();
                        } else if (this.errors.password) {
                            document.getElementById('senhaUsuario')?.focus();
                        } else if (this.errors.password_confirmation) {
                            document.getElementById('confirmarSenha')?.focus();
                        }
                    });
                } else {
                    const mensagem = error.response?.data?.message || error.message || 'Erro ao salvar usuário';
                    console.error('Erro completo:', error);
                    this.mostrarToast('Erro', mensagem, 'fa-exclamation-circle text-danger');
                }
            } finally {
                this.salvando = false;
            }
        },
        
        async excluirUsuario(usuario) {
            this.itemParaExcluir = usuario;
            this.tipoExclusao = 'usuario';
            
            const modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacaoExclusao'));
            modalConfirmacao.show();
        },
        
        async carregarUsuarios() {
            this.loading = true;
            try {
                const response = await axios.get('/api/administracao/usuarios');
                
                // A API agora retorna diretamente o array de usuários
                this.usuarios = response.data || [];
                
                // console.log('Usuários carregados:', this.usuarios.length);
            } catch (error) {
                console.error('Erro ao carregar usuários:', error);
                this.mostrarToast('Erro', 'Erro ao carregar usuários: ' + (error.response?.data?.message || error.message), 'fa-exclamation-circle text-danger');
                // Em caso de erro, usar dados mockados para teste
                this.usuarios = [
                    {
                        id: 1,
                        name: 'João Silva',
                        email: 'joao.silva@example.com',
                        roles: [
                            { 
                                id: 1, 
                                name: 'admin', 
                                display_name: 'Administrador',
                                permissions: [
                                    { id: 1, name: 'gerenciar_usuarios', display_name: 'Gerenciar Usuários' },
                                    { id: 2, name: 'gerenciar_papeis', display_name: 'Gerenciar Papéis' }
                                ]
                            }
                        ],
                        is_active: true,
                        login_type: 'local'
                    },
                    {
                        id: 2,
                        name: 'Maria Santos',
                        email: 'maria.santos@example.com',
                        roles: [
                            { 
                                id: 2, 
                                name: 'user', 
                                display_name: 'Usuário',
                                permissions: [
                                    { id: 3, name: 'visualizar_relatorios', display_name: 'Visualizar Relatórios' }
                                ]
                            }
                        ],
                        is_active: true,
                        login_type: 'ad'
                    }
                ];
            } finally {
                this.loading = false;
            }
        },
        
        // Métodos de filtro para usuários
        filtrarUsuarios() {
            this.paginaAtual = 1;
        },
        
        limparFiltrosUsuarios() {
            this.filtrosUsuarios = {
                nome: '',
                email: '',
                status: '',
                tipo: ''
            };
            this.paginaAtual = 1;
        },
        
        filtrarUsuariosComputed() {
            return this.usuarios.filter(usuario => {
                const nomeMatch = !this.filtrosUsuarios.nome || 
                    usuario.name.toLowerCase().includes(this.filtrosUsuarios.nome.toLowerCase());
                
                const emailMatch = !this.filtrosUsuarios.email || 
                    usuario.email.toLowerCase().includes(this.filtrosUsuarios.email.toLowerCase());
                
                const statusMatch = !this.filtrosUsuarios.status || 
                    (this.filtrosUsuarios.status === 'ativo' && usuario.is_active) ||
                    (this.filtrosUsuarios.status === 'inativo' && !usuario.is_active);
                
                const tipoMatch = !this.filtrosUsuarios.tipo || 
                    usuario.login_type === this.filtrosUsuarios.tipo;
                
                return nomeMatch && emailMatch && statusMatch && tipoMatch;
            });
        },
        
        // Métodos de paginação
        mudarPagina(pagina) {
            if (pagina >= 1 && pagina <= this.totalPaginas) {
                this.paginaAtual = pagina;
            }
        },
        

        

        
        // Métodos placeholder para outras funcionalidades
        gerenciarPapeis(usuario) {
            this.mostrarToast('Info', 'Funcionalidade de gerenciar papéis será implementada', 'fa-users-cog text-info');
        },

        // ===== MÉTODOS PARA GERENCIAR PERMISSÕES DOS PAPÉIS =====
        
        // Abrir modal para gerenciar permissões de um papel
        abrirModalGerenciarPermissoes(papel) {
            this.itemSelecionado = papel;
            this.carregarPermissoesDoPapel(papel);
            this.modalGerenciarPermissoes.show();
        },

        // Carregar permissões do papel (VERSÃO PADRONIZADA)
        async carregarPermissoesDoPapel(papel) {
            try {
                this.loadingPermissoesPapel = true;
                
                // Carregar permissões atuais do papel
                const responsePermissoesAtuais = await axios.get(`/api/administracao/papeis/${papel.id}/permissions`);
                this.permissoesAtuais = responsePermissoesAtuais.data || [];
                
                // Carregar todas as permissões disponíveis
                const responseTodasPermissoes = await axios.get('/api/administracao/permissoes');
                const todasPermissoes = responseTodasPermissoes.data || [];
                
                // Filtrar permissões disponíveis (que não estão no papel)
                const idsPermissoesAtuais = this.permissoesAtuais.map(p => p.id);
                this.permissoesDisponiveis = todasPermissoes.filter(p => !idsPermissoesAtuais.includes(p.id));
                
                // Limpar filtros
                this.filtroPermissoesAtuais = '';
                this.filtroPermissoesDisponiveis = '';
                
            } catch (error) {
                console.error('Erro ao carregar permissões do papel:', error);
                this.mostrarToast('Erro', 'Erro ao carregar permissões: ' + (error.response?.data?.message || error.message), 'fa-exclamation-circle text-danger');
            } finally {
                this.loadingPermissoesPapel = false;
            }
        },



        // Adicionar permissão ao papel
        async adicionarPermissaoAoPapel(permissao) {
            try {
                this.salvandoPermissoesPapel = true;
                
                const papelId = this.itemSelecionado.id;
                const permissaoId = permissao.id;
                
                // Obter todas as permissões atuais + a nova permissão
                const permissoesAtuais = this.permissoesAtuais.map(p => p.id);
                const todasPermissoes = [...permissoesAtuais, permissaoId];
                
                // Atualizar permissões do papel via API (POST com array completo)
                await axios.post(`/api/administracao/papeis/${papelId}/permissions`, {
                    permissions: todasPermissoes
                });

                // Atualizar listas locais
                this.permissoesAtuais.push(permissao);
                this.permissoesDisponiveis = this.permissoesDisponiveis.filter(p => p.id !== permissaoId);
                
                this.mostrarToast('Sucesso', `Permissão '${permissao.display_name}' adicionada ao papel com sucesso!`, 'fa-check-circle text-success');
                
                // Recarregar papéis para atualizar os contadores
                this.carregarPapeis();
                
            } catch (error) {
                console.error('Erro ao adicionar permissão ao papel:', error);
                this.mostrarToast('Erro', 'Erro ao adicionar permissão ao papel: ' + (error.response?.data?.message || error.message), 'fa-exclamation-circle text-danger');
            } finally {
                this.salvandoPermissoesPapel = false;
            }
        },

        // Remover permissão do papel
        async removerPermissaoDoPapel(permissao) {
            this.itemParaExcluir = { permissao, papel: this.itemSelecionado };
            this.tipoExclusao = 'permissaoPapel';
            this.mensagemConfirmacao = `Tem certeza que deseja remover a permissão <strong>"${permissao.display_name}"</strong> do papel <strong>"${this.itemSelecionado.display_name}"</strong>?`;
            
            const modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacaoExclusao'));
            modalConfirmacao.show();
        },
        
        // Fechar modal de permissões e limpar filtros
        fecharModalPermissoes() {
            this.modalGerenciarPermissoes.hide();
            this.filtroPermissoesAtuais = '';
            this.filtroPermissoesDisponiveis = '';
        },
        

        
        // ===== MÉTODOS PARA GERENCIAR USUÁRIOS DOS PAPÉIS =====
        
        // Abrir modal para gerenciar usuários de um papel
        async abrirModalGerenciarUsuarios(papel) {
            this.papelSelecionado = papel;
            this.loadingUsuariosPapel = true;
            
            // Limpar filtros
            this.filtroUsuariosAtuais = '';
            this.filtroUsuariosDisponiveis = '';
            
            try {
                // Carregar usuários atuais do papel
                const responseAtuais = await axios.get(`/api/administracao/papeis/${papel.id}/users`);
                this.usuariosAtuais = responseAtuais.data;
                
                // Carregar todos os usuários para a lista de disponíveis
                const responseTodos = await axios.get('/api/administracao/usuarios');
                this.todosUsuarios = responseTodos.data;
                
                // Filtrar usuários disponíveis (não estão no papel)
                const idsUsuariosAtuais = this.usuariosAtuais.map(u => u.id);
                this.usuariosDisponiveis = this.todosUsuarios.filter(usuario => 
                    !idsUsuariosAtuais.includes(usuario.id)
                );
                
                this.modalGerenciarUsuarios.show();
                
            } catch (error) {
                this.mostrarToast('Erro', 'Erro ao carregar usuários do papel', 'fa-exclamation-circle text-danger');
            } finally {
                this.loadingUsuariosPapel = false;
            }
        },

        // Filtrar usuários no modal
        filtrarUsuariosModal() {
            // Os filtros são aplicados automaticamente pelas computed properties
            // Este método pode ser usado para lógica adicional se necessário
        },

        // Toggle filtros de usuários atuais
        toggleFiltrosUsuariosAtuais() {
            this.filtrosUsuariosAtuaisVisiveis = !this.filtrosUsuariosAtuaisVisiveis;
        },

        // Toggle filtros de usuários disponíveis
        toggleFiltrosUsuariosDisponiveis() {
            this.filtrosUsuariosDisponiveisVisiveis = !this.filtrosUsuariosDisponiveisVisiveis;
        },

        // Adicionar usuário ao papel
        async adicionarUsuarioAoPapel(usuario) {
            try {
                this.salvandoUsuariosPapel = true;
                
                const papelId = this.papelSelecionado.id;
                const usuarioId = usuario.id;
                
                // Adicionar usuário ao papel via API
                await axios.post(`/api/administracao/papeis/${papelId}/users`, {
                    user_id: usuarioId
                });

                // Atualizar listas locais
                this.usuariosAtuais.push(usuario);
                this.usuariosDisponiveis = this.usuariosDisponiveis.filter(u => u.id !== usuarioId);
                
                this.mostrarToast('Sucesso', `Usuário '${usuario.name}' adicionado ao papel com sucesso!`, 'fa-check-circle text-success');
                
                // Recarregar papéis para atualizar os contadores
                this.carregarPapeis();
                
                // Recarregar usuários para sincronizar dados entre abas
                this.carregarUsuarios();
                
            } catch (error) {
                console.error('Erro ao adicionar usuário ao papel:', error);
                this.mostrarToast('Erro', 'Erro ao adicionar usuário ao papel: ' + (error.response?.data?.message || error.message), 'fa-exclamation-circle text-danger');
            } finally {
                this.salvandoUsuariosPapel = false;
            }
        },

        // Remover usuário do papel
        async removerUsuarioDoPapel(usuario) {
            this.itemParaExcluir = { usuario, papel: this.papelSelecionado };
            this.tipoExclusao = 'usuarioPapel';
            this.mensagemConfirmacao = `Tem certeza que deseja remover o usuário <strong>"${usuario.name}"</strong> do papel <strong>"${this.papelSelecionado.display_name}"</strong>?`;
            
            const modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacaoExclusao'));
            modalConfirmacao.show();
        },
        
        // ===== MÉTODOS PARA PAPÉIS =====
        
        // Toggle filtros de papéis
        toggleFiltrosPapeis() {
            this.filtrosPapeisVisiveis = !this.filtrosPapeisVisiveis;
        },
        
        // ===== MÉTODOS PARA VISUALIZAR DETALHES DA PERMISSÃO =====
        
        async abrirModalVisualizarDetalhes(permissao) {
            try {
                this.permissaoSelecionada = permissao;
                this.loadingDetalhesPermissao = true;
                this.filtroPapelPermissao = '';
                this.filtroUsuarioPermissao = '';
                this.paginaUsuarios = 1;
                
                // Carregar papéis que utilizam esta permissão
                const responsePapeis = await axios.get(`/api/administracao/permissoes/${permissao.id}/roles`);
                this.papeisPermissao = responsePapeis.data.roles || [];
                
                // Carregar usuários afetados por esta permissão
                const responseUsuarios = await axios.get(`/api/administracao/permissoes/${permissao.id}/users`);
                this.usuariosPermissao = responseUsuarios.data.users || [];
                
                this.loadingDetalhesPermissao = false;
                this.modalVisualizarDetalhes.show();
                
            } catch (error) {
                console.error('Erro ao carregar detalhes da permissão:', error);
                this.loadingDetalhesPermissao = false;
                this.mostrarToast('Erro', 'Erro ao carregar detalhes da permissão', 'error');
            }
        },
        
        mudarPaginaUsuarios(pagina) {
            if (pagina >= 1 && pagina <= this.totalPaginasUsuarios) {
                this.paginaUsuarios = pagina;
            }
        },
        
        // Abrir modal para criar papel
        abrirModalCriarPapel() {
            this.modoEdicaoPapel = false;
            this.limparFormularioPapel();
            this.modalPapel.show();
        },
        
        // Editar papel
        editarPapel(papel) {
            this.modoEdicaoPapel = true;
            this.itemSelecionado = papel;
            this.carregarPapelParaEdicao(papel);
            this.modalPapel.show();
        },
        
        // Excluir papel
        async excluirPapel(papel) {
            if (!confirm(`Tem certeza que deseja excluir o papel "${papel.display_name}"?`)) {
                return;
            }
            
            try {
                const response = await axios.delete(`/api/administracao/papeis/${papel.id}`);
                console.log('Resposta da exclusão:', response.data);
                
                this.mostrarToast('Sucesso', `Papel '${papel.display_name}' excluído com sucesso!`, 'fa-check-circle text-success');
                await this.carregarPapeis();
            } catch (error) {
                console.error('Erro ao excluir papel:', error);
                const mensagem = error.response?.data?.message || 'Erro ao excluir papel';
                this.mostrarToast('Erro', mensagem, 'fa-exclamation-circle text-danger');
            }
        },
        
        // Handler para exclusão de papel
        async handleExcluirPapel(papel) {
            console.log('Tentando excluir papel:', papel.display_name);
            console.log('Contadores - Usuários:', papel.users_count, 'Permissões:', papel.permissions_count);
            
            // Verificar se é o papel "Super Administrador"
            if (papel.name === 'super') {
                this.mostrarToast('Aviso', 'O papel "Super Administrador" não pode ser excluído', 'fa-exclamation-triangle text-warning');
                return;
            }
            
            // Verificar se tem vínculos
            if (papel.users_count > 0 || papel.permissions_count > 0) {
                this.abrirModalExclusaoInteligente(papel);
            } else {
                await this.excluirPapel(papel);
            }
        },
        
        // Abrir modal de exclusão inteligente
        async abrirModalExclusaoInteligente(papel) {
            this.papelParaExcluir = papel;
            
            try {
                // Carregar detalhes dos usuários vinculados
                const responseUsuarios = await axios.get(`/api/administracao/papeis/${papel.id}/users`);
                this.detalhesExclusao.usuarios = responseUsuarios.data || [];
                
                // Carregar detalhes das permissões vinculadas
                const responsePermissoes = await axios.get(`/api/administracao/papeis/${papel.id}/permissions`);
                this.detalhesExclusao.permissoes = responsePermissoes.data || [];
                
                this.modalExclusaoInteligente.show();
                
            } catch (error) {
                console.error('Erro ao carregar detalhes para exclusão:', error);
                this.mostrarToast('Erro', 'Erro ao carregar detalhes do papel', 'fa-exclamation-circle text-danger');
            }
        },
        
        // Excluir papel com remoção automática de vínculos
        async excluirPapelComVinculos() {
            if (!this.papelParaExcluir) return;
            
            this.excluindoPapel = true;
            
            try {
                // console.log('Iniciando exclusão inteligente do papel:', this.papelParaExcluir.display_name);
                // console.log('Detalhes de exclusão:', this.detalhesExclusao);
                
                // Primeiro, remover todas as permissões do papel
                if (this.detalhesExclusao.permissoes.length > 0) {
                    //console.log('Removendo permissões do papel...');
                    await axios.post(`/api/administracao/papeis/${this.papelParaExcluir.id}/permissions`, {
                        permissions: []
                    });
                    //console.log('Permissões removidas com sucesso');
                }
                
                // Depois, remover todos os usuários do papel
                if (this.detalhesExclusao.usuarios.length > 0) {
                    //console.log('Removendo usuários do papel...');
                    for (const usuario of this.detalhesExclusao.usuarios) {
                        //console.log('Processando usuário:', usuario.name, 'Roles:', usuario.roles);
                        
                        // Verificar se usuario.roles existe e é um array
                        const rolesAtuais = Array.isArray(usuario.roles) ? usuario.roles : [];
                        const papeisAtuais = rolesAtuais.filter(role => role && role.id !== this.papelParaExcluir.id);
                        
                        console.log('Papéis após filtro:', papeisAtuais);
                        
                        await axios.put(`/api/administracao/usuarios/${usuario.id}`, {
                            name: usuario.name,
                            email: usuario.email,
                            login_type: usuario.login_type,
                            is_active: usuario.is_active,
                            roles: papeisAtuais.map(role => role.id)
                        });
                        //console.log('Usuário atualizado:', usuario.name);
                    }
                    //console.log('Todos os usuários foram removidos do papel');
                }
                
                // Finalmente, excluir o papel
                //console.log('Excluindo o papel...');
                await axios.delete(`/api/administracao/papeis/${this.papelParaExcluir.id}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                //console.log('Papel excluído com sucesso!');
                this.modalExclusaoInteligente.hide();
                this.mostrarToast('Sucesso', `Papel '${this.papelParaExcluir.display_name}' excluído com sucesso!`, 'fa-check-circle text-success');
                this.carregarPapeis();
                this.carregarUsuarios();
                
            } catch (error) {
                console.error('Erro ao excluir papel com vínculos:', error);
                this.mostrarToast('Erro', 'Erro ao excluir papel', 'fa-exclamation-circle text-danger');
            } finally {
                this.excluindoPapel = false;
            }
        },
        
        // Mostrar mensagem quando papel não pode ser excluído (método antigo - mantido para compatibilidade)
        mostrarMensagemNaoExcluivel(papel) {
            //console.log('Método mostrarMensagemNaoExcluivel chamado para:', papel);
            
            let mensagem = `Não é possível excluir o papel '${papel.display_name}' porque:`;
            
            if (papel.users_count > 0) {
                mensagem += `\n• Possui ${papel.users_count} usuário(s) vinculado(s)`;
            }
            
            if (papel.permissions_count > 0) {
                mensagem += `\n• Possui ${papel.permissions_count} permissão(ões) vinculada(s)`;
            }
            
            mensagem += `\n\nPara excluir este papel, primeiro:`;
            
            if (papel.users_count > 0) {
                mensagem += `\n• Remova os usuários deste papel (use o botão "Editar" na aba "Usuários")`;
            }
            
            if (papel.permissions_count > 0) {
                mensagem += `\n• Remova as permissões deste papel (use o botão "Gerenciar Permissões")`;
            }
            
            this.mostrarToast('Ação não permitida', mensagem, 'fa-info-circle text-warning');
        },
        
        // Salvar papel
        async salvarPapel() {
            this.salvandoPapel = true;
            this.errorsPapel = {};
            
            try {
                const url = this.modoEdicaoPapel 
                    ? `/api/administracao/papeis/${this.itemSelecionado.id}`
                    : '/api/administracao/papeis';
                
                const method = this.modoEdicaoPapel ? 'put' : 'post';
                
                await axios[method](url, {
                    display_name: this.formPapel.display_name,
                    name: this.formPapel.name,
                    description: this.formPapel.description,
                    is_active: this.formPapel.is_active
                });
                
                this.modalPapel.hide();
                this.mostrarToast('Sucesso', 'Papel salvo com sucesso!', 'fa-check-circle text-success');
                this.carregarPapeis();
                
            } catch (error) {
                if (error.response && error.response.data.errors) {
                    this.errorsPapel = error.response.data.errors;
                } else {
                    this.mostrarToast('Erro', 'Erro ao salvar papel', 'fa-exclamation-circle text-danger');
                }
            } finally {
                this.salvandoPapel = false;
            }
        },
        
        // Carregar papéis da API
        async carregarPapeis() {
            this.loadingPapeis = true;
            try {
                const response = await axios.get('/api/administracao/papeis');
                
                console.log('Resposta da API de papéis:', response.data);
                
                // Garantir que sempre seja um array
                this.papeis = Array.isArray(response.data) ? response.data : [];
                
                // Debug: mostrar contadores de cada papel
                this.papeis.forEach(papel => {
                    console.log(`Papel: ${papel.display_name} - Usuários: ${papel.users_count}, Permissões: ${papel.permissions_count}`);
                });
                
            } catch (error) {
                console.error('Erro ao carregar papéis:', error);
                this.mostrarToast('Erro', 'Erro ao carregar papéis: ' + (error.response?.data?.message || error.message), 'fa-exclamation-circle text-danger');
                this.papeis = [];
            } finally {
                this.loadingPapeis = false;
            }
        },
        
        // Carregar papel para edição
        carregarPapelParaEdicao(papel) {
            this.formPapel = {
                display_name: papel.display_name,
                name: papel.name,
                description: papel.description,
                is_active: papel.is_active
            };
        },
        
        // Limpar formulário de papel
        limparFormularioPapel() {
            this.formPapel = {
                display_name: '',
                name: '',
                description: '',
                is_active: true
            };
            this.errorsPapel = {};
        },
        
        // Métodos de filtro para papéis
        filtrarPapeis() {
            this.paginaAtual = 1;
        },
        
        limparFiltrosPapeis() {
            this.filtrosPapeis = {
                nome: '',
                nome_interno: '',
                is_active: ''
            };
            this.paginaAtual = 1;
        },
        
        filtrarPapeisComputed() {
            if (!Array.isArray(this.papeis)) {
                return [];
            }
            
            return this.papeis.filter(papel => {
                const nomeMatch = !this.filtrosPapeis.nome || 
                    papel.display_name.toLowerCase().includes(this.filtrosPapeis.nome.toLowerCase());
                
                const nomeInternoMatch = !this.filtrosPapeis.nome_interno || 
                    papel.name.toLowerCase().includes(this.filtrosPapeis.nome_interno.toLowerCase());
                
                const statusMatch = !this.filtrosPapeis.is_active || 
                    (this.filtrosPapeis.is_active === '1' && papel.is_active) ||
                    (this.filtrosPapeis.is_active === '0' && !papel.is_active);
                
                return nomeMatch && nomeInternoMatch && statusMatch;
            });
        },
        
        // Gerenciar permissões do papel
        gerenciarPermissoes(papel) {
            this.mostrarToast('Info', `Gerenciando permissões do papel: ${papel.display_name}`, 'fa-key text-info');
        },
        
        // ===== MÉTODOS PARA PERMISSÕES =====
        
        // Toggle filtros de permissões
        toggleFiltrosPermissoes() {
            this.filtrosPermissoesVisiveis = !this.filtrosPermissoesVisiveis;
        },
        
        // Carregar permissões da API
        async carregarPermissoes() {
            this.loadingPermissoes = true;
            try {
                const response = await axios.get('/api/administracao/permissoes');
                
                // Garantir que sempre seja um array
                this.permissoes = Array.isArray(response.data) ? response.data : [];
            } catch (error) {
                console.error('Erro ao carregar permissões:', error);
                this.mostrarToast('Erro', 'Erro ao carregar permissões: ' + (error.response?.data?.message || error.message), 'fa-exclamation-circle text-danger');
                // Em caso de erro, usar dados mockados para teste
                this.permissoes = [
                    { id: 1, name: 'gerenciar_usuarios', display_name: 'Gerenciar Usuários' },
                    { id: 2, name: 'visualizar_relatorios', display_name: 'Visualizar Relatórios' }
                ];
            } finally {
                this.loadingPermissoes = false;
            }
        },
        
        // Métodos de filtro para permissões
        filtrarPermissoes() {
            this.paginaAtual = 1;
        },
        
        limparFiltrosPermissoes() {
            this.filtrosPermissoes = {
                nome: '',
                nome_interno: '',
                is_active: ''
            };
            this.paginaAtual = 1;
        },
        
        filtrarPermissoesComputed() {
            if (!Array.isArray(this.permissoes)) {
                return [];
            }
            
            return this.permissoes.filter(permissao => {
                const nomeMatch = !this.filtrosPermissoes.nome || 
                    permissao.display_name.toLowerCase().includes(this.filtrosPermissoes.nome.toLowerCase());
                
                const nomeInternoMatch = !this.filtrosPermissoes.nome_interno || 
                    permissao.name.toLowerCase().includes(this.filtrosPermissoes.nome_interno.toLowerCase());
                
                const statusMatch = !this.filtrosPermissoes.is_active || 
                    (this.filtrosPermissoes.is_active === '1' && permissao.is_active) ||
                    (this.filtrosPermissoes.is_active === '0' && !permissao.is_active);
                
                return nomeMatch && nomeInternoMatch && statusMatch;
            });
        },
        

        
        // ===== MÉTODOS CRUD PARA PERMISSÕES =====
        
        // Abrir modal para criar permissão
        abrirModalCriarPermissao() {
            this.modoEdicaoPermissao = false;
            this.limparFormularioPermissao();
            this.modalPermissao.show();
        },
        
        // Editar permissão
        editarPermissao(permissao) {
            this.modoEdicaoPermissao = true;
            this.itemSelecionado = permissao;
            this.carregarPermissaoParaEdicao(permissao);
            this.modalPermissao.show();
        },
        
        // Excluir permissão
        async excluirPermissao(permissao) {
            this.itemParaExcluir = permissao;
            this.tipoExclusao = 'permissao';
            this.mensagemConfirmacao = `Tem certeza que deseja excluir a permissão <strong>"${permissao.display_name}"</strong>?`;
            
            const modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacaoExclusao'));
            modalConfirmacao.show();
        },
        
        // Salvar permissão
        async salvarPermissao() {
            this.salvandoPermissao = true;
            this.errorsPermissao = {};
            
            try {
                const url = this.modoEdicaoPermissao 
                    ? `/api/administracao/permissoes/${this.itemSelecionado.id}`
                    : '/api/administracao/permissoes';
                
                const method = this.modoEdicaoPermissao ? 'put' : 'post';
                
                await axios[method](url, {
                    display_name: this.formPermissao.display_name,
                    name: this.formPermissao.name,
                    description: this.formPermissao.description,
                    is_active: this.formPermissao.is_active
                });
                
                this.modalPermissao.hide();
                this.mostrarToast('Sucesso', 'Permissão salva com sucesso!', 'fa-check-circle text-success');
                this.carregarPermissoes();
                
            } catch (error) {
                if (error.response && error.response.data.errors) {
                    this.errorsPermissao = error.response.data.errors;
                } else {
                    this.mostrarToast('Erro', 'Erro ao salvar permissão', 'fa-exclamation-circle text-danger');
                }
            } finally {
                this.salvandoPermissao = false;
            }
        },
        
        // Carregar permissão para edição
        carregarPermissaoParaEdicao(permissao) {
            this.formPermissao = {
                display_name: permissao.display_name,
                name: permissao.name,
                description: permissao.description,
                is_active: permissao.is_active
            };
        },
        
        // Limpar formulário de permissão
        limparFormularioPermissao() {
            this.formPermissao = {
                display_name: '',
                name: '',
                description: '',
                is_active: true
            };
            this.errorsPermissao = {};
        },
        
        mostrarToast(title, message, icon) {
            this.toastTitle = title;
            this.toastMessage = message;
            this.toastIcon = icon;
            this.toast.show();
        },
        

        
        // Mostrar mensagem explicativa para papel não excluível
        mostrarMensagemPapelNaoExcluivel(papel) {
            let mensagem = '';
            let titulo = 'Papel não pode ser excluído';
            
            if (papel.users_count > 0 && papel.permissions_count > 0) {
                mensagem = `O papel "${papel.display_name}" não pode ser excluído porque possui ${papel.users_count} usuário(s) e ${papel.permissions_count} permissão(ões) vinculados. Para excluí-lo, primeiro remova todos os usuários e permissões associados.`;
            } else if (papel.users_count > 0) {
                mensagem = `O papel "${papel.display_name}" não pode ser excluído porque possui ${papel.users_count} usuário(s) vinculado(s). Para excluí-lo, primeiro remova todos os usuários associados.`;
            } else if (papel.permissions_count > 0) {
                mensagem = `O papel "${papel.display_name}" não pode ser excluído porque possui ${papel.permissions_count} permissão(ões) vinculada(s). Para excluí-lo, primeiro remova todas as permissões associadas.`;
            }
            
            this.mostrarToast(titulo, mensagem, 'fa-info-circle text-warning');
        },

        // Mostrar mensagem explicativa para permissão não excluível
        mostrarMensagemPermissaoNaoExcluivel(permissao) {
            let mensagem = '';
            let titulo = 'Permissão não pode ser excluída';
            
            if (permissao.roles_count > 0) {
                mensagem = `A permissão "${permissao.display_name}" não pode ser excluída porque está vinculada a ${permissao.roles_count} papel(éis). Para excluí-la, primeiro remova todas as vinculações com papéis.`;
            }
            
            this.mostrarToast(titulo, mensagem, 'fa-info-circle text-warning');
        },

        // ===== MÉTODOS PARA FUNCIONALIDADES DA ABA PERMISSÕES =====


        
        // Ver detalhes da permissão
        async verDetalhesPermissao(permissao) {
            this.permissaoSelecionada = permissao;
            this.loadingDetalhesPermissao = true;
            
            try {
                // Carregar papéis que possuem esta permissão
                const responsePapeis = await axios.get(`/api/administracao/permissoes/${permissao.id}/roles`);
                this.papeisPermissao = responsePapeis.data;
                
                // Carregar usuários que possuem esta permissão
                const responseUsuarios = await axios.get(`/api/administracao/permissoes/${permissao.id}/users`);
                this.usuariosPermissao = responseUsuarios.data;
                
                this.modalDetalhesPermissao.show();
                
            } catch (error) {
                this.mostrarToast('Erro', 'Erro ao carregar detalhes da permissão', 'fa-exclamation-circle text-danger');
            } finally {
                this.loadingDetalhesPermissao = false;
            }
        },
        
        // Gerenciar papéis da permissão
        async gerenciarPapeisPermissao(permissao) {
            this.permissaoSelecionada = permissao;
            this.loadingPapeisPermissao = true;
            
            try {
                // Carregar papéis atuais da permissão
                const responsePapeis = await axios.get(`/api/administracao/permissoes/${permissao.id}/roles`);
                this.papeisAtuaisPermissao = responsePapeis.data;
                
                // Carregar papéis disponíveis para adicionar
                const responseDisponiveis = await axios.get(`/api/administracao/permissoes/${permissao.id}/available-roles`);
                this.papeisDisponiveisPermissao = responseDisponiveis.data;
                
                this.modalGerenciarPapeisPermissao.show();
                
            } catch (error) {
                this.mostrarToast('Erro', 'Erro ao carregar papéis da permissão', 'fa-exclamation-circle text-danger');
            } finally {
                this.loadingPapeisPermissao = false;
            }
        },
        
        // Adicionar papel à permissão
        async adicionarPapelAPermissao(papel) {
            try {
                await axios.post(`/api/administracao/permissoes/${this.permissaoSelecionada.id}/roles/${papel.id}`);
                
                // Mover papel da lista de disponíveis para a lista de atuais
                this.papeisAtuaisPermissao.push(papel);
                this.papeisDisponiveisPermissao = this.papeisDisponiveisPermissao.filter(p => p.id !== papel.id);
                
                this.mostrarToast('Sucesso', 'Papel adicionado à permissão com sucesso!', 'fa-check-circle text-success');
                
            } catch (error) {
                this.mostrarToast('Erro', 'Erro ao adicionar papel à permissão', 'fa-exclamation-circle text-danger');
            }
        },
        
        // Remover papel da permissão
        async removerPapelDaPermissao(papel) {
            try {
                await axios.delete(`/api/administracao/permissoes/${this.permissaoSelecionada.id}/roles/${papel.id}`);
                
                // Mover papel da lista de atuais para a lista de disponíveis
                this.papeisDisponiveisPermissao.push(papel);
                this.papeisAtuaisPermissao = this.papeisAtuaisPermissao.filter(p => p.id !== papel.id);
                
                this.mostrarToast('Sucesso', 'Papel removido da permissão com sucesso!', 'fa-check-circle text-success');
                
            } catch (error) {
                this.mostrarToast('Erro', 'Erro ao remover papel da permissão', 'fa-exclamation-circle text-danger');
            }
        },
        
        // ===== MÉTODO PRINCIPAL DE CONFIRMAÇÃO DE EXCLUSÃO =====
        async confirmarExclusao() {
            if (!this.itemParaExcluir || !this.tipoExclusao) return;
            
            this.excluindo = true;
            
            try {
                switch (this.tipoExclusao) {
                    case 'usuario':
                        await this.excluirUsuarioConfirmado();
                        break;
                    case 'papel':
                        await this.excluirPapelConfirmado();
                        break;
                    case 'permissao':
                        await this.excluirPermissaoConfirmada();
                        break;
                    case 'usuarioPapel':
                        await this.removerUsuarioPapelConfirmado();
                        break;
                    case 'permissaoPapel':
                        await this.removerPermissaoPapelConfirmada();
                        break;
                    default:
                        throw new Error('Tipo de exclusão não reconhecido');
                }
                
                // Fechar modal e mostrar toast de sucesso
                const modalConfirmacao = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacaoExclusao'));
                modalConfirmacao.hide();
                
                // Limpar estado
                this.itemParaExcluir = null;
                this.tipoExclusao = '';
                this.mensagemConfirmacao = '';
                
            } catch (error) {
                console.error('Erro na exclusão:', error);
                this.mostrarToast('Erro', 'Erro ao executar exclusão', 'fa-exclamation-circle text-danger');
            } finally {
                this.excluindo = false;
            }
        },
        
        // ===== MÉTODOS DE EXCLUSÃO CONFIRMADA =====
        
        async excluirUsuarioConfirmado() {
            try {
                const response = await axios.delete(`/api/administracao/usuarios/${this.itemParaExcluir.id}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                this.mostrarToast('Sucesso', 'Usuário excluído com sucesso!', 'fa-check-circle text-success');
                this.carregarUsuarios();
                this.carregarPapeis();
            } catch (error) {
                console.error('Erro ao excluir usuário:', error);
                const mensagem = error.response?.data?.message || error.message || 'Erro ao excluir usuário';
                this.mostrarToast('Erro', mensagem, 'fa-exclamation-circle text-danger');
                throw error; // Re-throw para ser capturado pelo método principal
            }
        },
        
        async excluirPapelConfirmado() {
            const response = await axios.delete(`/api/administracao/papeis/${this.itemParaExcluir.id}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            this.mostrarToast('Sucesso', 'Papel excluído com sucesso!', 'fa-check-circle text-success');
            this.carregarPapeis();
        },
        
        async excluirPermissaoConfirmada() {
                            const response = await axios.delete(`/api/administracao/permissoes/${this.itemParaExcluir.id}`);
            this.mostrarToast('Sucesso', 'Permissão excluída com sucesso!', 'fa-check-circle text-success');
            this.carregarPermissoes();
        },
        
        async removerUsuarioPapelConfirmado() {
            this.salvandoUsuariosPapel = true;
            
            try {
                const papelId = this.itemParaExcluir.papel.id;
                const usuarioId = this.itemParaExcluir.usuario.id;
                
                // Remover usuário do papel via API
                await axios.delete(`/api/administracao/papeis/${papelId}/users/${usuarioId}`);

                // Atualizar listas locais
                this.usuariosDisponiveis.push(this.itemParaExcluir.usuario);
                this.usuariosAtuais = this.usuariosAtuais.filter(u => u.id !== usuarioId);
                
                this.mostrarToast('Sucesso', `Usuário '${this.itemParaExcluir.usuario.name}' removido do papel com sucesso!`, 'fa-check-circle text-success');
                
                // Recarregar papéis para atualizar os contadores
                this.carregarPapeis();
                
                // Recarregar usuários para sincronizar dados entre abas
                this.carregarUsuarios();
                
            } catch (error) {
                console.error('Erro ao remover usuário do papel:', error);
                this.mostrarToast('Erro', 'Erro ao remover usuário do papel: ' + (error.response?.data?.message || error.message), 'fa-exclamation-circle text-danger');
            } finally {
                this.salvandoUsuariosPapel = false;
            }
        },
        
        async removerPermissaoPapelConfirmada() {
            this.salvandoPermissoesPapel = true;
            
            try {
                const papelId = this.itemParaExcluir.papel.id;
                const permissaoId = this.itemParaExcluir.permissao.id;
                
                // Obter permissões atuais sem a que será removida
                const permissoesRestantes = this.permissoesAtuais
                    .filter(p => p.id !== permissaoId)
                    .map(p => p.id);
                
                // Atualizar permissões do papel via API (POST com array de permissões restantes)
                await axios.post(`/api/administracao/papeis/${papelId}/permissions`, {
                    permissions: permissoesRestantes
                });

                // Atualizar listas locais
                this.permissoesDisponiveis.push(this.itemParaExcluir.permissao);
                this.permissoesAtuais = this.permissoesAtuais.filter(p => p.id !== permissaoId);
                
                this.mostrarToast('Sucesso', `Permissão '${this.itemParaExcluir.permissao.display_name}' removida do papel com sucesso!`, 'fa-check-circle text-success');
                
                // Recarregar papéis para atualizar os contadores
                this.carregarPapeis();
                
            } catch (error) {
                console.error('Erro ao remover permissão do papel:', error);
                this.mostrarToast('Erro', 'Erro ao remover permissão do papel: ' + (error.response?.data?.message || error.message), 'fa-exclamation-circle text-danger');
            } finally {
                this.salvandoPermissoesPapel = false;
            }
        },
        
        // Carregar dados do usuário logado
        async carregarDadosUsuario() {
            try {
                const response = await axios.get('/api/auth/me');
                this.currentUser = response.data;
                console.log('Usuário logado:', this.currentUser);
            } catch (error) {
                console.error('Erro ao carregar dados do usuário:', error);
            }
        },
    }
}
</script>

<!-- CSS centralizado em modern-interface.css -->

<!-- CSS centralizado em modern-interface.css -->
