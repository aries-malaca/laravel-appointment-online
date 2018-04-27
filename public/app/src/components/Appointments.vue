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
                <div class="row" v-if="user.is_client === 0">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Legend:</h4>
                                <span class="badge badge-info">Queued</span>
                                <span class="badge badge-success">Completed</span>
                                <span class="badge badge-danger">Cancelled/Expired</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <br/>
                                <h4>Filter by Branch:</h4>
                                <vue-select multiple v-model="branches" :options="branch_selection"></vue-select>
                            </div>
                        </div>
                        <div class="row" v-if="calendar_view">
                            <div class="col-md-12">
                                <br/>
                                <div class="portlet sale-summary">
                                    <div class="portlet-title">
                                        <h4>Appointment Summary</h4>
                                    </div>
                                    <div class="portlet-body">
                                        <ul class="list-unstyled">
                                            <li>
                                                <span class="sale-info">Viewing:</span>
                                                <span class="sale-num">{{ calendar_view.title }}</span>
                                            </li>
                                            <li>
                                                <span class="sale-info">Pending:</span>
                                                <span class="sale-num">{{ summary.pending.length }}</span>
                                            </li>
                                            <li>
                                                <span class="sale-info">Completed:</span>
                                                <span class="sale-num">{{ summary.completed.length }}</span>
                                            </li>
                                            <li>
                                                <span class="sale-info">Cancelled:</span>
                                                <span class="sale-num">{{ summary.cancelled.length }}</span>
                                            </li>
                                            <li>
                                                <span class="sale-info">Expired:</span>
                                                <span class="sale-num">{{ summary.expired.length }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="user.is_client===1">
                            <div class="col-md-12">
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
                    </div>
                    <div class="col-md-8">
                        <div class="portlet light calendar">
                            <div class="portlet-body">
                                <div id="calendar"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" v-else>
                    <div class="col-md-12">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#active-appointments" data-toggle="tab">Active Appointments</a>
                            </li>
                            <li>
                                <a href="#appointment-history" data-toggle="tab">Appointment History</a>
                            </li>
                        </ul>
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
            </div>
        </div>
        <booking-modal :toggle="toggle" :default_branch="user.branch" :lock_branch="false" :default_client="client" :lock_client="true"
                   @get_appointments="getAppointments" :token="token" :user="user" :configs="configs"/>
        <appointment-modal @refresh_list="refreshList"></appointment-modal>
    </div>
</template>

<script>
    import BookingModal from "./booking/BookingModal.vue";
    import AppointmentsTable from "./appointment/AppointmentsTable.vue";
    import AppointmentModal from "./appointment/AppointmentModal.vue";
    import VueSelect from "vue-select";

    export default {
        name: 'Appointments',
        components: { BookingModal, AppointmentsTable, AppointmentModal, VueSelect },
        data: function(){
            return {
                title: 'Appointments',
                branches:[{label:"ALL", value:0}],
                toggle:false,
                calendar_view:false,
                client:{},
                t:'',
                summary:{
                    pending:[],
                    completed:[],
                    cancelled:[],
                    expired:[],
                }
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
                    url = '/api/appointment/getAppointments/all/all/active?branches=' + this.branchIds;

                axios.get(url)
                    .then(function(response) {
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
                    url = '/api/appointment/getAppointments/all/all/inactive?branches=' + this.branchIds;

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
            refreshSummary(){
                var object = {
                    pending:[],
                    completed:[],
                    cancelled:[],
                    expired:[],
                };
                let u = this;
                if(this.calendar_view.options !== undefined)
                    this.calendar_view.options.events.filter((item)=>{
                        if(u.calendar_view.name==='month')
                            return (moment(item.start).format("MM") === moment(u.calendar_view.start).format("MM"));
                        else if(u.calendar_view.name==='agendaThreeDays'){
                            return (moment(item.start).format("YYYY-MM-DD") === moment(u.calendar_view.start).format("YYYY-MM-DD")) ||
                                (moment(item.start).format("YYYY-MM-DD") === moment(u.calendar_view.start).add(1,"days").format("YYYY-MM-DD")) ||
                                (moment(item.start).format("YYYY-MM-DD") === moment(u.calendar_view.start).add(2, "days").format("YYYY-MM-DD"));
                        }
                        else if(u.calendar_view.name==='agendaOneDay')
                            return (moment(item.start).format("YYYY-MM-DD") === moment(u.calendar_view.start).format("YYYY-MM-DD"));
                    }).forEach((event)=>{
                        if(event.transaction_status === 'reserved')
                            object.pending.push(event);
                        else if(event.transaction_status === 'completed')
                            object.completed.push(event);
                        else if(event.transaction_status === 'cancelled')
                            object.cancelled.push(event);
                        else if(event.transaction_status === 'expired')
                            object.expired.push(event);
                    });

                this.summary = object;
            },
            initCalendar(view){
                let u = this;
                setTimeout(()=>{
                    $('#calendar').fullCalendar('destroy');
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
            this.getAppointments();
            this.getAppointmentHistory();

            let u = this;
            this.$options.sockets.refreshAppointments = function(data){
                if(data.client_id !== u.user.id && u.user.is_client ===1)
                    return false;
                u.getAppointments();
                u.getAppointmentHistory();
            };
            setInterval(()=>{
                u.calendar_view = $("#calendar").fullCalendar('getView');
                u.t = $("#calendar").fullCalendar('getView').title;
                u.refreshSummary();
            },1000);
        },
        computed:{
            branchIds(){
                var a = [];
                var b = [];

                this.branches.forEach((item)=>{
                    a.push(item.value);
                });

                this.branch_selection.forEach((item)=>{
                    a.push(item.value);
                });

                if(a.indexOf(0) !== -1)
                    return b;

                return a;
            },
            branch_selection(){
                let u = this;
                var a = [];
                a.push({label:"ALL", value:0});

                this.$store.state.branches.branches.forEach(function(item){
                    a.push({label:item.branch_name, value:item.id});
                });

                return a.filter((branch)=>{
                    return (u.user.user_data.branches.indexOf(branch.value) !== -1 || u.user.user_data.branches.indexOf(0) !== -1)
                });
            },
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
                        transaction_status:item.transaction_status,
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
            },
            branches(){
                this.getAppointments();
                this.getAppointmentHistory();
            }
        }
    }
</script>
<style>
    .portlet.calendar .fc-event .fc-content{
        padding:0px !important;
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