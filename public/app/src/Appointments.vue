<template>
    <div class="appointments">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div> &nbsp;
                <button v-if="user.is_client == 1" @click="toggle = !toggle" type="button" class="btn green-meadow">Book Now</button>
                <div class="tools">
                    <a href="" class="collapse" data-original-title="" title=""> </a>
                    <a href="" class="reload" data-original-title="" title=""> </a>
                    <a href="" class="fullscreen" data-original-title="" title=""> </a>
                </div>
            </div>
            <div class="portlet-body">
                <appointments-table title="Active Appointments" :hide_client="true"
                                    :appointments="active_appointments" :token="token" :configs="configs" />
            </div>
        </div>
        <booking-modal :toggle="toggle" @get_appointments="getAppointments" :branches="branches" :token="token" :user="user"></booking-modal>
    </div>
</template>

<script>
    import BookingModal from "./modals/BookingModal.vue";
    import AppointmentsTable from "./tables/AppointmentsTable.vue";

    export default {
        name: 'Appointments',
        props: ['user','token','configs'],
        components: { BookingModal, AppointmentsTable },
        data: function(){
            return {
                title: 'Appointments',
                active_appointments:[],
                branches:[],
                toggle:false
            }
        },
        methods:{
            getBranches:function() {
                let u = this;
                axios.get('/api/branch/getBranches/active')
                .then(function (response) {
                    u.branches = response.data;
                });
            },
            getAppointments:function(){

            }
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');

            this.getBranches();
        }
    }
</script>
