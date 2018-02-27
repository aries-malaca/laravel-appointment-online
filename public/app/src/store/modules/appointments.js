export const appointments = {
    // This makes your getters, mutations, and actions accessed by, eg: 'myModule/myModularizedNumber' instead of mounting getters, mutations, and actions to the root namespace.
    namespaced: true,
    state: {
        active_appointments: [],
        appointment_history: [],
        viewing_id:0,
    },
    actions:{

    },
    getters:{
        //returns the object of the appointment with pending acknowledgement
        needsToAcknowledge(state){
            return state.appointment_history.find((appointment)=>{
                if(appointment.acknowledgement_data.signature===null){
                    return appointment;
                }
            });
        }
    },
    mutations:{
        updateActiveAppointments(state, appointments){
            state.active_appointments = appointments;
        },
        updateViewingID(state, id){
            state.viewing_id = id;
        },
        updateAppointmentHistory(state, appointments){
            state.appointment_history = appointments;
        },
    }
};