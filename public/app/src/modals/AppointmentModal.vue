<template>
    <div id="appointment-modal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Appointment Details</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <table class="table table-hover table-light" v-if="appointment.id !== undefined && user.id !== undefined">
                                <tbody>
                                    <tr>
                                        <td> Client Name: </td>
                                        <td v-if="user.is_client===1"> {{ appointment.client_name }} </td>
                                        <td v-else>
                                            <router-link v-bind:to="'/clients/'+appointment.client_id">{{ appointment.client_name }}</router-link>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Contact No:</td>
                                        <td>{{ appointment.client_contact }}</td>
                                    </tr>
                                    <tr>
                                        <td> Branch: </td>
                                        <td> {{ appointment.branch_name }} </td>
                                    </tr>
                                    <tr>
                                        <td> Date: </td>
                                        <td> {{ appointment.transaction_date_formatted }} </td>
                                    </tr>
                                    <tr>
                                        <td> Time: </td>
                                        <td> {{ appointment.transaction_time_formatted }} </td>
                                    </tr>
                                    <tr>
                                        <td> Technician: </td>
                                        <td> {{ appointment.technician_name }} </td>
                                    </tr>
                                    <tr>
                                        <td> Platform: </td>
                                        <td> {{ appointment.platform }} </td>
                                    </tr>
                                    <tr>
                                        <td> Booked By: </td>
                                        <td> {{ appointment.booked_by_name }} ({{ appointment.booked_by_type.toUpperCase() }})</td>
                                    </tr>
                                    <tr>
                                        <td> Status: </td>
                                        <td v-html="appointment.status_formatted"> </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-8">
                            <table class="table-responsive table table-hover table-bordered" v-if="appointment.services.length>0">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Service</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(service,key) in newTransaction.services">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        name: 'AppointmentModal',
        props:['token', 'appointment', 'user'],
        data: function(){
            return {

            }
        },
        methods:{

        }
    }
</script>