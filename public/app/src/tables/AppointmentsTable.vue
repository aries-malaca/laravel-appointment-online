<template>
    <div>
        <data-table
            :title="title"
            :columns="appointmentTable.columns"
            :rows="appointments"
            :onClick="appointmentTable.rowClicked"
            styleClass="table table-bordered table-hover table-striped"
        />
    </div>
</template>

<script>
    import DataTable from '../components/DataTable.vue';

    export default {
        name: 'ActiveAppointments',
        components:{ DataTable },
        props:['token', 'appointments', 'title', 'configs','hide_client'],
        data: function(){
            return {
                appointmentTable:{
                    columns: [
                        { label: 'Client', field: 'client_name', html:true, hidden: this.hide_client },
                        { label: 'Branch', field: 'branch_name', html:true },
                        { label: 'Technician', field: 'name', html:true },
                        { label: 'App. Date/Time', field: 'transaction_datetime_formatted' },
                        { label: 'App. Added', field: 'added_datetime_formatted' },
                    ],
                    rowClicked: this.viewAppointment,
                },
            }
        },
        methods:{

        }
    }
</script>