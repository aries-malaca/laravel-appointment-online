<template>
    <div>
        <data-table
            :title="title"
            :columns="appointmentTable.columns"
            :rows="appointments"
            :onClick="appointmentTable.rowClicked"
            styleClass="table table-bordered table-hover table-striped"
        />

        <appointment-modal @close_modal="closeModal" :user="user" :token="token" :id="display_appointment.id"></appointment-modal>
    </div>
</template>

<script>
    import DataTable from '../components/DataTable.vue';
    import AppointmentModal from '../modals/AppointmentModal.vue';

    export default {
        name: 'ActiveAppointments',
        components:{ DataTable, AppointmentModal },
        props:['token', 'appointments', 'title', 'configs','hide_client', 'user', 'hide_branch'],
        data: function(){
            return {
                appointmentTable:{
                    columns: [
                        { label: 'Client', field: 'client_name', hidden: this.hide_client },
                        { label: 'Branch', field: 'branch_name', hidden: this.hide_branch },
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
                setTimeout(function(){
                    $("#appointment-modal-" + appointment.id).modal("show");
                },200);
            },
            closeModal:function(){
                $("#appointment-modal-" + this.display_appointment.id).modal("hide");
                this.display_appointment = {};
            }
        }
    }
</script>