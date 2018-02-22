<template>
    <div>
        <div v-bind:id="'appointment-modal-'+ id " class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Appointment Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row" v-if="appointment.id !== undefined">
                            <div class="col-md-4">
                                <table class="table table-hover table-light" v-if="appointment.id !== undefined && user.id !== undefined">
                                    <tbody>
                                    <tr>
                                        <td>Reference No:</td>
                                        <td>
                                            {{ appointment.reference_no }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> Client Name: </td>
                                        <td v-if="user.is_client===1"> {{ appointment.client_name }} </td>
                                        <td v-else>
                                            <a target="_blank" v-bind:href="'/#/clients/'+appointment.client_id">{{ appointment.client_name }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Gender:</td>
                                        <td>
                                            <span class="badge badge-success" v-if="appointment.client_gender==='male'">MALE</span>
                                            <span class="badge badge-warning" v-if="appointment.client_gender==='female'">FEMALE</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Contact No:</td>
                                        <td>{{ appointment.client_contact }}</td>
                                    </tr>
                                    <tr>
                                        <td> Branch: </td>
                                        <td>
                                            <a target="_blank" v-if="user.is_client===0" v-bind:href="'/#/branches/'+appointment.branch_id"> {{ appointment.branch_name }} </a>
                                            <span v-else> {{ appointment.branch_name }} </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> Date: </td>
                                        <td> {{ appointment.transaction_date_formatted }} </td>
                                    </tr>
                                    <tr>
                                        <td> Time: </td>
                                        <td> {{ appointment.transaction_time_formatted }} </td>
                                    </tr>
                                    <tr>
                                        <td> Technician: </td>
                                        <td> {{ appointment.technician_name }} </td>
                                    </tr>
                                    <tr>
                                        <td> Platform: </td>
                                        <td> {{ appointment.platform }} </td>
                                    </tr>
                                    <tr>
                                        <td> Booked By: </td>
                                        <td> {{ appointment.booked_by_name }} ({{ appointment.booked_by_type.toUpperCase() }})</td>
                                    </tr>
                                    <tr>
                                        <td> Status: </td>
                                        <td v-html="appointment.status_formatted"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-8">
                                <div class="row" v-if="services.length>0">
                                    <div class="col-md-12">
                                        <h4>Services</h4>
                                        <table class="table-responsive table table-hover table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Service</th>
                                                <th>Time Booked</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-bind:style="service.item_status==='expired' || service.item_status==='cancelled'? 'text-decoration:line-through':''" v-for="(service,key) in services">
                                                <td>{{ service.item_name }}</td>
                                                <td>
                                                    <span>{{ moment(service.book_start_time).format("hh:mm A") }} - </span>
                                                    <span>{{ moment(service.book_end_time).format("hh:mm A") }}</span>
                                                </td>
                                                <td>{{ service.amount.toFixed(2) }}</td>
                                                <td>
                                                    <span class="badge badge-success" v-if="service.item_status=='completed'">COMPLETED</span>
                                                    <span class="badge badge-info" v-else-if="service.item_status=='reserved'">RESERVED</span>
                                                    <span class="badge badge-danger" v-else>{{ service.item_status }}</span>
                                                </td>
                                                <td>
                                                    <span v-if="service.item_status==='cancelled'"> Reason: {{ service.item_data.cancel_reason }} </span>
                                                    <button v-if="service.item_status==='reserved'" @click="showCancelItemModal(service)" class="btn btn-danger btn-xs">Cancel</button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row"  v-if="products.length>0">
                                    <div class="col-md-12">
                                        <h4>Products</h4>
                                        <table class="table-responsive table table-hover table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Qty.</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-bind:style="product.item_status==='expired' || product.item_status==='cancelled'? 'text-decoration:line-through':''" v-for="(product,key) in products">
                                                <td>{{ product.item_name }}</td>
                                                <td>{{ product.amount.toFixed(2) }}</td>
                                                <td>{{ product.quantity }}</td>
                                                <td>{{ (product.quantity * product.amount).toFixed(2) }}</td>
                                                <td>
                                                    <span class="badge badge-success" v-if="product.item_status=='served'">SERVED</span>
                                                    <span class="badge badge-info" v-else-if="product.item_status=='reserved'">RESERVED</span>
                                                    <span class="badge badge-danger" v-else>{{ product.item_status }}</span>
                                                </td>
                                                <td>
                                                    <button v-if="product.item_status==='reserved'" @click="showCancelItemModal(product)" class="btn btn-danger btn-xs">Cancel</button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div v-if="title==='Queuing' && serving_appointments.indexOf(appointment.id) !== -1 && appointment.acknowledgement_data !== null">
                                    <hr/>
                                    <h4>Complete This Appointment</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <img v-if="appointment.acknowledgement_data.signature !== undefined" :src="appointment.acknowledgement_data.signature" alt="Avatar" style="width:100%" />
                                                <img v-else :src="'/images/white.png'" alt="Avatar" style="width:100%" />
                                                <div class="container2" style="text-align:center">
                                                    <h5><b>{{ appointment.client_name }}</b></h5>
                                                    <p>Client's Signature</p>
                                                </div>
                                            </div><br/>
                                            <i v-if="acknowledgement_signing"><i class="fa fa-pencil"></i> Client is signing...</i>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Select Device:</label>
                                                <select v-model="device" class="form-control" :disabled="acknowledgement_connection">
                                                    <option value="1ssg2362346sd">Acer001</option>
                                                    <option value="2dsgds3262346">Acer002</option>
                                                </select>
                                            </div>

                                            <div class="alert alert-info" v-if="acknowledgement_connection">
                                                Device has received the appointment data, Waiting for acknowledgement. {{ acknowledgement_timer }}
                                            </div>

                                            <button class="btn btn-info btn-block" @click="emitSendData()">Launch Acknowledgement Form</button>
                                            <button class="btn btn-success btn-block" @click="emitCompleteAppointment()">Complete Appointment</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <loading v-else></loading>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showCancelItemModal(cancel_multiple)" v-if="appointment.transaction_status === 'reserved'" class="pull-left btn btn-danger">Cancel Appointment</button>
                        <a target="_blank" v-bind:href="'../../waiver/' + appointment.id +'?token='+ token" class="pull-left btn btn-success">View Waiver</a>
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-bind:id="'cancel-item-modal-'+id" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="cancel.type === 'multiple'">Cancel Appointment </h4>
                        <h4 class="modal-title" v-else>Cancel {{ cancel.type }} </h4>
                    </div>
                    <div class="modal-body">
                        <p v-if="cancel.type !== 'multiple'">Do you want to cancel {{ cancel.type }} ({{ cancel.name }})?</p>
                        <p v-else>Do you want to cancel this entire appointment?</p>
                        <div class="form-group" v-if="cancel.type !== 'product'">
                            <label>Select Reason:</label>
                            <select class="form-control" v-model="cancel.reason">
                                <option value="0">--Select--</option>
                                <option v-bind:value="reason" v-for="reason in reasons.service">{{ reason }}</option>
                                <option value="other">Other, Please specify</option>
                            </select>
                            <br/>
                            <input type="text" v-if="cancel.reason==='other'" v-model="cancel.reason_text" class="form-control" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" v-if="cancel.type === 'multiple'" @click="cancelAppointment" class="btn btn-danger" data-loading-text="Please wait...">Confirm</button>
                        <button type="button" v-else @click="cancelItem" class="btn btn-danger" data-loading-text="Please wait...">Confirm</button>
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Loading from '../etc/Loading.vue';
    export default {
        name: 'AppointmentModal',
        props:[ 'id'],
        components:{ Loading },
        data: function(){
            return {
                cancel:{},
                appointment:{},
                device:'',
                t:null,
                acknowledgement_timer:0,
                acknowledgement_connection:false,
                acknowledgement_signing:false,
                reasons:{
                    service:[
                        'Hair Length',
                        'Monthly Cycle',
                        'Medical Condition',
                        'Skin Surface Condition',
                        'No Show',
                        'Multiple Input'
                    ],
                    product:[]
                },
                cancel_multiple:{
                    id:0,
                    item_type:'multiple',
                    item_name:'',
                    reason:0,
                    reason_text:''
                }
            }
        },
        methods:{
            getAppointment:function(){
                this.appointment = {};
                let u = this;
                if(this.id !== undefined)
                    axios.get('/api/appointment/getAppointment/' + this.id)
                        .then(function (response) {
                            if(response.data)
                                u.appointment = response.data;
                         });
            },
            showCancelItemModal:function(item){
                this.cancel = {
                    id:item.id,
                    type:item.item_type,
                    name:item.item_name,
                    reason:0,
                    reason_text:''
                };
                $("#cancel-item-modal-"+this.id).modal("show");
            },
            makeRequest:function(url, method, data, success_callback, error_callback){
                axios({url:url, method:method, data:data})
                    .then(function () {
                        success_callback();
                    })
                    .catch(function (error) {
                        error_callback(error);
                    });
            },
            cancelItem:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                axios({url:'/api/appointment/cancelItem?token=' + this.token, method:'post', data:this.cancel})
                    .then(function (response) {
                        if(response.data.items_length === 0)
                            u.$socket.emit('unserveClient', u.appointment.branch_id, u.appointment.client_id);

                        toastr.success("Item successfully cancelled.");
                        u.$socket.emit('refreshAppointment', u.id);
                        u.$socket.emit('refreshAppointments', u.appointment.branch_id, u.appointment.client_id);

                        $btn.button('reset');
                        $("#cancel-item-modal-"+u.id).modal('hide');
                        u.getAppointment();
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                        $btn.button('reset');
                    });
            },
            emitCompleteAppointment:function(){
                let u = this;
                axios({url:'/api/appointment/completeAppointment?token=' + this.token, method:'post', data:{appointment:this.appointment}})
                    .then(function () {
                        u.$socket.emit('refreshAppointments', u.appointment.branch_id, u.appointment.client_id);
                        u.$socket.emit('refreshAppointment', u.appointment.id);
                        u.$socket.emit('unserveClient', u.appointment.branch_id, u.appointment.client_id);
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            cancelAppointment:function(){
                let u = this;
                let $btn = $(event.target);

                $btn.button('loading');
                this.cancel.id = this.id;

                this.makeRequest('/api/appointment/cancelAppointment?token=' + this.token, 'post', this.cancel, function(){
                        u.$socket.emit('refreshAppointment', u.id);
                        u.$socket.emit('unserveClient', u.appointment.branch_id, u.appointment.client_id);
                        u.$socket.emit('refreshAppointments', u.appointment.branch_id, u.appointment.client_id);
                        u.getAppointment();
                        toastr.success("Appointment successfully cancelled.");
                        $btn.button('reset');
                        $("#cancel-item-modal-"+u.id).modal('hide');
                    },
                    function(error){
                        XHRCatcher(error);
                        $btn.button('reset');
                    });
            },
            emitSendData(){
                if(this.device === ''){
                    toastr.error("Please select device first.");
                    return false;
                }

                this.$socket.emit('sendAppointmentData', this.appointment, this.device);
            },
            moment:moment
        },
        computed:{
            services:function(){
                if(this.appointment.items !== undefined)
                    return this.appointment.items.filter((item)=>{
                        return item.item_type === 'service'
                    });
                else
                    return [];
            },
            products:function(){
                if(this.appointment.items !== undefined)
                    return this.appointment.items.filter((item)=>{
                        return item.item_type === 'product'
                    });
                else
                    return [];
            },
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            },
            title(){
                return this.$store.state.title;
            },
            serving_appointments(){
                var ids = [];
                for(var x=0;x<this.$store.state.serving.length;x++)
                    ids.push(this.$store.state.serving[x].appointment_id);

                return ids;
            },
        },
        watch:{
            id:function(){
                this.getAppointment();
            }
        },
        mounted:function(){
            let u = this;
            this.$options.sockets.refreshAppointment = function(id){
                if(id === this.id)
                    u.getAppointment();
            };
            this.$options.sockets.receivedAppointmentData = function(data){
                if(data.appointment_id === u.appointment.id){
                    u.acknowledgement_timer = 20;

                    if(u.acknowledgement_timer > 0 && u.acknowledgement_connection)
                        return;

                    u.acknowledgement_connection = true;

                    u.t = setInterval(function(){
                        u.acknowledgement_timer --;
                        if(u.acknowledgement_timer === 0){
                            u.acknowledgement_connection = false;
                            clearInterval(u.t);
                            u.$socket.emit('signingTimeout', u.appointment.id, u.device);
                        }
                    },1000);
                }
            };
            this.$options.sockets.startSigning = function(data){
                if(data.appointment_id === u.appointment.id){
                    u.acknowledgement_signing = true;
                }
            };
            this.$options.sockets.stopSigning = function(data){
                if(data.appointment_id === u.appointment.id){
                    u.acknowledgement_signing = false;
                }
            };
            this.$options.sockets.cancelSigning = function(data){
                if(data.appointment_id === u.appointment.id){
                    u.acknowledgement_signing = false;
                    u.acknowledgement_connection = false;
                }
            };
            this.$options.sockets.sendSignature = function(data){
                if(data.appointment_id === u.appointment.id){
                    u.acknowledgement_data.signature = data.signature;
                    u.$socket.emit('receivedSignature', u.appointment.id);
                }
            };
        }
    }
</script>
<style>
    .card {
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
        width: 100%;
    }

    .card:hover {
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }

    .container2 {
        padding: 2px 16px;
    }
</style>