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
                    <div class="alert alert-danger" v-if="user.is_confirmed != 1 && user.username !== undefined">
                        <strong>Important!</strong>
                        Please verify your email address first to book an appointment or access your transactions. &nbsp;
                        <button data-loading-text="Sending..." class="btn btn-success btn-xs" @click="resendConfirmation($event)"> Resend Email </button> 
                    </div>
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
            resendConfirmation:function(event){
                var $btn = $(event.target);
                $btn.button('loading');
                var u = this;

                axios.get('/api/user/sendConfirmation?token=' + this.token)
                .then(function (response) {
                    if(response.data.result == 'success'){
                        toastr.success("Email sent! check your email to verify your account.");
                        u.user.is_confirmed = 1;
                        $btn.button('reset');
                    }
                })
                .catch(function (error) {
                    XHRCatcher(error);
                    $btn.button('reset');
                });    
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

            setTimeout(()=>{
                u.$options.sockets.pingUsers = function(){
                    u.$socket.emit('pongUsers', {id:u.$store.state.user.id, is_client:u.$store.state.user.is_client, platform:'web'});
                };
            },5000);

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

            setTimeout(function(){
                if(u.user !== null){

                    u.$store.dispatch('saveLocation');

                    if(u.user.is_client !== 1)
                        return false;

                    if(u.configs.FETCH_BOSS_TRANSACTIONS === undefined && u.user.is_client === 1)
                        return false;

                    axios.get(u.configs.FETCH_BOSS_TRANSACTIONS +""+ u.user.email)
                        .then(function (response) {
                            u.$store.commit('updateTransactions', response.data);
                        })
                        .catch(function (error) {
                        });
                }
            },3000);
        },
        watch:{
            title(){
                document.title = 'LAY-BARE Online | '+ this.title;
            },
        }
    }
</script>