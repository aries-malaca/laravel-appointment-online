import Vue from 'vue';
import Router from 'vue-router';

import Appointments from './Appointments.vue';
import Calendar from './Calendar.vue';
import Dashboard from './Dashboard.vue';
import Messages from './Messages.vue';
import Notifications from './Notifications.vue';
import Profile from './Profile.vue';
import Transactions from './Transactions.vue';

Vue.use(Router);

export default new Router({
    routes: [
        {
            path: '/appointments',
            name: 'Appointments',
            component: Appointments
        },
        {
            path: '/calendar',
            name: 'Calendar',
            component: Calendar
        },
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
            path: '/messages',
            name: 'Messages',
            component: Messages
        },
        {
            path: '/notifications',
            name: 'Notifications',
            component: Notifications
        },
        {
            path: '/profile',
            name: 'Profile',
            component: Profile
        },
        {
            path: '/transactions',
            name: 'Transactions',
            component: Transactions
        },
    ]
})
