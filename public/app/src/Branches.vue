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
                    <li>
                        <a href="#schedules" data-toggle="tab">Branch Schedules</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="branches">
                        <button type="button" @click="showAddBranchModal" class="btn green-meadow">New Branch</button>
                        <br/><br/>
                        <data-table
                                :columns="branchTable.columns"
                                :rows="branches"
                                :paginate="true"
                                :onClick="branchTable.rowClicked"
                                styleClass="table table-bordered table-hover table-striped"
                        />
                    </div>
                    <div class="tab-pane" id="clusters">

                    </div>
                    <div class="tab-pane" id="schedules">

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="add-branch-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog large">
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
                branchTable:{
                    columns: [
                        {
                            label: 'Branch Name',
                            field: 'branch_name',
                            filterable: true,
                            html:true
                        },
                        {
                            label: 'Contact No.',
                            field: 'branch_default_contact',
                            filterable: true,
                            html:true
                        },
                        {
                            label: 'Classification',
                            field: 'branch_classification',
                            filterable: true,
                            html:true
                        },
                        {
                            label: 'Region',
                            field: 'region_name',
                            filterable: true,
                            html:true
                        },
                        {
                            label: 'Status',
                            field: 'active_status',
                            filterable: true,
                            html:true
                        },
                    ],
                    rowClicked: this.viewBranch,
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
            }
        },
        mounted:function(){
            this.emit();
        }
    }
</script>