import Vue from 'vue';
import App from './components/App.vue';
import router from './router';
import VueSocketIO from 'vue-socket.io';
import {store} from './store/store';

Vue.config.productionTip = false;
Vue.config.debug = true;
Vue.config.devtools = true;

//frontend configurations
let client_socket = 'https://lbo-express.azurewebsites.net';
//end frontend configurations

Vue.use(VueSocketIO, client_socket);

new Vue({
    el: '#app',
    router,
    store,
    template: '<App/>',
    components: { App }
});