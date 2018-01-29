<template>
    <div class="page-quick-sidebar-wrapper" data-close-on-body-click="false" style="border-left:solid 1px #91624f">
        <a href="javascript:;" @click="show_conversation=false" class="page-quick-sidebar-toggler">
            <i class="icon-login"></i>
        </a>
        <div class="page-quick-sidebar">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a @click="show_conversation = false" data-target="#quick_sidebar_tab_1" data-toggle="tab"> Messenger </a>
                </li>
                <li>
                    <a href="javascript:;" data-target="#quick_sidebar_tab_3" data-toggle="tab"> Settings </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active page-quick-sidebar-chat"
                     v-bind:class="show_conversation?'page-quick-sidebar-content-item-shown':''" id="quick_sidebar_tab_1">
                    <contact-list :configs="configs" @showConversation="showConversation" :show_search="!show_conversation"
                              :user="user" :token="token" @refreshUnseen="refreshUnseen"/>

                    <conversation :configs="configs" @showConversation="showConversation" @refreshMessages="getMessages"
                                  :user="user" :token="token" :messages="messages" :partner="partner"/>
                </div>
                <div class="tab-pane page-quick-sidebar-settings" id="quick_sidebar_tab_3">

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ContactList from '../components/ContactList.vue';
    import Conversation from '../components/Conversation.vue';

    export default {
        name:'ChatLayout',
        components:{ ContactList, Conversation },
        props:['user','token','configs'],
        data:function(){
            return {
                show_conversation:false,
                partner:{},
                messages:[],
                unseen_messages:0
            }
        },
        methods:{
            notifyMe:function(sender_id){
                let u = this;
                axios.get('../../api/message/getLastMessage/'+ sender_id +'?token='+this.token)
                    .then(function (response) {
                        if(response.data.message !== undefined){
                            let e = response;
                            notify(response.data.first_name, response.data.message, '../../images/users/' + response.data.user_picture, function(){
                                u.showConversation(true, e.data);
                            });
                        }
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            refreshUnseen:function(count){
                this.$emit('refreshUnseen', count);
            },
            showConversation:function(value, user){
                this.show_conversation = value;

                if(value){
                    this.partner = user;
                    this.seenMessages();
                }
            },
            getMessages:function(){
                if(this.partner.id === undefined || !this.show_conversation){
                    return false;
                }
                let u = this;
                axios.get('../../api/message/getConversation/'+ this.partner.id +'?token='+this.token)
                    .then(function (response) {
                        u.messages = response.data;
                        $(".page-quick-sidebar-chat-user-messages").slimScroll({scrollTo: "1000000px"});
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            seenMessages:function(){
                axios.patch('/api/message/seenMessages?token=' + this.token, {sender_id:this.partner.id})
                    .then(function () {
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            }
        },
        watch:{
            'partner':function(){
                this.messages = [];
                this.getMessages();
            }
        },
        mounted:function(){
            let u = this;
            this.$options.sockets.newMessage = function(data){
                if(data.recipient_id === u.user.id)
                    u.getMessages();

                if(data.sender_id === u.partner.id && u.show_conversation)
                    u.seenMessages();

                if((u.partner.id !== data.sender_id || !u.show_conversation )&& data.recipient_id === u.user.id)
                    u.notifyMe(data.sender_id);
            };
        }
    }
</script>