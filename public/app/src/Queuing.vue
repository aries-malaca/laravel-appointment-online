<template>
    <div class="queuing">
        <div class="portlet light">
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
                            <div class="col-xs-6">
                                <button class="btn btn-info btn-block">Add Appointment</button>
                            </div>
                            <div class="col-xs-6">
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
                            <table class="table-responsive table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Technician</th>
                                        <th>Services</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="app in queued">
                                        <td>{{ app.client.client_name }}</td>
                                        <td>{{ app.client.technician_name }}</td>
                                        <td>
                                            <table class="table-responsive table table-hover table-bordered">
                                                <tbody>
                                                    <tr v-for="item in app.items" v-bind:style="( (Number(moment().format('X'))- item.item_data.called)<60 ? 'background-color:#b5ffd6':'')">
                                                        <td>{{ item.item_name }}</td>
                                                        <td>{{ moment(item.book_start_time).format("hh:mm a") }} - {{ moment(item.book_end_time).format("hh:mm a") }}</td>
                                                        <td>
                                                            <span class="badge badge-info">RESERVED</span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-xs btn-warning">View</button>
                                                            <button class="btn btn-xs btn-success" @click="emitCallItem(item.id)">Call</button>
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

                        </div>
                        <div class="tab-pane active" id="cancelled">

                        </div>
                        <div class="tab-pane" id="statistics">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VueSelect from "vue-select"
    export default {
        name: 'Queuing',
        props:['token','user'],
        components:{ VueSelect },
        data: function(){
            return {
                title: 'Queuing',
                branches:[],
                branch:null,
                appointments:[]
            }
        },
        methods:{
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
                            if(status === this.appointments[x].items[y].item_status && this.appointments[x].items[y].item_type ==='service')
                                data.push(this.appointments[x].items[y]);
                        }
                    }
                }
                return data;
            },
            emitCallItem:function(item_id){
                //talk to laravel server and to node server to emit event
                this.$socket.emit('callItem', this.branch.value, item_id);
            },
            callItem:function(item_id){
                ///call the item on client only
                for(var x=0;x<this.appointments.length;x++){
                    for(var y=0;y<this.appointments[x].items.length;y++){
                        if(this.appointments[x].items[y].id === item_id){
                            console.log(item_id);
                            this.appointments[x].items[y].item_data.called = Number(moment().format('X'));
                        }
                    }
                }
            },
            inArray:function(clients, id){
                for(var x=0;x<clients.length;x++){
                    if(clients[x].client_id === id)
                        return true;
                }
                return false;
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
                    u.callItem(data.item_id);
                }
            };
        },
        computed:{
            branch_selection:function(){
                var a = [];
                this.branches.forEach(function(item, i){
                    a.push({label:item.branch_name, value:item.id});
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
                            technician_id:this.appointments[x].technician_id,
                            technician_name:this.appointments[x].technician_name
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