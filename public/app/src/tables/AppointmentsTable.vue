<template>
    <div>
        <data-table
            :title="title"
            :columns="appointmentTable.columns"
            :rows="appointments"
            :onClick="appointmentTable.rowClicked"
            styleClass="table table-bordered table-hover table-striped"
        />

        <appointment-modal :user="user" :token="token" :appointment="display_appointment"></appointment-modal>
    </div>
</template>

<script>
    import DataTable from '../components/DataTable.vue';
    import AppointmentModal from '../modals/AppointmentModal.vue';

    export default {
        name: 'ActiveAppointments',
        components:{ DataTable, AppointmentModal },
        props:['token', 'appointments', 'title', 'configs','hide_client', 'user'],
        data: function(){
            return {
                appointmentTable:{
                    columns: [
                        { label: 'Client', field: 'client_name', hidden: this.hide_client },
                        { label: 'Branch', field: 'branch_name' },
                        { label: 'Technician', field: 'technician_name' },
                        { label: 'App. Date', field: 'transaction_date_formatted' },
                        { label: 'App. Time', field: 'transaction_time_formatted' },
                        { label: 'App. Added', field: 'transaction_added_formatted' },
                        { label: 'Status', field: 'status_formatted', html:true },
                    ],
                    rowClicked: this.viewAppointment,
                },
                display_appointment:{}
            }
        },
        methods:{
            viewAppointment:function(appointment) {
                this.display_appointment = appointment;
                $("#appointment-modal").modal("show");
            }
        }
    }
</script>