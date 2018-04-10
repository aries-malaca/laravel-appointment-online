import Vue from 'vue';
import Vuex from 'vuex';
import {services} from './modules/services';
import {branches} from './modules/branches';
import {products} from './modules/products';
import {technicians} from './modules/technicians';
import {appointments} from './modules/appointments';
import {messages} from './modules/messages';

Vue.use(Vuex);
export const store = new Vuex.Store({
    modules: {
        services,
        branches,
        products,
        technicians,
        appointments,
        messages,
    },
    state: {
        token: undefined,
        user: null,
        configs:null,
        transactions:false,
        menus:[],
        title: 'App',
        queuing_technicians:[],
        serving:[],
        queuing_branch:null,
        queuing_date:moment().format("YYYY-MM-DD")
    },
    actions:{
        fetchAuthenticatedUser(context){
            axios.get('/api/user/getUser?token=' + context.state.token)
                .then(function (response) {
                    context.commit('updateUser', response.data.user);
                    context.commit('updateMenus', response.data.menus);
                    context.commit('updateConfigs', response.data.configs);
                })
                .catch(function (error) {
                    XHRCatcher(error);
                });
        },
        saveLocation(context){

        }
    },
    mutations:{
        updateQueuingBranch(state, queuing_branch){
            state.queuing_branch = queuing_branch;
        },
        updateQueuingDate(state, queuing_date){
            state.queuing_date = queuing_date;
        },
        updateTransactions(state, transactions){
            state.transactions = transactions;
        },
        updateToken(state, token){
            state.token = token;
        },
        updateUser(state, user){
            state.user = user;
        },
        updateConfigs(state, configs){
            state.configs = configs;
        },
        updateMenus(state, menus){
            state.menus = menus;
        },
        updateTitle(state, title){
            state.title = title;
        },
        updateQueuingTechnicians(state, technicians){
            state.queuing_technicians = technicians;
        },
        updateServing(state, serving){
            state.serving = serving;
        }
    }
});