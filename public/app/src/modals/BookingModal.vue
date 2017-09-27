<template>
    <div id="booking-modal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
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
                            :disable_saving="disable_saving">
                        <div slot="page1">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h3>Select Branch</h3>
                                        <vue-select v-model="newTransaction.branch" :options="branch_selection"></vue-select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h3>Select Date</h3>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <input type="date" v-bind:min="current_date" v-model="newTransaction.transaction_date" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" v-if="show_technicians">
                                        <h3>Select Technician <input type="checkbox" v-model="show_technicians" /></h3>
                                        <vue-select v-model="newTransaction.technician" :options="technician_selection"></vue-select>
                                    </div>
                                    <div v-else>
                                        <button class="btn btn-success btn-md" @click="show_technicians=true"> Select preferred technician.</button>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>
                            </div>
                        </div>
                        <div slot="page2">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <h3>Item Selection</h3>
                                        <select class="form-control large" v-model="show_item_type">
                                            <option value="services">Services</option>
                                            <option value="products">Products</option>
                                        </select>
                                    </div>
                                    <div class="form-group" v-if="show_item_type=='services'">
                                        <h3>Select Service</h3>
                                        <vue-select v-model="service" :options="service_selection"></vue-select>
                                    </div>
                                    <div class="form-group" v-else>
                                        <h3>Select Product</h3>
                                        <vue-select v-model="product" :options="product_selection"></vue-select>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-success btn-lg" @click="addItem()"
                                                v-if="(service!==null && show_item_type=='services')
                                                        || (product!==null && show_item_type=='products')">
                                            ADD TO LIST
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-8" v-if="newTransaction.products !== undefined">
                                    <table class="table-responsive table table-hover table-bordered" v-if="newTransaction.services.length>0">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Service</th>
                                                <th>Start</th>
                                                <th>End</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(service,key) in newTransaction.services">
                                                <td>
                                                    <button @click="removeItem(key, 'services')" class="btn pull-right btn-xs btn-danger">X</button>
                                                </td>
                                                <td>{{ service.name }}</td>
                                                <td>
                                                    <vue-timepicker v-if="key===0 && newTransaction.services.length==1" format="hh:mm A" @change="computeEndTime(key)" v-model="newTransaction.services[key].start_object" :minute-interval="5"></vue-timepicker>
                                                    <span v-else>{{ service.start_object.hh }}:{{ service.start_object.mm }} {{ service.start_object.A }}</span>
                                                </td>
                                                <td>
                                                    <span>{{ service.end_object.hh }}:{{ service.end_object.mm }} {{ service.end_object.A }}</span>
                                                </td>
                                                <td>
                                                    <span class="pull-right">{{ service.price.toFixed(2) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"><h4 class="pull-right" style="font-weight: bold">Total Services:</h4></td>
                                                <td><h4 style="font-weight: bold;text-align:right">{{ total_services.toFixed(2) }}</h4></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table-responsive table table-hover table-bordered" v-if="newTransaction.products.length>0">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Amount</th>
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
                                </div>
                            </div>
                        </div>
                        <div slot="page3">

                        </div>
                    </wizard>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import VueGoodWizard from 'vue-good-wizard';
    import VueSelect from 'vue-select';
    import VueTimepicker from 'vue2-timepicker'
    import Wizard from '../components/Wizard.vue';

    export default {
        name: 'Booking Modal',
        props: ['user', 'branches','toggle','token'],
        components: { Wizard, VueSelect, VueTimepicker  },
        data: function(){
            return {
                current_date:moment().format("YYYY-MM-DD"),
                show_technicians:false,
                show_item_type:"services",
                services:[],
                products:[],
                service:null,
                product:null,
                newTransaction:{},
                steps: [
                    {
                        label: 'Appointment Info',
                        slot: 'page1',
                    },
                    {
                        label: 'Product/Services',
                        slot: 'page2',
                    },
                    {
                        label: 'Waiver',
                        slot: 'page3',
                    }
                ],
                disable_saving:false
            }
        },
        methods: {
            getServices:function(){
                let u = this;
                axios.get('/api/service/getServices/active')
                    .then(function (response) {
                        u.services = response.data;
                    });
            },
            getProducts:function(){
                let u = this;
                axios.get('/api/product/getProducts/active')
                    .then(function (response) {
                        response.data.forEach(function(item){
                            let name = item.product_group_name + " " + item.product_size + " " + item.product_variant;
                            item.name = name;
                            u.products.push(item);
                        });
                    });
            },
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
                    this.newTransaction.products.push({
                        id: this.product.value,
                        name: this.product.label,
                        price: this.product.price,
                        quantity: 1
                    });
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
                this.newTransaction[type].splice(key,1);
            },
            nextClicked(currentPage) {
                let dt = this.newTransaction.transaction_date ;

                if(currentPage===0){
                    if(this.newTransaction.branch === null || this.newTransaction.transaction_date === "" ){
                        toastr.error("Select Branch and Date.");
                        return false;
                    }
                    if(Number(moment(dt).format("X")) < Number(moment(moment().format("YYYY-MM-DD")).format("X"))){
                        toastr.error("Appointment date must be not less than current date and time.");
                        return false;
                    }
                }
                else if(currentPage === 1){
                    if(this.newTransaction.services.length === 0 && this.newTransaction.products.length === 0){
                        toastr.error("Please add at least one service or product to proceed.");
                        return false;
                    }
                }
                else if(currentPage === 2){
                    this.addAppointment();
                }

                return true; //return false if you want to prevent moving to next page
            },
            backClicked(currentPage) {
                if(currentPage === 1){
                    if(confirm("Selected items will be discarded, do you want to go back?")){
                        this.newTransaction.services = [];
                        this.newTransaction.products = [];
                        return true;
                    }
                }
                else if(currentPage === 2){
                    return true;
                }

                return false;
            },
            addAppointment:function(){
                let u = this;
                this.disable_saving = true;
                axios({url:'/api/appointment/addAppointment?token=' + this.token, method:'post', data:this.newTransaction})
                    .then(function () {
                        u.$emit('get_appointments');
                        toastr.success("Successfully booked.");
                        u.disable_saving = false;
                        $("#booking-modal").modal("hide");
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                        u.disable_saving = false;
                    });
            }
        },
        computed:{
            branch_selection:function(){
                var branches=[];
                for(var x=0;x<this.branches.length;x++){
                    branches.push({
                        label:this.branches[x].branch_name,
                        value:this.branches[x].id
                    });
                }
                return branches;
            },
            technician_selection:function(){
                return [{label:"aries", value:1},
                        {label:"buboy", value:2}]
            },
            product_selection:function(){
                var products = [];
                for(var x=0;x<this.products.length;x++){
                    products.push({
                        label: this.products[x].name,
                        value: this.products[x].id,
                        price: this.products[x].product_price
                    });
                }
                return products;
            },
            service_selection:function(){
                var services = [];
                for(var x=0;x<this.services.length;x++){
                    if(this.services[x].service_gender === this.user.gender){
                        var name = this.services[x].service_type_id !== 0 ?  this.services[x].service_name: this.services[x].package_name
                        services.push({
                            label: name + ' ' + this.user.gender.toUpperCase(),
                            value: this.services[x].id,
                            price: this.services[x].service_price,
                            minutes: this.services[x].service_minutes,
                        });
                    }
                }
                return services;
            },
            total_services:function() {
                var total = 0;
                for(var x=0;x<this.newTransaction.services.length;x++){
                    total += this.newTransaction.services[x].price;
                }

                return total;
            },
            total_products:function(){
                var total = 0;
                for(var x=0;x<this.newTransaction.products.length;x++){
                    total += (this.newTransaction.products[x].price * this.newTransaction.products[x].quantity) ;
                }
                return total;
            }
        },
        watch:{
            'newTransaction.branch':function(){
                this.getServices();
                this.getProducts();
            },
            toggle:function(){
                this.newTransaction={
                    branch:null,
                    technician:null,
                    id:0,
                    transaction_date:moment().format("YYYY-MM-DD"),
                    transaction_time:{
                        hh:moment().format("hh"),
                        mm: new moment().round(5,"minutes").format("mm"),
                        A:moment().format("A")
                    },
                    platform:'WEB',
                    services:[],
                    products:[]
                };
                $("#booking-modal").modal("show");
            }
        }
    }
</script>