<template>
    <div class="technicians">
        <div class="portlet light" v-show="view === false"  v-if="user.is_client !== 1 && gate(user, 'technicians','view')">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase">
                        {{ title }}
                    </span>
                </div>
            </div>
            <div class="portlet-body">
                <button type="button" @click="showAddModal" v-if="gate(user, 'technicians','add')" class="btn green-meadow">Add Technician</button>
                <br/><br/>
                <data-table
                        :columns="technicianTable.columns"
                        :rows="technicians"
                        :paginate="true"
                        :onClick="technicianTable.rowClicked"
                        styleClass="table table-bordered table-hover table-striped"
                />
                <technician-modal v-if="!technician" operation="add"></technician-modal>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>
        <technician-profile v-if="view" :with_back="true"></technician-profile>
    </div>
</template>
<script>
    import UnauthorizedError from './errors/UnauthorizedError.vue';
    import TechnicianProfile from './technicians/profile/TechnicianProfile.vue';
    import DataTable from './tables/DataTable.vue';
    import TechnicianModal from './technicians/TechnicianModal.vue';
    export default {
        name: 'Technicians',
        components:{ UnauthorizedError, TechnicianProfile, DataTable, TechnicianModal },
        data: function(){
            return {
                title: 'Technicians',
                technicianTable:{
                    columns: [
                        { label: 'Employee ID', field: 'employee_id',filterable:true},
                        { label: 'Name', field: 'name', filterable: true},
                        { label: 'Mobile', field: 'technician_data.mobile', filterable: true},
                        { label: 'Address', field: 'technician_data.address', filterable: true},
                        { label: 'Cluster', field: 'cluster_name', filterable: true},
                        { label: 'Branch', field: 'branch_name', filterable: true},
                    ],
                    rowClicked: this.viewTechnician,
                },
            }
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'Technicians');
            this.$store.commit('technicians/updateViewingTechnician', false);
            
            this.$store.dispatch('technicians/fetchTechnicians');
        },
        computed:{
            user(){
                return this.$store.state.user;
            },
            view(){
                return this.$store.state.technicians.viewing_technician;
            },
            token(){
                return this.$store.state.token;
            },
            cluster_ids(){
                let u = this;
                var ids = [];
                this.$store.state.branches.branches.filter((item)=>{
                    return u.user.user_data.branches.indexOf(item.id) !== -1;
                }).forEach((item)=>{
                    if(item.cluster_id !== 0)
                        ids.push(item.cluster_id);
                });

                return ids;
            },
            technicians(){
                let u = this;
                return this.$store.state.technicians.technicians.map((technician)=>{
                    technician.picture_html = '<img class="img-circle" style="height:35px" src="images/technicians/'+ technician.technician_picture +'" />';
                    technician.name = technician.first_name + ' ' + technician.last_name;
                    technician.branch_name = technician.branch ?  technician.branch.branch_name : 'N/A';
                    technician.cluster_name = technician.cluster_name !== null ?  technician.cluster_name : 'N/A';

                    return technician;
                }).filter((item)=>{
                    if(u.user.level === 1)
                        return true;

                    if(u.user.user_data.branches !== undefined && item.is_active)
                        return (u.user.user_data.branches.indexOf(0) !== -1 || u.cluster_ids.indexOf(item.cluster_id) !== -1);

                    return false;
                });
            },
            technician(){
                return this.$store.state.technicians.viewing_technician;
            }
        },
        methods:{
            showAddModal(){
                $("#add-technician-modal").modal("show");
            },
            viewTechnician(technician){
                this.$store.commit('technicians/updateViewingTechnician', technician)
            }        }
    }
</script>