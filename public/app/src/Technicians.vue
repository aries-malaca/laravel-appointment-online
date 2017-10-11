<template>
    <div class="technicians">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase">
                        {{ title }}
                    </span>
                </div>
            </div>
            <div class="portlet-body">
                <data-table
                        :columns="technicianTable.columns"
                        :rows="technicians"
                        :paginate="true"
                        :onClick="technicianTable.rowClicked"
                        styleClass="table table-bordered table-hover table-striped"
                />
            </div>
        </div>
    </div>
</template>
<script>
    import DataTable from './components/DataTable.vue';
    export default {
        name: 'Technicians',
        props:['token','user'],
        components:{ DataTable },
        data: function(){
            return {
                title: 'Technicians',
                technicians:[],
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
            }
        },
        methods:{
            getTechnicians:function(){
                let u = this;
                axios.get('/api/technician/getTechnicians')
                    .then(function (response) {
                        u.technicians = response.data;
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            }
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');

            this.getTechnicians();
        }
    }
</script>