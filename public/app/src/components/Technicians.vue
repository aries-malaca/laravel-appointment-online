<template>
    <div class="technicians">
        <div class="portlet light" v-show="view === false"  v-if="user.is_client !== 1 && gate(user, 'technicians','view')">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase">
                        {{ title }}
                    </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#technicians-list" data-toggle="tab">Technicians List</a>
                    </li>
                    <li v-if="gate(user, 'technician_schedules','view')">
                        <a href="#scheduling" data-toggle="tab">Scheduling</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <technicians-list></technicians-list>
                    <scheduling></scheduling>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>
        <technician-profile v-if="view" :with_back="true"></technician-profile>
    </div>
</template>
<script>
    import UnauthorizedError from './errors/UnauthorizedError.vue';
    import TechniciansList from './technicians/TechniciansList.vue';
    import Scheduling from './technicians/Scheduling.vue';
    import TechnicianProfile from './technicians/profile/TechnicianProfile.vue';

    export default {
        name: 'Technicians',
        components:{ TechniciansList, Scheduling, UnauthorizedError, TechnicianProfile },
        data: function(){
            return {
                title: 'Technicians',
            }
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'Technicians');
            this.$store.commit('technicians/updateViewingTechnician', false);
            this.$store.dispatch('technicians/fetchTechnicians');
        },
        computed:{
            user(){
                return this.$store.state.user;
            },
            view(){
                return this.$store.state.technicians.viewing_technician;
            },
        }
    }
</script>