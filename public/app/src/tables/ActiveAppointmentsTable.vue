<template>
    <div>
        <data-table
            title="Active Appointments"
            :columns="appointmentTable.columns"
            :rows="active_appointments"
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
        props:['token', 'active_appointments' ,'user_id', 'configs'],
        data: function(){
            return {
                appointmentTable:{
                    columns: [
                        { label: 'Branch', field: 'branch_name', html:true },
                        { label: 'Technician', field: 'name', html:true },
                        { label: 'App. Date/Time', field: 'transaction_datetime_formatted' },
                    ],
                    rowClicked: this.viewAppointment,
                },
            }
        },
        methods:{

        }
    }
</script>