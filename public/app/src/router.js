import Vue from 'vue';
import Router from 'vue-router';

import Appointments from './Appointments.vue';
import Branches from './Branches.vue';
import Calendar from './Calendar.vue';
import Clients from './Clients.vue';
import ClientProfileContainer from './profiles/ClientProfileContainer.vue';
import BranchProfileContainer from './profiles/BranchProfileContainer.vue';
import ControlPanel from './ControlPanel.vue';
import Dashboard from './Dashboard.vue';
import Messages from './Messages.vue';
import Notifications from './Notifications.vue';
import Profile from './Profile.vue';
import Queuing from './Queuing.vue';
import Reports from './Reports.vue';
import Services from './Services.vue';
import Technicians from './Technicians.vue';
import Transactions from './Transactions.vue';
import Testimonials from './tools/Testimonials.vue';
import BranchLocator from './tools/BranchLocator.vue';
import FrequentlyAskedQuestions from './tools/FrequentlyAskedQuestions.vue';
import PromoWallet from './tools/PromoWallet.vue';
import PremierLoyaltyCard from './tools/PremierLoyaltyCard.vue';

Vue.use(Router);

export default new Router({
    routes: [
        {
            path: '/appointments',
            name: 'Appointments',
            component: Appointments
        },
        {
            path: '/branches',
            name: 'Branches',
            component: Branches
        },
        {
            path: '/branches/:id',
            name: 'Branch Profile',
            component: BranchProfileContainer
        },
        {
            path: '/calendar',
            name: 'Calendar',
            component: Calendar
        },
        {
            path: '/clients',
            name: 'Clients',
            component: Clients
        },
        {
            path: '/clients/:id',
            name: 'Client Profile',
            component: ClientProfileContainer
        },
        {
            path: '/control_panel',
            name: 'ControlPanel',
            component: ControlPanel
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
            path: '/queuing',
            name: 'Queuing',
            component: Queuing
        },
        {
            path: '/reports',
            name: 'Reports',
            component: Reports
        },
        {
            path: '/services',
            name: 'Services',
            component: Services
        },
        {
            path: '/technicians',
            name: 'Technicians',
            component: Technicians
        },
        {
            path: '/transactions',
            name: 'Transactions',
            component: Transactions
        },
        {
            path: '/plc',
            name: 'PremierLoyaltyCard',
            component: PremierLoyaltyCard
        },
        {
            path: '/faqs',
            name: 'FrequentlyAskedQuestions',
            component: FrequentlyAskedQuestions
        },
        {
            path: '/locator',
            name: 'BranchLocator',
            component: BranchLocator
        },
        {
            path: '/wallet',
            name: 'PromoWallet',
            component: PromoWallet
        },
        {
            path: '/testimonials',
            name: 'Testimonials',
            component: Testimonials
        }
    ]
})
