import Vue from 'vue';
import Router from 'vue-router';

import Appointments from './components/Appointments.vue';
import Branches from './components/Branches.vue';
import Calendar from './components/Calendar.vue';
import Careers from './components/tools/Careers.vue';
import Clients from './components/Clients.vue';
import ClientProfileContainer from './components/clients/profile/ClientProfileContainer.vue';
import BranchProfileContainer from './components/branches/profile/BranchProfileContainer.vue';
import ControlPanel from './components/ControlPanel.vue';
import Dashboard from './components/Dashboard.vue';
import Messages from './components/Messages.vue';
import NewsFeeds from './components/tools/NewsFeeds.vue';
import Notifications from './components/Notifications.vue';
import PLCTracker from './components/plc/PLCTracker.vue';
import Profile from './components/Profile.vue';
import Promotions from './components/promotions/Promotions.vue';
import Queuing from './components/tools/Queuing.vue';
import Reports from './components/Reports.vue';
import Services from './components/Services.vue';
import Technicians from './components/Technicians.vue';
import TechnicianProfileContainer from './components/technicians/profile/TechnicianProfileContainer.vue';
import Transactions from './components/Transactions.vue';
import Testimonials from './components/tools/Testimonials.vue';
import BranchLocator from './components/tools/BranchLocator.vue';
import FrequentlyAskedQuestions from './components/tools/FrequentlyAskedQuestions.vue';
import PromoWallet from './components/promotions/PromoWallet.vue';
import PremierLoyaltyCard from './components/plc/PremierLoyaltyCard.vue';

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
            path: '/careers',
            name: 'Careers',
            component: Careers
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
            path: '/news',
            name: 'NewsFeeds',
            component: NewsFeeds
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
            path: '/promotions',
            name: 'Promotions',
            component: Promotions
        },
        {
            path: '/plctracker',
            name: 'PLCTracker',
            component: PLCTracker
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
            path: '/technicians/:id',
            name: 'Technician Profile',
            component: TechnicianProfileContainer
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
