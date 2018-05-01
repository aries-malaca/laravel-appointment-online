<template>
    <div>
        <div id="appointment-modal" data-backdrop="static" class="modal fade" tabindex="-1" data-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" @click="closeModal()" aria-hidden="true"></button>
                        <h4 class="modal-title">Appointment Details</h4>
                    </div>
                    <div class="modal-body">
                        <div v-if="needsToAcknowledge!==undefined && appointment.id !== undefined">
                            <div class="alert alert-warning" v-if="needsToAcknowledge.id===appointment.id && user.is_client === 1">
                                <strong>Attention! </strong>
                                You need to acknowledge this transaction to book another transaction.
                            </div>
                        </div>
                        <div class="row" v-if="appointment.id !== undefined">
                            <div class="col-md-5">
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
                                            <a target="_blank" v-bind:href="'/#/branches/'+appointment.branch_id"> {{ appointment.branch_name }} </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> Appointment: </td>
                                        <td> {{ appointment.transaction_date_formatted }} {{ appointment.transaction_time_formatted }}</td>
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
                                    <tr v-if="appointment.serve_time !== null">
                                        <td> Served: </td>
                                        <td>
                                            {{ moment(appointment.serve_time).format("hh:mm A") }}
                                            <span v-if="appointment.complete_time !== null"> - {{ moment(appointment.complete_time).format("hh:mm A") }}</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7">
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
                                                    <span :title="'By ' + service.item_data.cancel_by_name +', ' + moment(service.item_data.cancel_datetime).format('MM/DD/YYYY hh:mm A')"
                                                          class="badge badge-danger" v-if="service.item_status==='cancelled'"> Reason: {{ service.item_data.cancel_reason }} </span>
                                                    <button v-if="service.item_status==='reserved' && gate(user, 'appointments','update')" @click="showCancelItemModal(service)" class="btn btn-danger btn-xs">Cancel</button>
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
                                                    <span class="badge badge-success" v-if="product.item_status=='completed'">SERVED</span>
                                                    <span class="badge badge-info" v-else-if="product.item_status=='reserved'">RESERVED</span>
                                                    <span class="badge badge-danger" v-else>{{ product.item_status }}</span>
                                                </td>
                                                <td>
                                                    <button v-if="product.item_status==='reserved' && (gate(user, 'appointments','update') || user.is_client === 1)" @click="showCancelItemModal(product)" class="btn btn-danger btn-xs">Cancel</button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <button v-if="appointment.transaction_status === 'reserved' && title==='Queuing'"
                                        class="btn btn-success btn-sm" @click="showAddServiceForm">Add Service</button>
                                <hr/>

                                <div v-if="title==='Queuing' && serving_appointments.indexOf(appointment.id) !== -1">
                                    <h4 style="text-align:center">Complete Appointment</h4>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="card">
                                                <img v-if="appointment.acknowledgement_data === null" :src="'/images/white.png'" alt="Avatar" style="width:100%" />
                                                <div v-else>
                                                    <img v-if="appointment.acknowledgement_data.signature !== undefined && appointment.acknowledgement_data.signature !== null" :src="appointment.acknowledgement_data.signature" alt="Avatar" style="width:100%" />
                                                    <img v-else :src="'/images/white.png'" alt="Avatar" style="width:100%" />
                                                </div>

                                                <div class="container2" style="text-align:center">
                                                    <h5><b>{{ appointment.client_name }}</b></h5>
                                                    <span>Client's Signature</span>
                                                </div>
                                            </div><br/>
                                            <i v-if="acknowledgement_signing"><i class="fa fa-pencil"></i> Client is signing...</i>
                                            <i v-if="signing_finished"><i class="fa fa-check"></i> Client Finished Signing</i>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group"  v-if="queuing_branch.kiosk_data.length>0">
                                                <label>Select Device for Acknowledgement:</label>
                                                <select v-model="device" class="form-control" :disabled="acknowledgement_connection">
                                                    <option v-for="kiosk in queuing_branch.kiosk_data" :value="kiosk.serial_no">{{ kiosk.alias }}</option>
                                                </select>
                                            </div>
                                            <div class="alert alert-warning" v-else>
                                                No Kiosk available for this branch.
                                            </div>

                                            <div class="alert alert-info" v-if="acknowledgement_connection">
                                                Device has received the appointment data, Waiting for acknowledgement. {{ acknowledgement_timer }}
                                            </div>

                                            <div v-if="!acknowledgement_connection">
                                                <button class="btn btn-info btn-block" @click="emitSendData()" v-if="queuing_branch.kiosk_data.length>0">Send To Device</button>
                                                <button class="btn btn-success btn-block" @click="emitCompleteAppointment()">Complete Appointment</button>
                                                <button class="btn btn-block btn-warning" @click="emitRefreshKiosk()" v-if="queuing_branch.kiosk_data.length>0">Refresh Kiosk</button>
                                            </div>
                                            <div v-else>
                                                <button class="btn btn-danger btn-block" @click="cancelSigning()">Cancel Signing</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="appointment.transaction_status !== 'reserved' && appointment.transaction_status !== 'expired'" class="row">
                                    <div class="col-md-5">
                                        <h4 style="text-align:center">Acknowledgement</h4>
                                        <div class="card">
                                            <img v-if="appointment.acknowledgement_data.signature !== null" :src="appointment.acknowledgement_data.signature" alt="Avatar" style="width:100%" />
                                            <img v-else :src="'/images/white.png'" alt="Avatar" style="width:100%" />
                                            <div class="container2" style="text-align:center">
                                                <h5><b>{{ appointment.client_name }}</b></h5>
                                                <span>Client's Signature</span>
                                            </div>
                                        </div>
                                        <!-- div v-if="appointment.acknowledgement_data.signature === null && user.id === appointment.client_id" >
                                            <br/>
                                            <small>I acknowledge this transaction.</small><br/>
                                            <button class="btn btn-info btn-xs btn-block" @click="acknowledgeAppointment">Proceed</button>
                                        </div -->
                                    </div>
                                    <div class="col-md-7" v-if="appointment.transaction_status !== 'expired'">
                                        <div>
                                            <h4 style="text-align:center">Feedback</h4>
                                            <star-rating :item-size="30"
                                                         inactive-color="#e4eadb"
                                                         active-color="#67d21e"
                                                         :read-only="appointment.client_id !== user.id || !review_writing"
                                                         :increment="1"
                                                         text-class="starer"
                                                         v-model=" review.rating"/>
                                            <div v-if="review_writing && user.id === appointment.client_id">
                                                <textarea class="form-control" v-model="review.feedback" rows="5"></textarea>
                                                <button class="btn btn-info btn-xs pull-right" style="margin-top:10px;" @click="submitReview">Submit Review</button>
                                                <button class="btn btn-warning btn-xs pull-right" style="margin-top:10px; margin-right: 5px;" @click="cancelEditing">Cancel</button>
                                                <br/>
                                            </div>
                                            <div v-else>
                                                <div class="alert alert-info" v-if="appointment.review_date===null">
                                                    No feedback for this Transaction. <br/>
                                                    <a style="text-decoration: underline" v-if="user.id === appointment.client_id" @click="editReview">Write a review</a>
                                                </div>
                                                <div class="mt-comments" v-else>
                                                    <div class="mt-comment">
                                                        <div class="mt-comment-img">
                                                            <img style="height:36px" :src="'../../images/users/' + appointment.client_picture"> </div>
                                                        <div class="mt-comment-body">
                                                            <div class="mt-comment-info">
                                                                <span class="mt-comment-author">{{ appointment.client_name }}</span>
                                                                <span class="mt-comment-date">{{ moment(appointment.review_date).format("MM/DD/YYYY hh:mm A") }}</span>
                                                            </div>
                                                            <div class="mt-comment-text">
                                                                {{ appointment.feedback }}
                                                            </div>
                                                            <div class="mt-comment-details" v-if="!review_writing && user.id === appointment.client_id">
                                                                <a class="btn btn-info btn-xs" @click="editReview">Quick Edit</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" v-if="appointment.waiver_data !== undefined">
                        <button type="button" @click="showCancelItemModal(cancel_multiple)" v-if="appointment.transaction_status === 'reserved' && (gate(user, 'appointments','update') || user.is_client === 1)" class="pull-left btn btn-danger">Cancel Appointment</button>
                        <a target="_blank" v-show="appointment.waiver_data.questions !== undefined" v-bind:href="'../../waiver/' + appointment.id +'?token='+ token" class="pull-left btn btn-success">View Waiver</a>
                        <button type="button" @click="closeModal()" class="btn dark btn-outline">Close</button>
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

        <div v-bind:id="'add-item-modal-'+id" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Add Service</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Service Name</label>
                                <vue-select v-model="newItem" :options="service_selection"></vue-select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="saveItem" class="btn btn-success" data-loading-text="Please wait...">Save</button>
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { StarRating } from 'vue-rate-it';
    import VueSelect from 'vue-select';

    export default {
        name: 'AppointmentModal',
        components:{ StarRating, VueSelect },
        data: function(){
            return {
                cancel:{},
                appointment:{},
                device:'',
                t:null,
                acknowledgement_timer:0,
                acknowledgement_connection:false,
                acknowledgement_signing:false,
                signing_finished:false,
                signing_accepted:true,
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
                },
                review:{
                    appointment_id:0,
                    rating:0,
                    feedback:''
                },
                review_writing:false,
                newItem:{}
            }
        },
        methods:{
            saveItem(){
                let u = this;
                axios({url:'/api/appointment/saveItem?token=' + this.token, method:'post', data: {service:this.newItem, id:this.appointment.id}})
                    .then(function () {
                        u.$socket.emit('refreshAppointment', u.appointment.id);
                        u.$socket.emit('refreshAppointments', u.appointment.branch_id, u.appointment.client_id);
                        toastr.success("Successfully added Service.");
                        $("#add-item-modal-"+u.id).modal('hide');
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            showAddServiceForm(){
                this.newItem = null;
                $("#add-item-modal-"+ this.id).modal("show");
            },
            closeModal(){
                this.$store.commit('appointments/updateViewingID', undefined);
                $("#appointment-modal").modal("hide");
                this.review_writing = false;
            },
            getAppointment:function(){
                this.appointment = {};
                let u = this;
                if(this.id !== undefined)
                    axios.get('/api/appointment/getAppointment/' + this.id)
                        .then(function (response) {
                            if(response.data) {
                                u.appointment = response.data;
                                u.review.rating = response.data.rating;
                            }
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
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                        $btn.button('reset');
                    });
            },
            emitCompleteAppointment:function(){
                let u = this;
                axios({url:'/api/appointment/completeAppointment?token=' + this.token, method:'post', data:this.appointment})
                    .then(function () {
                        u.$socket.emit('refreshAppointments', u.appointment.branch_id, u.appointment.client_id);
                        u.$socket.emit('refreshAppointment', u.appointment.id);
                        u.$socket.emit('unserveClient', u.appointment.branch_id, u.appointment.client_id);
                        u.$socket.emit('completeAppointment', u.appointment);
                        toastr.success("Appointment successfully completed.");
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
                let u = this;
                this.signing_accepted = true;
                if(this.device === ''){
                    toastr.error("Please select device first.");
                    return false;
                }

                this.appointment.acknowledgement_data.signature = null;
                this.$socket.emit('sendAppointmentData', this.appointment, this.device);

                setTimeout(()=>{
                    if(!u.acknowledgement_connection && u.signing_accepted)
                        toastr.error("Device is Offline.");
                },3000)
            },
            emitRefreshKiosk(){
                this.$socket.emit('refreshKiosk', this.device);
            },
            cancelSigning(){
                this.acknowledgement_connection = false;
                this.acknowledgement_signing = false;
                this.signing_accepted = false;
                clearInterval(this.t);
                this.$socket.emit('signingTimeout', this.appointment.id, this.device);
            },
            /*acknowledgeAppointment(){
                let u = this;
                axios({url:'/api/appointment/acknowledgeAppointment?token=' + this.token, method:'post', data:this.appointment})
                    .then(function () {
                        u.$socket.emit('refreshAppointment', u.appointment.id);
                        u.$socket.emit('refreshAppointments', u.appointment.branch_id, u.appointment.client_id);
                        toastr.success("Successfully acknowledged the transaction.");
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },*/
            editReview(){
                this.review = {
                    rating: this.appointment.rating,
                    feedback: this.appointment.feedback,
                    appointment_id: this.appointment.id
                };
                this.review_writing = true;
            },
            cancelEditing(){
                this.review_writing = false;
            },
            submitReview(){
                let u = this;
                axios({url:'/api/review/submitReview?token=' + this.token, method:'post', data:this.review})
                    .then(function () {
                        u.$socket.emit('refreshAppointment', u.appointment.id);
                        toastr.success("Successfully submitted the feedback.");
                        u.review_writing = false;
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            }
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
            configs(){
                return this.$store.state.configs;
            },
            serving_appointments(){
                var ids = [];
                for(var x=0;x<this.$store.state.serving.length;x++)
                    ids.push(this.$store.state.serving[x].appointment_id);

                return ids;
            },
            queuing_branch(){
                return this.$store.state.queuing_branch;
            },
            id(){
                return this.$store.state.appointments.viewing_id;
            },
            needsToAcknowledge(){
                return this.$store.getters['appointments/needsToAcknowledge'];
            },
            service_selection:function(){
                var services = [];

                for(var x=0;x<this._services.length;x++){
                    if(this._services[x].service_gender === this.appointment.client_gender){
                        var name = this._services[x].service_type_id !== 0 ?  this._services[x].service_name: this._services[x].package_name;
                        services.push({
                            label: name + ' ' + this.appointment.client_gender.toUpperCase(),
                            value: this._services[x].id,
                            price: this._services[x].service_price,
                        });
                    }
                }
                return services;
            },
            _services(){
                return this.$store.getters['services/activeServices'];
            },
        },
        watch:{
            id:function(){
                this.getAppointment();
            },
            'queuing_branch.value':function(){
                if(this.queuing_branch.kiosk_data.length>0)
                    this.device = this.queuing_branch.kiosk_data[0].serial_no;
            }
        },
        mounted:function(){
            let u = this;
            this.$options.sockets.refreshAppointment = function(id){
                if(id === this.id)
                    u.getAppointment();
            };
            this.$options.sockets.receiveAppointmentData = function(data){
                if(data.appointment.id === u.appointment.id){

                    if(!data.accepted){
                        u.signing_accepted = false;
                        toastr.error("Other person currently Booking an appointment in Kiosk, please try again later.");
                        return false;
                    }

                    u.acknowledgement_timer = u.configs.ACKNOWLEDGEMENT_TIMEOUT;

                    if(u.acknowledgement_timer > 0 && u.acknowledgement_connection)
                        return;

                    u.acknowledgement_connection = true;
                    u.signing_finished = false;

                    u.t = setInterval(function(){
                        u.acknowledgement_timer --;
                        if(u.acknowledgement_timer === 0)
                            u.cancelSigning();

                    },1000);
                }
            };
            this.$options.sockets.startSigning = function(data){
                if(data=== u.appointment.id && u.acknowledgement_connection){
                    u.acknowledgement_signing = true;
                    u.signing_finished = false;
                }
            };
            this.$options.sockets.stopSigning = function(data){
                if(data.appointment_id === u.appointment.id && u.acknowledgement_connection){
                    u.acknowledgement_signing = false;
                    u.appointment.acknowledgement_data.signature = data.signature;
                }
            };
            this.$options.sockets.cancelSigning = function(data){
                if(data === u.appointment.id && u.acknowledgement_connection){
                    u.acknowledgement_signing = false;
                    u.acknowledgement_connection = false;
                    u.appointment.acknowledgement_data.signature = null;
                }
            };
            this.$options.sockets.finishSigning = function(data){
                if(data.appointment_id === u.appointment.id && u.acknowledgement_connection){
                    u.signing_finished = true;
                    u.acknowledgement_signing = false;
                    u.appointment.acknowledgement_data.signature = data.signature;
                    u.acknowledgement_connection = false
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

    .starer{
        color: #67d21e;
        font-size: 32px;
    }
</style>