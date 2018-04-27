<template>
    <div class="tab-pane active" id="branches">
        <button type="button" @click="showAddBranchModal" v-if="gate(user, 'branches','add')" class="btn green-meadow">
            New Branch
        </button>
        <br/><br/>
        <data-table :columns="branchTable.columns" :rows="filtered_branches" :paginate="true"
                    :onClick="branchTable.rowClicked" styleClass="table table-bordered table-hover table-striped" />

        <branch-modal v-if="!isViewing" operation="add" :map_coordinates="map"></branch-modal>
    </div>
</template>

<script>
    import DataTable from '../tables/DataTable.vue';
    import BranchModal from '../branches/BranchModal.vue'
    export default {
        name: 'BranchesList',
        components:{ DataTable, BranchModal},
        data: function(){
            return {
                title: 'Branches',
                clusters:[],
                cities:[],
                regions:[],
                newBranch:{},
                branchTable:{
                    columns: [
                        { label: 'Branch Name', field: 'branch_name', filterable: true },
                        { label: 'Contact No.', field: 'branch_contact', filterable: true },
                        { label: 'Email', field: 'branch_email', filterable: true },
                        { label: 'Classification', field: 'branch_classification_html',html:true, filterable: true },
                        { label: 'Cluster', field: 'cluster_name', filterable: true },
                    ],
                    rowClicked: this.viewBranch,
                },
                map:{
                    lat:14.5698,
                    long:121.0167
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
                            if(field === 'clusters'){
                                item.services = JSON.parse(item.services);
                                item.products = JSON.parse(item.products);
                            }
                            u[field].push(item);
                        });
                    });
            },
            showAddBranchModal:function(){
                $("#add-branch-modal").modal("show");

                setTimeout(function(){
                    initMap(14.5698,121.0167);
                },500);
            },
            viewBranch:function(branch) {
                this.$store.commit('branches/updateViewingBranch', branch);
            }
        },
        computed:{
            filtered_branches:function(){
                let u = this;
                return this.branches.filter(function(branch){
                    return (u.user.user_data.branches.indexOf(branch.id)  !== -1 || u.user.user_data.branches.indexOf(0) !== -1)
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
            branches(){
                return this.$store.state.branches.branches.map(function(item){
                    item.branch_classification_html =
                        item.branch_classification==='franchised'?'<span class="badge badge-info">Franchised</span>':'<span class="badge badge-success">Co-owned</span>';
                    return item;
                });
            },
            isViewing(){
                return this.$store.state.branches.viewing_branch !== false;
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