<template>
    <div style="background-color:#f2f6f9;padding-top:5px;">
        <div class="input-group" style="margin:5px;width: 98%;" v-if="user.is_client === 0" v-show="partner === false">
            <div class="input-icon">
                <vue-select :on-search="searchClients" :options="client_selection"
                            placeholder="Search for Client..." v-model="selected_client" />
            </div>
        </div>
        <div class="page-quick-sidebar-chat-users" data-rail-color="#e8fec7" v-show="partner === false"
             data-wrapper-class="page-quick-sidebar-list">
            <h3 class="list-heading" v-if="clients.length>0">Clients</h3>
            <ul class="media-list list-items">
                <li class="media" v-for="item in clients" @click="showConversation(item)" v-show="keyword==='' || item.username.toLowerCase().indexOf(keyword.toLowerCase()) !== -1"
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
            <h3 class="list-heading" v-if="user.is_client === 0">Staff</h3>
            <ul class="media-list list-items">
                <li class="media" v-for="item in admins" @click="showConversation(item)"
                    v-show="keyword==='' || item.username.toLowerCase().indexOf(keyword.toLowerCase()) !== -1" v-bind:style="item.unread > 0?'background-color:#e8fec7':''"
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
        </div>
    </div>
</template>
<script>
    import VueSelect from 'vue-select';
    export default{
        name:'ContactList',
        components:{ VueSelect },
        data:function(){
            return{
                keyword:'',
                selected_client:null,
                remote_clients:[]
            }
        },
        methods:{
            showConversation(item){
                this.$store.commit('messages/updatePartner', item);
            },
            searchClients:function(keyword,loading){
                loading(true);
                let u = this;
                axios.get('/api/client/searchClients', {params:{keyword:keyword}})
                    .then(function (response) {
                        u.remote_clients = [];
                        response.data.forEach(function(item){
                            u.remote_clients.push(item);
                        });
                        loading(false);
                    });
            },
        },
        watch: {
            selected_client(){
                if(this.selected_client.id !== undefined)
                    this.$store.commit('messages/updatePartner', this.selected_client)
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
            client_selection:function(){
                var remote_clients=[];
                for(var x=0;x<this.remote_clients.length;x++){
                    remote_clients.push({  label:this.remote_clients[x].username,
                        id:this.remote_clients[x].id,
                        first_name:this.remote_clients[x].first_name,
                        last_name:this.remote_clients[x].last_name,
                        username:this.remote_clients[x].username,
                        is_client:this.remote_clients[x].is_client,
                        last_activity:this.remote_clients[x].last_activity,
                        level_data:this.remote_clients[x].level_data,
                        level_name:this.remote_clients[x].level_name,
                        unread:this.remote_clients[x].unread,
                        user_data:this.remote_clients[x].user_data,
                        user_picture:this.remote_clients[x].user_picture,
                    });
                }
                return remote_clients;
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