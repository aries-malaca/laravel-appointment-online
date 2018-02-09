export const products = {
    // This makes your getters, mutations, and actions accessed by, eg: 'myModule/myModularizedNumber' instead of mounting getters, mutations, and actions to the root namespace.
    namespaced: true,
    state: {
        products: [],
        groups: [],
    },
    actions:{
        fetchProducts(context){
            axios.get('/api/product/getProducts')
                .then(function (response) {
                    context.commit('updateProducts', response.data);
                })
        },
        fetchGroups(context){
            axios.get('/api/product/getProductGroups')
                .then(function (response) {
                    context.commit('updateGroups', response.data);
                })
        }
    },
    getters:{
        activeProducts(state){
            return state.products.filter(function(item){
                return item.is_active === 1;
            });
        }
    },
    mutations:{
        updateProducts(state, products){
            state.products = products;
        },
        updateGroups(state, groups){
            state.groups = groups;
        },
    }
};