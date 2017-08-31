import Vue from 'vue';
import Router from 'vue-router';
import Dashboard from './Dashboard.vue';
import About from './About.vue';

Vue.use(Router);

export default new Router({
    routes: [
        {
            path: '/',
            name: 'Dashboard',
            component: Dashboard
        },
        {
            path: '/dashboard',
            name: 'Dashboard',
            component: Dashboard
        },
        {
            path: '/about',
            name: 'About',
            component: About
        }
    ]
})
