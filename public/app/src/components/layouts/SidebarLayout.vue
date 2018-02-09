<template>
    <div class="page-sidebar-wrapper">
        <!-- BEGIN SIDEBAR -->
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <div class="page-sidebar navbar-collapse collapse">
            <br/><br/>
            <!-- BEGIN SIDEBAR MENU -->
            <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
            <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
            <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
            <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
            <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
            <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
            <ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true"
                    data-slide-speed="200" v-if="user.level_data !== undefined || user.is_client === 1">
                <li class="nav-item start" v-bind:class="{ active: (title=='Dashboard') }">
                    <router-link to="/dashboard" class="nav-link">
                        <i class="icon-home"></i>
                        <span class="title">Dashboard</span>
                    </router-link>
                </li>
                
                <li class="nav-item" v-for="menu in menus" v-bind:class="{ active: (title==menu.title) }" v-if="gate(user.level_data.permissions, menu.url, 'view') || user.is_client === 1 || menu.title === 'Appointments'">
                    <router-link v-bind:to="'/'+ menu.url" class="nav-link">
                        <i v-bind:class="menu.icon"></i>
                        <span class="title">{{  menu.title }}</span>
                    </router-link>
                </li>

                <li class="nav-item">
                    <a @click="logout" class="nav-link">
                        <i class="fa fa-sign-out"></i>
                        <span class="title">Logout</span>
                    </a>
                </li>
            </ul>
            <!-- END SIDEBAR MENU -->
        </div>
        <!-- END SIDEBAR -->
    </div>
</template>

<script>
    export default {
        name:'SidebarLayout',
        computed:{
            user(){
                return this.$store.state.user;
            },
            menus(){
                return this.$store.state.menus;
            },
            title(){
                return this.$store.state.title;
            }
        },
        methods:{
            logout: function() {
                this.$emit('logout')
            },
            gate:gate
        }
    }
</script>