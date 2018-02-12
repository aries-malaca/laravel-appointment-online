<template>
    <div class="tab-pane active" id="technicians-list">
        <button type="button" @click="showAddModal" class="btn green-meadow">Add Technician</button>
        <br/><br/>
        <data-table
            :columns="technicianTable.columns"
            :rows="technicians"
            :paginate="true"
            :onClick="technicianTable.rowClicked"
            styleClass="table table-bordered table-hover table-striped"
        />

        <div class="modal fade" id="add-technician-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Add Technician</h4>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" @click="addTechnician($event)" data-loading-text="Saving..." class="btn green">Save</button>
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
    export default {
        name: 'TechniciansList',
        components:{ DataTable },
        data: function(){
            return {
                technicianTable:{
                    columns: [
                        { label: 'Photo', field: 'picture_html', html:true },
                        { label: 'Employee ID', field: 'employee_id',filterable:true},
                        { label: 'Name', field: 'name', filterable: true},
                        { label: 'Mobile', field: 'technician_data.mobile', filterable: true},
                        { label: 'Address', field: 'technician_data.address', filterable: true},
                        { label: 'Cluster', field: 'cluster_name', filterable: true},
                    ],
                    rowClicked: this.viewTechnician,
                },
                newTechnician:{}
            }
        },
        methods:{
            showAddModal(){
                $("#add-technician-modal").modal("show");
            },
            viewTechnician(technician){
                this.$store.commit('technicians/updateViewingTechnician', technician)
            }
        },
        computed:{
            token(){
                return this.$store.state.token;
            },
            technicians(){
                return this.$store.state.technicians.technicians;
            }
        }
    }
</script>