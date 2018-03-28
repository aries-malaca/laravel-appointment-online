<template>
    <div class="page-quick-sidebar-wrapper" data-close-on-body-click="false" style="border-left:solid 1px #91624f">
        <a href="javascript:;" @click="toggleChat" class="page-quick-sidebar-toggler">
            <i class="icon-login"></i>
        </a>
        <div class="page-quick-sidebar">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a data-target="#quick_sidebar_tab_1" data-toggle="tab"> Messenger </a>
                </li>
                <li>
                    <a href="javascript:;" data-target="#quick_sidebar_tab_3" data-toggle="tab"> Settings </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active page-quick-sidebar-chat"
                     v-bind:class="partner !== false ?'page-quick-sidebar-content-item-shown':''" id="quick_sidebar_tab_1">
                    <contact-list/>
                    <conversation/>
                </div>
                <div class="tab-pane page-quick-sidebar-settings" id="quick_sidebar_tab_3">

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ContactList from '../chat/ContactList.vue';
    import Conversation from '../chat/Conversation.vue';

    export default {
        name:'ChatLayout',
        components:{ ContactList, Conversation },
        computed:{
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            },
            configs(){
                return this.$store.state.configs;
            },
            chat_visibility(){
                return this.$store.state.messages.chat_visibility;
            },
            partner(){
                return this.$store.state.messages.partner;
            },
            contacts(){
                return this.$store.state.messages.contacts;
            },
            branches(){
                return this.$store.state.branches.branches;
            }
        },
        methods:{
            toggleChat(){
                this.$store.commit('messages/toggleVisibility', false);
                this.$store.commit('messages/updatePartner', false);
                $("body").removeClass("page-quick-sidebar-open");
            },
            getContactList:function(){
                let u = this;
                axios.get('../../api/contact/getContactList?token=' + this.token)
                    .then(function (response) {
                        var e = response.data.map((item)=>{
                            if(item.is_client === 0){
                                if(item.level_data.dashboard === 'BranchSupervisorDashboard'){
                                    for(var x=0;x<u.branches.length;x++){
                                        if(item.user_data.branches.indexOf(u.branches[x].id) !== -1)
                                            item.branch = u.branches[x].branch_name;
                                    }
                                }
                            }
                            return item;
                        });
                        u.$store.commit('messages/updateContactList', e);
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            getUnreadMessages(){
                let u = this;
                axios.get('../../api/message/getUnreadMessages?token=' + this.token)
                    .then(function (response) {
                        response.data.forEach((i)=>{
                            u.$store.commit('messages/addUnreadMessage', i );
                        });
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            seenMessages:function(){
                let u = this;
                axios.post('/api/message/seenMessages?token=' + this.token, {sender_id:this.partner.id})
                    .then(function () {
                        u.$store.commit('messages/removeUnreadMessages', u.partner.id);
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            notifyMe:function(sender_id){
                let u = this;
                axios.get('../../api/message/getLastMessage/'+ sender_id +'?token='+this.token)
                    .then(function (response) {
                        if(response.data.message !== undefined){
                            notify(response.data.first_name, response.data.message, '../../images/users/' + response.data.user_picture, function(){
                                u.$store.commit('messages/toggleVisibility', true);

                                setTimeout(()=>{
                                    var partner = u.contacts.find((i)=>{
                                        return (i.id === sender_id)
                                    });

                                    if(partner !== undefined) {
                                        u.$store.commit('messages/updatePartner', partner);
                                        $("body").addClass("page-quick-sidebar-open");
                                    }
                                },100);

                            });
                        }
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
        },
        mounted:function(){
            let u = this;
            this.getContactList();
            this.getUnreadMessages();

            this.$options.sockets.newMessage = function(data){
                if(data.sender_id === u.partner.id)
                    u.seenMessages();

                if(data.action === 'delete')
                    u.$store.commit('messages/removeUnreadMessages', data.sender_id);
                else{
                    if((u.partner.id !== data.sender_id)&& data.recipient_id === u.user.id) {
                        u.notifyMe(data.sender_id);
                        u.$store.commit('messages/addUnreadMessage', data);
                    }
                }

            };

            $(".page-quick-sidebar-chat-users").slimScroll({height: (window.innerHeight-150) + "px"});
        },
        watch:{
            partner(){
                if(this.partner !== false && this.partner.unread > 0)
                    this.seenMessages();
            }
        }
    }
</script>