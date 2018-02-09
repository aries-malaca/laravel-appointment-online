<template>
    <div class="tab-pane" id="schedules">
        <div class="tabbable-line">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#operating" data-toggle="tab">Operating Schedules</a>
                </li>
                <li>
                    <a href="#shifting" data-toggle="tab">Technician Shifting</a>
                </li>
            </ul>
            <div class="tab-content">
                <operating-schedules @refresh_branch="emitt"></operating-schedules>
                <technician-shifting @refresh_branch="emitt"></technician-shifting>
            </div>
        </div>
    </div>
</template>
<script>
    import OperatingSchedules from './schedules/OperatingSchedules.vue';
    import TechnicianShifting from './schedules/TechnicianShifting.vue';

    export default {
        name: 'Schedules',
        components:{ OperatingSchedules, TechnicianShifting },
        methods:{
            emitt(){
                this.$emit('refresh_branch');
            }
        }
    }
</script>