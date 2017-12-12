import Vue from 'vue';
import App from './App.vue';
import router from './router';
import VueSocketio from 'vue-socket.io';

Vue.config.productionTip = false;
Vue.config.debug = true;
Vue.config.devtools = true;
Vue.use(VueSocketio, 'https://system.lay-bare.com:5000');
new Vue({
    el: '#app',
    router,
    template: '<App/>',
    components: { App }
});