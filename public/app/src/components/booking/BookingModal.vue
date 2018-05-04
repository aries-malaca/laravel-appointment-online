<template>
    <div id="booking-modal" data-backdrop="static" class="modal fade" tabindex="-1" data-keyboard="false">
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Appointment Booking Form</h4>
                </div>
                <div class="modal-body">
                    <wizard :steps="steps"
                            :onNext="nextClicked"
                            :onBack="backClicked"
                            :toggle="toggle"
                            :disable_saving="disable_saving"
                            :close_enabled="true">
                        <div slot="page1">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group" v-if="!lock_client">
                                        <h4>Select Client:</h4>
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <a target="_blank" href="/#/clients" type="button" class="btn blue"><i class="fa fa-users"></i></a>
                                            </span>
                                            <vue-select :on-search="searchClients" :options="client_selection"
                                                placeholder="Search for Client..." v-model="newTransaction.client" />
                                        </div>
                                    </div>
                                    <div class="form-group" v-else>
                                        <h4>Client:</h4>
                                        <h3 style="font-weight:bold" v-if="default_client!==null">{{ default_client.label }}</h3>
                                    </div>
                                    <div v-if="newTransaction.client !== undefined" class="form-group">
                                        <div v-if="newTransaction.client !== null">
                                            <span v-html="newTransaction.client.picture_html_big"></span><br/>
                                            <table class="table table-hover table-light">
                                                <tbody>
                                                    <tr>
                                                        <td> Email:</td>
                                                        <td> {{ newTransaction.client.email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td> Mobile:</td>
                                                        <td> {{ newTransaction.client.user_mobile }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td> Gender:</td>
                                                        <td>
                                                            <span v-if="newTransaction.client.gender === 'male'" class="badge badge-success">Male</span>
                                                            <span class="badge badge-warning" v-else>Female</span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="form-group" v-if="default_client === null">
                                        <h4>Select Platform:</h4>
                                        <select v-model="newTransaction.platform" class="form-control">
                                            <option v-for="platform in platforms" :value="platform">{{ platform }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group" v-if="!lock_branch">
                                                <h4>Select Branch:</h4>
                                                <vue-select v-model="newTransaction.branch" :options="branch_selection"></vue-select>
                                            </div>
                                            <div class="form-group" v-else>
                                                <h4>Selected Branch:</h4>
                                                <h3 style="font-weight:bold" v-if="default_branch!==null">{{ default_branch.label }}</h3>
                                            </div>
                                            <table v-if="newTransaction.branch !== undefined" class="table table-hover table-light">
                                                <tbody>
                                                    <tr>
                                                        <td> {{ newTransaction.branch.branch_address }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <h4>Select Date:</h4>
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <input type="date" v-bind:min="current_date" v-model="newTransaction.transaction_date" class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group" v-if="show_technicians">
                                                <h4>Select Technician: <input type="checkbox" v-model="show_technicians" /></h4>
                                                <vue-select v-model="newTransaction.technician" :options="technician_selection"></vue-select>
                                            </div>
                                            <div v-else>
                                                <button class="btn btn-success btn-md btn-block" @click="show_technicians=true"> Select technician.</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div slot="page2">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <h4>Select Category</h4>
                                        <select class="form-control large" v-model="show_item_type">
                                            <option value="services">Services</option>
                                            <option value="products">Products</option>
                                        </select>
                                    </div>
                                    <div class="form-group" v-if="show_item_type=='services'">
                                        <h4>Select Service</h4>
                                        <vue-select v-model="service" :options="service_selection"></vue-select>
                                    </div>
                                    <div class="form-group" v-else>
                                        <h4>Select Product</h4>
                                        <vue-select v-model="product" :options="product_selection"></vue-select>
                                    </div>
                                    <div class="form-group" v-if="(service!==null && show_item_type=='services') || (product!==null && show_item_type=='products')">
                                        <item-card v-if="show_item_type=='products'"
                                                   type="products"
                                                   :picture="product.picture"
                                                   :description="product.description"
                                                />
                                        <item-card v-else
                                                   type="services"
                                                   :picture="service.picture"
                                                   :description="service.description"
                                                />
                                        <button class="btn btn-success btn-lg btn-block" @click="addItem()"> ADD TO LIST <i class="fa fa-chevron-right"></i> </button>
                                    </div>
                                </div>
                                <div class="col-md-8" v-if="newTransaction.products !== undefined" style="overflow-x:scroll;min-height:385px">
                                    <div class="mt-element-list">
                                        <div class="mt-list-head list-news font-white bg-blue" style="padding:10px; !important; text-align:left;">
                                            <div class="list-head-title-container">
                                                <h4 class="list-title">
                                                    Service/Product List
                                                    <span class="pull-right">{{ newTransaction.branch.label }}, {{ moment(newTransaction.transaction_date).format("MM/DD/YYYY") }}</span>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table-responsive table table-hover table-bordered" v-if="newTransaction.services.length>0">
                                        <thead>
                                            <tr>
                                                <th style="width:20px"></th>
                                                <th>Service</th>
                                                <th style="width:150px">Appointment Time</th>
                                                <th style="width:100px">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(service,key) in newTransaction.services">
                                                <td>
                                                    <button @click="removeItem(key, 'services')" class="btn pull-right btn-xs btn-danger">X</button>
                                                </td>
                                                <td>
                                                    {{ service.name }}
                                                </td>
                                                <td>
                                                    <vue-timepicker v-if="key===0 && newTransaction.services.length==1" format="hh:mm A" @change="computeEndTime(key)" v-model="newTransaction.services[key].start_object" :minute-interval="5"></vue-timepicker>
                                                    <span v-else>{{ service.start_object.hh }}:{{ service.start_object.mm }} {{ service.start_object.A }}</span>
                                                </td>
                                                <td>
                                                    <span class="pull-right">{{ service.price.toFixed(2) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><h4 class="pull-right" style="font-weight: bold">Total Services:</h4></td>
                                                <td><h4 style="font-weight: bold;text-align:right">{{ total_services.toFixed(2) }}</h4></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table-responsive table table-hover table-bordered" v-if="newTransaction.products.length>0">
                                        <thead>
                                            <tr>
                                                <th style="width:20px"></th>
                                                <th>Product</th>
                                                <th style="width:60px">Price</th>
                                                <th style="width:60px">Qty</th>
                                                <th style="width:100px">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(product,key) in newTransaction.products">
                                                <td><button @click="removeItem(key, 'products')" class="btn pull-right btn-xs btn-danger">X</button></td>
                                                <td>{{ product.name }}</td>
                                                <td>
                                                    <span class="pull-right">{{ product.price.toFixed(2) }}</span>
                                                </td>
                                                <td>
                                                    <input type="number" style="width:80px" class="form-control" v-model="newTransaction.products[key].quantity"/>
                                                </td>
                                                <td>
                                                    <span class="pull-right">{{ (product.price * product.quantity).toFixed(2) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"><h4 class="pull-right" style="font-weight: bold">Total Products:</h4></td>
                                                <td><h4 style="font-weight: bold;text-align:right">{{ total_products.toFixed(2) }}</h4></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="alert alert-info" v-if="newTransaction.products.length === 0 && newTransaction.services.length === 0">
                                        Service/Product List is empty.
                                    </div>

                                    <div v-if="booking_warning !== false" class="alert alert-danger">
                                        {{ booking_warning }}
                                    </div>
                                    <div v-if="hasOverlappedServices !== false" class="alert alert-danger">
                                        {{ hasOverlappedServices }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div slot="page3">
                            <waiver @sync_waiver_data="syncWaiverData" :the_waiver="newTransaction.waiver_data" :appointment="newTransaction"></waiver>
                        </div>
                    </wizard>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import VueGoodWizard from 'vue-good-wizard';
    import VueSelect from 'vue-select';
    import VueTimepicker from 'vue2-timepicker'
    import Wizard from './Wizard.vue';
    import ItemCard from './ItemCard.vue';
    import Waiver from './Waiver.vue';

    export default {
        name: 'BookingModal',
        props: ['toggle','default_branch', 'lock_branch', 'default_client', 'lock_client', 'queued'],
        components: { Wizard, VueSelect, VueTimepicker, ItemCard, Waiver},
        data: function(){
            return {
                current_date:moment().format("YYYY-MM-DD"),
                show_technicians:false,
                show_item_type:"services",
                service:null,
                product:null,
                clients:[],
                newTransaction:{},
                steps: [    { label: 'Info',  slot: 'page1' },
                            { label: 'Services', slot: 'page2' },
                            { label: 'Waiver',  slot: 'page3' }
                ],
                disable_saving:false,
                queue:[],
                platforms:[
                    'WALK-IN',
                    'CALL-IN',
                    'CHAT'
                ]
            }
        },
        methods: {
            inList:function(id, list){
                for(var x=0;x<this.newTransaction[list].length;x++){
                    if(this.newTransaction[list][x].id === id)
                        return true;
                }
                return false;
            },
            addItem:function(){
                let dt = this.newTransaction.transaction_date ;
                let tm = this.newTransaction.transaction_time ;
                let datetime_start = dt + ' ' + tm.hh + ":" + tm.mm + " " + tm.A;

                if(this.show_item_type === 'services'){
                    if(this.inList(this.service.value, 'services')){
                        toastr.error("Service already in list.");
                        return false;
                    }
                    this.newTransaction.services.push({
                        id: this.service.value,
                        name: this.service.label,
                        price: this.service.price,
                        service_type_id:this.service.service_type_id,
                        service_type_data: this.service.service_type_data,
                        minutes: this.service.minutes,
                        start: this.newTransaction.services.length===0 ? datetime_start :this.newTransaction.services[this.newTransaction.services.length-1].end,
                        start_object: this.newTransaction.services.length===0? this.dateTimeToObject(datetime_start) : this.newTransaction.services[this.newTransaction.services.length-1].end_object,
                        end: this.newTransaction.services.length===0 ? this.addMinutes(datetime_start, this.service.minutes) : this.addMinutes(this.newTransaction.services[this.newTransaction.services.length-1].end, this.service.minutes),
                        end_object: this.newTransaction.services.length===0 ? this.dateTimeToObject(this.addMinutes(datetime_start, this.service.minutes)) : this.dateTimeToObject(this.addMinutes(this.newTransaction.services[this.newTransaction.services.length-1].end, this.service.minutes))
                    });
                }
                else{
                    if(this.inList(this.product.value, 'products')){
                        toastr.error("Product already in list.");
                        return false;
                    }
                    this.newTransaction.products.push({ id: this.product.value,
                                                        name: this.product.label,
                                                        price: this.product.price,
                                                        quantity: 1});
                }
            },
            addMinutes:function(datetime, minutes){
                return moment(datetime).add(minutes,"minutes").format("YYYY-MM-DD hh:mm A");
            },
            dateTimeToObject:function(datetime){
                return {
                    date: moment(datetime).format("YYYY-MM-DD"),
                    hh: moment(datetime).format("hh"),
                    mm: moment(datetime).format("mm"),
                    A: moment(datetime).format("A"),
                }
            },
            ObjectToDateTime:function(object){
                return moment(object.date+" "+object.hh+":"+object.mm+" "+object.A).format("YYYY-MM-DD hh:mm A");
            },
            computeEndTime:function(key){
                this.newTransaction.services[key].start_object.date = this.newTransaction.transaction_date;
                this.newTransaction.services[key].start = this.ObjectToDateTime(this.newTransaction.services[key].start_object);
                this.newTransaction.services[key].end = this.addMinutes(this.newTransaction.services[key].start,this.newTransaction.services[key].minutes) ;
                this.newTransaction.services[key].end_object = this.dateTimeToObject(this.newTransaction.services[key].end);
            },
            removeItem:function(key, type){
                var starting = {
                    datetime: this.newTransaction[type][key].start
                };
                this.newTransaction[type].splice(key,1);

                if(type === 'services')
                    this.recomputeTimes(key, starting.datetime);
            },
            recomputeTimes:function(key, start){
                for(var x=key;x<this.newTransaction.services.length;x++){
                    this.newTransaction.services[x].start_object =  x > 0 ?
                                                    this.newTransaction.services[x-1].end_object: this.dateTimeToObject(start);

                    this.newTransaction.services[x].start = x > 0 ?
                                                    this.newTransaction.services[x-1].end: start;

                    this.newTransaction.services[x].end = this.addMinutes(this.newTransaction.services[x].start,this.newTransaction.services[x].minutes);
                    this.newTransaction.services[x].end_object = this.dateTimeToObject(this.newTransaction.services[x].end);
                }
            },
            nextClicked(currentPage) {
                let dt = this.newTransaction.transaction_date ;

                if(currentPage===0){
                    if(this.newTransaction.branch === null || this.newTransaction.transaction_date === "" || this.newTransaction.client === null ){
                        toastr.error("Please set Client, Branch and Date.");
                        return false;
                    }
                    if(Number(moment(dt).format("X")) < Number(moment(moment().format("YYYY-MM-DD")).format("X"))){
                        toastr.error("Appointment date must be not less than current date and time.");
                        return false;
                    }

                }
                else if(currentPage === 1){
                    if(this.newTransaction.services.length === 0 && this.user.is_client === 1){
                        toastr.error("Please add at least one service to proceed.");
                        return false;
                    }

                    if(this.newTransaction.services.length === 0 && this.newTransaction.products.length === 0 && this.user.is_client === 1){
                        toastr.error("Please add at least one service/product to proceed.");
                        return false;
                    }

                    for(var x=0;x<this.newTransaction.services.length;x++){
                        var obj = this.newTransaction.services[x].start_object;
                        if(obj.A === "" || obj.hh === "" || obj.mm === ""){
                            toastr.error("Invalid time for service #" + (x+1) + ".");
                            return false;
                        }
                    }

                    let error_booking = this.booking_warning;
                    if(error_booking){
                        toastr.error(error_booking);
                        return false;
                    }

                    let error_overlap = this.hasOverlappedServices;
                    if(error_overlap){
                        toastr.error(error_overlap);
                        return false;
                    }
                }
                else if(currentPage === 2){
                    for(var x=0;x<this.newTransaction.waiver_data.questions.length;x++){
                        if(this.newTransaction.waiver_data.questions[x].data.disallowed !== undefined && this.newTransaction.waiver_data.questions[x].selected){
                            for(var y=0;y<this.newTransaction.services.length;y++){
                                if(this.newTransaction.waiver_data.questions[x].data.disallowed.indexOf(this.newTransaction.services[y].id) !== -1 ){
                                    toastr.error("Cannot book " + this.newTransaction.services[y].name);
                                    return false;
                                }
                            }
                        }
                    }

                    this.addAppointment();
                }
                return true; //return false if you want to prevent moving to next page
            },
            backClicked(currentPage) {
                if(currentPage === 0)
                    $("#booking-modal").modal("hide");

                if(currentPage === 1){
                    if(confirm("Selected items will be discarded, do you want to go back?")){
                        this.newTransaction.services = [];
                        this.newTransaction.products = [];
                        this.service = null;
                        this.product = null;
                        return true;
                    }
                }
                else if(currentPage === 2)
                    return true;

                return false;
            },
            addAppointment:function(){
                if(this.disable_saving)
                    return false;

                if(!confirm("Confirm appointment booking?"))
                    return false;

                let u = this;
                this.disable_saving = true;
                axios({url:'/api/appointment/addAppointment?token=' + this.token, method:'post', data:this.newTransaction})
                    .then(function () {
                        u.$emit('get_appointments');
                        toastr.success("Appointment successfully booked.");
                        u.disable_saving = false;
                        $("#booking-modal").modal("hide");
                        u.$socket.emit('refreshAppointments', u.newTransaction.branch.value, u.newTransaction.client.value);
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                        u.disable_saving = false;
                    });
            },
            searchClients:function(keyword,loading){
                loading(true);
                let u = this;
                axios.get('/api/client/searchClients', {params:{keyword:keyword}})
                    .then(function (response) {
                        u.clients = [];
                        response.data.forEach(function(item){
                            u.clients.push(item);
                        });
                        loading(false);
                    });
            },
            syncWaiverData:function(data){
                this.newTransaction.waiver_data = data;
                this.newTransaction.waiver_data.disallow = data.disallow;
            },
            getTechnicians:function(){
                let u =this;
                axios.get('/api/technician/getBranchTechnicians/'+this.newTransaction.branch.value+'/'+this.newTransaction.transaction_date)
                .then(function (response) {
                    u.$store.commit('updateQueuingTechnicians',response.data);
                });
            },
            getQueue:function(){
                let u =this;
                axios.get('/api/appointment/getAppointments/queue/'+this.newTransaction.branch.value+'/'+this.newTransaction.transaction_date)
                    .then(function (response) {
                        u.queue = response.data;
                    });
            },
            isConflicted:function(newData, oldData){
                if(oldData.transaction_status !== 'reserved')
                    return false;

                let a = Number(moment(newData.start).format("X"));
                let b = Number(moment(newData.end).format("X"));
                let c = Number(moment(oldData.book_start_time).format("X"));
                let d = Number(moment(oldData.book_end_time).format("X"));

                return ((c<=a && d<=b &&d>=a) ||  (b<=d && c<=b &&a>=c) || (c<=b && a<=c) || (a<=d && c<= a));
            }
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
            branches(){
                return this.$store.getters['branches/activeBranches'];
            },
            products(){
                return this.$store.getters['products/activeProducts'].map(function(item){
                    item.name = item.product_group_name + " " + item.product_size + " " + item.product_variant;
                    return item;
                });
            },
            services(){
                return this.$store.getters['services/activeServices'];
            },
            filtered_queue:function(){
                var queue = [];
                for(var x=0;x<this.queue.length;x++){
                    for(var y=0;y<this.queue[x].items.length;y++){
                        this.queue[x].items[y].client_id = this.queue[x].client_id;
                        if(this.queue[x].items[y].item_status==='reserved')
                            queue.push(this.queue[x].items[y]);
                    }
                }
                return queue;
            },
            filtered_technician_queue:function(){
                var queue = [];
                if(this.newTransaction.technician !== null){
                    for(var x=0;x<this.queue.length;x++){
                        for(var y=0;y<this.queue[x].items.length;y++)
                            if(this.queue[x].items[y].item_status==='reserved' && this.queue[x].technician_id === this.newTransaction.technician.value)
                                queue.push(this.queue[x].items[y]);
                    }
                }
                return queue;
            },
            booking_warning:function(){
                if(this.branch_operating_schedule===false)
                    return "Selected Branch were closed in selected date/time.";


                var today = this.newTransaction.transaction_date;
                var allowance = (this.configs!==undefined)?Number(this.configs.SERVICE_TIME_ALLOWANCE):0;
                var extension = 0;

                if(this.newTransaction.branch!==undefined)
                    extension = this.newTransaction.branch.branch_data.type==='stand-alone'?Number(this.newTransaction.branch.branch_data.extension_minutes):0;

                for(var x=0;x<this.newTransaction.services.length;x++){
                    var s_start = Number(moment(this.newTransaction.services[x].start).format("X"));
                    var s_end = Number(moment(this.newTransaction.services[x].end).format("X"));
                    var operating_start = Number(moment(today+" "+this.branch_operating_schedule.start).format("X"));
                    var operating_end = Number(moment(today+" "+this.branch_operating_schedule.end).format("X"));
                    var hits = 0;

                    if((Number(s_start - moment().format("X"))) < (allowance*60) && this.user.is_client === 1)
                        return "Appointment Time start must be " + (allowance/60) + ' hours after the current time.';


                    if(s_start <  operating_start || s_start > operating_end || (operating_end + (60 * extension) )  < s_end )
                        return "Branch operating schedule is be between "+ moment(today+" "+this.branch_operating_schedule.start).format("hh:mm A") +" to " +
                                    moment(today+" "+this.branch_operating_schedule.end).format("hh:mm A") +".";

                    if(this.newTransaction.technician !== null){
                        var tech = this.newTransaction.technician;
                        var technician_start = Number(moment(today+" "+tech.schedule.start).format("X"));
                        var technician_end = Number(moment(today+" "+tech.schedule.end).format("X"));

                        if(s_start <  technician_start || s_start > technician_end)
                            return "Technician: " + tech.label + " only available between "+ moment(today+" "+ tech.schedule.start).format("hh:mm A")
                                    +" to " + moment(today+" "+tech.schedule.end).format("hh:mm A") +".";
                    }

                    for(var y=0;y<this.filtered_technician_queue.length;y++){
                        if(this.isConflicted(this.newTransaction.services[x], this.filtered_technician_queue[y]))
                            return "Technician selected has already booked the same time for service #" + (x+1);
                    }

                    for(var y=0;y<this.filtered_queue.length;y++){
                        if(this.isConflicted(this.newTransaction.services[x], this.filtered_queue[y])){
                            hits++;
                            if(this.newTransaction.branch.rooms < hits)
                                return "No room available for the time of service #" + (x+1);

                            if(this.technician_selection.length < hits)
                                return "No technician available for the time of service #" + (x+1);
                        }
                        if(this.isConflicted(this.newTransaction.services[x], this.filtered_queue[y])
                                && this.newTransaction.client.value === this.filtered_queue[y].client_id){
                            return "Already have existing scheduled booking for selected time at service #" + (x+1);
                        }
                    }
                }

                return false;
            },
            branch_selection:function(){
                var branches=[];
                for(var x=0;x<this.branches.length;x++){
                    branches.push({ label:this.branches[x].branch_name,
                                    value:this.branches[x].id,
                                    rooms:this.branches[x].rooms_count,
                                    products:this.branches[x].products,
                                    services:this.branches[x].services,
                                    schedules:this.branches[x].schedules_original,
                                    branch_data:this.branches[x].branch_data,
                                    branch_address:this.branches[x].branch_address,
                                    cluster_data:this.branches[x].cluster_data,
                                 });
                }
                return branches;
            },
            client_selection:function(){
                var clients=[];
                for(var x=0;x<this.clients.length;x++){
                    clients.push({  label:this.clients[x].username,
                                    value:this.clients[x].id,
                                    gender:this.clients[x].gender,
                                    email:this.clients[x].email,
                                    user_mobile:this.clients[x].user_mobile,
                                    picture_html_big:this.clients[x].picture_html_big,
                    });
                }
                return clients;
            },
            technician_selection:function(){
                this.newTransaction.technician = null;
                var technicians=[];
                for(var x=0;x<this.$store.state.queuing_technicians.length;x++){
                    technicians.push({   label:this.$store.state.queuing_technicians[x].name,
                                        value:this.$store.state.queuing_technicians[x].id,
                                        schedule:this.$store.state.queuing_technicians[x].schedule,
                                        employee_id:this.$store.state.queuing_technicians[x].employee_id,
                                        attendance:false,
                    });
                }
                return technicians;
            },
            product_selection:function(){
                var products = [];

                if(this.newTransaction.branch === undefined)
                    return products;

                for(var x=0;x<this.products.length;x++){
                    if(this.newTransaction.branch.products !== null && this.newTransaction.branch.products !== undefined)
                        if(this.newTransaction.branch.products.indexOf(this.products[x].id) !== -1)
                            products.push({ label: this.products[x].name,
                                            value: this.products[x].id,
                                            price: this.products[x].product_price,
                                            picture: this.products[x].product_picture,
                                            description: this.products[x].product_description
                            });
                }
                return products;
            },
            service_selection:function(){
                var services = [];

                if(this.newTransaction.branch === undefined)
                    return services;

                for(var x=0;x<this.services.length;x++){
                    if(this.newTransaction.branch.services !== null && this.newTransaction.branch.services !== undefined)
                        if(this.newTransaction.branch.services.indexOf(this.services[x].id) !== -1)
                            if(this.newTransaction.client !== null){
                                if(this.services[x].service_gender === this.newTransaction.client.gender){
                                    var name = this.services[x].service_type_id !== 0 ?  this.services[x].service_name: this.services[x].package_name
                                    services.push({ label: name + ' ' + this.newTransaction.client.gender.toUpperCase(),
                                        value: this.services[x].id,
                                        price: this.services[x].service_price,
                                        minutes: this.services[x].service_minutes,
                                        picture: this.services[x].service_picture,
                                        description: this.services[x].service_description,
                                        service_type_data: this.services[x].service_type_data,
                                        service_type_id: this.services[x].service_type_id
                                    });
                                }
                            }
                }
                return services;
            },
            total_services:function() {
                var total = 0;
                for(var x=0;x<this.newTransaction.services.length;x++)
                    total += this.newTransaction.services[x].price;

                return total;
            },
            total_products:function(){
                var total = 0;
                for(var x=0;x<this.newTransaction.products.length;x++)
                    total += (this.newTransaction.products[x].price * this.newTransaction.products[x].quantity);

                return total;
            },
            branch_operating_schedule:function(){
                if(this.newTransaction.branch !== undefined && this.newTransaction.transaction_date !==""){
                    if(this.newTransaction.services.length>0){
                        var f = this.newTransaction.services[0].start;

                        for(var x=0;x<this.newTransaction.branch.schedules.length;x++){
                            var e = this.newTransaction.branch.schedules[x];

                            if( Number(moment(e.date_start).format("X") <= Number(moment(f).format("X")) ) &&
                                Number(moment(e.date_end).format("X") >= Number(moment(f).format("X"))) ){

                                if(e.schedule_type === 'closed')
                                    return false;
                                else if(e.schedule_type === 'custom')
                                    return e.schedule_data[Number(moment(f).format("e"))];
                            }
                        }
                    }

                    for(var x=0;x<this.newTransaction.branch.schedules.length;x++){
                        if(this.newTransaction.branch.schedules[x].schedule_type === 'regular')
                            return this.newTransaction.branch.schedules[x].schedule_data[Number(moment(f).format("e"))];
                    }
                }
                return false;
            },
            hasOverlappedServices:function(){
                for(var x=0; x<this.newTransaction.services.length;x++){
                    var temp_a = this.newTransaction.services[x].service_type_data.restricted;

                    for(var y=0;y<this.newTransaction.services.length;y++){
                        if(y !== x){
                            var temp_b = this.newTransaction.services[y];
                            var condition_1 = temp_a.indexOf(0) !== -1;
                            var condition_2 = temp_a.indexOf(this.newTransaction.services[y].service_type_id) !== -1;
                            let u = this;
                            var condition_3 = temp_a.filter(function(i){
                                if(temp_b.service_type_id===0 && u.newTransaction.services[x].service_type_id === 0)
                                    return temp_b.service_type_data.restricted.indexOf(i) !== -1;
                            }).length > 0;
                            if(condition_1 || condition_2 || condition_3)
                                return this.newTransaction.services[x].name + " and " + this.newTransaction.services[y].name + " cannot be booked at the same time.";
                        }
                    }
                }

                return false;
            }
        },
        watch:{
            show_technicians(){
                this.newTransaction.technician = null;
            },
            'newTransaction.branch':function(){
                this.getTechnicians();
                this.getQueue();
            },
            'newTransaction.transaction_date':function(){
                this.getTechnicians();
                this.getQueue();
            },
            'newTransaction.technician':function(){
                if(this.newTransaction.technician !==null){
                    let u = this;
                    u.newTransaction.technician.attendance = false;

                    if(this.newTransaction.branch.cluster_data.ems_supported)
                        axios.get(this.newTransaction.branch.cluster_data.ems_server + this.configs.GET_TECHNICIAN_ATTENDANCE + this.newTransaction.technician.employee_id+'/'+this.newTransaction.transaction_date)
                            .then(function (response) {
                                u.newTransaction.technician.attendance = response.data;
                            }).catch(function(){
                                u.newTransaction.technician.attendance = false;
                            });
                }
            },
            toggle:function(){
                this.service = null;
                var allowance = (this.configs!==undefined)?Number(this.configs.SERVICE_TIME_ALLOWANCE):0;
                this.clients = [];
                this.newTransaction={
                    transaction_type:'branch_booking',
                    branch:this.default_branch!==null?{
                        value: this.default_branch.value,
                        label : this.default_branch.label,
                        rooms : (this.default_branch.rooms=== undefined?0:this.default_branch.rooms),
                        products : this.default_branch.products,
                        services : this.default_branch.services,
                        schedules : this.default_branch.schedules,
                        branch_data : this.default_branch.branch_data,
                        branch_address:this.default_branch.branch_address,
                        cluster_data:this.default_branch.cluster_data,
                    }:null,
                    client:this.default_client !== null ? (this.default_client.value!==undefined? { value: this.default_client.value,
                                                        label : this.default_client.label,
                                                        gender : this.default_client.gender,
                                                        email : this.default_client.email,
                                                        user_mobile: this.default_client.user_mobile,
                                                        picture_html_big : this.default_client.picture_html_big}: null
                    ):null,
                    technician:null,
                    id:0,
                    transaction_date:moment().format("YYYY-MM-DD"),
                    transaction_time:{   hh:moment().add((allowance/60),"hours").format("hh"),
                                         mm: new moment().add((allowance/60),"hours").add(5,"minutes").round(5,"minutes").format("mm"),
                                         A:moment().add((allowance/60),"hours").format("A")
                                    },
                    platform:this.default_client===null?'WALK-IN':'WEB',
                    services:[],
                    products:[]
                };
                $("#booking-modal").modal("show");
            }
        },
        mounted:function(){
            let u = this;
            this.$options.sockets.refreshAppointments = function(data){
                if(u.newTransaction.branch !== null && u.newTransaction.branch !== undefined)
                    if(data.branch_id === u.newTransaction.branch.value)
                        u.getQueue();
            };
        }
    }
</script>