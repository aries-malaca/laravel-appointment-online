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
                        <a href="#permissions" data-toggle="tab">Permissions</a>
                    </li>
                    <li>
                        <a href="#places" data-toggle="tab">Places</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <settings :token="token"></settings>
                    <levels :token="token"></levels>
                    <permissions :token="token"></permissions>
                    <places :token="token"></places>
                    <users :token="token"></users>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>
    </div>
</template>

<script>
    import VueSelect from "vue-select"
    import DataTable from './components/DataTable.vue';
    import Settings from './settings/Settings.vue';
    import Places from './settings/Places.vue';
    import Levels from './settings/Levels.vue';
    import Users from './settings/Users.vue';
    import Permissions from './settings/Permissions.vue';
    import UnauthorizedError from './errors/UnauthorizedError.vue';

    export default {
        name: 'ControlPanel',
        components:{ VueSelect, DataTable, Places, Levels, Users, Settings, UnauthorizedError},
        props: ['token','configs','user'],
        data: function(){
            return {
                title: 'Control Panel',
            }
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
        }
    }
</script>