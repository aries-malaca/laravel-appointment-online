<template>
    <div id="app">
        <!-- BEGIN HEADER -->
        <header-layout :user="user"></header-layout>
        <!-- END HEADER -->
        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"></div>
        <!-- END HEADER & CONTENT DIVIDER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- BEGIN SIDEBAR -->
            <sidebar-layout :menus="menus" :title="title"></sidebar-layout>
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
                    <router-view @update_user="getAuthenticatedUser" @update_title="updateTitle" :user="user"></router-view>
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
        <div class="page-footer">
            <div class="page-footer-inner"> 2017 &copy; Lay-Bare Online </div>
            <div class="scroll-to-top">
                <i class="icon-arrow-up"></i>
            </div>
        </div>
        <!-- END FOOTER -->
    </div>
</template>

<script>
    import HeaderLayout from './layouts/HeaderLayout.vue';
    import SidebarLayout from './layouts/SidebarLayout.vue';
    import ChatLayout from './layouts/ChatLayout.vue';

    export default {
        name: 'app',
        components: {
            HeaderLayout: HeaderLayout,
            SidebarLayout: SidebarLayout,
            ChatLayout: ChatLayout
        },
        data:function(){
            return {
                user:{},
                menus:[],
                title:'Dashboard',
                token:''
            }
        },
        methods:{
            getAuthenticatedUser:function(){
                var u = this;
                
                axios.get('/api/user/getUser?token=' + this.token)
                .then(function (response) {
                    u.user = response.data.user;
                    u.menus = response.data.menus;
                })
                .catch(function (error) {
                    XHRCatcher(error);
                });
            },
            updateTitle: function(title) {
                this.title = title;
            },
            resendConfirmation:function(event){
                var $btn = $(event.target);
                $btn.button('loading');
                var u = this;

                axios.get('/api/user/resendConfirmation?token=' + this.token)
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
            }
        },
        mounted:function(){
            this.token = $.cookie("login_cookie");
            this.getAuthenticatedUser();
        }
    }
</script>