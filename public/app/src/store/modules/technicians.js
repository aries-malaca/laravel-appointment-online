export const technicians = {
    // This makes your getters, mutations, and actions accessed by, eg: 'myModule/myModularizedNumber' instead of mounting getters, mutations, and actions to the root namespace.
    namespaced: true,
    state: {
        technicians: [],
        viewing_technician:false,
        editing_technician:false,
        reviews:[],
    },
    actions:{
        fetchTechnicians(context){
            axios.get('/api/technician/getTechnicians')
                .then(function (response) {
                    context.commit('updateTechnicians', response.data);
                })
        }
    },
    getters:{
        averageRating(state){
            var r = 0;
            for(var x=0;x<state.reviews.length;x++)
                r += state.reviews[x].rating;

            return r/state.reviews.length;
        }
    },
    mutations:{
        updateTechnicians(state, technicians){
            state.technicians = technicians;
        },
        updateEditingTechnician(state, technician){
            state.editing_technician = technician;
        },
        updateViewingTechnician(state, technician){
            state.viewing_technician = technician;
        },
        updateReviews(state, reviews){
            state.reviews = reviews;
        }
    }
};