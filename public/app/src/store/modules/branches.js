export const branches = {
    // This makes your getters, mutations, and actions accessed by, eg: 'myModule/myModularizedNumber' instead of mounting getters, mutations, and actions to the root namespace.
    namespaced: true,
    state: {
        branches: []
    },
    actions:{
        fetchBranches(context){
            axios.get('/api/branch/getBranches')
                .then(function (response) {
                    context.commit('updateBranches', response.data);
                })
        }
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
    }
};