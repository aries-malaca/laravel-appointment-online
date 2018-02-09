export const branches = {
    // This makes your getters, mutations, and actions accessed by, eg: 'myModule/myModularizedNumber' instead of mounting getters, mutations, and actions to the root namespace.
    namespaced: true,
    state: {
        branches: [],
        editing_branch:false,
        viewing_branch:false,
        cities:[],
        regions:[],
        clusters:[],
    },
    actions:{
        fetchBranches(context){
            axios.get('/api/branch/getBranches')
                .then(function (response) {
                    context.commit('updateBranches', response.data);
                })
        },
        fetchClusters(context){
            axios.get('/api/branch/getClusters')
                .then(function (response) {
                    context.commit('updateClusters', response.data);
                })
        },
        fetchCities(context){
            axios.get('/api/city/getCities')
                .then(function (response) {
                    context.commit('updateCities', response.data);
                })
        },
        fetchRegions(context){
            axios.get('/api/region/getRegions')
                .then(function (response) {
                    context.commit('updateRegions', response.data);
                })
        },
    },
    getters:{
        activeBranches(state){
            return state.branches.filter(function(item){
                return item.is_active === 1;
            });
        }
    },
    mutations:{
        updateBranches(state, branches){
            state.branches = branches;
        },
        updateRegions(state, regions){
            state.regions = regions;
        },
        updateCities(state, cities){
            state.cities = cities;
        },
        updateClusters(state, clusters){
            state.clusters = clusters;
        },
        updateEditingBranch(state, branch){
            state.editing_branch = branch;
        },
        updateViewingBranch(state, branch){
            state.viewing_branch = branch;
        }
    }
};