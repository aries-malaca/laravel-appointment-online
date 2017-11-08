<template>
    <div class="page-quick-sidebar-item">
        <div class="page-quick-sidebar-chat-user">
            <div class="page-quick-sidebar-nav">
                <a @click="hideConversation" class="page-quick-sidebar-back-to-list"><i class="icon-arrow-left"></i>Back</a>
                <div class="btn-group  pull-right">
                    <button class="btn blue dropdown-toggle btn-xs" type="button" data-toggle="dropdown" aria-expanded="false">
                        Options
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu">
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
            <div class="page-quick-sidebar-chat-user-form">
                <div class="input-group">
                    <input type="text" id="txt" class="form-control" @keypress="pressEnter($event)" v-model="newMessage.body" placeholder="Type a message here...">
                    <div class="input-group-btn">
                        <button type="button" id="btn" class="btn green" @click="sendMessage">
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
                    body:'',
                    recipient_id:undefined
                }
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
            pressEnter:function(event){
                if(event.keyCode === 13){
                    this.sendMessage();
                }
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
        }
    }
</script>