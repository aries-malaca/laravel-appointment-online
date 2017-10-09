<template>
    <div class="appointments">
        <div class="portlet light">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div> &nbsp;
                <button v-if="user.is_client == 1" @click="toggle = !toggle" type="button" class="btn green-meadow">Book Now</button>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#active-appointments" data-toggle="tab">Active Appointments</a>
                    </li>
                    <li>
                        <a href="#appointment-history" data-toggle="tab">Appointment History</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="active-appointments">
                        <appointments-table :hide_client="user.is_client===1" :user="user" @get_appointments="getAppointments"
                                            :paginate="true" :appointments="active_appointments" :token="token" :configs="configs" />
                    </div>
                    <div class="tab-pane" id="appointment-history">
                        <appointments-table :hide_client="user.is_client===1" :user="user" @get_appointments="getAppointmentHistory"
                                            :paginate="true" :appointments="appointment_history" :token="token" :configs="configs" />
                    </div>
                </div>
            </div>
        </div>
        <booking-modal :toggle="toggle" :default_branch="user.branch" :lock_branch="false" :default_client="client" :lock_client="true"
                   @get_appointments="getAppointments" :branches="branches" :token="token" :user="user" />
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
                appointment_history:[],
                branches:[],
                toggle:false,
                client:{}
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
                let u = this;
                var url = '/api/appointment/getAppointments/client/'+ this.user.id +'/active';

                if(this.user.is_client !== 1)
                    url = '/api/appointment/getAppointments/all/all/active';

                axios.get(url)
                    .then(function (response) {
                        u.active_appointments = [];
                        response.data.forEach(function(item){
                            if(u.user.is_client !== 1 && u.user.user_data.branches.indexOf(item.branch_id) === -1)
                                return false;

                            u.active_appointments.push(item);
                        });
                    });
            },
            getAppointmentHistory:function(){
                let u = this;
                var url = '/api/appointment/getAppointments/client/'+ this.user.id +'/inactive';

                if(this.user.is_client !== 1)
                    url = '/api/appointment/getAppointments/all/all/inactive';

                axios.get(url)
                    .then(function (response) {
                        u.appointment_history = [];
                        response.data.forEach(function(item){
                            if(u.user.is_client !== 1 && u.user.user_data.branches.indexOf(item.branch_id) === -1)
                                return false;

                            u.appointment_history.push(item);
                        });
                    });
            },
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
            this.getBranches();

            let u = this;
            this.$options.sockets.refreshAppointments = function(data){
                if(data.client_id !== u.user.id && u.user.is_client ===1)
                    return false;
                u.getAppointments();
                u.getAppointmentHistory();
            };

        },
        watch:{
            'user':function(){
                this.getAppointments();
                this.getAppointmentHistory();

                this.client = {
                    label:this.user.username,
                    value:this.user.id,
                    gender:this.user.gender
                };

            }
        }
    }
</script>
