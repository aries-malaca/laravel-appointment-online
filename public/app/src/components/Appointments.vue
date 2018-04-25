<template>
    <div class="appointments">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div> &nbsp;
                <span v-if="user.is_client === 1">
                    <button @click="toggle = !toggle" v-if="needsToAcknowledge === undefined " type="button" class="btn green-meadow">Book Now</button>
                    <button @click="acknowledgeModal" v-else type="button" class="btn green-meadow">Book Now</button>
                </span>
            </div>
            <div class="portlet-body" style="padding-top:0px;">
                <div class="row">
                    <div class="col-md-4">
                        <h4>Legend:</h4>
                        <span class="badge badge-info">Queued</span>
                        <span class="badge badge-success">Completed</span>
                        <span class="badge badge-danger">Cancelled/Expired</span>
                        <div v-if="user.is_client===1">
                            <br/>
                            <h4>Your last appointment:</h4>
                            <span v-if="!last_appointment">N/A</span>
                            <div class="alert alert-info" v-else>
                                Date: {{ moment(last_appointment.transaction_datetime).format("MM/DD/YYYY hh:mm A") }} <br/>
                                Branch: {{ last_appointment.branch_name }} <br/>
                                <button class="btn btn-xs btn-success" @click="viewAppointment(last_appointment.id)">View</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="portlet light calendar">
                            <div class="portlet-body">
                                <div id="calendar"></div>
                            </div>
                        </div>
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
            acknowledgeModal:function() {
                this.$store.commit('appointments/updateViewingID',this.needsToAcknowledge.id);
                $("#appointment-modal").modal("show");
            },
            getAppointments:function(){
                let u = this;

                var url = '/api/appointment/getAppointments/client/'+ this.user.id +'/active';

                if(this.user.is_client !== 1)
                    url = '/api/appointment/getAppointments/all/all/active?branches=' + this.user.user_data.branches;

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
                    url = '/api/appointment/getAppointments/all/all/inactive?branches=' + this.user.user_data.branches;

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
            },
            viewAppointment(id){
                this.display_id = id;
                this.$store.commit('appointments/updateViewingID', id);
                $("#appointment-modal").modal("show");
            },
            initCalendar(view){
                let u = this;
                setTimeout(()=>{
                    $('#calendar').fullCalendar({
                        allDaySlot: false,
                        defaultView:view,
                        timeFormat:'hh:mm A',
                        views:{
                            agendaThreeDays: {
                                type: 'agenda',
                                duration: { days: 3 },
                                buttonText:"3 Days"
                            },
                            agendaOneDay: {
                                type: 'agenda',
                                duration: { days: 1 },
                                buttonText:"1 Day"
                            },
                            month:{
                                eventLimit: 3
                            }
                        },
                        header: {
                            left: "title",
                            center: "",
                            right: "prev,next,today,month,agendaThreeDays,agendaOneDay"
                        },
                        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                        minTime:"08:00:00",
                        maxTime:"23:00:00",
                        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                        events: this.mappedAppointments,
                        eventClick: function(event) {
                            u.viewAppointment(event.id)
                        },
                    });
                }, 200);
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
            },
            needsToAcknowledge(){
                return this.$store.getters['appointments/needsToAcknowledge'];
            },
            all_appointments(){
                return this.appointment_history.concat(this.active_appointments);
            },
            last_appointment(){
                if(this.all_appointments.length > 0)
                    return this.all_appointments[this.all_appointments.length - 1];

                return false;
            },
            mappedAppointments(){
                return this.all_appointments.map((item)=>{
                    return {
                        resourceId:item.technician_id,
                        start:item.transaction_datetime,
                        end:function(){
                            for(var x=item.items.length - 1; x >= 0;x--){
                                if(item.item_status !== 'cancelled' && item.item_status !== 'expired')
                                    return item.items[x].book_end_time;
                            }
                        }(),
                        id:item.id,
                        backgroundColor:function(){
                            if(item.transaction_status === 'completed')
                                return "#0ed3c5";
                            else if(item.transaction_status === 'reserved')
                                return "#306fe0";

                            return "#ed4852";
                        }(),
                        title: function() {
                            var array = [];
                            item.items.forEach((i)=>{
                                if((i.item_status !== 'cancelled' && i.item_status !== 'expired') || item.transaction_status === 'cancelled'  || item.transaction_status === 'expired')
                                    array.push(i.item_name);
                            });

                            return array.join(", ");
                        }(),
                        borderColor:"blue",
                    }
                });
            },
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
            },
            all_appointments(){
                this.initCalendar('agendaThreeDays');
            }
        }
    }
</script>
<style>
    .portlet.calendar .fc-event .fc-content{
        padding:2px !important;
    }
    .portlet.calendar.light .fc-button{
        top:0px;
    }
    .fc-state-hover{
        border-bottom: 0px !important;
    }
    .fc-time, .fc-title{
        font-size:11px !important;
    }
</style>