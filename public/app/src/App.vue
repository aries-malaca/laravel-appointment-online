<template>
    <div id="app" v-if="token!==undefined">
        <!-- BEGIN HEADER -->
        <header-layout @logout="logout" :user="user"></header-layout>
        <!-- END HEADER -->
        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"></div>
        <!-- END HEADER & CONTENT DIVIDER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- BEGIN SIDEBAR -->
            <sidebar-layout @logout="logout" :menus="menus" :title="title"></sidebar-layout>
            <!-- END SIDEBAR -->
            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <!-- BEGIN CONTENT BODY -->
                <div class="page-content">

                    <div class="alert alert-danger" v-if="user.is_confirmed != 1 && user.username !== undefined">
                        <strong>Important!</strong> Please verify your email address first to book an appointment or access your transactions. &nbsp;
                        <button data-loading-text="Sending..." class="btn btn-success btn-xs" @click="resendConfirmation($event)"> Resend Email </button> 
                    </div>

                    <!-- BEGIN PAGE BASE CONTENT -->
                    <router-view @update_user="getAuthenticatedUser" :transactions="transactions" @update_title="updateTitle" :user="user" :token="token" :configs="configs"></router-view>
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
        <div class="chat-toggler quick-sidebar-toggler" style="cursor: pointer">
            <strong><i class="icon icon-bubbles"></i> Chat System</strong>
        </div>
    </div>
</template>

<style>
    .chat-toggler{
        position:fixed;
        right:0px;
        bottom: 0px;
        z-index:1001;
        display: block;
        background-color: white;
        padding: 10px 30px;
    }
</style>

<script>
    import HeaderLayout from './layouts/HeaderLayout.vue';
    import SidebarLayout from './layouts/SidebarLayout.vue';
    import ChatLayout from './layouts/ChatLayout.vue';

    export default {
        name: 'app',
        components: { HeaderLayout, SidebarLayout, ChatLayout },
        data:function(){
            return {
                user:{},
                menus:[],
                configs:[],
                title:'Dashboard',
                token:undefined,
                transactions:[]
            }
        },
        methods:{
            getAuthenticatedUser:function(){
                var u = this;
                axios.get('/api/user/getUser?token=' + this.token)
                .then(function (response) {
                    u.user = response.data.user;
                    u.menus = response.data.menus;
                    u.configs = response.data.configs;
                    u.getBossTransactions();
                })
                .catch(function (error) {
                    XHRCatcher(error);
                });
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
                    axios.patch('/api/user/destroyToken', { token : this.token, user_id : this.user.id})
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
            updateTitle: function(title) {
                this.title = title;
                document.title = 'LAY-BARE Online | '+ title;
            },
            getBossTransactions:function(){
                if(this.user.is_client !== 1)
                    return false;

                let u = this;
                if(this.configs.FETCH_BOSS_TRANSACTIONS === undefined && this.user.is_client === 1)
                    return false;
                axios.get(this.configs.FETCH_BOSS_TRANSACTIONS +""+ this.user.email)
                    .then(function (response) {
                        u.transactions = response.data;
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            }
        },
        mounted:function(){
            this.token = $.cookie("login_cookie");
            if(this.token === undefined)
                this.logout();

            this.$options.sockets.broadcast = function(count){
                console.log(count);
            };
        }
    }
</script>