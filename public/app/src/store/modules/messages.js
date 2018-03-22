export const messages = {
    // This makes your getters, mutations, and actions accessed by, eg: 'myModule/myModularizedNumber' instead of mounting getters, mutations, and actions to the root namespace.
    namespaced: true,
    state: {
        contacts: [],
        messages:[],
        partner:false,
        chat_visibility:false,
        unread_messages:[],
    },
    mutations:{
        toggleVisibility(state, s){
            state.chat_visibility = s;
        },
        updateMessages(state, messages){
            state.messages = messages;
        },
        updatePartner(state, partner){
            state.partner = partner;
        },
        updatePartnerByID(state, partnerID){
            state.partner = state.contacts.find((d)=>{
                if(d.id === partnerID)
                    return d;
            });
        },
        updateContactList(state, contacts){
            state.contacts = contacts;
        },
        addUnreadMessage(state, message){
            state.unread_messages.push(message);
        },
        removeUnreadMessages(state, sender_id){
            var s = [];
            for(var x=0;x<state.unread_messages.length;x++){
                if(state.unread_messages[x].sender_id !== sender_id)
                    s.push(state.unread_messages[x]);
            }

            state.unread_messages = s;
        }
    }
};