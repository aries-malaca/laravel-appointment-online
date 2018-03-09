<template>
    <div class="page-header navbar navbar-fixed-top" style="z-index:1000">
        <!-- BEGIN HEADER INNER -->
        <div class="page-header-inner ">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <router-link to="/dashboard" >
                    <img v-bind:src="'/logo.png'" alt="logo" class="logo-default" style="height:60px;margin:10px">
                </router-link>
                <div class="menu-toggler sidebar-toggler">
                    <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
                </div>
            </div>
            <!-- END LOGO -->
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
            <!-- END RESPONSIVE MENU TOGGLER -->
            <!-- BEGIN PAGE TOP -->
            <div class="page-top">
                <!-- BEGIN TOP NAVIGATION MENU -->
                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">
                        <li class="separator hide"> </li>
                        <!-- BEGIN NOTIFICATION DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <!-- DOC: Apply "dropdown-hoverable" class after "dropdown" and remove data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to enable hover dropdown mode -->
                        <!-- DOC: Remove "dropdown-hoverable" and add data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to the below A element with dropdown-toggle class -->
                        <li class="dropdown dropdown-extended dropdown-notification dropdown-dark" id="header_notification_bar">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                               @click="seenNotifications" data-close-others="true">
                                <i class="icon-bell"></i>
                                <span v-if="unread_notifications.length > 0" class="badge badge-success"> {{ unread_notifications.length }} </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="external">
                                    <h3>
                                        <span v-if="unread_notifications.length > 0" class="bold">{{ unread_notifications.length }} unread notifications </span>
                                    </h3>
                                    <router-link to="/notifications">view all</router-link>
                                </li>
                                <li>
                                    <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
                                        <li v-for="notification in notifications" :style="notification.is_read===0?'background-color: papayawhip;':''">
                                            <a href="javascript:;">
                                                <span class="time" style="background: inherit">{{ moment(notification.created_at).fromNow() }}</span>
                                                <span class="details">
                                            <span class="label label-sm label-icon label-info">
                                                <i class="fa fa-bullhorn"></i>
                                            </span>{{ notification.title }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <!-- END NOTIFICATION DROPDOWN -->

                        <!-- BEGIN USER LOGIN DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <li class="dropdown dropdown-user dropdown-dark">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <span class="username username-hide-on-mobile"> {{ user.username }}</span>
                                <!-- DOC: Do not remove below empty space(&nbsp;) as its purposely used -->
                                <img v-if="user.user_picture!==undefined" alt="" class="img-circle" v-bind:src="'/images/users/' + user.user_picture"/> </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <li>
                                    <router-link to="/profile">
                                        <i class="icon-user"></i> My Profile
                                    </router-link>
                                </li>
                                <li class="divider"> </li>
                                <li>
                                    <a @click="logout">
                                        <i class="icon-key"></i> Log Out </a>
                                </li>
                            </ul>
                        </li>
                        <!-- END USER LOGIN DROPDOWN -->

                    </ul>
                </div>
                <!-- END TOP NAVIGATION MENU -->
            </div>
            <!-- END PAGE TOP -->
        </div>
        <!-- END HEADER INNER -->
    </div>
</template>

<script>
    export default {
        name:'HeaderLayout',
        computed:{
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            },
            notifications(){
                return this.$store.state.notifications.notifications;
            },
            unread_notifications(){
                return this.$store.getters['notifications/unread_notifications'];
            }
        },
        methods:{
            logout: function() {
                this.$emit('logout')
            },
            seenNotifications(){
                let u = this;
                if(this.unreadCount>0)
                    setTimeout(()=>{
                        axios.get('/api/notification/seenNotifications?token=' + this.token)
                            .then(function () {
                                u.getNotifications();
                            })
                    }, 1000);
            },
            getNotifications(){
                let u = this;
                axios.get('/api/notification/getUserNotifications?token=' + this.token)
                    .then(function (response) {
                        u.$store.commit('notifications/updateNotifications', response.data);
                    })
            },
            moment:moment
        },
        mounted(){
            let u = this;

            this.getNotifications();
            this.$options.sockets.refreshNotifications = function(data){
                if(data.client_id === u.id)
                    u.getNotifications();
            };
        }
    }
</script>