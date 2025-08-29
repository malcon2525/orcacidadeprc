/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
import HomeComponent from './components/Home.vue';
import HeaderComponent from './components/Header.vue';
import ActiveDirectoryMain from './components/administracao/active-directory/ActiveDirectoryMain.vue';
import ListaUsuarios from './components/administracao/usuarios/ListaUsuarios.vue';
import BuscaGlobal from './components/administracao/usuarios/BuscaGlobal.vue';
import ListaMunicipios from './components/administracao/municipios/ListaMunicipios.vue';
import ListaEntidadesOrcamentariasAdmin from './components/administracao/entidades-orcamentarias/ListaEntidadesOrcamentariasAdmin.vue';
import ListaAprovacaoCadastros from './components/administracao/aprovacao-cadastros/ListaAprovacaoCadastros.vue';
import ListaUsuariosPorEntidade from './components/administracao/usuarios-por-entidade/ListaUsuariosPorEntidade.vue';
import GestaoEstruturaOrcamento from './components/administracao/estrutura-orcamento/GestaoEstruturaOrcamento.vue';
import ListaTipoOrcamento from './components/administracao/estrutura-orcamento/ListaTipoOrcamento.vue';
import EstruturaOrcamento from './components/administracao/estrutura-orcamento/EstruturaOrcamento.vue';
import VisualizacaoIntegrada from './components/administracao/estrutura-orcamento/VisualizacaoIntegrada.vue';

// DER-PR Components
import ImportarDerpr from './components/tabela_oficial/importar_derpr/Index.vue';
import ServicosGerais from './components/tabela_oficial/importar_derpr/components/ServicosGerais.vue';
import Insumos from './components/tabela_oficial/importar_derpr/components/Insumos.vue';
import FormulasTransporte from './components/tabela_oficial/importar_derpr/components/FormulasTransporte.vue';
import ImportarLoteDerpr from './components/tabela_oficial/importar_derpr/components/ImportarLoteDerpr.vue';

// SINAPI Components
import ImportarSinapi from './components/tabela_oficial/importar_sinapi/Index.vue';
import ComposicoesInsumos from './components/tabela_oficial/importar_sinapi/components/ComposicoesInsumos.vue';
import PercentagensMaoDeObra from './components/tabela_oficial/importar_sinapi/components/PercentagensMaoDeObra.vue';
import GravarSinapi from './components/tabela_oficial/importar_sinapi/components/GravarSinapi.vue';
import ConsultarSinapi from './components/tabela_oficial/consultar_sinapi/ConsultarSinapi.vue';
import ConsultarDerpr from './components/tabela_oficial/consultar_derpr/Index.vue';

// Componente Público
import FormularioSolicitacaoCadastro from './components/publico/FormularioSolicitacaoCadastro.vue';

// Componente Perfil
import ListaMeuPerfil from './components/perfil/meu-perfil/ListaMeuPerfil.vue';

// Composições Próprias Components
import ListaComposicaoPropria from './components/orcamento/composicao-propria/ListaComposicaoPropria.vue';
import ComposicaoPropriaForm from './components/orcamento/composicao-propria/ComposicaoPropriaForm.vue';

// Configuração Orçamento Components
import ConfiguracaoContextoOrcamento from './components/orcamento/configuracao_orcamento/ConfiguracaoContextoOrcamento.vue';

app.component('example-component', ExampleComponent);
app.component('home-component', HomeComponent);
app.component('header-component', HeaderComponent);
app.component('active-directory-main', ActiveDirectoryMain);
app.component('lista-usuarios', ListaUsuarios);
app.component('busca-global', BuscaGlobal);
app.component('lista-municipios', ListaMunicipios);
app.component('lista-entidades-orcamentarias-admin', ListaEntidadesOrcamentariasAdmin);
app.component('lista-aprovacao-cadastros', ListaAprovacaoCadastros);
app.component('lista-usuarios-por-entidade', ListaUsuariosPorEntidade);
app.component('gestao-estrutura-orcamento', GestaoEstruturaOrcamento);
app.component('lista-tipo-orcamento', ListaTipoOrcamento);
app.component('estrutura-orcamento', EstruturaOrcamento);
app.component('visualizacao-integrada', VisualizacaoIntegrada);

// DER-PR Components
app.component('importar-derpr', ImportarDerpr);
app.component('servicos-gerais', ServicosGerais);
app.component('insumos', Insumos);
app.component('formulas-transporte', FormulasTransporte);
app.component('importar-lote-derpr', ImportarLoteDerpr);

// SINAPI Components
app.component('importar-sinapi', ImportarSinapi);
app.component('composicoes-insumos', ComposicoesInsumos);
app.component('percentagens-mao-de-obra', PercentagensMaoDeObra);
app.component('gravar-sinapi', GravarSinapi);
app.component('consultar-sinapi-component', ConsultarSinapi);

// DER-PR Components
app.component('consultar-derpr-index', ConsultarDerpr);

// Componente Público
app.component('formulario-solicitacao-cadastro', FormularioSolicitacaoCadastro);

// Componente Perfil
app.component('lista-meu-perfil', ListaMeuPerfil);

// Composições Próprias Components
app.component('lista-composicao-propria', ListaComposicaoPropria);
app.component('composicao-propria-form', ComposicaoPropriaForm);

// Configuração Orçamento Components
app.component('configuracao-contexto-orcamento', ConfiguracaoContextoOrcamento);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');
