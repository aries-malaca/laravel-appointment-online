<template>
    <div style="background-color:#f2f6f9;padding-top:5px;">

        <div class="input-group" style="margin:5px;width: 98%;" v-if="user.is_client === 0" v-show="show_search">
            <div class="input-icon">
                <i class="fa fa-search"></i>
                <input type="text" class="form-control" v-model="keyword" placeholder="Search ..."/>
            </div>
            <div class="input-group-btn">
                <button type="button" class="btn yellow dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-cog"></i>
                    <i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu pull-right">
                    <li>
                        <a href="javascript:;"> Action </a>
                    </li>
                    <li>
                        <a href="javascript:;"> Another action </a>
                    </li>
                </ul>
            </div>
            <!-- /btn-group -->
        </div>

        <div class="page-quick-sidebar-chat-users" data-rail-color="#e8fec7" data-wrapper-class="page-quick-sidebar-list">
            <h3 class="list-heading" v-if="user.is_client === 0">Staff</h3>
            <ul class="media-list list-items">
                <li class="media" v-for="item in filtered_admins" @click="showConversation(item)"
                        v-bind:style="item.unread > 0?'background-color:#e8fec7':''" v-if="item.id!==user.id">
                    <div class="media-status">
                        <span class="badge badge-success" v-if="item.is_online">Online</span>
                        <span class="badge badge-info" v-if="item.unread > 0">{{ item.unread }}</span>
                    </div>
                    <img class="media-object" v-bind:src="'../../images/users/'+item.user_picture" alt="...">
                    <div class="media-body">
                        <h4 class="media-heading">{{ item.first_name }} {{ item.last_name }}</h4>
                        <div class="media-heading-sub">{{ item.level_name }}</div>
                        <div class="media-heading-small" v-if="!item.is_online && item.last_activity !== null">last seen {{ moment(item.last_activity).fromNow() }}</div>
                    </div>
                </li>
            </ul>
            <h3 class="list-heading" v-if="filtered_clients.length>0">Clients</h3>
            <ul class="media-list list-items">
                <li class="media" v-for="item in filtered_clients" @click="showConversation(item)"
                    v-bind:style="item.unread > 0?'background-color:#e8fec7':''" v-if="item.id!==user.id">
                    <div class="media-status">
                        <span class="badge badge-success" v-if="item.is_online">Online</span>
                        <span class="badge badge-info" v-if="item.unread > 0">{{ item.unread }}</span>
                    </div>
                    <img class="media-object" v-bind:src="'../../images/users/'+item.user_picture" alt="...">
                    <div class="media-body">
                        <h4 class="media-heading">{{ item.first_name }} {{ item.last_name }}</h4>
                        <div class="media-heading-sub">{{ item.level_name }}</div>
                        <div class="media-heading-small" v-if="!item.is_online && item.last_activity !== null">{{ moment(item.last_activity).fromNow() }}</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
    export default{
        name:'ContactList',
        props:['token','user','configs','show_search'],
        data:function(){
            return{
                users:[],
                keyword:'',
            }
        },
        methods:{
            refreshUnseen:function(){
                this.$emit('refreshUnseen', this.countUnseen());
            },
            showConversation:function(user){
                this.$emit('showConversation', true, user);
            },
            refreshContactBadge:function(id){
                let u = this;

                axios.get('../../api/message/countUnseenMessages/'+id+'?token=' + this.token)
                    .then(function (response) {
                        for(var x=0;x<u.users.length;x++){
                            if(id === u.users[x].id)
                                u.users[x].unread = response.data.count;
                        }
                        u.refreshUnseen();
                    });
            },
            getContactList:function(){
                let u = this;
                axios.get('../../api/message/getContactList?token=' + this.token)
                    .then(function (response) {
                        u.users = response.data;
                        u.refreshUnseen();
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            countUnseen:function(){
                var count = 0;
                for(var x=0;x<this.users.length;x++){
                    count+=this.users[x].unread;
                }
                return count;
            },
            moment:moment
        },
        watch:{
            user:function(){
                let u = this;
                //this.getContactList();

                this.$options.sockets.newMessage = function(data){
                    if(data.recipient_id===u.user.id){
                        u.refreshContactBadge(data.sender_id);
                        //u.getContactList();
                    }
                };

                this.$options.sockets.refreshContacts = function(data){
                    u.getContactList();
                };
            },
            show_search:function(){
               //if(this.show_search)
                    //this.getContactList();
            }
        },
        computed:{
            filtered_admins:function(){
                var u = this;
                return this.users.filter(function(user){
                    return (user.first_name.toLowerCase().search(u.keyword.toLowerCase()) !== -1 || user.last_name.toLowerCase().search(u.keyword.toLowerCase()) !== -1) &&
                                user.is_client === 0
                });
            },
            filtered_clients:function(){
                var u = this;
                return this.users.filter(function(user){
                    return (user.first_name.toLowerCase().search(u.keyword.toLowerCase()) !== -1 || user.last_name.toLowerCase().search(u.keyword.toLowerCase()) !== -1) &&
                        user.is_client === 1
                });
            }
        }
    }
</script>