<template>
    <div class="queuing">
        <div class="portlet light" v-if="user.is_client !== 1">
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-xs-3">
                                <h4 style="text-align:right">Branch:</h4>
                            </div>
                            <div class="col-xs-9">
                                <vue-select v-model="branch" :options="branch_selection"></vue-select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row" v-if="branch !== null">
                            <div class="col-md-6">
                                <button @click="toggle = !toggle" type="button" class="btn btn-info btn-block">Add Appointment</button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-warning btn-block">Queuing Screen</button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="tabbable-line" v-if="branch !== null">
                    <ul class="nav nav-tabs ">
                        <li class="active">
                            <a href="#queued" data-toggle="tab" aria-expanded="true"> Queued </a>
                        </li>
                        <li class="">
                            <a href="#completed" data-toggle="tab" aria-expanded="false"> Completed </a>
                        </li>
                        <li class="">
                            <a href="#cancelled" data-toggle="tab" aria-expanded="false"> Cancelled </a>
                        </li>
                        <li class="">
                            <a href="#statistics" data-toggle="tab" aria-expanded="false"> Statistics </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="queued">
                            <div class="alert alert-info" v-if="queued.length===0">
                                No Queued Appointment for Today,
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
                                    <tr v-for="app in queued">
                                        <td>{{ app.client.client_name }}</td>
                                        <td>
                                            <table class="table-responsive table table-bordered">
                                                <tbody>
                                                    <tr v-for="item in app.items">
                                                        <td>{{ item.item_name }}</td>
                                                        <td>{{ item.technician_name }}</td>
                                                        <td>{{ moment(item.book_start_time).format("hh:mm A") }} - {{ moment(item.book_end_time).format("hh:mm A") }}</td>
                                                        <td>
                                                            <span class="badge badge-info">RESERVED</span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-xs btn-warning" @click="viewAppointment(item.transaction_id)">View</button>
                                                            <button class="btn btn-xs btn-danger" @click="emitUnCallItem(item.id)" v-if="isOnCall(item)">Uncall</button>
                                                            <button class="btn btn-xs btn-success" @click="emitCallItem(item.id)"  v-if="!isOnCall(item) && !isOnServe(item)">Call</button>

                                                            <button class="btn btn-xs purple" @click="emitServeItem(item.id)" v-if="isOnCall(item)">Serve</button>
                                                            <button class="btn btn-xs btn-danger" @click="emitUnServeItem(item.id)" v-if="isOnServe(item)">Unserve</button>

                                                            <button class="btn btn-xs btn-info" @click="emitCompleteItem(item)" v-if="isOnServe(item)">Complete</button>
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
                        <div class="tab-pane" id="completed">
                            <div class="alert alert-info" v-if="completed.length===0">
                                No Completed Appointment for Today,
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
                                <tr v-for="app in completed">
                                    <td>{{ app.client.client_name }}</td>
                                    <td>
                                        <table class="table-responsive table table-bordered">
                                            <tbody>
                                                <tr v-for="item in app.items">
                                                    <td>{{ item.item_name }}</td>
                                                    <td>{{ item.technician_name }}</td>
                                                    <td>{{ moment(item.book_start_time).format("hh:mm A") }} - {{ moment(item.book_end_time).format("hh:mm A") }}</td>
                                                    <td>
                                                        <span class="badge badge-success">Completed</span>
                                                    </td>
                                                    <td>Served: {{ moment(item.serve_time).format("hh:mm A") }}</td>
                                                    <td>Completed: {{ moment(item.complete_time).format("hh:mm A") }}</td>
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
                                        <table class="table-responsive table table-bordered">
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

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>
        <appointment-modal @refresh_list="getAppointments" @close_modal="closeModal" :user="user" :token="token" :id="display_id"></appointment-modal>

        <booking-modal :toggle="toggle" @get_appointments="getAppointments" :lock_branch="true" :queued="queued" :configs="configs"
               :default_branch="branch" :default_client="null" :lock_client="false" :branches="branches" :token="token" :user="user" />
    </div>
</template>

<script>
    import BookingModal from "../modals/BookingModal.vue";
    import AppointmentModal from "../modals/AppointmentModal.vue";
    import VueSelect from "vue-select"
    import UnauthorizedError from '../errors/UnauthorizedError.vue';

    export default {
        name: 'Queuing',
        props:['token','user','configs'],
        components:{ VueSelect, BookingModal, AppointmentModal, UnauthorizedError },
        data: function(){
            return {
                title: 'Queuing',
                branches:[],
                branch:null,
                appointments:[],
                toggle:false,
                show:false,
                display_id:undefined
            }
        },
        methods:{
            viewAppointment:function(id) {
                this.display_id = id;
                setTimeout(function(){
                    $("#appointment-modal-" + id).modal("show");
                },200);
            },
            closeModal:function(){
                $("#appointment-modal-" + this.display_id).modal("hide");
                this.display_id = undefined;
            },
            getBranches:function(){
                let u = this;
                axios.get('/api/branch/getBranches/active')
                    .then(function (response) {
                        u.branches = response.data;
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            getAppointments:function(){
                if(this.branch === null)
                    return false;

                let u = this;
                let url = '/api/appointment/getAppointments/queue/'+this.branch.value+'/queue';

                this.title = 'Queuing ' + (this.branch!== null? '(' + this.branch.label + ')':'');
                this.$emit('update_title',  this.title);

                axios.get(url)
                    .then(function (response) {
                        u.appointments = [];
                        response.data.forEach(function(item){
                            u.appointments.push(item);
                        });
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
            filterItems:function(client_id, status){
                var data =[];
                for(var x=0;x<this.appointments.length;x++){
                    if(client_id === this.appointments[x].client_id){
                        for(var y=0;y<this.appointments[x].items.length;y++){
                            if(status === this.appointments[x].items[y].item_status && this.appointments[x].items[y].item_type ==='service'){
                                this.appointments[x].items[y].technician_name = this.appointments[x].technician_name;
                                this.appointments[x].items[y].client_id = this.appointments[x].client_id;
                                data.push(this.appointments[x].items[y]);
                            }
                        }
                    }
                }
                return data;
            },
            emitCallItem:function(item_id){
                let u = this;
                axios({url:'/api/appointment/callAppointment?token=' + this.token, method:'patch', data:{item_id:item_id}})
                    .then(function () {
                        u.$socket.emit('callItem', u.branch.value, item_id);
                        setTimeout(function(){
                            for(var x=0;x<u.appointments.length;x++){
                                for(var y=0;y<u.appointments[x].items.length;y++){
                                    if(item_id === u.appointments[x].items[y].id)
                                        if( (Number(moment().format('X')) - Number(u.appointments[x].items[y].item_data.called)) >60 ){
                                            u.$socket.emit('refreshAppointments', u.branch.value);
                                        }
                                }
                            }
                        },62000)
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            emitUnCallItem:function(item_id){
                let u = this;
                axios({url:'/api/appointment/unCallAppointment?token=' + this.token, method:'patch', data:{item_id:item_id}})
                    .then(function () {
                        u.$socket.emit('refreshAppointments', u.branch.value);
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            emitServeItem:function(item_id){
                let u = this;
                axios({url:'/api/appointment/serveAppointment?token=' + this.token, method:'patch', data:{item_id:item_id}})
                    .then(function () {
                        u.$socket.emit('refreshAppointments', u.branch.value);
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            emitUnServeItem:function(item_id){
                let u = this;
                axios({url:'/api/appointment/unServeAppointment?token=' + this.token, method:'patch', data:{item_id:item_id}})
                    .then(function () {
                        u.$socket.emit('refreshAppointments', u.branch.value);
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            emitCompleteItem:function(item){
                let u = this;
                axios({url:'/api/appointment/completeAppointment?token=' + this.token, method:'patch', data:{item_id:item.id}})
                    .then(function () {
                        u.$socket.emit('refreshAppointments', u.branch.value,item.client_id);
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            inArray:function(clients, id){
                for(var x=0;x<clients.length;x++){
                    if(clients[x].client_id === id)
                        return true;
                }
                return false;
            },
            isOnCall:function(item){
                return (Number(moment().format('X'))- item.item_data.called)<60 && !this.isOnServe(item);
            },
            isOnServe:function(item){
                return item.serve_time !== null;
            },
            moment:moment
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
            this.getBranches();

            let u = this;
            this.$options.sockets.refreshAppointments = function(data){
                if(data.branch_id === u.branch.value)
                    u.getAppointments();
            };
            this.$options.sockets.callItem = function(data){
                if(data.branch_id === u.branch.value){
                    u.getAppointments();
                }
            };
        },
        computed:{
            branch_selection:function(){
                var a = [];
                this.branches.forEach(function(item, i){
                    a.push({
                        label:item.branch_name,
                        value:item.id,
                        rooms:item.rooms_count,
                        schedules:item.schedules,
                        branch_data:item.branch_data,
                        branch_address:item.branch_address,
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
                return this.groupedItems('reserved')
            },
            completed:function(){
                return this.groupedItems('completed')
            },
            cancelled:function(){
                return this.groupedItems('cancelled')
            }
        },
        watch:{
            branch:function(){
                this.getAppointments();
            }
        }
    }
</script>
<style>
    .queuing .portlet td{
        font-size:12px !important;
    }
</style>