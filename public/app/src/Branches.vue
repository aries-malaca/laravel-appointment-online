<template>
    <div class="branches">
        <div class="portlet light" v-show="view=='list'">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> Branches and Clusters </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#branches" data-toggle="tab">Branches</a>
                    </li>
                    <li>
                        <a href="#clusters" data-toggle="tab">Clusters</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="branches">
                        <button type="button" @click="showAddBranchModal" class="btn green-meadow">New Branch</button>
                        <br/><br/>
                        <data-table :columns="branchTable.columns" :rows="branches" :paginate="true"
                            :onClick="branchTable.rowClicked" styleClass="table table-bordered table-hover table-striped" />
                    </div>
                    <div class="tab-pane" id="clusters">
                        <button type="button" @click="showAddClusterModal" class="btn green-meadow">New Cluster</button>
                        <br/><br/>
                        <data-table :columns="clusterTable.columns" :rows="clusters" :paginate="true"
                            :onClick="clusterTable.rowClicked" styleClass="table table-bordered table-hover table-striped" />
                    </div>
                </div>
            </div>
        </div>

        <branch-profile @edit_branch="editBranch" @back="view='list',view_id=0" :show="view=='single'" :with_back="true"
                :token="token" @update_branch="getBranches" :configs="configs" :id="view_id" />

        <div class="modal fade" id="add-branch-modal" data-backdrop="static" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newBranch.id==0">Add Branch</h4>
                        <h4 class="modal-title" v-else>Edit Branch</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">ID</label>
                                    <input type="number" @change="searchBranch(newBranch.search_id)"  class="form-control" v-model="newBranch.search_id" />
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="control-label">Branch Name</label>
                                    <input type="text" class="form-control" v-model="newBranch.branch_name" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">Branch Code</label>
                                    <input type="text" class="form-control" v-model="newBranch.branch_code" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Classification</label>
                                    <select class="form-control" v-model="newBranch.branch_classification">
                                        <option value="company-owned">Co-owned</option>
                                        <option value="franchised">Franchised</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Region</label>
                                    <select class="form-control" @change="newBranch.city_id=undefined" v-model="newBranch.region_id">
                                        <option v-for="region in regions" v-bind:value="region.id">{{ region.region_name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">City</label>
                                    <select class="form-control" v-model="newBranch.city_id">
                                        <option v-if="city.region_id==newBranch.region_id" v-for="city in cities" v-bind:value="city.id">{{ city.city_name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="control-label">Address</label>
                                    <textarea class="form-control" v-model="newBranch.branch_address"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Cluster</label>
                                    <select v-model="newBranch.cluster_id" class="form-control">
                                        <option v-for="cluster in clusters" v-bind:value="cluster.id">{{ cluster.cluster_name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">Rooms</label>
                                    <input type="number" class="form-control" v-model="newBranch.rooms_count"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Payment Methods</label>
                                    <input type="text" class="form-control" placeholder="Cash" v-model="newBranch.payment_methods"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Email</label>
                                    <input type="text" class="form-control" v-model="newBranch.branch_email" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Contact No.</label>
                                    <input type="text" class="form-control" v-model="newBranch.branch_contact" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Contact Person</label>
                                    <input type="text" class="form-control" v-model="newBranch.branch_contact_person" />
                                </div>
                            </div>
                            <div class="col-md-3" v-if="newBranch.social_media_accounts!==undefined">
                                <label class="control-label">Facebook</label>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <a target="_blank" v-bind:href="'//facebook.com/'+newBranch.social_media_accounts[0]" class="btn blue" type="button"><i class="fa fa-facebook-official"></i></a>
                                    </span>
                                    <input type="text" class="form-control" v-model="newBranch.social_media_accounts[0]"/>
                                </div>
                            </div>
                            <div class="col-md-3" v-if="newBranch.social_media_accounts!==undefined">
                                <label class="control-label">Twitter</label>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <a target="_blank" v-bind:href="'//twitter.com/'+newBranch.social_media_accounts[1]" class="btn blue" type="button"><i class="fa fa-twitter-square"></i></a>
                                    </span>
                                    <input type="text" class="form-control" v-model="newBranch.social_media_accounts[1]"/>
                                </div>
                            </div>
                        </div>
                        <div class="row" @mouseout="confirmMap">
                            <div class="col-md-12">
                                <div id="map-canvas"></div>
                                <input type="hidden" id="position" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="control-label">Directions</label>
                                <textarea rows="2" class="form-control" v-model="newBranch.directions"></textarea>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Welcome Message</label>
                                    <textarea rows="2" class="form-control" v-model="newBranch.welcome_message"></textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Opening Date</label>
                                    <input type="date" class="form-control" v-model="newBranch.opening_date" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newBranch.id==0" @click="addBranch($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateBranch($event)" data-loading-text="Updating..." class="btn green">Save</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <div class="modal fade" id="add-cluster-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newCluster.id==0">Add Cluster</h4>
                        <h4 class="modal-title" v-else>Edit Cluster</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Cluster Name</label>
                                    <input type="text" class="form-control" v-model="newCluster.cluster_name" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Cluster Owner</label>
                                    <input type="text" class="form-control" v-model="newCluster.cluster_owner" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Cluster Email</label>
                                    <input type="text" class="form-control" v-model="newCluster.cluster_email" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Services</label>
                                    <vue-select multiple v-model="newCluster.services" :options="service_selection">
                                    </vue-select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Products</label>
                                    <vue-select multiple v-model="newCluster.products" :options="product_selection">
                                    </vue-select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newCluster.id==0" @click="addCluster($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateCluster($event)" data-loading-text="Updating..." class="btn green">Save</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>
</template>

<script>
    import DataTable from './components/DataTable.vue';
    import VueSelect from 'vue-select';
    import BranchProfile from './profiles/BranchProfile.vue';

    export default {
        name: 'Branches',
        components:{ DataTable, VueSelect, BranchProfile },
        props: ['token','configs'],
        data: function(){
            return {
                title: 'Branches',
                view:'list',
                view_id:0,
                branches:[],
                clusters:[],
                services:[],
                products:[],
                cities:[],
                regions:[],
                newBranch:{},
                newCluster:{},
                branchTable:{
                    columns: [
                        { label: 'Branch Name', field: 'branch_name', filterable: true },
                        { label: 'Branch Code', field: 'branch_code', filterable: true },
                        { label: 'Contact No.', field: 'branch_contact', filterable: true },
                        { label: 'Classification', field: 'branch_classification_html',html:true, filterable: true },
                    ],
                    rowClicked: this.viewBranch,
                },
                clusterTable:{
                    columns: [
                        { label: 'Cluster Name', field: 'cluster_name', filterable: true },
                        { label: 'Cluster Owner', field: 'cluster_owner', filterable: true },
                        { label: 'Email', field: 'cluster_email', filterable: true }
                    ],
                    rowClicked: this.viewCluster,
                }
            }
        },
        methods:{
            getData:function(url, field){
                let u = this;
                axios.get(url)
                .then(function (response) {
                    u[field] = [];
                    response.data.forEach(function(item){
                        if(field == 'clusters'){
                            item.services = JSON.parse(item.services);
                            item.products = JSON.parse(item.products);
                        }
                        if(field == 'branches'){
                            if(item.branch_classification == 'company-owned')
                                item.branch_classification_html = '<span class="badge badge-success">Co-owned</span>';
                            else
                                item.branch_classification_html = '<span class="badge badge-warning">Franchised</span>';
                        }
                        u[field].push(item);
                    });
                });
            },
            showAddBranchModal:function(){
                this.newBranch = {
                    id:0,
                    search_id:0,
                    branch_name:'',
                    branch_code:'',
                    region_id:0,
                    city_id:0,
                    branch_address:'',
                    branch_email:'',
                    branch_contact:'',
                    branch_contact_person:'',
                    opening_date:moment().format("YYYY-MM-DD"),
                    rooms_count:1,
                    social_media_accounts:['',''],
                    directions:'',
                    map_coordinates:{
                        lat:14.5698,
                        long:121.0167
                    },
                    branch_classification:'company-owned',
                    payment_methods:'',
                    welcome_message:'',
                    branch_pictures:[],
                    cluster_id:0,
                };
                $("#add-branch-modal").modal("show");
                let u = this;
                setTimeout(function(){
                    initMap(Number(u.newBranch.map_coordinates.lat),Number(u.newBranch.map_coordinates.long));
                },500);
            },
            showAddClusterModal:function(){
                this.newCluster ={
                    id:0,
                    cluster_name:'',
                    cluster_owner:'',
                    cluster_email:'',
                    services:[],
                    products:[]
                };
                $("#add-cluster-modal").modal("show");
            },
            getCities:function () {
                this.getData('/api/city/getCities', 'cities');
            },
            getRegions:function () {
                this.getData('/api/region/getRegions', 'regions');
            },
            getServices:function () {
                this.getData('/api/service/getServices/active', 'services');
            },
            getProducts:function () {
                this.getData('/api/product/getProducts/active', 'products');
            },
            getBranches:function(){
                this.getData('/api/branch/getBranches', 'branches');
            },
            getClusters:function(){
                this.getData('/api/branch/getClusters', 'clusters');
            },
            viewBranch:function(branch) {
                this.view ='single';
                this.view_id = branch.id;
            },
            viewCluster:function(cluster) {
                this.newCluster ={
                    id:cluster.id,
                    cluster_name:cluster.cluster_name,
                    cluster_owner:cluster.cluster_owner,
                    cluster_email:cluster.cluster_email,
                    services:[],
                    products:[]
                };
                for(var x=0;x<cluster.services.length;x++){
                    this.newCluster.services.push({
                        value: cluster.services[x],
                        label: this.getServiceName(cluster.services[x])
                    });
                }
                for(var x=0;x<cluster.products.length;x++){
                    this.newCluster.products.push({
                        value: cluster.products[x],
                        label: this.getProductName(cluster.products[x])
                    });
                }
                $("#add-cluster-modal").modal("show");
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
            addCluster:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/branch/addCluster?token=' + this.token, 'post', this.newCluster, function(){
                    u.getClusters();
                    toastr.success("Cluster added successfully.");
                    $btn.button('reset');
                    $("#add-cluster-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateCluster:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/branch/updateCluster?token=' + this.token, 'patch', this.newCluster, function(){
                    u.getClusters();
                    toastr.success("Cluster updated successfully.");
                    $btn.button('reset');
                    $("#add-cluster-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            getServiceName:function(id){
                for(var x=0;x<this.services.length;x++){
                    if(id == this.services[x].id)
                        return this.services[x].service_name===null?this.services[x].package_name:this.services[x].service_name;
                }
                return 'Unknown';
            },
            getProductName:function(id){
                for(var x=0;x<this.products.length;x++){
                    if(id == this.products[x].id)
                        return this.products[x].product_name;
                }
                return 'Unknown';
            },
            searchBranch:function(id){
                let u = this;
                axios.get( this.getConfig('Search Branch API') +id)
                    .then(function (response) {
                        if(response.data.branch_id !== undefined){
                            if(!confirm("Branch has found in BOSS Server. Do you want to auto-ill fields?"))
                                return false;

                            u.newBranch.branch_name = response.data.branch_name;
                            u.newBranch.branch_address = response.data.address;
                            u.newBranch.branch_email = response.data.contact_person_email_address;
                        }
                    });
            },
            nextClicked(currentPage) {
                console.log('next clicked', currentPage);
                return true; //return false if you want to prevent moving to next page
            },
            backClicked(currentPage) {
                console.log('back clicked', currentPage);
                return true; //return false if you want to prevent moving to previous page
            },
            getConfig:function(config_name){
                for(var x=0;x<this.configs.length;x++){
                    if(config_name == this.configs[x].config_name)
                        return this.configs[x].config_value;
                }
                toastr.error("Invalid configuration for " + config_name);
            },
            confirmMap:function(){
                let position = document.getElementById("position").value;
                if(position != ''){
                    this.newBranch.map_coordinates.lat = Number(position.split(',')[0]);
                    this.newBranch.map_coordinates.long = Number(position.split(',')[1]);
                }
            },
            editBranch:function(branch) {
                let u = this;
                axios.get('/api/branch/getBranch/' + branch.id)
                .then(function (response) {
                    if(response.data.id !== undefined){
                        u.newBranch = response.data;
                        u.newBranch.search_id = response.data.id;
                        u.newBranch.opening_date = moment(response.data.opening_date).format("YYYY-MM-DD");
                        u.newBranch.branch_pictures = JSON.parse(u.newBranch.branch_pictures);
                        u.newBranch.branch_data = JSON.parse(u.newBranch.branch_data);
                        u.pictures = u.newBranch.branch_pictures;
                        u.newBranch.map_coordinates = JSON.parse(u.newBranch.map_coordinates);
                        u.newBranch.social_media_accounts = JSON.parse(u.newBranch.social_media_accounts);
                    }

                    setTimeout(function(){
                        initMap(u.newBranch.map_coordinates.lat, u.newBranch.map_coordinates.long);
                    },1000)
                });

                $("#add-branch-modal").modal("show");
            },
            addBranch:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/branch/addBranch?token=' + this.token, 'post', this.newBranch, function(){
                    u.getClusters();
                    toastr.success("Branch added successfully.");
                    $btn.button('reset');
                    $("#add-branch-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateBranch:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/branch/updateBranch?token=' + this.token, 'patch', this.newBranch, function(){
                    u.getBranches();
                    u.view_id = 0;
                    setTimeout(function(){
                        u.view_id = u.newBranch.id;
                    },500);
                    toastr.success("Branch updated successfully.");
                    $btn.button('reset');
                    $("#add-branch-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            }
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
            this.getBranches();
            this.getClusters();
            this.getProducts();
            this.getServices();
            this.getCities();
            this.getRegions();
        },
        computed:{
            service_selection:function(){
                var a = [];
                this.services.forEach(function(item){
                    var name = item.service_name ===null? item.package_name:item.service_name;
                    a.push({label:name, value:item.id});
                });
                return a;
            },
            product_selection:function(){
                var a = [];
                this.products.forEach(function(item){
                    a.push({label:item.product_name, value:item.id});
                });
                return a;
            }
        }
    }
</script>

<style>
    #map-canvas{
        margin:5px;
        height:250px;
        width:100%;
    }
</style>