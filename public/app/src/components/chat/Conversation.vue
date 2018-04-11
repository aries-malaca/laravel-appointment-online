<template>
    <div class="page-quick-sidebar-item">
        <div class="page-quick-sidebar-chat-user">
            <div class="page-quick-sidebar-nav">
                <a @click="hideConversation" class="page-quick-sidebar-back-to-list"><i class="icon-arrow-left"></i>
                    Back
                </a>
                <div class="btn-group  pull-right">
                    <button class="btn blue dropdown-toggle btn-xs" type="button" data-toggle="dropdown" aria-expanded="false">
                        <span v-if="partner.branch !== undefined"> {{ partner.branch }} </span>
                        <span v-else> {{ partner.first_name }} {{ partner.last_name }} </span>
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li v-if="partner.is_client == 1">
                            <a v-bind:href="'../../#/clients/'+partner.id"> View Profile </a>
                        </li>
                        <li>
                            <a @click="deleteConversation"> Delete Conversation </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="page-quick-sidebar-chat-user-messages">
                <small v-if="is_loading" style="text-align:center">Loading Messages...</small>
                <div v-bind:class="'post ' + (message.sender_id== user.id?'out':'in')" v-for="message,key in messages" :id="'message-' + key">
                    <img class="avatar" alt="" v-bind:src="'../../images/users/'+ (message.sender_id== user.id?user.user_picture:partner.user_picture)" />
                    <div class="message">
                        <span class="arrow"></span>
                        <a href="javascript:;" class="name">
                            {{ (message.sender_id == user.id?user.first_name + ' ' + user.last_name:partner.first_name +' '+partner.last_name) }}
                        </a><br/>
                        <span class="datetime" style="font-weight:normal;font-size:10px"> ({{ moment(message.created_at).format("MM/DD/YYYY hh:mm A") }}) </span>
                        <span class="body"> {{ message.body }}</span>
                    </div>
                </div>
            </div>
            <div class="page-quick-sidebar-chat-user-form" style="position: fixed;bottom: 0px;">
                <div style="padding:5px 0px 5px;font-size:11px;font-style: italic" v-bind:style="!is_typing?'color:#f2f6f9':''">
                    {{ partner.first_name }} is typing a message...
                </div>
                <div class="input-group">
                    <input type="text" id="txt" class="form-control" @keypress="keyPress($event)" v-model="newMessage.body"
                           placeholder="Type a message here...">
                    <div class="input-group-btn">
                        <button type="button" id="btn" class="btn green" @click="sendMessage" data-loading-text="Sending...">
                            <i class="fa fa-send"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        name:'Conversation',
        data:function(){
            return{
                newMessage:{
                    body:''
                },
                typing_stamp: 50,
                now:50,
                timer:false,
                limit:10,
                is_loading:false
            }
        },
        methods:{
            hideConversation:function(){
                this.$store.commit('messages/updatePartner', false);
                this.$store.commit('messages/updateMessages', []);
            },
            getMessages(latest){
                $(".page-quick-sidebar-chat-users").slimScroll({destroy: true});
                $(".page-quick-sidebar-chat-user-messages").slimScroll({height: (window.innerHeight-170) + "px"});

                let u = this;
                axios.get('../../api/message/getConversation/'+ this.partner.id +'/' + this.limit +'?token='+this.token )
                    .then(function (response) {
                        u.$store.commit('messages/updateMessages', response.data.messages);
                        u.$store.commit('messages/updateLastID', response.data.last_id);

                        $(".page-quick-sidebar-chat-users").slimScroll({destroy: true});

                        $(".page-quick-sidebar-chat-user-messages").slimScroll({height: (window.innerHeight-170) + "px"});
                        if(latest)
                            setTimeout(()=>{
                                $(".page-quick-sidebar-chat-user-messages").slimScroll({height:  (window.innerHeight-170) + "px", scrollTo: "1000000px"});
                                u.is_loading = false;
                            },100);
                        else
                            setTimeout(()=>{
                                $(".page-quick-sidebar-chat-user-messages").slimScroll({height:  (window.innerHeight-170) + "px", scrollTo: "1px"});
                                u.is_loading = false;
                            },100);

                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            sendMessage:function(){
                let u = this;
                let $btn = $("#btn");
                $btn.button('loading');
                axios.post('/api/message/sendMessage?token=' + u.token, {body:this.newMessage.body,recipient_id:this.partner.id})
                    .then(function () {
                        u.newMessage.body = '';
                        u.getMessages(true);
                        u.$socket.emit('newMessage', u.partner.id, u.user.id);
                        $btn.button('reset');
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        u.newMessage.body = '';
                        XHRCatcher(error);
                    });
            },
            keyPress:function(event){
                if(event.keyCode === 13)
                    this.sendMessage();
            },
            deleteConversation:function(){
                let u = this;
                axios.post('/api/message/deleteConversation?token=' + this.token, {recipient_id:this.partner.id})
                    .then(function () {
                        u.getMessages(true);
                        u.$socket.emit('newMessage', u.partner.id, u.user.id, 'delete');
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            }
        },
        mounted(){
            let u = this;
            this.$options.sockets.notifyTyping = function(data){
                if(data.recipient_id===u.user.id && this.partner.id===data.sender_id) {
                    u.typing_stamp = Number(moment().format('X'));
                    setTimeout(()=>{
                        u.typing_stamp = 50;
                    },300);
                }
            };

            this.$options.sockets.newMessage = function(data){
                if(data.recipient_id === u.user.id)
                    u.getMessages(true);
            };

        },
        watch:{
            'newMessage.body':function(){
                this.$socket.emit('notifyTyping', this.partner.id, this.user.id);
            },
            partner(){
                this.limit = 10;

                if(this.partner !== false)
                    this.getMessages(true);
                else
                    $(".page-quick-sidebar-chat-users").slimScroll({height: (window.innerHeight-150) + "px"});

                document.getElementById("txt").focus();
            },
            messages(){
                let u = this;

                if(u.partner !== false)
                    $('.page-quick-sidebar-chat-user-messages').slimScroll().bind('slimscrolling', function (e, pos) {
                        if(u.messages.length > 0)
                            if(!u.is_loading && pos ===0 && u.messages[0].id !== u.last_id){
                                u.limit +=10;
                                u.is_loading = true;
                                u.getMessages();
                            }
                    });
            }
        },
        computed:{
            is_typing:function(){
                return (Number(this.now) !== this.typing_stamp) ;
            },
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            },
            configs(){
                return this.$store.state.configs;
            },
            partner(){
                return this.$store.state.messages.partner;
            },
            messages(){
                let u = this;
                return this.$store.state.messages.messages.sort((a,b)=>{
                    return (a.created_at > b.created_at?1:-1);
                })
            },
            last_id(){
                return this.$store.state.messages.last_id;
            }
        },
    }
</script>