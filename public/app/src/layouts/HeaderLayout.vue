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
                        <!-- BEGIN INBOX DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <li class="dropdown dropdown-extended dropdown-inbox dropdown-dark" id="header_inbox_bar">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="icon-envelope-open"></i>
                                <span class="badge badge-danger"> 4 </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="external">
                                    <h3>You have
                                        <span class="bold">7 New</span> Messages</h3>
                                    <router-link to="/messages">view all</router-link>
                                </li>
                                <li>
                                    <ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                                        <li>
                                            <a href="#">
                                        <span class="photo">
                                            <img src="/images/users/Registration.png" class="img-circle" alt=""> </span>
                                                <span class="subject">
                                            <span class="from"> Lisa Wong </span>
                                            <span class="time">Just Now </span>
                                        </span>
                                                <span class="message"> Vivamus sed auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <!-- END INBOX DROPDOWN -->
                        <li class="separator hide"> </li>
                        <!-- BEGIN NOTIFICATION DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <!-- DOC: Apply "dropdown-hoverable" class after "dropdown" and remove data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to enable hover dropdown mode -->
                        <!-- DOC: Remove "dropdown-hoverable" and add data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to the below A element with dropdown-toggle class -->
                        <li class="dropdown dropdown-extended dropdown-notification dropdown-dark" id="header_notification_bar">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="icon-bell"></i>
                                <span class="badge badge-success"> 7 </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="external">
                                    <h3>
                                        <span class="bold">12 pending</span> notifications</h3>
                                    <router-link to="/notifications">view all</router-link>
                                </li>
                                <li>
                                    <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
                                        <li>
                                            <a href="javascript:;">
                                                <span class="time">14 hrs</span>
                                                <span class="details">
                                            <span class="label label-sm label-icon label-info">
                                                <i class="fa fa-bullhorn"></i>
                                            </span> Application error. </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <span class="time">2 days</span>
                                                <span class="details">
                                            <span class="label label-sm label-icon label-danger">
                                                <i class="fa fa-bolt"></i>
                                            </span> Database overloaded 68%. </span>
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
        props:['user'],
        methods:{
            logout: function() {
                this.$emit('logout')
            }
        }
    }
</script>