<template>
    <div class="branches">
        <div class="portlet light">
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

        <div class="modal fade" id="add-branch-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-full">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Add Branch</h4>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
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
    export default {
        name: 'Branches',
        components:{ DataTable, VueSelect },
        props: ['token'],
        data: function(){
            return {
                title: 'Branches',
                branches:[],
                clusters:[],
                services:[],
                products:[],
                newBranch:{
                    id:0,
                    branch_name:'',
                    branch_code:'',
                    region_id:0,
                    city_id:0,
                    branch_address:'',
                    branch_main_email:'',
                    branch_main_contact:'',
                    branch_main_contact_person:'',
                    opening_date:moment().format("YYYY-MM-DD"),
                    rooms_count:1,
                    social_media_accounts:[],
                    directions:'',
                    map_coordinates:[14,14],
                    map_picture: 'default_map.jpg',
                    operating_schedules:[],
                    branch_classification:'franchised',
                    payment_methods:[],
                    welcome_message:'',
                    branch_pictures:[],
                    cluster_id:0,
                },
                newCluster:{},
                branchTable:{
                    columns: [
                        { label: 'Branch Name', field: 'branch_name', filterable: true },
                        { label: 'Branch Code', field: 'branch_code', filterable: true },
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
                },
                display_branch:{},
            }
        },
        methods:{
            emit: function() {
                this.$emit('update_title', this.title)
            },
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
                        u[field].push(item);
                    });
                });
            },
            showAddBranchModal:function(){
                $("#add-branch-modal").modal("show");
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
            viewBranch:function() {

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
        },
        mounted:function(){
            this.emit();
            this.getBranches();
            this.getClusters();
            this.getProducts();
            this.getServices();
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