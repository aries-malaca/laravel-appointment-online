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
                        <data-table :columns="clusterTable.columns" :rows="clusterss" :paginate="true"
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
            <div class="modal-dialog modal-full">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Add Cluster</h4>
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
    </div>
</template>

<script>
    import DataTable from './components/DataTable.vue';
    export default {
        name: 'Branches',
        components:{ DataTable },
        data: function(){
            return {
                title: 'Branches',
                branches:[],
                clusters:[],
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
                newCluster:{
                    id:0,
                    cluster_name:'',
                    cluster_owner:'',
                    cluster_email:'',
                    services:[],
                    products:[]
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
            showAddBranchModal:function(){
                $("#add-branch-modal").modal("show");
            },
            getBranches:function(){
                let u = this;
                axios.get('/api/branch/getBranches')
                .then(function (response) {
                    u.branches = response.data;
                });
            },
            viewBranch:function(){

            },
            viewCluster:function(){

            }
        },
        mounted:function(){
            this.emit();
            this.getBranches();
        }
    }
</script>