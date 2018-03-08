export const chat = {
    // This makes your getters, mutations, and actions accessed by, eg: 'myModule/myModularizedNumber' instead of mounting getters, mutations, and actions to the root namespace.
    namespaced: true,
    state: {
        contacts: [],
        messages:[],
        is_visible:false
    },
    actions:{
    },
    mutations:{
        toggleVisibility(state){
            state.is_visible = !state.is_visible;
        }
    }
};