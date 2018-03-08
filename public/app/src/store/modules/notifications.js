export const notifications = {
    // This makes your getters, mutations, and actions accessed by, eg: 'myModule/myModularizedNumber' instead of mounting getters, mutations, and actions to the root namespace.
    namespaced: true,
    state: {
        notifications: [],
    },
    actions:{
        fetchNotifications(context){
            axios.get('/api/product/getProducts')
                .then(function (response) {
                    context.commit('updateProducts', response.data);
                })
        },
    },
    mutations:{
    }
};