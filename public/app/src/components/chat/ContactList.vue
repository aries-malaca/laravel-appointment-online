<template>
    <div style="background-color:#f2f6f9;padding-top:5px;">
        <div class="input-group" style="margin:5px;width: 98%;" v-if="user.is_client === 0" v-show="partner === false">
            <div class="input-icon">
                <i class="fa fa-search"></i>
                <input type="text" class="form-control" v-model="keyword" autocomplete="false" placeholder="Search ..."/>
            </div>
        </div>

        <div class="page-quick-sidebar-chat-users" data-rail-color="#e8fec7" v-show="partner === false"
             data-wrapper-class="page-quick-sidebar-list">
            <h3 class="list-heading" v-if="user.is_client === 0">Staff</h3>
            <ul class="media-list list-items">
                <li class="media" v-for="item in admins" @click="showConversation(item)"
                    v-show="keyword==='' || item.username.indexOf(keyword) !== -1" v-bind:style="item.unread > 0?'background-color:#e8fec7':''"
                    v-if="(user.is_client === 0 || item.level_data.dashboard==='CustomerServiceDashboard') && item.id!==user.id">
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
            <h3 class="list-heading" v-if="clients.length>0">Clients</h3>
            <ul class="media-list list-items">
                <li class="media" v-for="item in clients" @click="showConversation(item)" v-show="keyword==='' || item.username.indexOf(keyword) !== -1"
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
        data:function(){
            return{
                keyword:'',
            }
        },
        methods:{
            showConversation(item){
                this.$store.commit('messages/updatePartner', item);
            }
        },
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
            contacts(){
                return this.$store.state.messages.contacts;
            },
            partner(){
                return this.$store.state.messages.partner;
            },
            admins(){
                let u = this;
                return this.contacts.filter((item)=>{
                    return (item.is_client === 0)
                }).map((item)=>{
                    item.unread = u.unread_messages.filter((i)=>{
                        return i.sender_id === item.id;
                    }).length;
                    return item;
                });
            },
            clients(){
                let u = this;
                return this.contacts.filter((item)=>{
                    return (item.is_client === 1)
                }).map((item)=>{
                    item.unread = u.unread_messages.filter((i)=>{
                        return i.sender_id === item.id;
                    }).length;
                    return item;
                });
            },
            unread_messages(){
                return this.$store.state.messages.unread_messages;
            }
        }
    }
</script>