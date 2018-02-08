import Vue from 'vue';
import App from './components/App.vue';
import router from './router';
import VueSocketIO from 'vue-socket.io';
import {store} from './store/store';

Vue.config.productionTip = false;
Vue.config.debug = true;
Vue.config.devtools = true;

Vue.use(VueSocketIO, 'https://lbo-express.azurewebsites.net');

new Vue({
    el: '#app',
    router,
    store,
    template: '<App/>',
    components: { App }
});