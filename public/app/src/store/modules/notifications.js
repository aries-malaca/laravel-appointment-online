export const notifications = {
    // This makes your getters, mutations, and actions accessed by, eg: 'myModule/myModularizedNumber' instead of mounting getters, mutations, and actions to the root namespace.
    namespaced: true,
    state: {
        notifications: [],
    },
    actions:{

    },
    getters:{
        unread_notifications(state){
            return state.notifications.filter((item)=>{
                return item.is_read === 0;
            });
        }
    },
    mutations:{
        updateNotifications(state, notifications){
            state.notifications = notifications;
        }
    }
};