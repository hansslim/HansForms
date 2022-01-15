import Loading from "./components/Loading";

require('./bootstrap');

import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import VueFormulate from '@braid/vue-formulate'
import VModal from 'vue-js-modal'
import Toasted from 'vue-toasted';

Vue.use(VueFormulate, {
    useInputDecorators: false,
    classes: {
        outer: 'form-group',
        input (context) {
          switch (context.classification) {
              case 'box': return '';
              case 'group': return '';
              default: return 'form-control'
          }
        },
        inputHasErrors: 'is-invalid',
        help: 'form-text text-muted',
        errors: 'list-unstyled text-danger',
    },
});
Vue.use(VModal, { dynamicDefault: { height: 'auto', width: '60%', adaptive: true, scrollable: true } })
Vue.use(Toasted, {
    duration: 5000, position: 'top-center'
});
Vue.component('loading', Loading)

Vue.config.productionTip = false;

new Vue({
    el: '#app',
    router,
    store,
    render: h=>h(App),
});



