<template>
    <div class="page-quick-sidebar-item">
        <div class="page-quick-sidebar-chat-user">
            <div class="page-quick-sidebar-nav">
                <a @click="hideConversation" class="page-quick-sidebar-back-to-list"><i class="icon-arrow-left"></i>
                    Back
                </a>
                <div class="btn-group  pull-right">
                    <button class="btn blue dropdown-toggle btn-xs" type="button" data-toggle="dropdown" aria-expanded="false">
                        {{ partner.first_name }} {{ partner.last_name }}
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
                <div v-bind:class="'post ' + (message.sender_id== user.id?'out':'in')" v-for="message in messages">
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
            <div class="page-quick-sidebar-chat-user-form" style="padding-top:0px;">
                <div style="padding:5px 0px 5px;font-size:11px;font-style: italic" v-bind:style="!is_typing?'color:#f2f6f9':''">
                    {{ partner.first_name }} is typing a message...
                </div>
                <div class="input-group">
                    <input type="text" id="txt" class="form-control" @keypress="keyPress($event)" v-model="newMessage.body" placeholder="Type a message here...">
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
        props:['token','user','configs','partner','messages'],
        data:function(){
            return{
                newMessage:{
                    body:''
                },
                typing_stamp: 0,
                now:50,
                timer:false
            }
        },
        methods:{
            hideConversation:function(){
                this.$emit('showConversation', false, null);
            },
            sendMessage:function(){
                let u = this;
                let $btn = $("#btn");
                $btn.button('loading');
                axios.post('/api/message/sendMessage?token=' + u.token, {body:this.newMessage.body,recipient_id:this.partner.id})
                    .then(function () {
                        u.newMessage.body = '';
                        u.$emit('refreshMessages');
                        u.$socket.emit('newMessage', u.partner.id, u.user.id);
                        $btn.button('reset');
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        XHRCatcher(error);
                        u.newMessage.body = '';
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
                        u.$emit('refreshMessages');
                        u.$socket.emit('newMessage', u.partner.id, u.user.id);
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            moment:moment
        },
        watch:{
            'partner.id':function(){
                let u = this;
                this.$options.sockets.notifyTyping = function(data){
                    if(data.recipient_id===u.user.id && this.partner.id===data.sender_id)
                        u.typing_stamp = Number(moment().format('X'));
                };
            },
            messages:function(){
                this.typing_stamp = 0;
                let u = this;
                this.timer = setInterval(function(){
                    u.now = Number(moment().format("X"));
                },200);
            },
            'newMessage.body':function(){
                this.$socket.emit('notifyTyping', this.partner.id, this.user.id);
            }
        },
        computed:{
            is_typing:function(){
                return (Number(this.now) - this.typing_stamp) < 2;
            }
        },
    }
</script>