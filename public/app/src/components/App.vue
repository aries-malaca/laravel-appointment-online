<template>
    <div id="app" v-if="user!==null">
        <!-- BEGIN HEADER -->
        <header-layout @logout="logout"></header-layout>
        <!-- END HEADER -->
        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"></div>
        <!-- END HEADER & CONTENT DIVIDER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- BEGIN SIDEBAR -->
            <sidebar-layout @logout="logout"></sidebar-layout>
            <!-- END SIDEBAR -->
            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <!-- BEGIN CONTENT BODY -->
                <div class="page-content">
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <router-view></router-view>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <!-- END CONTENT BODY -->
            </div>
            <!-- END CONTENT -->
            <!-- BEGIN QUICK SIDEBAR -->
                <chat-layout></chat-layout>
            <!-- END QUICK SIDEBAR -->
        </div>
        <!-- END CONTAINER -->
        <!-- BEGIN FOOTER -->
        <div class="page-footer" style="z-index:1000">
            <div class="page-footer-inner"> 2017 &copy; Lay-Bare Online </div>
            <div class="scroll-to-top">
                <i class="icon-arrow-up"></i>
            </div>
        </div>
        <!-- END FOOTER -->
        <div class="chat-toggler quick-sidebar-toggler" style="cursor: pointer;border-top-left-radius:20px;color:white;background-color: #91624f;" v-if="user !== null" @click="toggleChat">
            <strong><i class="icon icon-bubbles"></i>
                <span v-if="user.is_client === 0">Chat System</span>
                <span v-else>Customer Service</span>
                <span v-show="unread_messages.length >0" class="badge badge-success">{{ unread_messages.length }}</span>
            </strong>
        </div>
    </div>
</template>
<script>
    import HeaderLayout from './layouts/HeaderLayout.vue';
    import SidebarLayout from './layouts/SidebarLayout.vue';
    import ChatLayout from './layouts/ChatLayout.vue';

    export default {
        name: 'app',
        components: { HeaderLayout, SidebarLayout, ChatLayout },
        data:function(){
            return {}
        },
        computed:{
            user(){
                return this.$store.state.user;
            },
            configs(){
                return this.$store.state.configs;
            },
            token(){
                return this.$store.state.token;
            },
            title(){
                return this.$store.state.title;
            },
            chat_visibility(){
                return this.$store.state.messages.chat_visibility;
            },
            unread_messages(){
                return this.$store.state.messages.unread_messages;
            }
        },
        methods:{
            toggleChat(){
                this.$store.commit('messages/toggleVisibility', true);
                $("body").addClass("page-quick-sidebar-open");
            },
            logout:function(){
                if(this.token !== undefined){
                    axios.post('/api/user/destroyToken', { token : this.token, user_id : this.user.id})
                    .then(function () {
                        $.removeCookie('login_cookie');
                        window.location.href = '../../login';
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
                }
                else{
                    $.removeCookie('login_cookie');
                    window.location.href = '../../login';
                }
            },
        },
        mounted:function(){
            let u = this;
            this.$store.commit('updateToken', $.cookie("login_cookie"));
            this.$store.dispatch('fetchAuthenticatedUser');
            u.$store.dispatch('services/fetchServices');
            u.$store.dispatch('branches/fetchBranches');
            u.$store.dispatch('products/fetchProducts');
            u.$store.dispatch('technicians/fetchTechnicians');


            u.$options.sockets.destroyToken = function(data){
                if(data.user_id === u.user.id && u.token === data.token){
                    alert("You session has been ended by other device using your account.");
                    location.reload();
                }
            };

            //listens to all socket events
            this.$options.sockets.refreshModel = function(data){
                if(data.model === 'services')
                    u.$store.dispatch('services/fetchServices');
                if(data.model === 'branches')
                    u.$store.dispatch('branches/fetchBranches');
                if(data.model === 'products')
                    u.$store.dispatch('products/fetchProducts');
                if(data.model === 'technicians')
                    u.$store.dispatch('technicians/fetchTechnicians');
            };

            Notification.requestPermission();
        },
        watch:{
            title(){
                document.title = 'LAY-BARE Online | '+ this.title;
            },
            user(){
                if(this.user !== null){
                    if(this.user.id !== undefined) {
                        if (this.user.user_data.prompt_change_password === 1)
                            window.location.href = '../../#/profile';

                        this.$store.commit('updateTransactions', this.user.transaction_data);
                    }
                }
            }
        }
    }
</script>