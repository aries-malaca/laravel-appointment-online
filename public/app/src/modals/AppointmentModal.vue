<template>
    <div>
        <div v-bind:id="'appointment-modal-'+ id " class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-full">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Appointment Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
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
                                                <th>Time Served</th>
                                                <th>Time Completed</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-bind:style="service.item_status==='expired' || service.item_status==='cancelled'? 'text-decoration:line-through':''" v-for="(service,key) in services">
                                                <td v-class="">{{ service.item_name }}</td>
                                                <td>
                                                    <span>{{ moment(service.book_start_time).format("hh:mm A") }} - </span>
                                                    <span>{{ moment(service.book_end_time).format("hh:mm A") }}</span>
                                                </td>
                                                <td>
                                                    <span v-if="service.serve_time!==null">{{ moment(service.serve_time).format("hh:mm A") }}</span>
                                                </td>
                                                <td>
                                                    <span v-if="service.complete_time!==null">{{ moment(service.complete_time).format("hh:mm A") }}</span>
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
                                            <tr  v-bind:style="product.item_status==='expired' || product.item_status==='cancelled'? 'text-decoration:line-through':''" v-for="(product,key) in products">
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
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showCancelItemModal(cancel_multiple)" v-if="appointment.transaction_status === 'reserved'" class="pull-left btn btn-danger">Cancel Appointment</button>
                        <a target="_blank" v-bind:href="'../../waiver/' + appointment.id +'?token='+ token" class="pull-left btn btn-success">View Waiver</a>
                        <button type="button" @click="closeModal" class="btn dark btn-outline">Close</button>
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
    export default {
        name: 'AppointmentModal',
        props:['token', 'id', 'user'],
        data: function(){
            return {
                cancel:{},
                appointment:{},
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
                this.makeRequest('/api/appointment/cancelItem?token=' + this.token, 'patch', this.cancel, function(){
                    u.getAppointment();
                    toastr.success("Item successfully cancelled.");
                    u.$socket.emit('refreshAppointment', u.id);
                    u.$socket.emit('refreshAppointments', u.appointment.branch_id, u.appointment.client_id);
                    $btn.button('reset');
                    $("#cancel-item-modal-"+u.id).modal('hide');
                },
                function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            cancelAppointment:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                this.cancel.id = this.id;
                this.makeRequest('/api/appointment/cancelAppointment?token=' + this.token, 'patch', this.cancel, function(){
                        u.$socket.emit('refreshAppointment', u.id);
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
            closeModal:function(){
                this.$emit('close_modal');
            },
            moment:moment
        },
        computed:{
            services:function(){
                var items = [];
                if(this.appointment.items!==undefined)
                    for(var x=0;x<this.appointment.items.length;x++){
                        if(this.appointment.items[x].item_type === 'service'){
                            items.push(this.appointment.items[x]);
                        }
                    }
                return items;
            },
            products:function(){
                var items = [];
                if(this.appointment.items!==undefined)
                    for(var x=0;x<this.appointment.items.length;x++){
                        if(this.appointment.items[x].item_type === 'product'){
                            items.push(this.appointment.items[x]);
                        }
                    }
                return items;
            }
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
        }
    }
</script>