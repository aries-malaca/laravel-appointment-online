import Vue from 'vue';
import Vuex from 'vuex';
import {services} from './modules/services';
import {branches} from './modules/branches';
import {products} from './modules/products';
import {technicians} from './modules/technicians';

Vue.use(Vuex);
export const store = new Vuex.Store({
    modules: {
        services,
        branches,
        products,
        technicians,
    },
    state: {
        token: undefined,
        user: null,
        configs:null,
        transactions:false,
        menus:[],
        title: 'App'
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
            setTimeout(function(){
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var geocoder = new google.maps.Geocoder;
                        geocoder.geocode({'location': { lat: position.coords.latitude, lng: position.coords.longitude}},
                            function(results, status) {
                                if (status === 'OK' && results.lat === undefined) {
                                    axios({url:'/api/user/saveLocation?token=' + context.state.token, method:'post', data:{ geolocation:results }})
                                        .then(function () {
                                            context.dispatch('fetchAuthenticatedUser');
                                        });
                                }
                            });
                    });
                }
            },1000);
        }
    },
    mutations:{
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
        }
    }
});