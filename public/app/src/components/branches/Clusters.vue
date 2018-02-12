<template>
    <div class="tab-pane" id="clusters">
        <button type="button" @click="showAddClusterModal" class="btn green-meadow">New Cluster</button>
        <br/><br/>
        <data-table :columns="clusterTable.columns" :rows="clusters" :paginate="true"
                    :onClick="clusterTable.rowClicked" styleClass="table table-bordered table-hover table-striped" />

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
                        <div class="row" v-if="newCluster.cluster_data !== undefined">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>EMS Supported</label>
                                    <div class="md-checkbox">
                                        <input type="checkbox" id="mmm" class="md-check" v-model="newCluster.cluster_data.ems_supported"/>
                                        <label for="mmm">
                                            <span></span>
                                            <span class="check"></span>
                                            <span class="box"></span>
                                            {{newCluster.cluster_data.ems_supported?'YES':'NO' }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Services</label>
                                    <vue-select multiple v-model="newCluster.services" :options="service_selection">
                                    </vue-select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
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
    import DataTable from '../tables/DataTable.vue';
    import VueSelect from 'vue-select';

    export default {
        name: 'Clusters',
        components:{ DataTable, VueSelect },
        data: function(){
            return {
                clusters:[],
                newCluster:{},
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
            showAddClusterModal:function(){
                this.newCluster ={
                    id:0,
                    cluster_name:'',
                    cluster_owner:'',
                    cluster_email:'',
                    services:[],
                    products:[],
                    cluster_data:{
                        ems_supported:false
                    }
                };
                $("#add-cluster-modal").modal("show");
            },
            getClusters:function(){
                let u = this;
                axios.get('/api/branch/getClusters')
                    .then(function (response) {
                        u.clusters = [];
                        response.data.forEach(function(item){
                            item.services = JSON.parse(item.services);
                            item.products = JSON.parse(item.products);
                            u.clusters.push(item);
                        });
                    });
            },
            viewCluster:function(cluster) {
                this.newCluster ={
                    id:cluster.id,
                    cluster_name:cluster.cluster_name,
                    cluster_owner:cluster.cluster_owner,
                    cluster_email:cluster.cluster_email,
                    services:[],
                    products:[],
                    cluster_data:{
                        ems_supported:cluster.cluster_data.ems_supported
                    }
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

                this.makeRequest('/api/branch/updateCluster?token=' + this.token, 'post', this.newCluster, function(){
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
                    if(id === this.services[x].id)
                        return (this.services[x].service_name===null?this.services[x].package_name:this.services[x].service_name)  + ' (' + this.services[x].service_gender.toUpperCase() +')';
                }
                return 'Unknown';
            },
            getProductName:function(id){
                for(var x=0;x<this.products.length;x++){
                    if(id === this.products[x].id)
                        return this.products[x].product_group_name + ' ' + this.products[x].product_size + ' ' + this.products[x].product_variant;
                }
                return 'Unknown';
            }
        },
        mounted:function(){
            this.getClusters();
        },
        computed:{
            service_selection:function(){
                return this.services.map(function(item){
                    item.label = (item.service_name ===null? item.package_name:item.service_name) + ' (' + item.service_gender.toUpperCase() +')';
                    item.value = item.id;
                    return item;
                });
            },
            product_selection:function(){
                let u = this;
                return this.products.map(function(item){
                    item.label = u.getProductName(item.id);
                    item.value = item.id;
                    return item;
                });
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
            products(){
                return this.$store.getters['products/activeProducts'];
            },
            services(){
                return this.$store.getters['services/activeServices'];
            },
        }
    }
</script>