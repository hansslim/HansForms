require('./bootstrap');

import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import VueFormulate from '@braid/vue-formulate'
import VModal from 'vue-js-modal'

Vue.use(VueFormulate);
Vue.use(VModal, { dynamicDefault: { height: 'auto', width: '60%', adaptive: true, scrollable: true } })

Vue.config.productionTip = false;

new Vue({
    el: '#app',
    router,
    store,
    render: h=>h(App),

});

