<template>
    <div class="queuing">
        <div class="portlet light" v-if="user.is_client !== 1 && gate(user, 'queuing','view')">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div>
                <div class="tools">
                    <a href="" class="collapse" data-original-title="" title=""> </a>
                    <a href="" class="reload" data-original-title="" title=""> </a>
                    <a href="" class="fullscreen" data-original-title="" title=""> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <h4 style="text-align:left">Branch:</h4>
                            </div>
                            <div class="col-sm-5">
                                <vue-select v-model="b" :options="branch_selection"></vue-select>
                            </div>
                            <div class="col-sm-1">
                                <h4 style="text-align:right">Date:</h4>
                            </div>
                            <div class="col-sm-4">
                                <input type="date" v-model="c" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row" v-if="branch !== null">
                            <div class="col-md-6">
                                <button @click="toggle = !toggle" v-if="gate(user, 'appointments','add')"
                                        type="button" class="btn btn-info btn-block">Add Appointment</button>
                            </div>
                            <div class="col-md-6" v-if="moment().format('YYYY-MM-DD') === moment(c).format('YYYY-MM-DD')">
                                <a v-bind:href="'../../queuing/web/' + branch.value" target="_blank" class="btn btn-warning btn-block">Queuing Screen</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tabbable-line" v-if="branch !== null">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#queued" data-toggle="tab" aria-expanded="true">
                                Queued
                                <span class="badge badge-success" v-if="queued.length > 0"> {{ queued.length }} </span>
                            </a>
                        </li>
                        <li class="">
                            <a href="#completed" data-toggle="tab" aria-expanded="false"> Completed
                                <span class="badge badge-success" v-if="completed.length > 0"> {{ completed.length }} </span>
                            </a>
                        </li>
                        <li class="">
                            <a href="#cancelled" data-toggle="tab" aria-expanded="false"> Cancelled
                                <span class="badge badge-success" v-if="cancelled.length > 0"> {{ cancelled.length }} </span>
                            </a>
                        </li>
                        <li class="">
                            <a href="#statistics" @click="getAppointments" data-toggle="tab" aria-expanded="false"> Full Day View
                                <span class="badge badge-info"> New! </span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="queued">
                            <div class="alert alert-info" v-if="queued.length===0">
                                No Queued Appointment for Today.
                            </div>
                            <div style="min-width:400px;overflow:scroll" v-else>
                                <table  class="table-responsive table-condensed table table-bordered" >
                                    <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Services</th>
                                        <th>Technician</th>
                                        <th>Platform</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="app in queued" v-bind:style="calling_clients.indexOf(app.client.client_id) !== -1?'background-color:#daffce': isOnServe(app)?'background-color:#e6bae8':''">
                                        <td style="width:18%;">
                                            <b>{{ app.client.client_name }}</b><br/>
                                        </td>
                                        <td style="width:35%;">
                                            <table class="table-responsive table table-condensed table-hover table-bordered" style="margin:0px;">
                                                <tbody>
                                                <tr style="cursor: pointer;" @click="viewAppointment(item.transaction_id)" v-for="item in app.items">
                                                    <td style="width:45%;"> {{ item.item_name }}</td>
                                                    <td style="width:55%;">
                                                        Booked: {{ moment(item.book_start_time).format("hh:mm A") }} - {{ moment(item.book_end_time).format("hh:mm A") }}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td style="width:15%;">
                                            {{ app.items[0].technician_name }}
                                        </td>
                                        <td style="width:9%;">
                                            {{ app.items[0].platform }}
                                        </td>
                                        <td style="width:13%;">
                                            <div v-if="isOnServe(app)">
                                                <span v-if="app.serve_time!==null"> Serving: {{ moment(app.items[0].serve_time).format("hh:mm A") }} </span>
                                            </div>
                                            <div v-else-if="calling_clients.indexOf(app.client.client_id) !== -1">
                                                Calling
                                            </div>
                                            <div v-else>
                                                Pending
                                            </div>
                                        </td>
                                        <td style="width:10%;">
                                            <div v-if="serving_clients.indexOf(app.client.client_id) === -1">
                                                <button class="btn btn-success btn-xs btn-block" v-if="calling_clients.indexOf(app.client.client_id) === -1 && gate(user, 'appointments','serve')" @click="emitCall(app.client.client_id)">Call</button>
                                                <button class="btn btn-warning btn-xs btn-block" v-else @click="emitUnCall(app.client.client_id)">UnCall</button>
                                            </div>
                                            <div>
                                                <button class="btn purple btn-xs btn-block" @click="showConfirmTechnicianModal(app)" v-if="calling_clients.indexOf(app.client.client_id) !== -1 && serving_appointments.indexOf(app.id)===-1 && gate(user, 'appointments','serve')">Serve</button>
                                                <button class="btn btn-warning btn-xs btn-block" @click="emitUnServe(app)" v-if="isOnServe(app) && gate(user, 'appointments','serve')">UnServe</button>
                                                <button class="btn btn-success btn-xs btn-block" @click="viewAppointment(onServeID(app))"  v-if="isOnServe(app) && gate(user, 'appointments','complete')">Complete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="completed">
                            <div class="alert alert-info" v-if="completed.length===0">
                                No Completed Appointment for Today.
                            </div>
                            <div style="min-width:400px;overflow:scroll" v-else>
                                <table  class="table-responsive table-condensed table table-bordered" >
                                    <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Services</th>
                                        <th>Technician</th>
                                        <th>Platform</th>
                                        <th>Served</th>
                                        <th>Completed</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="app in completed">
                                        <td style="width:18%;">
                                            <b>{{ app.client.client_name }}</b><br/>
                                        </td>
                                        <td style="width:34%;">
                                            <table class="table-responsive table table-condensed table-hover table-bordered" style="margin:0px;">
                                                <tbody>
                                                <tr style="cursor: pointer;" @click="viewAppointment(item.transaction_id)" v-for="item in app.items">
                                                    <td style="width:42%;"> {{ item.item_name }}</td>
                                                    <td style="width:58%;">
                                                        Booked: {{ moment(item.book_start_time).format("hh:mm A") }} - {{ moment(item.book_end_time).format("hh:mm A") }}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td style="width:18%;">
                                            {{ app.items[0].technician_name }}
                                        </td>
                                        <td style="width:10%;">
                                            {{ app.items[0].platform }}
                                        </td>
                                        <td style="width:10%;">
                                            {{  moment(app.items[0].serve_time).format("hh:mm A") }}
                                        </td>
                                        <td style="width:10%;">
                                            {{ moment(app.items[0].complete_time).format("hh:mm A") }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="cancelled">
                            <div class="alert alert-info" v-if="cancelled.length===0">
                                No Cancelled Appointment for Today,
                            </div>
                            <table class="table-responsive table table-hover table-bordered" v-else>
                                <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Services</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="app in cancelled">
                                    <td>{{ app.client.client_name }}</td>
                                    <td>
                                        <table class="table-responsive table table-bordered" style="margin:0px;">
                                            <tbody>
                                            <tr v-for="item in app.items">
                                                <td>{{ item.item_name }}</td>
                                                <td>{{ item.technician_name }}</td>
                                                <td>{{ moment(item.book_start_time).format("hh:mm A") }} - {{ moment(item.book_end_time).format("hh:mm A") }}</td>
                                                <td>
                                                    <span class="badge badge-danger">CANCELLED</span>
                                                </td>
                                                <td v-if="item.item_data!==undefined">Reason: {{ item.item_data.cancel_reason }}</td>
                                                <td v-if="item.item_data!==undefined">Cancelled By: {{ item.item_data.cancel_by_name }} ({{ item.item_data.cancel_by_type.toUpperCase() }})</td>
                                                <td>
                                                    <button class="btn btn-xs btn-warning" @click="viewAppointment(item.transaction_id)">View</button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="statistics">
                            <div v-if="operating_schedule">
                                <div v-if="technicians.length>0 && appointments.length>0">
                                    <span class="badge badge-info">Queued</span>
                                    <span class="badge badge-success">Completed</span>
                                    <span class="badge badge-danger">Cancelled/Expired</span>
                                    <br/>
                                    <div class="portlet light calendar">
                                        <div class="portlet-body">
                                            <div id="calendar"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-danger" v-else>
                                    This feature is not available. No Technicians/Appointments for this branch.
                                </div>
                            </div>
                            <div v-else>
                                <div class="alert alert-danger">
                                    No schedule for the selected branch.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>

        <booking-modal :toggle="toggle" @get_appointments="getAppointments" :lock_branch="true" :queued="queued"
               :default_branch="branch" :default_client="null" :lock_client="false" />

        <div data-backdrop="static" class="modal fade" id="confirm-technician-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Confirm Technician</h4>
                    </div>
                    <div class="modal-body" v-if="selected_client !== undefined">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Appointment ID</label>
                                    <select v-model="selected_appointment_id" class="form-control">
                                        <option v-for="id in getTransactionIDS(selected_client.items)" :value="id">{{ id }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Technician</label>
                                    <select v-model="selected_technician" class="form-control">
                                        <option v-for="technician in technicians" :value="technician.id">{{ technician.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <h4>Services</h4>
                        <table class="table-responsive table table-hover table-bordered">
                            <thead>
                            <tr>
                                <th>Service</th>
                                <th>Time Booked</th>
                                <th>Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="service in display_services">
                                <td>{{ service.item_name }}</td>
                                <td>
                                    <span>{{ moment(service.book_start_time).format("hh:mm A") }} - </span>
                                    <span>{{ moment(service.book_end_time).format("hh:mm A") }}</span>
                                </td>
                                <td>{{ service.amount.toFixed(2) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success btn-block" @click="emitServe">Confirm</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        <appointment-modal v-if="b!==null" @refresh_list="refreshList"></appointment-modal>
    </div>
</template>

<script>
    import BookingModal from "../booking/BookingModal.vue";
    import AppointmentModal from "../appointment/AppointmentModal.vue";
    import VueSelect from "vue-select"
    import UnauthorizedError from '../errors/UnauthorizedError.vue';

    export default {
        name: 'Queuing',
        components:{ VueSelect, BookingModal, AppointmentModal, UnauthorizedError },
        data: function(){
            return {
                title: 'Queuing',
                appointments:[],
                toggle:false,
                show:false,
                display_id:undefined,
                hasPendingCallback:false,
                calling:[],
                serving:[],
                selected_technician:undefined,
                selected_appointment_id:undefined,
                selected_client:undefined,
                b:null,
                c:null,
            }
        },
        methods:{
            changeDate(value){
                this.$store.commit('updateQueuingDate', value);
            },
            viewAppointment:function(id) {
                this.display_id = id;
                this.$store.commit('appointments/updateViewingID', id);
                $("#appointment-modal").modal("show");
            },
            closeModal:function(){
                this.$store.commit('appointments/updateViewingID', undefined);
                $("#appointment-modal").modal("hide");
                this.display_id = undefined;
            },
            getAppointments:function(){
                if(this.branch === null)
                    return false;

                let u = this;
                let url = '/api/appointment/getAppointments/queue/'+this.branch.value+'/'+ this.date;

                this.title = 'Queuing ' + (this.branch!== null? '(' + this.branch.label + ')':'');

                axios.get(url)
                    .then(function (response) {
                        u.appointments = [];
                        response.data.forEach(function(item){
                            if(item.item_type === 'product'){
                                item.book_start_time = moment().format("YYYY-MM-DD hh:mm:ss");
                                item.book_end_time = moment().format("YYYY-MM-DD hh:mm:ss");
                            }
                            u.appointments.push(item);
                        });
                        u.initAgenda();
                    });
            },
            groupedItems:function(status){
                var data = [];
                for(var x=0; x<this.clients.length;x++){
                    var items = this.filterItems(this.clients[x].client_id, status);

                    if(items.length > 0)
                        data.push({
                            client:this.clients[x],
                            items: items,
                        });
                }
                return data;
            },
            getTransactionIDS(items){
                var ids = [];
                for(var x=0;x<items.length;x++)
                    if(ids.indexOf(items[x].transaction_id) === -1)
                        ids.push(items[x].transaction_id);

                return ids;
            },
            onServeID(appointment){
                for(var x=0;x<appointment.items.length;x++){
                    if(this.serving_appointments.indexOf(appointment.items[x].transaction_id) !== -1)
                        return appointment.items[x].transaction_id;
                }
            },
            filterItems:function(client_id, status){
                var data =[];
                for(var x=0;x<this.appointments.length;x++){
                    if(client_id === this.appointments[x].client_id){
                        for(var y=0;y<this.appointments[x].items.length;y++){
                            if(status === this.appointments[x].items[y].item_status && this.appointments[x].items[y].item_type ==='service'){
                                this.appointments[x].items[y].technician_name = this.appointments[x].technician_name;
                                this.appointments[x].items[y].technician_id = this.appointments[x].technician_id;
                                this.appointments[x].items[y].client_id = this.appointments[x].client_id;
                                this.appointments[x].items[y].platform = this.appointments[x].platform;
                                this.appointments[x].items[y].transaction_datetime = this.appointments[x].transaction_datetime;
                                data.push(this.appointments[x].items[y]);
                            }
                        }
                    }
                }
                return data;
            },
            emitCall:function(client_id){
                let u = this;
                this.$socket.emit('callClient', client_id, this.branch.value);
                this.calling.push({client_id:client_id});
                setTimeout(function(){
                    u.refresh();
                },u.configs.QUEUE_CALLING_DURATION * 1000);
            },
            emitUnCall:function(client_id){
                this.$socket.emit('uncallClient', client_id, this.branch.value);
                this.removeData('calling', client_id);
            },
            removeData(field, id){
                for(var x=0;x<this[field].length;x++){
                    if(field==='calling'){
                        if(this[field][x].client_id===id)
                            this[field].splice(x,1);
                    }
                    else{
                        if(this[field][x].id===id)
                            this[field].splice(x,1);
                    }
                }
            },
            showConfirmTechnicianModal(client){
                this.selected_client = client;
                this.selected_technician = client.items[0].technician_id;
                this.selected_appointment_id = client.items[0].transaction_id;

                let u =this;
                axios.get('/api/technician/getBranchTechnicians/'+this.branch.value+'/'+this.date)
                    .then(function (response) {
                        u.$store.commit('updateQueuingTechnicians',response.data);
                        $("#confirm-technician-modal").modal("show");
                    });
            },
            emitServe:function(){
                let u = this;
                axios({url:'/api/appointment/serveAppointment?token=' + this.token, method:'post', data:{id:this.selected_appointment_id, technician_id:this.selected_technician}})
                    .then(function () {
                        u.$socket.emit('serveClient', u.selected_client.client.client_id, u.branch.value, u.selected_appointment_id);
                        u.$socket.emit('refreshAppointments', u.branch.value);
                        $("#confirm-technician-modal").modal("hide");
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            emitUnServe:function(appointment){
                let u = this;
                var client_id = 0;
                var transaction_id = 0;
                var find = appointment.items.find((i)=>{
                    if(u.serving_appointments.indexOf(i.transaction_id) !== -1){
                        if(client_id===0){
                            client_id = i.client_id;
                            transaction_id = i.transaction_id;
                        }
                        return true;
                    }
                });

                if(find !== undefined){
                    axios({url:'/api/appointment/unServeAppointment?token=' + this.token, method:'post', data:{id:transaction_id}})
                        .then(function () {
                            u.$socket.emit('unserveClient', u.branch.value, client_id);
                            u.$socket.emit('refreshAppointments', u.branch.value);
                        })
                        .catch(function (error) {
                            XHRCatcher(error);
                        });
                }
            },
            inArray:function(clients, id){
                for(var x=0;x<clients.length;x++){
                    if(clients[x].client_id === id)
                        return true;
                }
                return false;
            },
            refresh(){
                let u = this;
                if(u.branch !== null)
                    axios.get('https://lbo-express.azurewebsites.net/api/queuing/' + u.branch.value)
                        .then(function (response) {
                            u.calling = response.data.calling;
                            u.$store.commit('updateServing',response.data.serving);
                            u.initAgenda();
                        });
            },
            refreshList(){
              this.getAppointments();
            },
            isOnServe(appointment){
                for(var x=0;x<appointment.items.length;x++){
                    if(this.serving_appointments.indexOf(appointment.items[x].transaction_id) !== -1)
                        return true;
                }
                return false;
            },
            initAgenda(){
                let u = this;

                if(this.technicians.length > 0 && this.operating_schedule)
                setTimeout(()=>{
                    $('#calendar').fullCalendar("destroy");
                    $('#calendar').fullCalendar({
                        defaultView: 'agendaDay',
                        groupByResource: true,
                        header: false,
                        allDaySlot: false,
                        timeFormat:'hh:mm A',
                        defaultDate:moment(u.c),
                        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                        minTime:moment().subtract(1, "hours").format("HH:00"),
                        maxTime:this.operating_schedule.end,
                        resources: this.mappedTechnicians,
                        events: this.mappedAppointments,
                        eventClick: function(event) {
                            u.viewAppointment(event.id)
                        },
                    });
                }, 1000);
            }
        },
        mounted:function(){
            this.$store.commit('updateQueuingBranch', null);
            this.$store.commit('updateTitle', 'Queuing');
            let v = this.$store.state.queuing_branch;
            let w = this.$store.state.queuing_date;

            this.b = v;
            this.c = w;

            let u = this;
            this.$options.sockets.refreshAppointments = function(data){
                if(data.branch_id === u.branch.value)
                    u.getAppointments();
            };
            this.$options.sockets.callClient = function(data){
                if(data.branch_id===u.branch.value){
                    u.refresh();
                }
            };

            if(this.b !== null && this.c !== null){
                this.getAppointments();
                this.refresh();
            }
        },
        computed:{
            branch_selection:function(){
                let u = this;
                var a = [];
                this.branches.forEach(function(item){
                    if (u.user.user_data.branches.indexOf(item.id)  !== -1 || u.user.user_data.branches.indexOf(0) !== -1)
                        a.push({
                            label:item.branch_name,
                            value:item.id,
                            rooms:item.rooms_count,
                            schedules:item.schedules_original,
                            branch_data:item.branch_data,
                            products:item.products,
                            services:item.services,
                            branch_address:item.branch_address,
                            kiosk_data:item.kiosk_data,
                            cluster_data:item.cluster_data,
                        });
                });
                return a;
            },
            clients:function(){
                var clients = [];
                for(var x = 0; x<this.appointments.length;x++){
                    if(!this.inArray(clients, this.appointments[x].client_id))
                        clients.push({
                            client_id:this.appointments[x].client_id,
                            client_name:this.appointments[x].client_name,
                        });
                }
                return clients;
            },
            queued:function(){
                return this.groupedItems('reserved').sort((a, b)=>{
                    return ( Number(moment(a.items[0].transaction_datetime).format("X")) > Number(moment(b.items[0].transaction_datetime).format("X")) ? 1: -1);
                });
            },
            completed:function(){
                return this.groupedItems('completed')
            },
            cancelled:function(){
                return this.groupedItems('cancelled')
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
            branches(){
                return this.$store.getters['branches/activeBranches'];
            },
            calling_clients(){
                var ids = [];
                for(var x=0;x<this.calling.length;x++)
                    ids.push(this.calling[x].client_id);

                return ids;
            },
            serving_clients(){
                var ids = [];
                for(var x=0;x<this.$store.state.serving.length;x++)
                    ids.push(this.$store.state.serving[x].client_id);

                return ids;
            },
            serving_appointments(){
                var ids = [];
                for(var x=0;x<this.$store.state.serving.length;x++)
                    ids.push(this.$store.state.serving[x].appointment_id);

                return ids;
            },
            technicians(){
                return this.$store.state.queuing_technicians;
            },
            display_services(){
                var services = [];
                for(var x=0;x<this.queued.length;x++){
                    for(var y=0;y<this.queued[x].items.length;y++){
                        if(this.queued[x].items[y].transaction_id === this.selected_appointment_id && this.queued[x].items[y].item_type === 'service' )
                            services.push(this.queued[x].items[y]);
                    }
                }
                return services;
            },
            branch(){
                return this.$store.state.queuing_branch;
            },
            date(){
                return this.$store.state.queuing_date;
            },
            mappedTechnicians(){
                let array = this.technicians.map((item)=>{
                    return { id: item.id, title: item.name}
                });
                array.push({ id: 0, title: "Unassigned"});
                return array;
            },
            mappedAppointments(){
                return this.appointments.map((item)=>{
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
            operating_schedule(){
                if(this.branch !== undefined && this.c !==""){

                    for(var x=0;x<this.branch.schedules.length;x++){
                        var e = this.branch.schedules[x];

                        if( Number(moment(e.date_start).format("X") <= Number(moment(this.c).format("X")) ) &&
                            Number(moment(e.date_end).format("X") >= Number(moment(this.c).format("X"))) ){

                            if(e.schedule_type === 'closed')
                                return false;
                            else if(e.schedule_type === 'custom')
                                return e.schedule_data[Number(moment(this.c).format("e"))];
                        }
                    }

                    for(var x=0;x<this.branch.schedules.length;x++){
                        if(this.branch.schedules[x].schedule_type === 'regular')
                            return this.branch.schedules[x].schedule_data[Number(moment(this.c).format("e"))];
                    }
                }
                return false;
            }
        },
        watch:{
            branch:function(){
                if(this.branch !== null){
                    let u =this;
                    axios.get('/api/technician/getBranchTechnicians/' + this.branch.value + '/' + this.c)
                        .then(function (response) {
                            u.$store.commit('updateQueuingTechnicians',response.data);
                        });
                }
            },
            date:function(){
                this.getAppointments();
            },
            b(){
                if(this.b !== null) {
                    this.$store.commit('updateQueuingBranch', this.b);
                    this.getAppointments();
                    this.refresh();
                }
            },
            c(){
                if(this.c !== null){
                    this.$store.commit('updateQueuingDate', this.c);
                    this.refresh();
                }
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
    .fc-time-grid-container{
        height: fit-content !important;
    }
</style>