<template>
    <div class="control_panel">
        <div class="portlet light" v-if="user.is_client !== 1">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#settings" data-toggle="tab">System Settings</a>
                    </li>
                    <li>
                        <a href="#users" data-toggle="tab">Users</a>
                    </li>
                    <li>
                        <a href="#user-levels" data-toggle="tab">User Levels</a>
                    </li>
                    <li>
                        <a href="#places" data-toggle="tab">Places</a>
                    </li>
                    <li>
                        <a href="#database" data-toggle="tab">Database</a>
                    </li>
                    <li>
                        <a href="#logs" data-toggle="tab">Logs</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <settings></settings>
                    <levels></levels>
                    <places></places>
                    <users></users>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>
    </div>
</template>

<script>
    import VueSelect from "vue-select"
    import DataTable from './tables/DataTable.vue';
    import Settings from './controlpanel/Settings.vue';
    import Places from './controlpanel/Places.vue';
    import Levels from './controlpanel/Levels.vue';
    import Users from './controlpanel/Users.vue';
    import UnauthorizedError from './errors/UnauthorizedError.vue';

    export default {
        name: 'ControlPanel',
        components:{ VueSelect, DataTable, Places, Levels, Users, Settings, UnauthorizedError},
        data: function(){
            return {
                title: 'Control Panel',
            }
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'Control Panel');
        },
        computed:{
            user(){
                return this.$store.state.user;
            },
        }
    }
</script>