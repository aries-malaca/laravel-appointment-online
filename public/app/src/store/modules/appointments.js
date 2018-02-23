export const appointments = {
    // This makes your getters, mutations, and actions accessed by, eg: 'myModule/myModularizedNumber' instead of mounting getters, mutations, and actions to the root namespace.
    namespaced: true,
    state: {
        active_appointments: [],
        appointment_history: [],
    },
    actions:{

    },
    getters:{

    },
    mutations:{
        updateActiveAppointments(state, appointments){
            state.active_appointments = appointments;
        },
    }
};