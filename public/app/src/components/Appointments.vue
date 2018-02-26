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
                        <appointments-table :hide_client="user.is_client===1"
                                            :paginate="true" :appointments="active_appointments" />
                    </div>
                    <div class="tab-pane" id="appointment-history">
                        <appointments-table :hide_client="user.is_client===1"
                                            :paginate="true" :appointments="appointment_history" />
                    </div>
                </div>
            </div>
        </div>
        <booking-modal :toggle="toggle" :default_branch="user.branch" :lock_branch="false" :default_client="client" :lock_client="true"
                   @get_appointments="getAppointments" :branches="branches" :token="token" :user="user" :configs="configs"/>
        <appointment-modal @refresh_list="refreshList"></appointment-modal>
    </div>
</template>

<script>
    import BookingModal from "./booking/BookingModal.vue";
    import AppointmentsTable from "./appointment/AppointmentsTable.vue";
    import AppointmentModal from "./appointment/AppointmentModal.vue";

    export default {
        name: 'Appointments',
        components: { BookingModal, AppointmentsTable, AppointmentModal },
        data: function(){
            return {
                title: 'Appointments',
                branches:[],
                toggle:false,
                client:{}
            }
        },
        methods:{
            getAppointments:function(){
                let u = this;

                var url = '/api/appointment/getAppointments/client/'+ this.user.id +'/active';

                if(this.user.is_client !== 1)
                    url = '/api/appointment/getAppointments/all/all/active';

                axios.get(url)
                    .then(function (response) {
                        var appointments = [];
                        response.data.forEach(function(item){
                            if(u.user.is_client !== 1 && (u.user.user_data.branches.indexOf(item.branch_id) === -1 && u.user.user_data.branches.indexOf(0) === -1 ))
                                return false;

                            appointments.push(item);
                        });

                        u.$store.commit('appointments/updateActiveAppointments', appointments);
                    });
            },
            getAppointmentHistory:function(){
                let u = this;

                var url = '/api/appointment/getAppointments/client/'+ this.user.id +'/inactive';

                if(this.user.is_client !== 1)
                    url = '/api/appointment/getAppointments/all/all/inactive';

                axios.get(url)
                    .then(function (response) {
                        var appointments = [];
                        response.data.forEach(function(item){
                            if(u.user.is_client !== 1 && (u.user.user_data.branches.indexOf(item.branch_id) === -1 && u.user.user_data.branches.indexOf(0) === -1 ))
                                return false;

                            appointments.push(item);
                        });
                        u.$store.commit('appointments/updateAppointmentHistory', appointments);
                    });
            },
            refreshList(){
                this.getAppointments();
                this.getAppointmentHistory();
            }
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'Appointments');
            this.$store.dispatch('fetchAuthenticatedUser');
            this.branches = this.$store.state.branches.branches;
            this.getAppointments();
            this.getAppointmentHistory();

            let u = this;
            this.$options.sockets.refreshAppointments = function(data){
                if(data.client_id !== u.user.id && u.user.is_client ===1)
                    return false;
                u.getAppointments();
                u.getAppointmentHistory();
            };

        },
        computed:{
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            },
            configs(){
                return this.$store.state.configs;
            },
            active_appointments(){
                return this.$store.state.appointments.active_appointments;
            },
            appointment_history(){
                return this.$store.state.appointments.appointment_history;
            }
        },
        watch:{
            'user':function(){
                this.client = {
                    label:this.user.username,
                    value:this.user.id,
                    gender:this.user.gender,
                    user_mobile:this.user.user_mobile,
                    picture_html_big:this.user.picture_html_big,
                };
            }
        }
    }
</script>