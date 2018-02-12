export const technicians = {
    // This makes your getters, mutations, and actions accessed by, eg: 'myModule/myModularizedNumber' instead of mounting getters, mutations, and actions to the root namespace.
    namespaced: true,
    state: {
        technicians: [],
        viewing_technician:false,
        editing_technician:false
    },
    actions:{
        fetchTechnicians(context){
            axios.get('/api/technician/getTechnicians')
                .then(function (response) {
                    context.commit('updateTechnicians', response.data);
                })
        }
    },
    mutations:{
        updateTechnicians(state, technicians){
            state.technicians = technicians;
        },
        updateEditingTechnician(state, branch){
            state.editing_branch = branch;
        },
        updateViewingTechnician(state, technician){
            state.viewing_technician = technician;
        }
    }
};