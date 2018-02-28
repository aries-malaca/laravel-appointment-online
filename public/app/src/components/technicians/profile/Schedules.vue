<template>
    <div class="tab-pane active" id="schedules">
        <vue-calendar
                :show-limit="3"
                :events="[]"
                :disable="false"
                :highlight="false"
                @show-all="showAll"
                @day-clicked="dayClicked"
                @event-clicked="eventClicked"
                @month-changed="monthChanged"
        ></vue-calendar>
    </div>
</template>
<script>
    import VueCalendar from 'vue2-simple-calendar';
    export default {
        name: 'Schedules',
        components:{ VueCalendar },
        data: function(){
            return {
            }
        },
        methods:{
            showAll(events) {
                // Do something...
            },
            dayClicked(day) {
                // Do something...
            },
            eventClicked(event) {
                // Do something...
            },
            monthChanged(start, end) {
                // Do something...
            }
        },
        computed:{
            token(){
                return this.$store.state.token;
            },
        }
    }
</script>