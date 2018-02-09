export const services = {
    // This makes your getters, mutations, and actions accessed by, eg: 'myModule/myModularizedNumber' instead of mounting getters, mutations, and actions to the root namespace.
    namespaced: true,
    state: {
        services: [],
        types: [],
        packages: [],
    },
    actions:{
        fetchServices(context){
            axios.get('/api/service/getServices')
                .then(function (response) {
                    context.commit('updateServices', response.data);
                })
        },
        fetchPackages:function(context){
            axios.get('/api/service/getServicePackages')
                .then(function (response) {
                    context.commit('updatePackages', response.data);
                });
        },
        fetchTypes:function(context){
            axios.get('/api/service/getServiceTypes')
                .then(function (response) {
                    context.commit('updateTypes', response.data);
                });
        },
    },
    getters:{
        activeServices(state){
            return state.services.filter(function(item){
                return item.is_active === 1;
            });
        }
    },
    mutations:{
        updateServices(state, services){
            state.services = services;
        },
        updatePackages(state, packages){
            state.packages = packages;
        },
        updateTypes(state, types){
            state.types = types;
        }
    }
};