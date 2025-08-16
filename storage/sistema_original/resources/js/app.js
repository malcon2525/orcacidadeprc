/* Importar o JS do layout */
import './scripts_sbadmin';

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// Importar Bootstrap
import * as bootstrap from 'bootstrap'
window.bootstrap = bootstrap

import './bootstrap';
import { createApp } from 'vue';
import '../css/app.css';
import '../css/crud-styles.css';
import '../css/estrutura-orcamento-shared.css';
import '../css/sidebar.css';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});



// Importar componentes
import login from './components/autenticacao/login.vue';
import home from './components/Home.vue';
import msgsystem from './components/MsgSystem.vue';
import campoMoeda from './components/geral/CampoMoedaComponent.vue';
import campoFile from './components/geral/CampoFileComponent.vue';
import ImportarDerpr from './components/tabela_oficial/importar_derpr/Index.vue';
import ImportarSinapiRefatorado from './components/tabela_oficial/importar_sinapi/Index.vue';
import ConsultarDerprIndex from './components/tabela_oficial/consultar_derpr/Index.vue';
import ConsultarSINAPI from './components/tabela_oficial/consultar_sinapi/ConsultarSinapi.vue';
import ListaMunicipios from './components/municipios/ListaMunicipios.vue';
import ListaMunicipiosAdmin from './components/administracao/municipios/ListaMunicipios.vue';
import ListaEntidadesOrcamentariasAdmin from './components/administracao/entidades-orcamentarias/ListaEntidadesOrcamentariasAdmin.vue';
import ListaBDI from './components/orcamento/BDI/ListaBDI.vue';
import ListaCotacoes from './components/orcamento/cotacoes/ListaCotacoes.vue';
import ListaComposicaoPropria from './components/orcamento/composicao-propria/ListaComposicaoPropria.vue';
import CustoTransporteCrud from './components/transporte/CustoTransporteCrud.vue';
import ListaDmtDefault from './components/transporte/dmt-default/ListaDmtDefault.vue';
import ListaDmt from './components/transporte/dmt/ListaDmt.vue';
import ConfiguracaoGeralForm from './components/configuracoes_gerais/ConfiguracaoGeralForm.vue';

import header from './components/Header.vue';

// Componentes Active Directory
import ActiveDirectoryMain from './components/administracao/active-directory/ActiveDirectoryMain.vue';

// Registrar componentes
app.component('login-component', login);
app.component('home-component', home);
app.component('msgsystem-component', msgsystem);
app.component('campo-moeda-component', campoMoeda);
app.component('campo-file-component', campoFile);
app.component('importar-derpr', ImportarDerpr);
app.component('importar-sinapi-refatorado', ImportarSinapiRefatorado);
app.component('consultar-derpr-index', ConsultarDerprIndex);
app.component('consultar-sinapi-component', ConsultarSINAPI);
app.component('lista-municipios', ListaMunicipios);
app.component('lista-municipios-admin', ListaMunicipiosAdmin);
app.component('lista-entidades-orcamentarias-admin', ListaEntidadesOrcamentariasAdmin);
app.component('lista-bdi', ListaBDI);
app.component('lista-cotacoes', ListaCotacoes);
app.component('lista-composicao-propria', ListaComposicaoPropria);
app.component('custo-transporte-crud', CustoTransporteCrud);
app.component('lista-dmt-default', ListaDmtDefault);
app.component('lista-dmt', ListaDmt);
app.component('configuracao-geral-form', ConfiguracaoGeralForm);

app.component('header-component', header);

// Registrar componentes Active Directory
app.component('active-directory-main', ActiveDirectoryMain);

// ===== COMPONENTES DE ADMINISTRAÇÃO =====
import ListaUsuarios from './components/administracao/usuarios/ListaUsuarios.vue'
app.component('lista-usuarios', ListaUsuarios)

// Estrutura de Orçamento
import GestaoEstruturaOrcamento from './components/administracao/estrutura-orcamento/GestaoEstruturaOrcamento.vue';
import ListaTipoOrcamento from './components/administracao/estrutura-orcamento/ListaTipoOrcamento.vue';

app.component('gestao-estrutura-orcamento', GestaoEstruturaOrcamento);
app.component('lista-tipo-orcamento', ListaTipoOrcamento);

app.mount('#app');